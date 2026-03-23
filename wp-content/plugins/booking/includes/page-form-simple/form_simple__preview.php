<?php
/**
 * @version 1.0
 * @package     Form Preview
 * @category    WP Booking Calendar > Settings > Booking Form page
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-06-02
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
* Show Preview of Booking form
* @return void
*/
function wpbc_show_preview__form(){

	wpbc_show_preview__css();

	?>
	<div class="wpbc_settings_flex_container">
		<!--div class="wpbc_settings_flex_container_left"></div-->
		<div class="wpbc_settings_flex_container_right">
			<div class="metabox-holder" style="margin-top:30px;"><?php

				wpbc_open_meta_box_section__preview( 'wpbc_settings__form_preview', __( 'Preview', 'booking' ), array(
					'is_section_visible_after_load' => true,
					'is_show_minimize'              => true,
					'css_class'                     => 'postbox wpbc_container_always_hide__on_left_nav_click000'
				) );

				wpbc_show_preview_help_notice();
				$booking_form_theme = get_bk_option( 'booking_form_theme' );

				// Center with  padding
				?><div class="wpbc_center_preview <?php echo esc_attr( $booking_form_theme ); ?>"><?php

					$resource_id = wpbc_get_default_resource();
					if ( ! empty( $resource_id ) ){
						echo do_shortcode( '[booking resource_id=' . $resource_id . ']' );
					}

				?></div><?php

				wpbc_close_meta_box_section();
				?>
			</div>
		</div>
	</div><?php
}


	/**
	 * CSS for Preview
	 *
	 * @return void
	 */
	function wpbc_show_preview__css(){

		?>
		<style type="text/css">
			.wpbc_container_booking_form {
				width:100%;
				margin:0;
			}
			.wpbc_center_preview {
				max-width: 80%;
				min-width:200px;

				margin-left: auto !important;
				margin-right: auto !important;
				box-shadow: 0 4px 19px #474747;
				border: 2px solid #272727;
				border-radius: 5px;
				/*resize: both;*/
				resize: horizontal;
				overflow: auto;
				padding: 20px 50px 40px;
			}
			@media (max-width: 782px) {
				.wpbc_center_preview {
					padding: 20px 20px;
				}
			}
			@media (max-width: 399px) {
				.wpbc_center_preview {
					padding: 20px 10px;
				}
			}
			.wpbc_center_preview.wpbc_theme_dark_1 {
				/*Black Color*/
				background: #272727;
				color:#fff;
			}
			.wpbc_section_preview_container {
				border-radius: 4px;
			}
			.wpbc_section_preview_header {
				display: flex;
				flex-flow: row nowrap;
				border-bottom: 1px solid #ccd0d4;
				background: #272727;
				color: #fff;
				box-shadow: 0 0 5px #868686;
				height: 50px;
				border-radius: 4px;
			}
			.wpbc_section_preview_header h3.hndle{
				flex: 0 1 auto;
				border: none;
				color: #fff;
				font-size: 16px;
				font-weight: 600;
			}
			.wpbc_section_preview_mobile_btn_bar{
				display: flex;
				flex-flow: row wrap;
				justify-content: space-between;
				align-items: center;
				width: auto;
			}
			.wpbc_section_preview_mobile_btn_bar .wpbc_preview_a{
				margin: 0 10px;
				text-decoration: none;
				outline: none;
				box-shadow: none;
				color: #999;
			}
			.wpbc_section_preview_mobile_btn_bar .wpbc_preview_a.active{
				color:#fff;
			}
			.wpbc_section_preview_mobile_btn_bar .wpbc_preview_a:hover,
			.wpbc_section_preview_mobile_btn_bar .wpbc_preview_a:focus {
				color:#fff;
				text-decoration: none;
				outline: none;
				box-shadow: none;
			}
			.wpbc_section_preview_mobile_btn_bar .wpbc_preview_icon::before{
				font-size: 22px;
			}
			.wpbc_section_preview_mobile_btn_bar .wpbc_preview_icon.wpbc-bi-display::before,
			.wpbc_section_preview_mobile_btn_bar .wpbc_preview_icon.wpbc_icn_computer::before{
				font-size: 28px;
			}
		</style>
		<?php
	}


	/**
	 * Different Dark  design  of "Meta box" section open tag
	 *
	 * @param string $metabox_id		HTML  ID of section
	 * @param string $title				Title in section
	 * @param array $params		[
									'is_section_visible_after_load' => true,			// Default true		-  is this section Visible,  after  page loading or hidden - useful  for Booking > Settings General page LEFT column sections
									'is_show_minimize'   			=> true 			// Default true		-  is show minimize button at  top right section
								]
	 *
	 * @return void
	 */
	function wpbc_open_meta_box_section__preview( $metabox_id, $title, $params = array() ) {

		$defaults = array(
							'is_section_visible_after_load' => true,
							'is_show_minimize'              => true,
							'dismiss_button'                => '',
							'css_class'                     => 'postbox'
					);
		$params   = wp_parse_args( $params, $defaults );

		$my_close_open_win_id = $metabox_id . '_metabox';

		?>
		<div class='meta-box'>
			<div	id="<?php echo esc_attr( $my_close_open_win_id ); ?>"
					class="wpbc_section_preview_container <?php echo esc_attr( $params['css_class'] ); ?> <?php
										if ( $params['is_show_minimize'] ) {
											if ( '1' == get_user_option( 'booking_win_' . $my_close_open_win_id ) ) {
												echo 'closed';
											}
										}
					?>"
					style="<?php if ( ! $params['is_section_visible_after_load'] ) { echo 'display:none'; } ?>" >
				<div class="wpbc_section_preview_header postbox-header">
					<h3 class='hndle'>
					  <span><?php  echo wp_kses_post( $title ); ?></span>
					  <?php
					  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					  echo $params['dismiss_button']; ?>
					</h3>
					<div class="wpbc_section_preview_mobile_btn_bar">
						<a class="wpbc_preview_a active" href="javascript:void(0)" onclick="javascript:jQuery( '.wpbc_center_preview').css({'max-width': '90%', 'width': 'auto'});jQuery('.wpbc_preview_a' ).removeClass('active');jQuery(this).addClass('active');">
							<i class="wpbc_preview_icon menu_icon icon-1x wpbc-bi-display _icn_computer"></i>
						</a>
						<a class="wpbc_preview_a"  href="javascript:void(0)" onclick="javascript:jQuery( '.wpbc_center_preview').css({'max-width': '750px', 'width': 'auto'});jQuery('.wpbc_preview_a' ).removeClass('active');jQuery(this).addClass('active');">
							<i class="wpbc_preview_icon menu_icon icon-1x wpbc-bi-tablet _icn_tablet_mac"></i>
						</a>
						<a class="wpbc_preview_a"  href="javascript:void(0)" onclick="javascript:jQuery( '.wpbc_center_preview').css({'max-width': '350px', 'width': 'auto'});jQuery('.wpbc_preview_a' ).removeClass('active');jQuery(this).addClass('active');">
							<i class="wpbc_preview_icon menu_icon icon-1x wpbc-bi-phone _icn_phone_iphone"></i>
						</a>
					</div>
					<?php if ( $params['is_show_minimize'] ) { ?>
					<div  	title="<?php esc_attr_e('Click to toggle','booking'); ?>" class="handlediv"
							onclick="javascript:wpbc_verify_window_opening(<?php echo esc_attr( wpbc_get_current_user_id() ); ?>, '<?php echo esc_attr( $my_close_open_win_id ); ?>');"
					><br/></div>
					<?php } ?>
				</div>
				<div class="inside">
		<?php
	}


