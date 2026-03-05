<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

use ChiffreEnLettre\Converters\ConverterHelper;

/**
 * Custom breadcrumb rendering for conversion pages.
 * Fully self-contained — no dependency on any parent theme.
 */
class BreadcrumbController
{

    /**
     * Hook into WordPress.
     */
    public function init()
    {
        // Nothing to hook here — render() is called directly from templates
        // via the global function chiffre_breadcrumbs().
    }

    /**
     * Render breadcrumbs for conversion pages.
     * Standalone implementation — works with any WordPress theme.
     */
    public static function render()
    {
        global $wp_query;

        $delimiter = '<span class="delimiter">&raquo;</span>';
        $home_text = 'Home';
        $before = '<span class="current">';
        $after = '</span>';

        if (!is_home() && !is_front_page() || is_paged()) {

            $home_url = esc_url(home_url('/'));
            $breadcrumbs = [];

            // Home breadcrumb
            $breadcrumbs[] = [
                'url' => $home_url,
                'name' => $home_text,
            ];

            // Conversion page breadcrumb
            $number_to_convert = $wp_query->get('number_id');
            if (isset($number_to_convert) && $number_to_convert !== '') {
                $breadcrumbs[] = [
                    'name' => ConverterHelper::convert($number_to_convert, 'bread'),
                ];
            }

            if (!empty($breadcrumbs)) {
                $counter = 0;
                $item_list_elements = [];
                $breadcrumbs_schema = [
                    '@context' => 'http://schema.org',
                    '@type' => 'BreadcrumbList',
                    '@id' => '#Breadcrumb',
                ];

                echo '<nav id="crumbs">';

                foreach ($breadcrumbs as $item) {
                    $counter++;

                    if (!empty($item['url'])) {
                        echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['name']) . '</a>' . $delimiter;
                    } else {
                        echo $before . esc_html($item['name']) . $after;

                        global $wp;
                        $item['url'] = esc_url(home_url(add_query_arg([], $wp->request)));
                    }

                    $item_list_elements[] = [
                        '@type' => 'ListItem',
                        'position' => $counter,
                        'item' => [
                            'name' => $item['name'],
                            '@id' => $item['url'],
                        ],
                    ];
                }

                echo '</nav>';

                // Output breadcrumb JSON-LD schema
                $latest_element = array_pop($item_list_elements);

                if (!empty($item_list_elements) && is_array($item_list_elements)) {
                    $breadcrumbs_schema['itemListElement'] = $item_list_elements;
                    echo '<script type="application/ld+json">' . wp_json_encode($breadcrumbs_schema) . '</script>';
                }
            }
        }

        wp_reset_postdata();
    }
}
