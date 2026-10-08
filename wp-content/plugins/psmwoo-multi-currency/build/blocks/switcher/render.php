<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( is_admin() ) {
	return;
}
// render the switcher.
PSMWOO_MC_Switcher::render( $attributes );
