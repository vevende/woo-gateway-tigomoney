<?php

class Autoload {
	public static function register() {
		spl_autoload_register( array( 'self', 'spl_autoload_register' ) );
	}

	public static function spl_autoload_register( $class_name ) {
		$class_path = dirname( dirname( __FILE__ ) ) . '/classes/' . $class_name . '.php';
		if ( file_exists( $class_path ) ) {
			require_once $class_path;
		}
	}
}

Autoload::register();