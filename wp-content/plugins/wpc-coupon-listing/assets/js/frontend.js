'use strict';

(function($) {
  $(document).on('click touch', '.wpccl-coupon', function() {
    let $link = $(this), coupon = $link.data('coupon');

    if ($link.hasClass('wpccl-coupon-plain')) {
      return;
    }

    if ($link.hasClass('wpccl-coupon-applied')) {
      return;
    }

    if ($link.hasClass('wpccl-coupon-disabled')) {
      return;
    }

    $link.addClass('wpccl-coupon-loading');

    $.ajax({
      type: 'POST', url: wpccl_vars.wc_ajax_url.toString().
          replace('%%endpoint%%', 'wpccl_apply_coupon'), data: {
        action: 'wpccl_apply_coupon',
        coupon_code: coupon,
        nonce: wpccl_vars.nonce,
      }, dataType: 'json', success: function(response) {
        if (response) {
          $link.removeClass('wpccl-coupon-loading');
          $('#wpccl-popup [data-coupon="' + coupon + '"]').
              addClass('wpccl-coupon-applied');
        }
      }, complete: function() {
        if ($link.closest('.wpccl-popup').length) {
          // popup
          var current = $.featherlight.current();
          current.close();

          $(document.body).
              trigger('wc_update_cart').
              trigger('wc_fragment_refresh');

          if (wpccl_vars.is_checkout) {
            $(document.body).trigger('update_checkout');
          }
        } else {
          // shortcode
          location.reload();
        }
      },
    });
  });
})(jQuery);

function wpccl_load_coupons() {
  jQuery('#wpccl-popup .wpccl-coupons').addClass('wpccl-coupons-loading');

  jQuery.ajax({
    type: 'POST', url: wpccl_vars.wc_ajax_url.toString().
        replace('%%endpoint%%', 'wpccl_load_coupons'), data: {
      action: 'wpccl_load_coupons', nonce: wpccl_vars.nonce,
    }, success: function(response) {
      jQuery('#wpccl-popup .wpccl-coupons').
          html(response).
          removeClass('wpccl-coupons-loading');
      wpccl_countdown();
    },
  });
}

function wpccl_countdown() {
  if (wpccl_vars.countdown !== 'yes') {
    return;
  }

  jQuery('.wpccl-coupon-expiry').each(function() {
    let $this = jQuery(this), date = $this.attr('data-date'),
        date_tz = moment.tz(date, 'YYYY-MM-DD', wpccl_vars.timezone),
        date_format = '<span>%D %!D:' + wpccl_vars.day + ',' + wpccl_vars.days +
            ';</span> <span>%H</span>:<span>%M</span>:<span>%S</span>';

    if (date !== '') {
      $this.countdown(date_tz.toDate(), function(event) {
        $this.html(
            wpccl_vars.active_in.replace('%s', event.strftime(date_format)));
      }).on('finish.countdown', function() {
        $this.remove();
      });
    }
  });
}