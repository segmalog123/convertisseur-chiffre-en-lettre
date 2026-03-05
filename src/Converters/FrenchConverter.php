<?php
namespace ChiffreEnLettre\Converters;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * French number-to-text conversion.
 * Ported from the original numbertolettre.php.
 */
class FrenchConverter
{

    /**
     * Configuration array (replaces the global $NEL).
     *
     * @var array
     */
    private static $NEL = null;

    /**
     * Initialize the configuration array.
     */
    private static function initConfig()
    {
        if (self::$NEL !== null) {
            return;
        }

        self::$NEL = [
            '1-99' => [
                // 0-19
                '',
                'un',
                'deux',
                'trois',
                'quatre',
                'cinq',
                'six',
                'sept',
                'huit',
                'neuf',
                'dix',
                'onze',
                'douze',
                'treize',
                'quatorze',
                'quinze',
                'seize',
                'dix-sept',
                'dix-huit',
                'dix-neuf',
                // 20-29
                'vingt',
                'vingt et un',
                'vingt-deux',
                'vingt-trois',
                'vingt-quatre',
                'vingt-cinq',
                'vingt-six',
                'vingt-sept',
                'vingt-huit',
                'vingt-neuf',
                // 30-39
                'trente',
                'trente et un',
                'trente-deux',
                'trente-trois',
                'trente-quatre',
                'trente-cinq',
                'trente-six',
                'trente-sept',
                'trente-huit',
                'trente-neuf',
                // 40-49
                'quarante',
                'quarante et un',
                'quarante-deux',
                'quarante-trois',
                'quarante-quatre',
                'quarante-cinq',
                'quarante-six',
                'quarante-sept',
                'quarante-huit',
                'quarante-neuf',
                // 50-59
                'cinquante',
                'cinquante et un',
                'cinquante-deux',
                'cinquante-trois',
                'cinquante-quatre',
                'cinquante-cinq',
                'cinquante-six',
                'cinquante-sept',
                'cinquante-huit',
                'cinquante-neuf',
                // 60-69
                'soixante',
                'soixante et un',
                'soixante-deux',
                'soixante-trois',
                'soixante-quatre',
                'soixante-cinq',
                'soixante-six',
                'soixante-sept',
                'soixante-huit',
                'soixante-neuf',
                // 70-79
                'septante',
                'septante et un',
                'septante-deux',
                'septante-trois',
                'septante-quatre',
                'septante-cinq',
                'septante-six',
                'septante-sept',
                'septante-huit',
                'septante-neuf',
                // 80-89
                'huitante',
                'huitante et un',
                'huitante-deux',
                'huitante-trois',
                'huitante-quatre',
                'huitante-cinq',
                'huitante-six',
                'huitante-sept',
                'huitante-huit',
                'huitante-neuf',
                // 90-99
                'nonante',
                'nonante et un',
                'nonante-deux',
                'nonante-trois',
                'nonante-quatre',
                'nonante-cinq',
                'nonante-six',
                'nonante-sept',
                'nonante-huit',
                'nonante-neuf',
            ],

            'illi' => ['', 'm', 'b', 'tr', 'quatr', 'quint', 'sext'],
            'maxilli' => 0,
            'de_maxillions' => '',

            'septante' => false,
            'huitante' => false,
            'nonante' => false,
            'zillions' => false,
            'zilliard' => 1,
            'rectif' => false,
            'ordinal' => false,

            'separateur' => ' ',
        ];

        self::$NEL['maxilli'] = count(self::$NEL['illi']) - 1;
        self::$NEL['de_maxillions'] = " de " . self::$NEL['illi'][self::$NEL['maxilli']] . "illions";
    }

