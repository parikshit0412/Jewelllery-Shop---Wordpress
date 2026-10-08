<?php 
/*
Template Name: Home

*/
get_header();
?>

        <!-- Banner Slider -->
        <div class="tf-slideshow tf-btn-swiper-main">
            <div class="container-full-2">
                <div class="slideshow-container">
                    <div dir="ltr" class="swiper tf-swiper sw-slide-show slider_effect_fade" data-auto="true" data-loop="true" data-effect="fade"
                        data-delay="3000">
                        <div class="swiper-wrapper">
                            <!-- item 1 -->
                            <div class="swiper-slide">
                                <div class="slider-wrap style-5">
                                    <div class="sld_image type-radius">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg" alt="Slider"
                                            class="lazyload scale-item sale-item-1">
                                    </div>
                                    <div class="sld_content type-3">
                                        <h3 class="sub-title_sld text-white fade-item fade-item-1">
                                            HANDCRAFTED LUXURY
                                        </h3>
                                        <h2 class="title_sld h1 text-white fade-item fade-item-2">
                                            SHINE BRIGHT <br>
                                            WITH ELEGANCE
                                        </h2>
                                        <div class="fade-item fade-item-4">
                                            <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="tf-btn bg-primary animate-btn animate-dark">
                                                Shop now
                                                <i class="icon icon-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- item 2 -->
                            <div class="swiper-slide">
                                <div class="slider-wrap style-5">
                                    <div class="sld_image type-radius">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg" alt="Slider"
                                            class="lazyload scale-item sale-item-1">
                                    </div>
                                    <div class="sld_content type-3">
                                        <h3 class="sub-title_sld text-white fade-item fade-item-1">
                                            EXCLUSIVE DESIGNS
                                        </h3>
                                        <h2 class="title_sld h1 text-white fade-item fade-item-2">
                                            SHINE BRIGHT <br>
                                            WITH ELEGANCE
                                        </h2>
                                        <div class="fade-item fade-item-4">
                                            <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="tf-btn bg-primary animate-btn animate-dark">
                                                Shop now
                                                <i class="icon icon-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="slider-wrap style-5">
                                    <div class="sld_image type-radius">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg" alt="Slider"
                                            class="lazyload scale-item sale-item-1">
                                    </div>
                                    <div class="sld_content type-3">
                                        <h3 class="sub-title_sld text-white fade-item fade-item-1">
                                            TIMELESS BEAUTY
                                        </h3>
                                        <h2 class="title_sld h1 text-white fade-item fade-item-2">
                                            SHINE BRIGHT <br>
                                            WITH ELEGANCE
                                        </h2>
                                        <div class="fade-item fade-item-4">
                                            <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="tf-btn bg-primary animate-btn animate-dark">
                                                Shop now
                                                <i class="icon icon-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sw-dot-default style-white tf-sw-pagination"></div>
                    </div>
                    <div class="col-right d-md-none d-xl-block">
                        <div class="box-image_V01 type-4 hover-img h-md-100">
                            <a href="#" class="box-image_image img-style">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner2.jpg" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/banner2.jpg" alt="Image" class="h-100 lazyload">
                            </a>
                            <div class="box-image_content align-items-center text-center">
                                <h4 href="#" class="sub-title text-primary mb-16">NEW ARRIVALS</h4>
                                <a href="#" class="title link primary-2 h2 fw-semibold mb-32">
                                    Shine Bright with <br>
                                    Elegant Jewellery
                                </a>
                                <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="tf-btn bg-primary primary-2 animate-btn">
                                    Shop now
                                    <i class="icon icon-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Banner Slider -->

        <!-- Category -->
        <section class="flat-spacing">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h2 class="s-title h1 fw-medium">Shop By Category</h2>
                </div>
                <div dir="ltr" class="swiper tf-swiper wow fadeInUp" data-preview="2" data-tablet="2" data-mobile-sm="2" data-mobile="2"
                    data-space-lg="48" data-space-md="24" data-space="12" data-pagination="1" data-pagination-sm="1" data-pagination-md="1"
                    data-pagination-lg="1">
                    <div class="swiper-wrapper justify-content-center">
						                <?php
                $all_cats = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'parent' => 0
                ));

                $cats = array();
                if (!empty($all_cats) && !is_wp_error($all_cats)) {
                    foreach ($all_cats as $cat_item) {
                        $n = strtolower(trim($cat_item->name));
                        $s = strtolower(trim($cat_item->slug));
                        if (
                            strpos($n, 'necklace') !== false || 
                            strpos($s, 'necklace') !== false || 
                            strpos($n, 'earring') !== false || 
                            strpos($s, 'earring') !== false
                        ) {
                            $cats[] = $cat_item;
                        }
                    }
                }

                $i = 1;

                foreach($cats as $cat):

                    $thumb = get_term_meta($cat->term_id,'thumbnail_id',true);

                    $img = $thumb
                        ? wp_get_attachment_image_url($thumb,'medium')
                        : wc_placeholder_img_src();
                ?>

                        <!-- item 1 -->
                        <div class="swiper-slide" style="max-width: 480px;">
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

        <!-- Discover -->
        <section class="themesFlat">
            <div class="container">
                <div dir="ltr" class="swiper tf-swiper" data-preview="3" data-tablet="2" data-mobile-sm="2" data-mobile="1" data-space-lg="48" data-space-md="24" data-space="12" data-pagination="1" data-pagination-sm="2" data-pagination-md="2" data-pagination-lg="3">
                    <div class="swiper-wrapper">
                        <!-- item 1 -->
                        <div class="swiper-slide">
                            <div class="box-image_V06 type-space-2 hover-img wow fadeInLeft">
                                <ul class="product-badge_list">
                                    <li class="product-badge_item fw-normal h6 new">New arrival</li>
                                </ul>
                                <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="box-image_image img-style">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/bx3.jpg" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/bx3.jpg" alt="IMG" class="lazyload">
                                </a>
                                <div class="box-image_content">
                                    <p class="sub-title h6 fw-semibold text-primary">
                                        HANDCRAFTED
                                    </p>
                                    <h4 class="title">
                                        <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="link linkblack">
                                            Luxury Designs for  <br>
                                            Every Occasion
                                        </a>
                                    </h4>
                                    <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="tf-btn-line letter-space-0">Shop now</a>
                                </div>
                            </div>
                        </div>
                        <!-- item 2 -->
                        <div class="swiper-slide">
                            <div class="box-image_V06 type-space-2 hover-img wow fadeInLeft" data-wow-delay="0.1s">
                                <ul class="product-badge_list">
                                    <li class="product-badge_item fw-normal h6 sale">Trending</li>
                                </ul>
                                <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="box-image_image img-style">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/bx1.jpg" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/bx1.jpg" alt="IMG" class="lazyload">
                                </a>
                                <div class="box-image_content">
                                    <p class="sub-title h6 fw-semibold text-primary">
                                        EXCLUSIVE
                                    </p>
                                    <h4 class="title">
                                        <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="link linkblack">
                                            Timeless Beauty &  <br>
                                            Brilliant Shine
                                        </a>
                                    </h4>
                                    <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="tf-btn-line letter-space-0">Shop now</a>
                                </div>
                            </div>
                        </div>
                        <!-- item 3 -->
                        <div class="swiper-slide">
                            <div class="box-image_V06 type-space-2 hover-img wow fadeInLeft" data-wow-delay="0.2s">
                                <ul class="product-badge_list">
                                    <li class="product-badge_item flash-sale">
                                        <i class="icon icon-thunder"></i>
                                        Featured
                                    </li>
                                </ul>
                                <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="box-image_image img-style">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/bx2.jpg" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/bx2.jpg" alt="IMG" class="lazyload">
                                </a>
                                <div class="box-image_content">
                                    <p class="sub-title h6 fw-semibold text-primary">
                                        SIGNATURE
                                    </p>
                                    <h4 class="title">
                                        <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="link linkblack">
                                            Sparkle with Style  <br>
                                            & Elegance
                                        </a>
                                    </h4>
                                    <a href="<?php echo wc_get_page_permalink( 'shop' );?>" class="tf-btn-line letter-space-0">Shop now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Discover --> 

        <?php /* About Us / Our Story Hidden for now ?>
        <!-- Banner Countdown -->
        <section class="themesFlat mdlbanner">
            <div class="banner-cd_v01 style-3">
                <div class="img-item_bg">
                    <img class="lazyload" src="<?php echo get_template_directory_uri(); ?>/assets/images/mdlbg.png" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/mdlbg.png" alt="">
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="position-relative">
                                <div class="banner_img">
                                    <img class="lazyload" src="<?php echo get_template_directory_uri(); ?>/assets/images/bgleft.png" data-src="<?php echo get_template_directory_uri(); ?>/assets/images/bgleft.png" alt="Image">
                                </div>  
                            </div>
                        </div>
                        <div class="col-lg-6 d-flex align-items-center justify-content-center">
                            <div class="banner_content wow fadeInUp p-0">
                                <h2 class="title h1 fw-medium">Our Story!</h2>
                                <p class="sub-title">Handcrafted luxury jewellery for every special moment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Banner Countdown -->
        <?php */ ?>

