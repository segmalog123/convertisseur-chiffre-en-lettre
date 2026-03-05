<?php
namespace ChiffreEnLettre\Converters;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * English number-to-text conversion.
 * Ported from the original numbertolettre.php (English section).
 */
class EnglishConverter
{

    /**
     * Get the fractional place name.
     *
     * @param string $cop Decimal part string.
     * @return string
     */
    public static function f1($cop)
    {
        $len = strlen($cop);
        if ($len == 1) {
            $ch = ($cop == '1') ? 'tenth' : 'tenths';
        } elseif ($len == 2) {
            $ch = ($cop[1] == '1') ? 'hundreth' : 'hundreths';
        } elseif ($len == 3) {
            $ch = 'thousandths';
        } elseif ($len == 4) {
            $ch = 'ten-thousandths';
        } elseif ($len == 5) {
            $ch = ' hundred-thousandths';
        } elseif ($len == 6) {
            $ch = ' millionths';
        } elseif ($len == 7) {
            $ch = ' ten-millionths';
        } elseif ($len == 8) {
            $ch = ' hundred-millionths';
        } elseif ($len == 9) {
            $ch = ' billionths';
        } else {
            $ch = ' ten-billionths';
        }
        return $ch;
    }

    /**
     * Convert an integer to English words.
     *
     * @param float|int $x The number.
     * @return string
     */
    public static function fun($x)
    {
        $nwords = [
            'zero',
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
            'eleven',
            'twelve',
            'thirteen',
            'fourteen',
            'fifteen',
            'sixteen',
            'seventeen',
            'eighteen',
            'nineteen',
            'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
        ];

        if ($x < 0) {
            $w = 'minus ';
            $x = -$x;
        } else {
            $w = '';
        }

        if ($x < 21) {
            $w .= $nwords[$x];
        } elseif ($x < 100) {
            $w .= $nwords[10 * floor($x / 10)];
            $r = fmod($x, 10);
            if ($r > 0) {
                $w .= '-' . $nwords[$r];
            }
        } elseif ($x < 1000) {
            $w .= $nwords[floor($x / 100)] . ' hundred';
            $r = fmod($x, 100);
            if ($r > 0) {
                $w .= ' ' . self::fun($r);
            }
        } elseif ($x < 1000000) {
            $w .= self::fun(floor($x / 1000)) . ' thousand';
            $r = fmod($x, 1000);
            if ($r > 0) {
                $w .= ' ';
                if ($r < 100) {
                    $w .= ' ';
                }
                $w .= self::fun($r);
            }
        } else {
            $w .= self::fun(floor($x / 1000000)) . ' million';
            $r = fmod($x, 1000000);
            if ($r > 0) {
                $w .= ' ';
                if ($r < 100) {
                    $w .= ' ';
                }
                $w .= self::fun($r);
            }
        }

        return $w;
    }

    /**
     * Convert an integer string to English words, handling decimals.
     *
     * @param string $x The number string.
     * @return string
     */
    public static function convertIntegerToWords($x)
    {
        $i = 0;
        $ok = 0;
        do {
            if ($x[$i] == '0') {
                $ok = 1;
            } else {
                $ok = 0;
                break;
            }
            $i++;
        } while ($i < strlen($x));

        if ($ok == 1) {
            $w = 'zero';
        } else {
            if (!is_numeric($x)) {
                $w = 'is note mumber';
            } elseif (fmod($x, 1) != 0) {
                $pos = strpos($x, '.', 1);
                $cop = substr($x, $pos + 1, 10);
                $x = substr($x, 0, $pos);
                $w = self::fun($x) . ' and  ' . self::fun($cop) . ' ' . self::f1($cop);
            } else {
                $w = self::fun($x);
            }
        }

        return $w;
    }

