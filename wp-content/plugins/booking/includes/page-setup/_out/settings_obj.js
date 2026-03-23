"use strict";

// -----------------------------------------------------------------------------------------------------------------
// ==  Settings Obj  ==
// -----------------------------------------------------------------------------------------------------------------
//TODO: place initial  object  in other JS file. Later we will  use this obj.  for all settings pages.
/**
 * Request Object
 * Here we can  define parameters and Update it later,  when  some parameter was changed
 *
 */
var _wpbc_settings = function (obj, $) {
  // -----------------------------------------------------------------------------------------------------------------
  // Main Parameters
  // -----------------------------------------------------------------------------------------------------------------
  var p_general = obj.general_obj = obj.general_obj || {
    // sort        : "booking_id",
    // page_num    : 1,
    // create_date : "",
    // keyword     : ""
  };
  obj.get_param__general = function (param_key) {
    return p_general[param_key];
  };
  obj.set_param__general = function (param_key, param_val) {
    p_general[param_key] = param_val;
  };
  // -------------------------------------------------------------
  obj.set_all_params__general = function (request_param_obj) {
    p_general = request_param_obj;
  };
  obj.get_all_params__general = function () {
    return p_general;
  };
  // -------------------------------------------------------------
  obj.set_params_arr__general = function (params_arr) {
    _.each(params_arr, function (p_val, p_key, p_data) {
      obj.set_param__general(p_key, p_val);
    });
  };

  // -----------------------------------------------------------------------------------------------------------------
  // Secure parameters for Ajax
  // -----------------------------------------------------------------------------------------------------------------
  var p_secure = obj.security_obj = obj.security_obj || {
    user_id: 0,
    nonce: '',
    locale: ''
  };
  obj.set_param__secure = function (param_key, param_val) {
    p_secure[param_key] = param_val;
  };
  obj.get_param__secure = function (param_key) {
    return p_secure[param_key];
  };

  // -----------------------------------------------------------------------------------------------------------------
  // Other parameters
  // -----------------------------------------------------------------------------------------------------------------
  var p_other = obj.other_obj = obj.other_obj || {};
  obj.set_param__other = function (param_key, param_val) {
    p_other[param_key] = param_val;
  };
  obj.get_param__other = function (param_key) {
    return p_other[param_key];
  };
  return obj;
}(_wpbc_settings || {}, jQuery);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1zZXR1cC9fb3V0L3NldHRpbmdzX29iai5qcyIsIm5hbWVzIjpbIl93cGJjX3NldHRpbmdzIiwib2JqIiwiJCIsInBfZ2VuZXJhbCIsImdlbmVyYWxfb2JqIiwiZ2V0X3BhcmFtX19nZW5lcmFsIiwicGFyYW1fa2V5Iiwic2V0X3BhcmFtX19nZW5lcmFsIiwicGFyYW1fdmFsIiwic2V0X2FsbF9wYXJhbXNfX2dlbmVyYWwiLCJyZXF1ZXN0X3BhcmFtX29iaiIsImdldF9hbGxfcGFyYW1zX19nZW5lcmFsIiwic2V0X3BhcmFtc19hcnJfX2dlbmVyYWwiLCJwYXJhbXNfYXJyIiwiXyIsImVhY2giLCJwX3ZhbCIsInBfa2V5IiwicF9kYXRhIiwicF9zZWN1cmUiLCJzZWN1cml0eV9vYmoiLCJ1c2VyX2lkIiwibm9uY2UiLCJsb2NhbGUiLCJzZXRfcGFyYW1fX3NlY3VyZSIsImdldF9wYXJhbV9fc2VjdXJlIiwicF9vdGhlciIsIm90aGVyX29iaiIsInNldF9wYXJhbV9fb3RoZXIiLCJnZXRfcGFyYW1fX290aGVyIiwialF1ZXJ5Il0sInNvdXJjZXMiOlsiaW5jbHVkZXMvcGFnZS1zZXR1cC9fc3JjL3NldHRpbmdzX29iai5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy8gPT0gIFNldHRpbmdzIE9iaiAgPT1cclxuLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuLy9UT0RPOiBwbGFjZSBpbml0aWFsICBvYmplY3QgIGluIG90aGVyIEpTIGZpbGUuIExhdGVyIHdlIHdpbGwgIHVzZSB0aGlzIG9iai4gIGZvciBhbGwgc2V0dGluZ3MgcGFnZXMuXHJcbi8qKlxyXG4gKiBSZXF1ZXN0IE9iamVjdFxyXG4gKiBIZXJlIHdlIGNhbiAgZGVmaW5lIHBhcmFtZXRlcnMgYW5kIFVwZGF0ZSBpdCBsYXRlciwgIHdoZW4gIHNvbWUgcGFyYW1ldGVyIHdhcyBjaGFuZ2VkXHJcbiAqXHJcbiAqL1xyXG52YXIgX3dwYmNfc2V0dGluZ3MgPSAoZnVuY3Rpb24gKCBvYmosICQpIHtcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBNYWluIFBhcmFtZXRlcnNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBwX2dlbmVyYWwgPSBvYmouZ2VuZXJhbF9vYmogPSBvYmouZ2VuZXJhbF9vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBzb3J0ICAgICAgICA6IFwiYm9va2luZ19pZFwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBwYWdlX251bSAgICA6IDEsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIGNyZWF0ZV9kYXRlIDogXCJcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8ga2V5d29yZCAgICAgOiBcIlwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9O1xyXG5cdG9iai5nZXRfcGFyYW1fX2dlbmVyYWwgPSBmdW5jdGlvbiAoIHBhcmFtX2tleSApIHtcclxuXHRcdHJldHVybiBwX2dlbmVyYWxbIHBhcmFtX2tleSBdO1xyXG5cdH07XHJcblx0b2JqLnNldF9wYXJhbV9fZ2VuZXJhbCA9IGZ1bmN0aW9uICggcGFyYW1fa2V5LCBwYXJhbV92YWwgKSB7XHJcblx0XHRwX2dlbmVyYWxbIHBhcmFtX2tleSBdID0gcGFyYW1fdmFsO1xyXG5cdH07XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdG9iai5zZXRfYWxsX3BhcmFtc19fZ2VuZXJhbCA9IGZ1bmN0aW9uICggcmVxdWVzdF9wYXJhbV9vYmogKSB7XHJcblx0XHRwX2dlbmVyYWwgPSByZXF1ZXN0X3BhcmFtX29iajtcclxuXHR9O1xyXG5cdG9iai5nZXRfYWxsX3BhcmFtc19fZ2VuZXJhbCA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdHJldHVybiBwX2dlbmVyYWw7XHJcblx0fTtcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0b2JqLnNldF9wYXJhbXNfYXJyX19nZW5lcmFsID0gZnVuY3Rpb24oIHBhcmFtc19hcnIgKXtcclxuXHRcdF8uZWFjaCggcGFyYW1zX2FyciwgZnVuY3Rpb24gKCBwX3ZhbCwgcF9rZXksIHBfZGF0YSApe1xyXG5cdFx0XHRvYmouc2V0X3BhcmFtX19nZW5lcmFsKCBwX2tleSwgcF92YWwgKTtcclxuXHRcdH0gKTtcclxuXHR9XHJcblxyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFNlY3VyZSBwYXJhbWV0ZXJzIGZvciBBamF4XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgcF9zZWN1cmUgPSBvYmouc2VjdXJpdHlfb2JqID0gb2JqLnNlY3VyaXR5X29iaiB8fCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHVzZXJfaWQ6IDAsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG5vbmNlICA6ICcnLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRsb2NhbGUgOiAnJ1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9O1xyXG5cdG9iai5zZXRfcGFyYW1fX3NlY3VyZSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5LCBwYXJhbV92YWwgKSB7XHJcblx0XHRwX3NlY3VyZVsgcGFyYW1fa2V5IF0gPSBwYXJhbV92YWw7XHJcblx0fTtcclxuXHJcblx0b2JqLmdldF9wYXJhbV9fc2VjdXJlID0gZnVuY3Rpb24gKCBwYXJhbV9rZXkgKSB7XHJcblx0XHRyZXR1cm4gcF9zZWN1cmVbIHBhcmFtX2tleSBdO1xyXG5cdH07XHJcblxyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIE90aGVyIHBhcmFtZXRlcnNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdHZhciBwX290aGVyID0gb2JqLm90aGVyX29iaiA9IG9iai5vdGhlcl9vYmogfHwgeyB9O1xyXG5cclxuXHRvYmouc2V0X3BhcmFtX19vdGhlciA9IGZ1bmN0aW9uICggcGFyYW1fa2V5LCBwYXJhbV92YWwgKSB7XHJcblx0XHRwX290aGVyWyBwYXJhbV9rZXkgXSA9IHBhcmFtX3ZhbDtcclxuXHR9O1xyXG5cclxuXHRvYmouZ2V0X3BhcmFtX19vdGhlciA9IGZ1bmN0aW9uICggcGFyYW1fa2V5ICkge1xyXG5cdFx0cmV0dXJuIHBfb3RoZXJbIHBhcmFtX2tleSBdO1xyXG5cdH07XHJcblxyXG5cdHJldHVybiBvYmo7XHJcbn0oIF93cGJjX3NldHRpbmdzIHx8IHt9LCBqUXVlcnkgKSk7XHJcbiJdLCJtYXBwaW5ncyI6IkFBQUEsWUFBWTs7QUFDWjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxJQUFJQSxjQUFjLEdBQUksVUFBV0MsR0FBRyxFQUFFQyxDQUFDLEVBQUU7RUFFeEM7RUFDQTtFQUNBO0VBQ0EsSUFBSUMsU0FBUyxHQUFHRixHQUFHLENBQUNHLFdBQVcsR0FBR0gsR0FBRyxDQUFDRyxXQUFXLElBQUk7SUFDdkM7SUFDQTtJQUNBO0lBQ0E7RUFBQSxDQUNBO0VBQ2RILEdBQUcsQ0FBQ0ksa0JBQWtCLEdBQUcsVUFBV0MsU0FBUyxFQUFHO0lBQy9DLE9BQU9ILFNBQVMsQ0FBRUcsU0FBUyxDQUFFO0VBQzlCLENBQUM7RUFDREwsR0FBRyxDQUFDTSxrQkFBa0IsR0FBRyxVQUFXRCxTQUFTLEVBQUVFLFNBQVMsRUFBRztJQUMxREwsU0FBUyxDQUFFRyxTQUFTLENBQUUsR0FBR0UsU0FBUztFQUNuQyxDQUFDO0VBQ0Q7RUFDQVAsR0FBRyxDQUFDUSx1QkFBdUIsR0FBRyxVQUFXQyxpQkFBaUIsRUFBRztJQUM1RFAsU0FBUyxHQUFHTyxpQkFBaUI7RUFDOUIsQ0FBQztFQUNEVCxHQUFHLENBQUNVLHVCQUF1QixHQUFHLFlBQVk7SUFDekMsT0FBT1IsU0FBUztFQUNqQixDQUFDO0VBQ0Q7RUFDQUYsR0FBRyxDQUFDVyx1QkFBdUIsR0FBRyxVQUFVQyxVQUFVLEVBQUU7SUFDbkRDLENBQUMsQ0FBQ0MsSUFBSSxDQUFFRixVQUFVLEVBQUUsVUFBV0csS0FBSyxFQUFFQyxLQUFLLEVBQUVDLE1BQU0sRUFBRTtNQUNwRGpCLEdBQUcsQ0FBQ00sa0JBQWtCLENBQUVVLEtBQUssRUFBRUQsS0FBTSxDQUFDO0lBQ3ZDLENBQUUsQ0FBQztFQUNKLENBQUM7O0VBR0Q7RUFDQTtFQUNBO0VBQ0EsSUFBSUcsUUFBUSxHQUFHbEIsR0FBRyxDQUFDbUIsWUFBWSxHQUFHbkIsR0FBRyxDQUFDbUIsWUFBWSxJQUFJO0lBQ3hDQyxPQUFPLEVBQUUsQ0FBQztJQUNWQyxLQUFLLEVBQUksRUFBRTtJQUNYQyxNQUFNLEVBQUc7RUFDUixDQUFDO0VBQ2hCdEIsR0FBRyxDQUFDdUIsaUJBQWlCLEdBQUcsVUFBV2xCLFNBQVMsRUFBRUUsU0FBUyxFQUFHO0lBQ3pEVyxRQUFRLENBQUViLFNBQVMsQ0FBRSxHQUFHRSxTQUFTO0VBQ2xDLENBQUM7RUFFRFAsR0FBRyxDQUFDd0IsaUJBQWlCLEdBQUcsVUFBV25CLFNBQVMsRUFBRztJQUM5QyxPQUFPYSxRQUFRLENBQUViLFNBQVMsQ0FBRTtFQUM3QixDQUFDOztFQUdEO0VBQ0E7RUFDQTtFQUNBLElBQUlvQixPQUFPLEdBQUd6QixHQUFHLENBQUMwQixTQUFTLEdBQUcxQixHQUFHLENBQUMwQixTQUFTLElBQUksQ0FBRSxDQUFDO0VBRWxEMUIsR0FBRyxDQUFDMkIsZ0JBQWdCLEdBQUcsVUFBV3RCLFNBQVMsRUFBRUUsU0FBUyxFQUFHO0lBQ3hEa0IsT0FBTyxDQUFFcEIsU0FBUyxDQUFFLEdBQUdFLFNBQVM7RUFDakMsQ0FBQztFQUVEUCxHQUFHLENBQUM0QixnQkFBZ0IsR0FBRyxVQUFXdkIsU0FBUyxFQUFHO0lBQzdDLE9BQU9vQixPQUFPLENBQUVwQixTQUFTLENBQUU7RUFDNUIsQ0FBQztFQUVELE9BQU9MLEdBQUc7QUFDWCxDQUFDLENBQUVELGNBQWMsSUFBSSxDQUFDLENBQUMsRUFBRThCLE1BQU8sQ0FBRSIsImlnbm9yZUxpc3QiOltdfQ==
