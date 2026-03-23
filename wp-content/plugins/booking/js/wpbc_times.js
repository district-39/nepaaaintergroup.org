var start_time_checking_index;

/**
 * Get dots for partially  booked dates.
 *    For parent booking resources with  specific capacity,
 *    System do not show partially booked dates,  if at least one child booking resource fully  available
 *    Otherwise it show maximum number of time-slot in one specific child booking resource
 *
 * @param param_calendar_id				ID of booking resource
 * @param my_thisDateTime				timestamp of date
 * @returns {string}
 */
function wpbc_show_date_info_top( param_calendar_id, my_thisDateTime ) {

	var resource_id = parseInt(param_calendar_id.replace("calendar_booking", ''));

	if (isNaN(resource_id)) {
		return '';  // FixIn: 10.9.5.2.
	}

	// console.log( _wpbc.bookings_in_calendar__get( resource_id ) );		// for debug

	// 1. Get child booking resources  or single booking resource  that  exist  in dates :	[1] | [1,14,15,17]
	var child_resources_arr = wpbc_clone_obj(_wpbc.booking__get_param_value(resource_id, 'resources_id_arr__in_dates'));

	// '2023-08-21'
	var sql_date = wpbc__get__sql_class_date(new Date(my_thisDateTime));

	var child_resource_id;
	var merged_seconds;
	var dots_count = 0;

	var dots_in__resources = [];

	// Loop all resources ID.
	if (child_resources_arr !== null) {
		for (child_resource_id of child_resources_arr) {                                                                    // FixIn: 10.9.5.1.

			// _wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[12].booked_time_slots.merged_seconds		= [ "07:00:11 - 07:30:02", "10:00:11 - 00:00:00" ]
			// _wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[2].booked_time_slots.merged_seconds			= [  [ 25211, 27002 ], [ 36011, 86400 ]  ]

			if (false !== _wpbc.bookings_in_calendar__get_for_date(resource_id, sql_date)) {
				merged_seconds = _wpbc.bookings_in_calendar__get_for_date(resource_id, sql_date)[child_resource_id].booked_time_slots.merged_seconds;		// [  [ 25211, 27002 ], [ 36011, 86400 ]  ]
			} else {
				merged_seconds = [];
			}

			if (0 === merged_seconds.length) {
				return ''; 																		// Day available
			}

			for (var i = 0; i < merged_seconds.length; i++) {
				if (!wpbc_is_this_timeslot__full_day_booked(merged_seconds[i])) {			// Check  if this fully  booked date. If yes,  then  do not count it
					dots_count++;
				}
			}

			dots_in__resources.push(dots_count);
			dots_count = 0;
		}
	}
	var dots_count_max = Math.max.apply( Math, dots_in__resources );						// Get maximum value in array [ 1, 5, 3]  ->  5

	var dot_content = '';
	for ( var d = 0; d < dots_count_max; d++ ){
		dot_content += '&centerdot;';
	}

	dot_content = ( '' !== dot_content ) ? '<div class="wpbc_time_dots">' + dot_content + '</div>' : '';

	return dot_content;
}

/**
 * Show Date Info at  bottom  of the  Day Cell
 *
 * @param param_calendar_id		'calendar_booking1'
 * @param my_thisDateTime		56567557757
 * @returns {string}
 */
function wpbc_show_date_info_bottom( param_calendar_id, my_thisDateTime ) {

	var bottom_hint_arr = [];

	// Cost Hint
	if ( typeof (wpbc_show_day_cost_in_date_bottom) == 'function' ) {
		var cost_hint = wpbc_show_day_cost_in_date_bottom( param_calendar_id, my_thisDateTime );
		if ( '' !== cost_hint ) {
			bottom_hint_arr.push( wpbc_show_day_cost_in_date_bottom( param_calendar_id, my_thisDateTime ) );
		}
	}

	// Availability Hint		// FixIn: 10.6.4.1.
	var availability = wpbc_get_in_date_availability_hint( param_calendar_id, my_thisDateTime )
	if ( '' !== availability ) {
		bottom_hint_arr.push( availability );
	}

	var bottom_hint = bottom_hint_arr.join( '' );
	return bottom_hint;
}

