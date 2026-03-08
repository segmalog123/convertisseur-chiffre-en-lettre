<?php
/**
 * Plugin Name:       Convertisseur Chiffre en Lettre
 * Plugin URI:        https://www.chiffreenlettre.com
 * Description:       Converts numbers to text in French and English. Generates virtual pages with full Yoast SEO integration for /ecrire/{n}-en-lettre/ and /comment-on-dit/{n}-en-anglais/ URLs.
 * Version:           0.0.2
 * Author:            Chiffre en Lettre
 * Author URI:        https://www.chiffreenlettre.com
 * Text Domain:       convertisseur-chiffre-en-lettre
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Requires PHP:      7.2
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================
// EARLY CACHE BYPASS — must run before any cache plugin buffers
// ============================================================
// Tell WP Fastest Cache (and W3TC, WP Super Cache) NOT to cache
// our conversion pages. These pages are already cached by Cloudflare.
// This MUST be defined before plugins_loaded / output buffering starts.
$cel_request_uri = $_SERVER['REQUEST_URI'] ?? '';
if (
    strpos($cel_request_uri, '/ecrire/') !== false ||
    strpos($cel_request_uri, '/comment-on-dit/') !== false
) {
    if (!defined('DONOTCACHEPAGE')) {
        define('DONOTCACHEPAGE', true);
    }
    if (!defined('DONOTMINIFY')) {
        define('DONOTMINIFY', true); // Also skip JS/CSS minification on these pages
    }
}
unset($cel_request_uri);
// ============================================================

// Plugin constants
define('CEL_PLUGIN_VERSION', '1.0.0');
define('CEL_PLUGIN_FILE', __FILE__);
define('CEL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CEL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CEL_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * PSR-4 style Autoloader for ChiffreEnLettre namespace.
 */
spl_autoload_register(function ($class) {
    $prefix = 'ChiffreEnLettre\\';
    $base_dir = CEL_PLUGIN_DIR . 'src/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load global function wrappers (non-namespaced for template compatibility).
require_once CEL_PLUGIN_DIR . 'src/global-functions.php';

/**
 * Plugin activation.
 * We set a flag so rewrite rules are flushed on the NEXT request.
 * This is the correct WordPress pattern — flushing during activation
 * itself is unreliable because other plugins haven't registered their
 * rules yet at that point.
 */
function cel_activate()
{
    // Register our rules first
    $rewrite = new ChiffreEnLettre\RewriteRules();
    $rewrite->register();
    // Set flag to flush on next load
    update_option('cel_flush_rewrite_rules', true);
}
register_activation_hook(__FILE__, 'cel_activate');

/**
 * Plugin deactivation: flush rewrite rules.
 */
function cel_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'cel_deactivate');

/**
 * Flush rewrite rules on the first request after activation.
 * This is more reliable than flushing during the activation hook itself.
 */
function cel_maybe_flush_rewrite_rules()
{
    if (get_option('cel_flush_rewrite_rules')) {
        flush_rewrite_rules();
        delete_option('cel_flush_rewrite_rules');
    }
}
add_action('init', 'cel_maybe_flush_rewrite_rules', 20);

/**
 * Initialize the plugin.
 */
function cel_init_plugin()
{
    // Initialize the core plugin (handles templates, hooks, assets)
    $plugin = new \ChiffreEnLettre\Plugin();
    $plugin->init();

    // Initialize custom sitemaps
    $plugin_sitemap = new \ChiffreEnLettre\SitemapController();
    $plugin_sitemap->init();

    // Init Widgets
    add_action('widgets_init', function () {
        if (class_exists('\ChiffreEnLettre\Widgets\ConversionWidget')) {
            register_widget('\ChiffreEnLettre\Widgets\ConversionWidget');
        }
    });

}
add_action('plugins_loaded', 'cel_init_plugin');
