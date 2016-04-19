<?php
/**
 * Plugin Name: Woo TigoMoney Gateway
 * Description: Payment Gateway for TigoMoney in Woocommerce
 * Version: 2.7.0
 * Author: Vevende SRL
 * Author URI: https://www.vevende.com/
 *
 * @package WC_Gateway_TigoMoney
 * @version 2.7.0
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_TigoMoney')):

    class WcTigomoney {
        const VERSION = '2.7';

        /**
         * @var mixed
         */
        protected static $instance = null;

        public function __construct() {
            if (class_exists('WC_Payment_Gateway')) {
                include_once 'includes/class-wc-gateway-request.php';
                include_once 'includes/class-wc-gateway.php';
                add_filter('woocommerce_payment_gateways', array($this, 'add_gateway'));
            }
        }

        /**
         * @param $methods
         * @return mixed
         */
        public function addGateway($methods) {
            $methods[] = 'WC_Gateway_TigoMoney';
            return $methods;
        }

        /**
         * @return WcTigomoney - Main instance
         */
        public static function getInstance() {
            if (null == self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public static function pluginUpdater() {
            include_once 'updater.php';

            if (is_admin()) {
                $config = array(
                    'slug' => plugin_basename(__FILE__),
                    'proper_folder_name' => 'woocommerce-gateway-tigomoney',
                    'api_url' => 'https://api.github.com/repos/vevende/woocommerce-gateway-tigomoney',
                    'raw_url' => 'https://raw.github.com/vevende/woocommerce-gateway-tigomoney/master',
                    'github_url' => 'https://github.com/vevende/woocommerce-gateway-tigomoney',
                    'zip_url' => 'https://github.com/vevende/woocommerce-gateway-tigomoney/archive/master.zip',
                    'sslverify' => true,
                    'requires' => '3.0',
                    'tested' => '3.3',
                    'readme' => 'README.md',
                    'access_token' => '',
                );
                new WP_GitHub_Updater($config);
            }
        }


    }

    add_action('init', array('WC_TigoMoney', 'plugin_updater'));
    add_action('plugins_loaded', array('WC_TigoMoney', 'get_instance'), 0);
endif;
