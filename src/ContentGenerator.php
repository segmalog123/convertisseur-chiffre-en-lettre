<?php
namespace ChiffreEnLettre;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generates dynamic content to enrich conversion pages.
 * Supports context-specific content for better SEO engagement.
 */
class ContentGenerator
{
    /**
     * Get data for the Cheque Visual module.
     *
     * @param string $number Input number.
     * @param string $result Text result.
     * @param string $currency Currency label.
     * @param string $lang Target language for content ('fr' or 'en').
     * @return array
     */
    public static function getChequeData($number, $result, $currency = 'Euros', $lang = 'fr')
    {
        if ($lang === 'en') {
            $date = date('F j, Y');
            $amount_txt = ucfirst($result);
            $payee = 'Your Name Here';
            $num_fmt = number_format((float) str_replace(',', '.', $number), 2, '.', ',');
        } else {
            setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
            $date = date_i18n('j F Y');
            $amount_txt = ucfirst($result);
            $payee = 'Votre Nom Ici';
            $num_fmt = number_format((float) str_replace(',', '.', $number), 2, ',', ' ');
        }

        return [
            'date' => $date,
            'amount_num' => $num_fmt,
            'amount_txt' => $amount_txt,
            'currency' => $currency,
            'payee' => $payee,
        ];
    }

    /**
     * Get mathematical properties (explained in French) with high spintax variance.
     *
     * @param string $number
     * @return array List of fact strings (French).
     */
    public static function getMathFacts($number)
    {
        $n = (int) str_replace([' ', ','], '', $number);
        $facts = [];

        $pick = function ($variations) use ($n) {
            return $variations[$n % count($variations)];
        };

        // Even/Odd
        if ($n % 2 === 0) {
            $facts[] = $pick([
                "La parité de {$n} est paire, ce qui signifie qu'il est divisible par 2 exactement.",
                "Mathématiquement parlant, {$n} est un entier naturel pair.",
                "Puisqu'il se termine par " . ($n % 10) . ", {$n} fait partie des nombres pairs.",
                "Le nombre entier {$n} est un multiple de 2 (nombre pair)."
            ]);
        } else {
            $facts[] = $pick([
                "La parité de {$n} est impaire, il ne peut donc pas être divisé par 2 sans reste.",
                "Le nombre {$n} appartient à la catégorie algébrique des entiers impairs.",
                "Par ses propriétés arithmétiques basiques, {$n} est classifié comme un nombre impair.",
                "Étant donné son dernier chiffre, il est évident que {$n} est absolument impair."
            ]);
        }

        // Binary
        $bin = decbin($n);
        $facts[] = $pick([
            "Si l'on traduit {$n} dans le système binaire (base 2), on obtient la séquence : {$bin}.",
            "L'équivalent de {$n} en code binaire informatique classique s'écrit {$bin}.",
            "En numération de base 2 (ou binaire), la valeur de {$n} est strictement égale à {$bin}.",
            "Pour la logique machine, ce nombre {$n} est lu sous la forme binaire {$bin}."
        ]);

        // Hex
        $hex = strtoupper(dechex($n));
        $facts[] = $pick([
            "En notation hexadécimale (base 16), la valeur correspondante est {$hex}.",
            "Dans le système hexadécimal fréquemment utilisé en programmation, {$n} se note {$hex}.",
            "La conversion hexadécimale de l'entier décimal {$n} donne le résultat {$hex}.",
            "Si on l'encode sur la base arithmétique 16, {$n} s'affiche ainsi : {$hex}."
        ]);

        // Squared
        $sqrt = sqrt($n);
        if ($sqrt == floor($sqrt)) {
            $facts[] = $pick([
                "Fait notable : {$n} est reconnu comme étant le carré parfait du nombre entier {$sqrt}.",
                "Ce nombre {$n} possède une racine carrée exacte valant {$sqrt}.",
                "La géométrie nous indique que {$n} forme un carré parfait basé sur le côté {$sqrt}."
            ]);
        }

        return $facts;
    }

