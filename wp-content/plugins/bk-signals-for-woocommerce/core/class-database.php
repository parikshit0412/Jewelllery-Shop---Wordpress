<?php
defined( 'ABSPATH' ) || exit;

// Custom analytics tables require direct database reads and writes.
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

class BKSignals_Database {

    // Tables.

    public static function table_stats()        { global $wpdb; return $wpdb->prefix . 'bksignals_product_stats'; }
    public static function table_recent_sales() { global $wpdb; return $wpdb->prefix . 'bksignals_recent_sales'; }
    public static function table_sales_events() { global $wpdb; return $wpdb->prefix . 'bksignals_sales_events'; }
    public static function table_live()         { global $wpdb; return $wpdb->prefix . 'bksignals_live_visitors'; }
    public static function table_active_carts() { global $wpdb; return $wpdb->prefix . 'bksignals_active_carts'; }
    public static function table_daily()        { global $wpdb; return $wpdb->prefix . 'bksignals_daily_stats'; }
    public static function table_queue()        { global $wpdb; return $wpdb->prefix . 'bksignals_view_queue'; }

    private static function empty_table( string $table ): void {
        global $wpdb;

        $wpdb->query( $wpdb->prepare( 'DELETE FROM %i WHERE 1 = %d', $table, 1 ) );
    }

    // Views.

    public static function queue_view( $product_id, $visitor_key ) {
        global $wpdb;
        $wpdb->insert( self::table_queue(), [
            'product_id'  => (int) $product_id,
            'visitor_key' => sanitize_text_field( $visitor_key ),
            'viewed_at'   => current_time( 'mysql' ),
        ], [ '%d', '%s', '%s' ] );
    }

    // Flush views.
    public static function flush_view_queue() {
        global $wpdb;
        $queue_table = self::table_queue();
        $stats_table = self::table_stats();
        $daily_table = self::table_daily();

        // Pending rows.
        $rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i ORDER BY id ASC LIMIT %d', $queue_table, 2000 ) );
        if ( empty( $rows ) ) {
            return;
        }

        $ids_to_delete = [];
        $product_counts = [];

        foreach ( $rows as $row ) {
            $pid = (int) $row->product_id;
            $key = $row->visitor_key;
            $date = substr( $row->viewed_at, 0, 10 );

            $product_counts[ $pid ]['total'] = ( $product_counts[ $pid ]['total'] ?? 0 ) + 1;
            $product_counts[ $pid ]['dates'][ $date ] = ( $product_counts[ $pid ]['dates'][ $date ] ?? 0 ) + 1;
            $ids_to_delete[] = (int) $row->id;
        }

        foreach ( $product_counts as $pid => $data ) {
            $total = $data['total'];

            // Stats.
            $wpdb->query( $wpdb->prepare(
                "INSERT INTO %i
                    (product_id, views_total, views_24h, views_7d, views_30d, updated_at)
                 VALUES (%d, %d, %d, %d, %d, %s)
                 ON DUPLICATE KEY UPDATE
                    views_total = views_total + VALUES(views_total),
                    views_24h   = views_24h   + VALUES(views_24h),
                    views_7d    = views_7d    + VALUES(views_7d),
                    views_30d   = views_30d   + VALUES(views_30d),
                    updated_at  = VALUES(updated_at)",
                $stats_table, $pid, $total, $total, $total, $total, current_time( 'mysql' )
            ) );

            // Daily stats.
            foreach ( $data['dates'] as $date => $count ) {
                $wpdb->query( $wpdb->prepare(
                    "INSERT INTO %i (date, product_id, views)
                     VALUES (%s, %d, %d)
                     ON DUPLICATE KEY UPDATE views = views + VALUES(views)",
                    $daily_table, $date, $pid, $count
                ) );
            }
        }

        // Cleanup.
        if ( ! empty( $ids_to_delete ) ) {
            $last_id = max( $ids_to_delete );
            $wpdb->query( $wpdb->prepare( 'DELETE FROM %i WHERE id <= %d', $queue_table, $last_id ) );
        }
    }

    // Windows.

