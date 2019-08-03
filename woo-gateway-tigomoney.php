<?php

/**
 * @package WC_Gateway_TigoMoney
 * @version 3.2.0
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */

/**
 * Plugin Name: Woo TigoMoney Gateway
 * Plugin URI: https://github.com/vevende/woo-gateway-tigomoney
 * Description: Payment Gateway for TigoMoney in Woocommerce
 * Version: 3.2.0
 * Author: Humanzilla SRL
 * Author URI: https://www.vevende.com/
 * License: GPLv2 or later
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2019 Humanzilla.
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WC_TigoMoney')) :

    class WC_TigoMoney
    {
        const VERSION = '3.3.0';

        /**
         * @var mixed
         */
        protected static $instance = null;

        public function __construct()
        {
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
        public function add_Gateway($methods)
        {
            $methods[] = 'WC_Gateway_TigoMoney';
            return $methods;
        }

        /**
         * @return WcTigomoney - Main instance
         */
        public static function get_Instance()
        {
            if (null == self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }
    }
    add_filter('woocommerce_currencies', 'add_bob_currency');
    add_filter('woocommerce_currency_symbol', 'add_bob_currency_symbol', 10, 2);
    add_action('plugins_loaded', array('WC_TigoMoney', 'get_Instance'), 0);
endif;

function add_bob_currency($currencies)
{
    $currencies['BOB'] = __('Bolivian Boliviano (Bs.)', 'woocommerce');
    return $currencies;
}
function add_bob_currency_symbol($currency_symbol, $currency)
{
    switch ($currency) {
        case 'BOB':
            $currency_symbol = 'Bs.';
            break;
    }
    return $currency_symbol;
}
