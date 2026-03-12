<?php
/**
 * Template: /factorielle-de-X/
 * Dynamic factorial result page — full computation + rich content matching French layout.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ─── 1. Get & sanitise the number ──────────────────────────────────────── */
global $wp_query;
$x = intval($wp_query->get('factorial_id'));
if ($x < 0) {
    $x = 0;
}

/* ─── 2. Computation helpers ─────────────────────────────────────────────── */

/**
 * Exact factorial using BCMath (returns string of digits).
 */
function cel_factorial_exact(int $n): string
{
    if ($n <= 1) {
        return '1';
    }
    if (function_exists('bcmul')) {
        $r = '1';
        for ($i = 2; $i <= $n; $i++) {
            $r = bcmul($r, (string) $i);
        }
        return $r;
    }
    // Fallback: PHP native int (valid up to ~20)
    if ($n <= 20) {
        $r = 1;
        for ($i = 2; $i <= $n; $i++) {
            $r *= $i;
        }
        return (string) $r;
    }
    return '';
}

/** Trailing zeros using Legendre's formula */
function cel_trailing_zeros(int $n): int
{
    $z = 0;
    $p = 5;
    while ($p <= $n) {
        $z += intdiv($n, $p);
        $p *= 5;
    }
    return $z;
}

/** Scientific notation from a digit string */
function cel_sci_notation(string $digits, int $mantissa_digits = 6): string
{
    $len = strlen($digits);
    if ($len === 0) {
        return '';
    }
    $exp = $len - 1;
    $m = $digits[0] . '.' . substr($digits, 1, $mantissa_digits);
    $m = rtrim(rtrim($m, '0'), '.');
    return $m . ' &times; 10<sup>' . $exp . '</sup>';
}

/** Stirling's approximation → scientific notation */
function cel_stirling_sci(int $n): string
{
    if ($n <= 0) {
        return '1';
    }
    $log10 = 0.5 * log10(2 * M_PI * $n) + $n * log10($n / M_E);
    $exp = (int) floor($log10);
    $mant = pow(10, $log10 - $exp);
    return number_format($mant, 4) . ' &times; 10<sup>' . $exp . '</sup>';
}

/* ─── 3. Hard cap + tiered computation ──────────────────────────────────────── */

// Numbers > 10,000 are out of bounds — serve the theme's native 404 page
if ($x > 10000) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    include(get_query_template('404'));
    exit;
}

$EXACT_CAP = ($x <= 1000);   // Show exact integer for n <= 1000
$SCIENTIFIC_ONLY = !$EXACT_CAP;

$exact_str = $EXACT_CAP ? cel_factorial_exact($x) : '';
$digit_count = $EXACT_CAP ? strlen($exact_str) : null;
$trailing_zeros = cel_trailing_zeros($x);

$sci = $EXACT_CAP
    ? (strlen($exact_str) > 1 ? cel_sci_notation($exact_str) : '')
    : cel_stirling_sci($x);

// In running text
$display_in_text = ($x <= 20 && $exact_str !== '') ? $exact_str : cel_stirling_sci($x);

/* Build the equation line */
if ($x == 0 || $x == 1) {
    $equation_line = "{$x}! = 1";
} elseif ($x <= 10) {
    $parts = [];
    for ($i = $x; $i >= 1; $i--) {
        $parts[] = $i;
    }
    $eq_result = ($x <= 20 && $exact_str) ? $exact_str : cel_stirling_sci($x);
    $equation_line = "{$x}! = " . implode(' &times; ', $parts) . " = " . $eq_result;
} else {
    $eq_result = ($x <= 20 && $exact_str) ? $exact_str : cel_stirling_sci($x);
    $equation_line = "{$x}! = {$x} &times; " . ($x - 1) . " &times; &hellip; &times; 3 &times; 2 &times; 1 = " . $eq_result;
}