    /**
     * Convert a group of 3 digits to French text.
     *
     * @param int $par3 A number between 0 and 999.
     * @return string
     */
    private static function enlettresPar3($par3)
    {
        self::initConfig();

        if ($par3 == 0) {
            return '';
        }

        $centaine = floor($par3 / 100);
        $par2 = $par3 % 100;
        $dizaine = floor($par2 / 10);

        $nom_par2 = null;
        switch ($dizaine) {
            case 7:
                if (self::$NEL['septante'] === false) {
                    if ($par2 == 71) {
                        $nom_par2 = 'soixante et onze';
                    } else {
                        $nom_par2 = 'soixante-' . self::$NEL['1-99'][$par2 - 60];
                    }
                }
                break;
            case 8:
                if (self::$NEL['huitante'] === false) {
                    if ($par2 == 80) {
                        $nom_par2 = 'quatre-vingts';
                    } else {
                        $nom_par2 = 'quatre-vingt-' . self::$NEL['1-99'][$par2 - 80];
                    }
                }
                break;
            case 9:
                if (self::$NEL['nonante'] === false) {
                    $nom_par2 = 'quatre-vingt-' . self::$NEL['1-99'][$par2 - 80];
                }
                break;
        }

        if ($nom_par2 === null) {
            $nom_par2 = self::$NEL['1-99'][$par2];
            if (($dizaine == 8) && (self::$NEL['huitante'] === 'octante')) {
                $nom_par2 = str_replace('huitante', 'octante', $nom_par2);
            }
        }

        switch ($centaine) {
            case 0:
                return $nom_par2;
            case 1:
                return rtrim("cent {$nom_par2}");
        }

        $nom_centaine = self::$NEL['1-99'][$centaine];
        if ($par2 == 0) {
            return "{$nom_centaine} cents";
        }
        return "{$nom_centaine} cent {$nom_par2}";
    }

    /**
     * Generate the zillion name for a given index.
     *
     * @param int $idx Index.
     * @return string
     */
    private static function enlettresZilli($idx)
    {
        static $petit = [
        'n',
        'm',
        'b',
        'tr',
        'quatr',
        'quint',
        'sext',
        'sept',
        'oct',
        'non',
        ];
        static $unite = [
        '<',
        'un<',
        'duo<',
        'tre<sé',
        'quattuor<',
        'quin<',
        'se<xsé',
        'septe<mné',
        'octo<',
        'nove<mné',
        ];
        static $dizaine = [
        '',
        'né>déci<',
        'ms>viginti<',
        'ns>triginta<',
        'ns>quadraginta<',
        'ns>quinquaginta<',
        'né>sexaginta<',
        'né>septuaginta<',
        'mxs>octoginta<',
        'é>nonaginta<',
        ];
        static $centaine = [
        '>',
        'nxs>cent',
        'né>ducent',
        'ns>trécent',
        'ns>quadringent',
        'ns>quingent',
        'né>sescent',
        'né>septingent',
        'mxs>octingent',
        'é>nongent',
        ];

        static $recherche = [
        '/<[a-zé]*?([a-zé])[a-zé]*\\1[a-zé]*>/',
        '/<[a-zé]*>/',
        '/eé/',
        '/[ai]illi/',
        ];
        static $remplace = [
        '\\1',
        '',
        'é',
        'illi',
        ];

        $nom = '';
        while ($idx > 0) {
            $p = $idx % 1000;
            $idx = floor($idx / 1000);

            if ($p < 10) {
                $nom = $petit[$p] . 'illi' . $nom;
            } else {
                $nom = $unite[$p % 10] . $dizaine[floor($p / 10) % 10]
                    . $centaine[floor($p / 100)] . 'illi' . $nom;
            }
        }
        return preg_replace($recherche, $remplace, $nom);
    }

    /**
     * Get the -illions name for a given index.
     *
     * @param int $idx Index.
     * @return string
     */
    private static function enlettresIllions($idx)
    {
        self::initConfig();

        if ($idx == 0) {
            return '';
        }

        if (self::$NEL['zillions']) {
            return self::enlettresZilli($idx) . 'ons';
        }

        $suffixe = '';
        while ($idx > self::$NEL['maxilli']) {
            $idx -= self::$NEL['maxilli'];
            $suffixe .= self::$NEL['de_maxillions'];
        }
        return self::$NEL['illi'][$idx] . "illions{$suffixe}";
    }

    /**
     * Check if illiards should be used for a given index.
     *
     * @param int $idx Index.
     * @return bool
     */
    private static function enlettresAvecIlliards($idx)
    {
        self::initConfig();

        if ($idx == 0) {
            return false;
        }
        switch (self::$NEL['zilliard']) {
            case 0:
                return false;
            case 2:
                return true;
        }
        return ($idx == 1);
    }

