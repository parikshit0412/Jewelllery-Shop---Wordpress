<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wpccl_Backend' ) ) {
    class Wpccl_Backend {
        protected static $instance = null;

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct() {
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
            add_action( 'admin_init', [ $this, 'register_settings' ] );
            add_filter( 'pre_update_option', [ $this, 'last_saved' ], 10, 2 );
            add_action( 'admin_menu', [ $this, 'admin_menu' ] );
            add_filter( 'woocommerce_coupon_data_tabs', [ $this, 'coupon_tab' ] );
            add_action( 'woocommerce_coupon_data_panels', [ $this, 'coupon_tab_panel' ] );
            add_action( 'woocommerce_coupon_options_save', [ $this, 'save_coupon_settings' ] );

            // links
            add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
            add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );

            // compatibility
            add_filter( 'woocommerce_coupon_generator_coupon_meta_data', [ $this, 'set_coupon_public' ], 99, 3 );
        }
        public function enqueue_scripts() {
            wp_enqueue_style( 'wpccl-backend', WPCCL_URI . 'assets/css/backend.css', [ 'woocommerce_admin_styles' ], WPCCL_VERSION );
            wp_enqueue_script( 'wpccl-backend', WPCCL_URI . 'assets/js/backend.js', [ 'jquery' ], WPCCL_VERSION, true );
        }

        function register_settings() {
            // settings
            register_setting( 'wpccl_settings', 'wpccl_settings', [
                    'type'              => 'array',
                    'sanitize_callback' => [ 'Wpccl_Helper', 'sanitize_array' ],
            ] );

            // localization
            register_setting( 'wpccl_localization', 'wpccl_localization', [
                    'type'              => 'array',
                    'sanitize_callback' => [ 'Wpccl_Helper', 'sanitize_array' ],
            ] );
        }

        function last_saved( $value, $option ) {
            if ( $option == 'wpccl_settings' || $option == 'wpccl_localization' ) {
                $value['_last_saved']    = current_time( 'timestamp' );
                $value['_last_saved_by'] = get_current_user_id();
            }

            return $value;
        }

        function admin_menu() {
            add_submenu_page( 'wpclever', esc_html__( 'WPC Coupon Listing', 'wpc-coupon-listing' ), esc_html__( 'Coupon Listing', 'wpc-coupon-listing' ), 'manage_options', 'wpclever-wpccl', [
                    $this,
                    'admin_menu_content'
            ] );
        }

        function admin_menu_content() {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $active_tab = sanitize_key( wp_unslash( $_GET['tab'] ?? 'settings' ) );
            ?>
            <div class="wpclever_settings_page wrap">
                <div class="wpclever_settings_page_header">
                    <a class="wpclever_settings_page_header_logo" href="https://wpclever.net/"
                       target="_blank" title="Visit wpclever.net"></a>
                    <div class="wpclever_settings_page_header_text">
                        <div class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Coupon Listing', 'wpc-coupon-listing' ) . ' ' . esc_html( WPCCL_VERSION ); ?></div>
                        <div class="wpclever_settings_page_desc about-text">
                            <p>
                                <?php printf( /* translators: stars */ esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'wpc-coupon-listing' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
                                <br/>
                                <a href="<?php echo esc_url( WPCCL_REVIEWS ); ?>"
                                   target="_blank"><?php esc_html_e( 'Reviews', 'wpc-coupon-listing' ); ?></a> |
                                <a href="<?php echo esc_url( WPCCL_CHANGELOG ); ?>"
                                   target="_blank"><?php esc_html_e( 'Changelog', 'wpc-coupon-listing' ); ?></a> |
                                <a href="<?php echo esc_url( WPCCL_DISCUSSION ); ?>"
                                   target="_blank"><?php esc_html_e( 'Discussion', 'wpc-coupon-listing' ); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
                <h2></h2>
                <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                if ( isset( $_GET['settings-updated'] ) && sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) ) { ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e( 'Settings updated.', 'wpc-coupon-listing' ); ?></p>
                    </div>
                <?php } ?>
                <div class="wpclever_settings_page_nav">
                    <h2 class="nav-tab-wrapper">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpccl&tab=settings' ) ); ?>"
                           class="<?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
                            <?php esc_html_e( 'Settings', 'wpc-coupon-listing' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpccl&tab=localization' ) ); ?>"
                           class="<?php echo esc_attr( $active_tab === 'localization' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
                            <?php esc_html_e( 'Localization', 'wpc-coupon-listing' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-kit' ) ); ?>" class="nav-tab">
                            <?php esc_html_e( 'Essential Kit', 'wpc-coupon-listing' ); ?>
                        </a>
                    </h2>
                </div>
                <div class="wpclever_settings_page_content">
                    <?php if ( $active_tab === 'settings' ) {
                        $listing   = Wpccl_Helper::get_setting( 'listing', 'publish' );
                        $orderby   = Wpccl_Helper::get_setting( 'orderby', 'date' );
                        $order     = Wpccl_Helper::get_setting( 'order', 'DESC' );
                        $value     = Wpccl_Helper::get_setting( 'value', 'show' );
                        $expiry    = Wpccl_Helper::get_setting( 'expiry', 'show' );
                        $countdown = Wpccl_Helper::get_setting( 'countdown', 'no' );
                        $desc      = Wpccl_Helper::get_setting( 'desc', 'show' );
                        $message   = Wpccl_Helper::get_setting( 'message', 'show' );
                        ?>
                        <form method="post" action="options.php">
                            <table class="form-table">
                                <tr class="heading">
                                    <th colspan="2">
                                        <?php esc_html_e( 'General', 'wpc-coupon-listing' ); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Listing', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[listing]">
                                                <option value="publish" <?php selected( $listing, 'publish' ); ?>><?php esc_html_e( 'All published coupons', 'wpc-coupon-listing' ); ?></option>
                                                <option value="public" <?php selected( $listing, 'public' ); ?>><?php esc_html_e( 'Public coupons only', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                        <span class="description"><?php esc_html_e( 'You can set a coupon is public when editing a coupon.', 'wpc-coupon-listing' ); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Order by', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[orderby]">
                                                <option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'wpc-coupon-listing' ); ?></option>
                                                <option value="name" <?php selected( $orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Order', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[order]">
                                                <option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'wpc-coupon-listing' ); ?></option>
                                                <option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Descending', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="heading">
                                    <th colspan="2">
                                        <?php esc_html_e( 'Displaying', 'wpc-coupon-listing' ); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Discount value', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[value]">
                                                <option value="show" <?php selected( $value, 'show' ); ?>><?php esc_html_e( 'Show', 'wpc-coupon-listing' ); ?></option>
                                                <option value="hide" <?php selected( $value, 'hide' ); ?>><?php esc_html_e( 'Hide', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Expiry date', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[expiry]">
                                                <option value="show" <?php selected( $expiry, 'show' ); ?>><?php esc_html_e( 'Show', 'wpc-coupon-listing' ); ?></option>
                                                <option value="hide" <?php selected( $expiry, 'hide' ); ?>><?php esc_html_e( 'Hide', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Countdown for expiry date', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[countdown]">
                                                <option value="yes" <?php selected( $countdown, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-coupon-listing' ); ?></option>
                                                <option value="no" <?php selected( $countdown, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Description', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[desc]">
                                                <option value="show" <?php selected( $desc, 'show' ); ?>><?php esc_html_e( 'Show', 'wpc-coupon-listing' ); ?></option>
                                                <option value="hide" <?php selected( $desc, 'hide' ); ?>><?php esc_html_e( 'Hide', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Message', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <select name="wpccl_settings[message]">
                                                <option value="show" <?php selected( $message, 'show' ); ?>><?php esc_html_e( 'Show', 'wpc-coupon-listing' ); ?></option>
                                                <option value="hide" <?php selected( $message, 'hide' ); ?>><?php esc_html_e( 'Hide', 'wpc-coupon-listing' ); ?></option>
                                            </select>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="submit">
                                    <th colspan="2">
                                        <div class="wpclever_submit">
                                            <?php
                                            settings_fields( 'wpccl_settings' );
                                            submit_button( '', 'primary', 'submit', false );

                                            if ( function_exists( 'wpc_last_saved' ) ) {
                                                wpc_last_saved( Wpccl_Helper::get_settings() );
                                            }
                                            ?>
                                        </div>
                                        <a style="display: none;" class="wpclever_export"
                                           data-key="wpccl_settings"
                                           data-name="settings"
                                           href="#"><?php esc_html_e( 'import / export', 'wpc-coupon-listing' ); ?></a>
                                    </th>
                                </tr>
                            </table>
                        </form>
                    <?php } elseif ( $active_tab === 'localization' ) { ?>
                        <form method="post" action="options.php">
                            <table class="form-table">
                                <tr class="heading">
                                    <th scope="row"><?php esc_html_e( 'Localization', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <?php esc_html_e( 'Leave blank to use the default text and its equivalent translation in multiple languages.', 'wpc-coupon-listing' ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Button text', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[button]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'button' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'View Available Coupons', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Heading', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[heading]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'heading' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Select an available coupon below', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Applied', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[applied]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'applied' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Applied', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Individual use only', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text"
                                                   name="wpccl_localization[individual]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'individual' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Individual use only', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Discount value', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[discount]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'discount' ) ); ?>"
                                                   placeholder="<?php /* translators: value */
                                                   esc_attr_e( '%s Discount', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Product discount value', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text"
                                                   name="wpccl_localization[product_discount]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'product_discount' ) ); ?>"
                                                   placeholder="<?php /* translators: value */
                                                   esc_attr_e( '%s Product Discount', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Free shipping', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text"
                                                   name="wpccl_localization[free_shipping]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'free_shipping' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Free Shipping', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Expires on', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[expires]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'expires' ) ); ?>"
                                                   placeholder="<?php /* translators: date */
                                                   esc_attr_e( 'Expires on: %s', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Never expire', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text"
                                                   name="wpccl_localization[never_expire]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'never_expire' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Never expire', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Active in', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[active_in]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'active_in' ) ); ?>"
                                                   placeholder="<?php /* translators: time */
                                                   esc_attr_e( 'Active in %s', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Day', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[day]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'day' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Day', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Days', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[days]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'days' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Days', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Minimum spend', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text"
                                                   name="wpccl_localization[minimum_spend]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'minimum_spend' ) ); ?>"
                                                   placeholder="<?php /* translators: minimum */
                                                   esc_attr_e( 'The minimum spend for this coupon is %s.', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Maximum spend', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text"
                                                   name="wpccl_localization[maximum_spend]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'maximum_spend' ) ); ?>"
                                                   placeholder="<?php /* translators: maximum */
                                                   esc_attr_e( 'The maximum spend for this coupon is %s.', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e( 'Empty', 'wpc-coupon-listing' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="text" class="regular-text" name="wpccl_localization[empty]"
                                                   value="<?php echo esc_attr( Wpccl_Helper::localization( 'empty' ) ); ?>"
                                                   placeholder="<?php esc_attr_e( 'Have no coupons here!', 'wpc-coupon-listing' ); ?>"/>
                                        </label>
                                    </td>
                                </tr>
                                <tr class="submit">
                                    <th colspan="2">
                                        <div class="wpclever_submit">
                                            <?php
                                            settings_fields( 'wpccl_localization' );
                                            submit_button( '', 'primary', 'submit', false );

                                            if ( function_exists( 'wpc_last_saved' ) ) {
                                                wpc_last_saved( get_option( 'wpccl_localization', [] ) );
                                            }
                                            ?>
                                        </div>
                                        <a style="display: none;" class="wpclever_export"
                                           data-key="wpccl_localization"
                                           data-name="settings"
                                           href="#"><?php esc_html_e( 'import / export', 'wpc-coupon-listing' ); ?></a>
                                    </th>
                                </tr>
                            </table>
                        </form>
                    <?php } ?>
                </div><!-- /.wpclever_settings_page_content -->
                <div class="wpclever_settings_page_suggestion">
                    <div class="wpclever_settings_page_suggestion_label">
                        <span class="dashicons dashicons-yes-alt"></span> Suggestion
                    </div>
                    <div class="wpclever_settings_page_suggestion_content">
                        <div>
                            To display custom engaging real-time messages on any wished positions, please install
                            <a href="https://wordpress.org/plugins/wpc-smart-messages/" target="_blank">WPC Smart
                                Messages</a> plugin. It's free!
                        </div>
                        <div>
                            Wanna save your precious time working on variations? Try our brand-new free plugin
                            <a href="https://wordpress.org/plugins/wpc-variation-bulk-editor/" target="_blank">WPC
                                Variation Bulk Editor</a> and
                            <a href="https://wordpress.org/plugins/wpc-variation-duplicator/" target="_blank">WPC
                                Variation Duplicator</a>.
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        public function save_coupon_settings( $post_id ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce before this hook
            $public = isset( $_POST['wpccl_public'] );
            update_post_meta( $post_id, 'wpccl_public', $public );
        }

        public function coupon_tab( $tabs ) {
            $tabs['wpccl'] = [
                    'label'  => esc_html__( 'WPC Coupon Listing', 'wpc-coupon-listing' ),
                    'target' => 'wpccl_coupon_listing',
                    'class'  => '',
            ];

            return $tabs;
        }

        public function coupon_tab_panel( $coupon_id ) {
            ?>
            <div id="wpccl_coupon_listing" class="panel woocommerce_options_panel">
                <div class="options_group">
                    <?php
                    $value = get_post_meta( $coupon_id, 'wpccl_public', true );

                    woocommerce_wp_checkbox(
                            [
                                    'id'          => 'wpccl_public',
                                    'label'       => esc_html__( 'Public', 'wpc-coupon-listing' ),
                                    'description' => esc_html__( 'Check this box if the coupon is public and it will be shown on WPC Coupon Listing', 'wpc-coupon-listing' ),
                                    'value'       => wc_bool_to_string( $value ),
                            ]
                    );
                    ?>
                </div>
            </div>
            <?php
        }

        function action_links( $links, $file ) {
            static $plugin;

            if ( ! isset( $plugin ) ) {
                $plugin = plugin_basename( WPCCL_FILE );
            }

            if ( $plugin === $file ) {
                $settings = '<a href="' . esc_url( admin_url( 'admin.php?page=wpclever-wpccl&tab=settings' ) ) . '">' . esc_html__( 'Settings', 'wpc-coupon-listing' ) . '</a>';
                array_unshift( $links, $settings );
            }

            return (array) $links;
        }

        function row_meta( $links, $file ) {
            static $plugin;

            if ( ! isset( $plugin ) ) {
                $plugin = plugin_basename( WPCCL_FILE );
            }

            if ( $plugin === $file ) {
                $row_meta = [
                        'support' => '<a href="' . esc_url( WPCCL_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-coupon-listing' ) . '</a>',
                ];

                return array_merge( $links, $row_meta );
            }

            return (array) $links;
        }

        function set_coupon_public( $data, $coupon_id, $args ) {
            $data['wpccl_public'] = isset( $args['wpccl_public'] );

            return $data;
        }
    }
}

return Wpccl_Backend::instance();