/**
 * Get Availability Hint for in Day Cell
 *
 * @param param_calendar_id
 * @param my_thisDateTime
 * @returns {string}
 */
function wpbc_get_in_date_availability_hint( param_calendar_id, my_thisDateTime ) {								        // FixIn: 10.6.4.1.

	/*
	var sql_date 	= wpbc__get__sql_class_date( new Date( my_thisDateTime ) );						// '2023-08-21'
	var resource_id = parseInt( param_calendar_id.replace( "calendar_booking", '' ) );				// 1
	var day_availability_obj = _wpbc.bookings_in_calendar__get_for_date( resource_id, sql_date );	// obj

	if ( 'undefined' !== typeof (day_availability_obj.day_availability) ) {
		return '<strong>' + day_availability_obj.day_availability + '</strong> ' + 'available';
	} else {
		return '';
	}
	*/

	var resource_id = parseInt( param_calendar_id.replace( "calendar_booking", '' ) );

	// console.log( _wpbc.bookings_in_calendar__get( resource_id ) );		// for debug

	// 1. Get child booking resources  or single booking resource  that  exist  in dates :	[1] | [1,14,15,17]
	// var child_resources_arr = wpbc_clone_obj( _wpbc.booking__get_param_value( resource_id, 'resources_id_arr__in_dates' ) );

	// '2023-08-21'
	var sql_date = wpbc__get__sql_class_date( new Date( my_thisDateTime ) );

    var hint__in_day__availability = '';

    var get_for_date_obj = _wpbc.bookings_in_calendar__get_for_date( resource_id, sql_date );

    if ( false !== get_for_date_obj ){

        if (
               (undefined != get_for_date_obj[ 'summary' ])
            && (undefined != get_for_date_obj[ 'summary' ].hint__in_day__availability)
        ){
            hint__in_day__availability = get_for_date_obj[ 'summary' ].hint__in_day__availability;		// "5 available"
        }

    }

    return hint__in_day__availability;
}


/**
 * Hide Tippy tooltip on scroll, to prevent issue on mobile touch devices of showing tooltip at top left corner!
 * @param evt
 */
jQuery( window ).on( 'scroll', function ( event ){													//FixIn: 9.2.1.5	// FixIn: 9.4.3.3.
	if ( 'function' === typeof( wpbc_tippy ) ){
		wpbc_tippy.hideAll();
	}
} );


/**
 * Check if in booking form  exist  times fields for booking for specific time-slot
 * @param resource_id
 * @param form_elements
 * @returns {boolean}
 */
function wpbc_is_time_field_in_booking_form( resource_id, form_elements ){											// FixIn: 8.2.1.28.

	var count = form_elements.length;
	var start_time = false;
	var end_time = false;
	var duration = false;
	var element;

	/**
	 *  Get from booking form  'rangetime', 'durationtime', 'starttime', 'endtime',  if exists.
	 */
	for ( var i = 0; i < count; i++ ){

		element = form_elements[ i ];

		// Skip elements from garbage
		if ( jQuery( element ).closest( '.booking_form_garbage' ).length ){											// FixIn: 7.1.2.14.
			continue;
		}

		if (
			   ( element.name != undefined )
			&& ( element.name.indexOf( 'hint' ) !== -1 )				// FixIn: 9.5.5.2.
		){
			var my_element = element.name; //.toString();
			if ( my_element.indexOf( 'rangetime' ) !== -1 ){                       	// Range Time

				return true;
			}
			if ( (my_element.indexOf( 'durationtime' ) !== -1) ){                	// Duration
				duration = element.value;
			}
			if ( my_element.indexOf( 'starttime' ) !== -1 ){                     	// Start Time
				start_time = element.value;
			}
			if ( my_element.indexOf( 'endtime' ) !== -1 ){                        	// End Time
				end_time = element.value;
			}
		}
	}

	// Duration get Values
	if ( ( duration !== false ) && ( start_time !== false ) ){  // we have Duration and Start time
		return true;
	}

	if ( ( start_time !== false ) && ( end_time !== false ) ){  // we have End time and Start time
		return true;
	}

	return false;
}


