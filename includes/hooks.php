<?php

function woo_tigomoney_add_bob_currency($currencies)
{
    $currencies['BOB'] = __('Bolivian Boliviano (Bs.)', 'woocommerce');
    return $currencies;
}

add_filter('woocommerce_currencies', 'woo_tigomoney_add_bob_currency');

function woo_tigomoney_add_bob_currency_symbol($currency_symbol, $currency)
{
    switch ($currency) {
        case 'BOB':
            $currency_symbol = 'Bs.';
            break;
    }
    return $currency_symbol;
}

add_filter('woocommerce_currency_symbol', 'woo_tigomoney_add_bob_currency_symbol', 10, 2);
