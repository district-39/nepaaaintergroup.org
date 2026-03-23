"use strict";

/**
 * Define HTML ui Hooks: on KeyUp | Change | -> Sort Order & Number Items / Page
 * * We are chnaged it once, because such  elements always the same
 */
function wpbc_ajx_booking_define_ui_hooks_once() {
  //------------------------------------------------------------------------------------------------------------------
  // Booked dates
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_booking_date').on('change', function (event) {
    var changed_value = JSON.parse(jQuery('#wh_booking_date').val());
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_booking_date': changed_value,
      'page_num': 1,
      // Frontend selected elements (saving for future use, after F5)
      'ui_wh_booking_date_radio': jQuery('input[name="ui_wh_booking_date_radio"]:checked').val(),
      'ui_wh_booking_date_next': jQuery('#ui_wh_booking_date_next').val(),
      'ui_wh_booking_date_prior': jQuery('#ui_wh_booking_date_prior').val(),
      'ui_wh_booking_date_checkin': jQuery('#ui_wh_booking_date_checkin').val(),
      'ui_wh_booking_date_checkout': jQuery('#ui_wh_booking_date_checkout').val()
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Approved | Pending | All
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_approved').on('change', function (event) {
    var changed_value = jQuery('#wh_approved').val();
    changed_value = JSON.parse(changed_value);
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_approved': changed_value[0],
      'page_num': 1
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Keywords
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wpbc_search_field').on("keyup", function (event) {
    if (13 !== event.which) {
      wpbc_ajx_booking_searching_after_few_seconds('#wpbc_search_field'); // Searching after 1.5 seconds after Key Up
    } else {
      wpbc_ajx_booking_searching_after_few_seconds('#wpbc_search_field', 0); // Immediate search
    }
  });

  //------------------------------------------------------------------------------------------------------------------
  // Existing | Trash | Any
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_trash').on('change', function (event) {
    var changed_value = JSON.parse(jQuery('#wh_trash').val());
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_trash': changed_value[0],
      'page_num': 1
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // All bookings | New bookings
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_what_bookings').on('change', function (event) {
    var changed_value = JSON.parse(jQuery('#wh_what_bookings').val());
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_what_bookings': changed_value[0],
      'page_num': 1
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // "Creation Date"   of bookings
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_modification_date').on('change', function (event) {
    var changed_value = JSON.parse(jQuery('#wh_modification_date').val());
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_modification_date': changed_value,
      'page_num': 1,
      // Frontend selected elements (saving for future use, after F5)
      'ui_wh_modification_date_radio': jQuery('input[name="ui_wh_modification_date_radio"]:checked').val(),
      'ui_wh_modification_date_prior': jQuery('#ui_wh_modification_date_prior').val(),
      'ui_wh_modification_date_checkin': jQuery('#ui_wh_modification_date_checkin').val(),
      'ui_wh_modification_date_checkout': jQuery('#ui_wh_modification_date_checkout').val()
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Payment Status
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_pay_status').on('change', function (event) {
    var changed_value = JSON.parse(jQuery('#wh_pay_status').val());
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_pay_status': changed_value,
      'page_num': 1,
      // Frontend selected elements (saving for future use, after F5)
      'ui_wh_pay_status_radio': undefined === jQuery('input[name="ui_wh_pay_status_radio"]:checked').val() ? '' : jQuery('input[name="ui_wh_pay_status_radio"]:checked').val(),
      'ui_wh_pay_status_custom': jQuery('#ui_wh_pay_status_custom').val()
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Min Cost
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_cost').on('change', function (event) {
    var changed_value = jQuery('#wh_cost').val();
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_cost': changed_value,
      'page_num': 1
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Max Cost
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_cost2').on('change', function (event) {
    var changed_value = jQuery('#wh_cost2').val();
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_cost2': changed_value,
      'page_num': 1
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Booking resources
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_booking_type').on('change', function (event) {
    var changed_value = jQuery('#wh_booking_type').val(); // it's get as array
    if (Array.isArray(changed_value) && 0 === changed_value.length) {
      changed_value = ['-1'];
    }
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_booking_type': changed_value,
      'page_num': 1
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Sorting
  //------------------------------------------------------------------------------------------------------------------
  jQuery('#wh_sort').on('change', function (event) {
    var changed_value = jQuery('#wh_sort').val();
    changed_value = JSON.parse(changed_value);
    wpbc_ajx_booking_send_search_request_with_params({
      'wh_sort': changed_value[0]
    });
  });
}
jQuery(document).ready(function () {
  wpbc_ajx_booking_define_ui_hooks_once();
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1ib29raW5ncy9fb3V0L2Jvb2tpbmdzX19ob29rcy5qcyIsIm5hbWVzIjpbIndwYmNfYWp4X2Jvb2tpbmdfZGVmaW5lX3VpX2hvb2tzX29uY2UiLCJqUXVlcnkiLCJvbiIsImV2ZW50IiwiY2hhbmdlZF92YWx1ZSIsIkpTT04iLCJwYXJzZSIsInZhbCIsIndwYmNfYWp4X2Jvb2tpbmdfc2VuZF9zZWFyY2hfcmVxdWVzdF93aXRoX3BhcmFtcyIsIndoaWNoIiwid3BiY19hanhfYm9va2luZ19zZWFyY2hpbmdfYWZ0ZXJfZmV3X3NlY29uZHMiLCJ1bmRlZmluZWQiLCJBcnJheSIsImlzQXJyYXkiLCJsZW5ndGgiLCJkb2N1bWVudCIsInJlYWR5Il0sInNvdXJjZXMiOlsiaW5jbHVkZXMvcGFnZS1ib29raW5ncy9fc3JjL2Jvb2tpbmdzX19ob29rcy5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcclxuXHJcbi8qKlxyXG4gKiBEZWZpbmUgSFRNTCB1aSBIb29rczogb24gS2V5VXAgfCBDaGFuZ2UgfCAtPiBTb3J0IE9yZGVyICYgTnVtYmVyIEl0ZW1zIC8gUGFnZVxyXG4gKiAqIFdlIGFyZSBjaG5hZ2VkIGl0IG9uY2UsIGJlY2F1c2Ugc3VjaCAgZWxlbWVudHMgYWx3YXlzIHRoZSBzYW1lXHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9ib29raW5nX2RlZmluZV91aV9ob29rc19vbmNlKCl7XHJcblxyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gQm9va2VkIGRhdGVzXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRqUXVlcnkoICcjd2hfYm9va2luZ19kYXRlJyApLm9uKCAnY2hhbmdlJywgZnVuY3Rpb24oIGV2ZW50ICl7XHJcblxyXG5cdFx0dmFyIGNoYW5nZWRfdmFsdWUgPSBKU09OLnBhcnNlKCBqUXVlcnkoICcjd2hfYm9va2luZ19kYXRlJyApLnZhbCgpICk7XHJcblxyXG5cdFx0d3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X3dpdGhfcGFyYW1zKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3aF9ib29raW5nX2RhdGUnOiBjaGFuZ2VkX3ZhbHVlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncGFnZV9udW0nICAgICAgIDogMSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRnJvbnRlbmQgc2VsZWN0ZWQgZWxlbWVudHMgKHNhdmluZyBmb3IgZnV0dXJlIHVzZSwgYWZ0ZXIgRjUpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV93aF9ib29raW5nX2RhdGVfcmFkaW8nICAgOiBqUXVlcnkoICdpbnB1dFtuYW1lPVwidWlfd2hfYm9va2luZ19kYXRlX3JhZGlvXCJdOmNoZWNrZWQnICkudmFsKCksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV93aF9ib29raW5nX2RhdGVfbmV4dCcgICAgOiBqUXVlcnkoICcjdWlfd2hfYm9va2luZ19kYXRlX25leHQnICkudmFsKCksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV93aF9ib29raW5nX2RhdGVfcHJpb3InICAgOiBqUXVlcnkoICcjdWlfd2hfYm9va2luZ19kYXRlX3ByaW9yJyApLnZhbCgpLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndWlfd2hfYm9va2luZ19kYXRlX2NoZWNraW4nIDogalF1ZXJ5KCAnI3VpX3doX2Jvb2tpbmdfZGF0ZV9jaGVja2luJyApLnZhbCgpLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndWlfd2hfYm9va2luZ19kYXRlX2NoZWNrb3V0JzogalF1ZXJ5KCAnI3VpX3doX2Jvb2tpbmdfZGF0ZV9jaGVja291dCcgKS52YWwoKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdH0gKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBBcHByb3ZlZCB8IFBlbmRpbmcgfCBBbGxcclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdGpRdWVyeSggJyN3aF9hcHByb3ZlZCcgKS5vbiggJ2NoYW5nZScsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cclxuXHRcdHZhciBjaGFuZ2VkX3ZhbHVlID0galF1ZXJ5KCAnI3doX2FwcHJvdmVkJyApLnZhbCgpO1xyXG5cclxuXHRcdGNoYW5nZWRfdmFsdWUgPSBKU09OLnBhcnNlKCBjaGFuZ2VkX3ZhbHVlICk7XHJcblxyXG5cdFx0d3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X3dpdGhfcGFyYW1zKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3aF9hcHByb3ZlZCc6IGNoYW5nZWRfdmFsdWVbIDAgXSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3BhZ2VfbnVtJyAgIDogMVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdH0gKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBLZXl3b3Jkc1xyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0alF1ZXJ5KCAnI3dwYmNfc2VhcmNoX2ZpZWxkJyApLm9uKCBcImtleXVwXCIsIGZ1bmN0aW9uICggZXZlbnQgKXtcclxuXHRcdGlmICggMTMgIT09IGV2ZW50LndoaWNoICl7XHJcblx0XHRcdHdwYmNfYWp4X2Jvb2tpbmdfc2VhcmNoaW5nX2FmdGVyX2Zld19zZWNvbmRzKCAnI3dwYmNfc2VhcmNoX2ZpZWxkJyApO1x0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gU2VhcmNoaW5nIGFmdGVyIDEuNSBzZWNvbmRzIGFmdGVyIEtleSBVcFxyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0d3BiY19hanhfYm9va2luZ19zZWFyY2hpbmdfYWZ0ZXJfZmV3X3NlY29uZHMoICcjd3BiY19zZWFyY2hfZmllbGQnLCAwICk7XHRcdFx0XHRcdFx0XHRcdFx0Ly8gSW1tZWRpYXRlIHNlYXJjaFxyXG5cdFx0fVxyXG5cdH0gKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBFeGlzdGluZyB8IFRyYXNoIHwgQW55XHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRqUXVlcnkoICcjd2hfdHJhc2gnICkub24oICdjaGFuZ2UnLCBmdW5jdGlvbiggZXZlbnQgKXtcclxuXHJcblx0XHR2YXIgY2hhbmdlZF92YWx1ZSA9IEpTT04ucGFyc2UoIGpRdWVyeSggJyN3aF90cmFzaCcgKS52YWwoKSApO1xyXG5cclxuXHRcdHdwYmNfYWp4X2Jvb2tpbmdfc2VuZF9zZWFyY2hfcmVxdWVzdF93aXRoX3BhcmFtcygge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnd2hfdHJhc2gnOiBjaGFuZ2VkX3ZhbHVlWyAwIF0sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdwYWdlX251bSc6IDFcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHR9ICk7XHJcblxyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gQWxsIGJvb2tpbmdzIHwgTmV3IGJvb2tpbmdzXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRqUXVlcnkoICcjd2hfd2hhdF9ib29raW5ncycgKS5vbiggJ2NoYW5nZScsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cclxuXHRcdHZhciBjaGFuZ2VkX3ZhbHVlID0gSlNPTi5wYXJzZSggalF1ZXJ5KCAnI3doX3doYXRfYm9va2luZ3MnICkudmFsKCkgKTtcclxuXHJcblx0XHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doX3doYXRfYm9va2luZ3MnOiBjaGFuZ2VkX3ZhbHVlWyAwIF0sXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdwYWdlX251bSc6IDFcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHR9ICk7XHJcblxyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gXCJDcmVhdGlvbiBEYXRlXCIgICBvZiBib29raW5nc1xyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0alF1ZXJ5KCAnI3doX21vZGlmaWNhdGlvbl9kYXRlJyApLm9uKCAnY2hhbmdlJywgZnVuY3Rpb24oIGV2ZW50ICl7XHJcblxyXG5cdFx0dmFyIGNoYW5nZWRfdmFsdWUgPSBKU09OLnBhcnNlKCBqUXVlcnkoICcjd2hfbW9kaWZpY2F0aW9uX2RhdGUnICkudmFsKCkgKTtcclxuXHJcblx0XHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doX21vZGlmaWNhdGlvbl9kYXRlJzogY2hhbmdlZF92YWx1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3BhZ2VfbnVtJyAgICAgICA6IDEsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIEZyb250ZW5kIHNlbGVjdGVkIGVsZW1lbnRzIChzYXZpbmcgZm9yIGZ1dHVyZSB1c2UsIGFmdGVyIEY1KVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndWlfd2hfbW9kaWZpY2F0aW9uX2RhdGVfcmFkaW8nICAgOiBqUXVlcnkoICdpbnB1dFtuYW1lPVwidWlfd2hfbW9kaWZpY2F0aW9uX2RhdGVfcmFkaW9cIl06Y2hlY2tlZCcgKS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3VpX3doX21vZGlmaWNhdGlvbl9kYXRlX3ByaW9yJyAgIDogalF1ZXJ5KCAnI3VpX3doX21vZGlmaWNhdGlvbl9kYXRlX3ByaW9yJyApLnZhbCgpLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndWlfd2hfbW9kaWZpY2F0aW9uX2RhdGVfY2hlY2tpbicgOiBqUXVlcnkoICcjdWlfd2hfbW9kaWZpY2F0aW9uX2RhdGVfY2hlY2tpbicgKS52YWwoKSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3VpX3doX21vZGlmaWNhdGlvbl9kYXRlX2NoZWNrb3V0JzogalF1ZXJ5KCAnI3VpX3doX21vZGlmaWNhdGlvbl9kYXRlX2NoZWNrb3V0JyApLnZhbCgpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0fSApO1xyXG5cclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFBheW1lbnQgU3RhdHVzXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRqUXVlcnkoICcjd2hfcGF5X3N0YXR1cycgKS5vbiggJ2NoYW5nZScsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cclxuXHRcdHZhciBjaGFuZ2VkX3ZhbHVlID0gSlNPTi5wYXJzZSggalF1ZXJ5KCAnI3doX3BheV9zdGF0dXMnICkudmFsKCkgKTtcclxuXHJcblx0XHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doX3BheV9zdGF0dXMnOiBjaGFuZ2VkX3ZhbHVlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncGFnZV9udW0nICAgICAgIDogMSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gRnJvbnRlbmQgc2VsZWN0ZWQgZWxlbWVudHMgKHNhdmluZyBmb3IgZnV0dXJlIHVzZSwgYWZ0ZXIgRjUpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV93aF9wYXlfc3RhdHVzX3JhZGlvJyA6ICggKCB1bmRlZmluZWQgPT09IGpRdWVyeSggJ2lucHV0W25hbWU9XCJ1aV93aF9wYXlfc3RhdHVzX3JhZGlvXCJdOmNoZWNrZWQnICkudmFsKCkgKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PyAnJ1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0OiBqUXVlcnkoICdpbnB1dFtuYW1lPVwidWlfd2hfcGF5X3N0YXR1c19yYWRpb1wiXTpjaGVja2VkJyApLnZhbCgpXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgICksXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd1aV93aF9wYXlfc3RhdHVzX2N1c3RvbSc6IGpRdWVyeSggJyN1aV93aF9wYXlfc3RhdHVzX2N1c3RvbScgKS52YWwoKVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cclxuXHJcblx0fSApO1xyXG5cclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIE1pbiBDb3N0XHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRqUXVlcnkoICcjd2hfY29zdCcgKS5vbiggJ2NoYW5nZScsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cclxuXHRcdHZhciBjaGFuZ2VkX3ZhbHVlID0galF1ZXJ5KCAnI3doX2Nvc3QnICkudmFsKCk7XHJcblxyXG5cdFx0d3BiY19hanhfYm9va2luZ19zZW5kX3NlYXJjaF9yZXF1ZXN0X3dpdGhfcGFyYW1zKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd3aF9jb3N0JyA6IGNoYW5nZWRfdmFsdWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdwYWdlX251bSc6IDFcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHR9ICk7XHJcblxyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gTWF4IENvc3RcclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdGpRdWVyeSggJyN3aF9jb3N0MicgKS5vbiggJ2NoYW5nZScsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cclxuXHRcdHZhciBjaGFuZ2VkX3ZhbHVlID0galF1ZXJ5KCAnI3doX2Nvc3QyJyApLnZhbCgpO1xyXG5cclxuXHRcdHdwYmNfYWp4X2Jvb2tpbmdfc2VuZF9zZWFyY2hfcmVxdWVzdF93aXRoX3BhcmFtcygge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnd2hfY29zdDInIDogY2hhbmdlZF92YWx1ZSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3BhZ2VfbnVtJzogMVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdH0gKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBCb29raW5nIHJlc291cmNlc1xyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0alF1ZXJ5KCAnI3doX2Jvb2tpbmdfdHlwZScgKS5vbiggJ2NoYW5nZScsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cclxuXHRcdHZhciBjaGFuZ2VkX3ZhbHVlID0gIGpRdWVyeSggJyN3aF9ib29raW5nX3R5cGUnICkudmFsKCk7XHRcdC8vIGl0J3MgZ2V0IGFzIGFycmF5XHJcblx0XHRpZiAoICggQXJyYXkuaXNBcnJheSggY2hhbmdlZF92YWx1ZSApICkgJiYgKCAwID09PSBjaGFuZ2VkX3ZhbHVlLmxlbmd0aCApICl7XHJcblx0XHRcdGNoYW5nZWRfdmFsdWUgPSBbJy0xJ107XHJcblx0XHR9XHJcblx0XHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doX2Jvb2tpbmdfdHlwZScgOiBjaGFuZ2VkX3ZhbHVlLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncGFnZV9udW0nOiAxXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0fSApO1xyXG5cclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBTb3J0aW5nXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRqUXVlcnkoICcjd2hfc29ydCcgKS5vbiggJ2NoYW5nZScsIGZ1bmN0aW9uKCBldmVudCApe1xyXG5cclxuXHRcdHZhciBjaGFuZ2VkX3ZhbHVlID0galF1ZXJ5KCAnI3doX3NvcnQnICkudmFsKCk7XHJcblxyXG5cdFx0Y2hhbmdlZF92YWx1ZSA9IEpTT04ucGFyc2UoIGNoYW5nZWRfdmFsdWUgKTtcclxuXHJcblx0XHR3cGJjX2FqeF9ib29raW5nX3NlbmRfc2VhcmNoX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3doX3NvcnQnOiBjaGFuZ2VkX3ZhbHVlWyAwIF1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHR9ICk7XHJcblxyXG59XHJcblxyXG5qUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCl7XHJcblx0d3BiY19hanhfYm9va2luZ19kZWZpbmVfdWlfaG9va3Nfb25jZSgpO1xyXG59KTtcclxuIl0sIm1hcHBpbmdzIjoiQUFBQSxZQUFZOztBQUVaO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsU0FBU0EscUNBQXFDQSxD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
