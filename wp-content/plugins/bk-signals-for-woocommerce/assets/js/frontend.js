/* global BKSignals, jQuery */
(function ($) {
    'use strict';

    var cfg = window.BKSignals || {};

    function formatNumber(n) {
        n = parseInt(n, 10) || 0;
        if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
        if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
        return String(n);
    }

    function updateStatsOnPage(data) {
        if (typeof data.count !== 'undefined') {
            var pid = cfg.product_id;
            $('.st-live-visitors[data-product-id="' + pid + '"] .st-live-count,' +
              '.st-live-count[data-product-id="' + pid + '"],' +
              '[data-product-id="' + pid + '"] .st-live-count').text(data.count);
        }

        $('[data-product-id="' + cfg.product_id + '"] .st-stat-count').each(function () {
            var $el = $(this);
            var stat = $el.data('stat');

            if (stat === 'active_cart') {
                if (typeof data.active_cart !== 'undefined') {
                    $el.text(formatNumber(data.active_cart));
                }
                return;
            }

            var $widget = $el.closest('[data-window]');
            if (!$widget.length) {
                return;
            }

            var window_ = parseInt($widget.data('window'), 10) || 7;
            var suffix;
            if (window_ <= 1) suffix = '24h';
            else if (window_ <= 7) suffix = '7d';
            else suffix = '30d';

            var key = stat + '_' + suffix;
            if (typeof data[key] !== 'undefined') {
                $el.text(formatNumber(data[key]));
            }
        });
    }

    function heartbeat() {
        if (!cfg.product_id) return;
        $.post(cfg.ajax_url, {
            action: 'bksignals_live_heartbeat',
            nonce: cfg.nonce,
            product_id: cfg.product_id
        }, function (res) {
            if (res.success && res.data) {
                updateStatsOnPage(res.data);
            }
        });
    }

    function leaveLivePage() {
        if (!cfg.product_id) return;

        var data = new FormData();
        data.append('action', 'bksignals_live_leave');
        data.append('nonce', cfg.nonce);
        data.append('product_id', cfg.product_id);

        if (navigator.sendBeacon) {
            navigator.sendBeacon(cfg.ajax_url, data);
            return;
        }

        if (window.fetch) {
            fetch(cfg.ajax_url, {
                method: 'POST',
                body: data,
                keepalive: true
            });
        }
    }

    if (cfg.product_id && $('.st-live-count, .st-live-visitors, .st-stat-count, .st-general').length) {
        heartbeat();
        setInterval(heartbeat, cfg.heartbeat_ms || 30000);
        window.addEventListener('pagehide', leaveLivePage);
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                heartbeat();
            }
        });
    }

    function isMobileViewport() {
        return window.matchMedia && window.matchMedia('(max-width: 767px)').matches;
    }

    if (cfg.popup_enabled && (cfg.popup_mobile_enabled || !isMobileViewport())) {
        var popupContainer = null;
        var popupEl = null;
        var popupTimer = null;
        var fetchTimer = null;
        var position = cfg.popup_position || 'bottom-left';
        var design = cfg.popup_design || 'toast';
        var popupInterval = cfg.popup_interval || 8000;
        var salePool = [];
        var saleIndex = 0;

        function buildContainer() {
            var mobilePosition = cfg.popup_mobile_position || 'bottom-right';
            popupContainer = $('<div id="st-popup-container" class="st-pos--' + position + ' st-mobile-pos--' + mobilePosition + '"></div>').appendTo('body');
            popupContainer.css('--st-mobile-bottom-offset', (parseInt(cfg.popup_mobile_offset, 10) || 72) + 'px');
        }

        function fetchAndShow() {
            if (salePool.length && saleIndex < salePool.length) {
                showPopup(salePool[saleIndex]);
                saleIndex += 1;
                return;
            }

            salePool = [];
            saleIndex = 0;

            $.post(cfg.ajax_url, {
                action: 'bksignals_get_recent_sale',
                nonce: cfg.nonce,
                product_id: cfg.popup_product_scope ? (cfg.product_id || 0) : 0,
                limit: cfg.popup_limit || 10
            }, function (res) {
                if (res.success && res.data && Array.isArray(res.data.sales) && res.data.sales.length) {
                    salePool = res.data.sales;
                    saleIndex = 0;
                    showPopup(salePool[saleIndex]);
                    saleIndex += 1;
                } else {
                    fetchTimer = setTimeout(fetchAndShow, popupInterval);
                }
            });
        }

        function showPopup(data) {
            if (popupEl) {
                popupEl.remove();
                clearTimeout(popupTimer);
            }

            var popupHtml = buildPopupHtml(data, design);
            if (!popupHtml) {
                fetchTimer = setTimeout(fetchAndShow, popupInterval);
                return;
            }

            popupEl = $(popupHtml).appendTo(popupContainer);
            popupEl.css('--st-popup-duration', (cfg.popup_duration || 4000) + 'ms');

            setTimeout(function () {
                popupEl.addClass('st-popup--visible');
            }, 30);

            popupTimer = setTimeout(function () {
                popupEl.removeClass('st-popup--visible');
                setTimeout(function () {
                    if (popupEl) {
                        popupEl.remove();
                        popupEl = null;
                    }
                    fetchTimer = setTimeout(fetchAndShow, popupInterval);
                }, 350);
            }, cfg.popup_duration || 4000);
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function (ch) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                })[ch];
            });
        }

        function truncateProductName(value) {
            var chars = Array.from(String(value || ''));
            var limit = isMobileViewport() ? 24 : 50;
            var suffix = isMobileViewport() ? '***' : '****';

            if (chars.length <= limit) {
                return chars.join('');
            }

            return chars.slice(0, limit).join('').trim() + suffix;
        }

        function buildMeta(timeAgo) {
            var parts = [];

            if (timeAgo) {
                parts.push(timeAgo);
            }
            parts.push('Purchased');

            return parts.join(' - ');
        }

        function buildPopupBody(customer, product, meta) {
            return '<div class="st-popup-body"><strong>' + customer + '</strong>'
                + '<span class="st-popup-product-line">' + product + '</span>'
                + '<em class="st-popup-meta">' + meta + '</em></div>';
        }

        function buildPopupProgress() {
            return '<span class="st-popup-progress" aria-hidden="true">'
                + '<span class="st-popup-progress-line st-popup-progress-top"></span>'
                + '<span class="st-popup-progress-line st-popup-progress-right"></span>'
                + '<span class="st-popup-progress-line st-popup-progress-bottom"></span>'
                + '<span class="st-popup-progress-line st-popup-progress-left"></span>'
                + '</span>';
        }

        function buildPopupShell(className, content) {
            return '<div class="st-popup ' + className + '">' + content + buildPopupProgress() + '</div>';
        }

        function buildPopupHtml(data, selectedDesign) {
            if (!data.customer_name) {
                return '';
            }

            var customer = escapeHtml(data.customer_name);
            var product = escapeHtml(truncateProductName(data.product_name || 'a product'));
            var meta = escapeHtml(buildMeta(data.time_ago || ''));
            var image = data.product_image ? '<img class="st-popup-product-image" src="' + escapeHtml(data.product_image) + '" alt="">' : '';
            var media = image || '<span class="st-popup-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><path d="M3.3 7 12 12l8.7-5"></path><path d="M12 22V12"></path></svg></span>';
            var body = buildPopupBody(customer, product, meta);

            if (selectedDesign === 'card') {
                return buildPopupShell('st-popup--card', media + body);
            }
            if (selectedDesign === 'minimal') {
                return buildPopupShell('st-popup--minimal', body);
            }
            if (selectedDesign === 'compact') {
                return buildPopupShell('st-popup--compact', media + body);
            }
            if (selectedDesign === 'glass') {
                return buildPopupShell('st-popup--glass', media + body);
            }
            if (selectedDesign === 'spotlight') {
                return buildPopupShell('st-popup--spotlight', body);
            }
            if (selectedDesign === 'split') {
                return buildPopupShell('st-popup--split', media + body);
            }
            if (selectedDesign === 'ribbon') {
                return buildPopupShell('st-popup--ribbon', body);
            }
            if (selectedDesign === 'timeline') {
                return buildPopupShell('st-popup--timeline', (image || '<span class="st-popup-dot"></span>') + body);
            }
            if (selectedDesign === 'signature') {
                return buildPopupShell('st-popup--signature', media + body);
            }

            return buildPopupShell('st-popup--toast', media + body);
        }

        $(function () {
            buildContainer();
            fetchTimer = setTimeout(fetchAndShow, cfg.popup_delay || 5000);
        });

        $(window).on('pagehide', function () {
            clearTimeout(fetchTimer);
            clearTimeout(popupTimer);
        });
    }

})(jQuery);
