<?php

/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

$current_user_id = get_current_user_id();

$profile_image_id = get_user_meta(
    $current_user_id,
    'profile_image_id',
    true
);

if ($profile_image_id) {

    $avatar_url = wp_get_attachment_image_url(
        $profile_image_id,
        'thumbnail'
    );

} else {

    $avatar_url = get_avatar_url(
        $current_user_id
    );
}
defined('ABSPATH') || exit;
?>


<div class="row">

	<div class="col-xl-3 d-none d-xl-block">
		<div class="sidebar-account sidebar-content-wrap sticky-top">
			<div class="account-author">
				<div class="author_avatar">
					
					<div class="author_avatar">
    <div class="image">
        <img
            class="lazyload imgDash"
            src="<?php echo esc_url($avatar_url); ?>"
            data-src="<?php echo esc_url($avatar_url); ?>"
            alt="<?php echo esc_attr($current_user->display_name); ?>"
        >
    </div>

    <div class="btn-change_img box-icon changeImgDash">
        <i class="icon icon-camera"></i>
    </div>

    <input
        type="file"
        id="profile_image"
        accept="image/jpeg,image/png,image/webp"
        style="display:none;"
    >
</div>
					<div class="btn-change_img box-icon changeImgDash">
						<i class="icon icon-camera"></i>
					</div>
				</div>
                        <h4 class="author_name">
                            <?php echo esc_html( $current_user->display_name ); ?>
                        </h4>

                        <p class="author_email h6">
                            <?php echo esc_html( $current_user->user_email ); ?>
                        </p>
			</div>
			
		<?php
		/**
		 * My Account navigation.
		 *
		 * @since 2.6.0
		 */
		do_action('woocommerce_account_navigation'); ?>
		</div>

	</div>

	<div class="col-xl-9">
		<div class="woocommerce-MyAccount-content">
			<?php
			/**
			 * My Account content.
			 *
			 * @since 2.6.0
			 */
			do_action('woocommerce_account_content');
			?>
		</div>
	</div>
</div>