<?php
/*
Template Name: Register
*/

get_header(); ?>

<?php get_template_part('template-parts/inner-banner'); ?>

<!-- Login -->
<section class="flat-spacing">
    <div class="container">
        <div class="s-log">
            <div class="col-left">
                <h1 class="heading heading_cmn_white">Register</h1>
                <form class="form-login" id="custom-register-form">

                    <div class="list-ver">

                        <fieldset>
                            <input type="email"
                                name="user_email"
                                placeholder="Enter your email address *"
                                required>
                        </fieldset>

                        <fieldset class="password-wrapper">
                            <input type="password"
                                name="password"
                                class="password-field"
                                placeholder="Password *"
                                required>
                            <span class="toggle-pass icon-show-password"></span>
                        </fieldset>

                        <fieldset class="password-wrapper">
                            <input type="password"
                                name="confirm_password"
                                class="password-field"
                                placeholder="Confirm password *"
                                required>
                            <span class="toggle-pass icon-show-password"></span>
                        </fieldset>

                    </div>

                    <div class="register-message"></div>

                    <button type="submit" class="tf-btn animate-btn cmnylwbtnsam">
                        Register
                    </button>

                </form>
            </div>
            <div class="col-right">
                <h1 class="heading heading_cmn_white">Have An Account</h1>
                <p class="h6 text-sub">
                    Welcome back, log in to your account to enhance your shopping experience, receive coupons, and the best discount codes.
                </p>
                <a href="<?php echo home_url('login'); ?>" class="btn_log tf-btn animate-btn cmnylwbtnsam">
                    Login
                </a>
            </div>
        </div>
    </div>
</section>
<!-- /Login -->
<?php get_footer(); ?>