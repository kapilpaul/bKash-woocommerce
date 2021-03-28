import $ from 'jquery';
import { __ } from '@wordpress/i18n';
import { toast } from 'react-toastify';
import Swal from 'sweetalert2'
import '../styles/react-toastify.css';

/**
 * Bkash initialize, create payment and execute payment
 */
const dcBkash = {
  /**
   * Initialize.
   *
   * @return {void}
   */
  init() {
    this.loadScript();
  },

  /**
   * Load bKash script
   *
   * @return {void}
   */
  loadScript() {
    window.$ = $.noConflict(true);

    //fetching script
    $.getScript(dc_bkash_admin.script_url, () => {
      this.create_bkash_button();
    });
  },

  /**
   * Create bKash button
   *
   * @return {void}
   */
  create_bkash_button() {
    var bkashBtn = document.createElement('button');
    bkashBtn.id = 'bKash_button';
    bkashBtn.className = 'btn btn-danger';
    bkashBtn.setAttribute('disabled', 'disabled');
    bkashBtn.style.display = 'none';
    document.body.appendChild(bkashBtn);
  },

  /**
   * Initialize bKash
   *
   * @param {*} order_number
   * @param {*} amount
   * @param {*} create_payment
   */
  initBkash(order_number, amount, create_payment = false, callback = false, onClose = false) {
    let toastID = toast.info(__('bKash Processing...', dc_bkash_admin.text_domain), {
      position: 'bottom-right',
      autoClose: false,
      closeOnClick: false,
      pauseOnHover: false,
      draggable: false,
      closeButton: false,
    });

    let payment_request = {
      amount: amount,
      intent: 'sale',
      currency: 'BDT',
      merchantInvoiceNumber: order_number,
    };

    bKash.init({
      paymentMode: 'checkout',
      paymentRequest: payment_request,
      createRequest: () => {
        if (!create_payment) {
          bKash.create().onError();
          return;
        }

        bKash.create().onSuccess(create_payment);
      },
      executeRequestOnAuthorization: () => {
        if (callback && !onClose) {
          bKash.execute().onError();
          toast.dismiss(toastID); 
          return callback(true);
        }

        toast.dismiss();
        bKash.execute().onError();
      },
      onClose: () => {
        bKash.create().onError();
        toast.dismiss(toastID); 

        this.showAlert( __('Opps...'), __('Payment Cancelled!') );

        if ( onClose && callback ) {
          return callback(true, create_payment);
        }
      },
    });

    bKash.reconfigure({
      paymentRequest: payment_request
    });

    $('#bKash_button').removeAttr('disabled');
    $('#bKash_button').click();
  },

  /**
   * Show alert for payment error
   * 
   * @param {*} title 
   * @param {*} text 
   */
  showAlert(title, text) {
    Swal.fire({
      icon: 'error',
      title: title,
      text: text,
      confirmButtonText: 'OK',
    }).then((result) => {
    });
  }
};

export default dcBkash;
