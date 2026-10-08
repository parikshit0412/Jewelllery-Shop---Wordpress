/* global jQuery */
(function ($) {
    'use strict';

    function copyText(text, done) {
        function fallbackCopy() {
            var $temp = $('<textarea readonly></textarea>')
                .val(text)
                .css({
                    position: 'fixed',
                    top: '-1000px',
                    left: '-1000px'
                })
                .appendTo('body');

            $temp[0].focus();
            $temp[0].select();
            document.execCommand('copy');
            $temp.remove();
            done();
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(fallbackCopy);
            return;
        }

        fallbackCopy();
    }

    function flashCopied($el, copiedText) {
        var originalText = $el.text();
        $el.text(copiedText);
        setTimeout(function () {
            $el.text(originalText);
        }, 1200);
    }

    $(document).on('click', 'code', function () {
        var $code = $(this);
        copyText($code.text(), function () {
            $code.css('background', '#d1fae5');
            setTimeout(function () {
                $code.css('background', '');
            }, 800);
        });
    });

    $(document).on('click', '.st-copy-shortcode', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var $button = $(this);
        var text = $button.closest('td').find('code').first().text();

        copyText(text, function () {
            flashCopied($button, 'Copied');
        });
    });

    function toggleCustomCronFields() {
        var isCustom = $('#st-cron-interval').val() === 'bksignals_custom';
        $('#st-custom-cron-row').toggleClass('st-hidden', !isCustom);
    }

    $(document).on('change', '#st-cron-interval', toggleCustomCronFields);
    $(toggleCustomCronFields);

})(jQuery);
