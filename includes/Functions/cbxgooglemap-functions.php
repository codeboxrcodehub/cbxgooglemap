<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use enshrined\svgSanitize\Sanitizer;
use FreepikLabs\DomPurify\Purifier;

if ( ! function_exists( 'cbxgooglemap_esc_svg' ) ) {
	/**
	 * SVG sanitizer
	 *
	 * @param string $svg_content The content of the SVG file
	 *
	 * @return string|false The SVG content if found, or false on failure.
	 * @since 1.0.0
	 */
	function cbxgooglemap_esc_svg( $svg_content = '' ) {
		// Create a new sanitizer instance
		$sanitizer = new Sanitizer();

		return $sanitizer->sanitize( $svg_content );
	}// end method cbxgooglemap_esc_svg
}


if ( ! function_exists( 'cbxgooglemap_load_svg' ) ) {
	/**
	 * Load an SVG file from a directory.
	 *
	 * @param string $svg_name The name of the SVG file (without the .svg extension).
	 * @param string $directory The directory where the SVG files are stored.
	 *
	 * @return string|false The SVG content if found, or false on failure.
	 * @since 1.0.0
	 */
	function cbxgooglemap_load_svg( $svg_name = '', $folder = '' ) {
		//note: code partially generated using chatgpt
		if ( $svg_name == '' ) {
			return '';
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$credentials = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, null );
		if ( ! WP_Filesystem( $credentials ) ) {
			return; // Error handling here
		}

		global $wp_filesystem;

		$directory = cbxgooglemap_icon_path();

		// Sanitize the file name to prevent directory traversal attacks.
		$svg_name = sanitize_file_name( $svg_name );
		if ( $folder != '' ) {
			$folder = trailingslashit( $folder );
		}

		// Construct the full file path.
		$file_path = $directory . $folder . $svg_name . '.svg';

		$file_path = apply_filters( 'cbxgooglemap_svg_file_path', $file_path, $svg_name );

		// Check if the file exists.
		if ( $wp_filesystem->exists( $file_path ) && is_readable( $file_path ) ) {
			// Get the SVG file content.
			return $wp_filesystem->get_contents( $file_path );
		} else {
			// Return false if the file does not exist or is not readable.
			return '';
		}
	}//end method cbxgooglemap_load_svg
}

if ( ! function_exists( 'cbxgooglemap_icon_path' ) ) {
	/**
	 * Form icon path
	 *
	 * @return mixed|null
	 * @since 1.0.0
	 */
	function cbxgooglemap_icon_path() {
		$directory = trailingslashit( CBXGOOGLEMAP_ROOT_PATH ) . 'assets/icons/';

		return apply_filters( 'cbxgooglemap_icon_path', $directory );
	}//end method cbxgooglemap_icon_path
}

if ( ! function_exists( 'cbxgooglemap_is_rest_api_request' ) ) {
	/**
	 * Check if doing rest request
	 *
	 * @return bool
	 */
	function cbxgooglemap_is_rest_api_request() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );

		return ( false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), $rest_prefix ) );
	}//end function cbxgooglemap_is_rest_api_request
}

if ( ! function_exists( 'cbxgooglemap_doing_it_wrong' ) ) {
	/**
	 * Wrapper for _doing_it_wrong().
	 *
	 * @param string $function Function used.
	 * @param string $message Message to log.
	 * @param string $version Version the message was added in.
	 *
	 * @since  1.0.0
	 */
	function cbxgooglemap_doing_it_wrong( $function, $message, $version ) {
		// @codingStandardsIgnoreStart
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

		if ( wp_doing_ajax() || cbxgooglemap_is_rest_api_request() ) {
			do_action( 'doing_it_wrong_run', $function, $message, $version );
			error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
		} else {
			_doing_it_wrong( $function, $message, $version );
		}
		// @codingStandardsIgnoreEnd
	}//end function cbxgooglemap_doing_it_wrong
}

if ( ! function_exists( 'cbxgooglemap_login_url_with_redirect' ) ) {
	function cbxgooglemap_login_url_with_redirect() {
		//$login_url          = wp_login_url();
		//$redirect_url       = '';

		if ( is_singular() ) {
			$login_url = wp_login_url( get_permalink() );
			//$redirect_url = get_permalink();
		} else {
			global $wp;
			$login_url = wp_login_url( home_url( add_query_arg( [], $wp->request ) ) );
			//$redirect_url = home_url( add_query_arg( [], $wp->request ) );
		}

		return $login_url;
	}//end function cbxgooglemap_login_url_with_redirect
}

if ( ! function_exists( 'cbxgooglemap_check_and_deactivate_plugin' ) ) {
	/**
	 * Check any plugin and if version less than
	 *
	 * @param string $plugin_slug plugin slug
	 * @param string $required_version required plugin version
	 * @param string $transient transient name
	 *
	 * @return bool|void
	 * @since 2.0.0
	 */
	function cbxgooglemap_check_and_deactivate_plugin( $plugin_slug = '', $required_version = '', $transient = '' ) {
		if ( $plugin_slug == '' ) {
			return;
		}

		if ( $required_version == '' ) {
			return;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Check if the plugin is active
		if ( is_plugin_active( $plugin_slug ) ) {
			// Get the plugin data
			$plugin_data    = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_slug );
			$plugin_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
			if ( $plugin_version == '' || is_null( $plugin_version ) ) {
				return;
			}

			// Compare the plugin version with the required version
			if ( version_compare( $plugin_version, $required_version, '<' ) ) {
				// Deactivate the plugin
				deactivate_plugins( $plugin_slug );
				if ( $transient != '' ) {
					set_transient( $transient, 1 );
				}
			}
		}

		//return false;
	}//end method cbxgooglemap_check_and_deactivate_plugin
}

if(!function_exists('cbxgooglemap_decode_entities_array')){
	function cbxgooglemap_decode_entities_array($arr = []){
		return array_map(function ($v) {
			return is_string($v) ? html_entity_decode($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $v;
		}, $arr);
	}
}