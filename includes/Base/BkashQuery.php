<?php
/**
 * Bkash-woocommerce
 * Kapil Paul
 */

namespace Inc\Base;


use Inc\bKashWoocommerceGateway;

class BkashQuery extends bKashWoocommerceGateway
{
    private static $selfClassInstance;

    /**
     * @return BkashQuery
     */
    public static function getSelfClass()
    {
        if (!self::$selfClassInstance) {
            return self::$selfClassInstance = (new self);
        }
        return self::$selfClassInstance;
    }

    /**
     * @return string
     */
    public static function grantTokenUrl()
    {
        $selfClass = self::getSelfClass();
        if ($selfClass->get_option('test_mode') == 'on') {
            return 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/grant';
        }
        return 'https://checkout.pay.bka.sh/v1.2.0-beta/checkout/token/grant';
    }

    /**
     * @return string
     */
    public static function paymentQueryUrl()
    {
        $selfClass = self::getSelfClass();
        if ($selfClass->get_option('test_mode') == 'on') {
            return 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/query/';
        }
        return 'https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/query/';
    }

    /**
     * @return bool
     */
    public static function getToken()
    {
        if ($token = get_transient('bkash_token')) {
            return $token;
        }

        $selfClass = self::getSelfClass();
        $userName = $selfClass->get_option('username');
        $password = $selfClass->get_option('password');

        $data = [
            "app_key" => $selfClass->get_option('app_key'),
            "app_secret" => $selfClass->get_option('app_secret'),
        ];

        $headers = [
            "username" => $userName,
            "password" => $password,
            "Content-Type" => "application/json"
        ];

        $result = self::makeRequest(self::grantTokenUrl(), $data, $headers);

        if (isset($result['id_token']) && isset($result['token_type'])) {
            $token = $result['id_token'];
            set_transient('bkash_token', $token, $result['expires_in']);
            return $result['id_token'];
        }

        return false;
    }

    /**
     * @param $url
     * @param $data
     * @param array $headers
     * @return mixed|string
     */
    public static function makeRequest($url, $data, $headers = [])
    {
        $args = array(
            'body' => json_encode($data),
            'timeout' => '15',
            'redirection' => '15',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
            'cookies' => []
        );

        $response = wp_remote_retrieve_body(wp_remote_post($url, $args));
        return json_decode($response, true);
    }

    /**
     * verify payment on bKash end
     * @param $paymentID
     * @return bool|mixed|string
     */
    public static function verifyPayment($paymentID, $orderTotal)
    {
        $selfClass = self::getSelfClass();
        if ($selfClass->get_option('test_mode') == 'on') {
            return ['amount' => $orderTotal];
        }

        if ($token = self::getToken()) {
            $headers = [
                "Authorization" => "Bearer {$token}",
                "X-App-Key" => $selfClass->get_option('app_key')
            ];

            $args = array(
                'headers' => $headers,
            );

            $response = wp_remote_get(self::paymentQueryUrl() . $paymentID, $args);
            $result = json_decode(wp_remote_retrieve_body($response), true);

            if (!isset($result['errorCode']) && !isset($result['errorMessage'])) {
                return $result;
            }
        }
        return false;
    }
}
