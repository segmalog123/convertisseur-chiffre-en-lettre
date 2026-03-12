<?php
namespace ChiffreEnLettre\Converters;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Central dispatcher for conversion actions.
 * Ports funcConvert(), funcListNumber(), funcNumBetween(), funcPercent() from function_converter.php.
 * Also registers backward-compatible global functions so templates work seamlessly.
 */
class ConverterHelper
{

    /**
     * Register global helper functions for template compatibility.
     */
    public static function init()
    {
        // These global functions mirror the originals so templates don't need changes.
        if (!function_exists('funcConvert')) {
            function_exists('funcConvert') || true; // no-op, defining below
        }
    }

    /**
     * Detect which conversion context we're in (French or English) based on the current URL.
     *
     * @return string 'fr' or 'en'
     */
    public static function detectContext()
    {
        global $wp;
        $current_url = home_url(add_query_arg([], $wp->request ?? ''));

        if (strpos($current_url, '/comment-on-dit/') !== false) {
            return 'en';
        }
        return 'fr';
    }

    /**
     * Central conversion dispatcher.
     * Replaces the global funcConvert() function.
     *
     * @param string $number_to_convert The number from the URL.
     * @param string $action            Action: 'convert', 'url', 'title', 'desc', 'bread', 'h1', 'h2'.
     * @param string $type              Currency type (for 'convert' action).
     * @return string
     */
    public static function convert($number_to_convert, $action = '', $type = '')
    {
        // --- Input size guard ---
        // Reject numbers longer than 15 digits to prevent CPU spikes from bots/crawlers.
        // PHP floats lose precision beyond 15 significant digits anyway.
        $digits_only = preg_replace('/[^0-9]/', '', $number_to_convert);
        if (strlen($digits_only) > 15) {
            return 'Nombre trop grand';
        }

        $context = self::detectContext();
        $cache_key = 'conversion_' . md5($action . '_' . $number_to_convert . '_' . $type . '_' . $context);
        $cache_group = 'chiffre_en_lettre';

        $cached_result = wp_cache_get($cache_key, $cache_group);
        if ($cached_result !== false) {
            return $cached_result;
        }

        $result = '';

        // ---------- FRENCH CONTEXT ----------
        if ($context === 'fr') {
            if ($action === 'convert') {
                if ($type !== '') {
                    $result = FrenchConverter::enDevise($number_to_convert, $type)['final_number_lettre'];
                } else {
                    $result = FrenchConverter::enChiffre($number_to_convert)['final_number_lettre'];
                }
            } elseif ($action === 'url') {
                $result = site_url() . '/ecrire/' . $number_to_convert . '-en-lettre/';
            } elseif ($action === 'title') {
                $result = 'Comment écrire ' . $number_to_convert . ' en lettre - Chiffre en lettre';
            } elseif ($action === 'desc') {
                $result = 'Comment écrire ' . $number_to_convert . ' en lettre : '
                    . ucfirst(FrenchConverter::enChiffre($number_to_convert)['final_number_lettre'])
                    . '. Écriture du nombre ' . $number_to_convert . ' euros en lettre : '
                    . ucfirst(FrenchConverter::enDevise($number_to_convert, '0')['final_number_lettre']);
            } elseif ($action === 'bread') {
                $result = 'Comment écrire ' . $number_to_convert . ' en lettre';
            } elseif ($action === 'h1') {
                $result = 'Comment écrire ' . $number_to_convert . ' en lettre';
            }
        }

        // ---------- ENGLISH CONTEXT ----------
        if ($context === 'en') {
            if ($action === 'convert') {
                if ($type !== '') {
                    $result = ucfirst(EnglishConverter::convertCurrencyToWords($number_to_convert, $type));
                } else {
                    $result = ucfirst(EnglishConverter::convertCurrencyToWords($number_to_convert, 0));
                }
            } elseif ($action === 'url') {
                $result = site_url() . '/comment-on-dit/' . $number_to_convert . '-en-anglais/';
            } elseif ($action === 'title') {
                $result = 'Comment on dit ' . $number_to_convert . ' en anglais en lettres';
            } elseif ($action === 'desc') {
                $result = 'Voulez vous apprendre comment on dit ' . $number_to_convert
                    . ' en anglais? Voici la traduction correcte en anglais de ' . $number_to_convert
                    . '. Apprenez à dire et écrire les nombres en anglais sans fautes.';
            } elseif ($action === 'bread') {
                $result = 'Comment on dit ' . $number_to_convert . ' en anglais';
            } elseif ($action === 'h1') {
                $result = 'Comment on dit ' . $number_to_convert . ' en anglais en lettre';
            } elseif ($action === 'h2') {
                $result = 'Convertisseur Chiffre en Lettres en Anglais';
            }
        }

        wp_cache_set($cache_key, $result, $cache_group, 2592000); // Cache for 1 month

        return $result;
    }

    /**
     * Generate a list of similar/related numbers for internal linking.
     * Replaces funcListNumber().
     *
     * @param string $number_to_convert The number from the URL.
     * @return array Array of 10 number strings.
     */
    public static function listSimilarNumbers($number_to_convert)
    {
        // Parse the number to int
        $n = (int) str_replace(['.', ','], ['', '.'], $number_to_convert);

        // Use smart VIP linking to avoid redirects
        // We request 10 candidates
        $vips = \ChiffreEnLettre\NumberVipList::getSmartRelated($n, 10);

        // Format back to string with commas (if needed by templates, though usually integers don't have them)
        // Our system standardizes on commas for decimals, but VIPs are integers.
        $result = [];
        foreach ($vips as $v) {
            $result[] = (string) $v;
        }

        return $result;
    }

    /**
     * Get the range label for a number in the English context.
     * Replaces funcNumBetween().
     *
     * @param float $number The number.
     * @return string
     */
    public static function numberBetween($number)
    {
        if ($number <= 20) {
            return 'nombre en anglais de 1 à 20';
        }
        if ($number <= 100 && $number >= 20) {
            return 'nombre en anglais de 1 à 100';
        }
        if ($number <= 1000 && $number >= 100) {
            return 'nombre en anglais de 1 à 1000';
        }
        if ($number <= 10000 && $number >= 1000) {
            return 'chiffre en anglais de 1 a 10000';
        }
        return '';
    }

    /**
     * Get a percentage-like related number.
     * Replaces funcPercent().
     *
     * @param string $number_to_convert The number from the URL.
     * @return string
     */
    public static function percent($number_to_convert)
    {
        $similar = self::listSimilarNumbers($number_to_convert);
        $num = isset($similar[8]) ? $similar[8] : (!empty($similar) ? end($similar) : $number_to_convert);
        
        if ($num > 100) {
            return substr($num, 0, 2);
        }
        return $num;
    }
}
