<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package jewellery_wp
 */

?>

        <!-- Footer -->
        <footer class="tf-footer style-color-white bg-black">
            <div class="footer-body">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-sm-6 mb_30 mb-xl-0">
                            <div class="footer-infor">
                                <a href="<?php echo home_url('/'); ?>" class="logo-site">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logoftr.png" alt="">
                                </a>
                                <ul class="footer-contact mb-0">
                                    <li>
                                        <i class="icon icon-map-pin"></i>
                                        <span class="br-line"></span>
                                        <a href="https://www.google.com/maps?q=8500+Lorem+Street+Chicago,+IL+55030+Dolor+sit+amet" target="_blank" class="h6 link text-main">
                                            Address Goes Here, Address Goes Here
                                        </a>
                                    </li>
                                    <li>
                                        <i class="icon icon-phone"></i>
                                        <span class="br-line"></span>
                                        <a href="tel:+0000000000" class="h6 link text-main">+91 00000 00000</a>
                                    </li>
                                    <li>
                                        <i class="icon icon-envelope-simple"></i>
                                        <span class="br-line"></span>
                                        <a href="mailto:demo@gmail.com" class="h6 link text-main">demo@gmail.com</a>
                                    </li>
                                </ul>
                                <ul class="tf-social-icon_2">
                                    <li>
                                        <a href="https://www.facebook.com/" target="_blank" class="link text-white">
                                            <i class="icon-fb"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.instagram.com/" target="_blank" class="link text-white">
                                            <i class="icon-instagram-logo"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://x.com/" target="_blank" class="link text-white">
                                            <i class="icon-x"></i>
                                        </a>
                                    </li> 
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 mb_30 mb-xl-0">
                            <div class="footer-col-block">
                                <p class="footer-heading footer-heading-mobile">Shopping</p>
                                <div class="tf-collapse-content">
                                    <ul class="footer-menu-list">
                                        <li><a href="#" class="link h6">Shipping</a></li>
                                        <li><a href="#" class="link h6">Shop by Brand</a></li>
                                        <li><a href="#" class="link h6">Track order</a></li>
                                        <li><a href="#" class="link h6">Terms & Conditions</a></li> 
                                        <li><a href="<?php echo esc_url(home_url('/wishlist')); ?>" class="link h6">My Wishlist</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 mb_30 mb-sm-0 mbnoneres">
                            <div class="footer-col-block">
                                <p class="footer-heading footer-heading-mobile">Information</p>
                                <div class="tf-collapse-content">
                                    <ul class="footer-menu-list">
                                        <!-- <li><a href="#" class="link h6">About Us</a></li> -->
                                        <li><a href="#" class="link h6">Term & Policy</a></li>
                                        <li><a href="#" class="link h6">Help Center</a></li>
                                        <li><a href="#" class="link h6">Refunds</a></li> 
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="Newsletter_ftrlast footer-col-block">
                                <p class="footer-heading footer-heading-mobile">Newsletter</p>
                                <div class="tf-collapse-content">
                                    <div class="footer-newsletter">
                                        <p class="h6 caption">
                                            Become the first to know about offers, new collections trends.
                                        </p>
                                        <div class="sib-form sib-form_footer">
                                            <div id="sib-form-container" class="sib-form-container">  
                                                <div id="sib-container" class="sib-container--large sib-container--vertical ">
                                                    <form id="sib-form" method="POST" action="" data-type="subscription">
                                                        <div>
                                                            <div class="sib-form-block">
                                                                <p></p>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="sib-form-block">
                                                                <div class="sib-text-form-block">
                                                                    <p></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-content_fieldset form-get_email">
                                                            <div class="fieldset-input_email">
                                                                <div class="sib-input sib-form-block">
                                                                    <div class="form__entry entry_block">
                                                                        <div class="form__label-row ">
                                                                            <label class="entry__label d-none" for="EMAIL"></label>
                                                                            <div class="entry__field ip">
                                                                                <input class="input style-stroke-2" type="email" id="EMAIL" name="EMAIL" autocomplete="off" placeholder="Enter your email" data-required="true" required>
                                                                            </div>
                                                                        </div>
                                                                        <label class="entry__error entry__error--primary"></label>
                                                                        <label class="entry__specification"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="fiedset-button_submit">
                                                                <div class="sib-form-block">
                                                                    <button class="sib-form-block__button sib-form-block__button-with-loader tf-btn btn-white animate-btn animate-dark type-small-2" form="sib-form" type="submit">
                                                                        <svg class="icon clickable__icon progress-indicator__icon sib-hide-loader-icon" viewBox="0 0 512 512">
                                                                            <path d="M460.116 373.846l-20.823-12.022c-5.541-3.199-7.54-10.159-4.663-15.874 30.137-59.886 28.343-131.652-5.386-189.946-33.641-58.394-94.896-95.833-161.827-99.676C261.028 55.961 256 50.751 256 44.352V20.309c0-6.904 5.808-12.337 12.703-11.982 83.556 4.306 160.163 50.864 202.11 123.677 42.063 72.696 44.079 162.316 6.031 236.832-3.14 6.148-10.75 8.461-16.728 5.01z">
                                                                            </path>
                                                                        </svg>
                                                                        Subscribe
                                                                        <i class="icon icon-arrow-right"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="">
                                                            <div class="sib-optin sib-form-block">
                                                                <div class="form__entry entry_mcq">
                                                                    <div class="form__label-row ">
                                                                        <div class="entry__choice checkbox-wrap">
                                                                            <input type="checkbox" class="input_replaced tf-check style-3 style-white"
                                                                                value="1" id="OPT_IN" name="OPT_IN">
                                                                            <label for="OPT_IN" class="h6 text-main-5">
                                                                                By clicking subcribe, you agree to the
                                                                                <a href="#"
                                                                                    class="text-decoration-underline link text-main-5">Terms
                                                                                    of Service</a> and <a href="#"
                                                                                    class="text-decoration-underline link text-main-5">
                                                                                    Privacy Policy</a>.
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <label class="entry__error entry__error--primary"></label>
                                                                    <label class="entry__specification"></label>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <input type="hidden" name="locale" value="en">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="inner-bottom">
                        <ul class="list-hor">
                            <li>
                                <a href="#" class="h6 link text-main">Help</a>
                            </li>
                            <li class="br-line type-vertical"></li>
                            <li>
                                <a href="#" class="h6 link text-main">FAQs</a>
                            </li>
                        </ul>
                        <div class="list-hor flex-wrap">
                            <span class="h6">Payment:</span>
                            <ul class="payment-method-list">
                                <li><img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/visa-2.svg" alt="Payment"></li>
                                <li><img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/master-card-2.svg" alt="Payment"></li>  
                                <li><img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/paypal-2.svg" alt="Payment"></li>
                            </ul>
                        </div>
                        <div class="list-hor">
                            <!-- <div class="tf-currencies">
                                <select class="tf-dropdown-select style-default color-white-2 type-currencies">
                                    <option selected data-thumbnail="<?php //echo get_template_directory_uri(); ?>/assets/images/country/ind.png">IND</option>  
                                </select>
                            </div> -->
                            <!-- <span class="br-line type-vertical"></span>
                            <div class="tf-languages">
                                <?php // echo do_shortcode( '[psm-multi-currency-switcher size ="medium" flag="1" name="1" symbol="1" code="1"]'); ?>
                            
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /Footer -->
        
    </main>
    
    <!-- Mobile Menu -->
    <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
        <span class="icon-close-popup" data-bs-dismiss="offcanvas">
            <i class="icon-close"></i>
        </span>
        <div class="canvas-header">
            <p class="text-logo-mb">LOGO</p>
            <a href="<?php echo home_url('/login'); ?>" class="tf-btn type-small style-2">
                Login
                <i class="icon icon-user"></i>
            </a>
            <span class="br-line"></span>
        </div>
        <div class="canvas-body">
            <div class="mb-content-top">
                <!-- <ul class="nav-ul-mb" id="wrapper-menu-navigation1"></ul> -->
                <?php
								wp_nav_menu(
									array(
										'theme_location' => 'primary',
										'container'      => false,
										'menu_class'     => 'nav-ul-mb',
										'fallback_cb'    => false,
                                        'menu_id'        => 'wrapper-menu-navigation2',
                                        'walker'         => new Mobile_Menu_Walker(),
									)
								);
								?>   
            </div>
            <div class="group-btn">
                <a href="<?php echo esc_url(home_url('/wishlist')); ?>" class="tf-btn type-small style-2 woosw-show">
                    Wishlist
                    <i class="icon icon-heart"></i>
                </a> 
            </div>
            <div class="flow-us-wrap">
                <h5 class="title heading_cmn_white">Follow us on</h5>
                <ul class="tf-social-icon">
                    <li>
                        <a href="https://www.facebook.com/" target="_blank" class="social-facebook">
                            <span class="icon"><i class="icon-fb"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/" target="_blank" class="social-instagram">
                            <span class="icon"><i class="icon-instagram-logo"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://x.com/" target="_blank" class="social-x">
                            <span class="icon"><i class="icon-x"></i></span>
                        </a>
                    </li> 
                </ul>
            </div>
            <div class="payment-wrap">
                <h5 class="title heading_cmn_white">Payment:</h5>
                <ul class="payment-method-list">
                    <li><img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/visa.png" alt="Payment"></li>
                    <li><img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/master-card.png" alt="Payment"></li> 
                    <li><img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/paypal.png" alt="Payment"></li>
                </ul>
            </div>
        </div> 
    </div>
    <!-- /Mobile Menu -->

    <!-- Toolbar -->
    <div class="tf_mobtoolbar_bottom tf-toolbar-bottom">
        <div class="toolbar-item">
            <a href="<?php echo home_url('/shop'); ?>">
                <span class="toolbar-icon">
                    <i class="icon icon-storefront"></i>
                </span>
                <span class="toolbar-label">Shop</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="#search" data-bs-toggle="modal">
                <span class="toolbar-icon">
                    <i class="icon icon-magnifying-glass"></i>
                </span>
                <span class="toolbar-label">Search</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="<?php echo home_url('/my-account'); ?>">
                <span class="toolbar-icon">
                    <i class="icon icon-user"></i>
                </span>
                <span class="toolbar-label">Account</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="<?php echo esc_url(home_url('/wishlist')); ?>" class="woosw-show">
                <span class="toolbar-icon">
                    <i class="icon icon-heart"></i>
                    <span class="toolbar-count yith-wcwl-icon woosw-count">0</span>
                </span>
                <span class="toolbar-label">Wishlist</span>
            </a>
        </div>
        <div class="toolbar-item">
            <a href="<?php echo home_url('/cart'); ?>">
                <span class="toolbar-icon">
                    <i class="icon icon-shopping-cart-simple"></i>
                    <span class="count cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </span>
                <span class="toolbar-label">Cart</span>
            </a>
        </div>
    </div>
    <!-- /Toolbar -->

    <!-- Search -->
    <div class="modal modalCentered fade modal-search" id="search">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <span class="icon-close icon-close-popup" data-bs-dismiss="modal"></span>
                <div>
                                                    
                                <?php echo do_shortcode(' [apsw_search_bar_preview]'); ?>
                </div> 
            </div>
        </div>
    </div>
    <!-- /Search -->

    <!-- Shopping Cart -->
    <div class="offcanvas offcanvas-end popup-shopping-cart" id="shoppingCart"> 
        <div class="canvas-wrapper">
            <div class="popup-header">
                <span class="title fw-semibold h4">Shopping cart</span>
                <span class="icon-close icon-close-popup" data-bs-dismiss="offcanvas"></span>
            </div>
            <div class="wrap widget_shopping_cart_content">
                
        <?php woocommerce_mini_cart(); ?>
            </div>
        </div>
    </div>
    <!-- /Shopping Cart -->
<?php wp_footer(); ?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {

    $('.minus.qib-button').html('<i class="icon icon-minus"></i>');
    $('.plus.qib-button').html('<i class="icon icon-plus"></i>');

});

jQuery(function ($) {
    $('.single_add_to_cart_button').removeClass('single_add_to_cart_button button');
});
</script>

</body>
</html>
