////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Views
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Show Pagination
 *
 * @param pagination_container		- '.wpbc_rules_pagination'
 * @param params_obj				- JSON object	~	{ 'page_active': $page_active, 'pages_count': $pages_count }
 */
function wpbc_pagination_echo( pagination_container, container_header, container_footer, params_obj ){

	// Header: " 1 - 9 of many | < > ".
	var wpbc_pagination__prev_next = wp.template( 'wpbc_pagination__prev_next' );
	jQuery( container_header ).html( wpbc_pagination__prev_next( params_obj ) );

	// Active page Number in Selectbox
	var wpbc_pagination_active_page_in_selectbox = wp.template( 'wpbc_pagination_active_page_in_selectbox' );
	jQuery( container_footer ).html( wpbc_pagination_active_page_in_selectbox( params_obj ) );

	// Number of Items / Page.
	var wpbc_pagination_items_per_page = wp.template( 'wpbc_pagination_items_per_page' );
	jQuery( container_footer ).append( wpbc_pagination_items_per_page( params_obj ) );


	//
	// var pagination = wp.template( 'wpbc_pagination' );
	// jQuery( pagination_container ).html( '<div class="wpbc-bottom-pagination"></div>' );
	//
	// // Pagination
	// jQuery( pagination_container + ' .wpbc-bottom-pagination').append(  pagination( params_obj ) ) ;



	jQuery( pagination_container ).show();
}


/**
 * Blank function.  -- Redefine this function in specific page-XXXX.php  file for specific actions
 *
 * @param page_number	int
 */
function wpbc_pagination_click_page( page_number ){
	console.log( 'wpbc_pagination_click_page', page_number );
}