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


/**
 * Add custom fields to the order and user meta
 */
add_action('woocommerce_checkout_update_order_meta', 'save_john_deere_payment_fields');
function save_john_deere_payment_fields($order_id)
{
  if (!empty($_POST['jd_payment_option'])) {
    update_post_meta($order_id, 'jd_payment_option', sanitize_text_field($_POST['jd_payment_option']));
    update_user_meta(get_current_user_id(), 'jd_payment_option', sanitize_text_field($_POST['jd_payment_option']));
  }

  if (!empty($_POST['jd_account_number'])) {
    update_post_meta($order_id, 'jd_account_number', sanitize_text_field($_POST['jd_account_number']));
    update_user_meta(get_current_user_id(), 'jd_account_number', sanitize_text_field($_POST['jd_account_number']));
  }

  if (!empty($_POST['jd_account_name'])) {
    update_post_meta($order_id, 'jd_account_name', sanitize_text_field($_POST['jd_account_name']));
    update_user_meta(get_current_user_id(), 'jd_account_name', sanitize_text_field($_POST['jd_account_name']));
  }
}

/**
 * Display custom fields in the order details page
 */
add_action('woocommerce_admin_order_data_after_order_details', 'display_john_deere_payment_fields_admin');
function display_john_deere_payment_fields_admin($order)
{
  $jd_payment_option = get_post_meta($order->get_id(), 'jd_payment_option', true);
  $jd_account_number = get_post_meta($order->get_id(), 'jd_account_number', true);
  $jd_account_name = get_post_meta($order->get_id(), 'jd_account_name', true);

  echo '<div class="jd-financial-multi-use-line">';
  echo '<h3>' . __('John Deere Financial Multi-Use Line', 'woocommerce') . '</h3>';
  echo '<p><strong>' . __('Payment Option', 'woocommerce') . ':</strong> ' . $jd_payment_option . '<br/>';
  echo '<strong>' . __('Account Number', 'woocommerce') . ':</strong> ' . $jd_account_number . '<br/>';
  echo '<strong>' . __('Account Name', 'woocommerce') . ':</strong> ' . $jd_account_name . '</p>';
  echo '</div>';
}

/**
 * Add custom fields to the registration form
 */
add_action('woocommerce_register_form', 'add_custom_field_to_registration_form');
function add_custom_field_to_registration_form()
{
  ?>
  <p class="form-row form-row-wide">
  <h3>John Deere Financial Multi-Use Line</h3>
  <label for="reg_jd_account_number"><?php _e('John Deere Account Number', 'woocommerce'); ?> </label>
  <input type="number" class="input-text" name="jd_account_number" id="reg_jd_account_number" value="<?php if (!empty($_POST['jd_account_number'])) echo esc_attr($_POST['jd_account_number']); ?>" />
  </p>
  <p class="form-row form-row-wide">
    <label for="reg_jd_account_name"><?php _e('John Deere Account Name', 'woocommerce'); ?> </label>
    <input type="text" class="input-text" name="jd_account_name" id="reg_jd_account_name" value="<?php if (!empty($_POST['jd_account_name'])) echo esc_attr($_POST['jd_account_name']); ?>" />
  </p>
  <p class="form-row form-row-wide">
    <label><?php _e('John Deere Payment Option', 'woocommerce'); ?> </label>
    <input type="radio" name="jd_payment_option" value="Regular Limit Line" checked> Regular Limit Line. <br>
    <span style="font-size: 14px;">I agree that this transaction will be billed to my John Deere Financial Multi-Use Line
    </span>
    <br><br>
    <input type="radio" name="jd_payment_option" value="Special Term Limit Line"> Special Term Limit Line. <br>
    <span style="font-size: 14px;">I agree this transaction will be applied to my John Deere Financial Multi-Use Line.</span>
  </p>
<?php
}

/**
 * Validate custom fields in the registration form
 */
