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

                    <div class="login-message">
                        <?php if ( isset($_GET['verified']) && '1' === $_GET['verified'] ) : ?>
                            <div class="success" style="padding: 12px 16px; margin-bottom: 15px; border-radius: 4px; background: #e8f5e9; color: #2e7d32; font-size: 14px; border-left: 4px solid #2e7d32; display:block;">
                                <?php esc_html_e('Your email has been verified successfully! You can now log in below.', 'jewellery_wp'); ?>
                            </div>
                        <?php elseif ( isset($_GET['registered']) && 'verify' === $_GET['registered'] ) : ?>
                            <div class="success" style="padding: 12px 16px; margin-bottom: 15px; border-radius: 4px; background: #e3f2fd; color: #1565c0; font-size: 14px; border-left: 4px solid #1565c0; display:block;">
                                <?php esc_html_e('Registration successful! Please check your email inbox to verify your account before logging in.', 'jewellery_wp'); ?>
                            </div>
                        <?php elseif ( isset($_GET['resent']) && '1' === $_GET['resent'] ) : ?>
                            <div class="success" style="padding: 12px 16px; margin-bottom: 15px; border-radius: 4px; background: #e8f5e9; color: #2e7d32; font-size: 14px; border-left: 4px solid #2e7d32; display:block;">
                                <?php esc_html_e('A new verification email has been sent. Please check your inbox.', 'jewellery_wp'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

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
                    Create an account with us to enjoy a seamless shopping experience, track your orders, manage your wishlist, and more.
                </p>
                <a href="<?php echo home_url('/register'); ?>" class="btn_log tf-btn animate-btn cmnylwbtnsam">
                    Register
                </a>
            </div>
        </div>
    </div>
</section>
<!-- /Login -->
<?php get_footer(); ?>