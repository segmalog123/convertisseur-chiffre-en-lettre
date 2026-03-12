<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers custom sitemaps for conversion pages with Yoast SEO.
 */
class SitemapController
{

    /**
     * Hook into WordPress/Yoast.
     */
    public function init()
    {
        add_action('init', [$this, 'registerSitemaps']);
        add_action('init', [$this, 'registerSitemapActions']);
        add_filter('wpseo_sitemap_index', [$this, 'addEcrireSitemapIndex']);
        add_filter('wpseo_sitemap_index', [$this, 'addCommentOnDitSitemapIndex']);
        
        // New custom index filters
        add_filter('wpseo_sitemap_index', [$this, 'addFactorialSitemapIndex']);
        add_filter('wpseo_sitemap_index', [$this, 'addCalculatorsSitemapIndex']);
    }

    /**
     * Register the custom sitemaps with Yoast.
     */
    public function registerSitemaps()
    {
        global $wpseo_sitemaps;

        if (isset($wpseo_sitemaps) && !empty($wpseo_sitemaps)) {
            $wpseo_sitemaps->register_sitemap('ecrirechiffre', [$this, 'createEcrireSitemap']);
            $wpseo_sitemaps->register_sitemap('commentonditchiffre', [$this, 'createCommentOnDitSitemap']);
            
            // New sitemaps
            $wpseo_sitemaps->register_sitemap('factorielle', [$this, 'createFactorialSitemap']);
            $wpseo_sitemaps->register_sitemap('calculateurs', [$this, 'createCalculatorsSitemap']);
        }
    }

    /**
     * Register sitemap action hooks.
     */
    public function registerSitemapActions()
    {
        add_action('wp_seo_do_sitemap_our-ecrirechiffre', [$this, 'createEcrireSitemap']);
        add_action('wp_seo_do_sitemap_our-commentonditchiffre', [$this, 'createCommentOnDitSitemap']);
        
        // Actions for new sitemaps
        add_action('wp_seo_do_sitemap_our-factorielle', [$this, 'createFactorialSitemap']);
        add_action('wp_seo_do_sitemap_our-calculateurs', [$this, 'createCalculatorsSitemap']);
    }

    /**
     * Create the French conversion sitemap (ecrirechiffre).
     */
    public function createEcrireSitemap()
    {
        global $wpseo_sitemaps;
        $output = '';
        $vips = \ChiffreEnLettre\NumberVipList::getAllVips();

        foreach ($vips as $i) {
            $url = [];
            $url['loc'] = site_url() . '/ecrire/' . $i . '-en-lettre/';
            $url['mod'] = date('c', time());
            $output .= $wpseo_sitemaps->renderer->sitemap_url($url);
        }

        $sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
        $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $sitemap .= $output . '</urlset>';

        $wpseo_sitemaps->set_sitemap($sitemap);
    }

    /**
     * Create the English conversion sitemap (commentonditchiffre).
     */
    public function createCommentOnDitSitemap()
    {
        global $wpseo_sitemaps;
        $output = '';
        $vips = \ChiffreEnLettre\NumberVipList::getAllVips();

        foreach ($vips as $i) {
            $url = [];
            $url['loc'] = site_url() . '/comment-on-dit/' . $i . '-en-anglais/';
            $url['mod'] = date('c', time());
            $output .= $wpseo_sitemaps->renderer->sitemap_url($url);
        }

        $sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
        $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $sitemap .= $output . '</urlset>';

        $wpseo_sitemaps->set_sitemap($sitemap);
    }

    /**
     * Add the ecrire sitemap to the sitemap index.
     *
     * @param string $items Existing sitemap index items.
     * @return string
     */
    public function addEcrireSitemapIndex($items)
    {
        $items .= '    <sitemap>   
        <loc>' . site_url() . '/ecrirechiffre-sitemap.xml</loc>
        <lastmod>' . date('c', time()) . '</lastmod>
    </sitemap>
';
        return $items;
    }

    /**
     * Add the comment-on-dit sitemap to the sitemap index.
     *
     * @param string $items Existing sitemap index items.
     * @return string
     */
    public function addCommentOnDitSitemapIndex($items)
    {
        $items .= '    <sitemap>   
        <loc>' . site_url() . '/commentonditchiffre-sitemap.xml</loc>
        <lastmod>' . date('c', time()) . '</lastmod>
    </sitemap>
';
        return $items;
    }

    /**
     * Create the Factorial sitemap.
     */
    public function createFactorialSitemap()
    {
        global $wpseo_sitemaps;
        $output = '';

        // Factorial VIPs (0 to 200, plus multiples of 50 up to 10000)
        for ($i = 0; $i <= 10000; $i++) {
            if ($i <= 200 || ($i > 200 && $i % 50 === 0)) {
                $url = [];
                $url['loc'] = site_url() . '/factorielle-de-' . $i . '/';
                $url['mod'] = date('c', time());
                $output .= $wpseo_sitemaps->renderer->sitemap_url($url);
            }
        }

        $sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
        $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $sitemap .= $output . '</urlset>';

        $wpseo_sitemaps->set_sitemap($sitemap);
    }

    /**
     * Create the Calculators landing pages sitemap.
     */
    public function createCalculatorsSitemap()
    {
        global $wpseo_sitemaps;
        $output = '';

        $pages = [
            '/convertisseur-anglais/',
            '/calculatrice-factorielle/',
            '/calculatrice-diviseurs-pgcd-en-ligne/'
        ];

        foreach ($pages as $path) {
            $url = [];
            $url['loc'] = site_url() . $path;
            $url['mod'] = date('c', time());
            $output .= $wpseo_sitemaps->renderer->sitemap_url($url);
        }

        $sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
        $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $sitemap .= $output . '</urlset>';

        $wpseo_sitemaps->set_sitemap($sitemap);
    }

    /**
     * Add the factorial sitemap to the sitemap index.
     */
    public function addFactorialSitemapIndex($items)
    {
        $items .= '    <sitemap>   
        <loc>' . site_url() . '/factorielle-sitemap.xml</loc>
        <lastmod>' . date('c', time()) . '</lastmod>
    </sitemap>
';
        return $items;
    }

    /**
     * Add the calculators sitemap to the sitemap index.
     */
    public function addCalculatorsSitemapIndex($items)
    {
        $items .= '    <sitemap>   
        <loc>' . site_url() . '/calculateurs-sitemap.xml</loc>
        <lastmod>' . date('c', time()) . '</lastmod>
    </sitemap>
';
        return $items;
    }
}
