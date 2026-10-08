<?php
defined('ABSPATH') || exit;

global $product;

if ( empty($product) || ! $product->is_visible() ) {
    return;
}

$product_id = $product->get_id();

?>

<?php
defined('ABSPATH') || exit;

global $product;

if ( empty($product) || ! $product->is_visible() ) {
    return;
}

$product_id = $product->get_id();

?>

<div class="card-product style-6">

    <div class="card-product_wrapper aspect-ratio-0 d-flex style-line-radius">

        <!-- Product Image -->
        <a href="<?php echo esc_url(get_permalink()); ?>" class="product-img">

            <?php
            echo woocommerce_get_product_thumbnail(
                'woocommerce_thumbnail',
                array(
                    'class' => 'lazyload img-product'
                )
            );
            ?>


            <?php

            $gallery_ids = $product->get_gallery_image_ids();

            if(!empty($gallery_ids)) {

                echo wp_get_attachment_image(
                    $gallery_ids[0],
                    'woocommerce_thumbnail',
                    false,
                    array(
                        'class' => 'lazyload img-hover'
                    )
                );

            } else {

                echo woocommerce_get_product_thumbnail();

            }

            ?>

        </a>


        <!-- Product Actions -->
        <ul class="product-action_list">


            <!-- Wishlist -->
            <li class="wishlist">

                <div class="hover-tooltip tooltip-left box-icon">


                    <?php echo do_shortcode('[woosw id="' . $product_id . '"]'); ?>

                    <span class="tooltip">
                        Add to Wishlist
                    </span>

                </div>

            </li>



            <!-- Compare -->
            <li class="compare">

                <div class="hover-tooltip tooltip-left box-icon">


                    <?php
                    echo do_shortcode('[woosc id="' . $product_id . '"]');
                    ?>

                    <span class="tooltip">
                        Compare
                    </span>

                </div>

            </li>



            <!-- Quick View -->
            <li>

                <div class="hover-tooltip tooltip-left box-icon">

                    <?php echo do_shortcode('[woosq id="' . $product_id . '"]'); ?>

                    <span class="tooltip">
                        Quick View
                    </span>

                </div>

            </li>


        </ul>



        <!-- Add To Cart Button -->
        <div class="product-action_bot">

            <?php
            woocommerce_template_loop_add_to_cart();
            ?>

        </div>


    </div>



    <!-- Product Info -->
    <div class="card-product_info d-grid">


        <div>


            <!-- Rating -->
            <div class="rate_wrap w-100 mb-12">

                <?php
                echo wc_get_rating_html(
                    $product->get_average_rating()
                );
                ?>

            </div>



            <!-- Title -->
            <a href="<?php echo esc_url($product->get_permalink()); ?>"
               class="link name-product h4">

                <?php echo esc_html($product->get_title()); ?>

            </a>


        </div>



				<!-- Stock Progress -->
		<?php

		$stock_quantity = $product->get_stock_quantity();

		if ( $stock_quantity === null ) {
			$stock_quantity = 10;
		}


		$total_sales = (int) get_post_meta(
			$product->get_id(),
			'total_sales',
			true
		);

		// $total_sales = 95;

		// Total available + sold
		$total_quantity = $stock_quantity + $total_sales;


		// Calculate sold percentage
		$progress_percent = 0;

		if ( $total_quantity > 0 ) {
			$progress_percent = ($total_sales / $total_quantity) * 100;
		}

		?>

		<div class="product-progress_sold primary-2">

			<div class="progress-sold progress"
				role="progressbar"
				aria-valuemin="0"
				aria-valuemax="100">

				<div class="progress-bar"
					style="width: <?php echo esc_attr($progress_percent); ?>%">
				</div>

			</div>


			<div class="box-quantity">

				<p class="text-avaiable">
					Available:
					<span class="fw-bold text-black">
						<?php echo esc_html($stock_quantity); ?>
					</span>
				</p>


				<p class="text-avaiable">
					Sold:
					<span class="fw-bold text-black">
						<?php echo esc_html($total_sales); ?>
					</span>
				</p>

			</div>

		</div>



        <!-- Price -->
        <div class="price-wrap">

            <?php
            echo $product->get_price_html();
            ?>

        </div>



    </div>


</div>

