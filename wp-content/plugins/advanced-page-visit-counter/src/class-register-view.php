<?php


class APVC_Register_View {

	public $rawdata;
	public $referrer_url;
	public $user_device;
	public $campaign_fields;
	public $viewed_at;
	public $resource;
	public $referrer;
	public $campaign;
	public $session;
	public $country_id;
	public $state_id;
	public $city_id;
	public $visitor;
	public $ended_at;

	public function __construct( $rawData, $referrer_url, APVC_Visitor $visitor, $campaign_fields = array(), $viewed_at = null, $user_device = null, $ended_at = null ) {
		$this->rawdata         = $rawData;
		$this->referrer_url    = ( ! empty( $referrer_url ) ) ? trim( $referrer_url ) : '';
		$this->visitor         = $visitor;
		$this->country_id      = $visitor->create_country();
		$this->state_id        = $visitor->create_state( $this->country_id );
		$this->city_id         = $visitor->create_city( $this->country_id );
		$this->campaign_fields = $campaign_fields;
		$this->viewed_at       = $viewed_at;
		$this->ended_at        = $ended_at;
		$this->user_device     = $user_device;
		$this->resource        = APVC_Helper_Functions::get_page_type_db( $this->rawdata );
		$this->session         = APVC_Helper_Functions::get_user_session( $this );
		$view_id               = APVC_Helper_Functions::create_view( $this );
		APVC_Helper_Functions::set_session_int_view( $this->session, $view_id );
		APVC_Helper_Functions::set_session_last_view( $this->session, $view_id, $ended_at );
	}








}
