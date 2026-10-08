<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' );
?>

<div class="tf-mini-cart-wrap list-file-delete <?php echo WC()->cart->is_empty() ? 'wrap-empty_text' : ''; ?>">

<div class="tf-mini-cart-main">

<div class="tf-mini-cart-sroll">

<div class="tf-mini-cart-items <?php echo WC()->cart->is_empty() ? 'list-empty' : ''; ?>">


<?php if ( WC()->cart->is_empty() ) : ?>

<div class="box-text_empty type-shop_cart">

    <div class="shop-empty_top">

        <span class="icon">
            <i class="icon-shopping-cart-simple"></i>
        </span>

        <h3 class="text-emp fw-normal">
            <?php esc_html_e( 'Your cart is empty', 'woocommerce' ); ?>
        </h3>

        <p class="h6 text-main">
            Your cart is currently empty. Let us assist you in finding the right product
        </p>

    </div>


    <div class="shop-empty_bot">

        <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" 
           class="tf-btn animate-btn">
            Shopping
        </a>

        <a href="<?php echo esc_url(home_url('/')); ?>" 
           class="tf-btn style-line">
            Back to home
        </a>

    </div>

</div>


<?php else : ?>


<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :

$_product = apply_filters(
    'woocommerce_cart_item_product',
    $cart_item['data'],
    $cart_item,
    $cart_item_key
);

if ( ! $_product || ! $_product->exists() ) {
    continue;
}


$product_name = $_product->get_name();

$product_permalink = $_product->is_visible()
    ? $_product->get_permalink( $cart_item )
    : '';

?>


<div class="tf-mini-cart-item file-delete">


<div class="tf-mini-cart-image">

<?php echo $_product->get_image(
    'woocommerce_thumbnail',
    array(
        'class'=>'lazyload'
    )
); ?>

</div>


<div class="tf-mini-cart-info">


<div class="text-small text-main-2 sub">

<?php

$terms = get_the_terms(
    $_product->get_id(),
    'product_cat'
);

if($terms && !is_wp_error($terms)){
    echo esc_html($terms[0]->name);
}

?>

</div>



<h6 class="title">
<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				
<?php if($product_permalink): ?>

<a href="<?php echo esc_url($product_permalink); ?>"
class="link text-line-clamp-1">

<?php echo esc_html($product_name); ?>

</a>

<?php else: ?>

<?php echo esc_html($product_name); ?>

<?php endif; ?>


</h6>


<div class="d-flex justify-content-between align-items-center mt-2">

<div class="h6 fw-semibold mb-0">
<span class="price text-primary tf-mini-card-price">
Total : <?php echo WC()->cart->get_product_subtotal(
    $_product,
    $cart_item['quantity']
);?>
</span>
</div>

<div class="mini-cart-item-actions d-flex align-items-center gap-2">
    <div class="mini-cart-wishlist hover-tooltip tooltip-top">
        <a href="javascript:void(0);" 
           class="btn-move-to-wishlist" 
           data-id="<?php echo esc_attr( $_product->get_id() ); ?>" 
           data-product_id="<?php echo esc_attr( $_product->get_id() ); ?>" 
           data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" 
           data-remove_url="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" 
           aria-label="<?php esc_attr_e( 'Move to Wishlist', 'jewellery_wp' ); ?>">
            <i class="icon icon-heart"></i>
        </a>
        <span class="tooltip"><?php esc_html_e( 'Move to Wishlist', 'jewellery_wp' ); ?></span>
    </div>

    <?php
    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        'woocommerce_cart_item_remove_link',
        sprintf(
            '<a role="button" href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">&times;</a>',
            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
            /* translators: %s is the product name */
            esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
            esc_attr( $product_id ),
            esc_attr( $cart_item_key ),
            esc_attr( $_product->get_sku() ),
            /* translators: %s is the product name */
            esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) )
        ),
        $cart_item_key
    );
    ?>
</div>

</div>



</div>


</div>


</div>


<?php endforeach; ?>


<?php endif; ?>


</div>

</div>

</div>


<?php if ( ! WC()->cart->is_empty() ) : ?>


<div class="tf-mini-cart-bottom box-empty_clear">


<div class="tf-mini-cart-threshold">


<div class="text">

<h6 class="subtotal heading_cmn_white">

Subtotal 
(<span class="prd-count">

<?php echo WC()->cart->get_cart_contents_count(); ?>

</span> item)

</h6>


<h4 class="text-primary total-price tf-totals-total-value">

<?php echo WC()->cart->get_cart_subtotal(); ?>

</h4>


</div>







</div>



<div class="tf-mini-cart-bottom-wrap">


<div class="tf-mini-cart-view-checkout">


<a href="<?php echo esc_url(wc_get_cart_url()); ?>"
class="tf-btn btn-white animate-btn animate-dark line">

View cart

</a>



<a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
class="tf-btn animate-btn d-inline-flex bg-dark-2 w-100 justify-content-center">

<span>
Check out
</span>

</a>


</div>



<div class="free-shipping">

<i class="icon icon-truck"></i>

Free shipping on all orders over ₹1500

</div>



</div>


</div>


<?php endif; ?>


</div>


<?php do_action( 'woocommerce_after_mini_cart' ); ?>