<?php if ( have_rows('product_section') ) : ?>
    <?php while( have_rows('product_section') ) : the_row(); ?>

        <?php the_sub_field('sub_field_name'); ?>


        <!-- Deal Today -->
        <section class="flat-spacing woocommerce ">
            <div class="container">
                <div class="sect-title text-center wow fadeInUp">
                    <h2 class="title h1 mb-8">popular products</h2>
                    <p class="s-subtitle h6">Discover our exclusive jewellery collection crafted to add elegance and sparkle to every occasion. Explore premium designs crafted with timeless elegance.</p>
                </div>
                <div dir="ltr" class="swiper tf-swiper wow fadeInUp" data-preview="4" data-tablet="3" data-mobile-sm="2" data-mobile="2"
                    data-space-lg="48" data-space-md="30" data-space="12" data-pagination="2" data-pagination-sm="2" data-pagination-md="3"
                    data-pagination-lg="4">
                    <div class="swiper-wrapper">
                      <?php
$featured_posts = get_sub_field('popular_products');
if( $featured_posts ): ?>
    <?php foreach( $featured_posts as $product ): 

        // Setup this post for WP functions (variable must be named $post).
        setup_postdata($product); ?>
                        <!-- item 1 -->
                        <div class="swiper-slide">
                            <?php wc_get_template_part( 'content', 'product' );?>
                        </div>

    <?php endforeach; ?>
    <?php 
    // Reset the global post object so that the rest of the page works correctly.
    wp_reset_postdata(); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Deal Today -->
         
    <?php endwhile; ?>
