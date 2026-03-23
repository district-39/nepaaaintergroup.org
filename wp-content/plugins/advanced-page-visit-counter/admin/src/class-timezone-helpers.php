<?php

class APVC_Timezone_Helpers {

	public static function get_utc_timezone() {
		 return new \DateTimeZone( 'UTC' );
	}
	public static function get_utc_offset() {
		return '+00:00';
	}
	public static function get_local_timezone() {
		return new \DateTimeZone( self::get_local_offset() );
	}

	public static function get_local_offset() {
		$offset_number = (float) \get_option( 'gmt_offset' );
		$result        = '';
		$result        .= $offset_number < 0 ? '-' : '+';
		$whole_part    = \abs( $offset_number );
		$hour_part     = \floor( $whole_part );
		$minute_part   = $whole_part - $hour_part;
		$hours         = \strval( $hour_part );
		$minutes       = \strval( $minute_part * 60 );
		$result        .= \str_pad( $hours, 2, '0', \STR_PAD_LEFT );
		$result        .= ':';
		$result        .= \str_pad( $minutes, 2, '0', \STR_PAD_LEFT );
		return $result;
	}

	public static function start_of_locale_day( $datetime ) {
		$user_timezone = new \DateTimeZone( self::get_local_offset() );
		$utc_timezone  = new \DateTimeZone( 'UTC' );
		return $datetime->setTimezone( $user_timezone )->setTime( 0, 0, 0 )->setTimezone( $utc_timezone );
	}

	public static function end_of_locale_day( $datetime ) {
		$user_timezone = new \DateTimeZone( self::get_local_offset() );
		$utc_timezone  = new \DateTimeZone( 'UTC' );
		return $datetime->setTimezone( $user_timezone )->setTime( 23, 59, 59 )->setTimezone( $utc_timezone );
	}

	public static function get_wp_day_name( $day = 0 ) {
		$days = array(
			0 => 'sunday',
			1 => 'monday',
			2 => 'tuesday',
			3 => 'wednesday',
			4 => 'thursday',
			5 => 'friday',
			6 => 'saturday',
		);
		return $days[ $day ];
	}

}
