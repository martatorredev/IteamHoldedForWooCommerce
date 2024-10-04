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

// Función para obtener facturas desde Holded API
function obtener_facturas_desde_holded($customer_email) {
    $api_key = 'a18675fc28833186dcebddbb9393ebac'; // Sustituye con tu clave real de Holded
    $endpoint = 'https://api.holded.com/api/invoicing/v1/invoices?email=' . urlencode($customer_email);

    $response = wp_remote_get($endpoint, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
        )
    ));

    if (is_wp_error($response)) {
        return []; // Retornar un array vacío en caso de error
    }

    $body = wp_remote_retrieve_body($response);
    $facturas = json_decode($body, true);

    return $facturas;
}

// Modificar la función para mostrar las facturas en la pestaña
function mostrar_pestaña_facturas() {
    $current_user = wp_get_current_user();
    $facturas = obtener_facturas_desde_holded($current_user->user_email);

    echo '<h3>' . __('Tus Facturas', 'woocommerce-holded') . '</h3>';

    if (!empty($facturas)) {
        echo '<ul>';
        foreach ($facturas as $factura) {
            $factura_pdf = $factura['pdf']; // Asumiendo que el endpoint de Holded te da la URL del PDF
            echo '<li><a href="' . esc_url($factura_pdf) . '" target="_blank">Descargar Factura #' . esc_html($factura['id']) . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No tienes facturas disponibles.</p>';
    }
}
