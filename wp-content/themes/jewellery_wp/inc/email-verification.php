<?php
/**
 * WooCommerce Customer Email Verification
 *
 * Handles email verification workflow for customer registrations on My Account:
 * - Prevents unverified automatic login on registration
 * - Generates cryptographically secure verification tokens (24-hour expiration)
 * - Sends branded verification emails
 * - Blocks login attempts for unverified customer accounts with a resend option
 * - Handles email verification links and seamlessly redirects to password setup if auto-generated
 * - Provides rate-limited resend verification functionality
 * - Admin column & quick actions in Users list for store managers
 * - Localhost developer helper notice when running on local XAMPP without SMTP
 *
 * @package jewellery_wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jewellery_Email_Verification {

	/**
	 * Meta key for verification status: '1' = verified, '0' = unverified.
	 */
	const META_VERIFIED = '_is_email_verified';

	/**
	 * Meta key for hashed verification token.
	 */
	const META_TOKEN = '_email_verification_token';

	/**
	 * Meta key for token timestamp.
	 */
	const META_TOKEN_SENT = '_email_verification_sent';

	/**
	 * Meta key for resend rate-limiting timestamp.
	 */
	const META_LAST_RESEND = '_email_verification_last_resend';

	/**
	 * Meta key for verification completion timestamp.
	 */
	const META_VERIFIED_AT = '_email_verified_at';

	/**
	 * Meta key to store the last generated link for local testing.
	 */
	const META_DEV_LINK = '_email_verification_dev_link';

	/**
	 * Token expiry window in seconds (24 hours).
	 */
	const TOKEN_EXPIRY = 86400;

	/**
	 * Resend cooldown in seconds (60 seconds).
	 */
	const RESEND_COOLDOWN = 60;

	/**
	 * Singleton instance.
	 */
	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		// 1. Customer registration interception
		add_action( 'woocommerce_created_customer', array( $this, 'on_customer_registered' ), 10, 3 );
		add_filter( 'woocommerce_registration_auth_new_customer', array( $this, 'prevent_auto_login' ), 10, 2 );
		add_filter( 'woocommerce_email_enabled_customer_new_account', array( $this, 'suppress_default_new_account_email' ), 10, 3 );

		// 2. Authentication blocking for unverified users
		add_filter( 'authenticate', array( $this, 'check_email_verification_on_login' ), 30, 3 );

		// 3. Password reset guard
		add_filter( 'allow_password_reset', array( $this, 'guard_password_reset' ), 10, 2 );

		// 4. Verification & resend endpoints on template_redirect
		add_action( 'template_redirect', array( $this, 'handle_verification_endpoints' ) );

		// 5. My Account UI enhancements & styling
		add_action( 'woocommerce_before_customer_login_form', array( $this, 'render_dev_helper_notice' ), 5 );
		add_action( 'woocommerce_after_customer_login_form', array( $this, 'render_resend_verification_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// 6. Fix PHPMailer invalid from address on localhost
		add_filter( 'wp_mail_from', array( $this, 'filter_wp_mail_from' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'filter_wp_mail_from_name' ) );

		// 7. WordPress Admin Users list integration
		add_filter( 'manage_users_columns', array( $this, 'add_admin_users_column' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'render_admin_users_column' ), 10, 3 );
		add_filter( 'user_row_actions', array( $this, 'add_admin_user_row_actions' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'handle_admin_actions' ) );
		add_action( 'admin_notices', array( $this, 'render_admin_notices' ) );
	}

	/**
	 * Get the frontend login page URL.
	 */
	public static function get_login_url() {
		$login_page = get_page_by_path( 'login' );
		if ( $login_page ) {
			return home_url( '/login/' );
		}
		return wc_get_page_permalink( 'myaccount' );
	}

	/**
	 * Check if an environment is local (localhost / 127.0.0.1 / XAMPP).
	 */
	public static function is_local_environment() {
		$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		return (
			false !== strpos( $host, 'localhost' ) ||
			false !== strpos( $host, '127.0.0.1' ) ||
			( defined( 'WP_ENVIRONMENT_TYPE' ) && 'local' === WP_ENVIRONMENT_TYPE )
		);
	}

	/**
	 * Filter wp_mail_from to avoid invalid "wordpress@localhost" RFC errors in PHPMailer.
	 */
	public function filter_wp_mail_from( $from_email ) {
		// If current from address contains @localhost or is invalid, supply a valid address
		if ( false !== stristr( $from_email, '@localhost' ) || ! is_email( $from_email ) ) {
			$admin_email = get_option( 'admin_email' );
			if ( is_email( $admin_email ) && false === stristr( $admin_email, '@localhost' ) ) {
				return $admin_email;
			}
			return 'noreply@jewellery-shop.local';
		}
		return $from_email;
	}

	/**
	 * Filter wp_mail_from_name to display the site name.
	 */
	public function filter_wp_mail_from_name( $from_name ) {
		if ( 'WordPress' === $from_name || empty( $from_name ) ) {
			return get_bloginfo( 'name' );
		}
		return $from_name;
	}

	/**
	 * Check if a user is verified.
	 *
	 * Note: Administrators or existing accounts created prior to this feature are considered verified.
	 *
	 * @param int|WP_User $user
	 * @return bool
	 */
	public static function is_user_verified( $user ) {
		if ( is_numeric( $user ) ) {
			$user = get_user_by( 'id', $user );
		}
		if ( ! $user || ! ( $user instanceof WP_User ) ) {
			return false;
		}

		// Admins are always exempt
		if ( user_can( $user, 'manage_options' ) ) {
			return true;
		}

		$status = get_user_meta( $user->ID, self::META_VERIFIED, true );

		// Strictly require email verification for all non-admins
		return ( '1' === strval( $status ) );
	}

	/**
	 * Triggered immediately when a new customer registers via WooCommerce.
	 *
	 * @param int   $customer_id
	 * @param array $new_customer_data
	 * @param bool  $password_generated
	 */
	public function on_customer_registered( $customer_id, $new_customer_data, $password_generated ) {
		// Mark user unverified
		update_user_meta( $customer_id, self::META_VERIFIED, '0' );

		// Remember whether password was auto-generated
		update_user_meta( $customer_id, '_password_was_generated', $password_generated ? '1' : '0' );

		// Generate token & send verification email
		$this->send_verification_email( $customer_id );

		// Set a transient flag for the current session/IP to display the post-registration notice
		set_transient( 'jewellery_reg_verify_' . $customer_id, time(), 300 );
	}

	/**
	 * Prevent WooCommerce from automatically logging the customer in after registration.
	 *
	 * @param bool $auth
	 * @param int  $customer_id
	 * @return bool
	 */
	public function prevent_auto_login( $auth, $customer_id ) {
		// Do not auto-login unverified accounts
		if ( ! self::is_user_verified( $customer_id ) ) {
			$user = get_user_by( 'id', $customer_id );
			$email = $user ? $user->user_email : '';

			// Remove default WooCommerce auto-login notices
			wc_clear_notices();

			// Add our dedicated notice informing user to verify their email
			$msg = sprintf(
				__( 'Registration successful! We have sent a verification link to <strong>%s</strong>. Please check your inbox and verify your email address to activate your account.', 'jewellery_wp' ),
				esc_html( $email )
			);

			wc_add_notice( $msg, 'notice' );

			return false;
		}
		return $auth;
	}

	/**
	 * Suppress the default WooCommerce "Customer New Account" email while unverified.
	 */
	public function suppress_default_new_account_email( $enabled, $customer, $email_obj ) {
		if ( $customer instanceof WP_User ) {
			if ( ! self::is_user_verified( $customer ) ) {
				return false;
			}
		} elseif ( is_numeric( $customer ) ) {
			if ( ! self::is_user_verified( $customer ) ) {
				return false;
			}
		}
		return $enabled;
	}

	/**
	 * Intercept login authentication and block unverified customers.
	 */
	public function check_email_verification_on_login( $user, $username, $password ) {
		// If credentials failed, return existing error
		if ( is_wp_error( $user ) || ! ( $user instanceof WP_User ) ) {
			return $user;
		}

		// Admins are exempt
		if ( user_can( $user, 'manage_options' ) ) {
			return $user;
		}

		// Check verification status
		if ( ! self::is_user_verified( $user ) ) {
			$resend_url = wp_nonce_url(
				add_query_arg(
					array(
						'action'     => 'resend_verification',
						'user_email' => rawurlencode( $user->user_email ),
					),
					wc_get_page_permalink( 'myaccount' )
				),
				'jewellery_resend_verification'
			);

			return new WP_Error(
				'unverified_email',
				sprintf(
					__( '<strong>Account unverified:</strong> Your email address (%1$s) has not been verified yet. Please check your email or <a href="%2$s" class="jewellery-resend-link"><strong>click here to resend the verification link</strong></a>.', 'jewellery_wp' ),
					esc_html( $user->user_email ),
					esc_url( $resend_url )
				)
			);
		}

		return $user;
	}

	/**
	 * Guard password reset for unverified accounts.
	 */
	public function guard_password_reset( $allow, $user_id ) {
		if ( ! self::is_user_verified( $user_id ) ) {
			return new WP_Error(
				'unverified_email_reset',
				__( 'You must verify your email address before resetting your password.', 'jewellery_wp' )
			);
		}
		return $allow;
	}

	/**
	 * Generate a fresh verification token and send the email.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function send_verification_email( $user_id ) {
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return false;
		}

		// Generate random 32-character token
		$token = wp_generate_password( 32, false );

		// Store hashed token & timestamp
		update_user_meta( $user_id, self::META_TOKEN, wp_hash_password( $token ) );
		update_user_meta( $user_id, self::META_TOKEN_SENT, time() );

		// Build verification URL
		$verify_url = add_query_arg(
			array(
				'action'  => 'verify_email',
				'user_id' => $user_id,
				'token'   => $token,
			),
			wc_get_page_permalink( 'myaccount' )
		);

		// Store for dev environment helper
		update_user_meta( $user_id, self::META_DEV_LINK, $verify_url );

		// Prepare email content
		$site_title = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$subject    = sprintf( __( 'Verify your email address - %s', 'jewellery_wp' ), $site_title );
		$name       = ! empty( $user->display_name ) ? $user->display_name : $user->user_login;

		$body = $this->get_email_html_content( array(
			'site_title' => $site_title,
			'name'       => $name,
			'email'      => $user->user_email,
			'verify_url' => $verify_url,
		) );

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $site_title . ' <' . $this->filter_wp_mail_from( '' ) . '>',
		);

		// Send email
		$sent = wp_mail( $user->user_email, $subject, $body, $headers );

		// Log locally if debug or local
		if ( ! $sent && self::is_local_environment() ) {
			error_log( sprintf( '[Email Verification] Verification link for %s: %s', $user->user_email, $verify_url ) );
		}

		return $sent;
	}

	/**
	 * Handle Verification and Resend endpoints on template_redirect.
	 */
	public function handle_verification_endpoints() {
		if ( empty( $_GET['action'] ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_GET['action'] ) );

		// ----------------------------------------------------
		// Action: VERIFY EMAIL
		// ----------------------------------------------------
		if ( 'verify_email' === $action ) {
			$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
			$token   = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';

			if ( ! $user_id || empty( $token ) ) {
				wc_add_notice( __( 'Invalid email verification link.', 'jewellery_wp' ), 'error' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			$user = get_user_by( 'id', $user_id );
			if ( ! $user ) {
				wc_add_notice( __( 'User account not found.', 'jewellery_wp' ), 'error' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// Check if already verified
			if ( '1' === strval( get_user_meta( $user_id, self::META_VERIFIED, true ) ) ) {
				wc_add_notice( __( 'Your email address is already verified. You can log in below.', 'jewellery_wp' ), 'notice' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// Check token expiry
			$sent_time = (int) get_user_meta( $user_id, self::META_TOKEN_SENT, true );
			if ( empty( $sent_time ) || ( time() - $sent_time ) > self::TOKEN_EXPIRY ) {
				$resend_url = wp_nonce_url(
					add_query_arg(
						array(
							'action'     => 'resend_verification',
							'user_email' => rawurlencode( $user->user_email ),
						),
						wc_get_page_permalink( 'myaccount' )
					),
					'jewellery_resend_verification'
				);

				wc_add_notice(
					sprintf(
						__( 'This verification link has expired (links are valid for 24 hours). <a href="%s">Click here to request a new verification email</a>.', 'jewellery_wp' ),
						esc_url( $resend_url )
					),
					'error'
				);
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// Validate token hash
			$stored_hash = get_user_meta( $user_id, self::META_TOKEN, true );
			if ( empty( $stored_hash ) || ! wp_check_password( $token, $stored_hash ) ) {
				wc_add_notice( __( 'Invalid or corrupted verification link. Please request a new verification email below.', 'jewellery_wp' ), 'error' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// Verification successful!
			update_user_meta( $user_id, self::META_VERIFIED, '1' );
			update_user_meta( $user_id, self::META_VERIFIED_AT, current_time( 'mysql' ) );
			delete_user_meta( $user_id, self::META_TOKEN );
			delete_user_meta( $user_id, self::META_TOKEN_SENT );
			delete_user_meta( $user_id, self::META_DEV_LINK );

			// Check if user had an automatically generated password and needs to set it
			$password_was_generated = '1' === get_user_meta( $user_id, '_password_was_generated', true ) || 'yes' === get_option( 'woocommerce_registration_generate_password' );

			if ( $password_was_generated ) {
				$key = get_password_reset_key( $user );
				if ( ! is_wp_error( $key ) ) {
					$set_pass_url = add_query_arg(
						array(
							'key' => $key,
							'id'  => $user_id,
						),
						wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
					);

					wc_add_notice( __( 'Email verified successfully! Please choose a password for your account to complete registration.', 'jewellery_wp' ), 'success' );
					wp_safe_redirect( $set_pass_url );
					exit;
				}
			}

			// Otherwise, inform user they can log in
			wc_add_notice( __( 'Success! Your email address has been verified. You can now log in to your account.', 'jewellery_wp' ), 'success' );
			wp_safe_redirect( add_query_arg( 'verified', '1', self::get_login_url() ) );
			exit;
		}

		// ----------------------------------------------------
		// Action: RESEND VERIFICATION
		// ----------------------------------------------------
		if ( 'resend_verification' === $action ) {
			$email = isset( $_REQUEST['user_email'] ) ? sanitize_email( wp_unslash( $_REQUEST['user_email'] ) ) : '';

			// Verify nonce if provided via link
			if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'jewellery_resend_verification' ) ) {
				wc_add_notice( __( 'Security check failed. Please submit the resend form below.', 'jewellery_wp' ), 'error' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// Verify POST form nonce if submitted via form
			if ( isset( $_POST['resend_verification_submit'] ) ) {
				if ( ! isset( $_POST['jewellery_resend_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jewellery_resend_nonce'] ) ), 'jewellery_resend_form_action' ) ) {
					wc_add_notice( __( 'Security token expired. Please try again.', 'jewellery_wp' ), 'error' );
					wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
					exit;
				}
			}

			if ( empty( $email ) || ! is_email( $email ) ) {
				wc_add_notice( __( 'Please provide a valid email address.', 'jewellery_wp' ), 'error' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			$user = get_user_by( 'email', $email );

			// If no user found by email, check by login
			if ( ! $user ) {
				$user = get_user_by( 'login', $email );
			}

			if ( ! $user ) {
				// Don't leak whether email exists; display standard message
				wc_add_notice( sprintf( __( 'If an unverified account exists for %s, a verification link has been sent. Please check your inbox.', 'jewellery_wp' ), esc_html( $email ) ), 'notice' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// If already verified
			if ( self::is_user_verified( $user ) ) {
				wc_add_notice( __( 'This email address is already verified. You can log in directly.', 'jewellery_wp' ), 'notice' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// Check rate limit
			$last_resend = (int) get_user_meta( $user->ID, self::META_LAST_RESEND, true );
			$time_since  = time() - $last_resend;
			if ( $last_resend && $time_since < self::RESEND_COOLDOWN ) {
				$wait = self::RESEND_COOLDOWN - $time_since;
				wc_add_notice( sprintf( __( 'Please wait %d seconds before requesting another verification email.', 'jewellery_wp' ), $wait ), 'error' );
				wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			// Update last resend time and send email
			update_user_meta( $user->ID, self::META_LAST_RESEND, time() );
			$this->send_verification_email( $user->ID );

			wc_add_notice( sprintf( __( 'A verification email has been sent to <strong>%s</strong>. Please check your inbox and spam folder.', 'jewellery_wp' ), esc_html( $user->user_email ) ), 'success' );
			wp_safe_redirect( add_query_arg( 'resent', '1', self::get_login_url() ) );
			exit;
		}
	}

	/**
	 * Developer / Localhost helper notice on My Account page.
	 *
	 * When testing locally in XAMPP where sendmail/SMTP is not configured, this banner
	 * provides the direct verification link for any newly registered or unverified account.
	 */
	public function render_dev_helper_notice() {
		if ( ! self::is_local_environment() ) {
			return;
		}

		// Find most recent unverified user with a dev link
		$recent_unverified = get_users( array(
			'meta_key'     => self::META_VERIFIED,
			'meta_value'   => '0',
			'number'       => 1,
			'orderby'      => 'user_registered',
			'order'        => 'DESC',
		) );

		if ( empty( $recent_unverified ) ) {
			return;
		}

		$unverified_user = $recent_unverified[0];
		$dev_link = get_user_meta( $unverified_user->ID, self::META_DEV_LINK, true );

		if ( empty( $dev_link ) ) {
			return;
		}

		?>
		<div class="jewellery-dev-verify-banner">
			<div class="jewellery-dev-verify-inner">
				<span class="jewellery-dev-tag">LOCAL TEST MODE</span>
				<p>
					<strong>Pending Email Verification:</strong> Account <code><?php echo esc_html( $unverified_user->user_email ); ?></code> is pending verification.
					<br>
					<a href="<?php echo esc_url( $dev_link ); ?>" class="button jewellery-btn-quick-verify">
						&check; Click here to simulate email verification
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a "Resend Verification Email" widget beneath the customer login/register forms.
	 */
	public function render_resend_verification_form() {
		?>
		<div class="jewellery-resend-wrapper" id="resend-verification-section">
			<div class="jewellery-resend-card">
				<button type="button" class="jewellery-resend-toggle" onclick="var el=document.getElementById('jewellery-resend-box'); el.style.display = (el.style.display==='block' ? 'none' : 'block');">
					<?php esc_html_e( "Didn't receive verification email? Click to resend", 'jewellery_wp' ); ?>
				</button>
				<div id="jewellery-resend-box" class="jewellery-resend-box" style="display: none;">
					<h4><?php esc_html_e( 'Resend Verification Email', 'jewellery_wp' ); ?></h4>
					<p><?php esc_html_e( 'Enter the email address you registered with, and we will send you a fresh verification link.', 'jewellery_wp' ); ?></p>
					<form method="post" action="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
						<input type="hidden" name="action" value="resend_verification">
						<?php wp_nonce_field( 'jewellery_resend_form_action', 'jewellery_resend_nonce' ); ?>
						<p class="form-row form-row-wide">
							<label for="resend_user_email"><?php esc_html_e( 'Registered Email address', 'jewellery_wp' ); ?>&nbsp;<span class="required">*</span></label>
							<input type="email" class="input-text" name="user_email" id="resend_user_email" required autocomplete="email" placeholder="you@example.com">
						</p>
						<p class="form-row">
							<button type="submit" class="woocommerce-Button button" name="resend_verification_submit" value="1">
								<?php esc_html_e( 'Resend Verification Link', 'jewellery_wp' ); ?>
							</button>
						</p>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue styling for notices, badges, and the verification UI.
	 */
	public function enqueue_styles() {
		if ( ! is_account_page() ) {
			return;
		}

		$custom_css = "
		/* Email Verification UI Styles */
		.woocommerce-verification-notice-text {
			font-size: 13px;
			color: #e6dfd5;
			margin-bottom: 14px;
			line-height: 1.5;
		}
		.jewellery-dev-verify-banner {
			background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(197, 168, 128, 0.1));
			border: 1px solid rgba(212, 175, 55, 0.4);
			border-radius: 8px;
			padding: 14px 18px;
			margin-bottom: 25px;
			color: #e6dfd5;
			font-size: 14px;
		}
		.jewellery-dev-verify-inner {
			display: flex;
			flex-direction: column;
			gap: 8px;
		}
		.jewellery-dev-tag {
			align-self: flex-start;
			background: #d4af37;
			color: #1a1a1a;
			font-size: 11px;
			font-weight: 700;
			padding: 2px 8px;
			border-radius: 4px;
			letter-spacing: 0.5px;
			text-transform: uppercase;
		}
		.jewellery-btn-quick-verify {
			display: inline-block;
			background: #d4af37 !important;
			color: #111 !important;
			padding: 6px 14px !important;
			border-radius: 4px !important;
			font-weight: 600 !important;
			font-size: 13px !important;
			text-decoration: none !important;
			margin-top: 6px;
			border: none !important;
			transition: all 0.2s ease;
		}
		.jewellery-btn-quick-verify:hover {
			background: #c5a880 !important;
			color: #000 !important;
			transform: translateY(-1px);
		}
		.jewellery-resend-wrapper {
			margin-top: 35px;
			padding-top: 20px;
			border-top: 1px dashed rgba(255, 255, 255, 0.15);
		}
		.jewellery-resend-card {
			text-align: center;
		}
		.jewellery-resend-toggle {
			background: transparent;
			border: none;
			color: #d4af37;
			text-decoration: underline;
			font-size: 14px;
			cursor: pointer;
			padding: 6px 12px;
			transition: color 0.2s;
		}
		.jewellery-resend-toggle:hover {
			color: #ffffff;
		}
		.jewellery-resend-box {
			max-width: 500px;
			margin: 20px auto 0;
			background: rgba(30, 26, 23, 0.95);
			border: 1px solid rgba(212, 175, 55, 0.3);
			border-radius: 8px;
			padding: 24px;
			text-align: left;
			color: #e6dfd5;
			box-shadow: 0 10px 30px rgba(0,0,0,0.5);
		}
		.jewellery-resend-box h4 {
			color: #d4af37;
			margin-top: 0;
			margin-bottom: 8px;
			font-size: 18px;
		}
		.jewellery-resend-box p {
			font-size: 13px;
			line-height: 1.5;
			color: #ccc;
			margin-bottom: 16px;
		}
		.jewellery-resend-link {
			color: #d4af37 !important;
			text-decoration: underline !important;
		}
		.woocommerce-notices-wrapper .woocommerce-message,
		.woocommerce-notices-wrapper .woocommerce-info,
		.woocommerce-notices-wrapper .woocommerce-error {
			border-radius: 6px;
			padding: 12px 18px;
			margin-bottom: 25px;
			line-height: 1.5;
		}
		";

		wp_add_inline_style( 'theme-style', $custom_css );
	}

	/**
	 * HTML Email Content Template.
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_email_html_content( $args ) {
		$site_title = esc_html( $args['site_title'] );
		$name       = esc_html( $args['name'] );
		$verify_url = esc_url( $args['verify_url'] );

		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title><?php echo $site_title; ?></title>
		</head>
		<body style="margin:0;padding:0;background-color:#1c1917;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#333333;">
			<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#1c1917;padding:40px 10px;">
				<tr>
					<td align="center">
						<table role="presentation" width="100%" style="max-width:600px;background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.35);" cellspacing="0" cellpadding="0">
							<!-- Header -->
							<tr>
								<td style="background:linear-gradient(135deg, #2b2622 0%, #151312 100%);padding:35px 30px;text-align:center;border-bottom:3px solid #d4af37;">
									<h1 style="margin:0;color:#d4af37;font-size:24px;font-weight:600;letter-spacing:1px;text-transform:uppercase;">
										<?php echo $site_title; ?>
									</h1>
								</td>
							</tr>
							<!-- Body -->
							<tr>
								<td style="padding:40px 35px;line-height:1.6;color:#374151;font-size:15px;">
									<p style="margin-top:0;font-size:18px;font-weight:600;color:#111827;">
										Hello <?php echo $name; ?>,
									</p>
									<p>
										Thank you for registering an account with <strong><?php echo $site_title; ?></strong>.
									</p>
									<p>
										To ensure the security of your account and activate all customer features, please verify your email address by clicking the button below:
									</p>
									<!-- CTA Button -->
									<div style="text-align:center;margin:35px 0;">
										<a href="<?php echo $verify_url; ?>" style="background-color:#d4af37;background:linear-gradient(135deg, #d4af37 0%, #aa820a 100%);color:#ffffff;text-decoration:none;padding:14px 34px;border-radius:30px;font-size:15px;font-weight:bold;letter-spacing:0.5px;display:inline-block;box-shadow:0 4px 15px rgba(212,175,55,0.4);">
											Verify Email Address
										</a>
									</div>
									<p style="font-size:13px;color:#6b7280;line-height:1.5;">
										This verification link will expire in <strong>24 hours</strong>. If you did not create this account, no further action is required and you can safely ignore this email.
									</p>
									<hr style="border:none;border-top:1px solid #e5e7eb;margin:25px 0;">
									<p style="font-size:12px;color:#9ca3af;word-break:break-all;">
										If you are having trouble clicking the button, copy and paste this URL into your web browser:<br>
										<a href="<?php echo $verify_url; ?>" style="color:#d4af37;text-decoration:none;"><?php echo $verify_url; ?></a>
									</p>
								</td>
							</tr>
							<!-- Footer -->
							<tr>
								<td style="background-color:#f9fafb;padding:20px 30px;text-align:center;font-size:12px;color:#9ca3af;border-top:1px solid #f3f4f6;">
									&copy; <?php echo date( 'Y' ); ?> <?php echo $site_title; ?>. All rights reserved.
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}

	// ========================================================
	// ADMIN USERS LIST INTEGRATION
	// ========================================================

	/**
	 * Add "Email Verified" column to WP Admin Users list.
	 */
	public function add_admin_users_column( $columns ) {
		$columns['email_verified'] = __( 'Email Verified', 'jewellery_wp' );
		return $columns;
	}

	/**
	 * Render "Email Verified" column in WP Admin Users list.
	 */
	public function render_admin_users_column( $output, $column_name, $user_id ) {
		if ( 'email_verified' === $column_name ) {
			$user = get_user_by( 'id', $user_id );
			if ( ! $user ) {
				return $output;
			}

			// Admin is exempt
			if ( user_can( $user, 'manage_options' ) ) {
				return '<span style="color:#6b7280;font-size:12px;">' . esc_html__( 'Exempt (Admin)', 'jewellery_wp' ) . '</span>';
			}

			$verified = self::is_user_verified( $user );
			if ( $verified ) {
				$verified_at = get_user_meta( $user_id, self::META_VERIFIED_AT, true );
				$tooltip = $verified_at ? sprintf( __( 'Verified at: %s', 'jewellery_wp' ), $verified_at ) : '';
				return '<span style="display:inline-block;background:#e6f4ea;color:#137333;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;" title="' . esc_attr( $tooltip ) . '">&#10003; ' . esc_html__( 'Verified', 'jewellery_wp' ) . '</span>';
			} else {
				return '<span style="display:inline-block;background:#fef7e0;color:#b06000;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">&#9888; ' . esc_html__( 'Unverified', 'jewellery_wp' ) . '</span>';
			}
		}
		return $output;
	}

	/**
	 * Add quick row actions in WP Admin Users list.
	 */
	public function add_admin_user_row_actions( $actions, $user ) {
		if ( ! current_user_can( 'manage_options' ) || user_can( $user, 'manage_options' ) ) {
			return $actions;
		}

		if ( ! self::is_user_verified( $user ) ) {
			// Manual verify action
			$verify_url = wp_nonce_url(
				add_query_arg(
					array(
						'action'  => 'jewellery_admin_verify_user',
						'user_id' => $user->ID,
					),
					admin_url( 'users.php' )
				),
				'jewellery_admin_verify_' . $user->ID
			);
			$actions['manual_verify'] = '<a href="' . esc_url( $verify_url ) . '" style="color:#137333;font-weight:bold;">' . esc_html__( 'Mark Verified', 'jewellery_wp' ) . '</a>';

			// Resend verification action
			$resend_url = wp_nonce_url(
				add_query_arg(
					array(
						'action'  => 'jewellery_admin_resend_verification',
						'user_id' => $user->ID,
					),
					admin_url( 'users.php' )
				),
				'jewellery_admin_resend_' . $user->ID
			);
			$actions['admin_resend'] = '<a href="' . esc_url( $resend_url ) . '" style="color:#b06000;">' . esc_html__( 'Resend Verification', 'jewellery_wp' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Process admin actions from Users list.
	 */
	public function handle_admin_actions() {
		if ( ! current_user_can( 'manage_options' ) || empty( $_GET['action'] ) ) {
			return;
		}

		$action  = sanitize_text_field( wp_unslash( $_GET['action'] ) );
		$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;

		if ( ! $user_id ) {
			return;
		}

		if ( 'jewellery_admin_verify_user' === $action ) {
			check_admin_referer( 'jewellery_admin_verify_' . $user_id );
			update_user_meta( $user_id, self::META_VERIFIED, '1' );
			update_user_meta( $user_id, self::META_VERIFIED_AT, current_time( 'mysql' ) );
			delete_user_meta( $user_id, self::META_TOKEN );
			delete_user_meta( $user_id, self::META_TOKEN_SENT );
			delete_user_meta( $user_id, self::META_DEV_LINK );

			wp_safe_redirect( add_query_arg( 'jewellery_verified_notice', '1', admin_url( 'users.php' ) ) );
			exit;
		}

		if ( 'jewellery_admin_resend_verification' === $action ) {
			check_admin_referer( 'jewellery_admin_resend_' . $user_id );
			$this->send_verification_email( $user_id );

			wp_safe_redirect( add_query_arg( 'jewellery_resent_notice', '1', admin_url( 'users.php' ) ) );
			exit;
		}
	}

	/**
	 * Render admin notices in Users list after action.
	 */
	public function render_admin_notices() {
		if ( ! empty( $_GET['jewellery_verified_notice'] ) ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Customer has been marked as email verified.', 'jewellery_wp' ); ?></p>
			</div>
			<?php
		}
		if ( ! empty( $_GET['jewellery_resent_notice'] ) ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Verification email has been resent to the customer.', 'jewellery_wp' ); ?></p>
			</div>
			<?php
		}
	}
}

// Initialize the email verification system
Jewellery_Email_Verification::get_instance();
