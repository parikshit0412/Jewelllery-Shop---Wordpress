
<?php
/**
 * Inner Banner / Page Title / Breadcrumb
 *
 * File:
 * template-parts/inner-banner.php
 *
 * Usage:
 * get_template_part( 'template-parts/inner-banner' );
 */

defined( 'ABSPATH' ) || exit;


/* =========================================================
 * Helpers
 * ========================================================= */

/**
 * Get a clean title.
 */
if ( ! function_exists( 'mytheme_get_inner_page_title' ) ) {

    function mytheme_get_inner_page_title() {

        /* ---------------------------------------------
         * WooCommerce
         * --------------------------------------------- */

        if ( function_exists( 'is_shop' ) && is_shop() ) {

            return woocommerce_page_title( false );

        }

        if ( function_exists( 'is_product_category' ) && is_product_category() ) {

            return single_term_title( '', false );

        }

        if ( function_exists( 'is_product_tag' ) && is_product_tag() ) {

            return single_term_title( '', false );

        }

        if ( function_exists( 'is_product' ) && is_product() ) {

            return get_the_title();

        }

        if ( function_exists( 'is_cart' ) && is_cart() ) {

            return __( 'Cart', 'your-textdomain' );

        }

        if ( function_exists( 'is_checkout' ) && is_checkout() ) {

            return __( 'Checkout', 'your-textdomain' );

        }


        /* ---------------------------------------------
         * WooCommerce My Account
         * --------------------------------------------- */

        if ( function_exists( 'is_account_page' ) && is_account_page() ) {

            $endpoint = '';

            if (
                function_exists( 'WC' ) &&
                WC() &&
                isset( WC()->query )
            ) {
                $endpoint = WC()->query->get_current_endpoint();
            }

            $endpoint_titles = array(

                'orders' => __( 'Orders', 'your-textdomain' ),

                'view-order' => __( 'View Order', 'your-textdomain' ),

                'downloads' => __( 'Downloads', 'your-textdomain' ),

                'edit-address' => __( 'Addresses', 'your-textdomain' ),

                'edit-account' => __( 'Account Details', 'your-textdomain' ),

                'payment-methods' => __( 'Payment Methods', 'your-textdomain' ),

                'lost-password' => __( 'Lost Password', 'your-textdomain' ),

            );


            /*
             * WPC Smart Wishlist
             *
             * /my-account/wishlist/
             *
             * Some WPC versions use a custom endpoint
             * and some installations use the endpoint
             * directly in the URL.
             */

            if ( $endpoint === 'wishlist' ) {

                return __( 'Wishlist', 'your-textdomain' );

            }


            /*
             * Other registered endpoint.
             */
            if ( ! empty( $endpoint ) && isset( $endpoint_titles[ $endpoint ] ) ) {

                return $endpoint_titles[ $endpoint ];

            }


            /*
             * Check URL for WPC Smart Wishlist.
             *
             * This also handles installations where
             * WC()->query->get_current_endpoint()
             * does not return "wishlist".
             */
            $request_uri = isset( $_SERVER['REQUEST_URI'] )
                ? wp_unslash( $_SERVER['REQUEST_URI'] )
                : '';

            if (
                ! empty( $request_uri ) &&
                strpos( $request_uri, '/wishlist/' ) !== false
            ) {

                return __( 'Wishlist', 'your-textdomain' );

            }


            return __( 'My Account', 'your-textdomain' );
        }


        /* ---------------------------------------------
         * Search
         * --------------------------------------------- */

        if ( is_search() ) {

            return sprintf(
                __( 'Search Results for: %s', 'your-textdomain' ),
                get_search_query()
            );

        }


        /* ---------------------------------------------
         * 404
         * --------------------------------------------- */

        if ( is_404() ) {

            return __( 'Page Not Found', 'your-textdomain' );

        }


        /* ---------------------------------------------
         * Blog
         * --------------------------------------------- */

        if ( is_home() ) {

            return get_the_title( get_option( 'page_for_posts' ) )
                ?: __( 'Blog', 'your-textdomain' );

        }


        /* ---------------------------------------------
         * Single Post
         * --------------------------------------------- */

        if ( is_singular( 'post' ) ) {

            return get_the_title();

        }


        /* ---------------------------------------------
         * Pages
         * --------------------------------------------- */

        if ( is_page() ) {

            return get_the_title();

        }


        /* ---------------------------------------------
         * Archives
         * --------------------------------------------- */

        if ( is_archive() ) {

            $archive_title = get_the_archive_title();

            /*
             * Remove default prefixes such as:
             *
             * Category:
             * Tag:
             * Archives:
             */
            $archive_title = preg_replace(
                '/^(Category|Tag|Archives):\s*/i',
                '',
                wp_strip_all_tags( $archive_title )
            );

            return $archive_title;

        }


        /* ---------------------------------------------
         * Fallback
         * --------------------------------------------- */

        return wp_get_document_title();
    }
}


