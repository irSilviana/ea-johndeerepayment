<?php

/**
 * Plugin Name: John Deere Financial Multi-Use Line
 * Plugin URI: https://edamame.agency/
 * Description: Adds a new payment option on the checkout page for John Deere Financial Multi-Use Line.
 * Version: 1.0.0
 * Author: Irfani Silviana
 * Author URI: https://irfanisilviana.com/
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

add_action('plugins_loaded', 'init_john_deere_payment_gateway', 0);

function init_john_deere_payment_gateway()
{
  if (!class_exists('WC_Payment_Gateway')) return;

  /**
   * Gateway class
   */
  class WC_John_Deere_Payment_Gateway extends WC_Payment_Gateway
  {

    /**
     * Gateway instructions that will be added to the thank you page and emails.
     *
     * @var string
     */
    public $instructions;

    /**
     * Constructor for the gateway.
     */
    public function __construct()
    { // Setup general properties.
      $this->setup_properties();

      // Load the settings.
      $this->init_form_fields();
      $this->init_settings();

      // Define user set variables.
      $this->title          = $this->get_option('title');
      $this->description    = $this->get_option('description');
      $this->instructions   = $this->get_option('instructions');

      // Actions.
      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
      add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));

      // Customer Emails.
      add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
    }


    /**
     * Setup general properties for the gateway.
     */
    protected function setup_properties()
    {
      $this->id                 = 'john_deere';
      $this->icon               = apply_filters('woocommerce_john_deere_icon', plugins_url('/assets/images/john-deere-logo.png', __FILE__));
      // $this->method_title       = 'John Deere Payment Option';
      $this->method_title       = _x('John Deere Payment', 'Check payment method', 'woocommerce');
      $this->method_description = 'Allows payments with John Deere Financial Multi-Use Line';
      $this->has_fields         = true;
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
      $this->form_fields = array(
        'enabled' => array(
          'title'       => 'Enable/Disable',
          'label'       => 'Enable John Deere Payment',
          'type'        => 'checkbox',
          'default'     => 'no'
        ),
        'title' => array(
          'title'       => 'Title',
          'type'        => 'safe_text',
          'description' => 'This controls the title which the user sees during checkout.',
          'default'     => 'John Deere Payment', 'Check payment method',
          'desc_tip'    => true,
        ),
        'description' => array(
          'title'       => 'Description',
          'type'        => 'textarea',
          'description' => 'This controls the description which the user sees during checkout.',
          'default'     => 'Pay with John Deere Financial Multi-Use Line',
          'desc_tip'    => true,
        ),

        'instructions' => array(
          'title'       => 'Instructions',
          'type'        => 'textarea',
          'description' => 'Instructions that will be added to the thank you page and order emails.',
          'default'     => 'Your order will be paid with John Deere Financial Multi-Use Line',
          'desc_tip'    => true,
        ),
      );
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id Order ID.
     * @return array
     */
    public function process_payment($order_id)
    {
      $order = wc_get_order($order_id);

      $order->update_status('processing', 'Payment via John Deere Financial Multi-Use Line');


      $order->payment_complete();


      // Remove cart.
      WC()->cart->empty_cart();

      // Return thankyou redirect.
      return array(
        'result'   => 'success',
        'redirect' => $this->get_return_url($order),
      );
    }


    /**
     * Output for the order received page.
     */
    public function thankyou_page()
    {
      if ($this->instructions) {
        echo wp_kses_post(wpautop(wptexturize($this->instructions)));
      }
    }
  }

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
