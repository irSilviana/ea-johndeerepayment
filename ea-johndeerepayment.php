<?php

/**
 * Plugin Name: John Deere Financial Multi-Use Line
 * Plugin URI: https://edamame.agency/
 * Description: Adds a new payment option on the checkout page for John Deere Financial Multi-Use Line.
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
add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');
function enqueue_plugin_styles()
{
  wp_enqueue_style('my-plugin-styles', plugin_dir_url(__FILE__) . 'styles.css');
}

// Add the payment gateway
add_action('plugins_loaded', 'init_john_deere_payment_gateway', 0);
function init_john_deere_payment_gateway()
{
  if (!class_exists('WC_Payment_Gateway')) return;

  /**
   * Gateway class
   */
  include plugin_dir_path(__FILE__) . 'WC_John_Deere_Payment_Gateway.php';

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
