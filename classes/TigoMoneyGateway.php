<?php

class TigoMoneyGateway extends WC_Payment_Gateway {

	/** @var bool Whether or not logging is enabled */
	public static $log_enabled = false;

	/** @var WC_Logger Logger instance */
	public static $log = false;

	private $debug = false;
	private $access_key = null;
	private $secret_key = null;

	public function __construct() {
		$this->id                 = 'tigomoney';
		$this->has_fields         = false;
		$this->icon               = plugins_url( 'images/woocommerce-tigomoney.png', plugin_dir_path( __FILE__ ) );
		$this->method_title       = 'Tigo Money';
		$this->method_description = 'Pago en linea via Tigo Money Bolivia';
		$this->order_button_text  = 'Pagar con Tigo Money';

		$this->supports = array(
			'products',
		);

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->testmode    = 'yes' === $this->get_option( 'testmode', 'no' );
		$this->debug       = 'yes' === $this->get_option( 'debug', 'no' );

		// Tigomoney credentials
		$this->access_key = $this->get_option( 'access_key' );
		$this->secret_key = $this->get_option( 'secret_key' );

		$this->message_confirmation = $this->get_option( 'message_confirmation' );
		$this->message_notify       = $this->get_option( 'message_notify' );

		self::$log_enabled = $this->debug;

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = 'no';
		} else {
			add_action( 'woocommerce_update_options_payment_gateways_tigomoney', array(
				$this,
				'process_admin_options'
			) );
			add_action( 'woocommerce_api_wc_gateway_tigomoney', array( $this, 'return_handler' ) );
			add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		}
	}


	public function is_valid_for_use() {
		if ( $this->access_key != '' && $this->secret_key != '' ) {
			// Check if the gateway is enabled in the user's country
			return in_array( get_woocommerce_currency(),
				apply_filters( 'woocommerce_paypal_supported_currencies', array( 'BOB', 'PYG', 'ZAR' ) ) );
		}

		return false;
	}

	public function admin_options() {
		if ( $this->is_valid_for_use() ) {
			parent::admin_options();
		} else {
			?>
            <div class="inline error"><p>
                    <strong><?php _e( 'Gateway disabled', 'woocommerce' ); ?></strong>: <?php _e( 'TigoMoney does not support your store currency.', 'woocommerce' ); ?>
                </p></div>
			<?php
		}
	}

	// Process the payment and return the result
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		return array(
			'result'   => 'success',
			'redirect' => add_query_arg( array(
				'order' => $order->get_id(),
				'key'   => $order->order_key,
			), get_permalink( wc_get_page_id( 'pay' ) ) ),
		);
	}

	public function receipt_page( $order_id ) {
		echo '<p>Muchas Gracias, por favor paga con tu Billetera de Tigo Money</p>';
		$order  = wc_get_order( $order_id );
		$posted = wp_unslash( $_POST );

		if ( isset( $posted['tigomoney-phonenumber'] ) ) {
			$phone = trim( $posted['tigomoney-phonenumber'] );

			if ( preg_match( "/^[6-7][0-9]{7}$/", $phone ) ) {
				$tigomoney = new WC_Gateway_TigoMoney_Request( $this );
				$pay_url   = $tigomoney->get_request_url( $order, $posted['tigomoney-phonenumber'] );
				$mensaje   = $tigomoney->generate_arguments( $order, $posted['tigomoney-phonenumber'] );
				$tigomoney->pagoTigo( $mensaje, $order );
				exit();
			} else {
				echo '<p class="woocommerce-error">Por favor ingresa un número de movil válido.</p>';
			}

		}

		echo $this->generate_form( $order );
	}

	public function init_form_fields() {
		$this->form_fields = include( 'includes/settings.php' );
	}

	public function generate_form( $order ) {
		$form = '<form action="" method="post" id="payment-form" target="_top">';
		$form .= '<input type="text" id="id_tigomoney-phonenumber" name="tigomoney-phonenumber" required= value="" /> ';
		$form .= '<br><br>';
		$form .= '<input type="submit" class="button alt" id="submit-payment-form" value="Pagar con TigoMoney" /> ';
		$form .= '<a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">Cancelar</a>';
		$form .= '</form>';

		return $form;
	}

	// Output for the order received page.
	public function thankyou_page() {
		if ( $this->instructions ) {
			echo wpautop( wptexturize( $this->instructions ) );
		}
	}

	function return_handler() {
		@ob_clean();
		header( 'HTTP/1.1 200 OK' );

		if ( ! empty( $_GET ) ) {
			if ( array_key_exists( 'r', $_GET ) ) {
				$tigomoney = new WC_Gateway_TigoMoney_Request( $this );
				$posted    = $tigomoney->get_response_arguments( str_replace( ' ', '+', $_GET['r'] ) );
				$order     = new WC_Order( $posted['orderId'] );

				if ( $posted['codRes'] == '0' ) {
					$this->payment_status_completed( $order, $posted );
				} else {
					$this->payment_status_failed( $order, $posted );
				}

			} else {
				wp_die( 'Request Failure', 'TigoMoney Request', array( 'response' => 200 ) );
				exit();
			}
		}

		wp_redirect( wc_get_page_permalink( 'cart' ) );
		exit();
	}

	protected function payment_status_failed( $order, $posted ) {
		$errormessages = array(
			'4'    => 'Comercio no Registrado',
			'7'    => 'Acceso Denegado por favor intente nuevamente y verifique los datos incorporados',
			'8'    => 'PIN no valido, vuelva a intentar',
			'11'   => 'Tiempo de respuesta excedido, por favor inicie la transaccion nuevamente',
			'14'   => 'Billetera Movil destino no registrada, favor verifique sus datos',
			'17'   => 'Monto no valido, verifique los datos proporcionados',
			'19'   => 'Comercio no habilitado para el pago, favor comunicarse con el comercio',
			'23'   => 'El monto introducido es menor al requerido, favor verifique los datos',
			'24'   => 'El monto introducido es mayor al requerido, favor verifique los datos',
			'1001' => 'Los fondos en su Billetera movil son insuficientes, para cargar su billetera vaya al Punto Tigo Money mas cercano, marque *555#',
			'1002' => 'No ingresaste tu PIN o ingresaste un PIN incorrecto, tu transaccion no pudo ser completada, inicia la transaccion nuevamente y verifica en transacciones por completar',
			'1003' => 'Estimado Cliente llego a su limite de monto transaccionado, si tiene alguna consulta comuniquese con el *555',
			'1004' => 'Estimado Cliente excedio su limite de intentos de introducir su PIN, por favor comuniquese con el *555 para solicitar su nuevo PIN',
			'56'   => 'Mismo Monto, Origen y Destino dentro de 1 min  Señor Cliente su transaccion no fue completada favor intente nuevamente en 1 minuto',
		);


		if ( array_key_exists( 'codRes', $posted ) ) {
			wc_add_notice( 'Tigo Money > ' . $errormessages[ $posted['codRes'] ], 'error' );
		} else {
			wc_add_notice( 'Tigo Money > Lo sentimos, hubo un error desconocido', 'error' );
		}

		$order->update_status( 'failed', 'Pago rechazado: ' . $posted['codRes'] . ' ' . $posted['mensaje'] );

		wp_redirect( wc_get_page_permalink( 'cart' ) );
	}


	protected function payment_status_completed( $order, $posted ) {
		if ( $order->has_status( 'completed' ) ) {
			// Aborting, Order is already complete.
			exit;
		}

		// Good
		$order->add_order_note( $posted['mensaje'] );
		$order->reduce_order_stock();
		$order->payment_complete();

		WC()->cart->empty_cart();

		wc_add_notice( 'Tigo Money > ' . $posted['mensaje'], 'success' );

		wp_redirect( $this->get_return_url( $order ) );
	}
}