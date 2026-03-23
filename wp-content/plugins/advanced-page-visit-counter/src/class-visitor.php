<?php

class APVC_Visitor {

	private $id;
	private $geo;

	public function __construct( string $ip, string $user_agent ) {
		$geo_database = new APVC_Geo_Database();
		$this->id     = self::calculate_id( $ip, $user_agent );
		$this->geo    = $geo_database->ip_to_geo( $ip );
	}

	public function create_country() {
		return APVC_Query::create_country(
			array(
				'country_code' => $this->country_code(),
				'country'      => $this->country(),
				'continent'    => $this->continent(),
			)
		);
	}

	public function create_city( $country_id ) {
		return APVC_Query::create_city(
			array(
				'country_id'  => absint($country_id),
				'subdivision' => $this->subdivision(),
				'city'        => $this->city(),
			)
		);
	}

	public function create_state( $country_id ) {
		return APVC_Query::create_state(
			array(
				'country_id'  => absint($country_id),
				'state_name' => $this->subdivision(),
			)
		);
	}

	private function get_or_upsert_visitor() {
		APVC_Query::create_visitor(
			array(
				'visitor_id'   => $this->id(),
				'country_code' => $this->country_code(),
				'city'         => $this->city(),
				'subdivision'  => $this->subdivision(),
				'country'      => $this->country(),
				'continent'    => $this->continent(),
				'site_id'      => get_current_blog_id(),
			)
		);
	}

	/**
	 * @param string $ip
	 * @param string $user_agent
	 * @return string
	 */
	private function calculate_id( string $ip, string $user_agent ): string {
		$salt   = APVC_Salt::visitor_token_salt();
		$result = $salt . $ip . $user_agent;

		return md5( $result );
	}

	/**
	 * Return the database id for a visitor
	 *
	 * @return string
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * Return an ISO country code
	 *
	 * @return string|null
	 */
	public function country_code() {
		if ( isset( $this->geo['country']['iso_code'] ) ) {
			return $this->geo['country']['iso_code'];
		} else {
			return null;
		}
	}

	/**
	 * Return an English city name
	 *
	 * @return string|null
	 */
	public function city() {
		if ( isset( $this->geo['city']['names']['en'] ) ) {
			return $this->geo['city']['names']['en'];
		} else {
			return null;
		}
	}

	/**
	 * Return an English subdivision name
	 *
	 * @return string|null
	 */
	public function subdivision() {
		if ( isset( $this->geo['subdivisions'][0]['names']['en'] ) ) {
			return $this->geo['subdivisions'][0]['names']['en'];
		} else {
			return null;
		}
	}

	/**
	 * @return string|null
	 */
	public function country() {
		if ( isset( $this->geo['country']['names']['en'] ) ) {
			return $this->geo['country']['names']['en'];
		} else {
			return null;
		}
	}

	/**
	 * @return string|null
	 */
	public function continent() {
		if ( isset( $this->geo['continent']['names']['en'] ) ) {
			return $this->geo['continent']['names']['en'];
		} else {
			return null;
		}
	}
}