    /**
     * Convert a number to French text.
     *
     * @param string      $nombre    The number as a string.
     * @param int|null    $options   Options bitmask (unused in default config).
     * @param string|null $separateur Separator string.
     * @return string
     */
    public static function enlettres($nombre, $options = null, $separateur = null)
    {
        self::initConfig();

        if ($options !== null || $separateur !== null) {
            $NELsave = self::$NEL;
            // For simplicity we don't handle options bitmask here
            // as the site uses default config
            $nom = self::enlettres($nombre);
            self::$NEL = $NELsave;
            return $nom;
        }

        $nombre = preg_replace('/[^0-9]/', '', $nombre);
        $nombre = ltrim($nombre, '0');

        if ($nombre == '') {
            if (self::$NEL['ordinal'] === 'nieme') {
                return 'zéroïème';
            }
            return 'zéro';
        }

        $table_noms = [];
        for ($idx = 0; $nombre != ''; $idx++) {
            $par6 = (int) ((strlen($nombre) < 6) ? $nombre : substr($nombre, -6));
            $nombre = substr($nombre, 0, -6);

            if ($par6 == 0) {
                continue;
            }

            $nom_par3_sup = self::enlettresPar3(floor($par6 / 1000));
            $nom_par3_inf = self::enlettresPar3($par6 % 1000);

            $illions = self::enlettresIllions($idx);
            if (self::enlettresAvecIlliards($idx)) {
                if ($nom_par3_inf != '') {
                    $table_noms[$illions] = $nom_par3_inf;
                }
                if ($nom_par3_sup != '') {
                    $illiards = preg_replace('/illion/', 'illiard', $illions, 1);
                    $table_noms[$illiards] = $nom_par3_sup;
                }
            } else {
                switch ($nom_par3_sup) {
                    case '':
                        $nom_par6 = $nom_par3_inf;
                        break;
                    case 'un':
                        $nom_par6 = rtrim("mille {$nom_par3_inf}");
                        break;
                    default:
                        $nom_par3_sup = preg_replace('/(vingt|cent)s/', '\\1', $nom_par3_sup);
                        $nom_par6 = rtrim("{$nom_par3_sup} mille {$nom_par3_inf}");
                        break;
                }
                $table_noms[$illions] = $nom_par6;
            }
        }

        $nom_enlettres = '';
        foreach ($table_noms as $nom => $nombre) {
            if (self::$NEL['rectif']) {
                $nombre = str_replace(' ', '-', $nombre);
            }

            $nom = rtrim("{$nombre} {$nom}");
            if ($nombre == 'un') {
                $nom = preg_replace('/(illion|illiard)s/', '\\1', $nom, 1);
            }

            if ($nom_enlettres == '') {
                $nom_enlettres = $nom;
            } else {
                $nom_enlettres = $nom . self::$NEL['separateur'] . $nom_enlettres;
            }
        }

        if (self::$NEL['ordinal'] === false) {
            return $nom_enlettres;
        }

        $nom_enlettres = preg_replace('/(cent|vingt|illion|illiard)s/', '\\1', $nom_enlettres);

        if (self::$NEL['ordinal'] !== 'nieme') {
            return $nom_enlettres;
        }

        if ($nom_enlettres === 'un') {
            return 'premier';
        }

        switch (substr($nom_enlettres, -1)) {
            case 'e':
                return substr($nom_enlettres, 0, -1) . 'ième';
            case 'f':
                return substr($nom_enlettres, 0, -1) . 'vième';
            case 'q':
                return $nom_enlettres . 'uième';
        }

        return $nom_enlettres . 'ième';
    }

