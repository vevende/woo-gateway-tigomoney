<?php
/**
 * Plugin Name: Woo TigoMoney Gateway
 * Description: Payment Gateway for TigoMoney in Woocommerce
 * Version: 2.8.0
 * Author: Vevende SRL
 * Author URI: https://www.vevende.com/
 *
 * @package WC_Gateway_TigoMoney
 * @version 2.8.0
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_TigoMoney')):

    class WC_TigoMoney {
        const VERSION = '2.8.0';

        /**
         * @var mixed
         */
        protected static $instance = null;

        public function __construct() {
            if (class_exists('WC_Payment_Gateway')) {
                include_once 'includes/class-wc-gateway-request.php';
                include_once 'includes/class-wc-gateway.php';
                add_filter('woocommerce_payment_gateways', array($this, 'add_Gateway'));
            }
        }

        /**
         * @param $methods
         * @return mixed
         */
        public function add_Gateway($methods) {
            $methods[] = 'WC_Gateway_TigoMoney';
            return $methods;
        }

        /**
         * @return WcTigomoney - Main instance
         */
        public static function get_Instance() {
            if (null == self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

    }
    add_action('plugins_loaded', array('WC_TigoMoney', 'get_Instance'), 0);
endif;
