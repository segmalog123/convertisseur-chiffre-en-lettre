<?php
/**
 * Template: /diviseurs-de-X/
 * Dynamic divisors result page — full computation + rich content.
 * Plugin: Convertisseur Chiffre en Lettre
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ─── 1. Get & sanitise the number ──────────────────────────────────────── */
global $wp_query;
$x = intval($wp_query->get('diviseur_id'));
if ($x < 1) {
    $x = 1;
}

/* ─── 2. Hard cap — serve 404 for numbers out of range ──────────────────── */
if ($x > 1000000) {
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    include(get_query_template('404'));
    exit;
}

/* ─── 3. Compute divisors ─────────────────────────────────────────────────── */

/**
 * Returns the sorted list of all divisors of $n.
 */
function cel_get_divisors(int $n): array
{
    $divisors = [];
    for ($i = 1; $i <= (int) sqrt($n); $i++) {
        if ($n % $i === 0) {
            $divisors[] = $i;
            if ($i !== $n / $i) {
                $divisors[] = $n / $i;
            }
        }
    }
    sort($divisors);
    return $divisors;
}

/**
 * Returns true if $n is prime.
 */
function cel_is_prime(int $n): bool
{
    if ($n < 2) return false;
    if ($n === 2) return true;
    if ($n % 2 === 0) return false;
    for ($i = 3; $i <= (int) sqrt($n); $i += 2) {
        if ($n % $i === 0) return false;
    }
    return true;
}

/**
 * Returns true if $n is a perfect square.
 */
function cel_is_perfect_square(int $n): bool
{
    $sqrt = (int) round(sqrt($n));
    return $sqrt * $sqrt === $n;
}

/**
 * Returns true if the given divisor-page number should be indexed.
 * Rule: x <= 200 → index; x > 200 && x % 50 === 0 → index; else noindex
 */
function cel_diviseur_is_indexable(int $n): bool
{
    if ($n < 1 || $n > 1000000) return false;
    if ($n <= 1000) return true;
    if ($n <= 10000 && $n % 50 === 0) return true;
    if ($n <= 1000000 && $n % 500 === 0) return true;
    return false;
}

$all_divisors     = cel_get_divisors($x);
$total_divisors   = count($all_divisors);

$odd_divisors     = array_values(array_filter($all_divisors, fn($d) => $d % 2 !== 0));
$prime_divisors   = array_values(array_filter($all_divisors, fn($d) => cel_is_prime($d)));
$small_divisors   = array_values(array_filter($all_divisors, fn($d) => $d < 10));

$is_prime         = ($total_divisors === 2);
$is_perfect_sq    = cel_is_perfect_square($x);
$sqrt_x           = (int) round(sqrt($x));
$sqrt_x_approx    = round(sqrt($x), 2);

// Sum of proper divisors (all divisors except X itself)
$sum_proper       = array_sum($all_divisors) - $x;

// Format lists
$list_all         = implode(', ', $all_divisors);
$list_odd         = $odd_divisors ? implode(', ', $odd_divisors) : '';
$list_prime       = $prime_divisors ? implode(', ', $prime_divisors) : '';
$list_small       = $small_divisors ? implode(', ', $small_divisors) : '';

/* ─── 4. Spin arrays ─────────────────────────────────────────────────────── */
$array_a = [
    "Solution mathématique : diviseurs d'un entier",
    "Calculateur des diviseurs de l'entier naturel {$x}",
    "Trouver tous les diviseurs de {$x}",
    "Propriétés et liste des diviseurs de {$x}",
];
$array_b = [
    "Quels sont tous les diviseurs de {$x} ?",
    "La liste complète des diviseurs de {$x}",
    "Les diviseurs de l'entier {$x} (Pairs, Impairs, Premiers)",
    "Comment trouver les diviseurs de {$x} ?",
];
$spin = ($x * 7 + 13) % 4;
$pretitle = $array_a[$spin];
$h1_text  = $array_b[$spin];

/* ─── 5. Nearby indexable links ──────────────────────────────────────────── */
$nearby_anchors = [
    "Quels sont les diviseurs de %d ?",
    "Liste des diviseurs de %d",
    "Tous les diviseurs de %d",
    "Diviseurs de l'entier %d",
];

