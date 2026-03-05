<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Intercepts requests for non-VIP conversion pages and injects
 * a noindex meta tag to prevent Google from indexing them,
 * thereby avoiding 301 redirect chains and Soft 404 errors.
 */
class RedirectController
{
    /**
     * Hook into WordPress.
     */
    public function init()
    {
        // Hook into Yoast SEO to override robots meta
        add_filter('wpseo_robots', [$this, 'overrideYoastRobots'], 99);

        // Hook into WP core robots (fallback if Yoast is disabled)
        add_filter('wp_robots', [$this, 'overrideWpRobots'], 99);
    }

    /**
     * Override Yoast SEO robots meta for non-VIP pages.
     */
    public function overrideYoastRobots($robots)
    {
        global $wp_query;

        // Ensure we are in the main query and not admin
        if (is_admin() || !$wp_query->is_main_query()) {
            return $robots;
        }

        $number_id = $wp_query->get('number_id');

        if (!empty($number_id) && !NumberVipList::isVip($number_id)) {
            return 'noindex, follow';
        }

        return $robots;
    }

    /**
     * Override WP Core robots meta for non-VIP pages.
     */
    public function overrideWpRobots($robots)
    {
        global $wp_query;

        if (is_admin() || !$wp_query->is_main_query()) {
            return $robots;
        }

        $number_id = $wp_query->get('number_id');

        if (!empty($number_id) && !NumberVipList::isVip($number_id)) {
            $robots['noindex'] = true;
            $robots['follow'] = true;
            unset($robots['index']);
        }

        return $robots;
    }
}
