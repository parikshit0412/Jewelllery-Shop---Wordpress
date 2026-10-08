<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

// load translation functionality.
require_once PSMWOO_MC_ABSPATH . 'includes/translations.php';

// load helper functions.
require_once PSMWOO_MC_ABSPATH . 'includes/helpers.php';

// load installation functions.
require_once PSMWOO_MC_ABSPATH . 'includes/installation.php';

// load rest api functionality.
if ( PSMFR_REST_REQUEST ) {
	foreach ( glob( PSMWOO_MC_ABSPATH . 'includes/rest-api/*.php' ) as $psmwoo_mc_filename ) {
		include_once $psmwoo_mc_filename;
	}
}

// load ajax functionality.
if ( wp_doing_ajax() ) {
	require_once PSMWOO_MC_ABSPATH . 'includes/ajax.php';
}

// load gutenberg block.
require_once PSMWOO_MC_ABSPATH . 'includes/blocks.php';

// load compatibility classes.
require_once PSMWOO_MC_ABSPATH . 'includes/compatibility/autoloader.php';

// load exchange rate functionality.
require_once PSMWOO_MC_ABSPATH . 'includes/get-exchange-rate.php';

// load admin user interface.
if ( PSMFR_ADMIN_INTERFACE ) {
	require_once PSMWOO_MC_ABSPATH . 'includes/admin-pages/settings.php';
	require_once PSMWOO_MC_ABSPATH . 'includes/admin-pages/order-info-widget.php';
}

// load analytics functionality.
require_once PSMWOO_MC_ABSPATH . 'includes/admin-pages/analytics.php';

// load frontend functionality.
foreach ( glob( PSMWOO_MC_ABSPATH . 'includes/frontend/*.php' ) as $psmwoo_mc_filename ) {
	include_once $psmwoo_mc_filename;
}

require_once PSMWOO_MC_ABSPATH . 'includes/emails/exchange-rate-notification/actions.php';
