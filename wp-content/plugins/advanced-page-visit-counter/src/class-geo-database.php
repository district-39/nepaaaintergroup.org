<?php

class APVC_Geo_Database {

	private $latest_geo_database_checksum = '320bfae90bc6257f0cb52191cc410995';
	private $database_file_name           = APVC_GEO_DB_DIR.'/apvc-location-database.mmdb';

	public function ip_to_geo( $ip ) {
		try {
			$reader = new MaxMind\Db\Reader( $this->database_path() );
			$geo    = $reader->get( $ip );
			$reader->close();

			return $geo;
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
	}

	private function skip_update() {

		$file_missing = file_exists( $this->database_path() ) === false;

		if ( $file_missing ) {
			return false;
		}

		$valid_checksum = verify_file_md5( $this->database_path(), $this->latest_geo_database_checksum ) === true;

		if ( $valid_checksum ) {
			return true;
		}

		return false;
	}

	public function maybe_dispatch_download_job() {
		if ( $this->skip_update() ) {
			return;
		}

		if ( ! is_dir( APVC_GEO_DB_DIR ) ) {
			mkdir( APVC_GEO_DB_DIR, 0777, true );
		}

		$this->download();
	}

	public function download() {
		if ( $this->skip_update() ) {
			return;
		}

		update_option( 'apvc_is_database_downloading', '1' );

		try {
			wp_remote_get(
				$this->database_download_url(),
				array(
					'stream'   => true,
					'filename' => $this->zip_path(),
					'timeout'  => 60,
				)
			);

			$zip = new \ZipArchive();
			if ( $zip->open( $this->zip_path() ) === true ) {
				$zip->extractTo( $this->wordpress_upload_folder_path() );
				$zip->close();
			}
			sleep(10 );

			wp_delete_file( $this->zip_path() );

			$time = new \DateTime( 'now' );
			update_option( 'apvc_geo_database_date', $time->format( 'Y-m-d' ), 'no' );

		} catch ( \Exception $e ) {
			// Do nothing
		}

		update_option( 'apvc_is_database_downloading', 0 );
	}

	private function zip_file_name() {
		return 'apvc-geo-db.zip';
	}

	private function wordpress_upload_folder_path() {
		return APVC_GEO_DB_DIR;
	}

	private function zip_path() {
		return $this->wordpress_upload_folder_path() . '/'. $this->zip_file_name();
	}

	private function database_path() {
		return $this->database_file_name;
	}

	private function database_download_url() {
		return 'https://pagevisitcounter.com/apvc/db_file/' . $this->zip_file_name();
	}
}
