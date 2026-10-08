<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<div class="wrap st-admin">
    <h1><?php esc_html_e( 'Live Visitors Widget', 'bk-signals-for-woocommerce' ); ?></h1>

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
            <?php wp_nonce_field( 'bksignals_live_visitors_save' ); ?>
            <input type="hidden" name="action" value="bksignals_save_live_visitors">
            <input type="hidden" name="bksignals_action" value="create">
            <?php if ( null !== $edit_widget ) : ?>
                <input type="hidden" name="widget_index" value="<?php echo (int) $edit_index; ?>">
            <?php endif; ?>

            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Widget Name', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="widget_name" class="regular-text" value="<?php echo esc_attr( $edit_widget['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Example: Product Page Live', 'bk-signals-for-woocommerce' ); ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Shortcode Key', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="shortcode_key" class="regular-text" value="<?php echo esc_attr( $edit_widget['shortcode_key'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to generate automatically', 'bk-signals-for-woocommerce' ); ?>">
                        <p class="description"><?php esc_html_e( 'Example: product-live or campaign-live. This only appears inside the shortcode.', 'bk-signals-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Design', 'bk-signals-for-woocommerce' ); ?></th>
                    <td>
                        <?php
                        $selected_design = $edit_widget['design'] ?? 'minimal';
                        $designs = BKSignals_Admin_Live_Visitors::design_options();
                        ?>
                        <div class="st-design-grid">
                            <?php foreach ( $designs as $design_key => $design_label ) : ?>
                                <label class="st-design-option">
                                    <input type="radio" name="design" value="<?php echo esc_attr( $design_key ); ?>" <?php checked( $selected_design, $design_key ); ?>>
                                    <span class="st-design-card">
                                        <span class="st-design-title"><?php echo esc_html( $design_label ); ?></span>
                                        <span class="st-design-preview-shell st-live-preview-shell">
                                            <?php if ( 'modern' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--modern">
                                                    <span class="st-live-icon"><span class="st-live-dot"></span></span>
                                                    <span class="st-content"><strong class="st-live-count">5</strong><span><?php esc_html_e( 'people viewing now', 'bk-signals-for-woocommerce' ); ?></span></span>
                                                </span>
                                            <?php elseif ( 'card' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-card-widget st-live-visitors st-live-visitors--card">
                                                    <span class="st-card-icon"><span class="st-live-dot"></span></span>
                                                    <span class="st-card-body"><span class="st-card-value st-live-count">5</span><span class="st-card-label"><?php esc_html_e( 'Viewing Now', 'bk-signals-for-woocommerce' ); ?></span></span>
                                                </span>
                                            <?php elseif ( 'badge' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-badge st-live-visitors st-live-visitors--badge">
                                                    <span class="st-live-dot"></span><span class="st-live-count">5</span><?php esc_html_e( 'active', 'bk-signals-for-woocommerce' ); ?>
                                                </span>
                                            <?php elseif ( 'pulse' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--pulse">
                                                    <span class="st-live-dot"></span><strong class="st-live-count">5</strong><span><?php esc_html_e( 'live visitors', 'bk-signals-for-woocommerce' ); ?></span>
                                                </span>
                                            <?php elseif ( 'glass' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--glass">
                                                    <strong class="st-live-count">5</strong><span><?php esc_html_e( 'people are here now', 'bk-signals-for-woocommerce' ); ?></span>
                                                </span>
                                            <?php elseif ( 'compact' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--compact">
                                                    <span class="st-live-dot"></span><strong class="st-live-count">5</strong><span><?php esc_html_e( 'viewing', 'bk-signals-for-woocommerce' ); ?></span>
                                                </span>
                                            <?php elseif ( 'ribbon' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--ribbon">
                                                    <span class="st-ribbon-label"><?php esc_html_e( 'LIVE', 'bk-signals-for-woocommerce' ); ?></span><strong class="st-live-count">5</strong>
                                                </span>
                                            <?php elseif ( 'inline' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--inline">
                                                    <?php esc_html_e( 'Currently', 'bk-signals-for-woocommerce' ); ?><strong class="st-live-count">5</strong><?php esc_html_e( 'people viewing', 'bk-signals-for-woocommerce' ); ?>
                                                </span>
                                            <?php elseif ( 'spotlight' === $design_key ) : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--spotlight">
                                                    <strong class="st-live-count">5</strong><span><?php esc_html_e( 'active viewers', 'bk-signals-for-woocommerce' ); ?></span>
                                                </span>
                                            <?php else : ?>
                                                <span class="st-design-preview st-widget st-live-visitors st-live-visitors--minimal">
                                                    <span class="st-live-dot"></span><span class="st-live-count">5</span><?php esc_html_e( 'people are viewing this product', 'bk-signals-for-woocommerce' ); ?>
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
                <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals-live-visitors' ) ); ?>"><?php esc_html_e( 'Cancel', 'bk-signals-for-woocommerce' ); ?></a>
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
                    <th><?php esc_html_e( 'Status', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Shortcode', 'bk-signals-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'bk-signals-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $widgets as $idx => $widget ) : ?>
                <?php
                $shortcode_key = $widget['shortcode_key'] ?? $widget['id'] ?? '';
                $shortcode = '[bk_signals_live_visitors widget="' . $shortcode_key . '"]';
                ?>
                <tr>
                    <td><?php echo esc_html( $widget['name'] ?? '' ); ?></td>
                    <td><?php echo esc_html( $designs[ $widget['design'] ?? 'minimal' ] ?? ( $widget['design'] ?? 'minimal' ) ); ?></td>
                    <td><?php echo esc_html( ! empty( $widget['enabled'] ) ? __( 'Active', 'bk-signals-for-woocommerce' ) : __( 'Inactive', 'bk-signals-for-woocommerce' ) ); ?></td>
                    <td>
                        <code><?php echo esc_html( $shortcode ); ?></code>
                        <button type="button" class="button button-small st-copy-shortcode"><?php esc_html_e( 'Copy', 'bk-signals-for-woocommerce' ); ?></button>
                    </td>
                    <td>
                        <a class="button button-small" href="<?php echo esc_url( BKSignals_Helpers::admin_edit_url( 'bk-signals-live-visitors', (int) $idx ) ); ?>"><?php esc_html_e( 'Edit', 'bk-signals-for-woocommerce' ); ?></a>
                        <form class="st-inline-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'bksignals_live_visitors_save' ); ?>
                            <input type="hidden" name="action" value="bksignals_save_live_visitors">
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
