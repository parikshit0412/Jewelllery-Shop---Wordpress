<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * WooCommerce customer orders
 */
$orders_per_page = 5;
$current_page    = max( 1, get_query_var( 'paged' ) );

$current_user = wp_get_current_user();
$customer_id  = get_current_user_id();
$orders = wc_get_orders(
    array(
        'customer_id' => $customer_id,
        'limit'       => $orders_per_page,
        'paged'       => $current_page,
        'paginate'    => true,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'return'      => 'objects',
    )
);

$total_orders = isset( $orders->total ) ? (int) $orders->total : 0;
$total_pages  = isset( $orders->max_num_pages ) ? (int) $orders->max_num_pages : 1;

$order_items = isset( $orders->orders ) ? $orders->orders : array();

/*
 * Real order statistics
 *
 * Processing = waiting for confirmation
 * Completed  = successful orders
 * Total      = customer's total orders
 */
$processing_orders = wc_get_orders(
    array(
        'customer_id' => $customer_id,
        'status'      => array( 'pending', 'on-hold', 'processing' ),
        'limit'       => -1,
        'return'      => 'ids',
    )
);

$completed_orders = wc_get_orders(
    array(
        'customer_id' => $customer_id,
        'status'      => array( 'completed' ),
        'limit'       => -1,
        'return'      => 'ids',
    )
);

$processing_count = count( $processing_orders );
$completed_count  = count( $completed_orders );

