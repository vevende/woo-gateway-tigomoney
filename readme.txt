=== Woo TigoMoney Gateway ===

Contributors: vevende
Tags: store, sales, sell, mobile payment, tigo, tigo money, tigo money bolivia, woocommerce, bolivia, ecommerce, e-commerce, 
Requires at least: 4.1
Tested up to: 4.5
Stable tag: 2.7.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Provides integration between TigoMoney (Bolivia) mobile payments and WooCommerce.

== Description ==
Woo TigoMoney Gateway plugin allows to easily add TigoMoney Bolivia payment option to your Wordpress / Woocommerce website store. Tigo Money Users from Bolivia will be able to pay to your business tigo money account using their virtual mobile wallet money. 

You can integrate this to your Wordpress / Woocommerce Store and after applying to Tigo Money Business account start selling your products and services online. The plugin features also the testing mode for the required validation for the account and sandbox testing of sales.

= Features =
* Sell products and services using Tigo Money in your Wordpress / Woocommerce webpage.
* Helps in the activation of the Tigo Money Business Account with the Testing Mode
* You can set and modify the identification and encriptation key directly in the admin panel.
* The confirmation and notification message are customizable.
* At the Checkout, two fields are enabled: CI and Phone number.
* You can set the exchange rate from your store to the Bolivian Boliviano, for example if your store is in United States Dollars USD 
* Compatible with Woocommerce and any Woocommerce enabled theme.

== Installation ==
1. For installing the plugin, you need first to install Woocommerce plugin and have it activated.

2. After Woocommerce is working, download Woo TigoMoney Gateway or select it from the Wordpress Plugin Directory. In the admin panel, select plugin -> install new. Then upload or search it in the Wordpress Plugin Directory.

3. After installation of Tigo Money Plugin, you have to configure the basic settings in the plugin: 
Go to WooCommerce -> Settings. In the Checkout panel, select Tigo Money of the different Checkout Options.
* Enable Tigo Money
* Select if you need to enable the testing mode
* Select the title of the payment method for the user (default is TigoMoney)
* Select the description of the payment method for the user (default is Pay with TigoMoney)
* Enter the identification and encryption key provided by Tigo Money
* Enter your own Confirmation and Notification message
* If your webstore is not in bolivian boliviano (BOB) you can add an exchenge rate.
* In case of problems with the plugin, you can enable the debug log for troubleshooting.

Save the settings and the plugin is insalled. It will appear as an option of purchase for your clients in the checkout screen.

= Usage =
Once the plugin is activated, it enables itself as one of the methods of the WC_Payment_Gateway and let users follow the same steps as the other payments methods.

The user after Tigo Money payment method is selected, will be presented with a button that redirects to Tigo Money platform, where the user review the information and validates a Captcha entry. After this, the user receives a message where the user should confirm his PIN, or confirm the transaction via Tigo Money app.

After the payment is effective, woocommerce receives a confirmation of the payment and the buying process continues.

== Frequently Asked Questions ==
= Can I accept Tigo Money payments if I have a Tigo Money Wallet?
No, you need a Business Account with Tigo Money. You should register your account with them first.

= Can I use to process payments in Wordpress without installing woocommerce
No, this plugin requires woocommerce to work.

== Screenshots ==
1. Tigo Money Woocommerce Admin Panel Configuration
2. Tigo Money Checkout Process
3. Tigo Money Gateway (Vipagos) Confirmation Screen

== Changelog ==
= Version 2.5 
* Money exchange rate (for example, for using in websites that charge in USD dollars)
* Preparation for public use

= Version 2.0
* Added new version of Tigo Money response codes.

Version 1.0
* Basic funcionality

== Upgrade Notice ==
None

== Copyright ==

woo-gateway-tigomoney, Copyright 2016 Vevende.com
woo-gateway-tigomoney is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

== Commercial Support ==
If you need help installing or modifying this plugin, you can get commercial support sending an email to info@zoftco.com and accessing http://vevende.com
