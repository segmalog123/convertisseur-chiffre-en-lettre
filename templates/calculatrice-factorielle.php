<?php
/**
 * Template: Calculatrice Factorielle Landing Page (/calculatrice-factorielle/)
 * Plugin: Convertisseur Chiffre en Lettre
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<style>
    /* ── Layout ── */
    #sidebar {
        display: none !important;
    }

    .content {
        width: 100% !important;
    }

    /* ── Wrapper ── */
    .ntw-fc-wrap {
        max-width: 860px;
        margin: 0 auto;
        padding: 20px 15px 50px;
        font-family: inherit;
        color: #333;
        line-height: 1.7;
    }

    /* ── Intro box ── */
    .ntw-fc-intro {
        background: #f7fdf9;
        border-left: 4px solid #2a7d4f;
        padding: 14px 18px;
        border-radius: 6px;
        margin-bottom: 28px;
        font-size: 15px;
        color: #444;
    }

    .ntw-fc-intro p {
        margin: 0;
    }

    /* ── Tip box ── */
    .ntw-fc-tip {
        background: #fffbe6;
        border: 1px dashed #e0c000;
        border-radius: 6px;
        padding: 12px 16px;
        margin: 14px 0 20px;
        font-size: 14px;
    }

    .ntw-fc-tip strong {
        color: #7a5c00;
    }

    /* ── Headings ── */
    .ntw-fc-h2 {
        font-size: 1.35em;
        font-weight: 700;
        color: #1a5c30;
        border-bottom: 2px solid #c5e8d3;
        padding-bottom: 6px;
        margin: 36px 0 14px;
    }

    .ntw-fc-h3 {
        font-size: 1.1em;
        font-weight: 700;
        color: #2a7d4f;
        margin: 24px 0 10px;
    }

    /* ── Tables ── */
    .ntw-fc-table {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0 24px;
        font-size: 14px;
    }

    .ntw-fc-table th {
        background: #2a7d4f;
        color: #fff;
        padding: 9px 14px;
        text-align: left;
    }

    .ntw-fc-table td {
        padding: 8px 14px;
        border-bottom: 1px solid #eee;
    }

    .ntw-fc-table tr:nth-child(even) td {
        background: #f5faf7;
    }

    .ntw-fc-table tr:hover td {
        background: #e8f7ee;
    }

    /* ── Math formula display ── */
    .ntw-fc-formula-wrap {
        text-align: center;
        margin: 16px 0;
        padding: 14px;
        background: #f5faf7;
        border-radius: 6px;
    }

    .ntw-fc-formula {
        font-size: 1.25em;
        font-style: italic;
        font-family: Georgia, serif;
        color: #1a1a1a;
        letter-spacing: 0.03em;
    }

    /* ── Highlight box ── */
    .ntw-fc-highlight {
        background: #eafaf1;
        border: 1px solid #a2dbb8;
        border-radius: 6px;
        padding: 14px 18px;
        margin: 16px 0;
        font-size: 15px;
    }

    /* ── Step list ── */
    .ntw-fc-steps {
        padding-left: 0;
        list-style: none;
        margin: 12px 0 18px;
    }

    .ntw-fc-steps li {
        padding: 8px 0 8px 0;
        border-bottom: 1px solid #eee;
    }

    .ntw-fc-steps li:last-child {
        border: none;
    }

    .ntw-fc-steps .step-label {
        display: inline-block;
        background: #2a7d4f;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* ── Pill links ── */
    .ntw-fc-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        list-style: none;
        padding: 0;
        margin: 14px 0 24px;
    }

    .ntw-fc-pills li a {
        background: #2a7d4f;
        color: #fff;
        text-decoration: none;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        transition: background 0.2s;
    }

    .ntw-fc-pills li a:hover {
        background: #1a5c30;
    }

    /* ── Inline code style ── */
    .ntw-fc-code {
        background: #f0f0f0;
        border-radius: 4px;
        padding: 1px 6px;
        font-family: monospace;
        font-size: 0.95em;
    }
</style>

