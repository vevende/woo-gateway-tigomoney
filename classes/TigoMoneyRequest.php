<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TigoMoneyRequest {
	protected $gateway;
	protected $notify_url;

	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}

	protected function format_price( $value ) {
		return number_format( $value, $decimal = 2, $dec_point = '.', $thousands_sep = '' );
	}

	protected function format_name( $item_name ) {
		$item_name = sanitize_text_field( $item_name );

		if ( strlen( $item_name ) > 127 ) {
			$item_name = substr( $item_name, $start = 0, $length = 124 ) . '...';
		}

		return html_entity_decode( $item_name, $quote_style = ENT_NOQUOTES, $charset = 'UTF-8' );
	}

	protected function Encrypt( $data, $blocksize = 8 ) {
		$len   = strlen( $data );
		$extra = ( $len % $blocksize );

		if ( $extra > 0 ) {
			$padding = $blocksize - $extra;
			$data    = $data . str_repeat( $input = "\0", $padding );
		}

		$encrypted = openssl_encrypt( $data, $method = 'des-ede3', $password = $this->gateway->encrypt_key );

		return base64_encode( $encrypted );
	}

	protected function Decrypt( $data ) {
		$data      = base64_decode( $data );
		$decrypted = openssl_decrypt( $data, $method = 'des-ede3', $password = $this->gateway->encrypt_key );

		return trim( trim( $decrypted ), $charlist = "\0" );
	}
}
