<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Loads the correct template for conversion pages.
 * Intercepts WordPress template loading to serve virtual page templates.
 */
class TemplateLoader
{

    /**
     * Hook into WordPress.
     */
    public function init()
    {
        add_filter('template_include', [$this, 'loadTemplate']);
        add_action('wp', [$this, 'fix404Flags']);
    }

    /**
     * Force WordPress to recognize our virtual pages as valid (200 OK)
     * instead of throwing a 404 Not Found header.
     */
    public function fix404Flags()
    {
        global $wp_query;

        $number_to_convert = $wp_query->get('number_id');
        $cel_page = $wp_query->get('cel_page');
        $factorial_id_raw = $wp_query->get('factorial_id');

        // Check explicit 404 conditions (out-of-range numbers)
        if ($cel_page === 'factorielle-de-x' && $factorial_id_raw !== '') {
            if (!is_numeric($factorial_id_raw) || intval($factorial_id_raw) < 0 || intval($factorial_id_raw) > 10000) {
                $wp_query->set_404();
                status_header(404);
                return;
            }
        }

        if (!empty($number_to_convert) || $cel_page === 'convertisseur-anglais' || $cel_page === 'calculatrice-factorielle' || $cel_page === 'calculatrice-diviseurs-pgcd' || $cel_page === 'factorielle-de-x' || $cel_page === 'diviseurs-de-x') {
            $wp_query->is_404 = false;
            $wp_query->is_page = true;
            status_header(200);
        }
    }

    /**
     * Load the appropriate conversion template.
     *
     * @param string $template Default template path.
     * @return string Modified template path.
     */
    public function loadTemplate($template)
    {
        global $wp_query, $wp;

        // English landing page: /convertisseur-anglais/
        $cel_page = $wp_query->get('cel_page');
        if ($cel_page === 'convertisseur-anglais') {
            $plugin_template = CEL_PLUGIN_DIR . 'templates/convertisseur-anglais.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        // Factorial calculator landing page: /calculatrice-factorielle/
        if ($cel_page === 'calculatrice-factorielle') {
            $plugin_template = CEL_PLUGIN_DIR . 'templates/calculatrice-factorielle.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        // Divisors & PGCD calculator landing page: /calculatrice-diviseurs-pgcd-en-ligne/
        if ($cel_page === 'calculatrice-diviseurs-pgcd') {
            $plugin_template = CEL_PLUGIN_DIR . 'templates/calculatrice-diviseurs-pgcd.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        // Dynamic Factorial page: /factorielle-de-x/
        if ($cel_page === 'factorielle-de-x') {
            $x = $wp_query->get('factorial_id');
            if ($x !== '' && is_numeric($x) && intval($x) <= 10000) {
                $plugin_template = CEL_PLUGIN_DIR . 'templates/factorielle-de-x.php';
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
        }

        // Dynamic Divisors page: /diviseurs-de-X/
        if ($cel_page === 'diviseurs-de-x') {
            $plugin_template = CEL_PLUGIN_DIR . 'templates/diviseurs-de-x.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        $number_to_convert = $wp_query->get('number_id');

        if (!isset($number_to_convert) || $number_to_convert === '') {
            return $template;
        }

        $current_url = home_url(add_query_arg([], $wp->request ?? ''));

        // Determine which template to load
        if (strpos($current_url, '/comment-on-dit/') !== false) {
            $template_file = 'automatic-convert-english.php';
        } else {
            $template_file = 'automatic-convert.php';
        }

        // Allow theme override: check theme directory first
        $theme_template = locate_template('convertisseur-chiffre-en-lettre/' . $template_file);
        if ($theme_template) {
            return $theme_template;
        }

        // Fallback to plugin template
        $plugin_template = CEL_PLUGIN_DIR . 'templates/' . $template_file;
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }

        return $template;
    }
}