/**
 * Check if the selected date/time is already in the past compared to "today_arr".
 *
 * Behavior notes:
 * - If "is_enabled_change_over" is enabled, always returns false (no restriction).
 * - `sort_date_array[0]` is expected to be the earliest selected date (sorted ascending).
 * - `today_arr` is expected to be: [YYYY, MM, DD, HH, mm] (strings or numbers).
 * - `myTime` is expected to be "HH:MM" (24h).
 *
 * @param {string} myTime Time string in "HH:MM" format.
 * @param {Array<Array<string|number>>} sort_date_array Array of date arrays; first element is used as date to check.
 * @returns {boolean} True if selected date/time is in the past, otherwise false.
 */
function wpbc_is_time_in_the_past_for_today(myTime, sort_date_array) {

	// FixIn: 10.14.10.2.
	if ( true === _wpbc.get_other_param( 'is_enabled_change_over' ) ) {
		return false;
	}
	var date_to_check = sort_date_array[0];
	if ( parseInt( date_to_check[0] ) < parseInt( _wpbc.get_other_param( 'today_arr' )[0] ) ) {
		return true;
	}
	if (
		(parseInt( date_to_check[0] ) == parseInt( _wpbc.get_other_param( 'today_arr' )[0] )) &&
		(parseInt( date_to_check[1] ) < parseInt( _wpbc.get_other_param( 'today_arr' )[1] ))
	) {
		return true;
	}
	if (
		(parseInt( date_to_check[0] ) == parseInt( _wpbc.get_other_param( 'today_arr' )[0] )) &&
		(parseInt( date_to_check[1] ) == parseInt( _wpbc.get_other_param( 'today_arr' )[1] )) &&
		(parseInt( date_to_check[2] ) < parseInt( _wpbc.get_other_param( 'today_arr' )[2] ))
	) {
		return true;
	}
	if (
		(parseInt( date_to_check[0] ) == parseInt( _wpbc.get_other_param( 'today_arr' )[0] )) &&
		(parseInt( date_to_check[1] ) == parseInt( _wpbc.get_other_param( 'today_arr' )[1] )) &&
		(parseInt( date_to_check[2] ) == parseInt( _wpbc.get_other_param( 'today_arr' )[2] ))
	) {
		var mytime_value = myTime.split( ":" );
		mytime_value     = parseInt( mytime_value[0] ) * 60 + parseInt( mytime_value[1] );

		var current_time_value = parseInt( _wpbc.get_other_param( 'today_arr' )[3] ) * 60 + parseInt( _wpbc.get_other_param( 'today_arr' )[4] );

		if ( current_time_value > mytime_value ) {
			return true;
		}
	}
	return false;
}

/**
 * Validate a time string.
 *
 * Supported formats:
 *  - 24-hour time: "H:MM" or "HH:MM"
 *      Examples: "0:00", "09:30", "23:59", "24:00"
 *      Notes: "24:00" is allowed, but "24:01" is not.
 *
 *  - 12-hour time: "H:MM AM/PM" (AM/PM is case-insensitive; spaces are optional)
 *      Examples: "9:30 AM", "09:30pm", "12:05 PM"
 *
 * @param {string} time_str Raw time string entered by the user.
 * @returns {boolean} True if the string matches a valid supported time format.
 */
