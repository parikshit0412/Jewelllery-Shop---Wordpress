<?php 
/*
Template Name: Checkout
*/
get_header(); ?>

        <!-- Page Title -->
<?php get_template_part( 'template-parts/inner-banner' ); ?>
        <section class="flat-spacing">
            <div class="container">
                    <?php echo do_shortcode('[woocommerce_checkout]'); ?>
            </div>
        </section>
        <!-- /Check Out -->

<?php get_footer(); ?>