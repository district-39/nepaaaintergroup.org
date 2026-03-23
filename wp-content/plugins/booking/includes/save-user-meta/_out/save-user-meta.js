"use strict";

function wpbc_save_custom_user_data_from_element(el) {
  if (typeof WPBC_UserDataSaver === 'undefined') {
    console.error('WPBC | Global AJAX object is missing.');
    return;
  }
  const $el = jQuery(el);
  const user_id = $el.data('wpbc-u-save-user-id');
  const nonce = $el.data('wpbc-u-save-nonce');
  const nonce_action = $el.data('wpbc-u-save-action');
  const data_name = $el.data('wpbc-u-save-name');
  const fields_raw = $el.data('wpbc-u-save-fields') || '';
  const inline_value = $el.data('wpbc-u-save-value');
  const json = $el.data('wpbc-u-save-value-json');
  const callbackFnName = $el.data('wpbc-u-save-callback');
  const callbackFn = typeof window[callbackFnName] === 'function' ? window[callbackFnName] : null;
  if (callbackFnName && !callbackFn) {
    console.warn('WPBC | Callback not found:', callbackFnName);
  }
  if (!user_id || !nonce || !nonce_action || !data_name) {
    console.error('WPBC | Missing required data attributes.');
    return;
  }
  let serialized = '';
  if (typeof json === 'string' && json.trim() !== '') {
    try {
      serialized = jQuery.param(JSON.parse(json));
    } catch (e) {
      console.error('WPBC | Invalid JSON in data-wpbc-u-save-value-json');
      return;
    }
  } else if (inline_value !== undefined) {
    // Save simple direct value as single param.
    serialized = jQuery.param({
      value: inline_value
    });
  } else if (fields_raw) {
    const fields = fields_raw.split(',').map(s => s.trim()).filter(Boolean);
    const data = {};
    fields.forEach(function (selector) {
      const $field = jQuery(selector);
      if ($field.length) {
        const key = $field.attr('name') || $field.attr('id');
        if (key) {
          data[key] = $field.val();
        } else {
          console.warn('WPBC | Field missing name/id:', $field);
        }
      }
    });
    serialized = jQuery.param(data);
  } else {
    console.error('WPBC | Missing data-wpbc-u-save-fields or data-wpbc-u-save-value.');
    return;
  }
  jQuery(document).trigger('wpbc:userdata:beforeSave', [$el, serialized]);
  jQuery.ajax({
    url: WPBC_UserDataSaver.ajax_url,
    type: 'POST',
    data: {
      action: WPBC_UserDataSaver.action,
      user_id: user_id,
      nonce: nonce,
      nonce_action: nonce_action,
      data_name: data_name,
      data_value: serialized
    },
    success: function (response) {
      jQuery(document).trigger('wpbc:userdata:afterSave', [response]);
      if (response.success) {
        // console.log( 'WPBC | ' + (response.data.message || 'Saved successfully.') );
        if (callbackFn) {
          callbackFn(response);
        }
      } else {
        console.error('WPBC | ' + (response.data.message || 'Save error.'));
      }
    },
    error: function (xhr) {
      console.error('WPBC | AJAX error: ' + xhr.status + ' ' + xhr.statusText);
    }
  });
}

/**
 * Note:
 // This binds a handler *to event 'click'*, labeled 'myFeature'
$(document).on('click.myFeature', function () { ... });

// This binds another *to 'click'*, labeled 'debug'
$(document).on('click.debug', function () { ... });

// This fires the event 'click' — both will run:
$(document).trigger('click');

// This removes only handlers with .myFeature
$(document).off('click.myFeature');
 */

// jQuery( document ).on( 'wpbc:userdata:beforeSave.customLogger', function (e, $el, data) {
// 	// $el.prop('disabled', true);  // Example: disable the clicked element
// } );

