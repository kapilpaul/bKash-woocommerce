(function ($, window, document) {
  var script_loaded = false;
  var loader;

  const dc_bkash = window.dc_bkash;

  const dc_bkash_payment = {
    checkout_form: $('form.checkout, form#order_review'),
    is_bkash_selected: function () {
      return (
        $('.woocommerce-checkout')
          .find('input[name="payment_method"]:checked')
          .val() === 'bkash'
      );
    },
    set_loading_on: function () {
      dc_bkash_payment.checkout_form.addClass('processing').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6,
        },
      });
    },
    set_loading_done: function () {
      dc_bkash_payment.checkout_form.removeClass('processing').unblock();
    },
    submit_error: function (errorMessage) {
      dc_bkash_payment.set_loading_done();
      loader.style.display = 'none';

      $(
        '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message'
      ).remove();

      dc_bkash_payment.checkout_form.prepend(
        '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' +
          errorMessage +
          '</div>'
      );

      dc_bkash_payment.checkout_form.removeClass('processing');
      dc_bkash_payment.checkout_form
        .find('.input-text, select, input:checkbox')
        .trigger('validate')
        .blur();

      dc_bkash_payment.scroll_to_notice();
      $(document.body).trigger('checkout_error');
    },
    scroll_to_notice: function () {
      $('html, body').animate(
        {
          scrollTop: $('form.checkout, form#order_review').offset().top - 100,
        },
        1000
      );
    },
    set_order: function () {
      $.ajax({
        type: 'POST',
        url: wc_checkout_params.checkout_url,
        data: dc_bkash_payment.checkout_form.serialize(),
        dataType: 'json',
      })
        .done(function (result) {
          try {
            if (result.result === 'success') {
              dc_bkash_payment.init_bkash(
                result.order_number,
                result.amount,
                result.create_payment_data
              );

              return;
            } else if (result.result === 'failure') {
              throw new Error('Result failure');
            } else {
              throw new Error('Invalid response');
            }
          } catch (err) {
            // Reload page
            if (result.reload === true) {
              window.location.reload();
              return;
            }

            // Trigger update in case we need a fresh nonce
            if (result.refresh === true) {
              jQuery(document.body).trigger('update_checkout');
            }

            console.log('errr', result, wc_checkout_params.i18n_checkout_error);

            // Add new errors
            if (result.messages) {
              dc_bkash_payment.submit_error(result.messages);
            } else {
              dc_bkash_payment.submit_error(
                '<div class="woocommerce-error">' +
                  wc_checkout_params.i18n_checkout_error +
                  '</div>'
              );
            }
          }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
          dc_bkash_payment.submit_error(
            '<div class="woocommerce-error">' + errorThrown + '</div>'
          );
        });
    },
    do_submit: function () {
      dc_bkash_payment.set_loading_on();

      let set_order;

      dc_bkash_payment.set_order();
    },
    load_bkash_script: function () {
      if (!script_loaded) {
        //create loader for bKash
        dc_bkash_payment.create_bkash_loader();

        loader.style.display = 'block';

        //fetching script
        $.getScript(dc_bkash.script_url, function () {
          loader.style.display = 'none';
          dc_bkash_payment.create_bkash_button();
          script_loaded = true;
          window.$ = $.noConflict(true);
        });
      }
    },
    create_bkash_loader: function () {
      var elem = document.createElement('div');
      elem.className = 'bkash-loader';
      elem.id = 'bkash-loader';
      document.body.appendChild(elem);
      loader = document.getElementById('bkash-loader');
    },
    create_bkash_button: function () {
      var bkashBtn = document.createElement('button');
      bkashBtn.id = 'bKash_button';
      bkashBtn.className = 'btn btn-danger';
      bkashBtn.setAttribute('disabled', 'disabled');
      bkashBtn.style.display = 'none';
      document.body.appendChild(bkashBtn);
    },
    create_bkash_request: function (order_number) {
      let create_payment_data = {
        order_number: order_number,
        action: 'dc-edd-bkash-create-payment-request',
        _ajax_nonce: edd_bkash.nonce,
      };

      $.ajax({
        url: edd_bkash.ajaxurl,
        method: 'POST',
        data: create_payment_data,
        success: function (data) {
          if (data.success && data.data.paymentID != null) {
            data = data.data;
            payment_id = data.paymentID;
            bKash.create().onSuccess(data);
          } else {
            bKash.create().onError();
          }
        },
        error: function (errorMessage) {
          bKash.create().onError();
        },
      });
    },
    execute_bkash_request: function (order_number, payment_id) {
      let execute_payment_data = {
        payment_id: payment_id,
        order_number: order_number,
        action: 'dc-bkash-execute-payment-request',
        _nonce: dc_bkash.nonce,
      };

      $.ajax({
        url: dc_bkash.ajax_url,
        method: 'POST',
        data: execute_payment_data,
        success: function (response) {
          if (response.success && response.data.paymentID != null) {
            let data = response.data;
            window.location.href = data.order_success_url;
          } else {
            bKash.execute().onError(); //run clean up code
          }
        },
        error: function () {
          bKash.execute().onError(); // Run clean up code
        },
      });
    },
    init_bkash: function (order_number, amount, create_payment = false) {
      loader.style.display = 'block';

      let payment_id;
      let payment_request = {
        amount: amount,
        intent: 'sale',
        currency: 'BDT',
        merchantInvoiceNumber: order_number,
      };

      bKash.init({
        paymentMode: 'checkout',
        paymentRequest: payment_request,
        createRequest: function () {
          if (!create_payment) {
            bKash.create().onError();
          }
          payment_id = create_payment.paymentID;
          bKash.create().onSuccess(create_payment);
        },
        executeRequestOnAuthorization: function () {
          dc_bkash_payment.execute_bkash_request(order_number, payment_id);
        },
        onClose: function () {
          loader.style.display = 'none';
        },
      });

      $('#bKash_button').removeAttr('disabled');
      $('#bKash_button').click();
    },
    init: function () {
      //on change load payment script
      dc_bkash_payment.checkout_form.on(
        'click',
        'input[name="payment_method"]',
        function (e) {
          if (dc_bkash_payment.is_bkash_selected()) {
            dc_bkash_payment.load_bkash_script();
          }
        }
      );

      dc_bkash_payment.checkout_form.on('click', '#place_order', function (e) {
        e.preventDefault();

        if (dc_bkash_payment.is_bkash_selected()) {
          dc_bkash_payment.do_submit();
        }
      });
    },
  };

  dc_bkash_payment.init();
})(jQuery, window, document);
