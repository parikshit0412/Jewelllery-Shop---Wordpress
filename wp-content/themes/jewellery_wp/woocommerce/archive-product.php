<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;
get_header( 'shop' );?>

<?php do_action( 'woocommerce_before_main_content' );
?>
<!-- Page Title -->
<?php if(is_shop()):?>
<!-- Category Slider -->

        <!-- Category -->
        <section class="flat-spacing">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h2 class="s-title h1 fw-medium">Shop By Category</h2>
                </div>
                <div dir="ltr" class="swiper tf-swiper wow fadeInUp" data-preview="6" data-tablet="4" data-mobile-sm="3" data-mobile="2"
                    data-space-lg="48" data-space-md="24" data-space="12" data-pagination="2" data-pagination-sm="3" data-pagination-md="4"
                    data-pagination-lg="6">
                    <div class="swiper-wrapper">
						                <?php
                $cats = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'parent' => 0
                ));

                $i = 1;

                foreach($cats as $cat):

                    $thumb = get_term_meta($cat->term_id,'thumbnail_id',true);

                    $img = $thumb
                        ? wp_get_attachment_image_url($thumb,'medium')
                        : wc_placeholder_img_src();
                ?>

                        <!-- item 1 -->
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url(get_term_link($cat)); ?>"
                           class="widget-collection style-2 type-line_color_<?php echo $i; ?> hover-img">

                            <div class="collection_image img-style overflow-visible">
                                <img src="<?php echo esc_url($img); ?>" alt="">
                            </div>

                            <div class="collection_content">
                                <h4 class="collection_name">
                                    <?php echo esc_html($cat->name); ?>
                                </h4>

                                <span class="collection_count">
                                    <?php echo $cat->count; ?> Products
                                </span>
                            </div>

                        </a>
                        </div>
						 <?php
                    $i++;
                    if($i>6) $i=1;
                endforeach;
                ?>
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Category -->
        
<?php endif;?>
<div class="flat-spacing-3 pt-0">
    <div class="container">
        <div class="row">

            <!-- Sidebar -->
                <?php            

					echo '<div class="col-lg-3 col-md-4 col-sm-12">';

					echo '<div class="sidebar"><div class="canvas-sidebar sidebar-filter canvas-filter left"><div class="canvas-wrapper"><div class="canvas-body">';
					echo do_shortcode("[woof  sid='generator_6a6b8a5c01d66' autohide='0' autosubmit='1' is_ajax='0' ajax_redraw='1' start_filtering_btn='0' btn_position='b' dynamic_recount='-1' hide_terms_count_txt='0' mobile_mode='0' ]");
					echo '</div></div></div></div>';
					echo '</div>';
                ?>

            <!-- Products -->
            <div class="col-xl-9">

                <div class="tf-shop-control">

                    <!-- <div class="shop-sale-text d-none d-xl-flex">
                        <input type="checkbox" name="sale" class="tf-check" id="sale">
                        <label for="sale" class="label">
                            Show only products on sale
                        </label>
                    </div> -->

                    <!-- <div class="tf-control-filter d-xl-none">
                        <button type="button" id="filterShop" class="tf-btn-filter">
                            <span class="icon icon-filter"></span>
                            <span class="text">Filter</span>
                        </button>
                    </div> -->

                    <div class="tf-control-sorting">
<?php woocommerce_result_count(); 
woocommerce_catalog_ordering();?>
                    </div>

                </div>

                <div class="wrapper-control-shop gridLayout-wrapper">
					<div class="tf-control-sorting">
                        <?php
                        // Result Count + Sorting
                        do_action( 'woocommerce_before_shop_loop' );
                        ?>
						</div>
                    <?php if ( woocommerce_product_loop() ) : ?>

                        <?php woocommerce_product_loop_start(); ?>

                        <?php
                        while ( have_posts() ) :
                            the_post();

                            wc_get_template_part( 'content', 'product' );

                        endwhile;
                        ?>

                        <?php woocommerce_product_loop_end(); ?>

                        <?php
                        do_action( 'woocommerce_after_shop_loop' );
                        ?>

                    <?php else : ?>

                        <?php
                        do_action( 'woocommerce_no_products_found' );
                        ?>

                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>
</div><?php

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
