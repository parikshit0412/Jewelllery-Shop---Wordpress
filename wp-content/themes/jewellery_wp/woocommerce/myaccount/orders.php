<?php
defined( 'ABSPATH' ) || exit;

$current_user_id = get_current_user_id();
$current_page    = max( 1, absint( get_query_var( 'paged' ) ) );

$orders_per_page = 5;

$orders = wc_get_orders(
    array(
        'customer_id' => $current_user_id,
        'limit'       => $orders_per_page,
        'paged'       => $current_page,
        'paginate'    => true,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'return'      => 'objects',
    )
);

$total_orders = isset( $orders->total ) ? absint( $orders->total ) : 0;
$total_pages  = isset( $orders->max_num_pages ) ? absint( $orders->max_num_pages ) : 1;
?>

<div class="my-account-content">

    <div class="account-my_order">

        <h2 class="account-title type-semibold heading_cmn_white">
            My Order
        </h2>

        <div class="overflow-auto">

            <table class="table-my_order order_recent">

                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Products</th>
                        <th>Pricing</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody class="rightacountbody">

                    <?php if ( ! empty( $orders->orders ) ) : ?>

                        <?php foreach ( $orders->orders as $order ) : ?>

                            <?php
                            $order_id     = $order->get_id();
                            $order_number = $order->get_order_number();
                            $order_total  = $order->get_total();
                            $status       = $order->get_status();

                            /*
                             * Status CSS class
                             */
                            switch ( $status ) {

                                case 'completed':
                                    $status_class = 'stt-complete';
                                    break;

                                case 'cancelled':
                                case 'canceled':
                                    $status_class = 'stt-cancel';
                                    break;

                                case 'processing':
                                case 'on-hold':
                                case 'pending':
                                    $status_class = 'stt-pending';
                                    break;

                                case 'shipped':
                                case 'out-for-delivery':
                                case 'delivery':
                                    $status_class = 'stt-delivery';
                                    break;

                                default:
                                    $status_class = 'stt-pending';
                                    break;
                            }

                            $status_name = wc_get_order_status_name( $status );

                            /*
                             * View order URL
                             */
                            $view_order_url = $order->get_view_order_url();
                            ?>

                            <tr class="tb-order-item">

                                <!-- Order -->
                                <td class="tb-order_code">

                                    #<?php echo esc_html( $order_number ); ?>

                                </td>


                                <!-- Products -->
                                <td>

                                    <?php
                                    $items = $order->get_items();

                                    if ( ! empty( $items ) ) :
                                        ?>

                                        <?php
                                        $item_count = 0;

                                        foreach ( $items as $item_id => $item ) :

                                            $product = $item->get_product();

                                            if ( ! $product ) {
                                                continue;
                                            }

                                            /*
                                             * Show only first product.
                                             * Change to all products if required.
                                             */
                                            if ( $item_count > 0 ) {
                                                break;
                                            }

                                            $product_id  = $product->get_id();
                                            $product_url = $product->get_permalink();
                                            $product_name = $item->get_name();
                                            $product_image = $product->get_image(
                                                'woocommerce_thumbnail',
                                                array(
                                                    'class' => 'lazyload',
                                                )
                                            );
                                            ?>

                                            <div class="tb-order_product">

                                                <a
                                                    href="<?php echo esc_url( $product_url ); ?>"
                                                    class="img-prd"
                                                >
                                                    <?php echo wp_kses_post( $product_image ); ?>
                                                </a>

                                                <div class="infor-prd">

                                                    <h6>

                                                        <a
                                                            href="<?php echo esc_url( $product_url ); ?>"
                                                            class="prd_name link"
                                                        >
                                                            <?php echo esc_html( $product_name ); ?>
                                                        </a>

                                                    </h6>

                                                    <?php if ( $item->get_quantity() > 1 ) : ?>

                                                        <span class="prd-qty">
                                                            × <?php echo esc_html( $item->get_quantity() ); ?>
                                                        </span>

                                                    <?php endif; ?>


                                                                <?php if ( count( $items ) > 1 ) : ?>

                                                                    <span class="order-more-products">

                                                                        +
                                                                        <?php echo esc_html( count( $items ) - 1 ); ?>

                                                                        <?php esc_html_e( 'more item(s)', 'woocommerce' ); ?>

                                                                    </span>

                                                                <?php endif; ?>
                                                </div>

                                            </div>

                                            <?php
                                            $item_count++;

                                        endforeach;
                                        ?>

                                    <?php endif; ?>

                                </td>


                                <!-- Price -->
                                <td class="tb-order_price">

                                    <?php
                                    echo wp_kses_post(
                                        $order->get_formatted_order_total()
                                    );
                                    ?>

                                </td>


                                <!-- Status -->
                                <td>

                                    <div class="tb-order_status <?php echo esc_attr( $status_class ); ?>">

                                        <?php echo esc_html( $status_name ); ?>

                                    </div>

                                </td>


                                <!-- Action -->
                                <td class="tb-order_action">

                                    <a
                                        href="<?php echo esc_url( $view_order_url ); ?>"
                                        class="link fw-semibold"
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="5" class="text-center">

                                <?php esc_html_e( 'No orders found.', 'woocommerce' ); ?>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- Dynamic Pagination -->
        <?php if ( $total_pages > 1 ) : ?>

            <div class="wd-full wg-pagination">

                <?php

                echo wp_kses_post(
                    paginate_links(
                        array(
                            'base'      => esc_url_raw(
                                add_query_arg(
                                    'paged',
                                    '%#%'
                                )
                            ),
                            'format'    => '',
                            'current'   => $current_page,
                            'total'     => $total_pages,
                            'mid_size'  => 2,
                            'end_size'  => 1,
                            'prev_text' => '<i class="icon icon-caret-left"></i>',
                            'next_text' => '<i class="icon icon-caret-right"></i>',
                            'type'      => 'plain',
                        )
                    )
                );

                ?>

            </div>

        <?php endif; ?>

    </div>

</div>