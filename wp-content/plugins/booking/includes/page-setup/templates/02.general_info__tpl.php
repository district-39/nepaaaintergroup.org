<?php /**
 * @version 1.0
 * @description Template   for Setup pages
 * @category    Setup Templates
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-09-09
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Main - General Info ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - General Info - Step 01
 *
 * 	Help Tips:
 *
 *		<script type="text/html" id="tmpl-template_name_a">
 * 			Escaped:  	 {{data.test_key}}
 * 			HTML:  		{{{data.test_key}}}
 * 			JS: 	  	<# if (true) { alert( 1 ); } #>
 * 		</script>
 *
 * 		var template__var = wp.template( 'template_name_a' );
 *
 * 		jQuery( '.content' ).html( template__var( { 'test_key' => '<strong>Data</strong>' } ) );
 *
 * @return void
 */
function wpbc_stp_wiz__template__general_info(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__general_info">
	<div class="wpbc_page_main_section    wpbc_container    wpbc_form    wpbc_container_booking_form">
		<div class="wpbc__form__div wpbc_swp_section wpbc_swp_section__general_info">
			<div class="wpbc__row">
				<div class="wpbc__field">
					<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'Tell Us About Your Business', 'booking' ); ?></h1>
					<p class="wpbc_swp_section_header_description"><?php esc_html_e('This will help customize your experience.','booking'); ?></p>
				</div>
			</div>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<label><?php esc_html_e('What\'s the name of your business?','booking'); ?></label><br>
					<input type="text" name="wpbc_swp_business_name"  size="40"
						   value="<?php echo esc_attr( get_option( 'blogname' ) ); ?>"
						   placeholder="<?php echo esc_attr( __( 'Example', 'booking' ) ) . ': ' . esc_attr( get_option( 'blogname' ) ); ?>"
					/>
				</div>
				<div class="wpbc__field">
					<label><?php esc_html_e('Which one of these best describes you?','booking'); ?></label><br>
					<select name="wpbc_swp_booking_who_setup" onchange="javascript:console.log( this );" >
						<option value="starting"><?php esc_html_e('I\'m just starting my business','booking'); ?></option>
						<option value="in_business"><?php esc_html_e('I\'m already in business','booking'); ?></option>
						<option value="setup_for_client"><?php esc_html_e('I\'m setting up a plugin for a client','booking'); ?></option>
					</select>
				</div>
			</div>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<label><?php esc_html_e('What industry is your booking business in?','booking'); ?></label><br>
					<select name="wpbc_swp_industry">
						<option value="---">---</option>

						<optgroup label="Hospitality & Property">
							<option value="property_rentals">Property Rentals</option>
							<option value="hotels">Hotels</option>
							<option value="resorts">Resorts</option>
							<option value="rentals">Vacation Rentals</option>
							<option value="coworking_spaces">Coworking Spaces</option>
							<option value="other_hospitality">Other</option>
						</optgroup>

						<optgroup label="Transportation & Activities">
							<option value="car_rentals">Car Rentals</option>
							<option value="shuttles">Shuttle Services</option>
							<option value="limousines">Limousines</option>
							<option value="tour_operators">Tour Operators</option>
							<option value="adventure_parks">Adventure Parks</option>
							<option value="other_transportation">Other</option>
						</optgroup>

						<optgroup label="Events & Entertainment">
							<option value="event_venues">Event Venues</option>
							<option value="meeting_rooms">Meeting Rooms</option>
							<option value="escape_rooms">Escape Rooms</option>
							<option value="other_events">Other</option>
						</optgroup>

						<optgroup label="Health & Wellness">
							<option value="spas">Spas</option>
							<option value="salons">Salons</option>
							<option value="clinics">Clinics</option>
							<option value="massage_therapy">Massage Therapy</option>
							<option value="other_health">Other</option>
						</optgroup>

						<optgroup label="Healthcare">
							<option value="doctors">Doctors</option>
							<option value="dentists">Dentists</option>
							<option value="therapists">Therapists</option>
							<option value="chiropractors">Chiropractors</option>
							<option value="acupuncturists">Acupuncturists</option>
							<option value="other_healthcare">Other</option>
						</optgroup>

						<optgroup label="Professional Services">
							<option value="consultants">Consultants</option>
							<option value="lawyers">Lawyers</option>
							<option value="accountants">Accountants</option>
							<option value="financial_advisors">Financial Advisors</option>
							<option value="other_professional">Other</option>
						</optgroup>

						<optgroup label="Retail & E-Commerce">
							<option value="product_demos">Product Demonstrations</option>
							<option value="fitting_appointments">Fitting Appointments</option>
							<option value="equipment_rentals">Equipment Rentals</option>
							<option value="other_retail">Other</option>
						</optgroup>

						<optgroup label="Education & Training">
							<option value="tutors">Tutors</option>
							<option value="classes">Classes</option>
							<option value="schools">Schools</option>
							<option value="workshops">Workshops</option>
							<option value="other_education">Other</option>
						</optgroup>

						<optgroup label="Food & Restaurants">
							<option value="table_reservations">Table Reservations</option>
							<option value="catering">Catering</option>
							<option value="banquet_halls">Banquet Halls</option>
							<option value="other_food">Other</option>
						</optgroup>

						<optgroup label="Creative & Technical Services">
							<option value="photographers">Photographers</option>
							<option value="videographers">Videographers</option>
							<option value="web_developers">Web Developers</option>
							<option value="it_support">IT Support</option>
							<option value="art_studios">Art Studios</option>
							<option value="music_lessons">Music Lessons</option>
							<option value="other_creative">Other</option>
						</optgroup>

						<optgroup label="Legal Services">
							<option value="attorneys">Attorneys</option>
							<option value="legal_consultants">Legal Consultants</option>
							<option value="other_legal">Other</option>
						</optgroup>

						<optgroup label="Childcare & Home Services">
							<option value="daycares">Daycares</option>
							<option value="babysitting">Babysitting</option>
							<option value="nanny_services">Nanny Services</option>
							<option value="cleaners">Cleaners</option>
							<option value="repairs">Repair Services</option>
							<option value="handyman">Handyman</option>
							<option value="other_childcare">Other</option>
						</optgroup>

						<optgroup label="Sports & Fitness">
							<option value="gyms">Gyms</option>
							<option value="fitness_classes">Fitness Classes</option>
							<option value="yoga_studios">Yoga Studios</option>
							<option value="golf_lessons">Golf Lessons</option>
							<option value="sports_equipment_rentals">Sports Equipment Rentals</option>
							<option value="other_sports">Other</option>
						</optgroup>

						<optgroup label="Pet Services">
							<option value="veterinarians">Veterinarians</option>
							<option value="groomers">Pet Groomers</option>
							<option value="pet_sitting">Pet Sitting</option>
							<option value="other_pet">Other</option>
						</optgroup>

						<optgroup label="Personal Services">
							<option value="coaching">Coaching</option>
							<option value="counseling">Counseling</option>
							<option value="spiritual_services">Spiritual Services</option>
							<option value="interior_design">Interior Design</option>
							<option value="other_personal">Other</option>
						</optgroup>

						<optgroup label="Other">
							<option value="other">Other</option>
						</optgroup>
					</select>
				</div>
				<div class="wpbc__field">
					<label><?php esc_html_e('Your Booking Email Address','booking'); ?></label><br>
					<input type="text" name="wpbc_swp_email"  size="40"
						   value="<?php echo   esc_attr( get_option( 'admin_email' ) ); ?>"
						   placeholder="<?php echo esc_attr( __( 'Example', 'booking' ) ) . ': ' . esc_attr( get_option( 'admin_email' ) ); ?>"
					/>
					<span style="font-size:12px;"><?php esc_html_e('You can always change this later','booking'); ?>!</span>
				</div>
			</div>
			<!--div class="wpbc__row">
				<div class="wpbc__field"> <label>Details:</label><div class="wpbc__spacer" style="width:100%;clear:both;"></div>
					<span class="wpbc_wrap_textarea wpdev-form-control-wrap details4"><textarea name="details4" autocomplete="details"></textarea></span> </div>
			</div-->
			<div class="wpbc__spacer" style="width:100%;clear:both;height:40px;margin-bottom:5px;border-bottom:1px solid #ccc;"></div>
			<div class="wpbc__row wpbc_setup_wizard_page__section_footer">
				<div class="wpbc__field">
					<span class="wpdev-list-item">
						<input 	type="checkbox"
								style="margin-top: 0px;"
							   	class="wpdev-validates-as-required wpdev-checkbox"
							   	id="wpbc_swp_accept_send"
							   	name="wpbc_swp_accept_send"
							   	checked="checked"
							   	value="I accept sending data"
							    onclick=" if (jQuery(this ).is( ':checked' )) { jQuery( '#btn__toolbar__buttons_next').text( '<?php esc_attr_e( 'Save and Continue', 'booking' ); ?>' ); } else { jQuery( '#btn__toolbar__buttons_next').text( '<?php esc_attr_e( 'Skip', 'booking' ); ?>' );  } "
						/>
						<label for="wpbc_swp_accept_send"
							   class="wpdev-list-item-label"
							   style="font-size: 12px;font-weight: 400;">
							<?php esc_html_e('By checking this box, I agree to share data from this page to personalize my setup experience, receive more relevant content, and help improve WP Booking Calendar for all users.', 'booking'); ?>
						</label>
					</span>
				</div>
				<div class="wpbc__field wpbc_container wpbc_container_booking_form">
					<a	  style="margin-left: auto;"
						   class="wpbc_button_light button-primary"
						   id="btn__toolbar__buttons_next"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																								'current_step': '{{data.steps[ data.current_step ].next}}',
																								   'do_action': '{{data.steps[ data.current_step ].do_action}}',
																								'ui_clicked_element_id': 'btn__toolbar__buttons_next',
																								'step_data':{
																												'wpbc_swp_business_name':     jQuery( '[name=\'wpbc_swp_business_name\']').val(),
																												'wpbc_swp_booking_who_setup': jQuery( '[name=\'wpbc_swp_booking_who_setup\']').val(),
																												'wpbc_swp_industry':          jQuery( '[name=\'wpbc_swp_industry\']').val(),
																												'wpbc_swp_email':             jQuery( '[name=\'wpbc_swp_email\']').val(),
																												'wpbc_swp_accept_send':       jQuery( '[name=\'wpbc_swp_accept_send\']').is( ':checked' ) ? 'On' : 'Off'
																											}
																							} );
									wpbc_button_enable_loading_icon( this );
									wpbc_admin_show_message_processing( '' );" ><span><?php esc_html_e('Save and Continue','booking'); ?>&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_arrow_forward_ios"></i></a>
				</div>
			</div>
			<?php /**/ ?>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<p class="wpbc_exit_link_small">
						<a href="javascript:void(0)"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'skip_wizard' } ); "
						   title="<?php esc_attr_e('Exit and skip the setup wizard','booking'); ?>"
						><?php
							esc_html_e('Exit and skip the setup wizard','booking');
						?></a>
						<?php /* ?>
						<a href="javascript:void(0)" class="wpbc_button_danger" style="margin: 25px 0 0;  font-size: 12px;"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'make_reset' } ); "
						   title="<?php esc_attr_e('Reset the Setup Wizard and start from beginning','booking'); ?>"
						><?php
							esc_html_e('Reset Wizard','booking');
						?></a>
						<?php */ ?>
					</p>
				</div>
			</div>
			<?php /**/ ?>


		</div>
		<style type="text/css">
			.wpbc_ajx_page__container .wpbc_ajx_page__section_footer:not(.wpbc__row){ display: none;}
		</style>
	</div>
	</script><?php
}