    /**
     * Get grammar rules for the target language, explained in French with variance.
     *
     * @param string $number
     * @param string $target_lang The language of the number being written ('fr' or 'en').
     * @return array List of rules in French.
     */
    public static function getGrammarRules($number, $target_lang = 'fr')
    {
        $n = (int) str_replace([' ', ','], '', $number);
        $rules = [];

        $pick = function ($variations) use ($n) {
            return $variations[$n % count($variations)];
        };

        if ($target_lang === 'en') {
            // ENGLISH Rules
            if ($n >= 21 && $n <= 99) {
                $rules[] = $pick([
                    "Syntaxe anglaise : les nombres allant de 21 à 99 requièrent un trait d'union pour {$n}.",
                    "Pour {$n}, la grammaire veut que l'on lie la dizaine à l'unité avec un tiret.",
                    "L'anglais moderne oblige l'usage du trait d'union sur tous les composés de ce type."
                ]);
            }
            if ($n > 100 && $n % 100 !== 0) {
                $rules[] = $pick([
                    "Au Royaume-Uni, pour écrire des nombres comme {$n}, on ajoute la conjonction verbale 'and'.",
                    "L'habitude britannique dicte l'emploi du pont 'and' avant les dizaines d'un nombre tel que {$n}.",
                    "C'est une spécificité locale : les Anglais insèrent un 'and' dans la structure de {$n}."
                ]);
            }
            if ($n >= 1000) {
                $rules[] = $pick([
                    "Cas d'invariabilité : les substantifs anglais comme 'thousand' ou 'million' ne prennent jamais la marque du pluriel ici.",
                    "On remarquera que, même en multipliant les milliers dans {$n}, le mot 'thousand' reste singulier en anglais.",
                    "Les volumes importants comme {$n} n'entrainent aucune altération orthographique plurielle sur les multiplicateurs de base en anglais."
                ]);
            }
        } else {
            // FRENCH Rules
            if ($n < 100 && $n > 0) {
                $rules[] = $pick([
                    "Par principe, tous les éléments du nombre {$n} inférieur à cent utilisent des traits de liaison typographiques.",
                    "La grammaire de base exige qu'une valeur comme {$n} soit écrite avec un ou plusieurs traits d'union.",
                ]);
            }
            if ($n % 100 === 0 && $n >= 200 && $n < 1000) {
                $rules[] = $pick([
                    "Le marqueur de pluriel s'applique sur le mot cent, car il clôt la lecture de {$n}.",
                    "Ce nombre précis ({$n}) force le terme « cent » à obtenir un 's' final en l'absence de chiffres à sa suite."
                ]);
            }
            if ($n >= 1000 && $n < 2000) {
                $rules[] = $pick([
                    "L'adjectif numéral 'mille' est strictement invariant en genre comme en nombre.",
                    "Comme ce nombre implique la valeur des milliers, rappelez-vous que le mot « mille » s'écrit toujours sans 's'."
                ]);
            }
        }

        return $rules;
    }

    /**
     * Get contextual trivia (Age, Salary, etc.) for unique content.
     *
     * @param string $number
     * @return string|null A contextual fact string or null if none applies.
     */
    public static function getContextualTrivia($number)
    {
        $n = (int) str_replace([' ', ','], '', $number);
        $current_year = (int) date('Y');

        // Age Context (18-100)
        if ($n >= 18 && $n <= 100) {
            $birth_year = $current_year - $n;
            return "Si vous avez {$n} ans, vous êtes né(e) en {$birth_year}.";
        }

        // Monthly Salary Context (1200 - 10000)
        if ($n >= 1200 && $n <= 120000) {
            $monthly = number_format($n / 12, 2, ',', ' ');
            return "Un salaire annuel de {$n} € correspond à environ {$monthly} € par mois.";
        }

        // Distance Context (1 - 50)
        if ($n >= 1 && $n <= 50) {
            $steps = number_format($n * 1300, 0, ',', ' '); // Approx steps per km
            return "Parcourir {$n} km à pied représente environ {$steps} pas.";
        }

        // Year Context (1900 - 2026)
        if ($n >= 1900 && $n <= $current_year) {
            return "L'année {$n} est une année du calendrier grégorien.";
        }

        return null;
    }

