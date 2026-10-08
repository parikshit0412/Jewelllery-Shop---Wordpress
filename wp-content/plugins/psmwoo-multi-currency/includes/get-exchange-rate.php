<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Exchange_Rate {

	/**
	 * Get the exchange rates
	 *
	 * @param string $from The base currency code.
	 * @param string $to The target currency code.
	 * @param string $api_name The API name.
	 * @param string $api_key The API key.
	 * @return float
	 */
	public static function get_exchange_rate( $from, $to, $api_name, $api_key ) {

		$rate = false;
		if ( $from == $to ) {
			return 1;
		}
		switch ( $api_name ) {
			case 'yahoo':
				$rate = self::get_yahoo_exchange_rate( $from, $to );
				break;
		}

		return $rate;
	}

	/**
	 * Get the exchange rate from Yahoo Finance.
	 *
	 * @param string $from The base currency code.
	 * @param string $to The target currency code.
	 * @return float
	 */
	public static function get_yahoo_exchange_rate( $from, $to ) {

		$date = time();
		$time = $date - 60 * 86400;
		$url = "https://query1.finance.yahoo.com/v8/finance/chart/{$from}{$to}=X?symbol={$from}{$to}%3DX&period1={$time}&period2={$date}&interval=1d&includePrePost=false&events=div%7Csplit%7Cearn&lang=en-US&region=US&corsDomain=finance.yahoo.com";
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return false;
		}
		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code !== 200 ) {
			return false;
		}

		// get response body & decode response body.
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( isset( $data['chart']['result'][0]['meta']['regularMarketPrice'] ) ) {
			return (float) $data['chart']['result'][0]['meta']['regularMarketPrice'];
		}
		return false;
	}
}
