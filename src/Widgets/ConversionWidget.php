<?php
namespace ChiffreEnLettre\Widgets;

use WP_Widget;
use ChiffreEnLettre\NumberVipList;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Widget for the Convertisseur Chiffre en Lettre plugin.
 * Displays "Similar Numbers" with distinct French and English sections.
 */
class ConversionWidget extends WP_Widget
{
    /**
     * Set up the widget name and description.
     */
    public function __construct()
    {
        $widget_options = array(
            'classname' => 'cel_conversion_widget',
            'description' => 'Affiche des liens vers d\'autres nombres en français et/ou en anglais.'
        );
        parent::__construct(
            'cel_conversion_widget',
            'Convertisseur : Nombres Similaires',
            $widget_options
        );
    }

    /**
     * Frontend display of the widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        $show_french = !empty($instance['show_french']) ? $instance['show_french'] : false;
        $title_french = !empty($instance['title_french']) ? $instance['title_french'] : 'Nombres similaires à convertir';
        $french_numbers_str = !empty($instance['french_numbers']) ? $instance['french_numbers'] : '';

        $show_english = !empty($instance['show_english']) ? $instance['show_english'] : false;
        $title_english = !empty($instance['title_english']) ? $instance['title_english'] : 'Nombres similaires en anglais';
        $english_numbers_str = !empty($instance['english_numbers']) ? $instance['english_numbers'] : '';

        $show_factorial = !empty($instance['show_factorial']) ? $instance['show_factorial'] : false;
        $title_factorial = !empty($instance['title_factorial']) ? $instance['title_factorial'] : 'Calculer les Factorielles Proches';
        $factorial_numbers_str = !empty($instance['factorial_numbers']) ? $instance['factorial_numbers'] : '';

        // Do not display if none are checked
        if (!$show_french && !$show_english && !$show_factorial) {
            return;
        }

        echo $args['before_widget'];

        // Add some basic CSS for the widget pills if it's not loaded by the theme
        echo '<style>
            .cel-widget-pills { list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 8px; }
            .cel-widget-pills li { margin: 0; }
            .cel-widget-pills a { 
                display: inline-block; padding: 6px 14px; background: #e8f8f0; 
                border: 1px solid #b6e8c8; border-radius: 20px; color: #1a7a40; 
                text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; 
            }
            .cel-widget-pills a:hover { background: #d0f0e0; color: #12552d; border-color: #8cd6a9; }
            .cel-widget-section { margin-bottom: 25px; }
            .cel-widget-title { color: #1a7a40; font-size: 18px; font-weight: 700; margin-bottom: 15px; border-bottom: 1px solid #e8f8f0; padding-bottom: 8px; }
        </style>';

        // Get 5 smart "VIP" related numbers (we use 20 as base if we are not on a conversion page,
        // but try to get current number if we are)
        $current_number = get_query_var('number_id');
        if (empty($current_number)) {
            $current_number = '20'; // default fallback
        }

        // Clean current number for list generation
        $cleaned_number = str_replace(array(',', '.'), '', $current_number);

        // -- FRENCH SECTION --
        if ($show_french) {
            $similar_numbers_fr = [];
            if (!empty($french_numbers_str)) {
                $custom_fr = array_filter(array_map('trim', explode(',', $french_numbers_str)));
                if (!empty($custom_fr)) {
                    shuffle($custom_fr);
                    $similar_numbers_fr = array_slice($custom_fr, 0, 5);
                }
            }
            if (empty($similar_numbers_fr)) {
                $similar_numbers_fr = array_slice(NumberVipList::getSmartRelated($cleaned_number), 0, 5);
            }

            $anchor_texts_fr = [
                'Convertir %s € en lettres',
                'Le montant %s en toutes lettres',
                '%s dinar algérien en lettre',
                'Écrire %s euro en lettres',
                'Orthographe du chiffre %s',
                'Traduction pour le numéro %s',
                'numero %s en lettre dinars',
                'Comment écrire le montant %s'
            ];

            echo '<div class="cel-widget-section">';
            echo '<h4 class="cel-widget-title">' . esc_html($title_french) . '</h4>';
            echo '<ul class="cel-widget-pills">';
            foreach ($similar_numbers_fr as $index => $n) {
                // vary the anchor text
                $anchor_phrase = sprintf($anchor_texts_fr[$index % count($anchor_texts_fr)], $n);
                $url = esc_url(site_url('/ecrire/' . $n . '-en-lettre/'));
                echo '<li><a href="' . $url . '">' . esc_html($anchor_phrase) . '</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // -- ENGLISH SECTION --
        if ($show_english) {
            $anchor_texts_en = [
                '%s en anglais',
                'Comment on dit %s en anglais',
                '%s anglais',
                'Comment dit-on %s en anglais',
                'Comment dire %s en anglais',
                '%s en anglais en lettre',
                'Comment écrire %s en lettre anglais',
                'Traduire %s en anglais',
                '%s%% en anglais'
            ];

            echo '<div class="cel-widget-section">';
            echo '<h4 class="cel-widget-title">' . esc_html($title_english) . '</h4>';
            echo '<ul class="cel-widget-pills">';

            $similar_numbers_en = [];
            if (!empty($english_numbers_str)) {
                $custom_en = array_filter(array_map('trim', explode(',', $english_numbers_str)));
                if (!empty($custom_en)) {
                    shuffle($custom_en);
                    $similar_numbers_en = array_slice($custom_en, 0, 5);
                }
            }
            if (empty($similar_numbers_en)) {
                $similar_numbers_en = array_slice(NumberVipList::getSmartRelated((int) $cleaned_number + 1), 0, 5);
            }

            foreach ($similar_numbers_en as $index => $n) {
                // Handle percentage special case from anchors
                if (strpos($anchor_texts_en[$index % count($anchor_texts_en)], '%%') !== false) {
                    $anchor_phrase = sprintf(str_replace('%%', '%%', $anchor_texts_en[$index % count($anchor_texts_en)]), $n);
                } else {
                    $anchor_phrase = sprintf($anchor_texts_en[$index % count($anchor_texts_en)], $n);
                }

                $url = esc_url(site_url('/comment-on-dit/' . $n . '-en-anglais/'));
                echo '<li><a href="' . $url . '">' . esc_html($anchor_phrase) . '</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // -- FACTORIAL SECTION --
        if ($show_factorial) {
            $anchor_texts_fac = [
                'Factorielle de %s',
                'Factorielle %s',
                '%s factorielle',
                '%s!',
                'Calcul factorielle %s'
            ];

            echo '<div class="cel-widget-section">';
            echo '<h4 class="cel-widget-title">' . esc_html($title_factorial) . '</h4>';
            echo '<ul class="cel-widget-pills">';

            $similar_numbers_fac = [];
            if (!empty($factorial_numbers_str)) {
                $custom_fac = array_filter(array_map('trim', explode(',', $factorial_numbers_str)));
                if (!empty($custom_fac)) {
                    shuffle($custom_fac);
                    $similar_numbers_fac = array_slice($custom_fac, 0, 5);
                }
            }
            if (empty($similar_numbers_fac)) {
                $similar_numbers_fac = array_slice(NumberVipList::getSmartRelated((int) $cleaned_number), 0, 5);
            }

            foreach ($similar_numbers_fac as $index => $n) {
                // Limit factorial to 10000 to avoid 404s
                if ((int)$n >= 0 && (int)$n <= 10000) {
                    $anchor_phrase = sprintf($anchor_texts_fac[$index % count($anchor_texts_fac)], $n);
                    $url = esc_url(site_url('/factorielle-de-' . $n . '/'));
                    echo '<li><a href="' . $url . '">' . esc_html($anchor_phrase) . '</a></li>';
                }
            }
            echo '</ul>';
            echo '</div>';
        }

        echo $args['after_widget'];
    }

    /**
     * Backend widget form.
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $show_french = isset($instance['show_french']) ? (bool) $instance['show_french'] : true;
        $title_french = !empty($instance['title_french']) ? $instance['title_french'] : 'Nombres similaires à convertir';
        $french_numbers = !empty($instance['french_numbers']) ? $instance['french_numbers'] : '';

        $show_english = isset($instance['show_english']) ? (bool) $instance['show_english'] : true;
        $title_english = !empty($instance['title_english']) ? $instance['title_english'] : 'Nombres similaires en anglais';
        $english_numbers = !empty($instance['english_numbers']) ? $instance['english_numbers'] : '';

        $show_factorial = isset($instance['show_factorial']) ? (bool) $instance['show_factorial'] : true;
        $title_factorial = !empty($instance['title_factorial']) ? $instance['title_factorial'] : 'Calculer les Factorielles Proches';
        $factorial_numbers = !empty($instance['factorial_numbers']) ? $instance['factorial_numbers'] : '';
        ?>

        <p><strong>Section Française</strong></p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_french); ?>
                id="<?php echo esc_attr($this->get_field_id('show_french')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_french')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_french')); ?>">Afficher les liens français</label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title_french')); ?>">Titre (Français) :</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title_french')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title_french')); ?>" type="text"
                value="<?php echo esc_attr($title_french); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('french_numbers')); ?>">Nombres Français (séparés par des
                virgules) :</label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr($this->get_field_id('french_numbers')); ?>"
                name="<?php echo esc_attr($this->get_field_name('french_numbers')); ?>"><?php echo esc_textarea($french_numbers); ?></textarea>
            <small>Laissez vide pour générer automatiquement les nombres.</small>
        </p>

        <hr style="margin:15px 0;">

        <p><strong>Section Anglaise</strong></p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_english); ?>
                id="<?php echo esc_attr($this->get_field_id('show_english')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_english')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_english')); ?>">Afficher les liens anglais</label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title_english')); ?>">Titre (Anglais) :</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title_english')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title_english')); ?>" type="text"
                value="<?php echo esc_attr($title_english); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('english_numbers')); ?>">Nombres Anglais (séparés par des
                virgules) :</label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr($this->get_field_id('english_numbers')); ?>"
                name="<?php echo esc_attr($this->get_field_name('english_numbers')); ?>"><?php echo esc_textarea($english_numbers); ?></textarea>
            <small>Laissez vide pour générer automatiquement les nombres.</small>
        </p>

        <hr style="margin:15px 0;">

        <p><strong>Section Factorielle</strong></p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_factorial); ?>
                id="<?php echo esc_attr($this->get_field_id('show_factorial')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_factorial')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_factorial')); ?>">Afficher les liens factoriels</label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title_factorial')); ?>">Titre (Factorielle) :</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title_factorial')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title_factorial')); ?>" type="text"
                value="<?php echo esc_attr($title_factorial); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('factorial_numbers')); ?>">Nombres Factoriels (séparés par des
                virgules) :</label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr($this->get_field_id('factorial_numbers')); ?>"
                name="<?php echo esc_attr($this->get_field_name('factorial_numbers')); ?>"><?php echo esc_textarea($factorial_numbers); ?></textarea>
            <small>Laissez vide pour générer automatiquement les nombres. Nb > 10000 sont ignorés.</small>
        </p>

        <p><em>Le widget affichera automatiquement 5 liens VIP générés dynamiquement pour chaque langue cochée.</em></p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['show_french'] = !empty($new_instance['show_french']) ? 1 : 0;
        $instance['title_french'] = (!empty($new_instance['title_french'])) ? sanitize_text_field($new_instance['title_french']) : '';
        $instance['french_numbers'] = (!empty($new_instance['french_numbers'])) ? sanitize_textarea_field($new_instance['french_numbers']) : '';

        $instance['show_english'] = !empty($new_instance['show_english']) ? 1 : 0;
        $instance['title_english'] = (!empty($new_instance['title_english'])) ? sanitize_text_field($new_instance['title_english']) : '';
        $instance['english_numbers'] = (!empty($new_instance['english_numbers'])) ? sanitize_textarea_field($new_instance['english_numbers']) : '';

        $instance['show_factorial'] = !empty($new_instance['show_factorial']) ? 1 : 0;
        $instance['title_factorial'] = (!empty($new_instance['title_factorial'])) ? sanitize_text_field($new_instance['title_factorial']) : '';
        $instance['factorial_numbers'] = (!empty($new_instance['factorial_numbers'])) ? sanitize_textarea_field($new_instance['factorial_numbers']) : '';

        return $instance;
    }
}
