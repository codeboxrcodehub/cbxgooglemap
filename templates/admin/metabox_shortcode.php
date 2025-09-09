<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://codeboxr.com
 * @since      1.0.0
 *
 * @package    cbxgooglemap
 * @subpackage cbxgooglemap/templates/admin
 */

echo '<div class="cbxshortcode-wrap">';
echo '<span data-clipboard-text=\'[cbxgooglemap id="' . absint( $post_id ) . '"]\' title="' . esc_html__( 'Click to clipboard', 'cbxgooglemap' ) . '" id="cbxgooglemapshortcode-' . absint( $post_id ) . '" class="cbxshortcode cbxshortcode-edit cbxshortcode-' . absint( $post_id ) . '">[cbxgooglemap id="' . absint( $post_id ) . '"]</span>';
echo '<span class="cbxballon_ctp_btn cbxballon_ctp" aria-label="' . esc_html__( 'Click to copy', 'cbxgooglemap' ) . '" data-balloon-pos="up"><i></i></span>';
echo '</div>';