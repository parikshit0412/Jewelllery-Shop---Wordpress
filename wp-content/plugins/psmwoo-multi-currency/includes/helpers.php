<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Helper {

	/**
	 * Get exchange rate
	 *
	 * @param string $code - Currency code.
	 * @return float
	 */
	public static function get_exchange_rate( $code ) {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		if ( isset( $currencies[ $code ]['rate'] ) ) {
			return $currencies[ $code ]['rate'];
		}
		return 1;
	}

	/**
	 * Get exchange rate from API
	 *
	 * @param string $from - From currency code.
	 * @param string $to - To currency code.
	 * @return float
	 */
	public static function get_exchange_rate_from_api( $from, $to ) {

		$advanced = get_option( 'psmwoo_mc_advanced_settings' );
		return PSMWOO_MC_Exchange_Rate::get_exchange_rate( $from, $to, $advanced['currencyExchangeRateApi'], $advanced['currencyExchangeRateApiKey'] );
	}

	/**
	 * Get currency flag icon URL
	 *
	 * @param string $code - Currency code.
	 * @return string
	 */
	public static function get_flag_icon_url( $code ) {
		$country = self::get_country_code_by_currency( $code );
		if ( empty( $country ) ) {
			return '';
		}

		$country = strtolower( $country );
		$flag_path = 'assets/flags/' . $country . '.svg';

		if ( file_exists( PSMWOO_MC_ABSPATH . $flag_path ) ) {
			return PSMWOO_MC_PLUGIN_URL . $flag_path;
		}

		return '';
	}

	/**
	 * Update exchange rates
	 *
	 * @return void
	 */
	public static function update_exchange_rates() {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		$store_currency_code = get_option( 'woocommerce_currency' );
		foreach ( $currencies as $code => $currency ) {
			// exclude store currency.
			if ( $code === $store_currency_code ) {
				$currencies[ $code ]['rate'] = 1;
				continue;
			}
			// exclude if rate mode is manual.
			if ( $currency['rateMode'] === 'manual' ) {
				continue;
			}
			$rate = self::get_exchange_rate_from_api( $store_currency_code, $code );
			$currencies[ $code ]['rate'] = $rate ? $rate : $currencies[ $code ]['rate'];
		}
		update_option( 'psmwoo_mc_currencies', $currencies );
	}

	/**
	 * Get country code by currency code
	 *
	 * @param string $currency - Currency code.
	 * @return string
	 */
	public static function get_country_code_by_currency( $currency ) {
		$currency_country_map = array(
			'AED' => 'AE',
			'AFN' => 'AF',
			'ALL' => 'AL',
			'AMD' => 'AM',
			'ANG' => 'AN',
			'AOA' => 'AO',
			'ARS' => 'AR',
			'AUD' => 'AU',
			'AWG' => 'AW',
			'AZN' => 'AZ',
			'BAM' => 'BA',
			'BBD' => 'BB',
			'BDT' => 'BD',
			'BGN' => 'BG',
			'BHD' => 'BH',
			'BIF' => 'BI',
			'BMD' => 'BM',
			'BND' => 'BN',
			'BOB' => 'BO',
			'BRL' => 'BR',
			'BSD' => 'BS',
			'BTN' => 'BT',
			'BWP' => 'BW',
			'BYN' => 'BY',
			'BZD' => 'BZ',
			'CAD' => 'CA',
			'CDF' => 'CD',
			'CHF' => 'CH',
			'CLP' => 'CL',
			'CNY' => 'CN',
			'COP' => 'CO',
			'CRC' => 'CR',
			'CUP' => 'CU',
			'CVE' => 'CV',
			'CZK' => 'CZ',
			'DJF' => 'DJ',
			'DKK' => 'DK',
			'DOP' => 'DO',
			'DZD' => 'DZ',
			'EGP' => 'EG',
			'ERN' => 'ER',
			'ETB' => 'ET',
			'EUR' => 'EU',
			'FJD' => 'FJ',
			'FKP' => 'FK',
			'FOK' => 'FO',
			'GBP' => 'GB',
			'GEL' => 'GE',
			'GGP' => 'GG',
			'GHS' => 'GH',
			'GIP' => 'GI',
			'GMD' => 'GM',
			'GNF' => 'GN',
			'GTQ' => 'GT',
			'GYD' => 'GY',
			'HKD' => 'HK',
			'HNL' => 'HN',
			'HRK' => 'HR',
			'HTG' => 'HT',
			'HUF' => 'HU',
			'IDR' => 'ID',
			'ILS' => 'IL',
			'IMP' => 'IM',
			'INR' => 'IN',
			'IQD' => 'IQ',
			'IRR' => 'IR',
			'ISK' => 'IS',
			'JEP' => 'JE',
			'JMD' => 'JM',
			'JOD' => 'JO',
			'JPY' => 'JP',
			'KES' => 'KE',
			'KGS' => 'KG',
			'KHR' => 'KH',
			'KID' => 'KI',
			'KMF' => 'KM',
			'KRW' => 'KR',
			'KWD' => 'KW',
			'KYD' => 'KY',
			'KZT' => 'KZ',
			'LAK' => 'LA',
			'LBP' => 'LB',
			'LKR' => 'LK',
			'LRD' => 'LR',
			'LSL' => 'LS',
			'LYD' => 'LY',
			'MAD' => 'MA',
			'MDL' => 'MD',
			'MGA' => 'MG',
			'MKD' => 'MK',
			'MMK' => 'MM',
			'MNT' => 'MN',
			'MOP' => 'MO',
			'MRU' => 'MR',
			'MUR' => 'MU',
			'MVR' => 'MV',
			'MWK' => 'MW',
			'MXN' => 'MX',
			'MYR' => 'MY',
			'MZN' => 'MZ',
			'NAD' => 'NA',
			'NGN' => 'NG',
			'NIO' => 'NI',
			'NOK' => 'NO',
			'NPR' => 'NP',
			'NZD' => 'NZ',
			'OMR' => 'OM',
			'PAB' => 'PA',
			'PEN' => 'PE',
			'PGK' => 'PG',
			'PHP' => 'PH',
			'PKR' => 'PK',
			'PLN' => 'PL',
			'PYG' => 'PY',
			'QAR' => 'QA',
			'RON' => 'RO',
			'RSD' => 'RS',
			'RUB' => 'RU',
			'RWF' => 'RW',
			'SAR' => 'SA',
			'SBD' => 'SB',
			'SCR' => 'SC',
			'SDG' => 'SD',
			'SEK' => 'SE',
			'SGD' => 'SG',
			'SHP' => 'SH',
			'SLL' => 'SL',
			'SOS' => 'SO',
			'SRD' => 'SR',
			'SSP' => 'SS',
			'STN' => 'ST',
			'SYP' => 'SY',
			'SZL' => 'SZ',
			'THB' => 'TH',
			'TJS' => 'TJ',
			'TMT' => 'TM',
			'TND' => 'TN',
			'TOP' => 'TO',
			'TRY' => 'TR',
			'TTD' => 'TT',
			'TVD' => 'TV',
			'TWD' => 'TW',
			'TZS' => 'TZ',
			'UAH' => 'UA',
			'UGX' => 'UG',
			'USD' => 'US',
			'UYU' => 'UY',
			'UZS' => 'UZ',
			'VES' => 'VE',
			'VND' => 'VN',
			'VUV' => 'VU',
			'WST' => 'WS',
			'XAF' => 'XA',
			'XCD' => 'XC',
			'XOF' => 'XO',
			'XPF' => 'XP',
			'YER' => 'YE',
			'ZAR' => 'ZA',
			'ZMW' => 'ZM',
			'ZWL' => 'ZW',
		);
		return isset( $currency_country_map[ $currency ] ) ? $currency_country_map[ $currency ] : '';
	}

	/**
	 * Get currency code by country code
	 *
	 * @param string $country - Country code.
	 * @return string
	 */
	public static function get_currency_code_by_country( $country ) {

		$auto_select_country_currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
		foreach ( $auto_select_country_currencies as $code => $cur ) {
			if ( in_array( $country, $cur['countries'] ) ) {
				return $code;
			}
		}

		$country_currency_map = array(
			'AE'  => 'AED',
			'AF'  => 'AFN',
			'AL'  => 'ALL',
			'AM'  => 'AMD',
			'AN'  => 'ANG',
			'AO'  => 'AOA',
			'AR'  => 'ARS',
			'AU'  => 'AUD',
			'AW'  => 'AWG',
			'AZ'  => 'AZN',
			'BA'  => 'BAM',
			'BB'  => 'BBD',
			'BD'  => 'BDT',
			'BG'  => 'BGN',
			'BH'  => 'BHD',
			'BI'  => 'BIF',
			'BM'  => 'BMD',
			'BN'  => 'BND',
			'BO'  => 'BOB',
			'BR'  => 'BRL',
			'BS'  => 'BSD',
			'BT'  => 'BTN',
			'BW'  => 'BWP',
			'BY'  => 'BYN',
			'BZ'  => 'BZD',
			'CA'  => 'CAD',
			'CD'  => 'CDF',
			'CH'  => 'CHF',
			'CL'  => 'CLP',
			'CN'  => 'CNY',
			'CO'  => 'COP',
			'CR'  => 'CRC',
			'CU'  => 'CUP',
			'CV'  => 'CVE',
			'CZ'  => 'CZK',
			'DJ'  => 'DJF',
			'DK'  => 'DKK',
			'DO'  => 'DOP',
			'DZ'  => 'DZD',
			'EG'  => 'EGP',
			'ER'  => 'ERN',
			'ET'  => 'ETB',
			'EU'  => 'EUR',
			'DE'  => 'EUR',
			'AT'  => 'EUR',
			'BE'  => 'EUR',
			'CY'  => 'EUR',
			'EE'  => 'EUR',
			'FI'  => 'EUR',
			'FR'  => 'EUR',
			'GR'  => 'EUR',
			'IE'  => 'EUR',
			'IT'  => 'EUR',
			'LV'  => 'EUR',
			'LT'  => 'EUR',
			'LU'  => 'EUR',
			'MT'  => 'EUR',
			'NL'  => 'EUR',
			'PT'  => 'EUR',
			'SK'  => 'EUR',
			'SI'  => 'EUR',
			'FJ'  => 'FJD',
			'FK'  => 'FKP',
			'FO'  => 'FOK',
			'GB'  => 'GBP',
			'GE'  => 'GEL',
			'GG'  => 'GGP',
			'GH'  => 'GHS',
			'GI'  => 'GIP',
			'GM'  => 'GMD',
			'GN'  => 'GNF',
			'GT'  => 'GTQ',
			'GY'  => 'GYD',
			'HK'  => 'HKD',
			'HN'  => 'HNL',
			'HR'  => 'HRK',
			'HT'  => 'HTG',
			'HU'  => 'HUF',
			'ID'  => 'IDR',
			'IL'  => 'ILS',
			'IM'  => 'IMP',
			'IN'  => 'INR',
			'IQ'  => 'IQD',
			'IR'  => 'IRR',
			'IS'  => 'ISK',
			'JE'  => 'JEP',
			'JM'  => 'JMD',
			'JO'  => 'JOD',
			'JP'  => 'JPY',
			'KE'  => 'KES',
			'KG'  => 'KGS',
			'KH'  => 'KHR',
			'KI'  => 'KID',
			'KM'  => 'KMF',
			'KR'  => 'KRW',
			'KW'  => 'KWD',
			'KY'  => 'KYD',
			'KZ'  => 'KZT',
			'LA'  => 'LAK',
			'LB'  => 'LBP',
			'LK'  => 'LKR',
			'LR'  => 'LRD',
			'LS'  => 'LSL',
			'LY'  => 'LYD',
			'MA'  => 'MAD',
			'MD'  => 'MDL',
			'MG'  => 'MGA',
			'MK'  => 'MKD',
			'MM'  => 'MMK',
			'MN'  => 'MNT',
			'MO'  => 'MOP',
			'MR'  => 'MRU',
			'MU'  => 'MUR',
			'MV'  => 'MVR',
			'MW'  => 'MWK',
			'MX'  => 'MXN',
			'MY'  => 'MYR',
			'MZ'  => 'MZN',
			'NA'  => 'NAD',
			'NG'  => 'NGN',
			'NI'  => 'NIO',
			'NO'  => 'NOK',
			'NP'  => 'NPR',
			'NZ'  => 'NZD',
			'OM'  => 'OMR',
			'PA'  => 'PAB',
			'PE'  => 'PEN',
			'PG'  => 'PGK',
			'PH'  => 'PHP',
			'PK'  => 'PKR',
			'PL'  => 'PLN',
			'PY'  => 'PYG',
			'QA'  => 'QAR',
			'RO'  => 'RON',
			'RS'  => 'RSD',
			'RU'  => 'RUB',
			'RW'  => 'RWF',
			'SA'  => 'SAR',
			'SB'  => 'SBD',
			'SC'  => 'SCR',
			'SD'  => 'SDG',
			'SE'  => 'SEK',
			'SG'  => 'SGD',
			'SH'  => 'SHP',
			'SL'  => 'SLL',
			'SO'  => 'SOS',
			'SR'  => 'SRD',
			'SS'  => 'SSP',
			'ST'  => 'STN',
			'SY'  => 'SYP',
			'SZ'  => 'SZL',
			'TH'  => 'THB',
			'TJ'  => 'TJS',
			'TM'  => 'TMT',
			'TN'  => 'TND',
			'TO'  => 'TOP',
			'TR'  => 'TRY',
			'TT'  => 'TTD',
			'TVD' => 'TV',
			'TW'  => 'TWD',
			'TZ'  => 'TZS',
			'UA'  => 'UAH',
			'UG'  => 'UGX',
			'US'  => 'USD',
			'UY'  => 'UYU',
			'UZ'  => 'UZS',
			'VE'  => 'VES',
			'VN'  => 'VND',
			'VU'  => 'VUV',
			'WS'  => 'WST',
			'XA'  => 'XAF',
			'XC'  => 'XCD',
			'XO'  => 'XOF',
			'XP'  => 'XPF',
			'YE'  => 'YER',
			'ZA'  => 'ZAR',
			'ZM'  => 'ZMW',
			'ZW'  => 'ZWL',
		);

		return isset( $country_currency_map[ $country ] ) ? $country_currency_map[ $country ] : '';
	}

	/**
	 * Set cookie currency
	 *
	 * @param string $currency_code - Currency code.
	 * @return void
	 */
	public static function set_cookie_currency( $currency_code ) {
		setcookie( 'psmmc_currency', $currency_code, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Check if the current request is cart/checkout page request
	 *
	 * @return boolean
	 */
	public static function is_checkout() {

		global $wp;
		$is_checkout = false;

		if ( isset( $wp->query_vars['rest_route'] ) &&
			preg_match( '/^\/wc\/store\/v1/', $wp->query_vars['rest_route'] )
		) {
			PSMWOO_MC_Checkout_Currency::set_current_currency();
			$is_checkout = true;
		}

		$is_checkout = $is_checkout || is_checkout() || is_cart() || is_wc_endpoint_url( 'order-pay' ) || is_wc_endpoint_url( 'order-received' );
		return apply_filters( 'psmwoo_mc_is_checkout', $is_checkout );
	}

	/**
	 * Check if the current currency is the default currency
	 *
	 * @param string $currency - Currency code.
	 * @return boolean
	 */
	public static function is_default_currency( $currency = '' ) {
		return $currency === get_option( 'woocommerce_currency' );
	}

	/**
	 * Check if we need to use original product price (no need to convert)
	 *
	 * @param boolean    $flag - Original flag.
	 * @param float      $price - Product price.
	 * @param WC_Product $product - Product object.
	 * @return boolean
	 */
	public static function need_original_product_price( $flag, $price, $product ) {

		if ( empty( $price ) || ! is_numeric( $price ) || 0 === floatval( $price ) || self::is_json_product_request() ) {
			return true;
		}

		return apply_filters( 'psmwoo_mc_need_original_product_price', $flag, $price, $product );
	}

	/**
	 * Check if the current request is a JSON product request
	 * wp-json/wc/v3/products?consumer_key=&consumer_secret=&per_page=&page=
	 *
	 * @return boolean
	 */
	public static function is_json_product_request() {

		return isset( $_REQUEST['consumer_key'] ) && isset( $_REQUEST['consumer_secret'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Add new currency.
	 *
	 * @param string $code Currency code.
	 * @return array
	 */
	public static function add_new_currency( $code ) {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		$rate = self::get_exchange_rate_from_api( get_option( 'woocommerce_currency' ), $code );

		$currencies[ $code ] = array(
			'enabled'           => true,
			'symbol'            => get_woocommerce_currency_symbol( $code ),
			'currencyPosition'  => get_option( 'woocommerce_currency_pos' ),
			'thousandSeparator' => get_option( 'woocommerce_price_thousand_sep' ),
			'decimalSeparator'  => get_option( 'woocommerce_price_decimal_sep' ),
			'decimal'           => absint( get_option( 'woocommerce_price_num_decimals' ) ),
			'rounding'          => 'disabled',
			'roundingTo'        => 1,
			'roundingMinus'     => 0,
			'rate'              => $rate ? $rate : 1,
			'rateMode'          => 'auto', // TODO: If API is not available, set to manual.
			'fee'               => 0,
			'feeMode'           => 'fixed',
		);
		update_option( 'psmwoo_mc_currencies', $currencies );

		// update checkout currencies.
		$checkout_currencies = get_option( 'psmwoo_mc_checkout_currencies' );
		$checkout_currencies[ $code ] = array(
			'enabled'        => true,
			'paymentMethods' => array(),
		);
		update_option( 'psmwoo_mc_checkout_currencies', $checkout_currencies );

		// update auto select country currencies.
		$auto_select_country_currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
		$auto_select_country_currencies[ $code ] = array(
			'countries' => array(),
		);
		update_option( 'psmwoo_mc_auto_select_country_currencies', $auto_select_country_currencies );
		return $currencies;
	}
}
