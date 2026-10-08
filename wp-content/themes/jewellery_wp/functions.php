<?php

/**
 * jewellery_wp functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package jewellery_wp
 */

if (! defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function jewellery_wp_setup()
{
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on jewellery_wp, use a find and replace
		* to change 'jewellery_wp' to the name of your theme in all the template files.
		*/
	load_theme_textdomain('jewellery_wp', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support('title-tag');

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Secondary', 'jewellery_wp'),
			'primary' => __('Primary Menu', 'mytheme'),
			'footer-1' => __('Footer Menu 1', 'mytheme'),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'jewellery_wp_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_theme_support('woocommerce');

	// Optional Gallery Features
	add_theme_support('wc-product-gallery-zoom');
	add_theme_support('wc-product-gallery-lightbox');
	add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'jewellery_wp_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function jewellery_wp_content_width()
{
	$GLOBALS['content_width'] = apply_filters('jewellery_wp_content_width', 640);
}
add_action('after_setup_theme', 'jewellery_wp_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function jewellery_wp_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', 'jewellery_wp'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', 'jewellery_wp'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'jewellery_wp_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function jewellery_wp_scripts()
{
	wp_enqueue_style('jewellery_wp-style', get_stylesheet_uri(), array(), _S_VERSION);
	wp_style_add_data('jewellery_wp-style', 'rtl', 'replace');



	wp_enqueue_script('jewellery_wp-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'jewellery_wp_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

function add_menu_link_class($atts, $item, $args)
{
	if ($args->theme_location == 'primary') {
		$atts['class'] = 'item-link';
	}
	return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_link_class', 10, 3);

function mytheme_assets()
{

	// CSS
	wp_enqueue_style('fonts', get_template_directory_uri() . '/assets/fonts/fonts.css');
	wp_enqueue_style('icomoon', get_template_directory_uri() . '/assets/icon/icomoon/style.css');
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css');
	wp_enqueue_style('swiper', get_template_directory_uri() . '/assets/css/swiper-bundle.min.css');
	wp_enqueue_style('animate', get_template_directory_uri() . '/assets/css/animate.css');
	wp_enqueue_style('theme-style', get_template_directory_uri() . '/assets/css/styles.css',array(),filemtime(get_template_directory() . '/assets/css/styles.css'));



	// JS
	  wp_deregister_script('jquery');
	 	wp_register_script(
	     'jquery',
	     get_template_directory_uri() . '/assets/js/jquery.min.js',
	     array(),
	     '3.7.1',
	    true
	 );

	// 	wp_dequeue_script('wc-cart');
	// wp_dequeue_script('wc-checkout');
	wp_dequeue_script('jquery-blockui');
	// wp_enqueue_script( 'jquery-blockui' );
	// wp_enqueue_script('jquery');

	wp_enqueue_script('wc-cart-fragments');
	wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', ['jquery'], null, true);
	wp_enqueue_script('swiper', get_template_directory_uri() . '/assets/js/swiper-bundle.min.js', ['jquery'], null, true);
	wp_enqueue_script('carousel', get_template_directory_uri() . '/assets/js/carousel.js', ['jquery'], null, true);
	wp_enqueue_script('bootstrap-select', get_template_directory_uri() . '/assets/js/bootstrap-select.min.js', ['jquery'], null, true);
	wp_enqueue_script('lazysize', get_template_directory_uri() . '/assets/js/lazysize.min.js', [], null, true);
	wp_enqueue_script('wow', get_template_directory_uri() . '/assets/js/wow.min.js', [], null, true);
	wp_enqueue_script('infinityslide', get_template_directory_uri() . '/assets/js/infinityslide.js', ['jquery'], null, true);
	wp_enqueue_script('parallaxie', get_template_directory_uri() . '/assets/js/parallaxie.js', ['jquery'], null, true);
	wp_enqueue_script('countdown', get_template_directory_uri() . '/assets/js/count-down.js', ['jquery'], null, true);
	wp_enqueue_script('nouislider', get_template_directory_uri() . '/assets/js/nouislider.min.js', ['jquery'], null, true);
	if (is_shop()) {
		wp_enqueue_script('shop', get_template_directory_uri() . '/assets/js/shop.js', ['jquery'], null, true);
	}
	wp_enqueue_script('custom-register', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], filemtime(get_template_directory() . '/assets/js/main.js'), true);
	wp_enqueue_script('sibforms', get_template_directory_uri() . '/assets/js/sibforms.js', [], null, true);



	wp_localize_script('custom-register', 'wc_custom', [
		'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('update_profile_image'),
		
	]);
}
add_action('wp_enqueue_scripts', 'mytheme_assets');
function yith_wcwl_get_items_count()
{
	ob_start();
?>
	<a class="yith-wcwl-items-count nav-icon-item-2 text-black link woosw-show"
		href="<?php echo esc_url(home_url('/wishlist/')); ?>">
		<i class="icon icon-heart"></i>
		<span class="count yith-wcwl-icon woosw-count">0</span>
	</a>
<?php
	return ob_get_clean();
}
add_shortcode('yith_wcwl_items_count', 'yith_wcwl_get_items_count');

/**
 * Move Cart Item to Wishlist AJAX Handler
 */
add_action('wp_ajax_move_cart_item_to_wishlist', 'custom_move_cart_item_to_wishlist');
add_action('wp_ajax_nopriv_move_cart_item_to_wishlist', 'custom_move_cart_item_to_wishlist');

function custom_move_cart_item_to_wishlist()
{
	$cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
	$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

	if (empty($cart_item_key) && $product_id <= 0) {
		wp_send_json_error(array('message' => __('Invalid request parameters.', 'jewellery_wp')));
	}

	$wishlist_count = 0;

	// 1. Add to WPC Smart Wishlist
	if (class_exists('Woosw_Helper') && $product_id > 0) {
		$key = Woosw_Helper::get_key();
		if ($key !== '#') {
			$products = Woosw_Helper::get_ids($key);
			if (!array_key_exists($product_id, $products)) {
				$product = wc_get_product($product_id);
				$products = [
					$product_id => [
						'time'   => time(),
						'price'  => is_a($product, 'WC_Product') ? $product->get_price() : 0,
						'parent' => wp_get_post_parent_id($product_id) ?: 0,
						'note'   => ''
					]
				] + $products;
				update_option('woosw_list_' . $key, $products, false);
				Woosw_Helper::clear_internal_cache($key);
				if (class_exists('WPCleverWoosw')) {
					WPCleverWoosw::update_product_count($product_id, 'add');
				}
			}
			$wishlist_count = count($products);
		}
	}

	// 2. Remove from WooCommerce cart
	if (!empty($cart_item_key) && function_exists('WC') && WC()->cart) {
		WC()->cart->remove_cart_item($cart_item_key);
		WC()->cart->calculate_totals();
	}

	$cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
	$cart_subtotal = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_subtotal() : '';

	ob_start();
	woocommerce_mini_cart();
	$mini_cart_html = ob_get_clean();

	wp_send_json_success(array(
		'cart_count'     => $cart_count,
		'cart_subtotal'  => $cart_subtotal,
		'wishlist_count' => $wishlist_count,
		'mini_cart_html' => $mini_cart_html,
		'message'        => __('Item moved to your Wishlist!', 'jewellery_wp')
	));
}

/**
 * Change Read More and Loop Add to Cart button text to "Add to Cart" sitewide
 */
add_filter('woocommerce_product_add_to_cart_text', 'custom_change_loop_add_to_cart_text', 99, 2);
function custom_change_loop_add_to_cart_text($text, $product) {
	return __('Add to Cart', 'jewellery_wp');
}

add_filter('woocommerce_product_single_add_to_cart_text', 'custom_change_single_add_to_cart_text', 99, 2);
function custom_change_single_add_to_cart_text($text, $product) {
	return __('Add to Cart', 'jewellery_wp');
}

//crete custom taxonomy for product name material 
function create_material_taxonomy()
{
	$labels = array(
		'name'              => _x('Materials', 'taxonomy general name', 'textdomain'),
		'singular_name'     => _x('Material', 'taxonomy singular name', 'textdomain'),
		'search_items'      => __('Search Materials', 'textdomain'),
		'all_items'         => __('All Materials', 'textdomain'),
		'parent_item'       => __('Parent Material', 'textdomain'),
		'parent_item_colon' => __('Parent Material:', 'textdomain'),
		'edit_item'         => __('Edit Material', 'textdomain'),
		'update_item'       => __('Update Material', 'textdomain'),
		'add_new_item'      => __('Add New Material', 'textdomain'),
		'new_item_name'     => __('New Material Name', 'textdomain'),
		'menu_name'         => __('Material', 'textdomain'),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array('slug' => 'product_material'),
	);

	register_taxonomy('product_material', array('product'), $args);
}

add_action('init', 'create_material_taxonomy');

/*woocommerce*/
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

	// Cart count
	ob_start();
?>
	<span class="count cart-count">
		<?php echo WC()->cart->get_cart_contents_count(); ?>
	</span>
	<?php
	$fragments['.cart-count'] = ob_get_clean();


	// Mini cart
	ob_start();
	?>
	<div class="wrap widget_shopping_cart_content">
		<?php woocommerce_mini_cart(); ?>
	</div>
<?php

	$fragments['.widget_shopping_cart_content'] = ob_get_clean();


	return $fragments;
});

/**
 * WooCommerce Cart Expiry - Empty cart after 15 minutes
 */

/* -----------------------------------------
 * Add AJAX endpoint
 * ----------------------------------------- */
add_action('wp_ajax_custom_expire_cart', 'custom_expire_cart');
add_action('wp_ajax_nopriv_custom_expire_cart', 'custom_expire_cart');

function custom_expire_cart()
{

	if (WC()->cart) {
		WC()->cart->empty_cart();
	}

	wp_send_json_success(
		array(
			'message' => 'Your cart has expired.',
		)
	);
}


/* -----------------------------------------
 * Cart expiry message
 * ----------------------------------------- */
add_action('woocommerce_before_cart', 'custom_cart_expiry_timer');

function custom_cart_expiry_timer()
{

	if (! WC()->cart || WC()->cart->is_empty()) {
		return;
	}

?>

	<div class="tf-cart-sold">
		<div class="notification-sold bg-surface">
			<img class="icon" src="<?php echo get_template_directory_uri(); ?>/assets/icon/fire.svg" alt="Icon">
			<div class="custom-cart-expiry-message">
				Your cart will expire in
				<strong id="cart-expiry-timer">15:00</strong> minutes!
				Please checkout now before your items sell out!
			</div>
		</div>
		<div class="notification-progress">
			<div class="text">
				<!-- <i class="icon icon-truck"></i> -->
				<p class="h6">
					Free Shipping for orders over <span class="text-primary fw-bold">₹1500</span>
				</p>
			</div>
			<div class="progress_cart_onlyview progress-cart">
				<div class="value" style="width: 50%;" data-progress="50">
					<span class="round"></span>
				</div>
			</div>
		</div>
	</div>
<?php
}


/* -----------------------------------------
 * Timer JavaScript
 * ----------------------------------------- */
add_action('wp_footer', 'custom_cart_expiry_timer_script');

function custom_cart_expiry_timer_script()
{

	if (! is_cart()) {
		return;
	}

?>
	<script>
		jQuery(function($) {

			const CART_DURATION = 150 * 60 * 1000; // 15 minutes
			const STORAGE_KEY = 'wc_cart_expiry_time';

			let expiryTime = localStorage.getItem(STORAGE_KEY);

			/*
			 * Create timer if it doesn't exist
			 */
			if (!expiryTime) {

				expiryTime = Date.now() + CART_DURATION;

				localStorage.setItem(
					STORAGE_KEY,
					expiryTime
				);
			}

			expiryTime = parseInt(expiryTime, 10);

			let timerInterval = null;
			let cartExpired = false;


			/*
			 * Format timer
			 */
			function formatTime(seconds) {

				const minutes = Math.floor(seconds / 60);
				const secs = seconds % 60;

				return String(minutes).padStart(2, '0') + ':' +
					String(secs).padStart(2, '0');
			}


			/*
			 * Empty WooCommerce cart
			 */
			function expireCart() {

				if (cartExpired) {
					return;
				}

				cartExpired = true;

				clearInterval(timerInterval);

				$('#cart-expiry-timer').text('00:00');

				$('.custom-cart-expiry-message')
					.addClass('expired')
					.html(
						'Your cart has expired! Removing your items...'
					);


				/*
				 * WooCommerce AJAX
				 */
				$.ajax({
					type: 'POST',
					url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
					data: {
						action: 'custom_expire_cart'
					},
					success: function(response) {

						/*
						 * Remove timer
						 */
						localStorage.removeItem(STORAGE_KEY);


						/*
						 * Refresh WooCommerce fragments
						 */
						$(document.body).trigger(
							'wc_fragment_refresh'
						);


						/*
						 * Update cart page
						 */
						$(document.body).trigger(
							'updated_wc_div'
						);


						/*
						 * Redirect after short delay
						 */
						setTimeout(function() {

							window.location.href =
								'<?php echo esc_url(wc_get_cart_url()); ?>';

						}, 1000);
					},

					error: function() {

						/*
						 * Try again if AJAX fails
						 */
						cartExpired = false;

					}
				});
			}


			/*
			 * Update countdown
			 */
			function updateTimer() {

				const remaining =
					Math.max(
						0,
						Math.floor(
							(expiryTime - Date.now()) / 1000
						)
					);


				$('#cart-expiry-timer').text(
					formatTime(remaining)
				);


				/*
				 * Timer finished
				 */
				if (remaining <= 0) {

					expireCart();

				}
			}


			/*
			 * Start timer
			 */
			updateTimer();

			timerInterval = setInterval(
				updateTimer,
				1000
			);

		});
	</script>
<?php
}
remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

add_action('woocommerce_checkout_after_customer_details', 'woocommerce_checkout_payment', 20);

add_filter('woocommerce_order_button_text', function ($button_text) {
	return 'Payment';
});

/**
 * Add Buy It Now button after Add to Cart button.
 */
add_action('woocommerce_after_add_to_cart_button',	'custom_buy_now_button');

function custom_buy_now_button()
{

	global $product;

	// Product check.
	if (! $product instanceof WC_Product) {
		return;
	}

	// Product must be purchasable.
	if (! $product->is_purchasable()) {
		return;
	}

	// Product must be in stock.
	if (! $product->is_in_stock()) {
		return;
	}

?>

	<button
		type="submit"
		name="buy_now"
		value="1"
		class="tf-btn btn-primary custom-buy-now">
		<?php esc_html_e('BUY IT NOW', 'your-textdomain'); ?>
	</button>

<?php
}


/**
 * Redirect Buy It Now directly to checkout.
 *
 */
add_filter('woocommerce_add_to_cart_redirect',	'custom_buy_now_redirect');

function custom_buy_now_redirect($url)
{

	if (
		isset($_POST['buy_now']) &&
		'1' === $_POST['buy_now']
	) {
		return wc_get_checkout_url();
	}

	return $url;
}
add_action('woocommerce_after_add_to_cart_form', 'custom_product_after_cart', 20);

function custom_product_after_cart()
{

	if (!is_product()) {
		return;
	}

?>
	<div class="product-after-cart">

		<div class="tf-product-extra-link">
			<div class="product-extra-icon woosw-btn-wrapper">
				<?php echo do_shortcode('[woosw id="' . get_the_ID() . '"]'); ?>
			</div>

			<div class="product-extra-icon woosc-btn-wrapper">
				<?php echo do_shortcode('[woosc id="' . get_the_ID() . '"]'); ?>
			</div>

			<a href="#askQuestion" data-bs-toggle="modal" class="product-extra-icon link">
				<i class="icon icon-ques"></i>Ask a question
			</a>

			<a href="#shipAndDelivery" data-bs-toggle="modal" class="product-extra-icon link">
				<i class="icon icon-truck"></i>Delivery &amp; Return
			</a>
		</div>

		<div class="tf-product-delivery-return">

			<div class="product-delivery">
				<div class="icon icon-clock-cd"></div>
				<p class="h6">
					Estimate delivery times:
					<span class="fw-7 text-black">7-20 days</span>
				</p>
			</div>

			<div class="product-delivery return">
				<div class="icon icon-compare"></div>
				<p class="h6">
					Return within
					<span class="fw-7 text-black">10 days</span>
					of purchase.
					Duties &amp; taxes are non-refundable.
				</p>
			</div>

		</div>

		<div class="tf-product-trust-seal">

			<p class="h6 text-seal">
				Guarantee Safe Checkout:
			</p>

			<ul class="list-card">

				<li class="card-item">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment/visa.png" alt="Visa">
				</li>

				<li class="card-item">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment/master-card.png" alt="MasterCard">
				</li>

				<li class="card-item">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment/paypal.png" alt="PayPal">
				</li>

			</ul>

		</div>

	</div>
<?php
}

// Remove Reviews tab
add_filter('woocommerce_product_tabs', function ($tabs) {
	unset($tabs['reviews']);
	return $tabs;
}, 99);

// Move Reviews after Related Products
// add_action( 'woocommerce_after_single_product_summary', function() {
//     if ( comments_open() || get_comments_number() ) {
//         comments_template();
//     }
// }, 40 );


add_action('woocommerce_after_single_product_summary', 'custom_product_reviews_slider', 50);

function custom_product_reviews_slider()
{
	$product_id = get_queried_object_id();
	$reviews = get_comments(array(
		'status'      => 'approve',
		'post_id'     => $product_id,
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
	));

	// if ( empty( $reviews ) ) {
	//     return;
	// }
?>

	<section class="flat-spacing">
		<div class="container">

			<?php if (empty($reviews)) : ?>

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

						<?php foreach ($reviews as $review) :

							$product = wc_get_product($review->comment_post_ID);

							if (! $product) {
								continue;
							}

							$rating = (int) get_comment_meta(
								$review->comment_ID,
								'rating',
								true
							);

							$author = $review->comment_author;
							$content = $review->comment_content;

							$verified = wc_review_is_from_verified_owner($review->comment_ID);

							$image = wp_get_attachment_image_url(
								$product->get_image_id(),
								'woocommerce_thumbnail'
							);

							if (! $image) {
								$image = wc_placeholder_img_src();
							}
						?>

							<div class="swiper-slide">

								<div class="testimonial-V02 type-space-2 hover-img wow fadeInUp">

									<!-- Product -->
									<div class="tes_product">

										<div class="product-image img-style">
											<a href="<?php echo esc_url($product->get_permalink()); ?>">

												<img
													class="lazyload"
													src="<?php echo esc_url($image); ?>"
													data-src="<?php echo esc_url($image); ?>"
													alt="<?php echo esc_attr($product->get_name()); ?>">

											</a>
										</div>

										<div class="product-infor">

											<h5 class="prd_name fw-normal">

												<a
													href="<?php echo esc_url($product->get_permalink()); ?>"
													class="link">
													<?php echo esc_html($product->get_name()); ?>
												</a>

											</h5>

											<h6 class="prd_price">
												<?php echo wp_kses_post($product->get_price_html()); ?>
											</h6>

										</div>

									</div>

									<!-- Review -->
									<div class="tes_content">

										<div class="tes_icon">
											<i class="icon icon-block-quote"></i>
										</div>

										<h4 class="tes_title">
											<?php echo esc_html(wp_trim_words($content, 4, '')); ?>
										</h4>

										<p class="tes_text h4">
											“<?php echo esc_html($content); ?>”
										</p>

										<div class="tes_author">

											<p class="author-name h4">
												<?php echo esc_html($author); ?>
											</p>

											<?php if ($verified) : ?>
												<i class="author-verified icon-check-circle fs-24"></i>
											<?php endif; ?>

										</div>

										<div class="rate_wrap">

											<?php for ($i = 1; $i <= 5; $i++) : ?>

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


			<?php if (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())) : ?>
				<div id="review_form_wrapper">
					<div id="review_form">
						<?php
						$commenter    = wp_get_current_commenter();
						$comment_form = array(
							/* translators: %s is product title */
							'title_reply'         => have_comments() ? esc_html__('Add a review', 'woocommerce') : sprintf(esc_html__('Be the first to review &ldquo;%s&rdquo;', 'woocommerce'), get_the_title()),
							/* translators: %s is product title */
							'title_reply_to'      => esc_html__('Leave a Reply to %s', 'woocommerce'),
							'title_reply_before'  => '<span id="reply-title" class="comment-reply-title" role="heading" aria-level="3">',
							'title_reply_after'   => '</span>',
							'comment_notes_after' => '',
							'label_submit'        => esc_html__('Submit', 'woocommerce'),
							'logged_in_as'        => '',
							'comment_field'       => '',
						);

						$name_email_required = (bool) get_option('require_name_email', 1);
						$fields              = array(
							'author' => array(
								'label'        => __('Name', 'woocommerce'),
								'type'         => 'text',
								'value'        => $commenter['comment_author'],
								'required'     => $name_email_required,
								'autocomplete' => 'name',
							),
							'email'  => array(
								'label'        => __('Email', 'woocommerce'),
								'type'         => 'email',
								'value'        => $commenter['comment_author_email'],
								'required'     => $name_email_required,
								'autocomplete' => 'email',
							),
						);

						$comment_form['fields'] = array();

						foreach ($fields as $key => $field) {
							$field_html  = '<p class="comment-form-' . esc_attr($key) . '">';
							$field_html .= '<label for="' . esc_attr($key) . '">' . esc_html($field['label']);

							if ($field['required']) {
								$field_html .= '&nbsp;<span class="required">*</span>';
							}

							$field_html .= '</label><input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($field['type']) . '" autocomplete="' . esc_attr($field['autocomplete']) . '" value="' . esc_attr($field['value']) . '" size="30" ' . ($field['required'] ? 'required' : '') . ' /></p>';

							$comment_form['fields'][$key] = $field_html;
						}

						$account_page_url = wc_get_page_permalink('myaccount');
						if ($account_page_url) {
							/* translators: %s opening and closing link tags respectively */
							$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(esc_html__('You must be %1$slogged in%2$s to post a review.', 'woocommerce'), '<a href="' . esc_url($account_page_url) . '">', '</a>') . '</p>';
						}

						if (wc_review_ratings_enabled()) {
							$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating" id="comment-form-rating-label">' . esc_html__('Your rating', 'woocommerce') . (wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '') . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
						<option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
						<option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
						<option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
						<option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
						<option value="1">' . esc_html__('Very poor', 'woocommerce') . '</option>
					</select></div>';
						}

						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Your review', 'woocommerce') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

						comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
						?>
					</div>
				</div>
			<?php else : ?>
				<p class="woocommerce-verification-required"><?php esc_html_e('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'); ?></p>
			<?php endif; ?>

			<div class="clear"></div>

		</div>
	</section>

<?php
}



   
// add_action( 'template_redirect', function() {

//     if ( ! is_user_logged_in() ) {
//         return;
//     }

//     if ( is_page( 'login' ) || is_page( 'register' ) ) {
//         wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
//         exit;
//     }

// } );

/**
 * AJAX Registration
 */
add_action('wp_ajax_nopriv_custom_ajax_register', 'custom_ajax_register');
add_action('wp_ajax_custom_ajax_register', 'custom_ajax_register');

function custom_ajax_register()
{

	// -----------------------------
	// Get values
	// -----------------------------

	$email           = isset($_POST['user_email'])
		? sanitize_email(wp_unslash($_POST['user_email']))
		: '';

	$password        = isset($_POST['password'])
		? (string) wp_unslash($_POST['password'])
		: '';

	$confirm_password = isset($_POST['confirm_password'])
		? (string) wp_unslash($_POST['confirm_password'])
		: '';

	// -----------------------------
	// Validate email
	// -----------------------------

	if (empty($email)) {

		wp_send_json_error(
			array(
				'message' => 'Please enter your email address.',
			)
		);
	}

	if (! is_email($email)) {

		wp_send_json_error(
			array(
				'message' => 'Please enter a valid email address.',
			)
		);
	}


	// -----------------------------
	// Check email exists
	// -----------------------------

	if (email_exists($email)) {

		wp_send_json_error(
			array(
				'message' => 'An account already exists with this email address.',
			)
		);
	}


	// -----------------------------
	// Validate password
	// -----------------------------

	if (empty($password)) {

		wp_send_json_error(
			array(
				'message' => 'Please enter a password.',
			)
		);
	}


	if (strlen($password) < 6) {

		wp_send_json_error(
			array(
				'message' => 'Password must be at least 6 characters.',
			)
		);
	}


	// -----------------------------
	// Confirm password
	// -----------------------------

	if ($password !== $confirm_password) {

		wp_send_json_error(
			array(
				'message' => 'Passwords do not match.',
			)
		);
	}


	// -----------------------------
	// Create username from email
	// -----------------------------

	$email_parts = explode('@', $email);

	$username = isset($email_parts[0])
		? sanitize_user($email_parts[0], true)
		: 'user';


	// Fallback username
	if (empty($username)) {
		$username = 'user';
	}


	// -----------------------------
	// Make username unique
	// -----------------------------

	$original_username = $username;
	$counter = 1;

	while (username_exists($username)) {

		$username = $original_username . $counter;

		$counter++;
	}


	// -----------------------------
	// Debug log
	// -----------------------------

	error_log('REGISTER EMAIL: ' . $email);
	error_log('REGISTER USERNAME: ' . $username);


	// -----------------------------
	// Check WooCommerce function
	// -----------------------------

	if (! function_exists('wc_create_new_customer')) {

		wp_send_json_error(
			array(
				'message' => 'WooCommerce is not available.',
			)
		);
	}


	// -----------------------------
	// Create WooCommerce customer
	// -----------------------------

	$user_id = wc_create_new_customer(
		$email,
		$username,
		$password
	);


	// -----------------------------
	// Check error
	// -----------------------------

	if (is_wp_error($user_id)) {

		error_log(
			'REGISTER ERROR: ' . $user_id->get_error_message()
		);

		wp_send_json_error(
			array(
				'message' => $user_id->get_error_message(),
			)
		);
	}


	// -----------------------------
	// Check user ID
	// -----------------------------

	if (! $user_id) {

		wp_send_json_error(
			array(
				'message' => 'Unable to create your account.',
			)
		);
	}


	// -----------------------------
	// Auto login
	// -----------------------------

	wp_set_auth_cookie($user_id, true);

	wp_set_current_user($user_id);


	// -----------------------------
	// My Account URL
	// -----------------------------

	$redirect_url = wc_get_page_permalink('myaccount');


	// -----------------------------
	// Success response
	// -----------------------------

	wp_send_json_success(
		array(
			'message'  => 'Registration successful.',
			'redirect' => $redirect_url,
		)
	);
}


add_action('wp_ajax_nopriv_custom_ajax_login', 'custom_ajax_login');
add_action('wp_ajax_custom_ajax_login', 'custom_ajax_login');

function custom_ajax_login()
{


	$username = sanitize_user(wp_unslash($_POST['username'] ?? ''));
	$password = $_POST['password'] ?? '';
	$remember = !empty($_POST['remember']);

	if (!$username || !$password) {
		wp_send_json_error([
			'message' => 'Please enter your email and password.'
		]);
	}

	// Allow email login
	if (is_email($username)) {
		$user = get_user_by('email', $username);

		if ($user) {
			$username = $user->user_login;
		}
	}

	$credentials = [
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => $remember,
	];

	$user = wp_signon($credentials, is_ssl());

	if (is_wp_error($user)) {
		wp_send_json_error([
			'message' => 'Invalid email or password.'
		]);
	}

	wp_set_current_user($user->ID);
	wp_set_auth_cookie($user->ID, $remember);

	wp_send_json_success([
		'message'  => 'Login successful.',
		'redirect' => wc_get_page_permalink('myaccount'),
	]);
}


add_action('acf/init', function () {
	if (function_exists('acf_add_options_page')) {

		acf_add_options_page(array(
			'page_title'    => 'Theme General Settings',
			'menu_title'    => 'Theme Settings',
			'menu_slug'     => 'theme-general-settings',
			'capability'    => 'edit_posts',
			'redirect'      => false
		));

		acf_add_options_sub_page(array(
			'page_title'    => 'Theme Header Settings',
			'menu_title'    => 'Header',
			'parent_slug'   => 'theme-general-settings',
		));

		acf_add_options_sub_page(array(
			'page_title'    => 'Theme Footer Settings',
			'menu_title'    => 'Footer',
			'parent_slug'   => 'theme-general-settings',
		));
		acf_add_options_sub_page(array(
			'page_title'    => 'Theme Product Settings',
			'menu_title'    => 'Product',
			'parent_slug'   => 'theme-general-settings',
		));
	}
});

add_filter('woocommerce_product_tabs', 'custom_product_tab');

function custom_product_tab($tabs)
{

	$tabs['shipping_return_refund'] = array(
		'title'    => 'Shipping, Return & Refund Policy',
		'priority' => 30,
		'callback' => 'custom_shipping_return_refund_content',
	);
	$tabs['faqs'] = array(
		'title'    => 'FAQs',
		'priority' => 30,
		'callback' => 'custom_faqs_content',
	);

	return $tabs;
}
function custom_faqs_content()
{?>
<div class="tab-pane tab-faqs-descriptions active show">
                        <ul>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Type of diamond used?</div>
                                                    <div class="h6 fw-4 text">Natural diamonds with the highest ododj
                                                        purity</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Is the product unisex?</div>
                                                    <div class="h6 fw-4 text">No</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">What ring sizes are available?
                                                    </div>
                                                    <div class="h6 fw-4 text">Sizes 5-26; Any size not available online
                                                        can be customized.</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Product Finish</div>
                                                    <div class="h6 fw-4 text">High Polish</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Does the product cost include
                                                        GST?</div>
                                                    <div class="h6 fw-4 text">Yes</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">What % of GST is applicable on
                                                        the product?</div>
                                                    <div class="h6 fw-4 text">2%</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Does the product cost include
                                                        shipping?</div>
                                                    <div class="h6 fw-4 text">Yes</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Does the product cost include
                                                        product discounts?</div>
                                                    <div class="h6 fw-4 text">Yes. However, any applicable coupon can be
                                                        applied at the time of
                                                        payment.</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Are there any other hidden
                                                        costs?</div>
                                                    <div class="h6 fw-4 text">No there are no hidden costs or additional
                                                        charges.</div>
                                                </li>
                                                <li>
                                                    <div class="h6 fw-6 text-black title">Is there a price breakup
                                                        available for the product price?
                                                    </div>
                                                    <div class="h6 fw-4 text">Yes, same is available in the price
                                                        break-up section.</div>
                                                </li>
                                            </ul>
			</div>
<?php
}

function custom_shipping_return_refund_content()
{	?>
	<div class="mb_32">
		<div class="h6"><?php echo get_field('returns_refunds', 'option'); ?></div>
	</div>
	<div class="mb_32">
		<div class="h6"><?php echo get_field('shipping_data', 'option'); ?></div>
	</div>
<?php }

add_filter( 'woocommerce_add_to_cart_validation', 'only_logged_in_can_buy', 10, 3 );
function only_logged_in_can_buy( $passed, $product_id, $quantity ) {
    // Check if user is not logged in
    if ( ! is_user_logged_in() ) {
        // Display an error message
        wc_add_notice( 'You must be logged in to purchase products.', 'error' );
        // Prevent adding to cart
        $passed = false;
    }
    return $passed;
}



class Mobile_Menu_Walker extends Walker_Nav_Menu {

    private $dropdown_id = 0;

    // Start <ul>
    public function start_lvl(&$output, $depth = 0, $args = null) {

        if ($depth === 0) {
            $id = 'dropdown-menu-' . $this->dropdown_id;

            $output .= '<div id="' . esc_attr($id) . '" class="collapse">';
            $output .= '<ul class="sub-nav-menu">';
        } else {
            $output .= '<ul class="sub-nav-menu">';
        }
    }

    // End <ul>
    public function end_lvl(&$output, $depth = 0, $args = null) {

        if ($depth === 0) {
            $output .= '</ul>';
            $output .= '</div>';
        } else {
            $output .= '</ul>';
        }
    }

    // Start <li>
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {

        $has_children = in_array(
            'menu-item-has-children',
            $item->classes
        );

        if ($depth === 0) {

            $output .= '<li class="nav-mb-item">';

            if ($has_children) {

                $dropdown_id = 'dropdown-menu-' . $this->dropdown_id;

                $output .= '<a href="#' . esc_attr($dropdown_id) . '" 
                    class="collapsed mb-menu-link" 
                    data-bs-toggle="collapse" 
                    aria-expanded="false" 
                    aria-controls="' . esc_attr($dropdown_id) . '">';

                $output .= '<span>' . esc_html($item->title) . '</span>';

                $output .= '<span class="icon icon-caret-down"></span>';

                $output .= '</a>';

                $this->dropdown_id++;

            } else {

                $output .= '<a href="' . esc_url($item->url) . '" 
                    class="mb-menu-link">';

                $output .= '<span>' . esc_html($item->title) . '</span>';

                $output .= '</a>';
            }

        } else {

            $output .= '<li>';

            $output .= '<a href="' . esc_url($item->url) . '" 
                class="sub-nav-link">';

            $output .= esc_html($item->title);

            $output .= '</a>';
        }
    }

    // End <li>
    public function end_el(&$output, $item, $depth = 0, $args = null) {

        $output .= '</li>';
    }
}

add_action('template_redirect', function () {

    // If user is logged in
    if (is_user_logged_in()) {

        // Prevent access to Login page
        if (is_page('login')) {
            wp_safe_redirect(home_url('/my-account/'));
            exit;
        }

        // Prevent access to Register page
        if (is_page('register')) {
            wp_safe_redirect(home_url('/my-account/'));
            exit;
        }
    }

});


add_action('wp_ajax_update_profile_image', 'update_profile_image');

function update_profile_image() {

    // User must be logged in
    if ( ! is_user_logged_in() ) {
        wp_send_json_error('You must be logged in.');
    }

    // Verify nonce
    if (
        ! isset($_POST['nonce']) ||
        ! wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['nonce'])),
            'update_profile_image'
        )
    ) {
        wp_send_json_error('Security check failed.');
    }

    if ( empty($_FILES['profile_image']) ) {
        wp_send_json_error('Please select an image.');
    }

    $file = $_FILES['profile_image'];

    // Validate image
    $check = wp_check_filetype_and_ext(
        $file['tmp_name'],
        $file['name']
    );

    $allowed_types = array(
        'jpg',
        'jpeg',
        'png',
        'webp'
    );

    if (
        empty($check['ext']) ||
        ! in_array($check['ext'], $allowed_types, true)
    ) {
        wp_send_json_error('Invalid image type.');
    }

    // Upload to Media Library
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attachment_id = media_handle_upload(
        'profile_image',
        0
    );

    if ( is_wp_error($attachment_id) ) {
        wp_send_json_error($attachment_id->get_error_message());
    }

    $user_id = get_current_user_id();

    // Save attachment ID against user
    update_user_meta(
        $user_id,
        'profile_image_id',
        $attachment_id
    );

    $image_url = wp_get_attachment_image_url(
        $attachment_id,
        'thumbnail'
    );

    wp_send_json_success(array(
        'url' => $image_url,
        'attachment_id' => $attachment_id
    ));
}