<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package jewellery_wp
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>

    <style type="text/css">
        


body .qib-button-wrapper{
      display: flex;
    justify-content: center;
    border: 1px solid #f2c9323b;
    padding: 7px 0;
    border-radius: 6px;
    overflow: hidden;
    flex-grow: 1;
    min-width: 126px;
    max-width: 180px;
}

.qib-button-wrapper button.qib-button{
padding: 13px 12px 8px !important;
    font-weight: 400;
    font-size: 14px;
    min-width: auto;
    border: 0px !important;
}

.woocommerce div.product form.cart div.quantity{
    background-color: transparent;
}

#wpgs-gallery{
    height: 550px;
}





.woocommerce div.product .woocommerce-tabs ul.tabs::before{
    display: none;
}

.woocommerce div.product .woocommerce-tabs ul.tabs{
    border-bottom: 1px solid #ffffff36;
    padding: 0px;
    margin: 0px;
}



.woocommerce div.product .woocommerce-tabs ul.tabs li{
    border: 0px;
    border-radius: 0;
    background: transparent !important;
    margin: 0px;
    padding: 0px;
    position: relative;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li a{
    padding: 0 24px 16px;
    font-weight: 400;
    font-size: 32px;
    line-height: 100%;
    color: #ffffff;
    display: block;
    position: relative;
    text-align: center;
    white-space: nowrap;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover{
    color: #eac02c !important;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li:after{
        position: absolute;
    content: '';
    bottom: 0px;
    height: 2px;
    width: 0;
    left: 50%;
    transform: translateX(-50%);
    background-color: #eac02c !important;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
    border:0px;
}


.woocommerce div.product .woocommerce-tabs ul.tabs li::before{
    display: none;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li.active{
    background: transparent;
    color: #FFF;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li.active:after{
        width: 100%;
}

.woocommerce div.product .woocommerce-tabs .panel{
    background: #ffffff0a;
    margin-top: 48px;
}
.woocommerce div.product .woocommerce-tabs .panel h2{
    display: none;
}

h4.acf-label {
    margin-bottom: 16px;
}

.sect-title.type-2{
    margin-bottom: 16px;
}

.woocommerce #review_form #respond textarea{
    border-radius: 15px !important;
    margin-top: 7px;
     margin-bottom: 16px;
}
.woocommerce #review_form #respond p input{
     border-radius: 15px !important;
      margin-top: 7px;
       margin-bottom: 16px;
}

#review_form_wrapper{
    background: #ffffff0a;
    padding: 30px;
    margin-top: 32px;
}

.comment-form-rating {
    display: flex;
    column-gap: 25px;
    align-items: center;
    margin-top: 30px;
    margin-bottom: 30px;
}
.woocommerce #review_form #respond p{
    margin-bottom: 0px;
}

.sib-form .sib-form-container .input{
    background-color: transparent;
}

#add_payment_method table.cart td.actions .coupon .coupon-error-notice, .woocommerce-cart table.cart td.actions .coupon .coupon-error-notice, .woocommerce-checkout table.cart td.actions .coupon .coupon-error-notice{
    color: wheat;
    background: var(--wc-red);
    padding: 8px 20px;
    font-size: 14px;
}


.woocommerce form .form-row.woocommerce-invalid label{
    color: wheat;
    background: var(--wc-red);
    padding: 8px 20px;
     font-size: 14px;
      margin-bottom: 7px;
}

#add_payment_method .checkout .checkout-inline-error-message, .woocommerce-cart .checkout .checkout-inline-error-message, .woocommerce-checkout .checkout .checkout-inline-error-message{
 color: wheat;
    background: var(--wc-red);
    padding: 2px 20px;
     font-size: 12px;
     margin-top: 7px;
}


.input-radio{
    padding: 0 !important;
    position: relative;
    border: 1px solid #9a9a9a;
    border-radius: 50%;
    background: none;
    cursor: pointer;
    outline: 0;
    width: 22px;
        height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--white);
    -webkit-appearance: none;
}
.input-radio:checked {
    border-color: var(--black);
    background-color: var(--white);
}
.input-radio::before {
    content: "";
    position: absolute;
    border-radius: 50%;
            width: 16px;
        height: 16px;
    background-color: var(--black);
    opacity: 0;
}
.input-radio:checked::before {
    opacity: 1;
}


.woocommerce-shop .container .flat-spacing{
    padding-top: 0px !important;
}
.woof_remove_ppi {
    color: #f2c932;
    position: relative;
    padding-right: 16px !important;
    background: none !important;
    padding-bottom: 3px;
}

.woof_remove_ppi::after {
    content: "\00d7";
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    color: #f2c932;
    font-size: 20px;
    font-weight: 500;
    line-height: 1;
}


