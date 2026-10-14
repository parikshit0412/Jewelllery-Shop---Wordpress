<?php
/**
 * Template Name: Wishlist
 *
 * @package jewellery_wp
 */

get_header();

// Page Header & Breadcrumb
get_template_part( 'template-parts/inner-banner' );
?>

<section class="flat-spacing tf-wishlist-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="wishlist-wrapper">
                    <?php
                    if ( ! is_user_logged_in() ) {
                        ?>
                        <div class="text-center py-5">
                            <i class="icon icon-heart text-primary mb-3" style="font-size: 48px; display: inline-block;"></i>
                            <h3 class="heading_cmn_white mb-2"><?php esc_html_e( 'Please Log In to View Your Wishlist', 'jewellery_wp' ); ?></h3>
                            <p class="text-muted mb-4"><?php esc_html_e( 'You need to be logged in to save and manage your favourite jewellery pieces.', 'jewellery_wp' ); ?></p>
                            <a href="<?php echo esc_url( home_url('/login') ); ?>" class="tf-btn bg-primary animate-btn">
                                <i class="icon icon-user me-2"></i>
                                <?php esc_html_e( 'Log In / Register', 'jewellery_wp' ); ?>
                            </a>
                        </div>
                        <?php
                    } else {
                        // Output WPC Smart Wishlist or YITH or page content
                        if ( shortcode_exists( 'woosw_list' ) ) {
                            echo do_shortcode( '[woosw_list]' );
                        } elseif ( shortcode_exists( 'yith_wcwl_wishlist' ) ) {
                            echo do_shortcode( '[yith_wcwl_wishlist]' );
                        } else {
                            while ( have_posts() ) :
                                the_post();
                                the_content();
                            endwhile;
                        }
                    }
                    ?>
                </div>

                <div class="wishlist-actions text-center mt-4">
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="tf-btn bg-primary animate-btn">
                        <i class="icon icon-storefront me-2"></i>
                        <?php esc_html_e( 'Continue Shopping', 'jewellery_wp' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
