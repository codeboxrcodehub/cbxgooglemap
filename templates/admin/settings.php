<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<?php
$save_svg   = cbxgooglemap_esc_svg( cbxgooglemap_load_svg( 'icon_save' ) );
$more_v_svg = cbxgooglemap_esc_svg( cbxgooglemap_load_svg( 'icon_more_v' ) );
$import_svg = cbxgooglemap_esc_svg( cbxgooglemap_load_svg( 'icon_import' ) );
$export_svg = cbxgooglemap_esc_svg( cbxgooglemap_load_svg( 'icon_export' ) );

?>
<div class="wrap cbx-chota cbxchota-setting-common cbxgooglemap-page-wrapper cbxgooglemap-setting-wrapper"
     id="cbxgooglemap-setting">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2></h2>
				<?php do_action( 'cbxgooglemap_wpheading_wrap_before', 'settings' ); ?>
                <div class="wp-heading-wrap">
                    <div class="wp-heading-wrap-left pull-left">
						<?php do_action( 'cbxgooglemap_wpheading_wrap_left_before', 'settings' ); ?>
                        <h1 class="wp-heading-inline wp-heading-inline-cbxgooglemap">
							<?php esc_html_e( 'CBX Map: Global Settings', 'cbxgooglemap' ); ?>
                        </h1>
						<?php do_action( 'cbxgooglemap_wpheading_wrap_left_before', 'settings' ); ?>
                    </div>
                    <div class="wp-heading-wrap-right  pull-right">
						<?php do_action( 'cbxgooglemap_wpheading_wrap_right_before', 'settings' ); ?>
                        <button class="button secondary"
                                id="cbx-export-import"><?php esc_html_e( 'Export/Import', 'cbxgooglemap' ) ?></button>
                        <a href="<?php echo esc_url(admin_url( 'edit.php?post_type=cbxgooglemap&page=cbxgooglemap_support' )); ?>"
                           class="button outline primary"><?php esc_html_e( 'Support & Docs', 'cbxgooglemap' ); ?></a>
                        <a href="#" id="save_settings"
                           class="button primary icon icon-inline icon-right mr-5">
                            <i class="cbx-icon"><?php echo $save_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></i>
                            <span class="button-label"><?php esc_html_e( 'Save Settings', 'cbxgooglemap' ); ?></span>
                        </a>
						<?php do_action( 'cbxgooglemap_wpheading_wrap_right_after', 'settings' ); ?>
                    </div>
                </div>
				<?php do_action( 'cbxgooglemap_wpheading_wrap_after', 'settings' ); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
				<?php do_action( 'cbxgooglemap_settings_form_before', 'settings' );

                $security = wp_create_nonce( 'cbxgooglemap_settings_nonce' );

                $settings_export_url = add_query_arg( [
                'cbxgooglemap_settings_export' => 1,
                'security'                     => $security,
                ], site_url() );
                ?>

                <div class="cbx-export-import-wrapper">
                    <div class="inside cbx-export-import-inner">
                        <h2><?php esc_html_e( 'Export/Import', 'cbxgooglemap' ); ?></h2>
                        <div class="cbx-export-import-button-wrap">
                            <a href="<?php echo esc_url( $settings_export_url ); ?>" class="button secondary icon icon-right icon-inline"
                               id="cbx-export">
                                <i class="cbx-icon"><?php echo $export_svg;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></i>
                                <span class="button-label"><?php esc_html_e( 'Export', 'cbxgooglemap' ) ?></span>
                            </a>
                            <a class="button primary  icon icon-right icon-inline" id="cbxgooglemap_settings_import">
                                <i class="cbx-icon"><?php echo $import_svg;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></i>
                                <span class="button-label"><?php esc_html_e( 'Import', 'cbxgooglemap' ) ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="postbox">
                    <div class="clear clearfix"></div>
                    <div class="inside setting-form-wrap">
                        <div class="clear clearfix"></div>
						<?php do_action( 'cbxgooglemap_settings_form_start', 'settings' ); ?>
						<?php
						settings_errors();

						$settings->show_navigation();
						$settings->show_forms();
						?>
						<?php do_action( 'cbxgooglemap_settings_form_end', 'settings' ); ?>
                        <div class="clear clearfix"></div>
                    </div>
                    <div class="clear clearfix"></div>
                </div>
				<?php do_action( 'cbxgooglemap_settings_form_after', 'settings' ); ?>
            </div>
        </div>
    </div>
</div>