<?php endif; ?>

        <?php /* Coupon Section Hidden for now ?>
        <!-- Coupon -->
        <section class="themesFlat">
            <div class="container">
                <div class="clippatch wg-copy primary-2 wow fadeInUp">
                    <div class="content wrap-code">
                        <h3 class="text-white">Exclusive Luxury Jewellery Collection</h3>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Coupon -->
        <?php */ ?>
         <?php
      
    $reviews = get_comments( array(
        'status'      => 'approve',
        'post_type'   => 'product',
        'number'      => 12,
        'orderby'     => 'comment_date',
        'order'       => 'DESC',
        'meta_query'  => array(
            array(
                'key'     => 'rating',
                'compare' => 'EXISTS',
            ),
        ),
    ) );

    // if ( empty( $reviews ) ) {
    //     return;
    // }
    ?>

    <section class="flat-spacing">
        <div class="container">

        <?php if ( empty( $reviews ) ) : ?>

            <div class="sect-title type-2">

                <div class="flex-sm-1 wow fadeInUp">
                    <h1 class="s-title mb-8">Customer Reviews</h1>
                    <p class="s-subtitle h6">
                        What our customers are saying
                    </p>
                </div>
				</div>
            <div class="sect-title type-2">
				<div class="text-center py-40">
					<p class="h5 mb-0">No reviews found.</p>
				</div>
			</div>
        <?php else : ?>

            <div class="sect-title type-2">

                <div class="flex-sm-1 wow fadeInUp">
                    <h1 class="s-title mb-8">Customer Reviews</h1>
                    <p class="s-subtitle h6">
                        What our customers are saying
                    </p>
                </div>

                <div class="group-btn-slider wow fadeInUp" data-wow-delay="0.1s">
                    <div class="tf-sw-nav style-2 type-small nav-prev-swiper">
                        <i class="icon icon-caret-left"></i>
                    </div>

                    <div class="tf-sw-nav style-2 type-small nav-next-swiper">
                        <i class="icon icon-caret-right"></i>
                    </div>
                </div>

            </div>

            
                <div dir="ltr" class="swiper tf-swiper" data-preview="2" data-tablet="2" data-mobile-sm="1" data-mobile="1" data-space-lg="48"
                    data-space-md="32" data-space="12" data-pagination="1" data-pagination-sm="1" data-pagination-md="2" data-pagination-lg="2">
                    

                <div class="swiper-wrapper">

						<?php foreach ( $reviews as $review ) :

							$product = wc_get_product( $review->comment_post_ID );

							if ( ! $product ) {
								continue;
							}

							$rating = (int) get_comment_meta(
								$review->comment_ID,
								'rating',
								true
							);

							$author = $review->comment_author;
							$content = $review->comment_content;

							$verified = wc_review_is_from_verified_owner( $review->comment_ID );

							$image = wp_get_attachment_image_url(
								$product->get_image_id(),
								'woocommerce_thumbnail'
							);

							if ( ! $image ) {
								$image = wc_placeholder_img_src();
							}
						?>

                        <div class="swiper-slide">

                            <div class="testimonial-V02 type-space-2 hover-img wow fadeInUp">

                                <!-- Product -->
                                <div class="tes_product">

                                    <div class="product-image img-style">
                                        <a href="<?php echo esc_url( $product->get_permalink() ); ?>">

                                            <img
                                                class="lazyload"
                                                src="<?php echo esc_url( $image ); ?>"
                                                data-src="<?php echo esc_url( $image ); ?>"
                                                alt="<?php echo esc_attr( $product->get_name() ); ?>"
                                            >

                                        </a>
                                    </div>

                                    <div class="product-infor">

                                        <h5 class="prd_name fw-normal">

                                            <a
                                                href="<?php echo esc_url( $product->get_permalink() ); ?>"
                                                class="link"
                                            >
                                                <?php echo esc_html( $product->get_name() ); ?>
                                            </a>

                                        </h5>

                                        <h6 class="prd_price">
                                            <?php echo wp_kses_post( $product->get_price_html() ); ?>
                                        </h6>

                                    </div>

                                </div>

                                <!-- Review -->
                                <div class="tes_content">

                                    <div class="tes_icon">
                                        <i class="icon icon-block-quote"></i>
                                    </div>

                                    <h4 class="tes_title">
                                        <?php echo esc_html( wp_trim_words( $content, 4, '' ) ); ?>
                                    </h4>

                                    <p class="tes_text h4">
                                        “<?php echo esc_html( $content ); ?>”
                                    </p>

                                    <div class="tes_author">

                                        <p class="author-name h4">
                                            <?php echo esc_html( $author ); ?>
                                        </p>

                                        <?php if ( $verified ) : ?>
                                            <i class="author-verified icon-check-circle fs-24"></i>
                                        <?php endif; ?>

                                    </div>

                                    <div class="rate_wrap">

                                        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>

                                            <i class="icon-star <?php echo $i <= $rating ? 'text-star' : ''; ?>"></i>

                                        <?php endfor; ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>
		<?php endif; ?>
		
		

        </div>
    </section>
<?php get_footer(); ?>