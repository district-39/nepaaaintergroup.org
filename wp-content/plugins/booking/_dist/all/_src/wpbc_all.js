/**
 * =====================================================================================================================
 * JavaScript Util Functions		../includes/__js/utils/wpbc_utils.js
 * =====================================================================================================================
 */

/**
 * Trim  strings and array joined with  (,)
 *
 * @param string_to_trim   string / array
 * @returns string
 */
function wpbc_trim(string_to_trim) {

	if ( Array.isArray( string_to_trim ) ) {
		string_to_trim = string_to_trim.join( ',' );
	}

	if ( 'string' == typeof (string_to_trim) ) {
		string_to_trim = string_to_trim.trim();
	}

	return string_to_trim;
}

/**
 * Check if element in array
 *
 * @param array_here		array
 * @param p_val				element to  check
 * @returns {boolean}
 */
function wpbc_in_array(array_here, p_val) {
	for ( var i = 0, l = array_here.length; i < l; i++ ) {
		if ( array_here[i] == p_val ) {
			return true;
		}
	}
	return false;
}

/**
 * Prevent opening blank windows on WordPress playground for pseudo links like this: <a href="javascript:void(0)"> or # to stay in the same tab.
 */
(function () {
	'use strict';

	function is_playground_origin() {
		return location.origin === 'https://playground.wordpress.net';
	}

	function is_pseudo_link(a) {
		if ( !a || !a.getAttribute ) return true;
		var href = (a.getAttribute( 'href' ) || '').trim().toLowerCase();
		return (
			!href ||
			href === '#' ||
			href.indexOf( '#' ) === 0 ||
			href.indexOf( 'javascript:' ) === 0 ||
			href.indexOf( 'mailto:' ) === 0 ||
			href.indexOf( 'tel:' ) === 0
		);
	}

	function fix_target(a) {
		if ( ! a ) return;
		if ( is_pseudo_link( a ) || a.hasAttribute( 'data-wp-no-blank' ) ) {
			a.target = '_self';
		}
	}

	function init_fix() {
		// Optional: clean up current DOM (harmless—affects only pseudo/datamarked links).
		var nodes = document.querySelectorAll( 'a[href]' );
		for ( var i = 0; i < nodes.length; i++ ) fix_target( nodes[i] );

		// Late bubble-phase listeners (run after Playground's handlers)
		document.addEventListener( 'click', function (e) {
			var a = e.target && e.target.closest ? e.target.closest( 'a[href]' ) : null;
			if ( a ) fix_target( a );
		}, false );

		document.addEventListener( 'focusin', function (e) {
			var a = e.target && e.target.closest ? e.target.closest( 'a[href]' ) : null;
			if ( a ) fix_target( a );
		} );
	}

	function schedule_init() {
		if ( !is_playground_origin() ) return;
		setTimeout( init_fix, 1000 ); // ensure we attach after Playground's script.
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', schedule_init );
	} else {
		schedule_init();
	}
})();
"use strict";
/**
 * =====================================================================================================================
 *	includes/__js/wpbc/wpbc.js
 * =====================================================================================================================
 */

/**
 * Deep Clone of object or array
 *
 * @param obj
 * @returns {any}
 */
function wpbc_clone_obj( obj ){

	return JSON.parse( JSON.stringify( obj ) );
}



/**
 * Main _wpbc JS object
 */

var _wpbc = (function ( obj, $) {

	// Secure parameters for Ajax	------------------------------------------------------------------------------------
	var p_secure = obj.security_obj = obj.security_obj || {
															user_id: 0,
															nonce  : '',
															locale : ''
														  };
	obj.set_secure_param = function ( param_key, param_val ) {
		p_secure[ param_key ] = param_val;
	};

	obj.get_secure_param = function ( param_key ) {
		return p_secure[ param_key ];
	};


	// Calendars 	----------------------------------------------------------------------------------------------------
	var p_calendars = obj.calendars_obj = obj.calendars_obj || {
																		// sort            : "booking_id",
																		// sort_type       : "DESC",
																		// page_num        : 1,
																		// page_items_count: 10,
																		// create_date     : "",
																		// keyword         : "",
																		// source          : ""
																};

	/**
	 *  Check if calendar for specific booking resource defined   ::   true | false
	 *
	 * @param {string|int} resource_id
	 * @returns {boolean}
	 */
	obj.calendar__is_defined = function ( resource_id ) {

		return ('undefined' !== typeof( p_calendars[ 'calendar_' + resource_id ] ) );
	};

	/**
	 *  Create Calendar initializing
	 *
	 * @param {string|int} resource_id
	 */
	obj.calendar__init = function ( resource_id ) {

		p_calendars[ 'calendar_' + resource_id ] = {};
		p_calendars[ 'calendar_' + resource_id ][ 'id' ] = resource_id;
		p_calendars[ 'calendar_' + resource_id ][ 'pending_days_selectable' ] = false;

	};

	/**
	 * Check  if the type of this property  is INT
	 * @param property_name
	 * @returns {boolean}
	 */
	obj.calendar__is_prop_int = function ( property_name ) {													// FixIn: 9.9.0.29.

		var p_calendar_int_properties = ['dynamic__days_min', 'dynamic__days_max', 'fixed__days_num'];

		var is_include = p_calendar_int_properties.includes( property_name );

		return is_include;
	};


	/**
	 * Set params for all  calendars
	 *
	 * @param {object} calendars_obj		Object { calendar_1: {} }
	 * 												 calendar_3: {}, ... }
	 */
	obj.calendars_all__set = function ( calendars_obj ) {
		p_calendars = calendars_obj;
	};

	/**
	 * Get bookings in all calendars
	 *
	 * @returns {object|{}}
	 */
	obj.calendars_all__get = function () {
		return p_calendars;
	};

	/**
	 * Get calendar object   ::   { id: 1, … }
	 *
	 * @param {string|int} resource_id				  '2'
	 * @returns {object|boolean}					{ id: 2 ,… }
	 */
	obj.calendar__get_parameters = function ( resource_id ) {

		if ( obj.calendar__is_defined( resource_id ) ){

			return p_calendars[ 'calendar_' + resource_id ];
		} else {
			return false;
		}
	};

	/**
	 * Set calendar object   ::   { dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }
	 *
	 * if calendar object  not defined, then  it's will be defined and ID set
	 * if calendar exist, then  system set  as new or overwrite only properties from calendar_property_obj parameter,  but other properties will be existed and not overwrite, like 'id'
	 *
	 * @param {string|int} resource_id				  '2'
	 * @param {object} calendar_property_obj					  {  dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }  }
	 * @param {boolean} is_complete_overwrite		  if 'true' (default: 'false'),  then  only overwrite or add  new properties in  calendar_property_obj
	 * @returns {*}
	 *
	 * Examples:
	 *
	 * Common usage in PHP:
	 *   			echo "  _wpbc.calendar__set(  " .intval( $resource_id ) . ", { 'dates': " . wp_json_encode( $availability_per_days_arr ) . " } );";
	 */
	obj.calendar__set_parameters = function ( resource_id, calendar_property_obj, is_complete_overwrite = false  ) {

		if ( (!obj.calendar__is_defined( resource_id )) || (true === is_complete_overwrite) ){
			obj.calendar__init( resource_id );
		}

		for ( var prop_name in calendar_property_obj ){

			p_calendars[ 'calendar_' + resource_id ][ prop_name ] = calendar_property_obj[ prop_name ];
		}

		return p_calendars[ 'calendar_' + resource_id ];
	};

	/**
	 * Set property  to  calendar
	 * @param resource_id	"1"
	 * @param prop_name		name of property
	 * @param prop_value	value of property
	 * @returns {*}			calendar object
	 */
	obj.calendar__set_param_value = function ( resource_id, prop_name, prop_value ) {

		if ( (!obj.calendar__is_defined( resource_id )) ){
			obj.calendar__init( resource_id );
		}

		p_calendars[ 'calendar_' + resource_id ][ prop_name ] = prop_value;

		return p_calendars[ 'calendar_' + resource_id ];
	};

	/**
	 *  Get calendar property value   	::   mixed | null
	 *
	 * @param {string|int}  resource_id		'1'
	 * @param {string} prop_name			'selection_mode'
	 * @returns {*|null}					mixed | null
	 */
	obj.calendar__get_param_value = function( resource_id, prop_name ){

		if (
			   ( obj.calendar__is_defined( resource_id ) )
			&& ( 'undefined' !== typeof ( p_calendars[ 'calendar_' + resource_id ][ prop_name ] ) )
		){
			// FixIn: 9.9.0.29.
			if ( obj.calendar__is_prop_int( prop_name ) ){
				p_calendars[ 'calendar_' + resource_id ][ prop_name ] = parseInt( p_calendars[ 'calendar_' + resource_id ][ prop_name ] );
			}
			return  p_calendars[ 'calendar_' + resource_id ][ prop_name ];
		}

		return null;		// If some property not defined, then null;
	};
	// -----------------------------------------------------------------------------------------------------------------


	// Bookings 	----------------------------------------------------------------------------------------------------
	var p_bookings = obj.bookings_obj = obj.bookings_obj || {
																// calendar_1: Object {
 																//						   id:     1
 																//						 , dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, …
																// }
															};

	/**
	 *  Check if bookings for specific booking resource defined   ::   true | false
	 *
	 * @param {string|int} resource_id
	 * @returns {boolean}
	 */
	obj.bookings_in_calendar__is_defined = function ( resource_id ) {

		return ('undefined' !== typeof( p_bookings[ 'calendar_' + resource_id ] ) );
	};

	/**
	 * Get bookings calendar object   ::   { id: 1 , dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }
	 *
	 * @param {string|int} resource_id				  '2'
	 * @returns {object|boolean}					{ id: 2 , dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }
	 */
	obj.bookings_in_calendar__get = function( resource_id ){

		if ( obj.bookings_in_calendar__is_defined( resource_id ) ){

			return p_bookings[ 'calendar_' + resource_id ];
		} else {
			return false;
		}
	};

	/**
	 * Set bookings calendar object   ::   { dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }
	 *
	 * if calendar object  not defined, then  it's will be defined and ID set
	 * if calendar exist, then  system set  as new or overwrite only properties from calendar_obj parameter,  but other properties will be existed and not overwrite, like 'id'
	 *
	 * @param {string|int} resource_id				  '2'
	 * @param {object} calendar_obj					  {  dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }  }
	 * @returns {*}
	 *
	 * Examples:
	 *
	 * Common usage in PHP:
	 *   			echo "  _wpbc.bookings_in_calendar__set(  " .intval( $resource_id ) . ", { 'dates': " . wp_json_encode( $availability_per_days_arr ) . " } );";
	 */
	obj.bookings_in_calendar__set = function( resource_id, calendar_obj ){

		if ( ! obj.bookings_in_calendar__is_defined( resource_id ) ){
			p_bookings[ 'calendar_' + resource_id ] = {};
			p_bookings[ 'calendar_' + resource_id ][ 'id' ] = resource_id;
		}

		for ( var prop_name in calendar_obj ){

			p_bookings[ 'calendar_' + resource_id ][ prop_name ] = calendar_obj[ prop_name ];
		}

		return p_bookings[ 'calendar_' + resource_id ];
	};

	// Dates

	/**
	 *  Get bookings data for ALL Dates in calendar   ::   false | { "2023-07-22": {…}, "2023-07-23": {…}, … }
	 *
	 * @param {string|int} resource_id			'1'
	 * @returns {object|boolean}				false | Object {
																"2023-07-24": Object { ['summary']['status_for_day']: "available", day_availability: 1, max_capacity: 1, … }
																"2023-07-26": Object { ['summary']['status_for_day']: "full_day_booking", ['summary']['status_for_bookings']: "pending", day_availability: 0, … }
																"2023-07-29": Object { ['summary']['status_for_day']: "resource_availability", day_availability: 0, max_capacity: 1, … }
																"2023-07-30": {…}, "2023-07-31": {…}, …
															}
	 */
	obj.bookings_in_calendar__get_dates = function( resource_id){

		if (
			   ( obj.bookings_in_calendar__is_defined( resource_id ) )
			&& ( 'undefined' !== typeof ( p_bookings[ 'calendar_' + resource_id ][ 'dates' ] ) )
		){
			return  p_bookings[ 'calendar_' + resource_id ][ 'dates' ];
		}

		return false;		// If some property not defined, then false;
	};

	/**
	 * Set bookings dates in calendar object   ::    { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }
	 *
	 * if calendar object  not defined, then  it's will be defined and 'id', 'dates' set
	 * if calendar exist, then system add a  new or overwrite only dates from dates_obj parameter,
	 * but other dates not from parameter dates_obj will be existed and not overwrite.
	 *
	 * @param {string|int} resource_id				  '2'
	 * @param {object} dates_obj					  { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }
	 * @param {boolean} is_complete_overwrite		  if false,  then  only overwrite or add  dates from 	dates_obj
	 * @returns {*}
	 *
	 * Examples:
	 *   			_wpbc.bookings_in_calendar__set_dates( resource_id, { "2023-07-21": {…}, "2023-07-22": {…}, … }  );		<-   overwrite ALL dates
	 *   			_wpbc.bookings_in_calendar__set_dates( resource_id, { "2023-07-22": {…} },  false  );					<-   add or overwrite only  	"2023-07-22": {}
	 *
	 * Common usage in PHP:
	 *   			echo "  _wpbc.bookings_in_calendar__set_dates(  " . intval( $resource_id ) . ",  " . wp_json_encode( $availability_per_days_arr ) . "  );  ";
	 */
	obj.bookings_in_calendar__set_dates = function( resource_id, dates_obj , is_complete_overwrite = true ){

		if ( !obj.bookings_in_calendar__is_defined( resource_id ) ){
			obj.bookings_in_calendar__set( resource_id, { 'dates': {} } );
		}

		if ( 'undefined' === typeof (p_bookings[ 'calendar_' + resource_id ][ 'dates' ]) ){
			p_bookings[ 'calendar_' + resource_id ][ 'dates' ] = {}
		}

		if (is_complete_overwrite){

			// Complete overwrite all  booking dates
			p_bookings[ 'calendar_' + resource_id ][ 'dates' ] = dates_obj;
		} else {

			// Add only  new or overwrite exist booking dates from  parameter. Booking dates not from  parameter  will  be without chnanges
			for ( var prop_name in dates_obj ){

				p_bookings[ 'calendar_' + resource_id ]['dates'][ prop_name ] = dates_obj[ prop_name ];
			}
		}

		return p_bookings[ 'calendar_' + resource_id ];
	};


	/**
	 *  Get bookings data for specific date in calendar   ::   false | { day_availability: 1, ... }
	 *
	 * @param {string|int} resource_id			'1'
	 * @param {string} sql_class_day			'2023-07-21'
	 * @returns {object|boolean}				false | {
															day_availability: 4
															max_capacity: 4															//  >= Business Large
															2: Object { is_day_unavailable: false, _day_status: "available" }
															10: Object { is_day_unavailable: false, _day_status: "available" }		//  >= Business Large ...
															11: Object { is_day_unavailable: false, _day_status: "available" }
															12: Object { is_day_unavailable: false, _day_status: "available" }
														}
	 */
	obj.bookings_in_calendar__get_for_date = function( resource_id, sql_class_day ){

		if (
			   ( obj.bookings_in_calendar__is_defined( resource_id ) )
			&& ( 'undefined' !== typeof ( p_bookings[ 'calendar_' + resource_id ][ 'dates' ] ) )
			&& ( 'undefined' !== typeof ( p_bookings[ 'calendar_' + resource_id ][ 'dates' ][ sql_class_day ] ) )
		){
			return  p_bookings[ 'calendar_' + resource_id ][ 'dates' ][ sql_class_day ];
		}

		return false;		// If some property not defined, then false;
	};


	// Any  PARAMS   in bookings

	/**
	 * Set property  to  booking
	 * @param resource_id	"1"
	 * @param prop_name		name of property
	 * @param prop_value	value of property
	 * @returns {*}			booking object
	 */
	obj.booking__set_param_value = function ( resource_id, prop_name, prop_value ) {

		if ( ! obj.bookings_in_calendar__is_defined( resource_id ) ){
			p_bookings[ 'calendar_' + resource_id ] = {};
			p_bookings[ 'calendar_' + resource_id ][ 'id' ] = resource_id;
		}

		p_bookings[ 'calendar_' + resource_id ][ prop_name ] = prop_value;

		return p_bookings[ 'calendar_' + resource_id ];
	};

	/**
	 *  Get booking property value   	::   mixed | null
	 *
	 * @param {string|int}  resource_id		'1'
	 * @param {string} prop_name			'selection_mode'
	 * @returns {*|null}					mixed | null
	 */
	obj.booking__get_param_value = function( resource_id, prop_name ){

		if (
			   ( obj.bookings_in_calendar__is_defined( resource_id ) )
			&& ( 'undefined' !== typeof ( p_bookings[ 'calendar_' + resource_id ][ prop_name ] ) )
		){
			return  p_bookings[ 'calendar_' + resource_id ][ prop_name ];
		}

		return null;		// If some property not defined, then null;
	};




	/**
	 * Set bookings for all  calendars
	 *
	 * @param {object} calendars_obj		Object { calendar_1: { id: 1, dates: Object { "2023-07-22": {…}, "2023-07-23": {…}, "2023-07-24": {…}, … } }
	 * 												 calendar_3: {}, ... }
	 */
	obj.bookings_in_calendars__set_all = function ( calendars_obj ) {
		p_bookings = calendars_obj;
	};

	/**
	 * Get bookings in all calendars
	 *
	 * @returns {object|{}}
	 */
	obj.bookings_in_calendars__get_all = function () {
		return p_bookings;
	};
	// -----------------------------------------------------------------------------------------------------------------




	// Seasons 	----------------------------------------------------------------------------------------------------
	var p_seasons = obj.seasons_obj = obj.seasons_obj || {
																// calendar_1: Object {
 																//						   id:     1
 																//						 , dates:  Object { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, …
																// }
															};

	/**
	 * Add season names for dates in calendar object   ::    { "2023-07-21": [ 'wpbc_season_september_2023', 'wpbc_season_september_2024' ], "2023-07-22": [...], ... }
	 *
	 *
	 * @param {string|int} resource_id				  '2'
	 * @param {object} dates_obj					  { "2023-07-21": {…}, "2023-07-22": {…}, "2023-07-23": {…}, … }
	 * @param {boolean} is_complete_overwrite		  if false,  then  only  add  dates from 	dates_obj
	 * @returns {*}
	 *
	 * Examples:
	 *   			_wpbc.seasons__set( resource_id, { "2023-07-21": [ 'wpbc_season_september_2023', 'wpbc_season_september_2024' ], "2023-07-22": [...], ... }  );
	 */
	obj.seasons__set = function( resource_id, dates_obj , is_complete_overwrite = false ){

		if ( 'undefined' === typeof (p_seasons[ 'calendar_' + resource_id ]) ){
			p_seasons[ 'calendar_' + resource_id ] = {};
		}

		if ( is_complete_overwrite ){

			// Complete overwrite all  season dates
			p_seasons[ 'calendar_' + resource_id ] = dates_obj;

		} else {

			// Add only  new or overwrite exist booking dates from  parameter. Booking dates not from  parameter  will  be without chnanges
			for ( var prop_name in dates_obj ){

				if ( 'undefined' === typeof (p_seasons[ 'calendar_' + resource_id ][ prop_name ]) ){
					p_seasons[ 'calendar_' + resource_id ][ prop_name ] = [];
				}
				for ( var season_name_key in dates_obj[ prop_name ] ){
					p_seasons[ 'calendar_' + resource_id ][ prop_name ].push( dates_obj[ prop_name ][ season_name_key ] );
				}
			}
		}

		return p_seasons[ 'calendar_' + resource_id ];
	};


	/**
	 *  Get bookings data for specific date in calendar   ::   [] | [ 'wpbc_season_september_2023', 'wpbc_season_september_2024' ]
	 *
	 * @param {string|int} resource_id			'1'
	 * @param {string} sql_class_day			'2023-07-21'
	 * @returns {object|boolean}				[]  |  [ 'wpbc_season_september_2023', 'wpbc_season_september_2024' ]
	 */
	obj.seasons__get_for_date = function( resource_id, sql_class_day ){

		if (
			   ( 'undefined' !== typeof ( p_seasons[ 'calendar_' + resource_id ] ) )
			&& ( 'undefined' !== typeof ( p_seasons[ 'calendar_' + resource_id ][ sql_class_day ] ) )
		){
			return  p_seasons[ 'calendar_' + resource_id ][ sql_class_day ];
		}

		return [];		// If not defined, then [];
	};


	// Other parameters 			------------------------------------------------------------------------------------
	var p_other = obj.other_obj = obj.other_obj || { };

	obj.set_other_param = function ( param_key, param_val ) {
		p_other[ param_key ] = param_val;
	};

	obj.get_other_param = function ( param_key ) {
		return p_other[ param_key ];
	};

	/**
	 * Get all other params
	 *
	 * @returns {object|{}}
	 */
	obj.get_other_param__all = function () {
		return p_other;
	};

	// Messages 			        ------------------------------------------------------------------------------------
	var p_messages = obj.messages_obj = obj.messages_obj || { };

	obj.set_message = function ( param_key, param_val ) {
		p_messages[ param_key ] = param_val;
	};

	obj.get_message = function ( param_key ) {
		return p_messages[ param_key ];
	};

	/**
	 * Get all other params
	 *
	 * @returns {object|{}}
	 */
	obj.get_messages__all = function () {
		return p_messages;
	};

	// -----------------------------------------------------------------------------------------------------------------

	return obj;

}( _wpbc || {}, jQuery ));

window.__WPBC_DEV = true;

/**
 * Extend _wpbc with  new methods
 *
 * @type {*|{}}
 * @private
 */
_wpbc = (function (obj, $) {

	/**
	 * Dev logger (no-op unless window.__WPBC_DEV = true)
	 *
	 * @type {*|{warn: (function(*, *, *): void), error: (function(*, *, *): void), once: obj.dev.once, try: ((function(*, *, *): (*|undefined))|*)}}
	 */
	obj.dev = obj.dev || (() => {
		const seen    = new Set();
		const enabled = () => !!window.__WPBC_DEV;

		function out(level, code, msg, extra) {
			if ( !enabled() ) return;
			try {
				(console[level] || console.warn)( `[WPBC][${code}] ${msg}`, extra ?? '' );
			} catch {
			}
		}

		return {
			log  : (code, msg, extra) => out('log',   code, msg, extra),
			debug: (code, msg, extra) => out('debug', code, msg, extra),
			warn : (code, msg, extra) => out( 'warn', code, msg, extra ),
			error: (code, errOrMsg, extra) =>
				out( 'error', code,
					errOrMsg instanceof Error ? errOrMsg.message : String( errOrMsg ),
					errOrMsg instanceof Error ? errOrMsg : extra ),
			once : (code, msg, extra) => {
				if ( !enabled() ) return;
				const key = `${code}|${msg}`;
				if ( seen.has( key ) ) return;
				seen.add( key );
				out( 'error', code, msg, extra );
			},
			try  : (code, fn, extra) => {
				try {
					return fn();
				} catch ( e ) {
					out( 'error', code, e, extra );
				}
			}
		};
	})();

	// Optional: global traps in dev.
	if ( window.__WPBC_DEV ) {
		window.addEventListener( 'error', (e) => {
			try { _wpbc?.dev?.error( 'GLOBAL-ERROR', e?.error || e?.message, e ); } catch ( _ ) {}
		} );
		window.addEventListener( 'unhandledrejection', (e) => {
			try { _wpbc?.dev?.error( 'GLOBAL-REJECTION', e?.reason ); } catch ( _ ) {}
		} );
	}

	return obj;
	}( _wpbc || {}, jQuery ));

/**
 * Extend _wpbc with  new methods        // FixIn: 9.8.6.2.
 *
 * @type {*|{}}
 * @private
 */
 _wpbc = (function ( obj, $) {

	// Load Balancer 	-----------------------------------------------------------------------------------------------

	var p_balancer = obj.balancer_obj = obj.balancer_obj || {
																'max_threads': 2,
																'in_process' : [],
																'wait'       : []
															};

	 /**
	  * Set  max parallel request  to  load
	  *
	  * @param max_threads
	  */
	obj.balancer__set_max_threads = function ( max_threads ){

		p_balancer[ 'max_threads' ] = max_threads;
	};

	/**
	 *  Check if balancer for specific booking resource defined   ::   true | false
	 *
	 * @param {string|int} resource_id
	 * @returns {boolean}
	 */
	obj.balancer__is_defined = function ( resource_id ) {

		return ('undefined' !== typeof( p_balancer[ 'balancer_' + resource_id ] ) );
	};


	/**
	 *  Create balancer initializing
	 *
	 * @param {string|int} resource_id
	 */
	obj.balancer__init = function ( resource_id, function_name , params ={}) {

		var balance_obj = {};
		balance_obj[ 'resource_id' ]   = resource_id;
		balance_obj[ 'priority' ]      = 1;
		balance_obj[ 'function_name' ] = function_name;
		balance_obj[ 'params' ]        = wpbc_clone_obj( params );


		if ( obj.balancer__is_already_run( resource_id, function_name ) ){
			return 'run';
		}
		if ( obj.balancer__is_already_wait( resource_id, function_name ) ){
			return 'wait';
		}


		if ( obj.balancer__can_i_run() ){
			obj.balancer__add_to__run( balance_obj );
			return 'run';
		} else {
			obj.balancer__add_to__wait( balance_obj );
			return 'wait';
		}
	};

	 /**
	  * Can I Run ?
	  * @returns {boolean}
	  */
	obj.balancer__can_i_run = function (){
		return ( p_balancer[ 'in_process' ].length < p_balancer[ 'max_threads' ] );
	}

		 /**
		  * Add to WAIT
		  * @param balance_obj
		  */
		obj.balancer__add_to__wait = function ( balance_obj ) {
			p_balancer['wait'].push( balance_obj );
		}

		 /**
		  * Remove from Wait
		  *
		  * @param resource_id
		  * @param function_name
		  * @returns {*|boolean}
		  */
		obj.balancer__remove_from__wait_list = function ( resource_id, function_name ){

			var removed_el = false;

			if ( p_balancer[ 'wait' ].length ){					// FixIn: 9.8.10.1.
				for ( var i in p_balancer[ 'wait' ] ){
					if (
						(resource_id === p_balancer[ 'wait' ][ i ][ 'resource_id' ])
						&& (function_name === p_balancer[ 'wait' ][ i ][ 'function_name' ])
					){
						removed_el = p_balancer[ 'wait' ].splice( i, 1 );
						removed_el = removed_el.pop();
						p_balancer[ 'wait' ] = p_balancer[ 'wait' ].filter( function ( v ){
							return v;
						} );					// Reindex array
						return removed_el;
					}
				}
			}
			return removed_el;
		}

		/**
		* Is already WAIT
		*
		* @param resource_id
		* @param function_name
		* @returns {boolean}
		*/
		obj.balancer__is_already_wait = function ( resource_id, function_name ){

			if ( p_balancer[ 'wait' ].length ){				// FixIn: 9.8.10.1.
				for ( var i in p_balancer[ 'wait' ] ){
					if (
						(resource_id === p_balancer[ 'wait' ][ i ][ 'resource_id' ])
						&& (function_name === p_balancer[ 'wait' ][ i ][ 'function_name' ])
					){
						return true;
					}
				}
			}
			return false;
		}


		 /**
		  * Add to RUN
		  * @param balance_obj
		  */
		obj.balancer__add_to__run = function ( balance_obj ) {
			p_balancer['in_process'].push( balance_obj );
		}

		/**
		* Remove from RUN list
		*
		* @param resource_id
		* @param function_name
		* @returns {*|boolean}
		*/
		obj.balancer__remove_from__run_list = function ( resource_id, function_name ){

			 var removed_el = false;

			 if ( p_balancer[ 'in_process' ].length ){				// FixIn: 9.8.10.1.
				 for ( var i in p_balancer[ 'in_process' ] ){
					 if (
						 (resource_id === p_balancer[ 'in_process' ][ i ][ 'resource_id' ])
						 && (function_name === p_balancer[ 'in_process' ][ i ][ 'function_name' ])
					 ){
						 removed_el = p_balancer[ 'in_process' ].splice( i, 1 );
						 removed_el = removed_el.pop();
						 p_balancer[ 'in_process' ] = p_balancer[ 'in_process' ].filter( function ( v ){
							 return v;
						 } );		// Reindex array
						 return removed_el;
					 }
				 }
			 }
			 return removed_el;
		}

		/**
		* Is already RUN
		*
		* @param resource_id
		* @param function_name
		* @returns {boolean}
		*/
		obj.balancer__is_already_run = function ( resource_id, function_name ){

			if ( p_balancer[ 'in_process' ].length ){					// FixIn: 9.8.10.1.
				for ( var i in p_balancer[ 'in_process' ] ){
					if (
						(resource_id === p_balancer[ 'in_process' ][ i ][ 'resource_id' ])
						&& (function_name === p_balancer[ 'in_process' ][ i ][ 'function_name' ])
					){
						return true;
					}
				}
			}
			return false;
		}



	obj.balancer__run_next = function (){

		// Get 1st from  Wait list
		var removed_el = false;
		if ( p_balancer[ 'wait' ].length ){					// FixIn: 9.8.10.1.
			for ( var i in p_balancer[ 'wait' ] ){
				removed_el = obj.balancer__remove_from__wait_list( p_balancer[ 'wait' ][ i ][ 'resource_id' ], p_balancer[ 'wait' ][ i ][ 'function_name' ] );
				break;
			}
		}

		if ( false !== removed_el ){

			// Run
			obj.balancer__run( removed_el );
		}
	}

	 /**
	  * Run
	  * @param balance_obj
	  */
	obj.balancer__run = function ( balance_obj ){

		switch ( balance_obj[ 'function_name' ] ){

			case 'wpbc_calendar__load_data__ajx':

				// Add to run list
				obj.balancer__add_to__run( balance_obj );

				wpbc_calendar__load_data__ajx( balance_obj[ 'params' ] )
				break;

			default:
		}
	}

	return obj;

}( _wpbc || {}, jQuery ));


 	/**
 	 * -- Help functions ----------------------------------------------------------------------------------------------
	 */

	function wpbc_balancer__is_wait( params, function_name ){
//console.log('::wpbc_balancer__is_wait',params , function_name );
		if ( 'undefined' !== typeof (params[ 'resource_id' ]) ){

			var balancer_status = _wpbc.balancer__init( params[ 'resource_id' ], function_name, params );

			return ( 'wait' === balancer_status );
		}

		return false;
	}


	function wpbc_balancer__completed( resource_id , function_name ){
//console.log('::wpbc_balancer__completed',resource_id , function_name );
		_wpbc.balancer__remove_from__run_list( resource_id, function_name );
		_wpbc.balancer__run_next();
	}
/**
 * =====================================================================================================================
 *	includes/__js/cal/wpbc_cal.js
 * =====================================================================================================================
 */

/**
 * Order or child booking resources saved here:  	_wpbc.booking__get_param_value( resource_id, 'resources_id_arr__in_dates' )		[2,10,12,11]
 */

/**
 * How to check  booked times on  specific date: ?
 *
			_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21');

			console.log(
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[2].booked_time_slots.merged_seconds,
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[10].booked_time_slots.merged_seconds,
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[11].booked_time_slots.merged_seconds,
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[12].booked_time_slots.merged_seconds
					);
 *  OR
			console.log(
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[2].booked_time_slots.merged_readable,
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[10].booked_time_slots.merged_readable,
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[11].booked_time_slots.merged_readable,
						_wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[12].booked_time_slots.merged_readable
					);
 *
 */

/**
 * Days selection:
 * 					wpbc_calendar__unselect_all_dates( resource_id );
 *
 *					var resource_id = 1;
 * 	Example 1:		var num_selected_days = wpbc_auto_select_dates_in_calendar( resource_id, '2024-05-15', '2024-05-25' );
 * 	Example 2:		var num_selected_days = wpbc_auto_select_dates_in_calendar( resource_id, ['2024-05-09','2024-05-19','2024-05-25'] );
 *
 */


/**
 * C A L E N D A R  ---------------------------------------------------------------------------------------------------
 */


/**
 *  Show WPBC Calendar
 *
 * @param resource_id			- resource ID
 * @returns {boolean}
 */
function wpbc_calendar_show( resource_id ){

	// If no calendar HTML tag,  then  exit
	if ( 0 === jQuery( '#calendar_booking' + resource_id ).length ){ return false; }

	// If the calendar with the same Booking resource is activated already, then exit. But in Elementor the class can be stale, so verify instance.
	if ( jQuery( '#calendar_booking' + resource_id ).hasClass( 'hasDatepick' ) ) {

		var existing_inst = null;

		try {
			existing_inst = jQuery.datepick._getInst( jQuery( '#calendar_booking' + resource_id ).get( 0 ) );
		} catch ( e ) {
			existing_inst = null;
		}

		if ( existing_inst ) {
			return false;
		}

		// Stale marker: remove and continue with init.
		jQuery( '#calendar_booking' + resource_id ).removeClass( 'hasDatepick' );
	}



	// -----------------------------------------------------------------------------------------------------------------
	// Days selection
	// -----------------------------------------------------------------------------------------------------------------
	var local__is_range_select = false;
	var local__multi_days_select_num   = 365;					// multiple | fixed
	if ( 'dynamic' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ) ){
		local__is_range_select = true;
		local__multi_days_select_num = 0;
	}
	if ( 'single'  === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ) ){
		local__multi_days_select_num = 0;
	}

	// -----------------------------------------------------------------------------------------------------------------
	// Min - Max days to scroll/show
	// -----------------------------------------------------------------------------------------------------------------
	var local__min_date = 0;
 	local__min_date = new Date( _wpbc.get_other_param( 'today_arr' )[ 0 ], (parseInt( _wpbc.get_other_param( 'today_arr' )[ 1 ] ) - 1), _wpbc.get_other_param( 'today_arr' )[ 2 ], 0, 0, 0 );			// FixIn: 9.9.0.17.
//console.log( local__min_date );
	var local__max_date = _wpbc.calendar__get_param_value( resource_id, 'booking_max_monthes_in_calendar' );
	//local__max_date = new Date(2024, 5, 28);  It is here issue of not selectable dates, but some dates showing in calendar as available, but we can not select it.

	//// Define last day in calendar (as a last day of month (and not date, which is related to actual 'Today' date).
	//// E.g. if today is 2023-09-25, and we set 'Number of months to scroll' as 5 months, then last day will be 2024-02-29 and not the 2024-02-25.
	// var cal_last_day_in_month = jQuery.datepick._determineDate( null, local__max_date, new Date() );
	// cal_last_day_in_month = new Date( cal_last_day_in_month.getFullYear(), cal_last_day_in_month.getMonth() + 1, 0 );
	// local__max_date = cal_last_day_in_month;			// FixIn: 10.0.0.26.

	// Get start / end dates from  the Booking Calendar shortcode. Example: [booking calendar_dates_start='2026-01-01' calendar_dates_end='2026-12-31'  resource_id=1] // FixIn: 10.13.1.4.
	if ( false !== wpbc_calendar__get_dates_start( resource_id ) ) {
		local__min_date = wpbc_calendar__get_dates_start( resource_id );  // E.g. - local__min_date = new Date( 2025, 0, 1 );
	}
	if ( false !== wpbc_calendar__get_dates_end( resource_id ) ) {
		local__max_date = wpbc_calendar__get_dates_end( resource_id );    // E.g. - local__max_date = new Date( 2025, 11, 31 );
	}

	// In case we edit booking in past or have specific parameter in URL.
	if (   ( location.href.indexOf('page=wpbc-new') != -1 )
		&& (
			  ( location.href.indexOf('booking_hash') != -1 )                  // Comment this line for ability to add  booking in past days at  Booking > Add booking page.
		   || ( location.href.indexOf('allow_past') != -1 )                // FixIn: 10.7.1.2.
		)
	){
		// local__min_date = null;
		// FixIn: 10.14.1.4.
		local__min_date  = new Date( _wpbc.get_other_param( 'time_local_arr' )[0], ( parseInt( _wpbc.get_other_param( 'time_local_arr' )[1] ) - 1), _wpbc.get_other_param( 'time_local_arr' )[2], _wpbc.get_other_param( 'time_local_arr' )[3], _wpbc.get_other_param( 'time_local_arr' )[4], 0 );
		local__max_date = null;
	}

	var local__start_weekday    = _wpbc.calendar__get_param_value( resource_id, 'booking_start_day_weeek' );
	var local__number_of_months = parseInt( _wpbc.calendar__get_param_value( resource_id, 'calendar_number_of_months' ) );

	jQuery( '#calendar_booking' + resource_id ).text( '' );					// Remove all HTML in calendar tag
	// -----------------------------------------------------------------------------------------------------------------
	// Show calendar
	// -----------------------------------------------------------------------------------------------------------------
	jQuery('#calendar_booking'+ resource_id).datepick(
			{
				beforeShowDay: function ( js_date ){
									return wpbc__calendar__apply_css_to_days( js_date, {'resource_id': resource_id}, this );
							  },
				onSelect: function ( string_dates, js_dates_arr ){  /**
																	 *	string_dates   =   '23.08.2023 - 26.08.2023'    |    '23.08.2023 - 23.08.2023'    |    '19.09.2023, 24.08.2023, 30.09.2023'
																	 *  js_dates_arr   =   range: [ Date (Aug 23 2023), Date (Aug 25 2023)]     |     multiple: [ Date(Oct 24 2023), Date(Oct 20 2023), Date(Oct 16 2023) ]
																	 */
									return wpbc__calendar__on_select_days( string_dates, {'resource_id': resource_id}, this );
							  },
				onHover: function ( string_date, js_date ){
									return wpbc__calendar__on_hover_days( string_date, js_date, {'resource_id': resource_id}, this );
							  },
				onChangeMonthYear: function ( year, real_month, js_date__1st_day_in_month ){ },
				showOn        : 'both',
				numberOfMonths: local__number_of_months,
				stepMonths    : 1,
				// prevText      : '&laquo;',
				// nextText      : '&raquo;',
				prevText      : '&lsaquo;',
				nextText      : '&rsaquo;',
				dateFormat    : 'dd.mm.yy',
				changeMonth   : false,
				changeYear    : false,
				minDate       : local__min_date,
				maxDate       : local__max_date, 														// '1Y',
				// minDate: new Date(2020, 2, 1), maxDate: new Date(2020, 9, 31),             	// Ability to set any  start and end date in calendar
				showStatus      : false,
				multiSeparator  : ', ',
				closeAtTop      : false,
				firstDay        : local__start_weekday,
				gotoCurrent     : false,
				hideIfNoPrevNext: true,
				multiSelect     : local__multi_days_select_num,
				rangeSelect     : local__is_range_select,
				// showWeeks: true,
				useThemeRoller: false
			}
	);


	
	// -----------------------------------------------------------------------------------------------------------------
	// Clear today date highlighting
	// -----------------------------------------------------------------------------------------------------------------
	setTimeout( function (){  wpbc_calendars__clear_days_highlighting( resource_id );  }, 500 );                    	// FixIn: 7.1.2.8.
	
	// -----------------------------------------------------------------------------------------------------------------
	// Scroll calendar to  specific month
	// -----------------------------------------------------------------------------------------------------------------
	var start_bk_month = _wpbc.calendar__get_param_value( resource_id, 'calendar_scroll_to' );
	if ( false !== start_bk_month ){
		wpbc_calendar__scroll_to( resource_id, start_bk_month[ 0 ], start_bk_month[ 1 ] );
	}
}


	/**
	 * Apply CSS to calendar date cells
	 *
	 * @param date										-  JavaScript Date Obj:  		Mon Dec 11 2023 00:00:00 GMT+0200 (Eastern European Standard Time)
	 * @param calendar_params_arr						-  Calendar Settings Object:  	{
	 *																  						"resource_id": 4
	 *																					}
	 * @param datepick_this								- this of datepick Obj
	 * @returns {(*|string)[]|(boolean|string)[]}		- [ {true -available | false - unavailable}, 'CSS classes for calendar day cell' ]
	 */
	function wpbc__calendar__apply_css_to_days( date, calendar_params_arr, datepick_this ){

		var today_date = new Date( _wpbc.get_other_param( 'today_arr' )[ 0 ], (parseInt( _wpbc.get_other_param( 'today_arr' )[ 1 ] ) - 1), _wpbc.get_other_param( 'today_arr' )[ 2 ], 0, 0, 0 );								// Today JS_Date_Obj.
		var class_day     = wpbc__get__td_class_date( date );																					// '1-9-2023'
		var sql_class_day = wpbc__get__sql_class_date( date );																					// '2023-01-09'
		var resource_id = ( 'undefined' !== typeof(calendar_params_arr[ 'resource_id' ]) ) ? calendar_params_arr[ 'resource_id' ] : '1'; 		// '1'

		// Get Selected dates in calendar
		var selected_dates_sql = wpbc_get__selected_dates_sql__as_arr( resource_id );

		// Get Data --------------------------------------------------------------------------------------------------------
		var date_bookings_obj = _wpbc.bookings_in_calendar__get_for_date( resource_id, sql_class_day );


		// Array with CSS classes for date ---------------------------------------------------------------------------------
		var css_classes__for_date = [];
		css_classes__for_date.push( 'sql_date_'     + sql_class_day );				//  'sql_date_2023-07-21'
		css_classes__for_date.push( 'cal4date-'     + class_day );					//  'cal4date-7-21-2023'
		css_classes__for_date.push( 'wpbc_weekday_' + date.getDay() );				//  'wpbc_weekday_4'

		// Define Selected Check In/Out dates in TD  -----------------------------------------------------------------------
		if (
				( selected_dates_sql.length  )
			//&&  ( selected_dates_sql[ 0 ] !== selected_dates_sql[ (selected_dates_sql.length - 1) ] )
		){
			if ( sql_class_day === selected_dates_sql[ 0 ] ){
				css_classes__for_date.push( 'selected_check_in' );
				css_classes__for_date.push( 'selected_check_in_out' );
			}
			if (  ( selected_dates_sql.length > 1 ) && ( sql_class_day === selected_dates_sql[ (selected_dates_sql.length - 1) ] ) ) {
				css_classes__for_date.push( 'selected_check_out' );
				css_classes__for_date.push( 'selected_check_in_out' );
			}
		}


		var is_day_selectable = false;

		// If something not defined,  then  this date closed --------------------------------------------------------------- // FixIn: 10.12.4.6.
		if ( (false === date_bookings_obj) || ('undefined' === typeof (date_bookings_obj[resource_id])) ) {

			css_classes__for_date.push( 'date_user_unavailable' );

			return [ is_day_selectable, css_classes__for_date.join(' ')  ];
		}


		// -----------------------------------------------------------------------------------------------------------------
		//   date_bookings_obj  - Defined.            Dates can be selectable.
		// -----------------------------------------------------------------------------------------------------------------

		// -----------------------------------------------------------------------------------------------------------------
		// Add season names to the day CSS classes -- it is required for correct  work  of conditional fields --------------
		var season_names_arr = _wpbc.seasons__get_for_date( resource_id, sql_class_day );

		for ( var season_key in season_names_arr ){

			css_classes__for_date.push( season_names_arr[ season_key ] );				//  'wpdevbk_season_september_2023'
		}
		// -----------------------------------------------------------------------------------------------------------------


		// Cost Rate -------------------------------------------------------------------------------------------------------
		css_classes__for_date.push( 'rate_' + date_bookings_obj[ resource_id ][ 'date_cost_rate' ].toString().replace( /[\.\s]/g, '_' ) );						//  'rate_99_00' -> 99.00


		if ( parseInt( date_bookings_obj[ 'day_availability' ] ) > 0 ){
			is_day_selectable = true;
			css_classes__for_date.push( 'date_available' );
			css_classes__for_date.push( 'reserved_days_count' + parseInt( date_bookings_obj[ 'max_capacity' ] - date_bookings_obj[ 'day_availability' ] ) );
		} else {
			is_day_selectable = false;
			css_classes__for_date.push( 'date_user_unavailable' );
		}


		switch ( date_bookings_obj[ 'summary']['status_for_day' ] ){

			case 'available':
				break;

			case 'time_slots_booking':
				css_classes__for_date.push( 'timespartly', 'times_clock' );
				break;

			case 'full_day_booking':
				css_classes__for_date.push( 'full_day_booking' );
				break;

			case 'season_filter':
				css_classes__for_date.push( 'date_user_unavailable', 'season_unavailable' );
				date_bookings_obj[ 'summary']['status_for_bookings' ] = '';														// Reset booking status color for possible old bookings on this date
				break;

			case 'resource_availability':
				css_classes__for_date.push( 'date_user_unavailable', 'resource_unavailable' );
				date_bookings_obj[ 'summary']['status_for_bookings' ] = '';														// Reset booking status color for possible old bookings on this date
				break;

			case 'weekday_unavailable':
				css_classes__for_date.push( 'date_user_unavailable', 'weekday_unavailable' );
				date_bookings_obj[ 'summary']['status_for_bookings' ] = '';														// Reset booking status color for possible old bookings on this date
				break;

			case 'from_today_unavailable':
				css_classes__for_date.push( 'date_user_unavailable', 'from_today_unavailable' );
				date_bookings_obj[ 'summary']['status_for_bookings' ] = '';														// Reset booking status color for possible old bookings on this date
				break;

			case 'limit_available_from_today':
				css_classes__for_date.push( 'date_user_unavailable', 'limit_available_from_today' );
				date_bookings_obj[ 'summary']['status_for_bookings' ] = '';														// Reset booking status color for possible old bookings on this date
				break;

			case 'change_over':
				/*
				 *
				//  check_out_time_date2approve 	 	check_in_time_date2approve
				//  check_out_time_date2approve 	 	check_in_time_date_approved
				//  check_in_time_date2approve 		 	check_out_time_date_approved
				//  check_out_time_date_approved 	 	check_in_time_date_approved
				 */

				css_classes__for_date.push( 'timespartly', 'check_in_time', 'check_out_time' );
				// FixIn: 10.0.0.2.
				if ( date_bookings_obj[ 'summary' ][ 'status_for_bookings' ].indexOf( 'approved_pending' ) > -1 ){
					css_classes__for_date.push( 'check_out_time_date_approved', 'check_in_time_date2approve' );
				}
				if ( date_bookings_obj[ 'summary' ][ 'status_for_bookings' ].indexOf( 'pending_approved' ) > -1 ){
					css_classes__for_date.push( 'check_out_time_date2approve', 'check_in_time_date_approved' );
				}
				break;

			case 'check_in':
				css_classes__for_date.push( 'timespartly', 'check_in_time' );

				// FixIn: 9.9.0.33.
				if ( date_bookings_obj[ 'summary' ][ 'status_for_bookings' ].indexOf( 'pending' ) > -1 ){
					css_classes__for_date.push( 'check_in_time_date2approve' );
				} else if ( date_bookings_obj[ 'summary' ][ 'status_for_bookings' ].indexOf( 'approved' ) > -1 ){
					css_classes__for_date.push( 'check_in_time_date_approved' );
				}
				break;

			case 'check_out':
				css_classes__for_date.push( 'timespartly', 'check_out_time' );

				// FixIn: 9.9.0.33.
				if ( date_bookings_obj[ 'summary' ][ 'status_for_bookings' ].indexOf( 'pending' ) > -1 ){
					css_classes__for_date.push( 'check_out_time_date2approve' );
				} else if ( date_bookings_obj[ 'summary' ][ 'status_for_bookings' ].indexOf( 'approved' ) > -1 ){
					css_classes__for_date.push( 'check_out_time_date_approved' );
				}
				break;

			default:
				// mixed statuses: 'change_over check_out' .... variations.... check more in 		function wpbc_get_availability_per_days_arr()
				date_bookings_obj[ 'summary']['status_for_day' ] = 'available';
		}



		if ( 'available' != date_bookings_obj[ 'summary']['status_for_day' ] ){

			var is_set_pending_days_selectable = _wpbc.calendar__get_param_value( resource_id, 'pending_days_selectable' );	// set pending days selectable          // FixIn: 8.6.1.18.

			switch ( date_bookings_obj[ 'summary']['status_for_bookings' ] ){

				case '':
					// Usually  it's means that day  is available or unavailable without the bookings
					break;

				case 'pending':
					css_classes__for_date.push( 'date2approve' );
					is_day_selectable = (is_day_selectable) ? true : is_set_pending_days_selectable;
					break;

				case 'approved':
					css_classes__for_date.push( 'date_approved' );
					break;

				// Situations for "change-over" days: ----------------------------------------------------------------------
				case 'pending_pending':
					css_classes__for_date.push( 'check_out_time_date2approve', 'check_in_time_date2approve' );
					is_day_selectable = (is_day_selectable) ? true : is_set_pending_days_selectable;
					break;

				case 'pending_approved':
					css_classes__for_date.push( 'check_out_time_date2approve', 'check_in_time_date_approved' );
					is_day_selectable = (is_day_selectable) ? true : is_set_pending_days_selectable;
					break;

				case 'approved_pending':
					css_classes__for_date.push( 'check_out_time_date_approved', 'check_in_time_date2approve' );
					is_day_selectable = (is_day_selectable) ? true : is_set_pending_days_selectable;
					break;

				case 'approved_approved':
					css_classes__for_date.push( 'check_out_time_date_approved', 'check_in_time_date_approved' );
					break;

				default:

			}
		}

		return [ is_day_selectable, css_classes__for_date.join( ' ' ) ];
	}


	/**
	 * Mouseover calendar date cells
	 *
	 * @param string_date
	 * @param date										-  JavaScript Date Obj:  		Mon Dec 11 2023 00:00:00 GMT+0200 (Eastern European Standard Time)
	 * @param calendar_params_arr						-  Calendar Settings Object:  	{
	 *																  						"resource_id": 4
	 *																					}
	 * @param datepick_this								- this of datepick Obj
	 * @returns {boolean}
	 */
	function wpbc__calendar__on_hover_days( string_date, date, calendar_params_arr, datepick_this ) {

		if ( null === date ) {
			wpbc_calendars__clear_days_highlighting( ('undefined' !== typeof (calendar_params_arr[ 'resource_id' ])) ? calendar_params_arr[ 'resource_id' ] : '1' );		// FixIn: 10.5.2.4.
			return false;
		}

		var class_day     = wpbc__get__td_class_date( date );																					// '1-9-2023'
		var sql_class_day = wpbc__get__sql_class_date( date );																					// '2023-01-09'
		var resource_id = ( 'undefined' !== typeof(calendar_params_arr[ 'resource_id' ]) ) ? calendar_params_arr[ 'resource_id' ] : '1';		// '1'

		// Get Data --------------------------------------------------------------------------------------------------------
		var date_booking_obj = _wpbc.bookings_in_calendar__get_for_date( resource_id, sql_class_day );											// {...}

		if ( ! date_booking_obj ){ return false; }


		// T o o l t i p s -------------------------------------------------------------------------------------------------
		var tooltip_text = '';
		if ( date_booking_obj[ 'summary']['tooltip_availability' ].length > 0 ){
			tooltip_text +=  date_booking_obj[ 'summary']['tooltip_availability' ];
		}
		if ( date_booking_obj[ 'summary']['tooltip_day_cost' ].length > 0 ){
			tooltip_text +=  date_booking_obj[ 'summary']['tooltip_day_cost' ];
		}
		if ( date_booking_obj[ 'summary']['tooltip_times' ].length > 0 ){
			tooltip_text +=  date_booking_obj[ 'summary']['tooltip_times' ];
		}
		if ( date_booking_obj[ 'summary']['tooltip_booking_details' ].length > 0 ){
			tooltip_text +=  date_booking_obj[ 'summary']['tooltip_booking_details' ];
		}
		wpbc_set_tooltip___for__calendar_date( tooltip_text, resource_id, class_day );



		//  U n h o v e r i n g    in    UNSELECTABLE_CALENDAR  ------------------------------------------------------------
		var is_unselectable_calendar = ( jQuery( '#calendar_booking_unselectable' + resource_id ).length > 0);				// FixIn: 8.0.1.2.
		var is_booking_form_exist    = ( jQuery( '#booking_form_div' + resource_id ).length > 0 );

		if ( ( is_unselectable_calendar ) && ( ! is_booking_form_exist ) ){

			/**
			 *  Un Hover all dates in calendar (without the booking form), if only Availability Calendar here and we do not insert Booking form by mistake.
			 */

			wpbc_calendars__clear_days_highlighting( resource_id ); 							// Clear days highlighting

			var css_of_calendar = '.wpbc_only_calendar #calendar_booking' + resource_id;
			jQuery( css_of_calendar + ' .datepick-days-cell, '
				  + css_of_calendar + ' .datepick-days-cell a' ).css( 'cursor', 'default' );	// Set cursor to Default
			return false;
		}



		//  D a y s    H o v e r i n g  ------------------------------------------------------------------------------------
		if (
			   ( location.href.indexOf( 'page=wpbc' ) == -1 )
			|| ( location.href.indexOf( 'page=wpbc-new' ) > 0 )
			|| ( location.href.indexOf( 'page=wpbc-setup' ) > 0 )
			|| ( location.href.indexOf( 'page=wpbc-availability' ) > 0 )
			|| (  ( location.href.indexOf( 'page=wpbc-settings' ) > 0 )  &&
				  ( location.href.indexOf( '&tab=form' ) > 0 )
			   )
		){
			// The same as dates selection,  but for days hovering

			if ( 'function' == typeof( wpbc__calendar__do_days_highlight__bs ) ){
				wpbc__calendar__do_days_highlight__bs( sql_class_day, date, resource_id );
			}
		}

	}


	/**
	 * Select calendar date cells
	 *
	 * @param date										-  JavaScript Date Obj:  		Mon Dec 11 2023 00:00:00 GMT+0200 (Eastern European Standard Time)
	 * @param calendar_params_arr						-  Calendar Settings Object:  	{
	 *																  						"resource_id": 4
	 *																					}
	 * @param datepick_this								- this of datepick Obj
	 *
	 */
	function wpbc__calendar__on_select_days( date, calendar_params_arr, datepick_this ){

		var resource_id = ( 'undefined' !== typeof(calendar_params_arr[ 'resource_id' ]) ) ? calendar_params_arr[ 'resource_id' ] : '1';		// '1'

		// Set unselectable,  if only Availability Calendar  here (and we do not insert Booking form by mistake).
		var is_unselectable_calendar = ( jQuery( '#calendar_booking_unselectable' + resource_id ).length > 0);				// FixIn: 8.0.1.2.
		var is_booking_form_exist    = ( jQuery( '#booking_form_div' + resource_id ).length > 0 );
		if ( ( is_unselectable_calendar ) && ( ! is_booking_form_exist ) ){
			wpbc_calendar__unselect_all_dates( resource_id );																			// Unselect Dates
			jQuery('.wpbc_only_calendar .popover_calendar_hover').remove();                      							// Hide all opened popovers
			return false;
		}

		jQuery( '#date_booking' + resource_id ).val( date );																// Add selected dates to  hidden textarea


		if ( 'function' === typeof (wpbc__calendar__do_days_select__bs) ){ wpbc__calendar__do_days_select__bs( date, resource_id ); }

		wpbc_disable_time_fields_in_booking_form( resource_id );

		// Hook -- trigger day selection -----------------------------------------------------------------------------------
		var mouse_clicked_dates = date;																						// Can be: "05.10.2023 - 07.10.2023"  |  "10.10.2023 - 10.10.2023"  |
		var all_selected_dates_arr = wpbc_get__selected_dates_sql__as_arr( resource_id );									// Can be: [ "2023-10-05", "2023-10-06", "2023-10-07", … ]
		jQuery( ".booking_form_div" ).trigger( "date_selected", [ resource_id, mouse_clicked_dates, all_selected_dates_arr ] );
	}

	// Mark middle selected dates with 0.5 opacity		// FixIn: 10.3.0.9.
	jQuery( document ).ready( function (){
		jQuery( ".booking_form_div" ).on( 'date_selected', function ( event, resource_id, date ){
				if (
					   (  'fixed' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ))
					|| ('dynamic' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ))
				){
					var closed_timer = setTimeout( function (){
						var middle_days_opacity = _wpbc.get_other_param( 'calendars__days_selection__middle_days_opacity' );
						jQuery( '#calendar_booking' + resource_id + ' .datepick-current-day' ).not( ".selected_check_in_out" ).css( 'opacity', middle_days_opacity );
					}, 10 );
				}
		} );
	} );


	/**
	 * --  T i m e    F i e l d s     start  --------------------------------------------------------------------------
	 */

	/**
	 * Disable time slots in booking form depend on selected dates and booked dates/times
	 *
	 * @param resource_id
	 */
	function wpbc_disable_time_fields_in_booking_form( resource_id ){

		/**
		 * 	1. Get all time fields in the booking form as array  of objects
		 * 					[
		 * 					 	   {	jquery_option:      jQuery_Object {}
		 * 								name:               'rangetime2[]'
		 * 								times_as_seconds:   [ 21600, 23400 ]
		 * 								value_option_24h:   '06:00 - 06:30'
		 * 					     }
		 * 					  ...
		 * 						   {	jquery_option:      jQuery_Object {}
		 * 								name:               'starttime2[]'
		 * 								times_as_seconds:   [ 21600 ]
		 * 								value_option_24h:   '06:00'
		 *  					    }
		 * 					 ]
		 */
		var time_fields_obj_arr = wpbc_get__time_fields__in_booking_form__as_arr( resource_id );

		// 2. Get all selected dates in  SQL format  like this [ "2023-08-23", "2023-08-24", "2023-08-25", ... ]
		var selected_dates_arr = wpbc_get__selected_dates_sql__as_arr( resource_id );

		// 3. Get child booking resources  or single booking resource  that  exist  in dates
		var child_resources_arr = wpbc_clone_obj( _wpbc.booking__get_param_value( resource_id, 'resources_id_arr__in_dates' ) );

		var sql_date;
		var child_resource_id;
		var merged_seconds;
		var time_fields_obj;
		var is_intersect;
		var is_check_in;

		var today_time__real  = new Date( _wpbc.get_other_param( 'time_local_arr' )[0], ( parseInt( _wpbc.get_other_param( 'time_local_arr' )[1] ) - 1), _wpbc.get_other_param( 'time_local_arr' )[2], _wpbc.get_other_param( 'time_local_arr' )[3], _wpbc.get_other_param( 'time_local_arr' )[4], 0 );
		var today_time__shift = new Date( _wpbc.get_other_param( 'today_arr'      )[0], ( parseInt( _wpbc.get_other_param(      'today_arr' )[1] ) - 1), _wpbc.get_other_param( 'today_arr'      )[2], _wpbc.get_other_param( 'today_arr'      )[3], _wpbc.get_other_param( 'today_arr'      )[4], 0 );

		// 4. Loop  all  time Fields options		// FixIn: 10.3.0.2.
		for ( let field_key = 0; field_key < time_fields_obj_arr.length; field_key++ ){

			time_fields_obj_arr[ field_key ].disabled = 0;          // By default, this time field is not disabled.

			time_fields_obj = time_fields_obj_arr[ field_key ];		// { times_as_seconds: [ 21600, 23400 ], value_option_24h: '06:00 - 06:30', name: 'rangetime2[]', jquery_option: jQuery_Object {}}

			// Loop  all  selected dates.
			for ( var i = 0; i < selected_dates_arr.length; i++ ) {

				// Get Date: '2023-08-18'.
				sql_date = selected_dates_arr[i];

				var is_time_in_past = wpbc_check_is_time_in_past( today_time__shift, sql_date, time_fields_obj );
				// Exception  for 'End Time' field,  when  selected several dates. // FixIn: 10.14.1.5.
				if ( ('On' !== _wpbc.calendar__get_param_value( resource_id, 'booking_recurrent_time' )) &&
					(-1 !== time_fields_obj.name.indexOf( 'endtime' )) &&
					(selected_dates_arr.length > 1)
				) {
					is_time_in_past = wpbc_check_is_time_in_past( today_time__shift, selected_dates_arr[(selected_dates_arr.length - 1)], time_fields_obj );
				}
				if ( is_time_in_past ) {
					// This time for selected date already  in the past.
					time_fields_obj_arr[field_key].disabled = 1;
					break;											// exist  from   Dates LOOP.
				}
				// FixIn: 9.9.0.31.
				if (
					   ( 'Off' === _wpbc.calendar__get_param_value( resource_id, 'booking_recurrent_time' ) )
					&& ( selected_dates_arr.length>1 )
				){
					//TODO: skip some fields checking if it's start / end time for mulple dates  selection  mode.
					//TODO: we need to fix situation  for entimes,  when  user  select  several  dates,  and in start  time booked 00:00 - 15:00 , but systsme block untill 15:00 the end time as well,  which  is wrong,  because it 2 or 3 dates selection  and end date can be fullu  available

					if ( (0 == i) && (time_fields_obj[ 'name' ].indexOf( 'endtime' ) >= 0) ){
						break;
					}
					if ( ( (selected_dates_arr.length-1) == i ) && (time_fields_obj[ 'name' ].indexOf( 'starttime' ) >= 0) ){
						break;
					}
				}



				var how_many_resources_intersected = 0;
				// Loop all resources ID
					// for ( var res_key in child_resources_arr ){	 						// FixIn: 10.3.0.2.
				for ( let res_key = 0; res_key < child_resources_arr.length; res_key++ ){

					child_resource_id = child_resources_arr[ res_key ];

					// _wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[12].booked_time_slots.merged_seconds		= [ "07:00:11 - 07:30:02", "10:00:11 - 00:00:00" ]
					// _wpbc.bookings_in_calendar__get_for_date(2,'2023-08-21')[2].booked_time_slots.merged_seconds			= [  [ 25211, 27002 ], [ 36011, 86400 ]  ]

					if ( false !== _wpbc.bookings_in_calendar__get_for_date( resource_id, sql_date ) ){
						merged_seconds = _wpbc.bookings_in_calendar__get_for_date( resource_id, sql_date )[ child_resource_id ].booked_time_slots.merged_seconds;		// [  [ 25211, 27002 ], [ 36011, 86400 ]  ]
					} else {
						merged_seconds = [];
					}
					if ( time_fields_obj.times_as_seconds.length > 1 ){
						is_intersect = wpbc_is_intersect__range_time_interval(  [
																					[
																						( parseInt( time_fields_obj.times_as_seconds[0] ) + 20 ),
																						( parseInt( time_fields_obj.times_as_seconds[1] ) - 20 )
																					]
																				]
																				, merged_seconds );
					} else {
						is_check_in = (-1 !== time_fields_obj.name.indexOf( 'start' ));
						is_intersect = wpbc_is_intersect__one_time_interval(
																				( ( is_check_in )
																							  ? parseInt( time_fields_obj.times_as_seconds ) + 20
																							  : parseInt( time_fields_obj.times_as_seconds ) - 20
																				)
																				, merged_seconds );
					}
					if (is_intersect){
						how_many_resources_intersected++;			// Increase
					}

				}

				if ( child_resources_arr.length == how_many_resources_intersected ) {
					// All resources intersected,  then  it's means that this time-slot or time must  be  Disabled, and we can  exist  from   selected_dates_arr LOOP

					time_fields_obj_arr[ field_key ].disabled = 1;
					break;											// exist  from   Dates LOOP
				}
			}
		}


		// 5. Now we can disable time slot in HTML by  using  ( field.disabled == 1 ) property
		wpbc__html__time_field_options__set_disabled( time_fields_obj_arr );

		jQuery( ".booking_form_div" ).trigger( 'wpbc_hook_timeslots_disabled', [resource_id, selected_dates_arr] );					// Trigger hook on disabling timeslots.		Usage: 	jQuery( ".booking_form_div" ).on( 'wpbc_hook_timeslots_disabled', function ( event, bk_type, all_dates ){ ... } );		// FixIn: 8.7.11.9.
	}


		/**
		 * Check if specific time(-slot) already  in the past for selected date
		 *
		 * @param js_current_time_to_check		- JS Date
		 * @param sql_date						- '2025-01-26'
		 * @param time_fields_obj				- Object
		 * @returns {boolean}
		 */
		function wpbc_check_is_time_in_past( js_current_time_to_check, sql_date, time_fields_obj ) {

			// FixIn: 10.9.6.4
			var sql_date_arr = sql_date.split( '-' );
			var sql_date__midnight = new Date( parseInt( sql_date_arr[0] ), ( parseInt( sql_date_arr[1] ) - 1 ), parseInt( sql_date_arr[2] ), 0, 0, 0 );
			var sql_date__midnight_miliseconds = sql_date__midnight.getTime();

			var is_intersect = false;

			if ( time_fields_obj.times_as_seconds.length > 1 ) {

				if ( js_current_time_to_check.getTime() > (sql_date__midnight_miliseconds + (parseInt( time_fields_obj.times_as_seconds[0] ) + 20) * 1000) ) {
					is_intersect = true;
				}
				if ( js_current_time_to_check.getTime() > (sql_date__midnight_miliseconds + (parseInt( time_fields_obj.times_as_seconds[1] ) - 20) * 1000) ) {
					is_intersect = true;
				}

			} else {
				var is_check_in = (-1 !== time_fields_obj.name.indexOf( 'start' ));

				var times_as_seconds_check = (is_check_in) ? parseInt( time_fields_obj.times_as_seconds ) + 20 : parseInt( time_fields_obj.times_as_seconds ) - 20;

				times_as_seconds_check = sql_date__midnight_miliseconds + times_as_seconds_check * 1000;

				if ( js_current_time_to_check.getTime() > times_as_seconds_check ) {
					is_intersect = true;
				}
			}

			return is_intersect;
		}

		/**
		 * Is number inside /intersect  of array of intervals ?
		 *
		 * @param time_A		     	- 25800
		 * @param time_interval_B		- [  [ 25211, 27002 ], [ 36011, 86400 ]  ]
		 * @returns {boolean}
		 */
		function wpbc_is_intersect__one_time_interval( time_A, time_interval_B ){

			for ( var j = 0; j < time_interval_B.length; j++ ){

				if ( (parseInt( time_A ) > parseInt( time_interval_B[ j ][ 0 ] )) && (parseInt( time_A ) < parseInt( time_interval_B[ j ][ 1 ] )) ){
					return true
				}

				// if ( ( parseInt( time_A ) == parseInt( time_interval_B[ j ][ 0 ] ) ) || ( parseInt( time_A ) == parseInt( time_interval_B[ j ][ 1 ] ) ) ) {
				// 			// Time A just  at  the border of interval
				// }
			}

		    return false;
		}

		/**
		 * Is these array of intervals intersected ?
		 *
		 * @param time_interval_A		- [ [ 21600, 23400 ] ]
		 * @param time_interval_B		- [  [ 25211, 27002 ], [ 36011, 86400 ]  ]
		 * @returns {boolean}
		 */
		function wpbc_is_intersect__range_time_interval( time_interval_A, time_interval_B ){

			var is_intersect;

			for ( var i = 0; i < time_interval_A.length; i++ ){

				for ( var j = 0; j < time_interval_B.length; j++ ){

					is_intersect = wpbc_intervals__is_intersected( time_interval_A[ i ], time_interval_B[ j ] );

					if ( is_intersect ){
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Get all time fields in the booking form as array  of objects
		 *
		 * @param resource_id
		 * @returns []
		 *
		 * 		Example:
		 * 					[
		 * 					 	   {
		 * 								value_option_24h:   '06:00 - 06:30'
		 * 								times_as_seconds:   [ 21600, 23400 ]
		 * 					 	   		jquery_option:      jQuery_Object {}
		 * 								name:               'rangetime2[]'
		 * 					     }
		 * 					  ...
		 * 						   {
		 * 								value_option_24h:   '06:00'
		 * 								times_as_seconds:   [ 21600 ]
		 * 						   		jquery_option:      jQuery_Object {}
		 * 								name:               'starttime2[]'
		 *  					    }
		 * 					 ]
		 */
		function wpbc_get__time_fields__in_booking_form__as_arr( resource_id ){
		    /**
			 * Fields with  []  like this   select[name="rangetime1[]"]
			 * it's when we have 'multiple' in shortcode:   [select* rangetime multiple  "06:00 - 06:30" ... ]
			 */
			var time_fields_arr=[
									'select[name="rangetime' + resource_id + '"]',
									'select[name="rangetime' + resource_id + '[]"]',
									'select[name="starttime' + resource_id + '"]',
									'select[name="starttime' + resource_id + '[]"]',
									'select[name="endtime' + resource_id + '"]',
									'select[name="endtime' + resource_id + '[]"]'
								];

			var time_fields_obj_arr = [];

			// Loop all Time Fields
			for ( var ctf= 0; ctf < time_fields_arr.length; ctf++ ){

				var time_field = time_fields_arr[ ctf ];
				var time_option = jQuery( time_field + ' option' );

				// Loop all options in time field
				for ( var j = 0; j < time_option.length; j++ ){

					var jquery_option = jQuery( time_field + ' option:eq(' + j + ')' );
					var value_option_seconds_arr = jquery_option.val().split( '-' );
					var times_as_seconds = [];

					// Get time as seconds
					if ( value_option_seconds_arr.length ){									// FixIn: 9.8.10.1.
						for ( let i = 0; i < value_option_seconds_arr.length; i++ ){		// FixIn: 10.0.0.56.
							// value_option_seconds_arr[i] = '14:00 '  | ' 16:00'   (if from 'rangetime') and '16:00'  if (start/end time)

							var start_end_times_arr = value_option_seconds_arr[ i ].trim().split( ':' );

							var time_in_seconds = parseInt( start_end_times_arr[ 0 ] ) * 60 * 60 + parseInt( start_end_times_arr[ 1 ] ) * 60;

							times_as_seconds.push( time_in_seconds );
						}
					}

					time_fields_obj_arr.push( {
												'name'            : jQuery( time_field ).attr( 'name' ),
												'value_option_24h': jquery_option.val(),
												'jquery_option'   : jquery_option,
												'times_as_seconds': times_as_seconds
											} );
				}
			}

			return time_fields_obj_arr;
		}

			/**
			 * Disable HTML options and add booked CSS class
			 *
			 * @param time_fields_obj_arr      - this value is from  the func:  	wpbc_get__time_fields__in_booking_form__as_arr( resource_id )
			 * 					[
			 * 					 	   {	jquery_option:      jQuery_Object {}
			 * 								name:               'rangetime2[]'
			 * 								times_as_seconds:   [ 21600, 23400 ]
			 * 								value_option_24h:   '06:00 - 06:30'
			 * 	  						    disabled = 1
			 * 					     }
			 * 					  ...
			 * 						   {	jquery_option:      jQuery_Object {}
			 * 								name:               'starttime2[]'
			 * 								times_as_seconds:   [ 21600 ]
			 * 								value_option_24h:   '06:00'
			 *   							disabled = 0
			 *  					    }
			 * 					 ]
			 *
			 */
			function wpbc__html__time_field_options__set_disabled( time_fields_obj_arr ){

				var jquery_option;

				for ( var i = 0; i < time_fields_obj_arr.length; i++ ){

					var jquery_option = time_fields_obj_arr[ i ].jquery_option;

					if ( 1 == time_fields_obj_arr[ i ].disabled ){
						jquery_option.prop( 'disabled', true ); 		// Make disable some options
						jquery_option.addClass( 'booked' );           	// Add "booked" CSS class

						// if this booked element selected --> then deselect  it
						if ( jquery_option.prop( 'selected' ) ){
							jquery_option.prop( 'selected', false );

							jquery_option.parent().find( 'option:not([disabled]):first' ).prop( 'selected', true ).trigger( "change" );
						}

					} else {
						jquery_option.prop( 'disabled', false );  		// Make active all times
						jquery_option.removeClass( 'booked' );   		// Remove class "booked"
					}
				}

			}

	/**
	 * Check if this time_range | Time_Slot is Full Day  booked
	 *
	 * @param timeslot_arr_in_seconds		- [ 36011, 86400 ]
	 * @returns {boolean}
	 */
	function wpbc_is_this_timeslot__full_day_booked( timeslot_arr_in_seconds ){

		if (
				( timeslot_arr_in_seconds.length > 1 )
			&& ( parseInt( timeslot_arr_in_seconds[ 0 ] ) < 30 )
			&& ( parseInt( timeslot_arr_in_seconds[ 1 ] ) >  ( (24 * 60 * 60) - 30) )
		){
			return true;
		}

		return false;
	}


	// -----------------------------------------------------------------------------------------------------------------
	/*  ==  S e l e c t e d    D a t e s  /  T i m e - F i e l d s  ==
	// ----------------------------------------------------------------------------------------------------------------- */

	/**
	 *  Get all selected dates in SQL format like this [ "2023-08-23", "2023-08-24" , ... ]
	 *
	 * @param resource_id
	 * @returns {[]}			[ "2023-08-23", "2023-08-24", "2023-08-25", "2023-08-26", "2023-08-27", "2023-08-28", "2023-08-29" ]
	 */
	function wpbc_get__selected_dates_sql__as_arr( resource_id ){

		var selected_dates_arr = [];
		selected_dates_arr = jQuery( '#date_booking' + resource_id ).val().split(',');

		if ( selected_dates_arr.length ){												// FixIn: 9.8.10.1.
			for ( let i = 0; i < selected_dates_arr.length; i++ ){						// FixIn: 10.0.0.56.
				selected_dates_arr[ i ] = selected_dates_arr[ i ].trim();
				selected_dates_arr[ i ] = selected_dates_arr[ i ].split( '.' );
				if ( selected_dates_arr[ i ].length > 1 ){
					selected_dates_arr[ i ] = selected_dates_arr[ i ][ 2 ] + '-' + selected_dates_arr[ i ][ 1 ] + '-' + selected_dates_arr[ i ][ 0 ];
				}
			}
		}

		// Remove empty elements from an array
		selected_dates_arr = selected_dates_arr.filter( function ( n ){ return parseInt(n); } );

		selected_dates_arr.sort();

		return selected_dates_arr;
	}


	/**
	 * Get all time fields in the booking form as array  of objects
	 *
	 * @param resource_id
	 * @param is_only_selected_time
	 * @returns []
	 *
	 * 		Example:
	 * 					[
	 * 					 	   {
	 * 								value_option_24h:   '06:00 - 06:30'
	 * 								times_as_seconds:   [ 21600, 23400 ]
	 * 					 	   		jquery_option:      jQuery_Object {}
	 * 								name:               'rangetime2[]'
	 * 					     }
	 * 					  ...
	 * 						   {
	 * 								value_option_24h:   '06:00'
	 * 								times_as_seconds:   [ 21600 ]
	 * 						   		jquery_option:      jQuery_Object {}
	 * 								name:               'starttime2[]'
	 *  					    }
	 * 					 ]
	 */
	function wpbc_get__selected_time_fields__in_booking_form__as_arr( resource_id, is_only_selected_time = true ){
		/**
		 * Fields with  []  like this   select[name="rangetime1[]"]
		 * it's when we have 'multiple' in shortcode:   [select* rangetime multiple  "06:00 - 06:30" ... ]
		 */
		var time_fields_arr=[
								'select[name="rangetime' + resource_id + '"]',
								'select[name="rangetime' + resource_id + '[]"]',
								'select[name="starttime' + resource_id + '"]',
								'select[name="starttime' + resource_id + '[]"]',
								'select[name="endtime' + resource_id + '"]',
								'select[name="endtime' + resource_id + '[]"]',
								'select[name="durationtime' + resource_id + '"]',
								'select[name="durationtime' + resource_id + '[]"]'
							];

		var time_fields_obj_arr = [];

		// Loop all Time Fields
		for ( var ctf= 0; ctf < time_fields_arr.length; ctf++ ){

			var time_field = time_fields_arr[ ctf ];

			var time_option;
			if ( is_only_selected_time ){
				time_option = jQuery( '#booking_form' + resource_id + ' ' + time_field + ' option:selected' );			// Exclude conditional  fields,  because of using '#booking_form3 ...'
			} else {
				time_option = jQuery( '#booking_form' + resource_id + ' ' + time_field + ' option' );				// All  time fields
			}


			// Loop all options in time field
			for ( var j = 0; j < time_option.length; j++ ){

				var jquery_option = jQuery( time_option[ j ] );		// Get only  selected options 	//jQuery( time_field + ' option:eq(' + j + ')' );
				var value_option_seconds_arr = jquery_option.val().split( '-' );
				var times_as_seconds = [];

				// Get time as seconds
				if ( value_option_seconds_arr.length ){				 								// FixIn: 9.8.10.1.
					for ( let i = 0; i < value_option_seconds_arr.length; i++ ){					// FixIn: 10.0.0.56.
						// value_option_seconds_arr[i] = '14:00 '  | ' 16:00'   (if from 'rangetime') and '16:00'  if (start/end time)

						var start_end_times_arr = value_option_seconds_arr[ i ].trim().split( ':' );

						var time_in_seconds = parseInt( start_end_times_arr[ 0 ] ) * 60 * 60 + parseInt( start_end_times_arr[ 1 ] ) * 60;

						times_as_seconds.push( time_in_seconds );
					}
				}

				time_fields_obj_arr.push( {
											'name'            : jQuery( '#booking_form' + resource_id + ' ' + time_field ).attr( 'name' ),
											'value_option_24h': jquery_option.val(),
											'jquery_option'   : jquery_option,
											'times_as_seconds': times_as_seconds
										} );
			}
		}

		// Text:   [starttime] - [endtime] -----------------------------------------------------------------------------

		var text_time_fields_arr=[
									'input[name="starttime' + resource_id + '"]',
									'input[name="endtime' + resource_id + '"]',
								];
		for ( var tf= 0; tf < text_time_fields_arr.length; tf++ ){

			var text_jquery = jQuery( '#booking_form' + resource_id + ' ' + text_time_fields_arr[ tf ] );								// Exclude conditional  fields,  because of using '#booking_form3 ...'
			if ( text_jquery.length > 0 ){

				var time__h_m__arr = text_jquery.val().trim().split( ':' );														// '14:00'
				if ( 0 == time__h_m__arr.length ){
					continue;									// Not entered time value in a field
				}
				if ( 1 == time__h_m__arr.length ){
					if ( '' === time__h_m__arr[ 0 ] ){
						continue;								// Not entered time value in a field
					}
					time__h_m__arr[ 1 ] = 0;
				}
				var text_time_in_seconds = parseInt( time__h_m__arr[ 0 ] ) * 60 * 60 + parseInt( time__h_m__arr[ 1 ] ) * 60;

				var text_times_as_seconds = [];
				text_times_as_seconds.push( text_time_in_seconds );

				time_fields_obj_arr.push( {
											'name'            : text_jquery.attr( 'name' ),
											'value_option_24h': text_jquery.val(),
											'jquery_option'   : text_jquery,
											'times_as_seconds': text_times_as_seconds
										} );
			}
		}

		return time_fields_obj_arr;
	}



// ---------------------------------------------------------------------------------------------------------------------
/*  ==  S U P P O R T    for    C A L E N D A R  ==
// --------------------------------------------------------------------------------------------------------------------- */

	/**
	 * Get Calendar datepick Instance.
	 *
	 * @param {int|string} resource_id
	 * @returns {*|null}
	 */
	function wpbc_calendar__get_inst(resource_id) {

		if ( 'undefined' === typeof (resource_id) ) {
			resource_id = '1';
		}

		if ( jQuery( '#calendar_booking' + resource_id ).length > 0 ) {

			try {
				var inst = jQuery.datepick._getInst( jQuery( '#calendar_booking' + resource_id ).get( 0 ) );
				return inst ? inst : null;
			} catch ( e ) {
				return null;
			}
		}

		return null;
	}


	/**
	 * Unselect  all dates in calendar and visually update this calendar
	 *
	 * @param resource_id		ID of booking resource
	 * @returns {boolean}		true on success | false,  if no such  calendar
	 */
	function wpbc_calendar__unselect_all_dates( resource_id ){

		if ( 'undefined' === typeof (resource_id) ){
			resource_id = '1';
		}

		var inst = wpbc_calendar__get_inst( resource_id )

		if ( null !== inst ){

			// Unselect all dates and set  properties of Datepick
			jQuery( '#date_booking' + resource_id ).val( '' );      //FixIn: 5.4.3
			inst.stayOpen = false;
			inst.dates = [];
			jQuery.datepick._updateDatepick( inst );

			return true
		}

		return false;

	}

	/**
	 * Clear days highlighting in All or specific Calendars
	 *
     * @param resource_id  - can be skiped to  clear highlighting in all calendars
     */
	function wpbc_calendars__clear_days_highlighting( resource_id ){

		if ( 'undefined' !== typeof ( resource_id ) ){

			jQuery( '#calendar_booking' + resource_id + ' .datepick-days-cell-over' ).removeClass( 'datepick-days-cell-over' );		// Clear in specific calendar

		} else {
			jQuery( '.datepick-days-cell-over' ).removeClass( 'datepick-days-cell-over' );								// Clear in all calendars
		}
	}

	/**
	 * Scroll to specific month in calendar
	 *
	 * @param resource_id		ID of resource
	 * @param year				- real year  - 2023
	 * @param month				- real month - 12
	 * @returns {boolean}
	 */
	function wpbc_calendar__scroll_to( resource_id, year, month ){

		if ( 'undefined' === typeof (resource_id) ){ resource_id = '1'; }
		var inst = wpbc_calendar__get_inst( resource_id )
		if ( null !== inst ){

			year  = parseInt( year );
			month = parseInt( month ) - 1;		// In JS date,  month -1

			inst.cursorDate = new Date();
			// In some cases,  the setFullYear can  set  only Year,  and not the Month and day      // FixIn: 6.2.3.5.
			inst.cursorDate.setFullYear( year, month, 1 );
			inst.cursorDate.setMonth( month );
			inst.cursorDate.setDate( 1 );

			inst.drawMonth = inst.cursorDate.getMonth();
			inst.drawYear = inst.cursorDate.getFullYear();

			jQuery.datepick._notifyChange( inst );
			jQuery.datepick._adjustInstDate( inst );
			jQuery.datepick._showDate( inst );
			jQuery.datepick._updateDatepick( inst );

			return true;
		}
		return false;
	}

	/**
	 * Is this date selectable in calendar (mainly it's means AVAILABLE date)
	 *
	 * @param {int|string} resource_id		1
	 * @param {string} sql_class_day		'2023-08-11'
	 * @returns {boolean}					true | false
	 */
	function wpbc_is_this_day_selectable( resource_id, sql_class_day ){

		// Get Data --------------------------------------------------------------------------------------------------------
		var date_bookings_obj = _wpbc.bookings_in_calendar__get_for_date( resource_id, sql_class_day );

		var is_day_selectable = ( parseInt( date_bookings_obj[ 'day_availability' ] ) > 0 );

		if ( typeof (date_bookings_obj[ 'summary' ]) === 'undefined' ){
			return is_day_selectable;
		}

		if ( 'available' != date_bookings_obj[ 'summary']['status_for_day' ] ){

			var is_set_pending_days_selectable = _wpbc.calendar__get_param_value( resource_id, 'pending_days_selectable' );		// set pending days selectable          // FixIn: 8.6.1.18.

			switch ( date_bookings_obj[ 'summary']['status_for_bookings' ] ){
				case 'pending':
				// Situations for "change-over" days:
				case 'pending_pending':
				case 'pending_approved':
				case 'approved_pending':
					is_day_selectable = (is_day_selectable) ? true : is_set_pending_days_selectable;
					break;
				default:
			}
		}

		return is_day_selectable;
	}

	/**
	 * Is date to check IN array of selected dates
	 *
	 * @param {date}js_date_to_check		- JS Date			- simple  JavaScript Date object
	 * @param {[]} js_dates_arr			- [ JSDate, ... ]   - array  of JS dates
	 * @returns {boolean}
	 */
	function wpbc_is_this_day_among_selected_days( js_date_to_check, js_dates_arr ){

		for ( var date_index = 0; date_index < js_dates_arr.length ; date_index++ ){     									// FixIn: 8.4.5.16.
			if ( ( js_dates_arr[ date_index ].getFullYear() === js_date_to_check.getFullYear() ) &&
				 ( js_dates_arr[ date_index ].getMonth() === js_date_to_check.getMonth() ) &&
				 ( js_dates_arr[ date_index ].getDate() === js_date_to_check.getDate() ) ) {
					return true;
			}
		}

		return  false;
	}

	/**
	 * Get SQL Class Date '2023-08-01' from  JS Date
	 *
	 * @param date				JS Date
	 * @returns {string}		'2023-08-12'
	 */
	function wpbc__get__sql_class_date( date ){

		var sql_class_day = date.getFullYear() + '-';
			sql_class_day += ( ( date.getMonth() + 1 ) < 10 ) ? '0' : '';
			sql_class_day += ( date.getMonth() + 1 ) + '-'
			sql_class_day += ( date.getDate() < 10 ) ? '0' : '';
			sql_class_day += date.getDate();

			return sql_class_day;
	}

	/**
	 * Get JS Date from  the SQL date format '2024-05-14'
	 * @param sql_class_date
	 * @returns {Date}
	 */
	function wpbc__get__js_date( sql_class_date ){

		var sql_class_date_arr = sql_class_date.split( '-' );

		var date_js = new Date();

		date_js.setFullYear( parseInt( sql_class_date_arr[ 0 ] ), (parseInt( sql_class_date_arr[ 1 ] ) - 1), parseInt( sql_class_date_arr[ 2 ] ) );  // year, month, date

		// Without this time adjust Dates selection  in Datepicker can not work!!!
		date_js.setHours(0);
		date_js.setMinutes(0);
		date_js.setSeconds(0);
		date_js.setMilliseconds(0);

		return date_js;
	}

	/**
	 * Get TD Class Date '1-31-2023' from  JS Date
	 *
	 * @param date				JS Date
	 * @returns {string}		'1-31-2023'
	 */
	function wpbc__get__td_class_date( date ){

		var td_class_day = (date.getMonth() + 1) + '-' + date.getDate() + '-' + date.getFullYear();								// '1-9-2023'

		return td_class_day;
	}

	/**
	 * Get date params from  string date
	 *
	 * @param date			string date like '31.5.2023'
	 * @param separator		default '.'  can be skipped.
	 * @returns {  {date: number, month: number, year: number}  }
	 */
	function wpbc__get__date_params__from_string_date( date , separator){

		separator = ( 'undefined' !== typeof (separator) ) ? separator : '.';

		var date_arr = date.split( separator );
		var date_obj = {
			'year' :  parseInt( date_arr[ 2 ] ),
			'month': (parseInt( date_arr[ 1 ] ) - 1),
			'date' :  parseInt( date_arr[ 0 ] )
		};
		return date_obj;		// for 		 = new Date( date_obj.year , date_obj.month , date_obj.date );
	}

	/**
	 * Add Spin Loader to  calendar
	 * @param resource_id
	 */
	function wpbc_calendar__loading__start( resource_id ){
		if ( ! jQuery( '#calendar_booking' + resource_id ).next().hasClass( 'wpbc_spins_loader_wrapper' ) ){
			jQuery( '#calendar_booking' + resource_id ).after( '<div class="wpbc_spins_loader_wrapper"><div class="wpbc_spins_loader"></div></div>' );
		}
		if ( ! jQuery( '#calendar_booking' + resource_id ).hasClass( 'wpbc_calendar_blur_small' ) ){
			jQuery( '#calendar_booking' + resource_id ).addClass( 'wpbc_calendar_blur_small' );
		}
		wpbc_calendar__blur__start( resource_id );
	}

	/**
	 * Remove Spin Loader to  calendar
	 * @param resource_id
	 */
	function wpbc_calendar__loading__stop( resource_id ){
		jQuery( '#calendar_booking' + resource_id + ' + .wpbc_spins_loader_wrapper' ).remove();
		jQuery( '#calendar_booking' + resource_id ).removeClass( 'wpbc_calendar_blur_small' );
		wpbc_calendar__blur__stop( resource_id );
	}

	/**
	 * Add Blur to  calendar
	 * @param resource_id
	 */
	function wpbc_calendar__blur__start( resource_id ){
		if ( ! jQuery( '#calendar_booking' + resource_id ).hasClass( 'wpbc_calendar_blur' ) ){
			jQuery( '#calendar_booking' + resource_id ).addClass( 'wpbc_calendar_blur' );
		}
	}

	/**
	 * Remove Blur in  calendar
	 * @param resource_id
	 */
	function wpbc_calendar__blur__stop( resource_id ){
		jQuery( '#calendar_booking' + resource_id ).removeClass( 'wpbc_calendar_blur' );
	}


	// .................................................................................................................
	/*  ==  Calendar Update  - View  ==
	// ................................................................................................................. */

	/**
	 * Update look of calendar (safe).
	 *
	 * In Elementor preview the DOM can be re-rendered, so the calendar element may exist
	 * while the Datepick instance is missing. In that case try to (re)initialize.
	 *
	 * @param {int|string} resource_id
	 * @return {boolean} true if updated, false if not possible
	 */
	function wpbc_calendar__update_look(resource_id) {

		var inst = wpbc_calendar__get_inst( resource_id );

		// If instance missing, try to re-init calendar once.
		if ( null === inst ) {

			var jq_cal = jQuery( '#calendar_booking' + resource_id );

			if ( jq_cal.length && ('function' === typeof wpbc_calendar_show) ) {

				// Elementor sometimes leaves stale class without real instance.
				if ( jq_cal.hasClass( 'hasDatepick' ) ) {
					jq_cal.removeClass( 'hasDatepick' );
				}

				// Try to init datepick markup now.
				wpbc_calendar_show( resource_id );

				// Try again.
				inst = wpbc_calendar__get_inst( resource_id );
			}
		}

		// Still no instance -> do not crash the whole ajax flow.
		if ( null === inst ) {
			return false;
		}

		jQuery.datepick._updateDatepick( inst );
		return true;
	}



	/**
	 * Update dynamically Number of Months in calendar
	 *
	 * @param resource_id int
	 * @param months_number int
	 */
	function wpbc_calendar__update_months_number( resource_id, months_number ){
		var inst = wpbc_calendar__get_inst( resource_id );
		if ( null !== inst ){
			inst.settings[ 'numberOfMonths' ] = months_number;
			//_wpbc.calendar__set_param_value( resource_id, 'calendar_number_of_months', months_number );
			wpbc_calendar__update_look( resource_id );
		}
	}


	/**
	 * Show calendar in  different Skin
	 *
	 * @param selected_skin_url
	 */
	function wpbc__calendar__change_skin( selected_skin_url ){

	//console.log( 'SKIN SELECTION ::', selected_skin_url );

		// Remove CSS skin
		var stylesheet = document.getElementById( 'wpbc-calendar-skin-css' );
		stylesheet.parentNode.removeChild( stylesheet );


		// Add new CSS skin
		var headID = document.getElementsByTagName( "head" )[ 0 ];
		var cssNode = document.createElement( 'link' );
		cssNode.type = 'text/css';
		cssNode.setAttribute( "id", "wpbc-calendar-skin-css" );
		cssNode.rel = 'stylesheet';
		cssNode.media = 'screen';
		cssNode.href = selected_skin_url;	//"http://beta/wp-content/plugins/booking/css/skins/green-01.css";
		headID.appendChild( cssNode );
	}


	function wpbc__css__change_skin( selected_skin_url, stylesheet_id = 'wpbc-time_picker-skin-css' ){

		// Remove CSS skin
		var stylesheet = document.getElementById( stylesheet_id );
		stylesheet.parentNode.removeChild( stylesheet );


		// Add new CSS skin
		var headID = document.getElementsByTagName( "head" )[ 0 ];
		var cssNode = document.createElement( 'link' );
		cssNode.type = 'text/css';
		cssNode.setAttribute( "id", stylesheet_id );
		cssNode.rel = 'stylesheet';
		cssNode.media = 'screen';
		cssNode.href = selected_skin_url;	//"http://beta/wp-content/plugins/booking/css/skins/green-01.css";
		headID.appendChild( cssNode );
	}


// ---------------------------------------------------------------------------------------------------------------------
/*  ==  S U P P O R T    M A T H  ==
// --------------------------------------------------------------------------------------------------------------------- */

		/**
		 * Merge several  intersected intervals or return not intersected:                        [[1,3],[2,6],[8,10],[15,18]]  ->   [[1,6],[8,10],[15,18]]
		 *
		 * @param [] intervals			 [ [1,3],[2,4],[6,8],[9,10],[3,7] ]
		 * @returns []					 [ [1,8],[9,10] ]
		 *
		 * Exmample: wpbc_intervals__merge_inersected(  [ [1,3],[2,4],[6,8],[9,10],[3,7] ]  );
		 */
		function wpbc_intervals__merge_inersected( intervals ){

			if ( ! intervals || intervals.length === 0 ){
				return [];
			}

			var merged = [];
			intervals.sort( function ( a, b ){
				return a[ 0 ] - b[ 0 ];
			} );

			var mergedInterval = intervals[ 0 ];

			for ( var i = 1; i < intervals.length; i++ ){
				var interval = intervals[ i ];

				if ( interval[ 0 ] <= mergedInterval[ 1 ] ){
					mergedInterval[ 1 ] = Math.max( mergedInterval[ 1 ], interval[ 1 ] );
				} else {
					merged.push( mergedInterval );
					mergedInterval = interval;
				}
			}

			merged.push( mergedInterval );
			return merged;
		}


		/**
		 * Is 2 intervals intersected:       [36011, 86392]    <=>    [1, 43192]  =>  true      ( intersected )
		 *
		 * Good explanation  here https://stackoverflow.com/questions/3269434/whats-the-most-efficient-way-to-test-if-two-ranges-overlap
		 *
		 * @param  interval_A   - [ 36011, 86392 ]
		 * @param  interval_B   - [     1, 43192 ]
		 *
		 * @return bool
		 */
		function wpbc_intervals__is_intersected( interval_A, interval_B ) {

			if (
					( 0 == interval_A.length )
				 || ( 0 == interval_B.length )
			){
				return false;
			}

			interval_A[ 0 ] = parseInt( interval_A[ 0 ] );
			interval_A[ 1 ] = parseInt( interval_A[ 1 ] );
			interval_B[ 0 ] = parseInt( interval_B[ 0 ] );
			interval_B[ 1 ] = parseInt( interval_B[ 1 ] );

			var is_intersected = Math.max( interval_A[ 0 ], interval_B[ 0 ] ) - Math.min( interval_A[ 1 ], interval_B[ 1 ] );

			// if ( 0 == is_intersected ) {
			//	                                 // Such ranges going one after other, e.g.: [ 12, 15 ] and [ 15, 21 ]
			// }

			if ( is_intersected < 0 ) {
				return true;                     // INTERSECTED
			}

			return false;                       // Not intersected
		}


		/**
		 * Get the closets ABS value of element in array to the current myValue
		 *
		 * @param myValue 	- int element to search closet 			4
		 * @param myArray	- array of elements where to search 	[5,8,1,7]
		 * @returns int												5
		 */
		function wpbc_get_abs_closest_value_in_arr( myValue, myArray ){

			if ( myArray.length == 0 ){ 								// If the array is empty -> return  the myValue
				return myValue;
			}

			var obj = myArray[ 0 ];
			var diff = Math.abs( myValue - obj );             	// Get distance between  1st element
			var closetValue = myArray[ 0 ];                   			// Save 1st element

			for ( var i = 1; i < myArray.length; i++ ){
				obj = myArray[ i ];

				if ( Math.abs( myValue - obj ) < diff ){     			// we found closer value -> save it
					diff = Math.abs( myValue - obj );
					closetValue = obj;
				}
			}

			return closetValue;
		}


// ---------------------------------------------------------------------------------------------------------------------
/*  ==  T O O L T I P S  ==
// --------------------------------------------------------------------------------------------------------------------- */

	/**
	 * Define tooltip to show,  when  mouse over Date in Calendar
	 *
	 * @param  tooltip_text			- Text to show				'Booked time: 12:00 - 13:00<br>Cost: $20.00'
	 * @param  resource_id			- ID of booking resource	'1'
	 * @param  td_class				- SQL class					'1-9-2023'
	 * @returns {boolean}					- defined to show or not
	 */
	function wpbc_set_tooltip___for__calendar_date( tooltip_text, resource_id, td_class ){

		//TODO: make escaping of text for quot symbols,  and JS/HTML...

		jQuery( '#calendar_booking' + resource_id + ' td.cal4date-' + td_class ).attr( 'data-content', tooltip_text );

		var td_el = jQuery( '#calendar_booking' + resource_id + ' td.cal4date-' + td_class ).get( 0 );					// FixIn: 9.0.1.1.

		if (
			   ( 'undefined' !== typeof(td_el) )
			&& ( undefined == td_el._tippy )
			&& ( '' !== tooltip_text )
		){

			wpbc_tippy( td_el , {
					content( reference ){

						var popover_content = reference.getAttribute( 'data-content' );

						return '<div class="popover popover_tippy">'
									+ '<div class="popover-content">'
										+ popover_content
									+ '</div>'
							 + '</div>';
					},
					allowHTML        : true,
					trigger			 : 'mouseenter focus',
					interactive      : false,
					hideOnClick      : true,
					interactiveBorder: 10,
					maxWidth         : 550,
					theme            : 'wpbc-tippy-times',
					placement        : 'top',
					delay			 : [400, 0],																		// FixIn: 9.4.2.2.
					//delay			 : [0, 9999999999],						// Debuge  tooltip
					ignoreAttributes : true,
					touch			 : true,								//['hold', 500], // 500ms delay				// FixIn: 9.2.1.5.
					appendTo: () => document.body,
			});

			return  true;
		}

		return  false;
	}


// ---------------------------------------------------------------------------------------------------------------------
/*  ==  Dates Functions  ==
// --------------------------------------------------------------------------------------------------------------------- */

/**
 * Get number of dates between 2 JS Dates
 *
 * @param date1		JS Date
 * @param date2		JS Date
 * @returns {number}
 */
function wpbc_dates__days_between(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24;

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime();
    var date2_ms = date2.getTime();

    // Calculate the difference in milliseconds
    var difference_ms =  date1_ms - date2_ms;

    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY);
}


/**
 * Check  if this array  of dates is consecutive array  of dates or not.
 * 		e.g.  ['2024-05-09','2024-05-19','2024-05-30'] -> false
 * 		e.g.  ['2024-05-09','2024-05-10','2024-05-11'] -> true
 * @param sql_dates_arr	 array		e.g.: ['2024-05-09','2024-05-19','2024-05-30']
 * @returns {boolean}
 */
function wpbc_dates__is_consecutive_dates_arr_range( sql_dates_arr ){													// FixIn: 10.0.0.50.

	if ( sql_dates_arr.length > 1 ){
		var previos_date = wpbc__get__js_date( sql_dates_arr[ 0 ] );
		var current_date;

		for ( var i = 1; i < sql_dates_arr.length; i++ ){
			current_date = wpbc__get__js_date( sql_dates_arr[i] );

			if ( wpbc_dates__days_between( current_date, previos_date ) != 1 ){
				return  false;
			}

			previos_date = current_date;
		}
	}

	return true;
}


// ---------------------------------------------------------------------------------------------------------------------
/*  ==  Auto Dates Selection  ==
// --------------------------------------------------------------------------------------------------------------------- */

/**
 *  == How to  use ? ==
 *
 *  For Dates selection, we need to use this logic!     We need select the dates only after booking data loaded!
 *
 *  Check example bellow.
 *
 *	// Fire on all booking dates loaded
 *	jQuery( 'body' ).on( 'wpbc_calendar_ajx__loaded_data', function ( event, loaded_resource_id ){
 *
 *		if ( loaded_resource_id == select_dates_in_calendar_id ){
 *			wpbc_auto_select_dates_in_calendar( select_dates_in_calendar_id, '2024-05-15', '2024-05-25' );
 *		}
 *	} );
 *
 */


/**
 * Try to Auto select dates in specific calendar by simulated clicks in datepicker
 *
 * @param resource_id		1
 * @param check_in_ymd		'2024-05-09'		OR  	['2024-05-09','2024-05-19','2024-05-20']
 * @param check_out_ymd		'2024-05-15'		Optional
 *
 * @returns {number}		number of selected dates
 *
 * 	Example 1:				var num_selected_days = wpbc_auto_select_dates_in_calendar( 1, '2024-05-15', '2024-05-25' );
 * 	Example 2:				var num_selected_days = wpbc_auto_select_dates_in_calendar( 1, ['2024-05-09','2024-05-19','2024-05-20'] );
 */
function wpbc_auto_select_dates_in_calendar( resource_id, check_in_ymd, check_out_ymd = '' ){								// FixIn: 10.0.0.47.

	console.log( 'WPBC_AUTO_SELECT_DATES_IN_CALENDAR( RESOURCE_ID, CHECK_IN_YMD, CHECK_OUT_YMD )', resource_id, check_in_ymd, check_out_ymd );

	if (
		   ( '2100-01-01' == check_in_ymd )
		|| ( '2100-01-01' == check_out_ymd )
		|| ( ( '' == check_in_ymd ) && ( '' == check_out_ymd ) )
	){
		return 0;
	}

	// -----------------------------------------------------------------------------------------------------------------
	// If 	check_in_ymd  =  [ '2024-05-09','2024-05-19','2024-05-30' ]				ARRAY of DATES						// FixIn: 10.0.0.50.
	// -----------------------------------------------------------------------------------------------------------------
	var dates_to_select_arr = [];
	if ( Array.isArray( check_in_ymd ) ){
		dates_to_select_arr = wpbc_clone_obj( check_in_ymd );

		// -------------------------------------------------------------------------------------------------------------
		// Exceptions to  set  	MULTIPLE DAYS 	mode
		// -------------------------------------------------------------------------------------------------------------
		// if dates as NOT CONSECUTIVE: ['2024-05-09','2024-05-19','2024-05-30'], -> set MULTIPLE DAYS mode
		if (
			   ( dates_to_select_arr.length > 0 )
			&& ( '' == check_out_ymd )
			&& ( ! wpbc_dates__is_consecutive_dates_arr_range( dates_to_select_arr ) )
		){
			wpbc_cal_days_select__multiple( resource_id );
		}
		// if multiple days to select, but enabled SINGLE day mode, -> set MULTIPLE DAYS mode
		if (
			   ( dates_to_select_arr.length > 1 )
			&& ( '' == check_out_ymd )
			&& ( 'single' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ) )
		){
			wpbc_cal_days_select__multiple( resource_id );
		}
		// -------------------------------------------------------------------------------------------------------------
		check_in_ymd = dates_to_select_arr[ 0 ];
		if ( '' == check_out_ymd ){
			check_out_ymd = dates_to_select_arr[ (dates_to_select_arr.length-1) ];
		}
	}
	// -----------------------------------------------------------------------------------------------------------------


	if ( '' == check_in_ymd ){
		check_in_ymd = check_out_ymd;
	}
	if ( '' == check_out_ymd ){
		check_out_ymd = check_in_ymd;
	}

	if ( 'undefined' === typeof (resource_id) ){
		resource_id = '1';
	}


	var inst = wpbc_calendar__get_inst( resource_id );

	if ( null !== inst ){

		// Unselect all dates and set  properties of Datepick
		jQuery( '#date_booking' + resource_id ).val( '' );      														//FixIn: 5.4.3
		inst.stayOpen = false;
		inst.dates = [];
		var check_in_js = wpbc__get__js_date( check_in_ymd );
		var td_cell     = wpbc_get_clicked_td( inst.id, check_in_js );

		// Is ome type of error, then select multiple days selection  mode.
		if ( '' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ) ) {
 			_wpbc.calendar__set_param_value( resource_id, 'days_select_mode', 'multiple' );
		}


		// ---------------------------------------------------------------------------------------------------------
		//  == DYNAMIC ==
		if ( 'dynamic' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ) ){
			// 1-st click
			inst.stayOpen = false;
			jQuery.datepick._selectDay( td_cell, '#' + inst.id, check_in_js.getTime() );
			if ( 0 === inst.dates.length ){
				return 0;  								// First click  was unsuccessful, so we must not make other click
			}

			// 2-nd click
			var check_out_js = wpbc__get__js_date( check_out_ymd );
			var td_cell_out = wpbc_get_clicked_td( inst.id, check_out_js );
			inst.stayOpen = true;
			jQuery.datepick._selectDay( td_cell_out, '#' + inst.id, check_out_js.getTime() );
		}

		// ---------------------------------------------------------------------------------------------------------
		//  == FIXED ==
		if (  'fixed' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' )) {
			jQuery.datepick._selectDay( td_cell, '#' + inst.id, check_in_js.getTime() );
		}

		// ---------------------------------------------------------------------------------------------------------
		//  == SINGLE ==
		if ( 'single' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ) ){
			//jQuery.datepick._restrictMinMax( inst, jQuery.datepick._determineDate( inst, check_in_js, null ) );		// Do we need to run  this ? Please note, check_in_js must  have time,  min, sec defined to 0!
			jQuery.datepick._selectDay( td_cell, '#' + inst.id, check_in_js.getTime() );
		}

		// ---------------------------------------------------------------------------------------------------------
		//  == MULTIPLE ==
		if ( 'multiple' === _wpbc.calendar__get_param_value( resource_id, 'days_select_mode' ) ){

			var dates_arr;

			if ( dates_to_select_arr.length > 0 ){
				// Situation, when we have dates array: ['2024-05-09','2024-05-19','2024-05-30'].  and not the Check In / Check  out dates as parameter in this function
				dates_arr = wpbc_get_selection_dates_js_str_arr__from_arr( dates_to_select_arr );
			} else {
				dates_arr = wpbc_get_selection_dates_js_str_arr__from_check_in_out( check_in_ymd, check_out_ymd, inst );
			}

			if ( 0 === dates_arr.dates_js.length ){
				return 0;
			}

			// For Calendar Days selection
			for ( var j = 0; j < dates_arr.dates_js.length; j++ ){       // Loop array of dates

				var str_date = wpbc__get__sql_class_date( dates_arr.dates_js[ j ] );

				// Date unavailable !
				if ( 0 == _wpbc.bookings_in_calendar__get_for_date( resource_id, str_date ).day_availability ){
					return 0;
				}

				if ( dates_arr.dates_js[ j ] != -1 ) {
					inst.dates.push( dates_arr.dates_js[ j ] );
				}
			}

			var check_out_date = dates_arr.dates_js[ (dates_arr.dates_js.length - 1) ];

			inst.dates.push( check_out_date ); 			// Need add one additional SAME date for correct  works of dates selection !!!!!

			var checkout_timestamp = check_out_date.getTime();
			var td_cell = wpbc_get_clicked_td( inst.id, check_out_date );

			jQuery.datepick._selectDay( td_cell, '#' + inst.id, checkout_timestamp );
		}


		if ( 0 !== inst.dates.length ){
			// Scroll to specific month, if we set dates in some future months
			wpbc_calendar__scroll_to( resource_id, inst.dates[ 0 ].getFullYear(), inst.dates[ 0 ].getMonth()+1 );
		}

		return inst.dates.length;
	}

	return 0;
}

	/**
	 * Get HTML td element (where was click in calendar  day  cell)
	 *
	 * @param calendar_html_id			'calendar_booking1'
	 * @param date_js					JS Date
	 * @returns {*|jQuery}				Dom HTML td element
	 */
	function wpbc_get_clicked_td( calendar_html_id, date_js ){

	    var td_cell = jQuery( '#' + calendar_html_id + ' .sql_date_' + wpbc__get__sql_class_date( date_js ) ).get( 0 );

		return td_cell;
	}

	/**
	 * Get arrays of JS and SQL dates as dates array
	 *
	 * @param check_in_ymd							'2024-05-15'
	 * @param check_out_ymd							'2024-05-25'
	 * @param inst									Datepick Inst. Use wpbc_calendar__get_inst( resource_id );
	 * @returns {{dates_js: *[], dates_str: *[]}}
	 */
	function wpbc_get_selection_dates_js_str_arr__from_check_in_out( check_in_ymd, check_out_ymd , inst ){

		var original_array = [];
		var date;
		var bk_distinct_dates = [];

		var check_in_date = check_in_ymd.split( '-' );
		var check_out_date = check_out_ymd.split( '-' );

		date = new Date();
		date.setFullYear( check_in_date[ 0 ], (check_in_date[ 1 ] - 1), check_in_date[ 2 ] );                                    // year, month, date
		var original_check_in_date = date;
		original_array.push( jQuery.datepick._restrictMinMax( inst, jQuery.datepick._determineDate( inst, date, null ) ) ); //add date
		if ( ! wpbc_in_array( bk_distinct_dates, (check_in_date[ 2 ] + '.' + check_in_date[ 1 ] + '.' + check_in_date[ 0 ]) ) ){
			bk_distinct_dates.push( parseInt(check_in_date[ 2 ]) + '.' + parseInt(check_in_date[ 1 ]) + '.' + check_in_date[ 0 ] );
		}

		var date_out = new Date();
		date_out.setFullYear( check_out_date[ 0 ], (check_out_date[ 1 ] - 1), check_out_date[ 2 ] );                                    // year, month, date
		var original_check_out_date = date_out;

		var mewDate = new Date( original_check_in_date.getFullYear(), original_check_in_date.getMonth(), original_check_in_date.getDate() );
		mewDate.setDate( original_check_in_date.getDate() + 1 );

		while (
			(original_check_out_date > date) &&
			(original_check_in_date != original_check_out_date) ){
			date = new Date( mewDate.getFullYear(), mewDate.getMonth(), mewDate.getDate() );

			original_array.push( jQuery.datepick._restrictMinMax( inst, jQuery.datepick._determineDate( inst, date, null ) ) ); //add date
			if ( !wpbc_in_array( bk_distinct_dates, (date.getDate() + '.' + parseInt( date.getMonth() + 1 ) + '.' + date.getFullYear()) ) ){
				bk_distinct_dates.push( (parseInt(date.getDate()) + '.' + parseInt( date.getMonth() + 1 ) + '.' + date.getFullYear()) );
			}

			mewDate = new Date( date.getFullYear(), date.getMonth(), date.getDate() );
			mewDate.setDate( mewDate.getDate() + 1 );
		}
		original_array.pop();
		bk_distinct_dates.pop();

		return {'dates_js': original_array, 'dates_str': bk_distinct_dates};
	}

	/**
	 * Get arrays of JS and SQL dates as dates array
	 *
	 * @param dates_to_select_arr	= ['2024-05-09','2024-05-19','2024-05-30']
	 *
	 * @returns {{dates_js: *[], dates_str: *[]}}
	 */
	function wpbc_get_selection_dates_js_str_arr__from_arr( dates_to_select_arr ){										// FixIn: 10.0.0.50.

		var original_array    = [];
		var bk_distinct_dates = [];
		var one_date_str;

		for ( var d = 0; d < dates_to_select_arr.length; d++ ){

			original_array.push( wpbc__get__js_date( dates_to_select_arr[ d ] ) );

			one_date_str = dates_to_select_arr[ d ].split('-')
			if ( ! wpbc_in_array( bk_distinct_dates, (one_date_str[ 2 ] + '.' + one_date_str[ 1 ] + '.' + one_date_str[ 0 ]) ) ){
				bk_distinct_dates.push( parseInt(one_date_str[ 2 ]) + '.' + parseInt(one_date_str[ 1 ]) + '.' + one_date_str[ 0 ] );
			}
		}

		return {'dates_js': original_array, 'dates_str': original_array};
	}

// =====================================================================================================================
/*  ==  Auto Fill Fields / Auto Select Dates  ==
// ===================================================================================================================== */

jQuery( document ).ready( function (){

	var url_params = new URLSearchParams( window.location.search );

	// Disable days selection  in calendar,  after  redirection  from  the "Search results page,  after  search  availability" 			// FixIn: 8.8.2.3.
	if  ( 'On' != _wpbc.get_other_param( 'is_enabled_booking_search_results_days_select' ) ) {
		if (
			( url_params.has( 'wpbc_select_check_in' ) ) &&
			( url_params.has( 'wpbc_select_check_out' ) ) &&
			( url_params.has( 'wpbc_select_calendar_id' ) )
		){

			var select_dates_in_calendar_id = parseInt( url_params.get( 'wpbc_select_calendar_id' ) );

			// Fire on all booking dates loaded
			jQuery( 'body' ).on( 'wpbc_calendar_ajx__loaded_data', function ( event, loaded_resource_id ){

				if ( loaded_resource_id == select_dates_in_calendar_id ){
					wpbc_auto_select_dates_in_calendar( select_dates_in_calendar_id, url_params.get( 'wpbc_select_check_in' ), url_params.get( 'wpbc_select_check_out' ) );
				}
			} );
		}
	}

	if ( url_params.has( 'wpbc_auto_fill' ) ){

		var wpbc_auto_fill_value = url_params.get( 'wpbc_auto_fill' );

		// Convert back.     Some systems do not like symbol '~' in URL, so  we need to replace to  some other symbols
		wpbc_auto_fill_value = wpbc_auto_fill_value.replaceAll( '_^_', '~' );

		wpbc_auto_fill_booking_fields( wpbc_auto_fill_value );
	}

} );

/**
 * Autofill / select booking form  fields by  values from  the GET request  parameter: ?wpbc_auto_fill=
 *
 * @param auto_fill_str
 */
function wpbc_auto_fill_booking_fields( auto_fill_str ){																// FixIn: 10.0.0.48.

	if ( '' == auto_fill_str ){
		return;
	}

// console.log( 'WPBC_AUTO_FILL_BOOKING_FIELDS( AUTO_FILL_STR )', auto_fill_str);

	var fields_arr = wpbc_auto_fill_booking_fields__parse( auto_fill_str );

	for ( let i = 0; i < fields_arr.length; i++ ){
		jQuery( '[name="' + fields_arr[ i ][ 'name' ] + '"]' ).val( fields_arr[ i ][ 'value' ] );
	}
}

	/**
	 * Parse data from  get parameter:	?wpbc_auto_fill=visitors231^2~max_capacity231^2
	 *
	 * @param data_str      =   'visitors231^2~max_capacity231^2';
	 * @returns {*}
	 */
	function wpbc_auto_fill_booking_fields__parse( data_str ){

		var filter_options_arr = [];

		var data_arr = data_str.split( '~' );

		for ( var j = 0; j < data_arr.length; j++ ){

			var my_form_field = data_arr[ j ].split( '^' );

			var filter_name  = ('undefined' !== typeof (my_form_field[ 0 ])) ? my_form_field[ 0 ] : '';
			var filter_value = ('undefined' !== typeof (my_form_field[ 1 ])) ? my_form_field[ 1 ] : '';

			filter_options_arr.push(
										{
											'name'  : filter_name,
											'value' : filter_value
										}
								   );
		}
		return filter_options_arr;
	}

	/**
	 * Parse data from  get parameter:	?search_get__custom_params=...
	 *
	 * @param data_str      =   'text^search_field__display_check_in^23.05.2024~text^search_field__display_check_out^26.05.2024~selectbox-one^search_quantity^2~selectbox-one^location^Spain~selectbox-one^max_capacity^2~selectbox-one^amenity^parking~checkbox^search_field__extend_search_days^5~submit^^Search~hidden^search_get__check_in_ymd^2024-05-23~hidden^search_get__check_out_ymd^2024-05-26~hidden^search_get__time^~hidden^search_get__quantity^2~hidden^search_get__extend^5~hidden^search_get__users_id^~hidden^search_get__custom_params^~';
	 * @returns {*}
	 */
	function wpbc_auto_fill_search_fields__parse( data_str ){

		var filter_options_arr = [];

		var data_arr = data_str.split( '~' );

		for ( var j = 0; j < data_arr.length; j++ ){

			var my_form_field = data_arr[ j ].split( '^' );

			var filter_type  = ('undefined' !== typeof (my_form_field[ 0 ])) ? my_form_field[ 0 ] : '';
			var filter_name  = ('undefined' !== typeof (my_form_field[ 1 ])) ? my_form_field[ 1 ] : '';
			var filter_value = ('undefined' !== typeof (my_form_field[ 2 ])) ? my_form_field[ 2 ] : '';

			filter_options_arr.push(
										{
											'type'  : filter_type,
											'name'  : filter_name,
											'value' : filter_value
										}
								   );
		}
		return filter_options_arr;
	}


// ---------------------------------------------------------------------------------------------------------------------
/*  ==  Auto Update number of months in calendars ON screen size changed  ==
// --------------------------------------------------------------------------------------------------------------------- */

/**
 * Auto Update Number of Months in Calendar, e.g.:  		if    ( WINDOW_WIDTH <= 782px )   >>> 	MONTHS_NUMBER = 1
 *   ELSE:  number of months defined in shortcode.
 * @param resource_id int
 *
 */
function wpbc_calendar__auto_update_months_number__on_resize( resource_id ){

	if ( true === _wpbc.get_other_param( 'is_allow_several_months_on_mobile' ) ) {
		return false;
	}

	var local__number_of_months = parseInt( _wpbc.calendar__get_param_value( resource_id, 'calendar_number_of_months' ) );

	if ( local__number_of_months > 1 ){

		if ( jQuery( window ).width() <= 782 ){
			wpbc_calendar__update_months_number( resource_id, 1 );
		} else {
			wpbc_calendar__update_months_number( resource_id, local__number_of_months );
		}

	}
}

/**
 * Auto Update Number of Months in   ALL   Calendars
 *
 */
function wpbc_calendars__auto_update_months_number(){

	var all_calendars_arr = _wpbc.calendars_all__get();

	// This LOOP "for in" is GOOD, because we check  here keys    'calendar_' === calendar_id.slice( 0, 9 )
	for ( var calendar_id in all_calendars_arr ){
		if ( 'calendar_' === calendar_id.slice( 0, 9 ) ){
			var resource_id = parseInt( calendar_id.slice( 9 ) );			//  'calendar_3' -> 3
			if ( resource_id > 0 ){
				wpbc_calendar__auto_update_months_number__on_resize( resource_id );
			}
		}
	}
}

/**
 * If browser window changed,  then  update number of months.
 */
jQuery( window ).on( 'resize', function (){
	wpbc_calendars__auto_update_months_number();
} );

/**
 * Auto update calendar number of months on initial page load
 */
jQuery( document ).ready( function (){
	var closed_timer = setTimeout( function (){
		wpbc_calendars__auto_update_months_number();
	}, 100 );
});

// ---------------------------------------------------------------------------------------------------------------------
/*  ==  Check: calendar_dates_start: "2026-01-01", calendar_dates_end: "2026-12-31" ==  // FixIn: 10.13.1.4.
// --------------------------------------------------------------------------------------------------------------------- */
	/**
	 * Get Start JS Date of starting dates in calendar, from the _wpbc object.
	 *
	 * @param integer resource_id - resource ID, e.g.: 1.
	 */
	function wpbc_calendar__get_dates_start( resource_id ) {
		return wpbc_calendar__get_date_parameter( resource_id, 'calendar_dates_start' );
	}

	/**
	 * Get End JS Date of ending dates in calendar, from the _wpbc object.
	 *
	 * @param integer resource_id - resource ID, e.g.: 1.
	 */
	function wpbc_calendar__get_dates_end(resource_id) {
		return wpbc_calendar__get_date_parameter( resource_id, 'calendar_dates_end' );
	}

/**
 * Get validates date parameter.
 *
 * @param resource_id   - 1
 * @param parameter_str - 'calendar_dates_start' | 'calendar_dates_end' | ...
 */
function wpbc_calendar__get_date_parameter(resource_id, parameter_str) {

	var date_expected_ymd = _wpbc.calendar__get_param_value( resource_id, parameter_str );

	if ( ! date_expected_ymd ) {
		return false;             // '' | 0 | null | undefined  -> false.
	}

	if ( -1 !== date_expected_ymd.indexOf( '-' ) ) {

		var date_expected_ymd_arr = date_expected_ymd.split( '-' );	// '2025-07-26' -> ['2025', '07', '26']

		if ( date_expected_ymd_arr.length > 0 ) {
			var year  = (date_expected_ymd_arr.length > 0) ? parseInt( date_expected_ymd_arr[0] ) : new Date().getFullYear();	// Year.
			var month = (date_expected_ymd_arr.length > 1) ? (parseInt( date_expected_ymd_arr[1] ) - 1) : 0;  // (month - 1) or 0 - Jan.
			var day   = (date_expected_ymd_arr.length > 2) ? parseInt( date_expected_ymd_arr[2] ) : 1;  // date or Otherwise 1st of month

			var date_js = new Date( year, month, day, 0, 0, 0, 0 );

			return date_js;
		}
	}

	return false;  // Fallback,  if we not parsed this parameter  'calendar_dates_start' = '2025-07-26',  for example because of 'calendar_dates_start' = 'sfsdf'.
}
/**
 * ====================================================================================================================
 *	includes/__js/cal/days_select_custom.js
 * ====================================================================================================================
 */

// FixIn: 9.8.9.2.

/**
 * Re-Init Calendar and Re-Render it.
 *
 * @param resource_id
 */
function wpbc_cal__re_init( resource_id ){

	// Remove CLASS  for ability to re-render and reinit calendar.
	jQuery( '#calendar_booking' + resource_id ).removeClass( 'hasDatepick' );
	wpbc_calendar_show( resource_id );
}


/**
 * Re-Init previously  saved days selection  variables.
 *
 * @param resource_id
 */
function wpbc_cal_days_select__re_init( resource_id ){

	_wpbc.calendar__set_param_value( resource_id, 'saved_variable___days_select_initial'
		, {
			'dynamic__days_min'        : _wpbc.calendar__get_param_value( resource_id, 'dynamic__days_min' ),
			'dynamic__days_max'        : _wpbc.calendar__get_param_value( resource_id, 'dynamic__days_max' ),
			'dynamic__days_specific'   : _wpbc.calendar__get_param_value( resource_id, 'dynamic__days_specific' ),
			'dynamic__week_days__start': _wpbc.calendar__get_param_value( resource_id, 'dynamic__week_days__start' ),
			'fixed__days_num'          : _wpbc.calendar__get_param_value( resource_id, 'fixed__days_num' ),
			'fixed__week_days__start'  : _wpbc.calendar__get_param_value( resource_id, 'fixed__week_days__start' )
		}
	);
}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Set Single Day selection - after page load
 *
 * @param resource_id		ID of booking resource
 */
function wpbc_cal_ready_days_select__single( resource_id ){

	// Re-define selection, only after page loaded with all init vars
	jQuery(document).ready(function(){

		// Wait 1 second, just to  be sure, that all init vars defined
		setTimeout(function(){

			wpbc_cal_days_select__single( resource_id );

		}, 1000);
	});
}

/**
 * Set Single Day selection
 * Can be run at any  time,  when  calendar defined - useful for console run.
 *
 * @param resource_id		ID of booking resource
 */
function wpbc_cal_days_select__single( resource_id ){

	_wpbc.calendar__set_parameters( resource_id, {'days_select_mode': 'single'} );

	wpbc_cal_days_select__re_init( resource_id );
	wpbc_cal__re_init( resource_id );
}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Set Multiple Days selection  - after page load
 *
 * @param resource_id		ID of booking resource
 */
function wpbc_cal_ready_days_select__multiple( resource_id ){

	// Re-define selection, only after page loaded with all init vars
	jQuery(document).ready(function(){

		// Wait 1 second, just to  be sure, that all init vars defined
		setTimeout(function(){

			wpbc_cal_days_select__multiple( resource_id );

		}, 1000);
	});
}


/**
 * Set Multiple Days selection
 * Can be run at any  time,  when  calendar defined - useful for console run.
 *
 * @param resource_id		ID of booking resource
 */
function wpbc_cal_days_select__multiple( resource_id ){

	_wpbc.calendar__set_parameters( resource_id, {'days_select_mode': 'multiple'} );

	wpbc_cal_days_select__re_init( resource_id );
	wpbc_cal__re_init( resource_id );
}


// ---------------------------------------------------------------------------------------------------------------------

/**
 * Set Fixed Days selection with  1 mouse click  - after page load
 *
 * @integer resource_id			- 1				   -- ID of booking resource (calendar) -
 * @integer days_number			- 3				   -- number of days to  select	-
 * @array week_days__start	- [-1] | [ 1, 5]   --  { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat }
 */
function wpbc_cal_ready_days_select__fixed( resource_id, days_number, week_days__start = [-1] ){

	// Re-define selection, only after page loaded with all init vars
	jQuery(document).ready(function(){

		// Wait 1 second, just to  be sure, that all init vars defined
		setTimeout(function(){

			wpbc_cal_days_select__fixed( resource_id, days_number, week_days__start );

		}, 1000);
	});
}


/**
 * Set Fixed Days selection with  1 mouse click
 * Can be run at any  time,  when  calendar defined - useful for console run.
 *
 * @integer resource_id			- 1				   -- ID of booking resource (calendar) -
 * @integer days_number			- 3				   -- number of days to  select	-
 * @array week_days__start	- [-1] | [ 1, 5]   --  { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat }
 */
function wpbc_cal_days_select__fixed( resource_id, days_number, week_days__start = [-1] ){

	_wpbc.calendar__set_parameters( resource_id, {'days_select_mode': 'fixed'} );

	_wpbc.calendar__set_parameters( resource_id, {'fixed__days_num': parseInt( days_number )} );			// Number of days selection with 1 mouse click
	_wpbc.calendar__set_parameters( resource_id, {'fixed__week_days__start': week_days__start} ); 	// { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat }

	wpbc_cal_days_select__re_init( resource_id );
	wpbc_cal__re_init( resource_id );
}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Set Range Days selection  with  2 mouse clicks  - after page load
 *
 * @integer resource_id			- 1				   		-- ID of booking resource (calendar)
 * @integer days_min			- 7				   		-- Min number of days to select
 * @integer days_max			- 30			   		-- Max number of days to select
 * @array days_specific			- [] | [7,14,21,28]		-- Restriction for Specific number of days selection
 * @array week_days__start		- [-1] | [ 1, 5]   		--  { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat }
 */
function wpbc_cal_ready_days_select__range( resource_id, days_min, days_max, days_specific = [], week_days__start = [-1] ){

	// Re-define selection, only after page loaded with all init vars
	jQuery(document).ready(function(){

		// Wait 1 second, just to  be sure, that all init vars defined
		setTimeout(function(){

			wpbc_cal_days_select__range( resource_id, days_min, days_max, days_specific, week_days__start );
		}, 1000);
	});
}

/**
 * Set Range Days selection  with  2 mouse clicks
 * Can be run at any  time,  when  calendar defined - useful for console run.
 *
 * @integer resource_id			- 1				   		-- ID of booking resource (calendar)
 * @integer days_min			- 7				   		-- Min number of days to select
 * @integer days_max			- 30			   		-- Max number of days to select
 * @array days_specific			- [] | [7,14,21,28]		-- Restriction for Specific number of days selection
 * @array week_days__start		- [-1] | [ 1, 5]   		--  { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat }
 */
function wpbc_cal_days_select__range( resource_id, days_min, days_max, days_specific = [], week_days__start = [-1] ){

	_wpbc.calendar__set_parameters(  resource_id, {'days_select_mode': 'dynamic'}  );
	_wpbc.calendar__set_param_value( resource_id, 'dynamic__days_min'         , parseInt( days_min )  );           		// Min. Number of days selection with 2 mouse clicks
	_wpbc.calendar__set_param_value( resource_id, 'dynamic__days_max'         , parseInt( days_max )  );          		// Max. Number of days selection with 2 mouse clicks
	_wpbc.calendar__set_param_value( resource_id, 'dynamic__days_specific'    , days_specific  );	      				// Example [5,7]
	_wpbc.calendar__set_param_value( resource_id, 'dynamic__week_days__start' , week_days__start  );  					// { -1 - Any | 0 - Su,  1 - Mo,  2 - Tu, 3 - We, 4 - Th, 5 - Fr, 6 - Sat }

	wpbc_cal_days_select__re_init( resource_id );
	wpbc_cal__re_init( resource_id );
}

/**
 * ====================================================================================================================
 *	includes/__js/cal_ajx_load/wpbc_cal_ajx.js
 * ====================================================================================================================
 */

// ---------------------------------------------------------------------------------------------------------------------
//  A j a x    L o a d    C a l e n d a r    D a t a
// ---------------------------------------------------------------------------------------------------------------------

function wpbc_calendar__load_data__ajx( params ){

	// FixIn: 9.8.6.2.
	wpbc_calendar__loading__start( params['resource_id'] );

	// Trigger event for calendar before loading Booking data,  but after showing Calendar.
	if ( jQuery( '#calendar_booking' + params['resource_id'] ).length > 0 ){
		var target_elm = jQuery( 'body' ).trigger( "wpbc_calendar_ajx__before_loaded_data", [params['resource_id']] );
		 //jQuery( 'body' ).on( 'wpbc_calendar_ajx__before_loaded_data', function( event, resource_id ) { ... } );
	}

	if ( wpbc_balancer__is_wait( params , 'wpbc_calendar__load_data__ajx' ) ){
		return false;
	}

	// FixIn: 9.8.6.2.
	wpbc_calendar__blur__stop( params['resource_id'] );

	// -----------------------------------------------------------------------------------------------------------------
	// == Get start / end dates from  the Booking Calendar shortcode. ==
	// Example: [booking calendar_dates_start='2026-01-01' calendar_dates_end='2026-12-31'  resource_id=1]              // FixIn: 10.13.1.4.
	// -----------------------------------------------------------------------------------------------------------------
	if ( false !== wpbc_calendar__get_dates_start( params['resource_id'] ) ) {
		if ( ! params['dates_to_check'] ) { params['dates_to_check'] = []; }
		var dates_start = wpbc_calendar__get_dates_start( params['resource_id'] );  // E.g. - local__min_date = new Date( 2025, 0, 1 );
		if ( false !== dates_start ){
			params['dates_to_check'][0] = wpbc__get__sql_class_date( dates_start );
		}
	}
	if ( false !== wpbc_calendar__get_dates_end( params['resource_id'] ) ) {
		if ( !params['dates_to_check'] ) { params['dates_to_check'] = []; }
		var dates_end = wpbc_calendar__get_dates_end( params['resource_id'] );  // E.g. - local__min_date = new Date( 2025, 0, 1 );
		if ( false !== dates_end ) {
			params['dates_to_check'][1] = wpbc__get__sql_class_date( dates_end );
			if ( !params['dates_to_check'][0] ) {
				params['dates_to_check'][0] = wpbc__get__sql_class_date( new Date() );
			}
		}
	}
	// -----------------------------------------------------------------------------------------------------------------

// console.groupEnd(); console.time('resource_id_' + params['resource_id']);
console.groupCollapsed( 'WPBC_AJX_CALENDAR_LOAD' ); console.log( ' == Before Ajax Send - calendars_all__get() == ' , _wpbc.calendars_all__get() );
	if ( 'function' === typeof (wpbc_hook__init_timeselector) ) {
		wpbc_hook__init_timeselector();
	}

	// Start Ajax
	jQuery.post( wpbc_url_ajax,
				{
					action          : 'WPBC_AJX_CALENDAR_LOAD',
					wpbc_ajx_user_id: _wpbc.get_secure_param( 'user_id' ),
					nonce           : _wpbc.get_secure_param( 'nonce' ),
					wpbc_ajx_locale : _wpbc.get_secure_param( 'locale' ),

					calendar_request_params : params 						// Usually like: { 'resource_id': 1, 'max_days_count': 365 }
				},

				/**
				 * S u c c e s s
				 *
				 * @param response_data		-	its object returned from  Ajax - class-live-search.php
				 * @param textStatus		-	'success'
				 * @param jqXHR				-	Object
				 */
				function ( response_data, textStatus, jqXHR ) {
// console.timeEnd('resource_id_' + response_data['resource_id']);
console.log( ' == Response WPBC_AJX_CALENDAR_LOAD == ', response_data ); console.groupEnd();

					// FixIn: 9.8.6.2.
					var ajx_post_data__resource_id = wpbc_get_resource_id__from_ajx_post_data_url( this.data );
					wpbc_balancer__completed( ajx_post_data__resource_id , 'wpbc_calendar__load_data__ajx' );

					// Probably Error
					if ( (typeof response_data !== 'object') || (response_data === null) ){

						var jq_node  = wpbc_get_calendar__jq_node__for_messages( this.data );
						var message_type = 'info';

						if ( '' === response_data ){
							response_data = 'The server responds with an empty string. The server probably stopped working unexpectedly. <br>Please check your <strong>error.log</strong> in your server configuration for relative errors.';
							message_type = 'warning';
						}

						// Show Message
						wpbc_front_end__show_message( response_data , { 'type'     : message_type,
																		'show_here': {'jq_node': jq_node, 'where': 'after'},
																		'is_append': true,
																		'style'    : 'text-align:left;',
																		'delay'    : 0
																	} );
						return;
					}

					// Show Calendar
					wpbc_calendar__loading__stop( response_data[ 'resource_id' ] );

					// -------------------------------------------------------------------------------------------------
					// Bookings - Dates
					_wpbc.bookings_in_calendar__set_dates(  response_data[ 'resource_id' ], response_data[ 'ajx_data' ]['dates']  );

					// Bookings - Child or only single booking resource in dates
					_wpbc.booking__set_param_value( response_data[ 'resource_id' ], 'resources_id_arr__in_dates', response_data[ 'ajx_data' ][ 'resources_id_arr__in_dates' ] );

					// Aggregate booking resources,  if any ?
					_wpbc.booking__set_param_value( response_data[ 'resource_id' ], 'aggregate_resource_id_arr', response_data[ 'ajx_data' ][ 'aggregate_resource_id_arr' ] );
					// -------------------------------------------------------------------------------------------------

					// Update calendar
					wpbc_calendar__update_look( response_data[ 'resource_id' ] );

					if ( 'function' === typeof (wpbc_hook__init_timeselector) ) {
						wpbc_hook__init_timeselector();
					}

					if (
							( 'undefined' !== typeof (response_data[ 'ajx_data' ][ 'ajx_after_action_message' ]) )
						 && ( '' != response_data[ 'ajx_data' ][ 'ajx_after_action_message' ].replace( /\n/g, "<br />" ) )
					){

						var jq_node  = wpbc_get_calendar__jq_node__for_messages( this.data );

						// Show Message
						wpbc_front_end__show_message( response_data[ 'ajx_data' ][ 'ajx_after_action_message' ].replace( /\n/g, "<br />" ),
														{   'type'     : ( 'undefined' !== typeof( response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ] ) )
																		  ? response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ] : 'info',
															'show_here': {'jq_node': jq_node, 'where': 'after'},
															'is_append': true,
															'style'    : 'text-align:left;',
															'delay'    : 10000
														} );
					}

					if ( 'function' === typeof (wpbc_update_capacity_hint) ) {
						wpbc_update_capacity_hint( response_data['resource_id'] );
					}

					// Trigger event that calendar has been		 // FixIn: 10.0.0.44.
					if ( jQuery( '#calendar_booking' + response_data[ 'resource_id' ] ).length > 0 ){
						var target_elm = jQuery( 'body' ).trigger( "wpbc_calendar_ajx__loaded_data", [response_data[ 'resource_id' ]] );
						 //jQuery( 'body' ).on( 'wpbc_calendar_ajx__loaded_data', function( event, resource_id ) { ... } );
					}

					//jQuery( '#ajax_respond' ).html( response_data );		// For ability to show response, add such DIV element to page
				}
			  ).fail( function ( jqXHR, textStatus, errorThrown ) {    if ( window.console && window.console.log ){ console.log( 'Ajax_Error', jqXHR, textStatus, errorThrown ); }

					var ajx_post_data__resource_id = wpbc_get_resource_id__from_ajx_post_data_url( this.data );
					wpbc_balancer__completed( ajx_post_data__resource_id , 'wpbc_calendar__load_data__ajx' );

					// Get Content of Error Message
					var error_message = '<strong>' + 'Error!' + '</strong> ' + errorThrown ;
					if ( jqXHR.status ){
						error_message += ' (<b>' + jqXHR.status + '</b>)';
						if (403 == jqXHR.status ){
							error_message += '<br> Probably nonce for this page has been expired. Please <a href="javascript:void(0)" onclick="javascript:location.reload();">reload the page</a>.';
							error_message += '<br> Otherwise, please check this <a style="font-weight: 600;" href="https://wpbookingcalendar.com/faq/request-do-not-pass-security-check/?after_update=10.1.1">troubleshooting instruction</a>.<br>'
						}
					}
					var message_show_delay = 3000;
					if ( jqXHR.responseText ){
						error_message += ' ' + jqXHR.responseText;
						message_show_delay = 10;
					}
					error_message = error_message.replace( /\n/g, "<br />" );

					var jq_node  = wpbc_get_calendar__jq_node__for_messages( this.data );

					/**
					 * If we make fast clicking on different pages,
					 * then under calendar will show error message with  empty  text, because ajax was not received.
					 * To  not show such warnings we are set delay  in 3 seconds.  var message_show_delay = 3000;
					 */
					var closed_timer = setTimeout( function (){

																// Show Message
																wpbc_front_end__show_message( error_message , { 'type'     : 'error',
																												'show_here': {'jq_node': jq_node, 'where': 'after'},
																												'is_append': true,
																												'style'    : 'text-align:left;',
																												'css_class':'wpbc_fe_message_alt',
																												'delay'    : 0
																											} );
														   } ,
														   parseInt( message_show_delay )   );

			  })
	          // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
			  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
			  ;  // End Ajax
}



// ---------------------------------------------------------------------------------------------------------------------
// Support
// ---------------------------------------------------------------------------------------------------------------------

	/**
	 * Get Calendar jQuery node for showing messages during Ajax
	 * This parameter:   calendar_request_params[resource_id]   parsed from this.data Ajax post  data
	 *
	 * @param ajx_post_data_url_params		 'action=WPBC_AJX_CALENDAR_LOAD...&calendar_request_params%5Bresource_id%5D=2&calendar_request_params%5Bbooking_hash%5D=&calendar_request_params'
	 * @returns {string}	''#calendar_booking1'  |   '.booking_form_div' ...
	 *
	 * Example    var jq_node  = wpbc_get_calendar__jq_node__for_messages( this.data );
	 */
	function wpbc_get_calendar__jq_node__for_messages( ajx_post_data_url_params ){

		var jq_node = '.booking_form_div';

		var calendar_resource_id = wpbc_get_resource_id__from_ajx_post_data_url( ajx_post_data_url_params );

		if ( calendar_resource_id > 0 ){
			jq_node = '#calendar_booking' + calendar_resource_id;
		}

		return jq_node;
	}


	/**
	 * Get resource ID from ajx post data url   usually  from  this.data  = 'action=WPBC_AJX_CALENDAR_LOAD...&calendar_request_params%5Bresource_id%5D=2&calendar_request_params%5Bbooking_hash%5D=&calendar_request_params'
	 *
	 * @param ajx_post_data_url_params		 'action=WPBC_AJX_CALENDAR_LOAD...&calendar_request_params%5Bresource_id%5D=2&calendar_request_params%5Bbooking_hash%5D=&calendar_request_params'
	 * @returns {int}						 1 | 0  (if errror then  0)
	 *
	 * Example    var jq_node  = wpbc_get_calendar__jq_node__for_messages( this.data );
	 */
	function wpbc_get_resource_id__from_ajx_post_data_url( ajx_post_data_url_params ){

		// Get booking resource ID from Ajax Post Request  -> this.data = 'action=WPBC_AJX_CALENDAR_LOAD...&calendar_request_params%5Bresource_id%5D=2&calendar_request_params%5Bbooking_hash%5D=&calendar_request_params'
		var calendar_resource_id = wpbc_get_uri_param_by_name( 'calendar_request_params[resource_id]', ajx_post_data_url_params );
		if ( (null !== calendar_resource_id) && ('' !== calendar_resource_id) ){
			calendar_resource_id = parseInt( calendar_resource_id );
			if ( calendar_resource_id > 0 ){
				return calendar_resource_id;
			}
		}
		return 0;
	}


	/**
	 * Get parameter from URL  -  parse URL parameters,  like this: action=WPBC_AJX_CALENDAR_LOAD...&calendar_request_params%5Bresource_id%5D=2&calendar_request_params%5Bbooking_hash%5D=&calendar_request_params
	 * @param name  parameter  name,  like 'calendar_request_params[resource_id]'
	 * @param url	'parameter  string URL'
	 * @returns {string|null}   parameter value
	 *
	 * Example: 		wpbc_get_uri_param_by_name( 'calendar_request_params[resource_id]', this.data );  -> '2'
	 */
	function wpbc_get_uri_param_by_name( name, url ){

		url = decodeURIComponent( url );

		name = name.replace( /[\[\]]/g, '\\$&' );
		var regex = new RegExp( '[?&]' + name + '(=([^&#]*)|&|#|$)' ),
			results = regex.exec( url );
		if ( !results ) return null;
		if ( !results[ 2 ] ) return '';
		return decodeURIComponent( results[ 2 ].replace( /\+/g, ' ' ) );
	}

/**
 * =====================================================================================================================
 *	includes/__js/front_end_messages/wpbc_fe_messages.js
 * =====================================================================================================================
 */

// ---------------------------------------------------------------------------------------------------------------------
// Show Messages at Front-Edn side
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show message in content
 *
 * @param message				Message HTML
 * @param params = {
 *								'type'     : 'warning',							// 'error' | 'warning' | 'info' | 'success'
 *								'show_here' : {
 *													'jq_node' : '',				// any jQuery node definition
 *													'where'   : 'inside'		// 'inside' | 'before' | 'after' | 'right' | 'left'
 *											  },
 *								'is_append': true,								// Apply  only if 	'where'   : 'inside'
 *								'style'    : 'text-align:left;',				// styles, if needed
 *							    'css_class': '',								// For example can  be: 'wpbc_fe_message_alt'
 *								'delay'    : 0,									// how many microsecond to  show,  if 0  then  show forever
 *								'if_visible_not_show': false					// if true,  then do not show message,  if previos message was not hided (not apply if 'where'   : 'inside' )
 *				};
 * Examples:
 * 			var html_id = wpbc_front_end__show_message( 'You can test days selection in calendar', {} );
 *
 *			var notice_message_id = wpbc_front_end__show_message( _wpbc.get_message( 'message_check_required' ), { 'type': 'warning', 'delay': 10000, 'if_visible_not_show': true,
 *																  'show_here': {'where': 'right', 'jq_node': el,} } );
 *
 *			wpbc_front_end__show_message( response_data[ 'ajx_data' ][ 'ajx_after_action_message' ].replace( /\n/g, "<br />" ),
 *											{   'type'     : ( 'undefined' !== typeof( response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ] ) )
 *															  ? response_data[ 'ajx_data' ][ 'ajx_after_action_message_status' ] : 'info',
 *												'show_here': {'jq_node': jq_node, 'where': 'after'},
 *												'css_class':'wpbc_fe_message_alt',
 *												'delay'    : 10000
 *											} );
 *
 *
 * @returns string  - HTML ID		or 0 if not showing during this time.
 */
function wpbc_front_end__show_message( message, params = {} ){

	var params_default = {
								'type'     : 'warning',							// 'error' | 'warning' | 'info' | 'success'
								'show_here' : {
													'jq_node' : '',				// any jQuery node definition
													'where'   : 'inside'		// 'inside' | 'before' | 'after' | 'right' | 'left'
											  },
								'is_append': true,								// Apply  only if 	'where'   : 'inside'
								'style'    : 'text-align:left;',				// styles, if needed
							    'css_class': '',								// For example can  be: 'wpbc_fe_message_alt'
								'delay'    : 0,									// how many microsecond to  show,  if 0  then  show forever
								'if_visible_not_show': false,					// if true,  then do not show message,  if previos message was not hided (not apply if 'where'   : 'inside' )
								'is_scroll': true								// is scroll  to  this element
						};
	for ( var p_key in params ){
		params_default[ p_key ] = params[ p_key ];
	}
	params = params_default;

    var unique_div_id = new Date();
    unique_div_id = 'wpbc_notice_' + unique_div_id.getTime();

	params['css_class'] += ' wpbc_fe_message';
	if ( params['type'] == 'error' ){
		params['css_class'] += ' wpbc_fe_message_error';
		message = '<i class="menu_icon icon-1x wpbc_icn_report_gmailerrorred"></i>' + message;
	}
	if ( params['type'] == 'warning' ){
		params['css_class'] += ' wpbc_fe_message_warning';
		message = '<i class="menu_icon icon-1x wpbc_icn_warning"></i>' + message;
	}
	if ( params['type'] == 'info' ){
		params['css_class'] += ' wpbc_fe_message_info';
	}
	if ( params['type'] == 'success' ){
		params['css_class'] += ' wpbc_fe_message_success';
		message = '<i class="menu_icon icon-1x wpbc_icn_done_outline"></i>' + message;
	}

	var scroll_to_element = '<div id="' + unique_div_id + '_scroll" style="display:none;"></div>';
	message = '<div id="' + unique_div_id + '" class="wpbc_front_end__message ' + params['css_class'] + '" style="' + params[ 'style' ] + '">' + message + '</div>';


	var jq_el_message = false;
	var is_show_message = true;

	if ( 'inside' === params[ 'show_here' ][ 'where' ] ){

		if ( params[ 'is_append' ] ){
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).append( scroll_to_element );
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).append( message );
		} else {
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).html( scroll_to_element + message );
		}

	} else if ( 'before' === params[ 'show_here' ][ 'where' ] ){

		jq_el_message = jQuery( params[ 'show_here' ][ 'jq_node' ] ).siblings( '[id^="wpbc_notice_"]' );
		if ( (params[ 'if_visible_not_show' ]) && (jq_el_message.is( ':visible' )) ){
			is_show_message = false;
			unique_div_id = jQuery( jq_el_message.get( 0 ) ).attr( 'id' );
		}
		if ( is_show_message ){
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).before( scroll_to_element );
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).before( message );
		}

	} else if ( 'after' === params[ 'show_here' ][ 'where' ] ){

		jq_el_message = jQuery( params[ 'show_here' ][ 'jq_node' ] ).nextAll( '[id^="wpbc_notice_"]' );
		if ( (params[ 'if_visible_not_show' ]) && (jq_el_message.is( ':visible' )) ){
			is_show_message = false;
			unique_div_id = jQuery( jq_el_message.get( 0 ) ).attr( 'id' );
		}
		if ( is_show_message ){
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).before( scroll_to_element );		// We need to  set  here before(for handy scroll)
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).after( message );
		}

	} else if ( 'right' === params[ 'show_here' ][ 'where' ] ){

		jq_el_message = jQuery( params[ 'show_here' ][ 'jq_node' ] ).nextAll( '.wpbc_front_end__message_container_right' ).find( '[id^="wpbc_notice_"]' );
		if ( (params[ 'if_visible_not_show' ]) && (jq_el_message.is( ':visible' )) ){
			is_show_message = false;
			unique_div_id = jQuery( jq_el_message.get( 0 ) ).attr( 'id' );
		}
		if ( is_show_message ){
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).before( scroll_to_element );		// We need to  set  here before(for handy scroll)
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).after( '<div class="wpbc_front_end__message_container_right">' + message + '</div>' );
		}
	} else if ( 'left' === params[ 'show_here' ][ 'where' ] ){

		jq_el_message = jQuery( params[ 'show_here' ][ 'jq_node' ] ).siblings( '.wpbc_front_end__message_container_left' ).find( '[id^="wpbc_notice_"]' );
		if ( (params[ 'if_visible_not_show' ]) && (jq_el_message.is( ':visible' )) ){
			is_show_message = false;
			unique_div_id = jQuery( jq_el_message.get( 0 ) ).attr( 'id' );
		}
		if ( is_show_message ){
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).before( scroll_to_element );		// We need to  set  here before(for handy scroll)
			jQuery( params[ 'show_here' ][ 'jq_node' ] ).before( '<div class="wpbc_front_end__message_container_left">' + message + '</div>' );
		}
	}

	if (   ( is_show_message )  &&  ( parseInt( params[ 'delay' ] ) > 0 )   ){
		var closed_timer = setTimeout( function (){
													jQuery( '#' + unique_div_id ).fadeOut( 1500 );
										} , parseInt( params[ 'delay' ] )   );

		var closed_timer2 = setTimeout( function (){
														jQuery( '#' + unique_div_id ).trigger( 'hide' );
										}, ( parseInt( params[ 'delay' ] ) + 1501 ) );
	}

	// Check  if showed message in some hidden parent section and show it. But it must  be lower than '.wpbc_container'
	var parent_els = jQuery( '#' + unique_div_id ).parents().map( function (){
		if ( (!jQuery( this ).is( 'visible' )) && (jQuery( '.wpbc_container' ).has( this )) ){
			jQuery( this ).show();
		}
	} );

	if ( params[ 'is_scroll' ] ){
		wpbc_do_scroll( '#' + unique_div_id + '_scroll' );
	}

	return unique_div_id;
}


	/**
	 * Error message. 	Preset of parameters for real message function.
	 *
	 * @param el		- any jQuery node definition
	 * @param message	- Message HTML
	 * @returns string  - HTML ID		or 0 if not showing during this time.
	 */
	function wpbc_front_end__show_message__error( jq_node, message ){

		var notice_message_id = wpbc_front_end__show_message(
																message,
																{
																	'type'               : 'error',
																	'delay'              : 10000,
																	'if_visible_not_show': true,
																	'show_here'          : {
																							'where'  : 'right',
																							'jq_node': jq_node
																						   }
																}
														);
		return notice_message_id;
	}


	/**
	 * Error message UNDER element. 	Preset of parameters for real message function.
	 *
	 * @param el		- any jQuery node definition
	 * @param message	- Message HTML
	 * @returns string  - HTML ID		or 0 if not showing during this time.
	 */
	function wpbc_front_end__show_message__error_under_element( jq_node, message, message_delay ){

		if ( 'undefined' === typeof (message_delay) ){
			message_delay = 0
		}

		var notice_message_id = wpbc_front_end__show_message(
																message,
																{
																	'type'               : 'error',
																	'delay'              : message_delay,
																	'if_visible_not_show': true,
																	'show_here'          : {
																							'where'  : 'after',
																							'jq_node': jq_node
																						   }
																}
														);
		return notice_message_id;
	}


	/**
	 * Error message UNDER element. 	Preset of parameters for real message function.
	 *
	 * @param el		- any jQuery node definition
	 * @param message	- Message HTML
	 * @returns string  - HTML ID		or 0 if not showing during this time.
	 */
	function wpbc_front_end__show_message__error_above_element( jq_node, message, message_delay ){

		if ( 'undefined' === typeof (message_delay) ){
			message_delay = 10000
		}

		var notice_message_id = wpbc_front_end__show_message(
																message,
																{
																	'type'               : 'error',
																	'delay'              : message_delay,
																	'if_visible_not_show': true,
																	'show_here'          : {
																							'where'  : 'before',
																							'jq_node': jq_node
																						   }
																}
														);
		return notice_message_id;
	}


	/**
	 * Warning message. 	Preset of parameters for real message function.
	 *
	 * @param el		- any jQuery node definition
	 * @param message	- Message HTML
	 * @returns string  - HTML ID		or 0 if not showing during this time.
	 */
	function wpbc_front_end__show_message__warning( jq_node, message ){

		var notice_message_id = wpbc_front_end__show_message(
																message,
																{
																	'type'               : 'warning',
																	'delay'              : 10000,
																	'if_visible_not_show': true,
																	'show_here'          : {
																							'where'  : 'right',
																							'jq_node': jq_node
																						   }
																}
														);
		wpbc_highlight_error_on_form_field( jq_node );
		return notice_message_id;
	}


	/**
	 * Warning message UNDER element. 	Preset of parameters for real message function.
	 *
	 * @param el		- any jQuery node definition
	 * @param message	- Message HTML
	 * @returns string  - HTML ID		or 0 if not showing during this time.
	 */
	function wpbc_front_end__show_message__warning_under_element( jq_node, message ){

		var notice_message_id = wpbc_front_end__show_message(
																message,
																{
																	'type'               : 'warning',
																	'delay'              : 10000,
																	'if_visible_not_show': true,
																	'show_here'          : {
																							'where'  : 'after',
																							'jq_node': jq_node
																						   }
																}
														);
		return notice_message_id;
	}


	/**
	 * Warning message ABOVE element. 	Preset of parameters for real message function.
	 *
	 * @param el		- any jQuery node definition
	 * @param message	- Message HTML
	 * @returns string  - HTML ID		or 0 if not showing during this time.
	 */
	function wpbc_front_end__show_message__warning_above_element( jq_node, message ){

		var notice_message_id = wpbc_front_end__show_message(
																message,
																{
																	'type'               : 'warning',
																	'delay'              : 10000,
																	'if_visible_not_show': true,
																	'show_here'          : {
																							'where'  : 'before',
																							'jq_node': jq_node
																						   }
																}
														);
		return notice_message_id;
	}

	/**
	 * Highlight Error in specific field
	 *
	 * @param jq_node					string or jQuery element,  where scroll  to
	 */
	function wpbc_highlight_error_on_form_field( jq_node ){

		if ( !jQuery( jq_node ).length ){
			return;
		}
		if ( ! jQuery( jq_node ).is( ':input' ) ){
			// Situation with  checkboxes or radio  buttons
			var jq_node_arr = jQuery( jq_node ).find( ':input' );
			if ( !jq_node_arr.length ){
				return
			}
			jq_node = jq_node_arr.get( 0 );
		}
		var params = {};
		params[ 'delay' ] = 10000;

		if ( !jQuery( jq_node ).hasClass( 'wpbc_form_field_error' ) ){

			jQuery( jq_node ).addClass( 'wpbc_form_field_error' )

			if ( parseInt( params[ 'delay' ] ) > 0 ){
				var closed_timer = setTimeout( function (){
															 jQuery( jq_node ).removeClass( 'wpbc_form_field_error' );
														  }
											   , parseInt( params[ 'delay' ] )
									);

			}
		}
	}

/**
 * Scroll to specific element
 *
 * @param jq_node					string or jQuery element,  where scroll  to
 * @param extra_shift_offset		int shift offset from  jq_node
 */
function wpbc_do_scroll( jq_node , extra_shift_offset = 0 ){

	if ( !jQuery( jq_node ).length ){
		return;
	}
	var targetOffset = jQuery( jq_node ).offset().top;

	if ( targetOffset <= 0 ){
		if ( 0 != jQuery( jq_node ).nextAll( ':visible' ).length ){
			targetOffset = jQuery( jq_node ).nextAll( ':visible' ).first().offset().top;
		} else if ( 0 != jQuery( jq_node ).parent().nextAll( ':visible' ).length ){
			targetOffset = jQuery( jq_node ).parent().nextAll( ':visible' ).first().offset().top;
		}
	}

	if ( jQuery( '#wpadminbar' ).length > 0 ){
		targetOffset = targetOffset - 50 - 50;
	} else {
		targetOffset = targetOffset - 20 - 50;
	}
	targetOffset += extra_shift_offset;

	// Scroll only  if we did not scroll before
	if ( ! jQuery( 'html,body' ).is( ':animated' ) ){
		jQuery( 'html,body' ).animate( {scrollTop: targetOffset}, 500 );
	}
}



// FixIn: 10.2.0.4.
/**
 * Define Popovers for Timelines in WP Booking Calendar
 *
 * @returns {string|boolean}
 */
function wpbc_define_tippy_popover(){
	if ( 'function' !== typeof (wpbc_tippy) ){
		console.log( 'WPBC Error. wpbc_tippy was not defined.' );
		return false;
	}
	wpbc_tippy( '.popover_bottom.popover_click', {
		content( reference ){
			var popover_title = reference.getAttribute( 'data-original-title' );
			var popover_content = reference.getAttribute( 'data-content' );
			return '<div class="popover popover_tippy">'
				+ '<div class="popover-close"><a href="javascript:void(0)" onclick="javascript:this.parentElement.parentElement.parentElement.parentElement.parentElement._tippy.hide();" >&times;</a></div>'
				+ popover_content
				+ '</div>';
		},
		allowHTML        : true,
		trigger          : 'manual',
		interactive      : true,
		hideOnClick      : false,
		interactiveBorder: 10,
		maxWidth         : 550,
		theme            : 'wpbc-tippy-popover',
		placement        : 'bottom-start',
		touch            : ['hold', 500],
	} );
	jQuery( '.popover_bottom.popover_click' ).on( 'click', function (){
		if ( this._tippy.state.isVisible ){
			this._tippy.hide();
		} else {
			this._tippy.show();
		}
	} );
	wpbc_define_hide_tippy_on_scroll();
}



function wpbc_define_hide_tippy_on_scroll(){
	jQuery( '.flex_tl__scrolling_section2,.flex_tl__scrolling_sections' ).on( 'scroll', function ( event ){
		if ( 'function' === typeof (wpbc_tippy) ){
			wpbc_tippy.hideAll();
		}
	} );
}

/**
 * WPBC calendar loader bootstrap.
 * ==============================================================================================
 * - Finds every .calendar_loader_frame[data-wpbc-rid] on the page (now or later).
 * - For each loader element, waits a "grace" period (data-wpbc-grace, default 8000 ms):
 *     - If the real calendar appears: do nothing (loader naturally replaced).
 *     - If not: show a helpful message (missing jQuery/_wpbc/datepick) or a duplicate notice.
 * - Works with multiple calendars and even duplicate RIDs on the same page.
 * - No inline JS needed in the shortcode/template output.
 *
 * File:  ../includes/__js/client/cal/wpbc_cal_loader.js
 *
 * @since    10.14.5
 * @modified 2025-09-07 12:21
 * @version  1.0.0
 *
 */
/**
 * WPBC calendar loader bootstrap.
 * - Auto-detects .calendar_loader_frame[data-wpbc-rid] blocks.
 * - Waits a "grace" period per element before showing a helpful message
 *   if the real calendar hasn't replaced the loader.
 * - Multiple calendars and duplicate RIDs are handled.
 */
(function (w, d) {
	'use strict';

	/* ---------------------------------------------------------------------------
	 * Small utilities (snake_case)
	 * ------------------------------------------------------------------------ */

	/** Track processed loader elements; fallback to data flag if WeakSet missing. */
	var processed_set = typeof WeakSet === 'function' ? new WeakSet() : null;

	/** Return first match inside optional root. */
	function query_one(selector, root) {
		return (root || d).querySelector( selector );
	}

	/** Return NodeList of matches inside optional root. */
	function query_all(selector, root) {
		return (root || d).querySelectorAll( selector );
	}

	/** Run a callback when DOM is ready. */
	function on_ready(fn) {
		if ( d.readyState === 'loading' ) {
			d.addEventListener( 'DOMContentLoaded', fn );
		} else {
			fn();
		}
	}

	/** Clear interval safely. */
	function safe_clear(interval_id) {
		try {
			w.clearInterval( interval_id );
		} catch ( e ) {
		}
	}

	/** Mark element processed (WeakSet or data attribute). */
	function mark_processed(el) {
		if ( processed_set ) {
			processed_set.add( el );
		} else {
			try {
				el.dataset.wpbcProcessed = '1';
			} catch ( e ) {
			}
		}
	}

	/** Check if element was processed. */
	function is_processed(el) {
		return processed_set ? processed_set.has( el ) : (el && el.dataset && el.dataset.wpbcProcessed === '1');
	}

	/* ---------------------------------------------------------------------------
	 * Messages (fixed English strings; no i18n)
	 * ------------------------------------------------------------------------ */

	/**
	 * Build fixed English messages for a resource.
	 * @param {string|number} rid
	 * @return {{duplicate:string,support:string,lib_jq:string,lib_dp:string,lib_wpbc:string}}
	 */
	function get_messages(rid) {
		var rid_int = parseInt( rid, 10 );
		return {
			duplicate  :
				'You have added the same calendar (ID = ' + rid_int + ') more than once on this page. ' +
				'Please keep only one calendar with the same ID on a page to avoid conflicts.',
			init_failed:
				'The calendar could not be initialized on this page.' + '\n' +
				'Please check your browser console for JavaScript errors and conflicts with other scripts/plugins.',
			support    : 'Contact support@wpbookingcalendar.com if you have any questions.',
			lib_jq     :
				'It appears that the "jQuery" library is not loading correctly.' + '\n' +
				'For more information, please refer to this page: https://wpbookingcalendar.com/faq/',
			lib_dp     :
				'It appears that the "jQuery.datepick" library is not loading correctly.' + '\n' +
				'For more information, please refer to this page: https://wpbookingcalendar.com/faq/',
			lib_wpbc   :
				'It appears that the "_wpbc" library is not loading correctly.' + '\n' +
				'Please enable the loading of JS/CSS files for this page on the "WP Booking Calendar" - "Settings General" - "Advanced" page' + '\n' +
				'For more information, please refer to this page: https://wpbookingcalendar.com/faq/'
		};
	}

	/**
	 * Wrap plain text (with newlines) in a small HTML container.
	 * @param {string} msg
	 * @return {string}
	 */
	function wrap_html(msg) {
		return '<div style="font-size:13px;margin:10px;">' + String( msg || '' ).replace( /\n/g, '<br>' ) + '</div>';
	}

	/** Library presence checks (fast & cheap). */
	function has_jq() {
		return !!(w.jQuery && jQuery.fn && typeof jQuery.fn.on === 'function');
	}

	function has_dp() {
		return !!(w.jQuery && jQuery.datepick);
	}

	function has_wpbc() {
		return !!(w._wpbc && typeof w._wpbc.set_other_param === 'function');
	}

	function normalize_rid(rid) {
		var n = parseInt( rid, 10 );
		return (n > 0) ? String( n ) : '';
	}

	function get_rid_counts(rid) {
		var r = normalize_rid( rid );
		return {
			rid       : r,
			loaders   : r ? query_all( '.calendar_loader_frame[data-wpbc-rid="' + r + '"]' ).length : 0,
			containers: r ? query_all( '#calendar_booking' + r ).length : 0
		};
	}

	function is_duplicate_rid(rid) {
		var c = get_rid_counts( rid );
		return (c.loaders > 1) || (c.containers > 1);
	}

	/**
	 * Determine if the loader has been replaced by the real calendar.
	 *
	 * @param {Element} el       Loader element
	 * @param {string} rid       Resource ID
	 * @param {Element|null} container Optional #calendar_booking{rid} element
	 * @return {boolean}
	 */
	function is_replaced(el, rid, container) {
		var loader_still_in_dom = d.body.contains( el );
		var calendar_exists     = !!query_one( '.wpbc_calendar_id_' + rid, container || d );
		return (!loader_still_in_dom) || calendar_exists;
	}

	/**
	 * Start watcher for a single loader element.
	 * - Polls and observes the calendar container.
	 * - After grace, injects a suitable message if not replaced.
	 *
	 * @param {Element} el
	 */
	function start_for(el) {
		if ( ! el || is_processed( el ) ) {
			return;
		}
		mark_processed( el );

		var rid = el.dataset.wpbcRid;
		if ( ! rid ) {
			return;
		}

		var grace_ms = parseInt( el.dataset.wpbcGrace || '8000', 10 );
		if ( ! (grace_ms > 0) ) {
			grace_ms = 8000;
		}

		var container_id = 'calendar_booking' + rid;
		var container    = d.getElementById( container_id );
		var text_el      = query_one( '.calendar_loader_text', el );

		function replaced_now() {
			return is_replaced( el, rid, container );
		}

		// Already replaced -> nothing to do.
		if ( replaced_now() ) {
			return;
		}

		// 1) Cheap polling.
		var poll_id = w.setInterval( function () {
			if ( replaced_now() ) {
				safe_clear( poll_id );
				if ( observer ) {
					try {
						observer.disconnect();
					} catch ( e ) {
					}
				}
			}
		}, 250 );

		// 2) MutationObserver for faster reaction.
		var observer = null;
		if ( container && 'MutationObserver' in w ) {
			try {
				observer = new MutationObserver( function () {
					if ( replaced_now() ) {
						safe_clear( poll_id );
						try {
							observer.disconnect();
						} catch ( e ) {
						}
					}
				} );
				observer.observe( container, { childList: true, subtree: true } );
			} catch ( e ) {
			}
		}

		// 3) Final decision after grace period.
		w.setTimeout( function finalize_after_grace() {
			if ( replaced_now() ) {
				safe_clear( poll_id );
				if ( observer ) {
					try {
						observer.disconnect();
					} catch ( e ) {
					}
				}
				return;
			}

			var M = get_messages( rid );
			var msg;
			if ( ! has_jq() ) {
				msg = M.lib_jq;
			} else if ( ! has_wpbc() ) {
				msg = M.lib_wpbc;
			} else if ( ! has_dp() ) {
				msg = M.lib_dp;
			} else {
				// Libraries are present, but loader wasn't replaced -> decide what is most likely.
				if ( is_duplicate_rid( rid ) ) {
					msg = M.duplicate + '\n\n' + M.support;
				} else {
					msg = M.init_failed + '\n\n' + M.support;
				}
			}

			try {
				if ( text_el ) {
					text_el.innerHTML = wrap_html( msg );
				}
			} catch ( e ) {
			}

			safe_clear( poll_id );
			if ( observer ) {
				try {
					observer.disconnect();
				} catch ( e ) {
				}
			}
		}, grace_ms );
	}

	/**
	 * Initialize watchers for loader elements already in the DOM.
	 */
	function bootstrap_existing() {
		query_all( '.calendar_loader_frame[data-wpbc-rid]' ).forEach( start_for );
	}

	/**
	 * Observe the document for any new loader elements inserted later (AJAX, block render).
	 */
	function observe_new_loaders() {
		if ( ! ('MutationObserver' in w) ) {
			return;
		}
		try {
			var doc_observer = new MutationObserver( function (mutations) {
				for ( var i = 0; i < mutations.length; i++ ) {
					var nodes = mutations[i].addedNodes || [];
					for ( var j = 0; j < nodes.length; j++ ) {
						var node = nodes[j];
						if ( ! node || node.nodeType !== 1 ) {
							continue;
						}
						if ( node.matches && node.matches( '.calendar_loader_frame[data-wpbc-rid]' ) ) {
							start_for( node );
						}
						if ( node.querySelectorAll ) {
							var inner = node.querySelectorAll( '.calendar_loader_frame[data-wpbc-rid]' );
							if ( inner && inner.length ) {
								inner.forEach( start_for );
							}
						}
					}
				}
			} );
			doc_observer.observe( d.documentElement, { childList: true, subtree: true } );
		} catch ( e ) {
		}
	}

	/* ---------------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------------ */
	on_ready( function () {
		bootstrap_existing();
		observe_new_loaders();
	} );

})( window, document );

(function( w ) {

	'use strict';

	if ( ! w.WPBC_FE ) {
		w.WPBC_FE = {};
	}

	/**
	 * Auto-fill booking form fields (text/email) based on input "name" patterns.
	 *
	 * Form ID format: booking_form{resource_id}
	 * Skips date field: date_booking{resource_id}
	 *
	 * @param {number} resource_id Booking resource ID.
	 * @param {Object} fill_values Values to inject (strings).
	 *
	 * @return {boolean} True if form found and processed, false otherwise.
	 */
	w.WPBC_FE.autofill_booking_form_fields = function( resource_id, fill_values ) {

		resource_id  = parseInt( resource_id, 10 ) || 0;
		fill_values  = fill_values || {};

		var form_id   = 'booking_form' + resource_id;
		var date_name = 'date_booking' + resource_id;

		var submit_form = document.getElementById( form_id );

		if ( ! submit_form ) {
			/* eslint-disable no-console */
			console.error( 'WPBC: No booking form: ' + form_id );
			/* eslint-enable no-console */
			return false;
		}

		// Keep same regex rules and priority order as legacy inline JS.
		var rules = array_rules( fill_values );

		var elements = submit_form.elements || [];
		var count    = elements.length;
		var el;
		var i;
		var j;

		for ( i = 0; i < count; i++ ) {

			el = elements[ i ];

			if ( ! el || ! el.name ) {
				continue;
			}

			// Only text/email inputs.
			if ( ( el.type !== 'text' ) && ( el.type !== 'email' ) ) {
				continue;
			}

			// Skip date field.
			if ( el.name === date_name ) {
				continue;
			}

			// Fill only empty values (legacy behavior: == "").
			if ( el.value !== '' ) {
				continue;
			}

			for ( j = 0; j < rules.length; j++ ) {

				if ( rules[ j ].re.test( el.name ) ) {

					if ( rules[ j ].val !== '' ) {
						el.value = rules[ j ].val;
					}

					break; // Stop at first matching rule (priority).
				}
			}
		}

		return true;
	};

	/**
	 * Build rules array for autofill.
	 *
	 * @param {Object} fill_values Values to inject.
	 *
	 * @return {Array} Rules list.
	 */
	function array_rules( fill_values ) {

		// Normalize to strings (prevent "undefined" in fields).
		var nickname  = ( fill_values.nickname != null ) ? String( fill_values.nickname ) : '';
		var last_name = ( fill_values.last_name != null ) ? String( fill_values.last_name ) : '';
		var first_name = ( fill_values.first_name != null ) ? String( fill_values.first_name ) : '';
		var email     = ( fill_values.email != null ) ? String( fill_values.email ) : '';
		var phone     = ( fill_values.phone != null ) ? String( fill_values.phone ) : '';
		var nb_enfant = ( fill_values.nb_enfant != null ) ? String( fill_values.nb_enfant ) : '';
		var url       = ( fill_values.url != null ) ? String( fill_values.url ) : '';

		return [
			{ re: /^([A-Za-z0-9_\-\.])*(nickname){1}([A-Za-z0-9_\-\.])*$/, val: nickname },
			{ re: /^([A-Za-z0-9_\-\.])*(last|second){1}([_\-\.])?name([A-Za-z0-9_\-\.])*$/, val: last_name },
			{ re: /^name([0-9_\-\.])*$/, val: first_name },
			{ re: /^([A-Za-z0-9_\-\.])*(first|my){1}([_\-\.])?name([A-Za-z0-9_\-\.])*$/, val: first_name },
			{ re: /^(e)?([_\-\.])?mail([0-9_\-\.]*)$/, val: email },
			{ re: /^([A-Za-z0-9_\-\.])*(phone|fone){1}([A-Za-z0-9_\-\.])*$/, val: phone },
			{ re: /^(e)?([_\-\.])?nb_enfant([0-9_\-\.]*)$/, val: nb_enfant },
			{ re: /^([A-Za-z0-9_\-\.])*(URL|site|web|WEB){1}([A-Za-z0-9_\-\.])*$/, val: url }
		];
	}

})( window );

// == Submit Booking Data ==============================================================================================
// Refactored (safe), with new wpbc_* names.
// Backward-compatible wrappers for legacy function names are included at the bottom.
// @file: includes/__js/client/front_end_form/booking_form_submit.js

/**
 * Check fields at form and then send request (legacy: mybooking_submit).
 *
 * @param {HTMLFormElement} submit_form
 * @param {number|string}   resource_id
 * @param {string}          wpdev_active_locale
 *
 * @return {false|undefined} Legacy behavior: returns false in some cases, otherwise undefined.
 */
function wpbc_booking_form_submit( submit_form, resource_id, wpdev_active_locale ) {

	resource_id = parseInt( resource_id, 10 );

	// Safety guard (legacy code assumed valid form).
	if ( ! submit_form || ! submit_form.elements ) {
		/* eslint-disable no-console */
		console.error( 'WPBC: Invalid submit form in wpbc_booking_form_submit().' );
		/* eslint-enable no-console */
		return false;
	}

	// -------------------------------------------------------------------------
	// External hook: allow pause submit on confirmation/summary step.
	// -------------------------------------------------------------------------
	var target_elm = jQuery( '.booking_form_div' ).trigger( 'booking_form_submit_click', [ resource_id, submit_form, wpdev_active_locale ] ); // FixIn: 8.8.3.13.

	if (
		( jQuery( target_elm ).find( 'input[name="booking_form_show_summary"]' ).length > 0 ) &&
		( 'pause_submit' === jQuery( target_elm ).find( 'input[name="booking_form_show_summary"]' ).val() )
	) {
		return false;
	}

	// FixIn: 8.4.0.2.
	var is_error = wpbc_check_errors_in_booking_form( resource_id );
	if ( is_error ) {
		return false;
	}

	// -------------------------------------------------------------------------
	// Show message if no selected days in Calendar(s).
	// -------------------------------------------------------------------------
	var date_input = document.getElementById( 'date_booking' + resource_id );
	var date_value = ( date_input ) ? date_input.value : '';

	if ( '' === date_value ) {

		var arr_of_selected_additional_calendars = wpbc_get_arr_of_selected_additional_calendars( resource_id ); // FixIn: 8.5.2.26.

		if ( ! arr_of_selected_additional_calendars || ( arr_of_selected_additional_calendars.length === 0 ) ) {
			wpbc_front_end__show_message__error_under_element(
				'#booking_form_div' + resource_id + ' .bk_calendar_frame',
				_wpbc.get_message( 'message_check_no_selected_dates' ),
				3000
			);
			return;
		}
	}

	// -------------------------------------------------------------------------
	// FixIn: 6.1.1.3. Time selection availability checks.
	// -------------------------------------------------------------------------
	if ( typeof wpbc_is_this_time_selection_not_available === 'function' ) {

		if ( '' === date_value ) { // Primary calendar not selected.

			var additional_calendars_el = document.getElementById( 'additional_calendars' + resource_id );

			if ( additional_calendars_el !== null ) { // Checking additional calendars.

				var id_additional_str = additional_calendars_el.value;
				var id_additional_arr = id_additional_str.split( ',' );
				var is_times_dates_ok = false;

				for ( var ia = 0; ia < id_additional_arr.length; ia++ ) {

					var add_id = id_additional_arr[ ia ];

					var add_date_el = document.getElementById( 'date_booking' + add_id );
					var add_date_val = ( add_date_el ) ? add_date_el.value : '';

					if (
						( '' !== add_date_val ) &&
						( ! wpbc_is_this_time_selection_not_available( add_id, submit_form.elements ) )
					) {
						is_times_dates_ok = true;
					}
				}

				if ( ! is_times_dates_ok ) {
					return;
				}
			}

		} else { // Primary calendar selected.

			if ( wpbc_is_this_time_selection_not_available( resource_id, submit_form.elements ) ) {
				return;
			}
		}
	}

	// -------------------------------------------------------------------------
	// Serialize form (legacy format).
	// -------------------------------------------------------------------------
	var count    = submit_form.elements.length;
	var formdata = '';
	var inp_value;
	var inp_title_value;
	var element;
	var el_type;

	// Helper: legacy escaping for the serialized value.
	function wpbc_escape_serialized_value( val ) {

		val = ( val == null ) ? '' : String( val );

		// Replace registered characters.
		val = val.replace( new RegExp( '\\^', 'g' ), '&#94;' );
		val = val.replace( new RegExp( '~', 'g' ), '&#126;' );

		// Replace quotes.
		val = val.replace( /"/g, '&#34;' );
		val = val.replace( /'/g, '&#39;' );

		return val;
	}

	// Helper: determine UI type for title extraction (legacy logic).
	function wpbc_get_input_element_type( el ) {

		if ( ! el || ! el.tagName ) {
			return '';
		}

		var tag = String( el.tagName ).toLowerCase();

		if ( 'input' === tag ) {
			return ( el.type ) ? String( el.type ).toLowerCase() : 'text';
		}

		// Legacy used "select" string here.
		if ( 'select' === tag ) {
			return 'select';
		}

		return tag;
	}

	for ( var i = 0; i < count; i++ ) { // FixIn: 9.1.5.1.

		element = submit_form.elements[ i ];

		if ( ! element ) {
			continue;
		}

		if ( jQuery( element ).closest( '.booking_form_garbage' ).length ) {
			continue; // Skip elements from garbage. FixIn: 7.1.2.14.
		}

		if (
			( element.type !== 'button' ) &&
			( element.type !== 'hidden' ) &&
			( element.name !== ( 'date_booking' + resource_id ) )
			// && ( jQuery( element ).is( ':visible' ) ) //FixIn: 7.2.1.12.2
		) {

			// -------------------------------------------------------------
			// Get element value.
			// -------------------------------------------------------------
			if ( element.type === 'checkbox' ) {

				if ( element.value === '' ) {
					inp_value = element.checked;
				} else {
					inp_value = ( element.checked ) ? element.value : '';
				}

			} else if ( element.type === 'radio' ) {

				if ( element.checked ) {
					inp_value = element.value;
				} else {

					// Required radio: show warning if none checked.
					// FixIn: 7.0.1.62.
					if (
						( element.className.indexOf( 'wpdev-validates-as-required' ) !== -1 ) &&
						( jQuery( element ).is( ':visible' ) ) && // FixIn: 7.2.1.12.2.
						( ! jQuery( ':radio[name="' + element.name + '"]', submit_form ).is( ':checked' ) )
					) {
						wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required_for_radio_box' ) ); // FixIn: 8.5.1.3.
						return;
					}

					// Skip storing empty radio options.
					continue;
				}

			} else {
				inp_value = element.value;
			}

			inp_title_value = '';

			// -------------------------------------------------------------
			// Get human-friendly title value (legacy behavior).
			// -------------------------------------------------------------
			var input_element_type = wpbc_get_input_element_type( element );

			switch ( input_element_type ) {

				case 'text':
				case 'email':
					inp_title_value = inp_value;
					break;

				case 'select':
					inp_title_value = jQuery( element ).find( 'option:selected' ).text();
					break;

				case 'radio':
				case 'checkbox':
					if ( jQuery( element ).is( ':checked' ) ) {
						var label_element = jQuery( element ).parents( '.wpdev-list-item' ).find( '.wpdev-list-item-label' );
						if ( label_element.length ) {
							inp_title_value = label_element.html();
						}
					}
					break;

				default:
					inp_title_value = inp_value;
			}

			// -------------------------------------------------------------
			// Multiple select value extraction.
			// -------------------------------------------------------------
			if ( ( element.type === 'selectbox-multiple' ) || ( element.type === 'select-multiple' ) ) {
				inp_value = jQuery( '[name="' + element.name + '"]' ).val();
				if ( ( inp_value === null ) || ( String( inp_value ) === '' ) ) {
					inp_value = '';
				}
			}

			// -------------------------------------------------------------
			// Make validation only for visible elements.
			// -------------------------------------------------------------
			if ( jQuery( element ).is( ':visible' ) ) { // FixIn: 7.2.1.12.2.

				// Recheck max available visitors selection.
				if ( typeof wpbc__is_less_than_required__of_max_available_slots__bl === 'function' ) {
					if ( wpbc__is_less_than_required__of_max_available_slots__bl( resource_id, element ) ) {
						return;
					}
				}

				// Required fields.
				if ( element.className.indexOf( 'wpdev-validates-as-required' ) !== -1 ) {

					if ( ( element.type === 'checkbox' ) && ( element.checked === false ) ) {

						if ( ! jQuery( ':checkbox[name="' + element.name + '"]', submit_form ).is( ':checked' ) ) {
							wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required_for_check_box' ) ); // FixIn: 8.5.1.3.
							return;
						}
					}

					if ( element.type === 'radio' ) {

						if ( ! jQuery( ':radio[name="' + element.name + '"]', submit_form ).is( ':checked' ) ) {
							wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required_for_radio_box' ) ); // FixIn: 8.5.1.3.
							return;
						}
					}

					if ( ( element.type !== 'checkbox' ) && ( element.type !== 'radio' ) && ( '' === wpbc_trim( inp_value ) ) ) {
						wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_required' ) ); // FixIn: 8.5.1.3.
						return;
					}
				}

				// Email format validation.
				if ( element.className.indexOf( 'wpdev-validates-as-email' ) !== -1 ) {

					inp_value = String( inp_value ).replace( /^\s+|\s+$/gm, '' ); // Trim white space. FixIn: 5.4.5.
					var reg_email = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;

					if ( inp_value !== '' ) {
						if ( reg_email.test( inp_value ) === false ) {
							wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_email' ) ); // FixIn: 8.5.1.3.
							return;
						}
					}
				}

				// Same email field validation (verification field).
				if ( ( element.className.indexOf( 'wpdev-validates-as-email' ) !== -1 ) && ( element.className.indexOf( 'same_as_' ) !== -1 ) ) {

					var primary_email_name = element.className.match( /same_as_([^\s])+/gi );

					if ( primary_email_name !== null ) {

						primary_email_name = primary_email_name[ 0 ].substr( 8 );

						if ( jQuery( '[name="' + primary_email_name + resource_id + '"]' ).length > 0 ) {

							if ( jQuery( '[name="' + primary_email_name + resource_id + '"]' ).val() !== inp_value ) {
								wpbc_front_end__show_message__warning( element, _wpbc.get_message( 'message_check_same_email' ) ); // FixIn: 8.5.1.3.
								return;
							}
						}
					}

					// Skip one loop for the email verification field.
					continue; // FixIn: 8.1.2.15.
				}
			}

			// -------------------------------------------------------------
			// Get Form Data (legacy format).
			// -------------------------------------------------------------
			if ( element.name !== ( 'captcha_input' + resource_id ) ) {

				if ( formdata !== '' ) {
					formdata += '~';
				}

				el_type = element.type;

				if ( element.className.indexOf( 'wpdev-validates-as-email' ) !== -1 ) {
					el_type = 'email';
				}
				if ( element.className.indexOf( 'wpdev-validates-as-coupon' ) !== -1 ) {
					el_type = 'coupon';
				}

				inp_value = wpbc_escape_serialized_value( inp_value );

				if ( el_type === 'select-one' ) {
					el_type = 'selectbox-one';
				}
				if ( el_type === 'select-multiple' ) {
					el_type = 'selectbox-multiple';
				}

				formdata += el_type + '^' + element.name + '^' + inp_value;

				// Add title/label value (legacy).
				var clean_field_name = String( element.name );

				// BUGFIX: replaceAll(RegExp) is not supported in older browsers.
				// Keep legacy intent: remove [] suffix occurrences.
				clean_field_name = clean_field_name.replace( /\[\]/gi, '' );

				var resource_id_str = String( resource_id );

				// Legacy assumed suffix ends with resource_id, make it safe.
				if (
					( clean_field_name.length >= resource_id_str.length ) &&
					( clean_field_name.substr( clean_field_name.length - resource_id_str.length ) === resource_id_str )
				) {
					clean_field_name = clean_field_name.substr( 0, clean_field_name.length - resource_id_str.length );
				}

				formdata += '~' + el_type + '^' + clean_field_name + '_val' + resource_id + '^' + inp_title_value;
			}
		}
	}

	// TODO: here was function for 'Check if visitor finish dates selection.

	// Captcha verify.
	var captcha = document.getElementById( 'wpdev_captcha_challenge_' + resource_id );

	if ( captcha !== null ) {
		wpbc_form_submit_send( resource_id, formdata, captcha.value, document.getElementById( 'captcha_input' + resource_id ).value, wpdev_active_locale );
	} else {
		wpbc_form_submit_send( resource_id, formdata, '', '', wpdev_active_locale );
	}

	return;
}


/**
 * Gathering params for sending Ajax request and then send it (legacy: form_submit_send).
 *
 * @param {number|string} resource_id
 * @param {string}        formdata
 * @param {string}        captcha_chalange
 * @param {string}        user_captcha
 * @param {string}        wpdev_active_locale
 *
 * @return {undefined} Legacy behavior.
 */
function wpbc_form_submit_send( resource_id, formdata, captcha_chalange, user_captcha, wpdev_active_locale ) {

	resource_id = parseInt( resource_id, 10 );

	var my_booking_form = '';
	var booking_form_type_el = document.getElementById( 'booking_form_type' + resource_id );
	if ( booking_form_type_el !== null ) {
		my_booking_form = booking_form_type_el.value;
	}

	var my_booking_hash = '';
	if ( _wpbc.get_other_param( 'this_page_booking_hash' ) !== '' ) {
		my_booking_hash = _wpbc.get_other_param( 'this_page_booking_hash' );
	}

	var is_send_emeils = 1;
	if ( jQuery( '#is_send_email_for_pending' ).length ) { // FixIn: 8.7.9.5.

		is_send_emeils = jQuery( '#is_send_email_for_pending' ).is( ':checked' );

		if ( false === is_send_emeils ) {
			is_send_emeils = 0;
		} else {
			is_send_emeils = 1;
		}
	}

	var date_el = document.getElementById( 'date_booking' + resource_id );
	var date_value = ( date_el ) ? date_el.value : '';

	if ( '' !== date_value ) { // FixIn: 6.1.1.3.
		wpbc_send_ajax_submit( resource_id, formdata, captcha_chalange, user_captcha, is_send_emeils, my_booking_hash, my_booking_form, wpdev_active_locale );
	} else {
		jQuery( '#booking_form_div' + resource_id ).hide();
		jQuery( '#submiting' + resource_id ).hide();
	}

	// -------------------------------------------------------------------------
	// Additional calendars submit.
	// -------------------------------------------------------------------------
	var additional_calendars_el = document.getElementById( 'additional_calendars' + resource_id );
	if ( additional_calendars_el === null ) {
		return;
	}

	var id_additional_str = additional_calendars_el.value;
	var id_additional_arr = id_additional_str.split( ',' );

	// FixIn: 10.9.4.1.
	for ( var ia = 0; ia < id_additional_arr.length; ia++ ) {
		id_additional_arr[ ia ] = parseInt( id_additional_arr[ ia ], 10 );
	}

	if ( ! jQuery( '#booking_form_div' + resource_id ).is( ':visible' ) ) {
		wpbc_booking_form__spin_loader__show( resource_id ); // Show Spinner
	}

	// Helper: rewrite field name suffix from resource_id -> id_additional.
	function wpbc_rewrite_field_name_suffix( field_name, old_id, new_id ) {

		field_name = String( field_name );

		var old_id_str = String( old_id );
		var new_id_str = String( new_id );

		// Handle fields with [].
		if (
			( field_name.length >= ( old_id_str.length + 2 ) ) &&
			( field_name.substr( field_name.length - ( old_id_str.length + 2 ) ) === ( old_id_str + '[]' ) )
		) {
			return field_name.substr( 0, field_name.length - ( old_id_str.length + 2 ) ) + new_id_str + '[]';
		}

		// Handle fields without [].
		if (
			( field_name.length >= old_id_str.length ) &&
			( field_name.substr( field_name.length - old_id_str.length ) === old_id_str )
		) {
			return field_name.substr( 0, field_name.length - old_id_str.length ) + new_id_str;
		}

		// Fallback: return unchanged (safer than breaking name).
		return field_name;
	}

	for ( ia = 0; ia < id_additional_arr.length; ia++ ) {

		var id_additional = id_additional_arr[ ia ];

		// FixIn: 10.9.4.1.
		if ( id_additional <= 0 ) {
			continue;
		}

		// Rebuild formdata for each additional calendar (legacy behavior).
		var formdata_additional_arr = String( formdata ).split( '~' );
		var formdata_additional = '';

		for ( var j = 0; j < formdata_additional_arr.length; j++ ) {

			var my_form_field = formdata_additional_arr[ j ].split( '^' );

			if ( formdata_additional !== '' ) {
				formdata_additional += '~';
			}

			// Safety: ensure we have at least type ^ name ^ value.
			if ( my_form_field.length < 3 ) {
				formdata_additional += formdata_additional_arr[ j ];
				continue;
			}

			my_form_field[ 1 ] = wpbc_rewrite_field_name_suffix( my_form_field[ 1 ], resource_id, id_additional );
			formdata_additional += my_form_field[ 0 ] + '^' + my_form_field[ 1 ] + '^' + my_form_field[ 2 ];
		}

		// If payment form for main booking resource is showing, append for additional calendars.
		if ( jQuery( '#gateway_payment_forms' + resource_id ).length > 0 ) {
			jQuery( '#gateway_payment_forms' + resource_id ).after( '<div id="gateway_payment_forms' + id_additional + '"></div>' );
			jQuery( '#gateway_payment_forms' + resource_id ).after( '<div id="ajax_respond_insert' + id_additional + '" style="display:none;"></div>' );
		}

		// FixIn: 8.5.2.17.
		wpbc_send_ajax_submit( id_additional, formdata_additional, captcha_chalange, user_captcha, is_send_emeils, my_booking_hash, my_booking_form, wpdev_active_locale );
	}
}


/**
 * Send Ajax submit (legacy: send_ajax_submit).
 *
 * @param {number|string} resource_id
 * @param {string}        formdata
 * @param {string}        captcha_chalange
 * @param {string}        user_captcha
 * @param {number}        is_send_emeils
 * @param {string}        my_booking_hash
 * @param {string}        my_booking_form
 * @param {string}        wpdev_active_locale
 *
 * @return {undefined} Legacy behavior.
 */
function wpbc_send_ajax_submit(resource_id, formdata, captcha_chalange, user_captcha, is_send_emeils, my_booking_hash, my_booking_form, wpdev_active_locale) {

	resource_id = parseInt( resource_id, 10 );

	// Disable Submit | Show spin loader.
	wpbc_booking_form__on_submit__ui_elements_disable( resource_id );

	// FixIn: 2026-02-05 - pass preview context to booking create Ajax.
	var form_status  = wpbc__get_form_status_for_submit( resource_id );
	var preview_args = (form_status === 'preview') ? wpbc__get_bfb_preview_args_from_location() : null;

	var request_params = {
		'resource_id'              : resource_id,
		'dates_ddmmyy_csv'         : document.getElementById( 'date_booking' + resource_id ).value,
		'formdata'                 : formdata,
		'booking_hash'             : my_booking_hash,
		'custom_form'              : my_booking_form,
		'aggregate_resource_id_arr': ( ( null !== _wpbc.booking__get_param_value( resource_id, 'aggregate_resource_id_arr' ) ) ? _wpbc.booking__get_param_value( resource_id, 'aggregate_resource_id_arr' ).join( ',' ) : '' ),
		'captcha_chalange'         : captcha_chalange,
		'captcha_user_input'       : user_captcha,
		'is_emails_send'           : is_send_emeils,
		'active_locale'            : wpdev_active_locale,
		'form_status'              : form_status
	};

	// If preview, pass session identifiers so PHP can load transient snapshot.
	if ( preview_args && preview_args.token && preview_args.form_id ) {
		request_params['wpbc_bfb_preview']         = 1;
		request_params['wpbc_bfb_preview_token']   = preview_args.token;
		request_params['wpbc_bfb_preview_form_id'] = preview_args.form_id;
		request_params['wpbc_bfb_preview_nonce']   = preview_args.nonce; // note: URL param is `nonce`.
	}

	var is_exit = wpbc_ajx_booking__create( request_params );

	if ( true === is_exit ) {
		return;
	}
}



// == Helper Functions =================================================================================================

/**
 * Parse query string into {key:value} (old-browser safe).
 *
 * @param {string} qs
 * @return {Object}
 */
function wpbc__parse_query_string(qs) {
	var out = {};
	qs      = (qs || '');
	qs      = qs.replace( /^\?/, '' );
	if ( ! qs ) {
		return out;
	}

	var parts = qs.split( '&' );
	for ( var i = 0; i < parts.length; i++ ) {
		var kv = parts[i].split( '=' );
		var k  = decodeURIComponent( kv[0] || '' );
		if ( ! k ) {
			continue;
		}
		var v  = decodeURIComponent( kv.slice( 1 ).join( '=' ) || '' );
		out[k] = v;
	}
	return out;
}

/**
 * Detect preview args from current URL (iframe URL).
 *
 * @return {Object|null} { token, form_id, nonce } or null
 */
function wpbc__get_bfb_preview_args_from_location() {
	try {
		var p = wpbc__parse_query_string( (window.location && window.location.search) ? window.location.search : '' );

		if ( ! p.wpbc_bfb_preview || (p.wpbc_bfb_preview === '0') ) {
			return null;
		}

		if ( ! p.wpbc_bfb_preview_token || ! p.wpbc_bfb_preview_form_id ) {
			return null;
		}

		return {
			token  : String( p.wpbc_bfb_preview_token ),
			form_id: parseInt( p.wpbc_bfb_preview_form_id, 10 ) || 0,
			nonce  : (p.nonce) ? String( p.nonce ) : ''
		};
	} catch ( e ) {
		return null;
	}
}

/**
 * Resolve form status for submit.
 *
 * Priority:
 * 1) shortcode param exposed via _wpbc.booking__get_param_value(..., 'form_status')
 * 2) detect preview URL args
 * 3) fallback: published
 *
 * @param {number} resource_id
 * @return {string} 'preview'|'published'
 */
function wpbc__get_form_status_for_submit(resource_id) {

	var status = '';

	try {
		if ( (typeof _wpbc !== 'undefined') && _wpbc.booking__get_param_value ) {
			status = _wpbc.booking__get_param_value( resource_id, 'form_status' );
		}
	} catch ( e ) {}

	status = (status == null) ? '' : String( status );
	status = status.toLowerCase();

	// URL-based detection for preview iframe.
	var preview_args = wpbc__get_bfb_preview_args_from_location();
	if ( preview_args ) {
		return 'preview';
	}

	return (status === 'preview') ? 'preview' : 'published';
}



// == Backward-compatible wrappers (keep old global names working 100% as before). =====================================
function mybooking_submit( submit_form, resource_id, wpdev_active_locale ) {
	return wpbc_booking_form_submit( submit_form, resource_id, wpdev_active_locale );
}

try {
	var ev = (typeof CustomEvent === 'function') ? new CustomEvent( 'wpbc-ready' ) : document.createEvent( 'Event' );
	if ( ev.initEvent ) {
		ev.initEvent( 'wpbc-ready', true, true );
	}
	document.dispatchEvent( ev );
	console.log( 'wpbc-ready' );
} catch ( e ) {
	console.error( "WPBC event 'wpbc-ready' failed!", e );
}

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndwYmNfdXRpbHMuanMiLCJ3cGJjLmpzIiwiZGV2X2xvZy5qcyIsImFqeF9sb2FkX2JhbGFuY2VyLmpzIiwid3BiY19jYWwuanMiLCJkYXlzX3NlbGVjdF9jdXN0b20uanMiLCJ3cGJjX2NhbF9hanguanMiLCJ3cGJjX2ZlX21lc3NhZ2VzLmpzIiwidGltZWxpbmVfcG9wb3Zlci5qcyIsIndwYmNfY2FsX2xvYWRlci5qcyIsImF1dG9maWxsX2ZpZWxkcy5qcyIsImJvb2tpbmdfZm9ybV9zdWJtaXQuanMiLCJ3cGJjX3JlYWR5X2V2ZW50LmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2xHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQy9oQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2hFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDdFFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2pyRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDeE1BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2hSQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDaFpBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2xEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDeFVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNuSEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUN6cUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoid3BiY19hbGwuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbiAqIEphdmFTY3JpcHQgVXRpbCBGdW5jdGlvbnNcdFx0Li4vaW5jbHVkZXMvX19qcy91dGlscy93cGJjX3V0aWxzLmpzXHJcbiAqID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBUcmltICBzdHJpbmdzIGFuZCBhcnJheSBqb2luZWQgd2l0aCAgKCwpXHJcbiAqXHJcbiAqIEBwYXJhbSBzdHJpbmdfdG9fdHJpbSAgIHN0cmluZyAvIGFycmF5XHJcbiAqIEByZXR1cm5zIHN0cmluZ1xyXG4gKi9cclxuZnVuY3Rpb24gd3BiY190cmltKHN0cmluZ190b190cmltKSB7XHJcblxyXG5cdGlmICggQXJyYXkuaXNBcnJheSggc3RyaW5nX3RvX3RyaW0gKSApIHtcclxuXHRcdHN0cmluZ190b190cmltID0gc3RyaW5nX3RvX3RyaW0uam9pbiggJywnICk7XHJcblx0fVxyXG5cclxuXHRpZiAoICdzdHJpbmcnID09IHR5cGVvZiAoc3RyaW5nX3RvX3RyaW0pICkge1xyXG5cdFx0c3RyaW5nX3RvX3RyaW0gPSBzdHJpbmdfdG9fdHJpbS50cmltKCk7XHJcblx0fVxyXG5cclxuXHRyZXR1cm4gc3RyaW5nX3RvX3RyaW07XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBDaGVjayBpZiBlbGVtZW50IGluIGFycmF5XHJcbiAqXHJcbiAqIEBwYXJhbSBhcnJheV9oZXJlXHRcdGFycmF5XHJcbiAqIEBwYXJhbSBwX3ZhbFx0XHRcdFx0ZWxlbWVudCB0byAgY2hlY2tcclxuICogQHJldHVybnMge2Jvb2xlYW59XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2luX2FycmF5KGFycmF5X2hlcmUsIHBfdmFsKSB7XHJcblx0Zm9yICggdmFyIGkgPSAwLCBsID0gYXJyYXlfaGVyZS5sZW5ndGg7IGkgPCBsOyBpKysgKSB7XHJcblx0XHRpZiAoIGFycmF5X2hlcmVbaV0gPT0gcF92YWwgKSB7XHJcblx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRyZXR1cm4gZmFsc2U7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBQcmV2ZW50IG9wZW5pbmcgYmxhbmsgd2luZG93cyBvbiBXb3JkUHJlc3MgcGxheWdyb3VuZCBmb3IgcHNldWRvIGxpbmtzIGxpa2UgdGhpczogPGEgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiPiBvciAjIHRvIHN0YXkgaW4gdGhlIHNhbWUgdGFiLlxyXG4gKi9cclxuKGZ1bmN0aW9uICgpIHtcclxuXHQndXNlIHN0cmljdCc7XHJcblxyXG5cdGZ1bmN0aW9uIGlzX3BsYXlncm91bmRfb3JpZ2luKCkge1xyXG5cdFx0cmV0dXJuIGxvY2F0aW9uLm9yaWdpbiA9PT0gJ2h0dHBzOi8vcGxheWdyb3VuZC53b3JkcHJlc3MubmV0JztcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIGlzX3BzZXVkb19saW5rKGEpIHtcclxuXHRcdGlmICggIWEgfHwgIWEuZ2V0QXR0cmlidXRlICkgcmV0dXJuIHRydWU7XHJcblx0XHR2YXIgaHJlZiA9IChhLmdldEF0dHJpYnV0ZSggJ2hyZWYnICkgfHwgJycpLnRyaW0oKS50b0xvd2VyQ2FzZSgpO1xyXG5cdFx0cmV0dXJuIChcclxuXHRcdFx0IWhyZWYgfHxcclxuXHRcdFx0aHJlZiA9PT0gJyMnIHx8XHJcblx0XHRcdGhyZWYuaW5kZXhPZiggJyMnICkgPT09IDAgfHxcclxuXHRcdFx0aHJlZi5pbmRleE9mKCAnamF2YXNjcmlwdDonICkgPT09IDAgfHxcclxuXHRcdFx0aHJlZi5pbmRleE9mKCAnbWFpbHRvOicgKSA9PT0gMCB8fFxyXG5cdFx0XHRocmVmLmluZGV4T2YoICd0ZWw6JyApID09PSAwXHJcblx0XHQpO1xyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gZml4X3RhcmdldChhKSB7XHJcblx0XHRpZiAoICEgYSApIHJldHVybjtcclxuXHRcdGlmICggaXNfcHNldWRvX2xpbmsoIGEgKSB8fCBhLmhhc0F0dHJpYnV0ZSggJ2RhdGEtd3Atbm8tYmxhbmsnICkgKSB7XHJcblx0XHRcdGEudGFyZ2V0ID0gJ19zZWxmJztcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIGluaXRfZml4KCkge1xyXG5cdFx0Ly8gT3B0aW9uYWw6IGNsZWFuIHVwIGN1cnJlbnQgRE9NIChoYXJtbGVzc+KAlGFmZmVjdHMgb25seSBwc2V1ZG8vZGF0YW1hcmtlZCBsaW5rcykuXHJcblx0XHR2YXIgbm9kZXMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCAnYVtocmVmXScgKTtcclxuXHRcdGZvciAoIHZhciBpID0gMDsgaSA8IG5vZGVzLmxlbmd0aDsgaSsrICkgZml4X3RhcmdldCggbm9kZXNbaV0gKTtcclxuXHJcblx0XHQvLyBMYXRlIGJ1YmJsZS1waGFzZSBsaXN0ZW5lcnMgKHJ1biBhZnRlciBQbGF5Z3JvdW5kJ3MgaGFuZGxlcnMpXHJcblx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCAnY2xpY2snLCBmdW5jdGlvbiAoZSkge1xyXG5cdFx0XHR2YXIgYSA9IGUudGFyZ2V0ICYmIGUudGFyZ2V0LmNsb3Nlc3QgPyBlLnRhcmdldC5jbG9zZXN0KCAnYVtocmVmXScgKSA6IG51bGw7XHJcblx0XHRcdGlmICggYSApIGZpeF90YXJnZXQoIGEgKTtcclxuXHRcdH0sIGZhbHNlICk7XHJcblxyXG5cdFx0ZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lciggJ2ZvY3VzaW4nLCBmdW5jdGlvbiAoZSkge1xyXG5cdFx0XHR2YXIgYSA9IGUudGFyZ2V0ICYmIGUudGFyZ2V0LmNsb3Nlc3QgPyBlLnRhcmdldC5jbG9zZXN0KCAnYVtocmVmXScgKSA6IG51bGw7XHJcblx0XHRcdGlmICggYSApIGZpeF90YXJnZXQoIGEgKTtcclxuXHRcdH0gKTtcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIHNjaGVkdWxlX2luaXQoKSB7XHJcblx0XHRpZiAoICFpc19wbGF5Z3JvdW5kX29yaWdpbigpICkgcmV0dXJuO1xyXG5cdFx0c2V0VGltZW91dCggaW5pdF9maXgsIDEwMDAgKTsgLy8gZW5zdXJlIHdlIGF0dGFjaCBhZnRlciBQbGF5Z3JvdW5kJ3Mgc2NyaXB0LlxyXG5cdH1cclxuXHJcblx0aWYgKCBkb2N1bWVudC5yZWFkeVN0YXRlID09PSAnbG9hZGluZycgKSB7XHJcblx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCAnRE9NQ29udGVudExvYWRlZCcsIHNjaGVkdWxlX2luaXQgKTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0c2NoZWR1bGVfaW5pdCgpO1xyXG5cdH1cclxufSkoKTsiLCJcInVzZSBzdHJpY3RcIjtcclxuLyoqXHJcbiAqID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG4gKlx0aW5jbHVkZXMvX19qcy93cGJjL3dwYmMuanNcclxuICogPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqIERlZXAgQ2xvbmUgb2Ygb2JqZWN0IG9yIGFycmF5XHJcbiAqXHJcbiAqIEBwYXJhbSBvYmpcclxuICogQHJldHVybnMge2FueX1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfY2xvbmVfb2JqKCBvYmogKXtcclxuXHJcblx0cmV0dXJuIEpTT04ucGFyc2UoIEpTT04uc3RyaW5naWZ5KCBvYmogKSApO1xyXG59XHJcblxyXG5cclxuXHJcbi8qKlxyXG4gKiBNYWluIF93cGJjIEpTIG9iamVjdFxyXG4gKi9cclxuXHJcbnZhciBfd3BiYyA9IChmdW5jdGlvbiAoIG9iaiwgJCkge1xyXG5cclxuXHQvLyBTZWN1cmUgcGFyYW1ldGVycyBmb3IgQWpheFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHBfc2VjdXJlID0gb2JqLnNlY3VyaXR5X29iaiA9IG9iai5zZWN1cml0eV9vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR1c2VyX2lkOiAwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRub25jZSAgOiAnJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bG9jYWxlIDogJydcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgfTtcclxuXHRvYmouc2V0X3NlY3VyZV9wYXJhbSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5LCBwYXJhbV92YWwgKSB7XHJcblx0XHRwX3NlY3VyZVsgcGFyYW1fa2V5IF0gPSBwYXJhbV92YWw7XHJcblx0fTtcclxuXHJcblx0b2JqLmdldF9zZWN1cmVfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSApIHtcclxuXHRcdHJldHVybiBwX3NlY3VyZVsgcGFyYW1fa2V5IF07XHJcblx0fTtcclxuXHJcblxyXG5cdC8vIENhbGVuZGFycyBcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgcF9jYWxlbmRhcnMgPSBvYmouY2FsZW5kYXJzX29iaiA9IG9iai5jYWxlbmRhcnNfb2JqIHx8IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gc29ydCAgICAgICAgICAgIDogXCJib29raW5nX2lkXCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIHNvcnRfdHlwZSAgICAgICA6IFwiREVTQ1wiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBwYWdlX251bSAgICAgICAgOiAxLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBwYWdlX2l0ZW1zX2NvdW50OiAxMCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gY3JlYXRlX2RhdGUgICAgIDogXCJcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8ga2V5d29yZCAgICAgICAgIDogXCJcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gc291cmNlICAgICAgICAgIDogXCJcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqICBDaGVjayBpZiBjYWxlbmRhciBmb3Igc3BlY2lmaWMgYm9va2luZyByZXNvdXJjZSBkZWZpbmVkICAgOjogICB0cnVlIHwgZmFsc2VcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcclxuXHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHQgKi9cclxuXHRvYmouY2FsZW5kYXJfX2lzX2RlZmluZWQgPSBmdW5jdGlvbiAoIHJlc291cmNlX2lkICkge1xyXG5cclxuXHRcdHJldHVybiAoJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiggcF9jYWxlbmRhcnNbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXSApICk7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogIENyZWF0ZSBDYWxlbmRhciBpbml0aWFsaXppbmdcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcclxuXHQgKi9cclxuXHRvYmouY2FsZW5kYXJfX2luaXQgPSBmdW5jdGlvbiAoIHJlc291cmNlX2lkICkge1xyXG5cclxuXHRcdHBfY2FsZW5kYXJzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF0gPSB7fTtcclxuXHRcdHBfY2FsZW5kYXJzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bICdpZCcgXSA9IHJlc291cmNlX2lkO1xyXG5cdFx0cF9jYWxlbmRhcnNbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXVsgJ3BlbmRpbmdfZGF5c19zZWxlY3RhYmxlJyBdID0gZmFsc2U7XHJcblxyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIENoZWNrICBpZiB0aGUgdHlwZSBvZiB0aGlzIHByb3BlcnR5ICBpcyBJTlRcclxuXHQgKiBAcGFyYW0gcHJvcGVydHlfbmFtZVxyXG5cdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdCAqL1xyXG5cdG9iai5jYWxlbmRhcl9faXNfcHJvcF9pbnQgPSBmdW5jdGlvbiAoIHByb3BlcnR5X25hbWUgKSB7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS45LjAuMjkuXHJcblxyXG5cdFx0dmFyIHBfY2FsZW5kYXJfaW50X3Byb3BlcnRpZXMgPSBbJ2R5bmFtaWNfX2RheXNfbWluJywgJ2R5bmFtaWNfX2RheXNfbWF4JywgJ2ZpeGVkX19kYXlzX251bSddO1xyXG5cclxuXHRcdHZhciBpc19pbmNsdWRlID0gcF9jYWxlbmRhcl9pbnRfcHJvcGVydGllcy5pbmNsdWRlcyggcHJvcGVydHlfbmFtZSApO1xyXG5cclxuXHRcdHJldHVybiBpc19pbmNsdWRlO1xyXG5cdH07XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBTZXQgcGFyYW1zIGZvciBhbGwgIGNhbGVuZGFyc1xyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IGNhbGVuZGFyc19vYmpcdFx0T2JqZWN0IHsgY2FsZW5kYXJfMToge30gfVxyXG5cdCAqIFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCBjYWxlbmRhcl8zOiB7fSwgLi4uIH1cclxuXHQgKi9cclxuXHRvYmouY2FsZW5kYXJzX2FsbF9fc2V0ID0gZnVuY3Rpb24gKCBjYWxlbmRhcnNfb2JqICkge1xyXG5cdFx0cF9jYWxlbmRhcnMgPSBjYWxlbmRhcnNfb2JqO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIEdldCBib29raW5ncyBpbiBhbGwgY2FsZW5kYXJzXHJcblx0ICpcclxuXHQgKiBAcmV0dXJucyB7b2JqZWN0fHt9fVxyXG5cdCAqL1xyXG5cdG9iai5jYWxlbmRhcnNfYWxsX19nZXQgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRyZXR1cm4gcF9jYWxlbmRhcnM7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogR2V0IGNhbGVuZGFyIG9iamVjdCAgIDo6ICAgeyBpZDogMSwg4oCmIH1cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcdFx0XHRcdCAgJzInXHJcblx0ICogQHJldHVybnMge29iamVjdHxib29sZWFufVx0XHRcdFx0XHR7IGlkOiAyICzigKYgfVxyXG5cdCAqL1xyXG5cdG9iai5jYWxlbmRhcl9fZ2V0X3BhcmFtZXRlcnMgPSBmdW5jdGlvbiAoIHJlc291cmNlX2lkICkge1xyXG5cclxuXHRcdGlmICggb2JqLmNhbGVuZGFyX19pc19kZWZpbmVkKCByZXNvdXJjZV9pZCApICl7XHJcblxyXG5cdFx0XHRyZXR1cm4gcF9jYWxlbmRhcnNbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBTZXQgY2FsZW5kYXIgb2JqZWN0ICAgOjogICB7IGRhdGVzOiAgT2JqZWN0IHsgXCIyMDIzLTA3LTIxXCI6IHvigKZ9LCBcIjIwMjMtMDctMjJcIjoge+KApn0sIFwiMjAyMy0wNy0yM1wiOiB74oCmfSwg4oCmIH1cclxuXHQgKlxyXG5cdCAqIGlmIGNhbGVuZGFyIG9iamVjdCAgbm90IGRlZmluZWQsIHRoZW4gIGl0J3Mgd2lsbCBiZSBkZWZpbmVkIGFuZCBJRCBzZXRcclxuXHQgKiBpZiBjYWxlbmRhciBleGlzdCwgdGhlbiAgc3lzdGVtIHNldCAgYXMgbmV3IG9yIG92ZXJ3cml0ZSBvbmx5IHByb3BlcnRpZXMgZnJvbSBjYWxlbmRhcl9wcm9wZXJ0eV9vYmogcGFyYW1ldGVyLCAgYnV0IG90aGVyIHByb3BlcnRpZXMgd2lsbCBiZSBleGlzdGVkIGFuZCBub3Qgb3ZlcndyaXRlLCBsaWtlICdpZCdcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcdFx0XHRcdCAgJzInXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IGNhbGVuZGFyX3Byb3BlcnR5X29ialx0XHRcdFx0XHQgIHsgIGRhdGVzOiAgT2JqZWN0IHsgXCIyMDIzLTA3LTIxXCI6IHvigKZ9LCBcIjIwMjMtMDctMjJcIjoge+KApn0sIFwiMjAyMy0wNy0yM1wiOiB74oCmfSwg4oCmIH0gIH1cclxuXHQgKiBAcGFyYW0ge2Jvb2xlYW59IGlzX2NvbXBsZXRlX292ZXJ3cml0ZVx0XHQgIGlmICd0cnVlJyAoZGVmYXVsdDogJ2ZhbHNlJyksICB0aGVuICBvbmx5IG92ZXJ3cml0ZSBvciBhZGQgIG5ldyBwcm9wZXJ0aWVzIGluICBjYWxlbmRhcl9wcm9wZXJ0eV9vYmpcclxuXHQgKiBAcmV0dXJucyB7Kn1cclxuXHQgKlxyXG5cdCAqIEV4YW1wbGVzOlxyXG5cdCAqXHJcblx0ICogQ29tbW9uIHVzYWdlIGluIFBIUDpcclxuXHQgKiAgIFx0XHRcdGVjaG8gXCIgIF93cGJjLmNhbGVuZGFyX19zZXQoICBcIiAuaW50dmFsKCAkcmVzb3VyY2VfaWQgKSAuIFwiLCB7ICdkYXRlcyc6IFwiIC4gd3BfanNvbl9lbmNvZGUoICRhdmFpbGFiaWxpdHlfcGVyX2RheXNfYXJyICkgLiBcIiB9ICk7XCI7XHJcblx0ICovXHJcblx0b2JqLmNhbGVuZGFyX19zZXRfcGFyYW1ldGVycyA9IGZ1bmN0aW9uICggcmVzb3VyY2VfaWQsIGNhbGVuZGFyX3Byb3BlcnR5X29iaiwgaXNfY29tcGxldGVfb3ZlcndyaXRlID0gZmFsc2UgICkge1xyXG5cclxuXHRcdGlmICggKCFvYmouY2FsZW5kYXJfX2lzX2RlZmluZWQoIHJlc291cmNlX2lkICkpIHx8ICh0cnVlID09PSBpc19jb21wbGV0ZV9vdmVyd3JpdGUpICl7XHJcblx0XHRcdG9iai5jYWxlbmRhcl9faW5pdCggcmVzb3VyY2VfaWQgKTtcclxuXHRcdH1cclxuXHJcblx0XHRmb3IgKCB2YXIgcHJvcF9uYW1lIGluIGNhbGVuZGFyX3Byb3BlcnR5X29iaiApe1xyXG5cclxuXHRcdFx0cF9jYWxlbmRhcnNbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXVsgcHJvcF9uYW1lIF0gPSBjYWxlbmRhcl9wcm9wZXJ0eV9vYmpbIHByb3BfbmFtZSBdO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBwX2NhbGVuZGFyc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIFNldCBwcm9wZXJ0eSAgdG8gIGNhbGVuZGFyXHJcblx0ICogQHBhcmFtIHJlc291cmNlX2lkXHRcIjFcIlxyXG5cdCAqIEBwYXJhbSBwcm9wX25hbWVcdFx0bmFtZSBvZiBwcm9wZXJ0eVxyXG5cdCAqIEBwYXJhbSBwcm9wX3ZhbHVlXHR2YWx1ZSBvZiBwcm9wZXJ0eVxyXG5cdCAqIEByZXR1cm5zIHsqfVx0XHRcdGNhbGVuZGFyIG9iamVjdFxyXG5cdCAqL1xyXG5cdG9iai5jYWxlbmRhcl9fc2V0X3BhcmFtX3ZhbHVlID0gZnVuY3Rpb24gKCByZXNvdXJjZV9pZCwgcHJvcF9uYW1lLCBwcm9wX3ZhbHVlICkge1xyXG5cclxuXHRcdGlmICggKCFvYmouY2FsZW5kYXJfX2lzX2RlZmluZWQoIHJlc291cmNlX2lkICkpICl7XHJcblx0XHRcdG9iai5jYWxlbmRhcl9faW5pdCggcmVzb3VyY2VfaWQgKTtcclxuXHRcdH1cclxuXHJcblx0XHRwX2NhbGVuZGFyc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyBwcm9wX25hbWUgXSA9IHByb3BfdmFsdWU7XHJcblxyXG5cdFx0cmV0dXJuIHBfY2FsZW5kYXJzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF07XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogIEdldCBjYWxlbmRhciBwcm9wZXJ0eSB2YWx1ZSAgIFx0OjogICBtaXhlZCB8IG51bGxcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gIHJlc291cmNlX2lkXHRcdCcxJ1xyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBwcm9wX25hbWVcdFx0XHQnc2VsZWN0aW9uX21vZGUnXHJcblx0ICogQHJldHVybnMgeyp8bnVsbH1cdFx0XHRcdFx0bWl4ZWQgfCBudWxsXHJcblx0ICovXHJcblx0b2JqLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUgPSBmdW5jdGlvbiggcmVzb3VyY2VfaWQsIHByb3BfbmFtZSApe1xyXG5cclxuXHRcdGlmIChcclxuXHRcdFx0ICAgKCBvYmouY2FsZW5kYXJfX2lzX2RlZmluZWQoIHJlc291cmNlX2lkICkgKVxyXG5cdFx0XHQmJiAoICd1bmRlZmluZWQnICE9PSB0eXBlb2YgKCBwX2NhbGVuZGFyc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyBwcm9wX25hbWUgXSApIClcclxuXHRcdCl7XHJcblx0XHRcdC8vIEZpeEluOiA5LjkuMC4yOS5cclxuXHRcdFx0aWYgKCBvYmouY2FsZW5kYXJfX2lzX3Byb3BfaW50KCBwcm9wX25hbWUgKSApe1xyXG5cdFx0XHRcdHBfY2FsZW5kYXJzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bIHByb3BfbmFtZSBdID0gcGFyc2VJbnQoIHBfY2FsZW5kYXJzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bIHByb3BfbmFtZSBdICk7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuICBwX2NhbGVuZGFyc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyBwcm9wX25hbWUgXTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gbnVsbDtcdFx0Ly8gSWYgc29tZSBwcm9wZXJ0eSBub3QgZGVmaW5lZCwgdGhlbiBudWxsO1xyXG5cdH07XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblxyXG5cdC8vIEJvb2tpbmdzIFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBwX2Jvb2tpbmdzID0gb2JqLmJvb2tpbmdzX29iaiA9IG9iai5ib29raW5nc19vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIGNhbGVuZGFyXzE6IE9iamVjdCB7XHJcbiBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vXHRcdFx0XHRcdFx0ICAgaWQ6ICAgICAxXHJcbiBcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vXHRcdFx0XHRcdFx0ICwgZGF0ZXM6ICBPYmplY3QgeyBcIjIwMjMtMDctMjFcIjoge+KApn0sIFwiMjAyMy0wNy0yMlwiOiB74oCmfSwgXCIyMDIzLTA3LTIzXCI6IHvigKZ9LCDigKZcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqICBDaGVjayBpZiBib29raW5ncyBmb3Igc3BlY2lmaWMgYm9va2luZyByZXNvdXJjZSBkZWZpbmVkICAgOjogICB0cnVlIHwgZmFsc2VcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcclxuXHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHQgKi9cclxuXHRvYmouYm9va2luZ3NfaW5fY2FsZW5kYXJfX2lzX2RlZmluZWQgPSBmdW5jdGlvbiAoIHJlc291cmNlX2lkICkge1xyXG5cclxuXHRcdHJldHVybiAoJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiggcF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdICkgKTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgYm9va2luZ3MgY2FsZW5kYXIgb2JqZWN0ICAgOjogICB7IGlkOiAxICwgZGF0ZXM6ICBPYmplY3QgeyBcIjIwMjMtMDctMjFcIjoge+KApn0sIFwiMjAyMy0wNy0yMlwiOiB74oCmfSwgXCIyMDIzLTA3LTIzXCI6IHvigKZ9LCDigKYgfVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd8aW50fSByZXNvdXJjZV9pZFx0XHRcdFx0ICAnMidcclxuXHQgKiBAcmV0dXJucyB7b2JqZWN0fGJvb2xlYW59XHRcdFx0XHRcdHsgaWQ6IDIgLCBkYXRlczogIE9iamVjdCB7IFwiMjAyMy0wNy0yMVwiOiB74oCmfSwgXCIyMDIzLTA3LTIyXCI6IHvigKZ9LCBcIjIwMjMtMDctMjNcIjoge+KApn0sIOKApiB9XHJcblx0ICovXHJcblx0b2JqLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXQgPSBmdW5jdGlvbiggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRpZiAoIG9iai5ib29raW5nc19pbl9jYWxlbmRhcl9faXNfZGVmaW5lZCggcmVzb3VyY2VfaWQgKSApe1xyXG5cclxuXHRcdFx0cmV0dXJuIHBfYm9va2luZ3NbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBTZXQgYm9va2luZ3MgY2FsZW5kYXIgb2JqZWN0ICAgOjogICB7IGRhdGVzOiAgT2JqZWN0IHsgXCIyMDIzLTA3LTIxXCI6IHvigKZ9LCBcIjIwMjMtMDctMjJcIjoge+KApn0sIFwiMjAyMy0wNy0yM1wiOiB74oCmfSwg4oCmIH1cclxuXHQgKlxyXG5cdCAqIGlmIGNhbGVuZGFyIG9iamVjdCAgbm90IGRlZmluZWQsIHRoZW4gIGl0J3Mgd2lsbCBiZSBkZWZpbmVkIGFuZCBJRCBzZXRcclxuXHQgKiBpZiBjYWxlbmRhciBleGlzdCwgdGhlbiAgc3lzdGVtIHNldCAgYXMgbmV3IG9yIG92ZXJ3cml0ZSBvbmx5IHByb3BlcnRpZXMgZnJvbSBjYWxlbmRhcl9vYmogcGFyYW1ldGVyLCAgYnV0IG90aGVyIHByb3BlcnRpZXMgd2lsbCBiZSBleGlzdGVkIGFuZCBub3Qgb3ZlcndyaXRlLCBsaWtlICdpZCdcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcdFx0XHRcdCAgJzInXHJcblx0ICogQHBhcmFtIHtvYmplY3R9IGNhbGVuZGFyX29ialx0XHRcdFx0XHQgIHsgIGRhdGVzOiAgT2JqZWN0IHsgXCIyMDIzLTA3LTIxXCI6IHvigKZ9LCBcIjIwMjMtMDctMjJcIjoge+KApn0sIFwiMjAyMy0wNy0yM1wiOiB74oCmfSwg4oCmIH0gIH1cclxuXHQgKiBAcmV0dXJucyB7Kn1cclxuXHQgKlxyXG5cdCAqIEV4YW1wbGVzOlxyXG5cdCAqXHJcblx0ICogQ29tbW9uIHVzYWdlIGluIFBIUDpcclxuXHQgKiAgIFx0XHRcdGVjaG8gXCIgIF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19zZXQoICBcIiAuaW50dmFsKCAkcmVzb3VyY2VfaWQgKSAuIFwiLCB7ICdkYXRlcyc6IFwiIC4gd3BfanNvbl9lbmNvZGUoICRhdmFpbGFiaWxpdHlfcGVyX2RheXNfYXJyICkgLiBcIiB9ICk7XCI7XHJcblx0ICovXHJcblx0b2JqLmJvb2tpbmdzX2luX2NhbGVuZGFyX19zZXQgPSBmdW5jdGlvbiggcmVzb3VyY2VfaWQsIGNhbGVuZGFyX29iaiApe1xyXG5cclxuXHRcdGlmICggISBvYmouYm9va2luZ3NfaW5fY2FsZW5kYXJfX2lzX2RlZmluZWQoIHJlc291cmNlX2lkICkgKXtcclxuXHRcdFx0cF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdID0ge307XHJcblx0XHRcdHBfYm9va2luZ3NbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXVsgJ2lkJyBdID0gcmVzb3VyY2VfaWQ7XHJcblx0XHR9XHJcblxyXG5cdFx0Zm9yICggdmFyIHByb3BfbmFtZSBpbiBjYWxlbmRhcl9vYmogKXtcclxuXHJcblx0XHRcdHBfYm9va2luZ3NbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXVsgcHJvcF9uYW1lIF0gPSBjYWxlbmRhcl9vYmpbIHByb3BfbmFtZSBdO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBwX2Jvb2tpbmdzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF07XHJcblx0fTtcclxuXHJcblx0Ly8gRGF0ZXNcclxuXHJcblx0LyoqXHJcblx0ICogIEdldCBib29raW5ncyBkYXRhIGZvciBBTEwgRGF0ZXMgaW4gY2FsZW5kYXIgICA6OiAgIGZhbHNlIHwgeyBcIjIwMjMtMDctMjJcIjoge+KApn0sIFwiMjAyMy0wNy0yM1wiOiB74oCmfSwg4oCmIH1cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcdFx0XHQnMSdcclxuXHQgKiBAcmV0dXJucyB7b2JqZWN0fGJvb2xlYW59XHRcdFx0XHRmYWxzZSB8IE9iamVjdCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTA3LTI0XCI6IE9iamVjdCB7IFsnc3VtbWFyeSddWydzdGF0dXNfZm9yX2RheSddOiBcImF2YWlsYWJsZVwiLCBkYXlfYXZhaWxhYmlsaXR5OiAxLCBtYXhfY2FwYWNpdHk6IDEsIOKApiB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTA3LTI2XCI6IE9iamVjdCB7IFsnc3VtbWFyeSddWydzdGF0dXNfZm9yX2RheSddOiBcImZ1bGxfZGF5X2Jvb2tpbmdcIiwgWydzdW1tYXJ5J11bJ3N0YXR1c19mb3JfYm9va2luZ3MnXTogXCJwZW5kaW5nXCIsIGRheV9hdmFpbGFiaWxpdHk6IDAsIOKApiB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTA3LTI5XCI6IE9iamVjdCB7IFsnc3VtbWFyeSddWydzdGF0dXNfZm9yX2RheSddOiBcInJlc291cmNlX2F2YWlsYWJpbGl0eVwiLCBkYXlfYXZhaWxhYmlsaXR5OiAwLCBtYXhfY2FwYWNpdHk6IDEsIOKApiB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTA3LTMwXCI6IHvigKZ9LCBcIjIwMjMtMDctMzFcIjoge+KApn0sIOKAplxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0ICovXHJcblx0b2JqLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZGF0ZXMgPSBmdW5jdGlvbiggcmVzb3VyY2VfaWQpe1xyXG5cclxuXHRcdGlmIChcclxuXHRcdFx0ICAgKCBvYmouYm9va2luZ3NfaW5fY2FsZW5kYXJfX2lzX2RlZmluZWQoIHJlc291cmNlX2lkICkgKVxyXG5cdFx0XHQmJiAoICd1bmRlZmluZWQnICE9PSB0eXBlb2YgKCBwX2Jvb2tpbmdzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bICdkYXRlcycgXSApIClcclxuXHRcdCl7XHJcblx0XHRcdHJldHVybiAgcF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyAnZGF0ZXMnIF07XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIGZhbHNlO1x0XHQvLyBJZiBzb21lIHByb3BlcnR5IG5vdCBkZWZpbmVkLCB0aGVuIGZhbHNlO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqIFNldCBib29raW5ncyBkYXRlcyBpbiBjYWxlbmRhciBvYmplY3QgICA6OiAgICB7IFwiMjAyMy0wNy0yMVwiOiB74oCmfSwgXCIyMDIzLTA3LTIyXCI6IHvigKZ9LCBcIjIwMjMtMDctMjNcIjoge+KApn0sIOKApiB9XHJcblx0ICpcclxuXHQgKiBpZiBjYWxlbmRhciBvYmplY3QgIG5vdCBkZWZpbmVkLCB0aGVuICBpdCdzIHdpbGwgYmUgZGVmaW5lZCBhbmQgJ2lkJywgJ2RhdGVzJyBzZXRcclxuXHQgKiBpZiBjYWxlbmRhciBleGlzdCwgdGhlbiBzeXN0ZW0gYWRkIGEgIG5ldyBvciBvdmVyd3JpdGUgb25seSBkYXRlcyBmcm9tIGRhdGVzX29iaiBwYXJhbWV0ZXIsXHJcblx0ICogYnV0IG90aGVyIGRhdGVzIG5vdCBmcm9tIHBhcmFtZXRlciBkYXRlc19vYmogd2lsbCBiZSBleGlzdGVkIGFuZCBub3Qgb3ZlcndyaXRlLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd8aW50fSByZXNvdXJjZV9pZFx0XHRcdFx0ICAnMidcclxuXHQgKiBAcGFyYW0ge29iamVjdH0gZGF0ZXNfb2JqXHRcdFx0XHRcdCAgeyBcIjIwMjMtMDctMjFcIjoge+KApn0sIFwiMjAyMy0wNy0yMlwiOiB74oCmfSwgXCIyMDIzLTA3LTIzXCI6IHvigKZ9LCDigKYgfVxyXG5cdCAqIEBwYXJhbSB7Ym9vbGVhbn0gaXNfY29tcGxldGVfb3ZlcndyaXRlXHRcdCAgaWYgZmFsc2UsICB0aGVuICBvbmx5IG92ZXJ3cml0ZSBvciBhZGQgIGRhdGVzIGZyb20gXHRkYXRlc19vYmpcclxuXHQgKiBAcmV0dXJucyB7Kn1cclxuXHQgKlxyXG5cdCAqIEV4YW1wbGVzOlxyXG5cdCAqICAgXHRcdFx0X3dwYmMuYm9va2luZ3NfaW5fY2FsZW5kYXJfX3NldF9kYXRlcyggcmVzb3VyY2VfaWQsIHsgXCIyMDIzLTA3LTIxXCI6IHvigKZ9LCBcIjIwMjMtMDctMjJcIjoge+KApn0sIOKApiB9ICApO1x0XHQ8LSAgIG92ZXJ3cml0ZSBBTEwgZGF0ZXNcclxuXHQgKiAgIFx0XHRcdF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19zZXRfZGF0ZXMoIHJlc291cmNlX2lkLCB7IFwiMjAyMy0wNy0yMlwiOiB74oCmfSB9LCAgZmFsc2UgICk7XHRcdFx0XHRcdDwtICAgYWRkIG9yIG92ZXJ3cml0ZSBvbmx5ICBcdFwiMjAyMy0wNy0yMlwiOiB7fVxyXG5cdCAqXHJcblx0ICogQ29tbW9uIHVzYWdlIGluIFBIUDpcclxuXHQgKiAgIFx0XHRcdGVjaG8gXCIgIF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19zZXRfZGF0ZXMoICBcIiAuIGludHZhbCggJHJlc291cmNlX2lkICkgLiBcIiwgIFwiIC4gd3BfanNvbl9lbmNvZGUoICRhdmFpbGFiaWxpdHlfcGVyX2RheXNfYXJyICkgLiBcIiAgKTsgIFwiO1xyXG5cdCAqL1xyXG5cdG9iai5ib29raW5nc19pbl9jYWxlbmRhcl9fc2V0X2RhdGVzID0gZnVuY3Rpb24oIHJlc291cmNlX2lkLCBkYXRlc19vYmogLCBpc19jb21wbGV0ZV9vdmVyd3JpdGUgPSB0cnVlICl7XHJcblxyXG5cdFx0aWYgKCAhb2JqLmJvb2tpbmdzX2luX2NhbGVuZGFyX19pc19kZWZpbmVkKCByZXNvdXJjZV9pZCApICl7XHJcblx0XHRcdG9iai5ib29raW5nc19pbl9jYWxlbmRhcl9fc2V0KCByZXNvdXJjZV9pZCwgeyAnZGF0ZXMnOiB7fSB9ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mIChwX2Jvb2tpbmdzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bICdkYXRlcycgXSkgKXtcclxuXHRcdFx0cF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyAnZGF0ZXMnIF0gPSB7fVxyXG5cdFx0fVxyXG5cclxuXHRcdGlmIChpc19jb21wbGV0ZV9vdmVyd3JpdGUpe1xyXG5cclxuXHRcdFx0Ly8gQ29tcGxldGUgb3ZlcndyaXRlIGFsbCAgYm9va2luZyBkYXRlc1xyXG5cdFx0XHRwX2Jvb2tpbmdzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bICdkYXRlcycgXSA9IGRhdGVzX29iajtcclxuXHRcdH0gZWxzZSB7XHJcblxyXG5cdFx0XHQvLyBBZGQgb25seSAgbmV3IG9yIG92ZXJ3cml0ZSBleGlzdCBib29raW5nIGRhdGVzIGZyb20gIHBhcmFtZXRlci4gQm9va2luZyBkYXRlcyBub3QgZnJvbSAgcGFyYW1ldGVyICB3aWxsICBiZSB3aXRob3V0IGNobmFuZ2VzXHJcblx0XHRcdGZvciAoIHZhciBwcm9wX25hbWUgaW4gZGF0ZXNfb2JqICl7XHJcblxyXG5cdFx0XHRcdHBfYm9va2luZ3NbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXVsnZGF0ZXMnXVsgcHJvcF9uYW1lIF0gPSBkYXRlc19vYmpbIHByb3BfbmFtZSBdO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIHBfYm9va2luZ3NbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXTtcclxuXHR9O1xyXG5cclxuXHJcblx0LyoqXHJcblx0ICogIEdldCBib29raW5ncyBkYXRhIGZvciBzcGVjaWZpYyBkYXRlIGluIGNhbGVuZGFyICAgOjogICBmYWxzZSB8IHsgZGF5X2F2YWlsYWJpbGl0eTogMSwgLi4uIH1cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gcmVzb3VyY2VfaWRcdFx0XHQnMSdcclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gc3FsX2NsYXNzX2RheVx0XHRcdCcyMDIzLTA3LTIxJ1xyXG5cdCAqIEByZXR1cm5zIHtvYmplY3R8Ym9vbGVhbn1cdFx0XHRcdGZhbHNlIHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRkYXlfYXZhaWxhYmlsaXR5OiA0XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG1heF9jYXBhY2l0eTogNFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vICA+PSBCdXNpbmVzcyBMYXJnZVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQyOiBPYmplY3QgeyBpc19kYXlfdW5hdmFpbGFibGU6IGZhbHNlLCBfZGF5X3N0YXR1czogXCJhdmFpbGFibGVcIiB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDEwOiBPYmplY3QgeyBpc19kYXlfdW5hdmFpbGFibGU6IGZhbHNlLCBfZGF5X3N0YXR1czogXCJhdmFpbGFibGVcIiB9XHRcdC8vICA+PSBCdXNpbmVzcyBMYXJnZSAuLi5cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0MTE6IE9iamVjdCB7IGlzX2RheV91bmF2YWlsYWJsZTogZmFsc2UsIF9kYXlfc3RhdHVzOiBcImF2YWlsYWJsZVwiIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0MTI6IE9iamVjdCB7IGlzX2RheV91bmF2YWlsYWJsZTogZmFsc2UsIF9kYXlfc3RhdHVzOiBcImF2YWlsYWJsZVwiIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHQgKi9cclxuXHRvYmouYm9va2luZ3NfaW5fY2FsZW5kYXJfX2dldF9mb3JfZGF0ZSA9IGZ1bmN0aW9uKCByZXNvdXJjZV9pZCwgc3FsX2NsYXNzX2RheSApe1xyXG5cclxuXHRcdGlmIChcclxuXHRcdFx0ICAgKCBvYmouYm9va2luZ3NfaW5fY2FsZW5kYXJfX2lzX2RlZmluZWQoIHJlc291cmNlX2lkICkgKVxyXG5cdFx0XHQmJiAoICd1bmRlZmluZWQnICE9PSB0eXBlb2YgKCBwX2Jvb2tpbmdzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bICdkYXRlcycgXSApIClcclxuXHRcdFx0JiYgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mICggcF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyAnZGF0ZXMnIF1bIHNxbF9jbGFzc19kYXkgXSApIClcclxuXHRcdCl7XHJcblx0XHRcdHJldHVybiAgcF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyAnZGF0ZXMnIF1bIHNxbF9jbGFzc19kYXkgXTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gZmFsc2U7XHRcdC8vIElmIHNvbWUgcHJvcGVydHkgbm90IGRlZmluZWQsIHRoZW4gZmFsc2U7XHJcblx0fTtcclxuXHJcblxyXG5cdC8vIEFueSAgUEFSQU1TICAgaW4gYm9va2luZ3NcclxuXHJcblx0LyoqXHJcblx0ICogU2V0IHByb3BlcnR5ICB0byAgYm9va2luZ1xyXG5cdCAqIEBwYXJhbSByZXNvdXJjZV9pZFx0XCIxXCJcclxuXHQgKiBAcGFyYW0gcHJvcF9uYW1lXHRcdG5hbWUgb2YgcHJvcGVydHlcclxuXHQgKiBAcGFyYW0gcHJvcF92YWx1ZVx0dmFsdWUgb2YgcHJvcGVydHlcclxuXHQgKiBAcmV0dXJucyB7Kn1cdFx0XHRib29raW5nIG9iamVjdFxyXG5cdCAqL1xyXG5cdG9iai5ib29raW5nX19zZXRfcGFyYW1fdmFsdWUgPSBmdW5jdGlvbiAoIHJlc291cmNlX2lkLCBwcm9wX25hbWUsIHByb3BfdmFsdWUgKSB7XHJcblxyXG5cdFx0aWYgKCAhIG9iai5ib29raW5nc19pbl9jYWxlbmRhcl9faXNfZGVmaW5lZCggcmVzb3VyY2VfaWQgKSApe1xyXG5cdFx0XHRwX2Jvb2tpbmdzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF0gPSB7fTtcclxuXHRcdFx0cF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyAnaWQnIF0gPSByZXNvdXJjZV9pZDtcclxuXHRcdH1cclxuXHJcblx0XHRwX2Jvb2tpbmdzWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bIHByb3BfbmFtZSBdID0gcHJvcF92YWx1ZTtcclxuXHJcblx0XHRyZXR1cm4gcF9ib29raW5nc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdO1xyXG5cdH07XHJcblxyXG5cdC8qKlxyXG5cdCAqICBHZXQgYm9va2luZyBwcm9wZXJ0eSB2YWx1ZSAgIFx0OjogICBtaXhlZCB8IG51bGxcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfGludH0gIHJlc291cmNlX2lkXHRcdCcxJ1xyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBwcm9wX25hbWVcdFx0XHQnc2VsZWN0aW9uX21vZGUnXHJcblx0ICogQHJldHVybnMgeyp8bnVsbH1cdFx0XHRcdFx0bWl4ZWQgfCBudWxsXHJcblx0ICovXHJcblx0b2JqLmJvb2tpbmdfX2dldF9wYXJhbV92YWx1ZSA9IGZ1bmN0aW9uKCByZXNvdXJjZV9pZCwgcHJvcF9uYW1lICl7XHJcblxyXG5cdFx0aWYgKFxyXG5cdFx0XHQgICAoIG9iai5ib29raW5nc19pbl9jYWxlbmRhcl9faXNfZGVmaW5lZCggcmVzb3VyY2VfaWQgKSApXHJcblx0XHRcdCYmICggJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiAoIHBfYm9va2luZ3NbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXVsgcHJvcF9uYW1lIF0gKSApXHJcblx0XHQpe1xyXG5cdFx0XHRyZXR1cm4gIHBfYm9va2luZ3NbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXVsgcHJvcF9uYW1lIF07XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIG51bGw7XHRcdC8vIElmIHNvbWUgcHJvcGVydHkgbm90IGRlZmluZWQsIHRoZW4gbnVsbDtcclxuXHR9O1xyXG5cclxuXHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBTZXQgYm9va2luZ3MgZm9yIGFsbCAgY2FsZW5kYXJzXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge29iamVjdH0gY2FsZW5kYXJzX29ialx0XHRPYmplY3QgeyBjYWxlbmRhcl8xOiB7IGlkOiAxLCBkYXRlczogT2JqZWN0IHsgXCIyMDIzLTA3LTIyXCI6IHvigKZ9LCBcIjIwMjMtMDctMjNcIjoge+KApn0sIFwiMjAyMy0wNy0yNFwiOiB74oCmfSwg4oCmIH0gfVxyXG5cdCAqIFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCBjYWxlbmRhcl8zOiB7fSwgLi4uIH1cclxuXHQgKi9cclxuXHRvYmouYm9va2luZ3NfaW5fY2FsZW5kYXJzX19zZXRfYWxsID0gZnVuY3Rpb24gKCBjYWxlbmRhcnNfb2JqICkge1xyXG5cdFx0cF9ib29raW5ncyA9IGNhbGVuZGFyc19vYmo7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogR2V0IGJvb2tpbmdzIGluIGFsbCBjYWxlbmRhcnNcclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHtvYmplY3R8e319XHJcblx0ICovXHJcblx0b2JqLmJvb2tpbmdzX2luX2NhbGVuZGFyc19fZ2V0X2FsbCA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdHJldHVybiBwX2Jvb2tpbmdzO1xyXG5cdH07XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblxyXG5cclxuXHJcblx0Ly8gU2Vhc29ucyBcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgcF9zZWFzb25zID0gb2JqLnNlYXNvbnNfb2JqID0gb2JqLnNlYXNvbnNfb2JqIHx8IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBjYWxlbmRhcl8xOiBPYmplY3Qge1xyXG4gXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvL1x0XHRcdFx0XHRcdCAgIGlkOiAgICAgMVxyXG4gXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvL1x0XHRcdFx0XHRcdCAsIGRhdGVzOiAgT2JqZWN0IHsgXCIyMDIzLTA3LTIxXCI6IHvigKZ9LCBcIjIwMjMtMDctMjJcIjoge+KApn0sIFwiMjAyMy0wNy0yM1wiOiB74oCmfSwg4oCmXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gfVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBBZGQgc2Vhc29uIG5hbWVzIGZvciBkYXRlcyBpbiBjYWxlbmRhciBvYmplY3QgICA6OiAgICB7IFwiMjAyMy0wNy0yMVwiOiBbICd3cGJjX3NlYXNvbl9zZXB0ZW1iZXJfMjAyMycsICd3cGJjX3NlYXNvbl9zZXB0ZW1iZXJfMjAyNCcgXSwgXCIyMDIzLTA3LTIyXCI6IFsuLi5dLCAuLi4gfVxyXG5cdCAqXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge3N0cmluZ3xpbnR9IHJlc291cmNlX2lkXHRcdFx0XHQgICcyJ1xyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBkYXRlc19vYmpcdFx0XHRcdFx0ICB7IFwiMjAyMy0wNy0yMVwiOiB74oCmfSwgXCIyMDIzLTA3LTIyXCI6IHvigKZ9LCBcIjIwMjMtMDctMjNcIjoge+KApn0sIOKApiB9XHJcblx0ICogQHBhcmFtIHtib29sZWFufSBpc19jb21wbGV0ZV9vdmVyd3JpdGVcdFx0ICBpZiBmYWxzZSwgIHRoZW4gIG9ubHkgIGFkZCAgZGF0ZXMgZnJvbSBcdGRhdGVzX29ialxyXG5cdCAqIEByZXR1cm5zIHsqfVxyXG5cdCAqXHJcblx0ICogRXhhbXBsZXM6XHJcblx0ICogICBcdFx0XHRfd3BiYy5zZWFzb25zX19zZXQoIHJlc291cmNlX2lkLCB7IFwiMjAyMy0wNy0yMVwiOiBbICd3cGJjX3NlYXNvbl9zZXB0ZW1iZXJfMjAyMycsICd3cGJjX3NlYXNvbl9zZXB0ZW1iZXJfMjAyNCcgXSwgXCIyMDIzLTA3LTIyXCI6IFsuLi5dLCAuLi4gfSAgKTtcclxuXHQgKi9cclxuXHRvYmouc2Vhc29uc19fc2V0ID0gZnVuY3Rpb24oIHJlc291cmNlX2lkLCBkYXRlc19vYmogLCBpc19jb21wbGV0ZV9vdmVyd3JpdGUgPSBmYWxzZSApe1xyXG5cclxuXHRcdGlmICggJ3VuZGVmaW5lZCcgPT09IHR5cGVvZiAocF9zZWFzb25zWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF0pICl7XHJcblx0XHRcdHBfc2Vhc29uc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdID0ge307XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCBpc19jb21wbGV0ZV9vdmVyd3JpdGUgKXtcclxuXHJcblx0XHRcdC8vIENvbXBsZXRlIG92ZXJ3cml0ZSBhbGwgIHNlYXNvbiBkYXRlc1xyXG5cdFx0XHRwX3NlYXNvbnNbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXSA9IGRhdGVzX29iajtcclxuXHJcblx0XHR9IGVsc2Uge1xyXG5cclxuXHRcdFx0Ly8gQWRkIG9ubHkgIG5ldyBvciBvdmVyd3JpdGUgZXhpc3QgYm9va2luZyBkYXRlcyBmcm9tICBwYXJhbWV0ZXIuIEJvb2tpbmcgZGF0ZXMgbm90IGZyb20gIHBhcmFtZXRlciAgd2lsbCAgYmUgd2l0aG91dCBjaG5hbmdlc1xyXG5cdFx0XHRmb3IgKCB2YXIgcHJvcF9uYW1lIGluIGRhdGVzX29iaiApe1xyXG5cclxuXHRcdFx0XHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHBfc2Vhc29uc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyBwcm9wX25hbWUgXSkgKXtcclxuXHRcdFx0XHRcdHBfc2Vhc29uc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyBwcm9wX25hbWUgXSA9IFtdO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRmb3IgKCB2YXIgc2Vhc29uX25hbWVfa2V5IGluIGRhdGVzX29ialsgcHJvcF9uYW1lIF0gKXtcclxuXHRcdFx0XHRcdHBfc2Vhc29uc1sgJ2NhbGVuZGFyXycgKyByZXNvdXJjZV9pZCBdWyBwcm9wX25hbWUgXS5wdXNoKCBkYXRlc19vYmpbIHByb3BfbmFtZSBdWyBzZWFzb25fbmFtZV9rZXkgXSApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBwX3NlYXNvbnNbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXTtcclxuXHR9O1xyXG5cclxuXHJcblx0LyoqXHJcblx0ICogIEdldCBib29raW5ncyBkYXRhIGZvciBzcGVjaWZpYyBkYXRlIGluIGNhbGVuZGFyICAgOjogICBbXSB8IFsgJ3dwYmNfc2Vhc29uX3NlcHRlbWJlcl8yMDIzJywgJ3dwYmNfc2Vhc29uX3NlcHRlbWJlcl8yMDI0JyBdXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge3N0cmluZ3xpbnR9IHJlc291cmNlX2lkXHRcdFx0JzEnXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IHNxbF9jbGFzc19kYXlcdFx0XHQnMjAyMy0wNy0yMSdcclxuXHQgKiBAcmV0dXJucyB7b2JqZWN0fGJvb2xlYW59XHRcdFx0XHRbXSAgfCAgWyAnd3BiY19zZWFzb25fc2VwdGVtYmVyXzIwMjMnLCAnd3BiY19zZWFzb25fc2VwdGVtYmVyXzIwMjQnIF1cclxuXHQgKi9cclxuXHRvYmouc2Vhc29uc19fZ2V0X2Zvcl9kYXRlID0gZnVuY3Rpb24oIHJlc291cmNlX2lkLCBzcWxfY2xhc3NfZGF5ICl7XHJcblxyXG5cdFx0aWYgKFxyXG5cdFx0XHQgICAoICd1bmRlZmluZWQnICE9PSB0eXBlb2YgKCBwX3NlYXNvbnNbICdjYWxlbmRhcl8nICsgcmVzb3VyY2VfaWQgXSApIClcclxuXHRcdFx0JiYgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mICggcF9zZWFzb25zWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bIHNxbF9jbGFzc19kYXkgXSApIClcclxuXHRcdCl7XHJcblx0XHRcdHJldHVybiAgcF9zZWFzb25zWyAnY2FsZW5kYXJfJyArIHJlc291cmNlX2lkIF1bIHNxbF9jbGFzc19kYXkgXTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gW107XHRcdC8vIElmIG5vdCBkZWZpbmVkLCB0aGVuIFtdO1xyXG5cdH07XHJcblxyXG5cclxuXHQvLyBPdGhlciBwYXJhbWV0ZXJzIFx0XHRcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBwX290aGVyID0gb2JqLm90aGVyX29iaiA9IG9iai5vdGhlcl9vYmogfHwgeyB9O1xyXG5cclxuXHRvYmouc2V0X290aGVyX3BhcmFtID0gZnVuY3Rpb24gKCBwYXJhbV9rZXksIHBhcmFtX3ZhbCApIHtcclxuXHRcdHBfb3RoZXJbIHBhcmFtX2tleSBdID0gcGFyYW1fdmFsO1xyXG5cdH07XHJcblxyXG5cdG9iai5nZXRfb3RoZXJfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSApIHtcclxuXHRcdHJldHVybiBwX290aGVyWyBwYXJhbV9rZXkgXTtcclxuXHR9O1xyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgYWxsIG90aGVyIHBhcmFtc1xyXG5cdCAqXHJcblx0ICogQHJldHVybnMge29iamVjdHx7fX1cclxuXHQgKi9cclxuXHRvYmouZ2V0X290aGVyX3BhcmFtX19hbGwgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRyZXR1cm4gcF9vdGhlcjtcclxuXHR9O1xyXG5cclxuXHQvLyBNZXNzYWdlcyBcdFx0XHQgICAgICAgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBwX21lc3NhZ2VzID0gb2JqLm1lc3NhZ2VzX29iaiA9IG9iai5tZXNzYWdlc19vYmogfHwgeyB9O1xyXG5cclxuXHRvYmouc2V0X21lc3NhZ2UgPSBmdW5jdGlvbiAoIHBhcmFtX2tleSwgcGFyYW1fdmFsICkge1xyXG5cdFx0cF9tZXNzYWdlc1sgcGFyYW1fa2V5IF0gPSBwYXJhbV92YWw7XHJcblx0fTtcclxuXHJcblx0b2JqLmdldF9tZXNzYWdlID0gZnVuY3Rpb24gKCBwYXJhbV9rZXkgKSB7XHJcblx0XHRyZXR1cm4gcF9tZXNzYWdlc1sgcGFyYW1fa2V5IF07XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogR2V0IGFsbCBvdGhlciBwYXJhbXNcclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHtvYmplY3R8e319XHJcblx0ICovXHJcblx0b2JqLmdldF9tZXNzYWdlc19fYWxsID0gZnVuY3Rpb24gKCkge1xyXG5cdFx0cmV0dXJuIHBfbWVzc2FnZXM7XHJcblx0fTtcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0cmV0dXJuIG9iajtcclxuXHJcbn0oIF93cGJjIHx8IHt9LCBqUXVlcnkgKSk7XHJcbiIsIndpbmRvdy5fX1dQQkNfREVWID0gdHJ1ZTtcclxuXHJcbi8qKlxyXG4gKiBFeHRlbmQgX3dwYmMgd2l0aCAgbmV3IG1ldGhvZHNcclxuICpcclxuICogQHR5cGUgeyp8e319XHJcbiAqIEBwcml2YXRlXHJcbiAqL1xyXG5fd3BiYyA9IChmdW5jdGlvbiAob2JqLCAkKSB7XHJcblxyXG5cdC8qKlxyXG5cdCAqIERldiBsb2dnZXIgKG5vLW9wIHVubGVzcyB3aW5kb3cuX19XUEJDX0RFViA9IHRydWUpXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7Knx7d2FybjogKGZ1bmN0aW9uKCosICosICopOiB2b2lkKSwgZXJyb3I6IChmdW5jdGlvbigqLCAqLCAqKTogdm9pZCksIG9uY2U6IG9iai5kZXYub25jZSwgdHJ5OiAoKGZ1bmN0aW9uKCosICosICopOiAoKnx1bmRlZmluZWQpKXwqKX19XHJcblx0ICovXHJcblx0b2JqLmRldiA9IG9iai5kZXYgfHwgKCgpID0+IHtcclxuXHRcdGNvbnN0IHNlZW4gICAgPSBuZXcgU2V0KCk7XHJcblx0XHRjb25zdCBlbmFibGVkID0gKCkgPT4gISF3aW5kb3cuX19XUEJDX0RFVjtcclxuXHJcblx0XHRmdW5jdGlvbiBvdXQobGV2ZWwsIGNvZGUsIG1zZywgZXh0cmEpIHtcclxuXHRcdFx0aWYgKCAhZW5hYmxlZCgpICkgcmV0dXJuO1xyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdChjb25zb2xlW2xldmVsXSB8fCBjb25zb2xlLndhcm4pKCBgW1dQQkNdWyR7Y29kZX1dICR7bXNnfWAsIGV4dHJhID8/ICcnICk7XHJcblx0XHRcdH0gY2F0Y2gge1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIHtcclxuXHRcdFx0bG9nICA6IChjb2RlLCBtc2csIGV4dHJhKSA9PiBvdXQoJ2xvZycsICAgY29kZSwgbXNnLCBleHRyYSksXHJcblx0XHRcdGRlYnVnOiAoY29kZSwgbXNnLCBleHRyYSkgPT4gb3V0KCdkZWJ1ZycsIGNvZGUsIG1zZywgZXh0cmEpLFxyXG5cdFx0XHR3YXJuIDogKGNvZGUsIG1zZywgZXh0cmEpID0+IG91dCggJ3dhcm4nLCBjb2RlLCBtc2csIGV4dHJhICksXHJcblx0XHRcdGVycm9yOiAoY29kZSwgZXJyT3JNc2csIGV4dHJhKSA9PlxyXG5cdFx0XHRcdG91dCggJ2Vycm9yJywgY29kZSxcclxuXHRcdFx0XHRcdGVyck9yTXNnIGluc3RhbmNlb2YgRXJyb3IgPyBlcnJPck1zZy5tZXNzYWdlIDogU3RyaW5nKCBlcnJPck1zZyApLFxyXG5cdFx0XHRcdFx0ZXJyT3JNc2cgaW5zdGFuY2VvZiBFcnJvciA/IGVyck9yTXNnIDogZXh0cmEgKSxcclxuXHRcdFx0b25jZSA6IChjb2RlLCBtc2csIGV4dHJhKSA9PiB7XHJcblx0XHRcdFx0aWYgKCAhZW5hYmxlZCgpICkgcmV0dXJuO1xyXG5cdFx0XHRcdGNvbnN0IGtleSA9IGAke2NvZGV9fCR7bXNnfWA7XHJcblx0XHRcdFx0aWYgKCBzZWVuLmhhcygga2V5ICkgKSByZXR1cm47XHJcblx0XHRcdFx0c2Vlbi5hZGQoIGtleSApO1xyXG5cdFx0XHRcdG91dCggJ2Vycm9yJywgY29kZSwgbXNnLCBleHRyYSApO1xyXG5cdFx0XHR9LFxyXG5cdFx0XHR0cnkgIDogKGNvZGUsIGZuLCBleHRyYSkgPT4ge1xyXG5cdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRyZXR1cm4gZm4oKTtcclxuXHRcdFx0XHR9IGNhdGNoICggZSApIHtcclxuXHRcdFx0XHRcdG91dCggJ2Vycm9yJywgY29kZSwgZSwgZXh0cmEgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblx0fSkoKTtcclxuXHJcblx0Ly8gT3B0aW9uYWw6IGdsb2JhbCB0cmFwcyBpbiBkZXYuXHJcblx0aWYgKCB3aW5kb3cuX19XUEJDX0RFViApIHtcclxuXHRcdHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCAnZXJyb3InLCAoZSkgPT4ge1xyXG5cdFx0XHR0cnkgeyBfd3BiYz8uZGV2Py5lcnJvciggJ0dMT0JBTC1FUlJPUicsIGU/LmVycm9yIHx8IGU/Lm1lc3NhZ2UsIGUgKTsgfSBjYXRjaCAoIF8gKSB7fVxyXG5cdFx0fSApO1xyXG5cdFx0d2luZG93LmFkZEV2ZW50TGlzdGVuZXIoICd1bmhhbmRsZWRyZWplY3Rpb24nLCAoZSkgPT4ge1xyXG5cdFx0XHR0cnkgeyBfd3BiYz8uZGV2Py5lcnJvciggJ0dMT0JBTC1SRUpFQ1RJT04nLCBlPy5yZWFzb24gKTsgfSBjYXRjaCAoIF8gKSB7fVxyXG5cdFx0fSApO1xyXG5cdH1cclxuXHJcblx0cmV0dXJuIG9iajtcclxuXHR9KCBfd3BiYyB8fCB7fSwgalF1ZXJ5ICkpO1xyXG4iLCIvKipcclxuICogRXh0ZW5kIF93cGJjIHdpdGggIG5ldyBtZXRob2RzICAgICAgICAvLyBGaXhJbjogOS44LjYuMi5cclxuICpcclxuICogQHR5cGUgeyp8e319XHJcbiAqIEBwcml2YXRlXHJcbiAqL1xyXG4gX3dwYmMgPSAoZnVuY3Rpb24gKCBvYmosICQpIHtcclxuXHJcblx0Ly8gTG9hZCBCYWxhbmNlciBcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdHZhciBwX2JhbGFuY2VyID0gb2JqLmJhbGFuY2VyX29iaiA9IG9iai5iYWxhbmNlcl9vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdtYXhfdGhyZWFkcyc6IDIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2luX3Byb2Nlc3MnIDogW10sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3dhaXQnICAgICAgIDogW11cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fTtcclxuXHJcblx0IC8qKlxyXG5cdCAgKiBTZXQgIG1heCBwYXJhbGxlbCByZXF1ZXN0ICB0byAgbG9hZFxyXG5cdCAgKlxyXG5cdCAgKiBAcGFyYW0gbWF4X3RocmVhZHNcclxuXHQgICovXHJcblx0b2JqLmJhbGFuY2VyX19zZXRfbWF4X3RocmVhZHMgPSBmdW5jdGlvbiAoIG1heF90aHJlYWRzICl7XHJcblxyXG5cdFx0cF9iYWxhbmNlclsgJ21heF90aHJlYWRzJyBdID0gbWF4X3RocmVhZHM7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogIENoZWNrIGlmIGJhbGFuY2VyIGZvciBzcGVjaWZpYyBib29raW5nIHJlc291cmNlIGRlZmluZWQgICA6OiAgIHRydWUgfCBmYWxzZVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd8aW50fSByZXNvdXJjZV9pZFxyXG5cdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdCAqL1xyXG5cdG9iai5iYWxhbmNlcl9faXNfZGVmaW5lZCA9IGZ1bmN0aW9uICggcmVzb3VyY2VfaWQgKSB7XHJcblxyXG5cdFx0cmV0dXJuICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mKCBwX2JhbGFuY2VyWyAnYmFsYW5jZXJfJyArIHJlc291cmNlX2lkIF0gKSApO1xyXG5cdH07XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiAgQ3JlYXRlIGJhbGFuY2VyIGluaXRpYWxpemluZ1xyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtzdHJpbmd8aW50fSByZXNvdXJjZV9pZFxyXG5cdCAqL1xyXG5cdG9iai5iYWxhbmNlcl9faW5pdCA9IGZ1bmN0aW9uICggcmVzb3VyY2VfaWQsIGZ1bmN0aW9uX25hbWUgLCBwYXJhbXMgPXt9KSB7XHJcblxyXG5cdFx0dmFyIGJhbGFuY2Vfb2JqID0ge307XHJcblx0XHRiYWxhbmNlX29ialsgJ3Jlc291cmNlX2lkJyBdICAgPSByZXNvdXJjZV9pZDtcclxuXHRcdGJhbGFuY2Vfb2JqWyAncHJpb3JpdHknIF0gICAgICA9IDE7XHJcblx0XHRiYWxhbmNlX29ialsgJ2Z1bmN0aW9uX25hbWUnIF0gPSBmdW5jdGlvbl9uYW1lO1xyXG5cdFx0YmFsYW5jZV9vYmpbICdwYXJhbXMnIF0gICAgICAgID0gd3BiY19jbG9uZV9vYmooIHBhcmFtcyApO1xyXG5cclxuXHJcblx0XHRpZiAoIG9iai5iYWxhbmNlcl9faXNfYWxyZWFkeV9ydW4oIHJlc291cmNlX2lkLCBmdW5jdGlvbl9uYW1lICkgKXtcclxuXHRcdFx0cmV0dXJuICdydW4nO1xyXG5cdFx0fVxyXG5cdFx0aWYgKCBvYmouYmFsYW5jZXJfX2lzX2FscmVhZHlfd2FpdCggcmVzb3VyY2VfaWQsIGZ1bmN0aW9uX25hbWUgKSApe1xyXG5cdFx0XHRyZXR1cm4gJ3dhaXQnO1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHRpZiAoIG9iai5iYWxhbmNlcl9fY2FuX2lfcnVuKCkgKXtcclxuXHRcdFx0b2JqLmJhbGFuY2VyX19hZGRfdG9fX3J1biggYmFsYW5jZV9vYmogKTtcclxuXHRcdFx0cmV0dXJuICdydW4nO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0b2JqLmJhbGFuY2VyX19hZGRfdG9fX3dhaXQoIGJhbGFuY2Vfb2JqICk7XHJcblx0XHRcdHJldHVybiAnd2FpdCc7XHJcblx0XHR9XHJcblx0fTtcclxuXHJcblx0IC8qKlxyXG5cdCAgKiBDYW4gSSBSdW4gP1xyXG5cdCAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHQgICovXHJcblx0b2JqLmJhbGFuY2VyX19jYW5faV9ydW4gPSBmdW5jdGlvbiAoKXtcclxuXHRcdHJldHVybiAoIHBfYmFsYW5jZXJbICdpbl9wcm9jZXNzJyBdLmxlbmd0aCA8IHBfYmFsYW5jZXJbICdtYXhfdGhyZWFkcycgXSApO1xyXG5cdH1cclxuXHJcblx0XHQgLyoqXHJcblx0XHQgICogQWRkIHRvIFdBSVRcclxuXHRcdCAgKiBAcGFyYW0gYmFsYW5jZV9vYmpcclxuXHRcdCAgKi9cclxuXHRcdG9iai5iYWxhbmNlcl9fYWRkX3RvX193YWl0ID0gZnVuY3Rpb24gKCBiYWxhbmNlX29iaiApIHtcclxuXHRcdFx0cF9iYWxhbmNlclsnd2FpdCddLnB1c2goIGJhbGFuY2Vfb2JqICk7XHJcblx0XHR9XHJcblxyXG5cdFx0IC8qKlxyXG5cdFx0ICAqIFJlbW92ZSBmcm9tIFdhaXRcclxuXHRcdCAgKlxyXG5cdFx0ICAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdFx0ICAqIEBwYXJhbSBmdW5jdGlvbl9uYW1lXHJcblx0XHQgICogQHJldHVybnMgeyp8Ym9vbGVhbn1cclxuXHRcdCAgKi9cclxuXHRcdG9iai5iYWxhbmNlcl9fcmVtb3ZlX2Zyb21fX3dhaXRfbGlzdCA9IGZ1bmN0aW9uICggcmVzb3VyY2VfaWQsIGZ1bmN0aW9uX25hbWUgKXtcclxuXHJcblx0XHRcdHZhciByZW1vdmVkX2VsID0gZmFsc2U7XHJcblxyXG5cdFx0XHRpZiAoIHBfYmFsYW5jZXJbICd3YWl0JyBdLmxlbmd0aCApe1x0XHRcdFx0XHQvLyBGaXhJbjogOS44LjEwLjEuXHJcblx0XHRcdFx0Zm9yICggdmFyIGkgaW4gcF9iYWxhbmNlclsgJ3dhaXQnIF0gKXtcclxuXHRcdFx0XHRcdGlmIChcclxuXHRcdFx0XHRcdFx0KHJlc291cmNlX2lkID09PSBwX2JhbGFuY2VyWyAnd2FpdCcgXVsgaSBdWyAncmVzb3VyY2VfaWQnIF0pXHJcblx0XHRcdFx0XHRcdCYmIChmdW5jdGlvbl9uYW1lID09PSBwX2JhbGFuY2VyWyAnd2FpdCcgXVsgaSBdWyAnZnVuY3Rpb25fbmFtZScgXSlcclxuXHRcdFx0XHRcdCl7XHJcblx0XHRcdFx0XHRcdHJlbW92ZWRfZWwgPSBwX2JhbGFuY2VyWyAnd2FpdCcgXS5zcGxpY2UoIGksIDEgKTtcclxuXHRcdFx0XHRcdFx0cmVtb3ZlZF9lbCA9IHJlbW92ZWRfZWwucG9wKCk7XHJcblx0XHRcdFx0XHRcdHBfYmFsYW5jZXJbICd3YWl0JyBdID0gcF9iYWxhbmNlclsgJ3dhaXQnIF0uZmlsdGVyKCBmdW5jdGlvbiAoIHYgKXtcclxuXHRcdFx0XHRcdFx0XHRyZXR1cm4gdjtcclxuXHRcdFx0XHRcdFx0fSApO1x0XHRcdFx0XHQvLyBSZWluZGV4IGFycmF5XHJcblx0XHRcdFx0XHRcdHJldHVybiByZW1vdmVkX2VsO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gcmVtb3ZlZF9lbDtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCogSXMgYWxyZWFkeSBXQUlUXHJcblx0XHQqXHJcblx0XHQqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdFx0KiBAcGFyYW0gZnVuY3Rpb25fbmFtZVxyXG5cdFx0KiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHRcdCovXHJcblx0XHRvYmouYmFsYW5jZXJfX2lzX2FscmVhZHlfd2FpdCA9IGZ1bmN0aW9uICggcmVzb3VyY2VfaWQsIGZ1bmN0aW9uX25hbWUgKXtcclxuXHJcblx0XHRcdGlmICggcF9iYWxhbmNlclsgJ3dhaXQnIF0ubGVuZ3RoICl7XHRcdFx0XHQvLyBGaXhJbjogOS44LjEwLjEuXHJcblx0XHRcdFx0Zm9yICggdmFyIGkgaW4gcF9iYWxhbmNlclsgJ3dhaXQnIF0gKXtcclxuXHRcdFx0XHRcdGlmIChcclxuXHRcdFx0XHRcdFx0KHJlc291cmNlX2lkID09PSBwX2JhbGFuY2VyWyAnd2FpdCcgXVsgaSBdWyAncmVzb3VyY2VfaWQnIF0pXHJcblx0XHRcdFx0XHRcdCYmIChmdW5jdGlvbl9uYW1lID09PSBwX2JhbGFuY2VyWyAnd2FpdCcgXVsgaSBdWyAnZnVuY3Rpb25fbmFtZScgXSlcclxuXHRcdFx0XHRcdCl7XHJcblx0XHRcdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdCAvKipcclxuXHRcdCAgKiBBZGQgdG8gUlVOXHJcblx0XHQgICogQHBhcmFtIGJhbGFuY2Vfb2JqXHJcblx0XHQgICovXHJcblx0XHRvYmouYmFsYW5jZXJfX2FkZF90b19fcnVuID0gZnVuY3Rpb24gKCBiYWxhbmNlX29iaiApIHtcclxuXHRcdFx0cF9iYWxhbmNlclsnaW5fcHJvY2VzcyddLnB1c2goIGJhbGFuY2Vfb2JqICk7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQqIFJlbW92ZSBmcm9tIFJVTiBsaXN0XHJcblx0XHQqXHJcblx0XHQqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdFx0KiBAcGFyYW0gZnVuY3Rpb25fbmFtZVxyXG5cdFx0KiBAcmV0dXJucyB7Knxib29sZWFufVxyXG5cdFx0Ki9cclxuXHRcdG9iai5iYWxhbmNlcl9fcmVtb3ZlX2Zyb21fX3J1bl9saXN0ID0gZnVuY3Rpb24gKCByZXNvdXJjZV9pZCwgZnVuY3Rpb25fbmFtZSApe1xyXG5cclxuXHRcdFx0IHZhciByZW1vdmVkX2VsID0gZmFsc2U7XHJcblxyXG5cdFx0XHQgaWYgKCBwX2JhbGFuY2VyWyAnaW5fcHJvY2VzcycgXS5sZW5ndGggKXtcdFx0XHRcdC8vIEZpeEluOiA5LjguMTAuMS5cclxuXHRcdFx0XHQgZm9yICggdmFyIGkgaW4gcF9iYWxhbmNlclsgJ2luX3Byb2Nlc3MnIF0gKXtcclxuXHRcdFx0XHRcdCBpZiAoXHJcblx0XHRcdFx0XHRcdCAocmVzb3VyY2VfaWQgPT09IHBfYmFsYW5jZXJbICdpbl9wcm9jZXNzJyBdWyBpIF1bICdyZXNvdXJjZV9pZCcgXSlcclxuXHRcdFx0XHRcdFx0ICYmIChmdW5jdGlvbl9uYW1lID09PSBwX2JhbGFuY2VyWyAnaW5fcHJvY2VzcycgXVsgaSBdWyAnZnVuY3Rpb25fbmFtZScgXSlcclxuXHRcdFx0XHRcdCApe1xyXG5cdFx0XHRcdFx0XHQgcmVtb3ZlZF9lbCA9IHBfYmFsYW5jZXJbICdpbl9wcm9jZXNzJyBdLnNwbGljZSggaSwgMSApO1xyXG5cdFx0XHRcdFx0XHQgcmVtb3ZlZF9lbCA9IHJlbW92ZWRfZWwucG9wKCk7XHJcblx0XHRcdFx0XHRcdCBwX2JhbGFuY2VyWyAnaW5fcHJvY2VzcycgXSA9IHBfYmFsYW5jZXJbICdpbl9wcm9jZXNzJyBdLmZpbHRlciggZnVuY3Rpb24gKCB2ICl7XHJcblx0XHRcdFx0XHRcdFx0IHJldHVybiB2O1xyXG5cdFx0XHRcdFx0XHQgfSApO1x0XHQvLyBSZWluZGV4IGFycmF5XHJcblx0XHRcdFx0XHRcdCByZXR1cm4gcmVtb3ZlZF9lbDtcclxuXHRcdFx0XHRcdCB9XHJcblx0XHRcdFx0IH1cclxuXHRcdFx0IH1cclxuXHRcdFx0IHJldHVybiByZW1vdmVkX2VsO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0KiBJcyBhbHJlYWR5IFJVTlxyXG5cdFx0KlxyXG5cdFx0KiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHRcdCogQHBhcmFtIGZ1bmN0aW9uX25hbWVcclxuXHRcdCogQHJldHVybnMge2Jvb2xlYW59XHJcblx0XHQqL1xyXG5cdFx0b2JqLmJhbGFuY2VyX19pc19hbHJlYWR5X3J1biA9IGZ1bmN0aW9uICggcmVzb3VyY2VfaWQsIGZ1bmN0aW9uX25hbWUgKXtcclxuXHJcblx0XHRcdGlmICggcF9iYWxhbmNlclsgJ2luX3Byb2Nlc3MnIF0ubGVuZ3RoICl7XHRcdFx0XHRcdC8vIEZpeEluOiA5LjguMTAuMS5cclxuXHRcdFx0XHRmb3IgKCB2YXIgaSBpbiBwX2JhbGFuY2VyWyAnaW5fcHJvY2VzcycgXSApe1xyXG5cdFx0XHRcdFx0aWYgKFxyXG5cdFx0XHRcdFx0XHQocmVzb3VyY2VfaWQgPT09IHBfYmFsYW5jZXJbICdpbl9wcm9jZXNzJyBdWyBpIF1bICdyZXNvdXJjZV9pZCcgXSlcclxuXHRcdFx0XHRcdFx0JiYgKGZ1bmN0aW9uX25hbWUgPT09IHBfYmFsYW5jZXJbICdpbl9wcm9jZXNzJyBdWyBpIF1bICdmdW5jdGlvbl9uYW1lJyBdKVxyXG5cdFx0XHRcdFx0KXtcclxuXHRcdFx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblxyXG5cclxuXHRvYmouYmFsYW5jZXJfX3J1bl9uZXh0ID0gZnVuY3Rpb24gKCl7XHJcblxyXG5cdFx0Ly8gR2V0IDFzdCBmcm9tICBXYWl0IGxpc3RcclxuXHRcdHZhciByZW1vdmVkX2VsID0gZmFsc2U7XHJcblx0XHRpZiAoIHBfYmFsYW5jZXJbICd3YWl0JyBdLmxlbmd0aCApe1x0XHRcdFx0XHQvLyBGaXhJbjogOS44LjEwLjEuXHJcblx0XHRcdGZvciAoIHZhciBpIGluIHBfYmFsYW5jZXJbICd3YWl0JyBdICl7XHJcblx0XHRcdFx0cmVtb3ZlZF9lbCA9IG9iai5iYWxhbmNlcl9fcmVtb3ZlX2Zyb21fX3dhaXRfbGlzdCggcF9iYWxhbmNlclsgJ3dhaXQnIF1bIGkgXVsgJ3Jlc291cmNlX2lkJyBdLCBwX2JhbGFuY2VyWyAnd2FpdCcgXVsgaSBdWyAnZnVuY3Rpb25fbmFtZScgXSApO1xyXG5cdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCBmYWxzZSAhPT0gcmVtb3ZlZF9lbCApe1xyXG5cclxuXHRcdFx0Ly8gUnVuXHJcblx0XHRcdG9iai5iYWxhbmNlcl9fcnVuKCByZW1vdmVkX2VsICk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQgLyoqXHJcblx0ICAqIFJ1blxyXG5cdCAgKiBAcGFyYW0gYmFsYW5jZV9vYmpcclxuXHQgICovXHJcblx0b2JqLmJhbGFuY2VyX19ydW4gPSBmdW5jdGlvbiAoIGJhbGFuY2Vfb2JqICl7XHJcblxyXG5cdFx0c3dpdGNoICggYmFsYW5jZV9vYmpbICdmdW5jdGlvbl9uYW1lJyBdICl7XHJcblxyXG5cdFx0XHRjYXNlICd3cGJjX2NhbGVuZGFyX19sb2FkX2RhdGFfX2FqeCc6XHJcblxyXG5cdFx0XHRcdC8vIEFkZCB0byBydW4gbGlzdFxyXG5cdFx0XHRcdG9iai5iYWxhbmNlcl9fYWRkX3RvX19ydW4oIGJhbGFuY2Vfb2JqICk7XHJcblxyXG5cdFx0XHRcdHdwYmNfY2FsZW5kYXJfX2xvYWRfZGF0YV9fYWp4KCBiYWxhbmNlX29ialsgJ3BhcmFtcycgXSApXHJcblx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRkZWZhdWx0OlxyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0cmV0dXJuIG9iajtcclxuXHJcbn0oIF93cGJjIHx8IHt9LCBqUXVlcnkgKSk7XHJcblxyXG5cclxuIFx0LyoqXHJcbiBcdCAqIC0tIEhlbHAgZnVuY3Rpb25zIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQgKi9cclxuXHJcblx0ZnVuY3Rpb24gd3BiY19iYWxhbmNlcl9faXNfd2FpdCggcGFyYW1zLCBmdW5jdGlvbl9uYW1lICl7XHJcbi8vY29uc29sZS5sb2coJzo6d3BiY19iYWxhbmNlcl9faXNfd2FpdCcscGFyYW1zICwgZnVuY3Rpb25fbmFtZSApO1xyXG5cdFx0aWYgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChwYXJhbXNbICdyZXNvdXJjZV9pZCcgXSkgKXtcclxuXHJcblx0XHRcdHZhciBiYWxhbmNlcl9zdGF0dXMgPSBfd3BiYy5iYWxhbmNlcl9faW5pdCggcGFyYW1zWyAncmVzb3VyY2VfaWQnIF0sIGZ1bmN0aW9uX25hbWUsIHBhcmFtcyApO1xyXG5cclxuXHRcdFx0cmV0dXJuICggJ3dhaXQnID09PSBiYWxhbmNlcl9zdGF0dXMgKTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fVxyXG5cclxuXHJcblx0ZnVuY3Rpb24gd3BiY19iYWxhbmNlcl9fY29tcGxldGVkKCByZXNvdXJjZV9pZCAsIGZ1bmN0aW9uX25hbWUgKXtcclxuLy9jb25zb2xlLmxvZygnOjp3cGJjX2JhbGFuY2VyX19jb21wbGV0ZWQnLHJlc291cmNlX2lkICwgZnVuY3Rpb25fbmFtZSApO1xyXG5cdFx0X3dwYmMuYmFsYW5jZXJfX3JlbW92ZV9mcm9tX19ydW5fbGlzdCggcmVzb3VyY2VfaWQsIGZ1bmN0aW9uX25hbWUgKTtcclxuXHRcdF93cGJjLmJhbGFuY2VyX19ydW5fbmV4dCgpO1xyXG5cdH0iLCIvKipcclxuICogPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbiAqXHRpbmNsdWRlcy9fX2pzL2NhbC93cGJjX2NhbC5qc1xyXG4gKiA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuICovXHJcblxyXG4vKipcclxuICogT3JkZXIgb3IgY2hpbGQgYm9va2luZyByZXNvdXJjZXMgc2F2ZWQgaGVyZTogIFx0X3dwYmMuYm9va2luZ19fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ3Jlc291cmNlc19pZF9hcnJfX2luX2RhdGVzJyApXHRcdFsyLDEwLDEyLDExXVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBIb3cgdG8gY2hlY2sgIGJvb2tlZCB0aW1lcyBvbiAgc3BlY2lmaWMgZGF0ZTogP1xyXG4gKlxyXG5cdFx0XHRfd3BiYy5ib29raW5nc19pbl9jYWxlbmRhcl9fZ2V0X2Zvcl9kYXRlKDIsJzIwMjMtMDgtMjEnKTtcclxuXHJcblx0XHRcdGNvbnNvbGUubG9nKFxyXG5cdFx0XHRcdFx0XHRfd3BiYy5ib29raW5nc19pbl9jYWxlbmRhcl9fZ2V0X2Zvcl9kYXRlKDIsJzIwMjMtMDgtMjEnKVsyXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfc2Vjb25kcyxcclxuXHRcdFx0XHRcdFx0X3dwYmMuYm9va2luZ3NfaW5fY2FsZW5kYXJfX2dldF9mb3JfZGF0ZSgyLCcyMDIzLTA4LTIxJylbMTBdLmJvb2tlZF90aW1lX3Nsb3RzLm1lcmdlZF9zZWNvbmRzLFxyXG5cdFx0XHRcdFx0XHRfd3BiYy5ib29raW5nc19pbl9jYWxlbmRhcl9fZ2V0X2Zvcl9kYXRlKDIsJzIwMjMtMDgtMjEnKVsxMV0uYm9va2VkX3RpbWVfc2xvdHMubWVyZ2VkX3NlY29uZHMsXHJcblx0XHRcdFx0XHRcdF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZm9yX2RhdGUoMiwnMjAyMy0wOC0yMScpWzEyXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfc2Vjb25kc1xyXG5cdFx0XHRcdFx0KTtcclxuICogIE9SXHJcblx0XHRcdGNvbnNvbGUubG9nKFxyXG5cdFx0XHRcdFx0XHRfd3BiYy5ib29raW5nc19pbl9jYWxlbmRhcl9fZ2V0X2Zvcl9kYXRlKDIsJzIwMjMtMDgtMjEnKVsyXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfcmVhZGFibGUsXHJcblx0XHRcdFx0XHRcdF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZm9yX2RhdGUoMiwnMjAyMy0wOC0yMScpWzEwXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfcmVhZGFibGUsXHJcblx0XHRcdFx0XHRcdF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZm9yX2RhdGUoMiwnMjAyMy0wOC0yMScpWzExXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfcmVhZGFibGUsXHJcblx0XHRcdFx0XHRcdF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZm9yX2RhdGUoMiwnMjAyMy0wOC0yMScpWzEyXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfcmVhZGFibGVcclxuXHRcdFx0XHRcdCk7XHJcbiAqXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqIERheXMgc2VsZWN0aW9uOlxyXG4gKiBcdFx0XHRcdFx0d3BiY19jYWxlbmRhcl9fdW5zZWxlY3RfYWxsX2RhdGVzKCByZXNvdXJjZV9pZCApO1xyXG4gKlxyXG4gKlx0XHRcdFx0XHR2YXIgcmVzb3VyY2VfaWQgPSAxO1xyXG4gKiBcdEV4YW1wbGUgMTpcdFx0dmFyIG51bV9zZWxlY3RlZF9kYXlzID0gd3BiY19hdXRvX3NlbGVjdF9kYXRlc19pbl9jYWxlbmRhciggcmVzb3VyY2VfaWQsICcyMDI0LTA1LTE1JywgJzIwMjQtMDUtMjUnICk7XHJcbiAqIFx0RXhhbXBsZSAyOlx0XHR2YXIgbnVtX3NlbGVjdGVkX2RheXMgPSB3cGJjX2F1dG9fc2VsZWN0X2RhdGVzX2luX2NhbGVuZGFyKCByZXNvdXJjZV9pZCwgWycyMDI0LTA1LTA5JywnMjAyNC0wNS0xOScsJzIwMjQtMDUtMjUnXSApO1xyXG4gKlxyXG4gKi9cclxuXHJcblxyXG4vKipcclxuICogQyBBIEwgRSBOIEQgQSBSICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG5cclxuLyoqXHJcbiAqICBTaG93IFdQQkMgQ2FsZW5kYXJcclxuICpcclxuICogQHBhcmFtIHJlc291cmNlX2lkXHRcdFx0LSByZXNvdXJjZSBJRFxyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfY2FsZW5kYXJfc2hvdyggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0Ly8gSWYgbm8gY2FsZW5kYXIgSFRNTCB0YWcsICB0aGVuICBleGl0XHJcblx0aWYgKCAwID09PSBqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCApLmxlbmd0aCApeyByZXR1cm4gZmFsc2U7IH1cclxuXHJcblx0Ly8gSWYgdGhlIGNhbGVuZGFyIHdpdGggdGhlIHNhbWUgQm9va2luZyByZXNvdXJjZSBpcyBhY3RpdmF0ZWQgYWxyZWFkeSwgdGhlbiBleGl0LiBCdXQgaW4gRWxlbWVudG9yIHRoZSBjbGFzcyBjYW4gYmUgc3RhbGUsIHNvIHZlcmlmeSBpbnN0YW5jZS5cclxuXHRpZiAoIGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIHJlc291cmNlX2lkICkuaGFzQ2xhc3MoICdoYXNEYXRlcGljaycgKSApIHtcclxuXHJcblx0XHR2YXIgZXhpc3RpbmdfaW5zdCA9IG51bGw7XHJcblxyXG5cdFx0dHJ5IHtcclxuXHRcdFx0ZXhpc3RpbmdfaW5zdCA9IGpRdWVyeS5kYXRlcGljay5fZ2V0SW5zdCggalF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS5nZXQoIDAgKSApO1xyXG5cdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdGV4aXN0aW5nX2luc3QgPSBudWxsO1xyXG5cdFx0fVxyXG5cclxuXHRcdGlmICggZXhpc3RpbmdfaW5zdCApIHtcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFN0YWxlIG1hcmtlcjogcmVtb3ZlIGFuZCBjb250aW51ZSB3aXRoIGluaXQuXHJcblx0XHRqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCApLnJlbW92ZUNsYXNzKCAnaGFzRGF0ZXBpY2snICk7XHJcblx0fVxyXG5cclxuXHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRGF5cyBzZWxlY3Rpb25cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBsb2NhbF9faXNfcmFuZ2Vfc2VsZWN0ID0gZmFsc2U7XHJcblx0dmFyIGxvY2FsX19tdWx0aV9kYXlzX3NlbGVjdF9udW0gICA9IDM2NTtcdFx0XHRcdFx0Ly8gbXVsdGlwbGUgfCBmaXhlZFxyXG5cdGlmICggJ2R5bmFtaWMnID09PSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2RheXNfc2VsZWN0X21vZGUnICkgKXtcclxuXHRcdGxvY2FsX19pc19yYW5nZV9zZWxlY3QgPSB0cnVlO1xyXG5cdFx0bG9jYWxfX211bHRpX2RheXNfc2VsZWN0X251bSA9IDA7XHJcblx0fVxyXG5cdGlmICggJ3NpbmdsZScgID09PSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2RheXNfc2VsZWN0X21vZGUnICkgKXtcclxuXHRcdGxvY2FsX19tdWx0aV9kYXlzX3NlbGVjdF9udW0gPSAwO1xyXG5cdH1cclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBNaW4gLSBNYXggZGF5cyB0byBzY3JvbGwvc2hvd1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIGxvY2FsX19taW5fZGF0ZSA9IDA7XHJcbiBcdGxvY2FsX19taW5fZGF0ZSA9IG5ldyBEYXRlKCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0b2RheV9hcnInIClbIDAgXSwgKHBhcnNlSW50KCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0b2RheV9hcnInIClbIDEgXSApIC0gMSksIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RvZGF5X2FycicgKVsgMiBdLCAwLCAwLCAwICk7XHRcdFx0Ly8gRml4SW46IDkuOS4wLjE3LlxyXG4vL2NvbnNvbGUubG9nKCBsb2NhbF9fbWluX2RhdGUgKTtcclxuXHR2YXIgbG9jYWxfX21heF9kYXRlID0gX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdib29raW5nX21heF9tb250aGVzX2luX2NhbGVuZGFyJyApO1xyXG5cdC8vbG9jYWxfX21heF9kYXRlID0gbmV3IERhdGUoMjAyNCwgNSwgMjgpOyAgSXQgaXMgaGVyZSBpc3N1ZSBvZiBub3Qgc2VsZWN0YWJsZSBkYXRlcywgYnV0IHNvbWUgZGF0ZXMgc2hvd2luZyBpbiBjYWxlbmRhciBhcyBhdmFpbGFibGUsIGJ1dCB3ZSBjYW4gbm90IHNlbGVjdCBpdC5cclxuXHJcblx0Ly8vLyBEZWZpbmUgbGFzdCBkYXkgaW4gY2FsZW5kYXIgKGFzIGEgbGFzdCBkYXkgb2YgbW9udGggKGFuZCBub3QgZGF0ZSwgd2hpY2ggaXMgcmVsYXRlZCB0byBhY3R1YWwgJ1RvZGF5JyBkYXRlKS5cclxuXHQvLy8vIEUuZy4gaWYgdG9kYXkgaXMgMjAyMy0wOS0yNSwgYW5kIHdlIHNldCAnTnVtYmVyIG9mIG1vbnRocyB0byBzY3JvbGwnIGFzIDUgbW9udGhzLCB0aGVuIGxhc3QgZGF5IHdpbGwgYmUgMjAyNC0wMi0yOSBhbmQgbm90IHRoZSAyMDI0LTAyLTI1LlxyXG5cdC8vIHZhciBjYWxfbGFzdF9kYXlfaW5fbW9udGggPSBqUXVlcnkuZGF0ZXBpY2suX2RldGVybWluZURhdGUoIG51bGwsIGxvY2FsX19tYXhfZGF0ZSwgbmV3IERhdGUoKSApO1xyXG5cdC8vIGNhbF9sYXN0X2RheV9pbl9tb250aCA9IG5ldyBEYXRlKCBjYWxfbGFzdF9kYXlfaW5fbW9udGguZ2V0RnVsbFllYXIoKSwgY2FsX2xhc3RfZGF5X2luX21vbnRoLmdldE1vbnRoKCkgKyAxLCAwICk7XHJcblx0Ly8gbG9jYWxfX21heF9kYXRlID0gY2FsX2xhc3RfZGF5X2luX21vbnRoO1x0XHRcdC8vIEZpeEluOiAxMC4wLjAuMjYuXHJcblxyXG5cdC8vIEdldCBzdGFydCAvIGVuZCBkYXRlcyBmcm9tICB0aGUgQm9va2luZyBDYWxlbmRhciBzaG9ydGNvZGUuIEV4YW1wbGU6IFtib29raW5nIGNhbGVuZGFyX2RhdGVzX3N0YXJ0PScyMDI2LTAxLTAxJyBjYWxlbmRhcl9kYXRlc19lbmQ9JzIwMjYtMTItMzEnICByZXNvdXJjZV9pZD0xXSAvLyBGaXhJbjogMTAuMTMuMS40LlxyXG5cdGlmICggZmFsc2UgIT09IHdwYmNfY2FsZW5kYXJfX2dldF9kYXRlc19zdGFydCggcmVzb3VyY2VfaWQgKSApIHtcclxuXHRcdGxvY2FsX19taW5fZGF0ZSA9IHdwYmNfY2FsZW5kYXJfX2dldF9kYXRlc19zdGFydCggcmVzb3VyY2VfaWQgKTsgIC8vIEUuZy4gLSBsb2NhbF9fbWluX2RhdGUgPSBuZXcgRGF0ZSggMjAyNSwgMCwgMSApO1xyXG5cdH1cclxuXHRpZiAoIGZhbHNlICE9PSB3cGJjX2NhbGVuZGFyX19nZXRfZGF0ZXNfZW5kKCByZXNvdXJjZV9pZCApICkge1xyXG5cdFx0bG9jYWxfX21heF9kYXRlID0gd3BiY19jYWxlbmRhcl9fZ2V0X2RhdGVzX2VuZCggcmVzb3VyY2VfaWQgKTsgICAgLy8gRS5nLiAtIGxvY2FsX19tYXhfZGF0ZSA9IG5ldyBEYXRlKCAyMDI1LCAxMSwgMzEgKTtcclxuXHR9XHJcblxyXG5cdC8vIEluIGNhc2Ugd2UgZWRpdCBib29raW5nIGluIHBhc3Qgb3IgaGF2ZSBzcGVjaWZpYyBwYXJhbWV0ZXIgaW4gVVJMLlxyXG5cdGlmICggICAoIGxvY2F0aW9uLmhyZWYuaW5kZXhPZigncGFnZT13cGJjLW5ldycpICE9IC0xIClcclxuXHRcdCYmIChcclxuXHRcdFx0ICAoIGxvY2F0aW9uLmhyZWYuaW5kZXhPZignYm9va2luZ19oYXNoJykgIT0gLTEgKSAgICAgICAgICAgICAgICAgIC8vIENvbW1lbnQgdGhpcyBsaW5lIGZvciBhYmlsaXR5IHRvIGFkZCAgYm9va2luZyBpbiBwYXN0IGRheXMgYXQgIEJvb2tpbmcgPiBBZGQgYm9va2luZyBwYWdlLlxyXG5cdFx0ICAgfHwgKCBsb2NhdGlvbi5ocmVmLmluZGV4T2YoJ2FsbG93X3Bhc3QnKSAhPSAtMSApICAgICAgICAgICAgICAgIC8vIEZpeEluOiAxMC43LjEuMi5cclxuXHRcdClcclxuXHQpe1xyXG5cdFx0Ly8gbG9jYWxfX21pbl9kYXRlID0gbnVsbDtcclxuXHRcdC8vIEZpeEluOiAxMC4xNC4xLjQuXHJcblx0XHRsb2NhbF9fbWluX2RhdGUgID0gbmV3IERhdGUoIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RpbWVfbG9jYWxfYXJyJyApWzBdLCAoIHBhcnNlSW50KCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0aW1lX2xvY2FsX2FycicgKVsxXSApIC0gMSksIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RpbWVfbG9jYWxfYXJyJyApWzJdLCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0aW1lX2xvY2FsX2FycicgKVszXSwgX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAndGltZV9sb2NhbF9hcnInIClbNF0sIDAgKTtcclxuXHRcdGxvY2FsX19tYXhfZGF0ZSA9IG51bGw7XHJcblx0fVxyXG5cclxuXHR2YXIgbG9jYWxfX3N0YXJ0X3dlZWtkYXkgICAgPSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2Jvb2tpbmdfc3RhcnRfZGF5X3dlZWVrJyApO1xyXG5cdHZhciBsb2NhbF9fbnVtYmVyX29mX21vbnRocyA9IHBhcnNlSW50KCBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2NhbGVuZGFyX251bWJlcl9vZl9tb250aHMnICkgKTtcclxuXHJcblx0alF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS50ZXh0KCAnJyApO1x0XHRcdFx0XHQvLyBSZW1vdmUgYWxsIEhUTUwgaW4gY2FsZW5kYXIgdGFnXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBTaG93IGNhbGVuZGFyXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRqUXVlcnkoJyNjYWxlbmRhcl9ib29raW5nJysgcmVzb3VyY2VfaWQpLmRhdGVwaWNrKFxyXG5cdFx0XHR7XHJcblx0XHRcdFx0YmVmb3JlU2hvd0RheTogZnVuY3Rpb24gKCBqc19kYXRlICl7XHJcblx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiB3cGJjX19jYWxlbmRhcl9fYXBwbHlfY3NzX3RvX2RheXMoIGpzX2RhdGUsIHsncmVzb3VyY2VfaWQnOiByZXNvdXJjZV9pZH0sIHRoaXMgKTtcclxuXHRcdFx0XHRcdFx0XHQgIH0sXHJcblx0XHRcdFx0b25TZWxlY3Q6IGZ1bmN0aW9uICggc3RyaW5nX2RhdGVzLCBqc19kYXRlc19hcnIgKXsgIC8qKlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICpcdHN0cmluZ19kYXRlcyAgID0gICAnMjMuMDguMjAyMyAtIDI2LjA4LjIwMjMnICAgIHwgICAgJzIzLjA4LjIwMjMgLSAyMy4wOC4yMDIzJyAgICB8ICAgICcxOS4wOS4yMDIzLCAyNC4wOC4yMDIzLCAzMC4wOS4yMDIzJ1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICogIGpzX2RhdGVzX2FyciAgID0gICByYW5nZTogWyBEYXRlIChBdWcgMjMgMjAyMyksIERhdGUgKEF1ZyAyNSAyMDIzKV0gICAgIHwgICAgIG11bHRpcGxlOiBbIERhdGUoT2N0IDI0IDIwMjMpLCBEYXRlKE9jdCAyMCAyMDIzKSwgRGF0ZShPY3QgMTYgMjAyMykgXVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICovXHJcblx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiB3cGJjX19jYWxlbmRhcl9fb25fc2VsZWN0X2RheXMoIHN0cmluZ19kYXRlcywgeydyZXNvdXJjZV9pZCc6IHJlc291cmNlX2lkfSwgdGhpcyApO1xyXG5cdFx0XHRcdFx0XHRcdCAgfSxcclxuXHRcdFx0XHRvbkhvdmVyOiBmdW5jdGlvbiAoIHN0cmluZ19kYXRlLCBqc19kYXRlICl7XHJcblx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiB3cGJjX19jYWxlbmRhcl9fb25faG92ZXJfZGF5cyggc3RyaW5nX2RhdGUsIGpzX2RhdGUsIHsncmVzb3VyY2VfaWQnOiByZXNvdXJjZV9pZH0sIHRoaXMgKTtcclxuXHRcdFx0XHRcdFx0XHQgIH0sXHJcblx0XHRcdFx0b25DaGFuZ2VNb250aFllYXI6IGZ1bmN0aW9uICggeWVhciwgcmVhbF9tb250aCwganNfZGF0ZV9fMXN0X2RheV9pbl9tb250aCApeyB9LFxyXG5cdFx0XHRcdHNob3dPbiAgICAgICAgOiAnYm90aCcsXHJcblx0XHRcdFx0bnVtYmVyT2ZNb250aHM6IGxvY2FsX19udW1iZXJfb2ZfbW9udGhzLFxyXG5cdFx0XHRcdHN0ZXBNb250aHMgICAgOiAxLFxyXG5cdFx0XHRcdC8vIHByZXZUZXh0ICAgICAgOiAnJmxhcXVvOycsXHJcblx0XHRcdFx0Ly8gbmV4dFRleHQgICAgICA6ICcmcmFxdW87JyxcclxuXHRcdFx0XHRwcmV2VGV4dCAgICAgIDogJyZsc2FxdW87JyxcclxuXHRcdFx0XHRuZXh0VGV4dCAgICAgIDogJyZyc2FxdW87JyxcclxuXHRcdFx0XHRkYXRlRm9ybWF0ICAgIDogJ2RkLm1tLnl5JyxcclxuXHRcdFx0XHRjaGFuZ2VNb250aCAgIDogZmFsc2UsXHJcblx0XHRcdFx0Y2hhbmdlWWVhciAgICA6IGZhbHNlLFxyXG5cdFx0XHRcdG1pbkRhdGUgICAgICAgOiBsb2NhbF9fbWluX2RhdGUsXHJcblx0XHRcdFx0bWF4RGF0ZSAgICAgICA6IGxvY2FsX19tYXhfZGF0ZSwgXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vICcxWScsXHJcblx0XHRcdFx0Ly8gbWluRGF0ZTogbmV3IERhdGUoMjAyMCwgMiwgMSksIG1heERhdGU6IG5ldyBEYXRlKDIwMjAsIDksIDMxKSwgICAgICAgICAgICAgXHQvLyBBYmlsaXR5IHRvIHNldCBhbnkgIHN0YXJ0IGFuZCBlbmQgZGF0ZSBpbiBjYWxlbmRhclxyXG5cdFx0XHRcdHNob3dTdGF0dXMgICAgICA6IGZhbHNlLFxyXG5cdFx0XHRcdG11bHRpU2VwYXJhdG9yICA6ICcsICcsXHJcblx0XHRcdFx0Y2xvc2VBdFRvcCAgICAgIDogZmFsc2UsXHJcblx0XHRcdFx0Zmlyc3REYXkgICAgICAgIDogbG9jYWxfX3N0YXJ0X3dlZWtkYXksXHJcblx0XHRcdFx0Z290b0N1cnJlbnQgICAgIDogZmFsc2UsXHJcblx0XHRcdFx0aGlkZUlmTm9QcmV2TmV4dDogdHJ1ZSxcclxuXHRcdFx0XHRtdWx0aVNlbGVjdCAgICAgOiBsb2NhbF9fbXVsdGlfZGF5c19zZWxlY3RfbnVtLFxyXG5cdFx0XHRcdHJhbmdlU2VsZWN0ICAgICA6IGxvY2FsX19pc19yYW5nZV9zZWxlY3QsXHJcblx0XHRcdFx0Ly8gc2hvd1dlZWtzOiB0cnVlLFxyXG5cdFx0XHRcdHVzZVRoZW1lUm9sbGVyOiBmYWxzZVxyXG5cdFx0XHR9XHJcblx0KTtcclxuXHJcblxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gQ2xlYXIgdG9kYXkgZGF0ZSBoaWdobGlnaHRpbmdcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHNldFRpbWVvdXQoIGZ1bmN0aW9uICgpeyAgd3BiY19jYWxlbmRhcnNfX2NsZWFyX2RheXNfaGlnaGxpZ2h0aW5nKCByZXNvdXJjZV9pZCApOyAgfSwgNTAwICk7ICAgICAgICAgICAgICAgICAgICBcdC8vIEZpeEluOiA3LjEuMi44LlxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gU2Nyb2xsIGNhbGVuZGFyIHRvICBzcGVjaWZpYyBtb250aFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHN0YXJ0X2JrX21vbnRoID0gX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdjYWxlbmRhcl9zY3JvbGxfdG8nICk7XHJcblx0aWYgKCBmYWxzZSAhPT0gc3RhcnRfYmtfbW9udGggKXtcclxuXHRcdHdwYmNfY2FsZW5kYXJfX3Njcm9sbF90byggcmVzb3VyY2VfaWQsIHN0YXJ0X2JrX21vbnRoWyAwIF0sIHN0YXJ0X2JrX21vbnRoWyAxIF0gKTtcclxuXHR9XHJcbn1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIEFwcGx5IENTUyB0byBjYWxlbmRhciBkYXRlIGNlbGxzXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gZGF0ZVx0XHRcdFx0XHRcdFx0XHRcdFx0LSAgSmF2YVNjcmlwdCBEYXRlIE9iajogIFx0XHRNb24gRGVjIDExIDIwMjMgMDA6MDA6MDAgR01UKzAyMDAgKEVhc3Rlcm4gRXVyb3BlYW4gU3RhbmRhcmQgVGltZSlcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0XHRcdFx0XHRcdC0gIENhbGVuZGFyIFNldHRpbmdzIE9iamVjdDogIFx0e1xyXG5cdCAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFx0XHRcdFx0XHRcdFwicmVzb3VyY2VfaWRcIjogNFxyXG5cdCAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdCAqIEBwYXJhbSBkYXRlcGlja190aGlzXHRcdFx0XHRcdFx0XHRcdC0gdGhpcyBvZiBkYXRlcGljayBPYmpcclxuXHQgKiBAcmV0dXJucyB7KCp8c3RyaW5nKVtdfChib29sZWFufHN0cmluZylbXX1cdFx0LSBbIHt0cnVlIC1hdmFpbGFibGUgfCBmYWxzZSAtIHVuYXZhaWxhYmxlfSwgJ0NTUyBjbGFzc2VzIGZvciBjYWxlbmRhciBkYXkgY2VsbCcgXVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfX2NhbGVuZGFyX19hcHBseV9jc3NfdG9fZGF5cyggZGF0ZSwgY2FsZW5kYXJfcGFyYW1zX2FyciwgZGF0ZXBpY2tfdGhpcyApe1xyXG5cclxuXHRcdHZhciB0b2RheV9kYXRlID0gbmV3IERhdGUoIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RvZGF5X2FycicgKVsgMCBdLCAocGFyc2VJbnQoIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RvZGF5X2FycicgKVsgMSBdICkgLSAxKSwgX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAndG9kYXlfYXJyJyApWyAyIF0sIDAsIDAsIDAgKTtcdFx0XHRcdFx0XHRcdFx0Ly8gVG9kYXkgSlNfRGF0ZV9PYmouXHJcblx0XHR2YXIgY2xhc3NfZGF5ICAgICA9IHdwYmNfX2dldF9fdGRfY2xhc3NfZGF0ZSggZGF0ZSApO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vICcxLTktMjAyMydcclxuXHRcdHZhciBzcWxfY2xhc3NfZGF5ID0gd3BiY19fZ2V0X19zcWxfY2xhc3NfZGF0ZSggZGF0ZSApO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vICcyMDIzLTAxLTA5J1xyXG5cdFx0dmFyIHJlc291cmNlX2lkID0gKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mKGNhbGVuZGFyX3BhcmFtc19hcnJbICdyZXNvdXJjZV9pZCcgXSkgKSA/IGNhbGVuZGFyX3BhcmFtc19hcnJbICdyZXNvdXJjZV9pZCcgXSA6ICcxJzsgXHRcdC8vICcxJ1xyXG5cclxuXHRcdC8vIEdldCBTZWxlY3RlZCBkYXRlcyBpbiBjYWxlbmRhclxyXG5cdFx0dmFyIHNlbGVjdGVkX2RhdGVzX3NxbCA9IHdwYmNfZ2V0X19zZWxlY3RlZF9kYXRlc19zcWxfX2FzX2FyciggcmVzb3VyY2VfaWQgKTtcclxuXHJcblx0XHQvLyBHZXQgRGF0YSAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0dmFyIGRhdGVfYm9va2luZ3Nfb2JqID0gX3dwYmMuYm9va2luZ3NfaW5fY2FsZW5kYXJfX2dldF9mb3JfZGF0ZSggcmVzb3VyY2VfaWQsIHNxbF9jbGFzc19kYXkgKTtcclxuXHJcblxyXG5cdFx0Ly8gQXJyYXkgd2l0aCBDU1MgY2xhc3NlcyBmb3IgZGF0ZSAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdHZhciBjc3NfY2xhc3Nlc19fZm9yX2RhdGUgPSBbXTtcclxuXHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAnc3FsX2RhdGVfJyAgICAgKyBzcWxfY2xhc3NfZGF5ICk7XHRcdFx0XHQvLyAgJ3NxbF9kYXRlXzIwMjMtMDctMjEnXHJcblx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2NhbDRkYXRlLScgICAgICsgY2xhc3NfZGF5ICk7XHRcdFx0XHRcdC8vICAnY2FsNGRhdGUtNy0yMS0yMDIzJ1xyXG5cdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICd3cGJjX3dlZWtkYXlfJyArIGRhdGUuZ2V0RGF5KCkgKTtcdFx0XHRcdC8vICAnd3BiY193ZWVrZGF5XzQnXHJcblxyXG5cdFx0Ly8gRGVmaW5lIFNlbGVjdGVkIENoZWNrIEluL091dCBkYXRlcyBpbiBURCAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdGlmIChcclxuXHRcdFx0XHQoIHNlbGVjdGVkX2RhdGVzX3NxbC5sZW5ndGggIClcclxuXHRcdFx0Ly8mJiAgKCBzZWxlY3RlZF9kYXRlc19zcWxbIDAgXSAhPT0gc2VsZWN0ZWRfZGF0ZXNfc3FsWyAoc2VsZWN0ZWRfZGF0ZXNfc3FsLmxlbmd0aCAtIDEpIF0gKVxyXG5cdFx0KXtcclxuXHRcdFx0aWYgKCBzcWxfY2xhc3NfZGF5ID09PSBzZWxlY3RlZF9kYXRlc19zcWxbIDAgXSApe1xyXG5cdFx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAnc2VsZWN0ZWRfY2hlY2tfaW4nICk7XHJcblx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdzZWxlY3RlZF9jaGVja19pbl9vdXQnICk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCAgKCBzZWxlY3RlZF9kYXRlc19zcWwubGVuZ3RoID4gMSApICYmICggc3FsX2NsYXNzX2RheSA9PT0gc2VsZWN0ZWRfZGF0ZXNfc3FsWyAoc2VsZWN0ZWRfZGF0ZXNfc3FsLmxlbmd0aCAtIDEpIF0gKSApIHtcclxuXHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ3NlbGVjdGVkX2NoZWNrX291dCcgKTtcclxuXHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ3NlbGVjdGVkX2NoZWNrX2luX291dCcgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHJcblx0XHR2YXIgaXNfZGF5X3NlbGVjdGFibGUgPSBmYWxzZTtcclxuXHJcblx0XHQvLyBJZiBzb21ldGhpbmcgbm90IGRlZmluZWQsICB0aGVuICB0aGlzIGRhdGUgY2xvc2VkIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAvLyBGaXhJbjogMTAuMTIuNC42LlxyXG5cdFx0aWYgKCAoZmFsc2UgPT09IGRhdGVfYm9va2luZ3Nfb2JqKSB8fCAoJ3VuZGVmaW5lZCcgPT09IHR5cGVvZiAoZGF0ZV9ib29raW5nc19vYmpbcmVzb3VyY2VfaWRdKSkgKSB7XHJcblxyXG5cdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2RhdGVfdXNlcl91bmF2YWlsYWJsZScgKTtcclxuXHJcblx0XHRcdHJldHVybiBbIGlzX2RheV9zZWxlY3RhYmxlLCBjc3NfY2xhc3Nlc19fZm9yX2RhdGUuam9pbignICcpICBdO1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gICBkYXRlX2Jvb2tpbmdzX29iaiAgLSBEZWZpbmVkLiAgICAgICAgICAgIERhdGVzIGNhbiBiZSBzZWxlY3RhYmxlLlxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gQWRkIHNlYXNvbiBuYW1lcyB0byB0aGUgZGF5IENTUyBjbGFzc2VzIC0tIGl0IGlzIHJlcXVpcmVkIGZvciBjb3JyZWN0ICB3b3JrICBvZiBjb25kaXRpb25hbCBmaWVsZHMgLS0tLS0tLS0tLS0tLS1cclxuXHRcdHZhciBzZWFzb25fbmFtZXNfYXJyID0gX3dwYmMuc2Vhc29uc19fZ2V0X2Zvcl9kYXRlKCByZXNvdXJjZV9pZCwgc3FsX2NsYXNzX2RheSApO1xyXG5cclxuXHRcdGZvciAoIHZhciBzZWFzb25fa2V5IGluIHNlYXNvbl9uYW1lc19hcnIgKXtcclxuXHJcblx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCBzZWFzb25fbmFtZXNfYXJyWyBzZWFzb25fa2V5IF0gKTtcdFx0XHRcdC8vICAnd3BkZXZia19zZWFzb25fc2VwdGVtYmVyXzIwMjMnXHJcblx0XHR9XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHJcblx0XHQvLyBDb3N0IFJhdGUgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdyYXRlXycgKyBkYXRlX2Jvb2tpbmdzX29ialsgcmVzb3VyY2VfaWQgXVsgJ2RhdGVfY29zdF9yYXRlJyBdLnRvU3RyaW5nKCkucmVwbGFjZSggL1tcXC5cXHNdL2csICdfJyApICk7XHRcdFx0XHRcdFx0Ly8gICdyYXRlXzk5XzAwJyAtPiA5OS4wMFxyXG5cclxuXHJcblx0XHRpZiAoIHBhcnNlSW50KCBkYXRlX2Jvb2tpbmdzX29ialsgJ2RheV9hdmFpbGFiaWxpdHknIF0gKSA+IDAgKXtcclxuXHRcdFx0aXNfZGF5X3NlbGVjdGFibGUgPSB0cnVlO1xyXG5cdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2RhdGVfYXZhaWxhYmxlJyApO1xyXG5cdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ3Jlc2VydmVkX2RheXNfY291bnQnICsgcGFyc2VJbnQoIGRhdGVfYm9va2luZ3Nfb2JqWyAnbWF4X2NhcGFjaXR5JyBdIC0gZGF0ZV9ib29raW5nc19vYmpbICdkYXlfYXZhaWxhYmlsaXR5JyBdICkgKTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdGlzX2RheV9zZWxlY3RhYmxlID0gZmFsc2U7XHJcblx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAnZGF0ZV91c2VyX3VuYXZhaWxhYmxlJyApO1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHRzd2l0Y2ggKCBkYXRlX2Jvb2tpbmdzX29ialsgJ3N1bW1hcnknXVsnc3RhdHVzX2Zvcl9kYXknIF0gKXtcclxuXHJcblx0XHRcdGNhc2UgJ2F2YWlsYWJsZSc6XHJcblx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRjYXNlICd0aW1lX3Nsb3RzX2Jvb2tpbmcnOlxyXG5cdFx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAndGltZXNwYXJ0bHknLCAndGltZXNfY2xvY2snICk7XHJcblx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRjYXNlICdmdWxsX2RheV9ib29raW5nJzpcclxuXHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2Z1bGxfZGF5X2Jvb2tpbmcnICk7XHJcblx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRjYXNlICdzZWFzb25fZmlsdGVyJzpcclxuXHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2RhdGVfdXNlcl91bmF2YWlsYWJsZScsICdzZWFzb25fdW5hdmFpbGFibGUnICk7XHJcblx0XHRcdFx0ZGF0ZV9ib29raW5nc19vYmpbICdzdW1tYXJ5J11bJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0gPSAnJztcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gUmVzZXQgYm9va2luZyBzdGF0dXMgY29sb3IgZm9yIHBvc3NpYmxlIG9sZCBib29raW5ncyBvbiB0aGlzIGRhdGVcclxuXHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdGNhc2UgJ3Jlc291cmNlX2F2YWlsYWJpbGl0eSc6XHJcblx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdkYXRlX3VzZXJfdW5hdmFpbGFibGUnLCAncmVzb3VyY2VfdW5hdmFpbGFibGUnICk7XHJcblx0XHRcdFx0ZGF0ZV9ib29raW5nc19vYmpbICdzdW1tYXJ5J11bJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0gPSAnJztcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gUmVzZXQgYm9va2luZyBzdGF0dXMgY29sb3IgZm9yIHBvc3NpYmxlIG9sZCBib29raW5ncyBvbiB0aGlzIGRhdGVcclxuXHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdGNhc2UgJ3dlZWtkYXlfdW5hdmFpbGFibGUnOlxyXG5cdFx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAnZGF0ZV91c2VyX3VuYXZhaWxhYmxlJywgJ3dlZWtkYXlfdW5hdmFpbGFibGUnICk7XHJcblx0XHRcdFx0ZGF0ZV9ib29raW5nc19vYmpbICdzdW1tYXJ5J11bJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0gPSAnJztcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gUmVzZXQgYm9va2luZyBzdGF0dXMgY29sb3IgZm9yIHBvc3NpYmxlIG9sZCBib29raW5ncyBvbiB0aGlzIGRhdGVcclxuXHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdGNhc2UgJ2Zyb21fdG9kYXlfdW5hdmFpbGFibGUnOlxyXG5cdFx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAnZGF0ZV91c2VyX3VuYXZhaWxhYmxlJywgJ2Zyb21fdG9kYXlfdW5hdmFpbGFibGUnICk7XHJcblx0XHRcdFx0ZGF0ZV9ib29raW5nc19vYmpbICdzdW1tYXJ5J11bJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0gPSAnJztcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gUmVzZXQgYm9va2luZyBzdGF0dXMgY29sb3IgZm9yIHBvc3NpYmxlIG9sZCBib29raW5ncyBvbiB0aGlzIGRhdGVcclxuXHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdGNhc2UgJ2xpbWl0X2F2YWlsYWJsZV9mcm9tX3RvZGF5JzpcclxuXHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2RhdGVfdXNlcl91bmF2YWlsYWJsZScsICdsaW1pdF9hdmFpbGFibGVfZnJvbV90b2RheScgKTtcclxuXHRcdFx0XHRkYXRlX2Jvb2tpbmdzX29ialsgJ3N1bW1hcnknXVsnc3RhdHVzX2Zvcl9ib29raW5ncycgXSA9ICcnO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBSZXNldCBib29raW5nIHN0YXR1cyBjb2xvciBmb3IgcG9zc2libGUgb2xkIGJvb2tpbmdzIG9uIHRoaXMgZGF0ZVxyXG5cdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0Y2FzZSAnY2hhbmdlX292ZXInOlxyXG5cdFx0XHRcdC8qXHJcblx0XHRcdFx0ICpcclxuXHRcdFx0XHQvLyAgY2hlY2tfb3V0X3RpbWVfZGF0ZTJhcHByb3ZlIFx0IFx0Y2hlY2tfaW5fdGltZV9kYXRlMmFwcHJvdmVcclxuXHRcdFx0XHQvLyAgY2hlY2tfb3V0X3RpbWVfZGF0ZTJhcHByb3ZlIFx0IFx0Y2hlY2tfaW5fdGltZV9kYXRlX2FwcHJvdmVkXHJcblx0XHRcdFx0Ly8gIGNoZWNrX2luX3RpbWVfZGF0ZTJhcHByb3ZlIFx0XHQgXHRjaGVja19vdXRfdGltZV9kYXRlX2FwcHJvdmVkXHJcblx0XHRcdFx0Ly8gIGNoZWNrX291dF90aW1lX2RhdGVfYXBwcm92ZWQgXHQgXHRjaGVja19pbl90aW1lX2RhdGVfYXBwcm92ZWRcclxuXHRcdFx0XHQgKi9cclxuXHJcblx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICd0aW1lc3BhcnRseScsICdjaGVja19pbl90aW1lJywgJ2NoZWNrX291dF90aW1lJyApO1xyXG5cdFx0XHRcdC8vIEZpeEluOiAxMC4wLjAuMi5cclxuXHRcdFx0XHRpZiAoIGRhdGVfYm9va2luZ3Nfb2JqWyAnc3VtbWFyeScgXVsgJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0uaW5kZXhPZiggJ2FwcHJvdmVkX3BlbmRpbmcnICkgPiAtMSApe1xyXG5cdFx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdjaGVja19vdXRfdGltZV9kYXRlX2FwcHJvdmVkJywgJ2NoZWNrX2luX3RpbWVfZGF0ZTJhcHByb3ZlJyApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRpZiAoIGRhdGVfYm9va2luZ3Nfb2JqWyAnc3VtbWFyeScgXVsgJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0uaW5kZXhPZiggJ3BlbmRpbmdfYXBwcm92ZWQnICkgPiAtMSApe1xyXG5cdFx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdjaGVja19vdXRfdGltZV9kYXRlMmFwcHJvdmUnLCAnY2hlY2tfaW5fdGltZV9kYXRlX2FwcHJvdmVkJyApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdGNhc2UgJ2NoZWNrX2luJzpcclxuXHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ3RpbWVzcGFydGx5JywgJ2NoZWNrX2luX3RpbWUnICk7XHJcblxyXG5cdFx0XHRcdC8vIEZpeEluOiA5LjkuMC4zMy5cclxuXHRcdFx0XHRpZiAoIGRhdGVfYm9va2luZ3Nfb2JqWyAnc3VtbWFyeScgXVsgJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0uaW5kZXhPZiggJ3BlbmRpbmcnICkgPiAtMSApe1xyXG5cdFx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdjaGVja19pbl90aW1lX2RhdGUyYXBwcm92ZScgKTtcclxuXHRcdFx0XHR9IGVsc2UgaWYgKCBkYXRlX2Jvb2tpbmdzX29ialsgJ3N1bW1hcnknIF1bICdzdGF0dXNfZm9yX2Jvb2tpbmdzJyBdLmluZGV4T2YoICdhcHByb3ZlZCcgKSA+IC0xICl7XHJcblx0XHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2NoZWNrX2luX3RpbWVfZGF0ZV9hcHByb3ZlZCcgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRjYXNlICdjaGVja19vdXQnOlxyXG5cdFx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAndGltZXNwYXJ0bHknLCAnY2hlY2tfb3V0X3RpbWUnICk7XHJcblxyXG5cdFx0XHRcdC8vIEZpeEluOiA5LjkuMC4zMy5cclxuXHRcdFx0XHRpZiAoIGRhdGVfYm9va2luZ3Nfb2JqWyAnc3VtbWFyeScgXVsgJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0uaW5kZXhPZiggJ3BlbmRpbmcnICkgPiAtMSApe1xyXG5cdFx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdjaGVja19vdXRfdGltZV9kYXRlMmFwcHJvdmUnICk7XHJcblx0XHRcdFx0fSBlbHNlIGlmICggZGF0ZV9ib29raW5nc19vYmpbICdzdW1tYXJ5JyBdWyAnc3RhdHVzX2Zvcl9ib29raW5ncycgXS5pbmRleE9mKCAnYXBwcm92ZWQnICkgPiAtMSApe1xyXG5cdFx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdjaGVja19vdXRfdGltZV9kYXRlX2FwcHJvdmVkJyApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdGRlZmF1bHQ6XHJcblx0XHRcdFx0Ly8gbWl4ZWQgc3RhdHVzZXM6ICdjaGFuZ2Vfb3ZlciBjaGVja19vdXQnIC4uLi4gdmFyaWF0aW9ucy4uLi4gY2hlY2sgbW9yZSBpbiBcdFx0ZnVuY3Rpb24gd3BiY19nZXRfYXZhaWxhYmlsaXR5X3Blcl9kYXlzX2FycigpXHJcblx0XHRcdFx0ZGF0ZV9ib29raW5nc19vYmpbICdzdW1tYXJ5J11bJ3N0YXR1c19mb3JfZGF5JyBdID0gJ2F2YWlsYWJsZSc7XHJcblx0XHR9XHJcblxyXG5cclxuXHJcblx0XHRpZiAoICdhdmFpbGFibGUnICE9IGRhdGVfYm9va2luZ3Nfb2JqWyAnc3VtbWFyeSddWydzdGF0dXNfZm9yX2RheScgXSApe1xyXG5cclxuXHRcdFx0dmFyIGlzX3NldF9wZW5kaW5nX2RheXNfc2VsZWN0YWJsZSA9IF93cGJjLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAncGVuZGluZ19kYXlzX3NlbGVjdGFibGUnICk7XHQvLyBzZXQgcGVuZGluZyBkYXlzIHNlbGVjdGFibGUgICAgICAgICAgLy8gRml4SW46IDguNi4xLjE4LlxyXG5cclxuXHRcdFx0c3dpdGNoICggZGF0ZV9ib29raW5nc19vYmpbICdzdW1tYXJ5J11bJ3N0YXR1c19mb3JfYm9va2luZ3MnIF0gKXtcclxuXHJcblx0XHRcdFx0Y2FzZSAnJzpcclxuXHRcdFx0XHRcdC8vIFVzdWFsbHkgIGl0J3MgbWVhbnMgdGhhdCBkYXkgIGlzIGF2YWlsYWJsZSBvciB1bmF2YWlsYWJsZSB3aXRob3V0IHRoZSBib29raW5nc1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRcdGNhc2UgJ3BlbmRpbmcnOlxyXG5cdFx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdkYXRlMmFwcHJvdmUnICk7XHJcblx0XHRcdFx0XHRpc19kYXlfc2VsZWN0YWJsZSA9IChpc19kYXlfc2VsZWN0YWJsZSkgPyB0cnVlIDogaXNfc2V0X3BlbmRpbmdfZGF5c19zZWxlY3RhYmxlO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRcdGNhc2UgJ2FwcHJvdmVkJzpcclxuXHRcdFx0XHRcdGNzc19jbGFzc2VzX19mb3JfZGF0ZS5wdXNoKCAnZGF0ZV9hcHByb3ZlZCcgKTtcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHQvLyBTaXR1YXRpb25zIGZvciBcImNoYW5nZS1vdmVyXCIgZGF5czogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdGNhc2UgJ3BlbmRpbmdfcGVuZGluZyc6XHJcblx0XHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2NoZWNrX291dF90aW1lX2RhdGUyYXBwcm92ZScsICdjaGVja19pbl90aW1lX2RhdGUyYXBwcm92ZScgKTtcclxuXHRcdFx0XHRcdGlzX2RheV9zZWxlY3RhYmxlID0gKGlzX2RheV9zZWxlY3RhYmxlKSA/IHRydWUgOiBpc19zZXRfcGVuZGluZ19kYXlzX3NlbGVjdGFibGU7XHJcblx0XHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdFx0Y2FzZSAncGVuZGluZ19hcHByb3ZlZCc6XHJcblx0XHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2NoZWNrX291dF90aW1lX2RhdGUyYXBwcm92ZScsICdjaGVja19pbl90aW1lX2RhdGVfYXBwcm92ZWQnICk7XHJcblx0XHRcdFx0XHRpc19kYXlfc2VsZWN0YWJsZSA9IChpc19kYXlfc2VsZWN0YWJsZSkgPyB0cnVlIDogaXNfc2V0X3BlbmRpbmdfZGF5c19zZWxlY3RhYmxlO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRcdGNhc2UgJ2FwcHJvdmVkX3BlbmRpbmcnOlxyXG5cdFx0XHRcdFx0Y3NzX2NsYXNzZXNfX2Zvcl9kYXRlLnB1c2goICdjaGVja19vdXRfdGltZV9kYXRlX2FwcHJvdmVkJywgJ2NoZWNrX2luX3RpbWVfZGF0ZTJhcHByb3ZlJyApO1xyXG5cdFx0XHRcdFx0aXNfZGF5X3NlbGVjdGFibGUgPSAoaXNfZGF5X3NlbGVjdGFibGUpID8gdHJ1ZSA6IGlzX3NldF9wZW5kaW5nX2RheXNfc2VsZWN0YWJsZTtcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHRjYXNlICdhcHByb3ZlZF9hcHByb3ZlZCc6XHJcblx0XHRcdFx0XHRjc3NfY2xhc3Nlc19fZm9yX2RhdGUucHVzaCggJ2NoZWNrX291dF90aW1lX2RhdGVfYXBwcm92ZWQnLCAnY2hlY2tfaW5fdGltZV9kYXRlX2FwcHJvdmVkJyApO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRcdGRlZmF1bHQ6XHJcblxyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIFsgaXNfZGF5X3NlbGVjdGFibGUsIGNzc19jbGFzc2VzX19mb3JfZGF0ZS5qb2luKCAnICcgKSBdO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIE1vdXNlb3ZlciBjYWxlbmRhciBkYXRlIGNlbGxzXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gc3RyaW5nX2RhdGVcclxuXHQgKiBAcGFyYW0gZGF0ZVx0XHRcdFx0XHRcdFx0XHRcdFx0LSAgSmF2YVNjcmlwdCBEYXRlIE9iajogIFx0XHRNb24gRGVjIDExIDIwMjMgMDA6MDA6MDAgR01UKzAyMDAgKEVhc3Rlcm4gRXVyb3BlYW4gU3RhbmRhcmQgVGltZSlcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0XHRcdFx0XHRcdC0gIENhbGVuZGFyIFNldHRpbmdzIE9iamVjdDogIFx0e1xyXG5cdCAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFx0XHRcdFx0XHRcdFwicmVzb3VyY2VfaWRcIjogNFxyXG5cdCAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdCAqIEBwYXJhbSBkYXRlcGlja190aGlzXHRcdFx0XHRcdFx0XHRcdC0gdGhpcyBvZiBkYXRlcGljayBPYmpcclxuXHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX19jYWxlbmRhcl9fb25faG92ZXJfZGF5cyggc3RyaW5nX2RhdGUsIGRhdGUsIGNhbGVuZGFyX3BhcmFtc19hcnIsIGRhdGVwaWNrX3RoaXMgKSB7XHJcblxyXG5cdFx0aWYgKCBudWxsID09PSBkYXRlICkge1xyXG5cdFx0XHR3cGJjX2NhbGVuZGFyc19fY2xlYXJfZGF5c19oaWdobGlnaHRpbmcoICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChjYWxlbmRhcl9wYXJhbXNfYXJyWyAncmVzb3VyY2VfaWQnIF0pKSA/IGNhbGVuZGFyX3BhcmFtc19hcnJbICdyZXNvdXJjZV9pZCcgXSA6ICcxJyApO1x0XHQvLyBGaXhJbjogMTAuNS4yLjQuXHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgY2xhc3NfZGF5ICAgICA9IHdwYmNfX2dldF9fdGRfY2xhc3NfZGF0ZSggZGF0ZSApO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vICcxLTktMjAyMydcclxuXHRcdHZhciBzcWxfY2xhc3NfZGF5ID0gd3BiY19fZ2V0X19zcWxfY2xhc3NfZGF0ZSggZGF0ZSApO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vICcyMDIzLTAxLTA5J1xyXG5cdFx0dmFyIHJlc291cmNlX2lkID0gKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mKGNhbGVuZGFyX3BhcmFtc19hcnJbICdyZXNvdXJjZV9pZCcgXSkgKSA/IGNhbGVuZGFyX3BhcmFtc19hcnJbICdyZXNvdXJjZV9pZCcgXSA6ICcxJztcdFx0Ly8gJzEnXHJcblxyXG5cdFx0Ly8gR2V0IERhdGEgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdHZhciBkYXRlX2Jvb2tpbmdfb2JqID0gX3dwYmMuYm9va2luZ3NfaW5fY2FsZW5kYXJfX2dldF9mb3JfZGF0ZSggcmVzb3VyY2VfaWQsIHNxbF9jbGFzc19kYXkgKTtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gey4uLn1cclxuXHJcblx0XHRpZiAoICEgZGF0ZV9ib29raW5nX29iaiApeyByZXR1cm4gZmFsc2U7IH1cclxuXHJcblxyXG5cdFx0Ly8gVCBvIG8gbCB0IGkgcCBzIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdHZhciB0b29sdGlwX3RleHQgPSAnJztcclxuXHRcdGlmICggZGF0ZV9ib29raW5nX29ialsgJ3N1bW1hcnknXVsndG9vbHRpcF9hdmFpbGFiaWxpdHknIF0ubGVuZ3RoID4gMCApe1xyXG5cdFx0XHR0b29sdGlwX3RleHQgKz0gIGRhdGVfYm9va2luZ19vYmpbICdzdW1tYXJ5J11bJ3Rvb2x0aXBfYXZhaWxhYmlsaXR5JyBdO1xyXG5cdFx0fVxyXG5cdFx0aWYgKCBkYXRlX2Jvb2tpbmdfb2JqWyAnc3VtbWFyeSddWyd0b29sdGlwX2RheV9jb3N0JyBdLmxlbmd0aCA+IDAgKXtcclxuXHRcdFx0dG9vbHRpcF90ZXh0ICs9ICBkYXRlX2Jvb2tpbmdfb2JqWyAnc3VtbWFyeSddWyd0b29sdGlwX2RheV9jb3N0JyBdO1xyXG5cdFx0fVxyXG5cdFx0aWYgKCBkYXRlX2Jvb2tpbmdfb2JqWyAnc3VtbWFyeSddWyd0b29sdGlwX3RpbWVzJyBdLmxlbmd0aCA+IDAgKXtcclxuXHRcdFx0dG9vbHRpcF90ZXh0ICs9ICBkYXRlX2Jvb2tpbmdfb2JqWyAnc3VtbWFyeSddWyd0b29sdGlwX3RpbWVzJyBdO1xyXG5cdFx0fVxyXG5cdFx0aWYgKCBkYXRlX2Jvb2tpbmdfb2JqWyAnc3VtbWFyeSddWyd0b29sdGlwX2Jvb2tpbmdfZGV0YWlscycgXS5sZW5ndGggPiAwICl7XHJcblx0XHRcdHRvb2x0aXBfdGV4dCArPSAgZGF0ZV9ib29raW5nX29ialsgJ3N1bW1hcnknXVsndG9vbHRpcF9ib29raW5nX2RldGFpbHMnIF07XHJcblx0XHR9XHJcblx0XHR3cGJjX3NldF90b29sdGlwX19fZm9yX19jYWxlbmRhcl9kYXRlKCB0b29sdGlwX3RleHQsIHJlc291cmNlX2lkLCBjbGFzc19kYXkgKTtcclxuXHJcblxyXG5cclxuXHRcdC8vICBVIG4gaCBvIHYgZSByIGkgbiBnICAgIGluICAgIFVOU0VMRUNUQUJMRV9DQUxFTkRBUiAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHR2YXIgaXNfdW5zZWxlY3RhYmxlX2NhbGVuZGFyID0gKCBqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZ191bnNlbGVjdGFibGUnICsgcmVzb3VyY2VfaWQgKS5sZW5ndGggPiAwKTtcdFx0XHRcdC8vIEZpeEluOiA4LjAuMS4yLlxyXG5cdFx0dmFyIGlzX2Jvb2tpbmdfZm9ybV9leGlzdCAgICA9ICggalF1ZXJ5KCAnI2Jvb2tpbmdfZm9ybV9kaXYnICsgcmVzb3VyY2VfaWQgKS5sZW5ndGggPiAwICk7XHJcblxyXG5cdFx0aWYgKCAoIGlzX3Vuc2VsZWN0YWJsZV9jYWxlbmRhciApICYmICggISBpc19ib29raW5nX2Zvcm1fZXhpc3QgKSApe1xyXG5cclxuXHRcdFx0LyoqXHJcblx0XHRcdCAqICBVbiBIb3ZlciBhbGwgZGF0ZXMgaW4gY2FsZW5kYXIgKHdpdGhvdXQgdGhlIGJvb2tpbmcgZm9ybSksIGlmIG9ubHkgQXZhaWxhYmlsaXR5IENhbGVuZGFyIGhlcmUgYW5kIHdlIGRvIG5vdCBpbnNlcnQgQm9va2luZyBmb3JtIGJ5IG1pc3Rha2UuXHJcblx0XHRcdCAqL1xyXG5cclxuXHRcdFx0d3BiY19jYWxlbmRhcnNfX2NsZWFyX2RheXNfaGlnaGxpZ2h0aW5nKCByZXNvdXJjZV9pZCApOyBcdFx0XHRcdFx0XHRcdC8vIENsZWFyIGRheXMgaGlnaGxpZ2h0aW5nXHJcblxyXG5cdFx0XHR2YXIgY3NzX29mX2NhbGVuZGFyID0gJy53cGJjX29ubHlfY2FsZW5kYXIgI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQ7XHJcblx0XHRcdGpRdWVyeSggY3NzX29mX2NhbGVuZGFyICsgJyAuZGF0ZXBpY2stZGF5cy1jZWxsLCAnXHJcblx0XHRcdFx0ICArIGNzc19vZl9jYWxlbmRhciArICcgLmRhdGVwaWNrLWRheXMtY2VsbCBhJyApLmNzcyggJ2N1cnNvcicsICdkZWZhdWx0JyApO1x0Ly8gU2V0IGN1cnNvciB0byBEZWZhdWx0XHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblxyXG5cclxuXHRcdC8vICBEIGEgeSBzICAgIEggbyB2IGUgciBpIG4gZyAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRpZiAoXHJcblx0XHRcdCAgICggbG9jYXRpb24uaHJlZi5pbmRleE9mKCAncGFnZT13cGJjJyApID09IC0xIClcclxuXHRcdFx0fHwgKCBsb2NhdGlvbi5ocmVmLmluZGV4T2YoICdwYWdlPXdwYmMtbmV3JyApID4gMCApXHJcblx0XHRcdHx8ICggbG9jYXRpb24uaHJlZi5pbmRleE9mKCAncGFnZT13cGJjLXNldHVwJyApID4gMCApXHJcblx0XHRcdHx8ICggbG9jYXRpb24uaHJlZi5pbmRleE9mKCAncGFnZT13cGJjLWF2YWlsYWJpbGl0eScgKSA+IDAgKVxyXG5cdFx0XHR8fCAoICAoIGxvY2F0aW9uLmhyZWYuaW5kZXhPZiggJ3BhZ2U9d3BiYy1zZXR0aW5ncycgKSA+IDAgKSAgJiZcclxuXHRcdFx0XHQgICggbG9jYXRpb24uaHJlZi5pbmRleE9mKCAnJnRhYj1mb3JtJyApID4gMCApXHJcblx0XHRcdCAgIClcclxuXHRcdCl7XHJcblx0XHRcdC8vIFRoZSBzYW1lIGFzIGRhdGVzIHNlbGVjdGlvbiwgIGJ1dCBmb3IgZGF5cyBob3ZlcmluZ1xyXG5cclxuXHRcdFx0aWYgKCAnZnVuY3Rpb24nID09IHR5cGVvZiggd3BiY19fY2FsZW5kYXJfX2RvX2RheXNfaGlnaGxpZ2h0X19icyApICl7XHJcblx0XHRcdFx0d3BiY19fY2FsZW5kYXJfX2RvX2RheXNfaGlnaGxpZ2h0X19icyggc3FsX2NsYXNzX2RheSwgZGF0ZSwgcmVzb3VyY2VfaWQgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBTZWxlY3QgY2FsZW5kYXIgZGF0ZSBjZWxsc1xyXG5cdCAqXHJcblx0ICogQHBhcmFtIGRhdGVcdFx0XHRcdFx0XHRcdFx0XHRcdC0gIEphdmFTY3JpcHQgRGF0ZSBPYmo6ICBcdFx0TW9uIERlYyAxMSAyMDIzIDAwOjAwOjAwIEdNVCswMjAwIChFYXN0ZXJuIEV1cm9wZWFuIFN0YW5kYXJkIFRpbWUpXHJcblx0ICogQHBhcmFtIGNhbGVuZGFyX3BhcmFtc19hcnJcdFx0XHRcdFx0XHQtICBDYWxlbmRhciBTZXR0aW5ncyBPYmplY3Q6ICBcdHtcclxuXHQgKlx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICBcdFx0XHRcdFx0XHRcInJlc291cmNlX2lkXCI6IDRcclxuXHQgKlx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHQgKiBAcGFyYW0gZGF0ZXBpY2tfdGhpc1x0XHRcdFx0XHRcdFx0XHQtIHRoaXMgb2YgZGF0ZXBpY2sgT2JqXHJcblx0ICpcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX19jYWxlbmRhcl9fb25fc2VsZWN0X2RheXMoIGRhdGUsIGNhbGVuZGFyX3BhcmFtc19hcnIsIGRhdGVwaWNrX3RoaXMgKXtcclxuXHJcblx0XHR2YXIgcmVzb3VyY2VfaWQgPSAoICd1bmRlZmluZWQnICE9PSB0eXBlb2YoY2FsZW5kYXJfcGFyYW1zX2FyclsgJ3Jlc291cmNlX2lkJyBdKSApID8gY2FsZW5kYXJfcGFyYW1zX2FyclsgJ3Jlc291cmNlX2lkJyBdIDogJzEnO1x0XHQvLyAnMSdcclxuXHJcblx0XHQvLyBTZXQgdW5zZWxlY3RhYmxlLCAgaWYgb25seSBBdmFpbGFiaWxpdHkgQ2FsZW5kYXIgIGhlcmUgKGFuZCB3ZSBkbyBub3QgaW5zZXJ0IEJvb2tpbmcgZm9ybSBieSBtaXN0YWtlKS5cclxuXHRcdHZhciBpc191bnNlbGVjdGFibGVfY2FsZW5kYXIgPSAoIGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nX3Vuc2VsZWN0YWJsZScgKyByZXNvdXJjZV9pZCApLmxlbmd0aCA+IDApO1x0XHRcdFx0Ly8gRml4SW46IDguMC4xLjIuXHJcblx0XHR2YXIgaXNfYm9va2luZ19mb3JtX2V4aXN0ICAgID0gKCBqUXVlcnkoICcjYm9va2luZ19mb3JtX2RpdicgKyByZXNvdXJjZV9pZCApLmxlbmd0aCA+IDAgKTtcclxuXHRcdGlmICggKCBpc191bnNlbGVjdGFibGVfY2FsZW5kYXIgKSAmJiAoICEgaXNfYm9va2luZ19mb3JtX2V4aXN0ICkgKXtcclxuXHRcdFx0d3BiY19jYWxlbmRhcl9fdW5zZWxlY3RfYWxsX2RhdGVzKCByZXNvdXJjZV9pZCApO1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gVW5zZWxlY3QgRGF0ZXNcclxuXHRcdFx0alF1ZXJ5KCcud3BiY19vbmx5X2NhbGVuZGFyIC5wb3BvdmVyX2NhbGVuZGFyX2hvdmVyJykucmVtb3ZlKCk7ICAgICAgICAgICAgICAgICAgICAgIFx0XHRcdFx0XHRcdFx0Ly8gSGlkZSBhbGwgb3BlbmVkIHBvcG92ZXJzXHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblx0XHRqUXVlcnkoICcjZGF0ZV9ib29raW5nJyArIHJlc291cmNlX2lkICkudmFsKCBkYXRlICk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBBZGQgc2VsZWN0ZWQgZGF0ZXMgdG8gIGhpZGRlbiB0ZXh0YXJlYVxyXG5cclxuXHJcblx0XHRpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiAod3BiY19fY2FsZW5kYXJfX2RvX2RheXNfc2VsZWN0X19icykgKXsgd3BiY19fY2FsZW5kYXJfX2RvX2RheXNfc2VsZWN0X19icyggZGF0ZSwgcmVzb3VyY2VfaWQgKTsgfVxyXG5cclxuXHRcdHdwYmNfZGlzYWJsZV90aW1lX2ZpZWxkc19pbl9ib29raW5nX2Zvcm0oIHJlc291cmNlX2lkICk7XHJcblxyXG5cdFx0Ly8gSG9vayAtLSB0cmlnZ2VyIGRheSBzZWxlY3Rpb24gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdHZhciBtb3VzZV9jbGlja2VkX2RhdGVzID0gZGF0ZTtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIENhbiBiZTogXCIwNS4xMC4yMDIzIC0gMDcuMTAuMjAyM1wiICB8ICBcIjEwLjEwLjIwMjMgLSAxMC4xMC4yMDIzXCIgIHxcclxuXHRcdHZhciBhbGxfc2VsZWN0ZWRfZGF0ZXNfYXJyID0gd3BiY19nZXRfX3NlbGVjdGVkX2RhdGVzX3NxbF9fYXNfYXJyKCByZXNvdXJjZV9pZCApO1x0XHRcdFx0XHRcdFx0XHRcdC8vIENhbiBiZTogWyBcIjIwMjMtMTAtMDVcIiwgXCIyMDIzLTEwLTA2XCIsIFwiMjAyMy0xMC0wN1wiLCDigKYgXVxyXG5cdFx0alF1ZXJ5KCBcIi5ib29raW5nX2Zvcm1fZGl2XCIgKS50cmlnZ2VyKCBcImRhdGVfc2VsZWN0ZWRcIiwgWyByZXNvdXJjZV9pZCwgbW91c2VfY2xpY2tlZF9kYXRlcywgYWxsX3NlbGVjdGVkX2RhdGVzX2FyciBdICk7XHJcblx0fVxyXG5cclxuXHQvLyBNYXJrIG1pZGRsZSBzZWxlY3RlZCBkYXRlcyB3aXRoIDAuNSBvcGFjaXR5XHRcdC8vIEZpeEluOiAxMC4zLjAuOS5cclxuXHRqUXVlcnkoIGRvY3VtZW50ICkucmVhZHkoIGZ1bmN0aW9uICgpe1xyXG5cdFx0alF1ZXJ5KCBcIi5ib29raW5nX2Zvcm1fZGl2XCIgKS5vbiggJ2RhdGVfc2VsZWN0ZWQnLCBmdW5jdGlvbiAoIGV2ZW50LCByZXNvdXJjZV9pZCwgZGF0ZSApe1xyXG5cdFx0XHRcdGlmIChcclxuXHRcdFx0XHRcdCAgICggICdmaXhlZCcgPT09IF93cGJjLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnZGF5c19zZWxlY3RfbW9kZScgKSlcclxuXHRcdFx0XHRcdHx8ICgnZHluYW1pYycgPT09IF93cGJjLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnZGF5c19zZWxlY3RfbW9kZScgKSlcclxuXHRcdFx0XHQpe1xyXG5cdFx0XHRcdFx0dmFyIGNsb3NlZF90aW1lciA9IHNldFRpbWVvdXQoIGZ1bmN0aW9uICgpe1xyXG5cdFx0XHRcdFx0XHR2YXIgbWlkZGxlX2RheXNfb3BhY2l0eSA9IF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ2NhbGVuZGFyc19fZGF5c19zZWxlY3Rpb25fX21pZGRsZV9kYXlzX29wYWNpdHknICk7XHJcblx0XHRcdFx0XHRcdGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIHJlc291cmNlX2lkICsgJyAuZGF0ZXBpY2stY3VycmVudC1kYXknICkubm90KCBcIi5zZWxlY3RlZF9jaGVja19pbl9vdXRcIiApLmNzcyggJ29wYWNpdHknLCBtaWRkbGVfZGF5c19vcGFjaXR5ICk7XHJcblx0XHRcdFx0XHR9LCAxMCApO1xyXG5cdFx0XHRcdH1cclxuXHRcdH0gKTtcclxuXHR9ICk7XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiAtLSAgVCBpIG0gZSAgICBGIGkgZSBsIGQgcyAgICAgc3RhcnQgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0ICovXHJcblxyXG5cdC8qKlxyXG5cdCAqIERpc2FibGUgdGltZSBzbG90cyBpbiBib29raW5nIGZvcm0gZGVwZW5kIG9uIHNlbGVjdGVkIGRhdGVzIGFuZCBib29rZWQgZGF0ZXMvdGltZXNcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfZGlzYWJsZV90aW1lX2ZpZWxkc19pbl9ib29raW5nX2Zvcm0oIHJlc291cmNlX2lkICl7XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBcdDEuIEdldCBhbGwgdGltZSBmaWVsZHMgaW4gdGhlIGJvb2tpbmcgZm9ybSBhcyBhcnJheSAgb2Ygb2JqZWN0c1xyXG5cdFx0ICogXHRcdFx0XHRcdFtcclxuXHRcdCAqIFx0XHRcdFx0XHQgXHQgICB7XHRqcXVlcnlfb3B0aW9uOiAgICAgIGpRdWVyeV9PYmplY3Qge31cclxuXHRcdCAqIFx0XHRcdFx0XHRcdFx0XHRuYW1lOiAgICAgICAgICAgICAgICdyYW5nZXRpbWUyW10nXHJcblx0XHQgKiBcdFx0XHRcdFx0XHRcdFx0dGltZXNfYXNfc2Vjb25kczogICBbIDIxNjAwLCAyMzQwMCBdXHJcblx0XHQgKiBcdFx0XHRcdFx0XHRcdFx0dmFsdWVfb3B0aW9uXzI0aDogICAnMDY6MDAgLSAwNjozMCdcclxuXHRcdCAqIFx0XHRcdFx0XHQgICAgIH1cclxuXHRcdCAqIFx0XHRcdFx0XHQgIC4uLlxyXG5cdFx0ICogXHRcdFx0XHRcdFx0ICAge1x0anF1ZXJ5X29wdGlvbjogICAgICBqUXVlcnlfT2JqZWN0IHt9XHJcblx0XHQgKiBcdFx0XHRcdFx0XHRcdFx0bmFtZTogICAgICAgICAgICAgICAnc3RhcnR0aW1lMltdJ1xyXG5cdFx0ICogXHRcdFx0XHRcdFx0XHRcdHRpbWVzX2FzX3NlY29uZHM6ICAgWyAyMTYwMCBdXHJcblx0XHQgKiBcdFx0XHRcdFx0XHRcdFx0dmFsdWVfb3B0aW9uXzI0aDogICAnMDY6MDAnXHJcblx0XHQgKiAgXHRcdFx0XHRcdCAgICB9XHJcblx0XHQgKiBcdFx0XHRcdFx0IF1cclxuXHRcdCAqL1xyXG5cdFx0dmFyIHRpbWVfZmllbGRzX29ial9hcnIgPSB3cGJjX2dldF9fdGltZV9maWVsZHNfX2luX2Jvb2tpbmdfZm9ybV9fYXNfYXJyKCByZXNvdXJjZV9pZCApO1xyXG5cclxuXHRcdC8vIDIuIEdldCBhbGwgc2VsZWN0ZWQgZGF0ZXMgaW4gIFNRTCBmb3JtYXQgIGxpa2UgdGhpcyBbIFwiMjAyMy0wOC0yM1wiLCBcIjIwMjMtMDgtMjRcIiwgXCIyMDIzLTA4LTI1XCIsIC4uLiBdXHJcblx0XHR2YXIgc2VsZWN0ZWRfZGF0ZXNfYXJyID0gd3BiY19nZXRfX3NlbGVjdGVkX2RhdGVzX3NxbF9fYXNfYXJyKCByZXNvdXJjZV9pZCApO1xyXG5cclxuXHRcdC8vIDMuIEdldCBjaGlsZCBib29raW5nIHJlc291cmNlcyAgb3Igc2luZ2xlIGJvb2tpbmcgcmVzb3VyY2UgIHRoYXQgIGV4aXN0ICBpbiBkYXRlc1xyXG5cdFx0dmFyIGNoaWxkX3Jlc291cmNlc19hcnIgPSB3cGJjX2Nsb25lX29iaiggX3dwYmMuYm9va2luZ19fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ3Jlc291cmNlc19pZF9hcnJfX2luX2RhdGVzJyApICk7XHJcblxyXG5cdFx0dmFyIHNxbF9kYXRlO1xyXG5cdFx0dmFyIGNoaWxkX3Jlc291cmNlX2lkO1xyXG5cdFx0dmFyIG1lcmdlZF9zZWNvbmRzO1xyXG5cdFx0dmFyIHRpbWVfZmllbGRzX29iajtcclxuXHRcdHZhciBpc19pbnRlcnNlY3Q7XHJcblx0XHR2YXIgaXNfY2hlY2tfaW47XHJcblxyXG5cdFx0dmFyIHRvZGF5X3RpbWVfX3JlYWwgID0gbmV3IERhdGUoIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RpbWVfbG9jYWxfYXJyJyApWzBdLCAoIHBhcnNlSW50KCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0aW1lX2xvY2FsX2FycicgKVsxXSApIC0gMSksIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RpbWVfbG9jYWxfYXJyJyApWzJdLCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0aW1lX2xvY2FsX2FycicgKVszXSwgX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAndGltZV9sb2NhbF9hcnInIClbNF0sIDAgKTtcclxuXHRcdHZhciB0b2RheV90aW1lX19zaGlmdCA9IG5ldyBEYXRlKCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0b2RheV9hcnInICAgICAgKVswXSwgKCBwYXJzZUludCggX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAgICAgICd0b2RheV9hcnInIClbMV0gKSAtIDEpLCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0b2RheV9hcnInICAgICAgKVsyXSwgX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAndG9kYXlfYXJyJyAgICAgIClbM10sIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RvZGF5X2FycicgICAgICApWzRdLCAwICk7XHJcblxyXG5cdFx0Ly8gNC4gTG9vcCAgYWxsICB0aW1lIEZpZWxkcyBvcHRpb25zXHRcdC8vIEZpeEluOiAxMC4zLjAuMi5cclxuXHRcdGZvciAoIGxldCBmaWVsZF9rZXkgPSAwOyBmaWVsZF9rZXkgPCB0aW1lX2ZpZWxkc19vYmpfYXJyLmxlbmd0aDsgZmllbGRfa2V5KysgKXtcclxuXHJcblx0XHRcdHRpbWVfZmllbGRzX29ial9hcnJbIGZpZWxkX2tleSBdLmRpc2FibGVkID0gMDsgICAgICAgICAgLy8gQnkgZGVmYXVsdCwgdGhpcyB0aW1lIGZpZWxkIGlzIG5vdCBkaXNhYmxlZC5cclxuXHJcblx0XHRcdHRpbWVfZmllbGRzX29iaiA9IHRpbWVfZmllbGRzX29ial9hcnJbIGZpZWxkX2tleSBdO1x0XHQvLyB7IHRpbWVzX2FzX3NlY29uZHM6IFsgMjE2MDAsIDIzNDAwIF0sIHZhbHVlX29wdGlvbl8yNGg6ICcwNjowMCAtIDA2OjMwJywgbmFtZTogJ3JhbmdldGltZTJbXScsIGpxdWVyeV9vcHRpb246IGpRdWVyeV9PYmplY3Qge319XHJcblxyXG5cdFx0XHQvLyBMb29wICBhbGwgIHNlbGVjdGVkIGRhdGVzLlxyXG5cdFx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCBzZWxlY3RlZF9kYXRlc19hcnIubGVuZ3RoOyBpKysgKSB7XHJcblxyXG5cdFx0XHRcdC8vIEdldCBEYXRlOiAnMjAyMy0wOC0xOCcuXHJcblx0XHRcdFx0c3FsX2RhdGUgPSBzZWxlY3RlZF9kYXRlc19hcnJbaV07XHJcblxyXG5cdFx0XHRcdHZhciBpc190aW1lX2luX3Bhc3QgPSB3cGJjX2NoZWNrX2lzX3RpbWVfaW5fcGFzdCggdG9kYXlfdGltZV9fc2hpZnQsIHNxbF9kYXRlLCB0aW1lX2ZpZWxkc19vYmogKTtcclxuXHRcdFx0XHQvLyBFeGNlcHRpb24gIGZvciAnRW5kIFRpbWUnIGZpZWxkLCAgd2hlbiAgc2VsZWN0ZWQgc2V2ZXJhbCBkYXRlcy4gLy8gRml4SW46IDEwLjE0LjEuNS5cclxuXHRcdFx0XHRpZiAoICgnT24nICE9PSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2Jvb2tpbmdfcmVjdXJyZW50X3RpbWUnICkpICYmXHJcblx0XHRcdFx0XHQoLTEgIT09IHRpbWVfZmllbGRzX29iai5uYW1lLmluZGV4T2YoICdlbmR0aW1lJyApKSAmJlxyXG5cdFx0XHRcdFx0KHNlbGVjdGVkX2RhdGVzX2Fyci5sZW5ndGggPiAxKVxyXG5cdFx0XHRcdCkge1xyXG5cdFx0XHRcdFx0aXNfdGltZV9pbl9wYXN0ID0gd3BiY19jaGVja19pc190aW1lX2luX3Bhc3QoIHRvZGF5X3RpbWVfX3NoaWZ0LCBzZWxlY3RlZF9kYXRlc19hcnJbKHNlbGVjdGVkX2RhdGVzX2Fyci5sZW5ndGggLSAxKV0sIHRpbWVfZmllbGRzX29iaiApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRpZiAoIGlzX3RpbWVfaW5fcGFzdCApIHtcclxuXHRcdFx0XHRcdC8vIFRoaXMgdGltZSBmb3Igc2VsZWN0ZWQgZGF0ZSBhbHJlYWR5ICBpbiB0aGUgcGFzdC5cclxuXHRcdFx0XHRcdHRpbWVfZmllbGRzX29ial9hcnJbZmllbGRfa2V5XS5kaXNhYmxlZCA9IDE7XHJcblx0XHRcdFx0XHRicmVhaztcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gZXhpc3QgIGZyb20gICBEYXRlcyBMT09QLlxyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHQvLyBGaXhJbjogOS45LjAuMzEuXHJcblx0XHRcdFx0aWYgKFxyXG5cdFx0XHRcdFx0ICAgKCAnT2ZmJyA9PT0gX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdib29raW5nX3JlY3VycmVudF90aW1lJyApIClcclxuXHRcdFx0XHRcdCYmICggc2VsZWN0ZWRfZGF0ZXNfYXJyLmxlbmd0aD4xIClcclxuXHRcdFx0XHQpe1xyXG5cdFx0XHRcdFx0Ly9UT0RPOiBza2lwIHNvbWUgZmllbGRzIGNoZWNraW5nIGlmIGl0J3Mgc3RhcnQgLyBlbmQgdGltZSBmb3IgbXVscGxlIGRhdGVzICBzZWxlY3Rpb24gIG1vZGUuXHJcblx0XHRcdFx0XHQvL1RPRE86IHdlIG5lZWQgdG8gZml4IHNpdHVhdGlvbiAgZm9yIGVudGltZXMsICB3aGVuICB1c2VyICBzZWxlY3QgIHNldmVyYWwgIGRhdGVzLCAgYW5kIGluIHN0YXJ0ICB0aW1lIGJvb2tlZCAwMDowMCAtIDE1OjAwICwgYnV0IHN5c3RzbWUgYmxvY2sgdW50aWxsIDE1OjAwIHRoZSBlbmQgdGltZSBhcyB3ZWxsLCAgd2hpY2ggIGlzIHdyb25nLCAgYmVjYXVzZSBpdCAyIG9yIDMgZGF0ZXMgc2VsZWN0aW9uICBhbmQgZW5kIGRhdGUgY2FuIGJlIGZ1bGx1ICBhdmFpbGFibGVcclxuXHJcblx0XHRcdFx0XHRpZiAoICgwID09IGkpICYmICh0aW1lX2ZpZWxkc19vYmpbICduYW1lJyBdLmluZGV4T2YoICdlbmR0aW1lJyApID49IDApICl7XHJcblx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0aWYgKCAoIChzZWxlY3RlZF9kYXRlc19hcnIubGVuZ3RoLTEpID09IGkgKSAmJiAodGltZV9maWVsZHNfb2JqWyAnbmFtZScgXS5pbmRleE9mKCAnc3RhcnR0aW1lJyApID49IDApICl7XHJcblx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblxyXG5cclxuXHRcdFx0XHR2YXIgaG93X21hbnlfcmVzb3VyY2VzX2ludGVyc2VjdGVkID0gMDtcclxuXHRcdFx0XHQvLyBMb29wIGFsbCByZXNvdXJjZXMgSURcclxuXHRcdFx0XHRcdC8vIGZvciAoIHZhciByZXNfa2V5IGluIGNoaWxkX3Jlc291cmNlc19hcnIgKXtcdCBcdFx0XHRcdFx0XHQvLyBGaXhJbjogMTAuMy4wLjIuXHJcblx0XHRcdFx0Zm9yICggbGV0IHJlc19rZXkgPSAwOyByZXNfa2V5IDwgY2hpbGRfcmVzb3VyY2VzX2Fyci5sZW5ndGg7IHJlc19rZXkrKyApe1xyXG5cclxuXHRcdFx0XHRcdGNoaWxkX3Jlc291cmNlX2lkID0gY2hpbGRfcmVzb3VyY2VzX2FyclsgcmVzX2tleSBdO1xyXG5cclxuXHRcdFx0XHRcdC8vIF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZm9yX2RhdGUoMiwnMjAyMy0wOC0yMScpWzEyXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfc2Vjb25kc1x0XHQ9IFsgXCIwNzowMDoxMSAtIDA3OjMwOjAyXCIsIFwiMTA6MDA6MTEgLSAwMDowMDowMFwiIF1cclxuXHRcdFx0XHRcdC8vIF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZm9yX2RhdGUoMiwnMjAyMy0wOC0yMScpWzJdLmJvb2tlZF90aW1lX3Nsb3RzLm1lcmdlZF9zZWNvbmRzXHRcdFx0PSBbICBbIDI1MjExLCAyNzAwMiBdLCBbIDM2MDExLCA4NjQwMCBdICBdXHJcblxyXG5cdFx0XHRcdFx0aWYgKCBmYWxzZSAhPT0gX3dwYmMuYm9va2luZ3NfaW5fY2FsZW5kYXJfX2dldF9mb3JfZGF0ZSggcmVzb3VyY2VfaWQsIHNxbF9kYXRlICkgKXtcclxuXHRcdFx0XHRcdFx0bWVyZ2VkX3NlY29uZHMgPSBfd3BiYy5ib29raW5nc19pbl9jYWxlbmRhcl9fZ2V0X2Zvcl9kYXRlKCByZXNvdXJjZV9pZCwgc3FsX2RhdGUgKVsgY2hpbGRfcmVzb3VyY2VfaWQgXS5ib29rZWRfdGltZV9zbG90cy5tZXJnZWRfc2Vjb25kcztcdFx0Ly8gWyAgWyAyNTIxMSwgMjcwMDIgXSwgWyAzNjAxMSwgODY0MDAgXSAgXVxyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0bWVyZ2VkX3NlY29uZHMgPSBbXTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGlmICggdGltZV9maWVsZHNfb2JqLnRpbWVzX2FzX3NlY29uZHMubGVuZ3RoID4gMSApe1xyXG5cdFx0XHRcdFx0XHRpc19pbnRlcnNlY3QgPSB3cGJjX2lzX2ludGVyc2VjdF9fcmFuZ2VfdGltZV9pbnRlcnZhbCggIFtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0W1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCggcGFyc2VJbnQoIHRpbWVfZmllbGRzX29iai50aW1lc19hc19zZWNvbmRzWzBdICkgKyAyMCApLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCggcGFyc2VJbnQoIHRpbWVfZmllbGRzX29iai50aW1lc19hc19zZWNvbmRzWzFdICkgLSAyMCApXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdF1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdF1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgbWVyZ2VkX3NlY29uZHMgKTtcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdGlzX2NoZWNrX2luID0gKC0xICE9PSB0aW1lX2ZpZWxkc19vYmoubmFtZS5pbmRleE9mKCAnc3RhcnQnICkpO1xyXG5cdFx0XHRcdFx0XHRpc19pbnRlcnNlY3QgPSB3cGJjX2lzX2ludGVyc2VjdF9fb25lX3RpbWVfaW50ZXJ2YWwoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQoICggaXNfY2hlY2tfaW4gKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICA/IHBhcnNlSW50KCB0aW1lX2ZpZWxkc19vYmoudGltZXNfYXNfc2Vjb25kcyApICsgMjBcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgOiBwYXJzZUludCggdGltZV9maWVsZHNfb2JqLnRpbWVzX2FzX3NlY29uZHMgKSAtIDIwXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQsIG1lcmdlZF9zZWNvbmRzICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRpZiAoaXNfaW50ZXJzZWN0KXtcclxuXHRcdFx0XHRcdFx0aG93X21hbnlfcmVzb3VyY2VzX2ludGVyc2VjdGVkKys7XHRcdFx0Ly8gSW5jcmVhc2VcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRpZiAoIGNoaWxkX3Jlc291cmNlc19hcnIubGVuZ3RoID09IGhvd19tYW55X3Jlc291cmNlc19pbnRlcnNlY3RlZCApIHtcclxuXHRcdFx0XHRcdC8vIEFsbCByZXNvdXJjZXMgaW50ZXJzZWN0ZWQsICB0aGVuICBpdCdzIG1lYW5zIHRoYXQgdGhpcyB0aW1lLXNsb3Qgb3IgdGltZSBtdXN0ICBiZSAgRGlzYWJsZWQsIGFuZCB3ZSBjYW4gIGV4aXN0ICBmcm9tICAgc2VsZWN0ZWRfZGF0ZXNfYXJyIExPT1BcclxuXHJcblx0XHRcdFx0XHR0aW1lX2ZpZWxkc19vYmpfYXJyWyBmaWVsZF9rZXkgXS5kaXNhYmxlZCA9IDE7XHJcblx0XHRcdFx0XHRicmVhaztcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gZXhpc3QgIGZyb20gICBEYXRlcyBMT09QXHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8vIDUuIE5vdyB3ZSBjYW4gZGlzYWJsZSB0aW1lIHNsb3QgaW4gSFRNTCBieSAgdXNpbmcgICggZmllbGQuZGlzYWJsZWQgPT0gMSApIHByb3BlcnR5XHJcblx0XHR3cGJjX19odG1sX190aW1lX2ZpZWxkX29wdGlvbnNfX3NldF9kaXNhYmxlZCggdGltZV9maWVsZHNfb2JqX2FyciApO1xyXG5cclxuXHRcdGpRdWVyeSggXCIuYm9va2luZ19mb3JtX2RpdlwiICkudHJpZ2dlciggJ3dwYmNfaG9va190aW1lc2xvdHNfZGlzYWJsZWQnLCBbcmVzb3VyY2VfaWQsIHNlbGVjdGVkX2RhdGVzX2Fycl0gKTtcdFx0XHRcdFx0Ly8gVHJpZ2dlciBob29rIG9uIGRpc2FibGluZyB0aW1lc2xvdHMuXHRcdFVzYWdlOiBcdGpRdWVyeSggXCIuYm9va2luZ19mb3JtX2RpdlwiICkub24oICd3cGJjX2hvb2tfdGltZXNsb3RzX2Rpc2FibGVkJywgZnVuY3Rpb24gKCBldmVudCwgYmtfdHlwZSwgYWxsX2RhdGVzICl7IC4uLiB9ICk7XHRcdC8vIEZpeEluOiA4LjcuMTEuOS5cclxuXHR9XHJcblxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ2hlY2sgaWYgc3BlY2lmaWMgdGltZSgtc2xvdCkgYWxyZWFkeSAgaW4gdGhlIHBhc3QgZm9yIHNlbGVjdGVkIGRhdGVcclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ganNfY3VycmVudF90aW1lX3RvX2NoZWNrXHRcdC0gSlMgRGF0ZVxyXG5cdFx0ICogQHBhcmFtIHNxbF9kYXRlXHRcdFx0XHRcdFx0LSAnMjAyNS0wMS0yNidcclxuXHRcdCAqIEBwYXJhbSB0aW1lX2ZpZWxkc19vYmpcdFx0XHRcdC0gT2JqZWN0XHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19jaGVja19pc190aW1lX2luX3Bhc3QoIGpzX2N1cnJlbnRfdGltZV90b19jaGVjaywgc3FsX2RhdGUsIHRpbWVfZmllbGRzX29iaiApIHtcclxuXHJcblx0XHRcdC8vIEZpeEluOiAxMC45LjYuNFxyXG5cdFx0XHR2YXIgc3FsX2RhdGVfYXJyID0gc3FsX2RhdGUuc3BsaXQoICctJyApO1xyXG5cdFx0XHR2YXIgc3FsX2RhdGVfX21pZG5pZ2h0ID0gbmV3IERhdGUoIHBhcnNlSW50KCBzcWxfZGF0ZV9hcnJbMF0gKSwgKCBwYXJzZUludCggc3FsX2RhdGVfYXJyWzFdICkgLSAxICksIHBhcnNlSW50KCBzcWxfZGF0ZV9hcnJbMl0gKSwgMCwgMCwgMCApO1xyXG5cdFx0XHR2YXIgc3FsX2RhdGVfX21pZG5pZ2h0X21pbGlzZWNvbmRzID0gc3FsX2RhdGVfX21pZG5pZ2h0LmdldFRpbWUoKTtcclxuXHJcblx0XHRcdHZhciBpc19pbnRlcnNlY3QgPSBmYWxzZTtcclxuXHJcblx0XHRcdGlmICggdGltZV9maWVsZHNfb2JqLnRpbWVzX2FzX3NlY29uZHMubGVuZ3RoID4gMSApIHtcclxuXHJcblx0XHRcdFx0aWYgKCBqc19jdXJyZW50X3RpbWVfdG9fY2hlY2suZ2V0VGltZSgpID4gKHNxbF9kYXRlX19taWRuaWdodF9taWxpc2Vjb25kcyArIChwYXJzZUludCggdGltZV9maWVsZHNfb2JqLnRpbWVzX2FzX3NlY29uZHNbMF0gKSArIDIwKSAqIDEwMDApICkge1xyXG5cdFx0XHRcdFx0aXNfaW50ZXJzZWN0ID0gdHJ1ZTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0aWYgKCBqc19jdXJyZW50X3RpbWVfdG9fY2hlY2suZ2V0VGltZSgpID4gKHNxbF9kYXRlX19taWRuaWdodF9taWxpc2Vjb25kcyArIChwYXJzZUludCggdGltZV9maWVsZHNfb2JqLnRpbWVzX2FzX3NlY29uZHNbMV0gKSAtIDIwKSAqIDEwMDApICkge1xyXG5cdFx0XHRcdFx0aXNfaW50ZXJzZWN0ID0gdHJ1ZTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHZhciBpc19jaGVja19pbiA9ICgtMSAhPT0gdGltZV9maWVsZHNfb2JqLm5hbWUuaW5kZXhPZiggJ3N0YXJ0JyApKTtcclxuXHJcblx0XHRcdFx0dmFyIHRpbWVzX2FzX3NlY29uZHNfY2hlY2sgPSAoaXNfY2hlY2tfaW4pID8gcGFyc2VJbnQoIHRpbWVfZmllbGRzX29iai50aW1lc19hc19zZWNvbmRzICkgKyAyMCA6IHBhcnNlSW50KCB0aW1lX2ZpZWxkc19vYmoudGltZXNfYXNfc2Vjb25kcyApIC0gMjA7XHJcblxyXG5cdFx0XHRcdHRpbWVzX2FzX3NlY29uZHNfY2hlY2sgPSBzcWxfZGF0ZV9fbWlkbmlnaHRfbWlsaXNlY29uZHMgKyB0aW1lc19hc19zZWNvbmRzX2NoZWNrICogMTAwMDtcclxuXHJcblx0XHRcdFx0aWYgKCBqc19jdXJyZW50X3RpbWVfdG9fY2hlY2suZ2V0VGltZSgpID4gdGltZXNfYXNfc2Vjb25kc19jaGVjayApIHtcclxuXHRcdFx0XHRcdGlzX2ludGVyc2VjdCA9IHRydWU7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gaXNfaW50ZXJzZWN0O1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogSXMgbnVtYmVyIGluc2lkZSAvaW50ZXJzZWN0ICBvZiBhcnJheSBvZiBpbnRlcnZhbHMgP1xyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB0aW1lX0FcdFx0ICAgICBcdC0gMjU4MDBcclxuXHRcdCAqIEBwYXJhbSB0aW1lX2ludGVydmFsX0JcdFx0LSBbICBbIDI1MjExLCAyNzAwMiBdLCBbIDM2MDExLCA4NjQwMCBdICBdXHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19pc19pbnRlcnNlY3RfX29uZV90aW1lX2ludGVydmFsKCB0aW1lX0EsIHRpbWVfaW50ZXJ2YWxfQiApe1xyXG5cclxuXHRcdFx0Zm9yICggdmFyIGogPSAwOyBqIDwgdGltZV9pbnRlcnZhbF9CLmxlbmd0aDsgaisrICl7XHJcblxyXG5cdFx0XHRcdGlmICggKHBhcnNlSW50KCB0aW1lX0EgKSA+IHBhcnNlSW50KCB0aW1lX2ludGVydmFsX0JbIGogXVsgMCBdICkpICYmIChwYXJzZUludCggdGltZV9BICkgPCBwYXJzZUludCggdGltZV9pbnRlcnZhbF9CWyBqIF1bIDEgXSApKSApe1xyXG5cdFx0XHRcdFx0cmV0dXJuIHRydWVcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdC8vIGlmICggKCBwYXJzZUludCggdGltZV9BICkgPT0gcGFyc2VJbnQoIHRpbWVfaW50ZXJ2YWxfQlsgaiBdWyAwIF0gKSApIHx8ICggcGFyc2VJbnQoIHRpbWVfQSApID09IHBhcnNlSW50KCB0aW1lX2ludGVydmFsX0JbIGogXVsgMSBdICkgKSApIHtcclxuXHRcdFx0XHQvLyBcdFx0XHQvLyBUaW1lIEEganVzdCAgYXQgIHRoZSBib3JkZXIgb2YgaW50ZXJ2YWxcclxuXHRcdFx0XHQvLyB9XHJcblx0XHRcdH1cclxuXHJcblx0XHQgICAgcmV0dXJuIGZhbHNlO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogSXMgdGhlc2UgYXJyYXkgb2YgaW50ZXJ2YWxzIGludGVyc2VjdGVkID9cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gdGltZV9pbnRlcnZhbF9BXHRcdC0gWyBbIDIxNjAwLCAyMzQwMCBdIF1cclxuXHRcdCAqIEBwYXJhbSB0aW1lX2ludGVydmFsX0JcdFx0LSBbICBbIDI1MjExLCAyNzAwMiBdLCBbIDM2MDExLCA4NjQwMCBdICBdXHJcblx0XHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19pc19pbnRlcnNlY3RfX3JhbmdlX3RpbWVfaW50ZXJ2YWwoIHRpbWVfaW50ZXJ2YWxfQSwgdGltZV9pbnRlcnZhbF9CICl7XHJcblxyXG5cdFx0XHR2YXIgaXNfaW50ZXJzZWN0O1xyXG5cclxuXHRcdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgdGltZV9pbnRlcnZhbF9BLmxlbmd0aDsgaSsrICl7XHJcblxyXG5cdFx0XHRcdGZvciAoIHZhciBqID0gMDsgaiA8IHRpbWVfaW50ZXJ2YWxfQi5sZW5ndGg7IGorKyApe1xyXG5cclxuXHRcdFx0XHRcdGlzX2ludGVyc2VjdCA9IHdwYmNfaW50ZXJ2YWxzX19pc19pbnRlcnNlY3RlZCggdGltZV9pbnRlcnZhbF9BWyBpIF0sIHRpbWVfaW50ZXJ2YWxfQlsgaiBdICk7XHJcblxyXG5cdFx0XHRcdFx0aWYgKCBpc19pbnRlcnNlY3QgKXtcclxuXHRcdFx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBHZXQgYWxsIHRpbWUgZmllbGRzIGluIHRoZSBib29raW5nIGZvcm0gYXMgYXJyYXkgIG9mIG9iamVjdHNcclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHRcdCAqIEByZXR1cm5zIFtdXHJcblx0XHQgKlxyXG5cdFx0ICogXHRcdEV4YW1wbGU6XHJcblx0XHQgKiBcdFx0XHRcdFx0W1xyXG5cdFx0ICogXHRcdFx0XHRcdCBcdCAgIHtcclxuXHRcdCAqIFx0XHRcdFx0XHRcdFx0XHR2YWx1ZV9vcHRpb25fMjRoOiAgICcwNjowMCAtIDA2OjMwJ1xyXG5cdFx0ICogXHRcdFx0XHRcdFx0XHRcdHRpbWVzX2FzX3NlY29uZHM6ICAgWyAyMTYwMCwgMjM0MDAgXVxyXG5cdFx0ICogXHRcdFx0XHRcdCBcdCAgIFx0XHRqcXVlcnlfb3B0aW9uOiAgICAgIGpRdWVyeV9PYmplY3Qge31cclxuXHRcdCAqIFx0XHRcdFx0XHRcdFx0XHRuYW1lOiAgICAgICAgICAgICAgICdyYW5nZXRpbWUyW10nXHJcblx0XHQgKiBcdFx0XHRcdFx0ICAgICB9XHJcblx0XHQgKiBcdFx0XHRcdFx0ICAuLi5cclxuXHRcdCAqIFx0XHRcdFx0XHRcdCAgIHtcclxuXHRcdCAqIFx0XHRcdFx0XHRcdFx0XHR2YWx1ZV9vcHRpb25fMjRoOiAgICcwNjowMCdcclxuXHRcdCAqIFx0XHRcdFx0XHRcdFx0XHR0aW1lc19hc19zZWNvbmRzOiAgIFsgMjE2MDAgXVxyXG5cdFx0ICogXHRcdFx0XHRcdFx0ICAgXHRcdGpxdWVyeV9vcHRpb246ICAgICAgalF1ZXJ5X09iamVjdCB7fVxyXG5cdFx0ICogXHRcdFx0XHRcdFx0XHRcdG5hbWU6ICAgICAgICAgICAgICAgJ3N0YXJ0dGltZTJbXSdcclxuXHRcdCAqICBcdFx0XHRcdFx0ICAgIH1cclxuXHRcdCAqIFx0XHRcdFx0XHQgXVxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiB3cGJjX2dldF9fdGltZV9maWVsZHNfX2luX2Jvb2tpbmdfZm9ybV9fYXNfYXJyKCByZXNvdXJjZV9pZCApe1xyXG5cdFx0ICAgIC8qKlxyXG5cdFx0XHQgKiBGaWVsZHMgd2l0aCAgW10gIGxpa2UgdGhpcyAgIHNlbGVjdFtuYW1lPVwicmFuZ2V0aW1lMVtdXCJdXHJcblx0XHRcdCAqIGl0J3Mgd2hlbiB3ZSBoYXZlICdtdWx0aXBsZScgaW4gc2hvcnRjb2RlOiAgIFtzZWxlY3QqIHJhbmdldGltZSBtdWx0aXBsZSAgXCIwNjowMCAtIDA2OjMwXCIgLi4uIF1cclxuXHRcdFx0ICovXHJcblx0XHRcdHZhciB0aW1lX2ZpZWxkc19hcnI9W1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQnc2VsZWN0W25hbWU9XCJyYW5nZXRpbWUnICsgcmVzb3VyY2VfaWQgKyAnXCJdJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0J3NlbGVjdFtuYW1lPVwicmFuZ2V0aW1lJyArIHJlc291cmNlX2lkICsgJ1tdXCJdJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0J3NlbGVjdFtuYW1lPVwic3RhcnR0aW1lJyArIHJlc291cmNlX2lkICsgJ1wiXScsXHJcblx0XHRcdFx0XHRcdFx0XHRcdCdzZWxlY3RbbmFtZT1cInN0YXJ0dGltZScgKyByZXNvdXJjZV9pZCArICdbXVwiXScsXHJcblx0XHRcdFx0XHRcdFx0XHRcdCdzZWxlY3RbbmFtZT1cImVuZHRpbWUnICsgcmVzb3VyY2VfaWQgKyAnXCJdJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0J3NlbGVjdFtuYW1lPVwiZW5kdGltZScgKyByZXNvdXJjZV9pZCArICdbXVwiXSdcclxuXHRcdFx0XHRcdFx0XHRcdF07XHJcblxyXG5cdFx0XHR2YXIgdGltZV9maWVsZHNfb2JqX2FyciA9IFtdO1xyXG5cclxuXHRcdFx0Ly8gTG9vcCBhbGwgVGltZSBGaWVsZHNcclxuXHRcdFx0Zm9yICggdmFyIGN0Zj0gMDsgY3RmIDwgdGltZV9maWVsZHNfYXJyLmxlbmd0aDsgY3RmKysgKXtcclxuXHJcblx0XHRcdFx0dmFyIHRpbWVfZmllbGQgPSB0aW1lX2ZpZWxkc19hcnJbIGN0ZiBdO1xyXG5cdFx0XHRcdHZhciB0aW1lX29wdGlvbiA9IGpRdWVyeSggdGltZV9maWVsZCArICcgb3B0aW9uJyApO1xyXG5cclxuXHRcdFx0XHQvLyBMb29wIGFsbCBvcHRpb25zIGluIHRpbWUgZmllbGRcclxuXHRcdFx0XHRmb3IgKCB2YXIgaiA9IDA7IGogPCB0aW1lX29wdGlvbi5sZW5ndGg7IGorKyApe1xyXG5cclxuXHRcdFx0XHRcdHZhciBqcXVlcnlfb3B0aW9uID0galF1ZXJ5KCB0aW1lX2ZpZWxkICsgJyBvcHRpb246ZXEoJyArIGogKyAnKScgKTtcclxuXHRcdFx0XHRcdHZhciB2YWx1ZV9vcHRpb25fc2Vjb25kc19hcnIgPSBqcXVlcnlfb3B0aW9uLnZhbCgpLnNwbGl0KCAnLScgKTtcclxuXHRcdFx0XHRcdHZhciB0aW1lc19hc19zZWNvbmRzID0gW107XHJcblxyXG5cdFx0XHRcdFx0Ly8gR2V0IHRpbWUgYXMgc2Vjb25kc1xyXG5cdFx0XHRcdFx0aWYgKCB2YWx1ZV9vcHRpb25fc2Vjb25kc19hcnIubGVuZ3RoICl7XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDkuOC4xMC4xLlxyXG5cdFx0XHRcdFx0XHRmb3IgKCBsZXQgaSA9IDA7IGkgPCB2YWx1ZV9vcHRpb25fc2Vjb25kc19hcnIubGVuZ3RoOyBpKysgKXtcdFx0Ly8gRml4SW46IDEwLjAuMC41Ni5cclxuXHRcdFx0XHRcdFx0XHQvLyB2YWx1ZV9vcHRpb25fc2Vjb25kc19hcnJbaV0gPSAnMTQ6MDAgJyAgfCAnIDE2OjAwJyAgIChpZiBmcm9tICdyYW5nZXRpbWUnKSBhbmQgJzE2OjAwJyAgaWYgKHN0YXJ0L2VuZCB0aW1lKVxyXG5cclxuXHRcdFx0XHRcdFx0XHR2YXIgc3RhcnRfZW5kX3RpbWVzX2FyciA9IHZhbHVlX29wdGlvbl9zZWNvbmRzX2FyclsgaSBdLnRyaW0oKS5zcGxpdCggJzonICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdHZhciB0aW1lX2luX3NlY29uZHMgPSBwYXJzZUludCggc3RhcnRfZW5kX3RpbWVzX2FyclsgMCBdICkgKiA2MCAqIDYwICsgcGFyc2VJbnQoIHN0YXJ0X2VuZF90aW1lc19hcnJbIDEgXSApICogNjA7XHJcblxyXG5cdFx0XHRcdFx0XHRcdHRpbWVzX2FzX3NlY29uZHMucHVzaCggdGltZV9pbl9zZWNvbmRzICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHR0aW1lX2ZpZWxkc19vYmpfYXJyLnB1c2goIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J25hbWUnICAgICAgICAgICAgOiBqUXVlcnkoIHRpbWVfZmllbGQgKS5hdHRyKCAnbmFtZScgKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3ZhbHVlX29wdGlvbl8yNGgnOiBqcXVlcnlfb3B0aW9uLnZhbCgpLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnanF1ZXJ5X29wdGlvbicgICA6IGpxdWVyeV9vcHRpb24sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd0aW1lc19hc19zZWNvbmRzJzogdGltZXNfYXNfc2Vjb25kc1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0cmV0dXJuIHRpbWVfZmllbGRzX29ial9hcnI7XHJcblx0XHR9XHJcblxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogRGlzYWJsZSBIVE1MIG9wdGlvbnMgYW5kIGFkZCBib29rZWQgQ1NTIGNsYXNzXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSB0aW1lX2ZpZWxkc19vYmpfYXJyICAgICAgLSB0aGlzIHZhbHVlIGlzIGZyb20gIHRoZSBmdW5jOiAgXHR3cGJjX2dldF9fdGltZV9maWVsZHNfX2luX2Jvb2tpbmdfZm9ybV9fYXNfYXJyKCByZXNvdXJjZV9pZCApXHJcblx0XHRcdCAqIFx0XHRcdFx0XHRbXHJcblx0XHRcdCAqIFx0XHRcdFx0XHQgXHQgICB7XHRqcXVlcnlfb3B0aW9uOiAgICAgIGpRdWVyeV9PYmplY3Qge31cclxuXHRcdFx0ICogXHRcdFx0XHRcdFx0XHRcdG5hbWU6ICAgICAgICAgICAgICAgJ3JhbmdldGltZTJbXSdcclxuXHRcdFx0ICogXHRcdFx0XHRcdFx0XHRcdHRpbWVzX2FzX3NlY29uZHM6ICAgWyAyMTYwMCwgMjM0MDAgXVxyXG5cdFx0XHQgKiBcdFx0XHRcdFx0XHRcdFx0dmFsdWVfb3B0aW9uXzI0aDogICAnMDY6MDAgLSAwNjozMCdcclxuXHRcdFx0ICogXHQgIFx0XHRcdFx0XHRcdCAgICBkaXNhYmxlZCA9IDFcclxuXHRcdFx0ICogXHRcdFx0XHRcdCAgICAgfVxyXG5cdFx0XHQgKiBcdFx0XHRcdFx0ICAuLi5cclxuXHRcdFx0ICogXHRcdFx0XHRcdFx0ICAge1x0anF1ZXJ5X29wdGlvbjogICAgICBqUXVlcnlfT2JqZWN0IHt9XHJcblx0XHRcdCAqIFx0XHRcdFx0XHRcdFx0XHRuYW1lOiAgICAgICAgICAgICAgICdzdGFydHRpbWUyW10nXHJcblx0XHRcdCAqIFx0XHRcdFx0XHRcdFx0XHR0aW1lc19hc19zZWNvbmRzOiAgIFsgMjE2MDAgXVxyXG5cdFx0XHQgKiBcdFx0XHRcdFx0XHRcdFx0dmFsdWVfb3B0aW9uXzI0aDogICAnMDY6MDAnXHJcblx0XHRcdCAqICAgXHRcdFx0XHRcdFx0XHRkaXNhYmxlZCA9IDBcclxuXHRcdFx0ICogIFx0XHRcdFx0XHQgICAgfVxyXG5cdFx0XHQgKiBcdFx0XHRcdFx0IF1cclxuXHRcdFx0ICpcclxuXHRcdFx0ICovXHJcblx0XHRcdGZ1bmN0aW9uIHdwYmNfX2h0bWxfX3RpbWVfZmllbGRfb3B0aW9uc19fc2V0X2Rpc2FibGVkKCB0aW1lX2ZpZWxkc19vYmpfYXJyICl7XHJcblxyXG5cdFx0XHRcdHZhciBqcXVlcnlfb3B0aW9uO1xyXG5cclxuXHRcdFx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCB0aW1lX2ZpZWxkc19vYmpfYXJyLmxlbmd0aDsgaSsrICl7XHJcblxyXG5cdFx0XHRcdFx0dmFyIGpxdWVyeV9vcHRpb24gPSB0aW1lX2ZpZWxkc19vYmpfYXJyWyBpIF0uanF1ZXJ5X29wdGlvbjtcclxuXHJcblx0XHRcdFx0XHRpZiAoIDEgPT0gdGltZV9maWVsZHNfb2JqX2FyclsgaSBdLmRpc2FibGVkICl7XHJcblx0XHRcdFx0XHRcdGpxdWVyeV9vcHRpb24ucHJvcCggJ2Rpc2FibGVkJywgdHJ1ZSApOyBcdFx0Ly8gTWFrZSBkaXNhYmxlIHNvbWUgb3B0aW9uc1xyXG5cdFx0XHRcdFx0XHRqcXVlcnlfb3B0aW9uLmFkZENsYXNzKCAnYm9va2VkJyApOyAgICAgICAgICAgXHQvLyBBZGQgXCJib29rZWRcIiBDU1MgY2xhc3NcclxuXHJcblx0XHRcdFx0XHRcdC8vIGlmIHRoaXMgYm9va2VkIGVsZW1lbnQgc2VsZWN0ZWQgLS0+IHRoZW4gZGVzZWxlY3QgIGl0XHJcblx0XHRcdFx0XHRcdGlmICgganF1ZXJ5X29wdGlvbi5wcm9wKCAnc2VsZWN0ZWQnICkgKXtcclxuXHRcdFx0XHRcdFx0XHRqcXVlcnlfb3B0aW9uLnByb3AoICdzZWxlY3RlZCcsIGZhbHNlICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdGpxdWVyeV9vcHRpb24ucGFyZW50KCkuZmluZCggJ29wdGlvbjpub3QoW2Rpc2FibGVkXSk6Zmlyc3QnICkucHJvcCggJ3NlbGVjdGVkJywgdHJ1ZSApLnRyaWdnZXIoIFwiY2hhbmdlXCIgKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdGpxdWVyeV9vcHRpb24ucHJvcCggJ2Rpc2FibGVkJywgZmFsc2UgKTsgIFx0XHQvLyBNYWtlIGFjdGl2ZSBhbGwgdGltZXNcclxuXHRcdFx0XHRcdFx0anF1ZXJ5X29wdGlvbi5yZW1vdmVDbGFzcyggJ2Jvb2tlZCcgKTsgICBcdFx0Ly8gUmVtb3ZlIGNsYXNzIFwiYm9va2VkXCJcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIENoZWNrIGlmIHRoaXMgdGltZV9yYW5nZSB8IFRpbWVfU2xvdCBpcyBGdWxsIERheSAgYm9va2VkXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gdGltZXNsb3RfYXJyX2luX3NlY29uZHNcdFx0LSBbIDM2MDExLCA4NjQwMCBdXHJcblx0ICogQHJldHVybnMge2Jvb2xlYW59XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19pc190aGlzX3RpbWVzbG90X19mdWxsX2RheV9ib29rZWQoIHRpbWVzbG90X2Fycl9pbl9zZWNvbmRzICl7XHJcblxyXG5cdFx0aWYgKFxyXG5cdFx0XHRcdCggdGltZXNsb3RfYXJyX2luX3NlY29uZHMubGVuZ3RoID4gMSApXHJcblx0XHRcdCYmICggcGFyc2VJbnQoIHRpbWVzbG90X2Fycl9pbl9zZWNvbmRzWyAwIF0gKSA8IDMwIClcclxuXHRcdFx0JiYgKCBwYXJzZUludCggdGltZXNsb3RfYXJyX2luX3NlY29uZHNbIDEgXSApID4gICggKDI0ICogNjAgKiA2MCkgLSAzMCkgKVxyXG5cdFx0KXtcclxuXHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIGZhbHNlO1xyXG5cdH1cclxuXHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0LyogID09ICBTIGUgbCBlIGMgdCBlIGQgICAgRCBhIHQgZSBzICAvICBUIGkgbSBlIC0gRiBpIGUgbCBkIHMgID09XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcblx0LyoqXHJcblx0ICogIEdldCBhbGwgc2VsZWN0ZWQgZGF0ZXMgaW4gU1FMIGZvcm1hdCBsaWtlIHRoaXMgWyBcIjIwMjMtMDgtMjNcIiwgXCIyMDIzLTA4LTI0XCIgLCAuLi4gXVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0ICogQHJldHVybnMge1tdfVx0XHRcdFsgXCIyMDIzLTA4LTIzXCIsIFwiMjAyMy0wOC0yNFwiLCBcIjIwMjMtMDgtMjVcIiwgXCIyMDIzLTA4LTI2XCIsIFwiMjAyMy0wOC0yN1wiLCBcIjIwMjMtMDgtMjhcIiwgXCIyMDIzLTA4LTI5XCIgXVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfZ2V0X19zZWxlY3RlZF9kYXRlc19zcWxfX2FzX2FyciggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHR2YXIgc2VsZWN0ZWRfZGF0ZXNfYXJyID0gW107XHJcblx0XHRzZWxlY3RlZF9kYXRlc19hcnIgPSBqUXVlcnkoICcjZGF0ZV9ib29raW5nJyArIHJlc291cmNlX2lkICkudmFsKCkuc3BsaXQoJywnKTtcclxuXHJcblx0XHRpZiAoIHNlbGVjdGVkX2RhdGVzX2Fyci5sZW5ndGggKXtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS44LjEwLjEuXHJcblx0XHRcdGZvciAoIGxldCBpID0gMDsgaSA8IHNlbGVjdGVkX2RhdGVzX2Fyci5sZW5ndGg7IGkrKyApe1x0XHRcdFx0XHRcdC8vIEZpeEluOiAxMC4wLjAuNTYuXHJcblx0XHRcdFx0c2VsZWN0ZWRfZGF0ZXNfYXJyWyBpIF0gPSBzZWxlY3RlZF9kYXRlc19hcnJbIGkgXS50cmltKCk7XHJcblx0XHRcdFx0c2VsZWN0ZWRfZGF0ZXNfYXJyWyBpIF0gPSBzZWxlY3RlZF9kYXRlc19hcnJbIGkgXS5zcGxpdCggJy4nICk7XHJcblx0XHRcdFx0aWYgKCBzZWxlY3RlZF9kYXRlc19hcnJbIGkgXS5sZW5ndGggPiAxICl7XHJcblx0XHRcdFx0XHRzZWxlY3RlZF9kYXRlc19hcnJbIGkgXSA9IHNlbGVjdGVkX2RhdGVzX2FyclsgaSBdWyAyIF0gKyAnLScgKyBzZWxlY3RlZF9kYXRlc19hcnJbIGkgXVsgMSBdICsgJy0nICsgc2VsZWN0ZWRfZGF0ZXNfYXJyWyBpIF1bIDAgXTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvLyBSZW1vdmUgZW1wdHkgZWxlbWVudHMgZnJvbSBhbiBhcnJheVxyXG5cdFx0c2VsZWN0ZWRfZGF0ZXNfYXJyID0gc2VsZWN0ZWRfZGF0ZXNfYXJyLmZpbHRlciggZnVuY3Rpb24gKCBuICl7IHJldHVybiBwYXJzZUludChuKTsgfSApO1xyXG5cclxuXHRcdHNlbGVjdGVkX2RhdGVzX2Fyci5zb3J0KCk7XHJcblxyXG5cdFx0cmV0dXJuIHNlbGVjdGVkX2RhdGVzX2FycjtcclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgYWxsIHRpbWUgZmllbGRzIGluIHRoZSBib29raW5nIGZvcm0gYXMgYXJyYXkgIG9mIG9iamVjdHNcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdCAqIEBwYXJhbSBpc19vbmx5X3NlbGVjdGVkX3RpbWVcclxuXHQgKiBAcmV0dXJucyBbXVxyXG5cdCAqXHJcblx0ICogXHRcdEV4YW1wbGU6XHJcblx0ICogXHRcdFx0XHRcdFtcclxuXHQgKiBcdFx0XHRcdFx0IFx0ICAge1xyXG5cdCAqIFx0XHRcdFx0XHRcdFx0XHR2YWx1ZV9vcHRpb25fMjRoOiAgICcwNjowMCAtIDA2OjMwJ1xyXG5cdCAqIFx0XHRcdFx0XHRcdFx0XHR0aW1lc19hc19zZWNvbmRzOiAgIFsgMjE2MDAsIDIzNDAwIF1cclxuXHQgKiBcdFx0XHRcdFx0IFx0ICAgXHRcdGpxdWVyeV9vcHRpb246ICAgICAgalF1ZXJ5X09iamVjdCB7fVxyXG5cdCAqIFx0XHRcdFx0XHRcdFx0XHRuYW1lOiAgICAgICAgICAgICAgICdyYW5nZXRpbWUyW10nXHJcblx0ICogXHRcdFx0XHRcdCAgICAgfVxyXG5cdCAqIFx0XHRcdFx0XHQgIC4uLlxyXG5cdCAqIFx0XHRcdFx0XHRcdCAgIHtcclxuXHQgKiBcdFx0XHRcdFx0XHRcdFx0dmFsdWVfb3B0aW9uXzI0aDogICAnMDY6MDAnXHJcblx0ICogXHRcdFx0XHRcdFx0XHRcdHRpbWVzX2FzX3NlY29uZHM6ICAgWyAyMTYwMCBdXHJcblx0ICogXHRcdFx0XHRcdFx0ICAgXHRcdGpxdWVyeV9vcHRpb246ICAgICAgalF1ZXJ5X09iamVjdCB7fVxyXG5cdCAqIFx0XHRcdFx0XHRcdFx0XHRuYW1lOiAgICAgICAgICAgICAgICdzdGFydHRpbWUyW10nXHJcblx0ICogIFx0XHRcdFx0XHQgICAgfVxyXG5cdCAqIFx0XHRcdFx0XHQgXVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfZ2V0X19zZWxlY3RlZF90aW1lX2ZpZWxkc19faW5fYm9va2luZ19mb3JtX19hc19hcnIoIHJlc291cmNlX2lkLCBpc19vbmx5X3NlbGVjdGVkX3RpbWUgPSB0cnVlICl7XHJcblx0XHQvKipcclxuXHRcdCAqIEZpZWxkcyB3aXRoICBbXSAgbGlrZSB0aGlzICAgc2VsZWN0W25hbWU9XCJyYW5nZXRpbWUxW11cIl1cclxuXHRcdCAqIGl0J3Mgd2hlbiB3ZSBoYXZlICdtdWx0aXBsZScgaW4gc2hvcnRjb2RlOiAgIFtzZWxlY3QqIHJhbmdldGltZSBtdWx0aXBsZSAgXCIwNjowMCAtIDA2OjMwXCIgLi4uIF1cclxuXHRcdCAqL1xyXG5cdFx0dmFyIHRpbWVfZmllbGRzX2Fycj1bXHJcblx0XHRcdFx0XHRcdFx0XHQnc2VsZWN0W25hbWU9XCJyYW5nZXRpbWUnICsgcmVzb3VyY2VfaWQgKyAnXCJdJyxcclxuXHRcdFx0XHRcdFx0XHRcdCdzZWxlY3RbbmFtZT1cInJhbmdldGltZScgKyByZXNvdXJjZV9pZCArICdbXVwiXScsXHJcblx0XHRcdFx0XHRcdFx0XHQnc2VsZWN0W25hbWU9XCJzdGFydHRpbWUnICsgcmVzb3VyY2VfaWQgKyAnXCJdJyxcclxuXHRcdFx0XHRcdFx0XHRcdCdzZWxlY3RbbmFtZT1cInN0YXJ0dGltZScgKyByZXNvdXJjZV9pZCArICdbXVwiXScsXHJcblx0XHRcdFx0XHRcdFx0XHQnc2VsZWN0W25hbWU9XCJlbmR0aW1lJyArIHJlc291cmNlX2lkICsgJ1wiXScsXHJcblx0XHRcdFx0XHRcdFx0XHQnc2VsZWN0W25hbWU9XCJlbmR0aW1lJyArIHJlc291cmNlX2lkICsgJ1tdXCJdJyxcclxuXHRcdFx0XHRcdFx0XHRcdCdzZWxlY3RbbmFtZT1cImR1cmF0aW9udGltZScgKyByZXNvdXJjZV9pZCArICdcIl0nLFxyXG5cdFx0XHRcdFx0XHRcdFx0J3NlbGVjdFtuYW1lPVwiZHVyYXRpb250aW1lJyArIHJlc291cmNlX2lkICsgJ1tdXCJdJ1xyXG5cdFx0XHRcdFx0XHRcdF07XHJcblxyXG5cdFx0dmFyIHRpbWVfZmllbGRzX29ial9hcnIgPSBbXTtcclxuXHJcblx0XHQvLyBMb29wIGFsbCBUaW1lIEZpZWxkc1xyXG5cdFx0Zm9yICggdmFyIGN0Zj0gMDsgY3RmIDwgdGltZV9maWVsZHNfYXJyLmxlbmd0aDsgY3RmKysgKXtcclxuXHJcblx0XHRcdHZhciB0aW1lX2ZpZWxkID0gdGltZV9maWVsZHNfYXJyWyBjdGYgXTtcclxuXHJcblx0XHRcdHZhciB0aW1lX29wdGlvbjtcclxuXHRcdFx0aWYgKCBpc19vbmx5X3NlbGVjdGVkX3RpbWUgKXtcclxuXHRcdFx0XHR0aW1lX29wdGlvbiA9IGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKyAnICcgKyB0aW1lX2ZpZWxkICsgJyBvcHRpb246c2VsZWN0ZWQnICk7XHRcdFx0Ly8gRXhjbHVkZSBjb25kaXRpb25hbCAgZmllbGRzLCAgYmVjYXVzZSBvZiB1c2luZyAnI2Jvb2tpbmdfZm9ybTMgLi4uJ1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHRpbWVfb3B0aW9uID0galF1ZXJ5KCAnI2Jvb2tpbmdfZm9ybScgKyByZXNvdXJjZV9pZCArICcgJyArIHRpbWVfZmllbGQgKyAnIG9wdGlvbicgKTtcdFx0XHRcdC8vIEFsbCAgdGltZSBmaWVsZHNcclxuXHRcdFx0fVxyXG5cclxuXHJcblx0XHRcdC8vIExvb3AgYWxsIG9wdGlvbnMgaW4gdGltZSBmaWVsZFxyXG5cdFx0XHRmb3IgKCB2YXIgaiA9IDA7IGogPCB0aW1lX29wdGlvbi5sZW5ndGg7IGorKyApe1xyXG5cclxuXHRcdFx0XHR2YXIganF1ZXJ5X29wdGlvbiA9IGpRdWVyeSggdGltZV9vcHRpb25bIGogXSApO1x0XHQvLyBHZXQgb25seSAgc2VsZWN0ZWQgb3B0aW9ucyBcdC8valF1ZXJ5KCB0aW1lX2ZpZWxkICsgJyBvcHRpb246ZXEoJyArIGogKyAnKScgKTtcclxuXHRcdFx0XHR2YXIgdmFsdWVfb3B0aW9uX3NlY29uZHNfYXJyID0ganF1ZXJ5X29wdGlvbi52YWwoKS5zcGxpdCggJy0nICk7XHJcblx0XHRcdFx0dmFyIHRpbWVzX2FzX3NlY29uZHMgPSBbXTtcclxuXHJcblx0XHRcdFx0Ly8gR2V0IHRpbWUgYXMgc2Vjb25kc1xyXG5cdFx0XHRcdGlmICggdmFsdWVfb3B0aW9uX3NlY29uZHNfYXJyLmxlbmd0aCApe1x0XHRcdFx0IFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS44LjEwLjEuXHJcblx0XHRcdFx0XHRmb3IgKCBsZXQgaSA9IDA7IGkgPCB2YWx1ZV9vcHRpb25fc2Vjb25kc19hcnIubGVuZ3RoOyBpKysgKXtcdFx0XHRcdFx0Ly8gRml4SW46IDEwLjAuMC41Ni5cclxuXHRcdFx0XHRcdFx0Ly8gdmFsdWVfb3B0aW9uX3NlY29uZHNfYXJyW2ldID0gJzE0OjAwICcgIHwgJyAxNjowMCcgICAoaWYgZnJvbSAncmFuZ2V0aW1lJykgYW5kICcxNjowMCcgIGlmIChzdGFydC9lbmQgdGltZSlcclxuXHJcblx0XHRcdFx0XHRcdHZhciBzdGFydF9lbmRfdGltZXNfYXJyID0gdmFsdWVfb3B0aW9uX3NlY29uZHNfYXJyWyBpIF0udHJpbSgpLnNwbGl0KCAnOicgKTtcclxuXHJcblx0XHRcdFx0XHRcdHZhciB0aW1lX2luX3NlY29uZHMgPSBwYXJzZUludCggc3RhcnRfZW5kX3RpbWVzX2FyclsgMCBdICkgKiA2MCAqIDYwICsgcGFyc2VJbnQoIHN0YXJ0X2VuZF90aW1lc19hcnJbIDEgXSApICogNjA7XHJcblxyXG5cdFx0XHRcdFx0XHR0aW1lc19hc19zZWNvbmRzLnB1c2goIHRpbWVfaW5fc2Vjb25kcyApO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0dGltZV9maWVsZHNfb2JqX2Fyci5wdXNoKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnbmFtZScgICAgICAgICAgICA6IGpRdWVyeSggJyNib29raW5nX2Zvcm0nICsgcmVzb3VyY2VfaWQgKyAnICcgKyB0aW1lX2ZpZWxkICkuYXR0ciggJ25hbWUnICksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndmFsdWVfb3B0aW9uXzI0aCc6IGpxdWVyeV9vcHRpb24udmFsKCksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnanF1ZXJ5X29wdGlvbicgICA6IGpxdWVyeV9vcHRpb24sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndGltZXNfYXNfc2Vjb25kcyc6IHRpbWVzX2FzX3NlY29uZHNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHQvLyBUZXh0OiAgIFtzdGFydHRpbWVdIC0gW2VuZHRpbWVdIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdFx0dmFyIHRleHRfdGltZV9maWVsZHNfYXJyPVtcclxuXHRcdFx0XHRcdFx0XHRcdFx0J2lucHV0W25hbWU9XCJzdGFydHRpbWUnICsgcmVzb3VyY2VfaWQgKyAnXCJdJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0J2lucHV0W25hbWU9XCJlbmR0aW1lJyArIHJlc291cmNlX2lkICsgJ1wiXScsXHJcblx0XHRcdFx0XHRcdFx0XHRdO1xyXG5cdFx0Zm9yICggdmFyIHRmPSAwOyB0ZiA8IHRleHRfdGltZV9maWVsZHNfYXJyLmxlbmd0aDsgdGYrKyApe1xyXG5cclxuXHRcdFx0dmFyIHRleHRfanF1ZXJ5ID0galF1ZXJ5KCAnI2Jvb2tpbmdfZm9ybScgKyByZXNvdXJjZV9pZCArICcgJyArIHRleHRfdGltZV9maWVsZHNfYXJyWyB0ZiBdICk7XHRcdFx0XHRcdFx0XHRcdC8vIEV4Y2x1ZGUgY29uZGl0aW9uYWwgIGZpZWxkcywgIGJlY2F1c2Ugb2YgdXNpbmcgJyNib29raW5nX2Zvcm0zIC4uLidcclxuXHRcdFx0aWYgKCB0ZXh0X2pxdWVyeS5sZW5ndGggPiAwICl7XHJcblxyXG5cdFx0XHRcdHZhciB0aW1lX19oX21fX2FyciA9IHRleHRfanF1ZXJ5LnZhbCgpLnRyaW0oKS5zcGxpdCggJzonICk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vICcxNDowMCdcclxuXHRcdFx0XHRpZiAoIDAgPT0gdGltZV9faF9tX19hcnIubGVuZ3RoICl7XHJcblx0XHRcdFx0XHRjb250aW51ZTtcdFx0XHRcdFx0XHRcdFx0XHQvLyBOb3QgZW50ZXJlZCB0aW1lIHZhbHVlIGluIGEgZmllbGRcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0aWYgKCAxID09IHRpbWVfX2hfbV9fYXJyLmxlbmd0aCApe1xyXG5cdFx0XHRcdFx0aWYgKCAnJyA9PT0gdGltZV9faF9tX19hcnJbIDAgXSApe1xyXG5cdFx0XHRcdFx0XHRjb250aW51ZTtcdFx0XHRcdFx0XHRcdFx0Ly8gTm90IGVudGVyZWQgdGltZSB2YWx1ZSBpbiBhIGZpZWxkXHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR0aW1lX19oX21fX2FyclsgMSBdID0gMDtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0dmFyIHRleHRfdGltZV9pbl9zZWNvbmRzID0gcGFyc2VJbnQoIHRpbWVfX2hfbV9fYXJyWyAwIF0gKSAqIDYwICogNjAgKyBwYXJzZUludCggdGltZV9faF9tX19hcnJbIDEgXSApICogNjA7XHJcblxyXG5cdFx0XHRcdHZhciB0ZXh0X3RpbWVzX2FzX3NlY29uZHMgPSBbXTtcclxuXHRcdFx0XHR0ZXh0X3RpbWVzX2FzX3NlY29uZHMucHVzaCggdGV4dF90aW1lX2luX3NlY29uZHMgKTtcclxuXHJcblx0XHRcdFx0dGltZV9maWVsZHNfb2JqX2Fyci5wdXNoKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnbmFtZScgICAgICAgICAgICA6IHRleHRfanF1ZXJ5LmF0dHIoICduYW1lJyApLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3ZhbHVlX29wdGlvbl8yNGgnOiB0ZXh0X2pxdWVyeS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdqcXVlcnlfb3B0aW9uJyAgIDogdGV4dF9qcXVlcnksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndGltZXNfYXNfc2Vjb25kcyc6IHRleHRfdGltZXNfYXNfc2Vjb25kc1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiB0aW1lX2ZpZWxkc19vYmpfYXJyO1xyXG5cdH1cclxuXHJcblxyXG5cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbi8qICA9PSAgUyBVIFAgUCBPIFIgVCAgICBmb3IgICAgQyBBIEwgRSBOIEQgQSBSICA9PVxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcblx0LyoqXHJcblx0ICogR2V0IENhbGVuZGFyIGRhdGVwaWNrIEluc3RhbmNlLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtpbnR8c3RyaW5nfSByZXNvdXJjZV9pZFxyXG5cdCAqIEByZXR1cm5zIHsqfG51bGx9XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19jYWxlbmRhcl9fZ2V0X2luc3QocmVzb3VyY2VfaWQpIHtcclxuXHJcblx0XHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHJlc291cmNlX2lkKSApIHtcclxuXHRcdFx0cmVzb3VyY2VfaWQgPSAnMSc7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCBqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCApLmxlbmd0aCA+IDAgKSB7XHJcblxyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdHZhciBpbnN0ID0galF1ZXJ5LmRhdGVwaWNrLl9nZXRJbnN0KCBqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCApLmdldCggMCApICk7XHJcblx0XHRcdFx0cmV0dXJuIGluc3QgPyBpbnN0IDogbnVsbDtcclxuXHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0cmV0dXJuIG51bGw7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gbnVsbDtcclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBVbnNlbGVjdCAgYWxsIGRhdGVzIGluIGNhbGVuZGFyIGFuZCB2aXN1YWxseSB1cGRhdGUgdGhpcyBjYWxlbmRhclxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHJlc291cmNlX2lkXHRcdElEIG9mIGJvb2tpbmcgcmVzb3VyY2VcclxuXHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cdFx0dHJ1ZSBvbiBzdWNjZXNzIHwgZmFsc2UsICBpZiBubyBzdWNoICBjYWxlbmRhclxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfY2FsZW5kYXJfX3Vuc2VsZWN0X2FsbF9kYXRlcyggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0XHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHJlc291cmNlX2lkKSApe1xyXG5cdFx0XHRyZXNvdXJjZV9pZCA9ICcxJztcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgaW5zdCA9IHdwYmNfY2FsZW5kYXJfX2dldF9pbnN0KCByZXNvdXJjZV9pZCApXHJcblxyXG5cdFx0aWYgKCBudWxsICE9PSBpbnN0ICl7XHJcblxyXG5cdFx0XHQvLyBVbnNlbGVjdCBhbGwgZGF0ZXMgYW5kIHNldCAgcHJvcGVydGllcyBvZiBEYXRlcGlja1xyXG5cdFx0XHRqUXVlcnkoICcjZGF0ZV9ib29raW5nJyArIHJlc291cmNlX2lkICkudmFsKCAnJyApOyAgICAgIC8vRml4SW46IDUuNC4zXHJcblx0XHRcdGluc3Quc3RheU9wZW4gPSBmYWxzZTtcclxuXHRcdFx0aW5zdC5kYXRlcyA9IFtdO1xyXG5cdFx0XHRqUXVlcnkuZGF0ZXBpY2suX3VwZGF0ZURhdGVwaWNrKCBpbnN0ICk7XHJcblxyXG5cdFx0XHRyZXR1cm4gdHJ1ZVxyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBDbGVhciBkYXlzIGhpZ2hsaWdodGluZyBpbiBBbGwgb3Igc3BlY2lmaWMgQ2FsZW5kYXJzXHJcblx0ICpcclxuICAgICAqIEBwYXJhbSByZXNvdXJjZV9pZCAgLSBjYW4gYmUgc2tpcGVkIHRvICBjbGVhciBoaWdobGlnaHRpbmcgaW4gYWxsIGNhbGVuZGFyc1xyXG4gICAgICovXHJcblx0ZnVuY3Rpb24gd3BiY19jYWxlbmRhcnNfX2NsZWFyX2RheXNfaGlnaGxpZ2h0aW5nKCByZXNvdXJjZV9pZCApe1xyXG5cclxuXHRcdGlmICggJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiAoIHJlc291cmNlX2lkICkgKXtcclxuXHJcblx0XHRcdGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIHJlc291cmNlX2lkICsgJyAuZGF0ZXBpY2stZGF5cy1jZWxsLW92ZXInICkucmVtb3ZlQ2xhc3MoICdkYXRlcGljay1kYXlzLWNlbGwtb3ZlcicgKTtcdFx0Ly8gQ2xlYXIgaW4gc3BlY2lmaWMgY2FsZW5kYXJcclxuXHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHRqUXVlcnkoICcuZGF0ZXBpY2stZGF5cy1jZWxsLW92ZXInICkucmVtb3ZlQ2xhc3MoICdkYXRlcGljay1kYXlzLWNlbGwtb3ZlcicgKTtcdFx0XHRcdFx0XHRcdFx0Ly8gQ2xlYXIgaW4gYWxsIGNhbGVuZGFyc1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogU2Nyb2xsIHRvIHNwZWNpZmljIG1vbnRoIGluIGNhbGVuZGFyXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcdFx0SUQgb2YgcmVzb3VyY2VcclxuXHQgKiBAcGFyYW0geWVhclx0XHRcdFx0LSByZWFsIHllYXIgIC0gMjAyM1xyXG5cdCAqIEBwYXJhbSBtb250aFx0XHRcdFx0LSByZWFsIG1vbnRoIC0gMTJcclxuXHQgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2NhbGVuZGFyX19zY3JvbGxfdG8oIHJlc291cmNlX2lkLCB5ZWFyLCBtb250aCApe1xyXG5cclxuXHRcdGlmICggJ3VuZGVmaW5lZCcgPT09IHR5cGVvZiAocmVzb3VyY2VfaWQpICl7IHJlc291cmNlX2lkID0gJzEnOyB9XHJcblx0XHR2YXIgaW5zdCA9IHdwYmNfY2FsZW5kYXJfX2dldF9pbnN0KCByZXNvdXJjZV9pZCApXHJcblx0XHRpZiAoIG51bGwgIT09IGluc3QgKXtcclxuXHJcblx0XHRcdHllYXIgID0gcGFyc2VJbnQoIHllYXIgKTtcclxuXHRcdFx0bW9udGggPSBwYXJzZUludCggbW9udGggKSAtIDE7XHRcdC8vIEluIEpTIGRhdGUsICBtb250aCAtMVxyXG5cclxuXHRcdFx0aW5zdC5jdXJzb3JEYXRlID0gbmV3IERhdGUoKTtcclxuXHRcdFx0Ly8gSW4gc29tZSBjYXNlcywgIHRoZSBzZXRGdWxsWWVhciBjYW4gIHNldCAgb25seSBZZWFyLCAgYW5kIG5vdCB0aGUgTW9udGggYW5kIGRheSAgICAgIC8vIEZpeEluOiA2LjIuMy41LlxyXG5cdFx0XHRpbnN0LmN1cnNvckRhdGUuc2V0RnVsbFllYXIoIHllYXIsIG1vbnRoLCAxICk7XHJcblx0XHRcdGluc3QuY3Vyc29yRGF0ZS5zZXRNb250aCggbW9udGggKTtcclxuXHRcdFx0aW5zdC5jdXJzb3JEYXRlLnNldERhdGUoIDEgKTtcclxuXHJcblx0XHRcdGluc3QuZHJhd01vbnRoID0gaW5zdC5jdXJzb3JEYXRlLmdldE1vbnRoKCk7XHJcblx0XHRcdGluc3QuZHJhd1llYXIgPSBpbnN0LmN1cnNvckRhdGUuZ2V0RnVsbFllYXIoKTtcclxuXHJcblx0XHRcdGpRdWVyeS5kYXRlcGljay5fbm90aWZ5Q2hhbmdlKCBpbnN0ICk7XHJcblx0XHRcdGpRdWVyeS5kYXRlcGljay5fYWRqdXN0SW5zdERhdGUoIGluc3QgKTtcclxuXHRcdFx0alF1ZXJ5LmRhdGVwaWNrLl9zaG93RGF0ZSggaW5zdCApO1xyXG5cdFx0XHRqUXVlcnkuZGF0ZXBpY2suX3VwZGF0ZURhdGVwaWNrKCBpbnN0ICk7XHJcblxyXG5cdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdH1cclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIElzIHRoaXMgZGF0ZSBzZWxlY3RhYmxlIGluIGNhbGVuZGFyIChtYWlubHkgaXQncyBtZWFucyBBVkFJTEFCTEUgZGF0ZSlcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7aW50fHN0cmluZ30gcmVzb3VyY2VfaWRcdFx0MVxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBzcWxfY2xhc3NfZGF5XHRcdCcyMDIzLTA4LTExJ1xyXG5cdCAqIEByZXR1cm5zIHtib29sZWFufVx0XHRcdFx0XHR0cnVlIHwgZmFsc2VcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2lzX3RoaXNfZGF5X3NlbGVjdGFibGUoIHJlc291cmNlX2lkLCBzcWxfY2xhc3NfZGF5ICl7XHJcblxyXG5cdFx0Ly8gR2V0IERhdGEgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdHZhciBkYXRlX2Jvb2tpbmdzX29iaiA9IF93cGJjLmJvb2tpbmdzX2luX2NhbGVuZGFyX19nZXRfZm9yX2RhdGUoIHJlc291cmNlX2lkLCBzcWxfY2xhc3NfZGF5ICk7XHJcblxyXG5cdFx0dmFyIGlzX2RheV9zZWxlY3RhYmxlID0gKCBwYXJzZUludCggZGF0ZV9ib29raW5nc19vYmpbICdkYXlfYXZhaWxhYmlsaXR5JyBdICkgPiAwICk7XHJcblxyXG5cdFx0aWYgKCB0eXBlb2YgKGRhdGVfYm9va2luZ3Nfb2JqWyAnc3VtbWFyeScgXSkgPT09ICd1bmRlZmluZWQnICl7XHJcblx0XHRcdHJldHVybiBpc19kYXlfc2VsZWN0YWJsZTtcclxuXHRcdH1cclxuXHJcblx0XHRpZiAoICdhdmFpbGFibGUnICE9IGRhdGVfYm9va2luZ3Nfb2JqWyAnc3VtbWFyeSddWydzdGF0dXNfZm9yX2RheScgXSApe1xyXG5cclxuXHRcdFx0dmFyIGlzX3NldF9wZW5kaW5nX2RheXNfc2VsZWN0YWJsZSA9IF93cGJjLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAncGVuZGluZ19kYXlzX3NlbGVjdGFibGUnICk7XHRcdC8vIHNldCBwZW5kaW5nIGRheXMgc2VsZWN0YWJsZSAgICAgICAgICAvLyBGaXhJbjogOC42LjEuMTguXHJcblxyXG5cdFx0XHRzd2l0Y2ggKCBkYXRlX2Jvb2tpbmdzX29ialsgJ3N1bW1hcnknXVsnc3RhdHVzX2Zvcl9ib29raW5ncycgXSApe1xyXG5cdFx0XHRcdGNhc2UgJ3BlbmRpbmcnOlxyXG5cdFx0XHRcdC8vIFNpdHVhdGlvbnMgZm9yIFwiY2hhbmdlLW92ZXJcIiBkYXlzOlxyXG5cdFx0XHRcdGNhc2UgJ3BlbmRpbmdfcGVuZGluZyc6XHJcblx0XHRcdFx0Y2FzZSAncGVuZGluZ19hcHByb3ZlZCc6XHJcblx0XHRcdFx0Y2FzZSAnYXBwcm92ZWRfcGVuZGluZyc6XHJcblx0XHRcdFx0XHRpc19kYXlfc2VsZWN0YWJsZSA9IChpc19kYXlfc2VsZWN0YWJsZSkgPyB0cnVlIDogaXNfc2V0X3BlbmRpbmdfZGF5c19zZWxlY3RhYmxlO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0ZGVmYXVsdDpcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBpc19kYXlfc2VsZWN0YWJsZTtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIElzIGRhdGUgdG8gY2hlY2sgSU4gYXJyYXkgb2Ygc2VsZWN0ZWQgZGF0ZXNcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7ZGF0ZX1qc19kYXRlX3RvX2NoZWNrXHRcdC0gSlMgRGF0ZVx0XHRcdC0gc2ltcGxlICBKYXZhU2NyaXB0IERhdGUgb2JqZWN0XHJcblx0ICogQHBhcmFtIHtbXX0ganNfZGF0ZXNfYXJyXHRcdFx0LSBbIEpTRGF0ZSwgLi4uIF0gICAtIGFycmF5ICBvZiBKUyBkYXRlc1xyXG5cdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfaXNfdGhpc19kYXlfYW1vbmdfc2VsZWN0ZWRfZGF5cygganNfZGF0ZV90b19jaGVjaywganNfZGF0ZXNfYXJyICl7XHJcblxyXG5cdFx0Zm9yICggdmFyIGRhdGVfaW5kZXggPSAwOyBkYXRlX2luZGV4IDwganNfZGF0ZXNfYXJyLmxlbmd0aCA7IGRhdGVfaW5kZXgrKyApeyAgICAgXHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDguNC41LjE2LlxyXG5cdFx0XHRpZiAoICgganNfZGF0ZXNfYXJyWyBkYXRlX2luZGV4IF0uZ2V0RnVsbFllYXIoKSA9PT0ganNfZGF0ZV90b19jaGVjay5nZXRGdWxsWWVhcigpICkgJiZcclxuXHRcdFx0XHQgKCBqc19kYXRlc19hcnJbIGRhdGVfaW5kZXggXS5nZXRNb250aCgpID09PSBqc19kYXRlX3RvX2NoZWNrLmdldE1vbnRoKCkgKSAmJlxyXG5cdFx0XHRcdCAoIGpzX2RhdGVzX2FyclsgZGF0ZV9pbmRleCBdLmdldERhdGUoKSA9PT0ganNfZGF0ZV90b19jaGVjay5nZXREYXRlKCkgKSApIHtcclxuXHRcdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuICBmYWxzZTtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIEdldCBTUUwgQ2xhc3MgRGF0ZSAnMjAyMy0wOC0wMScgZnJvbSAgSlMgRGF0ZVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGRhdGVcdFx0XHRcdEpTIERhdGVcclxuXHQgKiBAcmV0dXJucyB7c3RyaW5nfVx0XHQnMjAyMy0wOC0xMidcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX19nZXRfX3NxbF9jbGFzc19kYXRlKCBkYXRlICl7XHJcblxyXG5cdFx0dmFyIHNxbF9jbGFzc19kYXkgPSBkYXRlLmdldEZ1bGxZZWFyKCkgKyAnLSc7XHJcblx0XHRcdHNxbF9jbGFzc19kYXkgKz0gKCAoIGRhdGUuZ2V0TW9udGgoKSArIDEgKSA8IDEwICkgPyAnMCcgOiAnJztcclxuXHRcdFx0c3FsX2NsYXNzX2RheSArPSAoIGRhdGUuZ2V0TW9udGgoKSArIDEgKSArICctJ1xyXG5cdFx0XHRzcWxfY2xhc3NfZGF5ICs9ICggZGF0ZS5nZXREYXRlKCkgPCAxMCApID8gJzAnIDogJyc7XHJcblx0XHRcdHNxbF9jbGFzc19kYXkgKz0gZGF0ZS5nZXREYXRlKCk7XHJcblxyXG5cdFx0XHRyZXR1cm4gc3FsX2NsYXNzX2RheTtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIEdldCBKUyBEYXRlIGZyb20gIHRoZSBTUUwgZGF0ZSBmb3JtYXQgJzIwMjQtMDUtMTQnXHJcblx0ICogQHBhcmFtIHNxbF9jbGFzc19kYXRlXHJcblx0ICogQHJldHVybnMge0RhdGV9XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19fZ2V0X19qc19kYXRlKCBzcWxfY2xhc3NfZGF0ZSApe1xyXG5cclxuXHRcdHZhciBzcWxfY2xhc3NfZGF0ZV9hcnIgPSBzcWxfY2xhc3NfZGF0ZS5zcGxpdCggJy0nICk7XHJcblxyXG5cdFx0dmFyIGRhdGVfanMgPSBuZXcgRGF0ZSgpO1xyXG5cclxuXHRcdGRhdGVfanMuc2V0RnVsbFllYXIoIHBhcnNlSW50KCBzcWxfY2xhc3NfZGF0ZV9hcnJbIDAgXSApLCAocGFyc2VJbnQoIHNxbF9jbGFzc19kYXRlX2FyclsgMSBdICkgLSAxKSwgcGFyc2VJbnQoIHNxbF9jbGFzc19kYXRlX2FyclsgMiBdICkgKTsgIC8vIHllYXIsIG1vbnRoLCBkYXRlXHJcblxyXG5cdFx0Ly8gV2l0aG91dCB0aGlzIHRpbWUgYWRqdXN0IERhdGVzIHNlbGVjdGlvbiAgaW4gRGF0ZXBpY2tlciBjYW4gbm90IHdvcmshISFcclxuXHRcdGRhdGVfanMuc2V0SG91cnMoMCk7XHJcblx0XHRkYXRlX2pzLnNldE1pbnV0ZXMoMCk7XHJcblx0XHRkYXRlX2pzLnNldFNlY29uZHMoMCk7XHJcblx0XHRkYXRlX2pzLnNldE1pbGxpc2Vjb25kcygwKTtcclxuXHJcblx0XHRyZXR1cm4gZGF0ZV9qcztcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIEdldCBURCBDbGFzcyBEYXRlICcxLTMxLTIwMjMnIGZyb20gIEpTIERhdGVcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBkYXRlXHRcdFx0XHRKUyBEYXRlXHJcblx0ICogQHJldHVybnMge3N0cmluZ31cdFx0JzEtMzEtMjAyMydcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX19nZXRfX3RkX2NsYXNzX2RhdGUoIGRhdGUgKXtcclxuXHJcblx0XHR2YXIgdGRfY2xhc3NfZGF5ID0gKGRhdGUuZ2V0TW9udGgoKSArIDEpICsgJy0nICsgZGF0ZS5nZXREYXRlKCkgKyAnLScgKyBkYXRlLmdldEZ1bGxZZWFyKCk7XHRcdFx0XHRcdFx0XHRcdC8vICcxLTktMjAyMydcclxuXHJcblx0XHRyZXR1cm4gdGRfY2xhc3NfZGF5O1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogR2V0IGRhdGUgcGFyYW1zIGZyb20gIHN0cmluZyBkYXRlXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gZGF0ZVx0XHRcdHN0cmluZyBkYXRlIGxpa2UgJzMxLjUuMjAyMydcclxuXHQgKiBAcGFyYW0gc2VwYXJhdG9yXHRcdGRlZmF1bHQgJy4nICBjYW4gYmUgc2tpcHBlZC5cclxuXHQgKiBAcmV0dXJucyB7ICB7ZGF0ZTogbnVtYmVyLCBtb250aDogbnVtYmVyLCB5ZWFyOiBudW1iZXJ9ICB9XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19fZ2V0X19kYXRlX3BhcmFtc19fZnJvbV9zdHJpbmdfZGF0ZSggZGF0ZSAsIHNlcGFyYXRvcil7XHJcblxyXG5cdFx0c2VwYXJhdG9yID0gKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChzZXBhcmF0b3IpICkgPyBzZXBhcmF0b3IgOiAnLic7XHJcblxyXG5cdFx0dmFyIGRhdGVfYXJyID0gZGF0ZS5zcGxpdCggc2VwYXJhdG9yICk7XHJcblx0XHR2YXIgZGF0ZV9vYmogPSB7XHJcblx0XHRcdCd5ZWFyJyA6ICBwYXJzZUludCggZGF0ZV9hcnJbIDIgXSApLFxyXG5cdFx0XHQnbW9udGgnOiAocGFyc2VJbnQoIGRhdGVfYXJyWyAxIF0gKSAtIDEpLFxyXG5cdFx0XHQnZGF0ZScgOiAgcGFyc2VJbnQoIGRhdGVfYXJyWyAwIF0gKVxyXG5cdFx0fTtcclxuXHRcdHJldHVybiBkYXRlX29iajtcdFx0Ly8gZm9yIFx0XHQgPSBuZXcgRGF0ZSggZGF0ZV9vYmoueWVhciAsIGRhdGVfb2JqLm1vbnRoICwgZGF0ZV9vYmouZGF0ZSApO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogQWRkIFNwaW4gTG9hZGVyIHRvICBjYWxlbmRhclxyXG5cdCAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfY2FsZW5kYXJfX2xvYWRpbmdfX3N0YXJ0KCByZXNvdXJjZV9pZCApe1xyXG5cdFx0aWYgKCAhIGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIHJlc291cmNlX2lkICkubmV4dCgpLmhhc0NsYXNzKCAnd3BiY19zcGluc19sb2FkZXJfd3JhcHBlcicgKSApe1xyXG5cdFx0XHRqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCApLmFmdGVyKCAnPGRpdiBjbGFzcz1cIndwYmNfc3BpbnNfbG9hZGVyX3dyYXBwZXJcIj48ZGl2IGNsYXNzPVwid3BiY19zcGluc19sb2FkZXJcIj48L2Rpdj48L2Rpdj4nICk7XHJcblx0XHR9XHJcblx0XHRpZiAoICEgalF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS5oYXNDbGFzcyggJ3dwYmNfY2FsZW5kYXJfYmx1cl9zbWFsbCcgKSApe1xyXG5cdFx0XHRqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCApLmFkZENsYXNzKCAnd3BiY19jYWxlbmRhcl9ibHVyX3NtYWxsJyApO1xyXG5cdFx0fVxyXG5cdFx0d3BiY19jYWxlbmRhcl9fYmx1cl9fc3RhcnQoIHJlc291cmNlX2lkICk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBSZW1vdmUgU3BpbiBMb2FkZXIgdG8gIGNhbGVuZGFyXHJcblx0ICogQHBhcmFtIHJlc291cmNlX2lkXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19jYWxlbmRhcl9fbG9hZGluZ19fc3RvcCggcmVzb3VyY2VfaWQgKXtcclxuXHRcdGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIHJlc291cmNlX2lkICsgJyArIC53cGJjX3NwaW5zX2xvYWRlcl93cmFwcGVyJyApLnJlbW92ZSgpO1xyXG5cdFx0alF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS5yZW1vdmVDbGFzcyggJ3dwYmNfY2FsZW5kYXJfYmx1cl9zbWFsbCcgKTtcclxuXHRcdHdwYmNfY2FsZW5kYXJfX2JsdXJfX3N0b3AoIHJlc291cmNlX2lkICk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBBZGQgQmx1ciB0byAgY2FsZW5kYXJcclxuXHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2NhbGVuZGFyX19ibHVyX19zdGFydCggcmVzb3VyY2VfaWQgKXtcclxuXHRcdGlmICggISBqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCApLmhhc0NsYXNzKCAnd3BiY19jYWxlbmRhcl9ibHVyJyApICl7XHJcblx0XHRcdGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIHJlc291cmNlX2lkICkuYWRkQ2xhc3MoICd3cGJjX2NhbGVuZGFyX2JsdXInICk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBSZW1vdmUgQmx1ciBpbiAgY2FsZW5kYXJcclxuXHQgKiBAcGFyYW0gcmVzb3VyY2VfaWRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2NhbGVuZGFyX19ibHVyX19zdG9wKCByZXNvdXJjZV9pZCApe1xyXG5cdFx0alF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS5yZW1vdmVDbGFzcyggJ3dwYmNfY2FsZW5kYXJfYmx1cicgKTtcclxuXHR9XHJcblxyXG5cclxuXHQvLyAuLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLlxyXG5cdC8qICA9PSAgQ2FsZW5kYXIgVXBkYXRlICAtIFZpZXcgID09XHJcblx0Ly8gLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4gKi9cclxuXHJcblx0LyoqXHJcblx0ICogVXBkYXRlIGxvb2sgb2YgY2FsZW5kYXIgKHNhZmUpLlxyXG5cdCAqXHJcblx0ICogSW4gRWxlbWVudG9yIHByZXZpZXcgdGhlIERPTSBjYW4gYmUgcmUtcmVuZGVyZWQsIHNvIHRoZSBjYWxlbmRhciBlbGVtZW50IG1heSBleGlzdFxyXG5cdCAqIHdoaWxlIHRoZSBEYXRlcGljayBpbnN0YW5jZSBpcyBtaXNzaW5nLiBJbiB0aGF0IGNhc2UgdHJ5IHRvIChyZSlpbml0aWFsaXplLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtpbnR8c3RyaW5nfSByZXNvdXJjZV9pZFxyXG5cdCAqIEByZXR1cm4ge2Jvb2xlYW59IHRydWUgaWYgdXBkYXRlZCwgZmFsc2UgaWYgbm90IHBvc3NpYmxlXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19jYWxlbmRhcl9fdXBkYXRlX2xvb2socmVzb3VyY2VfaWQpIHtcclxuXHJcblx0XHR2YXIgaW5zdCA9IHdwYmNfY2FsZW5kYXJfX2dldF9pbnN0KCByZXNvdXJjZV9pZCApO1xyXG5cclxuXHRcdC8vIElmIGluc3RhbmNlIG1pc3NpbmcsIHRyeSB0byByZS1pbml0IGNhbGVuZGFyIG9uY2UuXHJcblx0XHRpZiAoIG51bGwgPT09IGluc3QgKSB7XHJcblxyXG5cdFx0XHR2YXIganFfY2FsID0galF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKTtcclxuXHJcblx0XHRcdGlmICgganFfY2FsLmxlbmd0aCAmJiAoJ2Z1bmN0aW9uJyA9PT0gdHlwZW9mIHdwYmNfY2FsZW5kYXJfc2hvdykgKSB7XHJcblxyXG5cdFx0XHRcdC8vIEVsZW1lbnRvciBzb21ldGltZXMgbGVhdmVzIHN0YWxlIGNsYXNzIHdpdGhvdXQgcmVhbCBpbnN0YW5jZS5cclxuXHRcdFx0XHRpZiAoIGpxX2NhbC5oYXNDbGFzcyggJ2hhc0RhdGVwaWNrJyApICkge1xyXG5cdFx0XHRcdFx0anFfY2FsLnJlbW92ZUNsYXNzKCAnaGFzRGF0ZXBpY2snICk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyBUcnkgdG8gaW5pdCBkYXRlcGljayBtYXJrdXAgbm93LlxyXG5cdFx0XHRcdHdwYmNfY2FsZW5kYXJfc2hvdyggcmVzb3VyY2VfaWQgKTtcclxuXHJcblx0XHRcdFx0Ly8gVHJ5IGFnYWluLlxyXG5cdFx0XHRcdGluc3QgPSB3cGJjX2NhbGVuZGFyX19nZXRfaW5zdCggcmVzb3VyY2VfaWQgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cclxuXHRcdC8vIFN0aWxsIG5vIGluc3RhbmNlIC0+IGRvIG5vdCBjcmFzaCB0aGUgd2hvbGUgYWpheCBmbG93LlxyXG5cdFx0aWYgKCBudWxsID09PSBpbnN0ICkge1xyXG5cdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHR9XHJcblxyXG5cdFx0alF1ZXJ5LmRhdGVwaWNrLl91cGRhdGVEYXRlcGljayggaW5zdCApO1xyXG5cdFx0cmV0dXJuIHRydWU7XHJcblx0fVxyXG5cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIFVwZGF0ZSBkeW5hbWljYWxseSBOdW1iZXIgb2YgTW9udGhzIGluIGNhbGVuZGFyXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gcmVzb3VyY2VfaWQgaW50XHJcblx0ICogQHBhcmFtIG1vbnRoc19udW1iZXIgaW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19jYWxlbmRhcl9fdXBkYXRlX21vbnRoc19udW1iZXIoIHJlc291cmNlX2lkLCBtb250aHNfbnVtYmVyICl7XHJcblx0XHR2YXIgaW5zdCA9IHdwYmNfY2FsZW5kYXJfX2dldF9pbnN0KCByZXNvdXJjZV9pZCApO1xyXG5cdFx0aWYgKCBudWxsICE9PSBpbnN0ICl7XHJcblx0XHRcdGluc3Quc2V0dGluZ3NbICdudW1iZXJPZk1vbnRocycgXSA9IG1vbnRoc19udW1iZXI7XHJcblx0XHRcdC8vX3dwYmMuY2FsZW5kYXJfX3NldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdjYWxlbmRhcl9udW1iZXJfb2ZfbW9udGhzJywgbW9udGhzX251bWJlciApO1xyXG5cdFx0XHR3cGJjX2NhbGVuZGFyX191cGRhdGVfbG9vayggcmVzb3VyY2VfaWQgKTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBTaG93IGNhbGVuZGFyIGluICBkaWZmZXJlbnQgU2tpblxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHNlbGVjdGVkX3NraW5fdXJsXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19fY2FsZW5kYXJfX2NoYW5nZV9za2luKCBzZWxlY3RlZF9za2luX3VybCApe1xyXG5cclxuXHQvL2NvbnNvbGUubG9nKCAnU0tJTiBTRUxFQ1RJT04gOjonLCBzZWxlY3RlZF9za2luX3VybCApO1xyXG5cclxuXHRcdC8vIFJlbW92ZSBDU1Mgc2tpblxyXG5cdFx0dmFyIHN0eWxlc2hlZXQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ3dwYmMtY2FsZW5kYXItc2tpbi1jc3MnICk7XHJcblx0XHRzdHlsZXNoZWV0LnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoIHN0eWxlc2hlZXQgKTtcclxuXHJcblxyXG5cdFx0Ly8gQWRkIG5ldyBDU1Mgc2tpblxyXG5cdFx0dmFyIGhlYWRJRCA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCBcImhlYWRcIiApWyAwIF07XHJcblx0XHR2YXIgY3NzTm9kZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdsaW5rJyApO1xyXG5cdFx0Y3NzTm9kZS50eXBlID0gJ3RleHQvY3NzJztcclxuXHRcdGNzc05vZGUuc2V0QXR0cmlidXRlKCBcImlkXCIsIFwid3BiYy1jYWxlbmRhci1za2luLWNzc1wiICk7XHJcblx0XHRjc3NOb2RlLnJlbCA9ICdzdHlsZXNoZWV0JztcclxuXHRcdGNzc05vZGUubWVkaWEgPSAnc2NyZWVuJztcclxuXHRcdGNzc05vZGUuaHJlZiA9IHNlbGVjdGVkX3NraW5fdXJsO1x0Ly9cImh0dHA6Ly9iZXRhL3dwLWNvbnRlbnQvcGx1Z2lucy9ib29raW5nL2Nzcy9za2lucy9ncmVlbi0wMS5jc3NcIjtcclxuXHRcdGhlYWRJRC5hcHBlbmRDaGlsZCggY3NzTm9kZSApO1xyXG5cdH1cclxuXHJcblxyXG5cdGZ1bmN0aW9uIHdwYmNfX2Nzc19fY2hhbmdlX3NraW4oIHNlbGVjdGVkX3NraW5fdXJsLCBzdHlsZXNoZWV0X2lkID0gJ3dwYmMtdGltZV9waWNrZXItc2tpbi1jc3MnICl7XHJcblxyXG5cdFx0Ly8gUmVtb3ZlIENTUyBza2luXHJcblx0XHR2YXIgc3R5bGVzaGVldCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCBzdHlsZXNoZWV0X2lkICk7XHJcblx0XHRzdHlsZXNoZWV0LnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoIHN0eWxlc2hlZXQgKTtcclxuXHJcblxyXG5cdFx0Ly8gQWRkIG5ldyBDU1Mgc2tpblxyXG5cdFx0dmFyIGhlYWRJRCA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCBcImhlYWRcIiApWyAwIF07XHJcblx0XHR2YXIgY3NzTm9kZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdsaW5rJyApO1xyXG5cdFx0Y3NzTm9kZS50eXBlID0gJ3RleHQvY3NzJztcclxuXHRcdGNzc05vZGUuc2V0QXR0cmlidXRlKCBcImlkXCIsIHN0eWxlc2hlZXRfaWQgKTtcclxuXHRcdGNzc05vZGUucmVsID0gJ3N0eWxlc2hlZXQnO1xyXG5cdFx0Y3NzTm9kZS5tZWRpYSA9ICdzY3JlZW4nO1xyXG5cdFx0Y3NzTm9kZS5ocmVmID0gc2VsZWN0ZWRfc2tpbl91cmw7XHQvL1wiaHR0cDovL2JldGEvd3AtY29udGVudC9wbHVnaW5zL2Jvb2tpbmcvY3NzL3NraW5zL2dyZWVuLTAxLmNzc1wiO1xyXG5cdFx0aGVhZElELmFwcGVuZENoaWxkKCBjc3NOb2RlICk7XHJcblx0fVxyXG5cclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4vKiAgPT0gIFMgVSBQIFAgTyBSIFQgICAgTSBBIFQgSCAgPT1cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBNZXJnZSBzZXZlcmFsICBpbnRlcnNlY3RlZCBpbnRlcnZhbHMgb3IgcmV0dXJuIG5vdCBpbnRlcnNlY3RlZDogICAgICAgICAgICAgICAgICAgICAgICBbWzEsM10sWzIsNl0sWzgsMTBdLFsxNSwxOF1dICAtPiAgIFtbMSw2XSxbOCwxMF0sWzE1LDE4XV1cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gW10gaW50ZXJ2YWxzXHRcdFx0IFsgWzEsM10sWzIsNF0sWzYsOF0sWzksMTBdLFszLDddIF1cclxuXHRcdCAqIEByZXR1cm5zIFtdXHRcdFx0XHRcdCBbIFsxLDhdLFs5LDEwXSBdXHJcblx0XHQgKlxyXG5cdFx0ICogRXhtYW1wbGU6IHdwYmNfaW50ZXJ2YWxzX19tZXJnZV9pbmVyc2VjdGVkKCAgWyBbMSwzXSxbMiw0XSxbNiw4XSxbOSwxMF0sWzMsN10gXSAgKTtcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19pbnRlcnZhbHNfX21lcmdlX2luZXJzZWN0ZWQoIGludGVydmFscyApe1xyXG5cclxuXHRcdFx0aWYgKCAhIGludGVydmFscyB8fCBpbnRlcnZhbHMubGVuZ3RoID09PSAwICl7XHJcblx0XHRcdFx0cmV0dXJuIFtdO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR2YXIgbWVyZ2VkID0gW107XHJcblx0XHRcdGludGVydmFscy5zb3J0KCBmdW5jdGlvbiAoIGEsIGIgKXtcclxuXHRcdFx0XHRyZXR1cm4gYVsgMCBdIC0gYlsgMCBdO1xyXG5cdFx0XHR9ICk7XHJcblxyXG5cdFx0XHR2YXIgbWVyZ2VkSW50ZXJ2YWwgPSBpbnRlcnZhbHNbIDAgXTtcclxuXHJcblx0XHRcdGZvciAoIHZhciBpID0gMTsgaSA8IGludGVydmFscy5sZW5ndGg7IGkrKyApe1xyXG5cdFx0XHRcdHZhciBpbnRlcnZhbCA9IGludGVydmFsc1sgaSBdO1xyXG5cclxuXHRcdFx0XHRpZiAoIGludGVydmFsWyAwIF0gPD0gbWVyZ2VkSW50ZXJ2YWxbIDEgXSApe1xyXG5cdFx0XHRcdFx0bWVyZ2VkSW50ZXJ2YWxbIDEgXSA9IE1hdGgubWF4KCBtZXJnZWRJbnRlcnZhbFsgMSBdLCBpbnRlcnZhbFsgMSBdICk7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdG1lcmdlZC5wdXNoKCBtZXJnZWRJbnRlcnZhbCApO1xyXG5cdFx0XHRcdFx0bWVyZ2VkSW50ZXJ2YWwgPSBpbnRlcnZhbDtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdG1lcmdlZC5wdXNoKCBtZXJnZWRJbnRlcnZhbCApO1xyXG5cdFx0XHRyZXR1cm4gbWVyZ2VkO1xyXG5cdFx0fVxyXG5cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIElzIDIgaW50ZXJ2YWxzIGludGVyc2VjdGVkOiAgICAgICBbMzYwMTEsIDg2MzkyXSAgICA8PT4gICAgWzEsIDQzMTkyXSAgPT4gIHRydWUgICAgICAoIGludGVyc2VjdGVkIClcclxuXHRcdCAqXHJcblx0XHQgKiBHb29kIGV4cGxhbmF0aW9uICBoZXJlIGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vcXVlc3Rpb25zLzMyNjk0MzQvd2hhdHMtdGhlLW1vc3QtZWZmaWNpZW50LXdheS10by10ZXN0LWlmLXR3by1yYW5nZXMtb3ZlcmxhcFxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSAgaW50ZXJ2YWxfQSAgIC0gWyAzNjAxMSwgODYzOTIgXVxyXG5cdFx0ICogQHBhcmFtICBpbnRlcnZhbF9CICAgLSBbICAgICAxLCA0MzE5MiBdXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybiBib29sXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfaW50ZXJ2YWxzX19pc19pbnRlcnNlY3RlZCggaW50ZXJ2YWxfQSwgaW50ZXJ2YWxfQiApIHtcclxuXHJcblx0XHRcdGlmIChcclxuXHRcdFx0XHRcdCggMCA9PSBpbnRlcnZhbF9BLmxlbmd0aCApXHJcblx0XHRcdFx0IHx8ICggMCA9PSBpbnRlcnZhbF9CLmxlbmd0aCApXHJcblx0XHRcdCl7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpbnRlcnZhbF9BWyAwIF0gPSBwYXJzZUludCggaW50ZXJ2YWxfQVsgMCBdICk7XHJcblx0XHRcdGludGVydmFsX0FbIDEgXSA9IHBhcnNlSW50KCBpbnRlcnZhbF9BWyAxIF0gKTtcclxuXHRcdFx0aW50ZXJ2YWxfQlsgMCBdID0gcGFyc2VJbnQoIGludGVydmFsX0JbIDAgXSApO1xyXG5cdFx0XHRpbnRlcnZhbF9CWyAxIF0gPSBwYXJzZUludCggaW50ZXJ2YWxfQlsgMSBdICk7XHJcblxyXG5cdFx0XHR2YXIgaXNfaW50ZXJzZWN0ZWQgPSBNYXRoLm1heCggaW50ZXJ2YWxfQVsgMCBdLCBpbnRlcnZhbF9CWyAwIF0gKSAtIE1hdGgubWluKCBpbnRlcnZhbF9BWyAxIF0sIGludGVydmFsX0JbIDEgXSApO1xyXG5cclxuXHRcdFx0Ly8gaWYgKCAwID09IGlzX2ludGVyc2VjdGVkICkge1xyXG5cdFx0XHQvL1x0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gU3VjaCByYW5nZXMgZ29pbmcgb25lIGFmdGVyIG90aGVyLCBlLmcuOiBbIDEyLCAxNSBdIGFuZCBbIDE1LCAyMSBdXHJcblx0XHRcdC8vIH1cclxuXHJcblx0XHRcdGlmICggaXNfaW50ZXJzZWN0ZWQgPCAwICkge1xyXG5cdFx0XHRcdHJldHVybiB0cnVlOyAgICAgICAgICAgICAgICAgICAgIC8vIElOVEVSU0VDVEVEXHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHJldHVybiBmYWxzZTsgICAgICAgICAgICAgICAgICAgICAgIC8vIE5vdCBpbnRlcnNlY3RlZFxyXG5cdFx0fVxyXG5cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEdldCB0aGUgY2xvc2V0cyBBQlMgdmFsdWUgb2YgZWxlbWVudCBpbiBhcnJheSB0byB0aGUgY3VycmVudCBteVZhbHVlXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIG15VmFsdWUgXHQtIGludCBlbGVtZW50IHRvIHNlYXJjaCBjbG9zZXQgXHRcdFx0NFxyXG5cdFx0ICogQHBhcmFtIG15QXJyYXlcdC0gYXJyYXkgb2YgZWxlbWVudHMgd2hlcmUgdG8gc2VhcmNoIFx0WzUsOCwxLDddXHJcblx0XHQgKiBAcmV0dXJucyBpbnRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ1XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfZ2V0X2Fic19jbG9zZXN0X3ZhbHVlX2luX2FyciggbXlWYWx1ZSwgbXlBcnJheSApe1xyXG5cclxuXHRcdFx0aWYgKCBteUFycmF5Lmxlbmd0aCA9PSAwICl7IFx0XHRcdFx0XHRcdFx0XHQvLyBJZiB0aGUgYXJyYXkgaXMgZW1wdHkgLT4gcmV0dXJuICB0aGUgbXlWYWx1ZVxyXG5cdFx0XHRcdHJldHVybiBteVZhbHVlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR2YXIgb2JqID0gbXlBcnJheVsgMCBdO1xyXG5cdFx0XHR2YXIgZGlmZiA9IE1hdGguYWJzKCBteVZhbHVlIC0gb2JqICk7ICAgICAgICAgICAgIFx0Ly8gR2V0IGRpc3RhbmNlIGJldHdlZW4gIDFzdCBlbGVtZW50XHJcblx0XHRcdHZhciBjbG9zZXRWYWx1ZSA9IG15QXJyYXlbIDAgXTsgICAgICAgICAgICAgICAgICAgXHRcdFx0Ly8gU2F2ZSAxc3QgZWxlbWVudFxyXG5cclxuXHRcdFx0Zm9yICggdmFyIGkgPSAxOyBpIDwgbXlBcnJheS5sZW5ndGg7IGkrKyApe1xyXG5cdFx0XHRcdG9iaiA9IG15QXJyYXlbIGkgXTtcclxuXHJcblx0XHRcdFx0aWYgKCBNYXRoLmFicyggbXlWYWx1ZSAtIG9iaiApIDwgZGlmZiApeyAgICAgXHRcdFx0Ly8gd2UgZm91bmQgY2xvc2VyIHZhbHVlIC0+IHNhdmUgaXRcclxuXHRcdFx0XHRcdGRpZmYgPSBNYXRoLmFicyggbXlWYWx1ZSAtIG9iaiApO1xyXG5cdFx0XHRcdFx0Y2xvc2V0VmFsdWUgPSBvYmo7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gY2xvc2V0VmFsdWU7XHJcblx0XHR9XHJcblxyXG5cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbi8qICA9PSAgVCBPIE8gTCBUIEkgUCBTICA9PVxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcblx0LyoqXHJcblx0ICogRGVmaW5lIHRvb2x0aXAgdG8gc2hvdywgIHdoZW4gIG1vdXNlIG92ZXIgRGF0ZSBpbiBDYWxlbmRhclxyXG5cdCAqXHJcblx0ICogQHBhcmFtICB0b29sdGlwX3RleHRcdFx0XHQtIFRleHQgdG8gc2hvd1x0XHRcdFx0J0Jvb2tlZCB0aW1lOiAxMjowMCAtIDEzOjAwPGJyPkNvc3Q6ICQyMC4wMCdcclxuXHQgKiBAcGFyYW0gIHJlc291cmNlX2lkXHRcdFx0LSBJRCBvZiBib29raW5nIHJlc291cmNlXHQnMSdcclxuXHQgKiBAcGFyYW0gIHRkX2NsYXNzXHRcdFx0XHQtIFNRTCBjbGFzc1x0XHRcdFx0XHQnMS05LTIwMjMnXHJcblx0ICogQHJldHVybnMge2Jvb2xlYW59XHRcdFx0XHRcdC0gZGVmaW5lZCB0byBzaG93IG9yIG5vdFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfc2V0X3Rvb2x0aXBfX19mb3JfX2NhbGVuZGFyX2RhdGUoIHRvb2x0aXBfdGV4dCwgcmVzb3VyY2VfaWQsIHRkX2NsYXNzICl7XHJcblxyXG5cdFx0Ly9UT0RPOiBtYWtlIGVzY2FwaW5nIG9mIHRleHQgZm9yIHF1b3Qgc3ltYm9scywgIGFuZCBKUy9IVE1MLi4uXHJcblxyXG5cdFx0alF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKyAnIHRkLmNhbDRkYXRlLScgKyB0ZF9jbGFzcyApLmF0dHIoICdkYXRhLWNvbnRlbnQnLCB0b29sdGlwX3RleHQgKTtcclxuXHJcblx0XHR2YXIgdGRfZWwgPSBqUXVlcnkoICcjY2FsZW5kYXJfYm9va2luZycgKyByZXNvdXJjZV9pZCArICcgdGQuY2FsNGRhdGUtJyArIHRkX2NsYXNzICkuZ2V0KCAwICk7XHRcdFx0XHRcdC8vIEZpeEluOiA5LjAuMS4xLlxyXG5cclxuXHRcdGlmIChcclxuXHRcdFx0ICAgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mKHRkX2VsKSApXHJcblx0XHRcdCYmICggdW5kZWZpbmVkID09IHRkX2VsLl90aXBweSApXHJcblx0XHRcdCYmICggJycgIT09IHRvb2x0aXBfdGV4dCApXHJcblx0XHQpe1xyXG5cclxuXHRcdFx0d3BiY190aXBweSggdGRfZWwgLCB7XHJcblx0XHRcdFx0XHRjb250ZW50KCByZWZlcmVuY2UgKXtcclxuXHJcblx0XHRcdFx0XHRcdHZhciBwb3BvdmVyX2NvbnRlbnQgPSByZWZlcmVuY2UuZ2V0QXR0cmlidXRlKCAnZGF0YS1jb250ZW50JyApO1xyXG5cclxuXHRcdFx0XHRcdFx0cmV0dXJuICc8ZGl2IGNsYXNzPVwicG9wb3ZlciBwb3BvdmVyX3RpcHB5XCI+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHQrICc8ZGl2IGNsYXNzPVwicG9wb3Zlci1jb250ZW50XCI+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCsgcG9wb3Zlcl9jb250ZW50XHJcblx0XHRcdFx0XHRcdFx0XHRcdCsgJzwvZGl2PidcclxuXHRcdFx0XHRcdFx0XHQgKyAnPC9kaXY+JztcclxuXHRcdFx0XHRcdH0sXHJcblx0XHRcdFx0XHRhbGxvd0hUTUwgICAgICAgIDogdHJ1ZSxcclxuXHRcdFx0XHRcdHRyaWdnZXJcdFx0XHQgOiAnbW91c2VlbnRlciBmb2N1cycsXHJcblx0XHRcdFx0XHRpbnRlcmFjdGl2ZSAgICAgIDogZmFsc2UsXHJcblx0XHRcdFx0XHRoaWRlT25DbGljayAgICAgIDogdHJ1ZSxcclxuXHRcdFx0XHRcdGludGVyYWN0aXZlQm9yZGVyOiAxMCxcclxuXHRcdFx0XHRcdG1heFdpZHRoICAgICAgICAgOiA1NTAsXHJcblx0XHRcdFx0XHR0aGVtZSAgICAgICAgICAgIDogJ3dwYmMtdGlwcHktdGltZXMnLFxyXG5cdFx0XHRcdFx0cGxhY2VtZW50ICAgICAgICA6ICd0b3AnLFxyXG5cdFx0XHRcdFx0ZGVsYXlcdFx0XHQgOiBbNDAwLCAwXSxcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS40LjIuMi5cclxuXHRcdFx0XHRcdC8vZGVsYXlcdFx0XHQgOiBbMCwgOTk5OTk5OTk5OV0sXHRcdFx0XHRcdFx0Ly8gRGVidWdlICB0b29sdGlwXHJcblx0XHRcdFx0XHRpZ25vcmVBdHRyaWJ1dGVzIDogdHJ1ZSxcclxuXHRcdFx0XHRcdHRvdWNoXHRcdFx0IDogdHJ1ZSxcdFx0XHRcdFx0XHRcdFx0Ly9bJ2hvbGQnLCA1MDBdLCAvLyA1MDBtcyBkZWxheVx0XHRcdFx0Ly8gRml4SW46IDkuMi4xLjUuXHJcblx0XHRcdFx0XHRhcHBlbmRUbzogKCkgPT4gZG9jdW1lbnQuYm9keSxcclxuXHRcdFx0fSk7XHJcblxyXG5cdFx0XHRyZXR1cm4gIHRydWU7XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuICBmYWxzZTtcclxuXHR9XHJcblxyXG5cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbi8qICA9PSAgRGF0ZXMgRnVuY3Rpb25zICA9PVxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiBHZXQgbnVtYmVyIG9mIGRhdGVzIGJldHdlZW4gMiBKUyBEYXRlc1xyXG4gKlxyXG4gKiBAcGFyYW0gZGF0ZTFcdFx0SlMgRGF0ZVxyXG4gKiBAcGFyYW0gZGF0ZTJcdFx0SlMgRGF0ZVxyXG4gKiBAcmV0dXJucyB7bnVtYmVyfVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19kYXRlc19fZGF5c19iZXR3ZWVuKGRhdGUxLCBkYXRlMikge1xyXG5cclxuICAgIC8vIFRoZSBudW1iZXIgb2YgbWlsbGlzZWNvbmRzIGluIG9uZSBkYXlcclxuICAgIHZhciBPTkVfREFZID0gMTAwMCAqIDYwICogNjAgKiAyNDtcclxuXHJcbiAgICAvLyBDb252ZXJ0IGJvdGggZGF0ZXMgdG8gbWlsbGlzZWNvbmRzXHJcbiAgICB2YXIgZGF0ZTFfbXMgPSBkYXRlMS5nZXRUaW1lKCk7XHJcbiAgICB2YXIgZGF0ZTJfbXMgPSBkYXRlMi5nZXRUaW1lKCk7XHJcblxyXG4gICAgLy8gQ2FsY3VsYXRlIHRoZSBkaWZmZXJlbmNlIGluIG1pbGxpc2Vjb25kc1xyXG4gICAgdmFyIGRpZmZlcmVuY2VfbXMgPSAgZGF0ZTFfbXMgLSBkYXRlMl9tcztcclxuXHJcbiAgICAvLyBDb252ZXJ0IGJhY2sgdG8gZGF5cyBhbmQgcmV0dXJuXHJcbiAgICByZXR1cm4gTWF0aC5yb3VuZChkaWZmZXJlbmNlX21zL09ORV9EQVkpO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIENoZWNrICBpZiB0aGlzIGFycmF5ICBvZiBkYXRlcyBpcyBjb25zZWN1dGl2ZSBhcnJheSAgb2YgZGF0ZXMgb3Igbm90LlxyXG4gKiBcdFx0ZS5nLiAgWycyMDI0LTA1LTA5JywnMjAyNC0wNS0xOScsJzIwMjQtMDUtMzAnXSAtPiBmYWxzZVxyXG4gKiBcdFx0ZS5nLiAgWycyMDI0LTA1LTA5JywnMjAyNC0wNS0xMCcsJzIwMjQtMDUtMTEnXSAtPiB0cnVlXHJcbiAqIEBwYXJhbSBzcWxfZGF0ZXNfYXJyXHQgYXJyYXlcdFx0ZS5nLjogWycyMDI0LTA1LTA5JywnMjAyNC0wNS0xOScsJzIwMjQtMDUtMzAnXVxyXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfZGF0ZXNfX2lzX2NvbnNlY3V0aXZlX2RhdGVzX2Fycl9yYW5nZSggc3FsX2RhdGVzX2FyciApe1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDEwLjAuMC41MC5cclxuXHJcblx0aWYgKCBzcWxfZGF0ZXNfYXJyLmxlbmd0aCA+IDEgKXtcclxuXHRcdHZhciBwcmV2aW9zX2RhdGUgPSB3cGJjX19nZXRfX2pzX2RhdGUoIHNxbF9kYXRlc19hcnJbIDAgXSApO1xyXG5cdFx0dmFyIGN1cnJlbnRfZGF0ZTtcclxuXHJcblx0XHRmb3IgKCB2YXIgaSA9IDE7IGkgPCBzcWxfZGF0ZXNfYXJyLmxlbmd0aDsgaSsrICl7XHJcblx0XHRcdGN1cnJlbnRfZGF0ZSA9IHdwYmNfX2dldF9fanNfZGF0ZSggc3FsX2RhdGVzX2FycltpXSApO1xyXG5cclxuXHRcdFx0aWYgKCB3cGJjX2RhdGVzX19kYXlzX2JldHdlZW4oIGN1cnJlbnRfZGF0ZSwgcHJldmlvc19kYXRlICkgIT0gMSApe1xyXG5cdFx0XHRcdHJldHVybiAgZmFsc2U7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHByZXZpb3NfZGF0ZSA9IGN1cnJlbnRfZGF0ZTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdHJldHVybiB0cnVlO1xyXG59XHJcblxyXG5cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbi8qICA9PSAgQXV0byBEYXRlcyBTZWxlY3Rpb24gID09XHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuLyoqXHJcbiAqICA9PSBIb3cgdG8gIHVzZSA/ID09XHJcbiAqXHJcbiAqICBGb3IgRGF0ZXMgc2VsZWN0aW9uLCB3ZSBuZWVkIHRvIHVzZSB0aGlzIGxvZ2ljISAgICAgV2UgbmVlZCBzZWxlY3QgdGhlIGRhdGVzIG9ubHkgYWZ0ZXIgYm9va2luZyBkYXRhIGxvYWRlZCFcclxuICpcclxuICogIENoZWNrIGV4YW1wbGUgYmVsbG93LlxyXG4gKlxyXG4gKlx0Ly8gRmlyZSBvbiBhbGwgYm9va2luZyBkYXRlcyBsb2FkZWRcclxuICpcdGpRdWVyeSggJ2JvZHknICkub24oICd3cGJjX2NhbGVuZGFyX2FqeF9fbG9hZGVkX2RhdGEnLCBmdW5jdGlvbiAoIGV2ZW50LCBsb2FkZWRfcmVzb3VyY2VfaWQgKXtcclxuICpcclxuICpcdFx0aWYgKCBsb2FkZWRfcmVzb3VyY2VfaWQgPT0gc2VsZWN0X2RhdGVzX2luX2NhbGVuZGFyX2lkICl7XHJcbiAqXHRcdFx0d3BiY19hdXRvX3NlbGVjdF9kYXRlc19pbl9jYWxlbmRhciggc2VsZWN0X2RhdGVzX2luX2NhbGVuZGFyX2lkLCAnMjAyNC0wNS0xNScsICcyMDI0LTA1LTI1JyApO1xyXG4gKlx0XHR9XHJcbiAqXHR9ICk7XHJcbiAqXHJcbiAqL1xyXG5cclxuXHJcbi8qKlxyXG4gKiBUcnkgdG8gQXV0byBzZWxlY3QgZGF0ZXMgaW4gc3BlY2lmaWMgY2FsZW5kYXIgYnkgc2ltdWxhdGVkIGNsaWNrcyBpbiBkYXRlcGlja2VyXHJcbiAqXHJcbiAqIEBwYXJhbSByZXNvdXJjZV9pZFx0XHQxXHJcbiAqIEBwYXJhbSBjaGVja19pbl95bWRcdFx0JzIwMjQtMDUtMDknXHRcdE9SICBcdFsnMjAyNC0wNS0wOScsJzIwMjQtMDUtMTknLCcyMDI0LTA1LTIwJ11cclxuICogQHBhcmFtIGNoZWNrX291dF95bWRcdFx0JzIwMjQtMDUtMTUnXHRcdE9wdGlvbmFsXHJcbiAqXHJcbiAqIEByZXR1cm5zIHtudW1iZXJ9XHRcdG51bWJlciBvZiBzZWxlY3RlZCBkYXRlc1xyXG4gKlxyXG4gKiBcdEV4YW1wbGUgMTpcdFx0XHRcdHZhciBudW1fc2VsZWN0ZWRfZGF5cyA9IHdwYmNfYXV0b19zZWxlY3RfZGF0ZXNfaW5fY2FsZW5kYXIoIDEsICcyMDI0LTA1LTE1JywgJzIwMjQtMDUtMjUnICk7XHJcbiAqIFx0RXhhbXBsZSAyOlx0XHRcdFx0dmFyIG51bV9zZWxlY3RlZF9kYXlzID0gd3BiY19hdXRvX3NlbGVjdF9kYXRlc19pbl9jYWxlbmRhciggMSwgWycyMDI0LTA1LTA5JywnMjAyNC0wNS0xOScsJzIwMjQtMDUtMjAnXSApO1xyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hdXRvX3NlbGVjdF9kYXRlc19pbl9jYWxlbmRhciggcmVzb3VyY2VfaWQsIGNoZWNrX2luX3ltZCwgY2hlY2tfb3V0X3ltZCA9ICcnICl7XHRcdFx0XHRcdFx0XHRcdC8vIEZpeEluOiAxMC4wLjAuNDcuXHJcblxyXG5cdGNvbnNvbGUubG9nKCAnV1BCQ19BVVRPX1NFTEVDVF9EQVRFU19JTl9DQUxFTkRBUiggUkVTT1VSQ0VfSUQsIENIRUNLX0lOX1lNRCwgQ0hFQ0tfT1VUX1lNRCApJywgcmVzb3VyY2VfaWQsIGNoZWNrX2luX3ltZCwgY2hlY2tfb3V0X3ltZCApO1xyXG5cclxuXHRpZiAoXHJcblx0XHQgICAoICcyMTAwLTAxLTAxJyA9PSBjaGVja19pbl95bWQgKVxyXG5cdFx0fHwgKCAnMjEwMC0wMS0wMScgPT0gY2hlY2tfb3V0X3ltZCApXHJcblx0XHR8fCAoICggJycgPT0gY2hlY2tfaW5feW1kICkgJiYgKCAnJyA9PSBjaGVja19vdXRfeW1kICkgKVxyXG5cdCl7XHJcblx0XHRyZXR1cm4gMDtcclxuXHR9XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gSWYgXHRjaGVja19pbl95bWQgID0gIFsgJzIwMjQtMDUtMDknLCcyMDI0LTA1LTE5JywnMjAyNC0wNS0zMCcgXVx0XHRcdFx0QVJSQVkgb2YgREFURVNcdFx0XHRcdFx0XHQvLyBGaXhJbjogMTAuMC4wLjUwLlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIGRhdGVzX3RvX3NlbGVjdF9hcnIgPSBbXTtcclxuXHRpZiAoIEFycmF5LmlzQXJyYXkoIGNoZWNrX2luX3ltZCApICl7XHJcblx0XHRkYXRlc190b19zZWxlY3RfYXJyID0gd3BiY19jbG9uZV9vYmooIGNoZWNrX2luX3ltZCApO1xyXG5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIEV4Y2VwdGlvbnMgdG8gIHNldCAgXHRNVUxUSVBMRSBEQVlTIFx0bW9kZVxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gaWYgZGF0ZXMgYXMgTk9UIENPTlNFQ1VUSVZFOiBbJzIwMjQtMDUtMDknLCcyMDI0LTA1LTE5JywnMjAyNC0wNS0zMCddLCAtPiBzZXQgTVVMVElQTEUgREFZUyBtb2RlXHJcblx0XHRpZiAoXHJcblx0XHRcdCAgICggZGF0ZXNfdG9fc2VsZWN0X2Fyci5sZW5ndGggPiAwIClcclxuXHRcdFx0JiYgKCAnJyA9PSBjaGVja19vdXRfeW1kIClcclxuXHRcdFx0JiYgKCAhIHdwYmNfZGF0ZXNfX2lzX2NvbnNlY3V0aXZlX2RhdGVzX2Fycl9yYW5nZSggZGF0ZXNfdG9fc2VsZWN0X2FyciApIClcclxuXHRcdCl7XHJcblx0XHRcdHdwYmNfY2FsX2RheXNfc2VsZWN0X19tdWx0aXBsZSggcmVzb3VyY2VfaWQgKTtcclxuXHRcdH1cclxuXHRcdC8vIGlmIG11bHRpcGxlIGRheXMgdG8gc2VsZWN0LCBidXQgZW5hYmxlZCBTSU5HTEUgZGF5IG1vZGUsIC0+IHNldCBNVUxUSVBMRSBEQVlTIG1vZGVcclxuXHRcdGlmIChcclxuXHRcdFx0ICAgKCBkYXRlc190b19zZWxlY3RfYXJyLmxlbmd0aCA+IDEgKVxyXG5cdFx0XHQmJiAoICcnID09IGNoZWNrX291dF95bWQgKVxyXG5cdFx0XHQmJiAoICdzaW5nbGUnID09PSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2RheXNfc2VsZWN0X21vZGUnICkgKVxyXG5cdFx0KXtcclxuXHRcdFx0d3BiY19jYWxfZGF5c19zZWxlY3RfX211bHRpcGxlKCByZXNvdXJjZV9pZCApO1xyXG5cdFx0fVxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Y2hlY2tfaW5feW1kID0gZGF0ZXNfdG9fc2VsZWN0X2FyclsgMCBdO1xyXG5cdFx0aWYgKCAnJyA9PSBjaGVja19vdXRfeW1kICl7XHJcblx0XHRcdGNoZWNrX291dF95bWQgPSBkYXRlc190b19zZWxlY3RfYXJyWyAoZGF0ZXNfdG9fc2VsZWN0X2Fyci5sZW5ndGgtMSkgXTtcclxuXHRcdH1cclxuXHR9XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblxyXG5cdGlmICggJycgPT0gY2hlY2tfaW5feW1kICl7XHJcblx0XHRjaGVja19pbl95bWQgPSBjaGVja19vdXRfeW1kO1xyXG5cdH1cclxuXHRpZiAoICcnID09IGNoZWNrX291dF95bWQgKXtcclxuXHRcdGNoZWNrX291dF95bWQgPSBjaGVja19pbl95bWQ7XHJcblx0fVxyXG5cclxuXHRpZiAoICd1bmRlZmluZWQnID09PSB0eXBlb2YgKHJlc291cmNlX2lkKSApe1xyXG5cdFx0cmVzb3VyY2VfaWQgPSAnMSc7XHJcblx0fVxyXG5cclxuXHJcblx0dmFyIGluc3QgPSB3cGJjX2NhbGVuZGFyX19nZXRfaW5zdCggcmVzb3VyY2VfaWQgKTtcclxuXHJcblx0aWYgKCBudWxsICE9PSBpbnN0ICl7XHJcblxyXG5cdFx0Ly8gVW5zZWxlY3QgYWxsIGRhdGVzIGFuZCBzZXQgIHByb3BlcnRpZXMgb2YgRGF0ZXBpY2tcclxuXHRcdGpRdWVyeSggJyNkYXRlX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS52YWwoICcnICk7ICAgICAgXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vRml4SW46IDUuNC4zXHJcblx0XHRpbnN0LnN0YXlPcGVuID0gZmFsc2U7XHJcblx0XHRpbnN0LmRhdGVzID0gW107XHJcblx0XHR2YXIgY2hlY2tfaW5fanMgPSB3cGJjX19nZXRfX2pzX2RhdGUoIGNoZWNrX2luX3ltZCApO1xyXG5cdFx0dmFyIHRkX2NlbGwgICAgID0gd3BiY19nZXRfY2xpY2tlZF90ZCggaW5zdC5pZCwgY2hlY2tfaW5fanMgKTtcclxuXHJcblx0XHQvLyBJcyBvbWUgdHlwZSBvZiBlcnJvciwgdGhlbiBzZWxlY3QgbXVsdGlwbGUgZGF5cyBzZWxlY3Rpb24gIG1vZGUuXHJcblx0XHRpZiAoICcnID09PSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2RheXNfc2VsZWN0X21vZGUnICkgKSB7XHJcbiBcdFx0XHRfd3BiYy5jYWxlbmRhcl9fc2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2RheXNfc2VsZWN0X21vZGUnLCAnbXVsdGlwbGUnICk7XHJcblx0XHR9XHJcblxyXG5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gID09IERZTkFNSUMgPT1cclxuXHRcdGlmICggJ2R5bmFtaWMnID09PSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2RheXNfc2VsZWN0X21vZGUnICkgKXtcclxuXHRcdFx0Ly8gMS1zdCBjbGlja1xyXG5cdFx0XHRpbnN0LnN0YXlPcGVuID0gZmFsc2U7XHJcblx0XHRcdGpRdWVyeS5kYXRlcGljay5fc2VsZWN0RGF5KCB0ZF9jZWxsLCAnIycgKyBpbnN0LmlkLCBjaGVja19pbl9qcy5nZXRUaW1lKCkgKTtcclxuXHRcdFx0aWYgKCAwID09PSBpbnN0LmRhdGVzLmxlbmd0aCApe1xyXG5cdFx0XHRcdHJldHVybiAwOyAgXHRcdFx0XHRcdFx0XHRcdC8vIEZpcnN0IGNsaWNrICB3YXMgdW5zdWNjZXNzZnVsLCBzbyB3ZSBtdXN0IG5vdCBtYWtlIG90aGVyIGNsaWNrXHJcblx0XHRcdH1cclxuXHJcblx0XHRcdC8vIDItbmQgY2xpY2tcclxuXHRcdFx0dmFyIGNoZWNrX291dF9qcyA9IHdwYmNfX2dldF9fanNfZGF0ZSggY2hlY2tfb3V0X3ltZCApO1xyXG5cdFx0XHR2YXIgdGRfY2VsbF9vdXQgPSB3cGJjX2dldF9jbGlja2VkX3RkKCBpbnN0LmlkLCBjaGVja19vdXRfanMgKTtcclxuXHRcdFx0aW5zdC5zdGF5T3BlbiA9IHRydWU7XHJcblx0XHRcdGpRdWVyeS5kYXRlcGljay5fc2VsZWN0RGF5KCB0ZF9jZWxsX291dCwgJyMnICsgaW5zdC5pZCwgY2hlY2tfb3V0X2pzLmdldFRpbWUoKSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gID09IEZJWEVEID09XHJcblx0XHRpZiAoICAnZml4ZWQnID09PSBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2RheXNfc2VsZWN0X21vZGUnICkpIHtcclxuXHRcdFx0alF1ZXJ5LmRhdGVwaWNrLl9zZWxlY3REYXkoIHRkX2NlbGwsICcjJyArIGluc3QuaWQsIGNoZWNrX2luX2pzLmdldFRpbWUoKSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gID09IFNJTkdMRSA9PVxyXG5cdFx0aWYgKCAnc2luZ2xlJyA9PT0gX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdkYXlzX3NlbGVjdF9tb2RlJyApICl7XHJcblx0XHRcdC8valF1ZXJ5LmRhdGVwaWNrLl9yZXN0cmljdE1pbk1heCggaW5zdCwgalF1ZXJ5LmRhdGVwaWNrLl9kZXRlcm1pbmVEYXRlKCBpbnN0LCBjaGVja19pbl9qcywgbnVsbCApICk7XHRcdC8vIERvIHdlIG5lZWQgdG8gcnVuICB0aGlzID8gUGxlYXNlIG5vdGUsIGNoZWNrX2luX2pzIG11c3QgIGhhdmUgdGltZSwgIG1pbiwgc2VjIGRlZmluZWQgdG8gMCFcclxuXHRcdFx0alF1ZXJ5LmRhdGVwaWNrLl9zZWxlY3REYXkoIHRkX2NlbGwsICcjJyArIGluc3QuaWQsIGNoZWNrX2luX2pzLmdldFRpbWUoKSApO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gID09IE1VTFRJUExFID09XHJcblx0XHRpZiAoICdtdWx0aXBsZScgPT09IF93cGJjLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnZGF5c19zZWxlY3RfbW9kZScgKSApe1xyXG5cclxuXHRcdFx0dmFyIGRhdGVzX2FycjtcclxuXHJcblx0XHRcdGlmICggZGF0ZXNfdG9fc2VsZWN0X2Fyci5sZW5ndGggPiAwICl7XHJcblx0XHRcdFx0Ly8gU2l0dWF0aW9uLCB3aGVuIHdlIGhhdmUgZGF0ZXMgYXJyYXk6IFsnMjAyNC0wNS0wOScsJzIwMjQtMDUtMTknLCcyMDI0LTA1LTMwJ10uICBhbmQgbm90IHRoZSBDaGVjayBJbiAvIENoZWNrICBvdXQgZGF0ZXMgYXMgcGFyYW1ldGVyIGluIHRoaXMgZnVuY3Rpb25cclxuXHRcdFx0XHRkYXRlc19hcnIgPSB3cGJjX2dldF9zZWxlY3Rpb25fZGF0ZXNfanNfc3RyX2Fycl9fZnJvbV9hcnIoIGRhdGVzX3RvX3NlbGVjdF9hcnIgKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRkYXRlc19hcnIgPSB3cGJjX2dldF9zZWxlY3Rpb25fZGF0ZXNfanNfc3RyX2Fycl9fZnJvbV9jaGVja19pbl9vdXQoIGNoZWNrX2luX3ltZCwgY2hlY2tfb3V0X3ltZCwgaW5zdCApO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZiAoIDAgPT09IGRhdGVzX2Fyci5kYXRlc19qcy5sZW5ndGggKXtcclxuXHRcdFx0XHRyZXR1cm4gMDtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gRm9yIENhbGVuZGFyIERheXMgc2VsZWN0aW9uXHJcblx0XHRcdGZvciAoIHZhciBqID0gMDsgaiA8IGRhdGVzX2Fyci5kYXRlc19qcy5sZW5ndGg7IGorKyApeyAgICAgICAvLyBMb29wIGFycmF5IG9mIGRhdGVzXHJcblxyXG5cdFx0XHRcdHZhciBzdHJfZGF0ZSA9IHdwYmNfX2dldF9fc3FsX2NsYXNzX2RhdGUoIGRhdGVzX2Fyci5kYXRlc19qc1sgaiBdICk7XHJcblxyXG5cdFx0XHRcdC8vIERhdGUgdW5hdmFpbGFibGUgIVxyXG5cdFx0XHRcdGlmICggMCA9PSBfd3BiYy5ib29raW5nc19pbl9jYWxlbmRhcl9fZ2V0X2Zvcl9kYXRlKCByZXNvdXJjZV9pZCwgc3RyX2RhdGUgKS5kYXlfYXZhaWxhYmlsaXR5ICl7XHJcblx0XHRcdFx0XHRyZXR1cm4gMDtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGlmICggZGF0ZXNfYXJyLmRhdGVzX2pzWyBqIF0gIT0gLTEgKSB7XHJcblx0XHRcdFx0XHRpbnN0LmRhdGVzLnB1c2goIGRhdGVzX2Fyci5kYXRlc19qc1sgaiBdICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR2YXIgY2hlY2tfb3V0X2RhdGUgPSBkYXRlc19hcnIuZGF0ZXNfanNbIChkYXRlc19hcnIuZGF0ZXNfanMubGVuZ3RoIC0gMSkgXTtcclxuXHJcblx0XHRcdGluc3QuZGF0ZXMucHVzaCggY2hlY2tfb3V0X2RhdGUgKTsgXHRcdFx0Ly8gTmVlZCBhZGQgb25lIGFkZGl0aW9uYWwgU0FNRSBkYXRlIGZvciBjb3JyZWN0ICB3b3JrcyBvZiBkYXRlcyBzZWxlY3Rpb24gISEhISFcclxuXHJcblx0XHRcdHZhciBjaGVja291dF90aW1lc3RhbXAgPSBjaGVja19vdXRfZGF0ZS5nZXRUaW1lKCk7XHJcblx0XHRcdHZhciB0ZF9jZWxsID0gd3BiY19nZXRfY2xpY2tlZF90ZCggaW5zdC5pZCwgY2hlY2tfb3V0X2RhdGUgKTtcclxuXHJcblx0XHRcdGpRdWVyeS5kYXRlcGljay5fc2VsZWN0RGF5KCB0ZF9jZWxsLCAnIycgKyBpbnN0LmlkLCBjaGVja291dF90aW1lc3RhbXAgKTtcclxuXHRcdH1cclxuXHJcblxyXG5cdFx0aWYgKCAwICE9PSBpbnN0LmRhdGVzLmxlbmd0aCApe1xyXG5cdFx0XHQvLyBTY3JvbGwgdG8gc3BlY2lmaWMgbW9udGgsIGlmIHdlIHNldCBkYXRlcyBpbiBzb21lIGZ1dHVyZSBtb250aHNcclxuXHRcdFx0d3BiY19jYWxlbmRhcl9fc2Nyb2xsX3RvKCByZXNvdXJjZV9pZCwgaW5zdC5kYXRlc1sgMCBdLmdldEZ1bGxZZWFyKCksIGluc3QuZGF0ZXNbIDAgXS5nZXRNb250aCgpKzEgKTtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gaW5zdC5kYXRlcy5sZW5ndGg7XHJcblx0fVxyXG5cclxuXHRyZXR1cm4gMDtcclxufVxyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgSFRNTCB0ZCBlbGVtZW50ICh3aGVyZSB3YXMgY2xpY2sgaW4gY2FsZW5kYXIgIGRheSAgY2VsbClcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBjYWxlbmRhcl9odG1sX2lkXHRcdFx0J2NhbGVuZGFyX2Jvb2tpbmcxJ1xyXG5cdCAqIEBwYXJhbSBkYXRlX2pzXHRcdFx0XHRcdEpTIERhdGVcclxuXHQgKiBAcmV0dXJucyB7KnxqUXVlcnl9XHRcdFx0XHREb20gSFRNTCB0ZCBlbGVtZW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19nZXRfY2xpY2tlZF90ZCggY2FsZW5kYXJfaHRtbF9pZCwgZGF0ZV9qcyApe1xyXG5cclxuXHQgICAgdmFyIHRkX2NlbGwgPSBqUXVlcnkoICcjJyArIGNhbGVuZGFyX2h0bWxfaWQgKyAnIC5zcWxfZGF0ZV8nICsgd3BiY19fZ2V0X19zcWxfY2xhc3NfZGF0ZSggZGF0ZV9qcyApICkuZ2V0KCAwICk7XHJcblxyXG5cdFx0cmV0dXJuIHRkX2NlbGw7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgYXJyYXlzIG9mIEpTIGFuZCBTUUwgZGF0ZXMgYXMgZGF0ZXMgYXJyYXlcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBjaGVja19pbl95bWRcdFx0XHRcdFx0XHRcdCcyMDI0LTA1LTE1J1xyXG5cdCAqIEBwYXJhbSBjaGVja19vdXRfeW1kXHRcdFx0XHRcdFx0XHQnMjAyNC0wNS0yNSdcclxuXHQgKiBAcGFyYW0gaW5zdFx0XHRcdFx0XHRcdFx0XHRcdERhdGVwaWNrIEluc3QuIFVzZSB3cGJjX2NhbGVuZGFyX19nZXRfaW5zdCggcmVzb3VyY2VfaWQgKTtcclxuXHQgKiBAcmV0dXJucyB7e2RhdGVzX2pzOiAqW10sIGRhdGVzX3N0cjogKltdfX1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2dldF9zZWxlY3Rpb25fZGF0ZXNfanNfc3RyX2Fycl9fZnJvbV9jaGVja19pbl9vdXQoIGNoZWNrX2luX3ltZCwgY2hlY2tfb3V0X3ltZCAsIGluc3QgKXtcclxuXHJcblx0XHR2YXIgb3JpZ2luYWxfYXJyYXkgPSBbXTtcclxuXHRcdHZhciBkYXRlO1xyXG5cdFx0dmFyIGJrX2Rpc3RpbmN0X2RhdGVzID0gW107XHJcblxyXG5cdFx0dmFyIGNoZWNrX2luX2RhdGUgPSBjaGVja19pbl95bWQuc3BsaXQoICctJyApO1xyXG5cdFx0dmFyIGNoZWNrX291dF9kYXRlID0gY2hlY2tfb3V0X3ltZC5zcGxpdCggJy0nICk7XHJcblxyXG5cdFx0ZGF0ZSA9IG5ldyBEYXRlKCk7XHJcblx0XHRkYXRlLnNldEZ1bGxZZWFyKCBjaGVja19pbl9kYXRlWyAwIF0sIChjaGVja19pbl9kYXRlWyAxIF0gLSAxKSwgY2hlY2tfaW5fZGF0ZVsgMiBdICk7ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8geWVhciwgbW9udGgsIGRhdGVcclxuXHRcdHZhciBvcmlnaW5hbF9jaGVja19pbl9kYXRlID0gZGF0ZTtcclxuXHRcdG9yaWdpbmFsX2FycmF5LnB1c2goIGpRdWVyeS5kYXRlcGljay5fcmVzdHJpY3RNaW5NYXgoIGluc3QsIGpRdWVyeS5kYXRlcGljay5fZGV0ZXJtaW5lRGF0ZSggaW5zdCwgZGF0ZSwgbnVsbCApICkgKTsgLy9hZGQgZGF0ZVxyXG5cdFx0aWYgKCAhIHdwYmNfaW5fYXJyYXkoIGJrX2Rpc3RpbmN0X2RhdGVzLCAoY2hlY2tfaW5fZGF0ZVsgMiBdICsgJy4nICsgY2hlY2tfaW5fZGF0ZVsgMSBdICsgJy4nICsgY2hlY2tfaW5fZGF0ZVsgMCBdKSApICl7XHJcblx0XHRcdGJrX2Rpc3RpbmN0X2RhdGVzLnB1c2goIHBhcnNlSW50KGNoZWNrX2luX2RhdGVbIDIgXSkgKyAnLicgKyBwYXJzZUludChjaGVja19pbl9kYXRlWyAxIF0pICsgJy4nICsgY2hlY2tfaW5fZGF0ZVsgMCBdICk7XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyIGRhdGVfb3V0ID0gbmV3IERhdGUoKTtcclxuXHRcdGRhdGVfb3V0LnNldEZ1bGxZZWFyKCBjaGVja19vdXRfZGF0ZVsgMCBdLCAoY2hlY2tfb3V0X2RhdGVbIDEgXSAtIDEpLCBjaGVja19vdXRfZGF0ZVsgMiBdICk7ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8geWVhciwgbW9udGgsIGRhdGVcclxuXHRcdHZhciBvcmlnaW5hbF9jaGVja19vdXRfZGF0ZSA9IGRhdGVfb3V0O1xyXG5cclxuXHRcdHZhciBtZXdEYXRlID0gbmV3IERhdGUoIG9yaWdpbmFsX2NoZWNrX2luX2RhdGUuZ2V0RnVsbFllYXIoKSwgb3JpZ2luYWxfY2hlY2tfaW5fZGF0ZS5nZXRNb250aCgpLCBvcmlnaW5hbF9jaGVja19pbl9kYXRlLmdldERhdGUoKSApO1xyXG5cdFx0bWV3RGF0ZS5zZXREYXRlKCBvcmlnaW5hbF9jaGVja19pbl9kYXRlLmdldERhdGUoKSArIDEgKTtcclxuXHJcblx0XHR3aGlsZSAoXHJcblx0XHRcdChvcmlnaW5hbF9jaGVja19vdXRfZGF0ZSA+IGRhdGUpICYmXHJcblx0XHRcdChvcmlnaW5hbF9jaGVja19pbl9kYXRlICE9IG9yaWdpbmFsX2NoZWNrX291dF9kYXRlKSApe1xyXG5cdFx0XHRkYXRlID0gbmV3IERhdGUoIG1ld0RhdGUuZ2V0RnVsbFllYXIoKSwgbWV3RGF0ZS5nZXRNb250aCgpLCBtZXdEYXRlLmdldERhdGUoKSApO1xyXG5cclxuXHRcdFx0b3JpZ2luYWxfYXJyYXkucHVzaCggalF1ZXJ5LmRhdGVwaWNrLl9yZXN0cmljdE1pbk1heCggaW5zdCwgalF1ZXJ5LmRhdGVwaWNrLl9kZXRlcm1pbmVEYXRlKCBpbnN0LCBkYXRlLCBudWxsICkgKSApOyAvL2FkZCBkYXRlXHJcblx0XHRcdGlmICggIXdwYmNfaW5fYXJyYXkoIGJrX2Rpc3RpbmN0X2RhdGVzLCAoZGF0ZS5nZXREYXRlKCkgKyAnLicgKyBwYXJzZUludCggZGF0ZS5nZXRNb250aCgpICsgMSApICsgJy4nICsgZGF0ZS5nZXRGdWxsWWVhcigpKSApICl7XHJcblx0XHRcdFx0YmtfZGlzdGluY3RfZGF0ZXMucHVzaCggKHBhcnNlSW50KGRhdGUuZ2V0RGF0ZSgpKSArICcuJyArIHBhcnNlSW50KCBkYXRlLmdldE1vbnRoKCkgKyAxICkgKyAnLicgKyBkYXRlLmdldEZ1bGxZZWFyKCkpICk7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdG1ld0RhdGUgPSBuZXcgRGF0ZSggZGF0ZS5nZXRGdWxsWWVhcigpLCBkYXRlLmdldE1vbnRoKCksIGRhdGUuZ2V0RGF0ZSgpICk7XHJcblx0XHRcdG1ld0RhdGUuc2V0RGF0ZSggbWV3RGF0ZS5nZXREYXRlKCkgKyAxICk7XHJcblx0XHR9XHJcblx0XHRvcmlnaW5hbF9hcnJheS5wb3AoKTtcclxuXHRcdGJrX2Rpc3RpbmN0X2RhdGVzLnBvcCgpO1xyXG5cclxuXHRcdHJldHVybiB7J2RhdGVzX2pzJzogb3JpZ2luYWxfYXJyYXksICdkYXRlc19zdHInOiBia19kaXN0aW5jdF9kYXRlc307XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgYXJyYXlzIG9mIEpTIGFuZCBTUUwgZGF0ZXMgYXMgZGF0ZXMgYXJyYXlcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBkYXRlc190b19zZWxlY3RfYXJyXHQ9IFsnMjAyNC0wNS0wOScsJzIwMjQtMDUtMTknLCcyMDI0LTA1LTMwJ11cclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHt7ZGF0ZXNfanM6ICpbXSwgZGF0ZXNfc3RyOiAqW119fVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfZ2V0X3NlbGVjdGlvbl9kYXRlc19qc19zdHJfYXJyX19mcm9tX2FyciggZGF0ZXNfdG9fc2VsZWN0X2FyciApe1x0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDEwLjAuMC41MC5cclxuXHJcblx0XHR2YXIgb3JpZ2luYWxfYXJyYXkgICAgPSBbXTtcclxuXHRcdHZhciBia19kaXN0aW5jdF9kYXRlcyA9IFtdO1xyXG5cdFx0dmFyIG9uZV9kYXRlX3N0cjtcclxuXHJcblx0XHRmb3IgKCB2YXIgZCA9IDA7IGQgPCBkYXRlc190b19zZWxlY3RfYXJyLmxlbmd0aDsgZCsrICl7XHJcblxyXG5cdFx0XHRvcmlnaW5hbF9hcnJheS5wdXNoKCB3cGJjX19nZXRfX2pzX2RhdGUoIGRhdGVzX3RvX3NlbGVjdF9hcnJbIGQgXSApICk7XHJcblxyXG5cdFx0XHRvbmVfZGF0ZV9zdHIgPSBkYXRlc190b19zZWxlY3RfYXJyWyBkIF0uc3BsaXQoJy0nKVxyXG5cdFx0XHRpZiAoICEgd3BiY19pbl9hcnJheSggYmtfZGlzdGluY3RfZGF0ZXMsIChvbmVfZGF0ZV9zdHJbIDIgXSArICcuJyArIG9uZV9kYXRlX3N0clsgMSBdICsgJy4nICsgb25lX2RhdGVfc3RyWyAwIF0pICkgKXtcclxuXHRcdFx0XHRia19kaXN0aW5jdF9kYXRlcy5wdXNoKCBwYXJzZUludChvbmVfZGF0ZV9zdHJbIDIgXSkgKyAnLicgKyBwYXJzZUludChvbmVfZGF0ZV9zdHJbIDEgXSkgKyAnLicgKyBvbmVfZGF0ZV9zdHJbIDAgXSApO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIHsnZGF0ZXNfanMnOiBvcmlnaW5hbF9hcnJheSwgJ2RhdGVzX3N0cic6IG9yaWdpbmFsX2FycmF5fTtcclxuXHR9XHJcblxyXG4vLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuLyogID09ICBBdXRvIEZpbGwgRmllbGRzIC8gQXV0byBTZWxlY3QgRGF0ZXMgID09XHJcbi8vID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PSAqL1xyXG5cclxualF1ZXJ5KCBkb2N1bWVudCApLnJlYWR5KCBmdW5jdGlvbiAoKXtcclxuXHJcblx0dmFyIHVybF9wYXJhbXMgPSBuZXcgVVJMU2VhcmNoUGFyYW1zKCB3aW5kb3cubG9jYXRpb24uc2VhcmNoICk7XHJcblxyXG5cdC8vIERpc2FibGUgZGF5cyBzZWxlY3Rpb24gIGluIGNhbGVuZGFyLCAgYWZ0ZXIgIHJlZGlyZWN0aW9uICBmcm9tICB0aGUgXCJTZWFyY2ggcmVzdWx0cyBwYWdlLCAgYWZ0ZXIgIHNlYXJjaCAgYXZhaWxhYmlsaXR5XCIgXHRcdFx0Ly8gRml4SW46IDguOC4yLjMuXHJcblx0aWYgICggJ09uJyAhPSBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICdpc19lbmFibGVkX2Jvb2tpbmdfc2VhcmNoX3Jlc3VsdHNfZGF5c19zZWxlY3QnICkgKSB7XHJcblx0XHRpZiAoXHJcblx0XHRcdCggdXJsX3BhcmFtcy5oYXMoICd3cGJjX3NlbGVjdF9jaGVja19pbicgKSApICYmXHJcblx0XHRcdCggdXJsX3BhcmFtcy5oYXMoICd3cGJjX3NlbGVjdF9jaGVja19vdXQnICkgKSAmJlxyXG5cdFx0XHQoIHVybF9wYXJhbXMuaGFzKCAnd3BiY19zZWxlY3RfY2FsZW5kYXJfaWQnICkgKVxyXG5cdFx0KXtcclxuXHJcblx0XHRcdHZhciBzZWxlY3RfZGF0ZXNfaW5fY2FsZW5kYXJfaWQgPSBwYXJzZUludCggdXJsX3BhcmFtcy5nZXQoICd3cGJjX3NlbGVjdF9jYWxlbmRhcl9pZCcgKSApO1xyXG5cclxuXHRcdFx0Ly8gRmlyZSBvbiBhbGwgYm9va2luZyBkYXRlcyBsb2FkZWRcclxuXHRcdFx0alF1ZXJ5KCAnYm9keScgKS5vbiggJ3dwYmNfY2FsZW5kYXJfYWp4X19sb2FkZWRfZGF0YScsIGZ1bmN0aW9uICggZXZlbnQsIGxvYWRlZF9yZXNvdXJjZV9pZCApe1xyXG5cclxuXHRcdFx0XHRpZiAoIGxvYWRlZF9yZXNvdXJjZV9pZCA9PSBzZWxlY3RfZGF0ZXNfaW5fY2FsZW5kYXJfaWQgKXtcclxuXHRcdFx0XHRcdHdwYmNfYXV0b19zZWxlY3RfZGF0ZXNfaW5fY2FsZW5kYXIoIHNlbGVjdF9kYXRlc19pbl9jYWxlbmRhcl9pZCwgdXJsX3BhcmFtcy5nZXQoICd3cGJjX3NlbGVjdF9jaGVja19pbicgKSwgdXJsX3BhcmFtcy5nZXQoICd3cGJjX3NlbGVjdF9jaGVja19vdXQnICkgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gKTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdGlmICggdXJsX3BhcmFtcy5oYXMoICd3cGJjX2F1dG9fZmlsbCcgKSApe1xyXG5cclxuXHRcdHZhciB3cGJjX2F1dG9fZmlsbF92YWx1ZSA9IHVybF9wYXJhbXMuZ2V0KCAnd3BiY19hdXRvX2ZpbGwnICk7XHJcblxyXG5cdFx0Ly8gQ29udmVydCBiYWNrLiAgICAgU29tZSBzeXN0ZW1zIGRvIG5vdCBsaWtlIHN5bWJvbCAnficgaW4gVVJMLCBzbyAgd2UgbmVlZCB0byByZXBsYWNlIHRvICBzb21lIG90aGVyIHN5bWJvbHNcclxuXHRcdHdwYmNfYXV0b19maWxsX3ZhbHVlID0gd3BiY19hdXRvX2ZpbGxfdmFsdWUucmVwbGFjZUFsbCggJ19eXycsICd+JyApO1xyXG5cclxuXHRcdHdwYmNfYXV0b19maWxsX2Jvb2tpbmdfZmllbGRzKCB3cGJjX2F1dG9fZmlsbF92YWx1ZSApO1xyXG5cdH1cclxuXHJcbn0gKTtcclxuXHJcbi8qKlxyXG4gKiBBdXRvZmlsbCAvIHNlbGVjdCBib29raW5nIGZvcm0gIGZpZWxkcyBieSAgdmFsdWVzIGZyb20gIHRoZSBHRVQgcmVxdWVzdCAgcGFyYW1ldGVyOiA/d3BiY19hdXRvX2ZpbGw9XHJcbiAqXHJcbiAqIEBwYXJhbSBhdXRvX2ZpbGxfc3RyXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2F1dG9fZmlsbF9ib29raW5nX2ZpZWxkcyggYXV0b19maWxsX3N0ciApe1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRml4SW46IDEwLjAuMC40OC5cclxuXHJcblx0aWYgKCAnJyA9PSBhdXRvX2ZpbGxfc3RyICl7XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG5cclxuLy8gY29uc29sZS5sb2coICdXUEJDX0FVVE9fRklMTF9CT09LSU5HX0ZJRUxEUyggQVVUT19GSUxMX1NUUiApJywgYXV0b19maWxsX3N0cik7XHJcblxyXG5cdHZhciBmaWVsZHNfYXJyID0gd3BiY19hdXRvX2ZpbGxfYm9va2luZ19maWVsZHNfX3BhcnNlKCBhdXRvX2ZpbGxfc3RyICk7XHJcblxyXG5cdGZvciAoIGxldCBpID0gMDsgaSA8IGZpZWxkc19hcnIubGVuZ3RoOyBpKysgKXtcclxuXHRcdGpRdWVyeSggJ1tuYW1lPVwiJyArIGZpZWxkc19hcnJbIGkgXVsgJ25hbWUnIF0gKyAnXCJdJyApLnZhbCggZmllbGRzX2FyclsgaSBdWyAndmFsdWUnIF0gKTtcclxuXHR9XHJcbn1cclxuXHJcblx0LyoqXHJcblx0ICogUGFyc2UgZGF0YSBmcm9tICBnZXQgcGFyYW1ldGVyOlx0P3dwYmNfYXV0b19maWxsPXZpc2l0b3JzMjMxXjJ+bWF4X2NhcGFjaXR5MjMxXjJcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBkYXRhX3N0ciAgICAgID0gICAndmlzaXRvcnMyMzFeMn5tYXhfY2FwYWNpdHkyMzFeMic7XHJcblx0ICogQHJldHVybnMgeyp9XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19hdXRvX2ZpbGxfYm9va2luZ19maWVsZHNfX3BhcnNlKCBkYXRhX3N0ciApe1xyXG5cclxuXHRcdHZhciBmaWx0ZXJfb3B0aW9uc19hcnIgPSBbXTtcclxuXHJcblx0XHR2YXIgZGF0YV9hcnIgPSBkYXRhX3N0ci5zcGxpdCggJ34nICk7XHJcblxyXG5cdFx0Zm9yICggdmFyIGogPSAwOyBqIDwgZGF0YV9hcnIubGVuZ3RoOyBqKysgKXtcclxuXHJcblx0XHRcdHZhciBteV9mb3JtX2ZpZWxkID0gZGF0YV9hcnJbIGogXS5zcGxpdCggJ14nICk7XHJcblxyXG5cdFx0XHR2YXIgZmlsdGVyX25hbWUgID0gKCd1bmRlZmluZWQnICE9PSB0eXBlb2YgKG15X2Zvcm1fZmllbGRbIDAgXSkpID8gbXlfZm9ybV9maWVsZFsgMCBdIDogJyc7XHJcblx0XHRcdHZhciBmaWx0ZXJfdmFsdWUgPSAoJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiAobXlfZm9ybV9maWVsZFsgMSBdKSkgPyBteV9mb3JtX2ZpZWxkWyAxIF0gOiAnJztcclxuXHJcblx0XHRcdGZpbHRlcl9vcHRpb25zX2Fyci5wdXNoKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCduYW1lJyAgOiBmaWx0ZXJfbmFtZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd2YWx1ZScgOiBmaWx0ZXJfdmFsdWVcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHQgICApO1xyXG5cdFx0fVxyXG5cdFx0cmV0dXJuIGZpbHRlcl9vcHRpb25zX2FycjtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIFBhcnNlIGRhdGEgZnJvbSAgZ2V0IHBhcmFtZXRlcjpcdD9zZWFyY2hfZ2V0X19jdXN0b21fcGFyYW1zPS4uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGRhdGFfc3RyICAgICAgPSAgICd0ZXh0XnNlYXJjaF9maWVsZF9fZGlzcGxheV9jaGVja19pbl4yMy4wNS4yMDI0fnRleHRec2VhcmNoX2ZpZWxkX19kaXNwbGF5X2NoZWNrX291dF4yNi4wNS4yMDI0fnNlbGVjdGJveC1vbmVec2VhcmNoX3F1YW50aXR5XjJ+c2VsZWN0Ym94LW9uZV5sb2NhdGlvbl5TcGFpbn5zZWxlY3Rib3gtb25lXm1heF9jYXBhY2l0eV4yfnNlbGVjdGJveC1vbmVeYW1lbml0eV5wYXJraW5nfmNoZWNrYm94XnNlYXJjaF9maWVsZF9fZXh0ZW5kX3NlYXJjaF9kYXlzXjV+c3VibWl0Xl5TZWFyY2h+aGlkZGVuXnNlYXJjaF9nZXRfX2NoZWNrX2luX3ltZF4yMDI0LTA1LTIzfmhpZGRlbl5zZWFyY2hfZ2V0X19jaGVja19vdXRfeW1kXjIwMjQtMDUtMjZ+aGlkZGVuXnNlYXJjaF9nZXRfX3RpbWVefmhpZGRlbl5zZWFyY2hfZ2V0X19xdWFudGl0eV4yfmhpZGRlbl5zZWFyY2hfZ2V0X19leHRlbmReNX5oaWRkZW5ec2VhcmNoX2dldF9fdXNlcnNfaWRefmhpZGRlbl5zZWFyY2hfZ2V0X19jdXN0b21fcGFyYW1zXn4nO1xyXG5cdCAqIEByZXR1cm5zIHsqfVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfYXV0b19maWxsX3NlYXJjaF9maWVsZHNfX3BhcnNlKCBkYXRhX3N0ciApe1xyXG5cclxuXHRcdHZhciBmaWx0ZXJfb3B0aW9uc19hcnIgPSBbXTtcclxuXHJcblx0XHR2YXIgZGF0YV9hcnIgPSBkYXRhX3N0ci5zcGxpdCggJ34nICk7XHJcblxyXG5cdFx0Zm9yICggdmFyIGogPSAwOyBqIDwgZGF0YV9hcnIubGVuZ3RoOyBqKysgKXtcclxuXHJcblx0XHRcdHZhciBteV9mb3JtX2ZpZWxkID0gZGF0YV9hcnJbIGogXS5zcGxpdCggJ14nICk7XHJcblxyXG5cdFx0XHR2YXIgZmlsdGVyX3R5cGUgID0gKCd1bmRlZmluZWQnICE9PSB0eXBlb2YgKG15X2Zvcm1fZmllbGRbIDAgXSkpID8gbXlfZm9ybV9maWVsZFsgMCBdIDogJyc7XHJcblx0XHRcdHZhciBmaWx0ZXJfbmFtZSAgPSAoJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiAobXlfZm9ybV9maWVsZFsgMSBdKSkgPyBteV9mb3JtX2ZpZWxkWyAxIF0gOiAnJztcclxuXHRcdFx0dmFyIGZpbHRlcl92YWx1ZSA9ICgndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChteV9mb3JtX2ZpZWxkWyAyIF0pKSA/IG15X2Zvcm1fZmllbGRbIDIgXSA6ICcnO1xyXG5cclxuXHRcdFx0ZmlsdGVyX29wdGlvbnNfYXJyLnB1c2goXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3R5cGUnICA6IGZpbHRlcl90eXBlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J25hbWUnICA6IGZpbHRlcl9uYW1lLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3ZhbHVlJyA6IGZpbHRlcl92YWx1ZVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHRcdCAgICk7XHJcblx0XHR9XHJcblx0XHRyZXR1cm4gZmlsdGVyX29wdGlvbnNfYXJyO1xyXG5cdH1cclxuXHJcblxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLyogID09ICBBdXRvIFVwZGF0ZSBudW1iZXIgb2YgbW9udGhzIGluIGNhbGVuZGFycyBPTiBzY3JlZW4gc2l6ZSBjaGFuZ2VkICA9PVxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiBBdXRvIFVwZGF0ZSBOdW1iZXIgb2YgTW9udGhzIGluIENhbGVuZGFyLCBlLmcuOiAgXHRcdGlmICAgICggV0lORE9XX1dJRFRIIDw9IDc4MnB4ICkgICA+Pj4gXHRNT05USFNfTlVNQkVSID0gMVxyXG4gKiAgIEVMU0U6ICBudW1iZXIgb2YgbW9udGhzIGRlZmluZWQgaW4gc2hvcnRjb2RlLlxyXG4gKiBAcGFyYW0gcmVzb3VyY2VfaWQgaW50XHJcbiAqXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2NhbGVuZGFyX19hdXRvX3VwZGF0ZV9tb250aHNfbnVtYmVyX19vbl9yZXNpemUoIHJlc291cmNlX2lkICl7XHJcblxyXG5cdGlmICggdHJ1ZSA9PT0gX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAnaXNfYWxsb3dfc2V2ZXJhbF9tb250aHNfb25fbW9iaWxlJyApICkge1xyXG5cdFx0cmV0dXJuIGZhbHNlO1xyXG5cdH1cclxuXHJcblx0dmFyIGxvY2FsX19udW1iZXJfb2ZfbW9udGhzID0gcGFyc2VJbnQoIF93cGJjLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnY2FsZW5kYXJfbnVtYmVyX29mX21vbnRocycgKSApO1xyXG5cclxuXHRpZiAoIGxvY2FsX19udW1iZXJfb2ZfbW9udGhzID4gMSApe1xyXG5cclxuXHRcdGlmICggalF1ZXJ5KCB3aW5kb3cgKS53aWR0aCgpIDw9IDc4MiApe1xyXG5cdFx0XHR3cGJjX2NhbGVuZGFyX191cGRhdGVfbW9udGhzX251bWJlciggcmVzb3VyY2VfaWQsIDEgKTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdHdwYmNfY2FsZW5kYXJfX3VwZGF0ZV9tb250aHNfbnVtYmVyKCByZXNvdXJjZV9pZCwgbG9jYWxfX251bWJlcl9vZl9tb250aHMgKTtcclxuXHRcdH1cclxuXHJcblx0fVxyXG59XHJcblxyXG4vKipcclxuICogQXV0byBVcGRhdGUgTnVtYmVyIG9mIE1vbnRocyBpbiAgIEFMTCAgIENhbGVuZGFyc1xyXG4gKlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxlbmRhcnNfX2F1dG9fdXBkYXRlX21vbnRoc19udW1iZXIoKXtcclxuXHJcblx0dmFyIGFsbF9jYWxlbmRhcnNfYXJyID0gX3dwYmMuY2FsZW5kYXJzX2FsbF9fZ2V0KCk7XHJcblxyXG5cdC8vIFRoaXMgTE9PUCBcImZvciBpblwiIGlzIEdPT0QsIGJlY2F1c2Ugd2UgY2hlY2sgIGhlcmUga2V5cyAgICAnY2FsZW5kYXJfJyA9PT0gY2FsZW5kYXJfaWQuc2xpY2UoIDAsIDkgKVxyXG5cdGZvciAoIHZhciBjYWxlbmRhcl9pZCBpbiBhbGxfY2FsZW5kYXJzX2FyciApe1xyXG5cdFx0aWYgKCAnY2FsZW5kYXJfJyA9PT0gY2FsZW5kYXJfaWQuc2xpY2UoIDAsIDkgKSApe1xyXG5cdFx0XHR2YXIgcmVzb3VyY2VfaWQgPSBwYXJzZUludCggY2FsZW5kYXJfaWQuc2xpY2UoIDkgKSApO1x0XHRcdC8vICAnY2FsZW5kYXJfMycgLT4gM1xyXG5cdFx0XHRpZiAoIHJlc291cmNlX2lkID4gMCApe1xyXG5cdFx0XHRcdHdwYmNfY2FsZW5kYXJfX2F1dG9fdXBkYXRlX21vbnRoc19udW1iZXJfX29uX3Jlc2l6ZSggcmVzb3VyY2VfaWQgKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH1cclxufVxyXG5cclxuLyoqXHJcbiAqIElmIGJyb3dzZXIgd2luZG93IGNoYW5nZWQsICB0aGVuICB1cGRhdGUgbnVtYmVyIG9mIG1vbnRocy5cclxuICovXHJcbmpRdWVyeSggd2luZG93ICkub24oICdyZXNpemUnLCBmdW5jdGlvbiAoKXtcclxuXHR3cGJjX2NhbGVuZGFyc19fYXV0b191cGRhdGVfbW9udGhzX251bWJlcigpO1xyXG59ICk7XHJcblxyXG4vKipcclxuICogQXV0byB1cGRhdGUgY2FsZW5kYXIgbnVtYmVyIG9mIG1vbnRocyBvbiBpbml0aWFsIHBhZ2UgbG9hZFxyXG4gKi9cclxualF1ZXJ5KCBkb2N1bWVudCApLnJlYWR5KCBmdW5jdGlvbiAoKXtcclxuXHR2YXIgY2xvc2VkX3RpbWVyID0gc2V0VGltZW91dCggZnVuY3Rpb24gKCl7XHJcblx0XHR3cGJjX2NhbGVuZGFyc19fYXV0b191cGRhdGVfbW9udGhzX251bWJlcigpO1xyXG5cdH0sIDEwMCApO1xyXG59KTtcclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4vKiAgPT0gIENoZWNrOiBjYWxlbmRhcl9kYXRlc19zdGFydDogXCIyMDI2LTAxLTAxXCIsIGNhbGVuZGFyX2RhdGVzX2VuZDogXCIyMDI2LTEyLTMxXCIgPT0gIC8vIEZpeEluOiAxMC4xMy4xLjQuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cdC8qKlxyXG5cdCAqIEdldCBTdGFydCBKUyBEYXRlIG9mIHN0YXJ0aW5nIGRhdGVzIGluIGNhbGVuZGFyLCBmcm9tIHRoZSBfd3BiYyBvYmplY3QuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gaW50ZWdlciByZXNvdXJjZV9pZCAtIHJlc291cmNlIElELCBlLmcuOiAxLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfY2FsZW5kYXJfX2dldF9kYXRlc19zdGFydCggcmVzb3VyY2VfaWQgKSB7XHJcblx0XHRyZXR1cm4gd3BiY19jYWxlbmRhcl9fZ2V0X2RhdGVfcGFyYW1ldGVyKCByZXNvdXJjZV9pZCwgJ2NhbGVuZGFyX2RhdGVzX3N0YXJ0JyApO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogR2V0IEVuZCBKUyBEYXRlIG9mIGVuZGluZyBkYXRlcyBpbiBjYWxlbmRhciwgZnJvbSB0aGUgX3dwYmMgb2JqZWN0LlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGludGVnZXIgcmVzb3VyY2VfaWQgLSByZXNvdXJjZSBJRCwgZS5nLjogMS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2NhbGVuZGFyX19nZXRfZGF0ZXNfZW5kKHJlc291cmNlX2lkKSB7XHJcblx0XHRyZXR1cm4gd3BiY19jYWxlbmRhcl9fZ2V0X2RhdGVfcGFyYW1ldGVyKCByZXNvdXJjZV9pZCwgJ2NhbGVuZGFyX2RhdGVzX2VuZCcgKTtcclxuXHR9XHJcblxyXG4vKipcclxuICogR2V0IHZhbGlkYXRlcyBkYXRlIHBhcmFtZXRlci5cclxuICpcclxuICogQHBhcmFtIHJlc291cmNlX2lkICAgLSAxXHJcbiAqIEBwYXJhbSBwYXJhbWV0ZXJfc3RyIC0gJ2NhbGVuZGFyX2RhdGVzX3N0YXJ0JyB8ICdjYWxlbmRhcl9kYXRlc19lbmQnIHwgLi4uXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2NhbGVuZGFyX19nZXRfZGF0ZV9wYXJhbWV0ZXIocmVzb3VyY2VfaWQsIHBhcmFtZXRlcl9zdHIpIHtcclxuXHJcblx0dmFyIGRhdGVfZXhwZWN0ZWRfeW1kID0gX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsIHBhcmFtZXRlcl9zdHIgKTtcclxuXHJcblx0aWYgKCAhIGRhdGVfZXhwZWN0ZWRfeW1kICkge1xyXG5cdFx0cmV0dXJuIGZhbHNlOyAgICAgICAgICAgICAvLyAnJyB8IDAgfCBudWxsIHwgdW5kZWZpbmVkICAtPiBmYWxzZS5cclxuXHR9XHJcblxyXG5cdGlmICggLTEgIT09IGRhdGVfZXhwZWN0ZWRfeW1kLmluZGV4T2YoICctJyApICkge1xyXG5cclxuXHRcdHZhciBkYXRlX2V4cGVjdGVkX3ltZF9hcnIgPSBkYXRlX2V4cGVjdGVkX3ltZC5zcGxpdCggJy0nICk7XHQvLyAnMjAyNS0wNy0yNicgLT4gWycyMDI1JywgJzA3JywgJzI2J11cclxuXHJcblx0XHRpZiAoIGRhdGVfZXhwZWN0ZWRfeW1kX2Fyci5sZW5ndGggPiAwICkge1xyXG5cdFx0XHR2YXIgeWVhciAgPSAoZGF0ZV9leHBlY3RlZF95bWRfYXJyLmxlbmd0aCA+IDApID8gcGFyc2VJbnQoIGRhdGVfZXhwZWN0ZWRfeW1kX2FyclswXSApIDogbmV3IERhdGUoKS5nZXRGdWxsWWVhcigpO1x0Ly8gWWVhci5cclxuXHRcdFx0dmFyIG1vbnRoID0gKGRhdGVfZXhwZWN0ZWRfeW1kX2Fyci5sZW5ndGggPiAxKSA/IChwYXJzZUludCggZGF0ZV9leHBlY3RlZF95bWRfYXJyWzFdICkgLSAxKSA6IDA7ICAvLyAobW9udGggLSAxKSBvciAwIC0gSmFuLlxyXG5cdFx0XHR2YXIgZGF5ICAgPSAoZGF0ZV9leHBlY3RlZF95bWRfYXJyLmxlbmd0aCA+IDIpID8gcGFyc2VJbnQoIGRhdGVfZXhwZWN0ZWRfeW1kX2FyclsyXSApIDogMTsgIC8vIGRhdGUgb3IgT3RoZXJ3aXNlIDFzdCBvZiBtb250aFxyXG5cclxuXHRcdFx0dmFyIGRhdGVfanMgPSBuZXcgRGF0ZSggeWVhciwgbW9udGgsIGRheSwgMCwgMCwgMCwgMCApO1xyXG5cclxuXHRcdFx0cmV0dXJuIGRhdGVfanM7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHRyZXR1cm4gZmFsc2U7ICAvLyBGYWxsYmFjaywgIGlmIHdlIG5vdCBwYXJzZWQgdGhpcyBwYXJhbWV0ZXIgICdjYWxlbmRhcl9kYXRlc19zdGFydCcgPSAnMjAyNS0wNy0yNicsICBmb3IgZXhhbXBsZSBiZWNhdXNlIG9mICdjYWxlbmRhcl9kYXRlc19zdGFydCcgPSAnc2ZzZGYnLlxyXG59IiwiLyoqXHJcbiAqID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbiAqXHRpbmNsdWRlcy9fX2pzL2NhbC9kYXlzX3NlbGVjdF9jdXN0b20uanNcclxuICogPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuICovXHJcblxyXG4vLyBGaXhJbjogOS44LjkuMi5cclxuXHJcbi8qKlxyXG4gKiBSZS1Jbml0IENhbGVuZGFyIGFuZCBSZS1SZW5kZXIgaXQuXHJcbiAqXHJcbiAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxfX3JlX2luaXQoIHJlc291cmNlX2lkICl7XHJcblxyXG5cdC8vIFJlbW92ZSBDTEFTUyAgZm9yIGFiaWxpdHkgdG8gcmUtcmVuZGVyIGFuZCByZWluaXQgY2FsZW5kYXIuXHJcblx0alF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQgKS5yZW1vdmVDbGFzcyggJ2hhc0RhdGVwaWNrJyApO1xyXG5cdHdwYmNfY2FsZW5kYXJfc2hvdyggcmVzb3VyY2VfaWQgKTtcclxufVxyXG5cclxuXHJcbi8qKlxyXG4gKiBSZS1Jbml0IHByZXZpb3VzbHkgIHNhdmVkIGRheXMgc2VsZWN0aW9uICB2YXJpYWJsZXMuXHJcbiAqXHJcbiAqIEBwYXJhbSByZXNvdXJjZV9pZFxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxfZGF5c19zZWxlY3RfX3JlX2luaXQoIHJlc291cmNlX2lkICl7XHJcblxyXG5cdF93cGJjLmNhbGVuZGFyX19zZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnc2F2ZWRfdmFyaWFibGVfX19kYXlzX3NlbGVjdF9pbml0aWFsJ1xyXG5cdFx0LCB7XHJcblx0XHRcdCdkeW5hbWljX19kYXlzX21pbicgICAgICAgIDogX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdkeW5hbWljX19kYXlzX21pbicgKSxcclxuXHRcdFx0J2R5bmFtaWNfX2RheXNfbWF4JyAgICAgICAgOiBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2R5bmFtaWNfX2RheXNfbWF4JyApLFxyXG5cdFx0XHQnZHluYW1pY19fZGF5c19zcGVjaWZpYycgICA6IF93cGJjLmNhbGVuZGFyX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnZHluYW1pY19fZGF5c19zcGVjaWZpYycgKSxcclxuXHRcdFx0J2R5bmFtaWNfX3dlZWtfZGF5c19fc3RhcnQnOiBfd3BiYy5jYWxlbmRhcl9fZ2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2R5bmFtaWNfX3dlZWtfZGF5c19fc3RhcnQnICksXHJcblx0XHRcdCdmaXhlZF9fZGF5c19udW0nICAgICAgICAgIDogX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdmaXhlZF9fZGF5c19udW0nICksXHJcblx0XHRcdCdmaXhlZF9fd2Vla19kYXlzX19zdGFydCcgIDogX3dwYmMuY2FsZW5kYXJfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdmaXhlZF9fd2Vla19kYXlzX19zdGFydCcgKVxyXG5cdFx0fVxyXG5cdCk7XHJcbn1cclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuLyoqXHJcbiAqIFNldCBTaW5nbGUgRGF5IHNlbGVjdGlvbiAtIGFmdGVyIHBhZ2UgbG9hZFxyXG4gKlxyXG4gKiBAcGFyYW0gcmVzb3VyY2VfaWRcdFx0SUQgb2YgYm9va2luZyByZXNvdXJjZVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxfcmVhZHlfZGF5c19zZWxlY3RfX3NpbmdsZSggcmVzb3VyY2VfaWQgKXtcclxuXHJcblx0Ly8gUmUtZGVmaW5lIHNlbGVjdGlvbiwgb25seSBhZnRlciBwYWdlIGxvYWRlZCB3aXRoIGFsbCBpbml0IHZhcnNcclxuXHRqUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCl7XHJcblxyXG5cdFx0Ly8gV2FpdCAxIHNlY29uZCwganVzdCB0byAgYmUgc3VyZSwgdGhhdCBhbGwgaW5pdCB2YXJzIGRlZmluZWRcclxuXHRcdHNldFRpbWVvdXQoZnVuY3Rpb24oKXtcclxuXHJcblx0XHRcdHdwYmNfY2FsX2RheXNfc2VsZWN0X19zaW5nbGUoIHJlc291cmNlX2lkICk7XHJcblxyXG5cdFx0fSwgMTAwMCk7XHJcblx0fSk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTZXQgU2luZ2xlIERheSBzZWxlY3Rpb25cclxuICogQ2FuIGJlIHJ1biBhdCBhbnkgIHRpbWUsICB3aGVuICBjYWxlbmRhciBkZWZpbmVkIC0gdXNlZnVsIGZvciBjb25zb2xlIHJ1bi5cclxuICpcclxuICogQHBhcmFtIHJlc291cmNlX2lkXHRcdElEIG9mIGJvb2tpbmcgcmVzb3VyY2VcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfY2FsX2RheXNfc2VsZWN0X19zaW5nbGUoIHJlc291cmNlX2lkICl7XHJcblxyXG5cdF93cGJjLmNhbGVuZGFyX19zZXRfcGFyYW1ldGVycyggcmVzb3VyY2VfaWQsIHsnZGF5c19zZWxlY3RfbW9kZSc6ICdzaW5nbGUnfSApO1xyXG5cclxuXHR3cGJjX2NhbF9kYXlzX3NlbGVjdF9fcmVfaW5pdCggcmVzb3VyY2VfaWQgKTtcclxuXHR3cGJjX2NhbF9fcmVfaW5pdCggcmVzb3VyY2VfaWQgKTtcclxufVxyXG5cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG4vKipcclxuICogU2V0IE11bHRpcGxlIERheXMgc2VsZWN0aW9uICAtIGFmdGVyIHBhZ2UgbG9hZFxyXG4gKlxyXG4gKiBAcGFyYW0gcmVzb3VyY2VfaWRcdFx0SUQgb2YgYm9va2luZyByZXNvdXJjZVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxfcmVhZHlfZGF5c19zZWxlY3RfX211bHRpcGxlKCByZXNvdXJjZV9pZCApe1xyXG5cclxuXHQvLyBSZS1kZWZpbmUgc2VsZWN0aW9uLCBvbmx5IGFmdGVyIHBhZ2UgbG9hZGVkIHdpdGggYWxsIGluaXQgdmFyc1xyXG5cdGpRdWVyeShkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKXtcclxuXHJcblx0XHQvLyBXYWl0IDEgc2Vjb25kLCBqdXN0IHRvICBiZSBzdXJlLCB0aGF0IGFsbCBpbml0IHZhcnMgZGVmaW5lZFxyXG5cdFx0c2V0VGltZW91dChmdW5jdGlvbigpe1xyXG5cclxuXHRcdFx0d3BiY19jYWxfZGF5c19zZWxlY3RfX211bHRpcGxlKCByZXNvdXJjZV9pZCApO1xyXG5cclxuXHRcdH0sIDEwMDApO1xyXG5cdH0pO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIFNldCBNdWx0aXBsZSBEYXlzIHNlbGVjdGlvblxyXG4gKiBDYW4gYmUgcnVuIGF0IGFueSAgdGltZSwgIHdoZW4gIGNhbGVuZGFyIGRlZmluZWQgLSB1c2VmdWwgZm9yIGNvbnNvbGUgcnVuLlxyXG4gKlxyXG4gKiBAcGFyYW0gcmVzb3VyY2VfaWRcdFx0SUQgb2YgYm9va2luZyByZXNvdXJjZVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxfZGF5c19zZWxlY3RfX211bHRpcGxlKCByZXNvdXJjZV9pZCApe1xyXG5cclxuXHRfd3BiYy5jYWxlbmRhcl9fc2V0X3BhcmFtZXRlcnMoIHJlc291cmNlX2lkLCB7J2RheXNfc2VsZWN0X21vZGUnOiAnbXVsdGlwbGUnfSApO1xyXG5cclxuXHR3cGJjX2NhbF9kYXlzX3NlbGVjdF9fcmVfaW5pdCggcmVzb3VyY2VfaWQgKTtcclxuXHR3cGJjX2NhbF9fcmVfaW5pdCggcmVzb3VyY2VfaWQgKTtcclxufVxyXG5cclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuLyoqXHJcbiAqIFNldCBGaXhlZCBEYXlzIHNlbGVjdGlvbiB3aXRoICAxIG1vdXNlIGNsaWNrICAtIGFmdGVyIHBhZ2UgbG9hZFxyXG4gKlxyXG4gKiBAaW50ZWdlciByZXNvdXJjZV9pZFx0XHRcdC0gMVx0XHRcdFx0ICAgLS0gSUQgb2YgYm9va2luZyByZXNvdXJjZSAoY2FsZW5kYXIpIC1cclxuICogQGludGVnZXIgZGF5c19udW1iZXJcdFx0XHQtIDNcdFx0XHRcdCAgIC0tIG51bWJlciBvZiBkYXlzIHRvICBzZWxlY3RcdC1cclxuICogQGFycmF5IHdlZWtfZGF5c19fc3RhcnRcdC0gWy0xXSB8IFsgMSwgNV0gICAtLSAgeyAtMSAtIEFueSB8IDAgLSBTdSwgIDEgLSBNbywgIDIgLSBUdSwgMyAtIFdlLCA0IC0gVGgsIDUgLSBGciwgNiAtIFNhdCB9XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2NhbF9yZWFkeV9kYXlzX3NlbGVjdF9fZml4ZWQoIHJlc291cmNlX2lkLCBkYXlzX251bWJlciwgd2Vla19kYXlzX19zdGFydCA9IFstMV0gKXtcclxuXHJcblx0Ly8gUmUtZGVmaW5lIHNlbGVjdGlvbiwgb25seSBhZnRlciBwYWdlIGxvYWRlZCB3aXRoIGFsbCBpbml0IHZhcnNcclxuXHRqUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCl7XHJcblxyXG5cdFx0Ly8gV2FpdCAxIHNlY29uZCwganVzdCB0byAgYmUgc3VyZSwgdGhhdCBhbGwgaW5pdCB2YXJzIGRlZmluZWRcclxuXHRcdHNldFRpbWVvdXQoZnVuY3Rpb24oKXtcclxuXHJcblx0XHRcdHdwYmNfY2FsX2RheXNfc2VsZWN0X19maXhlZCggcmVzb3VyY2VfaWQsIGRheXNfbnVtYmVyLCB3ZWVrX2RheXNfX3N0YXJ0ICk7XHJcblxyXG5cdFx0fSwgMTAwMCk7XHJcblx0fSk7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogU2V0IEZpeGVkIERheXMgc2VsZWN0aW9uIHdpdGggIDEgbW91c2UgY2xpY2tcclxuICogQ2FuIGJlIHJ1biBhdCBhbnkgIHRpbWUsICB3aGVuICBjYWxlbmRhciBkZWZpbmVkIC0gdXNlZnVsIGZvciBjb25zb2xlIHJ1bi5cclxuICpcclxuICogQGludGVnZXIgcmVzb3VyY2VfaWRcdFx0XHQtIDFcdFx0XHRcdCAgIC0tIElEIG9mIGJvb2tpbmcgcmVzb3VyY2UgKGNhbGVuZGFyKSAtXHJcbiAqIEBpbnRlZ2VyIGRheXNfbnVtYmVyXHRcdFx0LSAzXHRcdFx0XHQgICAtLSBudW1iZXIgb2YgZGF5cyB0byAgc2VsZWN0XHQtXHJcbiAqIEBhcnJheSB3ZWVrX2RheXNfX3N0YXJ0XHQtIFstMV0gfCBbIDEsIDVdICAgLS0gIHsgLTEgLSBBbnkgfCAwIC0gU3UsICAxIC0gTW8sICAyIC0gVHUsIDMgLSBXZSwgNCAtIFRoLCA1IC0gRnIsIDYgLSBTYXQgfVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxfZGF5c19zZWxlY3RfX2ZpeGVkKCByZXNvdXJjZV9pZCwgZGF5c19udW1iZXIsIHdlZWtfZGF5c19fc3RhcnQgPSBbLTFdICl7XHJcblxyXG5cdF93cGJjLmNhbGVuZGFyX19zZXRfcGFyYW1ldGVycyggcmVzb3VyY2VfaWQsIHsnZGF5c19zZWxlY3RfbW9kZSc6ICdmaXhlZCd9ICk7XHJcblxyXG5cdF93cGJjLmNhbGVuZGFyX19zZXRfcGFyYW1ldGVycyggcmVzb3VyY2VfaWQsIHsnZml4ZWRfX2RheXNfbnVtJzogcGFyc2VJbnQoIGRheXNfbnVtYmVyICl9ICk7XHRcdFx0Ly8gTnVtYmVyIG9mIGRheXMgc2VsZWN0aW9uIHdpdGggMSBtb3VzZSBjbGlja1xyXG5cdF93cGJjLmNhbGVuZGFyX19zZXRfcGFyYW1ldGVycyggcmVzb3VyY2VfaWQsIHsnZml4ZWRfX3dlZWtfZGF5c19fc3RhcnQnOiB3ZWVrX2RheXNfX3N0YXJ0fSApOyBcdC8vIHsgLTEgLSBBbnkgfCAwIC0gU3UsICAxIC0gTW8sICAyIC0gVHUsIDMgLSBXZSwgNCAtIFRoLCA1IC0gRnIsIDYgLSBTYXQgfVxyXG5cclxuXHR3cGJjX2NhbF9kYXlzX3NlbGVjdF9fcmVfaW5pdCggcmVzb3VyY2VfaWQgKTtcclxuXHR3cGJjX2NhbF9fcmVfaW5pdCggcmVzb3VyY2VfaWQgKTtcclxufVxyXG5cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG4vKipcclxuICogU2V0IFJhbmdlIERheXMgc2VsZWN0aW9uICB3aXRoICAyIG1vdXNlIGNsaWNrcyAgLSBhZnRlciBwYWdlIGxvYWRcclxuICpcclxuICogQGludGVnZXIgcmVzb3VyY2VfaWRcdFx0XHQtIDFcdFx0XHRcdCAgIFx0XHQtLSBJRCBvZiBib29raW5nIHJlc291cmNlIChjYWxlbmRhcilcclxuICogQGludGVnZXIgZGF5c19taW5cdFx0XHQtIDdcdFx0XHRcdCAgIFx0XHQtLSBNaW4gbnVtYmVyIG9mIGRheXMgdG8gc2VsZWN0XHJcbiAqIEBpbnRlZ2VyIGRheXNfbWF4XHRcdFx0LSAzMFx0XHRcdCAgIFx0XHQtLSBNYXggbnVtYmVyIG9mIGRheXMgdG8gc2VsZWN0XHJcbiAqIEBhcnJheSBkYXlzX3NwZWNpZmljXHRcdFx0LSBbXSB8IFs3LDE0LDIxLDI4XVx0XHQtLSBSZXN0cmljdGlvbiBmb3IgU3BlY2lmaWMgbnVtYmVyIG9mIGRheXMgc2VsZWN0aW9uXHJcbiAqIEBhcnJheSB3ZWVrX2RheXNfX3N0YXJ0XHRcdC0gWy0xXSB8IFsgMSwgNV0gICBcdFx0LS0gIHsgLTEgLSBBbnkgfCAwIC0gU3UsICAxIC0gTW8sICAyIC0gVHUsIDMgLSBXZSwgNCAtIFRoLCA1IC0gRnIsIDYgLSBTYXQgfVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19jYWxfcmVhZHlfZGF5c19zZWxlY3RfX3JhbmdlKCByZXNvdXJjZV9pZCwgZGF5c19taW4sIGRheXNfbWF4LCBkYXlzX3NwZWNpZmljID0gW10sIHdlZWtfZGF5c19fc3RhcnQgPSBbLTFdICl7XHJcblxyXG5cdC8vIFJlLWRlZmluZSBzZWxlY3Rpb24sIG9ubHkgYWZ0ZXIgcGFnZSBsb2FkZWQgd2l0aCBhbGwgaW5pdCB2YXJzXHJcblx0alF1ZXJ5KGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpe1xyXG5cclxuXHRcdC8vIFdhaXQgMSBzZWNvbmQsIGp1c3QgdG8gIGJlIHN1cmUsIHRoYXQgYWxsIGluaXQgdmFycyBkZWZpbmVkXHJcblx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7XHJcblxyXG5cdFx0XHR3cGJjX2NhbF9kYXlzX3NlbGVjdF9fcmFuZ2UoIHJlc291cmNlX2lkLCBkYXlzX21pbiwgZGF5c19tYXgsIGRheXNfc3BlY2lmaWMsIHdlZWtfZGF5c19fc3RhcnQgKTtcclxuXHRcdH0sIDEwMDApO1xyXG5cdH0pO1xyXG59XHJcblxyXG4vKipcclxuICogU2V0IFJhbmdlIERheXMgc2VsZWN0aW9uICB3aXRoICAyIG1vdXNlIGNsaWNrc1xyXG4gKiBDYW4gYmUgcnVuIGF0IGFueSAgdGltZSwgIHdoZW4gIGNhbGVuZGFyIGRlZmluZWQgLSB1c2VmdWwgZm9yIGNvbnNvbGUgcnVuLlxyXG4gKlxyXG4gKiBAaW50ZWdlciByZXNvdXJjZV9pZFx0XHRcdC0gMVx0XHRcdFx0ICAgXHRcdC0tIElEIG9mIGJvb2tpbmcgcmVzb3VyY2UgKGNhbGVuZGFyKVxyXG4gKiBAaW50ZWdlciBkYXlzX21pblx0XHRcdC0gN1x0XHRcdFx0ICAgXHRcdC0tIE1pbiBudW1iZXIgb2YgZGF5cyB0byBzZWxlY3RcclxuICogQGludGVnZXIgZGF5c19tYXhcdFx0XHQtIDMwXHRcdFx0ICAgXHRcdC0tIE1heCBudW1iZXIgb2YgZGF5cyB0byBzZWxlY3RcclxuICogQGFycmF5IGRheXNfc3BlY2lmaWNcdFx0XHQtIFtdIHwgWzcsMTQsMjEsMjhdXHRcdC0tIFJlc3RyaWN0aW9uIGZvciBTcGVjaWZpYyBudW1iZXIgb2YgZGF5cyBzZWxlY3Rpb25cclxuICogQGFycmF5IHdlZWtfZGF5c19fc3RhcnRcdFx0LSBbLTFdIHwgWyAxLCA1XSAgIFx0XHQtLSAgeyAtMSAtIEFueSB8IDAgLSBTdSwgIDEgLSBNbywgIDIgLSBUdSwgMyAtIFdlLCA0IC0gVGgsIDUgLSBGciwgNiAtIFNhdCB9XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2NhbF9kYXlzX3NlbGVjdF9fcmFuZ2UoIHJlc291cmNlX2lkLCBkYXlzX21pbiwgZGF5c19tYXgsIGRheXNfc3BlY2lmaWMgPSBbXSwgd2Vla19kYXlzX19zdGFydCA9IFstMV0gKXtcclxuXHJcblx0X3dwYmMuY2FsZW5kYXJfX3NldF9wYXJhbWV0ZXJzKCAgcmVzb3VyY2VfaWQsIHsnZGF5c19zZWxlY3RfbW9kZSc6ICdkeW5hbWljJ30gICk7XHJcblx0X3dwYmMuY2FsZW5kYXJfX3NldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdkeW5hbWljX19kYXlzX21pbicgICAgICAgICAsIHBhcnNlSW50KCBkYXlzX21pbiApICApOyAgICAgICAgICAgXHRcdC8vIE1pbi4gTnVtYmVyIG9mIGRheXMgc2VsZWN0aW9uIHdpdGggMiBtb3VzZSBjbGlja3NcclxuXHRfd3BiYy5jYWxlbmRhcl9fc2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2R5bmFtaWNfX2RheXNfbWF4JyAgICAgICAgICwgcGFyc2VJbnQoIGRheXNfbWF4ICkgICk7ICAgICAgICAgIFx0XHQvLyBNYXguIE51bWJlciBvZiBkYXlzIHNlbGVjdGlvbiB3aXRoIDIgbW91c2UgY2xpY2tzXHJcblx0X3dwYmMuY2FsZW5kYXJfX3NldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdkeW5hbWljX19kYXlzX3NwZWNpZmljJyAgICAsIGRheXNfc3BlY2lmaWMgICk7XHQgICAgICBcdFx0XHRcdC8vIEV4YW1wbGUgWzUsN11cclxuXHRfd3BiYy5jYWxlbmRhcl9fc2V0X3BhcmFtX3ZhbHVlKCByZXNvdXJjZV9pZCwgJ2R5bmFtaWNfX3dlZWtfZGF5c19fc3RhcnQnICwgd2Vla19kYXlzX19zdGFydCAgKTsgIFx0XHRcdFx0XHQvLyB7IC0xIC0gQW55IHwgMCAtIFN1LCAgMSAtIE1vLCAgMiAtIFR1LCAzIC0gV2UsIDQgLSBUaCwgNSAtIEZyLCA2IC0gU2F0IH1cclxuXHJcblx0d3BiY19jYWxfZGF5c19zZWxlY3RfX3JlX2luaXQoIHJlc291cmNlX2lkICk7XHJcblx0d3BiY19jYWxfX3JlX2luaXQoIHJlc291cmNlX2lkICk7XHJcbn1cclxuIiwiLyoqXHJcbiAqID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbiAqXHRpbmNsdWRlcy9fX2pzL2NhbF9hanhfbG9hZC93cGJjX2NhbF9hanguanNcclxuICogPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuICovXHJcblxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gIEEgaiBhIHggICAgTCBvIGEgZCAgICBDIGEgbCBlIG4gZCBhIHIgICAgRCBhIHQgYVxyXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcbmZ1bmN0aW9uIHdwYmNfY2FsZW5kYXJfX2xvYWRfZGF0YV9fYWp4KCBwYXJhbXMgKXtcclxuXHJcblx0Ly8gRml4SW46IDkuOC42LjIuXHJcblx0d3BiY19jYWxlbmRhcl9fbG9hZGluZ19fc3RhcnQoIHBhcmFtc1sncmVzb3VyY2VfaWQnXSApO1xyXG5cclxuXHQvLyBUcmlnZ2VyIGV2ZW50IGZvciBjYWxlbmRhciBiZWZvcmUgbG9hZGluZyBCb29raW5nIGRhdGEsICBidXQgYWZ0ZXIgc2hvd2luZyBDYWxlbmRhci5cclxuXHRpZiAoIGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIHBhcmFtc1sncmVzb3VyY2VfaWQnXSApLmxlbmd0aCA+IDAgKXtcclxuXHRcdHZhciB0YXJnZXRfZWxtID0galF1ZXJ5KCAnYm9keScgKS50cmlnZ2VyKCBcIndwYmNfY2FsZW5kYXJfYWp4X19iZWZvcmVfbG9hZGVkX2RhdGFcIiwgW3BhcmFtc1sncmVzb3VyY2VfaWQnXV0gKTtcclxuXHRcdCAvL2pRdWVyeSggJ2JvZHknICkub24oICd3cGJjX2NhbGVuZGFyX2FqeF9fYmVmb3JlX2xvYWRlZF9kYXRhJywgZnVuY3Rpb24oIGV2ZW50LCByZXNvdXJjZV9pZCApIHsgLi4uIH0gKTtcclxuXHR9XHJcblxyXG5cdGlmICggd3BiY19iYWxhbmNlcl9faXNfd2FpdCggcGFyYW1zICwgJ3dwYmNfY2FsZW5kYXJfX2xvYWRfZGF0YV9fYWp4JyApICl7XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fVxyXG5cclxuXHQvLyBGaXhJbjogOS44LjYuMi5cclxuXHR3cGJjX2NhbGVuZGFyX19ibHVyX19zdG9wKCBwYXJhbXNbJ3Jlc291cmNlX2lkJ10gKTtcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyA9PSBHZXQgc3RhcnQgLyBlbmQgZGF0ZXMgZnJvbSAgdGhlIEJvb2tpbmcgQ2FsZW5kYXIgc2hvcnRjb2RlLiA9PVxyXG5cdC8vIEV4YW1wbGU6IFtib29raW5nIGNhbGVuZGFyX2RhdGVzX3N0YXJ0PScyMDI2LTAxLTAxJyBjYWxlbmRhcl9kYXRlc19lbmQ9JzIwMjYtMTItMzEnICByZXNvdXJjZV9pZD0xXSAgICAgICAgICAgICAgLy8gRml4SW46IDEwLjEzLjEuNC5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdGlmICggZmFsc2UgIT09IHdwYmNfY2FsZW5kYXJfX2dldF9kYXRlc19zdGFydCggcGFyYW1zWydyZXNvdXJjZV9pZCddICkgKSB7XHJcblx0XHRpZiAoICEgcGFyYW1zWydkYXRlc190b19jaGVjayddICkgeyBwYXJhbXNbJ2RhdGVzX3RvX2NoZWNrJ10gPSBbXTsgfVxyXG5cdFx0dmFyIGRhdGVzX3N0YXJ0ID0gd3BiY19jYWxlbmRhcl9fZ2V0X2RhdGVzX3N0YXJ0KCBwYXJhbXNbJ3Jlc291cmNlX2lkJ10gKTsgIC8vIEUuZy4gLSBsb2NhbF9fbWluX2RhdGUgPSBuZXcgRGF0ZSggMjAyNSwgMCwgMSApO1xyXG5cdFx0aWYgKCBmYWxzZSAhPT0gZGF0ZXNfc3RhcnQgKXtcclxuXHRcdFx0cGFyYW1zWydkYXRlc190b19jaGVjayddWzBdID0gd3BiY19fZ2V0X19zcWxfY2xhc3NfZGF0ZSggZGF0ZXNfc3RhcnQgKTtcclxuXHRcdH1cclxuXHR9XHJcblx0aWYgKCBmYWxzZSAhPT0gd3BiY19jYWxlbmRhcl9fZ2V0X2RhdGVzX2VuZCggcGFyYW1zWydyZXNvdXJjZV9pZCddICkgKSB7XHJcblx0XHRpZiAoICFwYXJhbXNbJ2RhdGVzX3RvX2NoZWNrJ10gKSB7IHBhcmFtc1snZGF0ZXNfdG9fY2hlY2snXSA9IFtdOyB9XHJcblx0XHR2YXIgZGF0ZXNfZW5kID0gd3BiY19jYWxlbmRhcl9fZ2V0X2RhdGVzX2VuZCggcGFyYW1zWydyZXNvdXJjZV9pZCddICk7ICAvLyBFLmcuIC0gbG9jYWxfX21pbl9kYXRlID0gbmV3IERhdGUoIDIwMjUsIDAsIDEgKTtcclxuXHRcdGlmICggZmFsc2UgIT09IGRhdGVzX2VuZCApIHtcclxuXHRcdFx0cGFyYW1zWydkYXRlc190b19jaGVjayddWzFdID0gd3BiY19fZ2V0X19zcWxfY2xhc3NfZGF0ZSggZGF0ZXNfZW5kICk7XHJcblx0XHRcdGlmICggIXBhcmFtc1snZGF0ZXNfdG9fY2hlY2snXVswXSApIHtcclxuXHRcdFx0XHRwYXJhbXNbJ2RhdGVzX3RvX2NoZWNrJ11bMF0gPSB3cGJjX19nZXRfX3NxbF9jbGFzc19kYXRlKCBuZXcgRGF0ZSgpICk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHR9XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcbi8vIGNvbnNvbGUuZ3JvdXBFbmQoKTsgY29uc29sZS50aW1lKCdyZXNvdXJjZV9pZF8nICsgcGFyYW1zWydyZXNvdXJjZV9pZCddKTtcclxuY29uc29sZS5ncm91cENvbGxhcHNlZCggJ1dQQkNfQUpYX0NBTEVOREFSX0xPQUQnICk7IGNvbnNvbGUubG9nKCAnID09IEJlZm9yZSBBamF4IFNlbmQgLSBjYWxlbmRhcnNfYWxsX19nZXQoKSA9PSAnICwgX3dwYmMuY2FsZW5kYXJzX2FsbF9fZ2V0KCkgKTtcclxuXHRpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiAod3BiY19ob29rX19pbml0X3RpbWVzZWxlY3RvcikgKSB7XHJcblx0XHR3cGJjX2hvb2tfX2luaXRfdGltZXNlbGVjdG9yKCk7XHJcblx0fVxyXG5cclxuXHQvLyBTdGFydCBBamF4XHJcblx0alF1ZXJ5LnBvc3QoIHdwYmNfdXJsX2FqYXgsXHJcblx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0YWN0aW9uICAgICAgICAgIDogJ1dQQkNfQUpYX0NBTEVOREFSX0xPQUQnLFxyXG5cdFx0XHRcdFx0d3BiY19hanhfdXNlcl9pZDogX3dwYmMuZ2V0X3NlY3VyZV9wYXJhbSggJ3VzZXJfaWQnICksXHJcblx0XHRcdFx0XHRub25jZSAgICAgICAgICAgOiBfd3BiYy5nZXRfc2VjdXJlX3BhcmFtKCAnbm9uY2UnICksXHJcblx0XHRcdFx0XHR3cGJjX2FqeF9sb2NhbGUgOiBfd3BiYy5nZXRfc2VjdXJlX3BhcmFtKCAnbG9jYWxlJyApLFxyXG5cclxuXHRcdFx0XHRcdGNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zIDogcGFyYW1zIFx0XHRcdFx0XHRcdC8vIFVzdWFsbHkgbGlrZTogeyAncmVzb3VyY2VfaWQnOiAxLCAnbWF4X2RheXNfY291bnQnOiAzNjUgfVxyXG5cdFx0XHRcdH0sXHJcblxyXG5cdFx0XHRcdC8qKlxyXG5cdFx0XHRcdCAqIFMgdSBjIGMgZSBzIHNcclxuXHRcdFx0XHQgKlxyXG5cdFx0XHRcdCAqIEBwYXJhbSByZXNwb25zZV9kYXRhXHRcdC1cdGl0cyBvYmplY3QgcmV0dXJuZWQgZnJvbSAgQWpheCAtIGNsYXNzLWxpdmUtc2VhcmNoLnBocFxyXG5cdFx0XHRcdCAqIEBwYXJhbSB0ZXh0U3RhdHVzXHRcdC1cdCdzdWNjZXNzJ1xyXG5cdFx0XHRcdCAqIEBwYXJhbSBqcVhIUlx0XHRcdFx0LVx0T2JqZWN0XHJcblx0XHRcdFx0ICovXHJcblx0XHRcdFx0ZnVuY3Rpb24gKCByZXNwb25zZV9kYXRhLCB0ZXh0U3RhdHVzLCBqcVhIUiApIHtcclxuLy8gY29uc29sZS50aW1lRW5kKCdyZXNvdXJjZV9pZF8nICsgcmVzcG9uc2VfZGF0YVsncmVzb3VyY2VfaWQnXSk7XHJcbmNvbnNvbGUubG9nKCAnID09IFJlc3BvbnNlIFdQQkNfQUpYX0NBTEVOREFSX0xPQUQgPT0gJywgcmVzcG9uc2VfZGF0YSApOyBjb25zb2xlLmdyb3VwRW5kKCk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gRml4SW46IDkuOC42LjIuXHJcblx0XHRcdFx0XHR2YXIgYWp4X3Bvc3RfZGF0YV9fcmVzb3VyY2VfaWQgPSB3cGJjX2dldF9yZXNvdXJjZV9pZF9fZnJvbV9hanhfcG9zdF9kYXRhX3VybCggdGhpcy5kYXRhICk7XHJcblx0XHRcdFx0XHR3cGJjX2JhbGFuY2VyX19jb21wbGV0ZWQoIGFqeF9wb3N0X2RhdGFfX3Jlc291cmNlX2lkICwgJ3dwYmNfY2FsZW5kYXJfX2xvYWRfZGF0YV9fYWp4JyApO1xyXG5cclxuXHRcdFx0XHRcdC8vIFByb2JhYmx5IEVycm9yXHJcblx0XHRcdFx0XHRpZiAoICh0eXBlb2YgcmVzcG9uc2VfZGF0YSAhPT0gJ29iamVjdCcpIHx8IChyZXNwb25zZV9kYXRhID09PSBudWxsKSApe1xyXG5cclxuXHRcdFx0XHRcdFx0dmFyIGpxX25vZGUgID0gd3BiY19nZXRfY2FsZW5kYXJfX2pxX25vZGVfX2Zvcl9tZXNzYWdlcyggdGhpcy5kYXRhICk7XHJcblx0XHRcdFx0XHRcdHZhciBtZXNzYWdlX3R5cGUgPSAnaW5mbyc7XHJcblxyXG5cdFx0XHRcdFx0XHRpZiAoICcnID09PSByZXNwb25zZV9kYXRhICl7XHJcblx0XHRcdFx0XHRcdFx0cmVzcG9uc2VfZGF0YSA9ICdUaGUgc2VydmVyIHJlc3BvbmRzIHdpdGggYW4gZW1wdHkgc3RyaW5nLiBUaGUgc2VydmVyIHByb2JhYmx5IHN0b3BwZWQgd29ya2luZyB1bmV4cGVjdGVkbHkuIDxicj5QbGVhc2UgY2hlY2sgeW91ciA8c3Ryb25nPmVycm9yLmxvZzwvc3Ryb25nPiBpbiB5b3VyIHNlcnZlciBjb25maWd1cmF0aW9uIGZvciByZWxhdGl2ZSBlcnJvcnMuJztcclxuXHRcdFx0XHRcdFx0XHRtZXNzYWdlX3R5cGUgPSAnd2FybmluZyc7XHJcblx0XHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRcdC8vIFNob3cgTWVzc2FnZVxyXG5cdFx0XHRcdFx0XHR3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKCByZXNwb25zZV9kYXRhICwgeyAndHlwZScgICAgIDogbWVzc2FnZV90eXBlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc2hvd19oZXJlJzogeydqcV9ub2RlJzoganFfbm9kZSwgJ3doZXJlJzogJ2FmdGVyJ30sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdpc19hcHBlbmQnOiB0cnVlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc3R5bGUnICAgIDogJ3RleHQtYWxpZ246bGVmdDsnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly8gU2hvdyBDYWxlbmRhclxyXG5cdFx0XHRcdFx0d3BiY19jYWxlbmRhcl9fbG9hZGluZ19fc3RvcCggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRcdFx0Ly8gQm9va2luZ3MgLSBEYXRlc1xyXG5cdFx0XHRcdFx0X3dwYmMuYm9va2luZ3NfaW5fY2FsZW5kYXJfX3NldF9kYXRlcyggIHJlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXSwgcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWydkYXRlcyddICApO1xyXG5cclxuXHRcdFx0XHRcdC8vIEJvb2tpbmdzIC0gQ2hpbGQgb3Igb25seSBzaW5nbGUgYm9va2luZyByZXNvdXJjZSBpbiBkYXRlc1xyXG5cdFx0XHRcdFx0X3dwYmMuYm9va2luZ19fc2V0X3BhcmFtX3ZhbHVlKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0sICdyZXNvdXJjZXNfaWRfYXJyX19pbl9kYXRlcycsIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ3Jlc291cmNlc19pZF9hcnJfX2luX2RhdGVzJyBdICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gQWdncmVnYXRlIGJvb2tpbmcgcmVzb3VyY2VzLCAgaWYgYW55ID9cclxuXHRcdFx0XHRcdF93cGJjLmJvb2tpbmdfX3NldF9wYXJhbV92YWx1ZSggcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdLCAnYWdncmVnYXRlX3Jlc291cmNlX2lkX2FycicsIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FnZ3JlZ2F0ZV9yZXNvdXJjZV9pZF9hcnInIF0gKTtcclxuXHRcdFx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHRcdFx0XHQvLyBVcGRhdGUgY2FsZW5kYXJcclxuXHRcdFx0XHRcdHdwYmNfY2FsZW5kYXJfX3VwZGF0ZV9sb29rKCByZXNwb25zZV9kYXRhWyAncmVzb3VyY2VfaWQnIF0gKTtcclxuXHJcblx0XHRcdFx0XHRpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiAod3BiY19ob29rX19pbml0X3RpbWVzZWxlY3RvcikgKSB7XHJcblx0XHRcdFx0XHRcdHdwYmNfaG9va19faW5pdF90aW1lc2VsZWN0b3IoKTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRpZiAoXHJcblx0XHRcdFx0XHRcdFx0KCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mIChyZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0pIClcclxuXHRcdFx0XHRcdFx0ICYmICggJycgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlJyBdLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApIClcclxuXHRcdFx0XHRcdCl7XHJcblxyXG5cdFx0XHRcdFx0XHR2YXIganFfbm9kZSAgPSB3cGJjX2dldF9jYWxlbmRhcl9fanFfbm9kZV9fZm9yX21lc3NhZ2VzKCB0aGlzLmRhdGEgKTtcclxuXHJcblx0XHRcdFx0XHRcdC8vIFNob3cgTWVzc2FnZVxyXG5cdFx0XHRcdFx0XHR3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKCByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX21lc3NhZ2UnIF0ucmVwbGFjZSggL1xcbi9nLCBcIjxiciAvPlwiICksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR7ICAgJ3R5cGUnICAgICA6ICggJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiggcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlX3N0YXR1cycgXSApIClcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICA/IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9zdGF0dXMnIF0gOiAnaW5mbycsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnOiB7J2pxX25vZGUnOiBqcV9ub2RlLCAnd2hlcmUnOiAnYWZ0ZXInfSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2lzX2FwcGVuZCc6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzdHlsZScgICAgOiAndGV4dC1hbGlnbjpsZWZ0OycsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWxheScgICAgOiAxMDAwMFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdGlmICggJ2Z1bmN0aW9uJyA9PT0gdHlwZW9mICh3cGJjX3VwZGF0ZV9jYXBhY2l0eV9oaW50KSApIHtcclxuXHRcdFx0XHRcdFx0d3BiY191cGRhdGVfY2FwYWNpdHlfaGludCggcmVzcG9uc2VfZGF0YVsncmVzb3VyY2VfaWQnXSApO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIFRyaWdnZXIgZXZlbnQgdGhhdCBjYWxlbmRhciBoYXMgYmVlblx0XHQgLy8gRml4SW46IDEwLjAuMC40NC5cclxuXHRcdFx0XHRcdGlmICggalF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgcmVzcG9uc2VfZGF0YVsgJ3Jlc291cmNlX2lkJyBdICkubGVuZ3RoID4gMCApe1xyXG5cdFx0XHRcdFx0XHR2YXIgdGFyZ2V0X2VsbSA9IGpRdWVyeSggJ2JvZHknICkudHJpZ2dlciggXCJ3cGJjX2NhbGVuZGFyX2FqeF9fbG9hZGVkX2RhdGFcIiwgW3Jlc3BvbnNlX2RhdGFbICdyZXNvdXJjZV9pZCcgXV0gKTtcclxuXHRcdFx0XHRcdFx0IC8valF1ZXJ5KCAnYm9keScgKS5vbiggJ3dwYmNfY2FsZW5kYXJfYWp4X19sb2FkZWRfZGF0YScsIGZ1bmN0aW9uKCBldmVudCwgcmVzb3VyY2VfaWQgKSB7IC4uLiB9ICk7XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly9qUXVlcnkoICcjYWpheF9yZXNwb25kJyApLmh0bWwoIHJlc3BvbnNlX2RhdGEgKTtcdFx0Ly8gRm9yIGFiaWxpdHkgdG8gc2hvdyByZXNwb25zZSwgYWRkIHN1Y2ggRElWIGVsZW1lbnQgdG8gcGFnZVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0ICApLmZhaWwoIGZ1bmN0aW9uICgganFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duICkgeyAgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ0FqYXhfRXJyb3InLCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKTsgfVxyXG5cclxuXHRcdFx0XHRcdHZhciBhanhfcG9zdF9kYXRhX19yZXNvdXJjZV9pZCA9IHdwYmNfZ2V0X3Jlc291cmNlX2lkX19mcm9tX2FqeF9wb3N0X2RhdGFfdXJsKCB0aGlzLmRhdGEgKTtcclxuXHRcdFx0XHRcdHdwYmNfYmFsYW5jZXJfX2NvbXBsZXRlZCggYWp4X3Bvc3RfZGF0YV9fcmVzb3VyY2VfaWQgLCAnd3BiY19jYWxlbmRhcl9fbG9hZF9kYXRhX19hangnICk7XHJcblxyXG5cdFx0XHRcdFx0Ly8gR2V0IENvbnRlbnQgb2YgRXJyb3IgTWVzc2FnZVxyXG5cdFx0XHRcdFx0dmFyIGVycm9yX21lc3NhZ2UgPSAnPHN0cm9uZz4nICsgJ0Vycm9yIScgKyAnPC9zdHJvbmc+ICcgKyBlcnJvclRocm93biA7XHJcblx0XHRcdFx0XHRpZiAoIGpxWEhSLnN0YXR1cyApe1xyXG5cdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICcgKDxiPicgKyBqcVhIUi5zdGF0dXMgKyAnPC9iPiknO1xyXG5cdFx0XHRcdFx0XHRpZiAoNDAzID09IGpxWEhSLnN0YXR1cyApe1xyXG5cdFx0XHRcdFx0XHRcdGVycm9yX21lc3NhZ2UgKz0gJzxicj4gUHJvYmFibHkgbm9uY2UgZm9yIHRoaXMgcGFnZSBoYXMgYmVlbiBleHBpcmVkLiBQbGVhc2UgPGEgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiIG9uY2xpY2s9XCJqYXZhc2NyaXB0OmxvY2F0aW9uLnJlbG9hZCgpO1wiPnJlbG9hZCB0aGUgcGFnZTwvYT4uJztcclxuXHRcdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICc8YnI+IE90aGVyd2lzZSwgcGxlYXNlIGNoZWNrIHRoaXMgPGEgc3R5bGU9XCJmb250LXdlaWdodDogNjAwO1wiIGhyZWY9XCJodHRwczovL3dwYm9va2luZ2NhbGVuZGFyLmNvbS9mYXEvcmVxdWVzdC1kby1ub3QtcGFzcy1zZWN1cml0eS1jaGVjay8/YWZ0ZXJfdXBkYXRlPTEwLjEuMVwiPnRyb3VibGVzaG9vdGluZyBpbnN0cnVjdGlvbjwvYT4uPGJyPidcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0dmFyIG1lc3NhZ2Vfc2hvd19kZWxheSA9IDMwMDA7XHJcblx0XHRcdFx0XHRpZiAoIGpxWEhSLnJlc3BvbnNlVGV4dCApe1xyXG5cdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICcgJyArIGpxWEhSLnJlc3BvbnNlVGV4dDtcclxuXHRcdFx0XHRcdFx0bWVzc2FnZV9zaG93X2RlbGF5ID0gMTA7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlID0gZXJyb3JfbWVzc2FnZS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKTtcclxuXHJcblx0XHRcdFx0XHR2YXIganFfbm9kZSAgPSB3cGJjX2dldF9jYWxlbmRhcl9fanFfbm9kZV9fZm9yX21lc3NhZ2VzKCB0aGlzLmRhdGEgKTtcclxuXHJcblx0XHRcdFx0XHQvKipcclxuXHRcdFx0XHRcdCAqIElmIHdlIG1ha2UgZmFzdCBjbGlja2luZyBvbiBkaWZmZXJlbnQgcGFnZXMsXHJcblx0XHRcdFx0XHQgKiB0aGVuIHVuZGVyIGNhbGVuZGFyIHdpbGwgc2hvdyBlcnJvciBtZXNzYWdlIHdpdGggIGVtcHR5ICB0ZXh0LCBiZWNhdXNlIGFqYXggd2FzIG5vdCByZWNlaXZlZC5cclxuXHRcdFx0XHRcdCAqIFRvICBub3Qgc2hvdyBzdWNoIHdhcm5pbmdzIHdlIGFyZSBzZXQgZGVsYXkgIGluIDMgc2Vjb25kcy4gIHZhciBtZXNzYWdlX3Nob3dfZGVsYXkgPSAzMDAwO1xyXG5cdFx0XHRcdFx0ICovXHJcblx0XHRcdFx0XHR2YXIgY2xvc2VkX3RpbWVyID0gc2V0VGltZW91dCggZnVuY3Rpb24gKCl7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIFNob3cgTWVzc2FnZVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoIGVycm9yX21lc3NhZ2UgLCB7ICd0eXBlJyAgICAgOiAnZXJyb3InLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnOiB7J2pxX25vZGUnOiBqcV9ub2RlLCAnd2hlcmUnOiAnYWZ0ZXInfSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnaXNfYXBwZW5kJzogdHJ1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc3R5bGUnICAgIDogJ3RleHQtYWxpZ246bGVmdDsnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjc3NfY2xhc3MnOid3cGJjX2ZlX21lc3NhZ2VfYWx0JyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgICB9ICxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgIHBhcnNlSW50KCBtZXNzYWdlX3Nob3dfZGVsYXkgKSAgICk7XHJcblxyXG5cdFx0XHQgIH0pXHJcblx0ICAgICAgICAgIC8vIC5kb25lKCAgIGZ1bmN0aW9uICggZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7ICAgaWYgKCB3aW5kb3cuY29uc29sZSAmJiB3aW5kb3cuY29uc29sZS5sb2cgKXsgY29uc29sZS5sb2coICdzZWNvbmQgc3VjY2VzcycsIGRhdGEsIHRleHRTdGF0dXMsIGpxWEhSICk7IH0gICAgfSlcclxuXHRcdFx0ICAvLyAuYWx3YXlzKCBmdW5jdGlvbiAoIGRhdGFfanFYSFIsIHRleHRTdGF0dXMsIGpxWEhSX2Vycm9yVGhyb3duICkgeyAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnYWx3YXlzIGZpbmlzaGVkJywgZGF0YV9qcVhIUiwgdGV4dFN0YXR1cywganFYSFJfZXJyb3JUaHJvd24gKTsgfSAgICAgfSlcclxuXHRcdFx0ICA7ICAvLyBFbmQgQWpheFxyXG59XHJcblxyXG5cclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4vLyBTdXBwb3J0XHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHQvKipcclxuXHQgKiBHZXQgQ2FsZW5kYXIgalF1ZXJ5IG5vZGUgZm9yIHNob3dpbmcgbWVzc2FnZXMgZHVyaW5nIEFqYXhcclxuXHQgKiBUaGlzIHBhcmFtZXRlcjogICBjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtc1tyZXNvdXJjZV9pZF0gICBwYXJzZWQgZnJvbSB0aGlzLmRhdGEgQWpheCBwb3N0ICBkYXRhXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gYWp4X3Bvc3RfZGF0YV91cmxfcGFyYW1zXHRcdCAnYWN0aW9uPVdQQkNfQUpYX0NBTEVOREFSX0xPQUQuLi4mY2FsZW5kYXJfcmVxdWVzdF9wYXJhbXMlNUJyZXNvdXJjZV9pZCU1RD0yJmNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zJTVCYm9va2luZ19oYXNoJTVEPSZjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtcydcclxuXHQgKiBAcmV0dXJucyB7c3RyaW5nfVx0JycjY2FsZW5kYXJfYm9va2luZzEnICB8ICAgJy5ib29raW5nX2Zvcm1fZGl2JyAuLi5cclxuXHQgKlxyXG5cdCAqIEV4YW1wbGUgICAgdmFyIGpxX25vZGUgID0gd3BiY19nZXRfY2FsZW5kYXJfX2pxX25vZGVfX2Zvcl9tZXNzYWdlcyggdGhpcy5kYXRhICk7XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19nZXRfY2FsZW5kYXJfX2pxX25vZGVfX2Zvcl9tZXNzYWdlcyggYWp4X3Bvc3RfZGF0YV91cmxfcGFyYW1zICl7XHJcblxyXG5cdFx0dmFyIGpxX25vZGUgPSAnLmJvb2tpbmdfZm9ybV9kaXYnO1xyXG5cclxuXHRcdHZhciBjYWxlbmRhcl9yZXNvdXJjZV9pZCA9IHdwYmNfZ2V0X3Jlc291cmNlX2lkX19mcm9tX2FqeF9wb3N0X2RhdGFfdXJsKCBhanhfcG9zdF9kYXRhX3VybF9wYXJhbXMgKTtcclxuXHJcblx0XHRpZiAoIGNhbGVuZGFyX3Jlc291cmNlX2lkID4gMCApe1xyXG5cdFx0XHRqcV9ub2RlID0gJyNjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3Jlc291cmNlX2lkO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBqcV9ub2RlO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIEdldCByZXNvdXJjZSBJRCBmcm9tIGFqeCBwb3N0IGRhdGEgdXJsICAgdXN1YWxseSAgZnJvbSAgdGhpcy5kYXRhICA9ICdhY3Rpb249V1BCQ19BSlhfQ0FMRU5EQVJfTE9BRC4uLiZjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtcyU1QnJlc291cmNlX2lkJTVEPTImY2FsZW5kYXJfcmVxdWVzdF9wYXJhbXMlNUJib29raW5nX2hhc2glNUQ9JmNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zJ1xyXG5cdCAqXHJcblx0ICogQHBhcmFtIGFqeF9wb3N0X2RhdGFfdXJsX3BhcmFtc1x0XHQgJ2FjdGlvbj1XUEJDX0FKWF9DQUxFTkRBUl9MT0FELi4uJmNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zJTVCcmVzb3VyY2VfaWQlNUQ9MiZjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtcyU1QmJvb2tpbmdfaGFzaCU1RD0mY2FsZW5kYXJfcmVxdWVzdF9wYXJhbXMnXHJcblx0ICogQHJldHVybnMge2ludH1cdFx0XHRcdFx0XHQgMSB8IDAgIChpZiBlcnJyb3IgdGhlbiAgMClcclxuXHQgKlxyXG5cdCAqIEV4YW1wbGUgICAgdmFyIGpxX25vZGUgID0gd3BiY19nZXRfY2FsZW5kYXJfX2pxX25vZGVfX2Zvcl9tZXNzYWdlcyggdGhpcy5kYXRhICk7XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19nZXRfcmVzb3VyY2VfaWRfX2Zyb21fYWp4X3Bvc3RfZGF0YV91cmwoIGFqeF9wb3N0X2RhdGFfdXJsX3BhcmFtcyApe1xyXG5cclxuXHRcdC8vIEdldCBib29raW5nIHJlc291cmNlIElEIGZyb20gQWpheCBQb3N0IFJlcXVlc3QgIC0+IHRoaXMuZGF0YSA9ICdhY3Rpb249V1BCQ19BSlhfQ0FMRU5EQVJfTE9BRC4uLiZjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtcyU1QnJlc291cmNlX2lkJTVEPTImY2FsZW5kYXJfcmVxdWVzdF9wYXJhbXMlNUJib29raW5nX2hhc2glNUQ9JmNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zJ1xyXG5cdFx0dmFyIGNhbGVuZGFyX3Jlc291cmNlX2lkID0gd3BiY19nZXRfdXJpX3BhcmFtX2J5X25hbWUoICdjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtc1tyZXNvdXJjZV9pZF0nLCBhanhfcG9zdF9kYXRhX3VybF9wYXJhbXMgKTtcclxuXHRcdGlmICggKG51bGwgIT09IGNhbGVuZGFyX3Jlc291cmNlX2lkKSAmJiAoJycgIT09IGNhbGVuZGFyX3Jlc291cmNlX2lkKSApe1xyXG5cdFx0XHRjYWxlbmRhcl9yZXNvdXJjZV9pZCA9IHBhcnNlSW50KCBjYWxlbmRhcl9yZXNvdXJjZV9pZCApO1xyXG5cdFx0XHRpZiAoIGNhbGVuZGFyX3Jlc291cmNlX2lkID4gMCApe1xyXG5cdFx0XHRcdHJldHVybiBjYWxlbmRhcl9yZXNvdXJjZV9pZDtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdFx0cmV0dXJuIDA7XHJcblx0fVxyXG5cclxuXHJcblx0LyoqXHJcblx0ICogR2V0IHBhcmFtZXRlciBmcm9tIFVSTCAgLSAgcGFyc2UgVVJMIHBhcmFtZXRlcnMsICBsaWtlIHRoaXM6IGFjdGlvbj1XUEJDX0FKWF9DQUxFTkRBUl9MT0FELi4uJmNhbGVuZGFyX3JlcXVlc3RfcGFyYW1zJTVCcmVzb3VyY2VfaWQlNUQ9MiZjYWxlbmRhcl9yZXF1ZXN0X3BhcmFtcyU1QmJvb2tpbmdfaGFzaCU1RD0mY2FsZW5kYXJfcmVxdWVzdF9wYXJhbXNcclxuXHQgKiBAcGFyYW0gbmFtZSAgcGFyYW1ldGVyICBuYW1lLCAgbGlrZSAnY2FsZW5kYXJfcmVxdWVzdF9wYXJhbXNbcmVzb3VyY2VfaWRdJ1xyXG5cdCAqIEBwYXJhbSB1cmxcdCdwYXJhbWV0ZXIgIHN0cmluZyBVUkwnXHJcblx0ICogQHJldHVybnMge3N0cmluZ3xudWxsfSAgIHBhcmFtZXRlciB2YWx1ZVxyXG5cdCAqXHJcblx0ICogRXhhbXBsZTogXHRcdHdwYmNfZ2V0X3VyaV9wYXJhbV9ieV9uYW1lKCAnY2FsZW5kYXJfcmVxdWVzdF9wYXJhbXNbcmVzb3VyY2VfaWRdJywgdGhpcy5kYXRhICk7ICAtPiAnMidcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2dldF91cmlfcGFyYW1fYnlfbmFtZSggbmFtZSwgdXJsICl7XHJcblxyXG5cdFx0dXJsID0gZGVjb2RlVVJJQ29tcG9uZW50KCB1cmwgKTtcclxuXHJcblx0XHRuYW1lID0gbmFtZS5yZXBsYWNlKCAvW1xcW1xcXV0vZywgJ1xcXFwkJicgKTtcclxuXHRcdHZhciByZWdleCA9IG5ldyBSZWdFeHAoICdbPyZdJyArIG5hbWUgKyAnKD0oW14mI10qKXwmfCN8JCknICksXHJcblx0XHRcdHJlc3VsdHMgPSByZWdleC5leGVjKCB1cmwgKTtcclxuXHRcdGlmICggIXJlc3VsdHMgKSByZXR1cm4gbnVsbDtcclxuXHRcdGlmICggIXJlc3VsdHNbIDIgXSApIHJldHVybiAnJztcclxuXHRcdHJldHVybiBkZWNvZGVVUklDb21wb25lbnQoIHJlc3VsdHNbIDIgXS5yZXBsYWNlKCAvXFwrL2csICcgJyApICk7XHJcblx0fVxyXG4iLCIvKipcclxuICogPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbiAqXHRpbmNsdWRlcy9fX2pzL2Zyb250X2VuZF9tZXNzYWdlcy93cGJjX2ZlX21lc3NhZ2VzLmpzXHJcbiAqID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG4gKi9cclxuXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4vLyBTaG93IE1lc3NhZ2VzIGF0IEZyb250LUVkbiBzaWRlXHJcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuLyoqXHJcbiAqIFNob3cgbWVzc2FnZSBpbiBjb250ZW50XHJcbiAqXHJcbiAqIEBwYXJhbSBtZXNzYWdlXHRcdFx0XHRNZXNzYWdlIEhUTUxcclxuICogQHBhcmFtIHBhcmFtcyA9IHtcclxuICpcdFx0XHRcdFx0XHRcdFx0J3R5cGUnICAgICA6ICd3YXJuaW5nJyxcdFx0XHRcdFx0XHRcdC8vICdlcnJvcicgfCAnd2FybmluZycgfCAnaW5mbycgfCAnc3VjY2VzcydcclxuICpcdFx0XHRcdFx0XHRcdFx0J3Nob3dfaGVyZScgOiB7XHJcbiAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnanFfbm9kZScgOiAnJyxcdFx0XHRcdC8vIGFueSBqUXVlcnkgbm9kZSBkZWZpbml0aW9uXHJcbiAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnd2hlcmUnICAgOiAnaW5zaWRlJ1x0XHQvLyAnaW5zaWRlJyB8ICdiZWZvcmUnIHwgJ2FmdGVyJyB8ICdyaWdodCcgfCAnbGVmdCdcclxuICpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9LFxyXG4gKlx0XHRcdFx0XHRcdFx0XHQnaXNfYXBwZW5kJzogdHJ1ZSxcdFx0XHRcdFx0XHRcdFx0Ly8gQXBwbHkgIG9ubHkgaWYgXHQnd2hlcmUnICAgOiAnaW5zaWRlJ1xyXG4gKlx0XHRcdFx0XHRcdFx0XHQnc3R5bGUnICAgIDogJ3RleHQtYWxpZ246bGVmdDsnLFx0XHRcdFx0Ly8gc3R5bGVzLCBpZiBuZWVkZWRcclxuICpcdFx0XHRcdFx0XHRcdCAgICAnY3NzX2NsYXNzJzogJycsXHRcdFx0XHRcdFx0XHRcdC8vIEZvciBleGFtcGxlIGNhbiAgYmU6ICd3cGJjX2ZlX21lc3NhZ2VfYWx0J1xyXG4gKlx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMCxcdFx0XHRcdFx0XHRcdFx0XHQvLyBob3cgbWFueSBtaWNyb3NlY29uZCB0byAgc2hvdywgIGlmIDAgIHRoZW4gIHNob3cgZm9yZXZlclxyXG4gKlx0XHRcdFx0XHRcdFx0XHQnaWZfdmlzaWJsZV9ub3Rfc2hvdyc6IGZhbHNlXHRcdFx0XHRcdC8vIGlmIHRydWUsICB0aGVuIGRvIG5vdCBzaG93IG1lc3NhZ2UsICBpZiBwcmV2aW9zIG1lc3NhZ2Ugd2FzIG5vdCBoaWRlZCAobm90IGFwcGx5IGlmICd3aGVyZScgICA6ICdpbnNpZGUnIClcclxuICpcdFx0XHRcdH07XHJcbiAqIEV4YW1wbGVzOlxyXG4gKiBcdFx0XHR2YXIgaHRtbF9pZCA9IHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoICdZb3UgY2FuIHRlc3QgZGF5cyBzZWxlY3Rpb24gaW4gY2FsZW5kYXInLCB7fSApO1xyXG4gKlxyXG4gKlx0XHRcdHZhciBub3RpY2VfbWVzc2FnZV9pZCA9IHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoIF93cGJjLmdldF9tZXNzYWdlKCAnbWVzc2FnZV9jaGVja19yZXF1aXJlZCcgKSwgeyAndHlwZSc6ICd3YXJuaW5nJywgJ2RlbGF5JzogMTAwMDAsICdpZl92aXNpYmxlX25vdF9zaG93JzogdHJ1ZSxcclxuICpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgJ3Nob3dfaGVyZSc6IHsnd2hlcmUnOiAncmlnaHQnLCAnanFfbm9kZSc6IGVsLH0gfSApO1xyXG4gKlxyXG4gKlx0XHRcdHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZScgXS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKSxcclxuICpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0eyAgICd0eXBlJyAgICAgOiAoICd1bmRlZmluZWQnICE9PSB0eXBlb2YoIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9zdGF0dXMnIF0gKSApXHJcbiAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICA/IHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXVsgJ2FqeF9hZnRlcl9hY3Rpb25fbWVzc2FnZV9zdGF0dXMnIF0gOiAnaW5mbycsXHJcbiAqXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3Nob3dfaGVyZSc6IHsnanFfbm9kZSc6IGpxX25vZGUsICd3aGVyZSc6ICdhZnRlcid9LFxyXG4gKlx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjc3NfY2xhc3MnOid3cGJjX2ZlX21lc3NhZ2VfYWx0JyxcclxuICpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgIDogMTAwMDBcclxuICpcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG4gKlxyXG4gKlxyXG4gKiBAcmV0dXJucyBzdHJpbmcgIC0gSFRNTCBJRFx0XHRvciAwIGlmIG5vdCBzaG93aW5nIGR1cmluZyB0aGlzIHRpbWUuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKCBtZXNzYWdlLCBwYXJhbXMgPSB7fSApe1xyXG5cclxuXHR2YXIgcGFyYW1zX2RlZmF1bHQgPSB7XHJcblx0XHRcdFx0XHRcdFx0XHQndHlwZScgICAgIDogJ3dhcm5pbmcnLFx0XHRcdFx0XHRcdFx0Ly8gJ2Vycm9yJyB8ICd3YXJuaW5nJyB8ICdpbmZvJyB8ICdzdWNjZXNzJ1xyXG5cdFx0XHRcdFx0XHRcdFx0J3Nob3dfaGVyZScgOiB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2pxX25vZGUnIDogJycsXHRcdFx0XHQvLyBhbnkgalF1ZXJ5IG5vZGUgZGVmaW5pdGlvblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3aGVyZScgICA6ICdpbnNpZGUnXHRcdC8vICdpbnNpZGUnIHwgJ2JlZm9yZScgfCAnYWZ0ZXInIHwgJ3JpZ2h0JyB8ICdsZWZ0J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9LFxyXG5cdFx0XHRcdFx0XHRcdFx0J2lzX2FwcGVuZCc6IHRydWUsXHRcdFx0XHRcdFx0XHRcdC8vIEFwcGx5ICBvbmx5IGlmIFx0J3doZXJlJyAgIDogJ2luc2lkZSdcclxuXHRcdFx0XHRcdFx0XHRcdCdzdHlsZScgICAgOiAndGV4dC1hbGlnbjpsZWZ0OycsXHRcdFx0XHQvLyBzdHlsZXMsIGlmIG5lZWRlZFxyXG5cdFx0XHRcdFx0XHRcdCAgICAnY3NzX2NsYXNzJzogJycsXHRcdFx0XHRcdFx0XHRcdC8vIEZvciBleGFtcGxlIGNhbiAgYmU6ICd3cGJjX2ZlX21lc3NhZ2VfYWx0J1xyXG5cdFx0XHRcdFx0XHRcdFx0J2RlbGF5JyAgICA6IDAsXHRcdFx0XHRcdFx0XHRcdFx0Ly8gaG93IG1hbnkgbWljcm9zZWNvbmQgdG8gIHNob3csICBpZiAwICB0aGVuICBzaG93IGZvcmV2ZXJcclxuXHRcdFx0XHRcdFx0XHRcdCdpZl92aXNpYmxlX25vdF9zaG93JzogZmFsc2UsXHRcdFx0XHRcdC8vIGlmIHRydWUsICB0aGVuIGRvIG5vdCBzaG93IG1lc3NhZ2UsICBpZiBwcmV2aW9zIG1lc3NhZ2Ugd2FzIG5vdCBoaWRlZCAobm90IGFwcGx5IGlmICd3aGVyZScgICA6ICdpbnNpZGUnIClcclxuXHRcdFx0XHRcdFx0XHRcdCdpc19zY3JvbGwnOiB0cnVlXHRcdFx0XHRcdFx0XHRcdC8vIGlzIHNjcm9sbCAgdG8gIHRoaXMgZWxlbWVudFxyXG5cdFx0XHRcdFx0XHR9O1xyXG5cdGZvciAoIHZhciBwX2tleSBpbiBwYXJhbXMgKXtcclxuXHRcdHBhcmFtc19kZWZhdWx0WyBwX2tleSBdID0gcGFyYW1zWyBwX2tleSBdO1xyXG5cdH1cclxuXHRwYXJhbXMgPSBwYXJhbXNfZGVmYXVsdDtcclxuXHJcbiAgICB2YXIgdW5pcXVlX2Rpdl9pZCA9IG5ldyBEYXRlKCk7XHJcbiAgICB1bmlxdWVfZGl2X2lkID0gJ3dwYmNfbm90aWNlXycgKyB1bmlxdWVfZGl2X2lkLmdldFRpbWUoKTtcclxuXHJcblx0cGFyYW1zWydjc3NfY2xhc3MnXSArPSAnIHdwYmNfZmVfbWVzc2FnZSc7XHJcblx0aWYgKCBwYXJhbXNbJ3R5cGUnXSA9PSAnZXJyb3InICl7XHJcblx0XHRwYXJhbXNbJ2Nzc19jbGFzcyddICs9ICcgd3BiY19mZV9tZXNzYWdlX2Vycm9yJztcclxuXHRcdG1lc3NhZ2UgPSAnPGkgY2xhc3M9XCJtZW51X2ljb24gaWNvbi0xeCB3cGJjX2ljbl9yZXBvcnRfZ21haWxlcnJvcnJlZFwiPjwvaT4nICsgbWVzc2FnZTtcclxuXHR9XHJcblx0aWYgKCBwYXJhbXNbJ3R5cGUnXSA9PSAnd2FybmluZycgKXtcclxuXHRcdHBhcmFtc1snY3NzX2NsYXNzJ10gKz0gJyB3cGJjX2ZlX21lc3NhZ2Vfd2FybmluZyc7XHJcblx0XHRtZXNzYWdlID0gJzxpIGNsYXNzPVwibWVudV9pY29uIGljb24tMXggd3BiY19pY25fd2FybmluZ1wiPjwvaT4nICsgbWVzc2FnZTtcclxuXHR9XHJcblx0aWYgKCBwYXJhbXNbJ3R5cGUnXSA9PSAnaW5mbycgKXtcclxuXHRcdHBhcmFtc1snY3NzX2NsYXNzJ10gKz0gJyB3cGJjX2ZlX21lc3NhZ2VfaW5mbyc7XHJcblx0fVxyXG5cdGlmICggcGFyYW1zWyd0eXBlJ10gPT0gJ3N1Y2Nlc3MnICl7XHJcblx0XHRwYXJhbXNbJ2Nzc19jbGFzcyddICs9ICcgd3BiY19mZV9tZXNzYWdlX3N1Y2Nlc3MnO1xyXG5cdFx0bWVzc2FnZSA9ICc8aSBjbGFzcz1cIm1lbnVfaWNvbiBpY29uLTF4IHdwYmNfaWNuX2RvbmVfb3V0bGluZVwiPjwvaT4nICsgbWVzc2FnZTtcclxuXHR9XHJcblxyXG5cdHZhciBzY3JvbGxfdG9fZWxlbWVudCA9ICc8ZGl2IGlkPVwiJyArIHVuaXF1ZV9kaXZfaWQgKyAnX3Njcm9sbFwiIHN0eWxlPVwiZGlzcGxheTpub25lO1wiPjwvZGl2Pic7XHJcblx0bWVzc2FnZSA9ICc8ZGl2IGlkPVwiJyArIHVuaXF1ZV9kaXZfaWQgKyAnXCIgY2xhc3M9XCJ3cGJjX2Zyb250X2VuZF9fbWVzc2FnZSAnICsgcGFyYW1zWydjc3NfY2xhc3MnXSArICdcIiBzdHlsZT1cIicgKyBwYXJhbXNbICdzdHlsZScgXSArICdcIj4nICsgbWVzc2FnZSArICc8L2Rpdj4nO1xyXG5cclxuXHJcblx0dmFyIGpxX2VsX21lc3NhZ2UgPSBmYWxzZTtcclxuXHR2YXIgaXNfc2hvd19tZXNzYWdlID0gdHJ1ZTtcclxuXHJcblx0aWYgKCAnaW5zaWRlJyA9PT0gcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnd2hlcmUnIF0gKXtcclxuXHJcblx0XHRpZiAoIHBhcmFtc1sgJ2lzX2FwcGVuZCcgXSApe1xyXG5cdFx0XHRqUXVlcnkoIHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ2pxX25vZGUnIF0gKS5hcHBlbmQoIHNjcm9sbF90b19lbGVtZW50ICk7XHJcblx0XHRcdGpRdWVyeSggcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnanFfbm9kZScgXSApLmFwcGVuZCggbWVzc2FnZSApO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0alF1ZXJ5KCBwYXJhbXNbICdzaG93X2hlcmUnIF1bICdqcV9ub2RlJyBdICkuaHRtbCggc2Nyb2xsX3RvX2VsZW1lbnQgKyBtZXNzYWdlICk7XHJcblx0XHR9XHJcblxyXG5cdH0gZWxzZSBpZiAoICdiZWZvcmUnID09PSBwYXJhbXNbICdzaG93X2hlcmUnIF1bICd3aGVyZScgXSApe1xyXG5cclxuXHRcdGpxX2VsX21lc3NhZ2UgPSBqUXVlcnkoIHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ2pxX25vZGUnIF0gKS5zaWJsaW5ncyggJ1tpZF49XCJ3cGJjX25vdGljZV9cIl0nICk7XHJcblx0XHRpZiAoIChwYXJhbXNbICdpZl92aXNpYmxlX25vdF9zaG93JyBdKSAmJiAoanFfZWxfbWVzc2FnZS5pcyggJzp2aXNpYmxlJyApKSApe1xyXG5cdFx0XHRpc19zaG93X21lc3NhZ2UgPSBmYWxzZTtcclxuXHRcdFx0dW5pcXVlX2Rpdl9pZCA9IGpRdWVyeSgganFfZWxfbWVzc2FnZS5nZXQoIDAgKSApLmF0dHIoICdpZCcgKTtcclxuXHRcdH1cclxuXHRcdGlmICggaXNfc2hvd19tZXNzYWdlICl7XHJcblx0XHRcdGpRdWVyeSggcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnanFfbm9kZScgXSApLmJlZm9yZSggc2Nyb2xsX3RvX2VsZW1lbnQgKTtcclxuXHRcdFx0alF1ZXJ5KCBwYXJhbXNbICdzaG93X2hlcmUnIF1bICdqcV9ub2RlJyBdICkuYmVmb3JlKCBtZXNzYWdlICk7XHJcblx0XHR9XHJcblxyXG5cdH0gZWxzZSBpZiAoICdhZnRlcicgPT09IHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ3doZXJlJyBdICl7XHJcblxyXG5cdFx0anFfZWxfbWVzc2FnZSA9IGpRdWVyeSggcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnanFfbm9kZScgXSApLm5leHRBbGwoICdbaWRePVwid3BiY19ub3RpY2VfXCJdJyApO1xyXG5cdFx0aWYgKCAocGFyYW1zWyAnaWZfdmlzaWJsZV9ub3Rfc2hvdycgXSkgJiYgKGpxX2VsX21lc3NhZ2UuaXMoICc6dmlzaWJsZScgKSkgKXtcclxuXHRcdFx0aXNfc2hvd19tZXNzYWdlID0gZmFsc2U7XHJcblx0XHRcdHVuaXF1ZV9kaXZfaWQgPSBqUXVlcnkoIGpxX2VsX21lc3NhZ2UuZ2V0KCAwICkgKS5hdHRyKCAnaWQnICk7XHJcblx0XHR9XHJcblx0XHRpZiAoIGlzX3Nob3dfbWVzc2FnZSApe1xyXG5cdFx0XHRqUXVlcnkoIHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ2pxX25vZGUnIF0gKS5iZWZvcmUoIHNjcm9sbF90b19lbGVtZW50ICk7XHRcdC8vIFdlIG5lZWQgdG8gIHNldCAgaGVyZSBiZWZvcmUoZm9yIGhhbmR5IHNjcm9sbClcclxuXHRcdFx0alF1ZXJ5KCBwYXJhbXNbICdzaG93X2hlcmUnIF1bICdqcV9ub2RlJyBdICkuYWZ0ZXIoIG1lc3NhZ2UgKTtcclxuXHRcdH1cclxuXHJcblx0fSBlbHNlIGlmICggJ3JpZ2h0JyA9PT0gcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnd2hlcmUnIF0gKXtcclxuXHJcblx0XHRqcV9lbF9tZXNzYWdlID0galF1ZXJ5KCBwYXJhbXNbICdzaG93X2hlcmUnIF1bICdqcV9ub2RlJyBdICkubmV4dEFsbCggJy53cGJjX2Zyb250X2VuZF9fbWVzc2FnZV9jb250YWluZXJfcmlnaHQnICkuZmluZCggJ1tpZF49XCJ3cGJjX25vdGljZV9cIl0nICk7XHJcblx0XHRpZiAoIChwYXJhbXNbICdpZl92aXNpYmxlX25vdF9zaG93JyBdKSAmJiAoanFfZWxfbWVzc2FnZS5pcyggJzp2aXNpYmxlJyApKSApe1xyXG5cdFx0XHRpc19zaG93X21lc3NhZ2UgPSBmYWxzZTtcclxuXHRcdFx0dW5pcXVlX2Rpdl9pZCA9IGpRdWVyeSgganFfZWxfbWVzc2FnZS5nZXQoIDAgKSApLmF0dHIoICdpZCcgKTtcclxuXHRcdH1cclxuXHRcdGlmICggaXNfc2hvd19tZXNzYWdlICl7XHJcblx0XHRcdGpRdWVyeSggcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnanFfbm9kZScgXSApLmJlZm9yZSggc2Nyb2xsX3RvX2VsZW1lbnQgKTtcdFx0Ly8gV2UgbmVlZCB0byAgc2V0ICBoZXJlIGJlZm9yZShmb3IgaGFuZHkgc2Nyb2xsKVxyXG5cdFx0XHRqUXVlcnkoIHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ2pxX25vZGUnIF0gKS5hZnRlciggJzxkaXYgY2xhc3M9XCJ3cGJjX2Zyb250X2VuZF9fbWVzc2FnZV9jb250YWluZXJfcmlnaHRcIj4nICsgbWVzc2FnZSArICc8L2Rpdj4nICk7XHJcblx0XHR9XHJcblx0fSBlbHNlIGlmICggJ2xlZnQnID09PSBwYXJhbXNbICdzaG93X2hlcmUnIF1bICd3aGVyZScgXSApe1xyXG5cclxuXHRcdGpxX2VsX21lc3NhZ2UgPSBqUXVlcnkoIHBhcmFtc1sgJ3Nob3dfaGVyZScgXVsgJ2pxX25vZGUnIF0gKS5zaWJsaW5ncyggJy53cGJjX2Zyb250X2VuZF9fbWVzc2FnZV9jb250YWluZXJfbGVmdCcgKS5maW5kKCAnW2lkXj1cIndwYmNfbm90aWNlX1wiXScgKTtcclxuXHRcdGlmICggKHBhcmFtc1sgJ2lmX3Zpc2libGVfbm90X3Nob3cnIF0pICYmIChqcV9lbF9tZXNzYWdlLmlzKCAnOnZpc2libGUnICkpICl7XHJcblx0XHRcdGlzX3Nob3dfbWVzc2FnZSA9IGZhbHNlO1xyXG5cdFx0XHR1bmlxdWVfZGl2X2lkID0galF1ZXJ5KCBqcV9lbF9tZXNzYWdlLmdldCggMCApICkuYXR0ciggJ2lkJyApO1xyXG5cdFx0fVxyXG5cdFx0aWYgKCBpc19zaG93X21lc3NhZ2UgKXtcclxuXHRcdFx0alF1ZXJ5KCBwYXJhbXNbICdzaG93X2hlcmUnIF1bICdqcV9ub2RlJyBdICkuYmVmb3JlKCBzY3JvbGxfdG9fZWxlbWVudCApO1x0XHQvLyBXZSBuZWVkIHRvICBzZXQgIGhlcmUgYmVmb3JlKGZvciBoYW5keSBzY3JvbGwpXHJcblx0XHRcdGpRdWVyeSggcGFyYW1zWyAnc2hvd19oZXJlJyBdWyAnanFfbm9kZScgXSApLmJlZm9yZSggJzxkaXYgY2xhc3M9XCJ3cGJjX2Zyb250X2VuZF9fbWVzc2FnZV9jb250YWluZXJfbGVmdFwiPicgKyBtZXNzYWdlICsgJzwvZGl2PicgKTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdGlmICggICAoIGlzX3Nob3dfbWVzc2FnZSApICAmJiAgKCBwYXJzZUludCggcGFyYW1zWyAnZGVsYXknIF0gKSA+IDAgKSAgICl7XHJcblx0XHR2YXIgY2xvc2VkX3RpbWVyID0gc2V0VGltZW91dCggZnVuY3Rpb24gKCl7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0alF1ZXJ5KCAnIycgKyB1bmlxdWVfZGl2X2lkICkuZmFkZU91dCggMTUwMCApO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdH0gLCBwYXJzZUludCggcGFyYW1zWyAnZGVsYXknIF0gKSAgICk7XHJcblxyXG5cdFx0dmFyIGNsb3NlZF90aW1lcjIgPSBzZXRUaW1lb3V0KCBmdW5jdGlvbiAoKXtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdGpRdWVyeSggJyMnICsgdW5pcXVlX2Rpdl9pZCApLnRyaWdnZXIoICdoaWRlJyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdH0sICggcGFyc2VJbnQoIHBhcmFtc1sgJ2RlbGF5JyBdICkgKyAxNTAxICkgKTtcclxuXHR9XHJcblxyXG5cdC8vIENoZWNrICBpZiBzaG93ZWQgbWVzc2FnZSBpbiBzb21lIGhpZGRlbiBwYXJlbnQgc2VjdGlvbiBhbmQgc2hvdyBpdC4gQnV0IGl0IG11c3QgIGJlIGxvd2VyIHRoYW4gJy53cGJjX2NvbnRhaW5lcidcclxuXHR2YXIgcGFyZW50X2VscyA9IGpRdWVyeSggJyMnICsgdW5pcXVlX2Rpdl9pZCApLnBhcmVudHMoKS5tYXAoIGZ1bmN0aW9uICgpe1xyXG5cdFx0aWYgKCAoIWpRdWVyeSggdGhpcyApLmlzKCAndmlzaWJsZScgKSkgJiYgKGpRdWVyeSggJy53cGJjX2NvbnRhaW5lcicgKS5oYXMoIHRoaXMgKSkgKXtcclxuXHRcdFx0alF1ZXJ5KCB0aGlzICkuc2hvdygpO1xyXG5cdFx0fVxyXG5cdH0gKTtcclxuXHJcblx0aWYgKCBwYXJhbXNbICdpc19zY3JvbGwnIF0gKXtcclxuXHRcdHdwYmNfZG9fc2Nyb2xsKCAnIycgKyB1bmlxdWVfZGl2X2lkICsgJ19zY3JvbGwnICk7XHJcblx0fVxyXG5cclxuXHRyZXR1cm4gdW5pcXVlX2Rpdl9pZDtcclxufVxyXG5cclxuXHJcblx0LyoqXHJcblx0ICogRXJyb3IgbWVzc2FnZS4gXHRQcmVzZXQgb2YgcGFyYW1ldGVycyBmb3IgcmVhbCBtZXNzYWdlIGZ1bmN0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGVsXHRcdC0gYW55IGpRdWVyeSBub2RlIGRlZmluaXRpb25cclxuXHQgKiBAcGFyYW0gbWVzc2FnZVx0LSBNZXNzYWdlIEhUTUxcclxuXHQgKiBAcmV0dXJucyBzdHJpbmcgIC0gSFRNTCBJRFx0XHRvciAwIGlmIG5vdCBzaG93aW5nIGR1cmluZyB0aGlzIHRpbWUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZV9fZXJyb3IoIGpxX25vZGUsIG1lc3NhZ2UgKXtcclxuXHJcblx0XHR2YXIgbm90aWNlX21lc3NhZ2VfaWQgPSB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG1lc3NhZ2UsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3R5cGUnICAgICAgICAgICAgICAgOiAnZXJyb3InLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2RlbGF5JyAgICAgICAgICAgICAgOiAxMDAwMCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdpZl92aXNpYmxlX25vdF9zaG93JzogdHJ1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnICAgICAgICAgIDoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doZXJlJyAgOiAncmlnaHQnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2pxX25vZGUnOiBqcV9ub2RlXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICAgfVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcblx0XHRyZXR1cm4gbm90aWNlX21lc3NhZ2VfaWQ7XHJcblx0fVxyXG5cclxuXHJcblx0LyoqXHJcblx0ICogRXJyb3IgbWVzc2FnZSBVTkRFUiBlbGVtZW50LiBcdFByZXNldCBvZiBwYXJhbWV0ZXJzIGZvciByZWFsIG1lc3NhZ2UgZnVuY3Rpb24uXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gZWxcdFx0LSBhbnkgalF1ZXJ5IG5vZGUgZGVmaW5pdGlvblxyXG5cdCAqIEBwYXJhbSBtZXNzYWdlXHQtIE1lc3NhZ2UgSFRNTFxyXG5cdCAqIEByZXR1cm5zIHN0cmluZyAgLSBIVE1MIElEXHRcdG9yIDAgaWYgbm90IHNob3dpbmcgZHVyaW5nIHRoaXMgdGltZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlX19lcnJvcl91bmRlcl9lbGVtZW50KCBqcV9ub2RlLCBtZXNzYWdlLCBtZXNzYWdlX2RlbGF5ICl7XHJcblxyXG5cdFx0aWYgKCAndW5kZWZpbmVkJyA9PT0gdHlwZW9mIChtZXNzYWdlX2RlbGF5KSApe1xyXG5cdFx0XHRtZXNzYWdlX2RlbGF5ID0gMFxyXG5cdFx0fVxyXG5cclxuXHRcdHZhciBub3RpY2VfbWVzc2FnZV9pZCA9IHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bWVzc2FnZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndHlwZScgICAgICAgICAgICAgICA6ICdlcnJvcicsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgICAgICAgICAgICA6IG1lc3NhZ2VfZGVsYXksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnaWZfdmlzaWJsZV9ub3Rfc2hvdyc6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc2hvd19oZXJlJyAgICAgICAgICA6IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3aGVyZScgIDogJ2FmdGVyJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdqcV9ub2RlJzoganFfbm9kZVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0cmV0dXJuIG5vdGljZV9tZXNzYWdlX2lkO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIEVycm9yIG1lc3NhZ2UgVU5ERVIgZWxlbWVudC4gXHRQcmVzZXQgb2YgcGFyYW1ldGVycyBmb3IgcmVhbCBtZXNzYWdlIGZ1bmN0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGVsXHRcdC0gYW55IGpRdWVyeSBub2RlIGRlZmluaXRpb25cclxuXHQgKiBAcGFyYW0gbWVzc2FnZVx0LSBNZXNzYWdlIEhUTUxcclxuXHQgKiBAcmV0dXJucyBzdHJpbmcgIC0gSFRNTCBJRFx0XHRvciAwIGlmIG5vdCBzaG93aW5nIGR1cmluZyB0aGlzIHRpbWUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZV9fZXJyb3JfYWJvdmVfZWxlbWVudCgganFfbm9kZSwgbWVzc2FnZSwgbWVzc2FnZV9kZWxheSApe1xyXG5cclxuXHRcdGlmICggJ3VuZGVmaW5lZCcgPT09IHR5cGVvZiAobWVzc2FnZV9kZWxheSkgKXtcclxuXHRcdFx0bWVzc2FnZV9kZWxheSA9IDEwMDAwXHJcblx0XHR9XHJcblxyXG5cdFx0dmFyIG5vdGljZV9tZXNzYWdlX2lkID0gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZShcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRtZXNzYWdlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd0eXBlJyAgICAgICAgICAgICAgIDogJ2Vycm9yJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWxheScgICAgICAgICAgICAgIDogbWVzc2FnZV9kZWxheSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdpZl92aXNpYmxlX25vdF9zaG93JzogdHJ1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnICAgICAgICAgIDoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doZXJlJyAgOiAnYmVmb3JlJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdqcV9ub2RlJzoganFfbm9kZVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0cmV0dXJuIG5vdGljZV9tZXNzYWdlX2lkO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIFdhcm5pbmcgbWVzc2FnZS4gXHRQcmVzZXQgb2YgcGFyYW1ldGVycyBmb3IgcmVhbCBtZXNzYWdlIGZ1bmN0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGVsXHRcdC0gYW55IGpRdWVyeSBub2RlIGRlZmluaXRpb25cclxuXHQgKiBAcGFyYW0gbWVzc2FnZVx0LSBNZXNzYWdlIEhUTUxcclxuXHQgKiBAcmV0dXJucyBzdHJpbmcgIC0gSFRNTCBJRFx0XHRvciAwIGlmIG5vdCBzaG93aW5nIGR1cmluZyB0aGlzIHRpbWUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZV9fd2FybmluZygganFfbm9kZSwgbWVzc2FnZSApe1xyXG5cclxuXHRcdHZhciBub3RpY2VfbWVzc2FnZV9pZCA9IHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2UoXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bWVzc2FnZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndHlwZScgICAgICAgICAgICAgICA6ICd3YXJuaW5nJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWxheScgICAgICAgICAgICAgIDogMTAwMDAsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnaWZfdmlzaWJsZV9ub3Rfc2hvdyc6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnc2hvd19oZXJlJyAgICAgICAgICA6IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3aGVyZScgIDogJ3JpZ2h0JyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdqcV9ub2RlJzoganFfbm9kZVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0d3BiY19oaWdobGlnaHRfZXJyb3Jfb25fZm9ybV9maWVsZCgganFfbm9kZSApO1xyXG5cdFx0cmV0dXJuIG5vdGljZV9tZXNzYWdlX2lkO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIFdhcm5pbmcgbWVzc2FnZSBVTkRFUiBlbGVtZW50LiBcdFByZXNldCBvZiBwYXJhbWV0ZXJzIGZvciByZWFsIG1lc3NhZ2UgZnVuY3Rpb24uXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gZWxcdFx0LSBhbnkgalF1ZXJ5IG5vZGUgZGVmaW5pdGlvblxyXG5cdCAqIEBwYXJhbSBtZXNzYWdlXHQtIE1lc3NhZ2UgSFRNTFxyXG5cdCAqIEByZXR1cm5zIHN0cmluZyAgLSBIVE1MIElEXHRcdG9yIDAgaWYgbm90IHNob3dpbmcgZHVyaW5nIHRoaXMgdGltZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlX193YXJuaW5nX3VuZGVyX2VsZW1lbnQoIGpxX25vZGUsIG1lc3NhZ2UgKXtcclxuXHJcblx0XHR2YXIgbm90aWNlX21lc3NhZ2VfaWQgPSB3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG1lc3NhZ2UsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0e1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3R5cGUnICAgICAgICAgICAgICAgOiAnd2FybmluZycsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGVsYXknICAgICAgICAgICAgICA6IDEwMDAwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2lmX3Zpc2libGVfbm90X3Nob3cnOiB0cnVlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3Nob3dfaGVyZScgICAgICAgICAgOiB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnd2hlcmUnICA6ICdhZnRlcicsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnanFfbm9kZSc6IGpxX25vZGVcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgICB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KTtcclxuXHRcdHJldHVybiBub3RpY2VfbWVzc2FnZV9pZDtcclxuXHR9XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBXYXJuaW5nIG1lc3NhZ2UgQUJPVkUgZWxlbWVudC4gXHRQcmVzZXQgb2YgcGFyYW1ldGVycyBmb3IgcmVhbCBtZXNzYWdlIGZ1bmN0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGVsXHRcdC0gYW55IGpRdWVyeSBub2RlIGRlZmluaXRpb25cclxuXHQgKiBAcGFyYW0gbWVzc2FnZVx0LSBNZXNzYWdlIEhUTUxcclxuXHQgKiBAcmV0dXJucyBzdHJpbmcgIC0gSFRNTCBJRFx0XHRvciAwIGlmIG5vdCBzaG93aW5nIGR1cmluZyB0aGlzIHRpbWUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZV9fd2FybmluZ19hYm92ZV9lbGVtZW50KCBqcV9ub2RlLCBtZXNzYWdlICl7XHJcblxyXG5cdFx0dmFyIG5vdGljZV9tZXNzYWdlX2lkID0gd3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZShcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRtZXNzYWdlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd0eXBlJyAgICAgICAgICAgICAgIDogJ3dhcm5pbmcnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2RlbGF5JyAgICAgICAgICAgICAgOiAxMDAwMCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdpZl92aXNpYmxlX25vdF9zaG93JzogdHJ1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzaG93X2hlcmUnICAgICAgICAgIDoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doZXJlJyAgOiAnYmVmb3JlJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdqcV9ub2RlJzoganFfbm9kZVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQpO1xyXG5cdFx0cmV0dXJuIG5vdGljZV9tZXNzYWdlX2lkO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogSGlnaGxpZ2h0IEVycm9yIGluIHNwZWNpZmljIGZpZWxkXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ganFfbm9kZVx0XHRcdFx0XHRzdHJpbmcgb3IgalF1ZXJ5IGVsZW1lbnQsICB3aGVyZSBzY3JvbGwgIHRvXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19oaWdobGlnaHRfZXJyb3Jfb25fZm9ybV9maWVsZCgganFfbm9kZSApe1xyXG5cclxuXHRcdGlmICggIWpRdWVyeSgganFfbm9kZSApLmxlbmd0aCApe1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblx0XHRpZiAoICEgalF1ZXJ5KCBqcV9ub2RlICkuaXMoICc6aW5wdXQnICkgKXtcclxuXHRcdFx0Ly8gU2l0dWF0aW9uIHdpdGggIGNoZWNrYm94ZXMgb3IgcmFkaW8gIGJ1dHRvbnNcclxuXHRcdFx0dmFyIGpxX25vZGVfYXJyID0galF1ZXJ5KCBqcV9ub2RlICkuZmluZCggJzppbnB1dCcgKTtcclxuXHRcdFx0aWYgKCAhanFfbm9kZV9hcnIubGVuZ3RoICl7XHJcblx0XHRcdFx0cmV0dXJuXHJcblx0XHRcdH1cclxuXHRcdFx0anFfbm9kZSA9IGpxX25vZGVfYXJyLmdldCggMCApO1xyXG5cdFx0fVxyXG5cdFx0dmFyIHBhcmFtcyA9IHt9O1xyXG5cdFx0cGFyYW1zWyAnZGVsYXknIF0gPSAxMDAwMDtcclxuXHJcblx0XHRpZiAoICFqUXVlcnkoIGpxX25vZGUgKS5oYXNDbGFzcyggJ3dwYmNfZm9ybV9maWVsZF9lcnJvcicgKSApe1xyXG5cclxuXHRcdFx0alF1ZXJ5KCBqcV9ub2RlICkuYWRkQ2xhc3MoICd3cGJjX2Zvcm1fZmllbGRfZXJyb3InIClcclxuXHJcblx0XHRcdGlmICggcGFyc2VJbnQoIHBhcmFtc1sgJ2RlbGF5JyBdICkgPiAwICl7XHJcblx0XHRcdFx0dmFyIGNsb3NlZF90aW1lciA9IHNldFRpbWVvdXQoIGZ1bmN0aW9uICgpe1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgalF1ZXJ5KCBqcV9ub2RlICkucmVtb3ZlQ2xhc3MoICd3cGJjX2Zvcm1fZmllbGRfZXJyb3InICk7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgICwgcGFyc2VJbnQoIHBhcmFtc1sgJ2RlbGF5JyBdIClcclxuXHRcdFx0XHRcdFx0XHRcdFx0KTtcclxuXHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHR9XHJcblxyXG4vKipcclxuICogU2Nyb2xsIHRvIHNwZWNpZmljIGVsZW1lbnRcclxuICpcclxuICogQHBhcmFtIGpxX25vZGVcdFx0XHRcdFx0c3RyaW5nIG9yIGpRdWVyeSBlbGVtZW50LCAgd2hlcmUgc2Nyb2xsICB0b1xyXG4gKiBAcGFyYW0gZXh0cmFfc2hpZnRfb2Zmc2V0XHRcdGludCBzaGlmdCBvZmZzZXQgZnJvbSAganFfbm9kZVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19kb19zY3JvbGwoIGpxX25vZGUgLCBleHRyYV9zaGlmdF9vZmZzZXQgPSAwICl7XHJcblxyXG5cdGlmICggIWpRdWVyeSgganFfbm9kZSApLmxlbmd0aCApe1xyXG5cdFx0cmV0dXJuO1xyXG5cdH1cclxuXHR2YXIgdGFyZ2V0T2Zmc2V0ID0galF1ZXJ5KCBqcV9ub2RlICkub2Zmc2V0KCkudG9wO1xyXG5cclxuXHRpZiAoIHRhcmdldE9mZnNldCA8PSAwICl7XHJcblx0XHRpZiAoIDAgIT0galF1ZXJ5KCBqcV9ub2RlICkubmV4dEFsbCggJzp2aXNpYmxlJyApLmxlbmd0aCApe1xyXG5cdFx0XHR0YXJnZXRPZmZzZXQgPSBqUXVlcnkoIGpxX25vZGUgKS5uZXh0QWxsKCAnOnZpc2libGUnICkuZmlyc3QoKS5vZmZzZXQoKS50b3A7XHJcblx0XHR9IGVsc2UgaWYgKCAwICE9IGpRdWVyeSgganFfbm9kZSApLnBhcmVudCgpLm5leHRBbGwoICc6dmlzaWJsZScgKS5sZW5ndGggKXtcclxuXHRcdFx0dGFyZ2V0T2Zmc2V0ID0galF1ZXJ5KCBqcV9ub2RlICkucGFyZW50KCkubmV4dEFsbCggJzp2aXNpYmxlJyApLmZpcnN0KCkub2Zmc2V0KCkudG9wO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0aWYgKCBqUXVlcnkoICcjd3BhZG1pbmJhcicgKS5sZW5ndGggPiAwICl7XHJcblx0XHR0YXJnZXRPZmZzZXQgPSB0YXJnZXRPZmZzZXQgLSA1MCAtIDUwO1xyXG5cdH0gZWxzZSB7XHJcblx0XHR0YXJnZXRPZmZzZXQgPSB0YXJnZXRPZmZzZXQgLSAyMCAtIDUwO1xyXG5cdH1cclxuXHR0YXJnZXRPZmZzZXQgKz0gZXh0cmFfc2hpZnRfb2Zmc2V0O1xyXG5cclxuXHQvLyBTY3JvbGwgb25seSAgaWYgd2UgZGlkIG5vdCBzY3JvbGwgYmVmb3JlXHJcblx0aWYgKCAhIGpRdWVyeSggJ2h0bWwsYm9keScgKS5pcyggJzphbmltYXRlZCcgKSApe1xyXG5cdFx0alF1ZXJ5KCAnaHRtbCxib2R5JyApLmFuaW1hdGUoIHtzY3JvbGxUb3A6IHRhcmdldE9mZnNldH0sIDUwMCApO1xyXG5cdH1cclxufVxyXG5cclxuIiwiXHJcbi8vIEZpeEluOiAxMC4yLjAuNC5cclxuLyoqXHJcbiAqIERlZmluZSBQb3BvdmVycyBmb3IgVGltZWxpbmVzIGluIFdQIEJvb2tpbmcgQ2FsZW5kYXJcclxuICpcclxuICogQHJldHVybnMge3N0cmluZ3xib29sZWFufVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19kZWZpbmVfdGlwcHlfcG9wb3Zlcigpe1xyXG5cdGlmICggJ2Z1bmN0aW9uJyAhPT0gdHlwZW9mICh3cGJjX3RpcHB5KSApe1xyXG5cdFx0Y29uc29sZS5sb2coICdXUEJDIEVycm9yLiB3cGJjX3RpcHB5IHdhcyBub3QgZGVmaW5lZC4nICk7XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fVxyXG5cdHdwYmNfdGlwcHkoICcucG9wb3Zlcl9ib3R0b20ucG9wb3Zlcl9jbGljaycsIHtcclxuXHRcdGNvbnRlbnQoIHJlZmVyZW5jZSApe1xyXG5cdFx0XHR2YXIgcG9wb3Zlcl90aXRsZSA9IHJlZmVyZW5jZS5nZXRBdHRyaWJ1dGUoICdkYXRhLW9yaWdpbmFsLXRpdGxlJyApO1xyXG5cdFx0XHR2YXIgcG9wb3Zlcl9jb250ZW50ID0gcmVmZXJlbmNlLmdldEF0dHJpYnV0ZSggJ2RhdGEtY29udGVudCcgKTtcclxuXHRcdFx0cmV0dXJuICc8ZGl2IGNsYXNzPVwicG9wb3ZlciBwb3BvdmVyX3RpcHB5XCI+J1xyXG5cdFx0XHRcdCsgJzxkaXYgY2xhc3M9XCJwb3BvdmVyLWNsb3NlXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiIG9uY2xpY2s9XCJqYXZhc2NyaXB0OnRoaXMucGFyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50LnBhcmVudEVsZW1lbnQucGFyZW50RWxlbWVudC5wYXJlbnRFbGVtZW50Ll90aXBweS5oaWRlKCk7XCIgPiZ0aW1lczs8L2E+PC9kaXY+J1xyXG5cdFx0XHRcdCsgcG9wb3Zlcl9jb250ZW50XHJcblx0XHRcdFx0KyAnPC9kaXY+JztcclxuXHRcdH0sXHJcblx0XHRhbGxvd0hUTUwgICAgICAgIDogdHJ1ZSxcclxuXHRcdHRyaWdnZXIgICAgICAgICAgOiAnbWFudWFsJyxcclxuXHRcdGludGVyYWN0aXZlICAgICAgOiB0cnVlLFxyXG5cdFx0aGlkZU9uQ2xpY2sgICAgICA6IGZhbHNlLFxyXG5cdFx0aW50ZXJhY3RpdmVCb3JkZXI6IDEwLFxyXG5cdFx0bWF4V2lkdGggICAgICAgICA6IDU1MCxcclxuXHRcdHRoZW1lICAgICAgICAgICAgOiAnd3BiYy10aXBweS1wb3BvdmVyJyxcclxuXHRcdHBsYWNlbWVudCAgICAgICAgOiAnYm90dG9tLXN0YXJ0JyxcclxuXHRcdHRvdWNoICAgICAgICAgICAgOiBbJ2hvbGQnLCA1MDBdLFxyXG5cdH0gKTtcclxuXHRqUXVlcnkoICcucG9wb3Zlcl9ib3R0b20ucG9wb3Zlcl9jbGljaycgKS5vbiggJ2NsaWNrJywgZnVuY3Rpb24gKCl7XHJcblx0XHRpZiAoIHRoaXMuX3RpcHB5LnN0YXRlLmlzVmlzaWJsZSApe1xyXG5cdFx0XHR0aGlzLl90aXBweS5oaWRlKCk7XHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHR0aGlzLl90aXBweS5zaG93KCk7XHJcblx0XHR9XHJcblx0fSApO1xyXG5cdHdwYmNfZGVmaW5lX2hpZGVfdGlwcHlfb25fc2Nyb2xsKCk7XHJcbn1cclxuXHJcblxyXG5cclxuZnVuY3Rpb24gd3BiY19kZWZpbmVfaGlkZV90aXBweV9vbl9zY3JvbGwoKXtcclxuXHRqUXVlcnkoICcuZmxleF90bF9fc2Nyb2xsaW5nX3NlY3Rpb24yLC5mbGV4X3RsX19zY3JvbGxpbmdfc2VjdGlvbnMnICkub24oICdzY3JvbGwnLCBmdW5jdGlvbiAoIGV2ZW50ICl7XHJcblx0XHRpZiAoICdmdW5jdGlvbicgPT09IHR5cGVvZiAod3BiY190aXBweSkgKXtcclxuXHRcdFx0d3BiY190aXBweS5oaWRlQWxsKCk7XHJcblx0XHR9XHJcblx0fSApO1xyXG59XHJcbiIsIi8qKlxyXG4gKiBXUEJDIGNhbGVuZGFyIGxvYWRlciBib290c3RyYXAuXHJcbiAqID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuICogLSBGaW5kcyBldmVyeSAuY2FsZW5kYXJfbG9hZGVyX2ZyYW1lW2RhdGEtd3BiYy1yaWRdIG9uIHRoZSBwYWdlIChub3cgb3IgbGF0ZXIpLlxyXG4gKiAtIEZvciBlYWNoIGxvYWRlciBlbGVtZW50LCB3YWl0cyBhIFwiZ3JhY2VcIiBwZXJpb2QgKGRhdGEtd3BiYy1ncmFjZSwgZGVmYXVsdCA4MDAwIG1zKTpcclxuICogICAgIC0gSWYgdGhlIHJlYWwgY2FsZW5kYXIgYXBwZWFyczogZG8gbm90aGluZyAobG9hZGVyIG5hdHVyYWxseSByZXBsYWNlZCkuXHJcbiAqICAgICAtIElmIG5vdDogc2hvdyBhIGhlbHBmdWwgbWVzc2FnZSAobWlzc2luZyBqUXVlcnkvX3dwYmMvZGF0ZXBpY2spIG9yIGEgZHVwbGljYXRlIG5vdGljZS5cclxuICogLSBXb3JrcyB3aXRoIG11bHRpcGxlIGNhbGVuZGFycyBhbmQgZXZlbiBkdXBsaWNhdGUgUklEcyBvbiB0aGUgc2FtZSBwYWdlLlxyXG4gKiAtIE5vIGlubGluZSBKUyBuZWVkZWQgaW4gdGhlIHNob3J0Y29kZS90ZW1wbGF0ZSBvdXRwdXQuXHJcbiAqXHJcbiAqIEZpbGU6ICAuLi9pbmNsdWRlcy9fX2pzL2NsaWVudC9jYWwvd3BiY19jYWxfbG9hZGVyLmpzXHJcbiAqXHJcbiAqIEBzaW5jZSAgICAxMC4xNC41XHJcbiAqIEBtb2RpZmllZCAyMDI1LTA5LTA3IDEyOjIxXHJcbiAqIEB2ZXJzaW9uICAxLjAuMFxyXG4gKlxyXG4gKi9cclxuLyoqXHJcbiAqIFdQQkMgY2FsZW5kYXIgbG9hZGVyIGJvb3RzdHJhcC5cclxuICogLSBBdXRvLWRldGVjdHMgLmNhbGVuZGFyX2xvYWRlcl9mcmFtZVtkYXRhLXdwYmMtcmlkXSBibG9ja3MuXHJcbiAqIC0gV2FpdHMgYSBcImdyYWNlXCIgcGVyaW9kIHBlciBlbGVtZW50IGJlZm9yZSBzaG93aW5nIGEgaGVscGZ1bCBtZXNzYWdlXHJcbiAqICAgaWYgdGhlIHJlYWwgY2FsZW5kYXIgaGFzbid0IHJlcGxhY2VkIHRoZSBsb2FkZXIuXHJcbiAqIC0gTXVsdGlwbGUgY2FsZW5kYXJzIGFuZCBkdXBsaWNhdGUgUklEcyBhcmUgaGFuZGxlZC5cclxuICovXHJcbihmdW5jdGlvbiAodywgZCkge1xyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0LyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0ICogU21hbGwgdXRpbGl0aWVzIChzbmFrZV9jYXNlKVxyXG5cdCAqIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuXHQvKiogVHJhY2sgcHJvY2Vzc2VkIGxvYWRlciBlbGVtZW50czsgZmFsbGJhY2sgdG8gZGF0YSBmbGFnIGlmIFdlYWtTZXQgbWlzc2luZy4gKi9cclxuXHR2YXIgcHJvY2Vzc2VkX3NldCA9IHR5cGVvZiBXZWFrU2V0ID09PSAnZnVuY3Rpb24nID8gbmV3IFdlYWtTZXQoKSA6IG51bGw7XHJcblxyXG5cdC8qKiBSZXR1cm4gZmlyc3QgbWF0Y2ggaW5zaWRlIG9wdGlvbmFsIHJvb3QuICovXHJcblx0ZnVuY3Rpb24gcXVlcnlfb25lKHNlbGVjdG9yLCByb290KSB7XHJcblx0XHRyZXR1cm4gKHJvb3QgfHwgZCkucXVlcnlTZWxlY3Rvciggc2VsZWN0b3IgKTtcclxuXHR9XHJcblxyXG5cdC8qKiBSZXR1cm4gTm9kZUxpc3Qgb2YgbWF0Y2hlcyBpbnNpZGUgb3B0aW9uYWwgcm9vdC4gKi9cclxuXHRmdW5jdGlvbiBxdWVyeV9hbGwoc2VsZWN0b3IsIHJvb3QpIHtcclxuXHRcdHJldHVybiAocm9vdCB8fCBkKS5xdWVyeVNlbGVjdG9yQWxsKCBzZWxlY3RvciApO1xyXG5cdH1cclxuXHJcblx0LyoqIFJ1biBhIGNhbGxiYWNrIHdoZW4gRE9NIGlzIHJlYWR5LiAqL1xyXG5cdGZ1bmN0aW9uIG9uX3JlYWR5KGZuKSB7XHJcblx0XHRpZiAoIGQucmVhZHlTdGF0ZSA9PT0gJ2xvYWRpbmcnICkge1xyXG5cdFx0XHRkLmFkZEV2ZW50TGlzdGVuZXIoICdET01Db250ZW50TG9hZGVkJywgZm4gKTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdGZuKCk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvKiogQ2xlYXIgaW50ZXJ2YWwgc2FmZWx5LiAqL1xyXG5cdGZ1bmN0aW9uIHNhZmVfY2xlYXIoaW50ZXJ2YWxfaWQpIHtcclxuXHRcdHRyeSB7XHJcblx0XHRcdHcuY2xlYXJJbnRlcnZhbCggaW50ZXJ2YWxfaWQgKTtcclxuXHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0LyoqIE1hcmsgZWxlbWVudCBwcm9jZXNzZWQgKFdlYWtTZXQgb3IgZGF0YSBhdHRyaWJ1dGUpLiAqL1xyXG5cdGZ1bmN0aW9uIG1hcmtfcHJvY2Vzc2VkKGVsKSB7XHJcblx0XHRpZiAoIHByb2Nlc3NlZF9zZXQgKSB7XHJcblx0XHRcdHByb2Nlc3NlZF9zZXQuYWRkKCBlbCApO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRlbC5kYXRhc2V0LndwYmNQcm9jZXNzZWQgPSAnMSc7XHJcblx0XHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvKiogQ2hlY2sgaWYgZWxlbWVudCB3YXMgcHJvY2Vzc2VkLiAqL1xyXG5cdGZ1bmN0aW9uIGlzX3Byb2Nlc3NlZChlbCkge1xyXG5cdFx0cmV0dXJuIHByb2Nlc3NlZF9zZXQgPyBwcm9jZXNzZWRfc2V0LmhhcyggZWwgKSA6IChlbCAmJiBlbC5kYXRhc2V0ICYmIGVsLmRhdGFzZXQud3BiY1Byb2Nlc3NlZCA9PT0gJzEnKTtcclxuXHR9XHJcblxyXG5cdC8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdCAqIE1lc3NhZ2VzIChmaXhlZCBFbmdsaXNoIHN0cmluZ3M7IG5vIGkxOG4pXHJcblx0ICogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG5cdC8qKlxyXG5cdCAqIEJ1aWxkIGZpeGVkIEVuZ2xpc2ggbWVzc2FnZXMgZm9yIGEgcmVzb3VyY2UuXHJcblx0ICogQHBhcmFtIHtzdHJpbmd8bnVtYmVyfSByaWRcclxuXHQgKiBAcmV0dXJuIHt7ZHVwbGljYXRlOnN0cmluZyxzdXBwb3J0OnN0cmluZyxsaWJfanE6c3RyaW5nLGxpYl9kcDpzdHJpbmcsbGliX3dwYmM6c3RyaW5nfX1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBnZXRfbWVzc2FnZXMocmlkKSB7XHJcblx0XHR2YXIgcmlkX2ludCA9IHBhcnNlSW50KCByaWQsIDEwICk7XHJcblx0XHRyZXR1cm4ge1xyXG5cdFx0XHRkdXBsaWNhdGUgIDpcclxuXHRcdFx0XHQnWW91IGhhdmUgYWRkZWQgdGhlIHNhbWUgY2FsZW5kYXIgKElEID0gJyArIHJpZF9pbnQgKyAnKSBtb3JlIHRoYW4gb25jZSBvbiB0aGlzIHBhZ2UuICcgK1xyXG5cdFx0XHRcdCdQbGVhc2Uga2VlcCBvbmx5IG9uZSBjYWxlbmRhciB3aXRoIHRoZSBzYW1lIElEIG9uIGEgcGFnZSB0byBhdm9pZCBjb25mbGljdHMuJyxcclxuXHRcdFx0aW5pdF9mYWlsZWQ6XHJcblx0XHRcdFx0J1RoZSBjYWxlbmRhciBjb3VsZCBub3QgYmUgaW5pdGlhbGl6ZWQgb24gdGhpcyBwYWdlLicgKyAnXFxuJyArXHJcblx0XHRcdFx0J1BsZWFzZSBjaGVjayB5b3VyIGJyb3dzZXIgY29uc29sZSBmb3IgSmF2YVNjcmlwdCBlcnJvcnMgYW5kIGNvbmZsaWN0cyB3aXRoIG90aGVyIHNjcmlwdHMvcGx1Z2lucy4nLFxyXG5cdFx0XHRzdXBwb3J0ICAgIDogJ0NvbnRhY3Qgc3VwcG9ydEB3cGJvb2tpbmdjYWxlbmRhci5jb20gaWYgeW91IGhhdmUgYW55IHF1ZXN0aW9ucy4nLFxyXG5cdFx0XHRsaWJfanEgICAgIDpcclxuXHRcdFx0XHQnSXQgYXBwZWFycyB0aGF0IHRoZSBcImpRdWVyeVwiIGxpYnJhcnkgaXMgbm90IGxvYWRpbmcgY29ycmVjdGx5LicgKyAnXFxuJyArXHJcblx0XHRcdFx0J0ZvciBtb3JlIGluZm9ybWF0aW9uLCBwbGVhc2UgcmVmZXIgdG8gdGhpcyBwYWdlOiBodHRwczovL3dwYm9va2luZ2NhbGVuZGFyLmNvbS9mYXEvJyxcclxuXHRcdFx0bGliX2RwICAgICA6XHJcblx0XHRcdFx0J0l0IGFwcGVhcnMgdGhhdCB0aGUgXCJqUXVlcnkuZGF0ZXBpY2tcIiBsaWJyYXJ5IGlzIG5vdCBsb2FkaW5nIGNvcnJlY3RseS4nICsgJ1xcbicgK1xyXG5cdFx0XHRcdCdGb3IgbW9yZSBpbmZvcm1hdGlvbiwgcGxlYXNlIHJlZmVyIHRvIHRoaXMgcGFnZTogaHR0cHM6Ly93cGJvb2tpbmdjYWxlbmRhci5jb20vZmFxLycsXHJcblx0XHRcdGxpYl93cGJjICAgOlxyXG5cdFx0XHRcdCdJdCBhcHBlYXJzIHRoYXQgdGhlIFwiX3dwYmNcIiBsaWJyYXJ5IGlzIG5vdCBsb2FkaW5nIGNvcnJlY3RseS4nICsgJ1xcbicgK1xyXG5cdFx0XHRcdCdQbGVhc2UgZW5hYmxlIHRoZSBsb2FkaW5nIG9mIEpTL0NTUyBmaWxlcyBmb3IgdGhpcyBwYWdlIG9uIHRoZSBcIldQIEJvb2tpbmcgQ2FsZW5kYXJcIiAtIFwiU2V0dGluZ3MgR2VuZXJhbFwiIC0gXCJBZHZhbmNlZFwiIHBhZ2UnICsgJ1xcbicgK1xyXG5cdFx0XHRcdCdGb3IgbW9yZSBpbmZvcm1hdGlvbiwgcGxlYXNlIHJlZmVyIHRvIHRoaXMgcGFnZTogaHR0cHM6Ly93cGJvb2tpbmdjYWxlbmRhci5jb20vZmFxLydcclxuXHRcdH07XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBXcmFwIHBsYWluIHRleHQgKHdpdGggbmV3bGluZXMpIGluIGEgc21hbGwgSFRNTCBjb250YWluZXIuXHJcblx0ICogQHBhcmFtIHtzdHJpbmd9IG1zZ1xyXG5cdCAqIEByZXR1cm4ge3N0cmluZ31cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cmFwX2h0bWwobXNnKSB7XHJcblx0XHRyZXR1cm4gJzxkaXYgc3R5bGU9XCJmb250LXNpemU6MTNweDttYXJnaW46MTBweDtcIj4nICsgU3RyaW5nKCBtc2cgfHwgJycgKS5yZXBsYWNlKCAvXFxuL2csICc8YnI+JyApICsgJzwvZGl2Pic7XHJcblx0fVxyXG5cclxuXHQvKiogTGlicmFyeSBwcmVzZW5jZSBjaGVja3MgKGZhc3QgJiBjaGVhcCkuICovXHJcblx0ZnVuY3Rpb24gaGFzX2pxKCkge1xyXG5cdFx0cmV0dXJuICEhKHcualF1ZXJ5ICYmIGpRdWVyeS5mbiAmJiB0eXBlb2YgalF1ZXJ5LmZuLm9uID09PSAnZnVuY3Rpb24nKTtcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIGhhc19kcCgpIHtcclxuXHRcdHJldHVybiAhISh3LmpRdWVyeSAmJiBqUXVlcnkuZGF0ZXBpY2spO1xyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gaGFzX3dwYmMoKSB7XHJcblx0XHRyZXR1cm4gISEody5fd3BiYyAmJiB0eXBlb2Ygdy5fd3BiYy5zZXRfb3RoZXJfcGFyYW0gPT09ICdmdW5jdGlvbicpO1xyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gbm9ybWFsaXplX3JpZChyaWQpIHtcclxuXHRcdHZhciBuID0gcGFyc2VJbnQoIHJpZCwgMTAgKTtcclxuXHRcdHJldHVybiAobiA+IDApID8gU3RyaW5nKCBuICkgOiAnJztcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIGdldF9yaWRfY291bnRzKHJpZCkge1xyXG5cdFx0dmFyIHIgPSBub3JtYWxpemVfcmlkKCByaWQgKTtcclxuXHRcdHJldHVybiB7XHJcblx0XHRcdHJpZCAgICAgICA6IHIsXHJcblx0XHRcdGxvYWRlcnMgICA6IHIgPyBxdWVyeV9hbGwoICcuY2FsZW5kYXJfbG9hZGVyX2ZyYW1lW2RhdGEtd3BiYy1yaWQ9XCInICsgciArICdcIl0nICkubGVuZ3RoIDogMCxcclxuXHRcdFx0Y29udGFpbmVyczogciA/IHF1ZXJ5X2FsbCggJyNjYWxlbmRhcl9ib29raW5nJyArIHIgKS5sZW5ndGggOiAwXHJcblx0XHR9O1xyXG5cdH1cclxuXHJcblx0ZnVuY3Rpb24gaXNfZHVwbGljYXRlX3JpZChyaWQpIHtcclxuXHRcdHZhciBjID0gZ2V0X3JpZF9jb3VudHMoIHJpZCApO1xyXG5cdFx0cmV0dXJuIChjLmxvYWRlcnMgPiAxKSB8fCAoYy5jb250YWluZXJzID4gMSk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBEZXRlcm1pbmUgaWYgdGhlIGxvYWRlciBoYXMgYmVlbiByZXBsYWNlZCBieSB0aGUgcmVhbCBjYWxlbmRhci5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7RWxlbWVudH0gZWwgICAgICAgTG9hZGVyIGVsZW1lbnRcclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gcmlkICAgICAgIFJlc291cmNlIElEXHJcblx0ICogQHBhcmFtIHtFbGVtZW50fG51bGx9IGNvbnRhaW5lciBPcHRpb25hbCAjY2FsZW5kYXJfYm9va2luZ3tyaWR9IGVsZW1lbnRcclxuXHQgKiBAcmV0dXJuIHtib29sZWFufVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIGlzX3JlcGxhY2VkKGVsLCByaWQsIGNvbnRhaW5lcikge1xyXG5cdFx0dmFyIGxvYWRlcl9zdGlsbF9pbl9kb20gPSBkLmJvZHkuY29udGFpbnMoIGVsICk7XHJcblx0XHR2YXIgY2FsZW5kYXJfZXhpc3RzICAgICA9ICEhcXVlcnlfb25lKCAnLndwYmNfY2FsZW5kYXJfaWRfJyArIHJpZCwgY29udGFpbmVyIHx8IGQgKTtcclxuXHRcdHJldHVybiAoIWxvYWRlcl9zdGlsbF9pbl9kb20pIHx8IGNhbGVuZGFyX2V4aXN0cztcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIFN0YXJ0IHdhdGNoZXIgZm9yIGEgc2luZ2xlIGxvYWRlciBlbGVtZW50LlxyXG5cdCAqIC0gUG9sbHMgYW5kIG9ic2VydmVzIHRoZSBjYWxlbmRhciBjb250YWluZXIuXHJcblx0ICogLSBBZnRlciBncmFjZSwgaW5qZWN0cyBhIHN1aXRhYmxlIG1lc3NhZ2UgaWYgbm90IHJlcGxhY2VkLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtFbGVtZW50fSBlbFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHN0YXJ0X2ZvcihlbCkge1xyXG5cdFx0aWYgKCAhIGVsIHx8IGlzX3Byb2Nlc3NlZCggZWwgKSApIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cdFx0bWFya19wcm9jZXNzZWQoIGVsICk7XHJcblxyXG5cdFx0dmFyIHJpZCA9IGVsLmRhdGFzZXQud3BiY1JpZDtcclxuXHRcdGlmICggISByaWQgKSB7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgZ3JhY2VfbXMgPSBwYXJzZUludCggZWwuZGF0YXNldC53cGJjR3JhY2UgfHwgJzgwMDAnLCAxMCApO1xyXG5cdFx0aWYgKCAhIChncmFjZV9tcyA+IDApICkge1xyXG5cdFx0XHRncmFjZV9tcyA9IDgwMDA7XHJcblx0XHR9XHJcblxyXG5cdFx0dmFyIGNvbnRhaW5lcl9pZCA9ICdjYWxlbmRhcl9ib29raW5nJyArIHJpZDtcclxuXHRcdHZhciBjb250YWluZXIgICAgPSBkLmdldEVsZW1lbnRCeUlkKCBjb250YWluZXJfaWQgKTtcclxuXHRcdHZhciB0ZXh0X2VsICAgICAgPSBxdWVyeV9vbmUoICcuY2FsZW5kYXJfbG9hZGVyX3RleHQnLCBlbCApO1xyXG5cclxuXHRcdGZ1bmN0aW9uIHJlcGxhY2VkX25vdygpIHtcclxuXHRcdFx0cmV0dXJuIGlzX3JlcGxhY2VkKCBlbCwgcmlkLCBjb250YWluZXIgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBBbHJlYWR5IHJlcGxhY2VkIC0+IG5vdGhpbmcgdG8gZG8uXHJcblx0XHRpZiAoIHJlcGxhY2VkX25vdygpICkge1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gMSkgQ2hlYXAgcG9sbGluZy5cclxuXHRcdHZhciBwb2xsX2lkID0gdy5zZXRJbnRlcnZhbCggZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRpZiAoIHJlcGxhY2VkX25vdygpICkge1xyXG5cdFx0XHRcdHNhZmVfY2xlYXIoIHBvbGxfaWQgKTtcclxuXHRcdFx0XHRpZiAoIG9ic2VydmVyICkge1xyXG5cdFx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdFx0b2JzZXJ2ZXIuZGlzY29ubmVjdCgpO1xyXG5cdFx0XHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9LCAyNTAgKTtcclxuXHJcblx0XHQvLyAyKSBNdXRhdGlvbk9ic2VydmVyIGZvciBmYXN0ZXIgcmVhY3Rpb24uXHJcblx0XHR2YXIgb2JzZXJ2ZXIgPSBudWxsO1xyXG5cdFx0aWYgKCBjb250YWluZXIgJiYgJ011dGF0aW9uT2JzZXJ2ZXInIGluIHcgKSB7XHJcblx0XHRcdHRyeSB7XHJcblx0XHRcdFx0b2JzZXJ2ZXIgPSBuZXcgTXV0YXRpb25PYnNlcnZlciggZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdFx0aWYgKCByZXBsYWNlZF9ub3coKSApIHtcclxuXHRcdFx0XHRcdFx0c2FmZV9jbGVhciggcG9sbF9pZCApO1xyXG5cdFx0XHRcdFx0XHR0cnkge1xyXG5cdFx0XHRcdFx0XHRcdG9ic2VydmVyLmRpc2Nvbm5lY3QoKTtcclxuXHRcdFx0XHRcdFx0fSBjYXRjaCAoIGUgKSB7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9ICk7XHJcblx0XHRcdFx0b2JzZXJ2ZXIub2JzZXJ2ZSggY29udGFpbmVyLCB7IGNoaWxkTGlzdDogdHJ1ZSwgc3VidHJlZTogdHJ1ZSB9ICk7XHJcblx0XHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gMykgRmluYWwgZGVjaXNpb24gYWZ0ZXIgZ3JhY2UgcGVyaW9kLlxyXG5cdFx0dy5zZXRUaW1lb3V0KCBmdW5jdGlvbiBmaW5hbGl6ZV9hZnRlcl9ncmFjZSgpIHtcclxuXHRcdFx0aWYgKCByZXBsYWNlZF9ub3coKSApIHtcclxuXHRcdFx0XHRzYWZlX2NsZWFyKCBwb2xsX2lkICk7XHJcblx0XHRcdFx0aWYgKCBvYnNlcnZlciApIHtcclxuXHRcdFx0XHRcdHRyeSB7XHJcblx0XHRcdFx0XHRcdG9ic2VydmVyLmRpc2Nvbm5lY3QoKTtcclxuXHRcdFx0XHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHZhciBNID0gZ2V0X21lc3NhZ2VzKCByaWQgKTtcclxuXHRcdFx0dmFyIG1zZztcclxuXHRcdFx0aWYgKCAhIGhhc19qcSgpICkge1xyXG5cdFx0XHRcdG1zZyA9IE0ubGliX2pxO1xyXG5cdFx0XHR9IGVsc2UgaWYgKCAhIGhhc193cGJjKCkgKSB7XHJcblx0XHRcdFx0bXNnID0gTS5saWJfd3BiYztcclxuXHRcdFx0fSBlbHNlIGlmICggISBoYXNfZHAoKSApIHtcclxuXHRcdFx0XHRtc2cgPSBNLmxpYl9kcDtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHQvLyBMaWJyYXJpZXMgYXJlIHByZXNlbnQsIGJ1dCBsb2FkZXIgd2Fzbid0IHJlcGxhY2VkIC0+IGRlY2lkZSB3aGF0IGlzIG1vc3QgbGlrZWx5LlxyXG5cdFx0XHRcdGlmICggaXNfZHVwbGljYXRlX3JpZCggcmlkICkgKSB7XHJcblx0XHRcdFx0XHRtc2cgPSBNLmR1cGxpY2F0ZSArICdcXG5cXG4nICsgTS5zdXBwb3J0O1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRtc2cgPSBNLmluaXRfZmFpbGVkICsgJ1xcblxcbicgKyBNLnN1cHBvcnQ7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHR0cnkge1xyXG5cdFx0XHRcdGlmICggdGV4dF9lbCApIHtcclxuXHRcdFx0XHRcdHRleHRfZWwuaW5uZXJIVE1MID0gd3JhcF9odG1sKCBtc2cgKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRzYWZlX2NsZWFyKCBwb2xsX2lkICk7XHJcblx0XHRcdGlmICggb2JzZXJ2ZXIgKSB7XHJcblx0XHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRcdG9ic2VydmVyLmRpc2Nvbm5lY3QoKTtcclxuXHRcdFx0XHR9IGNhdGNoICggZSApIHtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH0sIGdyYWNlX21zICk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBJbml0aWFsaXplIHdhdGNoZXJzIGZvciBsb2FkZXIgZWxlbWVudHMgYWxyZWFkeSBpbiB0aGUgRE9NLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIGJvb3RzdHJhcF9leGlzdGluZygpIHtcclxuXHRcdHF1ZXJ5X2FsbCggJy5jYWxlbmRhcl9sb2FkZXJfZnJhbWVbZGF0YS13cGJjLXJpZF0nICkuZm9yRWFjaCggc3RhcnRfZm9yICk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBPYnNlcnZlIHRoZSBkb2N1bWVudCBmb3IgYW55IG5ldyBsb2FkZXIgZWxlbWVudHMgaW5zZXJ0ZWQgbGF0ZXIgKEFKQVgsIGJsb2NrIHJlbmRlcikuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gb2JzZXJ2ZV9uZXdfbG9hZGVycygpIHtcclxuXHRcdGlmICggISAoJ011dGF0aW9uT2JzZXJ2ZXInIGluIHcpICkge1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblx0XHR0cnkge1xyXG5cdFx0XHR2YXIgZG9jX29ic2VydmVyID0gbmV3IE11dGF0aW9uT2JzZXJ2ZXIoIGZ1bmN0aW9uIChtdXRhdGlvbnMpIHtcclxuXHRcdFx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCBtdXRhdGlvbnMubGVuZ3RoOyBpKysgKSB7XHJcblx0XHRcdFx0XHR2YXIgbm9kZXMgPSBtdXRhdGlvbnNbaV0uYWRkZWROb2RlcyB8fCBbXTtcclxuXHRcdFx0XHRcdGZvciAoIHZhciBqID0gMDsgaiA8IG5vZGVzLmxlbmd0aDsgaisrICkge1xyXG5cdFx0XHRcdFx0XHR2YXIgbm9kZSA9IG5vZGVzW2pdO1xyXG5cdFx0XHRcdFx0XHRpZiAoICEgbm9kZSB8fCBub2RlLm5vZGVUeXBlICE9PSAxICkge1xyXG5cdFx0XHRcdFx0XHRcdGNvbnRpbnVlO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdGlmICggbm9kZS5tYXRjaGVzICYmIG5vZGUubWF0Y2hlcyggJy5jYWxlbmRhcl9sb2FkZXJfZnJhbWVbZGF0YS13cGJjLXJpZF0nICkgKSB7XHJcblx0XHRcdFx0XHRcdFx0c3RhcnRfZm9yKCBub2RlICk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0aWYgKCBub2RlLnF1ZXJ5U2VsZWN0b3JBbGwgKSB7XHJcblx0XHRcdFx0XHRcdFx0dmFyIGlubmVyID0gbm9kZS5xdWVyeVNlbGVjdG9yQWxsKCAnLmNhbGVuZGFyX2xvYWRlcl9mcmFtZVtkYXRhLXdwYmMtcmlkXScgKTtcclxuXHRcdFx0XHRcdFx0XHRpZiAoIGlubmVyICYmIGlubmVyLmxlbmd0aCApIHtcclxuXHRcdFx0XHRcdFx0XHRcdGlubmVyLmZvckVhY2goIHN0YXJ0X2ZvciApO1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSApO1xyXG5cdFx0XHRkb2Nfb2JzZXJ2ZXIub2JzZXJ2ZSggZC5kb2N1bWVudEVsZW1lbnQsIHsgY2hpbGRMaXN0OiB0cnVlLCBzdWJ0cmVlOiB0cnVlIH0gKTtcclxuXHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0LyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0ICogQm9vdFxyXG5cdCAqIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cdG9uX3JlYWR5KCBmdW5jdGlvbiAoKSB7XHJcblx0XHRib290c3RyYXBfZXhpc3RpbmcoKTtcclxuXHRcdG9ic2VydmVfbmV3X2xvYWRlcnMoKTtcclxuXHR9ICk7XHJcblxyXG59KSggd2luZG93LCBkb2N1bWVudCApO1xyXG4iLCIoZnVuY3Rpb24oIHcgKSB7XHJcblxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0aWYgKCAhIHcuV1BCQ19GRSApIHtcclxuXHRcdHcuV1BCQ19GRSA9IHt9O1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogQXV0by1maWxsIGJvb2tpbmcgZm9ybSBmaWVsZHMgKHRleHQvZW1haWwpIGJhc2VkIG9uIGlucHV0IFwibmFtZVwiIHBhdHRlcm5zLlxyXG5cdCAqXHJcblx0ICogRm9ybSBJRCBmb3JtYXQ6IGJvb2tpbmdfZm9ybXtyZXNvdXJjZV9pZH1cclxuXHQgKiBTa2lwcyBkYXRlIGZpZWxkOiBkYXRlX2Jvb2tpbmd7cmVzb3VyY2VfaWR9XHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge251bWJlcn0gcmVzb3VyY2VfaWQgQm9va2luZyByZXNvdXJjZSBJRC5cclxuXHQgKiBAcGFyYW0ge09iamVjdH0gZmlsbF92YWx1ZXMgVmFsdWVzIHRvIGluamVjdCAoc3RyaW5ncykuXHJcblx0ICpcclxuXHQgKiBAcmV0dXJuIHtib29sZWFufSBUcnVlIGlmIGZvcm0gZm91bmQgYW5kIHByb2Nlc3NlZCwgZmFsc2Ugb3RoZXJ3aXNlLlxyXG5cdCAqL1xyXG5cdHcuV1BCQ19GRS5hdXRvZmlsbF9ib29raW5nX2Zvcm1fZmllbGRzID0gZnVuY3Rpb24oIHJlc291cmNlX2lkLCBmaWxsX3ZhbHVlcyApIHtcclxuXHJcblx0XHRyZXNvdXJjZV9pZCAgPSBwYXJzZUludCggcmVzb3VyY2VfaWQsIDEwICkgfHwgMDtcclxuXHRcdGZpbGxfdmFsdWVzICA9IGZpbGxfdmFsdWVzIHx8IHt9O1xyXG5cclxuXHRcdHZhciBmb3JtX2lkICAgPSAnYm9va2luZ19mb3JtJyArIHJlc291cmNlX2lkO1xyXG5cdFx0dmFyIGRhdGVfbmFtZSA9ICdkYXRlX2Jvb2tpbmcnICsgcmVzb3VyY2VfaWQ7XHJcblxyXG5cdFx0dmFyIHN1Ym1pdF9mb3JtID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoIGZvcm1faWQgKTtcclxuXHJcblx0XHRpZiAoICEgc3VibWl0X2Zvcm0gKSB7XHJcblx0XHRcdC8qIGVzbGludC1kaXNhYmxlIG5vLWNvbnNvbGUgKi9cclxuXHRcdFx0Y29uc29sZS5lcnJvciggJ1dQQkM6IE5vIGJvb2tpbmcgZm9ybTogJyArIGZvcm1faWQgKTtcclxuXHRcdFx0LyogZXNsaW50LWVuYWJsZSBuby1jb25zb2xlICovXHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBLZWVwIHNhbWUgcmVnZXggcnVsZXMgYW5kIHByaW9yaXR5IG9yZGVyIGFzIGxlZ2FjeSBpbmxpbmUgSlMuXHJcblx0XHR2YXIgcnVsZXMgPSBhcnJheV9ydWxlcyggZmlsbF92YWx1ZXMgKTtcclxuXHJcblx0XHR2YXIgZWxlbWVudHMgPSBzdWJtaXRfZm9ybS5lbGVtZW50cyB8fCBbXTtcclxuXHRcdHZhciBjb3VudCAgICA9IGVsZW1lbnRzLmxlbmd0aDtcclxuXHRcdHZhciBlbDtcclxuXHRcdHZhciBpO1xyXG5cdFx0dmFyIGo7XHJcblxyXG5cdFx0Zm9yICggaSA9IDA7IGkgPCBjb3VudDsgaSsrICkge1xyXG5cclxuXHRcdFx0ZWwgPSBlbGVtZW50c1sgaSBdO1xyXG5cclxuXHRcdFx0aWYgKCAhIGVsIHx8ICEgZWwubmFtZSApIHtcclxuXHRcdFx0XHRjb250aW51ZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gT25seSB0ZXh0L2VtYWlsIGlucHV0cy5cclxuXHRcdFx0aWYgKCAoIGVsLnR5cGUgIT09ICd0ZXh0JyApICYmICggZWwudHlwZSAhPT0gJ2VtYWlsJyApICkge1xyXG5cdFx0XHRcdGNvbnRpbnVlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBTa2lwIGRhdGUgZmllbGQuXHJcblx0XHRcdGlmICggZWwubmFtZSA9PT0gZGF0ZV9uYW1lICkge1xyXG5cdFx0XHRcdGNvbnRpbnVlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBGaWxsIG9ubHkgZW1wdHkgdmFsdWVzIChsZWdhY3kgYmVoYXZpb3I6ID09IFwiXCIpLlxyXG5cdFx0XHRpZiAoIGVsLnZhbHVlICE9PSAnJyApIHtcclxuXHRcdFx0XHRjb250aW51ZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Zm9yICggaiA9IDA7IGogPCBydWxlcy5sZW5ndGg7IGorKyApIHtcclxuXHJcblx0XHRcdFx0aWYgKCBydWxlc1sgaiBdLnJlLnRlc3QoIGVsLm5hbWUgKSApIHtcclxuXHJcblx0XHRcdFx0XHRpZiAoIHJ1bGVzWyBqIF0udmFsICE9PSAnJyApIHtcclxuXHRcdFx0XHRcdFx0ZWwudmFsdWUgPSBydWxlc1sgaiBdLnZhbDtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRicmVhazsgLy8gU3RvcCBhdCBmaXJzdCBtYXRjaGluZyBydWxlIChwcmlvcml0eSkuXHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0cmV0dXJuIHRydWU7XHJcblx0fTtcclxuXHJcblx0LyoqXHJcblx0ICogQnVpbGQgcnVsZXMgYXJyYXkgZm9yIGF1dG9maWxsLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtPYmplY3R9IGZpbGxfdmFsdWVzIFZhbHVlcyB0byBpbmplY3QuXHJcblx0ICpcclxuXHQgKiBAcmV0dXJuIHtBcnJheX0gUnVsZXMgbGlzdC5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBhcnJheV9ydWxlcyggZmlsbF92YWx1ZXMgKSB7XHJcblxyXG5cdFx0Ly8gTm9ybWFsaXplIHRvIHN0cmluZ3MgKHByZXZlbnQgXCJ1bmRlZmluZWRcIiBpbiBmaWVsZHMpLlxyXG5cdFx0dmFyIG5pY2tuYW1lICA9ICggZmlsbF92YWx1ZXMubmlja25hbWUgIT0gbnVsbCApID8gU3RyaW5nKCBmaWxsX3ZhbHVlcy5uaWNrbmFtZSApIDogJyc7XHJcblx0XHR2YXIgbGFzdF9uYW1lID0gKCBmaWxsX3ZhbHVlcy5sYXN0X25hbWUgIT0gbnVsbCApID8gU3RyaW5nKCBmaWxsX3ZhbHVlcy5sYXN0X25hbWUgKSA6ICcnO1xyXG5cdFx0dmFyIGZpcnN0X25hbWUgPSAoIGZpbGxfdmFsdWVzLmZpcnN0X25hbWUgIT0gbnVsbCApID8gU3RyaW5nKCBmaWxsX3ZhbHVlcy5maXJzdF9uYW1lICkgOiAnJztcclxuXHRcdHZhciBlbWFpbCAgICAgPSAoIGZpbGxfdmFsdWVzLmVtYWlsICE9IG51bGwgKSA/IFN0cmluZyggZmlsbF92YWx1ZXMuZW1haWwgKSA6ICcnO1xyXG5cdFx0dmFyIHBob25lICAgICA9ICggZmlsbF92YWx1ZXMucGhvbmUgIT0gbnVsbCApID8gU3RyaW5nKCBmaWxsX3ZhbHVlcy5waG9uZSApIDogJyc7XHJcblx0XHR2YXIgbmJfZW5mYW50ID0gKCBmaWxsX3ZhbHVlcy5uYl9lbmZhbnQgIT0gbnVsbCApID8gU3RyaW5nKCBmaWxsX3ZhbHVlcy5uYl9lbmZhbnQgKSA6ICcnO1xyXG5cdFx0dmFyIHVybCAgICAgICA9ICggZmlsbF92YWx1ZXMudXJsICE9IG51bGwgKSA/IFN0cmluZyggZmlsbF92YWx1ZXMudXJsICkgOiAnJztcclxuXHJcblx0XHRyZXR1cm4gW1xyXG5cdFx0XHR7IHJlOiAvXihbQS1aYS16MC05X1xcLVxcLl0pKihuaWNrbmFtZSl7MX0oW0EtWmEtejAtOV9cXC1cXC5dKSokLywgdmFsOiBuaWNrbmFtZSB9LFxyXG5cdFx0XHR7IHJlOiAvXihbQS1aYS16MC05X1xcLVxcLl0pKihsYXN0fHNlY29uZCl7MX0oW19cXC1cXC5dKT9uYW1lKFtBLVphLXowLTlfXFwtXFwuXSkqJC8sIHZhbDogbGFzdF9uYW1lIH0sXHJcblx0XHRcdHsgcmU6IC9ebmFtZShbMC05X1xcLVxcLl0pKiQvLCB2YWw6IGZpcnN0X25hbWUgfSxcclxuXHRcdFx0eyByZTogL14oW0EtWmEtejAtOV9cXC1cXC5dKSooZmlyc3R8bXkpezF9KFtfXFwtXFwuXSk/bmFtZShbQS1aYS16MC05X1xcLVxcLl0pKiQvLCB2YWw6IGZpcnN0X25hbWUgfSxcclxuXHRcdFx0eyByZTogL14oZSk/KFtfXFwtXFwuXSk/bWFpbChbMC05X1xcLVxcLl0qKSQvLCB2YWw6IGVtYWlsIH0sXHJcblx0XHRcdHsgcmU6IC9eKFtBLVphLXowLTlfXFwtXFwuXSkqKHBob25lfGZvbmUpezF9KFtBLVphLXowLTlfXFwtXFwuXSkqJC8sIHZhbDogcGhvbmUgfSxcclxuXHRcdFx0eyByZTogL14oZSk/KFtfXFwtXFwuXSk/bmJfZW5mYW50KFswLTlfXFwtXFwuXSopJC8sIHZhbDogbmJfZW5mYW50IH0sXHJcblx0XHRcdHsgcmU6IC9eKFtBLVphLXowLTlfXFwtXFwuXSkqKFVSTHxzaXRlfHdlYnxXRUIpezF9KFtBLVphLXowLTlfXFwtXFwuXSkqJC8sIHZhbDogdXJsIH1cclxuXHRcdF07XHJcblx0fVxyXG5cclxufSkoIHdpbmRvdyApO1xyXG4iLCIvLyA9PSBTdWJtaXQgQm9va2luZyBEYXRhID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuLy8gUmVmYWN0b3JlZCAoc2FmZSksIHdpdGggbmV3IHdwYmNfKiBuYW1lcy5cclxuLy8gQmFja3dhcmQtY29tcGF0aWJsZSB3cmFwcGVycyBmb3IgbGVnYWN5IGZ1bmN0aW9uIG5hbWVzIGFyZSBpbmNsdWRlZCBhdCB0aGUgYm90dG9tLlxyXG4vLyBAZmlsZTogaW5jbHVkZXMvX19qcy9jbGllbnQvZnJvbnRfZW5kX2Zvcm0vYm9va2luZ19mb3JtX3N1Ym1pdC5qc1xyXG5cclxuLyoqXHJcbiAqIENoZWNrIGZpZWxkcyBhdCBmb3JtIGFuZCB0aGVuIHNlbmQgcmVxdWVzdCAobGVnYWN5OiBteWJvb2tpbmdfc3VibWl0KS5cclxuICpcclxuICogQHBhcmFtIHtIVE1MRm9ybUVsZW1lbnR9IHN1Ym1pdF9mb3JtXHJcbiAqIEBwYXJhbSB7bnVtYmVyfHN0cmluZ30gICByZXNvdXJjZV9pZFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gICAgICAgICAgd3BkZXZfYWN0aXZlX2xvY2FsZVxyXG4gKlxyXG4gKiBAcmV0dXJuIHtmYWxzZXx1bmRlZmluZWR9IExlZ2FjeSBiZWhhdmlvcjogcmV0dXJucyBmYWxzZSBpbiBzb21lIGNhc2VzLCBvdGhlcndpc2UgdW5kZWZpbmVkLlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19ib29raW5nX2Zvcm1fc3VibWl0KCBzdWJtaXRfZm9ybSwgcmVzb3VyY2VfaWQsIHdwZGV2X2FjdGl2ZV9sb2NhbGUgKSB7XHJcblxyXG5cdHJlc291cmNlX2lkID0gcGFyc2VJbnQoIHJlc291cmNlX2lkLCAxMCApO1xyXG5cclxuXHQvLyBTYWZldHkgZ3VhcmQgKGxlZ2FjeSBjb2RlIGFzc3VtZWQgdmFsaWQgZm9ybSkuXHJcblx0aWYgKCAhIHN1Ym1pdF9mb3JtIHx8ICEgc3VibWl0X2Zvcm0uZWxlbWVudHMgKSB7XHJcblx0XHQvKiBlc2xpbnQtZGlzYWJsZSBuby1jb25zb2xlICovXHJcblx0XHRjb25zb2xlLmVycm9yKCAnV1BCQzogSW52YWxpZCBzdWJtaXQgZm9ybSBpbiB3cGJjX2Jvb2tpbmdfZm9ybV9zdWJtaXQoKS4nICk7XHJcblx0XHQvKiBlc2xpbnQtZW5hYmxlIG5vLWNvbnNvbGUgKi9cclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBFeHRlcm5hbCBob29rOiBhbGxvdyBwYXVzZSBzdWJtaXQgb24gY29uZmlybWF0aW9uL3N1bW1hcnkgc3RlcC5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHRhcmdldF9lbG0gPSBqUXVlcnkoICcuYm9va2luZ19mb3JtX2RpdicgKS50cmlnZ2VyKCAnYm9va2luZ19mb3JtX3N1Ym1pdF9jbGljaycsIFsgcmVzb3VyY2VfaWQsIHN1Ym1pdF9mb3JtLCB3cGRldl9hY3RpdmVfbG9jYWxlIF0gKTsgLy8gRml4SW46IDguOC4zLjEzLlxyXG5cclxuXHRpZiAoXHJcblx0XHQoIGpRdWVyeSggdGFyZ2V0X2VsbSApLmZpbmQoICdpbnB1dFtuYW1lPVwiYm9va2luZ19mb3JtX3Nob3dfc3VtbWFyeVwiXScgKS5sZW5ndGggPiAwICkgJiZcclxuXHRcdCggJ3BhdXNlX3N1Ym1pdCcgPT09IGpRdWVyeSggdGFyZ2V0X2VsbSApLmZpbmQoICdpbnB1dFtuYW1lPVwiYm9va2luZ19mb3JtX3Nob3dfc3VtbWFyeVwiXScgKS52YWwoKSApXHJcblx0KSB7XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fVxyXG5cclxuXHQvLyBGaXhJbjogOC40LjAuMi5cclxuXHR2YXIgaXNfZXJyb3IgPSB3cGJjX2NoZWNrX2Vycm9yc19pbl9ib29raW5nX2Zvcm0oIHJlc291cmNlX2lkICk7XHJcblx0aWYgKCBpc19lcnJvciApIHtcclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBTaG93IG1lc3NhZ2UgaWYgbm8gc2VsZWN0ZWQgZGF5cyBpbiBDYWxlbmRhcihzKS5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIGRhdGVfaW5wdXQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2RhdGVfYm9va2luZycgKyByZXNvdXJjZV9pZCApO1xyXG5cdHZhciBkYXRlX3ZhbHVlID0gKCBkYXRlX2lucHV0ICkgPyBkYXRlX2lucHV0LnZhbHVlIDogJyc7XHJcblxyXG5cdGlmICggJycgPT09IGRhdGVfdmFsdWUgKSB7XHJcblxyXG5cdFx0dmFyIGFycl9vZl9zZWxlY3RlZF9hZGRpdGlvbmFsX2NhbGVuZGFycyA9IHdwYmNfZ2V0X2Fycl9vZl9zZWxlY3RlZF9hZGRpdGlvbmFsX2NhbGVuZGFycyggcmVzb3VyY2VfaWQgKTsgLy8gRml4SW46IDguNS4yLjI2LlxyXG5cclxuXHRcdGlmICggISBhcnJfb2Zfc2VsZWN0ZWRfYWRkaXRpb25hbF9jYWxlbmRhcnMgfHwgKCBhcnJfb2Zfc2VsZWN0ZWRfYWRkaXRpb25hbF9jYWxlbmRhcnMubGVuZ3RoID09PSAwICkgKSB7XHJcblx0XHRcdHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2VfX2Vycm9yX3VuZGVyX2VsZW1lbnQoXHJcblx0XHRcdFx0JyNib29raW5nX2Zvcm1fZGl2JyArIHJlc291cmNlX2lkICsgJyAuYmtfY2FsZW5kYXJfZnJhbWUnLFxyXG5cdFx0XHRcdF93cGJjLmdldF9tZXNzYWdlKCAnbWVzc2FnZV9jaGVja19ub19zZWxlY3RlZF9kYXRlcycgKSxcclxuXHRcdFx0XHQzMDAwXHJcblx0XHRcdCk7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBGaXhJbjogNi4xLjEuMy4gVGltZSBzZWxlY3Rpb24gYXZhaWxhYmlsaXR5IGNoZWNrcy5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0aWYgKCB0eXBlb2Ygd3BiY19pc190aGlzX3RpbWVfc2VsZWN0aW9uX25vdF9hdmFpbGFibGUgPT09ICdmdW5jdGlvbicgKSB7XHJcblxyXG5cdFx0aWYgKCAnJyA9PT0gZGF0ZV92YWx1ZSApIHsgLy8gUHJpbWFyeSBjYWxlbmRhciBub3Qgc2VsZWN0ZWQuXHJcblxyXG5cdFx0XHR2YXIgYWRkaXRpb25hbF9jYWxlbmRhcnNfZWwgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2FkZGl0aW9uYWxfY2FsZW5kYXJzJyArIHJlc291cmNlX2lkICk7XHJcblxyXG5cdFx0XHRpZiAoIGFkZGl0aW9uYWxfY2FsZW5kYXJzX2VsICE9PSBudWxsICkgeyAvLyBDaGVja2luZyBhZGRpdGlvbmFsIGNhbGVuZGFycy5cclxuXHJcblx0XHRcdFx0dmFyIGlkX2FkZGl0aW9uYWxfc3RyID0gYWRkaXRpb25hbF9jYWxlbmRhcnNfZWwudmFsdWU7XHJcblx0XHRcdFx0dmFyIGlkX2FkZGl0aW9uYWxfYXJyID0gaWRfYWRkaXRpb25hbF9zdHIuc3BsaXQoICcsJyApO1xyXG5cdFx0XHRcdHZhciBpc190aW1lc19kYXRlc19vayA9IGZhbHNlO1xyXG5cclxuXHRcdFx0XHRmb3IgKCB2YXIgaWEgPSAwOyBpYSA8IGlkX2FkZGl0aW9uYWxfYXJyLmxlbmd0aDsgaWErKyApIHtcclxuXHJcblx0XHRcdFx0XHR2YXIgYWRkX2lkID0gaWRfYWRkaXRpb25hbF9hcnJbIGlhIF07XHJcblxyXG5cdFx0XHRcdFx0dmFyIGFkZF9kYXRlX2VsID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdkYXRlX2Jvb2tpbmcnICsgYWRkX2lkICk7XHJcblx0XHRcdFx0XHR2YXIgYWRkX2RhdGVfdmFsID0gKCBhZGRfZGF0ZV9lbCApID8gYWRkX2RhdGVfZWwudmFsdWUgOiAnJztcclxuXHJcblx0XHRcdFx0XHRpZiAoXHJcblx0XHRcdFx0XHRcdCggJycgIT09IGFkZF9kYXRlX3ZhbCApICYmXHJcblx0XHRcdFx0XHRcdCggISB3cGJjX2lzX3RoaXNfdGltZV9zZWxlY3Rpb25fbm90X2F2YWlsYWJsZSggYWRkX2lkLCBzdWJtaXRfZm9ybS5lbGVtZW50cyApIClcclxuXHRcdFx0XHRcdCkge1xyXG5cdFx0XHRcdFx0XHRpc190aW1lc19kYXRlc19vayA9IHRydWU7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRpZiAoICEgaXNfdGltZXNfZGF0ZXNfb2sgKSB7XHJcblx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0fSBlbHNlIHsgLy8gUHJpbWFyeSBjYWxlbmRhciBzZWxlY3RlZC5cclxuXHJcblx0XHRcdGlmICggd3BiY19pc190aGlzX3RpbWVfc2VsZWN0aW9uX25vdF9hdmFpbGFibGUoIHJlc291cmNlX2lkLCBzdWJtaXRfZm9ybS5lbGVtZW50cyApICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFNlcmlhbGl6ZSBmb3JtIChsZWdhY3kgZm9ybWF0KS5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIGNvdW50ICAgID0gc3VibWl0X2Zvcm0uZWxlbWVudHMubGVuZ3RoO1xyXG5cdHZhciBmb3JtZGF0YSA9ICcnO1xyXG5cdHZhciBpbnBfdmFsdWU7XHJcblx0dmFyIGlucF90aXRsZV92YWx1ZTtcclxuXHR2YXIgZWxlbWVudDtcclxuXHR2YXIgZWxfdHlwZTtcclxuXHJcblx0Ly8gSGVscGVyOiBsZWdhY3kgZXNjYXBpbmcgZm9yIHRoZSBzZXJpYWxpemVkIHZhbHVlLlxyXG5cdGZ1bmN0aW9uIHdwYmNfZXNjYXBlX3NlcmlhbGl6ZWRfdmFsdWUoIHZhbCApIHtcclxuXHJcblx0XHR2YWwgPSAoIHZhbCA9PSBudWxsICkgPyAnJyA6IFN0cmluZyggdmFsICk7XHJcblxyXG5cdFx0Ly8gUmVwbGFjZSByZWdpc3RlcmVkIGNoYXJhY3RlcnMuXHJcblx0XHR2YWwgPSB2YWwucmVwbGFjZSggbmV3IFJlZ0V4cCggJ1xcXFxeJywgJ2cnICksICcmIzk0OycgKTtcclxuXHRcdHZhbCA9IHZhbC5yZXBsYWNlKCBuZXcgUmVnRXhwKCAnficsICdnJyApLCAnJiMxMjY7JyApO1xyXG5cclxuXHRcdC8vIFJlcGxhY2UgcXVvdGVzLlxyXG5cdFx0dmFsID0gdmFsLnJlcGxhY2UoIC9cIi9nLCAnJiMzNDsnICk7XHJcblx0XHR2YWwgPSB2YWwucmVwbGFjZSggLycvZywgJyYjMzk7JyApO1xyXG5cclxuXHRcdHJldHVybiB2YWw7XHJcblx0fVxyXG5cclxuXHQvLyBIZWxwZXI6IGRldGVybWluZSBVSSB0eXBlIGZvciB0aXRsZSBleHRyYWN0aW9uIChsZWdhY3kgbG9naWMpLlxyXG5cdGZ1bmN0aW9uIHdwYmNfZ2V0X2lucHV0X2VsZW1lbnRfdHlwZSggZWwgKSB7XHJcblxyXG5cdFx0aWYgKCAhIGVsIHx8ICEgZWwudGFnTmFtZSApIHtcclxuXHRcdFx0cmV0dXJuICcnO1xyXG5cdFx0fVxyXG5cclxuXHRcdHZhciB0YWcgPSBTdHJpbmcoIGVsLnRhZ05hbWUgKS50b0xvd2VyQ2FzZSgpO1xyXG5cclxuXHRcdGlmICggJ2lucHV0JyA9PT0gdGFnICkge1xyXG5cdFx0XHRyZXR1cm4gKCBlbC50eXBlICkgPyBTdHJpbmcoIGVsLnR5cGUgKS50b0xvd2VyQ2FzZSgpIDogJ3RleHQnO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIExlZ2FjeSB1c2VkIFwic2VsZWN0XCIgc3RyaW5nIGhlcmUuXHJcblx0XHRpZiAoICdzZWxlY3QnID09PSB0YWcgKSB7XHJcblx0XHRcdHJldHVybiAnc2VsZWN0JztcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4gdGFnO1xyXG5cdH1cclxuXHJcblx0Zm9yICggdmFyIGkgPSAwOyBpIDwgY291bnQ7IGkrKyApIHsgLy8gRml4SW46IDkuMS41LjEuXHJcblxyXG5cdFx0ZWxlbWVudCA9IHN1Ym1pdF9mb3JtLmVsZW1lbnRzWyBpIF07XHJcblxyXG5cdFx0aWYgKCAhIGVsZW1lbnQgKSB7XHJcblx0XHRcdGNvbnRpbnVlO1xyXG5cdFx0fVxyXG5cclxuXHRcdGlmICggalF1ZXJ5KCBlbGVtZW50ICkuY2xvc2VzdCggJy5ib29raW5nX2Zvcm1fZ2FyYmFnZScgKS5sZW5ndGggKSB7XHJcblx0XHRcdGNvbnRpbnVlOyAvLyBTa2lwIGVsZW1lbnRzIGZyb20gZ2FyYmFnZS4gRml4SW46IDcuMS4yLjE0LlxyXG5cdFx0fVxyXG5cclxuXHRcdGlmIChcclxuXHRcdFx0KCBlbGVtZW50LnR5cGUgIT09ICdidXR0b24nICkgJiZcclxuXHRcdFx0KCBlbGVtZW50LnR5cGUgIT09ICdoaWRkZW4nICkgJiZcclxuXHRcdFx0KCBlbGVtZW50Lm5hbWUgIT09ICggJ2RhdGVfYm9va2luZycgKyByZXNvdXJjZV9pZCApIClcclxuXHRcdFx0Ly8gJiYgKCBqUXVlcnkoIGVsZW1lbnQgKS5pcyggJzp2aXNpYmxlJyApICkgLy9GaXhJbjogNy4yLjEuMTIuMlxyXG5cdFx0KSB7XHJcblxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdC8vIEdldCBlbGVtZW50IHZhbHVlLlxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdGlmICggZWxlbWVudC50eXBlID09PSAnY2hlY2tib3gnICkge1xyXG5cclxuXHRcdFx0XHRpZiAoIGVsZW1lbnQudmFsdWUgPT09ICcnICkge1xyXG5cdFx0XHRcdFx0aW5wX3ZhbHVlID0gZWxlbWVudC5jaGVja2VkO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRpbnBfdmFsdWUgPSAoIGVsZW1lbnQuY2hlY2tlZCApID8gZWxlbWVudC52YWx1ZSA6ICcnO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdH0gZWxzZSBpZiAoIGVsZW1lbnQudHlwZSA9PT0gJ3JhZGlvJyApIHtcclxuXHJcblx0XHRcdFx0aWYgKCBlbGVtZW50LmNoZWNrZWQgKSB7XHJcblx0XHRcdFx0XHRpbnBfdmFsdWUgPSBlbGVtZW50LnZhbHVlO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblxyXG5cdFx0XHRcdFx0Ly8gUmVxdWlyZWQgcmFkaW86IHNob3cgd2FybmluZyBpZiBub25lIGNoZWNrZWQuXHJcblx0XHRcdFx0XHQvLyBGaXhJbjogNy4wLjEuNjIuXHJcblx0XHRcdFx0XHRpZiAoXHJcblx0XHRcdFx0XHRcdCggZWxlbWVudC5jbGFzc05hbWUuaW5kZXhPZiggJ3dwZGV2LXZhbGlkYXRlcy1hcy1yZXF1aXJlZCcgKSAhPT0gLTEgKSAmJlxyXG5cdFx0XHRcdFx0XHQoIGpRdWVyeSggZWxlbWVudCApLmlzKCAnOnZpc2libGUnICkgKSAmJiAvLyBGaXhJbjogNy4yLjEuMTIuMi5cclxuXHRcdFx0XHRcdFx0KCAhIGpRdWVyeSggJzpyYWRpb1tuYW1lPVwiJyArIGVsZW1lbnQubmFtZSArICdcIl0nLCBzdWJtaXRfZm9ybSApLmlzKCAnOmNoZWNrZWQnICkgKVxyXG5cdFx0XHRcdFx0KSB7XHJcblx0XHRcdFx0XHRcdHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2VfX3dhcm5pbmcoIGVsZW1lbnQsIF93cGJjLmdldF9tZXNzYWdlKCAnbWVzc2FnZV9jaGVja19yZXF1aXJlZF9mb3JfcmFkaW9fYm94JyApICk7IC8vIEZpeEluOiA4LjUuMS4zLlxyXG5cdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0Ly8gU2tpcCBzdG9yaW5nIGVtcHR5IHJhZGlvIG9wdGlvbnMuXHJcblx0XHRcdFx0XHRjb250aW51ZTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdGlucF92YWx1ZSA9IGVsZW1lbnQudmFsdWU7XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdGlucF90aXRsZV92YWx1ZSA9ICcnO1xyXG5cclxuXHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHQvLyBHZXQgaHVtYW4tZnJpZW5kbHkgdGl0bGUgdmFsdWUgKGxlZ2FjeSBiZWhhdmlvcikuXHJcblx0XHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFx0dmFyIGlucHV0X2VsZW1lbnRfdHlwZSA9IHdwYmNfZ2V0X2lucHV0X2VsZW1lbnRfdHlwZSggZWxlbWVudCApO1xyXG5cclxuXHRcdFx0c3dpdGNoICggaW5wdXRfZWxlbWVudF90eXBlICkge1xyXG5cclxuXHRcdFx0XHRjYXNlICd0ZXh0JzpcclxuXHRcdFx0XHRjYXNlICdlbWFpbCc6XHJcblx0XHRcdFx0XHRpbnBfdGl0bGVfdmFsdWUgPSBpbnBfdmFsdWU7XHJcblx0XHRcdFx0XHRicmVhaztcclxuXHJcblx0XHRcdFx0Y2FzZSAnc2VsZWN0JzpcclxuXHRcdFx0XHRcdGlucF90aXRsZV92YWx1ZSA9IGpRdWVyeSggZWxlbWVudCApLmZpbmQoICdvcHRpb246c2VsZWN0ZWQnICkudGV4dCgpO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblxyXG5cdFx0XHRcdGNhc2UgJ3JhZGlvJzpcclxuXHRcdFx0XHRjYXNlICdjaGVja2JveCc6XHJcblx0XHRcdFx0XHRpZiAoIGpRdWVyeSggZWxlbWVudCApLmlzKCAnOmNoZWNrZWQnICkgKSB7XHJcblx0XHRcdFx0XHRcdHZhciBsYWJlbF9lbGVtZW50ID0galF1ZXJ5KCBlbGVtZW50ICkucGFyZW50cyggJy53cGRldi1saXN0LWl0ZW0nICkuZmluZCggJy53cGRldi1saXN0LWl0ZW0tbGFiZWwnICk7XHJcblx0XHRcdFx0XHRcdGlmICggbGFiZWxfZWxlbWVudC5sZW5ndGggKSB7XHJcblx0XHRcdFx0XHRcdFx0aW5wX3RpdGxlX3ZhbHVlID0gbGFiZWxfZWxlbWVudC5odG1sKCk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cclxuXHRcdFx0XHRkZWZhdWx0OlxyXG5cdFx0XHRcdFx0aW5wX3RpdGxlX3ZhbHVlID0gaW5wX3ZhbHVlO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdC8vIE11bHRpcGxlIHNlbGVjdCB2YWx1ZSBleHRyYWN0aW9uLlxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdGlmICggKCBlbGVtZW50LnR5cGUgPT09ICdzZWxlY3Rib3gtbXVsdGlwbGUnICkgfHwgKCBlbGVtZW50LnR5cGUgPT09ICdzZWxlY3QtbXVsdGlwbGUnICkgKSB7XHJcblx0XHRcdFx0aW5wX3ZhbHVlID0galF1ZXJ5KCAnW25hbWU9XCInICsgZWxlbWVudC5uYW1lICsgJ1wiXScgKS52YWwoKTtcclxuXHRcdFx0XHRpZiAoICggaW5wX3ZhbHVlID09PSBudWxsICkgfHwgKCBTdHJpbmcoIGlucF92YWx1ZSApID09PSAnJyApICkge1xyXG5cdFx0XHRcdFx0aW5wX3ZhbHVlID0gJyc7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdC8vIE1ha2UgdmFsaWRhdGlvbiBvbmx5IGZvciB2aXNpYmxlIGVsZW1lbnRzLlxyXG5cdFx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcdGlmICggalF1ZXJ5KCBlbGVtZW50ICkuaXMoICc6dmlzaWJsZScgKSApIHsgLy8gRml4SW46IDcuMi4xLjEyLjIuXHJcblxyXG5cdFx0XHRcdC8vIFJlY2hlY2sgbWF4IGF2YWlsYWJsZSB2aXNpdG9ycyBzZWxlY3Rpb24uXHJcblx0XHRcdFx0aWYgKCB0eXBlb2Ygd3BiY19faXNfbGVzc190aGFuX3JlcXVpcmVkX19vZl9tYXhfYXZhaWxhYmxlX3Nsb3RzX19ibCA9PT0gJ2Z1bmN0aW9uJyApIHtcclxuXHRcdFx0XHRcdGlmICggd3BiY19faXNfbGVzc190aGFuX3JlcXVpcmVkX19vZl9tYXhfYXZhaWxhYmxlX3Nsb3RzX19ibCggcmVzb3VyY2VfaWQsIGVsZW1lbnQgKSApIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gUmVxdWlyZWQgZmllbGRzLlxyXG5cdFx0XHRcdGlmICggZWxlbWVudC5jbGFzc05hbWUuaW5kZXhPZiggJ3dwZGV2LXZhbGlkYXRlcy1hcy1yZXF1aXJlZCcgKSAhPT0gLTEgKSB7XHJcblxyXG5cdFx0XHRcdFx0aWYgKCAoIGVsZW1lbnQudHlwZSA9PT0gJ2NoZWNrYm94JyApICYmICggZWxlbWVudC5jaGVja2VkID09PSBmYWxzZSApICkge1xyXG5cclxuXHRcdFx0XHRcdFx0aWYgKCAhIGpRdWVyeSggJzpjaGVja2JveFtuYW1lPVwiJyArIGVsZW1lbnQubmFtZSArICdcIl0nLCBzdWJtaXRfZm9ybSApLmlzKCAnOmNoZWNrZWQnICkgKSB7XHJcblx0XHRcdFx0XHRcdFx0d3BiY19mcm9udF9lbmRfX3Nob3dfbWVzc2FnZV9fd2FybmluZyggZWxlbWVudCwgX3dwYmMuZ2V0X21lc3NhZ2UoICdtZXNzYWdlX2NoZWNrX3JlcXVpcmVkX2Zvcl9jaGVja19ib3gnICkgKTsgLy8gRml4SW46IDguNS4xLjMuXHJcblx0XHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdFx0aWYgKCBlbGVtZW50LnR5cGUgPT09ICdyYWRpbycgKSB7XHJcblxyXG5cdFx0XHRcdFx0XHRpZiAoICEgalF1ZXJ5KCAnOnJhZGlvW25hbWU9XCInICsgZWxlbWVudC5uYW1lICsgJ1wiXScsIHN1Ym1pdF9mb3JtICkuaXMoICc6Y2hlY2tlZCcgKSApIHtcclxuXHRcdFx0XHRcdFx0XHR3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlX193YXJuaW5nKCBlbGVtZW50LCBfd3BiYy5nZXRfbWVzc2FnZSggJ21lc3NhZ2VfY2hlY2tfcmVxdWlyZWRfZm9yX3JhZGlvX2JveCcgKSApOyAvLyBGaXhJbjogOC41LjEuMy5cclxuXHRcdFx0XHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRpZiAoICggZWxlbWVudC50eXBlICE9PSAnY2hlY2tib3gnICkgJiYgKCBlbGVtZW50LnR5cGUgIT09ICdyYWRpbycgKSAmJiAoICcnID09PSB3cGJjX3RyaW0oIGlucF92YWx1ZSApICkgKSB7XHJcblx0XHRcdFx0XHRcdHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2VfX3dhcm5pbmcoIGVsZW1lbnQsIF93cGJjLmdldF9tZXNzYWdlKCAnbWVzc2FnZV9jaGVja19yZXF1aXJlZCcgKSApOyAvLyBGaXhJbjogOC41LjEuMy5cclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8gRW1haWwgZm9ybWF0IHZhbGlkYXRpb24uXHJcblx0XHRcdFx0aWYgKCBlbGVtZW50LmNsYXNzTmFtZS5pbmRleE9mKCAnd3BkZXYtdmFsaWRhdGVzLWFzLWVtYWlsJyApICE9PSAtMSApIHtcclxuXHJcblx0XHRcdFx0XHRpbnBfdmFsdWUgPSBTdHJpbmcoIGlucF92YWx1ZSApLnJlcGxhY2UoIC9eXFxzK3xcXHMrJC9nbSwgJycgKTsgLy8gVHJpbSB3aGl0ZSBzcGFjZS4gRml4SW46IDUuNC41LlxyXG5cdFx0XHRcdFx0dmFyIHJlZ19lbWFpbCA9IC9eKFtBLVphLXowLTlfXFwtXFwuXFwrXSkrXFxAKFtBLVphLXowLTlfXFwtXFwuXSkrXFwuKFtBLVphLXpdezIsfSkkLztcclxuXHJcblx0XHRcdFx0XHRpZiAoIGlucF92YWx1ZSAhPT0gJycgKSB7XHJcblx0XHRcdFx0XHRcdGlmICggcmVnX2VtYWlsLnRlc3QoIGlucF92YWx1ZSApID09PSBmYWxzZSApIHtcclxuXHRcdFx0XHRcdFx0XHR3cGJjX2Zyb250X2VuZF9fc2hvd19tZXNzYWdlX193YXJuaW5nKCBlbGVtZW50LCBfd3BiYy5nZXRfbWVzc2FnZSggJ21lc3NhZ2VfY2hlY2tfZW1haWwnICkgKTsgLy8gRml4SW46IDguNS4xLjMuXHJcblx0XHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyBTYW1lIGVtYWlsIGZpZWxkIHZhbGlkYXRpb24gKHZlcmlmaWNhdGlvbiBmaWVsZCkuXHJcblx0XHRcdFx0aWYgKCAoIGVsZW1lbnQuY2xhc3NOYW1lLmluZGV4T2YoICd3cGRldi12YWxpZGF0ZXMtYXMtZW1haWwnICkgIT09IC0xICkgJiYgKCBlbGVtZW50LmNsYXNzTmFtZS5pbmRleE9mKCAnc2FtZV9hc18nICkgIT09IC0xICkgKSB7XHJcblxyXG5cdFx0XHRcdFx0dmFyIHByaW1hcnlfZW1haWxfbmFtZSA9IGVsZW1lbnQuY2xhc3NOYW1lLm1hdGNoKCAvc2FtZV9hc18oW15cXHNdKSsvZ2kgKTtcclxuXHJcblx0XHRcdFx0XHRpZiAoIHByaW1hcnlfZW1haWxfbmFtZSAhPT0gbnVsbCApIHtcclxuXHJcblx0XHRcdFx0XHRcdHByaW1hcnlfZW1haWxfbmFtZSA9IHByaW1hcnlfZW1haWxfbmFtZVsgMCBdLnN1YnN0ciggOCApO1xyXG5cclxuXHRcdFx0XHRcdFx0aWYgKCBqUXVlcnkoICdbbmFtZT1cIicgKyBwcmltYXJ5X2VtYWlsX25hbWUgKyByZXNvdXJjZV9pZCArICdcIl0nICkubGVuZ3RoID4gMCApIHtcclxuXHJcblx0XHRcdFx0XHRcdFx0aWYgKCBqUXVlcnkoICdbbmFtZT1cIicgKyBwcmltYXJ5X2VtYWlsX25hbWUgKyByZXNvdXJjZV9pZCArICdcIl0nICkudmFsKCkgIT09IGlucF92YWx1ZSApIHtcclxuXHRcdFx0XHRcdFx0XHRcdHdwYmNfZnJvbnRfZW5kX19zaG93X21lc3NhZ2VfX3dhcm5pbmcoIGVsZW1lbnQsIF93cGJjLmdldF9tZXNzYWdlKCAnbWVzc2FnZV9jaGVja19zYW1lX2VtYWlsJyApICk7IC8vIEZpeEluOiA4LjUuMS4zLlxyXG5cdFx0XHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIFNraXAgb25lIGxvb3AgZm9yIHRoZSBlbWFpbCB2ZXJpZmljYXRpb24gZmllbGQuXHJcblx0XHRcdFx0XHRjb250aW51ZTsgLy8gRml4SW46IDguMS4yLjE1LlxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHQvLyBHZXQgRm9ybSBEYXRhIChsZWdhY3kgZm9ybWF0KS5cclxuXHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHRpZiAoIGVsZW1lbnQubmFtZSAhPT0gKCAnY2FwdGNoYV9pbnB1dCcgKyByZXNvdXJjZV9pZCApICkge1xyXG5cclxuXHRcdFx0XHRpZiAoIGZvcm1kYXRhICE9PSAnJyApIHtcclxuXHRcdFx0XHRcdGZvcm1kYXRhICs9ICd+JztcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdGVsX3R5cGUgPSBlbGVtZW50LnR5cGU7XHJcblxyXG5cdFx0XHRcdGlmICggZWxlbWVudC5jbGFzc05hbWUuaW5kZXhPZiggJ3dwZGV2LXZhbGlkYXRlcy1hcy1lbWFpbCcgKSAhPT0gLTEgKSB7XHJcblx0XHRcdFx0XHRlbF90eXBlID0gJ2VtYWlsJztcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0aWYgKCBlbGVtZW50LmNsYXNzTmFtZS5pbmRleE9mKCAnd3BkZXYtdmFsaWRhdGVzLWFzLWNvdXBvbicgKSAhPT0gLTEgKSB7XHJcblx0XHRcdFx0XHRlbF90eXBlID0gJ2NvdXBvbic7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRpbnBfdmFsdWUgPSB3cGJjX2VzY2FwZV9zZXJpYWxpemVkX3ZhbHVlKCBpbnBfdmFsdWUgKTtcclxuXHJcblx0XHRcdFx0aWYgKCBlbF90eXBlID09PSAnc2VsZWN0LW9uZScgKSB7XHJcblx0XHRcdFx0XHRlbF90eXBlID0gJ3NlbGVjdGJveC1vbmUnO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRpZiAoIGVsX3R5cGUgPT09ICdzZWxlY3QtbXVsdGlwbGUnICkge1xyXG5cdFx0XHRcdFx0ZWxfdHlwZSA9ICdzZWxlY3Rib3gtbXVsdGlwbGUnO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Zm9ybWRhdGEgKz0gZWxfdHlwZSArICdeJyArIGVsZW1lbnQubmFtZSArICdeJyArIGlucF92YWx1ZTtcclxuXHJcblx0XHRcdFx0Ly8gQWRkIHRpdGxlL2xhYmVsIHZhbHVlIChsZWdhY3kpLlxyXG5cdFx0XHRcdHZhciBjbGVhbl9maWVsZF9uYW1lID0gU3RyaW5nKCBlbGVtZW50Lm5hbWUgKTtcclxuXHJcblx0XHRcdFx0Ly8gQlVHRklYOiByZXBsYWNlQWxsKFJlZ0V4cCkgaXMgbm90IHN1cHBvcnRlZCBpbiBvbGRlciBicm93c2Vycy5cclxuXHRcdFx0XHQvLyBLZWVwIGxlZ2FjeSBpbnRlbnQ6IHJlbW92ZSBbXSBzdWZmaXggb2NjdXJyZW5jZXMuXHJcblx0XHRcdFx0Y2xlYW5fZmllbGRfbmFtZSA9IGNsZWFuX2ZpZWxkX25hbWUucmVwbGFjZSggL1xcW1xcXS9naSwgJycgKTtcclxuXHJcblx0XHRcdFx0dmFyIHJlc291cmNlX2lkX3N0ciA9IFN0cmluZyggcmVzb3VyY2VfaWQgKTtcclxuXHJcblx0XHRcdFx0Ly8gTGVnYWN5IGFzc3VtZWQgc3VmZml4IGVuZHMgd2l0aCByZXNvdXJjZV9pZCwgbWFrZSBpdCBzYWZlLlxyXG5cdFx0XHRcdGlmIChcclxuXHRcdFx0XHRcdCggY2xlYW5fZmllbGRfbmFtZS5sZW5ndGggPj0gcmVzb3VyY2VfaWRfc3RyLmxlbmd0aCApICYmXHJcblx0XHRcdFx0XHQoIGNsZWFuX2ZpZWxkX25hbWUuc3Vic3RyKCBjbGVhbl9maWVsZF9uYW1lLmxlbmd0aCAtIHJlc291cmNlX2lkX3N0ci5sZW5ndGggKSA9PT0gcmVzb3VyY2VfaWRfc3RyIClcclxuXHRcdFx0XHQpIHtcclxuXHRcdFx0XHRcdGNsZWFuX2ZpZWxkX25hbWUgPSBjbGVhbl9maWVsZF9uYW1lLnN1YnN0ciggMCwgY2xlYW5fZmllbGRfbmFtZS5sZW5ndGggLSByZXNvdXJjZV9pZF9zdHIubGVuZ3RoICk7XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRmb3JtZGF0YSArPSAnficgKyBlbF90eXBlICsgJ14nICsgY2xlYW5fZmllbGRfbmFtZSArICdfdmFsJyArIHJlc291cmNlX2lkICsgJ14nICsgaW5wX3RpdGxlX3ZhbHVlO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvLyBUT0RPOiBoZXJlIHdhcyBmdW5jdGlvbiBmb3IgJ0NoZWNrIGlmIHZpc2l0b3IgZmluaXNoIGRhdGVzIHNlbGVjdGlvbi5cclxuXHJcblx0Ly8gQ2FwdGNoYSB2ZXJpZnkuXHJcblx0dmFyIGNhcHRjaGEgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ3dwZGV2X2NhcHRjaGFfY2hhbGxlbmdlXycgKyByZXNvdXJjZV9pZCApO1xyXG5cclxuXHRpZiAoIGNhcHRjaGEgIT09IG51bGwgKSB7XHJcblx0XHR3cGJjX2Zvcm1fc3VibWl0X3NlbmQoIHJlc291cmNlX2lkLCBmb3JtZGF0YSwgY2FwdGNoYS52YWx1ZSwgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdjYXB0Y2hhX2lucHV0JyArIHJlc291cmNlX2lkICkudmFsdWUsIHdwZGV2X2FjdGl2ZV9sb2NhbGUgKTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0d3BiY19mb3JtX3N1Ym1pdF9zZW5kKCByZXNvdXJjZV9pZCwgZm9ybWRhdGEsICcnLCAnJywgd3BkZXZfYWN0aXZlX2xvY2FsZSApO1xyXG5cdH1cclxuXHJcblx0cmV0dXJuO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIEdhdGhlcmluZyBwYXJhbXMgZm9yIHNlbmRpbmcgQWpheCByZXF1ZXN0IGFuZCB0aGVuIHNlbmQgaXQgKGxlZ2FjeTogZm9ybV9zdWJtaXRfc2VuZCkuXHJcbiAqXHJcbiAqIEBwYXJhbSB7bnVtYmVyfHN0cmluZ30gcmVzb3VyY2VfaWRcclxuICogQHBhcmFtIHtzdHJpbmd9ICAgICAgICBmb3JtZGF0YVxyXG4gKiBAcGFyYW0ge3N0cmluZ30gICAgICAgIGNhcHRjaGFfY2hhbGFuZ2VcclxuICogQHBhcmFtIHtzdHJpbmd9ICAgICAgICB1c2VyX2NhcHRjaGFcclxuICogQHBhcmFtIHtzdHJpbmd9ICAgICAgICB3cGRldl9hY3RpdmVfbG9jYWxlXHJcbiAqXHJcbiAqIEByZXR1cm4ge3VuZGVmaW5lZH0gTGVnYWN5IGJlaGF2aW9yLlxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19mb3JtX3N1Ym1pdF9zZW5kKCByZXNvdXJjZV9pZCwgZm9ybWRhdGEsIGNhcHRjaGFfY2hhbGFuZ2UsIHVzZXJfY2FwdGNoYSwgd3BkZXZfYWN0aXZlX2xvY2FsZSApIHtcclxuXHJcblx0cmVzb3VyY2VfaWQgPSBwYXJzZUludCggcmVzb3VyY2VfaWQsIDEwICk7XHJcblxyXG5cdHZhciBteV9ib29raW5nX2Zvcm0gPSAnJztcclxuXHR2YXIgYm9va2luZ19mb3JtX3R5cGVfZWwgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggJ2Jvb2tpbmdfZm9ybV90eXBlJyArIHJlc291cmNlX2lkICk7XHJcblx0aWYgKCBib29raW5nX2Zvcm1fdHlwZV9lbCAhPT0gbnVsbCApIHtcclxuXHRcdG15X2Jvb2tpbmdfZm9ybSA9IGJvb2tpbmdfZm9ybV90eXBlX2VsLnZhbHVlO1xyXG5cdH1cclxuXHJcblx0dmFyIG15X2Jvb2tpbmdfaGFzaCA9ICcnO1xyXG5cdGlmICggX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAndGhpc19wYWdlX2Jvb2tpbmdfaGFzaCcgKSAhPT0gJycgKSB7XHJcblx0XHRteV9ib29raW5nX2hhc2ggPSBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0aGlzX3BhZ2VfYm9va2luZ19oYXNoJyApO1xyXG5cdH1cclxuXHJcblx0dmFyIGlzX3NlbmRfZW1laWxzID0gMTtcclxuXHRpZiAoIGpRdWVyeSggJyNpc19zZW5kX2VtYWlsX2Zvcl9wZW5kaW5nJyApLmxlbmd0aCApIHsgLy8gRml4SW46IDguNy45LjUuXHJcblxyXG5cdFx0aXNfc2VuZF9lbWVpbHMgPSBqUXVlcnkoICcjaXNfc2VuZF9lbWFpbF9mb3JfcGVuZGluZycgKS5pcyggJzpjaGVja2VkJyApO1xyXG5cclxuXHRcdGlmICggZmFsc2UgPT09IGlzX3NlbmRfZW1laWxzICkge1xyXG5cdFx0XHRpc19zZW5kX2VtZWlscyA9IDA7XHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHRpc19zZW5kX2VtZWlscyA9IDE7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHR2YXIgZGF0ZV9lbCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnZGF0ZV9ib29raW5nJyArIHJlc291cmNlX2lkICk7XHJcblx0dmFyIGRhdGVfdmFsdWUgPSAoIGRhdGVfZWwgKSA/IGRhdGVfZWwudmFsdWUgOiAnJztcclxuXHJcblx0aWYgKCAnJyAhPT0gZGF0ZV92YWx1ZSApIHsgLy8gRml4SW46IDYuMS4xLjMuXHJcblx0XHR3cGJjX3NlbmRfYWpheF9zdWJtaXQoIHJlc291cmNlX2lkLCBmb3JtZGF0YSwgY2FwdGNoYV9jaGFsYW5nZSwgdXNlcl9jYXB0Y2hhLCBpc19zZW5kX2VtZWlscywgbXlfYm9va2luZ19oYXNoLCBteV9ib29raW5nX2Zvcm0sIHdwZGV2X2FjdGl2ZV9sb2NhbGUgKTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0alF1ZXJ5KCAnI2Jvb2tpbmdfZm9ybV9kaXYnICsgcmVzb3VyY2VfaWQgKS5oaWRlKCk7XHJcblx0XHRqUXVlcnkoICcjc3VibWl0aW5nJyArIHJlc291cmNlX2lkICkuaGlkZSgpO1xyXG5cdH1cclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEFkZGl0aW9uYWwgY2FsZW5kYXJzIHN1Ym1pdC5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIGFkZGl0aW9uYWxfY2FsZW5kYXJzX2VsID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdhZGRpdGlvbmFsX2NhbGVuZGFycycgKyByZXNvdXJjZV9pZCApO1xyXG5cdGlmICggYWRkaXRpb25hbF9jYWxlbmRhcnNfZWwgPT09IG51bGwgKSB7XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG5cclxuXHR2YXIgaWRfYWRkaXRpb25hbF9zdHIgPSBhZGRpdGlvbmFsX2NhbGVuZGFyc19lbC52YWx1ZTtcclxuXHR2YXIgaWRfYWRkaXRpb25hbF9hcnIgPSBpZF9hZGRpdGlvbmFsX3N0ci5zcGxpdCggJywnICk7XHJcblxyXG5cdC8vIEZpeEluOiAxMC45LjQuMS5cclxuXHRmb3IgKCB2YXIgaWEgPSAwOyBpYSA8IGlkX2FkZGl0aW9uYWxfYXJyLmxlbmd0aDsgaWErKyApIHtcclxuXHRcdGlkX2FkZGl0aW9uYWxfYXJyWyBpYSBdID0gcGFyc2VJbnQoIGlkX2FkZGl0aW9uYWxfYXJyWyBpYSBdLCAxMCApO1xyXG5cdH1cclxuXHJcblx0aWYgKCAhIGpRdWVyeSggJyNib29raW5nX2Zvcm1fZGl2JyArIHJlc291cmNlX2lkICkuaXMoICc6dmlzaWJsZScgKSApIHtcclxuXHRcdHdwYmNfYm9va2luZ19mb3JtX19zcGluX2xvYWRlcl9fc2hvdyggcmVzb3VyY2VfaWQgKTsgLy8gU2hvdyBTcGlubmVyXHJcblx0fVxyXG5cclxuXHQvLyBIZWxwZXI6IHJld3JpdGUgZmllbGQgbmFtZSBzdWZmaXggZnJvbSByZXNvdXJjZV9pZCAtPiBpZF9hZGRpdGlvbmFsLlxyXG5cdGZ1bmN0aW9uIHdwYmNfcmV3cml0ZV9maWVsZF9uYW1lX3N1ZmZpeCggZmllbGRfbmFtZSwgb2xkX2lkLCBuZXdfaWQgKSB7XHJcblxyXG5cdFx0ZmllbGRfbmFtZSA9IFN0cmluZyggZmllbGRfbmFtZSApO1xyXG5cclxuXHRcdHZhciBvbGRfaWRfc3RyID0gU3RyaW5nKCBvbGRfaWQgKTtcclxuXHRcdHZhciBuZXdfaWRfc3RyID0gU3RyaW5nKCBuZXdfaWQgKTtcclxuXHJcblx0XHQvLyBIYW5kbGUgZmllbGRzIHdpdGggW10uXHJcblx0XHRpZiAoXHJcblx0XHRcdCggZmllbGRfbmFtZS5sZW5ndGggPj0gKCBvbGRfaWRfc3RyLmxlbmd0aCArIDIgKSApICYmXHJcblx0XHRcdCggZmllbGRfbmFtZS5zdWJzdHIoIGZpZWxkX25hbWUubGVuZ3RoIC0gKCBvbGRfaWRfc3RyLmxlbmd0aCArIDIgKSApID09PSAoIG9sZF9pZF9zdHIgKyAnW10nICkgKVxyXG5cdFx0KSB7XHJcblx0XHRcdHJldHVybiBmaWVsZF9uYW1lLnN1YnN0ciggMCwgZmllbGRfbmFtZS5sZW5ndGggLSAoIG9sZF9pZF9zdHIubGVuZ3RoICsgMiApICkgKyBuZXdfaWRfc3RyICsgJ1tdJztcclxuXHRcdH1cclxuXHJcblx0XHQvLyBIYW5kbGUgZmllbGRzIHdpdGhvdXQgW10uXHJcblx0XHRpZiAoXHJcblx0XHRcdCggZmllbGRfbmFtZS5sZW5ndGggPj0gb2xkX2lkX3N0ci5sZW5ndGggKSAmJlxyXG5cdFx0XHQoIGZpZWxkX25hbWUuc3Vic3RyKCBmaWVsZF9uYW1lLmxlbmd0aCAtIG9sZF9pZF9zdHIubGVuZ3RoICkgPT09IG9sZF9pZF9zdHIgKVxyXG5cdFx0KSB7XHJcblx0XHRcdHJldHVybiBmaWVsZF9uYW1lLnN1YnN0ciggMCwgZmllbGRfbmFtZS5sZW5ndGggLSBvbGRfaWRfc3RyLmxlbmd0aCApICsgbmV3X2lkX3N0cjtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBGYWxsYmFjazogcmV0dXJuIHVuY2hhbmdlZCAoc2FmZXIgdGhhbiBicmVha2luZyBuYW1lKS5cclxuXHRcdHJldHVybiBmaWVsZF9uYW1lO1xyXG5cdH1cclxuXHJcblx0Zm9yICggaWEgPSAwOyBpYSA8IGlkX2FkZGl0aW9uYWxfYXJyLmxlbmd0aDsgaWErKyApIHtcclxuXHJcblx0XHR2YXIgaWRfYWRkaXRpb25hbCA9IGlkX2FkZGl0aW9uYWxfYXJyWyBpYSBdO1xyXG5cclxuXHRcdC8vIEZpeEluOiAxMC45LjQuMS5cclxuXHRcdGlmICggaWRfYWRkaXRpb25hbCA8PSAwICkge1xyXG5cdFx0XHRjb250aW51ZTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBSZWJ1aWxkIGZvcm1kYXRhIGZvciBlYWNoIGFkZGl0aW9uYWwgY2FsZW5kYXIgKGxlZ2FjeSBiZWhhdmlvcikuXHJcblx0XHR2YXIgZm9ybWRhdGFfYWRkaXRpb25hbF9hcnIgPSBTdHJpbmcoIGZvcm1kYXRhICkuc3BsaXQoICd+JyApO1xyXG5cdFx0dmFyIGZvcm1kYXRhX2FkZGl0aW9uYWwgPSAnJztcclxuXHJcblx0XHRmb3IgKCB2YXIgaiA9IDA7IGogPCBmb3JtZGF0YV9hZGRpdGlvbmFsX2Fyci5sZW5ndGg7IGorKyApIHtcclxuXHJcblx0XHRcdHZhciBteV9mb3JtX2ZpZWxkID0gZm9ybWRhdGFfYWRkaXRpb25hbF9hcnJbIGogXS5zcGxpdCggJ14nICk7XHJcblxyXG5cdFx0XHRpZiAoIGZvcm1kYXRhX2FkZGl0aW9uYWwgIT09ICcnICkge1xyXG5cdFx0XHRcdGZvcm1kYXRhX2FkZGl0aW9uYWwgKz0gJ34nO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHQvLyBTYWZldHk6IGVuc3VyZSB3ZSBoYXZlIGF0IGxlYXN0IHR5cGUgXiBuYW1lIF4gdmFsdWUuXHJcblx0XHRcdGlmICggbXlfZm9ybV9maWVsZC5sZW5ndGggPCAzICkge1xyXG5cdFx0XHRcdGZvcm1kYXRhX2FkZGl0aW9uYWwgKz0gZm9ybWRhdGFfYWRkaXRpb25hbF9hcnJbIGogXTtcclxuXHRcdFx0XHRjb250aW51ZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0bXlfZm9ybV9maWVsZFsgMSBdID0gd3BiY19yZXdyaXRlX2ZpZWxkX25hbWVfc3VmZml4KCBteV9mb3JtX2ZpZWxkWyAxIF0sIHJlc291cmNlX2lkLCBpZF9hZGRpdGlvbmFsICk7XHJcblx0XHRcdGZvcm1kYXRhX2FkZGl0aW9uYWwgKz0gbXlfZm9ybV9maWVsZFsgMCBdICsgJ14nICsgbXlfZm9ybV9maWVsZFsgMSBdICsgJ14nICsgbXlfZm9ybV9maWVsZFsgMiBdO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vIElmIHBheW1lbnQgZm9ybSBmb3IgbWFpbiBib29raW5nIHJlc291cmNlIGlzIHNob3dpbmcsIGFwcGVuZCBmb3IgYWRkaXRpb25hbCBjYWxlbmRhcnMuXHJcblx0XHRpZiAoIGpRdWVyeSggJyNnYXRld2F5X3BheW1lbnRfZm9ybXMnICsgcmVzb3VyY2VfaWQgKS5sZW5ndGggPiAwICkge1xyXG5cdFx0XHRqUXVlcnkoICcjZ2F0ZXdheV9wYXltZW50X2Zvcm1zJyArIHJlc291cmNlX2lkICkuYWZ0ZXIoICc8ZGl2IGlkPVwiZ2F0ZXdheV9wYXltZW50X2Zvcm1zJyArIGlkX2FkZGl0aW9uYWwgKyAnXCI+PC9kaXY+JyApO1xyXG5cdFx0XHRqUXVlcnkoICcjZ2F0ZXdheV9wYXltZW50X2Zvcm1zJyArIHJlc291cmNlX2lkICkuYWZ0ZXIoICc8ZGl2IGlkPVwiYWpheF9yZXNwb25kX2luc2VydCcgKyBpZF9hZGRpdGlvbmFsICsgJ1wiIHN0eWxlPVwiZGlzcGxheTpub25lO1wiPjwvZGl2PicgKTtcclxuXHRcdH1cclxuXHJcblx0XHQvLyBGaXhJbjogOC41LjIuMTcuXHJcblx0XHR3cGJjX3NlbmRfYWpheF9zdWJtaXQoIGlkX2FkZGl0aW9uYWwsIGZvcm1kYXRhX2FkZGl0aW9uYWwsIGNhcHRjaGFfY2hhbGFuZ2UsIHVzZXJfY2FwdGNoYSwgaXNfc2VuZF9lbWVpbHMsIG15X2Jvb2tpbmdfaGFzaCwgbXlfYm9va2luZ19mb3JtLCB3cGRldl9hY3RpdmVfbG9jYWxlICk7XHJcblx0fVxyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIFNlbmQgQWpheCBzdWJtaXQgKGxlZ2FjeTogc2VuZF9hamF4X3N1Ym1pdCkuXHJcbiAqXHJcbiAqIEBwYXJhbSB7bnVtYmVyfHN0cmluZ30gcmVzb3VyY2VfaWRcclxuICogQHBhcmFtIHtzdHJpbmd9ICAgICAgICBmb3JtZGF0YVxyXG4gKiBAcGFyYW0ge3N0cmluZ30gICAgICAgIGNhcHRjaGFfY2hhbGFuZ2VcclxuICogQHBhcmFtIHtzdHJpbmd9ICAgICAgICB1c2VyX2NhcHRjaGFcclxuICogQHBhcmFtIHtudW1iZXJ9ICAgICAgICBpc19zZW5kX2VtZWlsc1xyXG4gKiBAcGFyYW0ge3N0cmluZ30gICAgICAgIG15X2Jvb2tpbmdfaGFzaFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gICAgICAgIG15X2Jvb2tpbmdfZm9ybVxyXG4gKiBAcGFyYW0ge3N0cmluZ30gICAgICAgIHdwZGV2X2FjdGl2ZV9sb2NhbGVcclxuICpcclxuICogQHJldHVybiB7dW5kZWZpbmVkfSBMZWdhY3kgYmVoYXZpb3IuXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX3NlbmRfYWpheF9zdWJtaXQocmVzb3VyY2VfaWQsIGZvcm1kYXRhLCBjYXB0Y2hhX2NoYWxhbmdlLCB1c2VyX2NhcHRjaGEsIGlzX3NlbmRfZW1laWxzLCBteV9ib29raW5nX2hhc2gsIG15X2Jvb2tpbmdfZm9ybSwgd3BkZXZfYWN0aXZlX2xvY2FsZSkge1xyXG5cclxuXHRyZXNvdXJjZV9pZCA9IHBhcnNlSW50KCByZXNvdXJjZV9pZCwgMTAgKTtcclxuXHJcblx0Ly8gRGlzYWJsZSBTdWJtaXQgfCBTaG93IHNwaW4gbG9hZGVyLlxyXG5cdHdwYmNfYm9va2luZ19mb3JtX19vbl9zdWJtaXRfX3VpX2VsZW1lbnRzX2Rpc2FibGUoIHJlc291cmNlX2lkICk7XHJcblxyXG5cdC8vIEZpeEluOiAyMDI2LTAyLTA1IC0gcGFzcyBwcmV2aWV3IGNvbnRleHQgdG8gYm9va2luZyBjcmVhdGUgQWpheC5cclxuXHR2YXIgZm9ybV9zdGF0dXMgID0gd3BiY19fZ2V0X2Zvcm1fc3RhdHVzX2Zvcl9zdWJtaXQoIHJlc291cmNlX2lkICk7XHJcblx0dmFyIHByZXZpZXdfYXJncyA9IChmb3JtX3N0YXR1cyA9PT0gJ3ByZXZpZXcnKSA/IHdwYmNfX2dldF9iZmJfcHJldmlld19hcmdzX2Zyb21fbG9jYXRpb24oKSA6IG51bGw7XHJcblxyXG5cdHZhciByZXF1ZXN0X3BhcmFtcyA9IHtcclxuXHRcdCdyZXNvdXJjZV9pZCcgICAgICAgICAgICAgIDogcmVzb3VyY2VfaWQsXHJcblx0XHQnZGF0ZXNfZGRtbXl5X2NzdicgICAgICAgICA6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCAnZGF0ZV9ib29raW5nJyArIHJlc291cmNlX2lkICkudmFsdWUsXHJcblx0XHQnZm9ybWRhdGEnICAgICAgICAgICAgICAgICA6IGZvcm1kYXRhLFxyXG5cdFx0J2Jvb2tpbmdfaGFzaCcgICAgICAgICAgICAgOiBteV9ib29raW5nX2hhc2gsXHJcblx0XHQnY3VzdG9tX2Zvcm0nICAgICAgICAgICAgICA6IG15X2Jvb2tpbmdfZm9ybSxcclxuXHRcdCdhZ2dyZWdhdGVfcmVzb3VyY2VfaWRfYXJyJzogKCAoIG51bGwgIT09IF93cGJjLmJvb2tpbmdfX2dldF9wYXJhbV92YWx1ZSggcmVzb3VyY2VfaWQsICdhZ2dyZWdhdGVfcmVzb3VyY2VfaWRfYXJyJyApICkgPyBfd3BiYy5ib29raW5nX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnYWdncmVnYXRlX3Jlc291cmNlX2lkX2FycicgKS5qb2luKCAnLCcgKSA6ICcnICksXHJcblx0XHQnY2FwdGNoYV9jaGFsYW5nZScgICAgICAgICA6IGNhcHRjaGFfY2hhbGFuZ2UsXHJcblx0XHQnY2FwdGNoYV91c2VyX2lucHV0JyAgICAgICA6IHVzZXJfY2FwdGNoYSxcclxuXHRcdCdpc19lbWFpbHNfc2VuZCcgICAgICAgICAgIDogaXNfc2VuZF9lbWVpbHMsXHJcblx0XHQnYWN0aXZlX2xvY2FsZScgICAgICAgICAgICA6IHdwZGV2X2FjdGl2ZV9sb2NhbGUsXHJcblx0XHQnZm9ybV9zdGF0dXMnICAgICAgICAgICAgICA6IGZvcm1fc3RhdHVzXHJcblx0fTtcclxuXHJcblx0Ly8gSWYgcHJldmlldywgcGFzcyBzZXNzaW9uIGlkZW50aWZpZXJzIHNvIFBIUCBjYW4gbG9hZCB0cmFuc2llbnQgc25hcHNob3QuXHJcblx0aWYgKCBwcmV2aWV3X2FyZ3MgJiYgcHJldmlld19hcmdzLnRva2VuICYmIHByZXZpZXdfYXJncy5mb3JtX2lkICkge1xyXG5cdFx0cmVxdWVzdF9wYXJhbXNbJ3dwYmNfYmZiX3ByZXZpZXcnXSAgICAgICAgID0gMTtcclxuXHRcdHJlcXVlc3RfcGFyYW1zWyd3cGJjX2JmYl9wcmV2aWV3X3Rva2VuJ10gICA9IHByZXZpZXdfYXJncy50b2tlbjtcclxuXHRcdHJlcXVlc3RfcGFyYW1zWyd3cGJjX2JmYl9wcmV2aWV3X2Zvcm1faWQnXSA9IHByZXZpZXdfYXJncy5mb3JtX2lkO1xyXG5cdFx0cmVxdWVzdF9wYXJhbXNbJ3dwYmNfYmZiX3ByZXZpZXdfbm9uY2UnXSAgID0gcHJldmlld19hcmdzLm5vbmNlOyAvLyBub3RlOiBVUkwgcGFyYW0gaXMgYG5vbmNlYC5cclxuXHR9XHJcblxyXG5cdHZhciBpc19leGl0ID0gd3BiY19hanhfYm9va2luZ19fY3JlYXRlKCByZXF1ZXN0X3BhcmFtcyApO1xyXG5cclxuXHRpZiAoIHRydWUgPT09IGlzX2V4aXQgKSB7XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG59XHJcblxyXG5cclxuXHJcbi8vID09IEhlbHBlciBGdW5jdGlvbnMgPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG5cclxuLyoqXHJcbiAqIFBhcnNlIHF1ZXJ5IHN0cmluZyBpbnRvIHtrZXk6dmFsdWV9IChvbGQtYnJvd3NlciBzYWZlKS5cclxuICpcclxuICogQHBhcmFtIHtzdHJpbmd9IHFzXHJcbiAqIEByZXR1cm4ge09iamVjdH1cclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfX3BhcnNlX3F1ZXJ5X3N0cmluZyhxcykge1xyXG5cdHZhciBvdXQgPSB7fTtcclxuXHRxcyAgICAgID0gKHFzIHx8ICcnKTtcclxuXHRxcyAgICAgID0gcXMucmVwbGFjZSggL15cXD8vLCAnJyApO1xyXG5cdGlmICggISBxcyApIHtcclxuXHRcdHJldHVybiBvdXQ7XHJcblx0fVxyXG5cclxuXHR2YXIgcGFydHMgPSBxcy5zcGxpdCggJyYnICk7XHJcblx0Zm9yICggdmFyIGkgPSAwOyBpIDwgcGFydHMubGVuZ3RoOyBpKysgKSB7XHJcblx0XHR2YXIga3YgPSBwYXJ0c1tpXS5zcGxpdCggJz0nICk7XHJcblx0XHR2YXIgayAgPSBkZWNvZGVVUklDb21wb25lbnQoIGt2WzBdIHx8ICcnICk7XHJcblx0XHRpZiAoICEgayApIHtcclxuXHRcdFx0Y29udGludWU7XHJcblx0XHR9XHJcblx0XHR2YXIgdiAgPSBkZWNvZGVVUklDb21wb25lbnQoIGt2LnNsaWNlKCAxICkuam9pbiggJz0nICkgfHwgJycgKTtcclxuXHRcdG91dFtrXSA9IHY7XHJcblx0fVxyXG5cdHJldHVybiBvdXQ7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBEZXRlY3QgcHJldmlldyBhcmdzIGZyb20gY3VycmVudCBVUkwgKGlmcmFtZSBVUkwpLlxyXG4gKlxyXG4gKiBAcmV0dXJuIHtPYmplY3R8bnVsbH0geyB0b2tlbiwgZm9ybV9pZCwgbm9uY2UgfSBvciBudWxsXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX19nZXRfYmZiX3ByZXZpZXdfYXJnc19mcm9tX2xvY2F0aW9uKCkge1xyXG5cdHRyeSB7XHJcblx0XHR2YXIgcCA9IHdwYmNfX3BhcnNlX3F1ZXJ5X3N0cmluZyggKHdpbmRvdy5sb2NhdGlvbiAmJiB3aW5kb3cubG9jYXRpb24uc2VhcmNoKSA/IHdpbmRvdy5sb2NhdGlvbi5zZWFyY2ggOiAnJyApO1xyXG5cclxuXHRcdGlmICggISBwLndwYmNfYmZiX3ByZXZpZXcgfHwgKHAud3BiY19iZmJfcHJldmlldyA9PT0gJzAnKSApIHtcclxuXHRcdFx0cmV0dXJuIG51bGw7XHJcblx0XHR9XHJcblxyXG5cdFx0aWYgKCAhIHAud3BiY19iZmJfcHJldmlld190b2tlbiB8fCAhIHAud3BiY19iZmJfcHJldmlld19mb3JtX2lkICkge1xyXG5cdFx0XHRyZXR1cm4gbnVsbDtcclxuXHRcdH1cclxuXHJcblx0XHRyZXR1cm4ge1xyXG5cdFx0XHR0b2tlbiAgOiBTdHJpbmcoIHAud3BiY19iZmJfcHJldmlld190b2tlbiApLFxyXG5cdFx0XHRmb3JtX2lkOiBwYXJzZUludCggcC53cGJjX2JmYl9wcmV2aWV3X2Zvcm1faWQsIDEwICkgfHwgMCxcclxuXHRcdFx0bm9uY2UgIDogKHAubm9uY2UpID8gU3RyaW5nKCBwLm5vbmNlICkgOiAnJ1xyXG5cdFx0fTtcclxuXHR9IGNhdGNoICggZSApIHtcclxuXHRcdHJldHVybiBudWxsO1xyXG5cdH1cclxufVxyXG5cclxuLyoqXHJcbiAqIFJlc29sdmUgZm9ybSBzdGF0dXMgZm9yIHN1Ym1pdC5cclxuICpcclxuICogUHJpb3JpdHk6XHJcbiAqIDEpIHNob3J0Y29kZSBwYXJhbSBleHBvc2VkIHZpYSBfd3BiYy5ib29raW5nX19nZXRfcGFyYW1fdmFsdWUoLi4uLCAnZm9ybV9zdGF0dXMnKVxyXG4gKiAyKSBkZXRlY3QgcHJldmlldyBVUkwgYXJnc1xyXG4gKiAzKSBmYWxsYmFjazogcHVibGlzaGVkXHJcbiAqXHJcbiAqIEBwYXJhbSB7bnVtYmVyfSByZXNvdXJjZV9pZFxyXG4gKiBAcmV0dXJuIHtzdHJpbmd9ICdwcmV2aWV3J3wncHVibGlzaGVkJ1xyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19fZ2V0X2Zvcm1fc3RhdHVzX2Zvcl9zdWJtaXQocmVzb3VyY2VfaWQpIHtcclxuXHJcblx0dmFyIHN0YXR1cyA9ICcnO1xyXG5cclxuXHR0cnkge1xyXG5cdFx0aWYgKCAodHlwZW9mIF93cGJjICE9PSAndW5kZWZpbmVkJykgJiYgX3dwYmMuYm9va2luZ19fZ2V0X3BhcmFtX3ZhbHVlICkge1xyXG5cdFx0XHRzdGF0dXMgPSBfd3BiYy5ib29raW5nX19nZXRfcGFyYW1fdmFsdWUoIHJlc291cmNlX2lkLCAnZm9ybV9zdGF0dXMnICk7XHJcblx0XHR9XHJcblx0fSBjYXRjaCAoIGUgKSB7fVxyXG5cclxuXHRzdGF0dXMgPSAoc3RhdHVzID09IG51bGwpID8gJycgOiBTdHJpbmcoIHN0YXR1cyApO1xyXG5cdHN0YXR1cyA9IHN0YXR1cy50b0xvd2VyQ2FzZSgpO1xyXG5cclxuXHQvLyBVUkwtYmFzZWQgZGV0ZWN0aW9uIGZvciBwcmV2aWV3IGlmcmFtZS5cclxuXHR2YXIgcHJldmlld19hcmdzID0gd3BiY19fZ2V0X2JmYl9wcmV2aWV3X2FyZ3NfZnJvbV9sb2NhdGlvbigpO1xyXG5cdGlmICggcHJldmlld19hcmdzICkge1xyXG5cdFx0cmV0dXJuICdwcmV2aWV3JztcclxuXHR9XHJcblxyXG5cdHJldHVybiAoc3RhdHVzID09PSAncHJldmlldycpID8gJ3ByZXZpZXcnIDogJ3B1Ymxpc2hlZCc7XHJcbn1cclxuXHJcblxyXG5cclxuLy8gPT0gQmFja3dhcmQtY29tcGF0aWJsZSB3cmFwcGVycyAoa2VlcCBvbGQgZ2xvYmFsIG5hbWVzIHdvcmtpbmcgMTAwJSBhcyBiZWZvcmUpLiA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbmZ1bmN0aW9uIG15Ym9va2luZ19zdWJtaXQoIHN1Ym1pdF9mb3JtLCByZXNvdXJjZV9pZCwgd3BkZXZfYWN0aXZlX2xvY2FsZSApIHtcclxuXHRyZXR1cm4gd3BiY19ib29raW5nX2Zvcm1fc3VibWl0KCBzdWJtaXRfZm9ybSwgcmVzb3VyY2VfaWQsIHdwZGV2X2FjdGl2ZV9sb2NhbGUgKTtcclxufVxyXG4iLCJ0cnkge1xyXG5cdHZhciBldiA9ICh0eXBlb2YgQ3VzdG9tRXZlbnQgPT09ICdmdW5jdGlvbicpID8gbmV3IEN1c3RvbUV2ZW50KCAnd3BiYy1yZWFkeScgKSA6IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCAnRXZlbnQnICk7XHJcblx0aWYgKCBldi5pbml0RXZlbnQgKSB7XHJcblx0XHRldi5pbml0RXZlbnQoICd3cGJjLXJlYWR5JywgdHJ1ZSwgdHJ1ZSApO1xyXG5cdH1cclxuXHRkb2N1bWVudC5kaXNwYXRjaEV2ZW50KCBldiApO1xyXG5cdGNvbnNvbGUubG9nKCAnd3BiYy1yZWFkeScgKTtcclxufSBjYXRjaCAoIGUgKSB7XHJcblx0Y29uc29sZS5lcnJvciggXCJXUEJDIGV2ZW50ICd3cGJjLXJlYWR5JyBmYWlsZWQhXCIsIGUgKTtcclxufVxyXG4iXX0=
