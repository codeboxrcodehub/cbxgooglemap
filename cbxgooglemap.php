<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codeboxr.com
 * @since             1.0.0
 * @package           Cbxgooglemap
 *
 * @wordpress-plugin
 * Plugin Name:       CBX Map for Google Map & OpenStreetMap
 * Plugin URI:        https://codeboxr.com/product/cbx-google-map-for-wordpress/
 * Description:       Easy responsive embed of google map and openstreet map
 * Version:           2.0.0
 * Author:            Codeboxr
 * Author URI:        https://codeboxr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxgooglemap
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
use Cbx\Googlemap\Helpers\CBXGooglemapHelper;

if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'CBXGOOGLEMAP_PLUGIN_NAME' ) or define( 'CBXGOOGLEMAP_PLUGIN_NAME', 'cbxgooglemap' );
defined( 'CBXGOOGLEMAP_PLUGIN_VERSION' ) or define( 'CBXGOOGLEMAP_PLUGIN_VERSION', '2.0.0' );
defined( 'CBXGOOGLEMAP_BASE_NAME' ) or define( 'CBXGOOGLEMAP_BASE_NAME', plugin_basename( __FILE__ ) );
defined( 'CBXGOOGLEMAP_ROOT_PATH' ) or define( 'CBXGOOGLEMAP_ROOT_PATH', plugin_dir_path( __FILE__ ) );
defined( 'CBXGOOGLEMAP_ROOT_URL' ) or define( 'CBXGOOGLEMAP_ROOT_URL', plugin_dir_url( __FILE__ ) );

defined( 'CBXGOOGLEMAP_WP_MIN_VERSION' ) or define( 'CBXGOOGLEMAP_WP_MIN_VERSION', '5.3' );
defined( 'CBXGOOGLEMAP_PHP_MIN_VERSION' ) or define( 'CBXGOOGLEMAP_PHP_MIN_VERSION', '7.4' );

// Include the main class
if ( ! class_exists( 'CBXGoogleMap', false ) ) {
	include_once CBXGOOGLEMAP_ROOT_PATH . 'includes/CBXGoogleMap.php';
}

/**
 * Checking wp version
 *
 * @param $version
 *
 * @return bool
 */
function cbxgooglemap_compatible_wp_version( $version = '' ) {
    if($version == '') $version = CBXGOOGLEMAP_WP_MIN_VERSION;

    if ( version_compare( $GLOBALS['wp_version'], $version, '<' ) ) {
        return false;
    }

    // Add sanity checks for other version requirements here

    return true;
}//end method cbxgooglemap_compatible_wp_version

/**
 * Checking php version
 *
 * @param $version
 *
 * @return bool
 */
function cbxgooglemap_compatible_php_version( $version = '' ) {
    if($version == '') $version = CBXGOOGLEMAP_PHP_MIN_VERSION;

    if ( version_compare( PHP_VERSION, $version, '<' ) ) {
        return false;
    }

    return true;
}//end method cbxgooglemap_compatible_php_version

register_activation_hook( __FILE__, 'activate_cbxgooglemap' );
register_deactivation_hook( __FILE__, 'deactivate_cbxgooglemap' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cbxgooglemap-activator.php
 */
function activate_cbxgooglemap() {
	$wp_version  = CBXGOOGLEMAP_WP_MIN_VERSION;
	$php_version = CBXGOOGLEMAP_PHP_MIN_VERSION;

    $activate_ok = true;
	if ( ! cbxgooglemap_compatible_wp_version() ) {
        $activate_ok = false;
		deactivate_plugins( plugin_basename( __FILE__ ) );

		/* translators: WordPress version */
		wp_die( sprintf( esc_html__( 'CBX Google Map plugin requires WordPress %s or higher!', 'cbxgooglemap' ), esc_attr($wp_version) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	if ( ! cbxgooglemap_compatible_php_version() ) {
        $activate_ok = false;
		deactivate_plugins( plugin_basename( __FILE__ ) );

		/* translators: PHP version */
		wp_die( sprintf( esc_html__( 'CBX Google Map plugin requires PHP %s or higher!', 'cbxgooglemap' ), esc_attr($php_version) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

    if($activate_ok) {
        cbxgooglemap_core();
        CBXGooglemapHelper::activate();
    }
}//end method activate_cbxgooglemap

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cbxgooglemap-deactivator.php
 */
function deactivate_cbxgooglemap() {
	CBXGooglemapHelper::deactivate();
}

/**
 * Initialize the plugin manually
 *
 * @return CBXGoogleMap|null
 */
function cbxgooglemap_core() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	global $cbxgooglemap_core;

	if ( ! isset( $cbxgooglemap_core ) ) {
		$cbxgooglemap_core = run_cbxgooglemap();
	}

	return $cbxgooglemap_core;
}//end method cbxgooglemap_core

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cbxgooglemap() {
	return CBXGoogleMap::instance();
}//end method run_cbxgooglemap

$GLOBALS['cbxgooglemap_core'] = run_cbxgooglemap();