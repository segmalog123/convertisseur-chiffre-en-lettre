<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

use ChiffreEnLettre\Converters\ConverterHelper;

/**
 * Handles WordPress AJAX requests for inline number conversion.
 *
 * Used on the homepage (FR) and /convertisseur-anglais/ (EN) landing pages
 * so that non-VIP numbers get an instant result without navigating away.
 *
 * Endpoint: /wp-admin/admin-ajax.php?action=cel_convert
 */
class AjaxController
{
    /**
     * Register AJAX hooks.
     * Both logged-in and non-logged-in users need access.
     */
    public function init()
    {
        add_action('wp_ajax_cel_convert', [$this, 'handleConvert']);
        add_action('wp_ajax_nopriv_cel_convert', [$this, 'handleConvert']);
    }

    /**
     * Handle the AJAX conversion request.
     * Returns JSON: { success: true, result: "Vingt", lang: "fr" }
     */
    public function handleConvert()
    {
        // Verify nonce for basic security
        if (!check_ajax_referer('cel_convert_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'Invalid nonce'], 403);
        }

        $number = sanitize_text_field($_POST['number'] ?? '');
        $lang = sanitize_text_field($_POST['lang'] ?? 'fr');

        if ($number === '') {
            wp_send_json_error(['message' => 'Nombre manquant'], 400);
        }

        // Check if VIP — JS will navigate to dedicated page if true
        $is_vip = NumberVipList::isVip($number);

        // Use the same ConverterHelper as the rest of the plugin
        // action: 'convert' with context '0' means plain (no currency)
        if ($lang === 'en') {
            $result = ucfirst(ConverterHelper::convert($number, 'convert', '0'));
        } else {
            $result = ucfirst(ConverterHelper::convert($number, 'convert'));
        }

        if (empty($result)) {
            wp_send_json_error(['message' => 'Conversion impossible'], 422);
        }

        wp_send_json_success([
            'result' => $result,
            'number' => $number,
            'lang' => $lang,
            'is_vip' => $is_vip,
        ]);
    }
}
