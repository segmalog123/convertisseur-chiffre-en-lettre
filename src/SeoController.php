<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

use ChiffreEnLettre\Converters\ConverterHelper;

/**
 * Hooks into Yoast SEO to dynamically inject SEO data
 * for the virtual conversion pages.
 */
class SeoController
{

    /**
     * Hook into Yoast filters.
     */
    public function init()
    {
        add_filter('wpseo_title', [$this, 'filterTitle'], 10, 1);
        add_filter('wpseo_metadesc', [$this, 'filterMetaDesc'], 10, 1);
        add_filter('wpseo_opengraph_title', [$this, 'filterOgTitle'], 10, 1);
        add_filter('wpseo_opengraph_url', [$this, 'filterOgUrl'], 10, 1);
        add_filter('wpseo_opengraph_type', [$this, 'filterOgType'], 10, 1);
        add_filter('wpseo_opengraph_image', [$this, 'filterOgImage'], 10, 1);
        add_filter('wpseo_robots', [$this, 'filterRobots'], 10, 1);

        // Yoast's Canonical_Presenter skips virtual pages entirely,
        // so wpseo_canonical never fires. Two-part fix:
        // 1. Tell Yoast not to output its own canonical (return false)
        // 2. Output our own <link rel="canonical"> via wp_head
        add_filter('wpseo_canonical', [$this, 'disableYoastCanonical'], 10, 1);
        add_action('wp_head', [$this, 'outputCanonical'], 2);
    }

    /**
     * Get the current number_id from the query.
     *
     * @return string|false
     */
    private function getNumberId()
    {
        global $wp_query;
        $number_id = $wp_query->get('number_id');
        if (isset($number_id) && $number_id !== '') {
            return $number_id;
        }
        return false;
    }

    /**
     * Check if current page is the English landing page.
     *
     * @return bool
     */
    private function isEnglishLandingPage()
    {
        global $wp_query;
        return $wp_query->get('cel_page') === 'convertisseur-anglais';
    }

    /**
     * Check if current page is the Factorial Calculator landing page.
     *
     * @return bool
     */
    private function isFactorialPage()
    {
        global $wp_query;
        return $wp_query->get('cel_page') === 'calculatrice-factorielle';
    }

    /**
     * Get the current factorial_id from the query.
     *
     * @return string|false
     */
    private function getFactorialId()
    {
        global $wp_query;
        if ($wp_query->get('cel_page') === 'factorielle-de-x') {
            $x = $wp_query->get('factorial_id');
            if ($x !== '' && is_numeric($x) && intval($x) <= 10000) {
                return $x;
            }
        }
        return false;
    }

    /**
     * Filter the page title.
     *
     * @param string $title Original title.
     * @return string
     */
    public function filterTitle($title)
    {
        if ($this->isEnglishLandingPage()) {
            return 'Convertisseur Chiffre en Lettre Anglais - Convertir Nombre en Lettres';
        }
        if ($this->isFactorialPage()) {
            return 'Calculatrice Factorielle : Calcul et Formule de n! en Ligne';
        }
        $fact_x = $this->getFactorialId();
        if ($fact_x !== false) {
            return "Factorielle de {$fact_x} ({$fact_x}!) | Calcul, Résultat Exact et Formule";
        }
        $number = $this->getNumberId();
        if ($number !== false) {
            return ConverterHelper::convert($number, 'title');
        }
        return $title;
    }

