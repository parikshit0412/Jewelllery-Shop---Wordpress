<?php
/*
Template Name: Cart
*/

get_header(); ?>
        <!-- Page Title -->
<?php get_template_part( 'template-parts/inner-banner' ); ?>
        <!-- /Page Title -->

        <!-- View Cart -->
        <div class="flat-spacing each-list-prd">
            <div class="container">
                    <?php echo do_shortcode('[woocommerce_cart]'); ?>
            </div>
        </div>
        <!-- /View Cart -->
<?php get_footer(); ?>