<?php
namespace Cbx\Googlemap\Helpers;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

use Cbx\Googlemap\CBXGooglemapSettings;


class CBXGooglemapHelper {
	/**
	 * Is gutenberg edit page
	 *
	 * @return bool
	 *
	 * @since 1.1.5
	 */
	public static function is_gutenberg_page() {
		//if(!is_admin()) return false;
		if ( function_exists( 'is_gutenberg_page' ) &&
		     is_gutenberg_page()
		) {
			// The Gutenberg plugin is on.
			return true;
		}

		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			// Gutenberg page on 5+.
			return true;
		}

		return false;
	}//end is_gutenberg_page


	/**
	 * Enqueue css and js when needed  (for map render)
	 *
	 * @param bool $enqueue_js
	 * @param bool $enqueue_css
	 *
	 * @since 1.1.5
	 */
	public static function enqueue_js_css( $enqueue_js = true, $enqueue_css = true ) {
		$settings = new CBXGooglemapSettings();;
		$api_key    = $settings->get_field( 'apikey', 'cbxgooglemap_general', '' );
		$map_source = absint( $settings->get_field( 'mapsource', 'cbxgooglemap_general', 1 ) );

		if ( $enqueue_js ) {
			//handle enqueue js
			if ( ( $map_source == 1 && ! empty( $api_key ) ) || $map_source == 0 ) {
				if ( $map_source == 1 ) {
					wp_enqueue_script( 'coregooglemapapi' );
					//wp_enqueue_script( 'jqcbxgooglemap' );
				} else {
					wp_enqueue_script( 'coregooglemapapi' );
				}

				wp_enqueue_script( 'cbxgooglemap-events' );
				wp_enqueue_script( 'cbxgooglemap-public' );
			}
			//end handle enqueue js
		}

		if ( $enqueue_css ) {
			//handle enqueue css
			if ( $map_source == 0 ) {
				wp_enqueue_style( 'leaflet' );
			} else {
				//
			}

			wp_enqueue_style( 'cbxgooglemap-public' );
			//end handle enqueue css
		}

		do_action( 'cbxgooglemap_enqueue_js_css', $enqueue_js, $enqueue_css );

	}//end enqueue_js_css

	/**
	 * Register Custom Post Type cbxgooglemap
	 *
	 * @since    3.7.0
	 */
	public static function create_googlemap_post_type() {
		$post_slug_default = 'cbxgooglemap';

		$labels = [
			'name'               => _x( 'Maps', 'Post Type General Name', 'cbxgooglemap' ),
			'singular_name'      => _x( 'Map', 'Post Type Singular Name', 'cbxgooglemap' ),
			'menu_name'          => esc_html__( 'CBX Maps', 'cbxgooglemap' ),
			'parent_item_colon'  => esc_html__( 'Parent Item:', 'cbxgooglemap' ),
			'all_items'          => esc_html__( 'Maps', 'cbxgooglemap' ),
			'view_item'          => esc_html__( 'View Map', 'cbxgooglemap' ),
			'add_new_item'       => esc_html__( 'Add New Map', 'cbxgooglemap' ),
			'add_new'            => esc_html__( 'Add New', 'cbxgooglemap' ),
			'edit_item'          => esc_html__( 'Edit Map', 'cbxgooglemap' ),
			'update_item'        => esc_html__( 'Update Map', 'cbxgooglemap' ),
			'search_items'       => esc_html__( 'Search Map', 'cbxgooglemap' ),
			'not_found'          => esc_html__( 'Not found', 'cbxgooglemap' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'cbxgooglemap' ),
		];

		$args = [
			'label'               => esc_html__( 'Maps', 'cbxgooglemap' ),
			'description'         => esc_html__( 'Simple map using google map and openstreet map.', 'cbxgooglemap' ),
			'labels'              => apply_filters( 'cbxgooglemap_post_type_labels', $labels ),
			'supports'            => apply_filters( 'cbxgooglemap_post_type_supports', [ 'title' ] ),
			'hierarchical'        => false,
			'public'              => apply_filters( 'cbxgooglemap_post_type_public', false ),
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'rewrite'             => [ 'slug' => apply_filters( 'cbxgooglemap_single_slug', $post_slug_default ) ],
			//'menu_icon'           => 'dashicons-list-view',
			'menu_icon'           => apply_filters( 'cbxgooglemap_menu_icon', 'dashicons-location' ),
			'can_export'          => true,
			'has_archive'         => apply_filters( 'cbxgooglemap_post_type_has_archive', false ),
			'exclude_from_search' => apply_filters( 'cbxgooglemap_post_type_exclude_from_search', true ),
			'publicly_queryable'  => apply_filters( 'cbxgooglemap_post_type_publicly_queryable', false ),
			'capability_type'     => 'post',
		];

		register_post_type( 'cbxgooglemap', apply_filters( 'cbxgooglemap_post_type_args', $args ) );
	}//end create_googlemap_post_type

	/**
	 * Setup a post object and store the original loop item so we can reset it later
	 *
	 * @param obj $post_to_setup The post that we want to use from our custom loop
	 */
	public static function setup_admin_postdata( $post_to_setup ) {
		//only on the admin side
		if ( is_admin() ) {

			//get the post for both setup_postdata() and to be cached
			global $post;

			//only cache $post the first time through the loop
			if ( ! isset( $GLOBALS['post_cache'] ) ) {
				$GLOBALS['post_cache'] = $post; //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			}

			//setup the post data as usual
			$post = $post_to_setup;
			setup_postdata( $post );
		}
	}//end setup_admin_postdata

	/**
	 * Reset $post back to the original item
	 *
	 */
	public static function wp_reset_admin_postdata() {
		//only on the admin and if post_cache is set
		if ( is_admin() && ! empty( $GLOBALS['post_cache'] ) ) {

			//globalize post as usual
			global $post;

			//set $post back to the cached version and set it up
			$post = $GLOBALS['post_cache'];
			setup_postdata( $post );

			//cleanup
			unset( $GLOBALS['post_cache'] );
		}
	}//end wp_reset_admin_postdata

	public static function supported_post_types() {
		$allowed_post_types = [ 'cbxgooglemap' ];
		return apply_filters( 'cbxgooglemap_post_types_support', $allowed_post_types );
	}//end supported_post_types

	/**
	 * Returns all post types that has public view and visually accessible
	 *
	 * @return array
	 */
	public static function post_types() {
		$post_type_args = [
			'builtin' => [
				'options' => [
					'public'   => true,
					'_builtin' => true,
					'show_ui'  => true,
				],
				'label'   => esc_html__( 'Built in post types', 'cbxgooglemap' ),
			]
		];

		$post_type_args = apply_filters( 'cbxgooglemap_post_types', $post_type_args );

		$output    = 'objects'; // names or objects, note names is the default
		$operator  = 'and';     // 'and' or 'or'
		$postTypes = [];

		foreach ( $post_type_args as $postArgType => $postArgTypeArr ) {
			$types = get_post_types( $postArgTypeArr['options'], $output, $operator );

			if ( ! empty( $types ) ) {
				foreach ( $types as $type ) {
					$postTypes[ $postArgType ]['label']               = $postArgTypeArr['label'];
					$postTypes[ $postArgType ]['data'][ $type->name ] = $type->labels->name;
				}
			}
		}

		return $postTypes;

	}//end post_types

    /**
     * Shortcode builder for display and copy paste purpose
     *
     * @param $general_settings
     *
     * @return string
     */
	public static function shortcode_builder( $general_settings = [] ) {
		$settings     = new CBXGooglemapSettings();
		$zoom_default = $settings->get_field( 'zoom', 'cbxgooglemap_general', '8' );
		if ( $zoom_default == 0 ) {
			$zoom_default = 8;
		}

		$width_default = $settings->get_field( 'width', 'cbxgooglemap_general', '100%' );
		if ( $width_default == '' || $width_default == 0 ) {
			$width_default = '100%';
		}

		$height_default = intval( $settings->get_field( 'height', 'cbxgooglemap_general', '300' ) );
		if ( $height_default == 0 ) {
			$height_default = 300;
		}

		$scrollwheel_default = intval( $settings->get_field( 'scrollwheel', 'cbxgooglemap_general', 1 ) );
		$showinfo_default    = intval( $settings->get_field( 'showinfo', 'cbxgooglemap_general', 1 ) );
		$infow_open_default  = intval( $settings->get_field( 'infow_open', 'cbxgooglemap_general', 1 ) );
		$maptype_default     = esc_attr( $settings->get_field( 'maptype', 'cbxgooglemap_general', 'roadmap' ) );
		$mapicon_default     = esc_url( $settings->get_field( 'mapicon', 'cbxgooglemap_general', '' ) );

		$attr = [
			'width'       => $width_default,
			'height'      => $height_default,
			'zoom'        => $zoom_default,
			'scrollwheel' => $scrollwheel_default,
			'showinfo'    => $showinfo_default,
			'infow_open'  => $infow_open_default,
			'maptype'     => $maptype_default,
			'heading'     => 'Codeboxr(Sample)',
			'address'     => '6H, Dilara Tower, 77 Bir Uttam C.R. Dutta Road, Dhaka 1205(Sample)',
			'website'     => 'https://codeboxr.com/',
			'mapicon'     => $mapicon_default,
			'lat'         => '23.744825100000003',
			'lng'         => '90.39219739999999'
		];

		$attr = apply_filters( 'cbxgooglemap_builder_attr', $attr );

		$attr_html = '';

		foreach ( $attr as $key => $value ) {
			$attr_html .= ' ' . $key . '="' . $value . '" ';
		}

		return '[cbxgooglemap ' . $attr_html . ']';
	}//end method shortcode_builder

	/**
	 * Map type block compatible options
	 *
	 * @return mixed|void
	 */
	public static function maptype_block_options() {
		$maptypes = [
			'roadmap'   => esc_html__( 'Road Map', 'cbxgooglemap' ),
			'satellite' => esc_html__( 'Satellite Map', 'cbxgooglemap' ),
			'hybrid'    => esc_html__( 'Hybrid Map', 'cbxgooglemap' ),
			'terrain'   => esc_html__( 'Terrain Map', 'cbxgooglemap' ),
		];

		$maptype_arr = [];

		foreach ( $maptypes as $key => $value ) {
			$maptype_arr[] = [
				'label' => $value,
				'value' => $key
			];
		}

		return apply_filters( 'cbxgooglemap_maptype_block_options', $maptype_arr );
	}//end maptype_block_options


	/**
	 * @return mixed|void
	 */
	public static function get_maptype() {
		$maptype = [
			'roadmap'   => esc_html__( 'Road Map', 'cbxgooglemap' ),
			'satellite' => esc_html__( 'Satellite Map', 'cbxgooglemap' ),
			'hybrid'    => esc_html__( 'Hybrid Map', 'cbxgooglemap' ),
			'terrain'   => esc_html__( 'Terrain Map', 'cbxgooglemap' ),
		];

		return apply_filters( 'cbxgooglemap_maptype', $maptype );
	}// end get_maptype

	/**
	 * Get available map type reverser
	 *
	 * @return mixed|void
	 */
	public static function get_maptype_r() {
		$maptype   = CBXGooglemapHelper::get_maptype();
		$maptype_r = [];

		foreach ( $maptype as $key => $value ) {
			$maptype_r[ $value ] = $key;
		}

		return apply_filters( 'cbxgooglemap_maptype_r', $maptype_r );
	}//end get_maptype_r

	/**
	 * Add utm params to any url
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function url_utmy( $url = '' ) {
		if ( $url == '' ) {
			return $url;
		}

		$url = add_query_arg( [
			'utm_source'   => 'plgsidebarinfo',
			'utm_medium'   => 'plgsidebar',
			'utm_campaign' => 'wpfreemium',
		], $url );

		return $url;
	}//end url_utmy

	/**
	 * Most needed common strings needed in js throughout the plugin
	 *
	 * @return array
	 */
	public static function global_translation_strings() {
		$global_translation = [
			'is_user_logged_in' => is_user_logged_in() ? 1 : 0,
			'ajax'              => [
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'rest_url'   => esc_url_raw( rest_url() ),
				'ajax_fail'  => esc_html__( 'Request failed, please reload the page.', 'cbxgooglemap' ),
				'ajax_nonce' => wp_create_nonce( 'cbxgooglemap_nonce' ),
				'rest_nonce' => wp_create_nonce( 'wp_rest' ),
			],
			'teeny_setting'     => [
				'teeny'         => true,
				'media_buttons' => true,
				'editor_class'  => '',
				'textarea_rows' => 5,
				'quicktags'     => false,
				'menubar'       => false,
			],
			'copycmds'          => [
				'copy'       => esc_html__( 'Copy', 'cbxgooglemap' ),
				'copied'     => esc_html__( 'Copied', 'cbxgooglemap' ),
				'copy_tip'   => esc_html__( 'Click to copy', 'cbxgooglemap' ),
				'copied_tip' => esc_html__( 'Copied to clipboard', 'cbxgooglemap' ),
			],
			'delete_dialog'     => [
				'ok'                       => esc_attr_x( 'Ok', 'cbxgooglemap-dialog', 'cbxgooglemap' ),
				'cancel'                   => esc_attr_x( 'Cancel', 'cbxgooglemap-dialog', 'cbxgooglemap' ),
				'delete'                   => esc_attr_x( 'Delete', 'cbxgooglemap-dialog', 'cbxgooglemap' ),
				'are_you_sure_global'      => esc_html__( 'Are you sure?', 'cbxgooglemap' ),
				'are_you_sure_delete_desc' => esc_html__( 'Once you delete, it\'s gone forever. You can not revert it back.', 'cbxgooglemap' ),
			],
			'pickr_i18n'        => [
				// Strings visible in the UI
				'ui:dialog'       => esc_html__( 'color picker dialog', 'cbxgooglemap' ),
				'btn:toggle'      => esc_html__( 'toggle color picker dialog', 'cbxgooglemap' ),
				'btn:swatch'      => esc_html__( 'color swatch', 'cbxgooglemap' ),
				'btn:last-color'  => esc_html__( 'use previous color', 'cbxgooglemap' ),
				'btn:save'        => esc_html__( 'Save', 'cbxgooglemap' ),
				'btn:cancel'      => esc_html__( 'Cancel', 'cbxgooglemap' ),
				'btn:clear'       => esc_html__( 'Clear', 'cbxgooglemap' ),

				// Strings used for aria-labels
				'aria:btn:save'   => esc_html__( 'save and close', 'cbxgooglemap' ),
				'aria:btn:cancel' => esc_html__( 'cancel and close', 'cbxgooglemap' ),
				'aria:btn:clear'  => esc_html__( 'clear and close', 'cbxgooglemap' ),
				'aria:input'      => esc_html__( 'color input field', 'cbxgooglemap' ),
				'aria:palette'    => esc_html__( 'color selection area', 'cbxgooglemap' ),
				'aria:hue'        => esc_html__( 'hue selection slider', 'cbxgooglemap' ),
				'aria:opacity'    => esc_html__( 'selection slider', 'cbxgooglemap' ),
			],
			'awn_options'       => [
				'tip'           => esc_html__( 'Tip', 'cbxgooglemap' ),
				'info'          => esc_html__( 'Info', 'cbxgooglemap' ),
				'success'       => esc_html__( 'Success', 'cbxgooglemap' ),
				'warning'       => esc_html__( 'Attention', 'cbxgooglemap' ),
				'alert'         => esc_html__( 'Error', 'cbxgooglemap' ),
				'async'         => esc_html__( 'Loading', 'cbxgooglemap' ),
				'confirm'       => esc_html__( 'Confirmation', 'cbxgooglemap' ),
				'confirmOk'     => esc_html__( 'OK', 'cbxgooglemap' ),
				'confirmCancel' => esc_html__( 'Cancel', 'cbxgooglemap' )
			],
			'validation'        => [
				'required'    => esc_html__( 'This field is required.', 'cbxgooglemap' ),
				'remote'      => esc_html__( 'Please fix this field.', 'cbxgooglemap' ),
				'email'       => esc_html__( 'Please enter a valid email address.', 'cbxgooglemap' ),
				'url'         => esc_html__( 'Please enter a valid URL.', 'cbxgooglemap' ),
				'date'        => esc_html__( 'Please enter a valid date.', 'cbxgooglemap' ),
				'dateISO'     => esc_html__( 'Please enter a valid date ( ISO ).', 'cbxgooglemap' ),
				'number'      => esc_html__( 'Please enter a valid number.', 'cbxgooglemap' ),
				'digits'      => esc_html__( 'Please enter only digits.', 'cbxgooglemap' ),
				'equalTo'     => esc_html__( 'Please enter the same value again.', 'cbxgooglemap' ),
				'maxlength'   => esc_html__( 'Please enter no more than {0} characters.', 'cbxgooglemap' ),
				'minlength'   => esc_html__( 'Please enter at least {0} characters.', 'cbxgooglemap' ),
				'rangelength' => esc_html__( 'Please enter a value between {0} and {1} characters long.', 'cbxgooglemap' ),
				'range'       => esc_html__( 'Please enter a value between {0} and {1}.', 'cbxgooglemap' ),
				'max'         => esc_html__( 'Please enter a value less than or equal to {0}.', 'cbxgooglemap' ),
				'min'         => esc_html__( 'Please enter a value greater than or equal to {0}.', 'cbxgooglemap' ),
				'recaptcha'   => esc_html__( 'Please check the captcha.', 'cbxgooglemap' ),
			],
			'placeholder'       => [
				'select' => esc_html__( 'Please Select', 'cbxgooglemap' ),
				'search' => esc_html__( 'Search...', 'cbxgooglemap' ),
			],
			'upload'            => [
				'upload_btn'   => esc_html__( 'Upload', 'cbxgooglemap' ),
				'upload_title' => esc_html__( 'Select Media', 'cbxgooglemap' ),
			],
			'lang'              => get_user_locale(),
			'file_preview'      => [
				'browse'        => esc_attr__( 'Choose', 'cbxgooglemap' ),
				'chooseFile'    => esc_attr__( 'Take your pick...', 'cbxgooglemap' ),
				'label'         => esc_attr__( 'Choose Files to Upload', 'cbxgooglemap' ),
				'selectedCount' => esc_attr__( 'files selected', 'cbxgooglemap' )
			]
		];

		return apply_filters( 'cbxgooglemap_global_translation', $global_translation );
	}//end method global_translation_strings

	/**
	 * Returns codeboxr news feeds using transient cache
	 *
	 * @return false|mixed|\SimplePie\Item[]|null
	 */
	public static function codeboxr_news_feed() {
		$cache_key   = 'codeboxr_news_feed_cache';
		$cached_feed = get_transient( $cache_key );

		$news = false;

		if ( false === $cached_feed ) {
			include_once ABSPATH . WPINC . '/feed.php'; // Ensure feed functions are available
			$feed = fetch_feed( 'https://codeboxr.com/feed?post_type=post' );

			if ( is_wp_error( $feed ) ) {
				return false; // Return false if there's an error
			}

			$feed->init();

			$feed->set_output_encoding( 'UTF-8' );
			// this is the encoding parameter, and can be left unchanged in almost every case
			$feed->handle_content_type();
			// this double-checks the encoding type
			$feed->set_cache_duration( 21600 );
			// 21,600 seconds is six hours
			$limit  = $feed->get_item_quantity( 10 );
			// fetches the 18 most recent RSS feed stories
			$items  = $feed->get_items( 0, $limit );
			$blocks = array_slice( $items, 0, 10 );

			$news = [];
			foreach ( $blocks as $block ) {
				$url   = $block->get_permalink();
				$url   = CBXGooglemapHelper::url_utmy( esc_url( $url ) );
				$title = $block->get_title();

				$news[] = [ 'url' => $url, 'title' => $title ];
			}

			set_transient( $cache_key, $news, HOUR_IN_SECONDS * 6 ); // Cache for 6 hours
		} else {
			$news = $cached_feed;
		}

		return $news;
	}//end method codeboxr_news_feed

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		CBXGooglemapHelper::create_googlemap_post_type();

		add_option( 'cbxgooglemap_flush_rewrite_rules', 'true' );
	}//end activate

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( 'cbxgooglemap_flush_rewrite_rules' );
	}//end deactivate

	/**
	 * Load reset option table html
	 *
	 * @return string
	 */
	public static function setting_reset_html_table() {
		$option_values = self::getAllOptionNames();

		$table_html = '<p style="margin-bottom: 10px;" class="grouped gapless grouped_buttons" id="cbxgooglemap_setting_options_check_actions"><a href="#" class="button primary cbxgooglemap_setting_options_check_action_call">' . esc_html__( 'Check All',
				'cbxgooglemap' ) . '</a><a href="#" class="button outline cbxgooglemap_setting_options_check_action_ucall">' . esc_html__( 'Uncheck All',
				'cbxgooglemap' ) . '</a></p>';
		$table_html .= '<table class="widefat widethin cbxgooglemap_table_data" id="cbxgooglemap_setting_options_table">
                        <thead>
                        <tr>
                            <th class="row-title">' . esc_attr__( 'Option Name', 'cbxgooglemap' ) . '</th>
                            <th>' . esc_attr__( 'Option ID', 'cbxgooglemap' ) . '</th>		
                        </tr>
                    </thead>';

		$table_html .= '<tbody>';

		$i = 0;
		foreach ( $option_values as $key => $value ) {
			$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
			$i ++;
			$table_html .= '<tr class="' . esc_attr( $alternate_class ) . '">
                                <td class="row-title"><input checked class="magic-checkbox reset_options" type="checkbox" name="reset_options[' . $value['option_name'] . ']" id="reset_options_' . esc_attr( $value['option_name'] ) . '" value="' . $value['option_name'] . '" />
                                    <label for="reset_options_' . esc_attr( $value['option_name'] ) . '">' . esc_attr( $value['option_name'] ) . '</td>
                                <td>' . esc_attr( $value['option_id'] ) . '</td>									
                            </tr>';
		}

		$table_html .= '</tbody>';
		$table_html .= '<tfoot>
                <tr>
                    <th class="row-title">' . esc_attr__( 'Option Name', 'cbxgooglemap' ) . '</th>
                    <th>' . esc_attr__( 'Option ID', 'cbxgooglemap' ) . '</th>				
                </tr>
                </tfoot>
            </table>';

		return $table_html;
	} //end method setting_reset_html_table

	/**
	 * List all global option name with prefix cbxgooglemap_
	 * @since 1.0.0
	 */
	public static function getAllOptionNames() {
		global $wpdb;

		$prefix = 'cbxgooglemap_';

		$wild = '%';
		$like = $wpdb->esc_like( $prefix ) . $wild;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$option_names = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s", $like ), ARRAY_A );

		return apply_filters( 'cbxgooglemap_option_names', $option_names );
	}//end method getAllOptionNames

	/**
	 * Get any plugin version number
	 *
	 * @param $plugin_slug
	 *
	 * @return mixed|string
	 * @since 2.0.0
	 */
	public static function get_any_plugin_version( $plugin_slug = '' ) {
		if ( $plugin_slug == '' ) {
			return '';
		}

		// Ensure the required file is loaded
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get all installed plugins
		$all_plugins = get_plugins();

		// Check if the plugin exists
		if ( isset( $all_plugins[ $plugin_slug ] ) ) {
			return $all_plugins[ $plugin_slug ]['Version'];
		}

		// Return false if the plugin is not found
		return '';
	}//end method get_pro_addon_version
}//end class CBXGooglemapHelper