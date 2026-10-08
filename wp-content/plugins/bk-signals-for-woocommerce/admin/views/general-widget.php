<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<div class="wrap st-admin">
    <h1><?php esc_html_e( 'General Widget', 'bk-signals-for-woocommerce' ); ?></h1>

    <?php if ( BKSignals_Helpers::is_admin_notice( 'saved' ) ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Widget saved.', 'bk-signals-for-woocommerce' ); ?></p></div>
    <?php endif; ?>

    <div class="st-card">
        <h2>
            <?php
            echo $edit_widget
                ? esc_html__( 'Edit Widget', 'bk-signals-for-woocommerce' )
                : esc_html__( 'Create New Widget', 'bk-signals-for-woocommerce' );
            ?>
        </h2>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'bksignals_general_widget_save' ); ?>
            <input type="hidden" name="action" value="bksignals_save_general_widget">
            <input type="hidden" name="bksignals_action" value="create">
            <?php if ( null !== $edit_widget ) : ?>
                <input type="hidden" name="widget_index" value="<?php echo (int) $edit_index; ?>">
            <?php endif; ?>

            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Widget Name', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="widget_name" class="regular-text" value="<?php echo esc_attr( $edit_widget['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Example: Product Trust Summary', 'bk-signals-for-woocommerce' ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Shortcode Key', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="shortcode_key" class="regular-text" value="<?php echo esc_attr( $edit_widget['shortcode_key'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to generate automatically', 'bk-signals-for-woocommerce' ); ?>">
                        <p class="description"><?php esc_html_e( 'Example: product-summary or trust-summary. This only appears inside the shortcode.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Time Window', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <?php
                        $selected_window = (int) ( $edit_widget['window'] ?? 7 );
                        $windows = BKSignals_Admin_General_Widget::window_options();
                        ?>
                        <select name="window">
                            <?php foreach ( $windows as $window_value => $window_label ) : ?>
                                <option value="<?php echo (int) $window_value; ?>" <?php selected( $selected_window, (int) $window_value ); ?>>
                                    <?php echo esc_html( $window_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Show Metrics', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <?php
                        $show_views = $edit_widget['show_views'] ?? true;
                        $show_cart  = $edit_widget['show_cart'] ?? true;
                        $show_sales = $edit_widget['show_sales'] ?? true;
                        $show_live  = $edit_widget['show_live'] ?? true;
                        ?>
                        <label><input type="checkbox" name="show_views" value="1" <?php checked( $show_views ); ?>> <?php esc_html_e( 'Views', 'bk-signals-for-woocommerce' ); ?></label><br>
                        <label><input type="checkbox" name="show_cart" value="1" <?php checked( $show_cart ); ?>> <?php esc_html_e( 'In carts', 'bk-signals-for-woocommerce' ); ?></label><br>
                        <label><input type="checkbox" name="show_sales" value="1" <?php checked( $show_sales ); ?>> <?php esc_html_e( 'Sales', 'bk-signals-for-woocommerce' ); ?></label><br>
                        <label><input type="checkbox" name="show_live" value="1" <?php checked( $show_live ); ?>> <?php esc_html_e( 'Live visitors', 'bk-signals-for-woocommerce' ); ?></label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Design', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <?php
                        $selected_design = $edit_widget['design'] ?? 'trend-alert';
                        $designs = BKSignals_Admin_General_Widget::design_options();
                        $ticker_designs = [ 'trend-alert', 'market-signal', 'orange-pulse', 'trust-line', 'live-feed' ];
                        ?>
                        <div class="st-design-grid st-general-design-grid">
                            <?php foreach ( $designs as $design_key => $design_label ) : ?>
                                <label class="st-design-option">
                                    <input type="radio" name="design" value="<?php echo esc_attr( $design_key ); ?>" <?php checked( $selected_design, $design_key ); ?>>
                                    <span class="st-design-card">
                                        <span class="st-design-title"><?php echo esc_html( $design_label ); ?></span>
                                        <span class="st-design-preview-shell st-general-preview-shell">
                                            <?php if ( in_array( $design_key, $ticker_designs, true ) ) : ?>
                                                <span class="st-design-preview st-general-preview st-general-ticker st-general-ticker--<?php echo esc_attr( $design_key ); ?> st-general-ticker--count-4">
                                                    <span class="st-general-ticker-track">
                                                        <span class="st-general-ticker-message"><span class="st-general-message-icon st-general-message-icon--views" aria-hidden="true"></span><?php esc_html_e( 'Popular product! 1.2K people viewed this recently.', 'bk-signals-for-woocommerce' ); ?></span>
                                                        <span class="st-general-ticker-message"><span class="st-general-message-icon st-general-message-icon--cart" aria-hidden="true"></span><?php esc_html_e( 'Demand is rising: 34 people have this in cart.', 'bk-signals-for-woocommerce' ); ?></span>
                                                        <span class="st-general-ticker-message"><span class="st-general-message-icon st-general-message-icon--sales" aria-hidden="true"></span><?php esc_html_e( 'Trusted by shoppers: 12 purchases recently.', 'bk-signals-for-woocommerce' ); ?></span>
                                                        <span class="st-general-ticker-message"><span class="st-general-message-icon st-general-message-icon--live" aria-hidden="true"></span><?php esc_html_e( '5 people are viewing this product right now.', 'bk-signals-for-woocommerce' ); ?></span>
                                                    </span>
                                                </span>
                                            <?php else : ?>
                                                <span class="st-design-preview st-general-preview st-general-static st-general-static--<?php echo esc_attr( $design_key ); ?>">
                                                    <span class="st-general-static-window"><?php esc_html_e( 'The Last 7 Days', 'bk-signals-for-woocommerce' ); ?></span>
                                                    <span class="st-general-static-main"><strong>1.2K</strong> <?php esc_html_e( 'recent views', 'bk-signals-for-woocommerce' ); ?></span>
                                                    <span class="st-general-static-pills">
                                                        <span>34 <?php esc_html_e( 'carts', 'bk-signals-for-woocommerce' ); ?></span>
                                                        <span>12 <?php esc_html_e( 'sales', 'bk-signals-for-woocommerce' ); ?></span>
                                                        <span>5 <?php esc_html_e( 'live', 'bk-signals-for-woocommerce' ); ?></span>
                                                    </span>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Status', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" value="1" <?php checked( $edit_widget['enabled'] ?? true ); ?>>
                            <?php esc_html_e( 'Enable this widget', 'bk-signals-for-woocommerce' ); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <?php submit_button( $edit_widget ? __( 'Save Changes', 'bk-signals-for-woocommerce' ) : __( 'Create Widget', 'bk-signals-for-woocommerce' ), 'primary', 'submit', false ); ?>
            <?php if ( $edit_widget ) : ?>
                <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals-general-widget' ) ); ?>"><?php esc_html_e( 'Cancel', 'bk-signals-for-woocommerce' ); ?></a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ( ! empty( $widgets ) ) : ?>
    <div class="st-card">
        <h2><?php esc_html_e( 'Existing Widgets', 'bk-signals-for-woocommerce' ); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Name', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Design', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Window', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Shows', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Shortcode', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'bk-signals-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $widgets as $idx => $widget ) : ?>
                <?php
                $shortcode_key = $widget['shortcode_key'] ?? $widget['id'] ?? '';
                $shortcode = '[bk_signals_general widget="' . $shortcode_key . '"]';
                $window = (int) ( $widget['window'] ?? 7 );
                $shows = [];
                if ( ! empty( $widget['show_views'] ) ) { $shows[] = __( 'Views', 'bk-signals-for-woocommerce' ); }
                if ( ! empty( $widget['show_cart'] ) ) { $shows[] = __( 'Carts', 'bk-signals-for-woocommerce' ); }
                if ( ! empty( $widget['show_sales'] ) ) { $shows[] = __( 'Sales', 'bk-signals-for-woocommerce' ); }
                if ( ! empty( $widget['show_live'] ) ) { $shows[] = __( 'Live', 'bk-signals-for-woocommerce' ); }
                ?>
                <tr>
                    <td><?php echo esc_html( $widget['name'] ?? '' ); ?></td>
                    <td><?php echo esc_html( $designs[ $widget['design'] ?? 'trend-alert' ] ?? ( $widget['design'] ?? 'trend-alert' ) ); ?></td>
                    <td>
                        <?php
                        if ( isset( $windows[ $window ] ) ) {
                            echo esc_html( $windows[ $window ] );
                        } else {
                            /* translators: %d: selected time window in days. */
                            echo esc_html( sprintf( __( 'Last %d Days', 'bk-signals-for-woocommerce' ), $window ) );
                        }
                        ?>
                    </td>
                    <td><?php echo esc_html( implode( ', ', $shows ) ); ?></td>
                    <td><?php echo esc_html( ! empty( $widget['enabled'] ) ? __( 'Active', 'bk-signals-for-woocommerce' ) : __( 'Inactive', 'bk-signals-for-woocommerce' ) ); ?></td>
                    <td>
                        <code><?php echo esc_html( $shortcode ); ?></code>
                        <button type="button" class="button button-small st-copy-shortcode"><?php esc_html_e( 'Copy', 'bk-signals-for-woocommerce' ); ?></button>
                    </td>
                    <td>
                        <a class="button button-small" href="<?php echo esc_url( BKSignals_Helpers::admin_edit_url( 'bk-signals-general-widget', (int) $idx ) ); ?>"><?php esc_html_e( 'Edit', 'bk-signals-for-woocommerce' ); ?></a>
                        <form class="st-inline-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'bksignals_general_widget_save' ); ?>
                            <input type="hidden" name="action" value="bksignals_save_general_widget">
                            <input type="hidden" name="bksignals_action" value="delete">
                            <input type="hidden" name="widget_index" value="<?php echo (int) $idx; ?>">
                            <button type="submit" class="button button-small button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this widget?', 'bk-signals-for-woocommerce' ); ?>')"><?php esc_html_e( 'Delete', 'bk-signals-for-woocommerce' ); ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