?>

                    <!-- =========================
                         ORDER STATISTICS
                    ========================== -->

                    <div class="acount-order_stats">

                        <div
                            dir="ltr"
                            class="swiper tf-swiper"
                            data-preview="3"
                            data-tablet="3"
                            data-mobile-sm="2"
                            data-mobile="1"
                            data-space-lg="48"
                            data-space-md="16"
                            data-space="12"
                            data-pagination="1"
                            data-pagination-sm="2"
                            data-pagination-md="3"
                            data-pagination-lg="3"
                        >

                            <div class="swiper-wrapper">


                                <!-- PROCESSING -->

                                <div class="swiper-slide">

                                    <div class="order-box">

                                        <div class="order_icon">
                                            <i class="icon icon-package-thin"></i>
                                        </div>

                                        <div class="order_info">

                                            <p class="info_label h6">
                                                <?php esc_html_e( 'Wait for confirmation', 'woocommerce' ); ?>
                                            </p>

                                            <h2 class="info_count type-semibold heading_cmn_white">
                                                <?php echo esc_html( $processing_count ); ?>
                                            </h2>

                                        </div>

                                    </div>

                                </div>


                                <!-- COMPLETED -->

                                <div class="swiper-slide">

                                    <div class="order-box">

                                        <div class="order_icon">
                                            <i class="icon icon-check-fat"></i>
                                        </div>

                                        <div class="order_info">

                                            <p class="info_label h6">
                                                <?php esc_html_e( 'Successful order', 'woocommerce' ); ?>
                                            </p>

                                            <h2 class="info_count type-semibold heading_cmn_white">
                                                <?php echo esc_html( $completed_count ); ?>
                                            </h2>

                                        </div>

                                    </div>

                                </div>


                                <!-- TOTAL -->

                                <div class="swiper-slide">

                                    <div class="order-box">

                                        <div class="order_icon">
                                            <i class="icon icon-box-arrow-up"></i>
                                        </div>

                                        <div class="order_info">

                                            <p class="info_label h6">
                                                <?php esc_html_e( 'Total order', 'woocommerce' ); ?>
                                            </p>

                                            <h2 class="info_count type-semibold heading_cmn_white">
                                                <?php echo esc_html( $total_orders ); ?>
                                            </h2>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="sw-dot-default tf-sw-pagination"></div>

                        </div>

                    </div>


                    <!-- =========================
                         RECENT ORDERS
                    ========================== -->

                    <div class="account-my_order">

                        <h2 class="account-title type-semibold heading_cmn_white">
                            <?php esc_html_e( 'Recent Orders', 'woocommerce' ); ?>
                        </h2>


                        <?php if ( ! empty( $order_items ) ) : ?>

                            <div class="overflow-auto">

                                <table class="table-my_order order_recent">

                                    <thead>

                                        <tr>

                                            <th>
                                                <?php esc_html_e( 'Order', 'woocommerce' ); ?>
                                            </th>

                                            <th>
                                                <?php esc_html_e( 'Products', 'woocommerce' ); ?>
                                            </th>

                                            <th>
                                                <?php esc_html_e( 'Pricing', 'woocommerce' ); ?>
                                            </th>

                                            <th>
                                                <?php esc_html_e( 'Status', 'woocommerce' ); ?>
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody class="rightacountbody">

                                        <?php foreach ( $order_items as $order ) : ?>

                                            <?php
                                            $order_id     = $order->get_id();
                                            $order_status = $order->get_status();

                                            $status_label = wc_get_order_status_name( $order_status );

                                            $status_class = 'stt-pending';

                                            if ( 'completed' === $order_status ) {
                                                $status_class = 'stt-complete';
                                            } elseif ( in_array( $order_status, array( 'processing', 'on-hold' ), true ) ) {
                                                $status_class = 'stt-delivery';
                                            } elseif ( in_array( $order_status, array( 'cancelled', 'failed', 'refunded' ), true ) ) {
                                                $status_class = 'stt-cancel';
                                            }

                                            $view_order_url = $order->get_view_order_url();

                                            $items = $order->get_items();

                                            $first_item = reset( $items );

                                            $product = false;

                                            if ( $first_item ) {
                                                $product = $first_item->get_product();
                                            }
                                            ?>

                                            <tr class="tb-order-item">


                                                <!-- ORDER ID -->

                                                <td class="tb-order_code">

                                                    <a href="<?php echo esc_url( $view_order_url ); ?>">

                                                        #<?php echo esc_html( $order_id ); ?>

                                                    </a>

                                                </td>


                                                <!-- PRODUCT -->

                                                <td>

                                                    <?php if ( $product ) : ?>

                                                        <div class="tb-order_product">

                                                            <a
                                                                href="<?php echo esc_url( $product->get_permalink( $first_item ) ); ?>"
                                                                class="img-prd"
                                                            >

                                                                <?php
                                                                echo wp_kses_post(
                                                                    $product->get_image(
                                                                        'woocommerce_thumbnail'
                                                                    )
                                                                );
                                                                ?>

                                                            </a>


                                                            <div class="infor-prd">

                                                                <h6>

                                                                    <a
                                                                        href="<?php echo esc_url( $product->get_permalink( $first_item ) ); ?>"
                                                                        class="prd_name link"
                                                                    >

                                                                        <?php echo esc_html( $product->get_name() ); ?>

                                                                    </a>

                                                                </h6>


                                                                <?php if ( count( $items ) > 1 ) : ?>

                                                                    <span class="order-more-products">

                                                                        +
                                                                        <?php echo esc_html( count( $items ) - 1 ); ?>

                                                                        <?php esc_html_e( 'more item(s)', 'woocommerce' ); ?>

                                                                    </span>

                                                                <?php endif; ?>

                                                            </div>

                                                        </div>

                                                    <?php else : ?>

                                                        <span>
                                                            <?php esc_html_e( 'Product unavailable', 'woocommerce' ); ?>
                                                        </span>

                                                    <?php endif; ?>

                                                </td>


                                                <!-- TOTAL -->

                                                <td class="tb-order_price">

                                                    <?php
                                                    echo wp_kses_post(
                                                        $order->get_formatted_order_total()
                                                    );
                                                    ?>

                                                </td>


                                                <!-- STATUS -->

                                                <td>

                                                    <div class="tb-order_status <?php echo esc_attr( $status_class ); ?>">

                                                        <?php echo esc_html( $status_label ); ?>

                                                    </div>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>

                                </table>

                            </div>


                            <!-- =========================
                                 PAGINATION
                            ========================== -->

                            <?php if ( $total_pages > 1 ) : ?>

                                <div class="wd-full wg-pagination">

                                    <?php
                                    echo wp_kses_post(
                                        paginate_links(
                                            array(
                                                'base'      => esc_url_raw(
                                                    add_query_arg(
                                                        'paged',
                                                        '%#%',
                                                        wc_get_account_endpoint_url( 'dashboard' )
                                                    )
                                                ),
                                                'format'    => '',
                                                'current'   => $current_page,
                                                'total'     => $total_pages,
                                                'type'      => 'plain',
                                                'prev_text' => '<i class="icon icon-caret-left"></i>',
                                                'next_text' => '<i class="icon icon-caret-right"></i>',
                                            )
                                        )
                                    );
                                    ?>

                                </div>

                            <?php endif; ?>


                        <?php else : ?>

                            <!-- EMPTY ORDERS -->

                            <div class="woocommerce-info">

                                <?php esc_html_e( 'You have not placed any orders yet.', 'woocommerce' ); ?>

                                <a
                                    class="button"
                                    href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
                                >
                                    <?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
                                </a>

                            </div>

                        <?php endif; ?>

                    </div>