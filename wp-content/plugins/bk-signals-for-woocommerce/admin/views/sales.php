<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<div class="wrap st-admin">
    <h1><?php esc_html_e( 'Sales Widget', 'bk-signals-for-woocommerce' ); ?></h1>

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
            <?php wp_nonce_field( 'bksignals_sales_save' ); ?>
            <input type="hidden" name="action" value="bksignals_save_sales">
            <input type="hidden" name="bksignals_action" value="create">
            <?php if ( null !== $edit_widget ) : ?>
                <input type="hidden" name="widget_index" value="<?php echo (int) $edit_index; ?>">
            <?php endif; ?>

            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Widget Name', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="widget_name" class="regular-text" value="<?php echo esc_attr( $edit_widget['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Example: Product Page Sales', 'bk-signals-for-woocommerce' ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Shortcode Key', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="shortcode_key" class="regular-text" value="<?php echo esc_attr( $edit_widget['shortcode_key'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to generate automatically', 'bk-signals-for-woocommerce' ); ?>">
                        <p class="description"><?php esc_html_e( 'Example: product-sales or campaign-sales. This only appears inside the shortcode.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Time Window', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <?php
                        $selected_window = (int) ( $edit_widget['window'] ?? 7 );
                        $windows = BKSignals_Admin_Sales::window_options();
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
                    <th><?php esc_html_e( 'Design', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <?php
                        $selected_design = $edit_widget['design'] ?? 'minimal';
                        $designs = BKSignals_Admin_Sales::design_options();
                        $icon = '<svg class="st-sales-icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0L4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7 12 12l8.7-5"/><path d="M12 22V12"/></svg>';
                        ?>
                        <div class="st-design-grid">
                            <?php foreach ( $designs as $design_key => $design_label ) : ?>
                                <label class="st-design-option">
                                    <input type="radio" name="design" value="<?php echo esc_attr( $design_key ); ?>" <?php checked( $selected_design, $design_key ); ?>>
                                    <span class="st-design-card">
                                        <span class="st-design-title"><?php echo esc_html( $design_label ); ?></span>
                                        <span class="st-design-preview-shell st-sales-preview-shell">
                                            <?php if ( 'modern' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--modern">
                                                    <span class="st-sales-icon-box"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                                                    <span class="st-content"><strong>12</strong><span><?php esc_html_e( 'sales in 7 days', 'bk-signals-for-woocommerce' ); ?></span></span>
                                                </span>
                                            <?php elseif ( 'card' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-card-widget st-sales st-sales--card">
                                                    <span class="st-card-icon"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                                                    <span class="st-card-body"><span class="st-card-value">12</span><span class="st-card-label"><?php esc_html_e( 'Sales - 7 days', 'bk-signals-for-woocommerce' ); ?></span></span>
                                                </span>
                                            <?php elseif ( 'badge' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-badge st-sales st-sales--badge"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?><strong>12</strong><?php esc_html_e( 'sales', 'bk-signals-for-woocommerce' ); ?></span>
                                            <?php elseif ( 'metric-card' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--metric-card">
                                                    <span class="st-sales-metric-header"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?><?php esc_html_e( 'PRODUCT SALES', 'bk-signals-for-woocommerce' ); ?></span>
                                                    <strong>12</strong><span><?php esc_html_e( 'last 7 days', 'bk-signals-for-woocommerce' ); ?></span>
                                                </span>
                                            <?php elseif ( 'split' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--split">
                                                    <span class="st-sales-split-count">12</span>
                                                    <span class="st-sales-split-info"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?><?php esc_html_e( 'Sales', 'bk-signals-for-woocommerce' ); ?><small><?php esc_html_e( '7 days', 'bk-signals-for-woocommerce' ); ?></small></span>
                                                </span>
                                            <?php elseif ( 'pill' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--pill"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?><strong>12</strong><?php esc_html_e( 'sales', 'bk-signals-for-woocommerce' ); ?></span>
                                            <?php elseif ( 'trend' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--trend">
                                                    <span class="st-sales-trend-icon"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                                                    <span class="st-sales-trend-copy"><strong>12</strong><small><?php esc_html_e( 'sales in 7 days', 'bk-signals-for-woocommerce' ); ?></small></span>
                                                </span>
                                            <?php elseif ( 'spotlight' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--spotlight">
                                                    <span class="st-sales-spotlight-label"><?php esc_html_e( 'SALES', 'bk-signals-for-woocommerce' ); ?></span><strong>12</strong><span><?php esc_html_e( '7 days', 'bk-signals-for-woocommerce' ); ?></span>
                                                </span>
                                            <?php elseif ( 'clean' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--clean"><strong>12</strong><span><?php esc_html_e( 'sales in 7 days', 'bk-signals-for-woocommerce' ); ?></span></span>
                                            <?php else : ?>
                                                <span class="st-design-preview st-widget st-sales st-sales--minimal"><?php echo wp_kses( $icon, BKSignals_Dashboard::svg_allowed_html() ); ?><strong>12</strong><?php esc_html_e( 'sales in 7 days', 'bk-signals-for-woocommerce' ); ?></span>
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
                <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals-sales' ) ); ?>"><?php esc_html_e( 'Cancel', 'bk-signals-for-woocommerce' ); ?></a>
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
                    <th><?php esc_html_e( 'Status', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Shortcode', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'bk-signals-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $widgets as $idx => $widget ) : ?>
                <?php
                $shortcode_key = $widget['shortcode_key'] ?? $widget['id'] ?? '';
                $shortcode = '[bk_signals_sales widget="' . $shortcode_key . '"]';
                $window = (int) ( $widget['window'] ?? 7 );
                ?>
                <tr>
                    <td><?php echo esc_html( $widget['name'] ?? '' ); ?></td>
                    <td><?php echo esc_html( $designs[ $widget['design'] ?? 'minimal' ] ?? ( $widget['design'] ?? 'minimal' ) ); ?></td>
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
                    <td><?php echo esc_html( ! empty( $widget['enabled'] ) ? __( 'Active', 'bk-signals-for-woocommerce' ) : __( 'Inactive', 'bk-signals-for-woocommerce' ) ); ?></td>
                    <td>
                        <code><?php echo esc_html( $shortcode ); ?></code>
                        <button type="button" class="button button-small st-copy-shortcode"><?php esc_html_e( 'Copy', 'bk-signals-for-woocommerce' ); ?></button>
                    </td>
                    <td>
                        <a class="button button-small" href="<?php echo esc_url( BKSignals_Helpers::admin_edit_url( 'bk-signals-sales', (int) $idx ) ); ?>"><?php esc_html_e( 'Edit', 'bk-signals-for-woocommerce' ); ?></a>
                        <form class="st-inline-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'bksignals_sales_save' ); ?>
                            <input type="hidden" name="action" value="bksignals_save_sales">
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
