<?php

/**
 * Plugin Name: John Deere Financial Multi-Use Line
 * Plugin URI: https://edamame.agency/
 * Description: Adds a John Deere payment method to WooCommerce. Allows customers to enter and validates their John Deere account details at registration, checkout and account section. Displays John Deere payment method in the order details and emails. Provides administrators to manage user's John Deere account details from the admin area.
 * Version: 1.0.0
 * Author: Irfani Silviana
 * Author URI: https://irfanisilviana.com/
 * Text Domain: john-deere-payment
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


// If this file is called directly, abort.
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

// Include the main plugin file
include plugin_dir_path(__FILE__) . 'functions.php';

// Load the plugin text domain
add_action('plugins_loaded', 'john_deere_payment_load_textdomain');
function john_deere_payment_load_textdomain()
{
  load_plugin_textdomain('john-deere-payment', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Enqueue the plugin styles
add_action('wp_enqueue_scripts', 'enqueue_ea_john_deere_plugin_styles');
function enqueue_ea_john_deere_plugin_styles()
{
  // Register the style with WordPress
  wp_register_style('ea-john-deere-styles', plugin_dir_url(__FILE__) . '/assets/css/styles.css', array(), '1.0.0', 'all');

  // Enqueue the style
  wp_enqueue_style('ea-john-deere-styles');
}

add_action('admin_enqueue_scripts', 'ea_john_deere_admin_enqueue_styles');
function ea_john_deere_admin_enqueue_styles()
{
  // Register the style with WordPress
  wp_register_style('ea-john-deere-admin-styles', plugin_dir_url(__FILE__) . '/assets/css/admin-styles.css', array(), '1.0.0', 'all');

  // Enqueue the style
  wp_enqueue_style('ea-john-deere-admin-styles');
}

// Add the payment gateway
add_action('plugins_loaded', 'init_john_deere_payment_gateway', 0);
function init_john_deere_payment_gateway()
{
  if (!class_exists('WC_Payment_Gateway')) return;

  /**
   * Gateway class
   */
  include plugin_dir_path(__FILE__) . '/includes/class-wc-john-deere-payment-gateway.php';

  /**
   * Add the Gateway to WooCommerce
   **/
  function add_john_deere_payment_gateway($methods)
  {
    $methods[] = 'WC_John_Deere_Payment_Gateway';
    return $methods;
  }
  add_filter('woocommerce_payment_gateways', 'add_john_deere_payment_gateway');
}