function wpbc_is_valid_time_string(time_str) {

	if ( typeof time_str !== 'string' ) {
		return false;
	}

	const str = time_str.trim();
	if ( str === '' ) {
		return false;
	}

	// 1-2 digit hours, 2 digit minutes, optional AM/PM (case-insensitive).
	const match = str.match( /^(\d{1,2}):(\d{2})(?:\s*([AaPp][Mm]))?$/ );
	if ( ! match ) {
		return false;
	}

	const hour   = Number( match[1] );
	const minute = Number( match[2] );
	const ampm   = match[3] ? match[3].toUpperCase() : null;

	if ( ! Number.isInteger( hour ) || ! Number.isInteger( minute ) ) {
		return false;
	}

	if ( minute < 0 || minute > 59 ) {
		return false;
	}

	// 12-hour mode (AM/PM provided): hour must be 1..12.
	if ( ampm ) {
		return hour >= 1 && hour <= 12;
	}

	// 24-hour mode (no AM/PM): hour must be 0..24, but 24 only allowed with :00.
	if ( hour < 0 || hour > 24 ) {
		return false;
	}
	if ( hour === 24 && minute !== 0 ) {
		return false;
	}

	return true;
}


//TODO: Continue Refactoring here 2018-04-21

	//PS: This function  from personal.js
	function wpbc_is_this_time_selection_not_available( resource_id, form_elements ){

		// Skip this checking if we are in the Admin  panel at Add booking page
		if ( location.href.indexOf( 'page=wpbc-new' ) > 0 ) {
			return false;
		}

		var count = form_elements.length;
		var start_time = false;
		var end_time = false;
		var duration = false;
		var element;
		var element_start = false;
		var element_end = false;
		var element_duration = false;
		var element_rangetime = false;

		/**
		 *  Get from booking form  'rangetime', 'durationtime', 'starttime', 'endtime',  if exists.
		 */
		for ( var i = 0; i < count; i++ ){

			element = form_elements[ i ];

			// Skip elements from garbage
			if ( jQuery( element ).closest( '.booking_form_garbage' ).length ){											// FixIn: 7.1.2.14.
				continue;
			}

			if (
				   ( element.name != undefined )
				&& ( element.name.indexOf( 'hint' ) === -1 )				//FixIn: 9.5.5.2		// FixIn: 9.6.3.9.
			){
				var my_element = element.name; //.toString();
				if ( my_element.indexOf( 'rangetime' ) !== -1 ){                       	// Range Time
					if ( element.value == '' ){                                 										//FixIn: 7.0.Beta.19
					 	var notice_message_id = wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required' ) );
						return true;
					}
					var my_rangetime = element.value.split( '-' );
					if ( my_rangetime.length > 1 ){
						start_time = my_rangetime[ 0 ].replace( /(^\s+)|(\s+$)/g, "" ); 	// Trim
						end_time = my_rangetime[ 1 ].replace( /(^\s+)|(\s+$)/g, "" );
						element_rangetime = element;
					}
				}
				if ( (my_element.indexOf( 'durationtime' ) !== -1) ){                	// Duration
					duration = element.value;
					element_duration = element;
				}
				if ( my_element.indexOf( 'starttime' ) !== -1 ){                     	// Start Time
					start_time = element.value;
					element_start = element;
				}
				if ( my_element.indexOf( 'endtime' ) !== -1 ){                        	// End Time
					end_time = element.value;
					element_end = element;
				}
			}
		}


		// Duration get Values
		if ( (duration !== false) && (start_time !== false) ){  // we have Duration and Start time so  try to get End time

			var mylocalstarttime = start_time.split( ':' );
			var d = new Date( 1980, 1, 1, mylocalstarttime[ 0 ], mylocalstarttime[ 1 ], 0 );

			var my_duration = duration.split( ':' );
			my_duration = my_duration[ 0 ] * 60 * 60 * 1000 + my_duration[ 1 ] * 60 * 1000;
			d.setTime( d.getTime() + my_duration );

			var my_hours = d.getHours();
			if ( my_hours < 10 ) my_hours = '0' + (my_hours + '');
			var my_minutes = d.getMinutes();
			if ( my_minutes < 10 ) my_minutes = '0' + (my_minutes + '');

			// We are get end time
			end_time = (my_hours + '') + ':' + (my_minutes + '');
			if ( end_time == '00:00' ) end_time = '23:59';
		}


		if ( (start_time === false) || (end_time === false) ){                     // We do not have Start or End time or Both of them, so do not check it

			return false;

		} else {

			var valid_time = true;
			if ( (start_time == '') || (end_time == '') ) valid_time = false;

			if ( !wpbc_is_valid_time_string( start_time ) ) valid_time = false;
			if ( !wpbc_is_valid_time_string( end_time ) ) valid_time = false;

			if ( valid_time === true )
				if (
					(typeof(checkRecurentTimeInside) == 'function') &&

					( _wpbc.get_other_param( 'is_enabled_booking_recurrent_time' ) == true )
				){                                                                // Recheck Time here !!!
					valid_time = checkRecurentTimeInside( [ start_time, end_time ], resource_id );
				} else {

					if ( typeof(checkTimeInside) == 'function' ){
						valid_time = checkTimeInside( start_time, true, resource_id );
					}

					if ( valid_time === true ){
						if ( typeof(checkTimeInside) == 'function' ){
							valid_time = checkTimeInside( end_time, false, resource_id );
						}
					}
				}

			if ( valid_time !== true ){
				//return false;                                                  // do not show warning for setting pending days selectable,  if making booking for time-slot   // FixIn: 7.0.1.23.
				if ( (_wpbc.get_other_param( 'is_enabled_change_over' )) && (element_start !== false) && (element_end !== false) ){      // FixIn: 6.1.1.1.
					wpbc_front_end__show_message__warning_under_element( '#date_booking' + resource_id, _wpbc.get_message( 'message_check_no_selected_dates' )  );
				}
				if ( element_rangetime !== false ){ wpbc_front_end__show_message__warning_under_element( element_rangetime, _wpbc.get_message( 'message_error_range_time' ) ); }
				if ( element_duration !== false ){  wpbc_front_end__show_message__warning_under_element( element_duration, _wpbc.get_message( 'message_error_duration_time' ) ); }
				if ( element_start !== false ){ 	wpbc_front_end__show_message__warning_under_element( element_start, _wpbc.get_message( 'message_error_start_time' ) ); }
				if ( element_end !== false ){ 		wpbc_front_end__show_message__warning_under_element( element_end, _wpbc.get_message( 'message_error_end_time' ) ); }

				return true;

			} else {
				return false;
			}

		}


	}

	function checkTimeInside( mytime, is_start_time, bk_type ){

		var my_dates_str = document.getElementById( 'date_booking' + bk_type ).value;                 // GET DATES From TEXTAREA

		if ( my_dates_str.indexOf( ' - ' ) != 0 ){

			my_dates_str = my_dates_str.replace( ' - ', ', ' );										// FixIn: 10.2.3.2.
		}

		return checkTimeInsideProcess( mytime, is_start_time, bk_type, my_dates_str );
	}

	function checkRecurentTimeInside( my_rangetime, bk_type ){

		var valid_time = true;
		var my_dates_str = document.getElementById( 'date_booking' + bk_type ).value;                 // GET DATES From TEXTAREA
		// recurrent time check for all days in loop

		var date_array = my_dates_str.split( ", " );
		if ( date_array.length == 2 ){ // This recheck is need for editing booking, with single day
			if ( date_array[ 0 ] == date_array[ 1 ] ){
				date_array = [ date_array[ 0 ] ];
			}
		}
		var temp_date_str = '';
		for ( var i = 0; i < date_array.length; i++ ){  // Get SORTED selected days array
			temp_date_str = date_array[ i ];
			if ( checkTimeInsideProcess( my_rangetime[ 0 ], true, bk_type, temp_date_str ) == false ) valid_time = false;
			if ( checkTimeInsideProcess( my_rangetime[ 1 ], false, bk_type, temp_date_str ) == false ) valid_time = false;

		}

		return valid_time;
	}

	// Function check start and end time at selected days.
	function checkTimeInsideProcess( mytime, is_start_time, bk_type, my_dates_str ){
		var i, h, s, m;	// FixIn: 9.1.5.1.

		var date_array = my_dates_str.split( ',' );
		date_array = date_array.map( function (s) {
			return s.trim();
		} );
		if ( date_array.length == 2 ){ // This recheck is need for editing booking, with single day
			if ( date_array[ 0 ] == date_array[ 1 ] ){
				date_array = [ date_array[ 0 ] ];
			}
		}

		var temp_elemnt;
		var td_class;
		var sort_date_array = [];
		var work_date_array = [];
		var times_array = [];
		var is_check_for_time;

		for ( var i = 0; i < date_array.length; i++ ){  // Get SORTED selected days array
			temp_elemnt = date_array[ i ].split( "." );
			sort_date_array[ i ] = [ temp_elemnt[ 2 ], temp_elemnt[ 1 ] + '', temp_elemnt[ 0 ] + '' ]; // [2009,7,1],...
		}
		sort_date_array.sort();                                                                   // SORT    D a t e s
		for ( i = 0; i < sort_date_array.length; i++ ){                                  // trnasform to integers
			sort_date_array[ i ] = [ parseInt( sort_date_array[ i ][ 0 ] * 1 ), parseInt( sort_date_array[ i ][ 1 ] * 1 ), parseInt( sort_date_array[ i ][ 2 ] * 1 ) ]; // [2009,7,1],...
		}

		if ( is_start_time ) {
			if ( wpbc_is_time_in_the_past_for_today( mytime, sort_date_array ) ) {
				return false;
			}
		}

		//  CHECK FOR BOOKING INSIDE OF     S E L E C T E D    DAY RANGE AND FOR TOTALLY BOOKED DAYS AT THE START AND END OF RANGE
		work_date_array = sort_date_array;
		for ( var j = 0; j < work_date_array.length; j++ ){
			td_class = work_date_array[ j ][ 1 ] + '-' + work_date_array[ j ][ 2 ] + '-' + work_date_array[ j ][ 0 ];

			if ( (j == 0) || (j == (work_date_array.length - 1)) ) is_check_for_time = true;         // Check for time only start and end time
			else is_check_for_time = false;


		}  ///////////////////////////////////////////////////////////////////////////////////////////////////////


		// Check    START   OR    END   time for time no in correct fee range
		if ( is_start_time ) work_date_array = sort_date_array[ 0 ];
		else work_date_array = sort_date_array[ sort_date_array.length - 1 ];

		td_class = work_date_array[ 1 ] + '-' + work_date_array[ 2 ] + '-' + work_date_array[ 0 ];

		// Get dates and time from pending dates
		//deleted
		// Get dates and time from pending dates
		//deleted


		times_array.sort();                     // SORT TIMES

		var times_in_day = [];                  // array with all times
		var times_in_day_interval_marks = [];   // array with time interval marks 1- stsrt time 2 - end time


		for ( i = 0; i < times_array.length; i++ ){
			s = times_array[ i ][ 2 ];         // s = 2 - end time,   s = 1 - start time
			// Start close interval
			if ( (s == 2) && (i == 0) ){
				times_in_day[ times_in_day.length ] = 0;
				times_in_day_interval_marks[ times_in_day_interval_marks.length ] = 1;
			}
			// Normal
			times_in_day[ times_in_day.length ] = times_array[ i ][ 0 ] * 60 + parseInt( times_array[ i ][ 1 ] );
			times_in_day_interval_marks[ times_in_day_interval_marks.length ] = s;
			// End close interval
			if ( (s == 1) && (i == (times_array.length - 1)) ){
				times_in_day[ times_in_day.length ] = (24 * 60);
				times_in_day_interval_marks[ times_in_day_interval_marks.length ] = 2;
			}
		}

		// Get time from entered time
		var mytime_value = mytime.split( ":" );
		mytime_value = mytime_value[ 0 ] * 60 + parseInt( mytime_value[ 1 ] );

//alert('My time:'+ mytime_value + '  List of times: '+ times_in_day + '  Saved indexes: ' + start_time_checking_index + ' Days: ' + sort_date_array ) ;

		var start_i = 0;
		if ( start_time_checking_index != undefined )
			if ( start_time_checking_index[ 0 ] != undefined )
				if ( (!is_start_time) && (sort_date_array.length == 1) ){
					start_i = start_time_checking_index[ 0 ];
					/*start_i++;*/
				}
		i = start_i;

		// Main checking inside a day
		for ( i = start_i; i < times_in_day.length; i++ ){
			times_in_day[ i ] = parseInt( times_in_day[ i ] );
			mytime_value = parseInt( mytime_value );
			if ( is_start_time ){
				if ( mytime_value > times_in_day[ i ] ){
					// Its Ok, lets Loop to next item
				} else if ( mytime_value == times_in_day[ i ] ){
					if ( times_in_day_interval_marks[ i ] == 1 ){
						return false;     //start time is begin with some other interval
					} else {
						if ( (i + 1) <= (times_in_day.length - 1) ){
							if ( times_in_day[ i + 1 ] <= mytime_value ) return false;  //start time  is begin with next elemnt interval
							else {                                                 // start time from end of some other
								if ( sort_date_array.length > 1 )
									if ( (i + 1) <= (times_in_day.length - 1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
								start_time_checking_index = [ i, td_class, mytime_value ];
								return true;
							}
						}
						if ( sort_date_array.length > 1 )
							if ( (i + 1) <= (times_in_day.length - 1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
						start_time_checking_index = [ i, td_class, mytime_value ];
						return true;                                            // start time from end of some other
					}
				} else if ( mytime_value < times_in_day[ i ] ){
					if ( times_in_day_interval_marks[ i ] == 2 ){
						return false;     // start time inside of some interval
					} else {
						if ( sort_date_array.length > 1 )
							if ( (i + 1) <= (times_in_day.length - 1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
						start_time_checking_index = [ i, td_class, mytime_value ];
						return true;
					}
				}
			} else {
				if ( sort_date_array.length == 1 ){

					if ( start_time_checking_index != undefined )
						if ( start_time_checking_index[ 2 ] != undefined )

							if ( (start_time_checking_index[ 2 ] == times_in_day[ i ]) && (times_in_day_interval_marks[ i ] == 2) ){    // Good, because start time = end of some other interval and we need to get next interval for current end time.
							} else if ( times_in_day[ i ] < mytime_value ) return false;                 // some interval begins before end of curent "end time"
							else {
								if ( start_time_checking_index[ 2 ] >= mytime_value ) return false;  // we are select only one day and end time is earlythe starttime its wrong
								return true;                                                    // if we selected only one day so evrything is fine and end time no inside some other intervals
							}
				} else {
					if ( times_in_day[ i ] < mytime_value ) return false;                 // Some other interval start before we make end time in the booking at the end day selection
					else return true;
				}

			}
		}

		if ( is_start_time ) start_time_checking_index = [ i, td_class, mytime_value ];
		else {
			if ( start_time_checking_index != undefined )
				if ( start_time_checking_index[ 2 ] != undefined )
					if ( (sort_date_array.length == 1) && (start_time_checking_index[ 2 ] >= mytime_value) ) return false;  // we are select only one day and end time is earlythe starttime its wrong
		}
		return true;
	}
