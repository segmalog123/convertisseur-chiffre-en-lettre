<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

use ChiffreEnLettre\Converters\ConverterHelper;

/**
 * Renders the converter input UI blocks.
 * Fully self-contained — works with any WordPress theme.
 *
 * The UI is injected via:
 *  1. The `before_custom_header_block` / `after_custom_header_block` actions
 *     (for backward compat with the Sahifa child theme).
 *  2. The `wp_body_open` action (for any other theme).
 *  3. The [cel_converter_form] and [cel_converter_heading] shortcodes.
 */
class HeaderBlock
{

    /**
     * Hook into WordPress.
     */
    public function init()
    {
        // Backward compat: Sahifa child theme hooks
        add_action('before_custom_header_block', [$this, 'renderBeforeBlock']);
        add_action('after_custom_header_block', [$this, 'renderAfterBlock']);

        // Universal fallback: inject via wp_body_open for any theme
        add_action('wp_body_open', [$this, 'maybeRenderBlocks'], 20);

        // Shortcodes for complete theme independence
        add_shortcode('cel_converter_form', [$this, 'shortcodeForm']);
        add_shortcode('cel_converter_heading', [$this, 'shortcodeHeading']);
    }

    /**
     * Render the before+after blocks via wp_body_open ONLY if the theme
     * does NOT fire the custom header block actions (i.e., non-Sahifa themes).
     */
    public function maybeRenderBlocks()
    {
        // If the Sahifa-specific actions exist with attached callbacks, skip.
        if (has_action('before_custom_header_block') > 1) {
            return;
        }
        // Only render on conversion pages, front page, or English landing page
        global $wp_query;
        $number_to_convert = $wp_query->get('number_id');
        $cel_page = $wp_query->get('cel_page');
        $factorial_id = $wp_query->get('factorial_id');
        
        if (empty($number_to_convert) && !is_front_page() && !in_array($cel_page, ['convertisseur-anglais', 'calculatrice-factorielle', 'factorielle-de-x', 'calculatrice-diviseurs-pgcd', 'diviseurs-de-x']) && empty($factorial_id)) {
            return;
        }

        // Also skip for out-of-bounds factorial numbers (> 10000)
        if (!empty($factorial_id) && ((int)$factorial_id > 10000 || (int)$factorial_id < 0)) {
            return;
        }

        $this->renderBeforeBlock();
        $this->renderAfterBlock();
    }

