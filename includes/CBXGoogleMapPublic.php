<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    Cbxgooglemap
 * @subpackage Cbxgooglemap/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cbxgooglemap
 * @subpackage Cbxgooglemap/public
 * @author     Codeboxr <info@codeboxr.com>
 */

namespace Cbx\Googlemap;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Cbx\Googlemap\Helpers\CBXGooglemapHelper;
use Cbx\Googlemap\Widgets\Classic\CBXGoogleMapWidget;
use Cbx\Googlemap\Widgets\Elementor\CBXGooglemapElemWidget;

class CBXGoogleMapPublic {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 */

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.12
	 * @access   private
	 * @var      string $settings The ID of this plugin.
	 */
	private $settings;

	public function __construct() {
		$this->plugin_name = CBXGOOGLEMAP_PLUGIN_NAME;
		$this->version     = CBXGOOGLEMAP_PLUGIN_VERSION;

		$this->settings = new CBXGooglemapSettings();
	}//end constructor


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$page    = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$version = $this->version;

		$vendor_url = CBXGOOGLEMAP_ROOT_URL . 'assets/vendors/';
		$css_url    = CBXGOOGLEMAP_ROOT_URL . 'assets/css/';
		$js_url     = CBXGOOGLEMAP_ROOT_URL . 'assets/css/';

		$map_source = intval( $this->settings->get_option( 'mapsource', 'cbxgooglemap_general', 1 ) );

		if ( $map_source == 0 ) {
			wp_register_style( 'leaflet', $vendor_url . 'leaflet/leaflet.css', [], $version, 'all' );
			wp_register_style( 'cbxgooglemap-public', $css_url . 'cbxgooglemap-public.css', [ 'leaflet' ], $version, 'all' );
		} else {
			wp_register_style( 'cbxgooglemap-public', $css_url . 'cbxgooglemap-public.css', [], $version, 'all' );
		}

		//wp_enqueue_style( 'cbxgooglemap-public' );

