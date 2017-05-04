<?php

class TigoMoneyPlugin extends Singleton {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'check_environment' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
		add_filter( 'woocommerce_currencies', array( $this, 'add_currencies' ) );
		add_filter( 'woocommerce_currency_symbol', array( $this, 'add_currencies_symbols' ) );
		do_action( 'wc_tigo_money_loaded' );
	}

	public function check_environment() {
	}

	public function admin_notices() {
	}

	public function add_currencies( $currencies ) {
		$currencies['BOB'] = __( 'Bolivian Boliviano (Bs.)', 'woocommerce' );

		return $currencies;
	}

	public function add_currencies_symbols( $currency_symbol, $currency ) {
		switch ( $currency ) {
			case 'BOB':
				$currency_symbol = 'Bs.';
				break;
		}

		return $currency_symbol;
	}

	public function register_gateway( $methods ) {
		$methods[] = 'TigoMoneyGateway';

		return $methods;
	}
}