/* Developer technical limits paragraph */
if ($x <= 12) {
    $calc_text = "D'un point de vue informatique, le résultat de {$x}! est suffisamment petit pour être stocké dans un entier standard de 32 bits (dont la limite est d'environ 2,14 milliards). Tout langage de programmation classique ou tableur (comme Excel) calculera la factorielle de {$x} instantanément sans aucune perte de données.";
} elseif ($x <= 20) {
    $calc_text = "Techniquement, la valeur de {$x} factorielle dépasse la limite de stockage d'un entier standard de 32 bits, mais elle tient parfaitement dans l'architecture d'un entier de 64 bits. Les langages comme Python, Java ou C++ traiteront ce calcul nativement en utilisant des variables de type \"long\" pour maintenir une précision absolue.";
} elseif ($x <= 69) {
    // If exact capability wasn't calculated, we don't have exact digit count. We can approximate using Stirling's log10.
    $digits_display = $EXACT_CAP ? $digit_count : ((int) floor(0.5 * log10(2 * M_PI * $x) + $x * log10($x / M_E)) + 1);
    $calc_text = "Attention, la factorielle de {$x} produit un nombre gigantesque. Les calculatrices scientifiques de bureau ne possèdent pas un écran assez large pour afficher les {$digits_display} chiffres de ce résultat. Elles basculeront automatiquement sur un affichage en notation scientifique. De plus, avec le nombre {$x}, nous approchons de la limite matérielle des calculatrices de poche de base.";
} else {
    $digits_display = $EXACT_CAP ? $digit_count : ((int) floor(0.5 * log10(2 * M_PI * $x) + $x * log10($x / M_E)) + 1);
    $calc_text = "Le calcul de {$x}! provoque ce que l'on appelle une \"Erreur de dépassement de capacité\" (Overflow Error) sur la quasi-totalité des calculatrices de poche, qui s'arrêtent généralement à 69!. Pour traiter un nombre composé de {$digits_display} chiffres comme la factorielle de {$x}, il est obligatoire d'utiliser des algorithmes de programmation avancés (comme la bibliothèque BigInt) ou des moteurs de calcul côté serveur.";
}

/* ─── 4. Spinner Arrays ──────────────────────────────────────────────── */
$array_a = [
    "Solution mathématique complète pour la factorielle de {$x}",
    "Calculateur de la valeur exacte de {$x}!",
    "Obtenir le résultat et la formule de {$x} factorielle",
    "Résolution étape par étape de {$x}!"
];

$array_b = [
    "Quelle est la Factorielle de {$x} ? ({$x}!)",
    "Le Calcul de la Factorielle {$x}",
    "Valeur exacte et propriétés de {$x} Factorielle",
    "{$x}! : Résultat et Décomposition Mathématique"
];

// Determine index based on the number to have a consistent "random" spinner for the same page
// To avoid obvious patterns, we hash the number slightly instead of direct modulo
$spin_index = ($x * 7 + 13) % 4;
$pre_title = $array_a[$spin_index];
$h1_title = $array_b[$spin_index];

/* ─── 5. Paging Links (Nearby factorials) ────────────────────────────── */
$nearby_links = [];
if ($x <= 10000) {
    // Minus elements
    for ($i = 4; $i >= 1; $i--) {
        $n = $x - $i;
        if ($n >= 0) {
            $nearby_links[] = [
                'n' => $n,
                'txt_index' => $i
            ];
        }
    }
    // Plus elements
    for ($i = 1; $i <= 4; $i++) {
        $n = $x + $i;
        if ($n <= 10000) {
            $nearby_links[] = [
                'n' => $n,
                'txt_index' => $i + 4 // Shift index for different text strings if needed
            ];
        }
    }
}

$anchor_texts = [
    1 => "Calcul de la factorielle de %d",
    2 => "Valeur de %d factorielle",
    3 => "Que vaut la factorielle de %d ?",
    4 => "%d! formule de Stirling",
    5 => "factorielle de %d",
    6 => "%d factorielle",
    7 => "%d factorielle",
    8 => "factorielle %d!"
];

/* ─── FAQ Schema Variables ───────────────────────────────────────────── */
$faq_result_text = ($x <= 20 && $exact_str) ? $exact_str : 'approximativement ' . strip_tags(cel_stirling_sci($x));

