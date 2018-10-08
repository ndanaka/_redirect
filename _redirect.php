<?php
/**
 * Plugin Name:     _redirect
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     _redirect
 * Domain Path:     /languages
 * Version:         nightly
 *
 * @package         _redirect
 */

namespace _redirect;
use Miya\WP\GH_Auto_Updater;

require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

add_action( 'init', '_redirect\activate_updater' );

function activate_updater() {
	$plugin_slug = plugin_basename( __FILE__ ); // e.g. `hello/hello.php`.
	$gh_user = 'miya0001';                      // The user name of GitHub.
	$gh_repo = '_redirect';       // The repository name of your plugin.

	// Activate automatic update.
	new GH_Auto_Updater( $plugin_slug, $gh_user, $gh_repo );
}

function do_redirect() {
	$redirects = get_option( '_redirect', array() );
	if ( empty( $redirects ) ) {
		$redirects = array();
	}

	foreach ( $redirects as $redirect ) {
		$from = trim( $redirect['from'] );
		if ( ! empty( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] === $from ) {
			header( "Location: " . trim( $redirect['to'] ),
				true, trim( $redirect['code'] ) );
			exit;
		}
	}
}

add_action( 'plugins_loaded', function() {
	if ( is_admin() ) {
		$admin = new Admin();
		$admin->activate();
	} else {
		do_redirect();
	}
} );
