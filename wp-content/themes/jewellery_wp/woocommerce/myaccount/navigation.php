<?php
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce My Account Navigation
 *
 * Custom design + dynamic WooCommerce endpoints
 */

$account_menu_items = wc_get_account_menu_items();
?>

<nav class="sidebar-account-nav woocommerce-MyAccount-navigation">

    <ul class="my-account-nav">

        <?php foreach ( $account_menu_items as $endpoint => $label ) : ?>

            <?php
            /*
             * --------------------------------
             * Dynamic URL
             * --------------------------------
             */
            $url = wc_get_account_endpoint_url( $endpoint );


            /*
             * --------------------------------
             * Active endpoint
             * --------------------------------
             */

            $is_active = false;

            // Dashboard
            if ( 'dashboard' === $endpoint ) {

                $is_active = ! is_wc_endpoint_url();

            }
            // Other endpoints
            elseif ( is_wc_endpoint_url( $endpoint ) ) {

                $is_active = true;

            }


            /*
             * --------------------------------
             * Custom icons
             * --------------------------------
             */

            $icon = 'icon-circle-four';

            switch ( $endpoint ) {

                case 'dashboard':
                    $icon = 'icon-circle-four';
                    break;

                case 'orders':
                    $icon = 'icon-box-arrow-down';
                    break;

                case 'downloads':
                    $icon = 'icon-download';
                    break;

                case 'edit-address':
                    $icon = 'icon-address-book';
                    break;

                case 'edit-account':
                    $icon = 'icon-setting';
                    break;

                case 'customer-logout':
                    $icon = 'icon-sign-out';
                    break;

                default:
                    $icon = 'icon-circle-four';
                    break;
            }


            /*
             * --------------------------------
             * Logout
             * --------------------------------
             */

            if ( 'customer-logout' === $endpoint ) {
                $url = wc_logout_url();
            }


            /*
             * --------------------------------
             * Custom classes
             * --------------------------------
             */

            $classes = array(
                'my-account-nav_item',
                'h5',
            );

            if ( $is_active ) {
                $classes[] = 'active';
            }

            if ( 'customer-logout' === $endpoint ) {
                $classes[] = 'logout-item';
            }
            ?>

            <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--<?php echo esc_attr( $endpoint ); ?>">

                <a
                    href="<?php echo esc_url( $url ); ?>"
                    class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
                >

                    <i class="icon <?php echo esc_attr( $icon ); ?>"></i>

                        <?php echo esc_html( $label ); ?>

                </a>

            </li>

        <?php endforeach; ?>

    </ul>

</nav>