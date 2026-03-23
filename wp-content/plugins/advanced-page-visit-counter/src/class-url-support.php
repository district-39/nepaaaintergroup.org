<?php
use League\Uri\Uri;

class APVC_URL {

	private $url;
	private static $public_suffix_list;

	public function __construct( string $url ) {
		$this->url = $url;
	}

	public function is_valid_url() {
		$valid_url = filter_var( $this->url, FILTER_VALIDATE_URL );

		if ( ! $valid_url ) {
			return false;
		}

		try {
			$components = Uri::createFromString( $this->url );

			if ( is_null( $components->getHost() ) ) {
				return false;
			}

			return true;
		} catch ( UriException $e ) {
			return false;
		}
	}

	public function get_domain() {

		if ( $this->is_valid_url() ) {
			$components = Uri::createFromString( $this->url );
			$host       = $components->getHost();

			if ( ! is_null( $host ) ) {
				return $host;
			}
		}

		return null;
	}

}