    /**
     * Convert a number (possibly with comma decimal) to French text.
     *
     * @param string $nombre The number string.
     * @return array ['final_number_lettre' => string, 'number_int' => string]
     */
    public static function enChiffre($nombre)
    {
        $nombre = preg_replace('/[^0-9,.]/', '', $nombre);
        $nombre = str_replace('.', ',', $nombre);
        $array_data = [];
        $final_number = '';

        if (strpos($nombre, ',') !== false) {
            $split_number = explode(',', $nombre);

            for ($i = 1; $i < count($split_number); $i++) {
                $final_number .= $split_number[$i];
            }

            $number_int = $split_number[0] . ',' . $final_number;
            $split_number_int = explode(',', $number_int);

            $nbr1 = self::enlettres($split_number_int[0]);
            $nbr2 = self::enlettres($split_number_int[1]);

            $final_number_lettre = $nbr1 . ' virgule ' . $nbr2;

            $array_data = [
                'final_number_lettre' => $final_number_lettre,
                'number_int' => $number_int,
            ];
        } else {
            $array_data = [
                'final_number_lettre' => self::enlettres($nombre),
                'number_int' => $nombre,
            ];
        }

        return $array_data;
    }

    /**
     * Convert a number to its French currency text.
     *
     * @param string     $nombre The number string.
     * @param int|string $devise Currency type: 0=euros, 1=dinars, 2=dollar canadien.
     * @return array ['final_number_lettre' => string, 'number_int' => string]
     */
    public static function enDevise($nombre, $devise)
    {
        $nombre = preg_replace('/[^0-9,.]/', '', $nombre);
        $nombre = str_replace('.', ',', $nombre);

        $array_data = [];
        $final_number = '';
        $final_number_lettre = '';

        if (strpos($nombre, ',') !== false) {
            $split_number = explode(',', $nombre);

            for ($i = 1; $i < count($split_number); $i++) {
                $final_number .= $split_number[$i];
            }

            $number_int = $split_number[0] . ',' . $final_number;
            $split_number_int = explode(',', $number_int);

            $nbr1 = self::enlettres($split_number_int[0]);
            $nbr2 = self::enlettres($split_number_int[1]);

            if ($devise == 0) {
                $final_number_lettre = $nbr1 . ' euros et ' . $nbr2 . ' centimes';
            } elseif ($devise == 1) {
                $final_number_lettre = $nbr1 . ' dinars et ' . $nbr2 . ' millimes';
            } elseif ($devise == 2) {
                $final_number_lettre = $nbr1 . ' dollar canadien et ' . $nbr2 . ' cents';
            } elseif ($devise == 3) {
                $final_number_lettre = $nbr1 . ' dinars et ' . $nbr2 . ' centimes';
            } elseif ($devise == 4) {
                $final_number_lettre = $nbr1 . ' dirhams et ' . $nbr2 . ' centimes';
            } elseif ($devise == 5) {
                $final_number_lettre = $nbr1 . ' francs suisses et ' . $nbr2 . ' centimes';
            }

            $array_data = [
                'final_number_lettre' => $final_number_lettre,
                'number_int' => $number_int,
            ];
        } else {
            if ($devise == 0) {
                $final_number_lettre = self::enlettres($nombre) . ' euros';
            } elseif ($devise == 1) {
                $final_number_lettre = self::enlettres($nombre) . ' dinars';
            } elseif ($devise == 2) {
                $final_number_lettre = self::enlettres($nombre) . ' dollar canadien';
            } elseif ($devise == 3) {
                $final_number_lettre = self::enlettres($nombre) . ' dinars';
            } elseif ($devise == 4) {
                $final_number_lettre = self::enlettres($nombre) . ' dirhams';
            } elseif ($devise == 5) {
                $final_number_lettre = self::enlettres($nombre) . ' francs suisses';
            }

            $array_data = [
                'final_number_lettre' => $final_number_lettre,
                'number_int' => $nombre,
            ];
        }

        return $array_data;
    }

    /**
     * Get the range label for a number.
     *
     * @param float $n The number.
     * @return string
     */
    public static function fromZeroTo($n)
    {
        if ($n >= 0 && $n <= 100) {
            return '0 à 100';
        }
        if ($n > 100 && $n <= 1000) {
            return '0 à 1000';
        }
        if ($n > 1000 && $n <= 10000) {
            return '0 à 10000';
        }
        if ($n > 10000 && $n <= 1000000) {
            return '0 à 1000000';
        }
        return '';
    }
}
