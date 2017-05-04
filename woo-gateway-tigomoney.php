<?php
/**
 * Plugin Name: Woo TigoMoney Gateway
 * Description: Payment Gateway for TigoMoney in Woocommerce
 * Version: 4.0.0
 * Author: Vevende SRL
 * Author URI: https://www.vevende.com/
 *
 * @package WC_Gateway_TigoMoney
 * @version 4.0.0
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/includes/autoload.php';

add_action( 'plugins_loaded', 'woo_gateway_tigomoney' );

function woo_gateway_tigomoney() {
	TigoMoneyPlugin::initialize();
}
