<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

use Cbx\Googlemap\CBXGoogleMapAdmin;
use Cbx\Googlemap\CBXGoogleMapMisc;
use Cbx\Googlemap\CBXGoogleMapPublic;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cbxgooglemap
 * @subpackage Cbxgooglemap/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
final class CBXGoogleMap {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.1.12
	 */
	private static $instance = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.12
	 * @access   private
	 * @var      string $settings The ID of this plugin.
	 */
	private $settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        $this->plugin_name = CBXGOOGLEMAP_PLUGIN_NAME;
        $this->version     = CBXGOOGLEMAP_PLUGIN_VERSION;

        if ( cbxgooglemap_compatible_php_version() ) {
            $GLOBALS['cbxgooglemap_loaded'] = true;
            $this->include_files();



            $this->define_common_hooks();
            $this->define_admin_hooks();
            $this->define_public_hooks();
        }
        else {
            add_action( 'admin_notices', [ $this, 'php_version_notice' ] );
        }
	}//end method constructor

	/**
	 * Include necessary files
	 *
	 * @return void
	 */
	private function include_files() {
		require_once __DIR__ . '/../vendor/autoload.php';
	}//end method include_files

	/**
	 * Singleton Instance.
	 *
	 * Ensures only one instance of cbxgooglemap is loaded or can be loaded.
	 *
	 * @return self Main instance.
	 * @see cbxgooglemap_run()
	 * @since  1.1.12
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}//end method instance

	private function define_common_hooks() {
		$misc = new CBXGoogleMapMisc();

		//upgrade process
		//add_action('admin_init', [$misc, 'admin_init_upgrader_process']);
		//add_action( 'upgrader_process_complete', [ $misc, 'plugin_upgrader_process_complete' ], 10, 2 );

        add_action( 'plugins_loaded', [ $misc, 'plugin_upgrader_process_complete' ] );
		add_action( 'admin_notices', [ $misc, 'plugin_activate_upgrade_notices' ] );
        add_filter( 'plugin_action_links_' . CBXGOOGLEMAP_BASE_NAME, [ $misc, 'plugin_listing_setting_link' ] );
        add_filter( 'plugin_row_meta', [ $misc, 'custom_plugin_row_meta' ], 10, 4 );

		add_action( 'init', [ $misc, 'check_pro_addon' ] );
		add_action( 'after_plugin_row_cbxgooglemappro/cbxgooglemappro.php', [
			$misc,
			'custom_message_after_plugin_row_proaddon'
		], 10, 2 );

	}//end method define_common_hooks

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		global $wp_version;
		$plugin_admin = new CBXGoogleMapAdmin();

		//adding the setting action
		add_action( 'admin_init', [ $plugin_admin, 'setting_init' ] );
		//add new post type
		add_action( 'init', [ $plugin_admin, 'create_post_type' ], 0 );
		//create opverview menu page
		add_action( 'admin_menu', [ $plugin_admin, 'menu_pages' ] );
		//display meta fields	
		add_action( 'add_meta_boxes', [ $plugin_admin, 'add_meta_boxes' ] );
		//save meta fields
		add_action( 'save_post', [ $plugin_admin, 'metabox_save' ], 10, 3 ); //save meta
		add_action( 'wp_ajax_cbxgooglemap_settings_reset_load', [ $plugin_admin, 'settings_reset_load' ] );
		add_action( 'wp_ajax_cbxgooglemap_settings_reset', [ $plugin_admin, 'plugin_options_reset' ] );
		// Export/Import Settings Api
		add_action( 'template_redirect', [ $plugin_admin, 'settings_export' ] );
		add_action( 'wp_ajax_cbxgooglemap_settings_import', [ $plugin_admin, 'settings_import' ] );
		add_action( 'wp_ajax_cbxgooglemap_settings_reset_section', [ $plugin_admin, 'plugin_reset_section' ] );

		add_filter( 'manage_edit-cbxgooglemap_columns', [ $plugin_admin, 'cbxgooglemap_add_new_columns' ] );
		add_action( 'manage_cbxgooglemap_posts_custom_column', [ $plugin_admin, 'cbxgooglemap_manage_columns' ] );
		//js and css
		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_scripts' ] );

		//gutenberg
		add_action( 'init', [ $plugin_admin, 'gutenberg_blocks' ] );

		//gutenberg blocks
		if ( version_compare( $wp_version, '5.8' ) >= 0 ) {
			add_filter( 'block_categories_all', [ $plugin_admin, 'gutenberg_block_categories' ], 10, 2 );
		} else {
			add_filter( 'block_categories', [ $plugin_admin, 'gutenberg_block_categories' ], 10, 2 );
		}

		//add_filter( 'block_categories', $plugin_admin, 'gutenberg_block_categories', 10, 2 );
		add_action( 'enqueue_block_editor_assets', [ $plugin_admin, 'enqueue_block_editor_assets' ] );
	}//end define_admin_hooks

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new CBXGoogleMapPublic();

		add_action( 'init', [ $plugin_public, 'init_shortcodes' ] );
		add_action( 'widgets_init', [ $plugin_public, 'register_widget' ] );

		//elementor
		add_action( 'elementor/widgets/widgets_registered', [ $plugin_public, 'init_elementor_widgets' ] );
		add_action( 'elementor/elements/categories_registered', [ $plugin_public, 'add_elementor_widget_categories' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $plugin_public, 'elementor_icon_loader' ], 99999 );

		add_action( 'wp_enqueue_scripts', [ $plugin_public, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $plugin_public, 'enqueue_scripts' ] );
	}//end method define_public_hooks

    /**
     * Show php version notice in dashboard
     *
     * @return void
     */
    public function php_version_notice() {
        echo '<div class="error"><p>';
        /* Translators:  PHP Version */
        echo sprintf(esc_html__( 'CBX Map requires at least PHP %s. Please upgrade PHP to run CBX Map.', 'cbxgooglemap' ), esc_attr(CBXGOOGLEMAP_PHP_MIN_VERSION));
        echo '</p></div>';
    }//end method php_version_notice
}//end class CBXGoogleMap