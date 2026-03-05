<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles the admin settings page for the plugin.
 */
class AdminSettings
{
    /**
     * Hook into the admin.
     */
    public function init()
    {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    /**
     * Add the settings page under "Settings".
     */
    public function addSettingsPage()
    {
        add_options_page(
            'Convertisseur Ads',
            'Convertisseur Ads',
            'manage_options',
            'cel-settings',
            [$this, 'renderSettingsPage']
        );
    }

    /**
     * Register the settings and AJAX actions.
     */
    public function registerSettings()
    {
        // We still register the setting so WP knows about it, but we won't use options.php
        register_setting('cel_settings_group', 'cel_ad_code');

        // Handle custom AJAX save to bypass WAFs blocking options.php
        add_action('wp_ajax_cel_save_ad_code', [$this, 'ajaxSaveAdCode']);
    }

    /**
     * Render the settings page HTML and interceptform via JS.
     */
    public function renderSettingsPage()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Get options for French tool
        $fr_left = get_option('cel_ad_fr_left', '');
        $fr_center = get_option('cel_ad_fr_center', '');
        $fr_right = get_option('cel_ad_fr_right', '');

        // Get options for English tool
        $en_left = get_option('cel_ad_en_left', '');
        $en_center = get_option('cel_ad_en_center', '');
        $en_right = get_option('cel_ad_en_right', '');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p>Ici, vous pouvez définir jusqu'à 3 blocs de publicité (Gauche, Centre, Droite). Ils s'afficheront côte à côte. Si
                un bloc est vide, il n'apparaîtra pas.</p>

            <div id="cel-save-message" class="notice notice-success is-dismissible" style="display:none;">
                <p>Paramètres sauvegardés avec succès !</p>
            </div>

            <form id="cel-ads-form" method="post">
                <hr style="margin: 20px 0;">
                <h2>Ads : Outil de Conversion Français (/ecrire/...)</h2>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="cel_ad_fr_left">Bloc Gauche (FR)</label></th>
                            <td><textarea name="cel_ad_fr_left" id="cel_ad_fr_left" rows="5" cols="60"
                                    class="large-text code"><?php echo esc_textarea($fr_left); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cel_ad_fr_center">Bloc Central (FR)</label></th>
                            <td><textarea name="cel_ad_fr_center" id="cel_ad_fr_center" rows="5" cols="60"
                                    class="large-text code"><?php echo esc_textarea($fr_center); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cel_ad_fr_right">Bloc Droit (FR)</label></th>
                            <td><textarea name="cel_ad_fr_right" id="cel_ad_fr_right" rows="5" cols="60"
                                    class="large-text code"><?php echo esc_textarea($fr_right); ?></textarea></td>
                        </tr>
                    </tbody>
                </table>

                <hr style="margin: 20px 0;">
                <h2>Ads : Outil de Conversion Anglais (/comment-on-dit/...)</h2>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="cel_ad_en_left">Bloc Gauche (EN)</label></th>
                            <td><textarea name="cel_ad_en_left" id="cel_ad_en_left" rows="5" cols="60"
                                    class="large-text code"><?php echo esc_textarea($en_left); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cel_ad_en_center">Bloc Central (EN)</label></th>
                            <td><textarea name="cel_ad_en_center" id="cel_ad_en_center" rows="5" cols="60"
                                    class="large-text code"><?php echo esc_textarea($en_center); ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cel_ad_en_right">Bloc Droit (EN)</label></th>
                            <td><textarea name="cel_ad_en_right" id="cel_ad_en_right" rows="5" cols="60"
                                    class="large-text code"><?php echo esc_textarea($en_right); ?></textarea></td>
                        </tr>
                    </tbody>
                </table>

                <?php wp_nonce_field('cel_save_ad_code_nonce', 'cel_nonce'); ?>
                <p class="submit">
                    <button type="submit" class="button button-primary" id="cel-submit-btn">Sauvegarder les
                        modifications</button>
                    <span class="spinner" id="cel-spinner"></span>
                </p>
            </form>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                $('#cel-ads-form').on('submit', function (e) {
                    e.preventDefault();

                    var btn = $('#cel-submit-btn');
                    var spinner = $('#cel-spinner');
                    var msg = $('#cel-save-message');

                    btn.prop('disabled', true);
                    spinner.addClass('is-active');
                    msg.hide();

                    var data = {
                        action: 'cel_save_ad_code',
                        cel_nonce: $('#cel_nonce').val(),
                        cel_ad_fr_left: $('#cel_ad_fr_left').val(),
                        cel_ad_fr_center: $('#cel_ad_fr_center').val(),
                        cel_ad_fr_right: $('#cel_ad_fr_right').val(),
                        cel_ad_en_left: $('#cel_ad_en_left').val(),
                        cel_ad_en_center: $('#cel_ad_en_center').val(),
                        cel_ad_en_right: $('#cel_ad_en_right').val()
                    };

                    $.post(ajaxurl, data, function (response) {
                        btn.prop('disabled', false);
                        spinner.removeClass('is-active');

                        if (response.success) {
                            msg.show();
                        } else {
                            alert('Erreur lors de la sauvegarde: ' + (response.data || 'Inconnue'));
                        }
                    }).fail(function (xhr) {
                        btn.prop('disabled', false);
                        spinner.removeClass('is-active');
                        alert('Erreur réseau. Si Wordfence bloque toujours, autorisez cette action dans Wordfence Live Traffic.');
                    });
                });
            });
        </script>
        <?php
    }

    /**
     * AJAX handler to save the ad code securely while bypassing generic WAF rules.
     */
    public function ajaxSaveAdCode()
    {
        // Verify nonce
        if (!isset($_POST['cel_nonce']) || !wp_verify_nonce($_POST['cel_nonce'], 'cel_save_ad_code_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        // Verify permissions (admin only)
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        // The exact keys we expect to save
        $ad_keys = [
            'cel_ad_fr_left',
            'cel_ad_fr_center',
            'cel_ad_fr_right',
            'cel_ad_en_left',
            'cel_ad_en_center',
            'cel_ad_en_right'
        ];

        foreach ($ad_keys as $key) {
            if (isset($_POST[$key])) {
                $code = wp_unslash($_POST[$key]);
                update_option($key, $code);
            }
        }

        wp_send_json_success();
    }
}
