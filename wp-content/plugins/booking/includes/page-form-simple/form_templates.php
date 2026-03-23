<?php /**
 * @version 1.0
 * @package  Booking Calendar
 * @category Booking Forms Templates
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-10-08
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly           // FixIn: 10.6.2.1.


// ---------------------------------------------------------------------------------------------------------------------
//  Templates 	for 	"Booking  Form"
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Get Booking Form Template - HTML content with Shortcodes
 *
 * @param string $template_name
 *
 * @return string
 */
function wpbc_get__booking_form__template( $template_name ) {

	$booking_form = '';

	switch ( $template_name ) {

		case 'standard':

            $booking_form = '[calendar] \n\
<div class="standard-form"> \n\
 <p>'.__('First Name (required)' ,'booking').':<br />[text* name] </p> \n\
 <p>'.__('Last Name (required)' ,'booking').':<br />[text* secondname] </p> \n\
 <p>'.__('Email (required)' ,'booking').':<br />[email* email] </p> \n\
 <p>'.__('Phone' ,'booking').':<br />[text phone] </p> \n\
 <p>'.__('Adults' ,'booking').':<br />[selectbox visitors "1" "2" "3" "4"] </p> \n\
 <p>'.__('Children' ,'booking').':<br />[selectbox children "0" "1" "2" "3"] </p> \n\
 <p>'.__('Details' ,'booking').':<br /> [textarea details] </p> \n\
 <p>[checkbox* term_and_condition use_label_element "'.__('I Accept term and conditions' ,'booking').'"] </p> \n\
 <p>[captcha]</p> \n\
 <p>[submit class:btn "'.__('Send' ,'booking').'"]</p> \n\
</div>';
            break;


		case 'calendar_next_to_form':
            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
<r> \n\
	<c>[calendar]</c> \n\
	<spacer>width:40px;</spacer> \n\
	<c> \n\
		<r><c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l> <br>[text* name]</c></r> \n\
		<r><c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . '):</l>  <br>[text* secondname] </c></r> \n\
		<r><c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l>      <br>[email* email] </c></r> \n\
		<r><c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l>                 <br>[text phone] </c></r> \n\
		<r><c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l>                <br>[selectbox visitors "1" "2" "3" "4"] </c></r> \n\
		<r><c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l>              <br>[selectbox children "0" "1" "2" "3"] </c></r> \n\
		<r><c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l><br> [textarea details]</c></r> \n\
		<r> \n\
			<c>[checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"]</c> \n\
			<c>[captcha]</c> \n\
		</r> \n\
		<hr/> \n\
		<r><c>[submit class:btn "' . __( 'Send', 'booking' ) . '"]</c></r> \n\
	</c> \n\
</r>';

			break;


		case '2_columns':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div" style="padding: 1em 0;"> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c> \n\
		<c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c> \n\
		<c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n\
		<c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l> <div style="clear:both;width:100%"></div> \n\
			[textarea details] </c> \n\
	</r> \n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\	
	<p>[submit "' . __( 'Send', 'booking' ) . '"]</p> \n\
</div>';
	        break;


		case '2_columns_times_2_hours':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
<div class="wpbc__form__div" style="padding: 1em 0;"> \n\
    <r> \n\
		<c> <l>' . esc_html__( 'Select Date', 'booking' ) . ':</l><br />[calendar] </c> \n\
		<c> <l>' . esc_html__( 'Select Times', 'booking' ) . '*:</l><br /> \n\
			[selectbox* rangetime "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c> \n\
		<c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c> \n\
		<c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n\
		<c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l> <div style="clear:both;width:100%"></div> \n\
			[textarea details] </c> \n\
	</r> \n\
	<p>[submit "' . __( 'Send', 'booking' ) . '"]</p> \n\
</div>';
	        break;


		case '2_columns_times_30_minutes_wizard':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n\ 
		<r> \n\
			<c> <l>' . esc_html__( 'Select Date', 'booking' ) . ':</l><br />[calendar] </c> \n\ 
			<c> <l>' . esc_html__( 'Select Times', 'booking' ) . '*:</l><br /> \n\
				[selectbox rangetime "09:00 - 09:30" "09:30 - 10:00" "10:00 - 10:30" "10:30 - 11:00" "11:00 - 11:30" "11:30 - 12:00" "12:00 - 12:30" "12:30 - 13:00" "13:00 - 13:30" "13:30 - 14:00" "14:00 - 14:30" "14:30 - 15:00" "15:00 - 15:30" "15:30 - 16:00" "16:00 - 16:30" "16:30 - 17:00"] </c> \n\ 
		</r><hr> \n\ 
		<r> \n\
			<div class="wpbc__field" style="justify-content: flex-end"> \n\
     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2"> \n\ 
				' . esc_attr__( 'Next', 'booking' ) . ' \n\ 
				</a> \n\
			</div> \n\ 
		</r> \n\ 
</div> \n\
<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2 wpbc_wizard_step_hidden" style="clear:both"> \n\ 
	<r> \n\
		<c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c> \n\
		<c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c> \n\ 
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c> \n\ 
		<c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n\ 
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n\
		<c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n\ 
	</r> \n\
	<r> \n\
		<c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n\ 
			[textarea details] </c> \n\ 
	</r> \n\
	<spacer>height:10px;</spacer> \n\ 
	<hr> \n\
	<r> \n\
		<div class="wpbc__field" style="justify-content: flex-end"> \n\
			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1">' . esc_attr__( 'Back', 'booking' ) . '</a>&nbsp;&nbsp;&nbsp; \n\ 
			[submit "' . __( 'Send', 'booking' ) . '"] \n\ 
		</div> \n\ 
	</r> \n\
</div>';
	        break;


		case '2_columns_hint_cost_nights':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c><div class="form-hints">\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>  \n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</div></c>\n\
	</r>\n\
	<hr> \n\
	<r>\n\
		<c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Address (required)', 'booking' ) . ':</l><br />[text* address] </c>\n\
		<c> <l>' . esc_html__( 'City (required)', 'booking' ) . ':</l><br />[text* city] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Post code (required)', 'booking' ) . ':</l><br />[text* postcode] </c>\n\
		<c> <l>' . esc_html__( 'Country (required)', 'booking' ) . ':</l><br />[country] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr> \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;


		case '2_columns_hint_availability':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>  \n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> \n\
		</p></c>\n\ 
		<c><l>' . esc_html__( 'Availability', 'booking' ) . ':</l><spacer></spacer>[capacity_hint]</c> \n\   
	</r>\n\
	<hr>  \n\
	<r>\n\
		<c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Coupon (required)', 'booking' ) . ':</l><br />[coupon coupon] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr>  \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;


		case '2_columns_times_30_minutes_hints_coupon':

			$booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar] \n\
<r>\n\
	<c>  <l>' . esc_html__( 'Select Times', 'booking' ) . '*:</l><br />\n\
		[selectbox rangetime "9:00 AM - 9:30 AM@@09:00 - 09:30"  "9:30 AM - 10:00 AM@@09:30 - 10:00"  "10:00 AM - 10:30 AM@@10:00 - 10:30"  "10:30 AM - 11:00 AM@@10:30 - 11:00"  "11:00 AM - 11:30 AM@@11:00 - 11:30"  "11:30 AM - 12:00 PM@@11:30 - 12:00"  "12:00 PM - 12:30 PM@@12:00 - 12:30"  "12:30 PM - 1:00 PM@@12:30 - 13:00"  "1:00 PM - 1:30 PM@@13:00 - 13:30"  "1:30 PM - 2:00 PM@@13:30 - 14:00"  "2:00 PM - 2:30 PM@@14:00 - 14:30"  "2:30 PM - 3:00 PM@@14:30 - 15:00"  "3:00 PM - 3:30 PM@@15:00 - 15:30"  "3:30 PM - 4:00 PM@@15:30 - 16:00"  "4:00 PM - 4:30 PM@@16:00 - 16:30"  "4:30 PM - 5:00 PM@@16:30 - 17:00"  "5:00 PM - 5:30 PM@@17:00 - 17:30"  "5:30 PM - 6:00 PM@@17:30 - 18:00"  "6:00 PM - 6:30 PM@@18:00 - 18:30"]\n\
	</c>\n\
</r>\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c>
			<div>\n\
				' . __( 'Date', 'booking' ) . ': <strong>[selected_dates_hint]</strong><br> \n\  
				' . __( 'Times', 'booking' ) . ': <strong>[start_time_hint] - [end_time_hint]</strong><br>  \n\  
				' . __( 'Total cost', 'booking' ) . ': <strong>[cost_hint]</strong> \n\
			</div>\n\ 
		</c>\n\ 
	</r>\n\ 
	<hr>  \n\
	<r>\n\
		<c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Coupon (required)', 'booking' ) . ':</l><br />[coupon coupon] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr>  \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;


		case '2_columns_hints_coupon':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c>
			<div>\n\
				' . __( 'Date', 'booking' ) . ': <strong>[selected_dates_hint]</strong><br> \n\  
				' . __( 'Times', 'booking' ) . ': <strong>[start_time_hint] - [end_time_hint]</strong><br>  \n\  
				' . __( 'Total cost', 'booking' ) . ': <strong>[cost_hint]</strong> \n\
			</div>\n\ 
		</c>\n\ 
	</r>\n\ 
	<hr>  \n\
	<r>\n\
		<c> <l>' . esc_html__( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . esc_html__( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . esc_html__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . esc_html__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . esc_html__( 'Coupon (required)', 'booking' ) . ':</l><br />[coupon coupon] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr>  \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;
	}

	if (empty($booking_form)){
		$booking_form = wpbc_get__predefined_booking_form__template( $template_name );
	}

	return $booking_form;
}