    /**
     * Get highly specific, dynamic text explaining spelling rules for this exact number.
     * Uses modulo selection from large arrays of synonyms to crash duplication rates < 20%.
     *
     * @param string $number
     * @param string $lang Target language ('fr' or 'en')
     * @return array Array of explanation paragraphs.
     */
    public static function getDynamicSpellingText($number, $lang = 'fr')
    {
        $n = (int) str_replace([' ', ','], '', $number);
        $texts = [];

        $pick = function ($variations) use ($n) {
            return $variations[$n % count($variations)];
        };

        if ($lang === 'fr') {
            // Zéro
            if ($n === 0) {
                $texts[] = $pick([
                    "Fait grammatical symbolique : le nombre « zéro » est le seul nom valant quantité qui s'accorde au pluriel si employé comme substantif commun (des zéros).",
                    "Spécificité conceptuelle absolue : contrairement aux numéraux ordinaires, {$n} accepte paradoxalement une pluralisation francophone lorsqu'il qualifie de vagues éléments nuls.",
                    "La linguistique se détache ici des mathématiques concernant {$n} : pris isolément en tant qu'état marquant l'absence déclinable, il justifie d'acquérir occasionnellement un 's'."
                ]);
            }

            // Hyphens (1990 Reform)
            if ($n > 10 && $n < 100 && $n % 10 !== 0 && $n !== 11 && $n !== 71 && $n !== 81 && $n !== 91) {
                $texts[] = $pick([
                    "L'orthographe française classique séparait les mots par des espaces. Mais depuis la réforme de 1990, l'Académie recommande de relier les parties du nombre {$n} avec des traits d'union (ex : vingt-et-un).",
                    "Afin d'éviter tout questionnement, la règle orthographique modernisée depuis l'année 1990 oblige à relier toutes les syllabes numériques d'un nombre composé équivalent à {$n} avec des traits horizontaux.",
                    "Il est important de souligner que, suite aux ajustements de 1990, l'ensemble des éléments constituant la transcription lettrée de {$n} s'accrochent désormais obligatoirement avec un trait d'union coordonnant."
                ]);
            } elseif ($n > 100) {
                $texts[] = $pick([
                    "Bon à retenir pour {$n} : les nouvelles réglementations de 1990 autorisent expressément à apposer un trait d'union entre chaque terme, y compris les centaines.",
                    "L'usage des traits d'union s'est élargi. L'orthographe rectifiée implique que toutes les composantes d'un grand entier comme {$n} peuvent désormais être reliées officiellement par des tirets.",
                    "Pour clarifier la lecture de {$n}, le Conseil Supérieur de la Langue Française a standardisé l'apposition des traits d'union sur tous les blocs de mots décimaux."
                ]);
            }

            // Exceptions for 80, 20
            if ($n === 80 || ($n > 80 && $n < 100)) {
                $texts[] = $pick([
                    "Le terme « vingt » présente une rare subtilité : il subit le pluriel dans 'quatre-vingts' mais le reperd immédiatement s'il est suivi d'un adjectif (ex: quatre-vingt-deux), comme c'est le cas pour la tranche {$n}.",
                    "Exception grammaticale typiquement française que l'on retrouve avec {$n} : on accorde « vingt » en ajoutant un 's' terminal uniquement s'il est en queue de chaîne (ex = 80), mais jamais ailleurs.",
                    "Le calcul lexical du « vingt » dans la foulée de {$n} obéit à une contrainte forte : il ne peut s'accorder pluriellement que s'il clôture l'expression numérale."
                ]);
            }

            // Exceptions for 100
            if ($n === 100) {
                $texts[] = $pick([
                    "Règle invariable : le mot « cent » ne prend la marque du pluriel (un « s ») que s'il est multiplié par un autre entier, et qu'il termine la composante numérique du nombre.",
                    "Concernant le pivot centésimal {$n}, le mot « cent » reste singulier dans ce cas précis.",
                    "L'Académie française dicte que le terme « cent » ne s'accorde au pluriel que sous l'effet d'une multiplication, ce qui n'est pas le cas ici."
                ]);
            } elseif ($n % 100 === 0 && $n > 100 && $n < 1000) {
                $texts[] = $pick([
                    "Le saviez-vous ? Le mot « cent » prend exceptionnellement un « s » au pluriel dans ce nombre car il est multiplié par la centaine et termine l'expression.",
                    "Dans un volume rond comme {$n}, le multiplicateur frontal oblige le mot « cent » à endosser la marque grammaticale du pluriel.",
                    "C'est la règle d'or pour la valeur {$n} : puisqu'aucun chiffre additionnel ne suit la centaine multipliée, l'accord pluriel en 's' s'impose."
                ]);
            } elseif ($n > 100 && $n % 100 !== 0 && $n < 1000) {
                $texts[] = $pick([
                    "Dans cette configuration bien précise face à {$n}, le mot « cent » reste strictement au singulier (sans « s ») car il est suivi par une autre précision décimale ou unitaire.",
                    "Exception de centaine appliquée à {$n} : bien qu'étant logiquement multiplié, le pivot « cent » annule son pluriel dès qu'il est contraint par un chiffre suiveur.",
                    "On relève ici un mécanisme linguistique essentiel pour écrire {$n} : la queue du nombre bloque inéluctablement l'accord du marqueur 'cent'."
                ]);
            }

            // Mille
            if ($n >= 1000 && $n < 1000000) {
                if ($n >= 1000 && $n < 2000) {
                    $texts[] = $pick([
                        "Règle d'or de la langue française : le mot « mille » est totalement invariable. Quelle que soit la quantité qu'il exprime, il ne prendra jamais de « s » (ni de trait d'union s'il suit un multiplicateur).",
                        "L'orthographe de {$n} illustre le dogme de l'invariabilité des milliers : le mot « mille » s'écrit toujours au singulier.",
                        "En écrivant {$n}, notez bien que « mille » échappe à la règle du pluriel. C'est une exception historique française.",
                        "Le cap des milliers dans {$n} est marqué par le terme « mille », qui refuse obstinément la marque du pluriel.",
                        "Aucun ajout de lettre finale n'est permis sur le mot « mille » lors de la retranscription textuelle de {$n}.",
                        "La syntaxe francophone est stricte pour {$n} : le vocable désignant les milliers demeure gelé dans sa forme singulière."
                    ]);
                } else {
                    $multi_mille = floor($n / 1000);
                    $texts[] = $pick([
                        "Même si {$n} symbolise formellement {$multi_mille} milliers, le terme « mille » ne prendra jamais de « s ». C'est une invariabilité absolue.",
                        "Contrairement à la logique mathématique de multiplication ({$multi_mille} fois mille), le mot reste singulier dans l'écriture de {$n}.",
                        "Dans le cas spécifique de {$n}, la multiplicité n'affecte pas l'orthographe du grand volume : « mille » demeure sans marque de pluriel.",
                        "L'académie française impose pour un montant comme {$n} que le marqueur des milliers reste singulier, malgré le multiplicateur {$multi_mille}.",
                        "Ne tombez pas dans le piège pour {$n} : on ne pluralise jamais les milliers en lettres, peu importe leur quantité initiale.",
                        "La quantité exacte de {$multi_mille} milliers n'y change rien ; l'expression littérale de {$n} fige le mot central au singulier strict."
                    ]);
                }
            }

            // Millions / Milliards
            if ($n >= 1000000) {
                $texts[] = $pick([
                    "Contrairement au mot « mille », les termes « million » et « milliard » ont le statut grammatical de noms communs. Par conséquent, ils s'accordent en nombre et prennent naturellement un « s » au pluriel dès qu'il y en a au moins deux.",
                    "Fait linguistique sur {$n} : les mots dénotant les millions ou milliards sont des substantifs. Ils reçoivent donc explicitement un 's' terminal au pluriel.",
                    "La grammaire de {$n} impose un « s » sur le cap des millions ou milliards, se distinguant fondamentalement de l'invariabilité de « mille »."
                ]);
            }

            // Exceptions for 11, 71, 91 and the word "et"
            $last_digit = $n % 10;
            if ($last_digit === 1 && $n !== 11 && $n !== 71 && $n !== 81 && $n !== 91 && $n > 20) {
                $texts[] = $pick([
                    "Contrairement aux autres terminaisons, le suffixe « 1 » s'imbrique souvent au moyen du connecteur « et » dans ces ordres de grandeur, à la manière logique du terme {$n}.",
                    "Le traitement grammatical du chiffre un (vis-à-vis de {$n}) force habituellement l'intervention de la charnière 'et' en substitution au trait de coupure franc.",
                    "S'il arrive que {$n} doive s'accorder devant une notion de genre féminine (comme une 'page' ou une 'fois'), ce suffixe unique « un » pivotera phonétiquement vers « une ».",
                    "La conjonction de coordination 'et' sert de pont grammatical exclusif pour accrocher l'unité finale 1 dans la structure de {$n}.",
                    "C'est une tradition phonétique : on adoucit la prononciation de {$n} en insérant 'et' avant l'unité une, plutôt qu'un trait sec."
                ]);
            }
            if ($n === 11 || $n % 100 === 71 || $n % 100 === 91) {
                $texts[] = $pick([
                    "Pour construire ce type de nombre comme 71 ou 91 en France, on ne dit pas « soixante-dix-et-un » mais on utilise le composé « onze » (ex: soixante-et-onze) afin de respecter le compte sur une base séagésimale (base 60).",
                    "Afin de désigner {$n}, on s'affranchit du 'un' classique pour convoquer le bloc archaïque 'onze' selon le vieux système vicésimal.",
                    "La lecture de l'unité terminale dans {$n} recourt structurellement au groupe « onze », marquant une asymétrie avec le reste des déclinaisons décimales."
                ]);
            }
            if ($n % 100 === 81) {
                $texts[] = "Exception française : on écrit « quatre-vingt-un » en omettant délibérément la conjonction « et », contrairement aux autres cas similaires comme « vingt-et-un ».";
            }

            // Regional variants (Septante, Nonante, Huitante)
            if (($n >= 70 && $n < 80) || ($n >= 90 && $n < 100)) {
                $texts[] = $pick([
                    "Particularité helvète ou belge pour traduire la dizaine de {$n} : les zones géographiques ayant adopté l'usage de 'septante' ou 'nonante' contournent adroitement la méthode de calcul complexe en base-20 des Français.",
                    "Détail culturel captivant impliqué par {$n} : la Suisse et la Belgique francophone simplifient radicalement ce nombre via l'utilisation vernaculaire de mots clairs et logiques comme septante ou nonante.",
                    "En s'affranchissant du système vigésimal datant partiellement de l'époque gallo-romaine, certains territoires s'en remettent à un format lexique novateur pour aborder les grandeurs autour de {$n}."
                ]);
            }
            if ($n >= 80 && $n < 90) {
                $texts[] = $pick([
                    "Particularité régionale : en Suisse romande, il est fréquent d'entendre « huitante » à la place de « quatre-vingts », simplifiant grandement la lecture du nombre {$n}.",
                    "Coutume linguistique locale : les Helvètes suppriment souvent la mathématique « quatre fois vingt » intégrée dans {$n} pour lui substituer la racine logique 'huitante'."
                ]);
            }

        } else {
            // ENGLISH VARIATIONS

            // Zero
            if ($n === 0) {
                $texts[] = $pick([
                    "Vocabulaire : en anglais américain, on utilise presque exclusivement le mot « zero », tandis qu'en anglais britannique, on entend fréquemment « nought ».",
                    "Particularité d'usage de {$n} : les Britanniques le nomment souvent 'nought' mathématiquement, ou même 'nil' dans un score sportif, contre 'zero' aux Etats-Unis.",
                    "L'approche nominale de {$n} divise l'Atlantique, avec 'zero' pour cible en Amérique du nord et 'nought' dominant dans le Commonwealth."
                ]);
            }

            // Teens (13-19)
            if ($n >= 13 && $n <= 19) {
                $texts[] = $pick([
                    "Origine des mots : les nombres de 13 à 19 se terminent invariablement par le suffixe « -teen », correspondant étymologiquement à l'ajout de 10 (« ten »).",
                    "Spécificité de la tranche 13-19 étudiée ici avec {$n} : son marqueur nominal se termine obligatoirement par '-teen', indiquant l'addition de l'unité avec dix.",
                    "La phonétique anglophone de {$n} trahit sa construction : le morceau final '-teen' n'est autre qu'une déclinaison historique signifiant l'addition à base 10.",
                    "L'anglais encadre l'adolescence numérique ({$n}) par le récurrent suffixe '-teen'. C'est une règle pivot de la numération germanique originelle.",
                    "Face à un numéro comme {$n}, observez l'emploi indéfectible de la terminaison '-teen', qui lie sémantiquement la racine du chiffre simple à la dizaine."
                ]);
            }

            // Tens (20-90)
            if ($n >= 20 && $n < 100 && $n % 10 === 0) {
                $texts[] = $pick([
                    "Règle de base : les dizaines rondes anglophones se forment systématiquement avec l'ajout du suffixe final « -ty » (ici pour symboliser {$n}).",
                    "Contrairement aux 'teens', la terminaison '-ty' employée dans {$n} a pour vocation stricte de désigner un ensemble parfait de dizaines.",
                    "Attention à la prononciation anglaise de {$n} : l'accent tonique glisse généralement sur la première syllabe pour bien différencier ce '-ty' des jeunes '-teen'.",
                    "Point de grammaire fondamental sur {$n} : les multiples exclusifs de la dizaine entre 20 et 90 réclament le suffixe invariable '-ty'.",
                    "Pour retranscrire la pleine dizaine {$n}, la langue anglaise fusionne le terme racine ou son dérivé à l'incontournable particule nominale '-ty'.",
                    "En rédigeant {$n}, notez bien la clarté du suffixe : ce '-ty' indique sans ambiguïté la fermeture stricte d'un bloc de dix, sans reste unitaire."
                ]);
            }

            // Hyphens 21-99
            if ($n >= 21 && $n <= 99 && $n % 10 !== 0) {
                $texts[] = $pick([
                    "Syntaxe anglaise : les nombres allant de 21 à 99 requièrent un trait d'union pour relier adéquatement {$n}.",
                    "Pour écrire {$n}, la grammaire veut que l'on lie la dizaine à l'unité terminale avec un tiret.",
                    "L'anglais moderne oblige l'usage typographique restrictif du trait d'union sur tous les composés de ce calibre comme {$n}.",
                    "Attention à ce détail de ponctuation pour {$n} : vous devez obligatoirement intercaler un 'hyphen' (trait d'union) entre la lourde dizaine et la frêle unité.",
                    "Règle d'or de 21 à 99 : face au complexe {$n}, les académies requièrent de souder l'expression numérale avec un court tiret connectif.",
                    "Ne séparez jamais la dizaine et l'unité par un simple espace pour un montant comme {$n} ; la conjonction horizontale via trait d'union s'impose formellement.",
                    "Typographie cardinale anglo-saxonne : {$n} obéit à la loi de la jonction par tiret reliant distinctement la racine de la décade au chiffre accompagnateur."
                ]);
            }

            // Usage of "And"
            if ($n > 100 && $n % 100 !== 0) {
                $texts[] = $pick([
                    "Différence majeure transatlantique identifiée sur {$n} : la grammaire anglaise du Royaume-Uni impose structurellement d'insérer un mot de liaison abstrait « and » entre le groupe de têtes des centaines et la paire finale, pratique largement éludée aux États-Unis.",
                    "Focus sur la ponctuation verbale de {$n} : pour les locuteurs britanniques ou australiens, formuler ce montant à voix haute exhortera fatalement l'incrustation conjonctive d'un 'and' juste avant la lecture des paires terminant le propos numérique.",
                    "Les subtilités régionales au plan mondial, spécifiquement face à une chaîne comme {$n}, traduisent qu'un pont orthographique 'and' va apparaître en terre anglo-saxonne européenne alors qu'il sera rayé arbitrairement par le locuteur nord-américain moderne.",
                    "En analysant la construction de {$n} en langue de Shakespeare, notez que l'anglais britannique exige l'ajout méticuleux du connecteur « and » après les volumes de centaines.",
                    "Le formalisme des écoles londoniennes préconise fermement d'intercaler un « and » logique avant la fraction finale du nombre {$n}, spécificité gommée outre-Atlantique."
                ]);
            }

            // Invariable Thousand/Million
            if ($n >= 1000) {
                $texts[] = $pick([
                    "Règle inébranlable d'invariabilité anglo-saxonne : dans le scénario de {$n}, tout comme le mot 'mille' chez nous, les indicateurs « hundred », « thousand » ainsi que les échelons majeurs « million » préservent farouchement le singulier dès qu’ils portent un sens défini.",
                    "Le saviez-vous ? En écrivant en format anglophone le résultat associé à {$n}, n'essayez sous aucun prétexte de forcer le pluriel avec un « s » sur des jalons lexicaux de grandeur numérique (ex: 'thousand'). Ces unités de volume global demeurent mathématiquement fossilisées.",
                    "Singulier constant garanti : aucune terminaison en « s » ajoutant une portée plurielle n'est permise dans l'énonciation de {$n}. Le seul motif qui provoquerait cette tournure asymétrique résiderait dans une estimation vaste insaisissable (ex: 'thousands of books').",
                    "Avertissement grammatical anglophone pour {$n} : bien que ce montant soit par essence pluriel quant à sa valeur, les termes multiplicateurs pivots ('thousand') ne doivent jamais recevoir de consonne terminale pluralisante.",
                    "Les piliers de jauge tels que 'thousand' demeurent figés au singulier pur dans la prose anglophone. En rédigeant le libellé de {$n}, mémorisez bien cette invariabilité formelle.",
                    "L'anglais interdit la présence d'un pluriel de quantité (« s » final) sur les mots d'échelle pour encadrer le résultat strict de {$n}."
                ]);
            }

            // Punctuation Difference
            if ($n >= 1000) {
                $texts[] = $pick([
                    "Contraste de ponctuation radical pour un gabarit tel que {$n} : les cultures anglophones excluent volontairement l'espace usuel (standard européen) ou le point pour marquer graphiquement les grappes de milieux par tris ; ils convoquent systématiquement l'usage natif de la virgule isolatrice (,).",
                    "Rupture sur le système décimal anglicisé appliqué à {$n} : afin d'orienter le confort de survol visuel et découper les strates par paquets de mille, ces populations apposent mécaniquement la virgule (ex: 1,000) et se gardent bien du point, qu'elles cantonnent symboliquement à la décimation fractionnelle.",
                    "Si jamais vous couchiez {$n} sur écrit commercial financier rédigé en syntaxe américaine, gardez fermement à l'esprit que l'indicateur universel de compartimentation des trois zéros s'avère irrévocablement symbolisé par the 'comma' (notre modeste virgule de coupure).",
                    "La signalétique arithmétique globale change la donne face à {$n} : là où la France pose un espace visuel pour aérer les gros numéros, le cartulaire conventionnel UK/US exécute cette césure au moyen exclusif de la virgule fine.",
                    "Pour scinder par triplet ce bloc numérique {$n}, l'espace typographique adoré des Européens est banni sur les écrits anglais. Ce langage impose sa sacro-sainte ',' en guise de jalon."
                ]);
            }

            // General English Rule 1: Capitalization
            $texts[] = $pick([
                "Règle de capitalisation grammaticale anglophone : peu importe la longueur du nombre {$n}, les mots le composant s'écrivent entièrement en minuscules, à l'exception évidente du début de phrase.",
                "En typographie britannique et américaine, notez que l'écriture du résultat de {$n} s'effectue exclusivement en bas-de-casse (minuscules). On ne met de majuscule qu'au tout premier mot de la ligne.",
                "Contrairement à certains usages de titraille, le corps du texte stipule que le développement littéral de {$n} en anglais rejette formellement les majuscules en son milieu.",
                "Détail orthographique récurrent avec {$n} en langue anglaise : aucun substantif de valeur ou unité ne prend la majuscule. L'ensemble s'étale uniformément en minuscules courantes.",
                "Lors de la transcription formelle de {$n}, le dictionnaire anglais impose une casse mineure intégrale sur tous les vocables générés."
            ]);

            // General English Rule 2: Pluralization of the number itself
            if ($n >= 10) {
                $texts[] = $pick([
                    "Curiosité linguistique : en anglais, s'il faut parler des années ou des valeurs de {$n} au pluriel (ex: 'les années {$n}'), on accole simplement un 's' final au mot sans apostrophe (ex: {$n}s).",
                    "Formatage usuel : lorsque le contexte force le pluriel sur le nombre complet de {$n} (pour désigner une décennie ou un lot), la grammaire anglophone contemporaine proscrit l'apostrophe et soude directement le 's' au vocable pertinent.",
                    "Si le scénario implique de décliner l'idée générique de {$n} au pluriel (ex: 'des dizaines de {$n}'), l'Anglais de base suffixera la matrice numérale par une simple lettre 's' finale.",
                    "Pour exprimer collectivement la notion de plusieurs '{$n}', oubliez la syntaxe possessive à apostrophe ; les manuels de style prônent le suffixe brut 's' accouplé au segment terminal.",
                    "L'application du marqueur pluriel général sur l'entité globale de {$n} obéit à une règle directe en anglais : on greffe un 's' collé sans distinction au dernier terme de l'ensemble."
                ]);
            }
        }

        // Limit to max 4 diverse rules and randomly shuffle to make it look highly dynamic without losing order
        if (empty($texts)) {
            $texts[] = "La traduction et l'écriture des nombres requièrent la plus grande attention aux règles d'accord, de conjugaison et de ponctuation fixées soit par l'Académie française, soit par les universités linguistiques anglophones.";
        } else {
            // Select up to 4 applicable rules to keep UI balanced
            $texts = array_slice($texts, 0, 4);
        }

        return $texts;
    }