/**
 * Show Help notice to  Save changes
 *
 * @return void
 */
function wpbc_show_preview_help_notice(){

	$is_panel_visible = wpbc_is_dismissed_panel_visible( 'wpbc_show_preview_help_notice_id' );        // FixIn: 9.9.0.8.
	if ( $is_panel_visible ) {
		?><div id="wpbc_show_preview_help_notice_id" class="wpbc-settings-notice notice-warning notice-helpful-info"
			   style="max-width: Min(400px, 100%);margin: auto;padding: 4px 20px 7px;font-size: 14px;line-height: 28px;margin-bottom: 25px;"
		   >
			   <a style="margin: 0 7px 0 0;" href="javascript:void(0)" onclick="javascript:jQuery('.wpbc_submit_button_trigger').trigger('click');"
					data-original-title="<?php echo esc_attr(__('Update','booking')); ?>" class="tooltip_top "><i class="menu_icon icon-1x wpbc_icn_rotate_right wpbc_spin wpbc_animation_pause"></i><span></span></a>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.MissingTranslatorsComment
			echo sprintf( __( 'To update preview, please click on %1$sSave Changes%2$s button.', 'booking' ),
				'<a href="javascript:void(0)" onclick="javascript:jQuery(\'.wpbc_submit_button_trigger\').trigger(\'click\');" style="font-weight:600;text-underline-offset: 3px;text-decoration-thickness: 0px;text-decoration-style: dashed;">',
				'</a>' );

 			$is_panel_visible = wpbc_is_dismissed( 'wpbc_show_preview_help_notice_id', array(
											'title' => '<i class="menu_icon icon-1x wpbc_icn_close"></i> ',
											'hint'  => __( 'Dismiss', 'booking' ),
											'class' => 'wpbc_panel_get_started_dismiss',
											'css'   => 'background: #fff;border-radius: 7px;'
										));

	 	?></div><?php
	}
}

