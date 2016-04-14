<?php
/**
 * Plugin Name: WooCommerce TigoMoney Gateway
 * Description: WooCommerce Payment Gateway for TigoMoney
 * Version: 2.6.2
 * Author: Vevende SRL
 * Author URI: https://www.vevende.com/
 *
 * @package WC_Gateway_TigoMoney
 * @version 2.6.2
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('WC_Gateway_TigoMoney_Request')) {
	return;
}

class WC_Gateway_TigoMoney_Request {
	protected $gateway;
	protected $notify_url;

	public function __construct($gateway) {
		$this->gateway = $gateway;
		$this->notify_url = WC()->api_request_url('WC_Gateway_TigoMoney');
	}

	public function get_response_arguments($posted) {
		$posted = $this->Decrypt(str_replace(' ', '+', $posted));
		$posted = stripslashes_deep($posted);
		parse_str($posted, $data);
		stripslashes_deep($data);
		return $data;
	}

	public function get_request_url($order, $phonenumber) {
		$tigomoney_args = $this->generate_arguments($order, $phonenumber);
		$encrypted_params = $this->Encrypt($tigomoney_args);

		if ($this->gateway->sandbox) {
			$host = 'https://190.129.208.178:8181/vipagos/faces/payment.xhtml';
		} else {
			$host = 'https://vipagos.com.bo/vipagos/faces/payment.xhtml';
		}
		return sprintf('%s?key=%s&parametros=%s', $host, $this->gateway->identity_token, $encrypted_params);
	}

	protected function generate_arguments($order, $phonenumber) {
		$postmeta = get_post_meta($order->id);
		$params = array(
			'pv_orderId' => $order->id,
			'pv_monto' => $order->get_total(),
			'pv_linea' => $phonenumber,
			'pv_nombre' => $order->billing_first_name . ' ' . $order->billing_last_name,
			'pv_urlCorrecto' => esc_url($this->notify_url),
			'pv_urlError' => esc_url($this->notify_url),
			'pv_razonSocial' => $order->billing_recipient,
			'pv_nit' => $order->billing_nit,
			'pv_items' => '',
		);

        if ($this->gateway->settings['confirmation_message'] !== "")
        {
            $params['pv_confirmacion'] = $this->gateway->settings['confirmation_message'];
        }

        if ($this->gateway->settings['notify_message'] !== "")   
        {
            $params['pv_notificacion'] = $this->gateway->settings['notify_message'];
        }

		$item_number = 0;

		if (sizeof($order->get_items()) > 0) {
			foreach ($order->get_items() as $item) {

				$product = $order->get_product_from_item($item);
				$item_quantity = $item['qty'];
				$item_name = $this->item_name($item['name']);
				$item_price = $this->format_price($product->get_price());
				$item_total = $this->format_price($order->get_item_subtotal($item, false));
				$item_number++;

				$params['pv_items'] .= "*i$item_number|$item_quantity|$item_name|$item_price|$item_total";
			}
		}

		return implode(';', array_map(function ($key, $value) {
			return sprintf("%s=%s", $key, $value);
		}, array_keys($params), $params));
	}

	protected function format_price($value) {
		return number_format($value, 2, '.', '');
	}

	protected function item_name($item_name) {
		$item_name = sanitize_text_field($item_name);

		if (strlen($item_name) > 127) {
			$item_name = substr($item_name, 0, 124) . '...';
		}

		return html_entity_decode($item_name, ENT_NOQUOTES, 'UTF-8');
	}

	protected function Encrypt($data, $blocksize = 8) {
		$len = strlen($data);
		$extra = ($len % $blocksize);

		if ($extra > 0) {
			$padding = $blocksize - $extra;
			$data = $data . str_repeat("\0", $padding);
		}

		$encrypted = mcrypt_encrypt("tripledes", $this->gateway->encrypt_key, $data, "ecb");
		return base64_encode($encrypted);
	}

	protected function Decrypt($data) {
		$cipher = base64_decode($data);
		$decrypted = mcrypt_decrypt("tripledes", $this->gateway->encrypt_key, $cipher, "ecb");
		return trim(trim($decrypted), "\0");
	}
}
