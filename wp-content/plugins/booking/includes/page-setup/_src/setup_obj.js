"use strict";

// -----------------------------------------------------------------------------------------------------------------
// ==  Setup Wizard  -  Extend Settings Obj  -  '_wpbc_settings'  ==
// -----------------------------------------------------------------------------------------------------------------
/**
 * Extend _wpbc_settings with  new methods
 *
 * @type {*|{}}
 * @private
 */
 _wpbc_settings = (function ( obj, $) {
	 
	// -----------------------------------------------------------------------------------------------------------------
	// Setup Wizard Parameters
	// -----------------------------------------------------------------------------------------------------------------
	var p_setup_wizard = obj.setup_wizard_obj = obj.setup_wizard_obj || {
															// sort        : "booking_id",
															// page_num    : 1,
															// create_date : "",
															// keyword     : ""
														};
	obj.get_param__setup_wizard = function ( param_key ) {
		return p_setup_wizard[ param_key ];
	};
	obj.set_param__setup_wizard = function ( param_key, param_val ) {
		p_setup_wizard[ param_key ] = param_val;
	};
	// -------------------------------------------------------------
	obj.set_all_params__setup_wizard = function ( request_param_obj ) {
		p_setup_wizard = request_param_obj;
	};
	obj.get_all_params__setup_wizard = function () {
		return p_setup_wizard;
	};
	// -------------------------------------------------------------
	obj.set_params_arr__setup_wizard = function( params_arr ){
		_.each( params_arr, function ( p_val, p_key, p_data ){
			obj.set_param__setup_wizard( p_key, p_val );
		} );
	}

	return obj;
}( _wpbc_settings || {}, jQuery ));
