<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    Cbxgooglemap
 * @subpackage Cbxgooglemap/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cbxgooglemap
 * @subpackage Cbxgooglemap/admin
 * @author     Codeboxr <info@codeboxr.com>
 */

namespace Cbx\Googlemap;

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

use Cbx\Googlemap\Helpers\CBXGooglemapHelper;
use Cbx\Googlemap\Helpers\CBXGooglemapMetaHelper;

final class CBXGoogleMapAdmin
{

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
     * The ID of this plugin.
     *
     * @since    1.1.12
     * @access   private
     * @var      string $settings The ID of this plugin.
     */
    public $settings;

    /**
     * Initialize the class and set its properties.
     *
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->plugin_name = CBXGOOGLEMAP_PLUGIN_NAME;
        $this->version     = CBXGOOGLEMAP_PLUGIN_VERSION;

        //get instance of setting api
        $this->settings = new CBXGooglemapSettings();
    }//end of construtor

    /**
     * Initialize setting
     */
    public function setting_init()
    {
        //set the settings
        $this->settings->set_sections($this->get_settings_sections());
        $this->settings->set_fields($this->get_settings_fields());
        //initialize settings
        $this->settings->admin_init();
    }//end setting_init

    /**
     * Global Setting Sections
     *
     *
     * @return array
     */
    public function get_settings_sections()
    {
        return apply_filters(
            'cbxgooglemap_setting_sections', [
                [
                    'id'    => 'cbxgooglemap_general',
                    'title' => esc_html__('Default Config', 'cbxgooglemap')
                ],
                [
                    'id'    => 'cbxgooglemap_demo',
                    'title' => esc_html__('Demo & Shortcodes', 'cbxgooglemap')
                ],
                [
                    'id'    => 'cbxgooglemap_tools',
                    'title' => esc_html__('Tools', 'cbxgooglemap'),
                ]
            ]
        );
    }//end get_settings_sections

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields()
    {
        $general_settings = get_option('cbxgooglemap_general', []);

        $tools_delete_table_html = '<div id="setting_resetinfo">'.esc_html__('Loading ...', 'cbxgooglemap').'</div>';

        $settings_builtin_fields = [
            'cbxgooglemap_general' => [
                'general_heading' => [
                    'name'    => 'general_heading',
                    'label'   => esc_html__('Default Config', 'cbxgooglemap'),
                    'type'    => 'heading',
                    'default' => '',
                ],
                'mapsource'       => [
                    'name'              => 'mapsource',
                    'label'             => esc_html__('Map Source', 'cbxgooglemap'),
                    'type'              => 'radio',
                    'default'           => 1,
                    'options'           => [
                        0 => esc_html__('Openstreet Map(leafletjs)', 'cbxgooglemap'),
                        1 => esc_html__('Google Map', 'cbxgooglemap')
                    ],
                    'inline'            => 1,
                    'sanitize_callback' => 'absint'
                ],
                'apikey'          => [
                    'name'  => 'apikey',
                    'label' => esc_html__('Api Key', 'cbxgooglemap'),
                    'desc'  => esc_html__('Google map api key', 'cbxgooglemap'),
                    'type'  => 'text'

                ],
                'maptype'         => [
                    'name'              => 'maptype',
                    'label'             => esc_html__('Map Type', 'cbxgooglemap'),
                    'type'              => 'select',
                    'default'           => 'roadmap',
                    'options'           => [
                        'roadmap'   => esc_html__('Road Map', 'cbxgooglemap'),
                        'satellite' => esc_html__('Satellite Map', 'cbxgooglemap'),
                        'hybrid'    => esc_html__('Hybrid Map', 'cbxgooglemap'),
                        'terrain'   => esc_html__('Terrain Map', 'cbxgooglemap'),
                    ],
                    'desc'              => esc_html__('Google Map only', 'cbxgooglemap'),
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'zoom'            => [
                    'name'              => 'zoom',
                    'label'             => esc_html__('Zoom Level', 'cbxgooglemap'),
                    'desc'              => esc_html__('Default Zoom Level', 'cbxgooglemap'),
                    'type'              => 'number',
                    'step'              => 1,
                    'default'           => 8,
                    'sanitize_callback' => 'absint'

                ],
                'width'           => [
                    'name'    => 'with',
                    'label'   => esc_html__('Width', 'cbxgooglemap'),
                    'desc'    => esc_html__('Default is 100% to make the map responsive, if you want any fixed width then don\'t put, % or just put numeric value, don\'t px with numeric value', 'cbxgooglemap'),
                    'type'    => 'text',
                    'default' => '100%',

                ],
                'height'          => [
                    'name'              => 'height',
                    'label'             => esc_html__('Height', 'cbxgooglemap'),
                    'desc'              => esc_html__('Default height 300 as px, put any numeric value.', 'cbxgooglemap'),
                    'type'              => 'number',
                    'default'           => '300',
                    'sanitize_callback' => 'floatval'
                ],
                'scrollwheel'     => [
                    'name'              => 'scrollwheel',
                    'label'             => esc_html__('Mouse Scroll Wheel', 'cbxgooglemap'),
                    'desc'              => esc_html__('Enable/disable mouse scroll whell', 'cbxgooglemap'),
                    'type'              => 'radio',
                    'default'           => 1,
                    'options'           => [
                        '0' => esc_html__('Disable', 'cbxgooglemap'),
                        '1' => esc_html__('Enable', 'cbxgooglemap')
                    ],
                    'inline'            => 1,
                    'sanitize_callback' => 'absint'
                ],
                'showinfo'        => [
                    'name'              => 'showinfo',
                    'label'             => esc_html__('Show Info window', 'cbxgooglemap'),
                    'desc'              => esc_html__('Show information on click of marker', 'cbxgooglemap'),
                    'type'              => 'radio',
                    'default'           => 1,
                    'options'           => [
                        '0' => esc_html__('Disable', 'cbxgooglemap'),
                        '1' => esc_html__('Enable', 'cbxgooglemap')
                    ],
                    'inline'            => 1,
                    'sanitize_callback' => 'absint'
                ],
                'infow_open'      => [
                    'name'              => 'infow_open',
                    'label'             => esc_html__('Info/Popup Window', 'cbxgooglemap'),
                    'type'              => 'radio',
                    'default'           => 1,
                    'options'           => [
                        '0' => esc_html__('On Click', 'cbxgooglemap'),
                        '1' => esc_html__('Open(Default)', 'cbxgooglemap'),
                    ],
                    'inline'            => 1,
                    'sanitize_callback' => 'absint'
                ],
                'mapicon'         => [
                    'name'    => 'mapicon',
                    'label'   => esc_html__('Map Icon', 'cbxgooglemap'),
                    'type'    => 'file',
                    'default' => ''
                ],
                'hide_leaflet'    => [
                    'name'              => 'hide_leaflet',
                    'label'             => esc_html__('Hide Leaflet Branding', 'cbxgooglemap'),
                    'type'              => 'radio',
                    'default'           => 0,
                    'options'           => [
                        0 => esc_html__('Show/Default', 'cbxgooglemap'),
                        1 => esc_html__('Hide', 'cbxgooglemap'),
                    ],
                    'inline'            => 1,
                    'sanitize_callback' => 'absint'
                ],

            ],
            'cbxgooglemap_demo'    => [
                'demo_heading'   => [
                    'name'    => 'demo_heading',
                    'label'   => esc_html__('Demo & Shortcodes', 'cbxgooglemap'),
                    'type'    => 'heading',
                    'default' => '',
                ],
                'shortcode_demo' => [
                    'name'              => 'shortcode_demo',
                    'label'             => esc_html__('Shortcode & Demo', 'cbxgooglemap'),
                    'desc'              => esc_html__('Shortcode and demo based on default setting, please save once to check change.', 'cbxgooglemap'),
                    'type'              => 'shortcode',
                    'class'             => 'cbcurrencyconverter_demo_copy',
                    'default'           => CBXGooglemapHelper::shortcode_builder($general_settings),
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ],
            'cbxgooglemap_tools'   => [
                'tools_heading'        => [
                    'name'    => 'tools_heading',
                    'label'   => esc_html__('Tools Settings', 'cbxgooglemap'),
                    'type'    => 'heading',
                    'default' => '',
                ],
                'delete_global_config' => [
                    'name'    => 'delete_global_config',
                    'label'   => esc_html__('On Uninstall delete plugin data', 'cbxgooglemap'),
                    'desc'    => '<p>'.esc_html__('Delete Global Config data(options/plugin settings), custom table(s), files/folders, all map custom post type created by this plugin on uninstall. Please note that this process can not be undone and it is recommended to keep full database and files backup before doing this.', 'cbxgooglemap').'</p>',
                    'type'    => 'radio',
                    'options' => [
                        'yes' => esc_html__('Yes', 'cbxgooglemap'),
                        'no'  => esc_html__('No', 'cbxgooglemap'),
                    ],
                    'default' => 'no',
                ],
                'reset_data'           => [
                    'name'    => 'reset_data',
                    'label'   => esc_html__('Reset all section', 'cbxgooglemap'),
                    'desc'    => $tools_delete_table_html.'<p>'.esc_html__('This will reset all option/section created by this plugin.',
                            'cbxgooglemap').'<a data-busy="0" class="button secondary ml-20" id="reset_data_trigger"  href="#">'.esc_html__('Reset Sections',
                            'cbxgooglemap').'</a></p>',
                    'type'    => 'html',
                    'default' => 'off'
                ],
            ],
        ];


        $settings_fields = []; //final setting array that will be passed to different filters
        $sections        = $this->get_settings_sections();

        foreach ($sections as $section) {
            if ( ! isset($settings_builtin_fields[$section['id']])) {
                $settings_builtin_fields[$section['id']] = [];
            }
        }


        foreach ($sections as $section) {
            $settings_fields[$section['id']] = apply_filters('cbxgooglemap_global_'.$section['id'].'_fields', $settings_builtin_fields[$section['id']]);
        }

        return apply_filters('cbxgooglemap_global_fields', $settings_fields);
    }//end get_settings_fields

    /**
     * Register Custom Post Type cbxgooglemap
     *
     * @since    3.7.0
     */
    public function create_post_type()
    {
        CBXGooglemapHelper::create_googlemap_post_type();

        // Check the option we set on activation.
        if (get_option('cbxgooglemap_flush_rewrite_rules') === 'true') {
            flush_rewrite_rules();
            delete_option('cbxgooglemap_flush_rewrite_rules');
        }
    }//end create_post_type


    /**
     * Show menu page
     */
    public function menu_pages()
    {
        //setting page
        $setting_page_hook = add_submenu_page('edit.php?post_type=cbxgooglemap', esc_html__('CBX Map: Settings', 'cbxgooglemap'), esc_html__('Settings', 'cbxgooglemap'), 'manage_options', 'cbxgooglemap_settings', [
            $this,
            'menu_page_settings'
        ]);

        $doc_page_hook = add_submenu_page('edit.php?post_type=cbxgooglemap', esc_html__('CBX Map Helps & Updates', 'cbxgooglemap'), esc_html__('Helps & Updates', 'cbxgooglemap'), 'manage_options', 'cbxgooglemap_support', [
            $this,
            'menu_page_docs'
        ]);
    }//end menu_pages

    /**
     * Show cbxeventz Setting page
     */
    public function menu_page_settings()
    {
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo cbxgooglemap_get_template_html('admin/settings.php',
            [
                'ref'      => $this,
                'settings' => $this->settings
            ]
        );
    }//end menu_page_settings

    /**
     * Render the help & support page for this plugin.
     *
     * @since    1.0.0
     */
    public function menu_page_docs()
    {
        echo cbxgooglemap_get_template_html('admin/support.php');//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }//end method menu_page_docs

    /**
     * Add metabox for custom post type cbxfeedbackform && cbxfeedbackbtn
     *
     * @since    1.0.0
     */
    public function add_meta_boxes()
    {
        add_meta_box('cbxgooglemap_metabox', esc_html__('Map Parameters', 'cbxgooglemap'), [
            $this,
            'parameter_metabox_display'
        ], 'cbxgooglemap', 'normal', 'high');


        add_meta_box('cbxgooglemap_shortcode', esc_html__('Shortcode', 'cbxgooglemap'), [
            $this,
            'shortcode_metabox_display'
        ], 'cbxgooglemap', 'side', 'low');
    }//end add_meta_boxes

    /**
     * Show Shortcode display metabox
     *
     * @param $post
     */
    public function shortcode_metabox_display($post)
    {
        if (isset($post->ID) && $post->ID > 0) {
            $post_id   = $post->ID;
            $post_type = $post->post_type;

            echo cbxgooglemap_get_template_html('admin/metabox_shortcode.php', [//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'post_id'   => $post_id,
                'post_type' => $post_type
            ]);
        }
    }//end shortcode_metabox_display

    /**
     * Render metabox
     *
     * @param $post
     *
     * since v1.0.0
     */
    public function parameter_metabox_display($post)
    {
        global $post;
        $post_type = $post->post_type;


        $meta_fields = CBXGooglemapMetaHelper::cbxgooglemap_meta_fields();

        $combined_field = '_'.$post_type.'_combined'; //field name for non sortable fields
        $meta_prefix    = '_'.$post_type;               //field prefix for sortable fields


        CBXGooglemapMetaHelper::render_meta_fields($post, $meta_fields, $combined_field, $meta_prefix, true); //
    }//end parameter_metabox_display

    /**
     * Save meta box for cbxeventz
     *
     * @param $post_id
     */
    public function metabox_save($post_id, $post, $update)
    {

        $post_type = $post->post_type;

        // Check if our nonce is set.
        if ( ! isset($_POST['cbxmetahelper_'.$post_type.'_meta_box_nonce'])) {
            return $post_id;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['cbxmetahelper_'.$post_type.'_meta_box_nonce'])), 'cbxmetahelper_'.$post_type.'_meta_box')) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if (isset($_POST['post_type']) && $post_type == $_POST['post_type']) {

            if ( ! current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        $meta_fields = CBXGooglemapMetaHelper::cbxgooglemap_meta_fields($post_id);


        $combined_field = '_'.$post_type.'_combined'; //field name for non sortable fields
        $meta_prefix    = '_'.$post_type;               //field prefix for sortable fields

        $meta_combined = get_post_meta($post_id, $combined_field, true); //meta fields not sortable

        $meta_combined = ! is_array($meta_combined) ? [] : $meta_combined;


        $combined_arr = [];
        foreach ($meta_fields as $section_id => $fields) {
            foreach ($fields as $id => $field) {

                $field_type = $field['type'];

                $sanitize_callback = isset($field['sanitize_callback']) ? $field['sanitize_callback'] : null;

                if (isset($_POST[$meta_prefix.$id])) {

                    if (isset($_POST[$meta_prefix.$id])) {
                        $updated_value = wp_unslash($_POST[$meta_prefix.$id]);


                        if ($field_type == 'text') {
                            $updated_value = sanitize_text_field(wp_unslash($updated_value));
                        } elseif ($field_type == 'textarea') {
                            $updated_value = sanitize_textarea_field(wp_unslash($updated_value));
                        } else {
                            if ($sanitize_callback !== null && is_callable($sanitize_callback)) {
                                $updated_value = call_user_func($sanitize_callback, $updated_value);
                            }
                            else{
                                $updated_value = sanitize_text_field(wp_unslash($updated_value));
                            }
                        }

                        $is_sortable = isset($field['sortable']) ? $field['sortable'] : false;

                        if ($is_sortable) {
                            $ret = update_post_meta($post_id, $meta_prefix.$id, $updated_value); //update the combined meta
                        } else {
                            //save as combined meta
                            $meta_combined[$meta_prefix.$id] = $updated_value;
                        }
                    }

                }

            }
        }

        update_post_meta($post_id, $combined_field, $meta_combined); //update the combined meta

        do_action('cbxgooglemap_metabox_save', $post_id);

    }//end metabox_save

    /**
     * Add or adjust col for cbxgooglemap post type
     *
     * @param $cbxpoll_columns
     *
     * @return array
     *
     */
    public function cbxgooglemap_add_new_columns($columns)
    {
        unset($columns['date']);
        unset($columns['comments']);

        $columns['lat']  = esc_attr('Latitude', 'cbxgooglemap');
        $columns['lng']  = esc_attr('Longitude', 'cbxgooglemap');
        $columns['zoom'] = esc_attr('Zoom', 'cbxgooglemap');

        //$columns['shortcode'] = esc_attr( 'Shortcode', 'cbxgooglemap' );

        return $columns;
    }//end cbxgooglemap_add_new_columns

    /**
     * Add extra cols information for cbxgooglemap post type
     *
     * @param $column_name
     *
     *
     * show data to poll table custom column
     */
    public function cbxgooglemap_manage_columns($column_name)
    {
        global $wpdb, $post;

        $post_id   = intval($post->ID);
        $post_type = $post->post_type;

        $combined_field = '_cbxgooglemap_combined'; //field name for non sortable fields
        $meta_prefix    = '_cbxgooglemap';          //field prefix for sortable fields

        $meta_combined = get_post_meta($post_id, $combined_field, true);

        $lat = get_post_meta($post_id, $meta_prefix.'lat', true);
        $lat = ($lat !== false) ? $lat : '';

        $lng = get_post_meta($post_id, $meta_prefix.'lng', true);
        $lng = ($lng !== false) ? $lng : '';

        $zoom = (isset($meta_combined[$meta_prefix.'zoom']) && intval($meta_combined[$meta_prefix.'zoom']) > 0) ? intval($meta_combined[$meta_prefix.'zoom']) : '';


        switch ($column_name) {
            /*case 'shortcode':
                echo '<div class="cbxshortcode-wrap">';
                echo '<span data-clipboard-text=\'[cbxgooglemap id="' . absint( $post_id ) . '"]\' title="' . esc_html__( 'Click to clipboard', 'cbxgooglemap' ) . '" id="cbxgooglemapshortcode-' . absint( $post_id ) . '" class="cbxshortcode cbxshortcode-edit cbxshortcode-' . absint( $post_id ) . '">[cbxgooglemap id="' . absint( $post_id ) . '"]</span>';
                echo '<span class="cbxballon_ctp_btn cbxballon_ctp" aria-label="' . esc_html__( 'Click to copy', 'cbxgooglemap' ) . '" data-balloon-pos="up"><i></i></span>';
                echo '</div>';
                break;*/
            case 'lat':
                echo esc_html($lat);
                break;
            case 'lng':
                echo esc_html($lng);
                break;
            case 'zoom':
                echo esc_html($zoom);
                break;
            default:
                break;
        } // end switch
    }//end cbxgooglemap_manage_columns

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook)
    {
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $ver          = $this->version;
        $in_footer    = [
            'in_footer' => true,
        ];

        $vendor_url = CBXGOOGLEMAP_ROOT_URL.'assets/vendors/';
        //$css_url    = CBXGOOGLEMAP_ROOT_URL . 'assets/css/';
        //$js_url     = CBXGOOGLEMAP_ROOT_URL . 'assets/css/';

        $css_url_part         = CBXGOOGLEMAP_ROOT_URL.'assets/css/';
        $css_url_part_vendors = CBXGOOGLEMAP_ROOT_URL.'assets/vendors/';
        $js_url_part_vendors  = CBXGOOGLEMAP_ROOT_URL.'assets/vendors/';

        global $post_type, $post;

        $api_key    = esc_attr($this->settings->get_option('apikey', 'cbxgooglemap_general', ''));
        $map_source = absint($this->settings->get_option('mapsource', 'cbxgooglemap_general', 1));

        wp_register_style('awesome-notifications', $css_url_part_vendors.'awesome-notifications/style.css', [], $ver);
        wp_register_style('flatpickr', $css_url_part_vendors.'flatpickr/flatpickr.min.css', [], $ver);
        wp_register_style('cbxgooglemap-admin', $css_url_part.'cbxgooglemap-admin.css', [], $ver);

        //listing mode
        if (($hook == 'edit.php') && $post_type == 'cbxgooglemap') {
            wp_register_style('cbxgooglemap-admin', $css_url_part.'cbxgooglemap-admin.css', [], $ver);
            wp_enqueue_style('cbxgooglemap-admin');
        }

        //add new, edit mode
        if (($hook == 'post.php' || $hook == 'post-new.php') && $post_type == 'cbxgooglemap') {

            if ($map_source == 0) {
                wp_register_style('leaflet', $vendor_url.'leaflet/leaflet.css', [], $ver, 'all');
                wp_register_style('leaflet-control-geocoder', $vendor_url.'leaflet-control-geocoder/Control.Geocoder.css', [], $ver, 'all');
            }

            wp_register_style('select2', $vendor_url.'select2/select2.min.css', [], $ver);
            wp_register_style('cbxgooglemap-admin', $css_url_part.'cbxgooglemap-admin.css', [
                'select2',
            ], $ver);

            wp_enqueue_style('select2');

            if ($map_source == 0) {
                wp_enqueue_style('leaflet');
                wp_enqueue_style('leaflet-control-geocoder');
            }

            wp_enqueue_style('awesome-notifications');
            wp_enqueue_style('cbxgooglemap-admin');
        }//add new, edit mode

        if ($current_page == 'cbxgooglemap_settings') {
            wp_register_style('pickr', $css_url_part_vendors.'pickr/classic.min.css', [], $ver);
            wp_register_style('select2', $css_url_part_vendors.'select2/select2.min.css', [], $ver);

            wp_register_style('cbxgooglemap-setting', $css_url_part.'cbxgooglemap-setting.css', [
                'pickr',
                'awesome-notifications',
                'select2',
                'cbxgooglemap-admin'
            ], $ver);

            wp_enqueue_style('pickr');
            wp_enqueue_style('awesome-notifications');
            wp_enqueue_style('select2');

            //for demo css
            if ($map_source == 0) {
                wp_register_style('leaflet', $vendor_url.'leaflet/leaflet.css', [], $ver, 'all');
                wp_register_style('cbxgooglemap-public', $css_url_part.'cbxgooglemap-public.css', ['leaflet'], $ver, 'all');
                wp_enqueue_style('leaflet');

            } else {
                wp_register_style('cbxgooglemap-public', $css_url_part.'cbxgooglemap-public.css', [], $ver, 'all');
            }

            wp_enqueue_style('cbxgooglemap-public');
            wp_enqueue_style('cbxgooglemap-admin');
            wp_enqueue_style('cbxgooglemap-setting');

            //end for demo css
        }//end setting

        if ($current_page == 'cbxgooglemap_support') {
            wp_enqueue_style('cbxgooglemap-admin');
        }//end style adding for doc page

        //tax pages
        if (($hook == 'edit-tags.php' || $hook == 'term.php') && $post_type == 'cbxgooglemap' && $current_page == '') {
            wp_enqueue_style('awesome-notifications');
            wp_enqueue_style('cbxgooglemap-admin');
        }
    }//end enqueue_styles

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook)
    {
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $ver          = $this->version;
        $in_footer    = [
            'in_footer' => true,
        ];

        $vendor_url = CBXGOOGLEMAP_ROOT_URL.'assets/vendors/';
        $css_url    = CBXGOOGLEMAP_ROOT_URL.'assets/css/';
        $js_url     = CBXGOOGLEMAP_ROOT_URL.'assets/js/';

        //assets urls
        $js_url_part         = CBXGOOGLEMAP_ROOT_URL.'assets/js/';
        $js_url_part_vendors = CBXGOOGLEMAP_ROOT_URL.'assets/vendors/';
        $js_url_part_vanila  = CBXGOOGLEMAP_ROOT_URL.'assets/js/vanila/';
        $js_url_part_build   = CBXGOOGLEMAP_ROOT_URL.'assets/js/build/';


        $t = true;
        $f = false;


        $in_footer = [
            'in_footer' => $t,
        ];

        $in_footer_async = [
            'in_footer' => $t,
            'strategy'  => 'async'
        ];

        $global_translation = CBXGooglemapHelper::global_translation_strings();
        $plus_svg           = cbxgooglemap_esc_svg(cbxgooglemap_load_svg('icon_plus'));


        global $post_type, $post;
        $post_id = isset($post->ID) ? absint($post->ID) : 0;

        $api_key         = esc_attr($this->settings->get_option('apikey', 'cbxgooglemap_general', ''));
        $map_source      = absint($this->settings->get_option('mapsource', 'cbxgooglemap_general', 1));
        $default_mapicon = esc_url($this->settings->get_option('mapicon', 'cbxgooglemap_general', ''));

        /*if($default_mapicon == ''){
            $default_mapicon = 'https://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png';
        }*/

        /*$zoom_level_default = intval( $this->settings->get_option( 'zoom', 'cbxgooglemap_general', '8' ) );
        if ( $zoom_level_default == 0 ) {
            $zoom_level_default = 8;
        }*/


        wp_register_script('cbxgooglemap-events', $js_url.'cbxgooglemap-events.js', [], $ver, $in_footer);
        wp_register_script('awesome-notifications', $js_url_part_vendors.'awesome-notifications/script.js', [], $ver, $in_footer);
        wp_register_script('select2', $js_url_part_vendors.'select2/select2.min.js', ['jquery'], $ver, $in_footer);
        //wp_register_script( 'flatpickr', $js_url_part_vendors . 'flatpickr/flatpickr.min.js', [ 'jquery' ], $ver, true );
        wp_register_script('pickr', $js_url_part_vendors.'pickr/pickr.min.js', [], $ver, $in_footer);
        //wp_register_script( 'jquery-validate', $js_url_part_vendors . 'jquery-validation/jquery.validate.min.js', [ 'jquery' ], $ver, $in_footer );

        //maps listing mode
        if ($hook == 'edit.php' && $post_type == 'cbxgooglemap') {
            wp_register_script('cbxgooglemap-listing', $js_url.'cbxgooglemap-listing.js', ['jquery'], $ver, $t);

            $cbxgooglemap_listing_js_vars = apply_filters('cbxgooglemap_listing_js_vars', $global_translation);
            wp_localize_script('cbxgooglemap-listing', 'cbxgooglemap_listing', $cbxgooglemap_listing_js_vars);

            wp_enqueue_script('jquery');
            wp_enqueue_script('cbxgooglemap-listing');
        }

        //add new or edit mode
        if (($hook == 'post.php' || $hook == 'post-new.php') && $post_type == 'cbxgooglemap') {

            wp_enqueue_script('cbxgooglemap-events');


            $edit_js_dep = ['jquery', 'select2'];

            if ($map_source == 1 && $api_key != '') {
                wp_register_script('coregooglemapapi', '//maps.googleapis.com/maps/api/js?key='.$api_key.'&libraries=places&callback=Function.prototype', [], $ver, $in_footer_async);

	            $edit_js_dep[] = 'awesome-notifications';
	            $edit_js_dep[] = 'coregooglemapapi';
                $edit_js_dep[] = 'cbxgooglemap-events';

            } elseif ($map_source == 0) {
                wp_register_script('coregooglemapapi', $vendor_url.'leaflet/leaflet.js', [], $ver, $in_footer_async);
                wp_register_script('leaflet-control-geocoder', $vendor_url.'leaflet-control-geocoder/Control.Geocoder.js', [], $ver, $in_footer_async);

	            $edit_js_dep[] = 'awesome-notifications';
	            $edit_js_dep[] = 'coregooglemapapi';
                $edit_js_dep[] = 'leaflet-control-geocoder';
                $edit_js_dep[] = 'cbxgooglemap-events';
            }

            wp_enqueue_media();
            $main_marker = [];

            wp_register_script('cbxgooglemap-edit', $js_url.'cbxgooglemap-edit.js', $edit_js_dep, $ver, $in_footer);

            $edit_js_vars = [
                'search_address'        => esc_html__('Search Address', 'cbxgooglemap'),
                'icon_url_default'      => $default_mapicon,
                'api_key'               => $api_key,
                'map_source'            => $map_source,
                'map_title_placeholder' => esc_html__('Map Title Here', 'cbxgooglemap'),
                'no_address_found' => esc_html__('No details available for current input', 'cbxgooglemap'),
            ];

            $edit_js_vars = array_merge($edit_js_vars, $global_translation);

            $edit_js_vars = apply_filters('cbxgooglemap_edit_js_vars', $edit_js_vars, $post_id);

            wp_localize_script('cbxgooglemap-edit', 'cbxgooglemap_edit', $edit_js_vars);

            //enqueue dependency js cripts
            foreach ($edit_js_dep as $dep) {
                wp_enqueue_script($dep);
            }

            //enqueue main edit scripts
            wp_enqueue_script('cbxgooglemap-edit');
        }

        if ($current_page == 'cbxgooglemap_settings') {
            $setting_js_deps = [
                'jquery',
                'jquery-ui-sortable',
                'select2',
                'pickr',
                'awesome-notifications'
            ];

            $setting_js_deps = apply_filters('cbxgooglemap_setting_js_deps', $setting_js_deps);

            $blog_id = is_multisite() ? get_current_blog_id() : null;

            // Localize the script with new data
            $setting_js_vars = [
                'global_setting_link_html' => '<a href="'.esc_url(admin_url('edit.php?post_type=cbxgooglemap&page=cbxgooglemap_settings')).'"  class="button outline primary pull-right">'.esc_html__('Global Settings', 'cbxgooglemap').'</a>',
                'lang'                     => get_user_locale(),
                'clearPermalinks'          => esc_url_raw(get_rest_url($blog_id, 'cbxgooglemap/v1/admin/clear-permalinks')),
                'rest_end_points'          => [
                    'migrate_wp_wpsimplegooglemap' => esc_url_raw(get_rest_url('', 'cbxgooglemap/v1/migrate-wp-wpsimplegooglemap-data')),
                ],
            ];

            $import_modal_html = '<div id="cbxgooglemap_settings_import_modal_wrap" class="cbx-chota">';
            $import_modal_html .= '<h2>'.esc_html__('Import CBX Google Map Setting: Json file', 'cbxgooglemap').'</h2>';
            $import_modal_html .= '<form method="post" id="cbxgooglemap_settings_import_form">';
            $import_modal_html .= '<input type="file" name="file" id="cbxgooglemap_settings_import_file" accept="application/json" />';
            $import_modal_html .= '</form>';
            $import_modal_html .= '</div>';

            $setting_js_vars['import_modal']          = $import_modal_html;
            $setting_js_vars['import_modal_progress'] = '<p>'.esc_html__('Please wait, Importing...', 'cbxgooglemap').'</p>';

            $cbxgooglemap_setting_js_vars = apply_filters('cbxgooglemap_setting_js_vars', array_merge($setting_js_vars, $global_translation));

            wp_register_script('cbxgooglemap-setting', $js_url.'cbxgooglemap-setting.js', $setting_js_deps, $ver, $in_footer);

            wp_localize_script('cbxgooglemap-setting', 'cbxgooglemap_setting_js_var', $cbxgooglemap_setting_js_vars);

            //core
            wp_enqueue_script('jquery');
            wp_enqueue_media();

            //vendors
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('select2');
            wp_enqueue_script('pickr');
            wp_enqueue_script('awesome-notifications');

            //custom
            wp_enqueue_script('cbxgooglemap-setting');

            //for demo js (we need this but we will sort out later)
            if (($map_source == 1 && ! empty($api_key)) || $map_source == 0) {
                if ($map_source == 1) {
                    //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
                    wp_register_script('coregooglemapapi', '//maps.googleapis.com/maps/api/js?key='.esc_attr($api_key).'&libraries=places&callback=Function.prototype', [], $ver, $in_footer_async);
                    wp_register_script('cbxgooglemap-public', $js_url.'cbxgooglemap-public.js', [
                        'jquery',
                        'cbxgooglemap-events',
                        'coregooglemapapi'
                    ], $ver, $in_footer);

                    wp_enqueue_script('coregooglemapapi');
                } else {
                    wp_register_script('coregooglemapapi', $vendor_url.'leaflet/leaflet.js', [], $ver, $in_footer_async);
                    wp_register_script('cbxgooglemap-public', $js_url.'cbxgooglemap-public.js', [
                        'jquery',
                        'cbxgooglemap-events',
                        'coregooglemapapi',
                        //'leaflet-control-geocoder'
                    ], $ver, $in_footer);

                    wp_enqueue_script('cbxgooglemap-events');
                    wp_enqueue_script('coregooglemapapi');
                    //wp_enqueue_script( 'leaflet-control-geocoder' );
                }

                wp_enqueue_script('cbxgooglemap-public');
            }//end for demo js

        }//end js for setting page

        //enqueue js for tax page
        if (($hook == 'edit-tags.php' || $hook == 'term.php') && $post_type == 'cbxgooglemap' && $current_page == '') {
            //tax js dependencies

            $tax_js_deps = [
                //core
                'jquery',
                //vendors
                //'jquery-validate'
            ];

            $tax_js_deps = apply_filters('cbxgooglemap_tax_js_deps', $tax_js_deps);

            $new_map_link_html = '<a href="'.esc_url(admin_url('edit.php?post_type=cbxgooglemap')).'" class="button secondary icon icon-right icon-inline mr-5"><i  class="cbx-icon">'.$plus_svg.'</i>'.esc_html__('New Map',
                    'cbxgooglemap').'</a>';

            $global_setting_link_html = '<a href="'.esc_url(admin_url('edit.php?post_type=cbxgooglemap&page=cbxgooglemap_settings')).'"  class="button outline primary">'.esc_html__('Global Settings', 'cbxgooglemap').'</a>';

            $tax_js_vars =
                [
                    'tax_title_prefix' => esc_html__('Map:', 'cbxgooglemap'),
                    'tags_title'       => esc_html__('Map: Tags', 'cbxgooglemap'),
                    'category_title'   => esc_html__('Map: Category', 'cbxgooglemap'),
                    'tax_new_setting'  => '<div class="wp-heading-wrap-right pull-right">'.$new_map_link_html.$global_setting_link_html.'</div>'
                ];

            $tax_js_vars = apply_filters('cbxgooglemap_tax_js_vars', array_merge($tax_js_vars, $global_translation));

            wp_register_script('cbxgooglemap-tax', $js_url_part.'cbxgooglemap-tax.js', $tax_js_deps, $ver, true);
            wp_localize_script('cbxgooglemap-tax', 'cbxgooglemap_tax', $tax_js_vars);


            //core
            wp_enqueue_script('jquery');

            //vendors
            //wp_enqueue_script( 'jquery-validate' );

            //custom
            wp_enqueue_script('cbxgooglemap-tax');
        }//end enqueue js for tax page
    }//end enqueue_scripts

