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
_wpbc_settings = function (obj, $) {
  // -----------------------------------------------------------------------------------------------------------------
  // Setup Wizard Parameters
  // -----------------------------------------------------------------------------------------------------------------
  var p_setup_wizard = obj.setup_wizard_obj = obj.setup_wizard_obj || {
    // sort        : "booking_id",
    // page_num    : 1,
    // create_date : "",
    // keyword     : ""
  };
  obj.get_param__setup_wizard = function (param_key) {
    return p_setup_wizard[param_key];
  };
  obj.set_param__setup_wizard = function (param_key, param_val) {
    p_setup_wizard[param_key] = param_val;
  };
  // -------------------------------------------------------------
  obj.set_all_params__setup_wizard = function (request_param_obj) {
    p_setup_wizard = request_param_obj;
  };
  obj.get_all_params__setup_wizard = function () {
    return p_setup_wizard;
  };
  // -------------------------------------------------------------
  obj.set_params_arr__setup_wizard = function (params_arr) {
    _.each(params_arr, function (p_val, p_key, p_data) {
      obj.set_param__setup_wizard(p_key, p_val);
    });
  };
  return obj;
}(_wpbc_settings || {}, jQuery);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1zZXR1cC9fb3V0L3NldHVwX29iai5qcyIsIm5hbWVzIjpbIl93cGJjX3NldHRpbmdzIiwib2JqIiwiJCIsInBfc2V0dXBfd2l6YXJkIiwic2V0dXBfd2l6YXJkX29iaiIsImdldF9wYXJhbV9fc2V0dXBfd2l6YXJkIiwicGFyYW1fa2V5Iiwic2V0X3BhcmFtX19zZXR1cF93aXphcmQiLCJwYXJhbV92YWwiLCJzZXRfYWxsX3BhcmFtc19fc2V0dXBfd2l6YXJkIiwicmVxdWVzdF9wYXJhbV9vYmoiLCJnZXRfYWxsX3BhcmFtc19fc2V0dXBfd2l6YXJkIiwic2V0X3BhcmFtc19hcnJfX3NldHVwX3dpemFyZCIsInBhcmFtc19hcnIiLCJfIiwiZWFjaCIsInBfdmFsIiwicF9rZXkiLCJwX2RhdGEiLCJqUXVlcnkiXSwic291cmNlcyI6WyJpbmNsdWRlcy9wYWdlLXNldHVwL19zcmMvc2V0dXBfb2JqLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIlwidXNlIHN0cmljdFwiO1xyXG5cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gIFNldHVwIFdpemFyZCAgLSAgRXh0ZW5kIFNldHRpbmdzIE9iaiAgLSAgJ193cGJjX3NldHRpbmdzJyAgPT1cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLyoqXHJcbiAqIEV4dGVuZCBfd3BiY19zZXR0aW5ncyB3aXRoICBuZXcgbWV0aG9kc1xyXG4gKlxyXG4gKiBAdHlwZSB7Knx7fX1cclxuICogQHByaXZhdGVcclxuICovXHJcbiBfd3BiY19zZXR0aW5ncyA9IChmdW5jdGlvbiAoIG9iaiwgJCkge1xyXG5cdCBcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFNldHVwIFdpemFyZCBQYXJhbWV0ZXJzXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgcF9zZXR1cF93aXphcmQgPSBvYmouc2V0dXBfd2l6YXJkX29iaiA9IG9iai5zZXR1cF93aXphcmRfb2JqIHx8IHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gc29ydCAgICAgICAgOiBcImJvb2tpbmdfaWRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gcGFnZV9udW0gICAgOiAxLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBjcmVhdGVfZGF0ZSA6IFwiXCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIGtleXdvcmQgICAgIDogXCJcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fTtcclxuXHRvYmouZ2V0X3BhcmFtX19zZXR1cF93aXphcmQgPSBmdW5jdGlvbiAoIHBhcmFtX2tleSApIHtcclxuXHRcdHJldHVybiBwX3NldHVwX3dpemFyZFsgcGFyYW1fa2V5IF07XHJcblx0fTtcclxuXHRvYmouc2V0X3BhcmFtX19zZXR1cF93aXphcmQgPSBmdW5jdGlvbiAoIHBhcmFtX2tleSwgcGFyYW1fdmFsICkge1xyXG5cdFx0cF9zZXR1cF93aXphcmRbIHBhcmFtX2tleSBdID0gcGFyYW1fdmFsO1xyXG5cdH07XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdG9iai5zZXRfYWxsX3BhcmFtc19fc2V0dXBfd2l6YXJkID0gZnVuY3Rpb24gKCByZXF1ZXN0X3BhcmFtX29iaiApIHtcclxuXHRcdHBfc2V0dXBfd2l6YXJkID0gcmVxdWVzdF9wYXJhbV9vYmo7XHJcblx0fTtcclxuXHRvYmouZ2V0X2FsbF9wYXJhbXNfX3NldHVwX3dpemFyZCA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdHJldHVybiBwX3NldHVwX3dpemFyZDtcclxuXHR9O1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRvYmouc2V0X3BhcmFtc19hcnJfX3NldHVwX3dpemFyZCA9IGZ1bmN0aW9uKCBwYXJhbXNfYXJyICl7XHJcblx0XHRfLmVhY2goIHBhcmFtc19hcnIsIGZ1bmN0aW9uICggcF92YWwsIHBfa2V5LCBwX2RhdGEgKXtcclxuXHRcdFx0b2JqLnNldF9wYXJhbV9fc2V0dXBfd2l6YXJkKCBwX2tleSwgcF92YWwgKTtcclxuXHRcdH0gKTtcclxuXHR9XHJcblxyXG5cdHJldHVybiBvYmo7XHJcbn0oIF93cGJjX3NldHRpbmdzIHx8IHt9LCBqUXVlcnkgKSk7XHJcbiJdLCJtYXBwaW5ncyI6IkFBQUEsWUFBWTs7QUFFWjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQ0EsY0FBYyxHQUFJLFVBQVdDLEdBQUcsRUFBRUMsQ0FBQyxFQUFFO0VBRXJDO0VBQ0E7RUFDQTtFQUNBLElBQUlDLGNBQWMsR0FBR0YsR0FBRyxDQUFDRyxnQkFBZ0IsR0FBR0gsR0FBRyxDQUFDRyxnQkFBZ0IsSUFBSTtJQUN0RDtJQUNBO0lBQ0E7SUFDQTtFQUFBLENBQ0E7RUFDZEgsR0FBRyxDQUFDSSx1QkFBdUIsR0FBRyxVQUFXQyxTQUFTLEVBQUc7SUFDcEQsT0FBT0gsY0FBYyxDQUFFRyxTQUFTLENBQUU7RUFDbkMsQ0FBQztFQUNETCxHQUFHLENBQUNNLHVCQUF1QixHQUFHLFVBQVdELFNBQVMsRUFBRUUsU0FBUyxFQUFHO0lBQy9ETCxjQUFjLENBQUVHLFNBQVMsQ0FBRSxHQUFHRSxTQUFTO0VBQ3hDLENBQUM7RUFDRDtFQUNBUCxHQUFHLENBQUNRLDRCQUE0QixHQUFHLFVBQVdDLGlCQUFpQixFQUFHO0lBQ2pFUCxjQUFjLEdBQUdPLGlCQUFpQjtFQUNuQyxDQUFDO0VBQ0RULEdBQUcsQ0FBQ1UsNEJBQTRCLEdBQUcsWUFBWTtJQUM5QyxPQUFPUixjQUFjO0VBQ3RCLENBQUM7RUFDRDtFQUNBRixHQUFHLENBQUNXLDRCQUE0QixHQUFHLFVBQVVDLFVBQVUsRUFBRTtJQUN4REMsQ0FBQyxDQUFDQyxJQUFJLENBQUVGLFVBQVUsRUFBRSxVQUFXRyxLQUFLLEVBQUVDLEtBQUssRUFBRUMsTUFBTSxFQUFFO01BQ3BEakIsR0FBRyxDQUFDTSx1QkFBdUIsQ0FBRVUsS0FBSyxFQUFFRCxLQUFNLENBQUM7SUFDNUMsQ0FBRSxDQUFDO0VBQ0osQ0FBQztFQUVELE9BQU9mLEdBQUc7QUFDWCxDQUFDLENBQUVELGNBQWMsSUFBSSxDQUFDLENBQUMsRUFBRW1CLE1BQU8sQ0FBRSIsImlnbm9yZUxpc3QiOltdfQ==
