<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

add_action('woocommerce_order_status_completed', 'generar_cupon_primera_compra', 10, 1);
function generar_cupon_primera_compra($order_id) {
	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order($order_id);
	if ( ! $order ) {
		return;
	}
 
	$customer_id = $order->get_customer_id();
	if ( ! $customer_id ) {
		return;
	}

	$args = array(
		'customer_id' => $customer_id,
		'status'      => array('completed'),
		'return'      => 'ids'
	);
	$orders = wc_get_orders($args);
	 
	if ( count( $orders ) === 1 ) {
		$customer_name = $order->get_billing_first_name();
		$customer_name = sanitize_title( $customer_name );
		
		// Generar un cupón único con el nombre del cliente
		$coupon_code   = sanitize_title( 'bienvenido' . $customer_name . '20');
	
		if ( post_exists( $coupon_code ) ) {
			return;
		}
		
		$coupon_data = array(
			'post_title'   => $coupon_code,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'shop_coupon'
		);
		
		$new_coupon_id = wp_insert_post( $coupon_data, true );
		if ( is_wp_error( $new_coupon_id ) ) {
			error_log( 'Error al crear cupón: ' . $new_coupon_id->get_error_message() );
			return;
		}
		
		// Configurar los datos del cupón
		update_post_meta( $new_coupon_id, 'discount_type', 'fixed_cart' );
		update_post_meta( $new_coupon_id, 'coupon_amount', '20000' );
		update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
		update_post_meta( $new_coupon_id, 'usage_limit', '1' );
		update_post_meta( $new_coupon_id, 'expiry_date', date('Y-m-d', strtotime('+30 days')) );
		
		// Enviar el cupón por email al cliente
		$to      = sanitize_email( $order->get_billing_email() );
		$subject = 'Tu Cupón de Descuento';
		$message = sprintf( 'Gracias por tu primera compra, %s. Aquí tienes tu cupón: %s', esc_html( $order->get_billing_first_name() ), esc_html( $coupon_code ) );
		
		wp_mail( $to, $subject, $message );
	}
}
