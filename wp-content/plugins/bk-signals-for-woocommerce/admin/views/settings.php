<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<div class="wrap st-admin">
    <h1><?php esc_html_e( 'BK Signals Settings', 'bk-signals-for-woocommerce' ); ?></h1>

    <?php if ( BKSignals_Helpers::is_admin_notice( 'saved' ) ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'bk-signals-for-woocommerce' ); ?></p></div>
    <?php endif; ?>
    <?php if ( BKSignals_Helpers::is_admin_notice( 'cron_ran' ) ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Cron ran successfully. Data has been updated.', 'bk-signals-for-woocommerce' ); ?></p></div>
    <?php endif; ?>
    <?php if ( BKSignals_Helpers::is_admin_notice( 'reset' ) ) : ?>
        <div class="notice notice-warning is-dismissible"><p><?php esc_html_e( 'All statistics have been reset.', 'bk-signals-for-woocommerce' ); ?></p></div>
    <?php endif; ?>
    <?php if ( BKSignals_Helpers::is_admin_notice( 'product_reset' ) ) : ?>
        <div class="notice notice-warning is-dismissible"><p><?php esc_html_e( 'Product statistics have been reset.', 'bk-signals-for-woocommerce' ); ?></p></div>
    <?php endif; ?>

    <div class="st-card">
        <h2><?php esc_html_e( 'General Settings', 'bk-signals-for-woocommerce' ); ?></h2>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'bksignals_settings_save' ); ?>
            <input type="hidden" name="action" value="bksignals_save_settings">
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Cron Interval', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <select name="cron_interval" id="st-cron-interval">
                            <option value="bksignals_15min" <?php selected( $settings['cron_interval'], 'bksignals_15min' ); ?>><?php esc_html_e( 'Every 15 minutes (Recommended)', 'bk-signals-for-woocommerce' ); ?></option>
                            <option value="bksignals_30min" <?php selected( $settings['cron_interval'], 'bksignals_30min' ); ?>><?php esc_html_e( 'Every 30 minutes', 'bk-signals-for-woocommerce' ); ?></option>
                            <option value="hourly" <?php selected( $settings['cron_interval'], 'hourly' ); ?>><?php esc_html_e( 'Hourly', 'bk-signals-for-woocommerce' ); ?></option>
                            <option value="daily" <?php selected( $settings['cron_interval'], 'daily' ); ?>><?php esc_html_e( 'Daily', 'bk-signals-for-woocommerce' ); ?></option>
                            <option value="bksignals_custom" <?php selected( $settings['cron_interval'], 'bksignals_custom' ); ?>><?php esc_html_e( 'Custom interval', 'bk-signals-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Controls how often queued view data is processed.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr id="st-custom-cron-row" class="<?php echo esc_attr( 'bksignals_custom' === $settings['cron_interval'] ? '' : 'st-hidden' ); ?>">
                    <th><?php esc_html_e( 'Custom Cron Interval', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="custom_cron_value" value="<?php echo esc_attr( $settings['custom_cron_value'] ); ?>" min="1" max="1440" class="small-text">
                        <select name="custom_cron_unit">
                            <option value="minutes" <?php selected( $settings['custom_cron_unit'], 'minutes' ); ?>><?php esc_html_e( 'Minutes', 'bk-signals-for-woocommerce' ); ?></option>
                            <option value="hours" <?php selected( $settings['custom_cron_unit'], 'hours' ); ?>><?php esc_html_e( 'Hours', 'bk-signals-for-woocommerce' ); ?></option>
                            <option value="days" <?php selected( $settings['custom_cron_unit'], 'days' ); ?>><?php esc_html_e( 'Days', 'bk-signals-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Used only when Cron Interval is set to Custom interval. Minutes are limited to 5-1440, hours to 1-168, and days to 1-30.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Data Retention (days)', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="data_retention_days" value="<?php echo esc_attr( $settings['data_retention_days'] ); ?>" min="7" max="365" class="small-text">
                        <p class="description"><?php esc_html_e( 'Daily statistics older than this value will be removed.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Live Visitors Refresh Interval (seconds)', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="live_refresh_seconds" value="<?php echo esc_attr( $settings['live_refresh_seconds'] ); ?>" min="5" max="300" class="small-text">
                        <p class="description"><?php esc_html_e( 'How often product pages send a live visitor heartbeat.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Live Visitors Timeout (seconds)', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="live_timeout_seconds" value="<?php echo esc_attr( $settings['live_timeout_seconds'] ); ?>" min="30" max="3600" class="small-text">
                        <p class="description"><?php esc_html_e( 'Visitors are removed from the live count if no signal is received during this period.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Track Admin Views', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <label><input type="checkbox" name="track_admins" value="1" <?php checked( $settings['track_admins'], '1' ); ?>> <?php esc_html_e( 'Count product page visits from admin users. Useful for testing.', 'bk-signals-for-woocommerce' ); ?></label>
                        <p class="description"><?php esc_html_e( 'Recommended off on production stores.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Delete Data on Uninstall', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <label><input type="checkbox" name="delete_data_on_uninstall" value="1" <?php checked( $settings['delete_data_on_uninstall'], '1' ); ?>> <?php esc_html_e( 'Delete all BK Signals tables and options when the plugin is uninstalled.', 'bk-signals-for-woocommerce' ); ?></label>
                        <p class="description"><?php esc_html_e( 'Keep this disabled if you plan to reinstall BK Signals or move to another BK Signals version.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button( __( 'Save Settings', 'bk-signals-for-woocommerce' ) ); ?>
        </form>
    </div>

    <div class="st-card">
        <h2><?php esc_html_e( 'Data Processing', 'bk-signals-for-woocommerce' ); ?></h2>
        <p><?php esc_html_e( 'Process queued view data without waiting for cron.', 'bk-signals-for-woocommerce' ); ?></p>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'bksignals_run_cron_now' ); ?>
            <input type="hidden" name="action" value="bksignals_run_cron_now">
            <?php submit_button( __( 'Run Now (Flush + Recalculate)', 'bk-signals-for-woocommerce' ), 'secondary' ); ?>
        </form>
        <p>
            <?php
            /* translators: %d: number of queued product views. */
            printf( esc_html__( 'Queued views: %d', 'bk-signals-for-woocommerce' ), esc_html( $queue_count ) );
            ?>
        </p>
    </div>

    <div class="st-card">
        <h2><?php esc_html_e( 'Reset Product Statistics', 'bk-signals-for-woocommerce' ); ?></h2>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'bksignals_reset_product' ); ?>
            <input type="hidden" name="action" value="bksignals_reset_product">
            <table class="form-table">
                <tr><th><?php esc_html_e( 'Product ID', 'bk-signals-for-woocommerce' ); ?></th><td><input type="number" name="product_id" class="small-text" placeholder="123" min="1"></td></tr>
            </table>
            <?php submit_button( __( 'Reset This Product', 'bk-signals-for-woocommerce' ), 'secondary' ); ?>
        </form>
    </div>

    <div class="st-card st-danger-card">
        <h2 class="st-danger-title"><?php esc_html_e( 'Danger Zone', 'bk-signals-for-woocommerce' ); ?></h2>
        <p><?php esc_html_e( 'Permanently deletes all statistics. This action cannot be undone.', 'bk-signals-for-woocommerce' ); ?></p>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php esc_attr_e( 'All statistics will be deleted. Are you sure?', 'bk-signals-for-woocommerce' ); ?>')">
            <?php wp_nonce_field( 'bksignals_reset_all' ); ?>
            <input type="hidden" name="action" value="bksignals_reset_all">
            <?php submit_button( __( 'Reset All Statistics', 'bk-signals-for-woocommerce' ), 'delete' ); ?>
        </form>
    </div>
</div>
