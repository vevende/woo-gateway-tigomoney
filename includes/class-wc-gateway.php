<?php
/**
 * Plugin Name: Woo TigoMoney Gateway
 * Description: Payment Gateway for TigoMoney in Woocommerce
 * Version: 2.9.0
 * Author: Vevende SRL
 * Author URI: https://www.vevende.com/
 *
 * @package WC_Gateway_TigoMoney
 * @version 3.0.0
 * @category Gateway
 * @author Mario César Señoranis Ayala
 */


class WC_Gateway_TigoMoney extends WC_Payment_Gateway {

    public $encrypt_key;
    public $identity_token;
    public $confirmation_message;
    public $notify_message;
    public $debug;
    public $sandbox;

    public static $log_enabled = false;

    /**
     * Constructor for the gateway.
     */

    public function __construct() {

        $this->id = 'tigomoney';
        $this->icon = plugins_url('images/woocommerce-tigomoney.png', plugin_dir_path(__FILE__));
        $this->has_fields = false;
        $this->supports = array('products');
        $this->method_title = 'Tigo Money';
        $this->method_description = 'Pago en linea via Tigo Money Bolivia';
        $this->order_button_text = 'Pagar con Tigo Money';

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title = 'TigoMoney';
        $this->description = $this->get_option('description');

        // Tigomoney variables
        $this->identity_token = $this->get_option('identity_token');
        $this->encrypt_key = $this->get_option('encrypt_key');
        $this->confirmation_message = $this->get_option('confirmation_message');
        $this->notify_message = $this->get_option('notify_message');
        $this->debug = 'yes' === $this->get_option('debug', 'no');
        $this->sandbox = 'yes' === $this->get_option('sandbox', 'yes');

        if (!$this->is_available()) {
            $this->enabled = 'no';
        }

        self::$log_enabled = $this->debug;

        add_action('woocommerce_update_options_payment_gateways_tigomoney', array($this, 'process_admin_options'));
        add_action('woocommerce_api_wc_gateway_tigomoney', array($this, 'return_handler'));
        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
    }


    public function admin_options() {
        if (is_admin()) {
            parent::admin_options();
        }
    }

    // Process the payment and return the result
    public function process_payment($order_id) {
        $order = new WC_Order($order_id);

        return array(
            'result' => 'success',
            'redirect' => add_query_arg(array(
                'order' => $order->id,
                'key' => $order->order_key,
            ), get_permalink(woocommerce_get_page_id('pay'))),
        );
    }

    public function receipt_page($order_id) {
        echo '<p>Muchas Gracias, por favor paga con tu Billetera de Tigo Money</p>';
        $order = wc_get_order($order_id);
        $posted = wp_unslash($_POST);

        if (isset($posted['tigomoney-phonenumber'])) {
            $phone = trim($posted['tigomoney-phonenumber']);

            if (preg_match("/^[6-7][0-9]{7}$/", $phone)) {
                $tigomoney = new WC_Gateway_TigoMoney_Request($this);
                $pay_url = $tigomoney->get_request_url($order, $posted['tigomoney-phonenumber']);
                wp_redirect($pay_url);
                exit();
            } else {
                echo '<p class="woocommerce-error">Por favor ingresa un número de movil válido.</p>';
            }

        }

        echo $this->generate_form($order);
    }

    public function generate_form($order) {
        $form = '<form action="" method="post" id="payment-form" target="_top">';
        $form .= '<input type="text" id="id_tigomoney-phonenumber" name="tigomoney-phonenumber" required= value="" /> ';
        $form .= '<br><br>';
        $form .= '<input type="submit" class="button alt" id="submit-payment-form" value="Pagar con TigoMoney" /> ';
        $form .= '<a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">Cancelar</a>';
        $form .= '</form>';

        return $form;
    }

    // Look for the current plugin is ready for use
    public function is_available() {
        if ($this->encrypt_key != '' && $this->identity_token != '') {
            return true;
        }
        return false;
    }