add_action('woocommerce_register_post', 'validate_custom_field_in_registration_form', 10, 3);
function validate_custom_field_in_registration_form($username, $email, $validation_errors)
{
  if (!is_numeric($_POST['jd_account_number'])) {
    $validation_errors->add('jd_account_number_error', __('John Deere Account Number is  should be a number!', 'woocommerce'));
  }

  return $validation_errors;
}

/**
 * Save custom fields from the registration form
 */
add_action('woocommerce_created_customer', 'save_custom_field_from_registration_form');
function save_custom_field_from_registration_form($customer_id)
{
  if (isset($_POST['jd_account_number']) && is_numeric($_POST['jd_account_number'])) {
    update_user_meta($customer_id, 'jd_account_number', sanitize_text_field($_POST['jd_account_number']));
  }
  if (isset($_POST['jd_account_name'])) {
    update_user_meta($customer_id, 'jd_account_name', sanitize_text_field($_POST['jd_account_name']));
  }
  if (isset($_POST['jd_payment_option'])) {
    update_user_meta($customer_id, 'jd_payment_option', sanitize_text_field($_POST['jd_payment_option']));
  }
}


/**
 * Add custom fields to the User edit account form
 */
add_action('woocommerce_edit_account_form', 'add_custom_fields_to_edit_account_form');
function add_custom_fields_to_edit_account_form()
{
  $user_id = get_current_user_id();
  $jd_account_number = get_user_meta($user_id, 'jd_account_number', true);
  $jd_account_name = get_user_meta($user_id, 'jd_account_name', true);
  $jd_payment_option = get_user_meta($user_id, 'jd_payment_option', true);
  $jd_account_enabled = get_user_meta($user_id, 'jd_account_enabled', true);

  echo '<h3>' . __('John Deere Financial Multi-Use Line', 'woocommerce') . '</h3>';
?>
  <fieldset>
    <legend><?php _e('John Deere Account Details', 'woocommerce'); ?></legend>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">

      <input type="checkbox" class="woocommerce-Input woocommerce-Input--checkbox input-checkbox" name="jd_account_enabled" id="jd_account_enabled" value="1" <?php checked($jd_account_enabled, 1); ?> /> <?php _e('Enable / Disable John Deere Account ', 'woocommerce') ?>
    </p>
    <div id="jd_account_details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="jd_account_number"><?php _e('Account Number', 'woocommerce'); ?></label>
        <input type="number" class="woocommerce-Input woocommerce-Input--text input-text" name="jd_account_number" id="jd_account_number" value="<?php echo esc_attr($jd_account_number); ?>" />
      </p>
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="jd_account_name"><?php _e('Account Name', 'woocommerce'); ?></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="jd_account_name" id="jd_account_name" value="<?php echo esc_attr($jd_account_name); ?>" />
      </p>
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label><?php _e('Payment Option', 'woocommerce'); ?></label>
        <input type="radio" name="jd_payment_option" value="Regular Limit Line" <?php checked($jd_payment_option, 'Regular Limit Line'); ?>> Regular Limit Line<br>
        <input type="radio" name="jd_payment_option" value="Special Term Limit Line" <?php checked($jd_payment_option, 'Special Term Limit Line'); ?>> Special Term Limit Line
      </p>
    </div>
  </fieldset>
  <script type="text/javascript">
    document.getElementById('jd_account_enabled').addEventListener('change', function() {
      document.getElementById('jd_account_details').style.display = this.checked ? '' : 'none';
    });
  </script>
<?php
}

/**
 * Save custom fields from the User edit account form
 */
