<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin orchestrator.
 * Registers all controllers and hooks.
 */
class Plugin
{

    /**
     * Initialize all plugin components.
     */
    public function init()
    {
        // Core routing
        $rewrite = new RewriteRules();
        $rewrite->init();

        // Number conversion helpers (makes global helper functions available)
        Converters\ConverterHelper::init();

        // SEO integration (Yoast)
        $seo = new SeoController();
        $seo->init();

        // Sitemap integration (Yoast)
        $sitemap = new SitemapController();
        $sitemap->init();

        // Breadcrumbs
        $breadcrumb = new BreadcrumbController();
        $breadcrumb->init();

        // Header block (converter input UI)
        $header = new HeaderBlock();
        $header->init();

        // Template loader
        $template = new TemplateLoader();
        $template->init();

        // Redirect controller (301 for non-VIP numbers)
        $redirect = new RedirectController();
        $redirect->init();

        // AJAX controller (inline conversion for landing pages)
        $ajax = new AjaxController();
        $ajax->init();

        // Assets (JS/CSS)
        $assets = new AssetManager();
        $assets->init();

        // Admin Settings Page (Ad Integration)
        $settings = new AdminSettings();
        $settings->init();
    }
}
