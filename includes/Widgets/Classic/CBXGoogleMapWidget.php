<?php
namespace Cbx\Googlemap\Widgets\Classic;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Prevent direct file access
use Cbx\Googlemap\CBXGooglemapSettings;
use WP_Widget;

class CBXGoogleMapWidget extends WP_Widget {
	/**
	 * Unique identifier for your widget.
	 *
	 *
	 * @since    1.1.7
	 *
	 * @var      string
	 */
	protected $widget_slug = 'cbxgooglemap-widget'; //main parent plugin's language file
	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {
		parent::__construct(
			$this->get_widget_slug(),
			esc_html__( 'CBX Map: Location Map', 'cbxgooglemap' ),
			[
				'classname'   => 'widget-cbxgooglemap',
				'description' => esc_html__( 'CBX Map Widget', 'cbxgooglemap' )
			]
		);

	}//end constructor

	/**
	 * Return the widget slug.
	 *
	 * @return    Plugin slug variable.
	 * @since    1.0.0
	 *
	 */
	public function get_widget_slug() {
		return $this->widget_slug;
	}//end method get_widget_slug

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;

		$title = apply_filters( 'widget_title',
			empty( $instance['title'] ) ? esc_html__( 'CBX Map: Location Map','cbxgooglemap' ) : $instance['title'], $instance, $this->id_base );
		// Defining the Widget Title
		if ( $title ) {
			$widget_string .= $args['before_title'] . $title . $args['after_title'];
		} else {
			$widget_string .= $args['before_title'] . $args['after_title'];
		}

		ob_start();
		$settings         = new CBXGooglemapSettings();
		$general_settings = get_option( 'cbxgooglemap_general', [] );

		$api_key    = $settings->get_field( 'apikey', 'cbxgooglemap_general', '' );
		$map_source = intval( $settings->get_field( 'mapsource', 'cbxgooglemap_general', 1 ) );

		if ( $map_source == 1 && $api_key == '' ) {
			echo '<p style="text-align: center;">' . esc_html__( 'Google Map Api Key is invalid!', 'cbxgooglemap' ) . '</p>';
		} else {

			$id = intval( $instance['map_id'] );

			if ( $id > 0 ) {
				//render map from saved map
				echo do_shortcode( '[cbxgooglemap id="' . $id . '"]' );
			} else {
				//render map from custom attributes
				$maptype     = sanitize_text_field( wp_unslash($instance['maptype']) );
				$lat         = sanitize_text_field( wp_unslash($instance['lat']) );
				$lng         = sanitize_text_field( wp_unslash($instance['lng']) );
				$width       = sanitize_text_field( wp_unslash($instance['width']) );
				$height      = sanitize_text_field( wp_unslash($instance['height']) );
				$zoom        = sanitize_text_field( wp_unslash($instance['zoom']) );
				$scrollwheel = absint( $instance['scrollwheel'] );
				$showinfo    = absint( $instance['showinfo'] );
				$infow_open  = absint( $instance['infow_open'] );
				$heading     = sanitize_text_field( wp_unslash($instance['heading']) );
				$address     = sanitize_text_field( wp_unslash($instance['address']) );
				$website     = sanitize_text_field( wp_unslash($instance['website']) );

				echo do_shortcode( '[cbxgooglemap lat="' . esc_attr($lat) . '" lng="' . esc_attr($lng) . '" zoom="' . esc_attr($zoom) . '" scrollwheel="' . esc_attr($scrollwheel) . '" showinfo="' . esc_attr($showinfo) . '" infow_open="' . esc_attr($infow_open) . '" heading="' . esc_attr($heading) . '" maptype="' . esc_attr($maptype) . '" website="' . esc_attr($website) . '" address="' . esc_attr($address) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"]' );
			}
		}

		$content = ob_get_contents();
		ob_end_clean();

		$widget_string .= $content;
		$widget_string .= $after_widget;
		echo $widget_string;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}//end of method widget


	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance['title']       = isset( $new_instance['title'] ) ? sanitize_text_field( wp_unslash($new_instance['title']) ) : '';
		$instance['map_id']      = isset( $new_instance['map_id'] ) ? absint( $new_instance['map_id'] ) : 0;
		$instance['maptype']     = isset( $new_instance['maptype'] ) ? sanitize_text_field( wp_unslash($new_instance['maptype']) ) : 'roadmap';
		$instance['lat']         = isset( $new_instance['lat'] ) ? sanitize_text_field( wp_unslash($new_instance['lat']) ) : '';
		$instance['lng']         = isset( $new_instance['lng'] ) ? sanitize_text_field( wp_unslash($new_instance['lng']) ) : '';
		$instance['width']       = isset( $new_instance['width'] ) ? sanitize_text_field( wp_unslash($new_instance['width']) ) : '100%';
		$instance['height']      = isset( $new_instance['height'] ) ? sanitize_text_field( wp_unslash($new_instance['height']) ) : '300';
		$instance['zoom']        = isset( $new_instance['zoom'] ) ? sanitize_text_field( wp_unslash($new_instance['zoom']) ) : '8';
		$instance['scrollwheel'] = isset( $new_instance['scrollwheel'] ) ? absint( $new_instance['scrollwheel'] ) : 1;
		$instance['showinfo']    = isset( $new_instance['showinfo'] ) ? absint( $new_instance['showinfo'] ) : 1;
		$instance['infow_open']  = isset( $new_instance['infow_open'] ) ? absint( $new_instance['infow_open'] ) : 1;
		$instance['heading']     = isset( $new_instance['heading'] ) ? sanitize_text_field( wp_unslash($new_instance['heading']) ) : '';
		$instance['address']     = isset( $new_instance['address'] ) ? sanitize_text_field( wp_unslash($new_instance['address']) ) : '';
		$instance['website']     = isset( $new_instance['website'] ) ? sanitize_text_field( wp_unslash($new_instance['website']) ) : '';

