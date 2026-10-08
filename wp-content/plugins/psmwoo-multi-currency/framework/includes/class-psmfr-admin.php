<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR_Admin {

	/**
	 * Initializes the Psmfr_Admin class.
	 *
	 * This method is called when the plugin is loaded and is responsible for executing
	 * any necessary code.
	 *
	 * @return void
	 */
	public static function init() {

		// Register the admin menu.
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_menu' ) );

		// Enqueue the scripts and styles for the admin page.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// add the framework object to the global scope.
		add_action( 'admin_enqueue_scripts', array( 'PSMFR_Helper', 'render_framework_js_object' ) );
	}

	/**
	 * Registers the admin menu for the plugin.
	 *
	 * This method is called during the admin_init action and is responsible for adding
	 * a menu page to the WordPress admin menu.
	 *
	 * @return void
	 */
	public static function register_admin_menu() {

		$menu_icon = 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgOTY0IDgzMyIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMzc1Ljk3NyAwQzM4Ny4zNyAwIDM5NS4zNDUgMy41ODA3MyAzOTkuOTAyIDEwLjc0MjJDNDA0LjQ2IDE3LjU3ODEgNDA2LjczOCAzMS4wODcyIDQwNi43MzggNTEuMjY5NUM0MDYuNzM4IDY2LjI0MzUgNDA1Ljc2MiA3Ny40NzQgNDAzLjgwOSA4NC45NjA5QzQwMS44NTUgOTIuNDQ3OSAzOTguNiA5Ny40OTM1IDM5NC4wNDMgMTAwLjA5OEMzODkuODExIDEwMi43MDIgMzgzLjc4OSAxMDQuMDA0IDM3NS45NzcgMTA0LjAwNEMzNDQuMDc2IDEwNC4wMDQgMzE5LjMzNiAxMDcuNTg1IDMwMS43NTggMTE0Ljc0NkMyODQuNTA1IDEyMS41ODIgMjcyLjYyNCAxMzIuNDg3IDI2Ni4xMTMgMTQ3LjQ2MUMyNTkuOTI4IDE2Mi4xMDkgMjU3LjQ4NyAxODIuMTI5IDI1OC43ODkgMjA3LjUyTDI2MS4yMyAyNTQuODgzTDI2MS43MTkgMjY3LjU3OEMyNjEuNzE5IDMwMS43NTggMjUyLjkzIDMzMC4wNzggMjM1LjM1MiAzNTIuNTM5QzIxNy43NzMgMzc0LjY3NCAxODguOTY1IDM5MS40MzkgMTQ4LjkyNiA0MDIuODMyVjQwNy43MTVDMTg4Ljk2NSA0MTguNzgzIDIxNy45MzYgNDM1LjU0NyAyMzUuODQgNDU4LjAwOEMyNTQuMDY5IDQ4MC40NjkgMjYzLjE4NCA1MDguMzAxIDI2My4xODQgNTQxLjUwNEMyNjMuMTg0IDU0OC4wMTQgMjYzLjAyMSA1NTIuODk3IDI2Mi42OTUgNTU2LjE1MkwyNTguMzAxIDYyMC4xMTdDMjU3Ljk3NSA2MjQuMDIzIDI1Ny44MTIgNjI5Ljg4MyAyNTcuODEyIDYzNy42OTVDMjU3LjgxMiA2NTkuNTA1IDI2MS41NTYgNjc2LjkyMSAyNjkuMDQzIDY4OS45NDFDMjc2LjUzIDcwMi45NjIgMjg4LjkgNzEyLjU2NSAzMDYuMTUyIDcxOC43NUMzMjMuNDA1IDcyNS4yNiAzNDYuNjggNzI4LjUxNiAzNzUuOTc3IDcyOC41MTZDMzgzLjc4OSA3MjguNTE2IDM4OS44MTEgNzI5LjY1NSAzOTQuMDQzIDczMS45MzRDMzk4LjYgNzM0LjUzOCA0MDEuODU1IDczOS41ODMgNDAzLjgwOSA3NDcuMDdDNDA1Ljc2MiA3NTQuNTU3IDQwNi43MzggNzY1Ljc4OCA0MDYuNzM4IDc4MC43NjJDNDA2LjczOCA4MDAuOTQ0IDQwNC40NiA4MTQuNjE2IDM5OS45MDIgODIxLjc3N0MzOTUuMzQ1IDgyOC45MzkgMzg3LjM3IDgzMi41MiAzNzUuOTc3IDgzMi41MkMyOTQuMjcxIDgzMi41MiAyMzMuNzI0IDgxOC4wMzQgMTk0LjMzNiA3ODkuMDYyQzE1NC45NDggNzYwLjQxNyAxMzUuMjU0IDcxNS44MiAxMzUuMjU0IDY1NS4yNzNDMTM1LjI1NCA2NDUuODMzIDEzNS43NDIgNjM2LjA2OCAxMzYuNzE5IDYyNS45NzdMMTQzLjA2NiA1NTYuMTUyQzE0My4zOTIgNTUzLjU0OCAxNDMuNTU1IDU0OS42NDIgMTQzLjU1NSA1NDQuNDM0QzE0My41NTUgNTE2Ljc2NCAxMzMuNzg5IDQ5NS40NDMgMTE0LjI1OCA0ODAuNDY5Qzk1LjA1MjEgNDY1LjE2OSA2Ny4yMjAxIDQ1Ny41MiAzMC43NjE3IDQ1Ny41MkMyMi45NDkyIDQ1Ny41MiAxNi45MjcxIDQ1Ni4yMTcgMTIuNjk1MyA0NTMuNjEzQzguNDYzNTQgNDUwLjY4NCA1LjIwODMzIDQ0NS40NzUgMi45Mjk2OSA0MzcuOTg4QzAuOTc2NTYyIDQzMC41MDEgMCA0MTkuNTk2IDAgNDA1LjI3M0MwIDM5MC45NTEgMC45NzY1NjIgMzgwLjIwOCAyLjkyOTY5IDM3My4wNDdDNS4yMDgzMyAzNjUuNTYgOC40NjM1NCAzNjAuNTE0IDEyLjY5NTMgMzU3LjkxQzE2LjkyNzEgMzU0Ljk4IDIyLjk0OTIgMzUzLjUxNiAzMC43NjE3IDM1My41MTZDNjcuMjIwMSAzNTMuNTE2IDk0LjcyNjYgMzQ2LjAyOSAxMTMuMjgxIDMzMS4wNTVDMTMyLjE2MSAzMTYuMDgxIDE0MS42MDIgMjk0Ljc1OSAxNDEuNjAyIDI2Ny4wOUMxNDEuNjAyIDI2MS44ODIgMTQxLjQzOSAyNTcuODEyIDE0MS4xMTMgMjU0Ljg4M0wxMzYuMjMgMjAxLjY2QzEzNS4yNTQgMTkxLjg5NSAxMzQuNzY2IDE4Mi40NTQgMTM0Ljc2NiAxNzMuMzRDMTM0Ljc2NiAxMTQuNDIxIDE1NC40NiA3MC44MDA4IDE5My44NDggNDIuNDgwNUMyMzMuMjM2IDE0LjE2MDIgMjkzLjk0NSAwIDM3NS45NzcgMFoiIGZpbGw9ImN1cnJlbnRDb2xvciIvPjxwYXRoIGQ9Ik01ODcuNSAwQzY2OS41MzEgMCA3MzAuMjQxIDE0LjE2MDIgNzY5LjYyOSA0Mi40ODA1QzgwOS4wMTcgNzAuODAwOCA4MjguNzExIDExNC40MjEgODI4LjcxMSAxNzMuMzRDODI4LjcxMSAxODIuNDU0IDgyOC4yMjMgMTkxLjg5NSA4MjcuMjQ2IDIwMS42Nkw4MjIuMzYzIDI1NC44ODNDODIyLjAzOCAyNTcuODEyIDgyMS44NzUgMjYxLjg4MiA4MjEuODc1IDI2Ny4wOUM4MjEuODc1IDI5NC43NTkgODMxLjE1MiAzMTYuMDgxIDg0OS43MDcgMzMxLjA1NUM4NjguNTg3IDM0Ni4wMjkgODk2LjI1NyAzNTMuNTE2IDkzMi43MTUgMzUzLjUxNkM5NDAuNTI3IDM1My41MTYgOTQ2LjU0OSAzNTQuOTggOTUwLjc4MSAzNTcuOTFDOTU1LjAxMyAzNjAuNTE0IDk1OC4xMDUgMzY1LjU2IDk2MC4wNTkgMzczLjA0N0M5NjIuMzM3IDM4MC4yMDggOTYzLjQ3NyAzOTAuOTUxIDk2My40NzcgNDA1LjI3M0M5NjMuNDc3IDQxOS41OTYgOTYyLjMzNyA0MzAuNTAxIDk2MC4wNTkgNDM3Ljk4OEM5NTguMTA1IDQ0NS40NzUgOTU1LjAxMyA0NTAuNjg0IDk1MC43ODEgNDUzLjYxM0M5NDYuNTQ5IDQ1Ni4yMTcgOTQwLjUyNyA0NTcuNTIgOTMyLjcxNSA0NTcuNTJDODk2LjI1NyA0NTcuNTIgODY4LjI2MiA0NjUuMTY5IDg0OC43MyA0ODAuNDY5QzgyOS41MjUgNDk1LjQ0MyA4MTkuOTIyIDUxNi43NjQgODE5LjkyMiA1NDQuNDM0QzgxOS45MjIgNTQ5LjY0MiA4MjAuMDg1IDU1My41NDggODIwLjQxIDU1Ni4xNTJMODI2Ljc1OCA2MjUuOTc3QzgyNy43MzQgNjM2LjA2OCA4MjguMjIzIDY0NS44MzMgODI4LjIyMyA2NTUuMjczQzgyOC4yMjMgNzE1LjgyIDgwOC41MjkgNzYwLjQxNyA3NjkuMTQxIDc4OS4wNjJDNzI5Ljc1MyA4MTguMDM0IDY2OS4yMDYgODMyLjUyIDU4Ny41IDgzMi41MkM1NzYuMTA3IDgzMi41MiA1NjguMTMyIDgyOC45MzkgNTYzLjU3NCA4MjEuNzc3QzU1OS4wMTcgODE0LjYxNiA1NTYuNzM4IDgwMC45NDQgNTU2LjczOCA3ODAuNzYyQzU1Ni43MzggNzY1Ljc4OCA1NTcuNzE1IDc1NC41NTcgNTU5LjY2OCA3NDcuMDdDNTYxLjYyMSA3MzkuNTgzIDU2NC43MTQgNzM0LjUzOCA1NjguOTQ1IDczMS45MzRDNTczLjUwMyA3MjkuNjU1IDU3OS42ODggNzI4LjUxNiA1ODcuNSA3MjguNTE2QzYxNi43OTcgNzI4LjUxNiA2NDAuMDcyIDcyNS4yNiA2NTcuMzI0IDcxOC43NUM2NzQuNTc3IDcxMi41NjUgNjg2Ljk0NyA3MDIuOTYyIDY5NC40MzQgNjg5Ljk0MUM3MDEuOTIxIDY3Ni45MjEgNzA1LjY2NCA2NTkuNTA1IDcwNS42NjQgNjM3LjY5NUM3MDUuNjY0IDYyOS44ODMgNzA1LjUwMSA2MjQuMDIzIDcwNS4xNzYgNjIwLjExN0w3MDAuNzgxIDU1Ni4xNTJDNzAwLjQ1NiA1NTIuODk3IDcwMC4yOTMgNTQ4LjAxNCA3MDAuMjkzIDU0MS41MDRDNzAwLjI5MyA1MDguMzAxIDcwOS4yNDUgNDgwLjQ2OSA3MjcuMTQ4IDQ1OC4wMDhDNzQ1LjM3OCA0MzUuNTQ3IDc3NC41MTIgNDE4Ljc4MyA4MTQuNTUxIDQwNy43MTVWNDAyLjgzMkM3NzEuOTA4IDM5MC43ODggNzQxLjk2IDM3Mi41NTkgNzI0LjcwNyAzNDguMTQ1QzcwNy43OCAzMjMuNzMgNzAwLjI5MyAyOTIuNjQzIDcwMi4yNDYgMjU0Ljg4M0w3MDQuNjg4IDIwNy41MkM3MDUuMDEzIDIwNC4yNjQgNzA1LjE3NiAxOTkuNzA3IDcwNS4xNzYgMTkzLjg0OEM3MDUuMTc2IDE3Mi4wMzggNzAxLjQzMiAxNTQuNzg1IDY5My45NDUgMTQyLjA5QzY4Ni43ODQgMTI5LjA2OSA2NzQuNzQgMTE5LjQ2NiA2NTcuODEyIDExMy4yODFDNjQwLjg4NSAxMDcuMDk2IDYxNy40NDggMTA0LjAwNCA1ODcuNSAxMDQuMDA0QzU3OS42ODggMTA0LjAwNCA1NzMuNTAzIDEwMi43MDIgNTY4Ljk0NSAxMDAuMDk4QzU2NC43MTQgOTcuNDkzNSA1NjEuNjIxIDkyLjQ0NzkgNTU5LjY2OCA4NC45NjA5QzU1Ny43MTUgNzcuNDc0IDU1Ni43MzggNjYuMjQzNSA1NTYuNzM4IDUxLjI2OTVDNTU2LjczOCAzMS4wODcyIDU1OS4wMTcgMTcuNTc4MSA1NjMuNTc0IDEwLjc0MjJDNTY4LjEzMiAzLjU4MDczIDU3Ni4xMDcgMCA1ODcuNSAwWiIgZmlsbD0iY3VycmVudENvbG9yIi8+PC9zdmc+';

		// collect all submenus.
		$submenus = apply_filters( 'psmplugins_admin_submenus', array() );
		$submenus[] = array(
			'page_title' => esc_attr__( 'Framework', 'psmwoo-multi-currency' ),
			'menu_title' => esc_attr__( 'Framework', 'psmwoo-multi-currency' ),
			'capability' => 'manage_woocommerce',
			'menu_slug'  => 'psmfr',
			'callback'   => array( __CLASS__, 'render_admin_page' ),
		);

		/**
		 * Other Plugins submenu
		 */
		$submenus[] = array(
			'page_title' => esc_attr__( 'Other Plugins', 'psmwoo-multi-currency' ),
			'menu_title' => '<strong style="color:#F1C40F;">' . esc_html__( 'Other Plugins', 'psmwoo-multi-currency' ) . '</strong>',
			'capability' => 'manage_woocommerce',
			'menu_slug'  => 'psmwoo-other-plugins',
			'callback'   => array( __CLASS__, 'other_plugins_page_callback' ),
		);

		// set the default submenu as first submenu.
		$default_submenu = $submenus[0];

		// add menu page.
		add_menu_page(
			esc_attr__( 'PSM Plugins', 'psmwoo-multi-currency' ),
			esc_attr__( 'PSM Plugins', 'psmwoo-multi-currency' ),
			$default_submenu['capability'],
			$default_submenu['menu_slug'],
			$default_submenu['callback'],
			$menu_icon,
			40
		);

		// add submenus.
		foreach ( $submenus as $submenu ) {
			add_submenu_page(
				$default_submenu['menu_slug'],
				$submenu['page_title'],
				$submenu['menu_title'],
				$submenu['capability'],
				$submenu['menu_slug'],
				$submenu['callback']
			);
		}
	}

	/**
	 * Enqueues the scripts and styles for the admin page.
	 *
	 * This method is called during the admin_enqueue_scripts action and is responsible
	 * for enqueuing the necessary scripts and styles for the admin page.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public static function enqueue_scripts( $hook ) {

		// This is dummy css file loaded on every admin page. Main purpose is to load global inline styles using "psmfr" handle.
		wp_enqueue_style(
			'psmfr',
			PSMFR_PLUGIN_URL . 'framework/index.css',
			array(),
			'1.0.0'
		);

		// Load scripts and styles only on the settings page.
		if ( preg_match( '/psmfr$/', $hook ) ) {
			// Add a class to the body for styling purposes.
			add_filter(
				'admin_body_class',
				function ( $classes ) {
					$classes .= ' toplevel-psmfr-page';
					return $classes;
				}
			);

			// Load asset file.
			$assets = require PSMFR_PLUGIN_ABSPATH . '/build/framework/index.asset.php';

			// Enqueue the main admin page styles.
			wp_enqueue_style(
				'psmfr-admin-page',
				PSMFR_PLUGIN_URL . 'build/framework/index' . ( is_rtl() ? '-rtl.css' : '.css' ),
				array(),
				$assets['version']
			);

			// Enqueue the main admin page script.
			wp_enqueue_script(
				'psmfr-admin-page',
				PSMFR_PLUGIN_URL . 'build/framework/index.js',
				$assets['dependencies'],
				$assets['version'],
				true
			);

			// Load script translations.
			wp_set_script_translations( 'psmfr-admin-page', PSMFR_TEXTDOMAIN );
		}
	}

	/**
	 * Renders the submenu page for the plugin.
	 *
	 * This method is called when the submenu page is accessed and is responsible for
	 * displaying the content of the submenu page.
	 *
	 * @return void
	 */
	public static function render_admin_page() {
		echo '<div id="psmfr-admin-page"></div>';
	}



	/**
	 * Other plugins page callback.
	 *
	 * @return void
	 */
	public static function other_plugins_page_callback() {
		?>
		<section class="psmraq-os-plugin-showcase">
			<header class="psmraq-os-showcase-header">
				<h2>Our other plugins</h2>
				<p>From store enhancements to customer support, our plugins are built to help you sell better, communicate faster, and manage your business more efficiently.</p>
			</header>

			<div class="psmraq-os-grid-container">
				<div class="psmraq-os-modern-card">
					<div class="psmraq-os-image-wrapper">
						<img src="<?php echo esc_url( PSMFR_PLUGIN_URL . '/framework/src/images/multi-currency-banner.webp' ); ?>" alt="Multi-Currency Plugin" class="psmraq-os-card-img">
					</div>
					<div class="psmraq-os-card-body">
						<h3>Multi Currency Switcher for WooCommerce</h3>
						<p>Offer your customers a seamless multi-currency shopping experience. This plugin automatically updates exchange rates and detects customer location for easy global selling.</p>
						<a href="https://psmplugins.com/multi-currency-for-woocommerce" target="__blank" class="psmraq-os-card-link">View Plugin</a>
					</div>
				</div>

				<div class="psmraq-os-modern-card">
					<div class="psmraq-os-image-wrapper">
						<img src="<?php echo esc_url( PSMFR_PLUGIN_URL . '/framework/src/images/request-a-quote-banner.webp' ); ?>" alt="Request a Quote Plugin" class="psmraq-os-card-img">
					</div>
					<div class="psmraq-os-card-body">
						<h3>Request a Quote for WooCommerce</h3>
						<p>Turn your store into a negotiation hub. Allow customers to build custom inquiry lists for bulk orders and convert quotes to orders with a single click.</p>
						<a href="https://psmplugins.com/request-a-quote-for-woocommerce/" target="__blank" class="psmraq-os-card-link">View Plugin</a>
					</div>
				</div>

				<div class="psmraq-os-modern-card">
					<div class="psmraq-os-image-wrapper">
						<img src="<?php echo esc_url( PSMFR_PLUGIN_URL . '/framework/src/images/supportcandy-banner.webp' ); ?>" alt="SupportCandy Plugin" class="psmraq-os-card-img">
					</div>
					<div class="psmraq-os-card-body">
						<h3>SupportCandy – Helpdesk & Customer Support Ticket System</h3>
						<p>Streamline your customer service with a professional helpdesk. Organize, track, and resolve tickets efficiently directly from your website dashboard.</p>
						<a href="https://psmplugins.com/supportcandy/" target="__blank" class="psmraq-os-card-link">View Plugin</a>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
PSMFR_Admin::init();