    /**
     * Convert a number to English words in dollars.
     *
     * @param string $x The number string.
     * @return string
     */
    public static function convertTowordsDollar($x)
    {
        $i = 0;
        $ok = 0;
        do {
            if ($x[$i] == '0') {
                $ok = 1;
            } else {
                $ok = 0;
                break;
            }
            $i++;
        } while ($i < strlen($x));

        if ($ok == 1) {
            $w = 'zero dollar';
        } else {
            if (fmod($x, 1) != 0) {
                while ($x[strlen($x) - 1] == '0') {
                    $x = substr($x, 0, strlen($x) - 1);
                }
                $pos = strpos($x, '.', 1);
                $cop = substr($x, $pos + 1, 3);
                $x = substr($x, 0, $pos);
                if (strlen($cop) == 1) {
                    $cop = $cop . '0';
                }
                if (strlen($cop) == 3) {
                    if (intval($cop[2]) >= 5) {
                        $e = intval($cop[1]) + 1;
                        $cop[1] = $e;
                        $cop = substr($cop, 0, 2);
                    } else {
                        $cop = substr($cop, 0, 2);
                    }
                }
                $w = self::fun($x) . ' dollars  and  ' . self::fun($cop) . ' cents';
            } else {
                $w = self::fun($x) . ' dollars';
            }
        }

        return $w;
    }

    /**
     * Convert to English words in dinars.
     *
     * @param string $x The number string.
     * @return string
     */
    public static function convertTowordsDinar($x)
    {
        $i = 0;
        $ok = 0;
        do {
            if ($x[$i] == '0') {
                $ok = 1;
            } else {
                $ok = 0;
                break;
            }
            $i++;
        } while ($i < strlen($x));

        if ($ok == 1) {
            $w = 'zero euro';
        } else {
            if (fmod($x, 1) != 0) {
                while ($x[strlen($x) - 1] == '0') {
                    $x = substr($x, 0, strlen($x) - 1);
                }
                $pos = strpos($x, '.', 1);
                $cop = substr($x, $pos + 1, 3);
                $x = substr($x, 0, $pos);
                if (strlen($cop) == 1) {
                    $cop = $cop . '0';
                }
                if (strlen($cop) == 3) {
                    if (intval($cop[2]) >= 5) {
                        $e = intval($cop[1]) + 1;
                        $cop[1] = $e;
                        $cop = substr($cop, 0, 2);
                    } else {
                        $cop = substr($cop, 0, 2);
                    }
                }
                $w = self::fun($x) . ' euros  and  ' . self::fun($cop) . ' centimes';
            } else {
                $w = self::fun($x) . ' euros';
            }
        }

        return $w;
    }

    /**
     * Convert to English words in euros.
     * Note: The original code had swapped labels (euro function returns "dinar").
     * Keeping the exact same behavior for backward compatibility.
     *
     * @param string $x The number string.
     * @return string
     */
    public static function convertTowordsEuro($x)
    {
        $i = 0;
        $ok = 0;
        do {
            if ($x[$i] == '0') {
                $ok = 1;
            } else {
                $ok = 0;
                break;
            }
            $i++;
        } while ($i < strlen($x));

        if ($ok == 1) {
            $w = 'zero dinar';
        } else {
            if (fmod($x, 1) != 0) {
                while ($x[strlen($x) - 1] == '0') {
                    $x = substr($x, 0, strlen($x) - 1);
                }
                $pos = strpos($x, '.', 1);
                $cop = substr($x, $pos + 1, 3);
                $x = substr($x, 0, $pos);
                if (strlen($cop) == 1) {
                    $cop = $cop . '0';
                }
                if (strlen($cop) == 3) {
                    if (intval($cop[2]) >= 5) {
                        $e = intval($cop[1]) + 1;
                        $cop[1] = $e;
                        $cop = substr($cop, 0, 2);
                    } else {
                        $cop = substr($cop, 0, 2);
                    }
                }
                $w = self::fun($x) . ' dinars  and  ' . self::fun($cop) . ' millimes';
            } else {
                $w = self::fun($x) . ' dinars';
            }
        }

        return $w;
    }