    /**
     * Init all gutenberg blocks
     */
    public function gutenberg_blocks(){
        if ( ! function_exists('register_block_type')) {
            return;
        }

        $settings  = $this->settings;
        $in_footer = [
            'in_footer' => true,
        ];


        //$general_settings = get_option( 'cbxgooglemap_general', [] );

        //default field values
        $zoom_default = $settings->get_field('zoom', 'cbxgooglemap_general', '8');
        if ($zoom_default == 0) {
            $zoom_default = 8;
        }

        $width_default = $settings->get_field('width', 'cbxgooglemap_general', '100%');
        if ($width_default == '' || $width_default == 0) {
            $width_default = '100%';
        }

        $height_default = absint($settings->get_field('height', 'cbxgooglemap_general', '300'));
        if ($height_default == 0) {
            $height_default = 300;
        }


        $scrollwheel_default = absint($settings->get_field('scrollwheel', 'cbxgooglemap_general', 1));
        $showinfo_default    = absint($settings->get_field('showinfo', 'cbxgooglemap_general', 1));
        $infow_open_default  = absint($settings->get_field('infow_open', 'cbxgooglemap_general', 1));
        $maptype_default     = esc_attr($settings->get_field('maptype', 'cbxgooglemap_general', 'roadmap'));

        $query = get_posts([
            'post_type'      => 'cbxgooglemap',
            'orderby'        => 'date',
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        ]);

        $googleMap_posts = [];

        $googleMap_posts[] = [
            'label' => esc_html__('Select Map', 'cbxgooglemap'),
            'value' => '0'
        ];

        foreach ($query as $key => $data) {
            $googleMap_posts[] = [
                'label' => esc_html($data->post_title),
                'value' => absint($data->ID)
            ];
        }


        wp_register_script('cbxgooglemap-block', plugin_dir_url(__FILE__).'../assets/js/blocks/cbxgooglemap-block.js', [
            'wp-blocks',
            'wp-element',
            'wp-components',
            'wp-editor',
        ], filemtime(plugin_dir_path(__FILE__).'../assets/js/blocks/cbxgooglemap-block.js'), $in_footer);

        wp_register_style('cbxgooglemap-block', plugin_dir_url(__FILE__).'../assets/css/cbxgooglemap-block.css', [], filemtime(plugin_dir_path(__FILE__).'../assets/css/cbxgooglemap-block.css'));

        $js_vars = apply_filters('cbxgooglemap_block_js_vars',
            [
                'block_title'      => esc_html__('CBX Map: Location Map', 'cbxgooglemap'),
                'block_category'   => 'codeboxr',
                'block_icon'       => 'universal-access-alt',
                'general_settings' => [
                    'title'                 => esc_html__('CBX Map Settings', 'cbxgooglemap'),
                    'id'                    => esc_html__('Select Map', 'cbxgooglemap'),
                    'id_default'            => '',
                    'id_options'            => $googleMap_posts,
                    'id_note'               => esc_html__('Choose saved map or create from custom attributes below', 'cbxgooglemap'),
                    'custom_attribute_note' => esc_html__('Custom Map Attributes', 'cbxgooglemap'),
                    'maptype'               => esc_html__('Map Type(Google Map Only)', 'cbxgooglemap'),
                    'maptype_options'       => CBXGooglemapHelper::maptype_block_options(),
                    'lat'                   => esc_html__('Latitude', 'cbxgooglemap'),
                    'lng'                   => esc_html__('Longitude', 'cbxgooglemap'),
                    'width'                 => esc_html__('Width(Numeric, % allowed)', 'cbxgooglemap'),
                    'height'                => esc_html__('Height(only Numeric)', 'cbxgooglemap'),
                    'zoom'                  => esc_html__('zoom', 'cbxgooglemap'),
                    'scrollwheel'           => esc_html__('Mouse Scroll Wheel', 'cbxgooglemap'),
                    'showinfo'              => esc_html__('Show Popup', 'cbxgooglemap'),
                    'infow_open'            => esc_html__('Popup Auto Display ', 'cbxgooglemap'),
                    'heading'               => esc_html__('Popup Heading', 'cbxgooglemap'),
                    'address'               => esc_html__('Location Address', 'cbxgooglemap'),
                    'website'               => esc_html__('Website url', 'cbxgooglemap'),
                    'mapicon'               => esc_html__('Map Icon', 'cbxgooglemap'),
                    'mapicon_select'        => esc_html__('Select image', 'cbxgooglemap'),
                ],
            ]);

        wp_localize_script('cbxgooglemap-block', 'cbxgooglemap_block', $js_vars);

        register_block_type('codeboxr/cbxgooglemap', [
            'editor_script'   => 'cbxgooglemap-block',
            'editor_style'    => 'cbxgooglemap-block',
            'attributes'      => apply_filters('cbxgooglemap_block_attributes', [
                //general
                'id'      => [
                    'type'    => 'integer',
                    'default' => '0',
                ],
                'maptype' => [
                    'type'    => 'string',
                    'default' => $maptype_default
                ],
                'lat'     => [
                    'type'    => 'string',
                    'default' => ''
                ],
                'lng'     => [
                    'type'    => 'string',
                    'default' => ''
                ],
                'width'   => [
                    'type'    => 'string',
                    'default' => $width_default,
                ],

                'height'      => [
                    'type'    => 'integer',
                    'default' => $height_default
                ],
                'zoom'        => [
                    'type'    => 'string',
                    'default' => $zoom_default
                ],
                'scrollwheel' => [
                    'type'    => 'boolean',
                    'default' => ($scrollwheel_default) ? true : false
                ],
                'showinfo'    => [
                    'type'    => 'boolean',
                    'default' => ($showinfo_default) ? true : false
                ],
                'infow_open'  => [
                    'type'    => 'boolean',
                    'default' => ($infow_open_default) ? true : false
                ],
                'heading'     => [
                    'type'    => 'string',
                    'default' => ''
                ],
                'address'     => [
                    'type'    => 'string',
                    'default' => ''
                ],
                'website'     => [
                    'type'    => 'string',
                    'default' => ''
                ],
                'mapicon'     => [
                    'type'    => 'string',
                    'default' => '',
                ]
            ]),
            'render_callback' => [$this, 'render_block']
        ]);

    }//end gutenberg_blocks