    /**
     * Filter the meta description.
     *
     * @param string $desc Original description.
     * @return string
     */
    public function filterMetaDesc($desc)
    {
        if ($this->isEnglishLandingPage()) {
            return 'Convertissez instantanément vos chiffres en lettres anglaises. Outil gratuit pour écrire les nombres en anglais, idéal pour les chèques, Excel et documents officiels.';
        }
        if ($this->isFactorialPage()) {
            return 'Utilisez notre calculatrice factorielle gratuite pour trouver le factoriel d\'un nombre instantanément. Découvrez la formule de n!, la factorielle de 0, et la formule de Stirling.';
        }
        $fact_x = $this->getFactorialId();
        if ($fact_x !== false) {
            return "Quelle est la factorielle de {$fact_x} ? Obtenez le résultat exact de {$fact_x}!, la décomposition du calcul, le nombre de zéros finaux et les permutations possibles.";
        }
        $number = $this->getNumberId();
        if ($number !== false) {
            return ConverterHelper::convert($number, 'desc');
        }
        return $desc;
    }

    /**
     * Disable Yoast's canonical for our virtual pages.
     * Return false so Yoast doesn't output a <link rel="canonical">.
     * We handle it ourselves in outputCanonical().
     *
     * @param string $canonical Original canonical.
     * @return string|false
     */
    public function disableYoastCanonical($canonical)
    {
        $fact_x = $this->getFactorialId();
        if ($fact_x !== false) {
            return false;
        }
        $number = $this->getNumberId();
        if ($number !== false) {
            return false;
        }
        return $canonical;
    }

    /**
     * Output <link rel="canonical"> directly in <head>.
     * Yoast's Canonical_Presenter silently skips virtual pages,
     * so we must output it ourselves.
     */
    public function outputCanonical()
    {
        if ($this->isEnglishLandingPage()) {
            echo '<link rel="canonical" href="' . esc_url(home_url('/convertisseur-anglais/')) . '" />' . "\n";
            return;
        }
        if ($this->isFactorialPage()) {
            echo '<link rel="canonical" href="' . esc_url(home_url('/calculatrice-factorielle/')) . '" />' . "\n";
            return;
        }
        $fact_x = $this->getFactorialId();
        if ($fact_x !== false) {
            $url = home_url("/factorielle-de-{$fact_x}/");
            echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";
            return;
        }
        $number = $this->getNumberId();
        if ($number !== false) {
            $url = ConverterHelper::convert($number, 'url');
            echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";
        }
    }

    /**
     * Filter the OG title.
     *
     * @param string $title Original OG title.
     * @return string
     */
    public function filterOgTitle($title)
    {
        $number = $this->getNumberId();
        if ($number !== false) {
            return ConverterHelper::convert($number, 'title');
        }
        return $title;
    }

    /**
     * Filter the OG URL.
     *
     * @param string $url Original OG URL.
     * @return string
     */
    public function filterOgUrl($url)
    {
        $number = $this->getNumberId();
        if ($number !== false) {
            return ConverterHelper::convert($number, 'url');
        }
        return $url;
    }

    /**
     * Filter the OG type to 'article' for conversion pages.
     *
     * @param string $type Original OG type.
     * @return string
     */
    public function filterOgType($type)
    {
        $number = $this->getNumberId();
        if ($number !== false) {
            return 'article';
        }
        return $type;
    }

    /**
     * Filter the OG image.
     *
     * @param string $image Original OG image.
     * @return string
     */
    public function filterOgImage($image)
    {
        $number = $this->getNumberId();
        if ($number !== false) {
            return home_url('/wp-content/uploads/2020/04/chiffreenlettredefault.png');
        }
        return $image;
    }

    /**
     * Filter robots meta to ensure indexing.
     *
     * @param string $robots Original robots string.
     * @return string
     */
    public function filterRobots($robots)
    {
        if ($this->isEnglishLandingPage() || $this->isFactorialPage()) {
            return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        }
        $fact_x = $this->getFactorialId();
        if ($fact_x !== false) {
            $x = intval($fact_x);
            // IF X <= 200 : index
            // IF X > 200 AND X % 50 == 0 : index
            // Else : noindex, follow
            if ($x <= 200 || ($x > 200 && $x % 50 === 0)) {
                return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
            } else {
                return 'noindex, follow';
            }
        }
        $number = $this->getNumberId();
        if ($number !== false) {
            return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        }
        return $robots;
    }
}
