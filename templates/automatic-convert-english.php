<?php
/**
 * Template: English Number Conversion Results
 * Plugin: Convertisseur Chiffre en Lettre
 * Modern standalone design — independent of theme.
 */

if (!defined('ABSPATH')) {
    exit;
}

use ChiffreEnLettre\Converters\ConverterHelper;

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

global $wp_query;
$number_to_convert = $wp_query->get('number_id');
if (!isset($number_to_convert) || $number_to_convert === '') {
    $number_to_convert = '';
}
$number_to_convert_php = (float) str_replace(['.', ','], ['', '.'], $number_to_convert);

// Pre-compute all conversions once
$result_en_plain = ucfirst(funcConvert($number_to_convert, 'convert', '0'));
$result_en_euros = ucfirst(funcConvert($number_to_convert, 'convert', '5'));
$result_en_cad = ucfirst(funcConvert($number_to_convert, 'convert', '3'));
$result_en_dinar = ucfirst(funcConvert($number_to_convert, 'convert', '4'));
$result_en_dollars = ucfirst(funcConvert($number_to_convert, 'convert', '1'));
$result_fr_plain = ucfirst(enChiffre($number_to_convert)['final_number_lettre']);
$similar = funcListNumber($number_to_convert);
$percent_number = funcPercent($number_to_convert);
$similar_8_fr = ucfirst(enChiffre($similar[8])['final_number_lettre']);
$url_fr = esc_url(site_url('/ecrire/' . str_replace('.', ',', $number_to_convert) . '-en-lettre/'));

$is_vip = \ChiffreEnLettre\NumberVipList::isVip($number_to_convert);

// Phase 3 & 5: Content Enrichment & Gating
$cheque_data = \ChiffreEnLettre\ContentGenerator::getChequeData($number_to_convert, $result_en_dollars, 'Dollars', 'en');

if ($is_vip) {
    $math_facts = \ChiffreEnLettre\ContentGenerator::getMathFacts($number_to_convert);
    $grammar_rules = \ChiffreEnLettre\ContentGenerator::getGrammarRules($number_to_convert, 'en');
    $trivia = \ChiffreEnLettre\ContentGenerator::getContextualTrivia($number_to_convert);
    $dynamic_spelling = \ChiffreEnLettre\ContentGenerator::getDynamicSpellingText($number_to_convert, 'en');
}
?>