    /**
     * Getenberg server side render
     *
     * @param $settings
     *
     * @return string
     */
    public function render_block($attributes)
    {
        $settings = $this->settings;
        //$general_settings = get_option( 'cbxgooglemap_general', [] );

        $api_key    = $settings->get_field('apikey', 'cbxgooglemap_general', '');
        $map_source = (int) $settings->get_field( 'mapsource', 'cbxgooglemap_general', 1 );

        if ($map_source == 1 && $api_key == '') {
            echo '<p style="text-align: center;">'.esc_html__('Google Map Api Key is invalid!', 'cbxgooglemap').'</p>';
        } else {
            $id = isset($attributes['id']) ? absint($attributes['id']) : 0;

            if ($id > 0) {
                //render map from saved map
                return '[cbxgooglemap id="'.$id.'"]';
            } else {
                $attr = [];

                if (isset($attributes['lat'])) {
                    $attr['lat'] = sanitize_text_field(wp_unslash($attributes['lat']));
                }

                if (isset($attributes['lng'])) {
                    $attr['lng'] = sanitize_text_field(wp_unslash($attributes['lng']));
                }

                if (isset($attributes['width'])) {
                    $attr['width'] = sanitize_text_field($attributes['width']);
                }

                if (isset($attributes['height'])) {
                    $attr['height'] = (int) $attributes['height'];
                }

                if (isset($attributes['zoom'])) {
                    $attr['zoom'] = sanitize_text_field($attributes['zoom']);
                }


                $attr['scrollwheel'] = ! empty($attributes['scrollwheel']) ? 1 : 0;
                $attr['infow_open']  = ! empty($attributes['infow_open']) ? 1 : 0;
                $attr['showinfo']    = ! empty($attributes['showinfo']) ? 1 : 0;

                if (isset($attributes['heading'])) {
                    $attr['heading'] = sanitize_text_field(wp_unslash($attributes['heading']));
                }
                if (isset($attributes['address'])) {
                    $attr['address'] = sanitize_text_field(wp_unslash($attributes['address']));
                }
                if (isset($attributes['website'])) {
                    $attr['website'] = sanitize_text_field(wp_unslash($attributes['website']));
                }
                if (isset($attributes['maptype'])) {
                    $attr['maptype'] = sanitize_text_field(wp_unslash($attributes['maptype']));
                }
                if (isset($attributes['mapicon'])) {
                    $attr['mapicon'] = esc_url($attributes['mapicon']);
                }

                $attr = apply_filters('cbxgooglemap_block_shortcode_builder_attr', $attr, $attributes);

                $attr_html = '';

                foreach ($attr as $key => $value) {
                    $attr_html .= ' '.$key.'="'.$value.'" ';
                }

                //return do_shortcode( '[cbxgooglemap ' . $attr_html . ']' );
                return '[cbxgooglemap '.$attr_html.']';
            }
        }//end if api keys are ok

    }//end codeboxrflexiblecountdown_block_render

