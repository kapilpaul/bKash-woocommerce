<?php
/**
 * Bkash-woocommerce
 * Kapil Paul
 */

namespace Inc;

use Inc\Base\BkashQuery;

class Bkash
{
    private $table = 'bkash_transactions';

    /**
     * Bkash constructor.
     */
    public function __construct()
    {
        add_action('wp_ajax_wc-bkash-process', array($this, 'paymentStore'));
    }

    /**
     * Store the payment and insert
     * validation on bKash end by payment id
     */
    public function paymentStore()
    {
        try {
            if (!wp_verify_nonce($_POST['_ajax_nonce'], 'wc-bkash-process')) {
                wp_send_json_error(__('Something went wrong!', 'bKash-wc'));
            }

            $postParams = ['order_number', 'payment_id', 'trx_id', 'transaction_status', 'invoice_number'];
            $containsAllValues = !array_diff_key(array_flip($postParams), $_POST);

            if (!$containsAllValues) {
                wp_send_json_error(__('Params are missing.', 'bKash-wc'));
            }

            if (!$this->validateFields($_POST)) {
                wp_send_json_error(__('Empty value is not allowed', 'bKash-wc'));
            }

            $order_number = sanitize_key($_POST['order_number']);
            $order_number = isset($order_number) ? $order_number : 0;
            $payment_id = sanitize_key($_POST['payment_id']);
            $trx_id = sanitize_key($_POST['trx_id']);
            $transaction_status = sanitize_key($_POST['transaction_status']);
            $invoice_number = sanitize_key($_POST['invoice_number']);
            $order = wc_get_order($order_number);
            $orderGrandTotal = (float)$order->get_total();

            $paymentInfo = BkashQuery::verifyPayment($payment_id, $orderGrandTotal);

            if ($paymentInfo) {
                if ($paymentInfo['amount'] == $orderGrandTotal) {
                    $insertData = [
                        "order_number" => $order_number,
                        "payment_id" => $payment_id,
                        "trx_id" => $trx_id,
                        "transaction_status" => $transaction_status,
                        "invoice_number" => $invoice_number,
                        "amount" => $paymentInfo['amount'],
                    ];

                    if ($insert = $this->insertBkashPayment($insertData)) {
                        $order->add_order_note(sprintf(__('bKash payment completed.Transaction ID #%s! Amount: %s', 'bKash-wc'), $trx_id, $orderGrandTotal));
                        $order->payment_complete();
                        wp_send_json_success(__($order->get_view_order_url()));
                    }
                } else {
                    $order->update_status('on-hold', __('Partial payment.Transaction ID #%s! Amount: %s', 'bKash-wc'), $trx_id, $paymentInfo['amount']);
                }
            }
            wp_send_json_error(__("Failed", 'bKash-wc'));
        } catch (\Exception $e) {
            wp_send_json_error(__($e->getMessage(), 'bKash-wc'));
        }
    }

    /**
     * insert Payment info
     * to bKash transactions table
     * @param $paymentInfo
     * @return false|int
     */
    public function insertBkashPayment($paymentInfo)
    {
        global $wpdb;

        $insert = $wpdb->insert($wpdb->prefix . $this->table, array(
            "order_number" => $paymentInfo['order_number'],
            "payment_id" => $paymentInfo['payment_id'],
            "trx_id" => $paymentInfo['trx_id'],
            "transaction_status" => $paymentInfo['transaction_status'],
            "invoice_number" => $paymentInfo['invoice_number'],
            "amount" => $paymentInfo['amount'],
        ));

        return $insert;
    }

    public function validateFields($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return false;
            }
        }
        return true;
    }
}