    public static function recalculate_rolling_windows() {
        global $wpdb;
        $stats = self::table_stats();
        $daily = self::table_daily();
        $now   = current_time( 'mysql' );
        $today = current_time( 'Y-m-d' );
        $start_7d = gmdate( 'Y-m-d', current_time( 'timestamp' ) - ( 6 * DAY_IN_SECONDS ) );
        $start_30d = gmdate( 'Y-m-d', current_time( 'timestamp' ) - ( 29 * DAY_IN_SECONDS ) );

        $wpdb->query( $wpdb->prepare(
            "
            UPDATE %i s
            SET
                views_24h = COALESCE((
                    SELECT SUM(d.views) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                views_7d = COALESCE((
                    SELECT SUM(d.views) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                views_30d = COALESCE((
                    SELECT SUM(d.views) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                cart_24h = COALESCE((
                    SELECT SUM(d.add_to_cart) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                cart_7d = COALESCE((
                    SELECT SUM(d.add_to_cart) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                cart_30d = COALESCE((
                    SELECT SUM(d.add_to_cart) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                sales_24h = COALESCE((
                    SELECT SUM(d.sales) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                sales_7d = COALESCE((
                    SELECT SUM(d.sales) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                sales_30d = COALESCE((
                    SELECT SUM(d.sales) FROM %i d
                    WHERE d.product_id = s.product_id AND d.date >= %s
                ), 0),
                updated_at = %s
        ",
            $stats,
            $daily,
            $today,
            $daily,
            $start_7d,
            $daily,
            $start_30d,
            $daily,
            $today,
            $daily,
            $start_7d,
            $daily,
            $start_30d,
            $daily,
            $today,
            $daily,
            $start_7d,
            $daily,
            $start_30d,
            $now
        ) );
    }

    // Carts.

    public static function increment_cart( $product_id ) {
        global $wpdb;
        $stats = self::table_stats();
        $daily = self::table_daily();
        $date  = current_time( 'Y-m-d' );

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO %i (product_id, cart_total, cart_24h, cart_7d, cart_30d, updated_at)
             VALUES (%d, 1, 1, 1, 1, %s)
             ON DUPLICATE KEY UPDATE
                cart_total = cart_total + 1,
                cart_24h   = cart_24h   + 1,
                cart_7d    = cart_7d    + 1,
                cart_30d   = cart_30d   + 1,
                updated_at = VALUES(updated_at)",
            $stats, $product_id, current_time( 'mysql' )
        ) );

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO %i (date, product_id, add_to_cart)
             VALUES (%s, %d, 1)
             ON DUPLICATE KEY UPDATE add_to_cart = add_to_cart + 1",
            $daily, $date, $product_id
        ) );
    }

    public static function replace_active_cart( string $visitor_id, array $items ) {
        global $wpdb;

        $visitor_id = sanitize_text_field( $visitor_id );
        $table = self::table_active_carts();
        $now = current_time( 'mysql' );

        $wpdb->delete( $table, [ 'visitor_id' => $visitor_id ], [ '%s' ] );

        foreach ( $items as $product_id => $quantity ) {
            $product_id = absint( $product_id );
            $quantity = absint( $quantity );

            if ( ! $product_id || ! $quantity ) {
                continue;
            }

            $wpdb->query( $wpdb->prepare(
                "INSERT INTO %i (visitor_id, product_id, quantity, updated_at)
                 VALUES (%s, %d, %d, %s)
                 ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), updated_at = VALUES(updated_at)",
                $table,
                $visitor_id,
                $product_id,
                $quantity,
                $now
            ) );
        }
    }

    public static function count_active_carts( $product_id, $days = null ): int {
        global $wpdb;

        if ( null === $days ) {
            $days = (int) get_option( 'bksignals_active_cart_timeout_days', 14 );
        }

        $days = max( 1, min( 90, (int) $days ) );
        $cutoff = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( $days * DAY_IN_SECONDS ) );
        $active_carts = self::table_active_carts();

        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM %i
             WHERE product_id = %d AND quantity > 0 AND updated_at >= %s",
            $active_carts,
            (int) $product_id,
            $cutoff
        ) );
    }

    public static function count_all_active_carts( $days = null ): int {
        global $wpdb;

        if ( null === $days ) {
            $days = (int) get_option( 'bksignals_active_cart_timeout_days', 14 );
        }

        $days = max( 1, min( 90, (int) $days ) );
        $cutoff = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( $days * DAY_IN_SECONDS ) );
        $active_carts = self::table_active_carts();

        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM %i
             WHERE quantity > 0 AND updated_at >= %s",
            $active_carts,
            $cutoff
        ) );
    }

    public static function cleanup_active_carts() {
        global $wpdb;

        $days = (int) get_option( 'bksignals_active_cart_timeout_days', 14 );
        $days = max( 1, min( 90, $days ) );
        $cutoff = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( $days * DAY_IN_SECONDS ) );
        $active_carts = self::table_active_carts();

        $wpdb->query( $wpdb->prepare(
            "DELETE FROM %i WHERE updated_at < %s",
            $active_carts,
            $cutoff
        ) );
    }

    // Sales.

    public static function cleanup_invalid_order_sales( $limit = 200 ) {
        global $wpdb;

        if ( ! function_exists( 'wc_get_order' ) ) {
            return;
        }

        $limit = max( 20, min( 1000, absint( $limit ) ) );
        $valid_statuses = [ 'processing', 'completed' ];
        $sales_events = self::table_sales_events();

        $order_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT DISTINCT order_id
             FROM %i
             WHERE status = 'active' AND order_id > 0
             ORDER BY updated_at ASC
             LIMIT %d",
            $sales_events,
            $limit
        ) );

        foreach ( $order_ids as $order_id ) {
            $order_id = absint( $order_id );
            $order = wc_get_order( $order_id );

            if ( ! $order || ! in_array( $order->get_status(), $valid_statuses, true ) ) {
                self::void_order_sales( $order_id );
            }
        }
    }

    public static function record_order_sales( $order_id ) {
        global $wpdb;

        if ( ! function_exists( 'wc_get_order' ) ) {
            return;
        }

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $city = $order->get_billing_city();
        $customer_name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
        $now = current_time( 'mysql' );
        $purchased_at = $now;

        if ( $order->get_date_paid() ) {
            $purchased_at = $order->get_date_paid()->date( 'Y-m-d H:i:s' );
        } elseif ( $order->get_date_created() ) {
            $purchased_at = $order->get_date_created()->date( 'Y-m-d H:i:s' );
        }

        foreach ( $order->get_items() as $item ) {
            /** @var WC_Order_Item_Product $item */
            $order_item_id = absint( $item->get_id() );
            $product_id = absint( $item->get_product_id() );
            $quantity = max( 1, absint( $item->get_quantity() ) );

            if ( ! $order_item_id || ! $product_id || ! $quantity ) {
                continue;
            }

            $sales_events = self::table_sales_events();
            $existing = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM %i WHERE order_item_id = %d",
                $sales_events,
                $order_item_id
            ), ARRAY_A );

            if ( ! empty( $existing ) && 'active' === ( $existing['status'] ?? '' ) ) {
                continue;
            }

            $wpdb->query( $wpdb->prepare(
                "INSERT INTO %i
                    (order_item_id, order_id, product_id, quantity, status, customer_city, customer_name, purchased_at, voided_at, updated_at)
                 VALUES (%d, %d, %d, %d, 'active', %s, %s, %s, NULL, %s)
                 ON DUPLICATE KEY UPDATE
                    order_id = VALUES(order_id),
                    product_id = VALUES(product_id),
                    quantity = VALUES(quantity),
                    status = 'active',
                    customer_city = VALUES(customer_city),
                    customer_name = VALUES(customer_name),
                    purchased_at = VALUES(purchased_at),
                    voided_at = NULL,
                    updated_at = VALUES(updated_at)",
                $sales_events,
                $order_item_id,
                absint( $order_id ),
                $product_id,
                $quantity,
                sanitize_text_field( $city ),
                sanitize_text_field( $customer_name ),
                $purchased_at,
                $now
            ) );

            self::adjust_sales( $product_id, $quantity, 'increase', $city, $purchased_at, absint( $order_id ), $order_item_id, $customer_name );
        }
    }

