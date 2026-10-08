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
                    <!-- <div class="col-lg-7">
                        <div class="tf-page-checkout mb-lg-0">
                            <div class="wrap-coupon">
                                <h5 class="mb-12 heading_cmn_white">Have a coupon? <span class="text-primary">Enter your code</span></h5>
                                <form>
                                    <div class="ip-discount-code mb-0">
                                        <input type="text" placeholder="Enter your code" required>
                                        <button class="tf-btn animate-btn cmnwidthbtn" type="submit">
                                            Apply Code
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <form class="tf-checkout-cart-main">
                                <div class="box-ip-checkout estimate-shipping">
                                    <h2 class="title type-semibold heading_cmn_white">Infomation</h2>
                                    <div class="form_content">
                                        <div class="cols tf-grid-layout sm-col-2">
                                            <fieldset>
                                                <input type="text" name="first-name_infor" placeholder="First name" required>
                                            </fieldset>
                                            <fieldset>
                                                <input type="text" name="last-name_infor" placeholder="Last name" required>
                                            </fieldset>
                                        </div>
                                        <div class="cols tf-grid-layout sm-col-2">
                                            <fieldset>
                                                <input type="email" name="email_infor" placeholder="Email address" required>
                                            </fieldset>
                                            <fieldset>
                                                <input type="number" name="phone_infor" placeholder="Phone number" required>
                                            </fieldset>
                                        </div>
                                        <fieldset>
                                            <div class="tf-select">
                                                <select class="w-100" id="shipping-country-form" name="address[country]" data-default="">
                                                    <option selected disabled value="">Choose country / Region</option>
                                                    <option value="Indian" data-provinces='[["Indian Capital","Indian Capital"],["New South India","New South India"],["South Indian","South Indian"],["Western Indian","Western Indian"]]'>India</option>
                                                    <option value="Austria" data-provinces='[]'>Austria</option>
                                                    <option value="Belgium" data-provinces='[]'>Belgium</option> 
                                                    <option value="Czech Republic" data-provinces='[]'>Czechia</option>
                                                    <option value="Denmark" data-provinces='[]'>Denmark</option>
                                                    <option value="Finland" data-provinces='[]'>Finland</option>
                                                    <option value="France" data-provinces='[]'>France</option>
                                                    <option value="Germany" data-provinces='[]'>Germany</option>   
                                                    <option value="Japan" data-provinces='[]'>Japan</option>
                                                    <option value="Mexico" data-provinces='[]'>Mexico</option>
                                                    <option value="South Korea" data-provinces='[]'>South Korea</option>
                                                    <option value="Spain" data-provinces='[]'>Spain</option>
                                                    <option value="Italy" data-provinces='[]'>Italy</option> 
                                                </select>
                                            </div>
                                        </fieldset>
                                        <div class="cols tf-grid-layout sm-col-2">
                                            <fieldset>
                                                <input type="text" name="city_infor" placeholder="Town/City" required>
                                            </fieldset>
                                            <fieldset>
                                                <input type="text" name="street_infor" placeholder="Street" required>
                                            </fieldset>
                                        </div>
                                        <div class="cols tf-grid-layout sm-col-2">
                                            <fieldset>
                                                <div class="tf-select">
                                                    <select id="shipping-province-form" name="address[province]" data-default="">
                                                        <option selected disabled value="">Choose State</option>
                                                    </select>
                                                </div>
                                            </fieldset>
                                            <fieldset>
                                                <input type="number" name="number_card" placeholder="Postal code" required>
                                            </fieldset>
                                        </div>
                                        <textarea placeholder="Note about your order"></textarea>
                                    </div>
                                </div>
                                <div class="box-ip-payment">
                                    <h2 class="title type-semibold heading_cmn_white">Choose Payment Option</h2>
                                    <div class="payment-method-box" id="payment-method-box">
                                        <div class="payment_accordion">
                                            <label for="direct" class="payment_check checkbox-wrap " data-bs-toggle="collapse"
                                                data-bs-target="#direct-bank" aria-controls="direct-bank">
                                                <input type="radio" name="payment-method" class="tf-check-rounded style-2" id="direct" checked>
                                                <span class="pay-title">Direct bank transfer</span>
                                            </label>
                                            <div id="direct-bank" class=" collapse show" data-bs-parent="#payment-method-box">
                                                <p class="payment_body h6">
                                                    Make your payment directly into our bank account. Please use your Order ID as the payment
                                                    reference.
                                                    Your order will not be shipped until the funds have cleared in our account.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="payment_accordion">
                                            <label for="credit-card" class="payment_check checkbox-wrap" data-bs-toggle="collapse"
                                                data-bs-target="#credit-card-payment" aria-controls="credit-card-payment">
                                                <input type="radio" name="payment-method" class="tf-check-rounded style-2" id="credit-card">
                                                <span class="pay-title">Credit card</span>
                                            </label>
                                            <div id="credit-card-payment" class="collapse" data-bs-parent="#payment-method-box">
                                                <div class="payment_body form_content">
                                                    <fieldset>
                                                        <input type="text" placeholder="Name on card">
                                                    </fieldset>
                                                    <fieldset class="ip-card">
                                                        <input type="number" placeholder="Card numbers">
                                                        <div class="card-logo">
                                                            <img src="images/payment/visa-pay.svg" alt="Payment Logo">
                                                            <img src="images/payment/master-pay.svg" alt="Payment Logo">
                                                            <img src="images/payment/paypal-2.svg" alt="Payment Logo">
                                                        </div>
                                                    </fieldset>
                                                    <div class="cols tf-grid-layout sm-col-2">
                                                        <fieldset>
                                                            <input type="date">
                                                        </fieldset>
                                                        <fieldset>
                                                            <input type="number" placeholder="Postal code">
                                                        </fieldset>
                                                    </div>
                                                    <div class="checkbox-wrap">
                                                        <input id="save" type="checkbox" class="tf-check style-2">
                                                        <label for="save" class="h6">Save card details</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="payment_accordion">
                                            <label for="cash-on" class="payment_check checkbox-wrap" data-bs-toggle="collapse"
                                                data-bs-target="#cash-on-payment" aria-controls="cash-on-payment">
                                                <input type="radio" name="payment-method" class="tf-check-rounded style-2" id="cash-on">
                                                <span class="pay-title">Cash On Delivery</span>
                                            </label>
                                            <div id="cash-on-payment" class="collapse" data-bs-parent="#payment-method-box"></div>
                                        </div>
                                        <div class="payment_accordion">
                                            <label for="paypal" class="payment_check checkbox-wrap" data-bs-toggle="collapse"
                                                data-bs-target="#paypal-payment" aria-controls="paypal-payment">
                                                <input type="radio" name="payment-method" class="tf-check-rounded style-2" id="paypal">
                                                <span class="pay-title">Paypal</span>
                                            </label>
                                            <div id="paypal-payment" class="collapse" data-bs-parent="#payment-method-box"></div>
                                        </div>
                                    </div>
                                    <p class="h6 mb-20">
                                        Your personal data will be used to process your order, support your experience throughout this website, and
                                        for
                                        other purposes described in our privacy policy.
                                    </p>
                                    <div class="checkbox-wrap">
                                        <input id="agree" type="checkbox" class="tf-check style-2">
                                        <label for="agree" class="h6">I have read and agree to the website <span class="text-primary">terms and
                                                conditions *</span></label>
                                    </div>
                                </div>
                                <div class="box-ip-shipping">
                                    <h2 class="title type-semibold heading_cmn_white">Shipping Method</h2>
                                    <label for="freeship" class="check-ship mb-12">
                                        <input type="radio" id="freeship" class="tf-check-rounded style-2 line-black" name="checkshipping" checked>
                                        <span class="text h6">
                                            <span class="heading_cmn_blk">Free shipping (Estimate in 01/08 - 15/08/2026)</span>
                                            <span class="price heading_cmn_blk">$00.00</span>
                                        </span>
                                    </label>
                                    <label for="express" class="check-ship">
                                        <input type="radio" id="express" class="tf-check-rounded style-2 line-black" name="checkshipping">
                                        <span class="text h6">
                                            <span class="heading_cmn_blk">Express shipping (Estimate in 01/08 - 03/08/2026)</span>
                                            <span class="price fw-medium heading_cmn_blk">₹500.00</span>
                                        </span>
                                    </label>
                                </div>
                                <div class="button_submit">
                                    <button type="submit" class="tf-btn animate-btn cmnwidthbtn">
                                        Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="fl-sidebar-cart sticky-top">
                            <div class="box-your-order">
                                <h2 class="title type-semibold heading_cmn_white">Your Order</h2>
                                <ul class="list-order-product">
                                    <li class="order-item">
                                        <a href="#" class="img-prd">
                                            <img class="lazyload" src="images/vc1.jpg" alt="Gold">
                                        </a>
                                        <div class="infor-prd">
                                            <h6 class="prd_name">
                                                <a href="product-detail.html" class="link">
                                                    Product Name goes here
                                                </a>
                                            </h6> 
                                        </div>
                                        <p class="price-prd h6">
                                            ₹5000
                                        </p>
                                    </li>
                                    <li class="order-item">
                                        <a href="#" class="img-prd">
                                            <img class="lazyload" src="images/vc2.jpg" alt="Gold">
                                        </a>
                                        <div class="infor-prd">
                                            <h6 class="prd_name">
                                                <a href="product-detail.html" class="link">
                                                    Product Name goes here
                                                </a>
                                            </h6> 
                                        </div>
                                        <p class="price-prd h6">
                                            ₹5000
                                        </p>
                                    </li>
                                    <li class="order-item">
                                        <a href="#" class="img-prd">
                                            <img class="lazyload" src="images/vc3.jpg" alt="Gold">
                                        </a>
                                        <div class="infor-prd">
                                            <h6 class="prd_name">
                                                <a href="product-detail.html" class="link">
                                                    Product Name goes here
                                                </a>
                                            </h6> 
                                        </div>
                                        <p class="price-prd h6">
                                            ₹5000
                                        </p>
                                    </li>
                                </ul>
                                <ul class="list-total">
                                    <li class="total-item h6">
                                        <span class="fw-bold text-black">Discounts</span>
                                        <span>₹1000</span>
                                    </li>
                                    <li class="total-item h6">
                                        <span class="fw-bold text-black">Shipping</span>
                                        <span>Free</span>
                                    </li>
                                </ul>
                                <div class="last-total h5 fw-medium text-black">
                                    <span>Total</span>
                                    <span>₹4000</span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </section>
        <!-- /Check Out -->

<?php get_footer(); ?>