/**
 * Get Home breadcrumb.
 */
$home_url = home_url( '/' );


/**
 * Current page title.
 */
$page_title = mytheme_get_inner_page_title();

$page_title = wp_strip_all_tags( $page_title );


/**
 * Breadcrumb items.
 *
 * Each item:
 *
 * array(
 *     'title' => '',
 *     'url'   => '',
 * )
 */
$breadcrumbs = array();


/* =========================================================
 * WooCommerce Breadcrumb Logic
 * ========================================================= */

if ( function_exists( 'is_shop' ) && is_shop() ) {

    /*
     * Home → Shop
     */

    $breadcrumbs[] = array(
        'title' => __( 'Shop', 'your-textdomain' ),
        'url'   => '',
    );

}


/* ---------------------------------------------------------
 * Product Category
 * --------------------------------------------------------- */

elseif (
    function_exists( 'is_product_category' ) &&
    is_product_category()
) {

    $term = get_queried_object();

    /*
     * Parent categories.
     */
    if ( $term && ! empty( $term->parent ) ) {

        $ancestors = get_ancestors(
            $term->term_id,
            'product_cat'
        );

        $ancestors = array_reverse( $ancestors );

        foreach ( $ancestors as $ancestor_id ) {

            $ancestor = get_term(
                $ancestor_id,
                'product_cat'
            );

            if ( ! $ancestor || is_wp_error( $ancestor ) ) {
                continue;
            }

            $breadcrumbs[] = array(
                'title' => $ancestor->name,
                'url'   => get_term_link( $ancestor ),
            );
        }
    }

    /*
     * Current category.
     */
    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* ---------------------------------------------------------
 * Product Tag
 * --------------------------------------------------------- */

elseif (
    function_exists( 'is_product_tag' ) &&
    is_product_tag()
) {

    $breadcrumbs[] = array(
        'title' => __( 'Product Tags', 'your-textdomain' ),
        'url'   => '',
    );

    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* ---------------------------------------------------------
 * Single Product
 * --------------------------------------------------------- */

elseif (
    function_exists( 'is_product' ) &&
    is_product()
) {

    /*
     * Home → Shop
     */
    $shop_url = wc_get_page_permalink( 'shop' );

    if ( $shop_url ) {

        $breadcrumbs[] = array(
            'title' => __( 'Shop', 'your-textdomain' ),
            'url'   => $shop_url,
        );
    }


    /*
     * Product category.
     */
    $product_categories = get_the_terms(
        get_the_ID(),
        'product_cat'
    );

    if (
        ! empty( $product_categories ) &&
        ! is_wp_error( $product_categories )
    ) {

        /*
         * Use the first category.
         */
        $category = reset( $product_categories );

        if ( $category ) {

            /*
             * Parent categories.
             */
            if ( $category->parent ) {

                $ancestors = get_ancestors(
                    $category->term_id,
                    'product_cat'
                );

                $ancestors = array_reverse( $ancestors );

                foreach ( $ancestors as $ancestor_id ) {

                    $ancestor = get_term(
                        $ancestor_id,
                        'product_cat'
                    );

                    if (
                        ! $ancestor ||
                        is_wp_error( $ancestor )
                    ) {
                        continue;
                    }

                    $breadcrumbs[] = array(
                        'title' => $ancestor->name,
                        'url'   => get_term_link( $ancestor ),
                    );
                }
            }


            /*
             * Category.
             */
            $breadcrumbs[] = array(
                'title' => $category->name,
                'url'   => get_term_link( $category ),
            );
        }
    }


    /*
     * Product.
     */
    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * My Account
 * ========================================================= */

elseif (
    function_exists( 'is_account_page' ) &&
    is_account_page()
) {

    $endpoint = '';

    if (
        function_exists( 'WC' ) &&
        WC() &&
        isset( WC()->query )
    ) {

        $endpoint = WC()->query->get_current_endpoint();

    }


    /*
     * Detect WPC Smart Wishlist.
     */
    $is_wishlist = false;

    if ( $endpoint === 'wishlist' ) {

        $is_wishlist = true;

    }


    /*
     * Also detect /wishlist/ in URL.
     */
    $request_uri = isset( $_SERVER['REQUEST_URI'] )
        ? wp_unslash( $_SERVER['REQUEST_URI'] )
        : '';

    if (
        ! empty( $request_uri ) &&
        strpos( $request_uri, '/wishlist/' ) !== false
    ) {

        $is_wishlist = true;

    }


    /*
     * Home → My Account
     */
    $myaccount_url = wc_get_page_permalink( 'myaccount' );

    $breadcrumbs[] = array(
        'title' => __( 'My Account', 'your-textdomain' ),
        'url'   => $myaccount_url,
    );


    /*
     * My Account endpoint.
     */
    if ( $is_wishlist ) {

        $breadcrumbs[] = array(
            'title' => __( 'Wishlist', 'your-textdomain' ),
            'url'   => '',
        );

    } elseif ( ! empty( $endpoint ) ) {

        $endpoint_titles = array(

            'orders' => __( 'Orders', 'your-textdomain' ),

            'view-order' => __( 'View Order', 'your-textdomain' ),

            'downloads' => __( 'Downloads', 'your-textdomain' ),

            'edit-address' => __( 'Addresses', 'your-textdomain' ),

            'edit-account' => __( 'Account Details', 'your-textdomain' ),

            'payment-methods' => __( 'Payment Methods', 'your-textdomain' ),

            'lost-password' => __( 'Lost Password', 'your-textdomain' ),

        );


        if ( isset( $endpoint_titles[ $endpoint ] ) ) {

            $breadcrumbs[] = array(
                'title' => $endpoint_titles[ $endpoint ],
                'url'   => '',
            );

        } else {

            $breadcrumbs[] = array(
                'title' => $page_title,
                'url'   => '',
            );

        }

    }

}


/* =========================================================
 * Cart
 * ========================================================= */

elseif (
    function_exists( 'is_cart' ) &&
    is_cart()
) {

    $breadcrumbs[] = array(
        'title' => __( 'Cart', 'your-textdomain' ),
        'url'   => '',
    );

}


/* =========================================================
 * Checkout
 * ========================================================= */

elseif (
    function_exists( 'is_checkout' ) &&
    is_checkout()
) {

    $breadcrumbs[] = array(
        'title' => __( 'Checkout', 'your-textdomain' ),
        'url'   => '',
    );

}


/* =========================================================
 * Search
 * ========================================================= */

elseif ( is_search() ) {

    $breadcrumbs[] = array(
        'title' => __( 'Search', 'your-textdomain' ),
        'url'   => '',
    );

    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * 404
 * ========================================================= */

elseif ( is_404() ) {

    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * Blog
 * ========================================================= */

elseif ( is_home() ) {

    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * Single Post
 * ========================================================= */

elseif ( is_singular( 'post' ) ) {

    /*
     * Blog page.
     */
    $blog_page_id = get_option( 'page_for_posts' );

    if ( $blog_page_id ) {

        $breadcrumbs[] = array(
            'title' => get_the_title( $blog_page_id ),
            'url'   => get_permalink( $blog_page_id ),
        );

    } else {

        $breadcrumbs[] = array(
            'title' => __( 'Blog', 'your-textdomain' ),
            'url'   => get_post_type_archive_link( 'post' ),
        );

    }


    /*
     * Current post.
     */
    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * Normal Page
 * ========================================================= */

elseif ( is_page() ) {

    $page_id = get_queried_object_id();

    /*
     * Page ancestors.
     */
    $ancestors = get_post_ancestors( $page_id );

    if ( ! empty( $ancestors ) ) {

        $ancestors = array_reverse( $ancestors );

        foreach ( $ancestors as $ancestor_id ) {

            $breadcrumbs[] = array(
                'title' => get_the_title( $ancestor_id ),
                'url'   => get_permalink( $ancestor_id ),
            );

        }

    }


    /*
     * Current page.
     */
    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * Archives
 * ========================================================= */

elseif ( is_archive() ) {

    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * Fallback
 * ========================================================= */

else {

    $breadcrumbs[] = array(
        'title' => $page_title,
        'url'   => '',
    );

}


/* =========================================================
 * Output
 * ========================================================= */
?>

<section class="s-page-title">

    <div class="container">

        <div class="content">

            <!-- Page Title -->
            <h1 class="title-page">

                <?php echo esc_html( $page_title ); ?>

            </h1>


            <!-- Breadcrumb -->
            <ul class="breadcrumbs-page">

                <!-- Home -->
                <li>

                    <a
                        href="<?php echo esc_url( $home_url ); ?>"
                        class="h6 link"
                    >
                        <?php esc_html_e( 'Home', 'your-textdomain' ); ?>
                    </a>

                </li>


                <?php if ( ! empty( $breadcrumbs ) ) : ?>

                    <?php foreach ( $breadcrumbs as $breadcrumb ) : ?>

                        <li class="d-flex">

                            <i class="icon icon-caret-right"></i>

                        </li>


                        <li>

                            <?php if ( ! empty( $breadcrumb['url'] ) ) : ?>

                                <a
                                    href="<?php echo esc_url( $breadcrumb['url'] ); ?>"
                                    class="h6 link"
                                >
                                    <?php echo esc_html( $breadcrumb['title'] ); ?>
                                </a>

                            <?php else : ?>

                                <h6 class="current-page fw-normal">

                                    <?php echo esc_html( $breadcrumb['title'] ); ?>

                                </h6>

                            <?php endif; ?>

                        </li>

                    <?php endforeach; ?>

                <?php endif; ?>

            </ul>

        </div>

    </div>

</section>
