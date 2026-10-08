<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<div class="wrap st-admin">
    <h1><?php esc_html_e( 'Recent Sales Popup', 'bk-signals-for-woocommerce' ); ?></h1>

    <?php if ( BKSignals_Helpers::is_admin_notice( 'saved' ) ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'bk-signals-for-woocommerce' ); ?></p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'bksignals_popup_save' ); ?>
        <input type="hidden" name="action" value="bksignals_save_popup">

        <div class="st-card">
            <h2><?php esc_html_e( 'Behavior', 'bk-signals-for-woocommerce' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Status', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" value="1" <?php checked( $settings['enabled'], '1' ); ?>>
                            <?php esc_html_e( 'Enable recent sales popup', 'bk-signals-for-woocommerce' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Position', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <select name="position">
                            <?php foreach ( BKSignals_Admin_Popup::position_options() as $position_key => $position_label ) : ?>
                                <option value="<?php echo esc_attr( $position_key ); ?>" <?php selected( $settings['position'], $position_key ); ?>>
                                    <?php echo esc_html( $position_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Recent Sales Pool', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="limit" value="<?php echo esc_attr( $settings['limit'] ); ?>" min="1" max="50" class="small-text">
                        <p class="description"><?php esc_html_e( 'How many of the latest sales should rotate in sequence. Recommended: 10.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Product Page Scope', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="product_scope" value="1" <?php checked( $settings['product_scope'], '1' ); ?>>
                            <?php esc_html_e( 'On product pages, show only sales for the current product.', 'bk-signals-for-woocommerce' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Mobile Popup', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="mobile_enabled" value="1" <?php checked( $settings['mobile_enabled'], '1' ); ?>>
                            <?php esc_html_e( 'Show notifications on mobile devices', 'bk-signals-for-woocommerce' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Mobile Position', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <select name="mobile_position">
                            <?php foreach ( BKSignals_Admin_Popup::mobile_position_options() as $mobile_position_key => $mobile_position_label ) : ?>
                                <option value="<?php echo esc_attr( $mobile_position_key ); ?>" <?php selected( $settings['mobile_position'], $mobile_position_key ); ?>>
                                    <?php echo esc_html( $mobile_position_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Overrides the desktop position on mobile only.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Mobile Bottom Offset', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="mobile_offset" value="<?php echo esc_attr( $settings['mobile_offset'] ); ?>" min="0" max="240" class="small-text">
                        <?php esc_html_e( 'px', 'bk-signals-for-woocommerce' ); ?>
                        <p class="description"><?php esc_html_e( 'Raises bottom-positioned popups above mobile sticky menus. Recommended: 72.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Initial Delay', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="delay" value="<?php echo esc_attr( $settings['delay'] ); ?>" min="0" max="300" class="small-text">
                        <?php esc_html_e( 'seconds', 'bk-signals-for-woocommerce' ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Display Interval', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="interval" value="<?php echo esc_attr( $settings['interval'] ); ?>" min="5" max="300" class="small-text">
                        <?php esc_html_e( 'seconds between popups', 'bk-signals-for-woocommerce' ); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Display Duration', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="number" name="duration" value="<?php echo esc_attr( $settings['duration'] ); ?>" min="2" max="60" class="small-text">
                        <?php esc_html_e( 'seconds visible', 'bk-signals-for-woocommerce' ); ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="st-card">
            <h2><?php esc_html_e( 'Design', 'bk-signals-for-woocommerce' ); ?></h2>
            <?php $designs = BKSignals_Admin_Popup::design_options(); ?>
            <div class="st-design-grid st-popup-design-grid">
                <?php foreach ( $designs as $design_key => $design_label ) : ?>
                    <label class="st-design-option">
                        <input type="radio" name="design" value="<?php echo esc_attr( $design_key ); ?>" <?php checked( $settings['design'], $design_key ); ?>>
                        <span class="st-design-card">
                            <span class="st-design-title"><?php echo esc_html( $design_label ); ?></span>
                            <span class="st-design-preview-shell st-popup-preview-shell">
                                <span class="st-popup st-popup--<?php echo esc_attr( $design_key ); ?> st-popup--preview">
                                    <span class="st-popup-product-image st-popup-product-image--preview"></span>
                                    <span class="st-popup-body">
                                        <strong><?php esc_html_e( 'k*** c****', 'bk-signals-for-woocommerce' ); ?></strong>
                                        <span class="st-popup-product-line"><?php esc_html_e( 'Featured Wireless Charging Stand with Fast Magnetic Alignment****', 'bk-signals-for-woocommerce' ); ?></span>
                                        <em class="st-popup-meta"><?php esc_html_e( '5 minutes ago - Purchased', 'bk-signals-for-woocommerce' ); ?></em>
                                    </span>
                                    <span class="st-popup-progress" aria-hidden="true">
                                        <span class="st-popup-progress-line st-popup-progress-top"></span>
                                        <span class="st-popup-progress-line st-popup-progress-right"></span>
                                        <span class="st-popup-progress-line st-popup-progress-bottom"></span>
                                        <span class="st-popup-progress-line st-popup-progress-left"></span>
                                    </span>
                                </span>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <?php submit_button( __( 'Save Settings', 'bk-signals-for-woocommerce' ) ); ?>
    </form>
</div>