.woof_radio_term_reset_visible {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.woof_radio_term_reset_visible img {
    display: none !important;
}

.woof_radio_term_reset_visible::before {
    content: "\00d7";
    color: #f2c932;
    font-size: 20px;
    font-weight: 500;
    line-height: 1;
    display: inline-flex;
    vertical-align: middle;
    align-items: center;
    margin-top: 4px;
}

.card-product_wrapper img{
    object-fit: cover;
}

.woocommerce div.product form.cart div.quantity .qty {
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-bottom: 0 !important;
    margin-left: 0 !important;
    padding: 0px !important;
    border-radius: 0px !important;
    border: 0px !important;
    color: #FFF !important;
    background-color: transparent !important;
}

.qib-button-wrapper .quantity.wqpmb_quantity input.wqpmb_input_text.input-text.qty.text{
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-bottom: 0 !important;
    margin-left: 0 !important;
    padding: 0px !important;
    border-radius: 0px !important;
    border: 0px !important;
    color: #FFF !important;
    background-color: transparent !important;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li.active{
    border-bottom: 0px !important;
}

.woocommerce .star-rating::before, .woocommerce .star-rating span::before{
    color: #ef9122;
    font-weight: 400;
    font-size: 15px;
}

#add_payment_method #payment ul.payment_methods li input, .woocommerce-cart #payment ul.payment_methods li input, .woocommerce-checkout #payment ul.payment_methods li input{
    margin-bottom: -5px;
}


/* Hide default radio */
#shipping_method .shipping_method {
    position: absolute;
    opacity: 0;
    visibility: hidden;
}

/* Custom radio */
#shipping_method .shipping_method + label::before {
    content: "";
    width: 18px;
    height: 18px;
    min-width: 18px;
    border-radius: 50%;
    border: 2px solid #000;
    background: #fff;
    display: inline-block;
    box-sizing: border-box;
    margin: 0px 5px -4px 0;
}

/* Selected - inverted */
#shipping_method .shipping_method:checked + label::before {
    background: #000;
    border: 2px solid #fff;
}

#add_payment_method #payment div.payment_box p, .woocommerce-cart #payment div.payment_box p, .woocommerce-checkout #payment div.payment_box p{
color: #515151 !important
}


.shop_table.woocommerce-checkout-review-order-table .product-name img {
    max-width: 74px;
    margin-right: 15px;
}


a.added_to_cart.wc-forward {
    display: none;
}

.woosq-popup *, .woosq-sidebar *{
    color: #000;
}

.woosq-popup h2.product-info-name.heading_cmn_white {
    color: #000 !important;
}

.woosq-popup div.product form.cart .qib-button-wrapper div.quantity{
    filter: invert(1);
}

.woosq-popup .tf-btn .icon{
    color: #FFF !important;
}

.woosq-popup  button.single_add_to_cart_button, .woosq-popup .single_add_to_cart_button.btn-add-to-cart{
    vertical-align: middle;
    float: left;
    align-items: center !important;
    width: 210px;
    display: flex !important;
    padding: 0px !important;
}

.woosc_table a{
    color: #000;
}

.woosc-area .woosc-inner .woosc-table .woosc-table-inner .woosc-table-items table thead tr th .woosc-remove, .woosc-page .woosc-remove{
     color: #000;
}

.woosc-area .woosc-inner .woosc-table *{
    color: #000;
}

.woosw-popup .woosw-popup-inner .woosw-popup-content *{
     color: #000;
}

.woosw-popup .woosw-popup-inner .woosw-popup-content .woosw-popup-content-top *{
     color: #FFF;
}

.woocommerce .star-rating {
    height: 1.2em;
    width: 6.4em;
}

