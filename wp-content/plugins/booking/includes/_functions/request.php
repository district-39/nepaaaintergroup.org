<?php /**
 * @version 1.0
 * @description Universal class for REQUEST params cleaning (sanitizing) and saving such data, relative to current WP user.
 * @category  WPBC_AJX__REQUEST Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2022-10-29
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly            // FixIn: 9.3.1.2.


/**
 *  Class for sanitizing $_REQUEST parameters and saving or getting it in/from  DB
 *
 *
 * Example 1:
 *
       	$user_request = new WPBC_AJX__REQUEST( array(
												   'db_option_name'          => 'booking_availability_request_params',
												   'user_id'                 => wpbc_get_current_user_id(),
												   'request_rules_structure' => WPBC_AJX__Availability::request_rules_structure()
	       										)
	   					);
		$escaped_request_params_arr = $user_request->get_sanitized__saved__user_request_params();		// Get Saved

	    if ( false === $escaped_request_params_arr ) {			// This request was not saved before, then get sanitized direct parameters, like: 	$_REQUEST['resource_id']

			$request_prefix = false;
		    $escaped_request_params_arr = $user_request->get_sanitized__in_request__value_or_default( $request_prefix  );		 		// Direct: 	$_REQUEST['resource_id']
		}
 *
 *
 * Example 2:
 *
        $user_request = new WPBC_AJX__REQUEST( array(
												    'db_option_name'          => 'booking_availability_request_params',
												    'user_id'                 => wpbc_get_current_user_id(),
												    'request_rules_structure' => array(
			                                                                           'resource_id'                   => array( 'validate' => 'digit_or_csd',  	'default' => 1 )
																					 , 'ui_wh_booking_date_checkout'   => array( 'validate' => 'digit_or_date',  	'default' => '' )		                    // number | date 2016-07-20
																					 , 'wh_modification_date' 			=> array( 'validate' => 'array',  	        'default' => array( '3' ) )		                    // number | date 2016-07-20
																					 , 'ui_wh_modification_date_radio' => array( 'validate' => 'd',  	            'default' => 0 )		                    // '1' | '2' ....
																					 , 'keyword'     			        => array( 'validate' => 's',  	            'default' => '' )			                //string
																					 , 'wh_cost'  		                => array( 'validate' => 'float_or_empty',  	'default' => '' )	        // '1' | ''
																					 , 'ui_usr__send_emails'           => array( 'validate' => array( 'send', 'not_send' ),  'default' => 'send' )
																				 )
                         ) );
 *
 *
 * OR ******************************************************************************************************************
 *      $user_request = new WPBC_AJX__REQUEST();
 *
 *      $user_request->set_db_option_name__for_saved_request( 'booking_availability_request_params' );
 *
 *      $user_request->set_user_id( wpbc_get_current_user_id() );
 *
 *          $user_request->set_default_request_structure(  array(
 *                                                          'resource_id' => array( 'validate' => 'digit_or_csd',  'default' => array( '1' ) )
 *                                                  )  );
 *      OR:
 *          $user_request->set_default_request_structure(  WPBC_AJX__Availability::get__request_params__names_default()  );
 *
 ***********************************************************************************************************************
 *
 *     $params_arr = $user_request->get_sanitized__saved__user_request_params();                                        // Initial Load - get params SAVED in DB - user open page with same view as before
 * OR
 *     $params_arr = $user_request->get_sanitized__in_request__value_or_default( false  );                              // Direct  -  $_REQUEST['resource_id']
 * OR
 *     $params_arr = $user_request->get_sanitized__in_request__value_or_default( 'search_params' );                     // In Ajax request   -  $_REQUEST['search_params']['resource_id']
 *
 *
 ***********************************************************************************************************************
 *  Request Rules Structure
 ***********************************************************************************************************************
 *
  array(
		 , 'page_num'  			            => array( 'validate' => 'd',  	            'default' => 1 )				// 1 | 0
		 , 'cost'  			                => array( 'validate' => 'f',  	            'default' => 1.5 )				// 1.5 | 0
		 , 'wh_cost'  		                => array( 'validate' => 'float_or_empty',  	'default' => '' )			    // 1.5 | ''
		 , 'wh_num'  		                => array( 'validate' => 'digit_or_empty',  	'default' => '' )			    // 1 | ''
         , 'keyword'     			        => array( 'validate' => 's',  	            'default' => '' )			    // string

		 , 'wh_booking_date' 			    => array( 'validate' => 'array',  	        'default' => array( "3" ) )		// Elements of array checked as string
		 , 'ui_wh_booking_date'             => array( 'validate' => 'date',  	        'default' => '' )		        // Date                 e.g.: '2022-05-31' or ''

		                                                                                                                // 'digit_or_csd' can check about 'digit_or_csd' in arrays, as well
         , 'wh_booking_type'               => array( 'validate' => 'digit_or_csd',  	'default' => array( '0' ) )	    // Digit or comma separated digit ( e.g.: '12,a,45,9' => '12,0,45,9' )             // if ['0'] - All  booking resources,  ['-1'] - lost bookings in deleted resources
		 , 'ui_wh_booking_date_checkout'   => array( 'validate' => 'digit_or_date',  	'default' => '' )		        // digit or Date       e.g.:  '2022-05-31' or 5  or ''

		 , 'ui_wh_start'                   => array( 'validate' => 'checked_skip_it',  'default' => '' )		        // Skip  from  checking

		 , 'ui_usr__is_expand_remarks'     => array( 'validate' => array( 'On', 'Off' ),   'default' => 'Off' )
   );
 *
 *
 *
 ***********************************************************************************************************************
 *  Otherwise possible to use      ' Direct Clean Params '
 ***********************************************************************************************************************
 *
				$request_params_ajx_booking  = array(
										  'page_num'          => array( 'validate' => 'd', 					'default' => 1 )
										, 'page_items_count'  => array( 'validate' => 'd', 					'default' => 10 )
										, 'sort'              => array( 'validate' => array( 'ajx_booking_id' ),	'default' => 'ajx_booking_id' )
										, 'sort_type'         => array( 'validate' => array( 'ASC', 'DESC'),'default' => 'DESC' )
										, 'status'            => array( 'validate' => 's', 					'default' => '' )
										, 'keyword'           => array( 'validate' => 's', 					'default' => '' )
										, 'ru_create_date'    => array( 'validate' => 'date', 				'default' => '' )
				);
				$request_params_values = array(                                                                             // Usually 		$request_params_values 	is  $_REQUEST
										'page_num'         => 1,
										'page_items_count' => 3,
										'sort'             => 'ajx_booking_id',
										'sort_type'        => 'DESC',
										'status'           => '',
										'keyword'          => '',
										'ru_create_date'   => ''
								);
				$request_params = wpbc_sanitize_params_in_arr( $request_params_values, $request_params_ajx_booking );

 */