    /**
     * Register New Gutenberg block Category if need
     *
     * @param $categories
     * @param $post
     *
     * @return mixed
     */
    public function gutenberg_block_categories($categories, $post)
    {
        $found = false;
        foreach ($categories as $category) {
            if ($category['slug'] == 'codeboxr') {
                $found = true;
                break;
            }
        }

        if ( ! $found) {
            return array_merge(
                $categories,
                [
                    [
                        'slug'  => 'codeboxr',
                        'title' => esc_html__('CBX Blocks', 'cbxgooglemap'),
                    ],
                ]
            );
        }

        return $categories;
    }//end gutenberg_block_categories


    /**
     * Enqueue style for block editor
     */
    public function enqueue_block_editor_assets()
    {
    }//end enqueue_block_editor_assets

    /**
     * Load setting html
     *
     * @return void
     */
    public function settings_reset_load()
    {
        //security check
        check_ajax_referer('cbxgooglemap_nonce', 'security');

        $msg            = [];
        $msg['html']    = '';
        $msg['message'] = esc_html__('CBX Google Map reset setting html loaded successfully', 'cbxgooglemap');
        $msg['success'] = 1;

        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxgooglemap');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        $msg['html'] = CBXGooglemapHelper::setting_reset_html_table();

        wp_send_json($msg);
    } //end method settings_reset_load