get_header();
?>
<style>
    /* ── Layout / Colours ────────────────────────────────────── */
    #sidebar {
        display: none !important;
    }

    .content {
        width: 100% !important;
    }

    .nwf-wrap {
        max-width: 860px;
        margin: 0 auto;
        padding: 20px 15px 50px;
        line-height: 1.75;
        color: #333;
    }

    /* ── Intro / boxes ───────────────────────────────────────── */
    .nwf-section-intro {
        background: #f7fdf9;
        border-left: 4px solid #2a7d4f;
        padding: 14px 18px;
        border-radius: 6px;
        margin-bottom: 28px;
        font-size: 15px;
    }

    /* ── Exact result box ────────────────────────────────────── */
    .nwf-result-box {
        background: #1a5c30;
        color: #fff;
        border-radius: 10px;
        padding: 18px 22px;
        margin: 16px 0 24px;
        font-family: monospace;
        font-size: 0.95em;
        word-break: break-all;
        overflow-wrap: anywhere;
        line-height: 1.6;
    }

    .nwf-result-label {
        font-weight: 700;
        font-size: 1.05em;
        margin-bottom: 6px;
        font-family: inherit;
    }

    /* ── Formula ─────────────────────────────────────────────── */
    .nwf-formula-wrap {
        text-align: center;
        margin: 16px 0;
        padding: 14px;
        background: #f5faf7;
        border-radius: 6px;
        overflow-x: auto;
        max-width: 100%;
    }

    .nwf-formula {
        font-size: 1.2em;
        font-style: italic;
        font-family: Georgia, serif;
        color: #1a1a1a;
        word-break: break-all;
        overflow-wrap: break-word;
        display: inline-block;
        max-width: 100%;
    }

    /* ── Headings ────────────────────────────────────────────── */
    .nwf-h2 {
        font-size: 1.3em;
        font-weight: 700;
        color: #1a5c30;
        border-bottom: 2px solid #c5e8d3;
        padding-bottom: 5px;
        margin: 34px 0 12px;
    }

    .nwf-h3 {
        font-size: 1.05em;
        font-weight: 700;
        color: #2a7d4f;
        margin: 22px 0 8px;
    }

    /* ── Info boxes ──────────────────────────────────────────── */
    .nwf-info {
        background: #eafaf1;
        border: 1px solid #a2dbb8;
        border-radius: 6px;
        padding: 12px 18px;
        margin: 14px 0;
        font-size: 15px;
    }

    .nwf-warning {
        background: #fff8e1;
        border: 1px solid #ffe082;
        border-radius: 6px;
        padding: 12px 18px;
        margin: 14px 0;
        font-size: 15px;
    }

    /* ── Cross-link pills ─────────────────────────────────────── */
    .nwf-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        list-style: none;
        padding: 0;
        margin: 12px 0 20px;
    }

    .nwf-pills li a {
        background: #2a7d4f;
        color: #fff;
        text-decoration: none;
        padding: 5px 13px;
        border-radius: 18px;
        font-size: 13px;
        transition: background .2s;
    }

    .nwf-pills li a:hover {
        background: #1a5c30;
    }

    /* ── FAQ ─────────────────────────────────────────────────── */
    .nwf-faq-item {
        border-bottom: 1px solid #ddd;
        padding: 14px 0;
    }

    .nwf-faq-item:last-child {
        border: none;
    }

    .nwf-faq-q {
        font-weight: 700;
        font-size: 1em;
        color: #1a5c30;
        margin-bottom: 6px;
    }

    .nwf-faq-a {
        font-size: 14.5px;
        color: #555;
        word-break: break-word;
        overflow-wrap: break-word;
    }

    /* ── Default styling inside the header block via WP ─── */
    .before_html_custom_header_block h2 {
        display: none !important;
    }

    .before_html_custom_header_block p span {
        font-size: 16px;
    }
</style>