		return $instance;
	}//end of method widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {
		$general_settings = get_option( 'cbxgooglemap_general', [] );

		$map_id      = ( isset( $general_settings['map_id'] ) ) ? $general_settings['map_id'] : 0;
		$maptype     = ( isset( $general_settings['maptype'] ) ) ? $general_settings['maptype'] : 'roadmap';
		$lat         = ( isset( $general_settings['lat'] ) ) ? $general_settings['lat'] : '';
		$lng         = ( isset( $general_settings['lng'] ) ) ? $general_settings['lng'] : '';
		$width       = ( isset( $general_settings['width'] ) ) ? $general_settings['width'] : '100%';
		$height      = ( isset( $general_settings['height'] ) ) ? $general_settings['height'] : '300';
		$zoom        = ( isset( $general_settings['zoom'] ) ) ? $general_settings['zoom'] : '8';
		$scrollwheel = ( isset( $general_settings['scrollwheel'] ) ) ? $general_settings['scrollwheel'] : 1;
		$showinfo    = ( isset( $general_settings['showinfo'] ) ) ? $general_settings['showinfo'] : 1;
		$infow_open  = ( isset( $general_settings['infow_open'] ) ) ? $general_settings['infow_open'] : 1;
		$heading     = ( isset( $general_settings['heading'] ) ) ? $general_settings['heading'] : '';
		$address     = ( isset( $general_settings['address'] ) ) ? $general_settings['address'] : '';
		$website     = ( isset( $general_settings['website'] ) ) ? $general_settings['website'] : '';

		$instance    = wp_parse_args( (array) $instance,
			[
				'title'       => esc_html__( 'CBX Map: Loation Map', 'cbxgooglemap' ),
				'map_id'      => $map_id,
				'maptype'     => $maptype,
				'lat'         => $lat,
				'lng'         => $lng,
				'width'       => $width,
				'height'      => $height,
				'zoom'        => $zoom,
				'scrollwheel' => $scrollwheel,
				'showinfo'    => $showinfo,
				'infow_open'  => $infow_open,
				'heading'     => $heading,
				'address'     => $address,
				'website'     => $website,
			] );
		$title       = sanitize_text_field( wp_unslash($instance['title']) );
		$map_id      = absint( $instance['map_id'] );
		$maptype     = sanitize_text_field( wp_unslash( $instance['maptype'] ) );
		$lat         = sanitize_text_field( wp_unslash( $instance['lat'] ) );
		$lng         = sanitize_text_field( wp_unslash( $instance['lng'] ) );
		$width       = sanitize_text_field( wp_unslash( $instance['width'] ) );
		$height      = sanitize_text_field( wp_unslash( $instance['height'] ) );
		$zoom        = sanitize_text_field( wp_unslash( $instance['zoom'] ) );
		$scrollwheel = absint( $instance['scrollwheel'] );
		$showinfo    = absint( $instance['showinfo'] );
		$infow_open  = absint( $instance['infow_open'] );
		$heading     = sanitize_text_field( wp_unslash( $instance['heading'] ) );
		$address     = sanitize_text_field( wp_unslash( $instance['address'] ) );
		$website     = sanitize_text_field( wp_unslash( $instance['website'] ) );

		// Display the admin form
		include( plugin_dir_path( __FILE__ ) . 'views/admin.php' );
	}//end of method form
}//end class CBXGoogleMap_Widget