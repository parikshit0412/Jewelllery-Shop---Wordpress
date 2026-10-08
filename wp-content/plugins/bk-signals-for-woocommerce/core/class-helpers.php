<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Helpers {

    // Bots.

    private static $bot_patterns = [
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
        'yandexbot', 'facebookexternalhit', 'twitterbot', 'linkedinbot',
        'ahrefsbot', 'semrushbot', 'dotbot', 'mj12bot', 'blexbot',
        'gptbot', 'claudebot', 'anthropic', 'petalbot', 'bytespider',
        'ia_archiver', 'archive.org_bot', 'wget', 'curl', 'python-requests',
    ];

    public static function is_bot(): bool {
        $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) : '';
        if ( empty( $ua ) ) return true;
        foreach ( self::$bot_patterns as $pattern ) {
            if ( false !== strpos( $ua, $pattern ) ) return true;
        }
        return false;
    }

    public static function is_admin_user(): bool {
        return is_user_logged_in() && current_user_can( 'manage_options' );
    }

    public static function should_track(): bool {
        return ! self::is_bot() && ! self::is_admin_user();
    }

    // Visitor key.

    public static function get_visitor_key(): string {
        $ip  = self::get_client_ip();
        $ua  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        return md5( $ip . $ua );
    }

    private static function get_client_ip(): string {
        foreach ( [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ] as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $raw_value = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
                $ip = trim( explode( ',', $raw_value )[0] );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    // Visitor cookie.

    public static function get_visitor_id(): string {
        $cookie_name = 'bksignals_vid';
        $cookie_value = ! empty( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
        if ( '' !== $cookie_value && ctype_alnum( $cookie_value ) ) {
            return substr( $cookie_value, 0, 64 );
        }
        $vid = bin2hex( random_bytes( 16 ) );
        if ( ! headers_sent() ) {
            setcookie( $cookie_name, $vid, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
        }
        $_COOKIE[ $cookie_name ] = $vid; // Make it available during the current request too.
        return $vid;
    }

    // Time.

    public static function human_time_diff_short( $datetime ): string {
        $diff = time() - strtotime( $datetime );
        /* translators: %d: number of minutes. */
        if ( $diff < 60 )         return sprintf( _n( '%d minute ago', '%d minutes ago', 1, 'bk-signals-for-woocommerce' ), 1 );
        /* translators: %d: number of minutes. */
        if ( $diff < 3600 )       return sprintf( _n( '%d minute ago', '%d minutes ago', floor( $diff / 60 ), 'bk-signals-for-woocommerce' ), floor( $diff / 60 ) );
        /* translators: %d: number of hours. */
        if ( $diff < 86400 )      return sprintf( _n( '%d hour ago', '%d hours ago', floor( $diff / 3600 ), 'bk-signals-for-woocommerce' ), floor( $diff / 3600 ) );
        /* translators: %d: number of days. */
        return sprintf( _n( '%d day ago', '%d days ago', floor( $diff / 86400 ), 'bk-signals-for-woocommerce' ), floor( $diff / 86400 ) );
    }

    // Numbers.

    public static function format_number( int $n ): string {
        if ( $n >= 1000000 ) return round( $n / 1000000, 1 ) . 'M';
        if ( $n >= 1000 )    return round( $n / 1000, 1 ) . 'K';
        return (string) $n;
    }

    // Admin URLs.

    public static function admin_notice_url( string $page, string $notice ): string {
        $notice = sanitize_key( $notice );

        return add_query_arg(
            [
                'page'             => sanitize_key( $page ),
                'bksignals_notice' => $notice,
                '_wpnonce'         => wp_create_nonce( 'bksignals_notice_' . $notice ),
            ],
            admin_url( 'admin.php' )
        );
    }

    public static function is_admin_notice( string $notice ): bool {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return false;
        }

        $notice = sanitize_key( $notice );
        $posted_notice = isset( $_GET['bksignals_notice'] ) ? sanitize_key( wp_unslash( $_GET['bksignals_notice'] ) ) : '';
        $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

        return $notice === $posted_notice && wp_verify_nonce( $nonce, 'bksignals_notice_' . $notice );
    }

    public static function admin_edit_url( string $page, int $index ): string {
        $page = sanitize_key( $page );

        return add_query_arg(
            [
                'page'     => $page,
                'edit'     => $index,
                '_wpnonce' => wp_create_nonce( 'bksignals_edit_' . $page . '_' . $index ),
            ],
            admin_url( 'admin.php' )
        );
    }

    public static function get_admin_edit_index( array $widgets, string $page ) {
        if ( ! current_user_can( 'manage_woocommerce' ) || ! isset( $_GET['edit'] ) ) {
            return null;
        }

        $page = sanitize_key( $page );
        $index = absint( wp_unslash( $_GET['edit'] ) );
        $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'bksignals_edit_' . $page . '_' . $index ) ) {
            return null;
        }

        return isset( $widgets[ $index ] ) ? $index : null;
    }
}