add_action('woocommerce_save_account_details', 'save_jd_account_enabled');
function save_jd_account_enabled($user_id)
{
  if (isset($_POST['jd_account_enabled'])) {
    update_user_meta($user_id, 'jd_account_enabled', 1);
  } else {
    delete_user_meta($user_id, 'jd_account_enabled');
  }
  if (isset($_POST['jd_account_number']) && (is_numeric($_POST['jd_account_number']) || $_POST['jd_account_number'] === '')) {
    update_user_meta($user_id, 'jd_account_number', sanitize_text_field($_POST['jd_account_number']));
  }
  if (isset($_POST['jd_account_name'])) {
    update_user_meta($user_id, 'jd_account_name', sanitize_text_field($_POST['jd_account_name']));
  }
  if (isset($_POST['jd_payment_option'])) {
    update_user_meta($user_id, 'jd_payment_option', sanitize_text_field($_POST['jd_payment_option']));
  }
}



/**
 * Add custom fields to the user admin page
 */
add_action('show_user_profile', 'add_custom_fields_to_user_admin_page');
add_action('edit_user_profile', 'add_custom_fields_to_user_admin_page');
function add_custom_fields_to_user_admin_page($user)
{
  $jd_account_number = get_the_author_meta('jd_account_number', $user->ID);
  $jd_account_name = get_the_author_meta('jd_account_name', $user->ID);
  $jd_payment_option = get_the_author_meta('jd_payment_option', $user->ID);
  $jd_account_enabled = get_the_author_meta('jd_account_enabled', $user->ID);
?>
  <h3><?php _e('John Deere Account Details', 'woocommerce'); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="jd_account_enabled"><?php _e('Enable John Deere Account', 'woocommerce'); ?></label></th>
      <td><input type="checkbox" name="jd_account_enabled" id="jd_account_enabled" value="1" <?php checked($jd_account_enabled, 1); ?> /></td>
    </tr>
    <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <th><label for="jd_account_number"><?php _e('Account Number', 'woocommerce'); ?></label></th>
      <td><input type="number" name="jd_account_number" id="jd_account_number" value="<?php echo esc_attr($jd_account_number); ?>" class="regular-text" /></td>
    </tr>
    <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <th><label for="jd_account_name"><?php _e('Account Name', 'woocommerce'); ?></label></th>
      <td><input type="text" name="jd_account_name" id="jd_account_name" value="<?php echo esc_attr($jd_account_name); ?>" class="regular-text" /></td>
    </tr>
    <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <th><label><?php _e('Payment Option', 'woocommerce'); ?></label></th>
      <td>
        <input type="radio" name="jd_payment_option" value="Regular Limit Line" <?php checked($jd_payment_option, 'Regular Limit Line'); ?>> Regular Limit Line<br>
        <input type="radio" name="jd_payment_option" value="Special Term Limit Line" <?php checked($jd_payment_option, 'Special Term Limit Line'); ?>> Special Term Limit Line
      </td>
    </tr>
  </table>
  <script type="text/javascript">
    document.getElementById('jd_account_enabled').addEventListener('change', function() {
      var display = this.checked ? '' : 'none';
      Array.from(document.querySelectorAll('.jd-account-details')).forEach(function(el) {
        el.style.display = display;
      });
    });
  </script>
<?php
}

/**
 * Save custom fields from the user admin page
 */
add_action('personal_options_update', 'save_custom_fields_from_user_admin_page');
add_action('edit_user_profile_update', 'save_custom_fields_from_user_admin_page');
function save_custom_fields_from_user_admin_page($user_id)
{
  if (current_user_can('edit_user', $user_id)) {
    update_user_meta($user_id, 'jd_account_enabled', isset($_POST['jd_account_enabled']) ? 1 : 0);
    if (isset($_POST['jd_account_number']) && (is_numeric($_POST['jd_account_number']) || $_POST['jd_account_number'] === '')) {
      update_user_meta($user_id, 'jd_account_number', sanitize_text_field($_POST['jd_account_number']));
    }
    if (isset($_POST['jd_account_name'])) {
      update_user_meta($user_id, 'jd_account_name', sanitize_text_field($_POST['jd_account_name']));
    }
    if (isset($_POST['jd_payment_option'])) {
      update_user_meta($user_id, 'jd_payment_option', sanitize_text_field($_POST['jd_payment_option']));
    }
  }
}
