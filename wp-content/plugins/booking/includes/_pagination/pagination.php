<?php /**
 * @version 1.0
 * @description Pagination
 * @category  Pagination Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2020-01-23
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/*
 * Usage:  Pagination Class

	            <div class="wpbc_rules_pagination"></div>
				<?php
				$wpbc_pagination = new WPBC_Pagination();
				$wpbc_pagination->init(
										array(
											'load_on_page'  => 'wpbc-rules',
											'container'     => '.wpbc_rules_pagination',
											'on_click'	    => 'wpbc_rules_pagination_click'		// onclick = "javascript: wpbc_rules_listing_page( page_num );"  - need to  define this function in JS file
										)
				);
				$wpbc_pagination->show( 													        // Its showing with  JavaScript on document ready
										array(
											'page_active' => 3,
											'pages_count' => 20
										)
				);

OR (for showing with   JavaScript) :

	<script type="text/javascript">
		jQuery( document ).ready( function (){

			wpbc_pagination_echo( '.wpbc_rules_pagination','.wpbc_rules_pagination_header','.wpbc_rules_pagination_footer',
									{
										'page_active': page_number,
										'pages_count': Math.ceil( ajx_count / ajx_page_items_count )
									}
								);
		} );
	</script>

 */


class WPBC_Pagination {

	private $settings;

	/**
	 * Get parameter Value
	 *
	 * @param string $parameter	- name of parameter
	 *
	 * @return mixed
	 */
	public function get_settings( $parameter ){

		if ( ! empty( $this->settings[ $parameter ] ) ) {
			return $this->settings[ $parameter ];
		} else {
			return false;
		}
	}


	// <editor-fold     defaultstate="collapsed"                        desc=" ///  JS | CSS files   /// "  >
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// JS | CSS files
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Define HOOKs for loading CSS and  JavaScript files
	 */
	public function init_load_css_js() {
		// JS & CSS
		add_action( 'wpbc_enqueue_js_files',  array( $this, 'js_load_files' ),     50  );
		add_action( 'wpbc_enqueue_css_files', array( $this, 'enqueue_css_files' ), 50  );
	}

