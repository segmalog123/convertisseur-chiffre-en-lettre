/* 
 * Convertisseur Chiffre en Lettre - Frontend Script
 * Handles number input, validation, and redirection to conversion URLs.
 * On landing pages (homepage / convertisseur-anglais): shows result inline via AJAX.
 */

jQuery(document).ready(function ($) {
    'use strict';

    $('.wordpress-gdpr-popup-close').on('click', function () {
        $('.wordpress-gdpr-popup-agree').trigger('click');
    });

    $('.convert-button').on('click', function () {
        process_number();
    });

    $('.convert-input').keydown(function (e) {
        if (e.keyCode == 13) {
            process_number();
        }
    });

    // Close inline result box
    $(document).on('click', '.cel-result-close', function () {
        $('#cel-inline-result').slideUp(200);
    });

    function process_number() {
        var convert_input = $.trim($('.convert-input').val());

        var lang = jsdata.default_lang || 'fr';
        var slug1 = lang === 'en' ? 'comment-on-dit' : 'ecrire';
        var slug2 = lang === 'en' ? '-en-anglais' : '-en-lettre';

        // Override if button has explicit data-convert attribute
        if ($('.convert-button').attr('data-convert') == 'en') {
            slug1 = 'comment-on-dit';
            slug2 = '-en-anglais';
            lang = 'en';
        }

        // Strip leading zeros (only for integers)
        if (convert_input.indexOf(".") <= 0 && convert_input.indexOf(",") <= 0) {
            convert_input = convert_input.replace(convert_input.match("^0+(?!$)"), "");
        }

        if (lang === 'fr') {
            convert_input = convert_input.replace(/\./g, ',');
        }
        if (lang === 'en') {
            convert_input = convert_input.replace(/\,/g, '.');
        }

        if (lang === 'fr') {
            convert_input = convert_input.replace(/[^\d,-]/g, '');
        }

        convert_input = convert_input.replace(/\-/g, '');

        if (convert_input == '') {
            $('.error-input').empty().append('Ecrire un nombre à convertir svp!');
            return;
        }

        if (convert_input.startsWith(",")) {
            convert_input = '0' + convert_input;
        }
        if (convert_input.startsWith(".")) {
            convert_input = '0' + convert_input;
        }

        var occ_comma = occurrences(convert_input, ',');
        var occ_point = occurrences(convert_input, '.');
        if (occ_point > 1) {
            convert_input = convert_input.replace(/\./g, '');
        }
        if (occ_comma > 1) {
            convert_input = convert_input.replace(/\,/g, '');
        }

        // ── NORMAL MODE: navigate to the conversion URL ──────────────────
        window.location.href = jsdata.site_url + '/' + slug1 + '/' + convert_input + slug2 + '/';
    }

    // ── Download delay countdown ──────────────────────────────────────────
    $('.freeDownloadButton').prop('disabled', true);

    var fiveSeconds = new Date().getTime() + 30000;

    $('#downloadDelayTimeSec').countdown(fiveSeconds)
        .on('update.countdown', function (event) {
            var $this = $(this);
            if (!event.elapsed) {
                $this.html(event.strftime('%S'));
            }
        })
        .on('finish.countdown', function () {
            $('.freeDownloadButton').prop('disabled', false);
            $('#downloadDelayTimeSec').text("00");
            $('.freeDownloadButton').addClass("enbleddonlowd");
        });

    function occurrences(string, substring) {
        var n = 0, pos = 0;
        while (true) {
            pos = string.indexOf(substring, pos);
            if (pos != -1) { n++; pos += substring.length; }
            else { break; }
        }
        return n;
    }

    // ── COPY TO CLIPBOARD ───────────────────────────────────────────────
    $('.cel-copy-btn, .cel-currency-copy-btn').on('click', function () {
        var $btn = $(this);
        var textToCopy = $btn.attr('data-clipboard-text');
        var originalText = $btn.html();

        if (navigator.clipboard) {
            // Modern Clipboard API
            navigator.clipboard.writeText(textToCopy).then(function () {
                $btn.html('✓ Copié !').addClass('cel-copied');
                setTimeout(function () {
                    $btn.html(originalText).removeClass('cel-copied');
                }, 2000);
            });
        } else {
            // Fallback for older browsers
            var textArea = document.createElement("textarea");
            textArea.value = textToCopy;
            textArea.style.position = "absolute";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                $btn.html('✓ Copié !').addClass('cel-copied');
                setTimeout(function () {
                    $btn.html(originalText).removeClass('cel-copied');
                }, 2000);
            } catch (err) {
                console.error('Copy failed', err);
            }
            document.body.removeChild(textArea);
        }
    });

});