// jQuery( document ).on( 'wpbc:userdata:afterSave.customLogger', function (e, response) {
//
// 	console.log( 'Save finished. Server responded with:', response );
//
// 	if ( response.success ) {
// 		console.log( 'Saved!' );
// 	} else {
// 		console.log( 'Error: ' + ( response.data?.message || 'Unknown error' ) );
// 	}
//
// 	// Optional: re-enable all save buttons
// 	// jQuery('[data-wpbc-u-save-name]').prop('disabled', false);
// } );

// To remove it:
// jQuery(document).off('wpbc:userdata:afterSave.customLogger');
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvc2F2ZS11c2VyLW1ldGEvX291dC9zYXZlLXVzZXItbWV0YS5qcyIsIm5hbWVzIjpbIndwYmNfc2F2ZV9jdXN0b21fdXNlcl9kYXRhX2Zyb21fZWxlbWVudCIsImVsIiwiV1BCQ19Vc2VyRGF0YVNhdmVyIiwiY29uc29sZSIsImVycm9yIiwiJGVsIiwialF1ZXJ5IiwidXNlcl9pZCIsImRhdGEiLCJub25jZSIsIm5vbmNlX2FjdGlvbiIsImRhdGFfbmFtZSIsImZpZWxkc19yYXciLCJpbmxpbmVfdmFsdWUiLCJqc29uIiwiY2FsbGJhY2tGbk5hbWUiLCJjYWxsYmFja0ZuIiwid2luZG93Iiwid2FybiIsInNlcmlhbGl6ZWQiLCJ0cmltIiwicGFyYW0iLCJKU09OIiwicGFyc2UiLCJlIiwidW5kZWZpbmVkIiwidmFsdWUiLCJmaWVsZHMiLCJzcGxpdCIsIm1hcCIsInMiLCJmaWx0ZXIiLCJCb29sZWFuIiwiZm9yRWFjaCIsInNlbGVjdG9yIiwiJGZpZWxkIiwibGVuZ3RoIiwia2V5IiwiYXR0ciIsInZhbCIsImRvY3VtZW50IiwidHJpZ2dlciIsImFqYXgiLCJ1cmwiLCJhamF4X3VybCIsInR5cGUiLCJhY3Rpb24iLCJkYXRhX3ZhbHVlIiwic3VjY2VzcyIsInJlc3BvbnNlIiwibWVzc2FnZSIsInhociIsInN0YXR1cyIsInN0YXR1c1RleHQiXSwic291cmNlcyI6WyJpbmNsdWRlcy9zYXZlLXVzZXItbWV0YS9fc3JjL3NhdmUtdXNlci1tZXRhLmpzIl0sInNvdXJjZXNDb250ZW50IjpbImZ1bmN0aW9uIHdwYmNfc2F2ZV9jdXN0b21fdXNlcl9kYXRhX2Zyb21fZWxlbWVudChlbCkge1xyXG5cclxuXHRpZiAoIHR5cGVvZiBXUEJDX1VzZXJEYXRhU2F2ZXIgPT09ICd1bmRlZmluZWQnICkge1xyXG5cdFx0Y29uc29sZS5lcnJvciggJ1dQQkMgfCBHbG9iYWwgQUpBWCBvYmplY3QgaXMgbWlzc2luZy4nICk7XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG5cclxuXHRjb25zdCAkZWwgPSBqUXVlcnkoIGVsICk7XHJcblxyXG5cdGNvbnN0IHVzZXJfaWQgICAgICA9ICRlbC5kYXRhKCAnd3BiYy11LXNhdmUtdXNlci1pZCcgKTtcclxuXHRjb25zdCBub25jZSAgICAgICAgPSAkZWwuZGF0YSggJ3dwYmMtdS1zYXZlLW5vbmNlJyApO1xyXG5cdGNvbnN0IG5vbmNlX2FjdGlvbiA9ICRlbC5kYXRhKCAnd3BiYy11LXNhdmUtYWN0aW9uJyApO1xyXG5cdGNvbnN0IGRhdGFfbmFtZSAgICA9ICRlbC5kYXRhKCAnd3BiYy11LXNhdmUtbmFtZScgKTtcclxuXHRjb25zdCBmaWVsZHNfcmF3ICAgPSAkZWwuZGF0YSggJ3dwYmMtdS1zYXZlLWZpZWxkcycgKSB8fCAnJztcclxuXHRjb25zdCBpbmxpbmVfdmFsdWUgPSAkZWwuZGF0YSggJ3dwYmMtdS1zYXZlLXZhbHVlJyApO1xyXG5cdGNvbnN0IGpzb24gICAgICAgICA9ICRlbC5kYXRhKCAnd3BiYy11LXNhdmUtdmFsdWUtanNvbicgKTtcclxuXHJcblx0Y29uc3QgY2FsbGJhY2tGbk5hbWUgPSAkZWwuZGF0YSggJ3dwYmMtdS1zYXZlLWNhbGxiYWNrJyApO1xyXG5cdGNvbnN0IGNhbGxiYWNrRm4gICAgID0gdHlwZW9mIHdpbmRvd1tjYWxsYmFja0ZuTmFtZV0gPT09ICdmdW5jdGlvbicgPyB3aW5kb3dbY2FsbGJhY2tGbk5hbWVdIDogbnVsbDtcclxuXHRpZiAoIGNhbGxiYWNrRm5OYW1lICYmICFjYWxsYmFja0ZuICkge1xyXG5cdFx0Y29uc29sZS53YXJuKCAnV1BCQyB8IENhbGxiYWNrIG5vdCBmb3VuZDonLCBjYWxsYmFja0ZuTmFtZSApO1xyXG5cdH1cclxuXHJcblx0aWYgKCAhdXNlcl9pZCB8fCAhbm9uY2UgfHwgIW5vbmNlX2FjdGlvbiB8fCAhZGF0YV9uYW1lICkge1xyXG5cdFx0Y29uc29sZS5lcnJvciggJ1dQQkMgfCBNaXNzaW5nIHJlcXVpcmVkIGRhdGEgYXR0cmlidXRlcy4nICk7XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG5cclxuXHRsZXQgc2VyaWFsaXplZCA9ICcnO1xyXG5cclxuXHRpZiAoIHR5cGVvZiBqc29uID09PSAnc3RyaW5nJyAmJiBqc29uLnRyaW0oKSAhPT0gJycgKSB7XHJcblx0XHR0cnkge1xyXG5cdFx0XHRzZXJpYWxpemVkID0galF1ZXJ5LnBhcmFtKCBKU09OLnBhcnNlKCBqc29uICkgKTtcclxuXHRcdH0gY2F0Y2ggKCBlICkge1xyXG5cdFx0XHRjb25zb2xlLmVycm9yKCAnV1BCQyB8IEludmFsaWQgSlNPTiBpbiBkYXRhLXdwYmMtdS1zYXZlLXZhbHVlLWpzb24nICk7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHR9IGVsc2UgaWYgKCBpbmxpbmVfdmFsdWUgIT09IHVuZGVmaW5lZCApIHtcclxuXHRcdC8vIFNhdmUgc2ltcGxlIGRpcmVjdCB2YWx1ZSBhcyBzaW5nbGUgcGFyYW0uXHJcblx0XHRzZXJpYWxpemVkID0galF1ZXJ5LnBhcmFtKCB7IHZhbHVlOiBpbmxpbmVfdmFsdWUgfSApO1xyXG5cdH0gZWxzZSBpZiAoIGZpZWxkc19yYXcgKSB7XHJcblx0XHRjb25zdCBmaWVsZHMgPSBmaWVsZHNfcmF3LnNwbGl0KCAnLCcgKS5tYXAoIHMgPT4gcy50cmltKCkgKS5maWx0ZXIoIEJvb2xlYW4gKTtcclxuXHRcdGNvbnN0IGRhdGEgICA9IHt9O1xyXG5cclxuXHRcdGZpZWxkcy5mb3JFYWNoKCBmdW5jdGlvbiAoc2VsZWN0b3IpIHtcclxuXHRcdFx0Y29uc3QgJGZpZWxkID0galF1ZXJ5KCBzZWxlY3RvciApO1xyXG5cdFx0XHRpZiAoICRmaWVsZC5sZW5ndGggKSB7XHJcblx0XHRcdFx0Y29uc3Qga2V5ID0gJGZpZWxkLmF0dHIoICduYW1lJyApIHx8ICRmaWVsZC5hdHRyKCAnaWQnICk7XHJcblx0XHRcdFx0aWYgKCBrZXkgKSB7XHJcblx0XHRcdFx0XHRkYXRhW2tleV0gPSAkZmllbGQudmFsKCk7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdGNvbnNvbGUud2FybiggJ1dQQkMgfCBGaWVsZCBtaXNzaW5nIG5hbWUvaWQ6JywgJGZpZWxkICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9ICk7XHJcblxyXG5cdFx0c2VyaWFsaXplZCA9IGpRdWVyeS5wYXJhbSggZGF0YSApO1xyXG5cdH0gZWxzZSB7XHJcblx0XHRjb25zb2xlLmVycm9yKCAnV1BCQyB8IE1pc3NpbmcgZGF0YS13cGJjLXUtc2F2ZS1maWVsZHMgb3IgZGF0YS13cGJjLXUtc2F2ZS12YWx1ZS4nICk7XHJcblx0XHRyZXR1cm47XHJcblx0fVxyXG5cclxuXHRqUXVlcnkoIGRvY3VtZW50ICkudHJpZ2dlciggJ3dwYmM6dXNlcmRhdGE6YmVmb3JlU2F2ZScsIFsgJGVsLCBzZXJpYWxpemVkIF0gKTtcclxuXHJcblx0alF1ZXJ5LmFqYXgoIHtcclxuXHRcdHVybCAgICA6IFdQQkNfVXNlckRhdGFTYXZlci5hamF4X3VybCxcclxuXHRcdHR5cGUgICA6ICdQT1NUJyxcclxuXHRcdGRhdGEgICA6IHtcclxuXHRcdFx0YWN0aW9uICAgICAgOiBXUEJDX1VzZXJEYXRhU2F2ZXIuYWN0aW9uLFxyXG5cdFx0XHR1c2VyX2lkICAgICA6IHVzZXJfaWQsXHJcblx0XHRcdG5vbmNlICAgICAgIDogbm9uY2UsXHJcblx0XHRcdG5vbmNlX2FjdGlvbjogbm9uY2VfYWN0aW9uLFxyXG5cdFx0XHRkYXRhX25hbWUgICA6IGRhdGFfbmFtZSxcclxuXHRcdFx0ZGF0YV92YWx1ZSAgOiBzZXJpYWxpemVkXHJcblx0XHR9LFxyXG5cdFx0c3VjY2VzczogZnVuY3Rpb24gKHJlc3BvbnNlKSB7XHJcblxyXG5cdFx0XHRqUXVlcnkoIGRvY3VtZW50ICkudHJpZ2dlciggJ3dwYmM6dXNlcmRhdGE6YWZ0ZXJTYXZlJywgWyByZXNwb25zZSBdICk7XHJcblxyXG5cdFx0XHRpZiAoIHJlc3BvbnNlLnN1Y2Nlc3MgKSB7XHJcblx0XHRcdFx0Ly8gY29uc29sZS5sb2coICdXUEJDIHwgJyArIChyZXNwb25zZS5kYXRhLm1lc3NhZ2UgfHwgJ1NhdmVkIHN1Y2Nlc3NmdWxseS4nKSApO1xyXG5cdFx0XHRcdGlmICggY2FsbGJhY2tGbiApIHtcclxuXHRcdFx0XHRcdGNhbGxiYWNrRm4oIHJlc3BvbnNlICk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdGNvbnNvbGUuZXJyb3IoICdXUEJDIHwgJyArICggcmVzcG9uc2UuZGF0YS5tZXNzYWdlIHx8ICdTYXZlIGVycm9yLicgKSApO1xyXG5cdFx0XHR9XHJcblx0XHR9LFxyXG5cdFx0ZXJyb3IgIDogZnVuY3Rpb24gKCB4aHIgKSB7XHJcblx0XHRcdGNvbnNvbGUuZXJyb3IoICdXUEJDIHwgQUpBWCBlcnJvcjogJyArIHhoci5zdGF0dXMgKyAnICcgKyB4aHIuc3RhdHVzVGV4dCApO1xyXG5cdFx0fVxyXG5cdH0gKTtcclxufVxyXG5cclxuLyoqXHJcbiAqIE5vdGU6XHJcbiAvLyBUaGlzIGJpbmRzIGEgaGFuZGxlciAqdG8gZXZlbnQgJ2NsaWNrJyosIGxhYmVsZWQgJ215RmVhdHVyZSdcclxuJChkb2N1bWVudCkub24oJ2NsaWNrLm15RmVhdHVyZScsIGZ1bmN0aW9uICgpIHsgLi4uIH0pO1xyXG5cclxuLy8gVGhpcyBiaW5kcyBhbm90aGVyICp0byAnY2xpY2snKiwgbGFiZWxlZCAnZGVidWcnXHJcbiQoZG9jdW1lbnQpLm9uKCdjbGljay5kZWJ1ZycsIGZ1bmN0aW9uICgpIHsgLi4uIH0pO1xyXG5cclxuLy8gVGhpcyBmaXJlcyB0aGUgZXZlbnQgJ2NsaWNrJyDigJQgYm90aCB3aWxsIHJ1bjpcclxuJChkb2N1bWVudCkudHJpZ2dlcignY2xpY2snKTtcclxuXHJcbi8vIFRoaXMgcmVtb3ZlcyBvbmx5IGhhbmRsZXJzIHdpdGggLm15RmVhdHVyZVxyXG4kKGRvY3VtZW50KS5vZmYoJ2NsaWNrLm15RmVhdHVyZScpO1xyXG4gKi9cclxuXHJcblxyXG4vLyBqUXVlcnkoIGRvY3VtZW50ICkub24oICd3cGJjOnVzZXJkYXRhOmJlZm9yZVNhdmUuY3VzdG9tTG9nZ2VyJywgZnVuY3Rpb24gKGUsICRlbCwgZGF0YSkge1xyXG4vLyBcdC8vICRlbC5wcm9wKCdkaXNhYmxlZCcsIHRydWUpOyAgLy8gRXhhbXBsZTogZGlzYWJsZSB0aGUgY2xpY2tlZCBlbGVtZW50XHJcbi8vIH0gKTtcclxuXHJcbi8vIGpRdWVyeSggZG9jdW1lbnQgKS5vbiggJ3dwYmM6dXNlcmRhdGE6YWZ0ZXJTYXZlLmN1c3RvbUxvZ2dlcicsIGZ1bmN0aW9uIChlLCByZXNwb25zZSkge1xyXG4vL1xyXG4vLyBcdGNvbnNvbGUubG9nKCAnU2F2ZSBmaW5pc2hlZC4gU2VydmVyIHJlc3BvbmRlZCB3aXRoOicsIHJlc3BvbnNlICk7XHJcbi8vXHJcbi8vIFx0aWYgKCByZXNwb25zZS5zdWNjZXNzICkge1xyXG4vLyBcdFx0Y29uc29sZS5sb2coICdTYXZlZCEnICk7XHJcbi8vIFx0fSBlbHNlIHtcclxuLy8gXHRcdGNvbnNvbGUubG9nKCAnRXJyb3I6ICcgKyAoIHJlc3BvbnNlLmRhdGE/Lm1lc3NhZ2UgfHwgJ1Vua25vd24gZXJyb3InICkgKTtcclxuLy8gXHR9XHJcbi8vXHJcbi8vIFx0Ly8gT3B0aW9uYWw6IHJlLWVuYWJsZSBhbGwgc2F2ZSBidXR0b25zXHJcbi8vIFx0Ly8galF1ZXJ5KCdbZGF0YS13cGJjLXUtc2F2ZS1uYW1lXScpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xyXG4vLyB9ICk7XHJcblxyXG4vLyBUbyByZW1vdmUgaXQ6XHJcbi8vIGpRdWVyeShkb2N1bWVudCkub2ZmKCd3cGJjOnVzZXJkYXRhOmFmdGVyU2F2ZS5jdXN0b21Mb2dnZXInKTsiXSwibWFwcGluZ3MiOiI7O0FBQUEsU0FBU0EsdUNBQXVDQSxDQUFDQyxFQUFFLEVBQUU7RUFFcEQsSUFBSyxPQUFPQyxrQkFBa0IsS0FBSyxXQUFXLEVBQUc7SUFDaERDLE9BQU8sQ0FBQ0MsS0FBSyxDQUFFLHVDQUF3QyxDQUFDO0lBQ3hEO0VBQ0Q7RUFFQSxNQUFNQyxHQUFHLEdB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VSxHQUFHYixNQUFNLENBQUNlLEtBQUssQ0FBRWIsSUFBSyxDQUFDO0VBQ2xDLENBQUMsTUFBTTtJQUNOTCxPQUFPLENBQUNDLEtBQUssQ0FBRSxtRUFBb0UsQ0FBQztJQUNwRjtFQUNEO0VBRUFFLE1BQU0sQ0FBRWtDLFFBQVMsQ0FBQyxDQUFDQyxPQUFPLENBQUUsMEJBQTBCLEVBQUUsQ0FBRXBDLEdBQUcsRUFBRWMsVUFBVSxDQUFHLENBQUM7RUFFN0ViLE1BQU0sQ0FBQ29DLElBQUksQ0FBRTtJQUNaQyxHQUFHLEVBQU16QyxrQkFBa0IsQ0FBQzBDLFFBQVE7SUFDcENDLElBQUksRUFBSyxNQUFNO0lBQ2ZyQyxJQUFJLEVBQUs7TUFDUnNDLE1BQU0sRUFBUTVDLGtCQUFrQixDQUFDNEMsTUFBTTtNQUN2Q3ZDLE9BQU8sRUFBT0EsT0FBTztNQUNyQkUsS0FBSyxFQUFTQSxLQUFLO01BQ25CQyxZQUFZLEVBQUVBLFlBQVk7TUFDMUJDLFNBQVMsRUFBS0EsU0FBUztNQUN2Qm9DLFVBQVUsRUFBSTVCO0lBQ2YsQ0FBQztJQUNENkIsT0FBTyxFQUFFLFNBQUFBLENBQVVDLFFBQVEsRUFBRTtNQUU1QjNDLE1BQU0sQ0FBRWtDLFFBQVMsQ0FBQyxDQUFDQyxPQUFPLENBQUUseUJBQXlCLEVBQUUsQ0FBRVEsUUFBUSxDQUFHLENBQUM7TUFFckUsSUFBS0EsUUFBUSxDQUFDRCxPQUFPLEVBQUc7UUFDdkI7UUFDQSxJQUFLaEMsVUFBVSxFQUFHO1VBQ2pCQSxVQUFVLENBQUVpQyxRQUFTLENBQUM7UUFDdkI7TUFDRCxDQUFDLE1BQU07UUFDTjlDLE9BQU8sQ0FBQ0MsS0FBSyxDQUFFLFNBQVMsSUFBSzZDLFFBQVEsQ0FBQ3pDLElBQUksQ0FBQzBDLE9BQU8sSUFBSSxhQUFhLENBQUcsQ0FBQztNQUN4RTtJQUNELENBQUM7SUFDRDlDLEtBQUssRUFBSSxTQUFBQSxDQUFXK0MsR0FBRyxFQUFHO01BQ3pCaEQsT0FBTyxDQUFDQyxLQUFLLENBQUUscUJBQXFCLEdBQUcrQyxHQUFHLENBQUNDLE1BQU0sR0FBRyxHQUFHLEdBQUdELEdBQUcsQ0FBQ0UsVUFBVyxDQUFDO0lBQzNFO0VBQ0QsQ0FBRSxDQUFDO0FBQ0o7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFHQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQSIsImlnbm9yZUxpc3QiOltdfQ==
