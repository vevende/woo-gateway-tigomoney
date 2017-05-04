<?php

class TigoMoneyClient extends Singleton {
	public function __construct( $gateway ) {
		$this->gateway = $gateway;

		if ( $this->gateway->sandbox ) {
			$server_url = 'http://190.129.208.178:96/PasarelaServices/CustomerServices?wsdl';
		} else {
			$server_url = 'https://pasarela.tigomoney.com.bo/PasarelaServices/CustomerServices?wsdl';
		}

		$this->soapClient = new SoapClient( $server_url, array( "trace" => 1, "exception" => 0 ) );
	}

	public function requestPayment( $data ) {
		$response = $this->soapClient->__soapCall( $funcion_name = "solicitarPago", array(
			'key'        => $this->gateway->access_key,
			'parametros' => $data
		), $options = null );

	}
}
