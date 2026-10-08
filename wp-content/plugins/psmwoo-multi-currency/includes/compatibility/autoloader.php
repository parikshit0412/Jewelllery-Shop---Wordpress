<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

// WooCommerce Subscriptions compatibility.
if ( class_exists( 'WC_Subscriptions' ) ) {
	require_once __DIR__ . '/woo-subscriptions.php';
}

// WPML compatibility.
if ( class_exists( 'SitePress' ) ) {
	require_once __DIR__ . '/wpml.php';
}

// Polylang compatibility.
if ( class_exists( 'Polylang' ) ) {
	require_once __DIR__ . '/polylang.php';
}

// WooCommerce Table Rate Shipping compatibility.
if ( class_exists( 'WC_Table_Rate_Shipping' ) ) {
	require_once __DIR__ . '/woo-table-rate-shipping.php';
}

// WooCommerce Product Bundles compatibility.
if ( class_exists( 'WC_Bundles' ) ) {
	require_once __DIR__ . '/woo-product-bundles.php';
}

// WooCommerce Table Rate Shipping compatibility.
if ( defined( 'WC_NYP_PLUGIN_FILE' ) ) {
	require_once __DIR__ . '/woo-name-your-price.php';
}

// WooCommerce Product Addons compatibility.
if ( class_exists( 'WC_Product_Addons' ) ) {
	require_once __DIR__ . '/woo-product-addons.php';
}

// Role Based Pricing for WooCommerce compatibility.
if ( class_exists( 'AF_C_S_P_Price' ) ) {
	require_once __DIR__ . '/role-based-pricing-for-wc.php';
}

// WPC Product Bundles for WooCommerce.
if ( is_plugin_active( 'woo-product-bundle/wpc-product-bundles.php' ) ) {
	require_once __DIR__ . '/wpc-product-bundles-for-wc.php';
}