class WPBC_AJX__REQUEST {

	private $db_option_name__for_saved_request;
	private $request_rules_structure;
	private $user_id;

    public function __construct( $params ) {
		$defaults = array(
						   'db_option_name'          => 'general_booking_request_params',
						   'user_id'                 => wpbc_get_current_user_id(),
						   'request_rules_structure' => array()
                    );
		$params   = wp_parse_args( $params, $defaults );


	    if ( ! empty( $params['db_option_name'] ) ) {
		    $this->set_db_option_name__for_saved_request( $params['db_option_name'] );
	    }

        if ( ! empty( $params['user_id'] ) ) {
	        $this->set_user_id( $params['user_id'] );
        }

        if ( ! empty( $params['request_rules_structure'] ) ) {
	        $this->set_request_rules_structure( $params['request_rules_structure'] );
        }
    }

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Setters
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * Set name of option for saving request parameters into DB
		 *
		 * @param string $db_option_name
		 *
		 * @return void
		 */
		public function set_db_option_name__for_saved_request( $db_option_name ){
		    $this->db_option_name__for_saved_request = $db_option_name;
		}

		/**
		 * Set current active user ID
		 *
		 * @param int $user_id
		 *
		 * @return void
		 */
		public function set_user_id( $user_id ){
		    $this->user_id = $user_id;
		}

