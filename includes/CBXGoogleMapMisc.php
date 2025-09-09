<?php

namespace Cbx\Googlemap;

// If this file is called directly, abort.

use Cbx\Googlemap\Helpers\CBXGooglemapHelper;


if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Misc Class
 */
class CBXGoogleMapMisc {
	/**
	 * Method for constructor
	 *
	 * @since v2.0.0
	 */
	public function __construct() {

	}//end constructor

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		//load_plugin_textdomain('cbxgooglemap',false,CBXGOOGLEMAP_ROOT_PATH . 'languages/');
	}//end method load_plugin_textdomain

	/**
	 * Show a notice to anyone who has just installed the plugin for the first time
	 * This notice shouldn't display to anyone who has just updated this plugin
	 */
	public function plugin_activate_upgrade_notices() {
		$activation_notice_shown = false;

		$kiss_html_arr = [
			'strong' => [],
			'a'      => [
				'href'  => [],
				'class' => []
			]
		];

		if ( get_option( 'cbxgooglemap_flush_rewrite_rules' ) == 'true' ) {
			flush_rewrite_rules();
			delete_option( 'cbxgooglemap_flush_rewrite_rules' );
		}

		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'cbxgooglemap_activated_notice' ) ) {
			echo '<div class="notice notice-success is-dismissible" style="border-color: #2153cc !important;">';
			/* translators: %s: Plugin Version */
			echo '<p>' . sprintf( wp_kses( __( 'Thanks for installing/deactivating <strong>CBX Map for Google Map & OpenStreetMap</strong> V%s - Codeboxr Team',
					'cbxgooglemap' ), $kiss_html_arr ),
					esc_attr( CBXGOOGLEMAP_PLUGIN_VERSION ) ) . '</p>';
			/* translators: 1: Admin URL 2: Admin URL */
			echo '<p>' . sprintf( wp_kses( __( 'Check <a style="color: #6648fe !important; font-weight: bold;" href="%1$s" target="_blank">Documentation</a> | Create <a style="color: #6648fe !important; font-weight: bold;" href="%2$s" target="_blank">Map</a>',
					'cbxgooglemap' ), $kiss_html_arr ),
					'https://codeboxr.com/doc/cbxmap-doc/',
					esc_url( admin_url( 'post-new.php?post_type=cbxgooglemap' ) )
				) . '</p>';

			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'cbxgooglemap_activated_notice' );

			$this->pro_addon_compatibility_campaign();

			$activation_notice_shown = true;
		}

		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'cbxgooglemap_upgraded_notice' ) ) {
			if ( ! $activation_notice_shown ) {
				echo '<div class="notice notice-success is-dismissible" style="border-color: #2153cc !important;">';
				/* translators: %s: Plugin Version */
				echo '<p>' . sprintf( wp_kses( __( 'Thanks for upgrading <strong>CBX Map for Google Map & OpenStreetMap</strong> V%s , enjoy the new features and bug fixes - Codeboxr Team',
						'cbxgooglemap' ), $kiss_html_arr ),
						esc_attr( CBXGOOGLEMAP_PLUGIN_VERSION ) ) . '</p>';

				/* translators: 1: Admin URL 2: Admin URL */
				echo '<p>' . sprintf( wp_kses( __( 'Check <a style="color: #6648fe !important; font-weight: bold;" href="%1$s" target="_blank">Documentation</a> | Create <a style="color: #6648fe !important; font-weight: bold;" href="%2$s" target="_blank">Map</a>', 'cbxgooglemap' ), [ 'strong' => [], 'a' => [ 'href' => [], 'style' => [] ] ] ),
						'https://codeboxr.com/doc/cbxmap-doc/',
						esc_url( admin_url( 'post-new.php?post_type=cbxgooglemap' ) )
					) . '</p>';
				echo '</div>';

				$this->pro_addon_compatibility_campaign();
			}

			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'cbxgooglemap_upgraded_notice' );
		}//end cbxgooglemap_upgraded_notice

		if ( get_transient( 'cbxgooglemappro_deactivated_notice' ) ) {
			echo '<div class="notice notice-error is-dismissible" style="border-color: red !important;">';
			echo '<p>' . wp_kses( __( 'Currently installed <strong>CBX Map for Google Map & OpenStreetMap Pro Addon</strong> version 1.0.5(or earlier) is not compatible with the latest version of core plugin CBX Map for Google Map & OpenStreetMap V1.1.12 or later. - Codeboxr Team',
					'cbxgooglemap' ), $kiss_html_arr ) . '</p>';
			echo '</div>';

			delete_transient( 'cbxgooglemappro_deactivated_notice' );
		}//end checking cbxgooglemappro_deactivated_notice
	}//end plugin_activate_upgrade_notices

	/**
	 * Check plugin compatibility and pro addon install campaign
	 */
	public function pro_addon_compatibility_campaign() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		//if the pro addon is active or installed
		if ( defined( 'CBXGOOGLEMAPPRO_PLUGIN_NAME' ) || in_array( 'cbxgooglemappro/cbxgooglemappro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			//plugin is activated

			$plugin_version  = CBXGOOGLEMAPPRO_PLUGIN_VERSION;
			$pro_min_version = '2.0.0';

			if ( version_compare( $plugin_version, $pro_min_version, '<' ) ) {
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'CBX Map Pro Addon current version is not compatible with the latest CBX Map core plugin. Please update CBX Map Pro Addon to version 2.0.0 or later  - Codeboxr Team', 'cbxgooglemap' ) . '</p></div>';
			}

		} else {
			/* translators: %s: Plugin Link */
			$message = sprintf( __( 'CBX Map Pro Addon has distance search based listing, multiple marker in one map and more extra features, <a target="_blank" href="%s">try it</a> - Codeboxr Team', 'cbxgooglemap' ), esc_url( 'https://codeboxr.com/product/cbx-google-map-for-wordpress/' ) );
			echo '<div class="notice notice-success is-dismissible"><p>' . wp_kses_post( $message ) . '</p></div>';
		}
	}//end pro_addon_compatibility_campaign

	/**
	 * Pro Addon update message
	 */
	public function plugin_update_message_pro_addon() {
		/* translators: 1: Plugin Manual Download Link 2: Plugin Manual Download Link */
		echo ' ' . sprintf( wp_kses( __( 'Check how to <a style="color:#9c27b0 !important; font-weight: bold;" href="%1$s"><strong>Update manually</strong></a> , download the latest version from <a style="color:#9c27b0 !important; font-weight: bold;" href="%2$s"><strong>My Account</strong></a> section of Codeboxr.com', 'cbxgooglemap' ), [ 'strong' => [], 'a' => [ 'href' => [], 'style' => [] ] ] ), 'https://codeboxr.com/manual-update-pro-addon/', 'https://codeboxr.com/my-account/' );
	}//end plugin_update_message_pro_addon

	/**
	 * If we need to do something in upgrader process is completed
	 *
	 */
	public function plugin_upgrader_process_complete() {
		$saved_version = get_option( 'cbxgooglemap_version' );

		if ( $saved_version === false || version_compare( $saved_version, CBXGOOGLEMAP_PLUGIN_VERSION, '<' ) ) {
			set_transient( 'cbxgooglemap_flush_rewrite_rules', 1 );
			set_transient( 'cbxgooglemap_upgraded_notice', 1 );
			update_option( 'cbxgooglemap_version', CBXGOOGLEMAP_PLUGIN_VERSION );


			//pro addon compatibility
			$this->check_pro_addon();
		}
	}//end plugin_upgrader_process_complete

	/**
	 * Show notice about pro addon deactivation
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function check_pro_addon() {
		cbxgooglemap_check_and_deactivate_plugin( 'cbxgooglemappro/cbxgooglemappro.php', '2.0.0', 'cbxgooglemap_proaddon_deactivated' );
	}//end method check_pro_addon

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function plugin_listing_setting_link( $links ) {
		return array_merge( [
			'settings' => '<a style="color:#39A96B; font-weight: bold;" target="_blank" href="' . esc_url( admin_url( 'edit.php?post_type=cbxgooglemap&page=cbxgooglemap_settings' ) ) . '">' . esc_attr__( 'Settings', 'cbxgooglemap' ) . '</a>'
		], $links );

	}//end plugin_listing_setting_link

	/**
	 * Filters the array of row meta for each/specific plugin in the Plugins list table.
	 * Appends additional links below each/specific plugin on the plugins page.
	 *
	 * @access  public
	 *
	 * @param  array  $links_array  An array of the plugin's metadata
	 * @param  string  $plugin_file_name  Path to the plugin file
	 * @param  array  $plugin_data  An array of plugin data
	 * @param  string  $status  Status of the plugin
	 *
	 * @return  array       $links_array
	 * @since 1.0.0
	 */
	public function custom_plugin_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ) {
		if ( strpos( $plugin_file_name, CBXGOOGLEMAP_BASE_NAME ) !== false ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}


			$links_array[] = '<a style="color:#39A96B; font-weight: bold;" href="https://wordpress.org/support/plugin/cbxgooglemap/" target="_blank">' . esc_attr__( 'Free Support', 'cbxgooglemap' ) . '</a>';
			$links_array[] = '<a style="color:#39A96B; font-weight: bold;" href="https://wordpress.org/plugins/cbxgooglemap/#reviews" target="_blank">' . esc_attr__( 'Reviews', 'cbxgooglemap' ) . '</a>';
			$links_array[] = '<a target="_blank" style="color:#f44336 !important; font-weight: bold;" href="https://codeboxr.com/doc/cbxmap-doc/" aria-label="' . esc_attr__( 'Documentation', 'cbxgooglemap' ) . '">' . esc_html__( 'Documentation', 'cbxgooglemap' ) . '</a>';


			if ( in_array( 'cbxgooglemappro/cbxgooglemappro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined( 'CBXGOOGLEMAPPRO_PLUGIN_NAME' ) ) {
				$links_array[] = '<a style="color:#39A96B; font-weight: bold;" href="https://codeboxr.com/contact-us/" target="_blank">' . esc_attr__( 'Pro Support', 'cbxgooglemap' ) . '</a>';
			} else {
				$links_array[] = '<a target="_blank" style="color:#f44336 !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-google-map-for-wordpress/#downloadarea" aria-label="' . esc_attr__( 'Try Pro Addon', 'cbxgooglemap' ) . '">' . esc_html__( 'Try Pro Addon', 'cbxgooglemap' ) . '</a>';
			}
		}

		return $links_array;
	}//end custom_plugin_row_meta

	/**
	 * Show plugin update
	 *
	 * @param $plugin_file
	 * @param $plugin_data
	 *
	 * @return void
	 */
	public function custom_message_after_plugin_row_proaddon( $plugin_file, $plugin_data ) {
		if ( $plugin_file !== 'cbxgooglemappro/cbxgooglemappro.php' ) {
			return;
		}

		if ( defined( 'CBXGOOGLEMAPPRO_PLUGIN_NAME' ) ) {
			return;
		}

		$pro_addon_version  = CBXGooglemapHelper::get_any_plugin_version( 'cbxgooglemappro/cbxgooglemappro.php' );
		$pro_latest_version = '2.0.0';

		if ( $pro_addon_version != '' && version_compare( $pro_addon_version, $pro_latest_version, '<' ) ) {
			// Custom message to display

			$plugin_manual_update = 'https://codeboxr.com/manual-update-pro-addon/';


			/* translators:translators: %s: plugin setting url for licence */
			$custom_message = wp_kses( sprintf( __( '<strong>Note:</strong> CBX Map Pro Addon is custom plugin. This plugin can not be auto update from dashboard/plugin manager. For manual update please check <a target="_blank" href="%1$s">documentation</a>. <strong style="color: red;">It seems this plugin\'s current version is older than %2$s . To get the latest pro addon features, this plugin needs to upgrade to %2$s or later.</strong>', 'cbxgooglemap' ), esc_url( $plugin_manual_update ),
				$pro_latest_version ), [ 'strong' => [ 'style' => [] ], 'a' => [ 'href' => [], 'target' => [] ] ] );

			// Output a row with custom content
			echo '<tr class="plugin-update-tr">
            <td colspan="3" class="plugin-update colspanchange">
                <div class="notice notice-warning inline">
                    ' . wp_kses_post( $custom_message ) . '
                </div>
            </td>
          </tr>';
		}
	}//end method custom_message_after_plugin_row_proaddon
}//end class ComfortInvoiceMisc