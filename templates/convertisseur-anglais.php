<?php
/**
 * Template: English Number Converter Landing Page (/convertisseur-anglais/)
 * Plugin: Convertisseur Chiffre en Lettre
 * Modern standalone design — independent of theme.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<style>
    /* Force full width and hide sidebar for this specific template */
    #sidebar {
        display: none !important;
    }

    .content {
        width: 100% !important;
    }
</style>
<?php
?>

<div class="content">
    <?php chiffre_breadcrumbs(); ?>

    <div style="margin:0 auto; padding:0 10px 30px;">

        <!-- ═══ HERO: Intro ══════════════════════════════════════════ -->
        <div class="cel-result-hero">
            <p style="font-size: 16px; line-height: 1.5; opacity: 0.95; margin: 0 auto; max-width: 600px;">
                Convertissez instantanément n'importe quel chiffre en lettres anglaises.
                Idéal pour chèques, contrats et apprentissage.
            </p>
        </div>

        <!-- ═══ CURRENCY CARDS (Static Examples) ═════════════════════ -->
        <div class="cel-cards-grid">
            <div class="cel-card">
                <p class="cel-card-label">🇬🇧 Number</p>
                <p class="cel-card-value">One hundred</p>
            </div>
            <div class="cel-card">
                <p class="cel-card-label">💵 Dollars</p>
                <p class="cel-card-value">One hundred dollars</p>
            </div>
            <div class="cel-card">
                <p class="cel-card-label">💶 Euros</p>
                <p class="cel-card-value">One hundred euros</p>
            </div>
            <div class="cel-card">
                <p class="cel-card-label">🍁 Pounds</p>
                <p class="cel-card-value">One hundred pounds</p>
            </div>
        </div>

        <!-- ═══ HOW TO USE ═══════════════════════════════════════════ -->
        <div class="cel-section">
            <h2 class="cel-section-title">Comment utiliser le convertisseur ?</h2>
            <ol style="margin-left: 20px; color: #444; line-height: 1.8;">
                <li>Tapez le nombre à convertir dans le champ ci-dessus.</li>
                <li>Cliquez sur <strong>CONVERTIR</strong>.</li>
                <li>Le résultat s'affiche instantanément en anglais (entier, dollars, euros, livres).</li>
            </ol>
        </div>

        <!-- ═══ EXAMPLES PILLS ═══════════════════════════════════════ -->
        <div class="cel-section">
            <h2 class="cel-section-title">Exemples de conversions fréquentes</h2>
            <ul class="cel-pills">
                <?php
                $popular = [1, 10, 20, 50, 100, 200, 500, 1000, 10000, 1000000];
                foreach ($popular as $n):
                    $url = esc_url(home_url('/comment-on-dit/' . $n . '-en-anglais/'));
                    ?>
                    <li>
                        <a href="<?php echo $url; ?>">
                            <?php echo number_format($n, 0, ',', ' '); ?> en anglais
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- ═══ FAQ with Schema.org ══════════════════════════════════ -->
        <div class="cel-section" itemscope itemtype="https://schema.org/FAQPage">
            <h2 class="cel-section-title">Questions fréquentes (FAQ)</h2>

            <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" style="margin-bottom: 20px;">
                <h3 itemprop="name" style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">
                    Comment dit-on 1000 en anglais ?
                </h3>
                <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <p itemprop="text">
                        On dit <em>one thousand</em>. En anglais, le mot «&nbsp;thousand&nbsp;» reste invariable
                        lorsqu'il est précédé d'un chiffre précis.
                    </p>
                </div>
            </div>

            <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" style="margin-bottom: 20px;">
                <h3 itemprop="name" style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">
                    Comment écrire les centaines ?
                </h3>
                <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <p itemprop="text">
                        On utilise <em>hundred</em> : 200 = <em>two hundred</em>. Comme pour thousand,
                        il ne prend pas de «&nbsp;s&nbsp;» avec un chiffre devant.
                    </p>
                </div>
            </div>

            <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" style="margin-bottom: 20px;">
                <h3 itemprop="name" style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">
                    Différence anglais UK vs US ?
                </h3>
                <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <p itemprop="text">
                        <strong>UK :</strong> <em>one hundred and twenty</em> (avec «&nbsp;and&nbsp;»).<br>
                        <strong>US :</strong> <em>one hundred twenty</em> (souvent sans «&nbsp;and&nbsp;»).
                    </p>
                </div>
            </div>

            <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" style="margin-bottom: 20px;">
                <h3 itemprop="name" style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">
                    Milliard vs Billion ?
                </h3>
                <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <p itemprop="text">
                        Attention aux faux amis !<br>
                        1 Milliard (10<sup>9</sup>) = <em>one billion</em> en anglais.<br>
                        1 Billion (10<sup>12</sup>) = <em>one trillion</em> en anglais.
                    </p>
                </div>
            </div>

            <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                <h3 itemprop="name" style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">
                    Convertir dans Excel ?
                </h3>
                <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <p itemprop="text">
                        Excel n'a pas de fonction native pour l'anglais. Vous pouvez télécharger notre
                        <a href="<?php echo esc_url(site_url('/category/macro-excel/')); ?>">macro Excel gratuite</a>
                        pour le faire automatiquement.
                    </p>
                </div>
            </div>
        </div>

        <!-- Schema.org FAQPage JSON-LD -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Comment dit-on 1000 en anglais ?",
                    "acceptedAnswer": { "@type": "Answer", "text": "On dit one thousand. En anglais, le mot 'thousand' reste invariable lorsqu'il est précédé d'un chiffre précis." }
                },
                {
                    "@type": "Question",
                    "name": "Comment écrire les centaines ?",
                    "acceptedAnswer": { "@type": "Answer", "text": "On utilise 'hundred' : 200 = two hundred. Comme pour thousand, il ne prend pas de 's' avec un chiffre devant." }
                },
                {
                    "@type": "Question",
                    "name": "Différence anglais UK vs US ?",
                    "acceptedAnswer": { "@type": "Answer", "text": "UK: one hundred and twenty. US: one hundred twenty (souvent sans 'and')." }
                },
                {
                    "@type": "Question",
                    "name": "Milliard vs Billion ?",
                    "acceptedAnswer": { "@type": "Answer", "text": "Attention aux faux amis ! 1 Milliard (10^9) = one billion en anglais. 1 Billion (10^12) = one trillion en anglais." }
                },
                {
                    "@type": "Question",
                    "name": "Convertir dans Excel ?",
                    "acceptedAnswer": { "@type": "Answer", "text": "Excel n'a pas de fonction native pour l'anglais. Vous pouvez télécharger notre macro Excel gratuite sur le site." }
                }
            ]
        }
        </script>

        <!-- ═══ EXCEL SECTION ════════════════════════════════════════ -->
        <div class="cel-section">
            <h2 class="cel-section-title">Macro Excel Anglaise</h2>
            <p>
                Gagnez du temps dans vos factures et devis. Téléchargez notre module VBA pour convertir automatiquement
                vos montants en lettres anglaises directement dans Excel.
            </p>
            <a class="cel-crosslink" href="<?php echo esc_url(site_url('/category/macro-excel/')); ?>">
                📥 Télécharger la Macro Excel
            </a>
        </div>

    </div><!-- /max-width wrapper -->
</div><!-- .content -->

<?php
get_footer();
?>