    public static function void_order_sales( $order_id ) {
        global $wpdb;
        $sales_events = self::table_sales_events();

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM %i
             WHERE order_id = %d AND status = 'active'",
            $sales_events,
            absint( $order_id )
        ), ARRAY_A );

        if ( empty( $rows ) ) {
            return;
        }

        $now = current_time( 'mysql' );

        foreach ( $rows as $row ) {
            self::adjust_sales(
                (int) $row['product_id'],
                max( 1, (int) $row['quantity'] ),
                'decrease',
                (string) ( $row['customer_city'] ?? '' ),
                (string) ( $row['purchased_at'] ?? $now ),
                (int) $row['order_id'],
                (int) $row['order_item_id'],
                (string) ( $row['customer_name'] ?? '' )
            );

            $wpdb->update(
                self::table_sales_events(),
                [
                    'status'     => 'void',
                    'voided_at'  => $now,
                    'updated_at' => $now,
                ],
                [ 'order_item_id' => (int) $row['order_item_id'] ],
                [ '%s', '%s', '%s' ],
                [ '%d' ]
            );
        }
    }

    public static function record_sale( $product_id, $customer_city = '' ) {
        self::adjust_sales( (int) $product_id, 1, 'increase', $customer_city, current_time( 'mysql' ), 0, 0, '' );
    }

    private static function adjust_sales( $product_id, $quantity, string $direction, $customer_city = '', $purchased_at = '', $order_id = 0, $order_item_id = 0, $customer_name = '' ) {
        global $wpdb;
        $stats = self::table_stats();
        $daily = self::table_daily();
        $popup = self::table_recent_sales();
        $quantity = max( 1, absint( $quantity ) );
        $date = $purchased_at ? substr( $purchased_at, 0, 10 ) : current_time( 'Y-m-d' );
        $now = current_time( 'mysql' );
        $event_ts = $purchased_at ? strtotime( $purchased_at ) : current_time( 'timestamp' );
        $now_ts = current_time( 'timestamp' );
        $qty_24h = ( $event_ts >= $now_ts - DAY_IN_SECONDS ) ? $quantity : 0;
        $qty_7d = ( $event_ts >= $now_ts - ( 7 * DAY_IN_SECONDS ) ) ? $quantity : 0;
        $qty_30d = ( $event_ts >= $now_ts - ( 30 * DAY_IN_SECONDS ) ) ? $quantity : 0;

        if ( 'decrease' === $direction ) {
            $wpdb->query( $wpdb->prepare(
                "UPDATE %i
                 SET sales_total = GREATEST(sales_total - %d, 0),
                     sales_24h   = GREATEST(sales_24h - %d, 0),
                     sales_7d    = GREATEST(sales_7d - %d, 0),
                     sales_30d   = GREATEST(sales_30d - %d, 0),
                     updated_at  = %s
                 WHERE product_id = %d",
                $stats,
                $quantity,
                $qty_24h,
                $qty_7d,
                $qty_30d,
                $now,
                (int) $product_id
            ) );

            $wpdb->query( $wpdb->prepare(
                "UPDATE %i
                 SET sales = GREATEST(sales - %d, 0)
                 WHERE date = %s AND product_id = %d",
                $daily,
                $quantity,
                $date,
                (int) $product_id
            ) );

            if ( $order_id ) {
                $wpdb->delete( $popup, [ 'order_id' => (int) $order_id ], [ '%d' ] );
            }

            return;
        }

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO %i (product_id, sales_total, sales_24h, sales_7d, sales_30d, updated_at)
             VALUES (%d, %d, %d, %d, %d, %s)
             ON DUPLICATE KEY UPDATE
                sales_total = sales_total + VALUES(sales_total),
                sales_24h   = sales_24h   + VALUES(sales_24h),
                sales_7d    = sales_7d    + VALUES(sales_7d),
                sales_30d   = sales_30d   + VALUES(sales_30d),
                updated_at  = VALUES(updated_at)",
            $stats,
            (int) $product_id,
            $quantity,
            $qty_24h,
            $qty_7d,
            $qty_30d,
            $now
        ) );

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO %i (date, product_id, sales)
             VALUES (%s, %d, %d)
             ON DUPLICATE KEY UPDATE sales = sales + VALUES(sales)",
            $daily,
            $date,
            (int) $product_id,
            $quantity
        ) );

        $wpdb->insert( $popup, [
            'order_id'      => (int) $order_id,
            'order_item_id' => (int) $order_item_id,
            'product_id'    => (int) $product_id,
            'customer_city' => sanitize_text_field( $customer_city ),
            'customer_name' => sanitize_text_field( $customer_name ),
            'created_at'    => $purchased_at ?: $now,
        ], [ '%d', '%d', '%d', '%s', '%s', '%s' ] );

        $retention_days = max( 7, min( 365, (int) get_option( 'bksignals_data_retention_days', 90 ) ) );
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM %i WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $popup,
            $retention_days
        ) );
    }

    // Live visitors.

    public static function upsert_live_visitor( $product_id, $visitor_id ) {
        global $wpdb;
        $live = self::table_live();

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO %i (visitor_id, product_id, last_seen)
             VALUES (%s, %d, %s)
             ON DUPLICATE KEY UPDATE last_seen = VALUES(last_seen)",
            $live,
            $visitor_id, (int) $product_id, current_time( 'mysql' )
        ) );
    }

    public static function remove_live_visitor( $product_id, $visitor_id ) {
        global $wpdb;
        $wpdb->delete(
            self::table_live(),
            [
                'visitor_id' => sanitize_text_field( $visitor_id ),
                'product_id' => (int) $product_id,
            ],
            [ '%s', '%d' ]
        );
    }

    public static function count_live_visitors( $product_id, $seconds = null ) {
        global $wpdb;
        if ( null === $seconds ) {
            $seconds = (int) get_option( 'bksignals_live_timeout_seconds', 180 );
        }
        $seconds = max( 30, min( 3600, (int) $seconds ) );
        $cutoff = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - (int) $seconds );
        $live = self::table_live();

        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM %i
             WHERE product_id = %d AND last_seen >= %s",
            $live,
            (int) $product_id, $cutoff
        ) );
    }

    public static function cleanup_live_visitors() {
        global $wpdb;
        $seconds = (int) get_option( 'bksignals_live_timeout_seconds', 180 );
        $seconds = max( 30, min( 3600, $seconds ) );
        $cutoff = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - $seconds );
        $live = self::table_live();

        $wpdb->query( $wpdb->prepare(
            "DELETE FROM %i WHERE last_seen < %s",
            $live,
            $cutoff
        ) );
    }

    // Retention.

    // Daily cleanup.
    public static function purge_old_daily_stats() {
        global $wpdb;
        $days = (int) get_option( 'bksignals_data_retention_days', 90 );
        if ( $days < 1 ) return;
        $daily = self::table_daily();

        $wpdb->query( $wpdb->prepare(
            "DELETE FROM %i WHERE date < DATE_SUB(CURDATE(), INTERVAL %d DAY)",
            $daily,
            $days
        ) );
    }

    // Product reset.
    public static function reset_product_stats( int $product_id ) {
        global $wpdb;
        $wpdb->delete( self::table_stats(), [ 'product_id' => $product_id ], [ '%d' ] );
        $wpdb->delete( self::table_daily(), [ 'product_id' => $product_id ], [ '%d' ] );
        $wpdb->delete( self::table_recent_sales(), [ 'product_id' => $product_id ], [ '%d' ] );
        $wpdb->delete( self::table_sales_events(), [ 'product_id' => $product_id ], [ '%d' ] );
        $wpdb->delete( self::table_active_carts(), [ 'product_id' => $product_id ], [ '%d' ] );
    }

    // Full reset.
    public static function reset_all_stats() {
        global $wpdb;
        self::empty_table( self::table_stats() );
        self::empty_table( self::table_daily() );
        self::empty_table( self::table_recent_sales() );
        self::empty_table( self::table_sales_events() );
        self::empty_table( self::table_queue() );
        self::empty_table( self::table_active_carts() );
    }

    public static function count_queued_views(): int {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare( 'SELECT COUNT(*) FROM %i WHERE 1 = %d', self::table_queue(), 1 )
        );
    }

    public static function count_all_live_visitors(): int {
        global $wpdb;

        $live_timeout = max( 30, min( 3600, (int) get_option( 'bksignals_live_timeout_seconds', 180 ) ) );
        $live_cutoff  = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp', true ) - $live_timeout );

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                'SELECT COUNT(DISTINCT visitor_id) FROM %i WHERE last_seen >= %s',
                self::table_live(),
                $live_cutoff
            )
        );
    }

    public static function get_admin_summary_metrics(): array {
        global $wpdb;

        $stats_table = self::table_stats();

        return [
            'total_products' => (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i WHERE 1 = %d', $stats_table, 1 ) ),
            'total_views'    => (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COALESCE(SUM(views_total), 0) FROM %i WHERE 1 = %d', $stats_table, 1 ) ),
            'total_cart'     => (int) self::count_all_active_carts(),
            'total_sales'    => (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COALESCE(SUM(sales_total), 0) FROM %i WHERE 1 = %d', $stats_table, 1 ) ),
            'total_live'     => (int) self::count_all_live_visitors(),
            'queued_views'   => (int) self::count_queued_views(),
        ];
    }

    public static function get_top_products( $limit = 5 ): array {
        global $wpdb;

        $limit = max( 1, min( 20, (int) $limit ) );

        return (array) $wpdb->get_results(
            $wpdb->prepare(
                "SELECT s.product_id, s.views_total, s.cart_total, s.sales_total, p.post_title
                 FROM %i s
                 LEFT JOIN %i p ON p.ID = s.product_id
                 ORDER BY s.views_total DESC, s.sales_total DESC
                 LIMIT %d",
                self::table_stats(),
                $wpdb->posts,
                $limit
            ),
            ARRAY_A
        );
    }

    // Stats.

    public static function get_product_stats( $product_id ) {
        global $wpdb;
        $stats = self::table_stats();

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM %i WHERE product_id = %d",
            $stats,
            (int) $product_id
        ), ARRAY_A );
        return $row ?: [];
    }

    public static function get_product_metric_for_window( $product_id, string $metric, int $window ): int {
        global $wpdb;

        $columns = [
            'views'       => 'views',
            'add_to_cart' => 'add_to_cart',
            'sales'       => 'sales',
        ];

        if ( ! isset( $columns[ $metric ] ) ) {
            return 0;
        }

        $stats = self::get_product_stats( $product_id );
        $prefix = ( 'add_to_cart' === $metric ) ? 'cart' : $metric;

        if ( $window >= 999 ) {
            return (int) ( $stats[ $prefix . '_total' ] ?? 0 );
        }
        if ( $window <= 1 ) {
            return (int) ( $stats[ $prefix . '_24h' ] ?? 0 );
        }
        if ( $window <= 7 ) {
            return (int) ( $stats[ $prefix . '_7d' ] ?? 0 );
        }
        if ( 30 === $window ) {
            return (int) ( $stats[ $prefix . '_30d' ] ?? 0 );
        }

        $window = max( 2, min( 365, $window ) );
        $start_date = gmdate( 'Y-m-d', current_time( 'timestamp' ) - ( ( $window - 1 ) * DAY_IN_SECONDS ) );
        $daily = self::table_daily();

        $column = $columns[ $metric ];

        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COALESCE(SUM(%i), 0)
             FROM %i
             WHERE product_id = %d AND date >= %s",
            $column,
            $daily,
            (int) $product_id,
            $start_date
        ) );
    }

    public static function get_recent_sales( $product_id, $limit = 10 ) {
        global $wpdb;
        $limit = max( 1, min( 50, (int) $limit ) );
        $fetch_limit = min( 150, max( $limit, $limit * 3 ) );
        $recent_sales = self::table_recent_sales();
        $sales_events = self::table_sales_events();
        $posts = $wpdb->posts;

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT rs.*,
                    COALESCE(NULLIF(rs.customer_name, ''), se.customer_name) as customer_name,
                    p.post_title as product_name
             FROM %i rs
             INNER JOIN %i se
                ON se.order_item_id = rs.order_item_id
               AND se.order_id = rs.order_id
               AND se.status = 'active'
             LEFT JOIN %i p ON p.ID = rs.product_id
             WHERE rs.product_id = %d
               AND rs.order_id > 0
               AND rs.order_item_id > 0
               AND COALESCE(NULLIF(rs.customer_name, ''), se.customer_name) <> ''
             ORDER BY rs.created_at DESC
             LIMIT %d",
            $recent_sales,
            $sales_events,
            $posts,
            (int) $product_id, $fetch_limit
        ), ARRAY_A );

        $rows = self::filter_valid_recent_sales_rows( $rows );

        return self::enrich_recent_sales_rows( self::dedupe_recent_sales_rows( $rows, $limit ) );
    }

    public static function get_any_recent_sales( $limit = 20 ) {
        global $wpdb;
        $limit = max( 1, min( 50, (int) $limit ) );
        $fetch_limit = min( 150, max( $limit, $limit * 3 ) );
        $recent_sales = self::table_recent_sales();
        $sales_events = self::table_sales_events();
        $posts = $wpdb->posts;

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT rs.*,
                    COALESCE(NULLIF(rs.customer_name, ''), se.customer_name) as customer_name,
                    p.post_title as product_name
             FROM %i rs
             INNER JOIN %i se
                ON se.order_item_id = rs.order_item_id
               AND se.order_id = rs.order_id
               AND se.status = 'active'
             LEFT JOIN %i p ON p.ID = rs.product_id
             WHERE rs.order_id > 0
               AND rs.order_item_id > 0
               AND COALESCE(NULLIF(rs.customer_name, ''), se.customer_name) <> ''
             ORDER BY rs.created_at DESC
             LIMIT %d",
            $recent_sales,
            $sales_events,
            $posts,
            $fetch_limit
        ), ARRAY_A );

        $rows = self::filter_valid_recent_sales_rows( $rows );

        return self::enrich_recent_sales_rows( self::dedupe_recent_sales_rows( $rows, $limit ) );
    }

    private static function filter_valid_recent_sales_rows( array $rows ): array {
        if ( empty( $rows ) || ! function_exists( 'wc_get_order' ) ) {
            return [];
        }

        $valid = [];
        $valid_statuses = [ 'processing', 'completed' ];

        foreach ( $rows as $row ) {
            $order_id = absint( $row['order_id'] ?? 0 );
            $customer_name = trim( (string) ( $row['customer_name'] ?? '' ) );
            $product_name = trim( (string) ( $row['product_name'] ?? '' ) );

            if ( ! $order_id || '' === $customer_name || '' === $product_name ) {
                continue;
            }

            $order = wc_get_order( $order_id );
            if ( ! $order || ! in_array( $order->get_status(), $valid_statuses, true ) ) {
                self::void_order_sales( $order_id );
                continue;
            }

            $valid[] = $row;
        }

        return $valid;
    }

    private static function dedupe_recent_sales_rows( array $rows, int $limit ): array {
        $seen = [];
        $deduped = [];

        foreach ( $rows as $row ) {
            $order_item_id = (int) ( $row['order_item_id'] ?? 0 );
            $key = $order_item_id > 0 ? 'item:' . $order_item_id : 'row:' . (int) ( $row['id'] ?? 0 );

            if ( isset( $seen[ $key ] ) ) {
                continue;
            }

            $seen[ $key ] = true;
            $deduped[] = $row;

            if ( count( $deduped ) >= $limit ) {
                break;
            }
        }

        return $deduped;
    }

    private static function enrich_recent_sales_rows( array $rows ): array {
        foreach ( $rows as &$row ) {
            $product_id = (int) ( $row['product_id'] ?? 0 );
            $image = '';

            if ( $product_id ) {
                $image_id = get_post_thumbnail_id( $product_id );
                if ( $image_id ) {
                    $image = wp_get_attachment_image_url( $image_id, 'thumbnail' );
                }
            }

            $row['product_image'] = $image ?: '';
            $row['masked_customer_name'] = self::mask_customer_name( (string) ( $row['customer_name'] ?? '' ) );
        }
        unset( $row );

        return $rows;
    }

    private static function mask_customer_name( string $name ): string {
        $name = trim( preg_replace( '/\s+/u', ' ', $name ) );

        if ( '' === $name ) {
            return '';
        }

        $parts = preg_split( '/\s+/u', $name );
        if ( ! is_array( $parts ) ) {
            return '';
        }

        foreach ( $parts as $index => $part ) {
            $parts[ $index ] = self::mask_name_part( $part, 0 === $index );
        }

        return implode( ' ', array_filter( $parts ) );
    }

    private static function mask_name_part( string $part, bool $is_first ): string {
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $part, 'UTF-8' ) : strlen( $part );

        if ( $length <= 0 ) {
            return '';
        }

        $first = function_exists( 'mb_substr' ) ? mb_substr( $part, 0, 1, 'UTF-8' ) : substr( $part, 0, 1 );

        return $first . ( $is_first ? '***' : '****' );
    }
}