<div class="content">
    <?php chiffre_breadcrumbs(); ?>

    <div class="ntw-fc-wrap">

        <!-- ═══════════════════ INTRO ═════════════════════════════════ -->
        <div class="ntw-fc-intro">
            <p>
                Notre <strong>calculatrice factorielle</strong> en ligne gratuite vous permet de trouver instantanément
                la factorielle de n'importe quel entier positif. Tapez simplement un nombre dans la case en haut de la
                page et cliquez sur <strong>CALCULER</strong>. Vous obtiendrez le résultat exact, la notation
                scientifique pour les nombres très grands, ainsi qu'une décomposition étape par étape du calcul.
            </p>
        </div>

        <div class="ntw-fc-tip">
            <p>
                <strong>💡 Astuce rapide :</strong> Pour effectuer un <em>factorielle calcul</em> spécifique, saisissez
                simplement le chiffre dans la barre de recherche ci-dessus. Vous pouvez également parcourir nos pages
                dédiées ci-dessous pour des explications détaillées, des propriétés mathématiques et des exemples
                pratiques.
            </p>
        </div>

        <!-- ═══════════════════ SECTION 1 ════════════════════════════ -->
        <h2 class="ntw-fc-h2">1) Comment utiliser notre calculatrice factorielle ?</h2>

        <p>Le calcul de la factorielle est une opération courante mais fastidieuse à faire à la main. Notre outil
            simplifie ce processus en 3 étapes :</p>

        <ul class="ntw-fc-steps">
            <li>
                <span class="step-label">Étape 1</span>
                Entrez le nombre que vous souhaitez calculer dans le champ de saisie en haut de la page.
            </li>
            <li>
                <span class="step-label">Étape 2</span>
                Cliquez sur le bouton <strong>CALCULER</strong>.
            </li>
            <li>
                <span class="step-label">Étape 3</span>
                L'équivalent mathématique exact s'affiche instantanément. Copiez-le pour vos devoirs ou étudiez la
                décomposition détaillée.
            </li>
        </ul>

        <div class="ntw-fc-highlight">
            <strong>Remarque sur les grands nombres :</strong> Notre moteur est conçu pour gérer des nombres massifs.
            Pour les résultats astronomiques, il utilise automatiquement la notation scientifique ou applique la
            <strong>formule de Stirling</strong> pour fournir une approximation extrêmement précise.
        </div>

        <!-- ═══════════════════ SECTION 2 ════════════════════════════ -->
        <h2 class="ntw-fc-h2">2) Qu'est-ce que le factoriel d'un nombre ? (Définition)</h2>

        <p>
            En mathématiques, le <strong>factoriel d'un nombre</strong> entier naturel <em>n</em>, noté avec un point
            d'exclamation <span class="ntw-fc-code">n!</span>, est le produit de tous les entiers positifs strictement
            inférieurs ou égaux à ce nombre. C'est un concept fondamental utilisé principalement en combinatoire, en
            algèbre et en probabilités pour calculer des permutations et des combinaisons.
        </p>

        <!-- 2.1 The Formula -->
        <h3 class="ntw-fc-h3">La Formule de la Factorielle n</h3>

        <p>La formule standard pour calculer la <strong>factorielle n</strong> s'écrit de la manière suivante :</p>
        <div class="ntw-fc-formula-wrap">
            <span class="ntw-fc-formula">
                <em>n</em>! = <em>n</em> &times; (<em>n</em> &minus; 1) &times; (<em>n</em> &minus; 2) &times; &hellip;
                &times; 1
            </span>
        </div>

        <p>
            Il existe également une définition récursive, très utilisée en programmation informatique, qui définit une
            factorielle en fonction de la factorielle du nombre précédent :
        </p>
        <div class="ntw-fc-formula-wrap">
            <span class="ntw-fc-formula">
                <em>n</em>! = <em>n</em> &times; (<em>n</em> &minus; 1)!
            </span>
        </div>

        <!-- 2.2 Basic Factorials -->
        <h3 class="ntw-fc-h3">Tableau de référence : Les Factorielles de Base (1 à 5)</h3>

        <p>
            Mémoriser les premières factorielles est essentiel pour le calcul mental. Par exemple, connaître par cœur la
            <strong>factorielle de 5</strong> vous fera gagner beaucoup de temps.
        </p>

        <table class="ntw-fc-table">
            <thead>
                <tr>
                    <th>Nombre (<em>n</em>)</th>
                    <th>Factorielle (<em>n</em>!)</th>
                    <th>Décomposition du Calcul</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>1</td>
                    <td>1</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>2</td>
                    <td>2 &times; 1</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>6</td>
                    <td>3 &times; 2 &times; 1</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>24</td>
                    <td>4 &times; 3 &times; 2 &times; 1</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>120</td>
                    <td>5 &times; 4 &times; 3 &times; 2 &times; 1</td>
                </tr>
            </tbody>
        </table>

        <!-- ═══════════════════ SECTION 3 ════════════════════════════ -->
        <h2 class="ntw-fc-h2">3) Règles Spéciales en Mathématiques</h2>

        <p>
            Les factorielles obéissent à un ensemble de règles strictes, en particulier lorsqu'il s'agit du chiffre zéro
            ou des grands nombres.
        </p>

        <!-- 3.1 Zero -->
        <h3 class="ntw-fc-h3">Pourquoi la factorielle de 0 est égale à 1 ?</h3>

        <p>C'est l'une des questions les plus posées en mathématiques : que vaut <strong>0 factorielle</strong> ? Par
            convention mathématique universelle, la <strong>factorielle de 0</strong> est exactement égale à un :</p>
        <div class="ntw-fc-formula-wrap">
            <span class="ntw-fc-formula">0! = 1</span>
        </div>
        <p>
            On appelle cela un <strong>"produit vide"</strong>. Cette règle est absolument nécessaire pour que les
            formules mathématiques de permutations et d'arrangements restent valides même lorsque l'on choisit zéro
            élément dans un ensemble.
        </p>

        <!-- 3.2 52 Factoriel -->
        <h3 class="ntw-fc-h3">L'exemple de la Factorielle 52 (Permutations)</h3>

        <p>
            Les factorielles grandissent de manière exponentielle. Prenons l'exemple célèbre de la <strong>factorielle
                52</strong> (<span class="ntw-fc-code">52!</span>). Ce nombre représente le nombre total de façons
            possibles de mélanger un jeu standard de 52 cartes. Ce chiffre est si gigantesque (environ 8.06 &times;
            10<sup>67</sup>) que chaque fois que vous battez correctement un jeu de cartes, vous créez très probablement
            une séquence de cartes qui n'a jamais existé auparavant dans toute l'histoire de l'humanité !
        </p>

        <!-- ═══════════════════ POPULAR LINKS ════════════════════════ -->
        <h3 class="ntw-fc-h3">Calculs Factoriels Populaires</h3>

        <ul class="ntw-fc-pills">
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-5/')); ?>">Factorielle de 5</a></li>
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-3/')); ?>">3 factoriel</a></li>
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-0/')); ?>">Factorielle de 0</a></li>
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-4/')); ?>">4 factoriel</a></li>
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-1/')); ?>">Factorielle de 1</a></li>
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-2/')); ?>">Factorielle de 2</a></li>
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-52/')); ?>">factorielle 52</a></li>
            <li><a href="<?php echo esc_url(site_url('/factorielle-de-10/')); ?>">Factorielle de 10</a></li>
        </ul>

        <!-- ═══════════════════ FAQ ══════════════════════════════════ -->
        <h2 class="ntw-fc-h2">FAQ</h2>

        <h3 class="ntw-fc-h3">Comment calculer le factoriel d'un nombre ?</h3>
        <p>
            Pour calculer le factoriel d'un nombre <em>n</em>, vous devez multiplier ce nombre par tous les entiers
            positifs qui le précèdent jusqu'à 1. Par exemple, pour 4, le calcul est 4 &times; 3 &times; 2 &times; 1 =
            24.
        </p>

        <h3 class="ntw-fc-h3">Combien vaut la factorielle de 0 ?</h3>
        <p>
            Par convention mathématique, la factorielle de 0 vaut 1 (0! = 1). Cela garantit que les équations
            algébriques de combinaisons fonctionnent correctement.
        </p>

        <h3 class="ntw-fc-h3">Qu'est-ce que la formule de Stirling ?</h3>
        <p>
            La formule de Stirling est une formule mathématique utilisée pour trouver une excellente approximation des
            factorielles de très grands nombres, là où les calculatrices classiques affichent une erreur de dépassement
            de capacité.
        </p>

    </div><!-- /.ntw-fc-wrap -->
</div><!-- .content -->

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
                if (e.key === 'Enter') { e.preventDefault(); e.stopImmediatePropagation(); doFactorialRedirect(); }
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
    "name": "Comment calculer le factoriel d'un nombre ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Pour calculer le factoriel d'un nombre n, vous devez multiplier ce nombre par tous les entiers positifs qui le précèdent jusqu'à 1. Par exemple, pour 4, le calcul est 4 × 3 × 2 × 1 = 24."
    }
  }, {
    "@type": "Question",
    "name": "Combien vaut la factorielle de 0 ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Par convention mathématique, la factorielle de 0 vaut 1 (0! = 1). Cela garantit que les équations algébriques de combinaisons fonctionnent correctement."
    }
  }, {
    "@type": "Question",
    "name": "Qu'est-ce que la formule de Stirling ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "La formule de Stirling est une formule mathématique utilisée pour trouver une excellente approximation des factorielles de très grands nombres, là où les calculatrices classiques affichent une erreur de dépassement de capacité."
    }
  }]
}
</script>

<?php get_footer(); ?>