		$elementor_preview = isset( $_REQUEST['elementor-preview'] ) ? intval( $_REQUEST['elementor-preview'] ) : 0;// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $elementor_preview > 0 || ( is_admin() && CBXGooglemapHelper::is_gutenberg_page() ) ) {
			CBXGooglemapHelper::enqueue_js_css( false, true );
		}
	}//end enqueue_styles

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$page    = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';// phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$version = $this->version;
		$in_footer    = [
			'in_footer' => true,
		];

        $in_footer_async = [
            'in_footer' => true,
            'strategy' => 'async'
        ];


		$vendor_url = CBXGOOGLEMAP_ROOT_URL . 'assets/vendors/';
		$css_url    = CBXGOOGLEMAP_ROOT_URL . 'assets/css/';
		$js_url     = CBXGOOGLEMAP_ROOT_URL . 'assets/js/';

		$api_key         = esc_attr( $this->settings->get_option( 'apikey', 'cbxgooglemap_general', '' ) );
		$map_source      = intval( $this->settings->get_option( 'mapsource', 'cbxgooglemap_general', 1 ) );
		$default_mapicon = esc_url( $this->settings->get_option( 'mapicon', 'cbxgooglemap_general', '' ) );

		wp_register_script( 'cbxgooglemap-events', $js_url . 'cbxgooglemap-events.js', [], $version, $in_footer );

		if ( ( $map_source == 1 && ! empty( $api_key ) ) || $map_source == 0 ) {
			wp_enqueue_script( 'jquery' );

			if ( $map_source == 1 ) {
				wp_register_script( 'coregooglemapapi', '//maps.googleapis.com/maps/api/js?key=' . esc_attr( $api_key ) . '&libraries=places&callback=Function.prototype', [], $version, $in_footer_async );
				wp_register_script( 'cbxgooglemap-public', $js_url . 'cbxgooglemap-public.js', [
					'jquery',
					'cbxgooglemap-events'
				], $version, $in_footer );

				wp_enqueue_script( 'coregooglemapapi' );
			} else {
				wp_register_script( 'coregooglemapapi', $vendor_url . 'leaflet/leaflet.js', [], $version, $in_footer_async );
				wp_register_script( 'cbxgooglemap-public', $js_url . 'cbxgooglemap-public.js', [
					'jquery',
					'coregooglemapapi',
					'cbxgooglemap-events'
				], $version, $in_footer );
			}

			$cbxgooglemap_public_js_vars = [
				'icon_url_default' => $default_mapicon,
				'api_key'          => $api_key,
				'map_source'       => $map_source,
				'extra_markers'    => []
			];

			wp_localize_script( 'cbxgooglemap-public', 'cbxgooglemap_public', apply_filters( 'cbxgooglemap_public_js_vars', $cbxgooglemap_public_js_vars ) );


			$elementor_preview = isset( $_REQUEST['elementor-preview'] ) ? intval( $_REQUEST['elementor-preview'] ) : 0;// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $elementor_preview > 0 || ( is_admin() && CBXGooglemapHelper::is_gutenberg_page() ) ) {
				CBXGooglemapHelper::enqueue_js_css( true, false );
			}
		}
	}//end enqueue_scripts

	/**
	 * Init all shortcodes
	 */
	public function init_shortcodes() {
		add_shortcode( 'cbxgooglemap', [ $this, 'cbxgooglemap_shortcode' ] );
	}//end init_shortcodes

	/**
	 * Shortcode callback for shortcode '[cbxgooglemap]'
	 */
	public function cbxgooglemap_shortcode( $atts ) {

		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		$settings = $this->settings;

		CBXGooglemapHelper::enqueue_js_css();

		$general_settings = get_option( 'cbxgooglemap_general', [] );

		$hide_leaflet = intval( $settings->get_field( 'hide_leaflet', 'cbxgooglemap_general', 0 ) );
		$api_key      = esc_attr( $settings->get_field( 'apikey', 'cbxgooglemap_general', '' ) );
		$map_source   = intval( $settings->get_field( 'mapsource', 'cbxgooglemap_general', 1 ) );

		//if google map and api key is not present
		if ( $map_source == 1 && $api_key == '' ) {
			return '<p style="text-align: center;">' . esc_html__( 'Google Map Api Key is invalid or empty. Please set in plugin setting.', 'cbxgooglemap' ) . '</p>';
		}

		$zoom_default = intval( $settings->get_field( 'zoom', 'cbxgooglemap_general', '8' ) );
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

		$maptype_default = $settings->get_field( 'maptype', 'cbxgooglemap_general', 'roadmap' );
		$mapicon_default = $settings->get_field( 'mapicon', 'cbxgooglemap_general', '' );

		if ( $mapicon_default != '' ) {
			$mapicon_default = esc_url( $mapicon_default );
		}


		$atts = shortcode_atts(
			[
				'id'          => '',
				'maptype'     => esc_attr( $maptype_default ),
				'lat'         => '',
				'lng'         => '',
				'width'       => $width_default,
				'height'      => $height_default,
				'zoom'        => $zoom_default,
				'scrollwheel' => $scrollwheel_default,
				'showinfo'    => $showinfo_default,
				'infow_open'  => $infow_open_default, //added in v1.1.2
				'heading'     => '',
				'address'     => '',
				'website'     => '',
				'mapicon'     => $mapicon_default, //added in v1.1.2
			],
			$atts, 'cbxgooglemap' );

		$id = isset( $atts['id'] ) ? intval( $atts['id'] ) : 0;

		$data_custom_attrs = [
			'data-post-id' => $id
		];

		$output_html  = '';

		if ( $id > 0 ) {
			//show maps from post data
			$combined_field = '_cbxgooglemap_combined'; //field name for non sortable fields
			$meta_prefix    = '_cbxgooglemap';          //field prefix for sortable fields

			$lat = get_post_meta( $id, $meta_prefix . 'lat', true );


			$lat = ( $lat !== false ) ? $lat : '';

			$lng = get_post_meta( $id, $meta_prefix . 'lng', true );
			$lng = ( $lng !== false ) ? $lng : '';

			if ( $lat == '' || $lng == '' ) {
				return esc_html__( 'Please set Latitude and Longitude both to display a map.', 'cbxgooglemap' );
			} //at least we need lat lng


			$meta_combined = get_post_meta( $id, $combined_field, true );

			$heading = isset( $meta_combined['_cbxgooglemaptitle'] ) ? esc_html( $meta_combined['_cbxgooglemaptitle'] ) : '';

			$zoom = ( isset( $meta_combined[ $meta_prefix . 'zoom' ] ) && intval( $meta_combined[ $meta_prefix . 'zoom' ] ) > 0 ) ? intval( $meta_combined[ $meta_prefix . 'zoom' ] ) : $zoom_default;

			$scrollwheel = ( isset( $meta_combined[ $meta_prefix . 'scrollwheel' ] ) ) ? intval( $meta_combined[ $meta_prefix . 'scrollwheel' ] ) : intval( $scrollwheel_default );
			$showinfo    = ( isset( $meta_combined[ $meta_prefix . 'showinfo' ] ) ) ? intval( $meta_combined[ $meta_prefix . 'showinfo' ] ) : intval( $showinfo_default );
			$infow_open  = ( isset( $meta_combined[ $meta_prefix . 'infow_open' ] ) ) ? intval( $meta_combined[ $meta_prefix . 'infow_open' ] ) : intval( $infow_open_default );

			$width  = ( isset( $meta_combined[ $meta_prefix . 'width' ] ) && $meta_combined[ $meta_prefix . 'width' ] != '' ) ? $meta_combined[ $meta_prefix . 'width' ] : $width_default;
			$height = ( isset( $meta_combined[ $meta_prefix . 'height' ] ) && intval( $meta_combined[ $meta_prefix . 'height' ] ) > 0 ) ? intval( $meta_combined[ $meta_prefix . 'height' ] ) : $height_default;

			$address = ( isset( $meta_combined[ $meta_prefix . 'address' ] ) && esc_attr( $meta_combined[ $meta_prefix . 'address' ] ) != '' ) ? esc_attr( $meta_combined[ $meta_prefix . 'address' ] ) : '';
			$website = ( isset( $meta_combined[ $meta_prefix . 'website' ] ) && esc_url( $meta_combined[ $meta_prefix . 'website' ] ) != '' ) ? esc_url( $meta_combined[ $meta_prefix . 'website' ] ) : '';

			$mapicon = ( isset( $meta_combined[ $meta_prefix . 'mapicon' ] ) && esc_url( $meta_combined[ $meta_prefix . 'mapicon' ] ) != '' ) ? esc_url( $meta_combined[ $meta_prefix . 'mapicon' ] ) : $mapicon_default;
			$maptype = ( isset( $meta_combined[ $meta_prefix . 'maptype' ] ) && esc_attr( $meta_combined[ $meta_prefix . 'maptype' ] ) != '' ) ? esc_attr( $meta_combined[ $meta_prefix . 'maptype' ] ) : '';

		}
        else {
			//show map from shortcode params only
			$lat = isset( $atts['lat'] ) ? floatval( $atts['lat'] ) : '';
			$lng = isset( $atts['lat'] ) ? floatval( $atts['lng'] ) : '';

            if($lat === 0) $lat = '';
            if($lng === 0) $lng = '';

			if ( $lat == '' || $lng == '' ) {
				return esc_html__( 'Please set Latitude and Longitude both to display a map.', 'cbxgooglemap' );
			} //at least we need lat lng


			$width  = isset( $atts['width'] ) ? $atts['width'] : '';
			$height = isset( $atts['height'] ) ? intval( $atts['height'] ) : '';

			$zoom    = isset( $atts['zoom'] ) ? $atts['zoom'] : $zoom_default;
			$heading = isset( $atts['heading'] ) ? sanitize_text_field( wp_unslash( $atts['heading'] ) ) : '';
			$address = isset( $atts['address'] ) ? sanitize_text_field( wp_unslash( $atts['address'] ) ) : '';
			$website = isset( $atts['website'] ) ? sanitize_text_field( $atts['website'] ) : '';
			$mapicon = isset( $atts['mapicon'] ) ? sanitize_text_field( $atts['mapicon'] ) : '';
			$maptype = isset( $atts['maptype'] ) ? sanitize_text_field( $atts['maptype'] ) : '';

			$showinfo    = isset( $atts['showinfo'] ) ? absint( $atts['showinfo'] ) : $showinfo_default;
			$infow_open  = isset( $atts['infow_open'] ) ? absint( $atts['infow_open'] ) : $infow_open_default;
			$scrollwheel = isset( $atts['scrollwheel'] ) ? absint( $atts['scrollwheel'] ) : $scrollwheel_default;


		}

		if ( $heading == '' && $website == '' ) {
			$showinfo = 0;
		}

		if ( is_numeric( $width ) ) {
			$width = $width . 'px';
		}


		$extra_class = ( $hide_leaflet ) ? 'cbxgooglemap_wrapper_hideleaflet' : '';



		$data_custom_attrs = apply_filters( 'cbxgooglemap_data_custom_attrs', $data_custom_attrs, $id, $atts );

		$data_custom_attrs_html = ' ';
		foreach ( $data_custom_attrs as $custom_attr_key => $custom_attr_value ) {
			$data_custom_attrs_html .= ' ' . $custom_attr_key . '="' . esc_attr( $custom_attr_value ) . '" ';
		}

		$output_html .= '<div class="cbxgooglemap_wrapper ' . esc_attr( $extra_class ) . '">';
		$output_html .= '<div class="cbxgooglemap_embed" ' . $data_custom_attrs_html . ' data-render="0" data-mapsource="' . absint( $map_source ) . '" style="width: ' . esc_attr($width) . '; height: ' . esc_attr($height) . 'px;" data-scrollwheel="' . absint( $scrollwheel ) . '"  data-showinfo="' . absint( $showinfo ) . '" data-infow_open="' . absint( $infow_open ) . '" data-heading="' . esc_attr( $heading ) . '" data-address="' . esc_attr( $address ) . '" data-website="' . esc_url( $website ) . '"  data-mapicon="' . esc_url( $mapicon ) . '" data-maptype="' . esc_attr( $maptype ) . '" data-lat="' . esc_attr( $lat ) . '" data-lng="' . esc_attr( $lng ) . '" data-zoom="' . esc_attr( $zoom ) . '" ></div>';
		$output_html .= '</div>';

		return apply_filters('cbxgooglemap_shortcode_html', $output_html, $id, $atts);
	}//end cbxgooglemap_shortcode

	/**
	 * Register classic widget
	 */
	public function register_widget() {
		register_widget( CBXGoogleMapWidget::class );
	}//end register_widget

	/**
	 * Register elementor widget
	 *
	 * @return void
	 */
	public function init_elementor_widgets() {
		\Elementor\Plugin::instance()->widgets_manager->register( new CBXGooglemapElemWidget() );
	}//end widgets_registered

	/**
	 * Add new category to elementor
	 *
	 * @param $elements_manager
	 */
	public function add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'codeboxr',
			[
				'title' => esc_html__( 'Codeboxr Widgets', 'cbxgooglemap' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}//end add_elementor_widget_categories

	/**
	 * Load Elementor Custom Icon
	 */
	function elementor_icon_loader() {
		wp_register_style( 'cbxgooglemap-elementor-icon', CBXGOOGLEMAP_ROOT_URL . 'assets/css/cbxgooglemap-elementor.css', false, CBXGOOGLEMAP_PLUGIN_VERSION );
		wp_enqueue_style( 'cbxgooglemap-elementor-icon' );
	}//end elementor_icon_loader
}//end class CBXGoogleMapPublic