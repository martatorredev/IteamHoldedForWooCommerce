<?php
/**
 * Plugin name: Holded Invoices for WooCommerce
 * Description: Añade una sección de facturas en la página de "Mi cuenta" en WooCommerce.
 * Version: 1.0
 * Author: Marta Torre
 * Author URI: https://martatorre.dev/
 */

 // Asegurar que el plugin se carga dentro de WordPress.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Agregar pestaña de "Facturas" en la página de Mi Cuenta
function agregar_pestaña_facturas($items) {
    $items['facturas'] = __('Facturas', 'woocommerce-holded');
    return $items;
}
add_filter('woocommerce_account_menu_items', 'agregar_pestaña_facturas');

// Añadir el contenido de la pestaña "Facturas"
function mostrar_pestaña_facturas() {
    echo '<h3>' . __('Tus Facturas', 'woocommerce-holded') . '</h3>';
    
    // Aquí es donde vamos a mostrar las facturas desde Holded
    echo '<p>Aquí aparecerán las facturas de tus pedidos.</p>';
}
add_action('woocommerce_account_facturas_endpoint', 'mostrar_pestaña_facturas');

// Crear el endpoint para la pestaña de facturas
function agregar_endpoint_facturas() {
    add_rewrite_endpoint('facturas', EP_ROOT | EP_PAGES);
}
add_action('init', 'agregar_endpoint_facturas');

// Asegurarse de que los enlaces del menú se generen correctamente.
function plugin_activar_rewrite() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'plugin_activar_rewrite');