    /**
     * Full plugin reset and redirect
     */
    public function plugin_options_reset()
    {

        //security check
        check_ajax_referer('cbxgooglemap_nonce', 'security');

        $url = admin_url('admin.php?page=cbxgooglemap_settings');

        $msg            = [];
        $msg['message'] = esc_html__('CBX Google Map setting options reset successfully', 'cbxgooglemap');
        $msg['success'] = 1;
        $msg['url']     = $url;

        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxgooglemap');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        do_action('cbxgooglemap_plugin_reset_before');

        $plugin_resets = wp_unslash($_POST); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

        //delete options
        $reset_options = isset($plugin_resets['reset_options']) ? $plugin_resets['reset_options'] : [];
        $option_values = (is_array($reset_options) && sizeof($reset_options) > 0) ? array_values($reset_options) : array_values(CBXGooglemapHelper::getAllOptionNames());

        foreach ($option_values as $key => $option) {
            delete_option($option);
        }

        do_action('cbxgooglemap_plugin_option_delete');
        do_action('cbxgooglemap_plugin_reset_after');
        do_action('cbxgooglemap_plugin_reset');

        wp_send_json($msg);
    } //end plugin_reset

    /**
     * Export plugin global settings
     *
     * @return void
     */
    public function settings_export()
    {

        if (isset($_GET['cbxgooglemap_settings_export'])) {
            $url = admin_url('admin.php?page=cbxgooglemap_settings');

            $msg            = [];
            $msg['message'] = esc_html__('Google Map setting exported successfully', 'cbxgooglemap');
            $msg['success'] = 1;
            $msg['url']     = $url;

            $passed = true;

            $nonce = isset($_GET['security']) ? sanitize_text_field(wp_unslash($_GET['security'])) : '';
            if ( ! wp_verify_nonce($nonce, 'cbxgooglemap_settings_nonce')) {
                $passed         = false;
                $msg['success'] = 0;
                $msg['message'] = esc_html__('Security check failed', 'cbxgooglemap');
            }

            if ( ! current_user_can('manage_options')) {
                $passed         = true;
                $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxgooglemap');
                $msg['success'] = 0;
            }

            if ( ! $passed) {
                wp_send_json($msg);
            }

            // Get Meta box data
            $sections = CBXGoogleMapAdmin::get_settings_sections();

            $data = [
                'cbxgooglemap_settings' => true
            ];

            $single_section = isset($_REQUEST['section']) ? sanitize_text_field(wp_unslash($_REQUEST['section'])) : '';

            foreach ($sections as $section) {
                if ($single_section != '' && $section['id'] != $single_section) {
                    continue;
                }


                $sections_fields = get_option($section['id']);
                if ( ! is_array($sections_fields)) {
                    $sections_fields = [];
                }

                $data[$section['id']] = $sections_fields;

                if ($single_section != '' && $section['id'] == $single_section) {
                    break;
                }
            }

            // Create JSON
            $generate_json = wp_json_encode($data, JSON_PRETTY_PRINT);

            // Create filename
            $filename = 'cbxgooglemap_settings_json';

            // Force download .json file with JSON in it
            header('Content-type: application/vnd.ms-excel');
            header('Content-Type: application/force-download');
            header('Content-Type: application/download');
            header('Content-disposition: '.$filename.'.json');
            header('Content-disposition: filename='.$filename.'.json');

            print $generate_json;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            exit;
        }//end if export mode
    }//end method settings_export

