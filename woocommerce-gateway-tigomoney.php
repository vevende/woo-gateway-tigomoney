<?php
/**
 * Plugin Name: TigoMoney Gateway
 * Description: TigoMoney Gateway
 * Version: 2.0
 * Author: Vevende SRL
 * Author URI: https://www.vevende.com/

 *
 * @package WC_Gateway_TigoMoney
 * @version 1.2
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WC_TigoMoney')):

	class WC_TigoMoney {
		const VERSION = '1.1';
		protected static $instance = null;

		private function __construct() {
			if (class_exists('WC_Payment_Gateway')) {
				include_once 'includes/class-wc-gateway-request.php';
				include_once 'includes/class-wc-gateway.php';
				add_filter('woocommerce_payment_gateways', array($this, 'add_gateway'));
			}
		}

		public static function get_instance() {
			if (null == self::$instance) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function add_gateway($methods) {
			$methods[] = 'WC_Gateway_TigoMoney';
			return $methods;
		}

	}

	add_action('plugins_loaded', array('WC_TigoMoney', 'get_instance'), 0);
endif;
