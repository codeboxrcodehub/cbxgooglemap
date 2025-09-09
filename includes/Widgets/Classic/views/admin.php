<?php
/**
 * Provide a admin widget view for the plugin
 *
 * This file is used to markup the admin facing widget form
 *
 * @link       https://codeboxr.com
 * @since      1.1.7
 *
 * @package    cbxgooglemap
 * @subpackage cbxgooglemap/Widgets/Classic/views
 */

use Cbx\Googlemap\Helpers\CBXGooglemapHelper;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<?php
do_action( 'cbxgooglemap_form_before_admin', $instance, $this );
?>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>">
			<?php esc_html_e( 'Title', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>"/> </label>
    </p>

    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'map_id' )); ?>">
			<?php esc_html_e( 'Predefined Map', 'cbxgooglemap' ); ?>
        </label>
		<?php
		$query = get_posts( [
			'post_type'      => 'cbxgooglemap',
			'orderby'        => 'date',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		] );

		?>
        <select class="widefat" name="<?php echo esc_attr($this->get_field_name( 'map_id' )); ?>"
                id="<?php echo esc_attr($this->get_field_id( 'map_id' )); ?>">
            <option value="0"><?php esc_html_e( 'Choose Ready Map or Use custom params', 'cbxgooglemap' ); ?></option>
			<?php
			foreach ( $query as $post ) :
				CBXGooglemapHelper::setup_admin_postdata( $post );
				$post_id    = intval( get_the_ID() );
				$post_title = get_the_title();

				echo '<option ' . selected( $post_id, $map_id, false ) . ' value="' . intval( $post_id ) . '">' . esc_attr( $post_title ) . '</option>';

			endforeach;
			CBXGooglemapHelper::wp_reset_admin_postdata();
			?>
        </select>
    </p>

    <p><h3><?php esc_html_e( 'Custom Map Attributes', 'cbxgooglemap' ) ?></h3></p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'maptype' )); ?>"><?php esc_html_e( 'Map Type:', 'cbxgooglemap' ); ?>
            <select name="<?php echo esc_attr($this->get_field_name( 'maptype' )); ?>"
                    id="<?php echo esc_attr($this->get_field_id( 'maptype' )); ?>" class="widefat">
                <option value="roadmap" <?php selected( $maptype, 'roadmap' ); ?>><?php esc_html_e( 'Road Map', 'cbxgooglemap' ); ?></option>
                <option value="satellite" <?php selected( $maptype, 'satellite' ); ?>><?php esc_html_e( 'Satellite Map', 'cbxgooglemap' ); ?></option>
                <option value="hybrid" <?php selected( $maptype, 'hybrid' ); ?>><?php esc_html_e( 'Hybrid Map', 'cbxgooglemap' ); ?></option>
                <option value="terrain" <?php selected( $maptype, 'terrain' ); ?>><?php esc_html_e( 'Terrain Map', 'cbxgooglemap' ); ?></option>
            </select> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'lat' )); ?>">
			<?php esc_html_e( 'Latitude', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'lat' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'lat' )); ?>" type="text"
                   value="<?php echo esc_attr( $lat ); ?>"/> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'lng' )); ?>">
			<?php esc_html_e( 'Longitude', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'lng' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'lng' )); ?>" type="text"
                   value="<?php echo esc_attr( $lng ); ?>"/> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'width' )); ?>">
			<?php esc_html_e( 'Width(Numeric or with %)', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'width' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'width' )); ?>" type="text"
                   value="<?php echo esc_attr( $width ); ?>"/> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'height' )); ?>">
			<?php esc_html_e( 'Height', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'height' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'height' )); ?>" type="text"
                   value="<?php echo esc_attr( $height ); ?>"/> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'zoom' )); ?>">
			<?php esc_html_e( 'Zoom(Numeric Value)', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'zoom' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'zoom' )); ?>" type="text"
                   value="<?php echo esc_attr( $zoom ); ?>"/> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'scrollwheel' )); ?>"><?php esc_html_e( 'Mouse Scroll Wheel', 'cbxgooglemap' ); ?>
            <select name="<?php echo esc_attr($this->get_field_name( 'scrollwheel' )); ?>"
                    id="<?php echo esc_attr($this->get_field_id( 'scrollwheel' )); ?>" class="widefat">
                <option value="1" <?php selected( $scrollwheel, '1' ); ?>><?php esc_html_e( 'Enable', 'cbxgooglemap' ); ?></option>
                <option value="0" <?php selected( $scrollwheel, '0' ); ?>><?php esc_html_e( 'Disable', 'cbxgooglemap' ); ?></option>
            </select> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'showinfo' )); ?>"><?php esc_html_e( 'Show Popup', 'cbxgooglemap' ); ?>
            <select name="<?php echo esc_attr($this->get_field_name( 'showinfo' )); ?>"
                    id="<?php echo esc_attr($this->get_field_id( 'showinfo' )); ?>" class="widefat">
                <option value="1" <?php selected( $showinfo, '1' ); ?>><?php esc_html_e( 'Enable', 'cbxgooglemap' ); ?></option>
                <option value="0" <?php selected( $showinfo, '0' ); ?>><?php esc_html_e( 'Disable', 'cbxgooglemap' ); ?></option>
            </select> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'infow_open' )); ?>"><?php esc_html_e( 'Show Popup', 'cbxgooglemap' ); ?>
            <select name="<?php echo esc_attr($this->get_field_name( 'infow_open' )); ?>"
                    id="<?php echo esc_attr($this->get_field_id( 'infow_open' )); ?>" class="widefat">
                <option value="1" <?php selected( $infow_open, '1' ); ?>><?php esc_html_e( 'Open(Default)', 'cbxgooglemap' ); ?></option>
                <option value="0" <?php selected( $infow_open, '0' ); ?>><?php esc_html_e( 'On Click', 'cbxgooglemap' ); ?></option>
            </select> </label>
    </p>
    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'heading' )); ?>">
			<?php esc_html_e( 'Popup Heading', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'heading' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'heading' )); ?>" type="text"
                   value="<?php echo esc_attr( $heading ); ?>"/> </label>
    </p>

    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'address' )); ?>">
			<?php esc_html_e( 'Address', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'address' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'address' )); ?>" type="text"
                   value="<?php echo esc_attr( $address ); ?>"/> </label>
    </p>

    <p>
        <label for="<?php echo esc_attr($this->get_field_id( 'website' )); ?>">
			<?php esc_html_e( 'Website url', 'cbxgooglemap' ); ?>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'website' )); ?>"
                   name="<?php echo esc_attr($this->get_field_name( 'website' )); ?>" type="url"
                   value="<?php echo esc_attr( $website ); ?>"/> </label>
    </p>

<?php
do_action( 'cbxgooglemap_form_after_admin', $instance, $this );