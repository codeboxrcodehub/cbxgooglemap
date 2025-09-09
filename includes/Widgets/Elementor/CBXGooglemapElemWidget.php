<?php
namespace CBX\GoogleMap\Widgets\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


use Cbx\Googlemap\CBXGooglemapSettings;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * Google Maps Widget
 */
class CBXGooglemapElemWidget extends \Elementor\Widget_Base {

	/**
	 * Retrieve google maps widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'cbxgooglemap_google_map';
	}

	/**
	 * Retrieve google maps widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return esc_html__( 'CBX Map: Location Map', 'cbxgooglemap' );
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @return array Widget categories.
	 * @since  1.0.10
	 * @access public
	 *
	 */
	public function get_categories() {
		return [ 'codeboxr' ];
	}

	/**
	 * Retrieve google maps widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'cbxmap-icon';
	}

	/**
	 * Register google maps widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$settings         = new CBXGooglemapSettings();
		//$general_settings = get_option( 'cbxgooglemap_general', [] );

		//default field values
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
		$maptype_default     = $settings->get_field( 'maptype', 'cbxgooglemap_general', 'roadmap' );


		$this->start_controls_section(
			'section_cbxmap',
			[
				'label' => esc_html__( 'CBX Map', 'cbxgooglemap' ),
			]
		);

		$query = get_posts( [
			'post_type'      => 'cbxgooglemap',
			'orderby'        => 'date',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		] );

		$googleMap_posts = [];

		$googleMap_posts[''] = esc_html__( 'Choose Ready Map or Use custom params', 'cbxgooglemap' );

		foreach ( $query as $key => $data ) {
			$googleMap_posts[ $data->ID ] = $data->post_title;
		}


		$this->add_control(
			'cbxgooglemap_post_id',
			[
				'label'       => esc_html__( 'Predefined Map', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'placeholder' => esc_html__( 'Choose Ready Map or Use custom params', 'cbxgooglemap' ),
				'default'     => '',
				'label_block' => true,
				'options'     => $googleMap_posts,
				'description' => esc_html__( 'Choose predefined map or create from custom attributes below', 'cbxgooglemap' ),
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,

			]
		);

		//regular fields
		$this->add_control(
			'cbxgooglemap_custom_1',
			[
				'label'   => esc_html__( 'Custom Map Attributes', 'cbxgooglemap' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'default' => '',
			]
		);

		$this->add_control(
			'cbxgooglemap_maptype',
			[
				'label'       => esc_html__( 'Map Type', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					'roadmap'   => esc_html__( 'Road Map', 'cbxgooglemap' ),
					'satellite' => esc_html__( 'Satellite Map', 'cbxgooglemap' ),
					'hybrid'    => esc_html__( 'Hybrid Map', 'cbxgooglemap' ),
					'terrain'   => esc_html__( 'Terrain Map', 'cbxgooglemap' ),
				],
				'default'     => $maptype_default,
				'description' => esc_html__( 'Google Map only', 'cbxgooglemap' ),
			]
		);

		$this->add_control(
			'cbxgooglemap_lat',
			[
				'label'       => esc_html__( 'Latitude', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Latitude', 'cbxgooglemap' ),
				'default'     => '',
			]
		);

		$this->add_control(
			'cbxgooglemap_lng',
			[
				'label'       => esc_html__( 'Longitude', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Longitude', 'cbxgooglemap' ),
				'default'     => '',
			]
		);

		$this->add_control(
			'cbxgooglemap_width',
			[
				'label'       => esc_html__( 'Width(Numeric or with %)', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Width', 'cbxgooglemap' ),
				'default'     => $width_default,
			]
		);

		$this->add_control(
			'cbxgooglemap_height',
			[
				'label'       => esc_html__( 'Height', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Height', 'cbxgooglemap' ),
				'default'     => $height_default,
			]
		);

		$this->add_control(
			'cbxgooglemap_zoom',
			[
				'label'       => esc_html__( 'Zoom(Numeric Value)', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Zoom', 'cbxgooglemap' ),
				'default'     => $zoom_default,
			]
		);

		$this->add_control(
			'cbxgooglemap_scrollwheel',
			[
				'label'   => esc_html__( 'Mouse Scroll Wheel', 'cbxgooglemap' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'1' => esc_html__( 'Enable', 'cbxgooglemap' ),
					'0' => esc_html__( 'Disable', 'cbxgooglemap' ),
				],
				'default' => $scrollwheel_default,
			]
		);

		$this->add_control(
			'cbxgooglemap_showinfo',
			[
				'label'   => esc_html__( 'Show Popup', 'cbxgooglemap' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'1' => esc_html__( 'Enable', 'cbxgooglemap' ),
					'0' => esc_html__( 'Disable', 'cbxgooglemap' ),
				],
				'default' => $showinfo_default,
			]
		);

		$this->add_control(
			'cbxgooglemap_infow_open',
			[
				'label'   => esc_html__( 'Info/Popup Window', 'cbxgooglemap' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'1' => esc_html__( 'Open(Default)', 'cbxgooglemap' ),
					'0' => esc_html__( 'On Click', 'cbxgooglemap' ),
				],
				'default' => $infow_open_default,
			]
		);

		$this->add_control(
			'cbxgooglemap_heading',
			[
				'label'       => esc_html__( 'Popup Heading', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Popup Heading', 'cbxgooglemap' ),
				'default'     => '',
			]
		);

		$this->add_control(
			'cbxgooglemap_address',
			[
				'label'       => esc_html__( 'Location Address', 'cbxgooglemap' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Address', 'cbxgooglemap' ),
				'default'     => '',
			]
		);

		$this->add_control(
			'cbxgooglemap_website',
			[
				'label'         => esc_html__( 'Website url', 'cbxgooglemap' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'Website', 'cbxgooglemap' ),
				'default'       => [
					'url' => '',
				],
				'show_external' => false,
			]
		);

		$this->add_control(
			'cbxgooglemap_mapicon',
			[
				'label'   => esc_html__( 'Map Icon', 'cbxgooglemap' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			]
		);

		$this->end_controls_section();
	}//end _register_controls

	/**
	 * Render google maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings         = new CBXGooglemapSettings();
		$general_settings = get_option( 'cbxgooglemap_general', [] );

		$api_key    = $settings->get_field( 'apikey', 'cbxgooglemap_general', '' );
		$map_source = intval( $settings->get_field( 'mapsource', 'cbxgooglemap_general', 1 ) );

		if ( $map_source == 1 && $api_key == '' ) {
			echo '<p style="text-align: center;">' . esc_html__( 'Google Map Api Key is invalid!',
					'cbxgooglemap' ) . '</p>';
		} else {
			$settings = $this->get_settings();

			$id = intval( $settings['cbxgooglemap_post_id'] );

			if ( $id > 0 ) {
				//render map from saved map
				echo do_shortcode( '[cbxgooglemap id="' . absint($id) . '"]' );
			} else {

				//render map from custom attributes
				$lat         = sanitize_text_field( $settings['cbxgooglemap_lat'] );
				$lng         = sanitize_text_field( $settings['cbxgooglemap_lng'] );
				$width       = sanitize_text_field( $settings['cbxgooglemap_width'] );
				$height      = sanitize_text_field( $settings['cbxgooglemap_height'] );
				$zoom        = $settings['cbxgooglemap_zoom'];
				$scrollwheel = intval( $settings['cbxgooglemap_scrollwheel'] );
				$showinfo    = intval( $settings['cbxgooglemap_showinfo'] );
				$infow_open  = intval( $settings['cbxgooglemap_infow_open'] );
				$heading     = sanitize_text_field( $settings['cbxgooglemap_heading'] );
				$address     = sanitize_text_field( $settings['cbxgooglemap_address'] );
				$maptype     = sanitize_text_field( $settings['cbxgooglemap_maptype'] );

				$website_data = $settings['cbxgooglemap_website'];
				$mapicon_data = $settings['cbxgooglemap_mapicon'];
				$website      = isset( $website_data['url'] ) ? $website_data['url'] : '';
				$mapicon      = isset( $mapicon_data['url'] ) ? $mapicon_data['url'] : '';


				echo do_shortcode( '[cbxgooglemap lat="' . esc_attr($lat) . '" lng="' . esc_attr($lng) . '" zoom="' . esc_attr($zoom) . '" scrollwheel="' . esc_attr($scrollwheel) . '" showinfo="' . esc_attr($showinfo) . '" infow_open="' . esc_attr($infow_open) . '" heading="' . esc_attr($heading) . '" maptype="' . esc_attr($maptype) . '" website="' . esc_attr($website) . '" address="' . esc_attr($address) . '" mapicon="' . esc_attr($mapicon) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"]' );
			}
		}
	}//end render

	/**
	 * Render google maps widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function _content_template() {
	}//end _content_template
}//end CBXGooglemapElemWidget