/**
 * Get "Content of booking fields data" - HTML content with Shortcodes
 *
 * @param string $template_name
 *
 * @return string
 */
function wpbc_get__booking_data__template( $template_name ) {

	$booking_form = '';

	switch ( $template_name ) {

		case 'standard':
		case '2_columns':
		case 'calendar_next_to_form':
		case 'wizard_no_times_a':
            $booking_form = '<div class="standard-content-form"> \n\
    <b>' . esc_html__('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>' . esc_html__('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>' . esc_html__('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>' . esc_html__('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>' . esc_html__('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>' . esc_html__('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>' . esc_html__('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
            break;

		case '2_columns_times_2_hours':
		case '2_columns_times_30_minutes_wizard':
            $booking_form = '<div class="standard-content-form"> \n\
    <b>' . esc_html__('Times' ,'booking').'</b>:      <f>[rangetime]</f><br/> \n\
    <b>' . esc_html__('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>' . esc_html__('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>' . esc_html__('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>' . esc_html__('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>' . esc_html__('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>' . esc_html__('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>' . esc_html__('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
			break;



		case 'appointments_service_c':
            $booking_form = '<div class="standard-content-form"> \n\
    <b>' . esc_html__('Times' ,'booking').'</b>:      <f>[rangetime]</f><br/> \n\
    <b>' . esc_html__('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>' . esc_html__('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>' . esc_html__('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>' . esc_html__('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>' . esc_html__('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
			break;

		case '2_columns_hint_cost_nights':

            $booking_form = '<div class="standard-content-form"> \n\
    <b>' . esc_html__('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>' . esc_html__('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>' . esc_html__('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>' . esc_html__('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>' . esc_html__('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>' . esc_html__('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>' . esc_html__('Address' ,'booking').'</b>:    <f>[address]</f><br/> \n\
    <b>' . esc_html__('City' ,'booking').'</b>:       <f>[city]</f><br/> \n\
    <b>' . esc_html__('Post code' ,'booking').'</b>:  <f>[postcode]</f><br/> \n\
    <b>' . esc_html__('Country' ,'booking').'</b>:    <f>[country]</f><br/> \n\
    <b>' . esc_html__('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
	        break;

		case '2_columns_hint_availability':
		case '2_columns_hints_coupon':
            $booking_form = '<div class="standard-content-form"> \n\
    <b>' . esc_html__('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>' . esc_html__('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>' . esc_html__('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>' . esc_html__('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>' . esc_html__('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>' . esc_html__('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>' . esc_html__('Coupon' ,'booking').'</b>:     <f>[coupon]</f><br/> \n\
    <b>' . esc_html__('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
	        break;

		case '2_columns_times_30_minutes_hints_coupon':
            $booking_form = '<div class="standard-content-form"> \n\
    <b>' . esc_html__('Times' ,'booking').'</b>:      <f>[rangetime]</f><br/> \n\
    <b>' . esc_html__('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>' . esc_html__('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>' . esc_html__('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>' . esc_html__('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>' . esc_html__('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>' . esc_html__('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>' . esc_html__('Coupon' ,'booking').'</b>:     <f>[coupon]</f><br/> \n\
    <b>' . esc_html__('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
 	        break;
	}

	return $booking_form;
}




function wpbc_get__predefined_booking_form__template( $form_type ){

    $form_content = '';
	// FixIn: 10.7.1.4.
	if ( in_array( $form_type, array( 'appointments30' ) ) ) {
		$form_content = '';
        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        $form_content .='<div class="wpbc_wizard__border_container"> \n';
        $form_content .='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n';
        $form_content .='		<r> \n';
        $form_content .='			<c> <l>' . esc_attr__( 'Select Date', 'booking' )  . ':</l><br /> [calendar] </c> \n';
		$form_content .='			<c> <l>' . esc_attr__( 'Select Times', 'booking' ) . ':</l><br /> \n';
        $form_content .='			[selectbox rangetime "09:00 - 09:30" "09:30 - 10:00" "10:00 - 10:30" "10:30 - 11:00" "11:00 - 11:30" "11:30 - 12:00" "12:00 - 12:30" "12:30 - 13:00" "13:00 - 13:30" "13:30 - 14:00" "14:00 - 14:30" "14:30 - 15:00" "15:00 - 15:30" "15:30 - 16:00" "16:00 - 16:30" "16:30 - 17:00" "17:00 - 17:30" "17:30 - 18:00"] \n';
        $form_content .='		</c> \n';
        $form_content .='		</r> \n';
        if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .= '		<r> <c> \n';
            $form_content .= '				<div class="form-hints"> \n';
            $form_content .= '				' . esc_attr__( 'Date', 'booking' )  . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
            $form_content .= '			   	' . esc_attr__( 'Time', 'booking' )  . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
            $form_content .= '				</div> \n';
            $form_content .= '			</c> \n';
            $form_content .= '			<c>' . esc_attr__( 'Total Cost', 'booking' )  . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
            $form_content .= '	    </r> \n';
        }
        $form_content .='		<hr><r> \n';
        $form_content .='			<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        $form_content .='     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2" > \n';
        $form_content .='				' . esc_attr__( 'Next', 'booking' ) . ' \n';
        $form_content .='				</a> \n';
        $form_content .='			</div> \n';
        $form_content .='		</r> \n';
        $form_content .='</div> \n';
        $form_content .='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2 wpbc_wizard_step_hidden" style="display:none;clear:both;"> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* name] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* secondname] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[email* email] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
        $form_content .='			[textarea details] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<spacer>height:10px;</spacer> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
        $form_content .='		<c> [captcha] </c> \n';
        $form_content .='	</r> \n';
        if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .= '	<r> <c> \n';
            $form_content .= '			<div class="form-hints"> \n';
            $form_content .= '				' . esc_attr__( 'Date', 'booking' )  . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
            $form_content .= '			   	' . esc_attr__( 'Time', 'booking' )  . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
            $form_content .= '			</div> \n';
            $form_content .= '		</c> \n';
            $form_content .= '		<c>' . esc_attr__( 'Total Cost', 'booking' )  . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
            $form_content .= '	</r> \n';
        }
        $form_content .='	<hr><r> \n';
        $form_content .='		<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        $form_content .='			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1">' . esc_attr__( 'Back', 'booking' ) . '</a>&nbsp;&nbsp;&nbsp; \n';
        $form_content .='			[submit "' . esc_attr__( 'Send', 'booking' ) . '"] \n';
        $form_content .='		</div> \n';
        $form_content .='	</r> \n';
        $form_content .='</div> \n';
        $form_content .='</div> \n'; // class=wpbc_wizard__border_container.

	}

    if ( in_array( $form_type, array( 'times', 'times30', 'times15', 'times120', 'times60', 'times60_24h', 'endtime', 'durationtime' ) ) ){
        $form_content = '';
        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        $form_content .='<div class="wpbc__form__div">  \n';
        $form_content .='	<r>  \n';
        $form_content .='		<c> [calendar]  </c> \n';

        if ( in_array( $form_type, array( 'times', 'times30', 'times15', 'times120', 'times60', 'times60_24h' ) ) ){
            $form_content .='		<c>  <l>' . esc_attr__( 'Select Times', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br /> \n';

            if ( $form_type == 'times' ){
                $form_content .='			[selectbox rangetime "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] \n';
            }
            if ( $form_type == 'times120' ){
                $form_content .='			[selectbox* rangetime  "6:00 AM - 8:00 AM@@06:00 - 08:00"  "8:00 AM - 10:00 AM@@08:00 - 10:00"  "10:00 AM - 12:00 PM@@10:00 - 12:00"  "12:00 PM - 2:00 PM@@12:00 - 14:00"  "2:00 PM - 4:00 PM@@14:00 - 16:00"  "4:00 PM - 6:00 PM@@16:00 - 18:00"  "6:00 PM - 8:00 PM@@18:00 - 20:00"] \n';
            }
            if ( $form_type == 'times60' ){
                $form_content .='			[selectbox* rangetime  "6:00 AM - 7:00 AM@@06:00 - 07:00"  "7:00 AM - 8:00 AM@@07:00 - 08:00"  "8:00 AM - 9:00 AM@@08:00 - 09:00"  "9:00 AM - 10:00 AM@@09:00 - 10:00"  "10:00 AM - 11:00 AM@@10:00 - 11:00"  "11:00 AM - 12:00 PM@@11:00 - 12:00"  "12:00 PM - 1:00 PM@@12:00 - 13:00"  "1:00 PM - 2:00 PM@@13:00 - 14:00"  "2:00 PM - 3:00 PM@@14:00 - 15:00"  "3:00 PM - 4:00 PM@@15:00 - 16:00"  "4:00 PM - 5:00 PM@@16:00 - 17:00"  "5:00 PM - 6:00 PM@@17:00 - 18:00"  "6:00 PM - 7:00 PM@@18:00 - 19:00"  "7:00 PM - 8:00 PM@@19:00 - 20:00"  "8:00 PM - 9:00 PM@@20:00 - 21:00"] \n';
            }
            if ( $form_type == 'times60_24h' ){
                $form_content .='			[selectbox* rangetime  "07:00 - 08:00"  "08:00 - 09:00"  "09:00 - 10:00"  "10:00 - 11:00"  "11:00 - 12:00"  "12:00 - 13:00"  "13:00 - 14:00"  "14:00 - 15:00"  "15:00 - 16:00"  "16:00 - 17:00"  "17:00 - 18:00"  "18:00 - 19:00"  "19:00 - 20:00"  "20:00 - 21:00"] \n';
            }
            if ( $form_type == 'times30' ){
                $form_content .='			[selectbox rangetime "06:00 - 06:30" "06:30 - 07:00" "07:00 - 07:30" "07:30 - 08:00" "08:00 - 08:30" "08:30 - 09:00" "09:00 - 09:30" "09:30 - 10:00" "10:00 - 10:30" "10:30 - 11:00" "11:00 - 11:30" "11:30 - 12:00" "12:00 - 12:30" "12:30 - 13:00" "13:00 - 13:30" "13:30 - 14:00" "14:00 - 14:30" "14:30 - 15:00" "15:00 - 15:30" "15:30 - 16:00" "16:00 - 16:30" "16:30 - 17:00" "17:00 - 17:30" "17:30 - 18:00" "18:00 - 18:30" "18:30 - 19:00" "19:00 - 19:30" "19:30 - 20:00" "20:00 - 20:30" "20:30 - 21:00" "21:00 - 21:30"] \n';
            }
            if ( $form_type == 'times15' ){
                $form_content .='			[selectbox rangetime "8:00 AM - 8:15 AM@@08:00 - 08:15" "8:15 AM - 8:30 AM@@08:15 - 08:30" "8:30 AM - 8:45 AM@@08:30 - 08:45" "8:45 AM - 9:00 AM@@08:45 - 09:00" "9:00 AM - 9:15 AM@@09:00 - 09:15" "9:15 AM - 9:30 AM@@09:15 - 09:30" "9:30 AM - 9:45 AM@@09:30 - 09:45" "9:45 AM - 10:00 AM@@09:45 - 10:00" "10:00 AM - 10:15 AM@@10:00 - 10:15" "10:15 AM - 10:30 AM@@10:15 - 10:30" "10:30 AM - 10:45 AM@@10:30 - 10:45" "10:45 AM - 11:00 AM@@10:45 - 11:00" "11:00 AM - 11:15 AM@@11:00 - 11:15" "11:15 AM - 11:30 AM@@11:15 - 11:30" "11:30 AM - 11:45 AM@@11:30 - 11:45" "11:45 AM - 12:00 AM@@11:45 - 12:00" "12:00 AM - 12:15 AM@@12:00 - 12:15" "12:15 AM - 12:30 AM@@12:15 - 12:30" "12:30 AM - 12:45 AM@@12:30 - 12:45" "12:45 AM - 1:00 PM@@12:45 - 13:00" "1:00 PM - 1:15 PM@@13:00 - 13:15" "1:15 PM - 1:30 PM@@13:15 - 13:30" "1:30 PM - 1:45 PM@@13:30 - 13:45" "1:45 PM - 2:00 PM@@13:45 - 14:00" "2:00 PM - 2:15 PM@@14:00 - 14:15" "2:15 PM - 2:30 PM@@14:15 - 14:30" "2:30 PM - 2:45 PM@@14:30 - 14:45" "2:45 PM - 3:00 PM@@14:45 - 15:00" "3:00 PM - 3:15 PM@@15:00 - 15:15" "3:15 PM - 3:30 PM@@15:15 - 15:30" "3:30 PM - 3:45 PM@@15:30 - 15:45" "3:45 PM - 4:00 PM@@15:45 - 16:00" "4:00 PM - 4:15 PM@@16:00 - 16:15" "4:15 PM - 4:30 PM@@16:15 - 16:30" "4:30 PM - 4:45 PM@@16:30 - 16:45" "4:45 PM - 5:00 PM@@16:45 - 17:00" "5:00 PM - 5:15 PM@@17:00 - 17:15" "5:15 PM - 5:30 PM@@17:15 - 17:30" "5:30 PM - 5:45 PM@@17:30 - 17:45" "5:45 PM - 6:00 PM@@17:45 - 18:00" "6:00 PM - 6:15 PM@@18:00 - 18:15" "6:15 PM - 6:30 PM@@18:15 - 18:30" "6:30 PM - 6:45 PM@@18:30 - 18:45" "6:45 PM - 7:00 PM@@18:45 - 19:00" "7:00 PM - 7:15 PM@@19:00 - 19:15" "7:15 PM - 7:30 PM@@19:15 - 19:30" "7:30 PM - 7:45 PM@@19:30 - 19:45" "7:45 PM - 8:00 PM@@19:45 - 20:00" "8:00 PM - 8:15 PM@@20:00 - 20:15" "8:15 PM - 8:30 PM@@20:15 - 20:30" "8:30 PM - 8:45 PM@@20:30 - 20:45" "8:45 PM - 9:00 PM@@20:45 - 21:00" "9:00 PM - 9:15 PM@@21:00 - 21:15" "9:15 PM - 9:30 PM@@21:15 - 21:30" "9:30 PM - 9:45 PM@@21:30 - 21:45"] \n';
            }
            $form_content .='		</c> \n';
        }
        if ( in_array( $form_type, array( 'endtime', 'durationtime'  ) ) ){
              $form_content .='	</r> \n';
              $form_content .='	<r> \n';
              $form_content .='		<c>  <l>' . esc_attr__( 'Start Time', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br /> \n';
              $form_content .='			[selectbox starttime "08:00" "09:00" "10:00" "11:00" "12:00" "13:00" "14:00" "15:00" "16:00" "17:00" "18:00" "19:00" "20:00" "21:00" "22:00"] \n';
              $form_content .='		</c> \n';
              $form_content .='		<c> \n';
              if ( $form_type == 'endtime' ){
                  $form_content .='		     <l>' . esc_attr__( 'End Time', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br /> \n';
                  $form_content .='			[selectbox endtime "08:00" "09:00" "10:00" "11:00" "12:00" "13:00" "14:00" "15:00" "16:00" "17:00" "18:00" "19:00" "20:00" "21:00" "22:00" "23:00"]  \n';
              }
              if ( $form_type == 'durationtime' ){
                  $form_content .='		     <l>' . esc_attr__( 'Duration Time', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br /> \n';
                  $form_content .='			[selectbox durationtime "15 min@@00:15" "30 min@@00:30" "45 min@@00:45" "1 hour@@01:00" "1 hour 30 min@@01:30" "2 hours@@02:00" "3 hours@@03:00" "4 hours@@04:00"] \n';
              }
              $form_content .='		</c> \n';
        }
        $form_content .='	</r> \n';
        if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .='	<r> \n';
            $form_content .='		<c> \n';
            if ( class_exists( 'wpdev_bk_biz_l' ) ){                                                  // >= biz_l
                $form_content .='			<l>' . esc_attr__( 'Availability', 'booking' ) . ':</l><spacer></spacer> \n';
                $form_content .='			[capacity_hint] \n';
                $form_content .='			<spacer>height:1em;</spacer> \n';
            }
            $form_content .='		    <p> \n';
            $form_content .='				' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
            $form_content .='				([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
            $form_content .='				' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
            $form_content .='		    </p> \n';
            $form_content .='		</c> \n';
            $form_content .='	</r><hr> \n';
        }
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* name] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* secondname] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[email* email] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
        $form_content .='			[textarea details] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<spacer>height:10px;</spacer> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
        $form_content .='		<c> [captcha] </c> \n';
        $form_content .='	</r> \n';
        if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .='	<r> \n';
            $form_content .='		<c><p> \n';
            $form_content .='			' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
            $form_content .='			([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
            $form_content .='			' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
            $form_content .='		</p></c> \n';
            $form_content .='	</r> \n';
        }
        $form_content .='	<hr> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <p>[submit "' . esc_attr__( 'Send', 'booking' ) . '"]</p> </c> \n';
        $form_content .='	</r> \n';
        $form_content .='</div> \n';
    }

    if ($form_type == 'timesweek'){
           $form_content = '';
           $form_content .='[calendar] \n';
           $form_content .='<div class="times-form"> \n';
           $form_content .='<p> \n';
           $form_content .='    [condition name="weekday-condition" type="weekday" value="*"] \n';
           $form_content .='        Select Time Slot:<br> [selectbox rangetime multiple "10:00 - 12:00" "12:00 - 14:00" "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"] \n';
           $form_content .='    [/condition] \n';
           $form_content .='    [condition name="weekday-condition" type="weekday" value="1,2"] \n';
           $form_content .='        Select Time Slot available on Monday, Tuesday:<br>    [selectbox rangetime multiple "10:00 - 12:00" "12:00 - 14:00"] \n';
           $form_content .='    [/condition] \n';
           $form_content .='    [condition name="weekday-condition" type="weekday" value="3,4"] \n';
           $form_content .='        Select Time Slot available on Wednesday, Thursday:<br>  [selectbox rangetime multiple "14:00 - 16:00" "16:00 - 18:00" "18:00 - 20:00"] \n';
           $form_content .='    [/condition] \n';
           $form_content .='    [condition name="weekday-condition" type="weekday" value="5,6,0"] \n';
           $form_content .='        Select Time Slot available on Friday, Saturday, Sunday:<br> [selectbox rangetime multiple "12:00 - 14:00" "14:00 - 16:00" "16:00 - 18:00"] \n';
           $form_content .='    [/condition] \n';
           $form_content .='</p> \n';
           $form_content .='     <p>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* name] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* secondname] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[email* email] </p>   \n';
           $form_content .='     <p>' . esc_attr__( 'Phone', 'booking' ) . ':<br />[text phone] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Adults', 'booking' ) . ':<br />[selectbox visitors "1" "2" "3" "4"]</p> \n';
           $form_content .='     <p>' . esc_attr__( 'Children', 'booking' ) . ':<br />[selectbox children "0" "1" "2" "3"]</p> \n';
           $form_content .='     <p>' . esc_attr__( 'Details', 'booking' ) . ':<br /> [textarea details] </p> \n';
           $form_content .='     <p>[checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </p>\n';
           $form_content .='     <p>[captcha]</p> \n';
           $form_content .='     <p>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</p> \n';
           $form_content .='</div>';
    }

    if ($form_type == 'hints'){
           $form_content = '';
           $form_content .='[calendar] \n';
           $form_content .='<div class="standard-form"> \n';
           if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
               $form_content .='     <div class="form-hints"> \n';
               $form_content .='          ' . esc_attr__( 'Dates', 'booking' ) . ':[selected_short_timedates_hint]  ([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br><br> \n';
               $form_content .='          ' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': [cost_hint] <br> \n';
               $form_content .='     </div><hr/> \n';
           }
           $form_content .='     <p>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* name] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* secondname] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[email* email] </p>   \n';
           $form_content .='     <p>' . esc_attr__( 'Phone', 'booking' ) . ':<br />[text phone] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Adults', 'booking' ) . ':<br />[selectbox visitors "1" "2" "3" "4"]</p> \n';
           $form_content .='     <p>' . esc_attr__( 'Children', 'booking' ) . ':<br />[selectbox children "0" "1" "2" "3"]</p> \n';
           $form_content .='     <p>' . esc_attr__( 'Details', 'booking' ) . ':<br /> [textarea details] </p> \n';
           $form_content .='     <p>[checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </p>\n';
           $form_content .='     <p>[captcha]</p> \n';
           $form_content .='     <p>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</p> \n';
           $form_content .='</div>';
    }

    // FixIn: 8.7.3.5.
    if ( 'hints-dev' == $form_type ){
            $form_content = '';
            $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
            $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
			$form_content .='[calendar] \n';
            $form_content .='<div class="wpbc__form__div"> \n';

            if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              						// >= biz_m
				$form_content .='	<r> \n';
                $form_content .='		<c><div> \n';
                $form_content .='			' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
                $form_content .='			([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
                $form_content .='			' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
				if ( class_exists( 'wpdev_bk_biz_l' ) ){                                                      			// >= biz_l
					$form_content .='		' . esc_attr__( 'Availability', 'booking' ) . ': [capacity_hint] \n';
				}
                $form_content .='		</div></c> \n';
				$form_content .='	</r><hr>  \n';
            }

            $form_content .='	<r> \n';
            $form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* name] </c> \n';
            $form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* secondname] </c> \n';
            $form_content .='	</r> \n';
            $form_content .='	<r> \n';
            $form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[email* email] </c> \n';
            $form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n';
            $form_content .='	</r> \n';
            $form_content .='	<r> \n';
            $form_content .='		<c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n';
            $form_content .='		<c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n';
            $form_content .='	</r> \n';
            $form_content .='	<r> \n';
            $form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
            $form_content .='			[textarea details] </c> \n';
            $form_content .='	</r> \n';
            $form_content .='	<spacer>height:10px;</spacer> \n';
            $form_content .='	<r> \n';
            $form_content .='		<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
            $form_content .='		<c> [captcha] </c> \n';
            $form_content .='	</r> \n';
            if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
                $form_content .='	<r> \n';
                $form_content .='		<c><p> \n';
                $form_content .='			' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
                $form_content .='			([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
                $form_content .='			' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
                $form_content .='		</p></c> \n';
                $form_content .='	</r> \n';
            }
            $form_content .='	<hr> \n';
            $form_content .='	<r> \n';
            $form_content .='		<c> <p>[submit "' . esc_attr__( 'Send', 'booking' ) . '"]</p> </c> \n';
            $form_content .='	</r> \n';
            $form_content .='</div> \n';
    }

	if ( ($form_type == 'payment') || ($form_type == 'paymentUS') ){
        $form_content = '';
        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        $form_content .='[calendar] \n';
        $form_content .='<div class="payment-form"> \n';
        $form_content .='     <p>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* name] </p> \n';
        $form_content .='     <p>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* secondname] </p> \n';
        $form_content .='     <p>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[email* email] </p> \n';
        $form_content .='     <p>' . esc_attr__( 'Phone', 'booking' ) . ':<br />[text phone] </p> \n';
        $form_content .='     <p>' . esc_attr__( 'Address', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />  [text* address] </p> \n';
        $form_content .='     <p>' . esc_attr__( 'City', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />  [text* city] </p> \n';
        $form_content .='     <p>' . esc_attr__( 'Post code', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />  [text* postcode] </p> \n';
        if ( $form_type == 'paymentUS' ){                                                                                // FixIn: 8.1.1.5.
            $form_content .='     <p>' . esc_attr__( 'Country', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />  [country "US"] </p> \n';
            $form_content .='     <p>' . esc_attr__( 'State', 'booking' ) . ':<br /> [selectbox state "" "Alabama@@AL" "Alaska@@AK" "Arizona@@AZ" "Arkansas@@AR" "California@@CA" "Colorado@@CO" "Connecticut@@CT" "Delaware@@DE" "Florida@@FL" "Georgia@@GA" "Hawaii@@HI" "Idaho@@ID" "Illinois@@IL" "Indiana@@IN" "Iowa@@IA" "Kansas@@KS" "Kentucky@@KY" "Louisiana@@LA" "Maine@@ME" "Maryland@@MD" "Massachusetts@@MA" "Michigan@@MI" "Minnesota@@MN" "Mississippi@@MS" "Missouri@@MO" "Montana@@MT" "Nebraska@@NE" "Nevada@@NV" "New Hampshire@@NH" "New Jersey@@NJ" "New Mexico@@NM" "New York@@NY" "North Carolina@@NC" "North Dakota@@ND" "Ohio@@OH" "Oklahoma@@OK" "Oregon@@OR" "Pennsylvania@@PA" "Rhode Island@@RI" "South Carolina@@SC" "South Dakota@@SD" "Tennessee@@TN" "Texas@@TX" "Utah@@UT" "Vermont@@VT" "Virginia@@VA" "Washington@@WA" "West Virginia@@WV" "Wisconsin@@WI" "Wyoming@@WY"] </p> \n';
		} else {
			$form_content .='     <p>' . esc_attr__( 'Country', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />  [country] </p> \n';
		}
        $form_content .='     <p>' . esc_attr__( 'Adults', 'booking' ) . ':<br />[selectbox visitors "1" "2" "3" "4"]</p> \n';
        $form_content .='     <p>' . esc_attr__( 'Children', 'booking' ) . ':<br />[selectbox children "0" "1" "2" "3"]</p> \n';
        $form_content .='     <p>' . esc_attr__( 'Details', 'booking' ) . ':<br /> [textarea details] </p> \n';
        $form_content .='     <p>[checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </p> \n';
        $form_content .='     <p>[captcha]</p> \n';
        $form_content .='     <p>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</p> \n';
        $form_content .='</div>';
    }

    if (
        ($form_type == 'wizard') ||
        ( in_array( $form_type, array( 'wizard_times', 'wizard_times30', 'wizard_times15', 'wizard_times120', 'wizard_times60', 'wizard_times60_24h' ) ) )
    ){
        // FixIn: 8.6.1.15.
		$form_content = '';
        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        $form_content .='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n';
        $form_content .='		<r> \n';
        $form_content .='			<c> [calendar] </c> \n';

        if ( in_array( $form_type, array( 'wizard_times', 'wizard_times30', 'wizard_times15', 'wizard_times120', 'wizard_times60', 'wizard_times60_24h' ) ) ){
            $form_content .='		<c>  <l>' . esc_attr__( 'Select Times', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br /> \n';

            if ( $form_type == 'wizard_times' ){
                $form_content .='			[selectbox rangetime "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] \n';
            }
            if ( $form_type == 'wizard_times120' ){
                $form_content .='			[selectbox* rangetime  "6:00 AM - 8:00 AM@@06:00 - 08:00"  "8:00 AM - 10:00 AM@@08:00 - 10:00"  "10:00 AM - 12:00 PM@@10:00 - 12:00"  "12:00 PM - 2:00 PM@@12:00 - 14:00"  "2:00 PM - 4:00 PM@@14:00 - 16:00"  "4:00 PM - 6:00 PM@@16:00 - 18:00"  "6:00 PM - 8:00 PM@@18:00 - 20:00"] \n';
            }
            if ( $form_type == 'wizard_times60' ){
                $form_content .='			[selectbox* rangetime  "6:00 AM - 7:00 AM@@06:00 - 07:00"  "7:00 AM - 8:00 AM@@07:00 - 08:00"  "8:00 AM - 9:00 AM@@08:00 - 09:00"  "9:00 AM - 10:00 AM@@09:00 - 10:00"  "10:00 AM - 11:00 AM@@10:00 - 11:00"  "11:00 AM - 12:00 PM@@11:00 - 12:00"  "12:00 PM - 1:00 PM@@12:00 - 13:00"  "1:00 PM - 2:00 PM@@13:00 - 14:00"  "2:00 PM - 3:00 PM@@14:00 - 15:00"  "3:00 PM - 4:00 PM@@15:00 - 16:00"  "4:00 PM - 5:00 PM@@16:00 - 17:00"  "5:00 PM - 6:00 PM@@17:00 - 18:00"  "6:00 PM - 7:00 PM@@18:00 - 19:00"  "7:00 PM - 8:00 PM@@19:00 - 20:00"  "8:00 PM - 9:00 PM@@20:00 - 21:00"] \n';
            }
            if ( $form_type == 'wizard_times60_24h' ){
                $form_content .='			[selectbox* rangetime  "07:00 - 08:00"  "08:00 - 09:00"  "09:00 - 10:00"  "10:00 - 11:00"  "11:00 - 12:00"  "12:00 - 13:00"  "13:00 - 14:00"  "14:00 - 15:00"  "15:00 - 16:00"  "16:00 - 17:00"  "17:00 - 18:00"  "18:00 - 19:00"  "19:00 - 20:00"  "20:00 - 21:00"] \n';
            }
            if ( $form_type == 'wizard_times30' ){
                $form_content .='			[selectbox rangetime "09:00 - 09:30" "09:30 - 10:00" "10:00 - 10:30" "10:30 - 11:00" "11:00 - 11:30" "11:30 - 12:00" "12:00 - 12:30" "12:30 - 13:00" "13:00 - 13:30" "13:30 - 14:00" "14:00 - 14:30" "14:30 - 15:00" "15:00 - 15:30" "15:30 - 16:00" "16:00 - 16:30" "16:30 - 17:00" "17:00 - 17:30" "17:30 - 18:00" "18:00 - 18:30" "18:30 - 19:00"] \n';
            }
            if ( $form_type == 'wizard_times15' ){
                $form_content .='			[selectbox rangetime "8:00 AM - 8:15 AM@@08:00 - 08:15" "8:15 AM - 8:30 AM@@08:15 - 08:30" "8:30 AM - 8:45 AM@@08:30 - 08:45" "8:45 AM - 9:00 AM@@08:45 - 09:00" "9:00 AM - 9:15 AM@@09:00 - 09:15" "9:15 AM - 9:30 AM@@09:15 - 09:30" "9:30 AM - 9:45 AM@@09:30 - 09:45" "9:45 AM - 10:00 AM@@09:45 - 10:00" "10:00 AM - 10:15 AM@@10:00 - 10:15" "10:15 AM - 10:30 AM@@10:15 - 10:30" "10:30 AM - 10:45 AM@@10:30 - 10:45" "10:45 AM - 11:00 AM@@10:45 - 11:00" "11:00 AM - 11:15 AM@@11:00 - 11:15" "11:15 AM - 11:30 AM@@11:15 - 11:30" "11:30 AM - 11:45 AM@@11:30 - 11:45" "11:45 AM - 12:00 AM@@11:45 - 12:00" "12:00 AM - 12:15 AM@@12:00 - 12:15" "12:15 AM - 12:30 AM@@12:15 - 12:30" "12:30 AM - 12:45 AM@@12:30 - 12:45" "12:45 AM - 1:00 PM@@12:45 - 13:00" "1:00 PM - 1:15 PM@@13:00 - 13:15" "1:15 PM - 1:30 PM@@13:15 - 13:30" "1:30 PM - 1:45 PM@@13:30 - 13:45" "1:45 PM - 2:00 PM@@13:45 - 14:00" "2:00 PM - 2:15 PM@@14:00 - 14:15" "2:15 PM - 2:30 PM@@14:15 - 14:30" "2:30 PM - 2:45 PM@@14:30 - 14:45" "2:45 PM - 3:00 PM@@14:45 - 15:00" "3:00 PM - 3:15 PM@@15:00 - 15:15" "3:15 PM - 3:30 PM@@15:15 - 15:30" "3:30 PM - 3:45 PM@@15:30 - 15:45" "3:45 PM - 4:00 PM@@15:45 - 16:00" "4:00 PM - 4:15 PM@@16:00 - 16:15" "4:15 PM - 4:30 PM@@16:15 - 16:30" "4:30 PM - 4:45 PM@@16:30 - 16:45" "4:45 PM - 5:00 PM@@16:45 - 17:00" "5:00 PM - 5:15 PM@@17:00 - 17:15" "5:15 PM - 5:30 PM@@17:15 - 17:30" "5:30 PM - 5:45 PM@@17:30 - 17:45" "5:45 PM - 6:00 PM@@17:45 - 18:00" "6:00 PM - 6:15 PM@@18:00 - 18:15" "6:15 PM - 6:30 PM@@18:15 - 18:30" "6:30 PM - 6:45 PM@@18:30 - 18:45" "6:45 PM - 7:00 PM@@18:45 - 19:00" "7:00 PM - 7:15 PM@@19:00 - 19:15" "7:15 PM - 7:30 PM@@19:15 - 19:30" "7:30 PM - 7:45 PM@@19:30 - 19:45" "7:45 PM - 8:00 PM@@19:45 - 20:00" "8:00 PM - 8:15 PM@@20:00 - 20:15" "8:15 PM - 8:30 PM@@20:15 - 20:30" "8:30 PM - 8:45 PM@@20:30 - 20:45" "8:45 PM - 9:00 PM@@20:45 - 21:00" "9:00 PM - 9:15 PM@@21:00 - 21:15" "9:15 PM - 9:30 PM@@21:15 - 21:30" "9:30 PM - 9:45 PM@@21:30 - 21:45"] \n';
            }
            $form_content .='		</c> \n';
        } else if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .='			<c><p> \n';
            $form_content .='				' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
            $form_content .='				([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
            $form_content .='				' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
            $form_content .='			</p></c> \n';
        }
        $form_content .='		</r> <hr> \n';
        $form_content .='		<r> \n';
        $form_content .='			<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        $form_content .='     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2" > \n';
        $form_content .='				' . esc_attr__( 'Next', 'booking' ) . ' \n';
        $form_content .='				</a> \n';
        $form_content .='			</div> \n';
        $form_content .='		</r> \n';
        $form_content .='</div> \n';
        $form_content .='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2 wpbc_wizard_step_hidden" style="display:none;clear:both;"> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* name] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* secondname] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[email* email] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
        $form_content .='			[textarea details] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<spacer>height:10px;</spacer> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
        $form_content .='		<c> [captcha] </c> \n';
        $form_content .='	</r> \n';
        if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .='	<r> \n';
            $form_content .='		<c><div class="form-hints"> \n';
            $form_content .='			' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
            $form_content .='			([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
            $form_content .='			' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
            $form_content .='		</div></c> \n';
            $form_content .='	</r> \n';
        }
        $form_content .='	<hr> \n';
        $form_content .='	<r> \n';
        $form_content .='		<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        $form_content .='			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1">' . esc_attr__( 'Back', 'booking' ) . '</a>&nbsp;&nbsp;&nbsp; \n';
        $form_content .='			[submit "' . esc_attr__( 'Send', 'booking' ) . '"] \n';
        $form_content .='		</div> \n';
        $form_content .='	</r> \n';
        $form_content .='</div> \n';

    }

    if ($form_type == '2collumns')  { // calendar next to  form
        $form_content = '';

        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        $form_content .='<r> \n';
        $form_content .='	<c>[calendar]</c> \n';
        $form_content .='	<spacer>width:40px;</spacer> \n';
        $form_content .='	<c> \n';
        $form_content .='		<r><c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l> <br>[text* name]</c></r> \n';
        $form_content .='		<r><c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l>  <br>[text* secondname] </c></r> \n';
        $form_content .='		<r><c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l>      <br>[email* email] </c></r> \n';
        $form_content .='		<r><c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l>                 <br>[text phone] </c></r> \n';
        $form_content .='		<r><c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l>                <br>[selectbox visitors "1" "2" "3" "4"] </c></r> \n';
        $form_content .='		<r><c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l>              <br>[selectbox children "0" "1" "2" "3"] </c></r> \n';
        $form_content .='		<r><c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><br> [textarea details]</c></r> \n';
        $form_content .='		<r> \n';
        $form_content .='			<c>[checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"]</c> \n';
        $form_content .='			<c>[captcha]</c> \n';
        $form_content .='		</r> \n';
        $form_content .='		<hr/> \n';
        $form_content .='		<r><c>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</c></r> \n';
        $form_content .='	</c> \n';
        $form_content .='</r> \n';


        // $form_content .='<r> \n';
        // $form_content .='	<c>[calendar]</c> \n';
        // $form_content .='	<c> \n';
        // $form_content .='		<r><c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* name]</c></r> \n';
        // $form_content .='		<r><c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* secondname]</c></r> \n';
        // $form_content .='		<r><c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[email* email]</c></r> \n';
        // $form_content .='		<r><c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br>[text phone]</c></r> \n';
        // $form_content .='		<r><c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br>[selectbox visitors "1" "2" "3" "4"]</c></r> \n';
        // $form_content .='		<r><c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br>[selectbox children "0" "1" "2" "3"]</c></r> \n';
        // $form_content .='		<r><c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><br> [textarea details]</c></r> \n';
        // $form_content .='		<r> \n';
        // $form_content .='			<c>[checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"]</c> \n';
        // $form_content .='			<c>[captcha]</c> \n';
        // $form_content .='		</r> \n';
        // $form_content .='		<hr/> \n';
        // $form_content .='		<r><c>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</c></r> \n';
        // $form_content .='	</c> \n';
        // $form_content .='</r> \n';

        // $form_content .='<div class="wpbc_sections"> \n';
        // $form_content .='	<div class="wpbc_section_50"> \n';
        // $form_content .='		[calendar] \n';
        // $form_content .='	</div> \n';
        // $form_content .='	<div class="wpbc_section_spacer"></div> \n';
        // $form_content .='	<div class="wpbc_section_50"> \n';
        // $form_content .='		<p>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* name] </p> \n';
        // $form_content .='		<p>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* secondname] </p>  \n';
        // $form_content .='		<p>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[email* email] </p>  \n';
        // $form_content .='		<p>' . esc_attr__( 'Phone', 'booking' ) . ':<br />[text phone] </p>  \n';
        // $form_content .='		<p>' . esc_attr__( 'Adults', 'booking' ) . ':<br />[selectbox visitors "1" "2" "3" "4"]</p> \n';
        // $form_content .='		<p>' . esc_attr__( 'Children', 'booking' ) . ':<br />[selectbox children "0" "1" "2" "3"]</p>  \n';
        // $form_content .='	</div> \n';
        // $form_content .='	<div class="wpbc_section_100"> \n';
        // $form_content .='		<p>' . esc_attr__( 'Details', 'booking' ) . ':<br /> [textarea details]</p>  \n';
        // $form_content .='		[captcha] \n';
        // $form_content .='		<p>[checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"]</p> \n';
        // $form_content .='		<hr/> \n';
        // $form_content .='		<p>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"] </p> \n';
        // $form_content .='	</div> \n';
        // $form_content .='</div> \n';
    }

    // FixIn: 8.7.7.15.
    if ($form_type == 'fields2columns')  { // 2 columns form
        $form_content = '';
        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        $form_content .='[calendar]\n';
        $form_content .='<div class="wpbc__form__div">\n';
        $form_content .='	<r>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* name] </c>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* secondname] </c>\n';
        $form_content .='	</r>\n';
        $form_content .='	<r>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[email* email] </c>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br>[text phone] </c>\n';
        $form_content .='	</r>\n';
        $form_content .='	<r>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Address', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* address] </c>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'City', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* city] </c>\n';
        $form_content .='	</r>\n';
        $form_content .='	<r>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Post code', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* postcode] </c>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Country', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[country] </c>\n';
        $form_content .='	</r>\n';
        $form_content .='	<r>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br>[selectbox visitors "1" "2" "3" "4" "5"] </c>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br>[selectbox children "0" "1" "2" "3"] </c>\n';
        $form_content .='	</r>\n';
        $form_content .='	<r>\n';
        $form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
        $form_content .='			[textarea details] </c>\n';
        $form_content .='	</r><br>\n';
        $form_content .='	<p>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</p>\n';
        $form_content .='</div>';
    }

    // FixIn: 8.8.2.6.
    if ($form_type == 'fields3columns')  { // 3 columns form
        $form_content = '';
        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
        $form_content .='<r>\n';
        $form_content .='    <c> [calendar] </c>\n';
        $form_content .='    <c>\n';
        $form_content .='        <div class="wpbc__form__div">\n';
        $form_content .='            <r>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* name] </c>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* secondname] </c>\n';
        $form_content .='            </r>\n';
        $form_content .='            <r>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[email* email] </c>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br>[text phone] </c>\n';
        $form_content .='            </r>\n';
        $form_content .='            <r>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Address', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* address] </c>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'City', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* city] </c>\n';
        $form_content .='            </r>\n';
        $form_content .='            <r>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Post code', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[text* postcode] </c>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Country', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br>[country] </c>\n';
        $form_content .='            </r>\n';
        $form_content .='            <r>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br>[selectbox visitors "1" "2" "3" "4" "5"] </c>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br>[selectbox children "0" "1" "2" "3"] </c>\n';
        $form_content .='            </r>\n';
        $form_content .='            <r>\n';
        $form_content .='                <c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
        $form_content .='                    [textarea details] </c>\n';
        $form_content .='            </r>\n';
        $form_content .='            <p>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</p>\n';
        $form_content .='        </div>\n';
        $form_content .='    </c>\n';
        $form_content .='</r>\n';
    }


    // FixIn: 8.7.11.14.
    if ($form_type == 'fields2columnstimes')  { // 2 columns form
        $form_content  = '';
        $form_content .='<div class="wpbc__form__div"> \n';
        $form_content .='    <r> \n';
        $form_content .='		<c> <l>Select Date:</l><br />[calendar] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Select Times', 'booking' ) . '*:</l><br /> \n';
        $form_content .='			[select* rangetime "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* name] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* secondname] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[email* email] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
        $form_content .='			[textarea details] </c> \n';
        $form_content .='	</r><br /> \n';
        $form_content .='	<p>[submit "' . esc_attr__( 'Send', 'booking' ) . '"]</p>\n';
        $form_content .='</div>\n';
    }

    if ($form_content == '') { // Default Form.
           $form_content = '';
           $form_content .='[calendar] \n';
           $form_content .='<div class="standard-form"> \n';
           $form_content .='     <p>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* name] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[text* secondname] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):<br />[email* email] </p>   \n';
           $form_content .='     <p>' . esc_attr__( 'Phone', 'booking' ) . ':<br />[text phone] </p> \n';
           $form_content .='     <p>' . esc_attr__( 'Adults', 'booking' ) . ':<br />[selectbox visitors "1" "2" "3" "4"]</p> \n';
           $form_content .='     <p>' . esc_attr__( 'Children', 'booking' ) . ':<br />[selectbox children "0" "1" "2" "3"]</p> \n';
           $form_content .='     <p>' . esc_attr__( 'Details', 'booking' ) . ':<br /> [textarea details] </p> \n';
           $form_content .='     <p>[checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </p>\n';
           $form_content .='     <p>[captcha]</p> \n';
           $form_content .='     <p>[submit class:btn "' . esc_attr__( 'Send', 'booking' ) . '"]</p> \n';
           $form_content .='</div>';
    }


	if ( in_array( $form_type, array( 'appointments_service_a' ) ) ) {
		// FixIn: 10.9.6.5.
		$form_content = '';
		$form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
		$form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
		$form_content .='<div class="wpbc_wizard__border_container"> \n';
		$form_content .='	<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n';
		$form_content .='		[steps_timeline steps_count="3" active_step="1"] \n';
		$form_content .='		<r> \n';
		$form_content .='		 <c class="wpbc_aling_center"> \n';
		$form_content .='			<l>' . esc_attr__( 'Select Service', 'booking' ) . ' *</l><br /><spacer>height:20px;</spacer> \n';
		$form_content .='			[selectbox durationtime "Service A (20 min)@@00:20" "Service B (30 min)@@00:30" "Service C (45 min)@@00:45" "Service D (1 hour)@@01:00"]<spacer>height:15px;</spacer> \n';
		$form_content .='		</c> \n';
		$form_content .='		</r> \n';
		$form_content .='	    <hr><r> \n';
		$form_content .='			<c class="wpbc_aling_right" > \n';
		$form_content .='     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2"> \n';
		$form_content .='					' . esc_attr__( 'Next', 'booking' ) . ' \n';
		$form_content .='				</a> \n';
		$form_content .='			</c> \n';
		$form_content .='		</r> \n';
		$form_content .='	</div> \n';
		$form_content .='	<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2  wpbc_wizard_step_hidden" style="display:none;clear:both;"> \n';
		$form_content .='		[steps_timeline steps_count="3" active_step="2"]<hr> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c>  <l>' . esc_attr__( 'Select Date', 'booking' ) . ' *</l><br />  [calendar] </c> \n';
		$form_content .='			<c>  <l>' . esc_attr__( 'Select Times', 'booking' ) . ' *</l><br /> \n';
		$form_content .='			    [selectbox starttime "09:00" "09:30" "10:00" "10:30" "11:00" "11:30" "12:00" "12:30" "13:00" "13:30" "14:00" "14:30" "15:00" "15:30" "16:00" "16:30" "17:00" "17:30" "18:00"] \n';
		$form_content .='			</c> \n';
		$form_content .='		</r> \n';
		if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
			$form_content .= '		<r> <c> \n';
			$form_content .= '				<div class="form-hints"> \n';
			$form_content .= '				' . esc_attr__( 'Date', 'booking' ) . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
			$form_content .= '			   	' . esc_attr__( 'Time', 'booking' ) . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
			$form_content .= '				</div> \n';
			$form_content .= '			</c> \n';
			$form_content .= '			<c>' . esc_attr__( 'Total Cost', 'booking' ) . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
			$form_content .= '	    </r> \n';
		}
		$form_content .='	    <hr> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c class="wpbc_aling_right" > \n';
		$form_content .='				<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1"> \n';
		$form_content .='					' . esc_attr__( 'Back', 'booking' ) . '</a><spacer>width:20px;</spacer> \n';
		$form_content .='     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_3" > \n';
		$form_content .='					' . esc_attr__( 'Next', 'booking' ) . '</a> \n';
		$form_content .='			</c> \n';
		$form_content .='		</r> \n';
		$form_content .='	</div> \n';
		$form_content .='	<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step3 wpbc_wizard_step_hidden" style="display:none;clear:both;"> \n';
		$form_content .='	[steps_timeline steps_count="3" active_step="3"]<hr> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' *</l><br />[text* name] </c> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' *</l><br />[text* secondname] </c> \n';
		$form_content .='	</r> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' *</l><br />[email* email] </c> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . '</l><br />[text phone] </c> \n';
		$form_content .='	</r> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . '</l><spacer></spacer> \n';
		$form_content .='			[textarea details] </c> \n';
		$form_content .='	</r> \n';
		$form_content .='	<spacer>height:10px;</spacer> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
		$form_content .='		<c> [captcha] </c> \n';
		$form_content .='	</r> \n';
		if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
			$form_content .='	<r> <c> \n';
			$form_content .='			<div class="form-hints"> \n';
			$form_content .='				' . esc_attr__( 'Date', 'booking' ) . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
			$form_content .='			   	' . esc_attr__( 'Time', 'booking' ) . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
			$form_content .='			</div> \n';
			$form_content .='		</c> \n';
			$form_content .='		<c>' . esc_attr__( 'Total Cost', 'booking' ) . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
			$form_content .='	</r> \n';
		}
		$form_content .='	<hr> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c class="wpbc_aling_right" > \n';
		$form_content .='			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2"> \n';
		$form_content .='				' . esc_attr__( 'Back', 'booking' ) . '</a><spacer>width:20px;</spacer> \n';
		$form_content .='			[submit "' . esc_attr__( 'Send', 'booking' ) . '"] \n';
		$form_content .='		</c> \n';
		$form_content .='	</r> \n';
		$form_content .='	</div> \n';
		$form_content .='</div>';
	}

	if ( in_array( $form_type, array( 'appointments_service_b' ) ) ) {
		// FixIn: 10.9.6.5.
		$form_content = '';
		$form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
		$form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
		$form_content .='<div class="wpbc_wizard__border_container"> \n';
		$form_content .='	<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n';
		$form_content .='		[steps_timeline steps_count="3" active_step="1"] \n';
		$form_content .='		<r> \n';
		$form_content .='		 <c class="wpbc_aling_center"> \n';
		$form_content .='			<l>' . esc_attr__( 'Select Service', 'booking' ) . ' *</l><br /><spacer>height:20px;</spacer> \n';
		$form_content .='			[selectbox durationtime "Service A (20 min)@@00:20" "Service B (30 min)@@00:30" "Service C (45 min)@@00:45" "Service D (1 hour)@@01:00"]<spacer>height:15px;</spacer> \n';
		$form_content .='		</c> \n';
		$form_content .='		</r> \n';
		$form_content .='	    <hr> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c>  <l>' . esc_attr__( 'Select Date', 'booking' ) . ' *</l><br />  [calendar] </c> \n';
		$form_content .='			<c>  <l>' . esc_attr__( 'Select Times', 'booking' ) . ' *</l><br /> \n';
		$form_content .='			    [selectbox starttime "09:00" "09:30" "10:00" "10:30" "11:00" "11:30" "12:00" "12:30" "13:00" "13:30" "14:00" "14:30" "15:00" "15:30" "16:00" "16:30" "17:00" "17:30" "18:00"] \n';
		$form_content .='			</c> \n';
		$form_content .='		</r> \n';
		if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
			$form_content .= '		<r> <c> \n';
			$form_content .= '				<div class="form-hints"> \n';
			$form_content .= '				' . esc_attr__( 'Date', 'booking' ) . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
			$form_content .= '			   	' . esc_attr__( 'Time', 'booking' ) . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
			$form_content .= '				</div> \n';
			$form_content .= '			</c> \n';
			$form_content .= '			<c>' . esc_attr__( 'Total Cost', 'booking' ) . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
			$form_content .= '	    </r> \n';
		}
		$form_content .='	    <hr> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c class="wpbc_aling_right" > \n';
		$form_content .='     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2" > \n';
		$form_content .='					' . esc_attr__( 'Next', 'booking' ) . '</a> \n';
		$form_content .='			</c> \n';
		$form_content .='		</r> \n';
		$form_content .='	</div> \n';
		$form_content .='	<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2 wpbc_wizard_step_hidden" style="display:none;clear:both;"> \n';
		$form_content .='		[steps_timeline steps_count="3" active_step="2"] \n';
		$form_content .='		<r> \n';
		$form_content .='			<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' *</l><br />[text* name] </c> \n';
		$form_content .='			<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' *</l><br />[text* secondname] </c> \n';
		$form_content .='		</r> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' *</l><br />[email* email] </c> \n';
		$form_content .='			<c> <l>' . esc_attr__( 'Phone', 'booking' ) . '</l><br />[text phone] </c> \n';
		$form_content .='		</r> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c> <l>' . esc_attr__( 'Details', 'booking' ) . '</l><spacer></spacer> \n';
		$form_content .='				[textarea details] </c> \n';
		$form_content .='		</r> \n';
		$form_content .='		<spacer>height:10px;</spacer> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
		$form_content .='			<c> [captcha] </c> \n';
		$form_content .='		</r> \n';
		if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
			$form_content .='		<r> <c> \n';
			$form_content .='				<div class="form-hints"> \n';
			$form_content .='					' . esc_attr__( 'Date', 'booking' ) . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
			$form_content .='				   	' . esc_attr__( 'Time', 'booking' ) . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
			$form_content .='				</div> \n';
			$form_content .='			</c> \n';
			$form_content .='			<c>' . esc_attr__( 'Total Cost', 'booking' ) . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
			$form_content .='		</r> \n';
		}
		$form_content .='		<hr> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c class="wpbc_aling_right" > \n';
		$form_content .='				<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1"> \n';
		$form_content .='					' . esc_attr__( 'Back', 'booking' ) . '</a><spacer>width:20px;</spacer> \n';
		$form_content .='				[submit "' . esc_attr__( 'Send', 'booking' ) . '"] \n';
		$form_content .='			</c> \n';
		$form_content .='		</r> \n';
		$form_content .='	</div> \n';
		$form_content .='</div>';
	}

	// FixIn: 10.12.4.8.
	if ( in_array( $form_type, array( 'appointments_service_c' ) ) ) {
		$form_content = '';
		$form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
		$form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
		$form_content .='<div class="wpbc_wizard__border_container"> \n';
		$form_content .='	<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n';
		$form_content .='		<r> \n';
		$form_content .='		 <c style="flex: 1 1 160px;padding-right: 20px;margin: 12px 0;border-right: 1px solid #ccc;align-self: stretch;"> \n';
		$form_content .='			 <r> \n';
		$form_content .='				<c> \n';
		$form_content .='			<l>' . esc_attr__( 'Select Service', 'booking' ) . ' *</l><br /><spacer>height:20px;</spacer> \n';
		$form_content .='			[selectbox durationtime "Service A (20 min)@@00:20" "Service B (30 min)@@00:30" "Service C (45 min)@@00:45" "Service D (1 hour)@@01:00"]<spacer>height:15px;</spacer> \n';
		$form_content .='					</c> \n';
		$form_content .='			 </r> \n';
		$form_content .='		</c> \n';
		$form_content .='		<c style="flex: 1 1 70%;"> \n';
		$form_content .='			<r style="gap: 30px;"> \n';
		$form_content .='				<c style="flex: 1 1 220px;">  <l>' . esc_attr__( 'Select Date', 'booking' ) . ' *</l><br /><spacer>height:18px;</spacer>  [calendar] </c> \n';
		$form_content .='				<c style="flex: 1 1 190px;">  <l>' . esc_attr__( 'Select Times', 'booking' ) . ' *</l><br /><spacer>height:20px;</spacer> \n';
		$form_content .='			    [selectbox starttime "09:00" "09:30" "10:00" "10:30" "11:00" "11:30" "12:00" "12:30" "13:00" "13:30" "14:00" "14:30" "15:00" "15:30" "16:00" "16:30" "17:00" "17:30" "18:00"] \n';
		$form_content .='				</c> \n';
		$form_content .='			</r> \n';
		$form_content .='		</c> \n';
		$form_content .='		</r> \n';
		if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
			$form_content .= '		<r> <c> \n';
			$form_content .= '				<div class="form-hints"> \n';
			$form_content .= '				' . esc_attr__( 'Date', 'booking' ) . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
			$form_content .= '			   	' . esc_attr__( 'Time', 'booking' ) . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
			$form_content .= '				</div> \n';
			$form_content .= '			</c> \n';
			$form_content .= '			<c>' . esc_attr__( 'Total Cost', 'booking' ) . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
			$form_content .= '	    </r> \n';
		}
		$form_content .='	    <hr> \n';
		$form_content .='		<r> \n';
		$form_content .='			<c class="wpbc_aling_right" > \n';
		$form_content .='     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2" > \n';
		$form_content .='					' . esc_attr__( 'Next', 'booking' ) . '</a> \n';
		$form_content .='			</c> \n';
		$form_content .='		</r> \n';
		$form_content .='	</div> \n';
		$form_content .='	<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2 wpbc_wizard_step_hidden" style="display:none;clear:both;"> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' *</l><br />[text* name] </c> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' *</l><br />[text* secondname] </c> \n';
		$form_content .='	</r> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' *</l><br />[email* email] </c> \n';
		$form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . '</l><br />[text phone] </c> \n';
		$form_content .='	</r> \n';
		$form_content .='	<r> \n';
 		$form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . '</l><spacer></spacer> \n';
		$form_content .='			[textarea details] </c> \n';
		$form_content .='	</r> \n';
		$form_content .='	<spacer>height:10px;</spacer> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
		$form_content .='		<c> [captcha] </c> \n';
		$form_content .='	</r> \n';
		if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
			$form_content .='	<r> <c> \n';
			$form_content .='			<div class="form-hints"> \n';
			$form_content .='				' . esc_attr__( 'Date', 'booking' ) . ': &nbsp;<strong>[selected_dates_hint]</strong><spacer>width:2em;</spacer> \n';
			$form_content .='			   	' . esc_attr__( 'Time', 'booking' ) . ': &nbsp;<strong>[start_time_hint] - [end_time_hint]</strong> \n';
			$form_content .='			</div> \n';
			$form_content .='		</c> \n';
			$form_content .='		<c>' . esc_attr__( 'Total Cost', 'booking' ) . ': &nbsp;<strong>[cost_hint]</strong></c> \n';
			$form_content .='	</r> \n';
		}
		$form_content .='	<hr> \n';
		$form_content .='	<r> \n';
		$form_content .='		<c class="wpbc_aling_right" > \n';
		$form_content .='			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1"> \n';
		$form_content .='				' . esc_attr__( 'Back', 'booking' ) . '</a><spacer>width:20px;</spacer> \n';
		$form_content .='			[submit "' . esc_attr__( 'Send', 'booking' ) . '"] \n';
		$form_content .='		</c> \n';
		$form_content .='	</r> \n';
		$form_content .='	</div> \n';
		$form_content .='</div> \n';
	}

	// FixIn: 10.12.4.9.
	if ( in_array( $form_type, array( 'wizard_no_times_a' ) ) ) {
		$form_content = '<style type="text/css">.wpbc_container_booking_form .wpbc__field .wpbc_calendar_wraper:has(.cal_month_num_1){width:auto;} .wpbc_hints{float: right;margin: 10px;}</style>';
        $form_content .='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n';
        $form_content .='      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n';
		$form_content .='<div class="wpbc_wizard__border_container"> \n';
        $form_content .='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n';
        $form_content .='		<r> \n';
        $form_content .='			<c> [calendar] \n';
        if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .='			<div class="wpbc_hints"> \n';
            $form_content .='				' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
            $form_content .='				([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
            $form_content .='				' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
            $form_content .='			</div> \n';
        }
        $form_content .='			</c> \n';
        $form_content .='		</r> <hr> \n';
        $form_content .='		<r> \n';
        $form_content .='			<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        $form_content .='     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2" > \n';
        $form_content .='				' . esc_attr__( 'Next', 'booking' ) . ' \n';
        $form_content .='				</a> \n';
        $form_content .='			</div> \n';
        $form_content .='		</r> \n';
        $form_content .='</div> \n';
        $form_content .='<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2 wpbc_wizard_step_hidden" style="display:none;clear:both;"> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'First Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* name] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Last Name', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[text* secondname] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Email', 'booking' ) . ' (' . esc_attr__( 'required', 'booking' ) . '):</l><br />[email* email] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> <l>' . esc_attr__( 'Details', 'booking' ) . ':</l><spacer></spacer> \n';
        $form_content .='			[textarea details] </c> \n';
        $form_content .='	</r> \n';
        $form_content .='	<spacer>height:10px;</spacer> \n';
        $form_content .='	<r> \n';
        $form_content .='		<c> [checkbox* term_and_condition use_label_element "' . esc_attr__( 'I Accept term and conditions', 'booking' ) . '"] </c> \n';
        $form_content .='		<c> [captcha] </c> \n';
        $form_content .='	</r> \n';
        if ( class_exists( 'wpdev_bk_biz_m' ) ){                                              // >= biz_m
            $form_content .='	<r> \n';
            $form_content .='		<c><div class="form-hints"> \n';
            $form_content .='			' . esc_attr__( 'Dates', 'booking' ) . ': <strong>[selected_short_timedates_hint]</strong> \n';
            $form_content .='			([nights_number_hint] - ' . esc_attr__( 'night(s)', 'booking' ) . ')<br> \n';
            $form_content .='			' . esc_attr__( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br> \n';
            $form_content .='		</div></c> \n';
            $form_content .='	</r> \n';
        }
        $form_content .='	<hr> \n';
        $form_content .='	<r> \n';
        $form_content .='		<div class="wpbc__field" style="justify-content: flex-end;"> \n';
        $form_content .='			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1">' . esc_attr__( 'Back', 'booking' ) . '</a>&nbsp;&nbsp;&nbsp; \n';
        $form_content .='			[submit "' . esc_attr__( 'Send', 'booking' ) . '"] \n';
        $form_content .='		</div> \n';
        $form_content .='	</r> \n';
        $form_content .='</div> \n';
		$form_content .='</div> \n';
	}

	return $form_content;
}


function wpbc_get__predefined_booking_data__template( $form_type ){

	$form_content = '';
	// FixIn: 10.7.1.4.
    if (
        ( in_array( $form_type, array( 'appointments30' ) ) )
    ){
        $form_content = '';
        $form_content .='<div class="standard-content-form"> \n';
        $form_content .='    <b>' . esc_attr__( 'Times', 'booking' ) . '</b>:      <f>[rangetime]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'First Name', 'booking' ) . '</b>: <f>[name]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Last Name', 'booking' ) . '</b>:  <f>[secondname]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Email', 'booking' ) . '</b>:      <f>[email]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Phone', 'booking' ) . '</b>:      <f>[phone]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Details', 'booking' ) . '</b>:    <f>[details]</f>\n';
        $form_content .='</div>';
    }
    if ( ($form_type == 'payment')  || ($form_type == 'paymentUS') || ( 'fields2columns' == $form_type ) || ( 'fields3columns' == $form_type ) ) {               //FixIn: 8.7.7.15      // FixIn: 8.8.2.6.
        $form_content = '';
        $form_content .='<div class="standard-content-form"> \n';
        $form_content .='    <b>' . esc_attr__( 'First Name', 'booking' ) . '</b>: <f>[name]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Last Name', 'booking' ) . '</b>:  <f>[secondname]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Email', 'booking' ) . '</b>:      <f>[email]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Phone', 'booking' ) . '</b>:      <f>[phone]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Address', 'booking' ) . '</b>:    <f>[address]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'City', 'booking' ) . '</b>:       <f>[city]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Post code', 'booking' ) . '</b>:  <f>[postcode]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Country', 'booking' ) . '</b>:    <f>[country]</f><br>\n';
        if ( $form_type == 'paymentUS' ) {
        $form_content .='    <b>' . esc_attr__( 'State', 'booking' ) . '</b>:      <f>[state]</f><br>\n';
        }
        $form_content .='    <b>' . esc_attr__( 'Adults', 'booking' ) . '</b>:     <f>[visitors]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Children', 'booking' ) . '</b>:   <f>[children]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Details', 'booking' ) . '</b>:    <f>[details]</f>\n';
        $form_content .='</div>';
    }

    if (
           ( in_array( $form_type, array( 'timesweek', 'fields2columnstimes' ) ) )
        || ( in_array( $form_type, array( 'times', 'times30', 'times15', 'times120', 'times60', 'times60_24h', 'endtime', 'durationtime' ) ) )
        || ( in_array( $form_type, array( 'wizard_times', 'wizard_times30', 'wizard_times15', 'wizard_times120', 'wizard_times60', 'wizard_times60_24h' ) ) )
    ){
        $form_content = '';
        $form_content .='<div class="standard-content-form"> \n';
        $form_content .='    <b>' . esc_attr__( 'Times', 'booking' ) . '</b>:      <f>[rangetime]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'First Name', 'booking' ) . '</b>: <f>[name]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Last Name', 'booking' ) . '</b>:  <f>[secondname]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Email', 'booking' ) . '</b>:      <f>[email]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Phone', 'booking' ) . '</b>:      <f>[phone]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Adults', 'booking' ) . '</b>:     <f>[visitors]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Children', 'booking' ) . '</b>:   <f>[children]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Details', 'booking' ) . '</b>:    <f>[details]</f>\n';
        $form_content .='</div>';
    }

    if ( 'hints-dev' == $form_type ){
        $form_content = '';
        $form_content .='<div class="standard-content-form"> \n';
        $form_content .='    <b>' . esc_attr__( 'First Name', 'booking' ) . '</b>: <f>[name]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Last Name', 'booking' ) . '</b>:  <f>[secondname]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Email', 'booking' ) . '</b>:      <f>[email]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Adults', 'booking' ) . '</b>:     <f>[visitors]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Children', 'booking' ) . '</b>:   <f>[children]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Details', 'booking' ) . '</b>:    <f>[details]</f>\n';
        $form_content .='</div>';
    }

    if (  ($form_type == 'wizard') || ($form_type == '2collumns') || ($form_content == 'hints') || ($form_content == '') ){
        $form_content = '';
        $form_content .='<div class="standard-content-form"> \n';
        $form_content .='    <b>' . esc_attr__( 'First Name', 'booking' ) . '</b>: <f>[name]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Last Name', 'booking' ) . '</b>:  <f>[secondname]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Email', 'booking' ) . '</b>:      <f>[email]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Phone', 'booking' ) . '</b>:      <f>[phone]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Adults', 'booking' ) . '</b>:     <f>[visitors]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Children', 'booking' ) . '</b>:   <f>[children]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Details', 'booking' ) . '</b>:    <f>[details]</f>\n';
        $form_content .='</div>';
    }
 	// FixIn: 10.12.4.8.
	if ( in_array( $form_type, array( 'appointments_service_a', 'appointments_service_b', 'appointments_service_c' ) ) ) {
		$form_content = '';
		$form_content .= '<div class="standard-content-form"> \n';
		$form_content .= '    <b>' . esc_attr__( 'Service', 'booking' ) . '</b>:    <f>[durationtime_val]</f><br>\n';
		$form_content .= '    <b>' . esc_attr__( 'Start Times', 'booking' ) . '</b>:<f>[starttime]</f><br>\n';
		$form_content .= '    <b>' . esc_attr__( 'First Name', 'booking' ) . '</b>: <f>[name]</f><br>\n';
		$form_content .= '    <b>' . esc_attr__( 'Last Name', 'booking' ) . '</b>:  <f>[secondname]</f><br>\n';
		$form_content .= '    <b>' . esc_attr__( 'Email', 'booking' ) . '</b>:      <f>[email]</f><br>\n';
		$form_content .= '    <b>' . esc_attr__( 'Phone', 'booking' ) . '</b>:      <f>[phone]</f><br>\n';
		$form_content .= '    <b>' . esc_attr__( 'Details', 'booking' ) . '</b>:    <f>[details]</f>\n';
		$form_content .= '</div>';
	}

 	// FixIn: 10.12.4.9.
	if ( in_array( $form_type, array( 'wizard_no_times_a' ) ) ) {
		$form_content = '';
		$form_content .= '<div class="standard-content-form"> \n';
        $form_content .='    <b>' . esc_attr__( 'First Name', 'booking' ) . '</b>: <f>[name]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Last Name', 'booking' ) . '</b>:  <f>[secondname]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Email', 'booking' ) . '</b>:      <f>[email]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Phone', 'booking' ) . '</b>:      <f>[phone]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Adults', 'booking' ) . '</b>:     <f>[visitors]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Children', 'booking' ) . '</b>:   <f>[children]</f><br>\n';
        $form_content .='    <b>' . esc_attr__( 'Details', 'booking' ) . '</b>:    <f>[details]</f>\n';
		$form_content .= '</div>';
	}

	return $form_content;
}
