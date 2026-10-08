<?php
/*
Template Name: login
*/

get_header(); ?>

<?php //get_template_part('template-parts/inner-banner'); 
?>
<!-- Login -->
<section class="flat-spacing">
    <div class="container">
        <div class="s-log">
            <div class="col-left">
                <h1 class="heading heading_cmn_white">Login</h1>
                <form class="form-login" id="ajaxLoginForm">
                    <div class="list-ver">

                        <fieldset>
                            <input type="text"
                                name="username"
                                placeholder="Enter your email address *"
                                required>
                        </fieldset>

                        <fieldset class="password-wrapper mb-8">
                            <input class="password-field"
                                name="password"
                                type="password"
                                placeholder="Password *"
                                required>
                            <span class="toggle-pass icon-show-password"></span>
                        </fieldset>

                        <div class="check-bottom">
                            <div class="checkbox-wrap">
                                <input id="remember"
                                    name="remember"
                                    type="checkbox"
                                    class="tf-check">
                                <label for="remember" class="h6">Keep me signed in</label>
                            </div>

                            <h6>
                                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="link">
                                    Forgot your password?
                                </a>
                            </h6>
                        </div>

                    </div>

                    <div class="login-message"></div>

                    <button id="btnLogin"
                        type="submit"
                        class="tf-btn animate-btn cmnwidthbtn">
                        Login
                    </button>
                </form>
            </div>
            <div class="col-right">
                <h1 class="heading heading_cmn_white">New Customer</h1>
                <p class="h6 text-sub">
                    For customers who register a new account, we are offering you a ₹500 shopping voucher and a 30% discount code. Happy
                    shopping!
                </p>
                <div class="get-discout-wrap">
                    <h6 class="fw-semibold mb-16 heading_cmn_white">Sign up and get your discount code</h6>
                    <div class="box-discount style-2">
                        <div class="discount-top">
                            <div class="discount-off">
                                <p class="h6">Discount</p>
                                <h6 class="sale-off h6 fw-bold heading_cmn_white">30% OFF</h6>
                            </div>
                            <div class="discount-from">
                                <p class="h6">
                                    For all orders <br class="d-sm-none"> form ₹15000
                                </p>
                            </div>
                        </div>
                        <div class="discount-bot">
                            <h6 class="text-nowrap fw-bold heading_cmn_white">Code: ********</h6>
                            <a href="<?php echo home_url('/register'); ?>" class="tf-btn animate-btn fw-bold">
                                Register
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Login -->
<?php get_footer(); ?>