	/** JSS */
	public function js_load_files( $where_to_load ) {

		$in_footer = true;

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

			wp_enqueue_script( 'wpbc-pagination'
				, trailingslashit( plugins_url( '', __FILE__ ) ) . 'pagination.js'         /* wpbc_plugin_url( '/_out/js/code_mirror.js' ) */
				, array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );

			/**
			wp_localize_script( 'wpbc_all', 'wpbc_live_request_obj'
								, array(
										'contacts'  => '',
										'reminders' => ''
									)
			);
		 	*/
		}
	}

	/** CSS */
	public function enqueue_css_files( $where_to_load ) {

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

			wp_enqueue_style( 'wpbc-pagination', trailingslashit( plugins_url( '', __FILE__ ) ) . 'pagination.css', array(), WP_BK_VERSION_NUM );
		}
	}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc=" ///  Templates  /// "  >
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// Templates
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function init_load_templates() {

		add_action( 'wpbc_hook_settings_page_footer', array( $this, 'hook__page_footer_tmpl' ) );
	}


	/**
	 * Templates at footer of page
	 *
	 * @param $page string
	 */
	public function hook__page_footer_tmpl( $page ){

		if ( $this->get_settings( 'load_on_page' ) === $page ) {

			$this->template__prev_next();
			$this->template__items_per_page();
			$this->template__active_page_in_selectbox();
		}
	}

	/**
	 * Pagination - " 1 - 9 of many | < > "
	 *
	 * @return void
	 */
	private function template__prev_next() {

		?>
		<script type="text/html" id="tmpl-wpbc_pagination__prev_next">
			<div class="wpbc_pagination__prev_next   wpbc_pagination_el">
				<#
					var items = {};
					items['start'] = ( data.page_active - 1 ) * data.page_items_count + 1;
					items['end'] = data.page_active * data.page_items_count;
					items['end'] = ( items['end'] > data.total_count ) ? data.total_count : items['end'];
					items['total'] = data.total_count;
				#>
				<# if ( data.pages_count > 1 ) { #>
				<?php
					echo '<div class="wpbc_ui_el">{{items.start}} - {{items.end}} ' . esc_html__( 'of', 'booking' ) . ' {{items.total}}</div>';
				?>
				<# } else { #>
				<?php
					echo '<div class="wpbc_ui_el">{{items.start}} - {{items.end}} ' . esc_html__( 'bookings', 'booking' ) . ' </div>';
				?>
				<# } #>
				<# if ( data.pages_count > 1 ) { #>
				<div class="wpbc_ui_el">
					<a class="wpbc_ui_el__a <# if ( 1 == data.page_active ) { #> disabled<# } #>"
					   href="javascript:void(0)"
					<# if ( 1 != data.page_active ) { #> onclick="javascript:<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->get_settings( 'on_click' ); ?>( parseInt( {{ data.page_active }} ) - 1 );" <# } #>
					>
					<?php
					echo '<i class="menu_icon icon-1x wpbc_icn_chevron_left"></i>';
					?>
					</a>
				</div>
				<# } #>
				<# if ( data.pages_count > 1 ) { #>
				<div class="wpbc_ui_el">
					<a class="wpbc_ui_el__a <# if ( data.pages_count == data.page_active ) { #> disabled<# } #>"
					   href="javascript:void(0)"
					<# if ( data.pages_count != data.page_active ) { #> onclick="javascript:<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->get_settings( 'on_click' ); ?>( parseInt( {{ data.page_active }} ) + 1 );" <# } #>
					>
					<?php
					echo '<i class="menu_icon icon-1x wpbc_icn_chevron_right"></i>';
					?>
					</a>
				</div>
				<# } #>

			</div>
		</script><?php

	}


	/**
	 * Pagination - select Number of Items / Page.
	 *
	 * @return void
	 */
	private function template__items_per_page() {
		?>
		<script type="text/html" id="tmpl-wpbc_pagination_items_per_page">
			<div class="wpbc_pagination_items_per_page wpbc_pagination_el">
				<div class="">
					<label class="wpbc_ui_el"><?php esc_html_e( 'Show', 'booking' ); ?></label>
					<select class="wpbc_ui_el  wpbc_items_per_page" autocomplete="off">
						<#
							var my_options_arr = [5, 10, 50, 100];
							var is_selected = '';
							_.each(
								my_options_arr,
								function ( p_val, p_key, p_data ) {
									is_selected = '';
									if ( data.page_items_count == p_val ) {
										is_selected = ' selected="selected" ';
									}
									#><option value="{{p_val}}" {{is_selected}}>{{p_val}}</option><#
								}
							);
						#>
					</select>
					<label class="wpbc_ui_el"><?php esc_html_e( 'per page', 'booking' ); ?></label>
				</div>
			</div>
		</script>
		<?php
	}

	/**
	 * Pagination - Active page Number in Selectbox
	 *
	 * @return void
	 */
	private function template__active_page_in_selectbox(){
		?>
		<script type="text/html" id="tmpl-wpbc_pagination_active_page_in_selectbox">
			<div class="wpbc_pagination_active_page_in_selectbox wpbc_pagination_el">
				<# if ( data.pages_count > 1 ) { #>
				<div class="">
					<label class="wpbc_ui_el"><?php esc_html_e( 'Page', 'booking' ); ?>: </label>
					<select class="wpbc_ui_el"
							autocomplete="off"
							onchange="javascript:<?php
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo $this->get_settings( 'on_click' );
									  ?>( ( parseInt( jQuery( this ).val() ) ) );"
					>
						<#
							var is_selected = '';
							for ( var pg_num = 1; pg_num <= data.pages_count; pg_num++ ){
								is_selected = ( pg_num == data.page_active ) ? ' selected="selected" ' : '';
								#><option value="{{pg_num}}" {{is_selected}}>{{pg_num}}</option><#
							}
						#>
					</select>
				</div>
				<# } #>
			</div>
		</script>
		<?php
	}



	private function template__pagination_old(){

		// Pagination
		?><script type="text/html" id="tmpl-wpbc_pagination">
			<div class="wpbc-ajax-pagination wpbc-ajax-pagination-container">
				<# if ( data.pages_count > 1 ) { #>
				<div class="ui_element">
					<a class="wpbc_ui_control wpbc_ui_button <# if ( 1 == data.page_active ) { #> disabled<# } #>"
					   href="javascript:void(0)"
						<# if ( 1 != data.page_active ) { #> onclick="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $this->get_settings( 'on_click' ); ?>( parseInt(  {{ data.page_active }} ) - 1 );" <# } #>
					 >
						<?php esc_html_e('Prev', 'booking'); ?>
					</a>
				</div>
				<# }

					/** Number visible pages (links) that linked to active page, other pages skipped by "..." */
					var num_closed_steps = 2;

					for ( var pg_num = 1; pg_num <= data.pages_count; pg_num++ ){

						if ( ! (
								   ( data.pages_count > ( num_closed_steps * 4) )
								&& ( pg_num > num_closed_steps )
								&& ( ( data.pages_count - pg_num + 1 ) > num_closed_steps )
								&& (  Math.abs( data.page_active - pg_num ) > num_closed_steps )
						   ) )
						{
							#> <div class="ui_element"><a  class="wpbc_ui_control wpbc_ui_button <# if ( pg_num == data.page_active ) { #> active<# } #>"
								href="javascript:void(0)" onclick="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $this->get_settings( 'on_click' ); ?>( {{pg_num}} );" >
						{{pg_num}}</a></div> <#

									if ( ( data.pages_count > ( num_closed_steps * 4) )
									&& ( (pg_num+1) > num_closed_steps )
									&& ( ( data.pages_count - ( pg_num + 1 ) ) > num_closed_steps )
									&& ( Math.abs(data.page_active - ( pg_num + 1 ) ) > num_closed_steps )
									) {
										#><div class="ui_element"><a class="wpbc_ui_control wpbc_ui_button disabled" href="javascript:void(0);">...</a></div><#
									}
						 }
					}

				if ( data.pages_count > 1 ) { #>
				<div class="ui_element">
					<a 	class="wpbc_ui_control wpbc_ui_button <# if ( data.pages_count == data.page_active ) { #> disabled<# } #>"
						href="javascript:void(0)"
						<# if ( data.pages_count != data.page_active ) { #> onclick="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $this->get_settings( 'on_click' ); ?>( parseInt(  {{ data.page_active }} ) + 1 );" <# } #>
					>
						<?php esc_html_e('Next', 'booking'); ?>
					</a>
				</div>
				<# } #>
			</div>
		</script><?php

		// Pagination Items per page
		?><script type="text/html" id="tmpl-wpbc_pagination_items_per_page">
			<div class="wpbc-ajax-pagination_items_per_page">
				<div class="ui_element">
					<select class="wpbc_items_per_page wpbc_ui_control wpbc_ui_select " autocomplete="off">
						<#
							var my_options_arr = [5, 10, 50, 100];
							var is_selected = '';
							_.each( my_options_arr, function ( p_val, p_key, p_data ) {
								 is_selected = '';
								 if ( data.page_items_count == p_val ) {
									is_selected = ' selected="selected" ';
								 }
								 #><option value="{{p_val}}" {{is_selected}}>{{p_val}}</option><#
							});
						#>
					</select>
				</div>
				<div class="ui_element">
					<label class="wpbc_ui_control_label"><?php esc_html_e('per page','booking'); ?></label>
				</div>
				<?php

				if (0) {
				?>
				<div class="ui_element">
					<select class="wpbc_items_sort_type wpbc_ui_control wpbc_ui_select" autocomplete="off">
						<#
							my_options_arr = {
												'ASC':  '<?php echo esc_js(__( 'ASC', 'booking' )); ?>',
												'DESC': '<?php echo esc_js(__( 'DESC', 'booking' )); ?>',
											 };
							is_selected = '';
							_.each( my_options_arr, function ( p_val, p_key, p_data ) {
								 is_selected = '';
								 if ( data.sort_type == p_key ) {
									is_selected = ' selected="selected" ';
								 }
								 #><option value="{{p_key}}" {{is_selected}}>{{p_val}}</option><#
							});
						#>
					</select>
				</div>
				<?php } ?>
			</div>
		</script><?php
	}

	// </editor-fold>


	/**
	 * Init Pagination on start - define 'load_on_page', 'container', 'on_click' function
	 *
	 * @param array $params = array(
											'load_on_page'  => 'wpbc-settings',					// defined at 	function in_page() {
											'container'     => '.wpbc_settings_pagination',		// defined in 	function content(),  	like		<div class="wpbc_rules_pagination"></div>
											'on_click'	    => 'wpbc_pagination_click_page'		// onclick = "javascript: wpbc_pagination_click_page( page_active );"  - need to  define this function in JS file
									);
	 */
	public function init( $params = array() ) {

		$defaults = array(
							'load_on_page'  => 'wpbc-settings',					// defined at 	function in_page() {
							'container'     => '.wpbc_settings_pagination',		// defined in 	function content(),  	like		<div class="wpbc_rules_pagination"></div>
							'on_click'	    => 'wpbc_pagination_click_page'		// onclick = "javascript: wpbc_pagination_click_page( page_active );"  - need to  define this function in JS file
					);
		$this->settings   = wp_parse_args( $params, $defaults );

		$this->init_load_templates();
	}


	/**
	 * Show pagination
	 *
	 * @param array $params = array(
										'page_active' => 1,
										'pages_count' => 10
								)
	 */
	public function show( $params = array() ) {
		$defaults = array(
							'page_active' => 1,
							'pages_count' => 1,
							'total_count' => 1
					);
		$params   = wp_parse_args( $params, $defaults );
		?>
		<script type="text/javascript">

			jQuery( document ).ready( function () {

				wpbc_pagination_echo(
					'<?php echo esc_attr( $this->get_settings( 'container' ) ); ?>',
					'<?php echo esc_attr( $this->get_settings( 'container_header' ) ); ?>',
					'<?php echo esc_attr( $this->get_settings( 'container_footer' ) ); ?>',
					<?php
					echo wp_json_encode(
						array(
							'page_active' => $params['page_active'],
							'pages_count' => $params['pages_count'],
							'total_count' => $params['total_count'],
						)
					);
					?>
				);
			} );
		</script>
		<?php
	}
}

/**
 * Just for loading CSS and  JavaScript files for all  Settings pages
 */
 if ( true ) {
	$wpbc_js_css_loading = new WPBC_Pagination;
	$wpbc_js_css_loading->init_load_css_js();
 }


//TODO: delete ../email-reminders/_src/css/o-pagination.css