    // Settings for TigoMoney Gateway
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Habilitar Medio de Pago', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Habilitar Tigo Money', 'woocommerce'),
                'default' => 'yes',
            ),

            'sandbox' => array(
                'title' => __('Modo de pruebas', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Habilitar Modo de pruebas', 'woocommerce'),
                'default' => 'yes',
            ),

            'debug' => array(
                'title' => __('Registro de desarrollo', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Permitir Registro', 'woocommerce'),
                'default' => 'no',
            ),

            'description' => array(
                'title' => __('Descripción', 'woocommerce'),
                'type' => 'text',
                'desc_tip' => true,
                'description' => __('Describe el método de pago al usuario al finalizar la compra.', 'woocommerce'),
                'default' => __('Paga via TigoMoney.', 'woocommerce'),
            ),

            'identity_token' => array(
                'title' => __('Llave de Identificación', 'woocommerce'),
                'type' => 'text',
                'description' => __('Ingrese su llave TigoMoney de Identificación, esta se usara para identificar al comercio dentro de la pasarela de pagos VIPAGOS. ', 'woocommerce'),
                'default' => '',
                'desc_tip' => true,
            ),

            'encrypt_key' => array(
                'title' => __('Llave de Encriptación', 'woocommerce'),
                'type' => 'text',
                'description' => __('Esta se usara para encriptar los parámetros antes de re direccionar a la pasarela de pagos VIPAGOS así como también desencriptar la respuestas de esta misma.', 'woocommerce'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => '',
            ),

            'confirmation_message' => array(
                'title' => __('Mensaje de Confirmación', 'woocommerce'),
                'type' => 'text',
                'description' => __('Este mensaje será enviado al cliente en el SMS de confirmación cuando el pago se haya realizado de manera exitosa.', 'woocommerce'),
                'default' => 'Mensaje Confirmación',
                'desc_tip' => true,
            ),

            'notify_message' => array(
                'title' => __('Mensaje de Notificación', 'woocommerce'),
                'type' => 'text',
                'description' => __('Mensaje adicional que se enviara en la notificación cuando se realice el cobro.', 'woocommerce'),
                'default' => 'Mensaje Notificacion',
                'desc_tip' => true,

            )
        );
        if (get_woocommerce_currency() != "BOB")
        {
          $this->form_fields['usdbob'] = array(
                  'title' => __('Tipo de Cambio ('.get_woocommerce_currency().') y (BOB)', 'woocommerce'),
                  'type' => 'text',
                  'description' => __('Tipo de cambio entre la moneda del sitio ('.get_woocommerce_currency().') y TigoMoney.', 'woocommerce'),
                  'default' => '1',
                  'desc_tip' => true,
          );
        } else {
          $this->gateway->settings['usdbob'] = 1;
        }

    }

    // Output for the order received page.
    public function thankyou_page() {
        if ($this->instructions) {
            echo wpautop(wptexturize($this->instructions));
        }
    }

    function return_handler() {
        @ob_clean();
        header('HTTP/1.1 200 OK');

        if (!empty($_GET)) {
            if (array_key_exists('r', $_GET)) {
                $tigomoney = new WC_Gateway_TigoMoney_Request($this);
                $posted = $tigomoney->get_response_arguments(str_replace(' ', '+', $_GET['r']));
                $order = new WC_Order($posted['orderId']);

                if ($posted['codRes'] == '0') {
                    $this->payment_status_completed($order, $posted);
                } else {
                    $this->payment_status_failed($order, $posted);
                }

            } else {
                wp_die('Request Failure', 'TigoMoney Request', array('response' => 200));
                exit();
            }
        }

        wp_redirect(wc_get_page_permalink('cart'));
        exit();
    }

    protected function payment_status_failed($order, $posted) {
        $errormessages = array(
            '4' => 'Comercio no Registrado',
            '7' => 'Acceso Denegado por favor intente nuevamente y verifique los datos incorporados',
            '8' => 'PIN no valido, vuelva a intentar',
            '11' => 'Tiempo de respuesta excedido, por favor inicie la transaccion nuevamente',
            '14' => 'Billetera Movil destino no registrada, favor verifique sus datos',
            '17' => 'Monto no valido, verifique los datos proporcionados',
            '19' => 'Comercio no habilitado para el pago, favor comunicarse con el comercio',
            '23' => 'El monto introducido es menor al requerido, favor verifique los datos',
            '24' => 'El monto introducido es mayor al requerido, favor verifique los datos',
            '1001' => 'Los fondos en su Billetera movil son insuficientes, para cargar su billetera vaya al Punto Tigo Money mas cercano, marque *555#',
            '1002' => 'No ingresaste tu PIN o ingresaste un PIN incorrecto, tu transaccion no pudo ser completada, inicia la transaccion nuevamente y verifica en transacciones por completar',
            '1003' => 'Estimado Cliente llego a su limite de monto transaccionado, si tiene alguna consulta comuniquese con el *555',
            '1004' => 'Estimado Cliente excedio su limite de intentos de introducir su PIN, por favor comuniquese con el *555 para solicitar su nuevo PIN',
            '56' => 'Mismo Monto, Origen y Destino dentro de 1 min  Señor Cliente su transaccion no fue completada favor intente nuevamente en 1 minuto',
        );


        if (array_key_exists('codRes', $posted)) {
            wc_add_notice('Tigo Money > ' . $errormessages[$posted['codRes']], 'error');
        } else {
            wc_add_notice('Tigo Money > Lo sentimos, hubo un error desconocido', 'error');
        }

        $order->update_status('failed', 'Pago rechazado: ' . $posted['codRes'] . ' ' . $posted['mensaje']);

        wp_redirect(wc_get_page_permalink('cart'));
    }


    protected function payment_status_completed($order, $posted) {
        if ($order->has_status('completed')) {
            // Aborting, Order is already complete.
            exit;
        }

        // Good
        $order->add_order_note($posted['mensaje']);
        $order->reduce_order_stock();
        $order->payment_complete();

        WC()->cart->empty_cart();

        wc_add_notice('Tigo Money > ' . $posted['mensaje'], 'success');

        wp_redirect($this->get_return_url($order));
    }
}
