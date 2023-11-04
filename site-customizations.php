<?php
/**
 * Plugin Name: Site customizations
 * Description: Custom changes for the website.
 * Author: A Ali
 * Version: 1.0.0
 */


/**
 * Enqueuing scripts and styles for the front-end of the websites.
 */
add_action( 'wp_enqueue_scripts', 'site_customizations_scripts_and_styles' );

function site_customizations_scripts_and_styles() {
	wp_register_style( 'site-customizations-css', plugins_url( '/style.css' , __FILE__ ) );
	wp_enqueue_style( 'site-customizations-css' );

	wp_register_script( 'site-customizations-js', plugins_url( '/cs-script.js' , __FILE__ ) );
	wp_enqueue_script( 'site-customizations-js' );

	wp_localize_script( 'site-customizations-js', 'ilaa_ajax_object',
			array( 
				'ajaxurl' => admin_url('admin-ajax.php'),
			)
		);
}

/**
 * Changing required fields on checkout page to optional.
 */
add_filter( 'woocommerce_checkout_fields' , 'site_customizations_checkout_change_required_fields_to_optional' );
function site_customizations_checkout_change_required_fields_to_optional( $fields ) {
	
	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
	$chosen_shipping = $chosen_methods[0];
	//echo 'chosen_shipping: ';
	//var_dump($fields);
	if ( 0 === strpos( $chosen_shipping, 'local_pickup' ) ) {

		unset($fields['billing']['billing_address_1']); // Value of field name to be hidden
		unset($fields['billing']['billing_address_2']);
		unset($fields['billing']['billing_city']);
		unset($fields['billing']['billing_postcode']);
		unset($fields['billing']['billing_country']);
		unset($fields['billing']['billing_state']);
		
		unset($fields['shipping']['shipping_address_1']); // Value of field name to be hidden
		unset($fields['shipping']['shipping_address_2']);
		unset($fields['shipping']['shipping_city']);
		unset($fields['shipping']['shipping_postcode']);
		unset($fields['shipping']['shipping_country']);
		unset($fields['shipping']['shipping_state']);
		
	}
	
	wc_enqueue_js( "
		let isRefreshPage = false;
		jQuery('form.checkout').on('change','input[name^=\"shipping_method\"]',function() {
			var val = jQuery( this ).val();
			if (val) {
				isRefreshPage = true;
				jQuery('#place_order').hide();
			}
		});
		 
		jQuery('body').on('updated_checkout', function(){
			if (isRefreshPage) {
				location.reload();
			}
		});
   " );

     return $fields;
}

/**
 * Customise the Add to cart button on shop page.
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'sc_filter_shop_page_add_to_cart_button', 10, 3 );
function sc_filter_shop_page_add_to_cart_button( $html, $product, $args ) {
	if ( ! is_shop() ) {
		return $html;
	}

	//var_dump($product->get_type());
	global $wpdb;
	$pid = $product->get_id();
	$options_count = $wpdb->get_var( "SELECT COUNT(*) FROM dl_pofw_product_option WHERE product_id={$pid}" );

	if ($options_count == 0) {

		return sprintf(
			'<a href="%s" class="%s" %s >%s</a>',
			esc_url( $product->get_permalink() ),
			'button product_type_simple add_to_cart_button ajax_add_to_cart',
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			_( 'Add to cart' )
		);
	}

	return sprintf(
		'<a href="" class="%s" id="%s" %s >%s</a>',
		'button product_type_simple sc_ajax_add_to_cart',
		'sc_ajax_add_to_cart-' . $pid,
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		_( 'Add to cart' )
	);

}

/**
 * Ajax callbacks
 */
add_action( 'wp_ajax_cs_add_to_cart', 'sc_add_to_cart_callback' );
add_action( 'wp_ajax_nopriv_cs_add_to_cart', 'sc_add_to_cart_callback' );

function sc_add_to_cart_callback(){
	global $woocommerce;
	$productid = $_POST["productid"];

	$res = $woocommerce->cart->add_to_cart( $productid );

	if(!$res){
		echo "ERROR";die;
	}

	echo "ADDED";die;
}