<div class="content">
    <?php chiffre_breadcrumbs(); ?>

    <div style="margin:0 auto; padding:0 10px 30px;">

        <!-- ═══ HERO: Main Result ═══════════════════════════════════ -->
        <div class="cel-result-hero">
            <div class="cel-result-wrapper"
                style="display:flex; align-items:center; justify-content:center; gap:15px; flex-wrap:wrap;">
                <p class="cel-main-result" id="celMainResultText" style="margin:0;">
                    <?php echo esc_html($result_en_plain); ?>
                </p>
                <button class="cel-copy-btn" id="celCopyBtn"
                    data-clipboard-text="<?php echo esc_attr($result_en_plain); ?>">
                    ⧉ Copier
                </button>
            </div>
        </div>

        <!-- ═══ CURRENCY CARDS ══════════════════════════════════════ -->
        <div class="cel-cards-grid">
            <div class="cel-card">
                <p class="cel-card-label">💵 US Dollars</p>
                <p class="cel-card-value"><?php echo esc_html($result_en_dollars); ?></p>
            </div>
            <div class="cel-card">
                <p class="cel-card-label">💶 Euros</p>
                <p class="cel-card-value"><?php echo esc_html($result_en_euros); ?></p>
            </div>
            <div class="cel-card">
                <p class="cel-card-label">🍁 Canadian Dollar</p>
                <p class="cel-card-value"><?php echo esc_html($result_en_cad); ?></p>
            </div>
            <div class="cel-card">
                <p class="cel-card-label">🌙 Dinar</p>
                <p class="cel-card-value"><?php echo esc_html($result_en_dinar); ?></p>
            </div>
            <div class="cel-card"
                style="grid-column: 1 / -1; display:flex; align-items:center; justify-content:center; background:#e8f8f0; border-color:#b6e8c8;">
                <h4 class="cel-card-label" style="margin: 0 10px 0 0;">🇫🇷 En français</h4>
                <p class="cel-card-value" style="margin:0;">
                    <a href="<?php echo $url_fr; ?>" style="color:#1a7a40; font-weight:700;">
                        Voir la traduction française du nombre <?php echo esc_html($number_to_convert); ?> →
                    </a>
                </p>
            </div>
        </div>

        <!-- ═══ CHEQUE VISUAL (English Layout) ═══════════════════════ -->
        <div class="cel-cheque">
            <div class="cel-cheque-top">
                <div class="cel-cheque-date">
                    <span class="cel-cheque-label">DATE</span>
                    <?php echo esc_html($cheque_data['date']); ?>
                </div>
            </div>

            <div class="cel-cheque-row">
                <span class="cel-cheque-label">AMOUNT</span>
                <div class="cel-cheque-amount-box">
                    $ <?php echo esc_html($cheque_data['amount_num']); ?>
                </div>
            </div>

            <div class="cel-cheque-row">
                <div class="cel-cheque-words">
                    <?php echo esc_html($cheque_data['amount_txt']); ?>
                </div>
            </div>

            <div class="cel-cheque-row">
                <span class="cel-cheque-label">PAY TO THE ORDER OF</span>
                <div class="cel-cheque-payee">
                    <?php echo esc_html($cheque_data['payee']); ?>
                </div>
            </div>

            <div class="cel-cheque-bottom">
                <div class="cel-cheque-signature">
                    Signature
                </div>
            </div>
        </div>

        <?php
        $ad_left = get_option('cel_ad_en_left', '');
        $ad_center = get_option('cel_ad_en_center', '');
        $ad_right = get_option('cel_ad_en_right', '');

        if (!empty(trim($ad_left)) || !empty(trim($ad_center)) || !empty(trim($ad_right))):
            ?>
            <!-- ═══ AD CODE INJECTION (3 COLUMNS) ════════════════════════════════════════ -->
            <div
                style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin: 30px 0; min-height: 90px;">
                <?php if (!empty(trim($ad_left))): ?>
                    <div style="flex: 1; min-width: 250px; text-align: center;">
                        <?php echo $ad_left; // XSS ignored intentionally to allow raw JS/HTML from admin ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty(trim($ad_center))): ?>
                    <div style="flex: 1; min-width: 250px; text-align: center;">
                        <?php echo $ad_center; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty(trim($ad_right))): ?>
                    <div style="flex: 1; min-width: 250px; text-align: center;">
                        <?php echo $ad_right; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($is_vip): // 🛑 SEO CONTENT GATING FOR VIP ONLY ?>

            <!-- ═══ ENGLISH SEO CONTEXT ═══════════════════════════════════════ -->
            <div class="cel-section">
                <h2 class="cel-section-title">Contextes et usages : <?php echo esc_html($number_to_convert); ?> en anglais
                </h2>
                <ul style="margin:0; padding-left:20px; color:#444; line-height: 1.6;">
                    <?php
                    $english_seo_facts = \ChiffreEnLettre\ContentGenerator::getEnglishSeoFacts($number_to_convert, $result_en_plain);
                    foreach ($english_seo_facts as $fact): ?>
                        <li style="margin-bottom:10px;"><?php echo $fact; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- ═══ SPELLING RULES (Dynamic) ═════════════════════════════ -->
            <div class="cel-section">
                <h2 class="cel-section-title">Règles d'écriture en anglais — <?php echo esc_html($number_to_convert); ?>
                </h2>
                <?php foreach ($dynamic_spelling as $text): ?>
                    <p><?php echo esc_html($text); ?></p>
                <?php endforeach; ?>
                <p>
                    En résumé, le nombre <strong><?php echo esc_html($number_to_convert); ?></strong>
                    s'écrit <strong><?php echo esc_html($result_en_plain); ?></strong> en anglais.
                </p>
            </div>

            <!-- ═══ SIMILAR NUMBERS ══════════════════════════════════════ -->
            <div class="cel-section">
                <h2 class="cel-section-title">Nombres similaires à <?php echo esc_html($number_to_convert); ?> en anglais
                </h2>
                <ul class="cel-pills">
                    <?php
                    $smart_similar = \ChiffreEnLettre\NumberVipList::getSmartRelated((int) $number_to_convert_php, 8);
                    $anchor_texts = [
                        '%s en anglais',
                        'Comment on dit %s en anglais',
                        '%s anglais',
                        'Comment dit-on %s en anglais',
                        'Comment dire %s en anglais',
                        '%s en anglais en lettre',
                        'Comment écrire %s en lettre anglais',
                        'Traduire %s en anglais'
                    ];

                    foreach ($smart_similar as $index => $n):
                        $anchor_phrase = sprintf($anchor_texts[$index % count($anchor_texts)], $n);
                        ?>
                        <li>
                            <a href="<?php echo esc_url(site_url('/comment-on-dit/' . $n . '-en-anglais/')); ?>">
                                <?php echo esc_html($anchor_phrase); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; // End VIP Gating ?>

    </div><!-- /max-width wrapper -->
</div><!-- .content -->

<?php get_footer(); ?>