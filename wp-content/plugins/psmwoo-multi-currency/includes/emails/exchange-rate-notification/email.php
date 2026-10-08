<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PSMWOO_MC_Exchange_Rate_Notification' ) ) {

	class PSMWOO_MC_Exchange_Rate_Notification extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'psmwmc_exchange_rate_notification';
			$this->title          = '[PSMWMC] Exchange Rate Notification (PRO)';
			$this->description    = 'Email sent for exchange rate updates.';
			$this->subject        = 'Exchange Rates Updated';
			$this->template_html  = 'html-template.php';
			$this->template_base  = plugin_dir_path( __FILE__ );
			$this->recipient      = $this->get_option( 'recipient', get_option( 'admin_email' ) );
			$this->placeholders   = array(
				'{customer_name}'  => '',
				'{custom_content}' => '',
			);

			parent::__construct();
		}

		/**
		 * Email type options.
		 *
		 * @return array
		 */
		public function get_email_type_options() {
			return array(
				'html' => __( 'HTML', 'psmwoo-multi-currency' ),
			);
		}

		/**
		 * Get the email content.
		 *
		 * @return string
		 */
		public function get_content_html() {
			$admin = get_user_by( 'email', get_option( 'admin_email' ) );
			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'customer_name'  => $admin ? $admin->display_name : '',
					'custom_content' => 'The exchange rates have been updated successfully.',
					'email_heading'  => $this->get_heading(),
					'email'          => $this,
				),
				'',
				$this->template_base
			);
			return ob_get_clean();
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'psmwoo-multi-currency' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'    => array(
					'title'    => __( 'Enable/Disable', 'psmwoo-multi-currency' ),
					'type'     => 'checkbox',
					'label'    => __( 'Enable this email notification', 'psmwoo-multi-currency' ),
					'default'  => 'no',
					'disabled' => true,
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'psmwoo-multi-currency' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
					'disabled'    => true,
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'psmwoo-multi-currency' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'psmwoo-multi-currency' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
					'disabled'    => true,
				),
			);
		}
	}
}
