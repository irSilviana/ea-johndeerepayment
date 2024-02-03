<?php

/**
 * Plugin Name: John Deere Financial Multi-Use Line
 * Plugin URI: https://edamame.agency/
 * Description: Adds a new payment option on the checkout page for John Deere Financial Multi-Use Line.
 * Version: 1.0.0
 * Author: Irfani Silviana
 * Author URI: https://irfanisilviana.com/
 */


// If this file is called directly, abort.
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

// Include the main plugin file
include plugin_dir_path(__FILE__) . 'functions.php';

// Add the payment gateway
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
      $this->enabled        = $this->get_option('enabled');
      $this->description    = $this->get_option('description');
      $this->instructions   = $this->get_option('instructions');

      // Actions.
      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
      add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
      add_filter('woocommerce_payment_complete_order_status', array($this, 'change_payment_complete_order_status'), 10, 3);

      // Customer Emails.
      add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
    }


    /**
     * Setup general properties for the gateway.
     */
    protected function setup_properties()
    {
      $this->id                 = 'john_deere_payment';
      $this->icon               = apply_filters('woocommerce_john_deere_icon', plugins_url('/assets/images/john-deere-logo.png', __FILE__));
      $this->method_title       = 'John Deere Financial Multi-Use Line';
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
        'order_status' => array(
          'title'       => __('Order Status', 'woocommerce'),
          'type'        => 'select',
          'description' => __('Choose the order status for the John Deere payment method.', 'woocommerce'),
          'default'     => 'pending',
          'options'     => array(
            'pending'    => __('Pending payment', 'woocommerce'),
            'processing' => __('Processing', 'woocommerce'),
            'on-hold'    => __('On hold', 'woocommerce'),
            'completed'  => __('Completed', 'woocommerce'),
          ),
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
     * Validate input fields.
     */
    public function validate_fields()
    {
      if (empty($_POST['jd_payment_option'])) {
        wc_add_notice(__('Please select a payment option.', 'woocommerce'), 'error');
        return false;
      }

      if (empty($_POST['jd_account_number'])) {
        wc_add_notice(__('Please enter your John Deere Multi-Use Account Number.', 'woocommerce'), 'error');
        return false;
      }

      if (empty($_POST['jd_account_name'])) {
        wc_add_notice(__('Please enter the name on the account.', 'woocommerce'), 'error');
        return false;
      }

      return true;
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


      $order_status = $this->get_option('order_status', 'processing');
      $order->update_status($order_status, 'Payment via John Deere Financial Multi-Use Line');

      // Reduce stock levels
      wc_reduce_stock_levels($order_id);

      $order->save();


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


    /**
     * Display input fields in the checkout
     */
    public function payment_fields()
    {
      $user_id = get_current_user_id(); // Get the ID of the current user
      $jd_account_enabled = get_user_meta($user_id, 'jd_account_enabled', true); // Get the enabled status of the John Deere account
      $jd_account_number = get_user_meta($user_id, 'jd_account_number', true); // Get the account number
      $jd_account_name = get_user_meta($user_id, 'jd_account_name', true); // Get the account name
      $jd_payment_option = get_user_meta($user_id, 'jd_payment_option', true); // Get the payment option

?>
      <div id="john_deere_payment_fields">
        <h3>John Deere Payment Details</h3>

        <?php
        // Add the input field for the account name
        woocommerce_form_field('jd_account_name', array(
          'type'          => 'text',
          'class'         => array('jd-account-name form-row-wide'),
          'label'         => __('Name on the account'),
          'required'      => true,
          'default'       => $jd_account_name ? $jd_account_name : ''
        ));

        // Add the input field for the account number
        woocommerce_form_field('jd_account_number', array(
          'type'          => 'number',
          'class'         => array('jd-account-number form-row-wide'),
          'label'         => __('John Deere Multi-Use Account Number'),
          'required'      => true,
          'default'       => $jd_account_number ? $jd_account_number : ''
        ));

        // Add the radio buttons for payment options
        woocommerce_form_field('jd_payment_option', array(
          'type'          => 'radio',
          'class'         => array('jd-payment-option form-row-wide'),
          'label'         => __('Choose your payment option'),
          'options'       => array(
            'Regular Limit Line' => 'Regular Limit Line. I agree that this transaction will be billed to my John Deere Financial Multi-Use Line',
            'Special Term Limit Line' => 'Special Term Limit Line. I agree this transaction will be applied to my John Deere Financial Multi-Use Line.',
          ),
          'required'      => true,
          'default'       => $jd_payment_option ? $jd_payment_option : ''
        ));
        ?>
      </div>
<?php
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
