<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Notices
 * @category Alerts
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2014.10.17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_Notices {

	private $messages = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->hooks();
	}


	/**
	 * Hooks.
	 *
	 * @return void
	 */
	private function hooks() {

		// Define Messages.
		add_action( 'init', array( $this, 'set_messages' ) );

		// Where to show messages ?
		add_action( 'wpbc_hook_booking_page_header', array( $this, 'show_system_messages' ) );
		add_action( 'wpbc_hook_add_booking_page_header', array( $this, 'show_system_messages' ) );
		add_action( 'wpbc_hook_settings_page_header', array( $this, 'show_system_messages' ) );
	}


	/**
	 * Define messageas.
	 *
	 * @return void
	 */
	public function set_messages() {

		$this->messages['updated_paid_to_free'] = '<strong>' . esc_html__( 'Warning!', 'booking' ) . '</strong> ' .
			/* translators: 1: ... */
			sprintf( __( 'Probably you updated your paid version of Booking Calendar by free version or update process failed. You can request the new update of your paid version at %1$sthis page%2$s.', 'booking' ), '<a href="https://wpbookingcalendar.com/request-update/" target="_blank">', '</a>' );
	}


	/**
	 * Define secion for the system messages at the admin panel  and show System Messages.
	 *
	 * @param string $my_page - tag of the page.
	 */
	public function show_system_messages( $my_page ) {

		if (! empty($this->messages['updated_paid_to_free'])){

			?><div class="wpbc_admin_system_message wpbc_page_<?php echo esc_attr( $my_page ); ?>"><?php

				/** Static messages - user  need to click  for closing these messages */

			if ( wpbc_is_updated_paid_to_free() ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->get_formated_message( $this->messages['updated_paid_to_free'], 'updated error' );
			}

			?></div><?php
		}
    }



	private function get_formated_message( $message, $message_type = 'updated', $inner_message_id = '' ) {


		// Recheck  for any "lang" shortcodes for replacing to correct language.
		$message = wpbc_lang( $message );

		// Escape any JavaScript from  message.
		$notice = html_entity_decode( esc_js( $message ), ENT_QUOTES );

		if ( ! empty( $inner_message_id ) ) {
			$inner_message_id_attr = 'id="wpbc_inner_message_' . $inner_message_id . '"';
		} else {
			$inner_message_id_attr = '';
		}


		$notice_hide = '<a style="background: #fff;border-radius: 7px;margin: 10px 0 0 30px;float: right;font-weight: 600;text-decoration: underline;"  title="' . esc_js( __( "Hide", 'booking' ) ) . '"  href="javascript:void(0)" onclick="javascript:jQuery(this).parent().parent().fadeOut( 500 );">' . esc_html( __( 'Hide', 'booking' ) ) . '</a>';
		$wpbc_metabox_id = 'wpbc_message_update_free_to_paid';

		ob_start();
		$is_panel_visible = wpbc_is_dismissed( $wpbc_metabox_id, array(
			'title' => __( 'Dismiss', 'booking' ), //  ' <i class="menu_icon icon-1x wpbc_icn_close"></i> ',            // &times;.
			'hint'  => __( 'Dismiss Forever', 'booking' ),
			'class' => 'wpbc_message_update_free_to_paid',
			'css'   => 'background: #fff;border-radius: 7px;margin: 10px 0 0 30px;text-decoration: underline;',
		) );
		?>
		<script type="text/javascript">
			jQuery( '#<?php echo esc_js( $wpbc_metabox_id ); ?> .wpbc_message_update_free_to_paid' ).on( 'click', function (event) {
				jQuery( '#<?php echo esc_attr( $wpbc_metabox_id ); ?>' ).parent().fadeOut(500);
			} );
		</script>
		<?php
		$dismiss_button_content = ob_get_clean();

		if ( $is_panel_visible ) {

			$notice = '<div ' . $inner_message_id_attr . ' class="wpbc_inner_message ' . $message_type . '"><div id="' . esc_attr( $wpbc_metabox_id ) . '">' .
					  	$dismiss_button_content .
					  	$notice_hide .
					  	$notice .
					  '</div></div>';
		} else {
			$notice = '';
		}

        return  $notice;
    }
}