		/**
		 * Set request structure   for validation and default values
		 *
		 * @param array $request_rules_structure            array(
	     *                                                          'resource_id' => array( 'validate' => 'digit_or_csd',  'default' => array( '1' ) )
		 *                                                          , ...
	     *                                                  )
		 *
		 * @return void
		 */
		public function set_request_rules_structure( $request_rules_structure ){
		    $this->request_rules_structure = $request_rules_structure;
		}


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// Request Structure
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get params names for escaping and/or default value of such  params
	 *
	 * @param string $structure_type    'validate_and_default' (default) | 'validate' | 'default'
	 *
	 * @return array
	 *                      if $structure_type == ''               array ( 'keyword'      => array( 'validate' => 's', 'default' => '' )
	 *                                                                   , 'wh_booking_date' => array( 'validate' => 'digit_or_date', 'default' => '3' ), ...
	 *                      if $structure_type == 'validate'       array ( 'keyword'      => 's'
	 *                                                                   , 'wh_booking_date' => 'digit_or_date'), ...
	 *                      if $structure_type == 'default'        array ( 'keyword'      => ''
	 *                                                                  , 'wh_booking_date'  => '3' ), ...
	 */
	public function get_request_rules_structure( $structure_type = 'validate_and_default' ){

		$params_for_cleaning = $this->request_rules_structure;

		if ( 'validate_and_default' == $structure_type ) {
			return $params_for_cleaning;
		}

		$return_simple_arr = array();

		foreach ( $params_for_cleaning as $key => $value ) {
			$return_simple_arr[ $key ] = $value[ $structure_type ];
		}
		return $return_simple_arr;

	}


	/**
	 * Get default values from rules structure
	 *
	 * @return array      array ( 'keyword'      => '', 'wh_booking_date'  => '3' ), ...
	 */
	public function get_request_rules__default( ){

		return $this->get_request_rules_structure( 'default' );
	}


	/**
	 * Get validate rules from params structure
	 *
	 * @return array      array ( 'keyword'      => 's', 'wh_booking_date' => 'digit_or_date'), ...
	 */
	public function get_request_rules__validate( ){

		return $this->get_request_rules_structure( 'validate' );
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Get user Saved params
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get $_REQUEST saved before in DB from user  -  Sanitized Array
	 *
	 * @return
	            * false   - if not saved before
            * OR
	            * array(
					  *   'page_num' 			=> 1
					  * , 'page_items_count' 	=> 100
					  * , 'sort' 				=> 'ajx_booking_id'
					  * , 'sort_type' 			=> 'DESC'
					  * , 'keyword' 			=> ''
					  * , 'status' 				=> ''
				* )
	 */
	public function get_sanitized__saved__user_request_params() {

		$request_params_values_arr = $this->user_request_params__db_get();		// - $request_params_values_arr - un-serialized here automatically

		if ( false !== $request_params_values_arr ) {

			$request_params_structure = $this->get_request_rules_structure();		    /**
																							 *    array(      'page_num'          => array( 'validate' => 'd',                    'default' => 1 )
																							 * , 'page_items_count'  => array( 'validate' => 'd',                    'default' => 10 )
																							 * , 'sort'              => array( 'validate' => array( 'booking_id' ),    'default' => 'booking_id' )
																							 * , 'sort_type'         => array( 'validate' => array( 'ASC', 'DESC'),'default' => 'DESC' )
																							 * , 'keyword'           => array( 'validate' => 's',                    'default' => '' )
																							 * , 'create_date'       => array( 'validate' => 'date',                'default' => '' )
																							 * )
																							 */

			$escaped_request_params   = wpbc_sanitize_params_in_arr( $request_params_values_arr, $request_params_structure );	// Escaping params here

			return $escaped_request_params;

		} else {
			return false;
		}
	}


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Get sanitized $_REQUEST params      |      default values
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get "sanitized values" from $_REQUEST or "default values", based on rules structure
	 *
	 * @param string | false $prefix_in_request
	 *
	 *          default FALSE
	 *
	 *          string parameter useful, if request like this (usually  in Ajax):
	 *
     *                                        $_REQUEST (
	 *													['listing_page']['page_num'] => 3
	 *													['listing_page']['page_items_count'] => 20
	 *													['listing_page']['sort'] => 'booking_id'
	 *										 )
	 *
	 *          then we use this:            $this->get_sanitized__in_request__value_or_default( 'listing_page' )
	 *
	 *
	 * @return array
	 *                     Example:    array(
	 *                                          ['page_num'] => 3
	 *                                          ['page_items_count'] => 20
	 *                                          ['sort'] => 'booking_id'
	 *                                      )
	 */
	public function get_sanitized__in_request__value_or_default(  $prefix_in_request = false ){

		$request_rules_structure = $this->get_request_rules_structure();

		$request_params_values_arr = array();

		foreach ( $request_rules_structure as $request_key => $clean_type ) {

			$exist_request_param_value = wpbc_get_direct_value_in_request( $request_key, $prefix_in_request );          // Get direct request values:           $_REQUEST['page_num'] => 3
																											// if ( $prefix_in_request == 'listing_page' ):     $_REQUEST['listing_page']['page_num'] => 3
			if ( false !== $exist_request_param_value ) {
				$request_params_values_arr[ $request_key ] = $exist_request_param_value;
			}
		}

		$escaped_request_params = wpbc_sanitize_params_in_arr( $request_params_values_arr, $request_rules_structure );

		return $escaped_request_params;
	}


	// <editor-fold     defaultstate="collapsed"                        desc=" D B    S A V E  /   G E T     U S E R     R E Q U E S T "  >

		/**
		 * Save user filter request - saving user filters in toolbar
		 *
		 * @param array $params_arr
		 *
		 * @return bool|int
		 */
		function user_request_params__db_save( $params_arr ) {

			return update_user_option( $this->user_id, $this->db_option_name__for_saved_request ,  $params_arr );
		}

		/**
		 * Delete saved user request - used for resetting user filters in toolbar
		 *
		 * @return bool
		 */
		function user_request_params__db_delete() {

			return delete_user_option( $this->user_id, $this->db_option_name__for_saved_request );
		}

		/**
		 * Get saved user filter request - params for user filters in toolbar
		 *
		 * @return false|mixed
		 */
		function user_request_params__db_get() {

			return get_user_option( $this->db_option_name__for_saved_request, $this->user_id );
		}

	// </editor-fold>

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Get user option  -   single parameter
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get user saved option  from  Request in Booking Listing
	 *
	 * @param $user_id		int			1
	 * @param $option_name	string		'ui_usr__send_emails'
	 *
	 * @return false|mixed
	 */
	function user_request_params__get_option( $option_name ){

		// Get User saved option from  request
		$escaped_request_params = $this->get_sanitized__saved__user_request_params();
		if ( ( ! empty( $escaped_request_params ) ) && ( ! empty( $escaped_request_params[ $option_name ] ) ) ) {
			return $escaped_request_params[ $option_name ];
		}

		// Get default option
		$default_param_values   = $this->get_request_rules_structure( 'default' );
		if  ( ! empty( $default_param_values[ $option_name ] ) ) {
			return $default_param_values[ $option_name ];
		}

		// There is no such option
		return false;
	}

		// Helpers:
		/**
		 * Is send emails ?		 Check DB SAVED user defined option from Options toolbar.
		 * @param $user_id  int  ID  of user
		 *
		 * @return int   1 | 0
		 */
		function user_request_option__is_send_emails(){

			$is_send_emeils = $this->user_request_params__get_option( 'ui_usr__send_emails' );

			$is_send_emeils = ( 'send' == $is_send_emeils ) ? 1 : 0;

			return $is_send_emeils;
		}
}


// ---------------------------------------------------------------------------------------------------------------------
// Request
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Get indexed or direct  request  from  REQUEST
 *
 * @param       $request_key
 * @param false $prefix_in_request
 *
 * @return false|mixed
 */
function wpbc_get_direct_value_in_request( $request_key, $prefix_in_request = false ) {

	/**
	 * Get value from 'direct request' -  "$_REQUEST['page_num'] => 3"
	 *    or
	 * from  'prefix request' - "$_REQUEST['request_params']['page_num'] => 3"
     */

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( empty( $prefix_in_request ) ) && ( isset( $_REQUEST[ $request_key ] ) ) ) {
		return $_REQUEST[ $request_key ];  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ( ! empty( $prefix_in_request ) ) && ( isset( $_REQUEST[ $prefix_in_request ] ) ) && ( isset( $_REQUEST[ $prefix_in_request ][ $request_key ] ) ) ) {
		return $_REQUEST[ $prefix_in_request ][ $request_key ];  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	return  false;
}