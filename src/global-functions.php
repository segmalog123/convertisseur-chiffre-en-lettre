<?php
/**
 * Global function wrappers for backward compatibility.
 * This file is intentionally NOT namespaced so these functions
 * are available in the global scope for templates and themes.
 *
 * @package ConvertisseurChiffreEnLettre
 */

if (!defined('ABSPATH')) {
    exit;
}

// =========================================================================
// CONVERTER FUNCTIONS
// =========================================================================

if (!function_exists('funcConvert')) {
    function funcConvert($number_to_convert, $action = '', $type = '')
    {
        return \ChiffreEnLettre\Converters\ConverterHelper::convert($number_to_convert, $action, $type);
    }
}

if (!function_exists('funcListNumber')) {
    function funcListNumber($number_to_convert)
    {
        return \ChiffreEnLettre\Converters\ConverterHelper::listSimilarNumbers($number_to_convert);
    }
}

if (!function_exists('funcNumBetween')) {
    function funcNumBetween($number_to_convert)
    {
        return \ChiffreEnLettre\Converters\ConverterHelper::numberBetween($number_to_convert);
    }
}

if (!function_exists('funcPercent')) {
    function funcPercent($number_to_convert)
    {
        return \ChiffreEnLettre\Converters\ConverterHelper::percent($number_to_convert);
    }
}

if (!function_exists('from_zero_to')) {
    function from_zero_to($n)
    {
        return \ChiffreEnLettre\Converters\FrenchConverter::fromZeroTo($n);
    }
}



if (!function_exists('enChiffre')) {
    function enChiffre($nombre)
    {
        return \ChiffreEnLettre\Converters\FrenchConverter::enChiffre($nombre);
    }
}

if (!function_exists('enDevise')) {
    function enDevise($nombre, $devise)
    {
        return \ChiffreEnLettre\Converters\FrenchConverter::enDevise($nombre, $devise);
    }
}

if (!function_exists('enlettres')) {
    function enlettres($nombre, $options = null, $separateur = null)
    {
        return \ChiffreEnLettre\Converters\FrenchConverter::enlettres($nombre, $options, $separateur);
    }
}

if (!function_exists('convertCurrencyToWords')) {
    function convertCurrencyToWords($number, $to)
    {
        return \ChiffreEnLettre\Converters\EnglishConverter::convertCurrencyToWords($number, $to);
    }
}

if (!function_exists('convertIntegerToWords')) {
    function convertIntegerToWords($x)
    {
        return \ChiffreEnLettre\Converters\EnglishConverter::convertIntegerToWords($x);
    }
}

// =========================================================================
// BREADCRUMB FUNCTION
// =========================================================================

if (!function_exists('chiffre_breadcrumbs')) {
    function chiffre_breadcrumbs()
    {
        \ChiffreEnLettre\BreadcrumbController::render();
    }
}
