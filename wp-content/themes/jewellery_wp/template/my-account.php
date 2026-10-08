<?php 
/*
Template Name: My Account
*/
get_header(); ?>


<?php get_template_part( 'template-parts/inner-banner' ); ?>
        <section class="flat-spacing">
            <div class="container">
                    <?php the_content(); ?>
            </div>
        </section>
<?php get_footer(); ?>