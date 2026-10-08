<?php
defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter

class BKSignals_Installer {

    public static function activate() {
        global $wpdb;

        BKSignals_Cron::register_intervals();
        $stats_table = $wpdb->prefix . 'bksignals_product_stats';
        $stats_table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $stats_table ) );

        if ( BKSIGNALS_VERSION !== get_option( 'bksignals_db_version' ) || $stats_table_exists !== $stats_table ) {
            self::create_tables();
        }
        self::set_defaults();
        BKSignals_Cron::schedule();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        BKSignals_Cron::unschedule();
        flush_rewrite_rules();
    }

    private static function create_tables() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();

        $sql = [];

        // Stats.
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bksignals_product_stats (
            product_id   BIGINT(20) UNSIGNED NOT NULL,
            views_total  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            views_24h    INT(10) UNSIGNED NOT NULL DEFAULT 0,
            views_7d     INT(10) UNSIGNED NOT NULL DEFAULT 0,
            views_30d    INT(10) UNSIGNED NOT NULL DEFAULT 0,
            cart_total   BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            cart_24h     INT(10) UNSIGNED NOT NULL DEFAULT 0,
            cart_7d      INT(10) UNSIGNED NOT NULL DEFAULT 0,
            cart_30d     INT(10) UNSIGNED NOT NULL DEFAULT 0,
            sales_total  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            sales_24h    INT(10) UNSIGNED NOT NULL DEFAULT 0,
            sales_7d     INT(10) UNSIGNED NOT NULL DEFAULT 0,
            sales_30d    INT(10) UNSIGNED NOT NULL DEFAULT 0,
            updated_at   DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY  (product_id)
        ) $charset;";

        // Recent sales.
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bksignals_recent_sales (
            id              BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id        BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            order_item_id   BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            product_id      BIGINT(20) UNSIGNED NOT NULL,
            customer_city   VARCHAR(100) NOT NULL DEFAULT '',
            customer_name   VARCHAR(160) NOT NULL DEFAULT '',
            created_at      DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY order_id    (order_id),
            KEY order_item_id (order_item_id),
            KEY product_id  (product_id),
            KEY created_at  (created_at)
        ) $charset;";

        // Sales events.
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bksignals_sales_events (
            order_item_id BIGINT(20) UNSIGNED NOT NULL,
            order_id      BIGINT(20) UNSIGNED NOT NULL,
            product_id    BIGINT(20) UNSIGNED NOT NULL,
            quantity      INT(10) UNSIGNED NOT NULL DEFAULT 1,
            status        VARCHAR(20) NOT NULL DEFAULT 'active',
            customer_city VARCHAR(100) NOT NULL DEFAULT '',
            customer_name VARCHAR(160) NOT NULL DEFAULT '',
            purchased_at  DATETIME NOT NULL,
            voided_at     DATETIME NULL DEFAULT NULL,
            updated_at    DATETIME NOT NULL,
            PRIMARY KEY (order_item_id),
            KEY order_id (order_id),
            KEY product_status (product_id, status),
            KEY purchased_at (purchased_at)
        ) $charset;";

        // Live visitors.
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bksignals_live_visitors (
            visitor_id  VARCHAR(64) NOT NULL,
            product_id  BIGINT(20) UNSIGNED NOT NULL,
            last_seen   DATETIME NOT NULL,
            PRIMARY KEY (visitor_id, product_id),
            KEY product_last_seen (product_id, last_seen),
            KEY last_seen (last_seen)
        ) $charset;";

        // Active carts.
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bksignals_active_carts (
            visitor_id  VARCHAR(64) NOT NULL,
            product_id  BIGINT(20) UNSIGNED NOT NULL,
            quantity    INT(10) UNSIGNED NOT NULL DEFAULT 1,
            updated_at  DATETIME NOT NULL,
            PRIMARY KEY (visitor_id, product_id),
            KEY product_updated (product_id, updated_at),
            KEY updated_at (updated_at)
        ) $charset;";

        // Daily stats.
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bksignals_daily_stats (
            date        DATE NOT NULL,
            product_id  BIGINT(20) UNSIGNED NOT NULL,
            views       INT(10) UNSIGNED NOT NULL DEFAULT 0,
            add_to_cart INT(10) UNSIGNED NOT NULL DEFAULT 0,
            sales       INT(10) UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (date, product_id),
            KEY product_date (product_id, date)
        ) $charset;";

        // View queue.
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bksignals_view_queue (
            id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id  BIGINT(20) UNSIGNED NOT NULL,
            visitor_key VARCHAR(64) NOT NULL,
            viewed_at   DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY viewed_at  (viewed_at)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ( $sql as $query ) {
            dbDelta( $query );
        }

        update_option( 'bksignals_db_version', BKSIGNALS_VERSION );
    }

    public static function maybe_upgrade() {
        global $wpdb;

        self::create_tables();

        if ( '1' !== get_option( 'bksignals_live_index_version', '0' ) ) {
            $table = $wpdb->prefix . 'bksignals_live_visitors';
            $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

            if ( $table_exists === $table ) {
                $exists = $wpdb->get_var( $wpdb->prepare(
                    'SHOW INDEX FROM %i WHERE Key_name = %s',
                    $table,
                    'product_last_seen'
                ) );

                if ( ! $exists ) {
                    $wpdb->query( $wpdb->prepare( 'ALTER TABLE %i ADD KEY product_last_seen (product_id, last_seen) /* %d */', $table, 1 ) );
                }

                update_option( 'bksignals_live_index_version', '1' );
            }
        }

        if ( '1' !== get_option( 'bksignals_daily_index_version', '0' ) ) {
            $table = $wpdb->prefix . 'bksignals_daily_stats';
            $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

            if ( $table_exists === $table ) {
                $exists = $wpdb->get_var( $wpdb->prepare(
                    'SHOW INDEX FROM %i WHERE Key_name = %s',
                    $table,
                    'product_date'
                ) );

                if ( ! $exists ) {
                    $wpdb->query( $wpdb->prepare( 'ALTER TABLE %i ADD KEY product_date (product_id, date) /* %d */', $table, 1 ) );
                }

                update_option( 'bksignals_daily_index_version', '1' );
            }
        }

        if ( '1' !== get_option( 'bksignals_active_cart_table_version', '0' ) ) {
            $charset = $wpdb->get_charset_collate();
            $table = $wpdb->prefix . 'bksignals_active_carts';

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $active_cart_sql = $wpdb->prepare( "CREATE TABLE IF NOT EXISTS %i (
                visitor_id  VARCHAR(64) NOT NULL,
                product_id  BIGINT(20) UNSIGNED NOT NULL,
                quantity    INT(10) UNSIGNED NOT NULL DEFAULT 1,
                updated_at  DATETIME NOT NULL,
                PRIMARY KEY (visitor_id, product_id),
                KEY product_updated (product_id, updated_at),
                KEY updated_at (updated_at)
            ) $charset;", $table );
            dbDelta( $active_cart_sql );

            update_option( 'bksignals_active_cart_table_version', '1' );
        }

        if ( false === get_option( 'bksignals_active_cart_timeout_days' ) ) {
            update_option( 'bksignals_active_cart_timeout_days', '14' );
        }

        if ( '1' !== get_option( 'bksignals_sales_events_table_version', '0' ) ) {
            $charset = $wpdb->get_charset_collate();
            $table = $wpdb->prefix . 'bksignals_sales_events';

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $sales_events_sql = $wpdb->prepare( "CREATE TABLE IF NOT EXISTS %i (
                order_item_id BIGINT(20) UNSIGNED NOT NULL,
                order_id      BIGINT(20) UNSIGNED NOT NULL,
                product_id    BIGINT(20) UNSIGNED NOT NULL,
                quantity      INT(10) UNSIGNED NOT NULL DEFAULT 1,
                status        VARCHAR(20) NOT NULL DEFAULT 'active',
                customer_city VARCHAR(100) NOT NULL DEFAULT '',
                purchased_at  DATETIME NOT NULL,
                voided_at     DATETIME NULL DEFAULT NULL,
                updated_at    DATETIME NOT NULL,
                PRIMARY KEY (order_item_id),
                KEY order_id (order_id),
                KEY product_status (product_id, status),
                KEY purchased_at (purchased_at)
            ) $charset;", $table );
            dbDelta( $sales_events_sql );

            update_option( 'bksignals_sales_events_table_version', '1' );
        }

        if ( '1' !== get_option( 'bksignals_recent_sales_order_columns_version', '0' ) ) {
            $table = $wpdb->prefix . 'bksignals_recent_sales';
            $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

            if ( $table_exists === $table ) {
                if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM %i LIKE %s', $table, 'order_id' ) ) ) {
                    $wpdb->query( $wpdb->prepare( 'ALTER TABLE %i ADD order_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER id /* %d */', $table, 1 ) );
                }
                if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM %i LIKE %s', $table, 'order_item_id' ) ) ) {
                    $wpdb->query( $wpdb->prepare( 'ALTER TABLE %i ADD order_item_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER order_id /* %d */', $table, 1 ) );
                }
                if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW INDEX FROM %i WHERE Key_name = %s', $table, 'order_id' ) ) ) {
                    $wpdb->query( $wpdb->prepare( 'ALTER TABLE %i ADD KEY order_id (order_id) /* %d */', $table, 1 ) );
                }
                if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW INDEX FROM %i WHERE Key_name = %s', $table, 'order_item_id' ) ) ) {
                    $wpdb->query( $wpdb->prepare( 'ALTER TABLE %i ADD KEY order_item_id (order_item_id) /* %d */', $table, 1 ) );
                }
            }

            update_option( 'bksignals_recent_sales_order_columns_version', '1' );
        }

        if ( '1' !== get_option( 'bksignals_sales_customer_columns_version', '0' ) ) {
            foreach ( [ $wpdb->prefix . 'bksignals_recent_sales', $wpdb->prefix . 'bksignals_sales_events' ] as $table ) {
                $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

                if ( $table_exists !== $table ) {
                    continue;
                }

                if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM %i LIKE %s', $table, 'customer_name' ) ) ) {
                    $wpdb->query( $wpdb->prepare( 'ALTER TABLE %i ADD customer_name VARCHAR(160) NOT NULL DEFAULT \'\' AFTER customer_city /* %d */', $table, 1 ) );
                }
            }

            update_option( 'bksignals_sales_customer_columns_version', '1' );
        }

        if ( false === get_option( 'bksignals_popup_mobile_offset' ) ) {
            update_option( 'bksignals_popup_mobile_offset', '72' );
        }

        if ( false === get_option( 'bksignals_popup_mobile_position' ) ) {
            update_option( 'bksignals_popup_mobile_position', 'bottom-right' );
        }
    }

    private static function set_defaults() {
        $defaults = [
            'bksignals_live_visitors_enabled' => '1',
            'bksignals_views_enabled'         => '1',
            'bksignals_cart_enabled'          => '1',
            'bksignals_sales_enabled'         => '1',
            'bksignals_popup_enabled'         => '1',
            'bksignals_popup_delay'           => '5',
            'bksignals_popup_duration'        => '4',
            'bksignals_popup_interval'        => '8',
            'bksignals_popup_limit'           => '10',
            'bksignals_popup_product_scope'   => '1',
            'bksignals_popup_mobile_enabled'  => '1',
            'bksignals_popup_mobile_offset'   => '72',
            'bksignals_popup_mobile_position' => 'bottom-right',
            'bksignals_live_threshold'        => '2',
            'bksignals_view_window'           => '7',
            'bksignals_live_refresh_seconds'  => '30',
            'bksignals_live_timeout_seconds'  => '180',
            'bksignals_active_cart_timeout_days' => '14',
            'bksignals_cron_interval'         => 'bksignals_15min',
            'bksignals_custom_cron_value'     => '2',
            'bksignals_custom_cron_unit'      => 'hours',
            'bksignals_delete_data_on_uninstall' => '0',
            // Retention.
            'bksignals_data_retention_days'  => '90',
            'bksignals_reset_24h_interval'   => 'daily',
            // Testing.
            'bksignals_track_admins'         => '0',
        ];

        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( $key ) ) {
                update_option( $key, $value );
            }
        }
    }
}
