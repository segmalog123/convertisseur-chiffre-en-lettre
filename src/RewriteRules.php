<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles URL rewrite rules and query variable registration
 * for the virtual conversion pages.
 */
class RewriteRules
{

    /**
     * Hook into WordPress.
     */
    public function init()
    {
        add_action('init', [$this, 'register']);
        add_filter('query_vars', [$this, 'registerQueryVars']);
        // Use template_redirect (fires on every front-end request) instead of
        // redirect_canonical (which WordPress skips for virtual pages).
        add_action('template_redirect', [$this, 'forceTrailingSlash'], 1);
    }

    /**
     * Register the rewrite rules for both conversion endpoints.
     * The /? at the end makes the trailing slash optional so both
     * /ecrire/95-en-lettre and /ecrire/95-en-lettre/ are matched.
     */
    public function register()
    {
        // French: /ecrire/{number}-en-lettre/
        add_rewrite_rule(
            'ecrire/([a-z0-9,]+)-en-lettre/?$',
            'index.php?pagename=ecrire&number_id=$matches[1]',
            'top'
        );

        // English: /comment-on-dit/{number}-en-anglais/
        add_rewrite_rule(
            'comment-on-dit/([a-z0-9,.]+)-en-anglais/?$',
            'index.php?pagename=comment-on-dit&number_id=$matches[1]',
            'top'
        );

        // English landing page: /convertisseur-anglais/
        add_rewrite_rule(
            'convertisseur-anglais/?$',
            'index.php?cel_page=convertisseur-anglais',
            'top'
        );
    }

    /**
     * Register the number_id query variable.
     *
     * @param array $vars Existing query vars.
     * @return array Modified query vars.
     */
    public function registerQueryVars($vars)
    {
        $vars[] = 'number_id';
        $vars[] = 'cel_page'; // used for /convertisseur-anglais/
        return $vars;
    }

    /**
     * Force trailing slash on conversion URLs via 301 redirect.
     * Fires on template_redirect so it works for virtual pages.
     *
     * /ecrire/95-en-lettre   → 301 → /ecrire/95-en-lettre/
     * /comment-on-dit/25-en-anglais → 301 → /comment-on-dit/25-en-anglais/
     */
    public function forceTrailingSlash()
    {
        global $wp_query;

        $number_id = $wp_query->get('number_id');
        if (empty($number_id)) {
            return;
        }

        // Get the current request URI (e.g. /ecrire/95-en-lettre)
        $request_uri = $_SERVER['REQUEST_URI'];
        $path = parse_url($request_uri, PHP_URL_PATH);

        // If the path does NOT end with a trailing slash, 301 redirect
        if ($path !== '/' && substr($path, -1) !== '/') {
            $redirect_url = trailingslashit(home_url($path));
            // Preserve any query string
            $query = parse_url($request_uri, PHP_URL_QUERY);
            if (!empty($query)) {
                $redirect_url .= '?' . $query;
            }
            wp_redirect($redirect_url, 301);
            exit;
        }
    }
}

