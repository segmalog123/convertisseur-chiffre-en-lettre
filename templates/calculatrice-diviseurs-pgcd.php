<?php
/**
 * Template: Calculatrice Diviseurs et PGCD Landing Page (/calculatrice-diviseurs-pgcd-en-ligne/)
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

        <p>
            Notre outil mathématique gratuit vous permet de <strong>trouver les diviseurs d'un entier</strong>
            instantanément. Que vous cherchiez la liste complète des diviseurs d'un nombre unique ou que vous ayez
            besoin
            d'une <strong>calculatrice PGCD</strong> pour trouver les diviseurs communs entre deux nombres, il vous
            suffit d'entrer vos valeurs ci-dessus et de cliquer sur <strong>CALCULER</strong>.
        </p>

        <div class="ntw-fc-tip">
            <p>
                <strong>💡 Vous cherchez du calcul littéral ?</strong> Si vous souhaitez factoriser une équation
                algébrique
                contenant des lettres (comme des polynômes ou des identités remarquables), cet outil arithmétique n'est
                pas adapté. Utilisez plutôt notre outil dédié : ➔ <strong>Calculatrice de Factorisation
                    Littérale</strong>
            </p>
        </div>

        <h2 class="ntw-fc-h2">1) Comment utiliser notre Calculateur de Diviseurs ?</h2>

        <p>Notre système intelligent agit à la fois comme un extracteur de diviseurs et un <strong>pgcd
                calculateur</strong>.</p>

        <ul class="ntw-fc-steps">
            <li>
                <strong>Pour trouver les diviseurs d'un entier naturel :</strong> Entrez simplement un seul nombre (par
                exemple, 24) dans la barre de recherche. L'outil générera la liste de tous les diviseurs, y compris les
                diviseurs impairs et les diviseurs premiers.
            </li>
            <li>
                <strong>Pour trouver le PGCD (Plus Grand Commun Diviseur) :</strong> Entrez deux nombres séparés par une
                virgule (par exemple, 24, 42). Le système se transformera en <strong>pgcd en ligne</strong>, vous
                donnant les diviseurs communs, le PGCD, et même le PPCM (Plus Petit Commun Multiple).
            </li>
        </ul>

        <h2 class="ntw-fc-h2">2) Qu'est-ce qu'un diviseur d'un entier naturel ?</h2>

        <p>
            En mathématiques, on dit qu'un nombre entier <span class="ntw-fc-code">a</span> est un diviseur d'un entier
            naturel <span class="ntw-fc-code">b</span> si la division de <span class="ntw-fc-code">b</span> par <span
                class="ntw-fc-code">a</span> donne un résultat entier, sans aucun reste.
        </p>

        <p>Par exemple, si l'on cherche <strong>tous les diviseurs de 12</strong> :</p>
        <ul class="ntw-fc-steps">
            <li>12 divisé par 3 égale 4 (Le reste est 0). Donc, 3 et 4 sont des diviseurs de 12.</li>
            <li>12 divisé par 5 égale 2,4 (Il y a un reste/une décimale). Donc, 5 n'est pas un diviseur de 12.</li>
        </ul>

        <p>La liste complète des diviseurs de 12 est donc : 1, 2, 3, 4, 6, et 12.</p>

        <h3 class="ntw-fc-h3">Les Différents Types de Diviseurs</h3>
        <p>Lors des exercices de mathématiques, on vous demandera souvent de filtrer ces résultats. Notre outil calcule
            automatiquement :</p>
        <ul class="ntw-fc-steps">
            <li><strong>Les diviseurs impairs :</strong> Les nombres de la liste qui ne sont pas divisibles par 2 (ex: 1
                et 3 pour le nombre 12).</li>
            <li><strong>Les diviseurs premiers :</strong> Les diviseurs qui sont des nombres premiers, c'est-à-dire
                divisibles uniquement par 1 et par eux-mêmes (ex: 2 et 3 pour le nombre 12).</li>
        </ul>

        <h2 class="ntw-fc-h2">3) Trouver le PGCD et les Diviseurs Communs</h2>

        <p>
            Lorsque vous comparez deux nombres, vous cherchez à identifier les diviseurs qu'ils partagent. C'est ce
            qu'on appelle les <strong>diviseurs communs</strong>.
        </p>

        <p>
            Parmi cette liste de diviseurs partagés, le nombre le plus élevé est appelé le <strong>PGCD</strong> (Plus
            Grand Commun Diviseur). Notre <strong>calculatrice pgcd</strong> extrait ces données instantanément, ce qui
            est particulièrement utile pour simplifier des fractions au maximum.
        </p>

        <p><em>(Note : En plus du PGCD, notre algorithme affichera systématiquement le multiple commun (PPCM) sur les
                pages générées, car les deux concepts sont mathématiquement liés).</em></p>


        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <h3 class="ntw-fc-h3">Exemples de Calculs Fréquents</h3>

        <ul class="ntw-fc-pills">
            <li><a href="<?php echo esc_url(site_url('/diviseurs-de-24/')); ?>">Les diviseurs de 24</a></li>
            <li><a href="<?php echo esc_url(site_url('/diviseurs-de-12/')); ?>">Les diviseurs de 12</a></li>
            <li><a href="<?php echo esc_url(site_url('/diviseurs-de-16/')); ?>">Les diviseurs de 16</a></li>
            <li><a href="<?php echo esc_url(site_url('/diviseurs-de-5/')); ?>">Les diviseurs de 5</a></li>
            <li><a href="<?php echo esc_url(site_url('/pgcd-de-24-et-42/')); ?>">Diviseurs communs de 24 et 42</a></li>
            <li><a href="<?php echo esc_url(site_url('/pgcd-de-12-et-16/')); ?>">PGCD de 12 et 16</a></li>
            <li><a href="<?php echo esc_url(site_url('/diviseurs-de-100/')); ?>">Les diviseurs de 100</a></li>
            <li><a href="<?php echo esc_url(site_url('/pgcd-de-24-et-36/')); ?>">PGCD de 24 et 36</a></li>
        </ul>

        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <h2 class="ntw-fc-h2">FAQ</h2>

        <h3 class="ntw-fc-h3">Comment trouver les diviseurs d'un nombre ?</h3>
        <p>
            Pour trouver manuellement les diviseurs d'un entier naturel, il faut le diviser successivement par les
            nombres entiers (1, 2, 3, 4...) jusqu'à la racine carrée de ce nombre. Chaque fois que la division tombe
            juste (sans reste), le diviseur et le quotient font partie de la liste des diviseurs.
        </p>

        <h3 class="ntw-fc-h3">Quels sont tous les diviseurs de 24 ?</h3>
        <p>
            La liste complète de tous les diviseurs de 24 est : 1, 2, 3, 4, 6, 8, 12 et 24. Parmi eux, les diviseurs
            impairs sont 1 et 3, et les diviseurs premiers sont 2 et 3.
        </p>

        <h3 class="ntw-fc-h3">Comment utiliser cette calculatrice PGCD ?</h3>
        <p>
            Pour trouver le PGCD, tapez simplement vos deux nombres dans la barre de recherche (ex: 24, 42). L'outil
            extraira la liste des diviseurs pour chaque nombre, identifiera les diviseurs communs, et mettra en évidence
            le plus grand (le PGCD).
        </p>

    </div><!-- /.ntw-fc-wrap -->
</div><!-- .content -->

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
            // split by comma or spaces
            var parts = val.split(/[,\s]+/).map(function (s) { return s.trim(); }).filter(function (s) { return s !== ''; });

            if (parts.length === 1) {
                var num1 = parseInt(parts[0], 10);
                if (isNaN(num1) || num1 <= 0) {
                    if (err) err.textContent = 'Veuillez entrer un entier naturel supérieur à 0.';
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
                
                // Arrange small first
                var num1 = Math.min(raw1, raw2);
                var num2 = Math.max(raw1, raw2);
                
                if (err) err.textContent = '';
                window.location.href = '<?php echo esc_js(home_url("/")); ?>pgcd-de-' + num1 + '-et-' + num2 + '/';
            }
        }

        window.addEventListener('load', function () {
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.convert-button');
                // Target the button if it's the specific divisor action
                // For now, if we are on this page, the main button should do this.
                // Or if it has data-convert="divisors"
                if (btn && (btn.dataset.convert === 'divisors' || !btn.dataset.convert)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    doDivisorsRedirect();
                }
            }, true);
            var inp = document.querySelector('.convert-input');
            if (inp) inp.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    doDivisorsRedirect();
                }
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
    "name": "Comment trouver les diviseurs d'un nombre ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Pour trouver manuellement les diviseurs d'un entier naturel, il faut le diviser successivement par les nombres entiers (1, 2, 3, 4...) jusqu'à la racine carrée de ce nombre. Chaque fois que la division tombe juste (sans reste), le diviseur et le quotient font partie de la liste des diviseurs."
    }
  }, {
    "@type": "Question",
    "name": "Quels sont tous les diviseurs de 24 ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "La liste complète de tous les diviseurs de 24 est : 1, 2, 3, 4, 6, 8, 12 et 24. Parmi eux, les diviseurs impairs sont 1 et 3, et les diviseurs premiers sont 2 et 3."
    }
  }, {
    "@type": "Question",
    "name": "Comment utiliser cette calculatrice PGCD ?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Pour trouver le PGCD, tapez simplement vos deux nombres dans la barre de recherche (ex: 24, 42). L'outil extraira la liste des diviseurs pour chaque nombre, identifiera les diviseurs communs, et mettra en évidence le plus grand (le PGCD)."
    }
  }]
}
</script>

<?php get_footer(); ?>