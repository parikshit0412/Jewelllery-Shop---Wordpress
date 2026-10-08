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
                    <!-- <div class="col-xxl-9 col-xl-8">
                        <div class="tf-cart-sold">
                            <div class="notification-sold bg-surface">
                                <img class="icon" src="icon/fire.svg" alt="Icon">
                                <div class="count-text h6">
                                    Your cart will expire in
                                    <div class="js-countdown time-count cd-has-zero cd-no" data-timer="65" data-labels=":,:,:,"></div>
                                    minutes! Please checkout now before your items sell out!
                                </div>
                            </div>
                            <div class="notification-progress">
                                <div class="text">
                                    <p class="h6">
                                        Free Shipping for orders over <span class="text-primary fw-bold">₹150</span>
                                    </p>
                                </div>
                                <div class="progress_cart_onlyview progress-cart">
                                    <div class="value" style="width: 0%;" data-progress="50">
                                        <span class="round"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form>
                            <table class="tf-table-page-cart">
                                <thead>
                                    <tr>
                                        <th class="h6">Product</th>
                                        <th class="h6">Price</th>
                                        <th class="h6">Quality</th>
                                        <th class="h6">Total price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="tf-cart_item each-prd file-delete">
                                        <td>
                                            <div class="cart_product">
                                                <a href="product-detail.html" class="img-prd">
                                                    <img class="lazyload" src="images/vc1.jpg" data-src="images/vc1.jpg"
                                                        alt="T Shirt">
                                                </a>
                                                <div class="infor-prd">
                                                    <h6 class="prd_name">
                                                        <a href="product-detail.html" class="link">
                                                            Product Name Goes Here
                                                        </a>
                                                    </h6> 
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart_price h6 each-price" data-cart-title="Price">₹22.99</td>
                                        <td class="cart_quantity" data-cart-title="Quantity">
                                            <div class="wg-quantity">
                                                <button class="btn-quantity minus-quantity" type="button">
                                                    <i class="icon-minus fs-14"></i>
                                                </button>
                                                <input class="quantity-product" type="text" name="number" value="1">
                                                <button class="btn-quantity plus-quantity" type="button">
                                                    <i class="icon-plus fs-14"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="cart_total h6 each-subtotal-price" data-cart-title="Total"></td>
                                        <td class="cart_remove remove link" data-cart-title="Remove">
                                            <i class="icon icon-close"></i>
                                        </td>
                                    </tr>
                                    <tr class="tf-cart_item each-prd file-delete">
                                        <td>
                                            <div class="cart_product">
                                                <a href="product-detail.html" class="img-prd">
                                                    <img class="lazyload" src="images/vc2.jpg" data-src="images/vc2.jpg"
                                                        alt="T Shirt">
                                                </a>
                                                <div class="infor-prd">
                                                    <h6 class="prd_name">
                                                        <a href="product-detail.html" class="link">
                                                            Product Name Goes Here
                                                        </a>
                                                    </h6> 
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart_price h6 each-price" data-cart-title="Price">₹59.99</td>
                                        <td class="cart_quantity" data-cart-title="Quantity">
                                            <div class="wg-quantity">
                                                <button class="btn-quantity minus-quantity" type="button">
                                                    <i class="icon-minus fs-14"></i>
                                                </button>
                                                <input class="quantity-product" type="text" name="number" value="1">
                                                <button class="btn-quantity plus-quantity" type="button">
                                                    <i class="icon-plus fs-14"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="cart_total h6 each-subtotal-price" data-cart-title="Total"></td>
                                        <td class="cart_remove remove link" data-cart-title="Remove">
                                            <i class="icon icon-close"></i>
                                        </td>
                                    </tr>
                                    <tr class="tf-cart_item each-prd file-delete">
                                        <td>
                                            <div class="cart_product">
                                                <a href="product-detail.html" class="img-prd">
                                                    <img class="lazyload" src="images/vc3.jpg" data-src="images/vc3.jpg" alt="T Shirt">
                                                </a>
                                                <div class="infor-prd">
                                                    <h6 class="prd_name">
                                                        <a href="product-detail.html" class="link">
                                                            Product Name Goes Here
                                                        </a>
                                                    </h6> 
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart_price h6 each-price" data-cart-title="Price">₹99.00</td>
                                        <td class="cart_quantity" data-cart-title="Quantity">
                                            <div class="wg-quantity">
                                                <button class="btn-quantity minus-quantity" type="button">
                                                    <i class="icon-minus fs-14"></i>
                                                </button>
                                                <input class="quantity-product" type="text" name="number" value="1">
                                                <button class="btn-quantity plus-quantity" type="button">
                                                    <i class="icon-plus fs-14"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="cart_total h6 each-subtotal-price" data-cart-title="Total"></td>
                                        <td class="cart_remove remove link" data-cart-title="Remove">
                                            <i class="icon icon-close"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="ip-discount-code">
                                <input type="text" placeholder="Add voucher discount" required>
                                <button class="tf-btn animate-btn cmnwidthbtn" type="submit">
                                    Apply Code
                                </button>
                            </div>
                            <div class="group-discount mb-xl-0">
                                <div class="box-discount">
                                    <div class="discount-top">
                                        <div class="discount-off">
                                            <p class="h6">Discount</p>
                                            <h6 class="sale-off h6 fw-bold heading_cmn_white">30% OFF</h6>
                                        </div>
                                        <div class="discount-from">
                                            <p class="h6">
                                                For all orders <br>form ₹150
                                            </p>
                                        </div>
                                    </div>
                                    <div class="discount-bot heading_cmn_white">
                                        <h6>Code: <span class="coupon-code">SamarShaw</span></h6>
                                        <button class="tf-btn coupon-copy-wrap h6" type="button">
                                            Apply Code
                                        </button>
                                    </div>
                                </div>
                                <div class="box-discount">
                                    <div class="discount-top">
                                        <div class="discount-off">
                                            <p class="h6">Discount</p>
                                            <h6 class="sale-off h6 fw-bold heading_cmn_white">15% OFF</h6>
                                        </div>
                                        <div class="discount-from">
                                            <p class="h6">
                                                For all orders <br>form ₹100
                                            </p>
                                        </div>
                                    </div>
                                    <div class="discount-bot heading_cmn_white">
                                        <h6>Code: <span class="coupon-code">SamarSingh</span></h6>
                                        <button class="tf-btn coupon-copy-wrap h6" type="button">
                                            Apply Code
                                        </button>
                                    </div>
                                </div>
                                <div class="box-discount">
                                    <div class="discount-top">
                                        <div class="discount-off">
                                            <p class="h6">Discount</p>
                                            <h6 class="sale-off h6 fw-bold heading_cmn_white">20% OFF</h6>
                                        </div>
                                        <div class="discount-from">
                                            <p class="h6">
                                                For all orders <br>form ₹200
                                            </p>
                                        </div>
                                    </div>
                                    <div class="discount-bot heading_cmn_white">
                                        <h6>Code: <span class="coupon-code">SamarYadav</span></h6>
                                        <button class="tf-btn coupon-copy-wrap h6" type="button">
                                            Apply Code
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-xxl-3 col-xl-4">
                        <div class="fl-sidebar-cart bg-white-smoke sticky-top">
                            <div class="box-order-summary">
                                <h4 class="title fw-semibold">Order Summary</h4>
                                <div class="subtotal h6 text-button d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold heading_cmn_white">Subtotal</h6>
                                    <span class="total">-₹80.00</span>
                                </div>
                                <div class="discount text-button d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold heading_cmn_white">Discounts</h6>
                                    <span class="total h6">-₹80.00</span>
                                </div>
                                <div class="ship">
                                    <h6 class="fw-bold heading_cmn_white">Shipping</h6>
                                    <div class="flex-grow-1">
                                        <fieldset class="ship-item">
                                            <input type="radio" name="ship-check" class="tf-check-rounded" id="free" checked>
                                            <label class="h6" for="free">
                                                <span>Free Shipping</span>
                                                <span class="price">₹0.00</span>
                                            </label>
                                        </fieldset>
                                        <fieldset class="ship-item">
                                            <input type="radio" name="ship-check" class="tf-check-rounded" id="local">
                                            <label class="h6" for="local">
                                                <span>Local:</span>
                                                <span class="price">₹35.00</span>
                                            </label>
                                        </fieldset>
                                        <fieldset class="ship-item">
                                            <input type="radio" name="ship-check" class="tf-check-rounded" id="rate">
                                            <label class="h6" for="rate">
                                                <span>Flat Rate:</span>
                                                <span class="price">₹35.00</span>
                                            </label>
                                        </fieldset>
                                    </div>
                                </div>
                                <h5 class="total-order d-flex justify-content-between align-items-center">
                                    <span>Total</span>
                                    <span class="total each-total-price">₹186,99</span>
                                </h5>
                                <div class="list-ver">
                                    <a href="checkout.html" class="tf-btn w-100 animate-btn">
                                        Process to checkout
                                        <i class="icon icon-arrow-right"></i>
                                    </a>
                                    <a href="shop-default.html" class="tf-btn btn-white animate-btn animate-dark w-100">
                                        Continue shopping
                                        <i class="icon icon-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div> -->
            </div>
        </div>
        <!-- /View Cart -->
<?php get_footer(); ?>