.woocommerce-checkout select.apsw-category-items{
    border: 0px !important;
}



    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>



    <!-- Scroll Top -->
    <button id="goTop">
        <span class="border-progress"></span>
        <span class="icon icon-caret-up"></span>
    </button>

    <!-- preload -->
    <div class="preload preload-container primary-2" id="preload">
        <div class="preload-logo">
            <div class="spinner"></div>
        </div>
    </div>
    <!-- /preload -->

    <main id="wrapper"> 
        <!-- Top Bar-->
        <div class="tf-topbar type-space-2 line-bt primary-2">
            <div class="container-full-2">
                <div class="row">
                    <div class="col-xl-7 col-lg-8">
                        <div class="topbar-left justify-content-center justify-content-sm-start">
                            <ul class="topbar-option-list">
                                <li class="h6 d-none d-sm-flex">
                                    <a href="tel:0000000000" class="text-main link track"><i class="icon icon-phone"></i> Call us for free: +91 00000 00000</a>
                                </li>
                                <li class="br-line d-none d-sm-flex bg-line opacity-100"></li>
                                <li class="h6">
                                    <a href="mailto:demo@gmail.com" class="text-main link track">
                                        <i class="icon icon-envelope-simple"></i>
                                        <span>
                                            Email us:
                                        </span>
                                        <span>
                                            demo@gmail.com
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-4 d-none d-lg-block">
                        <ul class="topbar-right topbar-option-list">
                            <li class="h6">
                                <a href="#" class="text-main link">Help & FAQs</a>
                            </li> 
                            <li class="tf-languages d-none d-xl-block">
                                <!-- <select class="tf-dropdown-select style-default color-white-2 type-languages">
                                    <option>English</option> 
                                    <option>Hindi</option>
                                    <option>French</option> 
                                </select> -->
                                <?php //echo do_shortcode('[gt-link lang="en" label="English" widget_look="flags_name"]');?>
                            </li>
                            <li class="tf-currencies d-none d-xl-block">
                                <!-- <select class="tf-dropdown-select style-default color-white-2 type-currencies">
                                    <option selected data-thumbnail="<?php //echo get_template_directory_uri(); ?>/assets/images/country/ind.png">IND</option> 
                                    <option selected data-thumbnail="<?php //echo get_template_directory_uri(); ?>/assets/images/country/ind.png">IND</option> 
                                </select> -->
								<?php //echo do_shortcode( '[psm-multi-currency-switcher size ="medium" flag="1" name="1" symbol="1" code="1"]'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> 
        <!-- /Top Bar -->

        <!-- Header -->
        <header class="tf-header style-5 primary-2 mb-24 header_main">
            <div class="header-top">
                <div class="container-full-2">
                    <div class="row align-items-center">
                        <div class="col-md-4 col-3 d-xl-none">
                            <a href="#mobileMenu" data-bs-toggle="offcanvas" class="btn-mobile-menu">
                                <span></span>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6 text-center text-xl-start">
                            <a href="<?php echo home_url('/'); ?>" class="logo-site justify-content-center justify-content-xl-start">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="LOGO">
                            </a>
                        </div>
                        <div class="col-xl-10 col-md-4 col-3">
                            <div class="header-right">
                                <?php echo do_shortcode(' [apsw_search_bar_preview]'); ?>
                                <!-- <form class="form_search-product style-search-2 style-search-3 d-none d-xl-flex">
                                    <div class="select-category">
                                        <select name="product_cat" id="product_cat" class="dropdown_product_cat">
                                            <option value="" selected="selected">All Categories</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                            <option class="level-0" value="Categorie Name">Categorie Name</option>
                                        </select>
                                        <ul class="select-options">
                                            <li class="link" rel=""><span>All Categories</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span> </li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                            <li class="link" rel="Categorie Name"><span>Categorie Name</span></li>
                                        </ul>
                                    </div>
                                    <span class="br-line type-vertical"></span>
                                    <input class="style-def" type="text" placeholder="Search for products..." required>
                                    <button type="submit" class="btn-submit-2 animate-btn">
                                        <span class="h6 fw-bold">Search</span>
                                    </button>
                                </form> -->
                                <ul class="nav-icon-list text-nowrap">
                                    <li class="d-none d-lg-flex">
    <a class="nav-icon-item-2 text-black link"
       href="<?php echo esc_url(
           is_user_logged_in()
               ? wc_get_account_endpoint_url('dashboard')
               : home_url('login')
       ); ?>">

        <i class="icon icon-user"></i>

        <div class="nav-icon-item_sub">

            <?php if (is_user_logged_in()) : ?>

                <span class="text-sub text-main-6 text-small-2">
                    Hello, <?php echo esc_html(wp_get_current_user()->display_name); ?>
                </span>

                <span class="h6">My account</span>

            <?php else : ?>

                <span class="text-sub text-main-6 text-small-2">
                    Hello, sign in
                </span>

                <span class="h6">Your account</span>

            <?php endif; ?>

        </div>
    </a>