    /**
     * Convert to English words in Canadian dollars.
     *
     * @param string $x The number string.
     * @return string
     */
    public static function convertTowordsCanadian($x)
    {
        $i = 0;
        $ok = 0;
        do {
            if ($x[$i] == '0') {
                $ok = 1;
            } else {
                $ok = 0;
                break;
            }
            $i++;
        } while ($i < strlen($x));

        if ($ok == 1) {
            $w = 'zero dollar';
        } else {
            if (fmod($x, 1) != 0) {
                while ($x[strlen($x) - 1] == '0') {
                    $x = substr($x, 0, strlen($x) - 1);
                }
                $pos = strpos($x, '.', 1);
                $cop = substr($x, $pos + 1, 3);
                $x = substr($x, 0, $pos);
                if (strlen($cop) == 1) {
                    $cop = $cop . '0';
                }
                if (strlen($cop) == 3) {
                    if (intval($cop[2]) >= 5) {
                        $e = intval($cop[1]) + 1;
                        $cop[1] = $e;
                        $cop = substr($cop, 0, 2);
                    } else {
                        $cop = substr($cop, 0, 2);
                    }
                }
                $w = self::fun($x) . ' canadian dollars  and  ' . self::fun($cop) . ' cents';
            } else {
                $w = self::fun($x) . ' canadian dollars';
            }
        }

        return $w;
    }

    /**
     * Convert to English words in pounds.
     *
     * @param string $x The number string.
     * @return string
     */
    public static function convertTowordsPounds($x)
    {
        $i = 0;
        $ok = 0;
        do {
            if ($x[$i] == '0') {
                $ok = 1;
            } else {
                $ok = 0;
                break;
            }
            $i++;
        } while ($i < strlen($x));

        if ($ok == 1) {
            $w = 'zero pounds';
        } else {
            if (!is_numeric($x)) {
                $w = '';
            } elseif (fmod($x, 1) != 0) {
                while ($x[strlen($x) - 1] == '0') {
                    $x = substr($x, 0, strlen($x) - 1);
                }
                $pos = strpos($x, '.', 1);
                $cop = substr($x, $pos + 1, 3);
                $x = substr($x, 0, $pos);
                if (strlen($cop) == 1) {
                    $cop = $cop . '0';
                }
                if (strlen($cop) == 3) {
                    if (intval($cop[2]) >= 5) {
                        $e = intval($cop[1]) + 1;
                        $cop[1] = $e;
                        $cop = substr($cop, 0, 2);
                    } else {
                        $cop = substr($cop, 0, 2);
                    }
                }
                $w = self::fun($x) . ' pounds  and  ' . self::fun($cop) . ' pence';
            } else {
                $w = self::fun($x) . ' pounds';
            }
        }
        return $w;
    }

    /**
     * Convert a number to English currency words.
     *
     * @param string     $number The number string.
     * @param int|string $to     Currency type: 0=words, 1=dollar, 2=pounds, 3=canadian, 4=euro, 5=dinar.
     * @return string
     */
    public static function convertCurrencyToWords($number, $to)
    {
        $i = 0;
        $ch = '';
        do {
            if ($number[$i] != ',') {
                $ch .= $number[$i];
            }
            $i++;
        } while ($i < strlen($number));

        $number = $ch;
        if (!is_numeric($ch)) {
            $out = 'Use a proper number format';
        } else {
            if ($to == '0') {
                $out = self::convertIntegerToWords($number);
            }
            if ($to == '1') {
                $out = self::convertTowordsDollar($number);
            }
            if ($to == '2') {
                $out = self::convertTowordsPounds($number);
            }
            if ($to == '3') {
                $out = self::convertTowordsCanadian($number);
            }
            if ($to == '4') {
                $out = self::convertTowordsEuro($number);
            }
            if ($to == '5') {
                $out = self::convertTowordsDinar($number);
            }
        }
        return $out;
    }
}