    /**
     * Render the converter input form (before block).
     */
    public function renderBeforeBlock()
    {
        global $wp_query, $wp;

        $number_to_convert = $wp_query->get('number_id');
        if (!isset($number_to_convert) || $number_to_convert === '') {
            $number_to_convert = '';
        }

        $cel_page = $wp_query->get('cel_page');
        $factorial_id = $wp_query->get('factorial_id');
        $is_factorial = !empty($factorial_id) || $factorial_id === '0';

        // Suppress entirely for out-of-bounds pages — let the 404 render clean
        if ($is_factorial && ((int)$factorial_id > 10000 || (int)$factorial_id < 0)) {
            return;
        }

        $current_url = home_url(add_query_arg([], $wp->request ?? ''));
        $convert_to = 'fr';
        if (strpos($current_url, '/comment-on-dit/') !== false) {
            $convert_to = 'en';
        } elseif ($cel_page === 'calculatrice-factorielle' || $is_factorial) {
            $convert_to = 'factorial';
        } elseif ($cel_page === 'calculatrice-diviseurs-pgcd' || $cel_page === 'diviseurs-de-x') {
            $convert_to = 'divisors';
        }
        ?>
        <div class="container cat-box-content before_html_custom_header_block">
            <div class="e3lan e3lan-below_header" style="line-height: initial;">
                <?php
                if (is_front_page()) {
                    echo ' <h1 class="block_h1_front" style="padding-top: 10px;">Écrire les Chiffres en Lettres</h1> ';
                }
                ?>
                <p style="padding: 18px 25px 0px 25px;">
                    <span style="font-size: 16px; line-height: 2em;">
                        <?php
                        if ($cel_page === 'calculatrice-factorielle' || $is_factorial) {
                            if ($is_factorial) {
                                // Spin arrays for factorials pre-title
                                $array_a = [
                                    "Solution mathématique complète pour la factorielle de {$factorial_id}",
                                    "Calculateur de la valeur exacte de {$factorial_id}!",
                                    "Obtenir le résultat et la formule de {$factorial_id} factorielle",
                                    "Résolution étape par étape de {$factorial_id}!"
                                ];
                                $spin_index = ((int)$factorial_id * 7 + 13) % 4;
                                echo esc_html($array_a[$spin_index]);
                            } else {
                                echo "Calculer la factorielle (n!) de n'importe quel nombre instantanément";
                            }
                        } elseif ($cel_page === 'calculatrice-diviseurs-pgcd') {
                            echo "Calculer les diviseurs ou le PGCD instantanément";
                        } elseif ($cel_page === 'diviseurs-de-x') {
                            $div_id_spin = $wp_query->get('diviseur_id');
                            $array_a_div = [
                                "Solution mathématique : diviseurs d'un entier",
                                "Calculateur des diviseurs de l'entier naturel {$div_id_spin}",
                                "Trouver tous les diviseurs de {$div_id_spin}",
                                "Propriétés et liste des diviseurs de {$div_id_spin}",
                            ];
                            echo esc_html($array_a_div[((int)$div_id_spin * 7 + 13) % 4]);
                        } elseif (strpos($current_url, '/comment-on-dit/') !== false) {
                            echo ConverterHelper::convert('', 'h2');
                        } else {
                            ?>
                            Ecrire un Nombre en Lettres et
                            <a href="<?php echo esc_url(site_url('/category/macro-excel/')); ?>">
                                <span style="color:#32A0E3;text-decoration: underline;">Télécharger Gratuitement</span>
                            </a>
                            des Macros Excel
                            <?php
                        }
                        ?>
                    </span>
                </p>
                <?php
                if ($cel_page === 'calculatrice-diviseurs-pgcd' || $cel_page === 'diviseurs-de-x') {
                    $placeholder = 'Entrez un nombre (ex: 24) ou deux nombres (ex: 24, 42)';
                    $btn_text = 'CALCULER';
                } elseif ($cel_page === 'calculatrice-factorielle' || $is_factorial) {
                    $placeholder = 'Entrez un entier positif (ex: 5)';
                    $btn_text = 'CALCULER';
                } else {
                    $placeholder = 'Entrez le chiffre à convertir ici';
                    $btn_text = 'CONVERTIR';
                }
                
                $div_id_val = ($cel_page === 'diviseurs-de-x') ? $wp_query->get('diviseur_id') : '';
                $search_val = $is_factorial ? $factorial_id : ($div_id_val !== '' ? $div_id_val : $number_to_convert);
                ?>
                <p class="convert-block">
                    <input min="0" step="any" class="convert-input" type="text" name="tolettre" required=""
                        title="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($search_val); ?>"
                        placeholder="<?php echo esc_attr($placeholder); ?>" autocomplete="off">
                    <button class="convert-button" data-convert="<?php echo esc_attr($convert_to); ?>" type="button"
                        name="submitted"><i class="fa fa-refresh"></i> <?php echo esc_html($btn_text); ?></button>
                </p>
                <p style="text-align: center;text-align: center;color: red;padding: 5px;">
                    <span class="error-input"></span>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Render the conversion result heading (after block).
     */
    public function renderAfterBlock()
    {
        global $wp_query;

        // Suppress for out-of-bounds pages
        $factorial_id_check = $wp_query->get('factorial_id');
        if (!empty($factorial_id_check) && ((int)$factorial_id_check > 10000 || (int)$factorial_id_check < 0)) {
            return;
        }

        $number_to_convert = $wp_query->get('number_id');
        if (!isset($number_to_convert) || $number_to_convert === '') {
            $number_to_convert = '';
        }
        ?>
        <div class="container cat-box-content after_html_custom_header_block" style="position: relative">
            <div class="convert-block">
                <div class="e3lan e3lan-below_header" style="font-size: 12px; line-height: 2em;padding: 0px 5px">
                    <?php
                    $number_to_convert = $wp_query->get('number_id');
                    $cel_page_after = $wp_query->get('cel_page');
                    $factorial_id = $wp_query->get('factorial_id');
                    $is_factorial = !empty($factorial_id) || $factorial_id === '0';

                    if ($is_factorial && ((int)$factorial_id > 10000 || (int)$factorial_id < 0)) {
                        return; // Out of bounds
                    }

                    if (isset($number_to_convert) && $number_to_convert !== '') {
                        ?>
                        <h1><?php echo esc_html(ConverterHelper::convert($number_to_convert, 'h1')); ?></h1>
                        <?php
                    } elseif ($is_factorial) {
                        $array_b = [
                            "Quelle est la Factorielle de {$factorial_id} ? ({$factorial_id}!)",
                            "Le Calcul de la Factorielle {$factorial_id}",
                            "Valeur exacte et propriétés de {$factorial_id} Factorielle",
                            "{$factorial_id}! : Résultat et Décomposition Mathématique"
                        ];
                        $spin_index = ((int)$factorial_id * 7 + 13) % 4;
                        ?>
                        <h1><?php echo esc_html($array_b[$spin_index]); ?></h1>
                        <?php
                    } elseif ($cel_page_after === 'convertisseur-anglais') {
                        ?>
                        <h1>Convertisseur Chiffre en Lettre Anglais</h1>
                        <?php
                    } elseif ($cel_page_after === 'calculatrice-factorielle') {
                        ?>
                        <h1>Calculatrice Factorielle : Calculer la Factorielle (n!)</h1>
                        <?php
                    } elseif ($cel_page_after === 'calculatrice-diviseurs-pgcd') {
                        ?>
                        <h1>Calculateur de Diviseurs et PGCD en Ligne</h1>
                        <?php
                    } elseif ($cel_page_after === 'diviseurs-de-x') {
                        $div_id = $wp_query->get('diviseur_id');
                        $array_b_div = [
                            "Quels sont tous les diviseurs de {$div_id} ?",
                            "La liste complète des diviseurs de {$div_id}",
                            "Les diviseurs de l'entier {$div_id} (Pairs, Impairs, Premiers)",
                            "Comment trouver les diviseurs de {$div_id} ?",
                        ];
                        $spin_div = ((int)$div_id * 7 + 13) % 4;
                        ?>
                        <h1><?php echo esc_html($array_b_div[$spin_div]); ?></h1>
                        <?php
                    } elseif (is_home() || is_front_page()) {
                        ?>
                        <p style="font-size: 1.6em; font-weight: 700; line-height: 1.3; margin: 0; color: inherit;">Chiffre en Lettre - écrire les chiffres en lettres facilement</p>
                        <?php
                    } else {
                        ?>
                        <h2>Chiffre en Lettre - écrire les chiffres en lettres facilement</h2>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Shortcode: [cel_converter_form]
     * Allows placing the converter form anywhere in any theme.
     */
    public function shortcodeForm($atts)
    {
        ob_start();
        $this->renderBeforeBlock();
        return ob_get_clean();
    }

    /**
     * Shortcode: [cel_converter_heading]
     * Allows placing the conversion heading anywhere in any theme.
     */
    public function shortcodeHeading($atts)
    {
        ob_start();
        $this->renderAfterBlock();
        return ob_get_clean();
    }
}