<div class="content">

    <?php if (function_exists('chiffre_breadcrumbs')) {
        chiffre_breadcrumbs();
    } ?>



    <div class="nwf-wrap">

        <!-- ══ INTRO ═══════════════════════════════════════════════════ -->
        <div class="nwf-section-intro">
            <p>
                Si vous cherchez à connaître la valeur de la <strong>factorielle de <?php echo $x; ?></strong>, vous
                êtes au bon endroit.
                En mathématiques combinatoires, la <strong>factorielle d'un entier naturel <?php echo $x; ?></strong>
                (notée mathématiquement par un point d'exclamation, <strong><?php echo $x; ?>!</strong>) représente le
                produit de ce nombre par tous les entiers strictement positifs qui lui sont inférieurs.
            </p>
        </div>

        <p>La valeur exacte de <strong><?php echo $x; ?>!</strong> est :</p>

        <div class="nwf-result-box">
            <div class="nwf-result-label">
                <?php echo $SCIENTIFIC_ONLY ? 'Valeur Approximative :' : esc_html("{$x}! ="); ?>
            </div>
            <?php if ($SCIENTIFIC_ONLY): ?>
                <?php echo $sci; ?> <span style="font-size:0.8em;opacity:0.8;">(Approximation de Stirling)</span>
            <?php else: ?>
                <?php echo esc_html($exact_str ?: "Nombre trop grand — voir la notation scientifique"); ?>
            <?php endif; ?>
        </div>

        <?php if ($x > 15 && $sci): ?>
            <p class="nwf-info">
                💡 <strong>Le saviez-vous ?</strong> Parce que la multiplication successive produit une croissance
                exponentielle, le résultat de <strong><?php echo $x; ?> factorielle</strong> est un nombre extrêmement
                volumineux. En sciences et en ingénierie, il est souvent plus lisible de l'exprimer en notation
                scientifique, soit approximativement <strong><?php echo $sci; ?></strong>.
            </p>
        <?php endif; ?>

        <!-- ══ HOW TO CALCULATE ════════════════════════════════════════ -->
        <h2 class="nwf-h2">Comment calculer <?php echo $x; ?> factorielle ? (Formule)</h2>

        <p>Pour comprendre d'où provient ce résultat, il faut appliquer la formule fondamentale de la factorielle :</p>

        <div class="nwf-formula-wrap">
            <span class="nwf-formula">n! = n &times; (n&minus;1) &times; (n&minus;2) &times; &hellip; &times; 1</span>
        </div>

        <p>En appliquant cette équation à notre entier <strong><?php echo $x; ?></strong>, la décomposition du calcul
            est la suivante :</p>

        <div class="nwf-formula-wrap">
            <span class="nwf-formula">
                <?php echo $equation_line; ?>
            </span>
        </div>

        <!-- ══ MATHEMATICAL PROPERTIES ════════════════════════════════ -->
        <h2 class="nwf-h2">🧮 Propriétés Mathématiques Uniques de <?php echo $x; ?>!</h2>

        <h3 class="nwf-h3">Nombre de chiffres et Zéros finaux</h3>
        <p>
            Une question classique lors des examens de mathématiques est de déterminer le nombre de zéros à la fin d'une
            factorielle.
            La <strong>factorielle de <?php echo $x; ?></strong> se termine par très exactement
            <strong><?php echo $trailing_zeros; ?></strong> zéros consécutifs.
            Pourquoi ? Parce qu'un zéro final est créé par chaque paire de facteurs (2 &times; 5) contenue dans le
            développement du calcul.
        </p>

        <?php
        $actual_digits = $EXACT_CAP ? $digit_count : ((int) floor(0.5 * log10(2 * M_PI * $x) + $x * log10($x / M_E)) + 1);
        ?>
        <p>Lorsqu'on l'écrit en entier, le résultat de <strong><?php echo $x; ?>!</strong> comporte un total de
            <strong><?php echo number_format($actual_digits, 0, ',', ' '); ?></strong> chiffres.
        </p>


        <h3 class="nwf-h3">Permutations et Combinatoire</h3>
        <p>
            Dans le domaine des probabilités, <strong><?php echo $x; ?>!</strong> définit le nombre d'arrangements
            possibles (permutations) d'un ensemble.
            Si vous possédez <strong><?php echo $x; ?></strong> objets distincts, il existe exactement
            <strong><?php echo $SCIENTIFIC_ONLY ? "environ " . strip_tags($sci) : $display_in_text; ?></strong> manières
            différentes de les ordonner.
        </p>

        <!-- ══ CAPABILITY LIMITS ═══════════════════════════════════════ -->
        <h2 class="nwf-h2">💻 Limites Informatiques pour le nombre <?php echo $x; ?></h2>
        <p>
            <?php echo $calc_text; ?>
        </p>

        <?php if ($x > 10): ?>
            <h3 class="nwf-h3">L'Approximation de Stirling pour <?php echo $x; ?>!</h3>
            <p>
                Pour les nombres élevés comme <strong><?php echo $x; ?></strong>, le calcul exact demande beaucoup de
                ressources.
                Les mathématiciens utilisent donc l'<strong>approximation de Stirling</strong> pour évaluer la factorielle
                rapidement :
            </p>
            <div class="nwf-formula-wrap">
                <span class="nwf-formula">n! &approx; &radic;(2&pi;n) &times; (n/e)<sup>n</sup></span>
            </div>
            <p>
                En appliquant ce théorème mathématique à <strong><?php echo $x; ?></strong>, l'estimation scientifique
                obtenue
                (<strong><?php echo cel_stirling_sci($x); ?></strong>) est incroyablement proche de notre valeur
                mathématique réelle.
            </p>
        <?php endif; ?>

        <!-- ══ RELATED SEARCHES ══════════════════════════════════════════ -->
        <h2 class="nwf-h2">🌐 Recherches associées</h2>
        <ul class="nwf-pills">
            <?php foreach ($nearby_links as $link): ?>
                <li>
                    <a href="<?php echo esc_url(home_url('/factorielle-de-' . $link['n'] . '/')); ?>">
                        <?php echo esc_html(sprintf($anchor_texts[$link['txt_index']], $link['n'])); ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li>
                <a href="<?php echo esc_url(home_url('/ecrire/' . $x . '-en-lettre/')); ?>">
                    Écrire le nombre <?php echo $x; ?> en lettres (Orthographe)
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url(home_url('/comment-on-dit/' . $x . '-en-anglais/')); ?>">
                    Traduction de <?php echo $x; ?> en Anglais
                </a>
            </li>
        </ul>

        <!-- ══ FAQ ═════════════════════════════════════════════════════ -->
        <h2 class="nwf-h2">FAQ</h2>

        <div class="nwf-faq-item">
            <div class="nwf-faq-q">Comment trouver la factorielle de <?php echo $x; ?> ?</div>
            <div class="nwf-faq-a">
                Pour trouver la factorielle de <?php echo $x; ?>, il faut multiplier le nombre <?php echo $x; ?> par
                tous les nombres entiers strictement positifs qui le précèdent.
                La formule complète est <?php echo $x; ?> &times; <?php echo ($x - 1); ?> &times;
                <?php echo ($x - 2); ?> &hellip; &times; 1, ce qui nous donne exactement
                <?php echo $faq_result_text; ?>.
            </div>
        </div>

        <div class="nwf-faq-item">
            <div class="nwf-faq-q">Combien de zéros finaux possède <?php echo $x; ?> factorielle ?</div>
            <div class="nwf-faq-a">
                Le résultat de <?php echo $x; ?> factorielle se termine par <?php echo $trailing_zeros; ?> zéros
                consécutifs. Ce nombre est défini par la quantité de paires de nombres premiers (2 et 5) présentes dans
                la décomposition du produit.
            </div>
        </div>

        <div class="nwf-faq-item">
            <div class="nwf-faq-q">Que veut dire le symbole <?php echo $x; ?>! ?</div>
            <div class="nwf-faq-a">
                En mathématiques, le point d'exclamation suivant le nombre <?php echo $x; ?> (écrit <?php echo $x; ?>!)
                est le symbole de l'opération "factorielle". Il indique qu'il faut effectuer le produit de
                <?php echo $x; ?> par tous les entiers inférieurs jusqu'à 1.
            </div>
        </div>

    </div><!-- /.nwf-wrap -->
</div><!-- .content -->

<!-- JavaScript logic for search bar matching English script -->
<script>
    (function () {
        function doFactorialRedirect() {
            var input = document.querySelector('.convert-input');
            if (!input) return;
            var val = parseInt(input.value, 10);
            var err = document.querySelector('.error-input');
            if (isNaN(val) || val < 0) {
                if (err) err.textContent = 'Veuillez entrer un entier positif valide.';
                return;
            }
            if (val > 10000) {
                if (err) err.textContent = '\u26a0\ufe0f Notre calculatrice prend en charge les nombres de 0 à 10 000.';
                return;
            }
            if (err) err.textContent = '';
            window.location.href = '<?php echo esc_js(home_url("/")); ?>factorielle-de-' + val + '/';
        }
        window.addEventListener('load', function () {
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.convert-button[data-convert="factorial"]');
                if (btn) { e.preventDefault(); e.stopImmediatePropagation(); doFactorialRedirect(); }
            }, true);
            var inp = document.querySelector('.convert-input');
            if (inp) inp.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && e.target.closest('.convert-block') && !e.target.closest('.sidebar-widget')) { e.preventDefault(); e.stopImmediatePropagation(); doFactorialRedirect(); }
            }, true);
        });
    }());
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "Comment trouver la factorielle de <?php echo $x; ?> ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Pour trouver la factorielle de <?php echo $x; ?>, il faut multiplier le nombre <?php echo $x; ?> par tous les nombres entiers strictement positifs qui le précèdent. La formule complète est <?php echo $x; ?> * <?php echo ($x - 1); ?> * <?php echo ($x - 2); ?> ... * 1, ce qui nous donne exactement <?php echo wp_strip_all_tags($faq_result_text); ?>."
    }
  }, {
    "@type": "Question",
    "name": "Combien de zéros finaux possède <?php echo $x; ?> factorielle ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Le résultat de <?php echo $x; ?> factorielle se termine par <?php echo $trailing_zeros; ?> zéros consécutifs. Ce nombre est défini par la quantité de paires de nombres premiers (2 et 5) présentes dans la décomposition du produit."
    }
  }, {
    "@type": "Question",
    "name": "Que veut dire le symbole <?php echo $x; ?>! ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "En mathématiques, le point d'exclamation suivant le nombre <?php echo $x; ?> (écrit <?php echo $x; ?>!) est le symbole de l'opération 'factorielle'. Il indique qu'il faut effectuer le produit de <?php echo $x; ?> par tous les entiers inférieurs jusqu'à 1."
    }
  }]
}
</script>

<?php get_footer(); ?>