</li>
                                    <li class="d-none d-sm-flex">
                                        <!-- <a class="nav-icon-item-2 text-black link" href="wishlist.html">
                                            <i class="icon icon-heart"></i>
                                            <span class="count">24</span>
                                        </a> -->
                                        <?php echo do_shortcode('[yith_wcwl_items_count]'); ?>
                                    </li>
                                    <li>
                                        
                                        <?php //echo do_shortcode('[taiowc]'); ?>
                                         <a class="nav-icon-item-2 text-black link" href="#shoppingCart" data-bs-toggle="offcanvas">
                                            <div class="position-relative d-flex">
                                                <i class="icon icon-shopping-cart-simple"></i>
                                                <span class="count cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                            </div>
                                        </a> 
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-inner d-none d-xl-block">
                <div class="container-full-2">
                    <div class="header-inner_wrap">
                        <div class="col-left"> 
                            <nav class="box-navigation">
                                <?php
								wp_nav_menu(
									array(
										'theme_location' => 'primary',
										'container'      => false,
										'menu_class'     => 'box-nav-menu',
										'fallback_cb'    => false,
									)
								);
								?>    
                            </nav>
                        </div>
                        <div class="col-right">
                            <i class="icon icon-sparkle"></i>
                            <p class="h6 text-black text-line-clamp-1">
                                Handcrafted Luxury Jewellery &bull; Worldwide Shipping
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <header class="tf-header header-fixed style-5 bg-black primary-2">
            <div class="tf-topbar type-space-2 line-bt primary-2">
                <div class="container-full-2">
                    <div class="row">
                        <div class="col-xl-7 col-lg-8">
                            <div class="topbar-left justify-content-center justify-content-sm-start">
                                <ul class="topbar-option-list">
                                    <li class="h6 d-none d-sm-flex">
                                        <a href="tel:0000000000" class="text-main link track"><i class="icon icon-phone"></i> Call us for free: +91 00000 00000</a>
                                    </li>
                                    <li class="br-line d-none d-sm-flex bg-line opacity-100"></li>
                                    <li class="h6">
                                        <a href="mailto:demo@gmail.com" class="text-main link track">
                                            <i class="icon icon-envelope-simple"></i>
                                            <span>
                                                Email us:
                                            </span>
                                            <span>
                                                demo@gmail.com
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-4 d-none d-lg-block">
                            <ul class="topbar-right topbar-option-list">
                                <li class="h6">
                                    <a href="#" class="text-main link">Help & FAQs</a>
                                </li> 
                                <li class="tf-languages d-none d-xl-block">
                                    <!-- <select class="tf-dropdown-select style-default color-white-2 type-languages">
                                        <option>English</option> 
                                        <option>Hindi</option>
                                        <option>French</option> 
                                    </select> -->
                                    <?php // echo do_shortcode('[gt-link lang="en" label="English" widget_look="flags_name"]');?>
                                </li>
                                <li class="tf-currencies d-none d-xl-block">
                                    <?php // echo do_shortcode( '[psm-multi-currency-switcher size ="medium" flag="1" name="1" symbol="1" code="1"]'); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> 

            <div class="header-top">
                <div class="container-full-2">
                    <div class="row align-items-center">
                        <div class="col-md-4 col-3 d-xl-none">
                            <a href="#mobileMenu" data-bs-toggle="offcanvas" class="btn-mobile-menu">
                                <span></span>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6 text-center text-xl-start">
                            <a href="<?php echo home_url('/'); ?>" class="logo-site justify-content-center justify-content-xl-start">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="LOGO">
                            </a>
                        </div>
                        <div class="col-xl-10 col-md-4 col-3">
                            <div class="header-right">
                                
                                <?php echo do_shortcode(' [apsw_search_bar_preview]'); ?>
                                <ul class="nav-icon-list text-nowrap">
                                    <li class="d-none d-lg-flex">
                                        <a class="nav-icon-item-2 text-black link" href="<?php echo home_url('/login'); ?>">
                                            <i class="icon icon-user"></i>
                                            <div class="nav-icon-item_sub">
                                                <span class="text-sub text-main-6 text-small-2">Hello, sign in</span>
                                                <span class="h6">Your account</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="d-none d-sm-flex">
                                        <!-- <a class="nav-icon-item-2 text-black link" href="wishlist.html">
                                            <i class="icon icon-heart"></i>
                                            <span class="count">24</span>
                                        </a> -->
                                        
                                        <?php echo do_shortcode('[yith_wcwl_items_count]'); ?>
                                    </li>
                                    <li>
                                        <a class="nav-icon-item-2 text-black link" href="#shoppingCart" data-bs-toggle="offcanvas">
                                            <div class="position-relative d-flex">
                                                <i class="icon icon-shopping-cart-simple"></i>
                                                <span class="count cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                            
                                            </div>
                                        </a> 
                                        <?php //echo do_shortcode('[taiowc]'); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-inner d-none d-xl-block">
                <div class="container-full-2">
                    <div class="header-inner_wrap">
                        <div class="col-left"> 
                            <nav class="box-navigation">
                                <?php
								wp_nav_menu(
									array(
										'theme_location' => 'primary',
										'container'      => false,
										'menu_class'     => 'box-nav-menu',
										'fallback_cb'    => false,
									)
								);
								?>                            
								</nav>
                        </div>
                        <div class="col-right">
                            <i class="icon icon-sparkle"></i>
                            <p class="h6 text-black text-line-clamp-1">
                                Handcrafted Luxury Jewellery &bull; Worldwide Shipping
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- /Header --> 
