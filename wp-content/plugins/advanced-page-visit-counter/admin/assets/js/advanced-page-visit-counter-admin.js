(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	const confirmCleanOldData = document.querySelector('#confirm-clean');
	const confirmText = document.querySelector('#confirm-text');
	let bsRangePickerRange = $('#bs-rangepicker-range');
	let dashCharts = $('#apvc_dashboard_chart');
	let loadMoreSpinnerReports = $('.apvc-load-more-reports');
	let loadMore = $('.apvc-load-more');
	let filtersSubmit = $('#advanced_filters_rp');
	let filtersSubmitPl = $('#advanced_filters_pl');
	let generateShortcode = $('.apvc-generate-shortcode');
	let copyShortcode = $('#apvc-copy-shortcode');
	let copyCampaignURL = $('.copy_campaign_url');
	let copyCampaignShortURL = $('.copy_campaign_short_url');
	let resetShortcode = $('#apvc-reset-shortcode');
	let saveShortcode = $('#save_shortcode');
	let deleteShortcode = $('.delete-sh');
	let copyShortcodeList = $('.copy-sh');
	let setStartingCount = $('.set_starting_count');
	let setStartingCountSubmit = $('#set_starting_count');
	let migrateData = $('#apvc_notice_hide');
	let migrationStart = $('#apvc_start_migration');
	let apvc_go_with_fresh = $('#apvc_go_with_fresh');
	let cleanConfirm = $('.cleanConfirm');
	let cleanCancel = $('.cleanCancel');

	function apvc_number_format( num ) {
		if( 'yes' === APVC_ADMIN.apvc_convert_num_to_k ){
			return Math.abs(num) > 999 ? Math.sign(num)*((Math.abs(num)/1000).toFixed(1)) + 'k' : Math.sign(num)*Math.abs(num);
		}
	}

	// Alert With Functional Confirm Button
	if (confirmText) {
		confirmText.onclick = function () {
			Swal.fire({
				title: APVC_ADMIN.reset_delete_warning_title,
				text: APVC_ADMIN.reset_delete_warning_text,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: APVC_ADMIN.reset_delete_warning_conf_text,
				customClass: {
					confirmButton: 'btn btn-primary me-3',
					cancelButton: 'btn btn-label-secondary'
				},
				buttonsStyling: false
			}).then(function (result) {
				if (result.value) {
					Swal.fire({
						icon: 'success',
						title: APVC_ADMIN.reset_delete_success_title,
						text: APVC_ADMIN.reset_delete_success_text,
						customClass: {
							confirmButton: 'btn btn-success'
						}
					});
					jQuery.ajax({
						type: 'GET',
						url: APVC_ADMIN.reset_everything,
						success: function (response) {}
					});
				}
			});
		};
	}

	if (bsRangePickerRange.length) {
		bsRangePickerRange.daterangepicker({
			ranges: {
				Today: [moment(), moment()],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				'Last Quarter': [moment().subtract(3, 'month').startOf('month'), moment().endOf('month')],
				'Last 6 Months': [moment().subtract(6, 'month').startOf('month'), moment().endOf('month')],
				'Last Year': [moment().subtract(12, 'month').startOf('month'), moment().endOf('month')],
			},
			startDate: moment().subtract(6, 'days'),
			endDate: moment(),
			opens: 'right'
		}).on('change', function(){
			let startDate =  $("#bs-rangepicker-range").data('daterangepicker').startDate.format('YYYY-MM-DD 00:00:00');
			let endDate =  $("#bs-rangepicker-range").data('daterangepicker').endDate.format('YYYY-MM-DD 23:59:59');
			let page = $("#apvc_page_id").val();
			jQuery.ajax({
				type: 'GET',
				url: APVC_ADMIN.dashboard_rest_url,
				beforeSend: function (xhr) {
					jQuery("#apvc-overlay").show();
					xhr.setRequestHeader('X-WP-Nounce', APVC_ADMIN.apvcWPnonce );
				},
				data: {
					startDate: startDate,
					endDate: endDate,
				},
				success: function (response) {
					if( false === response ){
						return;
					}
					jQuery(".apvc_dash_visitors_rng").text(response.stats.visitors);
					jQuery(".apvc_dash_views_rng").text(response.stats.views);
					jQuery(".apvc_dash_sessions_rng").text(response.stats.sessions);
					jQuery(".apvc_dash_unq_vis_rng").text(response.stats.unq_visitors);
					jQuery("#apvc-overlay").hide();
					$('html, body').animate({
						scrollTop: $("#apvc_advanced_filters").offset().top
					}, 100);
					apvc_get_trending_data( startDate, endDate );
					apvc_dashboard_chart( response.all_stats );
				}
			});
		});
	}

	if (dashCharts.length) {
		jQuery.ajax({
			type: 'GET',
			url: APVC_ADMIN.dashboard_rest_url,
			beforeSend: function (xhr) {},
			success: function (response) {
				if (false === response) {
					return;
				}
				apvc_dashboard_chart(response.all_stats);
			}
		});
	}

	function apvc_get_trending_data( startDate, endDate ){
		jQuery.ajax({
			type: 'GET',
			url: APVC_ADMIN.refer_refresh_url,
			beforeSend: function (xhr) {
				jQuery("#apvc-overlay").show();
				xhr.setRequestHeader('X-WP-Nounce', APVC_ADMIN.apvcWPnonce );
			},
			data: {
				startDate: startDate,
				endDate: endDate,
			},
			success: function (response) {
				if( false === response ){
					return;
				}
				jQuery(".apvc_ref_name").text(response);
				jQuery("#apvc-overlay").hide();
				$('html, body').animate({
					scrollTop: $("#apvc_advanced_filters").offset().top
				}, 100);
			}
		});
	}
	function apvc_dashboard_chart( response ) {
		const lineAreaChart = document.getElementById('apvc_dashboard_chart');
		if (lineAreaChart) {
			const greyLightColor = '#EDF1F4',
				blueColor = '#2B9AFF',
				blueLightColor = '#84D0FF';

			let labelColor, borderColor, legendColor;
			labelColor = config.colors.textMuted;
			legendColor = config.colors.bodyColor;
			borderColor = config.colors.borderColor;

			let chartConfiguration = {
				type: 'line',
				data: {
					labels: response.date,
					datasets: [
						{
							label: APVC_ADMIN.lb_visitors,
							data: response.visitors,
							tension: 0,
							fill: true,
							backgroundColor: blueColor,
							pointStyle: 'circle',
							borderColor: 'transparent',
						},
						{
							label: APVC_ADMIN.lb_views,
							data: response.views,
							tension: 0,
							fill: true,
							backgroundColor: blueLightColor,
							pointStyle: 'circle',
							borderColor: 'transparent',
						},
						{
							label: APVC_ADMIN.lb_sessions,
							data: response.sessions,
							tension: 0,
							fill: true,
							backgroundColor: greyLightColor,
							pointStyle: 'circle',
							borderColor: 'transparent',
						}
					]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							align: 'start',
							labels: {
								usePointStyle: true,
								padding: 30,
								margin: 10,
								boxWidth: 6,
								boxHeight: 6,
								color: legendColor,
								backgroundColor: legendColor
							}
						}
					},
					scales: {
						x: {
							grid: {
								color: 'transparent',
								borderColor: borderColor
							},
							ticks: {
								color: labelColor
							}
						},
						y: {
							min: 0,
							max: ( Math.ceil((parseInt( Math.max.apply(null, response.views ) ) + 20)/10)*10 ),
							grid: {
								color: 'transparent',
								borderColor: borderColor
							},
							ticks: {
								stepSize: parseInt( Math.max.apply(null, response.views ) / 20 ),
								color: labelColor
							}
						}
					}
				}
			}
			let lineAreaChartVar = null;

			let chartStatus = Chart.getChart("apvc_dashboard_chart");
			if (chartStatus != undefined) {
				chartStatus.destroy();
			}
			lineAreaChartVar =  new Chart(lineAreaChart, chartConfiguration);
		}
	}

	if (loadMoreSpinnerReports.length) {
		$(window).scroll(function() {
			if($(window).scrollTop() + $(window).height() == $(document).height()) {
				let p_id = $("#apvc-reports-pageid").val();
				let offset = $("#apvc-reports-off").val();
				let page_status = $("#apvc-reports-pagi-end").val();
				const startDate = $("#startDate").val();
				const endDate = $("#endDate").val();
				const device_type = $("#apvc_rp_device_type").val();
				const device_os = $("#apvc_rp_device_os").val();
				const device_br = $("#apvc_rp_device_br").val();

				if( 1 === parseInt(page_status) ){
					return;
				}

				jQuery.ajax({
					type: 'GET',
					url: APVC_ADMIN.reports_det_page_rest_url,
					beforeSend: function (xhr) {
						$(".apvc-load-more-reports").css('display','block');
					},
					data: {
						p_id: p_id,
						startDate: startDate,
						endDate: endDate,
						device_type: device_type,
						device_os: device_os,
						device_br: device_br,
						per_page: 10,
						limit: offset,
					},
					success: function (response) {
						if( false === response ){
							return;
						}
						if( response.reports ) {
							if( response.reports.length > 0 ){
								let obj = response.reports;
								let html = '';
								for ( var i = 0, l = obj.length; i < l; i++ ) {
									html += '<tr>\n' +
										'<td>'+ obj[i].singular_id +'</td>\n' +
										'<td>'+ obj[i].stored_title +' <a href="'+ obj[i].stored_url +'" target="_blank">  <i class=\'bx bx-link-external\'></i></a></td>\n' +
										'<td>'+ obj[i].stored_type +'</td>\n' +
										'<td>'+ obj[i].viewed_at +'</td>\n' +
										'<td>' +
										'<strong>'+ APVC_ADMIN.device_type_label +'</strong> '+ obj[i].device_type +'<br />' +
										'<strong>'+ APVC_ADMIN.device_os_label +'</strong> '+ obj[i].device_os +'<br />' +
										'<strong>'+ APVC_ADMIN.device_br_label +'</strong> '+ obj[i].device_browser +'<br />' +
										'<strong>'+ APVC_ADMIN.device_br_ver_label +'</strong> '+ obj[i].device_browser_ver +'<br />' +
										'</td>\n' +
										'<td><b>'+APVC_ADMIN.lb_city+'</b> '+ obj[i].city +' <br /><b>'+APVC_ADMIN.lb_state+'</b> '+ obj[i].state_name +' <br /><b>'+APVC_ADMIN.lb_country+'</b> '+ obj[i].country +' <br /></td>\n' +
										'</tr>';
								}
								offset = parseInt(offset) + 10;
								$("#apvc-reports-off").val( offset );
								$('.reports-table-body').append( html );
							}
							if( response.reports.length === 0 ){
								$("#apvc-reports-pagi-end").val( 1 );
							}
						}
						$(".apvc-load-more-reports").css('display','none');
					}
				});

			}
		});
	}

	if (loadMore.length) {
		$(window).scroll(function() {

			if($(window).scrollTop() + $(window).height() == $(document).height()) {

				let offset = $("#apvc-reports-off").val();
				let page_status = $("#apvc-reports-pagi-end").val();
				const post_type = $("#apvc_post_type").val();
			console.log(post_type);
				jQuery.ajax({
					type: 'GET',
					url: APVC_ADMIN.get_reports_stats_with_range,
					beforeSend: function (xhr) {
						$(".apvc-load-more").css('display','block');
					},
					data: {
						post_type: post_type,
						per_page: 10,
						limit: offset,
					},
					success: function (response) {
						if( false === response ){
							return;
						}
						if( response.reports ) {
							if( response.reports.length > 0 ){
								let obj = response.reports;
								let html = '';
								for ( var i = 0, l = obj.length; i < l; i++ ) {
									html += '<tr>\n' +
										'<td>'+ obj[i].id +'</td>\n' +
										'<td>'+ obj[i].stored_title +' <a href="'+ obj[i].stored_url +'" target="_blank">  <i class=\'bx bx-link-external\'></i></a></td>\n' +
										'<td>' +
										'<strong>'+ APVC_ADMIN.starting_count_label+'</strong> '+ apvc_number_format( obj[i].starting_count ) +'<br/>' +
										'<strong>'+ APVC_ADMIN.page_views_label+'</strong> '+ apvc_number_format( obj[i].views ) +'<br/>' +
										'<strong>'+ APVC_ADMIN.user_sessions_label+'</strong> '+ apvc_number_format( obj[i].user_sessions ) +'<br/>' +
										'</td>\n' +
										'<td>'+ obj[i].stored_type_label +'</td>\n' +
										'<td><a href="'+ APVC_ADMIN.dashboard_link +'&section=apvc-reports&sub_section=repots&p_id='+ obj[i].singular_id +'">'+APVC_ADMIN.view_more_data+'</a></td>\n' +
										'<td><a href="#" class="set_starting_count" data-id="'+ obj[i].singular_id +'">'+APVC_ADMIN.set_starting_count_str+'</a></td>\n' +
										'</tr>';
								}
								offset = parseInt(offset) + 10;
								$("#apvc-reports-off").val( offset );
								$("#apvc_post_type").val( post_type );
								$('.reports-table-body').append( html );
							}
							if( response.reports.length === 0 ){
								$("#apvc-reports-pagi-end").val( 1 );
							}
						}
						$(".apvc-load-more").css('display','none');
					}
				});

			}
		});
	}

	if (filtersSubmit.length) {
		$(filtersSubmit).on('click', function( e ){
			e.preventDefault();
			const startDate = $("#bs-rangepicker-range").data('daterangepicker').startDate.format('YYYY-MM-DD');
			const endDate = $("#bs-rangepicker-range").data('daterangepicker').endDate.format('YYYY-MM-DD');
			const device_type = $("#apvc_rp_device_type").val();
			const device_os = $("#apvc_rp_device_os").val();
			const device_br = $("#apvc_rp_device_br").val();

			const set_filter = true;
			let currLoc = $(location).attr('href');
			let URL = currLoc;
			let condition = '';
			if( startDate !== null ){
				condition += '&startDate='+startDate+'&endDate='+endDate;
			}
			if( device_type !== null ){
				condition += '&device_type='+device_type;
			}
			if( device_os !== null ){
				condition += '&device_os='+device_os;
			}
			if( device_br !== null ){
				condition += '&device_br='+device_br;
			}
			window.location.href = URL+condition+'&set_filter='+set_filter;
		});
	}

	if (filtersSubmitPl.length) {
		$(filtersSubmitPl).on('click', function( e ){
			e.preventDefault();
			const page_type = $("#page_type").val();
			const apvc_list_post_types = $("#apvc_list_post_types").val();

			if( page_type === null ){
				return;
			}

			const set_filter = true;
			let currLoc = $(location).attr('href');
			let URL = currLoc;
			let condition = '';
			if( apvc_list_post_types !== null ){
				condition += '&post_type='+apvc_list_post_types;
			}
			window.location.href = URL+condition+'&set_filter='+set_filter;
		});
	}
	if (generateShortcode.length) {
		$(generateShortcode).on('click', function( e ){
			e.preventDefault();
			let form = document.querySelector('#sh_generate_frm');
			const formData = new FormData(form);
			const values = [...formData.entries()];

			jQuery.ajax({
				type: 'GET',
				url: APVC_ADMIN.generate_shortcode,
				beforeSend: function (xhr) {},
				data: {
					'border_size': formData.get('border_size') ,
					'border_radius': formData.get('border_radius') ,
					'border-style': formData.get('border-style') ,
					'border_color': formData.get('border_color') ,
					'font_color': formData.get('font_color') ,
					'background_color': formData.get('background_color') ,
					'font-style': formData.get('font-style') ,
					'padding': formData.get('padding') ,
					'width': formData.get('width') ,
					'for_specific_post': formData.get('for_specific_post') ,
					'show_icon': formData.get('show_icon') ,
					'show_today_visits': formData.get('show_today_visits') ,
					'show_global_visits': formData.get('show_global_visits') ,
					'show_current_total_visits': formData.get('show_current_total_visits') ,
					'counter_label': formData.get('counter_label') ,
					'today_counter_label': formData.get('today_counter_label') ,
					'global_counter_label': formData.get('global_counter_label') ,
					'widget-template': formData.get('widget-template') ,
				},
				success: function (response) {
					if( true === response.success ){
						$('.show_shortcode').html(response.data.shortcode);
						$('.show_generated_shortcode').html(response.data.generated);
						$('#generated_shortcode').val(response.data.shortcode);

						window.location.href = '#apvc-shortcode-generator';
						$('#apvc-save-shortcode').removeAttr('disabled');
						$('#apvc-copy-shortcode').removeAttr('disabled');
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
				}
			});
		});
	}
	if (copyShortcode.length) {
		$(copyShortcode).on('click', function( e ) {
			e.preventDefault();
			copyTextById('show_shortcode')
		});
	}
	if (copyCampaignURL.length) {
		$(copyCampaignURL).on('click', function( e ) {
			e.preventDefault();
			let id = $(this).data('id');
			copyTextById('cmp_full_url_'+id );
		});
	}

	if (copyCampaignShortURL.length) {
		$(copyCampaignShortURL).on('click', function( e ) {
			e.preventDefault();
			e.preventDefault();
			let id = $(this).data('id');
			copyTextById('cmp_short_url_'+id );
		});
	}
	function urlencode(str) {
		str = (str + '').toString();

		// Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
		// PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
		return encodeURIComponent(str)
			.replace('!', '%21')
			.replace('\'', '%27')
			.replace('(', '%28')
			.replace(')', '%29')
			.replace('*', '%2A')
			.replace('%20', '+');
	}
	if (resetShortcode.length) {
		$(resetShortcode).on('click', function( e ) {
			e.preventDefault();
			window.location.reload();
		});
	}
	if (saveShortcode.length) {
		$(saveShortcode).on('click', function( e ){
			e.preventDefault();
			let shortcode_name = $("#shortcode_name").val();
			let shortcode_val = $("#generated_shortcode").val();
			jQuery.ajax({
				type: 'GET',
				url: APVC_ADMIN.save_shortcode,
				beforeSend: function (xhr) {},
				data: {
					'shortcode_name': shortcode_name,
					'shortcode_val': shortcode_val,
				},
				success: function (response) {
					if( true === response.success ){
						$('.sh_modal_body').html( '<div class="sh_success">'+APVC_ADMIN.sh_saved+'</div>' );
						setTimeout(function(){
							window.location.reload()
						}, 3000 );
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
				}
			});
		});
	}
	if (deleteShortcode.length) {
		$(deleteShortcode).on('click', function( e ){
			e.preventDefault();
			let id = $(this).data('id');
			jQuery.ajax({
				type: 'GET',
				url: APVC_ADMIN.delete_shortcode,
				beforeSend: function (xhr) {},
				data: {
					'id': id,
				},
				success: function (response) {
					if( true === response.success ){
						$("#modals-show-success-del-sh").modal('show');
						setTimeout(function(){
							window.location.reload()
						}, 1000 );
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log( xhr.status );
				}
			});
		});
	}
	if (copyShortcodeList.length) {
		$(copyShortcodeList).on('click', function( e ) {
			e.preventDefault();
			let id = $(this).data('id');
			copyTextById('cst_'+id );
		});
	}
	const copyToClipboard = (elementId) => {
		// Create a "hidden" input
		var aux = document.createElement("input");
		// Assign it the value of the specified element
		aux.setAttribute("value", document.getElementById(elementId).innerHTML);
		// Append it to the body
		document.body.appendChild(aux);
		// Highlight its content
		aux.select();
		// Copy the highlighted text
		document.execCommand("copy");
		// Remove it from the body
		document.body.removeChild(aux);
	};

	const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
	const copyIOS = (id) => {
		const text = document.getElementById(id).innerHTML;

		if (!navigator.clipboard) {
			const textarea = document.createElement("textarea");

			textarea.value = text;
			textarea.style.fontSize = "20px";
			document.body.appendChild(textarea);

			const range = document.createRange();
			range.selectNodeContents(textarea);

			const selection = window.getSelection();
			selection.removeAllRanges();
			selection.addRange(range);
			textarea.setSelectionRange(0, 999999);

			document.execCommand("copy");

			document.body.removeChild(textarea);
		}

		navigator.clipboard.writeText(text);
	};
	const copyTextById = (id) => {
		if (isIOS) {
			return copyIOS(id);
		}
		copyToClipboard(id);
	};

	if (setStartingCount.length) {
		$(document).on("click", '.set_starting_count', function( e ){
			e.preventDefault();
			let id = $(this).data('id');
			$("#modals-set-starting-count").modal('show');
			jQuery.ajax({
				type: 'GET',
				url: APVC_ADMIN.get_starting_count,
				beforeSend: function (xhr) {
					$("#modals-set-starting-count").modal('show');
				},
				data: {
					'id': id,
				},
				success: function (response) {
					console.log(response);
					if( true === response.success ){
						$("#inp_starting_count").val(response.data.results);
						$("#sc_post_id").val(id);
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log( xhr.status );
				}
			});
		});
	}

	if (setStartingCountSubmit.length) {
		$(document).on("click", '#set_starting_count', function( e ){
			e.preventDefault();
			let id = $("#sc_post_id").val();
			let count = $("#inp_starting_count").val();
			jQuery.ajax({
				type: 'GET',
				url: APVC_ADMIN.set_starting_count,
				beforeSend: function (xhr) {
					$("#modals-set-starting-count").modal('hide');
				},
				data: {
					'id': id,
					'count': count,
				},
				success: function (response) {
					console.log(response);
					if( true === response.success ){
						$("#modals-show-success-set-starting-count").modal('show');
						setTimeout(function(){
							window.location.reload()
						}, 1000 );
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log( xhr.status );
				}
			});
		});
	}

	if (migrateData.length) {
		$(migrateData).on('click', function( e ){
			e.preventDefault();
			document.getElementById('apvc_notice_hide').disabled = true;
			document.getElementById('mig_message').style.display = "block";

			jQuery.ajax({
				type: 'GET',
				url: APVC_ADMIN.start_migration,
				beforeSend: function (xhr) {},
				data: {},
				success: function (response) {
					alert('Migration successfully completed.');
					setTimeout(function(){
						window.location.href = APVC_ADMIN.migration_done;
					}, 2000 );
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log( xhr.status );
				}
			});

		});
	}

	if (migrationStart.length) {
		$(migrationStart).on('click', function( e ){

			let btnText = APVC_ADMIN.mig_process_started_btn_lbl
			let thisButton = document.getElementById('apvc_start_migration');
			let otherButton = document.getElementById('apvc_go_with_fresh');

			if (thisButton.childNodes[0]) {
				thisButton.childNodes[0].nodeValue = btnText;
			}
			thisButton.disabled = true;
			otherButton.disabled = true;

			$('.migration_process').css('display','block');
			$("#apvc_mig_process_log").append(APVC_ADMIN.mig_process_started+'\n');

			var dataCount = $("#apvc_old_records").val(); // your data array
			var chunkSize = 500; // Number of rows to process per request
			var totalChunks = Math.ceil(dataCount / chunkSize); // Assuming 'totalDataCount' is known
			var processedDataCount = 0; // Counter to keep track of the number of processed data
			let progress = 20;
			function apvcUpdateProgress( chunkIndex ) {
				var progressa = (chunkIndex / totalChunks) * 100;
				$('.progress-bar').css('width', progressa + '%');
			}

			function apvcUpdateMigration() {
				$.ajax({
					url: APVC_ADMIN.update_migration,
					method: 'POST',
					success: function(response) {},
					error: function(error) {console.log(error);}
				});
			}

			function apvcProcessData(chunkIndex) {
				$.ajax({
					url: APVC_ADMIN.migrate_data,
					method: 'POST',
					data: {
						'chunkIndex': 0,//chunkIndex,
						'chunkSize': 0//chunkSize
					},
					success: function(response) {
						setInterval(function(){
							apvcUpdateProgress(100);
							console.log('migration is going on');
						}, 1000);

						setTimeout(function(){
							window.location.reload();
						},5000);

						console.log("All data processed successfully!");
						$("#apvc_mig_process_log").prepend( APVC_ADMIN.mig_process_completed+'\n' );
						return;

						/*
						processedDataCount += chunkSize;
						apvcUpdateProgress(chunkIndex + 1);
						$("#apvc_mig_process_log").prepend(APVC_ADMIN.mig_process_records + '\n' );
						if(chunkIndex < totalChunks) {
							apvcProcessData(chunkIndex + 1 );
						} else {
							apvcUpdateMigration();
							$("#apvc_mig_process_log").prepend( APVC_ADMIN.mig_process_completed );
							setTimeout(function(){ $('#confirm-clean').trigger('click'); }, 2000 );
							console.log("All data processed successfully!");
						}
						*/
					},
					error: function(error) {
						console.log(error);
					}
				});
			}

			// Start processing from the first chunk
			apvcProcessData(0);
		});
	}

	if (apvc_go_with_fresh.length) {
		$(apvc_go_with_fresh).on('click', function( e ){

			// let otherButton = document.getElementById('apvc_start_migration');
			let thisButton = document.getElementById('apvc_go_with_fresh');
			thisButton.disabled = true;
			// otherButton.disabled = true;

			$.ajax({
				url: APVC_ADMIN.start_fresh,
				method: 'POST',
				data: {
					'action': 'fresh',//chunkIndex,
				},
				success: function(response) {
					console.log(response)
					if( response.success == true ){
						alert("Update successfully completed.");
						setTimeout(function(){ window.location.reload() }, 2000 );
					}
				},
				error: function(error) {console.log(error);}
			});
		});
	}

	if (cleanConfirm.length) {
		$(cleanConfirm).on('click', function( e ){
			// let otherButton = document.getElementById('apvc_start_migration');
			let thisButton = document.getElementById('apvc_go_with_fresh');
			thisButton.disabled = true;
			// otherButton.disabled = true;

			$.ajax({
				url: APVC_ADMIN.clean_old_stats,
				method: 'POST',
				success: function(response) {
					if( response.success == true ){
						alert("Update successfully completed.");
						setTimeout(function(){ window.location.reload() }, 2000 );
					}
				},
				error: function(error) {console.log(error);}
			});
		});
	}

	if (confirmCleanOldData) {
		confirmCleanOldData.onclick = function () {
			Swal.fire({
				title: APVC_ADMIN.mig_are_you_sure,
				text: APVC_ADMIN.mig_cnf_msg_for_del,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: APVC_ADMIN.mig_confirm_lbl,
				customClass: {
					confirmButton: 'btn btn-primary me-3 cleanConfirm',
					cancelButton: 'btn btn-label-secondary cleanCancel'
				},
				buttonsStyling: false
			}).then(function (result) {
				if (result.value) {
					Swal.fire({
						icon: 'success',
						title: APVC_ADMIN.mig_success_lbl,
						text: APVC_ADMIN.mig_success_msg,
						customClass: {
							confirmButton: 'btn btn-primary'
						}
					});

					let otherButton = document.getElementById('apvc_start_migration');
					let thisButton = document.getElementById('apvc_go_with_fresh');
					thisButton.disabled = true;
					otherButton.disabled = true;

					$.ajax({
						url: APVC_ADMIN.clean_old_stats,
						method: 'POST',
						success: function(response) {
							if( response.success == true ){
								setTimeout(function(){ window.location.reload() }, 1000 );
							}
						},
						error: function(error) {console.log(error);}
					});

				} else if (result.dismiss === Swal.DismissReason.cancel) {
					Swal.fire({
						title: APVC_ADMIN.mig_warning_lbl,
						text: APVC_ADMIN.mig_cncl_msg_for_del,
						icon: 'error',
						customClass: {
							confirmButton: 'btn btn-primary'
						}
					});
					setTimeout(function(){ window.location.reload(); }, 2000 );
				}
			});
		};
	}



	$( window ).on( "load", function() {
		// Handler for `load` called.

		let currentPage = $("#apvc_page_id").val();

		if('dashboard' === currentPage ) {
			/* Dashboard Widgets API Calls */
			$.ajax({
				url: APVC_ADMIN.dash_get_yearly_stats,
				method: 'POST',
				beforeSend: function () {
					$('.tv_yearly').block({
						message: '<div class="spinner-border text-primary" role="status"></div>',
						css: {
							backgroundColor: 'transparent',
							border: '0'
						},
						overlayCSS: {
							backgroundColor: '#fff',
							opacity: 0.8
						}
					});
				},
				success: function (response) {

					if (response.success == true) {
						$('.tv_yearly').unblock();
						$('.tv_yearly_val').text(response.data.current);
						if( 'pos' === response.data.status ) {
							$('.diff_period_yearly').html( '<small class="ap_u_arrow text-success">'+response.data.difference+' &uarr;</small>' );
						} else {
							$('.diff_period_yearly').html( '<small class="ap_d_arrow text-danger">'+response.data.difference+' &darr;</small>' );
						}
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.dash_get_monthly_stats,
				method: 'POST',
				beforeSend: function () {
					$('.tv_monthly').block({
						message: '<div class="spinner-border text-primary" role="status"></div>',
						css: {
							backgroundColor: 'transparent',
							border: '0'
						},
						overlayCSS: {
							backgroundColor: '#fff',
							opacity: 0.8
						}
					});
				},
				success: function (response) {
					if (response.success == true) {
						$('.tv_monthly').unblock();
						$('.tv_monthly_val').text(response.data.current);
						if( 'pos' === response.data.status ) {
							$('.diff_period_monthly').html( '<small class="ap_u_arrow text-success">'+response.data.difference+' &uarr;</small>' );
						} else {
							$('.diff_period_monthly').html( '<small class="ap_d_arrow text-danger">'+response.data.difference+' &darr;</small>' );
						}
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.dash_get_weekly_stats,
				method: 'POST',
				beforeSend: function () {
					$('.tv_weekly').block({
						message: '<div class="spinner-border text-primary" role="status"></div>',
						css: {
							backgroundColor: 'transparent',
							border: '0'
						},
						overlayCSS: {
							backgroundColor: '#fff',
							opacity: 0.8
						}
					});
				},
				success: function (response) {
					if (response.success == true) {
						$('.tv_weekly').unblock();
						$('.tv_weekly_val').text(response.data.current);
						if( 'pos' === response.data.status ) {
							$('.diff_period_weekly').html( '<small class="ap_u_arrow text-success">'+response.data.difference+' &uarr;</small>' );
						} else {
							$('.diff_period_weekly').html( '<small class="ap_d_arrow text-danger">'+response.data.difference+' &darr;</small>' );
						}
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.dash_get_daily_stats,
				method: 'POST',
				beforeSend: function () {
					$('.tv_daily').block({
						message: '<div class="spinner-border text-primary" role="status"></div>',
						css: {
							backgroundColor: 'transparent',
							border: '0'
						},
						overlayCSS: {
							backgroundColor: '#fff',
							opacity: 0.8
						}
					});
				},
				success: function (response) {
					if (response.success == true) {
						$('.tv_daily').unblock();
						$('.tv_daily_val').text(response.data.current);
						if( 'pos' === response.data.status ) {
							$('.diff_period_daily').html( '<small class="ap_u_arrow text-success">'+response.data.difference+' &uarr;</small>' );
						} else {
							$('.diff_period_daily').html( '<small class="ap_d_arrow text-danger">'+response.data.difference+' &darr;</small>' );
						}
					}
				}, error: function (error) {
					console.log(error);
				}
			});
		}

		if('trending' === currentPage ) {
			/* Trending Page Widgets API Calls */
			$.ajax({
				url: APVC_ADMIN.trend_get_top_articles,
				method: 'POST',
				beforeSend: function () {
					$('.tr_top_articles').block({
						message: '<div class="spinner-border text-primary" role="status"></div>',
						css: {
							backgroundColor: 'transparent',
							border: '0'
						},
						overlayCSS: {
							backgroundColor: '#fff',
							opacity: 0.8
						}
					});
				},
				success: function (response) {
					if (response.success == true) {
						let html = '';
						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="5" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.page_id+'</td>';
							html += '<td><a href="'+val.page_url+'" target="_blank">  <i class="bx bx-link-external"></i> '+val.page_title+'</a></td>';
							html += '<td>'+val.views+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.user_sessions+'</td>';
							html += '</tr>';
						});
						$('.tr_top_articles_body').html( html );
						$('.tr_top_articles').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.trend_get_top_posts,
				method: 'POST',
				beforeSend: function () { $('.tr_top_posts').block({
						message: '<div class="spinner-border text-primary" role="status"></div>',
						css: {
							backgroundColor: 'transparent',
							border: '0'
						},
						overlayCSS: {
							backgroundColor: '#fff',
							opacity: 0.8
						}
					}); },
				success: function (response) {
					if (response.success == true) {
						let html = '';
						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="5" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.page_id+'</td>';
							html += '<td>'+val.page_title+'<a href="'+val.page_url+'" target="_blank">  <i class="bx bx-link-external"></i> </a></td>';
							html += '<td>'+val.views+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.user_sessions+'</td>';
							html += '</tr>';
						});
						$('.tr_top_posts_body').html( html );
						$('.tr_top_posts').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.trend_get_top_countries,
				method: 'POST',
				beforeSend: function () { $('.tr_top_countries').block({
					message: '<div class="spinner-border text-primary" role="status"></div>',
					css: {
						backgroundColor: 'transparent',
						border: '0'
					},
					overlayCSS: {
						backgroundColor: '#fff',
						opacity: 0.8
					}
				}); },
				success: function (response) {
					if (response.success == true) {
						let html = '';
						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="4" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.country+'</td>';
							html += '<td>'+val.views+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.user_sessions+'</td>';
							html += '</tr>';
						});
						$('.tr_top_countries_body').html( html );
						$('.tr_top_countries').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.trend_get_top_states,
				method: 'POST',
				beforeSend: function () { $('.tr_top_states').block({
					message: '<div class="spinner-border text-primary" role="status"></div>',
					css: {
						backgroundColor: 'transparent',
						border: '0'
					},
					overlayCSS: {
						backgroundColor: '#fff',
						opacity: 0.8
					}
				}); },
				success: function (response) {
					if (response.success == true) {
						let html = '';
						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="4" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.state+'</td>';
							html += '<td>'+val.views+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.user_sessions+'</td>';
							html += '</tr>';
						});
						$('.tr_top_states_body').html( html );
						$('.tr_top_states').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.trend_get_top_cities,
				method: 'POST',
				beforeSend: function () { $('.tr_top_city').block({
					message: '<div class="spinner-border text-primary" role="status"></div>',
					css: {
						backgroundColor: 'transparent',
						border: '0'
					},
					overlayCSS: {
						backgroundColor: '#fff',
						opacity: 0.8
					}
				}); },
				success: function (response) {
					if (response.success == true) {
						let html = '';

						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="4" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.city+'</td>';
							html += '<td>'+val.views+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.user_sessions+'</td>';
							html += '</tr>';
						});
						$('.tr_top_city_body').html( html );
						$('.tr_top_city').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.trend_get_top_devices,
				method: 'POST',
				beforeSend: function () { $('.tr_top_devices').block({
					message: '<div class="spinner-border text-primary" role="status"></div>',
					css: {
						backgroundColor: 'transparent',
						border: '0'
					},
					overlayCSS: {
						backgroundColor: '#fff',
						opacity: 0.8
					}
				}); },
				success: function (response) {
					if (response.success == true) {
						let html = '';
						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="4" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.device+'</td>';
							html += '<td>'+val.views+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.user_sessions+'</td>';
							html += '</tr>';
						});
						$('.tr_top_devices_body').html( html );
						$('.tr_top_devices').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
				url: APVC_ADMIN.trend_get_top_devices_os,
				method: 'POST',
				beforeSend: function () { $('.tr_top_devices_os').block({
					message: '<div class="spinner-border text-primary" role="status"></div>',
					css: {
						backgroundColor: 'transparent',
						border: '0'
					},
					overlayCSS: {
						backgroundColor: '#fff',
						opacity: 0.8
					}
				}); },
				success: function (response) {
					if (response.success == true) {
						let html = '';
						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="4" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.device_os+'</td>';
							html += '<td>'+val.views+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.user_sessions+'</td>';
							html += '</tr>';
						});
						$('.tr_top_devices_os_body').html( html );
						$('.tr_top_devices_os').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

			$.ajax({
			url: APVC_ADMIN.trend_get_top_browsers,
			method: 'POST',
			beforeSend: function () { $('.tr_top_devices_browsers').block({
				message: '<div class="spinner-border text-primary" role="status"></div>',
				css: {
					backgroundColor: 'transparent',
					border: '0'
				},
				overlayCSS: {
					backgroundColor: '#fff',
					opacity: 0.8
				}
			}); },
			success: function (response) {
				if (response.success == true) {
					let html = '';
					if( '' == response.data ) {
						html += '<tr>';
						html += '<td colspan="4" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
						html += '</tr>';
					}
					$.each(response.data, function(key,val) {
						html += '<tr>';
						html += '<td>'+val.device_browser+'</td>';
						html += '<td>'+val.views+'</td>';
						html += '<td>'+val.visitors+'</td>';
						html += '<td>'+val.user_sessions+'</td>';
						html += '</tr>';
					});
					$('.tr_top_devices_browsers_body').html( html );
					$('.tr_top_devices_browsers').unblock();
				}
			}, error: function (error) {
				console.log(error);
			}
		});
		}

		if( 'refer' === currentPage ){
			$.ajax({
				url: APVC_ADMIN.trending_get_referrer,
				method: 'POST',
				beforeSend: function () {
					$('.tr_trending_referrer').block({
						message: '<div class="spinner-border text-primary" role="status"></div>',
						css: {
							backgroundColor: 'transparent',
							border: '0'
						},
						overlayCSS: {
							backgroundColor: '#fff',
							opacity: 0.8
						}
					});
				},
				success: function (response) {
					if (response.success == true) {
						$('.tr_trending_referrer_body').html( response.data );
						$('.tr_trending_referrer').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});


			$.ajax({
				url: APVC_ADMIN.trending_refferer_list,
				method: 'POST',
				beforeSend: function () { $('.tr_top_referrers_list').block({
					message: '<div class="spinner-border text-primary" role="status"></div>',
					css: {
						backgroundColor: 'transparent',
						border: '0'
					},
					overlayCSS: {
						backgroundColor: '#fff',
						opacity: 0.8
					}
				}); },
				success: function (response) {
					if (response.success == true) {
						let html = '';
						if( '' == response.data ) {
							html += '<tr>';
							html += '<td colspan="5" class="text-center pt-5"><h5>'+APVC_ADMIN.no_data_found+'</h5></td>';
							html += '</tr>';
						}
						$.each(response.data, function(key,val) {
							html += '<tr>';
							html += '<td>'+val.referrer+'</td>';
							html += '<td>'+val.type+'</td>';
							html += '<td>'+val.visitors+'</td>';
							html += '<td>'+val.views+'</td>';
							html += '</tr>';
						});
						$('.tr_top_referrers_list_body').html( html );
						$('.tr_top_referrers_list').unblock();
					}
				}, error: function (error) {
					console.log(error);
				}
			});

		}
	});



})( jQuery );
