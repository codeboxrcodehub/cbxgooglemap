<?php
namespace Cbx\Googlemap\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

use Cbx\Googlemap\CBXGooglemapSettings;


class CBXGooglemapMetaHelper {
	/**
	 * Session field sections
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public static function meta_field_sections( $post_id = 0, $post_type = 'cbxgooglemap' ) {
		$sections = [
			'mapsettings' => esc_html__( 'Map Settings', 'cbxgooglemap' ),
			'maplocation' => esc_html__( 'Primary Marker', 'cbxgooglemap' ),
		];

		return apply_filters( 'cbxgooglemap_sections', $sections );
	}//end method meta_field_sections

	/**
	 * Meta fields for sessions
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public static function cbxgooglemap_meta_fields( $post_id = 0 ) {
		$meta_fields      = [];
		$settings         = new CBXGooglemapSettings();
		$general_settings = get_option( 'cbxgooglemap_general', [] );

		$zoom_level = intval( $settings->get_field( 'zoom', 'cbxgooglemap_general', '8' ) );
		if ( $zoom_level == 0 ) {
			$zoom_level = 8;
		}


		$width_default = $settings->get_field( 'width', 'cbxgooglemap_general', '100%' );
		if ( $width_default == '' || $width_default == 0 ) {
			$width_default = '100%';
		}


		$height_default = intval( $settings->get_field( 'height', 'cbxgooglemap_general', '300' ) );
		if ( $height_default == 0 ) {
			$height_default = 300;
		}

		$scrollwheel = intval( $settings->get_field( 'scrollwheel', 'cbxgooglemap_general', 1 ) );
		$showinfo    = intval( $settings->get_field( 'showinfo', 'cbxgooglemap_general', 1 ) );
		$infow_open  = intval( $settings->get_field( 'infow_open', 'cbxgooglemap_general', 1 ) );
		$maptype     = $settings->get_field( 'maptype', 'cbxgooglemap_general', 'roadmap' );


		$meta_fields['mapsettings'] = apply_filters( 'cbxgooglemap_mapsettings_fields', [
			'maptype'     => [
				'label'             => esc_html__( 'Map Type', 'cbxgooglemap' ),
				'type'              => 'select',
				'default'           => $maptype,
				'options'           => [
					'roadmap'   => esc_html__( 'Road Map', 'cbxgooglemap' ),
					'satellite' => esc_html__( 'Satellite Map', 'cbxgooglemap' ),
					'hybrid'    => esc_html__( 'Hybrid Map', 'cbxgooglemap' ),
					'terrain'   => esc_html__( 'Terrain Map', 'cbxgooglemap' ),
				],
				'desc'              => esc_html__( 'Google Map only', 'cbxgooglemap' ),
				'sanitize_callback' => 'sanitize_text_field'
			],
			'zoom'        => [
				'label'             => esc_html__( 'Zoom Level', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'Plain address with road no etc', 'cbxgooglemap' ),
				'type'              => 'text',
				'default'           => $zoom_level,
				'sanitize_callback' => 'sanitize_text_field'
			],
			'width'       => [
				'label'   => esc_html__( 'Width', 'cbxgooglemap' ),
				'desc'    => esc_html__( 'Map width, use % if need, don\'t use px as it will be automatically if no % used.', 'cbxgooglemap' ),
				'type'    => 'text',
				'default' => $width_default,
			],
			'height'      => [
				'label'             => esc_html__( 'Height', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'Map height, don\'t use px', 'cbxgooglemap' ),
				'type'              => 'text',
				'default'           => $height_default,
				'sanitize_callback' => 'sanitize_text_field'
			],
			'scrollwheel' => [
				'label'             => esc_html__( 'Mouse Scroll Wheel', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'Enable/disable mouse scroll whell', 'cbxgooglemap' ),
				'type'              => 'radio',
				'default'           => $scrollwheel,
				'desc_tip'          => true,
				'options'           => [
					'1' => esc_html__( 'Enable', 'cbxgooglemap' ),
					'0' => esc_html__( 'Disable', 'cbxgooglemap' ),
				],
				'inline'            => true,
				'sanitize_callback' => 'absint'
			],
			'showinfo'    => [
				'label'             => esc_html__( 'Show Info window', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'Show information on click of marker', 'cbxgooglemap' ),
				'type'              => 'radio',
				'default'           => $showinfo,
				'desc_tip'          => true,
				'options'           => [
					'1' => esc_html__( 'Enable', 'cbxgooglemap' ),
					'0' => esc_html__( 'Disable', 'cbxgooglemap' ),
				],
				'inline'            => true,
				'sanitize_callback' => 'absint'
			],
			'infow_open'  => [
				'label'             => esc_html__( 'Info/Popup Window', 'cbxgooglemap' ),
				'type'              => 'radio',
				'default'           => $infow_open,
				'desc_tip'          => true,
				'options'           => [
					'1' => esc_html__( 'Open(Default)', 'cbxgooglemap' ),
					'0' => esc_html__( 'On Click', 'cbxgooglemap' ),
				],
				'inline'            => true,
				'sanitize_callback' => 'absint'
			],
		] );

		$meta_fields['maplocation'] = apply_filters( 'cbxgooglemap_maplocation_fields', [
			'title'    => [
				'label'             => esc_html__( 'Marker Heading', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'Heading title will be used for marker pop info', 'cbxgooglemap' ),
				'type'              => 'text',
				'default'           => 'Default Title',
				'sanitize_callback' => 'sanitize_text_field'
			],
			'address'  => [
				'label'             => esc_html__( 'Address', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'Plain address with road no etc', 'cbxgooglemap' ),
				'type'              => 'text',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field'
			],
			'website'  => [
				'label'             => esc_html__( 'Website Link', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'If website link is given then market popup will show the Title as linked with this address', 'cbxgooglemap' ),
				'type'              => 'text',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field'
			],
			'location' => [
				'label'   => esc_html__( 'Map Location', 'cbxgooglemap' ),
				'desc'    => esc_html__( 'Type location and select from google map auto suggest.', 'cbxgooglemap' ),
				'type'    => 'location',
				'default' => '',
			],
			'lat'      => [
				'label'             => esc_html__( 'Latitude', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'If you select location from google map then latitude will be picked automatically but still you can put manually', 'cbxgooglemap' ),
				'type'              => 'lat',
				'default'           => '',
				'sortable'          => true,
				'sanitize_callback' => 'sanitize_text_field'
			],
			'lng'      => [
				'label'             => esc_html__( 'Longitude', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'If you select location from google map then longitude will be picked automatically but still you can put manually', 'cbxgooglemap' ),
				'type'              => 'lng',
				'default'           => '',
				'sortable'          => true,
				'sanitize_callback' => 'sanitize_text_field'
			],

			'mapicon' => [
				'label'             => esc_html__( 'Map Icon', 'cbxgooglemap' ),
				'desc'              => esc_html__( 'Map marker custom icon', 'cbxgooglemap' ),
				'type'              => 'file',
				'default'           => '',
				'desc_tip'          => true,
				'sanitize_callback' => 'sanitize_text_field'
			],

		] );

		return apply_filters( 'cbxgooglemap_fields', $meta_fields );
	}//end method cbxgooglemap_meta_fields

	/**
	 * Render the meta fields html
	 *
	 * @param $post
	 * @param $meta_fields
	 * @param $combined_field
	 * @param $meta_prefix
	 * @param $sectionable
	 *
	 * @return void\
	 */
	public static function render_meta_fields( $post, $meta_fields, $combined_field, $meta_prefix, $sectionable = false ) {
		$settings = new CBXGooglemapSettings();

		$general_settings    = get_option( 'cbxgooglemap_general', [] );

		$api_key             = esc_attr( $settings->get_field( 'apikey', 'cbxgooglemap_general', '' ) );
		$map_source          = absint( $settings->get_field( 'mapsource', 'cbxgooglemap_general', 1 ) );
		$scrollwheel_default = absint( $settings->get_field( 'scrollwheel', 'cbxgooglemap_general', 1 ) );
		$showinfo_default    = absint( $settings->get_field( 'showinfo', 'cbxgooglemap_general', 1 ) );

		$mapicon_default = esc_url( $settings->get_field( 'mapicon', 'cbxgooglemap_general', '' ) );

		if ( isset( $post->ID ) && $post->ID > 0 ) {
			$post_id   = $post->ID;
			$post_type = $post->post_type;


			wp_nonce_field( 'cbxmetahelper_' . $post_type . '_meta_box', 'cbxmetahelper_' . $post_type . '_meta_box_nonce' );

			$meta_combined = get_post_meta( $post_id, $combined_field, true ); //meta fields not sortable

			$mapicon = isset( $meta_combined['_cbxgooglemapmapicon'] ) ? esc_url( $meta_combined['_cbxgooglemapmapicon'] ) : $mapicon_default;

			$sections_found = false;
			$sections       = [];

			$sections = CBXGooglemapMetaHelper::meta_field_sections( $post_id, $post_type );

			echo '<div class="metabox-holder-wrapper">';

			//print the sections
			if ( $sectionable ) {

				$sections_html = '<h2 class="nav-tab-wrapper" id="cbxgooglemap_meta_tabs">';
				foreach ( $meta_fields as $section_id => $fields ) {
					$sections_html .= '<a id="metabox-content' . esc_attr( $section_id ) . '-tab" class="nav-tab" href="#metabox-content' . esc_attr( $section_id ) . '">' . ( isset( $sections[ $section_id ] ) ? $sections[ $section_id ] : ucfirst( $section_id ) ) . '</a>';
				}

				$sections_html .= '</h2>';
				echo $sections_html;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '<div class="metabox-holder metabox-holder-cbxgooglemap metabox-holder-cbxgooglemap-' . esc_attr( $post_type ) . '">';

			foreach ( $meta_fields as $section_id => $fields ) {
				echo '<div id="metabox-content' . esc_attr( $section_id ) . '" class="metabox-content metabox-content-cbxgooglemap metabox-content-cbxgooglemap-' . esc_attr( $post_type ) . '">';
				echo '<table class="form-table">';

				foreach ( $fields as $id => $field ) {

					$is_sortable     = isset( $field['sortable'] ) ? ( $field['sortable'] ) : false;
					$multiple_select = ( isset( $field['multiple'] ) ) ? $field['multiple'] : false;

					if ( $is_sortable ) {
						$meta = get_post_meta( $post_id, $meta_prefix . $id, true );
					} else {
						//for combined field
						$meta = isset( $meta_combined[ $meta_prefix . $id ] ) ? $meta_combined[ $meta_prefix . $id ] : '';
					}


					if ( $meta == '' && isset( $field['default'] ) ) {
						$meta = $field['default'];
					}

					$label = isset( $field['label'] ) ? $field['label'] : '';

					echo '<tr>';
					$colspan = 1;
					if ( $label == '' ) {
						$colspan = 2;
					}
					echo ( $label != '' ) ? '<th><label for="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '">' . esc_attr( $label ) . '</label></th>' : '';

					echo '<td colspan="' . esc_attr( $colspan ) . '">';

					switch ( $field['type'] ) {
						case 'text':
							echo '<input type="text" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_text cbxgooglemapmeta_input_' . esc_attr( $id ) . '" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-text-' . esc_attr( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;

						case 'location':

							if ( $map_source == 1 && $api_key == '' ) {

								echo '<input type="text" class="cbxeventzmeta_input cbxeventzmeta_input_location" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-location-' . intval( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />';
								/* translators: %s: Admin URL */
								echo '<p>' . sprintf( esc_html__( 'Google map api key missing, alternative openstreet map can be enabled from <a href="%s" target="_blank">global setting</a>', 'cbxgooglemap' ), esc_url( admin_url( 'edit.php?post_type=cbxgooglemap&page=cbxgooglemap_settings' ) ) ) . '</p>';
							} elseif (
								$map_source == 1 ) {
								$data_custom_attrs = [];

								$data_custom_attrs      = apply_filters( 'cbxgooglemap_data_custom_attrs', $data_custom_attrs, $post_id, [] );
								$data_custom_attrs_html = ' ';
								foreach ( $data_custom_attrs as $custom_attr_key => $custom_attr_value ) {
									$data_custom_attrs_html .= ' ' . $custom_attr_key . '="' . $custom_attr_value . '" ';
								}

								echo '<input type="text" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_location" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-location-' . intval( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" /><div ' . esc_html( $data_custom_attrs_html ) . ' data-apikey="' . esc_attr( $api_key ) . '" data-mapsource="1" class="map_canvas"></div>';

								if ( isset( $field['desc'] ) ) {
									echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
								}
							} else {
								$data_custom_attrs = [];

								$data_custom_attrs      = apply_filters( 'cbxgooglemap_data_custom_attrs', $data_custom_attrs, $post_id, [] );
								$data_custom_attrs_html = ' ';
								foreach ( $data_custom_attrs as $custom_attr_key => $custom_attr_value ) {
									$data_custom_attrs_html .= ' ' . $custom_attr_key . '="' . $custom_attr_value . '" ';
								}

								echo '<input style="width:100%;" type="hidden" placeholder="' . esc_html__( 'Type full address and enter to find the lat, lon of the location', 'cbxgooglemap' ) . '" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_location" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-location-' . esc_attr( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" /><div ' . esc_html( $data_custom_attrs_html ) . ' data-mapsource="0" class="map_canvas"></div>';
								if ( isset( $field['desc'] ) ) {
									echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
								}
							}

							break;
						case 'lat':
							echo '<input type="text" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_lat" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-lat-' . intval( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;
						case 'lng':
							echo '<input type="text" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_lng" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-lng-' . intval( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;
						case 'number':
							echo '<input type="number" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_number" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-text-' . intval( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />
			            <p class="description">' . esc_html( $field['desc'] ) . '</p>';
							break;

						case 'url':
							echo '<input type="url" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_url" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-url-' . intval( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;

						case 'file':
							echo '<div class="cbxgooglemapmeta_input_file_wrap">';
							echo '<input type="text" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_file cbxgooglemapmeta_filepicker" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-file-' . intval( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />';

							$icon_extra_class  = '';
							$marker_icon       = '';
							$trash_extra_class = '';

							if ( $meta == '' ) {
								$icon_extra_class  = 'cbxgooglemapmeta_marker_hide';
								$file_picked_class = 'cbxgooglemapmeta_left_space';
								$trash_extra_class = 'cbxgooglemapmeta_trash_hide';
							} else {
								$marker_icon       = ' background-image: url(\'' . esc_url( $meta ) . '\') ;';
								$file_picked_class = 'cbxgooglemapmeta_filepicked';
							}

							echo '<span style="' . esc_attr( $marker_icon ) . '" class="cbxgooglemapmeta_marker ' . esc_attr( $icon_extra_class ) . '"></span>';

							echo '<input type="button" class="button cbxgooglemapmeta_filepicker_btn ' . esc_attr( $file_picked_class ) . '" value="' . esc_attr( $label ) . '" />';
							echo '<span class="cbxgooglemapmeta_trash dashicons dashicons-no-alt ' . esc_attr( $trash_extra_class ) . '"></span>';
							echo '</div>';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;

						case 'color':

							echo '<input type="text" class="cbxgooglemapmeta_input cbxgooglemapmeta_input_color cbxgooglemapmeta_colorpicker" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-color-' . esc_attr( $post_id ) . '" value="' . esc_attr( $meta ) . '" size="30" />';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;

						case 'select':
							echo '<select name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-select-' . intval( $post_id ) . '" class="selecttwo-select">';

							if ( isset( $field['optgroup'] ) && intval( $field['optgroup'] ) ) {

								foreach ( $field['options'] as $optlabel => $data ) {
									echo '<optgroup label="' . esc_attr( $optlabel ) . '">';
									foreach ( $data as $index => $option ) {
										echo '<option ' . ( ( $meta == $index ) ? ' selected="selected"' : '' ) . ' value="' . esc_attr( $index ) . '">' . esc_attr( $option ) . '</option>';
									}

								}
							} else {
								foreach ( $field['options'] as $index => $option ) {
									echo '<option ' . ( ( $meta == $index ) ? ' selected="selected"' : '' ) . ' value="' . esc_attr( $index ) . '">' . esc_attr( $option ) . '</option>';
								}
							}
							echo '</select><br/>';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;
						case 'multiselect':

							echo '<select name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '[]" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-multiselect-' . esc_attr( $post_id ) . '" class="selecttwo-select" multiple>';
							if ( isset( $field['optgroup'] ) && intval( $field['optgroup'] ) ) {

								foreach ( $field['options'] as $optlabel => $data ) {
									echo '<optgroup label="' . esc_attr( $optlabel ) . '">';
									foreach ( $data as $key => $val ) {
										echo '<option value="' . $key . '"', is_array( $meta ) && in_array( $key, $meta ) ? ' selected="selected"' : '', ' >' . esc_html( $val ) . '</option>';
									}
									echo '<optgroup>';
								}

							} else {
								foreach ( $field['options'] as $key => $val ) {
									echo '<option value="' . $key . '"', is_array( $meta ) && in_array( $key, $meta ) ? ' selected="selected"' : '', ' >' . esc_html( $val ) . '</option>';
								}
							}

							echo '</select>';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;

						case 'radio':
							if ( in_array( 'inline', $field ) ) {
								$inline       = isset( $field['inline'] ) ? intval( $field['inline'] ) : 0;
								$inline_class = ( $inline ) ? 'cbxgooglemap-metabox-label-radio-inline' : '';
								$br_html      = ( ! $inline ) ? '<br/>' : '';
								$last_class   = '';
							}
							$last_element = array_key_last( $field['options'] );
							echo '<fieldset>
								<legend class="screen-reader-text"><span>input type="radio"</span></legend>';
							foreach ( $field['options'] as $key => $value ) {
								if ( $key === $last_element ) {
									$last_class = 'label-margin-none';
								}
								echo '<label title="g:i a" for="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-radio-' . esc_attr( $post_id ) . '-' . esc_attr( $key ) . '" class="' . esc_attr( $inline_class ) . ' ' . esc_attr( $last_class ) . '">
										<input id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-radio-' . esc_attr( $post_id ) . '-' . esc_attr( $key ) . '" type="radio" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" value="' . esc_attr( $key ) . '" ' . ( ( $meta == $key ) ? '  checked="checked" ' : '' ) . '  />
										<span>' . esc_html( $value ) . '</span>
									</label>' . esc_html( $br_html );


							}
							echo '</fieldset>';
							echo esc_html( $br_html );
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;

						case 'checkbox':
							echo '<input type="checkbox" name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-checkbox-' . esc_attr( $post_id ) . '" class="cb-checkbox checkbox-' . esc_attr( $post_id ) . '" ' . ( $meta ? ' checked="checked"' : '' ) . '/>';
							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;
						// checkbox_group
						case 'multicheck':
							if ( $meta == '' ) {
								$meta = [];
								foreach ( $field['options'] as $option ) {
									array_push( $meta, $option['value'] );
								}
							}

							foreach ( $field['options'] as $option ) {
								echo '<input type="checkbox" value="' . $option['value'] . '" name="' . esc_attr( $meta_prefix ) . $id . '[]" id="' . $option['value'] . '-mult-chk-' . $post_id . '-field-' . esc_attr( $meta_prefix ) . $id . '" class="cb-multi-check mult-check-' . $post_id . '"', $meta && in_array( $option['value'], $meta ) ? ' checked="checked"' : '', ' />
                        <label for="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['label'] ) . '</label><br/>';
							}

							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;

						case 'post_type':
							$post_type_    = ( isset( $field['post_type'] ) ) ? $field['post_type'] : [ 'post' ];
							$post_order_   = ( isset( $field['post_order'] ) ) ? $field['post_order'] : 'DESC';
							$post_orderby_ = ( isset( $field['post_orderby'] ) ) ? $field['post_orderby'] : 'ID';

							if ( ! is_array( $post_type_ ) ) {
								$post_type_ = (array) $post_type_;
							}

							// WP_Query arguments

							$args = [
								'post_type'      => $post_type_,
								'post_status'    => [ 'publish' ],
								'order'          => $post_order_,
								'orderby '       => $post_orderby_,
								'posts_per_page' => '-1',
							];

							// The Query
							$query_post_types = get_posts( $args );

							// The Loop

							$posts_found = [];
							foreach ( $query_post_types as $post ) : CBXGooglemapHelper::setup_admin_postdata( $post );

								$post_id_                 = get_the_ID();
								$post_title_              = get_the_title( $post_id_ );
								$posts_found[ $post_id_ ] = $post_title_;

							endforeach;
							CBXGooglemapHelper::wp_reset_admin_postdata();


							if ( $multiple_select ) {
								echo '<select name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '[]" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-post_type-' . intval( $post_id ) . '" class="selecttwo-select" multiple >';
								foreach ( $posts_found as $key => $val ) {
									echo '<option value="' . esc_attr( $key ) . '"', is_array( $meta ) && in_array( $key, $meta ) ? ' selected="selected"' : '', ' >' . esc_attr( $val ) . '</option>';
								}
								echo '</select>';
							} else {
								echo '<select name="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '" id="' . esc_attr( $meta_prefix ) . esc_attr( $id ) . '-post_type-' . intval( $post_id ) . '" class="selecttwo-select" >';
								foreach ( $posts_found as $index => $option ) {
									echo '<option ' . ( ( $meta == $index ) ? ' selected="selected"' : '' ) . ' value="' . esc_attr( $index ) . '">' . esc_attr( $option ) . '</option>';
								}
								echo '</select>';
							}

							if ( isset( $field['desc'] ) ) {
								echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
							}
							break;
					}

					do_action( 'cbxgooglemap_meta_fields_render', $field['type'], $field, $post, $meta_combined );

					echo '</td>';

					echo '</tr>';
				}
				echo '</table>';

				echo '</div>';
			}

			echo '</div>';
			echo '</div>';

		} else {
			echo esc_html__( 'Please save first to add extra information.', 'cbxgooglemap' );
		}
	}//end method render_meta_fields

}//end class CBXGooglemapHelper