    /**
     * Accurately converts an English cardinal text (e.g. "Twenty-one") 
     * into an ordinal text (e.g. "Twenty-first").
     *
     * @param string $plain_text 
     * @return string
     */
    public static function getEnglishOrdinal($plain_text)
    {
        $text = strtolower(trim($plain_text));
        if (empty($text))
            return '';

        $exceptions = [
            'one' => 'first',
            'two' => 'second',
            'three' => 'third',
            'five' => 'fifth',
            'eight' => 'eighth',
            'nine' => 'ninth',
            'twelve' => 'twelfth',
        ];

        // Match the very last word directly, optionally preceded by a space or hyphen
        preg_match('/([ \-]?)([a-z]+)$/', $text, $matches);
        if (!$matches)
            return $text . 'th';

        $separator = $matches[1];
        $lastWord = $matches[2];
        $prefix = substr($text, 0, -strlen($matches[0]));

        if (isset($exceptions[$lastWord])) {
            $ordinalLast = $exceptions[$lastWord];
        } elseif (substr($lastWord, -2) === 'ty') { // twenty -> twentieth
            $ordinalLast = substr($lastWord, 0, -1) . 'ieth';
        } else {
            $ordinalLast = $lastWord . 'th';
        }

        return ucfirst($prefix . $separator . $ordinalLast);
    }

