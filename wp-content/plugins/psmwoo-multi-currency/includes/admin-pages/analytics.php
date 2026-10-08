<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

use Automattic\WooCommerce\Admin\API\Reports\Categories\DataStore as CategoriesDataStore;
use Automattic\WooCommerce\Admin\API\Reports\Products\DataStore as ProductsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\Coupons\DataStore as CouponsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore as CustomersDataStore;

class PSMWOO_MC_Analytics {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_woo_analytics_hooks' ) );
		add_action( 'init', array( __CLASS__, 'add_currency_filter' ) );
	}

	/**
	 * Add currency filter.
	 *
	 * @return void
	 */
	public static function add_currency_filter() {

		if ( ! isset( $_REQUEST['page'] ) || 'wc-admin' !== $_REQUEST['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$currencies = get_option( 'psmwoo_mc_currencies' );
		$currency_codes = get_woocommerce_currencies();

		$response = array(
			array(
				'label' => __( 'All currencies', 'psmwoo-multi-currency' ),
				'value' => 'all_currency',
			),
		);
		foreach ( $currencies as $code => $currency ) {
			$response[] = array(
				'label' => $currency_codes[ $code ],
				'value' => $code,
			);
		}

		$data_registry = Automattic\WooCommerce\Blocks\Package::container()->get(
			Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class
		);

		$data_registry->add( 'multiCurrency', $response );
	}

	/**
	 * Register WooCommerce analytics hooks.
	 *
	 * @return void
	 */
	public static function register_woo_analytics_hooks() {

		// ANALYTICS.
		add_filter( 'woocommerce_leaderboards', array( __CLASS__, 'custom_leaderboards_analytics' ), 20, 5 );

		// ORDERS.
		add_filter( 'woocommerce_analytics_orders_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_orders_stats_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_orders_select_query', array( __CLASS__, 'woocommerce_analytics_orders_select_query' ), 20, 2 );
		add_filter( 'woocommerce_analytics_orders_stats_select_query', array( __CLASS__, 'woocommerce_analytics_orders_stats_select_query' ), 20, 2 );

		// PRODUCTS.
		add_filter( 'woocommerce_analytics_products_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_products_stats_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_products_select_query', array( __CLASS__, 'woocommerce_analytics_products_select_query' ), 20, 2 );
		add_filter( 'woocommerce_analytics_products_stats_select_query', array( __CLASS__, 'woocommerce_analytics_products_stats_select_query' ), 20, 2 );

		// REVENUE.
		add_filter( 'woocommerce_analytics_revenue_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_revenue_select_query', array( __CLASS__, 'woocommerce_analytics_revenue_select_query' ), 20, 2 );

		// CATEGORIES.
		add_filter( 'woocommerce_analytics_categories_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_clauses_select_categories_subquery', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_categories_select_query', array( __CLASS__, 'woocommerce_analytics_categories_select_query' ), 20, 2 );

		// COUPONS.
		add_filter( 'woocommerce_analytics_coupons_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_coupons_stats_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_coupons_stats_select_query', array( __CLASS__, 'woocommerce_analytics_coupons_stats_select_query' ), 20, 2 );
		add_filter( 'woocommerce_analytics_coupons_select_query', array( __CLASS__, 'woocommerce_analytics_coupons_select_query' ), 20, 2 );

		// TAXES.
		add_filter( 'woocommerce_analytics_taxes_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_taxes_stats_query_args', array( __CLASS__, 'apply_currency_arg' ) );
		add_filter( 'woocommerce_analytics_taxes_stats_select_query', array( __CLASS__, 'woocommerce_analytics_taxes_stats_select_query' ), 20, 2 );
		add_filter( 'woocommerce_analytics_taxes_select_query', array( __CLASS__, 'woocommerce_analytics_taxes_select_query' ), 20, 2 );

		// add currency to the join query.
		add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', array( __CLASS__, 'add_join_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_join_products_subquery', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_products_stats_total', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_products_stats_interval', array( __CLASS__, 'add_join_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_join_coupons_subquery', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_coupons_stats_total', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_coupons_stats_interval', array( __CLASS__, 'add_join_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_join_taxes_subquery', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_taxes_stats_total', array( __CLASS__, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_taxes_stats_interval', array( __CLASS__, 'add_join_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_join_categories_subquery', array( __CLASS__, 'add_join_subquery' ) );

		// add currency to the where clause.
		add_filter( 'woocommerce_analytics_clauses_where_orders_subquery', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_orders_stats_total', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_orders_stats_interval', array( __CLASS__, 'add_where_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_where_products_subquery', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_products_stats_total', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_products_stats_interval', array( __CLASS__, 'add_where_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_where_coupons_subquery', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_coupons_stats_total', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_coupons_stats_interval', array( __CLASS__, 'add_where_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_where_taxes_subquery', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_taxes_stats_total', array( __CLASS__, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_taxes_stats_interval', array( __CLASS__, 'add_where_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_where_categories_subquery', array( __CLASS__, 'add_where_subquery' ) );

		// add currency to the select clause.
		add_filter( 'woocommerce_analytics_clauses_select_orders_subquery', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', array( __CLASS__, 'add_select_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_select_products_subquery', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_products_stats_total', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_products_stats_interval', array( __CLASS__, 'add_select_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_select_coupons_subquery', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_coupons_stats_total', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_coupons_stats_interval', array( __CLASS__, 'add_select_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_select_taxes_subquery', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_taxes_stats_total', array( __CLASS__, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_taxes_stats_interval', array( __CLASS__, 'add_select_subquery' ) );
	}

	/**
	 * Apply currency arg.
	 *
	 * @param array $args The query args.
	 * @return array
	 */
	public static function apply_currency_arg( $args ) {

		$currency = self::get_requested_currency();
		if ( $currency ) {
			$args['currency'] = $currency;
		}
		return $args;
	}

	/**
	 * Add join subquery for multi-currency in WooCommerce Analytics.
	 *
	 * @param array $clauses The query clauses.
	 * @return array
	 */
	public static function add_join_subquery( $clauses ) {

		global $wpdb;
		$currency = self::get_requested_currency();
		if ( $currency ) {

			if ( self::is_hpos_active() ) {
				$clauses[] = "JOIN {$wpdb->prefix}wc_orders wc_orders ON {$wpdb->prefix}wc_order_stats.order_id = wc_orders.id";
			} else {
				$clauses[] = "JOIN {$wpdb->postmeta} currency_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = currency_postmeta.post_id";
			}
		}
		return $clauses;
	}

	/**
	 * Add where subquery for multi-currency filtering.
	 *
	 * @param array $clauses The query clauses.
	 * @return array
	 */
	public static function add_where_subquery( $clauses ) {
		global $wpdb;

		$currency = self::get_requested_currency();
		if ( $currency ) {
			if ( self::is_hpos_active() ) {
				$clauses[] = "AND wc_orders.currency = '{$currency}'";
			} else {
				$clauses[] = "AND currency_postmeta.meta_key = '_order_currency' AND currency_postmeta.meta_value = '{$currency}'";
			}
		}
		return $clauses;
	}

	/**
	 * Add select subquery to include currency in results.
	 *
	 * @param array $clauses The query clauses.
	 * @return array
	 */
	public static function add_select_subquery( $clauses ) {
		global $wpdb;

		$currency = self::get_requested_currency();
		if ( $currency ) {
			if ( self::is_hpos_active() ) {
				$clauses[] = ', wc_orders.currency AS currency';
			} else {
				$clauses[] = ', currency_postmeta.meta_value AS currency';
			}
		}

		return $clauses;
	}

	/**
	 * Get excluded statuses.
	 *
	 * @return array
	 */
	public static function get_excluded_statuses() {
		$excluded_statuses = \WC_Admin_Settings::get_option( 'woocommerce_excluded_report_order_statuses', array( 'pending', 'failed', 'cancelled' ) );
		$excluded_statuses = array_merge( array( 'auto-draft', 'trash' ), array_map( 'esc_sql', $excluded_statuses ) );
		$excluded_statuses = preg_filter( '/^/', 'wc-', $excluded_statuses );
		return $excluded_statuses;
	}

	/**
	 * Prepare excluded statuses.
	 *
	 * @return string
	 */
	public static function prepare_excluded_statuses() {
		$excluded_statuses = self::get_excluded_statuses();

		return implode(
			',',
			array_map(
				function ( $value ) {
					global $wpdb;
					return $wpdb->prepare( '%s', $value );
				},
				$excluded_statuses
			)
		);
	}

	/**
	 * Get order product lookup results.
	 *
	 * @param array  $args The query args.
	 * @param string $type The type of query.
	 * @return array
	 */
	public static function get_order_product_lookup_results( $args, $type = false ) {
		global $wpdb;

		$before_date = isset( $args['before'] ) ? get_gmt_from_date( $args['before'], 'Y-m-d H:i:s' ) : '';
		$after_date = isset( $args['after'] ) ? get_gmt_from_date( $args['after'], 'Y-m-d H:i:s' ) : '';
		$excluded_statuses = self::prepare_excluded_statuses();
		$date_type = ! $type ? 'date_created' : get_option( 'woocommerce_date_type', 'date_paid' );
		switch ( $date_type ) {
			case 'date_created':
				$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->prepare(
						"SELECT `table_name`.*
					FROM {$wpdb->prefix}wc_order_product_lookup as `table_name`
					JOIN {$wpdb->prefix}wc_order_stats as `order_table` ON `table_name`.`order_id` = `order_table`.`order_id` 
					WHERE `order_table`.`status` NOT IN (%s) 
					AND `order_table`.`date_created` >= %s 
					AND `order_table`.`date_created` <= %s",
						$excluded_statuses,
						$after_date,
						$before_date
					),
					ARRAY_A
				);
				break;
			case 'date_paid':
				$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->prepare(
						"SELECT `table_name`.*
					FROM {$wpdb->prefix}wc_order_product_lookup as `table_name`
					JOIN {$wpdb->prefix}wc_order_stats as `order_table` ON `table_name`.`order_id` = `order_table`.`order_id` 
					WHERE `order_table`.`status` NOT IN (%s) 
					AND `order_table`.`date_paid` >= %s 
					AND `order_table`.`date_paid` <= %s",
						$excluded_statuses,
						$after_date,
						$before_date
					),
					ARRAY_A
				);
				break;
			case 'date_completed':
				$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->prepare(
						"SELECT `table_name`.*
					FROM {$wpdb->prefix}wc_order_product_lookup as `table_name`
					JOIN {$wpdb->prefix}wc_order_stats as `order_table` ON `table_name`.`order_id` = `order_table`.`order_id` 
					WHERE `order_table`.`status` NOT IN (%s) 
					AND `order_table`.`date_completed` >= %s 
					AND `order_table`.`date_completed` <= %s",
						$excluded_statuses,
						$after_date,
						$before_date
					),
					ARRAY_A
				);
				break;
			default:
				$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->prepare(
						"SELECT `table_name`.*
				FROM {$wpdb->prefix}wc_order_product_lookup as `table_name`
				JOIN {$wpdb->prefix}wc_order_stats as `order_table` ON `table_name`.`order_id` = `order_table`.`order_id` 
				WHERE `order_table`.`status` NOT IN (%s) 
				AND `order_table`.`date_created` >= %s 
				AND `order_table`.`date_created` <= %s",
						$excluded_statuses,
						$after_date,
						$before_date
					),
					ARRAY_A
				);
				break;
		}
		return $results;
	}

	/**
	 * Check if HPOS is active.
	 *
	 * @return bool
	 */
	private static function is_hpos_active() {
		return class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	}

	/**
	 * Check if apply for all currency.
	 *
	 * @return bool
	 */
	public static function apply_for_all_currency() {
		return ! self::get_requested_currency();
	}

	/**
	 * Get net revenue lookup by product id.
	 *
	 * @param array $args The query args.
	 * @param bool  $variation The variation.
	 * @return array
	 */
	public static function get_net_revenue_lookup_by_product_id( $args, $variation = false ) {
		$default_currency = get_option( 'woocommerce_currency' );
		$results = self::get_order_product_lookup_results( $args );
		$args_net_revenue = array();
		foreach ( $results as $key => $value ) {
			$product_id = intval( $value['product_id'] );
			$order = wc_get_order( $value['order_id'] );
			$currency_code = $order->get_currency();
			$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
			if ( ! $rate ) {
				$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
			}
			$product_price = (float) $value['product_net_revenue'] / $rate;
			if ( $variation ) {
				$variation_id = intval( $value['variation_id'] ) ? $value['variation_id'] : 0;
				if ( ! isset( $args_net_revenue[ $variation_id ] ) ) {
					$args_net_revenue[ $variation_id ] = $product_price;
				} else {
					$args_net_revenue[ $variation_id ] = $args_net_revenue[ $variation_id ] + $product_price;
				}
			} elseif ( ! isset( $args_net_revenue[ $product_id ] ) ) {
					$args_net_revenue[ $product_id ] = $product_price;
			} else {
				$args_net_revenue[ $product_id ] = $args_net_revenue[ $product_id ] + $product_price;
			}
		}
		return $args_net_revenue;
	}

	/**
	 * Get order coupon lookup results.
	 *
	 * @param array $args The query args.
	 * @return array
	 */
	public static function get_order_coupon_lookup_results( $args ) {
		global $wpdb;
		$before_date       = isset( $args['before'] ) ? get_gmt_from_date( $args['before'], 'Y-m-d H:i:s' ) : '';
		$after_date        = isset( $args['after'] ) ? get_gmt_from_date( $args['after'], 'Y-m-d H:i:s' ) : '';
		$excluded_statuses = self::prepare_excluded_statuses();

		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT `table_name`.*
			FROM {$wpdb->prefix}wc_order_coupon_lookup as `table_name`
			JOIN {$wpdb->prefix}wc_order_stats as `order_table` ON `table_name`.`order_id` = `order_table`.`order_id` 
			WHERE `order_table`.`status` NOT IN ( %s )
			  AND `order_table`.`date_created` >= %s
			  AND `order_table`.`date_created` <= %s",
				$excluded_statuses,
				$after_date,
				$before_date
			),
			ARRAY_A
		);
		return $results;
	}

	/**
	 * Get analytics tax info.
	 *
	 * @param array $args The query args.
	 * @return array
	 */
	public static function get_order_tax_lookup_results( $args ) {
		global $wpdb;
		$before_date       = isset( $args['before'] ) ? get_gmt_from_date( $args['before'], 'Y-m-d H:i:s' ) : '';
		$after_date        = isset( $args['after'] ) ? get_gmt_from_date( $args['after'], 'Y-m-d H:i:s' ) : '';
		$excluded_statuses = self::prepare_excluded_statuses();

		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT `table_name`.*
			FROM {$wpdb->prefix}wc_order_tax_lookup as `table_name`
			JOIN {$wpdb->prefix}wc_order_stats as `order_table` ON `table_name`.`order_id` = `order_table`.`order_id` 
			WHERE `order_table`.`status` NOT IN ( %s )
			  AND `order_table`.`date_created` >= %s
			  AND `order_table`.`date_created` <= %s",
				$excluded_statuses,
				$after_date,
				$before_date
			),
			ARRAY_A
		);
		return $results;
	}

	/**
	 * Get analytics tax info.
	 *
	 * @param array $args The query args.
	 * @return array
	 */
	public static function get_tax_info_lookup_by_tax_id( $args ) {
		$data = self::get_order_tax_lookup_results( $args );
		$args_taxes = array();
		foreach ( $data as $key => $value ) {
			$tax_id = intval( $value['tax_rate_id'] );
			$order = wc_get_order( $value['order_id'] );
			$currency_code = $order->get_currency();
			$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
			if ( ! $rate ) {
				$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
			}
			$shipping_tax = (float) $value['shipping_tax'] / $rate;
			$order_tax    = (float) $value['order_tax'] / $rate;
			$total_tax    = (float) $value['total_tax'] / $rate;
			if ( ! isset( $args_taxes[ $tax_id ] ) ) {
				$args_taxes[ $tax_id ] = array(
					'shipping_tax' => $shipping_tax,
					'order_tax'    => $order_tax,
					'total_tax'    => $total_tax,
				);
			} else {
				$args_taxes[ $tax_id ]['shipping_tax'] = $args_taxes[ $tax_id ]['shipping_tax'] + $shipping_tax;
				$args_taxes[ $tax_id ]['order_tax']    = $args_taxes[ $tax_id ]['order_tax'] + $order_tax;
				$args_taxes[ $tax_id ]['total_tax']    = $args_taxes[ $tax_id ]['total_tax'] + $total_tax;
			}
		}
		return $args_taxes;
	}

	/**
	 * WooCommerce analytics orders select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_products_select_query( $results, $args ) {
		if ( self::apply_for_all_currency() ) {
			if ( ! isset( $results->data ) ) {
				return $results;
			}
			$args_net_revenue = self::get_net_revenue_lookup_by_product_id( $args );
			foreach ( $results->data as $key => $value ) {
				$product_id = intval( $value['product_id'] );
				$results->data[ $key ]['net_revenue'] = isset( $args_net_revenue[ $product_id ] ) ? $args_net_revenue[ $product_id ] : 0;
			}
		}
		return $results;
	}

	/**
	 * WooCommerce analytics orders stats select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_products_stats_select_query( $results, $args ) {

		if ( self::apply_for_all_currency() ) {
			$args_data = self::get_order_product_lookup_results( $args );
			$net_revenue = 0;
			$default_currency = get_option( 'woocommerce_currency' );

			foreach ( $args_data as $key => $value ) {
				$order = wc_get_order( $value['order_id'] );
				$currency_code = $order->get_currency();
				$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
				if ( ! $rate ) {
					$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
				}
				$net_revenue += (float) $value['product_net_revenue'] / $rate;

				$results->totals->net_revenue = $net_revenue;
			}
		}
		return $results;
	}


	/**
	 * WooCommerce analytics orders select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_revenue_select_query( $results, $args ) {
		if ( self::apply_for_all_currency() ) {
			$data = self::get_analytics_revenue_info( $args, 'revenue' );
			$results->totals->gross_sales     = $data['gross_sales'];
			$results->totals->net_revenue     = $data['net_revenue'];
			$results->totals->total_sales     = $data['total_sales'];
			$results->totals->shipping        = $data['shipping'];
			$results->totals->taxes           = $data['taxes'];
			$results->totals->avg_order_value = ( isset( $results->totals ) && isset( $results->totals->orders_count ) && $results->totals->orders_count > 0 ) ? $data['net_revenue'] / $results->totals->orders_count : 0;
			$results->totals->coupons         = $data['coupon'];
			if ( isset( $results->intervals ) && ! empty( $results->intervals ) ) {
				foreach ( $results->intervals as $interval_key => $interval ) {
					$interval_data = self::get_analytics_revenue_info(
						array(
							'before' => $interval['date_end'],
							'after'  => $interval['date_start'],
						),
						'revenue'
					);

					$results->intervals[ $interval_key ]['subtotals']->gross_sales = $interval_data['gross_sales'];
					$results->intervals[ $interval_key ]['subtotals']->net_revenue = $interval_data['net_revenue'];
					$results->intervals[ $interval_key ]['subtotals']->taxes       = $interval_data['taxes'];
					$results->intervals[ $interval_key ]['subtotals']->shipping    = $interval_data['shipping'];
					$results->intervals[ $interval_key ]['subtotals']->total_sales = $interval_data['total_sales'];
					$results->intervals[ $interval_key ]['subtotals']->coupons     = $interval_data['coupon'];

				}
			}
		}
		return $results;
	}

	/**
	 * Get analytics revenue info.
	 *
	 * @param array $args The query args.
	 * @param array $category_id The category id.
	 * @return array
	 */
	public static function get_net_revenue_lookup_by_category_id( $args, $category_id ) {
		$net_revenue = 0;
		$data        = self::get_order_product_lookup_results( $args );
		$default_currency = get_option( 'woocommerce_currency' );
		foreach ( $data as $key => $value ) {
			$product_cats_ids = wc_get_product_term_ids( intval( $value['product_id'] ), 'product_cat' );
			if ( in_array( $category_id, $product_cats_ids ) ) {
				$order = wc_get_order( $value['order_id'] );
				$currency_code = $order->get_currency();
				$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
				if ( ! $rate ) {
					$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
				}
				$net_revenue += (float) $value['product_net_revenue'] / $rate;
			}
		}
		return $net_revenue;
	}

	/**
	 * Get analytics revenue info.
	 *
	 * @param array $args The query args.
	 * @return array
	 */
	public static function get_amount_lookup_by_coupon_id( $args ) {

		$data = self::get_order_coupon_lookup_results( $args );
		$args_discount_amount = array();
		foreach ( $data as $key => $value ) {
			$coupon_id = intval( $value['coupon_id'] );
			$order = wc_get_order( $value['order_id'] );
			$currency_code = $order->get_currency();
			$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
			if ( ! $rate ) {
				$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
			}
			$discount_amount = (float) $value['discount_amount'] / $rate;
			if ( ! isset( $args_discount_amount[ $coupon_id ] ) ) {
				$args_discount_amount[ $coupon_id ] = $discount_amount;
			} else {
				$args_discount_amount[ $coupon_id ] = $args_discount_amount[ $coupon_id ] + $discount_amount;
			}
		}
		return $args_discount_amount;
	}

	/**
	 * WooCommerce analytics categories select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_categories_select_query( $results, $args ) {

		if ( self::apply_for_all_currency() ) {
			if ( ! isset( $results->data ) ) {
				return $results;
			}
			foreach ( $results->data as $category_key => $category ) {
				$results->data[ $category_key ]['net_revenue'] = self::get_net_revenue_lookup_by_category_id( $args, $category['category_id'] );
			}
		}
		return $results;
	}


	/**
	 * WooCommerce analytics coupons select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_coupons_stats_select_query( $results, $args ) {
		if ( self::apply_for_all_currency() ) {
			$data = self::get_amount_lookup_by_coupon_id( $args );
			$total_discount_amount = 0;
			foreach ( $data as $key => $value ) {
				$total_discount_amount += $value;
			}
			$results->totals->amount = $total_discount_amount;
		}
		return $results;
	}


	/**
	 * WooCommerce analytics coupons select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_coupons_select_query( $results, $args ) {

		if ( self::apply_for_all_currency() ) {
			$data = self::get_amount_lookup_by_coupon_id( $args );
			if ( isset( $results->totals ) ) {
				$results->totals->amount = $data ? array_sum( $data ) : 0;
			} else {
				if ( ! isset( $results->data ) ) {
					return $results;
				}
				foreach ( $results->data as $coupon_key => $coupon ) {
					$coupon_id                              = intval( $coupon['coupon_id'] );
					$results->data[ $coupon_key ]['amount'] = isset( $data[ $coupon_id ] ) ? $data[ $coupon_id ] : 0;
				}
			}
		}
		return $results;
	}


	/**
	 * WooCommerce analytics taxes select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_taxes_stats_select_query( $results, $args ) {

		if ( self::apply_for_all_currency() ) {
			$data = self::get_order_product_lookup_results( $args );
			$total_shipping_tax_amount = 0;
			$total_order_tax           = 0;
			foreach ( $data as $key => $value ) {
				$order = wc_get_order( $value['order_id'] );
				$currency_code = $order->get_currency();
				$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
				if ( ! $rate ) {
					$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
				}
				$total_shipping_tax_amount += (float) $value['shipping_tax_amount'] / $rate;
				$total_order_tax           += (float) $value['tax_amount'] / $rate;
			}

			$results->totals->order_tax    = $total_order_tax;
			$results->totals->shipping_tax = $total_shipping_tax_amount;
			$results->totals->total_tax    = $total_shipping_tax_amount + $total_order_tax;
		}
		return $results;
	}

	/**
	 * WooCommerce analytics taxes select query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_taxes_select_query( $results, $args ) {

		if ( self::apply_for_all_currency() ) {
			if ( ! isset( $results->data ) ) {
				return $results;
			}
			$data = self::get_tax_info_lookup_by_tax_id( $args );
			foreach ( $results->data as $tax_key => $tax ) {
				$tax_id = intval( $tax['tax_rate_id'] );
				if ( isset( $data[ $tax_id ] ) ) {
					$results->data[ $tax_key ]['shipping_tax'] = isset( $data[ $tax_id ] ) ? $data[ $tax_id ]['shipping_tax'] : 0;
					$results->data[ $tax_key ]['order_tax']    = isset( $data[ $tax_id ] ) ? $data[ $tax_id ]['order_tax'] : 0;
					$results->data[ $tax_key ]['total_tax']    = isset( $data[ $tax_id ] ) ? $data[ $tax_id ]['total_tax'] : 0;
				}
			}
		}
		return $results;
	}


	/**
	 * Leaderboards categories by currency.
	 *
	 * @param string $currency The currency.
	 * @param array  $category The category.
	 * @param array  $args The query args.
	 * @return array
	 */
	public static function leaderboards_categories_by_currency( $currency, $category, $args ) {
		$currency_code = $currency ? $currency : get_option( 'woocommerce_currency' );
		$currency_symbol = get_woocommerce_currency_symbol( $currency_code );
		$args_data       = self::get_order_product_lookup_results( $args );
		$net_revenue     = 0;
		$item_sold       = 0;
		$default_currency = get_option( 'woocommerce_currency' );

		foreach ( $args_data as $key => $value ) {
			$product_cats_ids = wc_get_product_term_ids( intval( $value['product_id'] ), 'product_cat' );
			if ( in_array( $category['category_id'], $product_cats_ids ) ) {
				$product_net_revenue = (float) $value['product_net_revenue'];
				$order = wc_get_order( $value['order_id'] );
				$currency_code = $order->get_currency();
				if ( $currency ) {
					if ( $currency === $currency_code ) {
						$net_revenue += $product_net_revenue;
						$item_sold    = $item_sold + intval( $value['product_qty'] );
					}
				} else {
					$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
					if ( ! $rate ) {
						$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
					}
					$net_revenue += $product_net_revenue / $rate;
					$item_sold    = $item_sold + intval( $value['product_qty'] );
				}
			}
		}

		$category['net_revenue'] = $net_revenue;
		$category['items_sold'] = $item_sold;
		$category['currency_symbol'] = $currency_symbol;

		return $category;
	}

	/**
	 * Get categories leaderboard.
	 *
	 * @param int    $per_page The number of items per page.
	 * @param string $after The after date.
	 * @param string $before The before date.
	 * @param string $persisted_query The persisted query.
	 * @return array
	 */
	public static function get_categories_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$categories_data_store = new CategoriesDataStore();
		$categories_data = $per_page > 0 ? $categories_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_categories_query_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- woocomerce core hook, cannot be prefixed.
				array(
					'orderby'       => 'items_sold',
					'order'         => 'desc',
					'after'         => $after,
					'before'        => $before,
					'per_page'      => $per_page,
					'extended_info' => true,
				)
			)
		)->data : array();

		$rows = array();

		foreach ( $categories_data as $category ) {
			$url_query = wp_parse_args(
				array(
					'filter'     => 'single_category',
					'categories' => $category['category_id'],
				),
				$persisted_query
			);
			$category_url = wc_admin_url( '/analytics/categories', $url_query );
			$category_name = isset( $category['extended_info'] ) && isset( $category['extended_info']['name'] ) ? $category['extended_info']['name'] : '';

			$currency = self::get_requested_currency();
			$args = array(
				'after'  => $after,
				'before' => $before,
			);

			$category = self::leaderboards_categories_by_currency( $currency, $category, $args );
			$rows[] = array(
				array(
					'display' => '<a href="' . esc_attr( $category_url ) . '">' . esc_html( $category_name ) . '</a>',
					'value'   => $category_name,
				),
				array(
					'display' => wc_admin_number_format( $category['items_sold'] ),
					'value'   => $category['items_sold'],
				),
				array(
					'display' => wp_kses_post( html_entity_decode( '~' . $category['currency_symbol'] . number_format( $category['net_revenue'], 2, '.', ',' ) ) ),
					'value'   => $category['net_revenue'],
				),
			);
		}

		return array(
			'id'      => 'categories',
			'label'   => __( 'Top Categories - Items Sold', 'psmwoo-multi-currency' ),
			'headers' => array(
				array(
					'label' => __( 'Category', 'psmwoo-multi-currency' ),
				),
				array(
					'label' => __( 'Items Sold', 'psmwoo-multi-currency' ),
				),
				array(
					'label' => __( 'Net Sales', 'psmwoo-multi-currency' ),
				),
			),
			'rows'    => $rows,
		);
	}


	/**
	 * Get products leaderboard.
	 *
	 * @param int    $per_page The number of items per page.
	 * @param string $after The after date.
	 * @param string $before The before date.
	 * @param string $persisted_query The persisted query.
	 * @return array
	 */
	public static function get_products_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$products_data_store = new ProductsDataStore();
		$products_data = $per_page > 0 ? $products_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_products_query_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- woocomerce core hook, cannot be prefixed.
				array(
					'orderby'       => 'items_sold',
					'order'         => 'desc',
					'after'         => $after,
					'before'        => $before,
					'per_page'      => $per_page,
					'extended_info' => true,
				)
			)
		)->data : array();

		$rows = array();
		$currency       = self::get_requested_currency();
		$currency_code  = $currency ? $currency : get_option( 'woocommerce_currency' );
		$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

		foreach ( $products_data as $product ) {
			$url_query = wp_parse_args(
				array(
					'filter'   => 'single_product',
					'products' => $product['product_id'],
				),
				$persisted_query
			);
			$product_url = wc_admin_url( '/analytics/products', $url_query );
			$product_name = isset( $product['extended_info'] ) && isset( $product['extended_info']['name'] ) ? $product['extended_info']['name'] : '';
			if ( isset( $_GET['currency'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$currency = sanitize_text_field( wp_unslash( $_GET['currency'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} else {
				$args = array(
					'after'  => $after,
					'before' => $before,
				);
				$args_net_revenue = self::get_net_revenue_lookup_by_product_id( $args );
				$product['net_revenue'] = isset( $args_net_revenue[ $product['product_id'] ] ) ? $args_net_revenue[ $product['product_id'] ] : 0;
			}
			$rows[] = array(
				array(
					'display' => '<a href="' . esc_attr( $product_url ) . '">' . esc_html( $product_name ) . '</a>',
					'value'   => $product_name,
				),
				array(
					'display' => wc_admin_number_format( $product['items_sold'] ),
					'value'   => $product['items_sold'],
				),
				array(
					'display' => wp_kses_post( html_entity_decode( '~' . $currency_symbol . number_format( $product['net_revenue'], 2, '.', ',' ) ) ),
					'value'   => $product['net_revenue'],
				),
			);
		}
		return array(
			'id'      => 'products',
			'label'   => __( 'Top Products - Items Sold', 'psmwoo-multi-currency' ),
			'headers' => array(
				array(
					'label' => __( 'Product', 'psmwoo-multi-currency' ),
				),
				array(
					'label' => __( 'Items Sold', 'psmwoo-multi-currency' ),
				),
				array(
					'label' => __( 'Net Sales', 'psmwoo-multi-currency' ),
				),
			),
			'rows'    => $rows,
		);
	}

	/**
	 * Get coupons leaderboard.
	 *
	 * @param int    $per_page The number of items per page.
	 * @param string $after The after date.
	 * @param string $before The before date.
	 * @param string $persisted_query The persisted query.
	 * @return array
	 */
	public static function get_coupons_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$coupons_data_store = new CouponsDataStore();
		$coupons_data = $per_page > 0 ? $coupons_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_coupons_query_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- woocomerce core hook, cannot be prefixed.
				array(
					'orderby'       => 'orders_count',
					'order'         => 'desc',
					'after'         => $after,
					'before'        => $before,
					'per_page'      => $per_page,
					'extended_info' => true,
				)
			)
		)->data : array();

		$rows = array();
		$selected_currency = self::get_requested_currency();
		$currency_code    = $selected_currency ? $selected_currency : get_option( 'woocommerce_currency' );
		$currency_symbol  = get_woocommerce_currency_symbol( $currency_code );

		foreach ( $coupons_data as $coupon ) {
			$url_query = wp_parse_args(
				array(
					'filter'  => 'single_coupon',
					'coupons' => $coupon['coupon_id'],
				),
				$persisted_query
			);
			$coupon_url = wc_admin_url( '/analytics/coupons', $url_query );
			$coupon_code = isset( $coupon['extended_info']['code'] ) ? $coupon['extended_info']['code'] : '';

			// Convert coupon amount to selected currency if needed.
			$total_discount_converted = $coupon['amount'] ? $coupon['amount'] : 0;
			if ( $coupon['amount'] && isset( $coupon['currency'] ) && ( $currency_code != $coupon['currency'] ) ) {
				// If coupon has a currency, convert using the rate. Otherwise, use as is.
				$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
				if ( $rate && $rate > 0 ) {
					$total_discount_converted = $coupon['amount'] * $rate;
				}
			}

			$rows[] = array(
				array(
					'display' => '<a href="' . esc_attr( $coupon_url ) . '">' . esc_html( $coupon_code ) . '</a>',
					'value'   => $coupon_code,
				),
				array(
					'display' => wc_admin_number_format( (int) $coupon['orders_count'] ),
					'value'   => (int) $coupon['orders_count'],
				),
				array(
					'display' => wp_kses_post( html_entity_decode( '~' . $currency_symbol . number_format( $total_discount_converted, 2, '.', ',' ) ) ),
					'value'   => $total_discount_converted,
				),
			);
		}

		return array(
			'id'      => 'coupons',
			'label'   => __( 'Top Coupons - Number of Orders', 'psmwoo-multi-currency' ),
			'headers' => array(
				array( 'label' => __( 'Coupon Code', 'psmwoo-multi-currency' ) ),
				array( 'label' => __( 'Orders', 'psmwoo-multi-currency' ) ),
				array( 'label' => __( 'Amount Discounted', 'psmwoo-multi-currency' ) ),
			),
			'rows'    => $rows,
		);
	}


	/**
	 * Get customers leaderboard.
	 *
	 * @param int    $per_page The number of items per page.
	 * @param string $after The after date.
	 * @param string $before The before date.
	 * @param string $persisted_query The persisted query.
	 * @return array
	 */
	public static function get_customers_leaderboard( $per_page, $after, $before, $persisted_query ) {
		$currency = get_option( 'woocommerce_currency' );
		$customers_data_store = new CustomersDataStore();
		$customers_data = $per_page > 0 ? $customers_data_store->get_data(
			apply_filters(
				'woocommerce_analytics_customers_query_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- woocomerce core hook, cannot be prefixed.
				array(
					'orderby'  => 'total_spend',
					'order'    => 'desc',
					'after'    => $after,
					'before'   => $before,
					'per_page' => $per_page,
				)
			)
		)->data : array();

		$rows = array();
		$currency       = self::get_requested_currency();
		$store_default_currency = get_option( 'woocommerce_currency' );
		$currency_code  = $currency ? $currency : $store_default_currency;
		$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

		foreach ( $customers_data as $customer ) {
			$url_query    = wp_parse_args(
				array(
					'filter'    => 'single_customer',
					'customers' => $customer['id'],
				),
				$persisted_query
			);
			$customer_url = wc_admin_url( '/analytics/customers', $url_query );

			$total_spend_converted = 0;
			$args = array(
				'customer_id' => $customer['id'],
				'status'      => array( 'completed', 'processing', 'on-hold' ),
				'limit'       => -1,
				'return'      => 'objects',
				'date_after'  => $after,
				'date_before' => $before,
			);

			if ( $currency_code != $store_default_currency ) {
				$args['meta_query'] = array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- This is necessary to filter orders by currency to avoid unnecessary conversions.
					array(
						'key'   => '_psmwmc_currency',
						'value' => $currency_code,
					),
				);
			}
			$customer_orders = wc_get_orders( $args );

			foreach ( $customer_orders as $order ) {
				$order_total = (float) $order->get_total();
				$order_currency = $order->get_currency();

				if ( $order_currency === $currency_code ) {
					$total_spend_converted += $order_total;
				} else {
					$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
					if ( ! $rate ) {
						$rate = PSMWOO_MC_Helper::get_exchange_rate( $order_currency );
					}
					if ( $rate && $rate > 0 ) {
						$total_spend_converted += $order_total / $rate;
					} else {
						$total_spend_converted += $order_total;
					}
				}
			}

			$rows[] = array(
				array(
					'display' => '<a href="' . esc_attr( $customer_url ) . '">' . esc_html( $customer['username'] ) . '</a>',
					'value'   => $customer['username'] ? $customer['username'] : $customer['name'],
				),
				array(
					'display' => $customer['orders_count'],
					'value'   => $customer['orders_count'],
				),
				array(
					'display' => wp_kses_post( html_entity_decode( '~' . $currency_symbol . number_format( $total_spend_converted, 2, '.', ',' ) ) ),
					'value'   => $total_spend_converted,
				),
			);
		}
		return array(
			'id'      => 'customers',
			'label'   => __( 'Top Customers - Total Spend', 'psmwoo-multi-currency' ),
			'headers' => array(
				array(
					'label' => __( 'Customer Name', 'psmwoo-multi-currency' ),
				),
				array(
					'label' => __( 'Orders', 'psmwoo-multi-currency' ),
				),
				array(
					'label' => __( 'Total Spend', 'psmwoo-multi-currency' ),
				),
			),
			'rows'    => $rows,
		);
	}

	/**
	 * Custom leaderboards analytics.
	 *
	 * @param array  $leaderboards The leaderboards.
	 * @param int    $per_page The number of items per page.
	 * @param string $after The after date.
	 * @param string $before The before date.
	 * @param string $persisted_query The persisted query.
	 * @return array
	 */
	public static function custom_leaderboards_analytics( $leaderboards, $per_page, $after, $before, $persisted_query ) {
		$leaderboards = array(
			self::get_categories_leaderboard( $per_page, $after, $before, $persisted_query ),
			self::get_products_leaderboard( $per_page, $after, $before, $persisted_query ),
			self::get_coupons_leaderboard( $per_page, $after, $before, $persisted_query ),
			self::get_customers_leaderboard( $per_page, $after, $before, $persisted_query ),
		);
		return $leaderboards;
	}


	/**
	 * Filter the orders query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_orders_select_query( $results, $args ) {
		if ( self::apply_for_all_currency() ) {

			$default_currency = get_option( 'woocommerce_currency' );

			foreach ( $results->data as $key => $value ) {
				$order = wc_get_order( $value['order_id'] );
				$currency_code = $order->get_currency();
				$order_by_currency = $default_currency !== $currency_code ? true : false;
				if ( $order_by_currency ) {
					$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
					if ( ! $rate ) {
						$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
					}
					if ( 1 !== $rate ) {
						$results->data[ $key ]['net_total'] = (float) $value['net_total'] / $rate;
						$results->data[ $key ]['total_sales'] = (float) $value['total_sales'] / $rate;
					}
				}
			}
		}
		return $results;
	}

	/**
	 * Filter the orders stats query.
	 *
	 * @param array $results The query results.
	 * @param array $args The query args.
	 * @return array
	 */
	public static function woocommerce_analytics_orders_stats_select_query( $results, $args ) {

		if ( self::apply_for_all_currency() ) {
			$data = self::get_analytics_revenue_info( $args, 'orders' );
			$results->totals->gross_sales = $data['gross_sales'];
			$results->totals->net_revenue = $data['net_revenue'];
			$results->totals->total_sales = $data['total_sales'];
			$results->totals->shipping = $data['shipping'];
			$results->totals->taxes = $data['taxes'];
			$results->totals->avg_order_value = $results->totals->orders_count > 0 ? $data['net_revenue'] / $results->totals->orders_count : 0;
			$results->totals->coupons = $data['coupon'];
		}

		return $results;
	}

	/**
	 * Get analytics revenue info.
	 *
	 * @param array  $args The query args.
	 * @param string $type The type of query.
	 * @return array
	 */
	public static function get_analytics_revenue_info( $args, $type = false ) {
		$args_data = self::get_order_product_lookup_results( $args, $type );
		$net_revenue = 0;
		$total_sales = 0;
		$shipping_totals = 0;
		$tax_amounts = 0;
		$coupon_amounts = 0;
		foreach ( $args_data as $key => $value ) {
			$tax = (float) $value['tax_amount'] + (float) $value['shipping_tax_amount'];
			$order = wc_get_order( $value['order_id'] );
			$currency_code = $order->get_currency();
			$default_currency = get_option( 'woocommerce_currency' );
			$order_by_currency = $default_currency !== $currency_code ? true : false;
			if ( $order_by_currency ) {
				$rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
				if ( ! $rate ) {
					$rate = PSMWOO_MC_Helper::get_exchange_rate( $currency_code );
				}
				$net_revenue += (float) $value['product_net_revenue'] / $rate;
				$total_sales += (float) $value['product_gross_revenue'] / $rate;
				$shipping_totals += (float) $value['shipping_amount'] / $rate;
				$tax_amounts += $tax / $rate;
				$coupon_amounts += (float) $value['coupon_amount'] / $rate;
			} else {
				$net_revenue += (float) $value['product_net_revenue'];
				$total_sales += (float) $value['product_gross_revenue'];
				$shipping_totals += (float) $value['shipping_amount'];
				$tax_amounts += $tax;
				$coupon_amounts += (float) $value['coupon_amount'];
			}
		}
		$gross_sales = $coupon_amounts > 0 ? $net_revenue + $coupon_amounts : $net_revenue;
		return array(
			'gross_sales' => $gross_sales,
			'net_revenue' => $net_revenue,
			'total_sales' => $total_sales,
			'shipping'    => $shipping_totals,
			'taxes'       => $tax_amounts,
			'coupon'      => $coupon_amounts,
		);
	}

	/**
	 * Get the requested currency from the query string, sanitized.
	 *
	 * @return string|false
	 */
	private static function get_requested_currency() {
		if ( isset( $_GET['currency'] ) && ! empty( $_GET['currency'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sanitize_text_field( wp_unslash( $_GET['currency'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		return false;
	}
}
PSMWOO_MC_Analytics::init();