// Build 4 below + 4 above, skip non-indexable and non-positive
$nearby_below = [];
for ($i = $x - 1; $i >= max(1, $x - 600) && count($nearby_below) < 4; $i--) {
    if (cel_diviseur_is_indexable($i)) {
        $nearby_below[] = $i;
    }
}
$nearby_below = array_reverse($nearby_below);

$nearby_above = [];
for ($i = $x + 1; $i <= min(1000000, $x + 600) && count($nearby_above) < 4; $i++) {
    if (cel_diviseur_is_indexable($i)) {
        $nearby_above[] = $i;
    }
}

$nearby_links = array_merge($nearby_below, $nearby_above);

// Example calculation lines (first 3 pairs)
$example_pairs = [];
$shown = 0;
foreach ($all_divisors as $d) {
    if ($d * $d > $x) break;
    $q = $x / $d;
    $example_pairs[] = ['d' => $d, 'q' => (int)$q];
    if (++$shown >= 3) break;
}

get_header();
?>
<style>
    /* ── Layout ── */
    #sidebar { display: none !important; }
    .content { width: 100% !important; }

    /* ── Wrapper ── */
    .nwf-wrap {
        max-width: 860px;
        margin: 0 auto;
        padding: 20px 15px 50px;
        font-family: inherit;
        color: #333;
        line-height: 1.7;
    }

    /* ── Result box ── */
    .nwf-result-box {
        background: #eafaf1;
        border: 1px solid #a2dbb8;
        border-radius: 8px;
        padding: 16px 22px;
        margin: 20px 0 28px;
    }
    .nwf-result-label { font-size: 13px; color: #555; margin-bottom: 6px; }
    .nwf-result-value {
        font-size: 1.5em;
        font-weight: 700;
        color: #1a5c30;
        word-break: break-all;
        line-height: 1.6;
    }
    .nwf-result-count {
        font-size: 14px;
        color: #2a7d4f;
        margin-top: 8px;
    }

    /* ── Headings ── */
    .nwf-h2 {
        font-size: 1.3em;
        font-weight: 700;
        color: #1a5c30;
        border-bottom: 2px solid #c5e8d3;
        padding-bottom: 6px;
        margin: 36px 0 14px;
    }
    .nwf-h3 {
        font-size: 1.05em;
        font-weight: 700;
        color: #2a7d4f;
        margin: 22px 0 8px;
    }

    /* ── Info box ── */
    .nwf-info-box {
        background: #f7fdf9;
        border-left: 4px solid #2a7d4f;
        border-radius: 0 6px 6px 0;
        padding: 12px 16px;
        margin: 16px 0;
        font-size: 14px;
        color: #333;
    }

    /* ── Classification table ── */
    .nwf-table {
        width: 100%;
        border-collapse: collapse;
        margin: 14px 0 22px;
        font-size: 14px;
    }
    .nwf-table th {
        background: #2a7d4f;
        color: #fff;
        padding: 9px 14px;
        text-align: left;
    }
    .nwf-table td {
        padding: 8px 14px;
        border-bottom: 1px solid #eee;
    }
    .nwf-table tr:nth-child(even) td { background: #f5faf7; }

    /* ── Formula ── */
    .nwf-formula-wrap { margin: 14px 0; }
    .nwf-formula {
        font-family: monospace;
        background: #f0f0f0;
        padding: 5px 12px;
        border-radius: 4px;
        display: inline-block;
        font-size: 0.95em;
    }

    /* ── Pill links ── */
    .nwf-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 12px 0 24px;
    }
    .nwf-pills li a {
        background: #2a7d4f;
        color: #fff;
        text-decoration: none;
        padding: 5px 13px;
        border-radius: 20px;
        font-size: 13px;
        display: inline-block;
    }
    .nwf-pills li a:hover { background: #1a5c30; }

    /* ── FAQ ── */
    .nwf-faq-item { border-bottom: 1px solid #e0e0e0; padding: 14px 0; }
    .nwf-faq-q { font-weight: 700; color: #1a5c30; margin-bottom: 6px; }
    .nwf-faq-a { color: #444; font-size: 14px; }
</style>

<div class="content">
    <?php chiffre_breadcrumbs(); ?>

    <div class="nwf-wrap">

        <!-- ══ RESULT BOX ══════════════════════════════════════════════════ -->
        <div class="nwf-result-box">
            <div class="nwf-result-label">Liste des diviseurs de <?php echo $x; ?></div>
            <div class="nwf-result-value"><?php echo esc_html($list_all); ?></div>
            <div class="nwf-result-count">
                <?php echo $x; ?> possède <strong><?php echo $total_divisors; ?> diviseur<?php echo $total_divisors > 1 ? 's' : ''; ?></strong>.
            </div>
        </div>

        <!-- ══ INTRO ════════════════════════════════════════════════════════ -->
        <p>
            Si vous vous demandez <strong>quels sont tous les diviseurs de <?php echo $x; ?></strong>, notre moteur
            mathématique a décomposé ce nombre pour vous. En arithmétique, un diviseur d'un entier naturel est un
            nombre qui le divise de manière exacte, c'est-à-dire sans laisser de reste.
        </p>
        <p>
            Le nombre <strong><?php echo $x; ?></strong> possède exactement
            <strong><?php echo $total_divisors; ?> diviseur<?php echo $total_divisors > 1 ? 's' : ''; ?></strong>.
            La <strong>liste des diviseurs de <?php echo $x; ?></strong> par ordre croissant est :
            <strong><?php echo esc_html($list_all); ?></strong>.
        </p>
        <?php if ($x > 50): ?>
        <p>
            Puisque <?php echo $x; ?> est un nombre relativement grand, il est utile de classer ses diviseurs par
            catégories (pairs, impairs et premiers) pour faciliter la résolution de vos exercices de mathématiques.
        </p>
        <?php endif; ?>

        <!-- ══ CLASSIFICATION ════════════════════════════════════════════════ -->
        <h2 class="nwf-h2">🧮 Classification des diviseurs de <?php echo $x; ?></h2>
        <p>
            Pour répondre aux questions les plus fréquentes en calcul arithmétique, nous avons filtré les diviseurs
            de l'entier <strong><?php echo $x; ?></strong> selon leurs propriétés mathématiques.
        </p>

        <table class="nwf-table">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Liste</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Tous les diviseurs</strong></td>
                    <td><?php echo esc_html($list_all); ?></td>
                    <td><?php echo $total_divisors; ?></td>
                </tr>
                <tr>
                    <td><strong>Diviseurs impairs</strong></td>
                    <td>
                        <?php if ($odd_divisors): ?>
                            <?php echo esc_html($list_odd); ?>
                        <?php else: ?>
                            <em>Aucun (excepté 1)</em>
                        <?php endif; ?>
                    </td>
                    <td><?php echo count($odd_divisors); ?></td>
                </tr>
                <tr>
                    <td><strong>Diviseurs premiers</strong></td>
                    <td>
                        <?php if ($prime_divisors): ?>
                            <?php echo esc_html($list_prime); ?>
                        <?php else: ?>
                            <em>Aucun</em>
                        <?php endif; ?>
                    </td>
                    <td><?php echo count($prime_divisors); ?></td>
                </tr>
                <?php if ($x >= 10): ?>
                <tr>
                    <td><strong>Diviseurs &lt; 10</strong></td>
                    <td><?php echo $small_divisors ? esc_html($list_small) : '<em>Aucun</em>'; ?></td>
                    <td><?php echo count($small_divisors); ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h3 class="nwf-h3">Quels sont les diviseurs impairs de <?php echo $x; ?> ?</h3>
        <p>
            Parmi la liste complète, les nombres qui ne sont pas divisibles par 2 sont appelés les <strong>diviseurs
            impairs</strong>.
            <?php if ($odd_divisors): ?>
                Les diviseurs impairs de <strong><?php echo $x; ?></strong> sont :
                <strong><?php echo esc_html($list_odd); ?></strong>.
            <?php else: ?>
                Le nombre <?php echo $x; ?> ne possède aucun diviseur impair à l'exception de 1.
            <?php endif; ?>
        </p>

        <h3 class="nwf-h3">Diviseurs de <?php echo $x; ?> qui sont des nombres premiers</h3>
        <p>
            La décomposition en facteurs premiers est essentielle pour trouver le PGCD ou simplifier des fractions.
            Parmi tous les diviseurs de <strong><?php echo $x; ?></strong>, ceux qui sont des nombres premiers
            (divisibles uniquement par 1 et par eux-mêmes) sont :
            <?php if ($prime_divisors): ?>
                <strong><?php echo esc_html($list_prime); ?></strong>.
            <?php else: ?>
                <strong>aucun</strong> (<?php echo $x; ?> n'admet pas de diviseur premier).
            <?php endif; ?>
        </p>

        <?php if ($x >= 10): ?>
        <h3 class="nwf-h3">Diviseurs de <?php echo $x; ?> inférieurs à 10</h3>
        <p>
            Dans de nombreux problèmes de mathématiques au collège, il est demandé de se concentrer sur les petits
            diviseurs pour simplifier mentalement le calcul. Les diviseurs de <strong><?php echo $x; ?></strong>
            strictement inférieurs à 10 sont :
            <?php if ($small_divisors): ?>
                <strong><?php echo esc_html($list_small); ?></strong>.
            <?php else: ?>
                <strong>aucun</strong>.
            <?php endif; ?>
        </p>
        <?php endif; ?>

        <!-- ══ PROPERTIES ════════════════════════════════════════════════════ -->
        <h2 class="nwf-h2">🔬 Propriétés et Nature du nombre <?php echo $x; ?></h2>

        <?php if ($is_prime): ?>
        <div class="nwf-info-box">
            💡 <strong>Le saviez-vous ?</strong> L'entier naturel <strong><?php echo $x; ?></strong> est un
            <strong>nombre premier</strong>. Cela signifie qu'il ne possède que deux diviseurs distincts : 1 et
            lui-même. Il ne peut être divisé par aucun autre nombre entier.
        </div>
        <?php endif; ?>

        <?php if ($is_perfect_sq): ?>
        <div class="nwf-info-box">
            📐 <strong>Fait mathématique intéressant :</strong> <?php echo $x; ?> est un <strong>carré parfait</strong>
            (car <?php echo $sqrt_x; ?> × <?php echo $sqrt_x; ?> = <?php echo $x; ?>). En arithmétique, les carrés
            parfaits sont les seuls nombres qui possèdent un <strong>nombre impair de diviseurs</strong>
            (ici, <?php echo $total_divisors; ?>).
        </div>
        <?php endif; ?>

        <?php if ($sum_proper === $x): ?>
        <div class="nwf-info-box">
            ⭐ Le nombre <strong><?php echo $x; ?></strong> est classé comme un <strong>Nombre Parfait</strong>. C'est
            une propriété extrêmement rare en mathématiques où la somme de ses diviseurs stricts est exactement égale
            au nombre lui-même !
        </div>
        <?php elseif ($sum_proper > $x): ?>
        <div class="nwf-info-box">
            Si l'on additionne tous les diviseurs stricts de <strong><?php echo $x; ?></strong> (sans compter
            <?php echo $x; ?> lui-même), on obtient <strong><?php echo number_format($sum_proper, 0, ',', ' '); ?></strong>.
            Puisque cette somme est supérieure au nombre lui-même, <strong><?php echo $x; ?></strong> est
            mathématiquement classé comme un <strong>Nombre Abondant</strong>.
        </div>
        <?php else: ?>
        <div class="nwf-info-box">
            La somme des diviseurs propres de <strong><?php echo $x; ?></strong> est égale à
            <strong><?php echo number_format($sum_proper, 0, ',', ' '); ?></strong>. Comme ce total est inférieur
            au nombre de départ, <strong><?php echo $x; ?></strong> est considéré en arithmétique comme un
            <strong>Nombre Déficient</strong>.
        </div>
        <?php endif; ?>

        <!-- ══ METHOD ════════════════════════════════════════════════════════ -->
        <h2 class="nwf-h2">📝 Comment trouver les diviseurs de <?php echo $x; ?> ? (La Méthode)</h2>
        <p>
            Pour trouver manuellement les diviseurs d'un entier naturel comme <strong><?php echo $x; ?></strong>,
            la méthode la plus rapide est de tester la division pour tous les nombres entiers en allant de 1 jusqu'à
            la racine carrée de <strong><?php echo $x; ?></strong> (qui est d'environ
            <strong><?php echo $sqrt_x_approx; ?></strong>).
            Si la division donne un nombre entier, nous avons trouvé une paire de diviseurs :
        </p>
        <ul style="list-style: disc; padding-left: 22px; font-size: 15px; line-height: 2;">
            <?php foreach ($example_pairs as $pair): ?>
            <li>
                <span class="nwf-formula"><?php echo $x; ?> ÷ <?php echo $pair['d']; ?> = <?php echo $pair['q']; ?></span>
                → Donc <strong><?php echo $pair['d']; ?></strong>
                <?php if ($pair['d'] !== $pair['q']): ?> et <strong><?php echo $pair['q']; ?></strong><?php endif; ?>
                sont des diviseurs.
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- ══ RECHERCHES ASSOCIÉES ══════════════════════════════════════════ -->
        <h2 class="nwf-h2">🌐 Recherches associées</h2>
        <ul class="nwf-pills">
            <?php
            $i_anchor = 0;
            foreach ($nearby_links as $n):
                $anchor = sprintf($nearby_anchors[$i_anchor % 4], $n);
                $i_anchor++;
            ?>
            <li>
                <a href="<?php echo esc_url(home_url('/diviseurs-de-' . $n . '/')); ?>">
                    <?php echo esc_html($anchor); ?>
                </a>
            </li>
            <?php endforeach; ?>
            <!-- Cross-links to other converters for the same number X -->
            <?php
            /**
             * Mirrors NumberVipList rules (src/NumberVipList.php):
             * 1–1000 all ; 1001–10000 step 50 ; 10001–100000 step 500 ; 100001–1000000 step 5000
             * + hardcoded exceptions (years, common cheque amounts, 1000000)
             */
            $vip_hardcoded = [2019,2020,2021,2022,2023,2024,2025,2026,1500,2500,3500,4500,7500,15000,25000,35000,45000,75000,150000,250000,350000,450000,750000,1000000];
            $x_is_vip = ($x <= 1000)
                || ($x <= 10000   && $x % 50   === 0)
                || ($x <= 100000  && $x % 500  === 0)
                || ($x <= 1000000 && $x % 5000 === 0)
                || in_array($x, $vip_hardcoded);
            if ($x_is_vip): ?>
            <li><a href="<?php echo esc_url(home_url('/ecrire/' . $x . '-en-lettre/')); ?>"><?php echo $x; ?> en lettres</a></li>
            <li><a href="<?php echo esc_url(home_url('/comment-on-dit/' . $x . '-en-anglais/')); ?>"><?php echo $x; ?> en anglais</a></li>
            <?php endif; ?>
            <?php
            // Factorielle cross-link: only if page exists (x <= 10000) AND is indexed (x <= 200 or x % 50 === 0)
            $fact_is_indexed = ($x <= 200) || ($x > 200 && $x <= 10000 && $x % 50 === 0);
            if ($fact_is_indexed): ?>
            <li><a href="<?php echo esc_url(home_url('/factorielle-de-' . $x . '/')); ?>">Factorielle de <?php echo $x; ?></a></li>
            <?php endif; ?>
        </ul>

        <!-- ══ FAQ ════════════════════════════════════════════════════════════ -->
        <h2 class="nwf-h2">FAQ</h2>

        <div class="nwf-faq-item">
            <div class="nwf-faq-q">Quels sont tous les diviseurs de <?php echo $x; ?> ?</div>
            <div class="nwf-faq-a">
                Les diviseurs de l'entier naturel <?php echo $x; ?> sont tous les nombres qui le divisent sans
                laisser de reste. La liste complète est : <?php echo esc_html($list_all); ?>.
            </div>
        </div>

        <div class="nwf-faq-item">
            <div class="nwf-faq-q">Quels sont les diviseurs impairs de <?php echo $x; ?> ?</div>
            <div class="nwf-faq-a">
                Parmi l'ensemble de ses diviseurs, les diviseurs impairs de <?php echo $x; ?> sont :
                <?php echo $odd_divisors ? esc_html($list_odd) : '1 uniquement'; ?>.
            </div>
        </div>

        <div class="nwf-faq-item">
            <div class="nwf-faq-q">Combien de diviseurs possède <?php echo $x; ?> ?</div>
            <div class="nwf-faq-a">
                Le nombre <?php echo $x; ?> possède un total de <strong><?php echo $total_divisors; ?></strong>
                diviseur<?php echo $total_divisors > 1 ? 's' : ''; ?>.
                <?php if ($is_prime): ?>
                    Puisqu'il n'en possède que deux, c'est un <strong>nombre premier</strong>.
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.nwf-wrap -->
</div><!-- .content -->

<!-- ══ SEARCH BAR REDIRECT ═══════════════════════════════════════════════════ -->
<script>
(function () {
    function doDivisorsRedirect() {
        var input = document.querySelector('.convert-input');
        if (!input) return;
        var val = input.value.trim();
        var err = document.querySelector('.error-input');
        if (!val) {
            if (err) err.textContent = 'Veuillez entrer un nombre ou deux nombres séparés par une virgule.';
            return;
        }
        var parts = val.split(/[,\s]+/).map(function (s) { return s.trim(); }).filter(function (s) { return s !== ''; });
        if (parts.length === 1) {
            var num1 = parseInt(parts[0], 10);
            if (isNaN(num1) || num1 <= 0) {
                if (err) err.textContent = 'Veuillez entrer un entier naturel supérieur à 0.';
                return;
            }
            if (num1 > 1000000) {
                if (err) err.textContent = 'Les calculs sont limités aux nombres de 1 à 1 000 000.';
                return;
            }
            if (err) err.textContent = '';
            window.location.href = '<?php echo esc_js(home_url("/")); ?>diviseurs-de-' + num1 + '/';
        } else if (parts.length >= 2) {
            var raw1 = parseInt(parts[0], 10);
            var raw2 = parseInt(parts[1], 10);
            if (isNaN(raw1) || isNaN(raw2) || raw1 <= 0 || raw2 <= 0) {
                if (err) err.textContent = 'Veuillez entrer deux entiers naturels supérieurs à 0.';
                return;
            }
            if (raw1 > 200 || raw2 > 200) {
                if (err) err.textContent = 'Les calculs de PGCD sont limités aux nombres de 1 à 200.';
                return;
            }
            var mn = Math.min(raw1, raw2);
            var mx = Math.max(raw1, raw2);
            if (err) err.textContent = '';
            window.location.href = '<?php echo esc_js(home_url("/")); ?>pgcd-de-' + mn + '-et-' + mx + '/';
        }
    }
    window.addEventListener('load', function () {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.convert-button[data-convert="divisors"]');
            if (btn) { e.preventDefault(); e.stopImmediatePropagation(); doDivisorsRedirect(); }
        }, true);
        var inp = document.querySelector('.convert-input');
        if (inp) inp.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); e.stopImmediatePropagation(); doDivisorsRedirect(); }
        }, true);
    });
}());
</script>

<!-- ══ JSON-LD FAQPage schema ═════════════════════════════════════════════════ -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "Quels sont tous les diviseurs de <?php echo $x; ?> ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Les diviseurs de l'entier naturel <?php echo $x; ?> sont tous les nombres qui le divisent sans laisser de reste. La liste complète est : <?php echo esc_js($list_all); ?>."
    }
  }, {
    "@type": "Question",
    "name": "Quels sont les diviseurs impairs de <?php echo $x; ?> ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Parmi l'ensemble de ses diviseurs, les diviseurs impairs de <?php echo $x; ?> sont : <?php echo esc_js($odd_divisors ? $list_odd : '1 uniquement'); ?>."
    }
  }, {
    "@type": "Question",
    "name": "Combien de diviseurs possède <?php echo $x; ?> ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Le nombre <?php echo $x; ?> possède un total de <?php echo $total_divisors; ?> diviseur<?php echo $total_divisors > 1 ? 's' : ''; ?><?php echo $is_prime ? '. Puisqu\'il n\'en possède que deux, c\'est un nombre premier.' : '.'; ?>"
    }
  }]
}
</script>

<?php get_footer(); ?>
