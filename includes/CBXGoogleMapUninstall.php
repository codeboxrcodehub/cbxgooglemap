<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

use Cbx\Googlemap\CBXGooglemapSettings;
use Cbx\Googlemap\Helpers\CBXGooglemapHelper;

/**
 * Fired during plugin uninstall/delete.
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 * @since      1.0.0
 * @package    cbxgooglemap
 * @subpackage cbxgooglemap/includes
 * @author     CBX Team <info@codeboxr.com>
 */
class CBXGoogleMapUninstall {
	/**
	 * Uninstall plugin functionality
	 *
	 *
	 * @since    1.1.3
	 */
	public static function uninstall() {
		// For the regular site.
		if ( ! is_multisite() ) {
			self::uninstall_tasks();
		} else {
			//for multi site
			global $wpdb;

			$blog_ids         = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM %s", $wpdb->blogs ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$original_blog_id = get_current_blog_id();

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				self::uninstall_tasks();
			}

			switch_to_blog( $original_blog_id );
		}
	}//end method uninstall

	/**
	 * Do the necessary uninstall tasks
	 *
	 * @return void
	 */
	public static function uninstall_tasks() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$settings             = new CBXGooglemapSettings();
		$delete_global_config = $settings->get_field( 'delete_global_config', 'cbxgooglemap_tools', 'no' );

		if ( $delete_global_config == 'yes' ) {
			//before hook
			do_action( 'cbxgooglemap_plugin_uninstall_before' );

			//delete options
			$option_values = CBXGooglemapHelper::getAllOptionNames();

			do_action( 'cbxgooglemap_plugin_options_deleted_before' );

			foreach ( $option_values as $key => $option_value ) {
				$option = $option_value['option_name'];

				do_action( 'cbxgooglemap_plugin_option_delete_before', $option );
				delete_option( $option );
				do_action( 'cbxgooglemap_plugin_option_delete_after', $option );
			}

			do_action( 'cbxgooglemap_plugin_options_deleted_after' );
			do_action( 'cbxgooglemap_plugin_options_deleted' );
			//end delete options

			//after hook
			do_action( 'cbxgooglemap_plugin_uninstall_after' );
		}
	}//end method uninstall
}//end class Uninstall
