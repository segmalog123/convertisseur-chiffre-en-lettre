<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Determines algorithmically whether a number deserves a full indexed page.
 *
 * VIP numbers:
 *   1 – 1000           every integer
 *   1001 – 10000       multiples of 50
 *   10001 – 100000     multiples of 500
 *   100001 – 1000000   multiples of 5000
 *
 * Plus hardcoded exceptions: recent years, common cheque amounts.
 *
 * All decimal numbers (with . or ,) are NOT VIP.
 * All integers outside the ranges above are NOT VIP.
 *
 * This is intentionally a pure static class with zero DB calls.
 * The check runs in < 0.01ms.
 */
class NumberVipList
{
    /**
     * Hardcoded special numbers always considered VIP,
     * regardless of step rules.
     */
    private static $hardcoded = [
        // Recent/upcoming years
        2019,
        2020,
        2021,
        2022,
        2023,
        2024,
        2025,
        2026,
        // Common cheque / administrative amounts
        1500,
        2500,
        3500,
        4500,
        7500,
        15000,
        25000,
        35000,
        45000,
        75000,
        150000,
        250000,
        350000,
        450000,
        750000,
        // Common round numbers outside step coverage
        1000000,
    ];

    /**
     * Check if a number (as received from the URL) belongs to the VIP list.
     *
     * @param  string $raw The raw number string from the URL (e.g. "1050", "10,75").
     * @return bool
     */
    public static function isVip(string $raw): bool
    {
        // Decimal numbers (contain . or ,) are never VIP
        if (strpos($raw, '.') !== false || strpos($raw, ',') !== false) {
            return false;
        }

        // Must be a pure integer string
        if (!ctype_digit($raw)) {
            return false;
        }

        $n = (int) $raw;

        // Negative or zero: not VIP
        if ($n <= 0) {
            return false;
        }

        // 1 – 1000: every integer
        if ($n <= 1000) {
            return true;
        }

        // 1001 – 10000: multiples of 50
        if ($n <= 10000 && $n % 50 === 0) {
            return true;
        }

        // 10001 – 100000: multiples of 500
        if ($n <= 100000 && $n % 500 === 0) {
            return true;
        }

        // 100001 – 1000000: multiples of 5000
        if ($n <= 1000000 && $n % 5000 === 0) {
            return true;
        }

        // Hardcoded exceptions
        if (in_array($n, self::$hardcoded, true)) {
            return true;
        }

        return false;
    }

    /**
     * Get a complete array of all VIP numbers.
     * Useful for building sitemaps (returns ~1565 items).
     *
     * @return int[]
     */
    public static function getAllVips(): array
    {
        $vips = [];

        // 1 to 1000
        for ($i = 1; $i <= 1000; $i++) {
            $vips[] = $i;
        }

        // 1050 to 10000 by 50
        for ($i = 1050; $i <= 10000; $i += 50) {
            $vips[] = $i;
        }

        // 10500 to 100000 by 500
        for ($i = 10500; $i <= 100000; $i += 500) {
            $vips[] = $i;
        }

        // 105000 to 1000000 by 5000
        for ($i = 105000; $i <= 1000000; $i += 5000) {
            $vips[] = $i;
        }

        // Add hardcoded
        foreach (self::$hardcoded as $h) {
            $vips[] = $h;
        }

        $vips = array_unique($vips);
        sort($vips);

        return $vips;
    }

    /**
     * Get a list of "Smart Related" VIP numbers for internal linking.
     * Ensures we only link to indexable pages (VIPs), avoiding 301 redirects.
     *
     * @param int $n The current number
     * @param int $limit How many to return
     * @return array List of integer VIPs
     */
    public static function getSmartRelated(int $n, int $limit = 8): array
    {
        $candidates = [];

        // Strategy depends on magnitude to find relevant neighbors
        if ($n <= 1000) {
            // Neighbors are integers. Check +/- 15 to find enough
            for ($i = 1; $i <= 15; $i++) {
                $candidates[] = $n - $i;
                $candidates[] = $n + $i;
            }
        } elseif ($n <= 10000) {
            // Neighbors are multiples of 50. Check +/- 5 steps
            $base = (int) (round($n / 50) * 50);
            for ($i = 0; $i <= 8; $i++) { // start 0 to include base if != n
                $candidates[] = $base - ($i * 50);
                $candidates[] = $base + ($i * 50);
            }
        } elseif ($n <= 100000) {
            // Neighbors are multiples of 500
            $base = (int) (round($n / 500) * 500);
            for ($i = 0; $i <= 8; $i++) {
                $candidates[] = $base - ($i * 500);
                $candidates[] = $base + ($i * 500);
            }
        } else {
            // Neighbors are multiples of 5000
            $base = (int) (round($n / 5000) * 5000);
            for ($i = 0; $i <= 8; $i++) {
                $candidates[] = $base - ($i * 5000);
                $candidates[] = $base + ($i * 5000);
            }
        }

        // Add 2 random VIPs from the hardcoded list for discovery/variety
        if (!empty(self::$hardcoded)) {
            $rand_keys = array_rand(self::$hardcoded, min(2, count(self::$hardcoded)));
            if (!is_array($rand_keys))
                $rand_keys = [$rand_keys];
            foreach ($rand_keys as $k) {
                $candidates[] = self::$hardcoded[$k];
            }
        }

        // Filter: Must be > 0, != $n, and isVip()
        $vips = [];
        foreach ($candidates as $cand) {
            if ($cand > 0 && $cand !== $n && self::isVip((string) $cand)) {
                $vips[] = $cand;
            }
        }

        $vips = array_unique($vips);

        // Sort by distance to $n (so we show closest first)
        usort($vips, function ($a, $b) use ($n) {
            return abs($a - $n) <=> abs($b - $n);
        });

        return array_slice($vips, 0, $limit);
    }
}