    /**
     * Import settings
     *
     * @return void
     */
    public function settings_import()
    {
        //security check
        check_ajax_referer('cbxgooglemap_nonce', 'security');

        $msg            = [];
        $msg['message'] = esc_html__('CBX Google Map Settings imported successfully', 'cbxgooglemap');
        $msg['success'] = 1;

        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxgooglemap');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        // Import settings api data
        $settings_data = isset($_REQUEST['settings_data']) ? sanitize_text_field(wp_unslash($_REQUEST['settings_data'])) : '';
        $settings_data = str_replace('data:application/json;base64,', '', $settings_data);
        $settings_data = base64_decode($settings_data);

        $settings_data = json_decode($settings_data, true);
        // Redirect to settings page
        $msg['url'] = admin_url('admin.php?page=cbxgooglemap_settings');

        if (isset($settings_data['cbxgooglemap_settings'])) {
            unset($settings_data['cbxgooglemap_settings']);

            if (is_array($settings_data)) {
                foreach ($settings_data as $key => $value) {
                    update_option($key, $value);
                }
            }
            wp_send_json($msg);
        }

        $msg['message'] = esc_html__('Sorry, wrong format data', 'cbxgooglemap');
        $msg['success'] = 0;
        wp_send_json($msg);
    }//end method settings_import

    /**
     * Reset single section
     *
     * @return void
     */
    public function plugin_reset_section()
    {

        //security check
        check_ajax_referer('cbxgooglemap_nonce', 'security');

        $url = admin_url('admin.php?page=cbxgooglemap_settings');

        $msg            = [];
        $msg['message'] = esc_html__('Comfort Google Map setting section reset successfully', 'cbxgooglemap');
        $msg['success'] = 1;
        $msg['url']     = $url;

        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxgooglemap');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        $section = isset($_POST['section']) ? sanitize_text_field(wp_unslash($_POST['section'])) : '';
        if ($section == '') {
            $msg['message'] = esc_html__('Option section setting failed, as no section id is empty', 'cbxgooglemap');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        //before hooks
        do_action('cbxgooglemap_plugin_reset_section_before');


        //delete the section
        delete_option($section);

        //after hoooks
        do_action('cbxgooglemap_plugin_reset_section_after');
        do_action('cbxgooglemap_plugin_reset_section');

        wp_send_json($msg);
    }//end method plugin_reset_section
}//end class CBXGoogleMapAdmin