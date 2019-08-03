<?php

/**
 * @package WC_Gateway_TigoMoney
 * @version 3.2.0
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */

/**
 * Plugin Name:         Woo TigoMoney Gateway
 * Plugin URI:          https://github.com/vevende/woo-gateway-tigomoney
 * Description:         Payment Gateway for TigoMoney in Woocommerce setup for Bolivia 🇧🇴
 * Version:             3.3.0
 * Requires at least:   5.2
 * Requires PHP:        7.2
 * Author:              Vevende
 * Author URI:          https://www.vevende.com/
 * Text Domain:         woo-tigomoney
 * Domain Path:         /languages
 * License:             GPLv2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
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

Copyright 2019 HUMANZILLA SRL <vevende@humanzilla.com>.
*/

defined('ABSPATH') || exit;
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);


class Woo_Gateway_Tigomoney
{
    const VERSION = '3.3.0';

    protected static $instance = null;

    protected function __construct()
    { }

    private function __clone()
    { }

    private function __wakeup()
    { }

    final public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function init()
    {
        $this->define('WOO_TIGOMONEY_FILE', __FILE__);
        $this->define('WOO_TIGOMONEY_ABSPATH', dirname(__FILE__) . DS);
        $this->define('WOO_TIGOMONEY_INCLUDE', dirname(__FILE__) . DS . 'includes' . DS);
        $this->define('WOO_TIGOMONEY_VERSION', $this::VERSION);

        register_activation_hook(WOO_TIGOMONEY_FILE, array($this, 'on_activation'));
        register_deactivation_hook(WOO_TIGOMONEY_FILE, array($this, 'on_deactivation'));

        add_action('plugins_loaded', array($this, 'on_plugins_loaded'));
    }

    function on_activation()
    { }

    function on_deactivation()
    { }

    public function on_plugins_loaded()
    {
        if (!$this->check_dependencies()) {

            add_action('admin_notices', function () {
                $wordpress_version    = get_bloginfo('version');
                $has_valid_wp_version = version_compare($wordpress_version, '5.2.0', '>=');

                if ($has_valid_wp_version) {
                    $message = 'Se requiere una version de WooCommerce 3.6 o superior';
                } else {
                    $message = 'Se requiere una version de Wordpress 5.2 o superior y WooCommerce 3.6 o superior';
                }

                printf('<div class="error"><p>%s</p></div>', $message);
            });

            deactivate_plugins(plugin_basename(WOO_TIGOMONEY_FILE));
            unset($_GET['activate']);

            return;
        }

        $this->includes();
    }

    protected function includes()
    {
        require_once WOO_TIGOMONEY_INCLUDE . 'class-wc-gateway-request.php';
        require_once WOO_TIGOMONEY_INCLUDE . 'class-wc-gateway.php';
        require_once WOO_TIGOMONEY_INCLUDE . 'hooks.php';
    }

    protected function hooks()
    { }

    protected function check_dependencies()
    {
        $woocommerce_minimum_met = class_exists('WooCommerce') && version_compare(WC_VERSION, '3.6', '>=');
        if (!$woocommerce_minimum_met) {
            return false;
        }
        $wordpress_version = get_bloginfo('version');
        return version_compare($wordpress_version, '5.2.0', '>=');
    }

    protected function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

Woo_Gateway_Tigomoney::instance()->init();