    /**
     * Generates English-specific SEO context (Ordinals, years, age) 
     * to avoid duplicate content penalties with the French math facts.
     * Includes extensive spintax so pages have unique phrasing.
     *
     * @param string $number
     * @param string $result_en_plain
     * @return array
     */
    public static function getEnglishSeoFacts($number, $result_en_plain)
    {
        $n = (int) str_replace([' ', ','], '', $number);
        $facts = [];

        $pick = function ($variations) use ($n) {
            return $variations[$n % count($variations)];
        };

        $ordinal = self::getEnglishOrdinal($result_en_plain);
        $ord_lower = strtolower($ordinal);
        $res_lower = strtolower($result_en_plain);

        // Ordinal Spintax
        $facts[] = $pick([
            "<strong>Forme ordinale :</strong> Pour indiquer une position (le {$n}ème), la traduction est \"{$ordinal}\". L'anglais recourt aux nombres ordinaux : <em>the {$ord_lower}</em>.",
            "<strong>Comment dire le {$n}e :</strong> En anglais, l'équivalent de « le {$n}ème » s'écrit de manière ordinale : <em>the {$ord_lower}</em>.",
            "<strong>Le {$n}ème en anglais :</strong> Si votre but est d'exprimer un rang (le {$n}e composant), la bonne expression est <em>the {$ord_lower}</em>.",
            "<strong>Classement et ordre :</strong> Pour verbaliser la {$n}ème place ou position, le terme anglophone adéquat est formellement <em>the {$ord_lower}</em>."
        ]);

        // Age Context (often searched "comment on dit j'ai X ans en anglais")
        if ($n >= 1 && $n <= 110) {
            $facts[] = $pick([
                "<strong>L'âge de {$n} ans :</strong> Pour dire \"j'ai {$n} ans\", l'anglais emploie le verbe 'to be' (être). On traduira donc par : <em>I am {$res_lower} years old</em>.",
                "<strong>Comment dire j'ai {$n} ans :</strong> Contrairement au français, l'anglais ne dit pas « j'ai », mais « je suis vieux de ». D'où la phrase <em>I am {$res_lower} years old</em>.",
                "<strong>Exprimer l'âge ({$n} ans) :</strong> Si vous voulez parler d'un âge précis comme {$n} ans, la structure grammaticale britannique impose le verbe être : <em>I'm {$res_lower} years old</em>.",
                "<strong>Dire son âge :</strong> Pour indiquer votre âge de {$n} ans, n'utilisez jamais le verbe « to have ». La traduction exacte est <em>I am {$res_lower} years old</em>."
            ]);
        }

        // Decades context ("les années 90")
        if ($n >= 10 && $n <= 90 && $n % 10 === 0) {
            $facts[] = $pick([
                "<strong>Décennie (Les années {$n}) :</strong> Pour parler de la décennie des années {$n} en anglais, on ajoute un 's' final sans apostrophe. On dira par exemple <em>the {$res_lower}s</em>.",
                "<strong>Les années {$n} en anglais :</strong> En abordant cette décennie historique, la grammaire exige simplement l'ajout direct du pluriel : <em>the {$res_lower}s</em>.",
                "<strong>Époque des années {$n} :</strong> Si vous faites référence à la culture de cette tranche d'âge, l'anglais accole un 's' à la fin du chiffre rond : <em>the {$res_lower}s</em>."
            ]);
        }

        // Specific Year context (19XX, 20XX)
        if ($n >= 1900 && $n <= 2099) {
            $facts[] = $pick([
                "<strong>Date et année ({$n}) :</strong> S'il s'agit d'une année historique, la lecture omet souvent le « hundred ». L'anglais la découpe usuellement en deux blocs de chiffres à prononcer.",
                "<strong>Prononcer l'année {$n} :</strong> Gardez en tête qu'un millésime tel que {$n} se scinde presque toujours en deux nombres distincts à l'oral anglophone pour plus de fluidité."
            ]);
        }

        // Generic pluralization rule (fallback if few rules match)
        if ($n >= 100) {
            $facts[] = $pick([
                "<strong>Astuce de grammaire :</strong> Contrairement à de nombreuses langues, l'anglais interdit formellement de mettre un \"s\" pluriel sur les mots servant de multiplicateurs pour le nombre {$n} (tels que <em>hundred</em>, <em>thousand</em> ou <em>million</em>).",
                "<strong>Règle du pluriel :</strong> Lorsque vous prononcez un montant global comme {$n}, souvenez-vous que les unités de mille ou de cent restent absolument invariables en anglais courant.",
                "<strong>Invariabilité mathématique :</strong> Pour la valeur de {$n}, le mot signifiant cent ou mille (s'il est présent) ne prend pas de 's'. C'est une faute classique que font les francophones."
            ]);
        }

        $limit_facts = [];
        foreach ($facts as $f) {
            $limit_facts[] = $f;
        }

        return array_slice($limit_facts, 0, 4);
    }
}
