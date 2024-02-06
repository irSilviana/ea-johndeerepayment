<?php

class WC_John_Deere_Payment_Gateway extends WC_Payment_Gateway
{
  /**
   * Gateway instructions that will be added to the thank you page and emails.
   *
   * @var string
   */
  public $instructions;

  /**
   * Unique identifier for the payment gateway
   * @var string
   */
  public $id = 'john_deere';

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
    $this->icon               = apply_filters('woocommerce_john_deere_icon', plugin_dir_url(dirname(__FILE__)) . '/assets/images/john-deere-logo.png');
    $this->method_title       = 'John Deere Financial Multi-Use Line';
    $this->method_description = 'Allows payments with John Deere Financial Multi-Use Line';
    $this->has_fields         = true;
  }

  /**
   * Initialise Gateway Settings Form Fields.
   */
  public function init_form_fields()
  {
    // Get all users with the role 'administrator'
    $admins = get_users(array('role' => 'administrator'));

    // Prepare an array of admin email addresses
    $admin_emails = array();
    foreach ($admins as $admin) {
      $admin_emails[$admin->user_email] = $admin->user_email;
    }


    $this->form_fields = array(
      'enabled' => array(
        'title'       => __('Enable/Disable', 'john-deere-payment'),
        'label'       => __('Enable John Deere Payment', 'john-deere-payment'),
        'type'        => 'checkbox',
        'default'     => 'no'
      ),
      'jd_account_mode' => array(
        'title'       => __('John Deere Account Mode', 'john-deere-payment'),
        'type'        => 'select',
        'description' => __('Choose the general mode of the John Deere payment method. 
        <p>
       <strong>Preselected Users</strong>: Only users who are preselected will be able to use the John Deere payment method. <br/>
        <strong>Enabled Users</strong>: All users who have the John Deere account enabled will be able to use the John Deere payment method. <br/>
       <strong> All Users</strong>: All users will be able to use the John Deere payment method.
        </p>
        ', 'john-deere-payment'),
        'default'     => 'preselected_users',
        'options'     => array(
          'preselected_users' => __('Preselected Users (Recommended)', 'john-deere-payment'),
          'enabled_users' => __('All Users with JD Account Enabled', 'john-deere-payment'),
          'all_users' => __('All Users', 'john-deere-payment'),
        ),
      ),
      'admin_email' => array(
        'title'       => __('Admin Email', 'domain'), // Replace 'domain' with your text domain
        'type'        => 'select',
        'description' => __('Select the email address of the admin.', 'domain'), // Replace 'domain' with your text domain
        'default'     => get_option('admin_email'), // Default value is the current admin email
        'options'     => $admin_emails, // Options for the dropdown
      ),
      'title' => array(
        'title'       => __('Title', 'john-deere-payment'),
        'type'        => 'safe_text',
        'description' => __('This controls the title which the user sees during checkout.', 'john-deere-payment'),
        'default'     => 'John Deere Payment', 'Check payment method',
        'desc_tip'    => true,
      ),
      'order_status' => array(
        'title'       => __('Order Status',  'john-deere-payment'),
        'type'        => 'select',
        'description' => __('Choose the order status for the John Deere payment method. The default value is "Processing". Remember! Except "Completed", you are required to update the order status manually.',  'john-deere-payment'),
        'desc_tip'    => true,
        'default'     => 'processing',
        'options'     => array(
          'pending'    => __('Pending payment',  'john-deere-payment'),
          'processing' => __('Processing',  'john-deere-payment'),
          'on-hold'    => __('On hold',  'john-deere-payment'),
          'completed'  => __('Completed',  'john-deere-payment'),
        ),
      ),
      'description' => array(
        'title'       => __('Description', 'john-deere-payment'),
        'type'        => 'textarea',
        'description' => __('This controls the description which the user sees during checkout.', 'john-deere-payment'),
        'default'     => 'Pay with John Deere Financial Multi-Use Line',
        'desc_tip'    => true,
      ),

      'instructions' => array(
        'title'       => __('Instructions', 'john-deere-payment'),
        'type'        => 'textarea',
        'description' => __('Instructions that will be added to the thank you page and order emails.', 'john-deere-payment'),
        'default'     => 'Your order will be paid with John Deere Financial Multi-Use Line',
        'desc_tip'    => true,
      ),
      // Front end fields
      'account_name' => array(
        'title'       => __('Account Name', 'john-deere-payment'),
        'type'        => 'text',
        'description' => __('This controls the account name field which the user sees during checkout.', 'john-deere-payment'),
        'default'     => __('Name on the account', 'john-deere-payment'),
        'desc_tip'    => true,
      ),
      'account_number' => array(
        'title'       => __('Account Number', 'john-deere-payment'),
        'type'        => 'text',
        'description' => __('This controls the account number field which the user sees during checkout.', 'john-deere-payment'),
        'default'     => __('John Deere Multi-Use Account Number', 'john-deere-payment'),
        'desc_tip'    => true,
      ),
      'regular_limit_line' => array(
        'title'       => __('Regular Limit Line', 'john-deere-payment'),
        'type'        => 'text',
        'description' => __('This controls the title for Regular Limit Line field.', 'john-deere-payment'),
        'default'     => __('Regular Limit Line', 'john-deere-payment'),
        'desc_tip'    => true,
      ),
      'regular_limit_line_desc' => array(
        'title'       => __('Regular Limit Line Description', 'john-deere-payment'),
        'type'        => 'textarea',
        'description' => __('This controls the description for Regular Limit Line field.', 'john-deere-payment'),
        'default'     => __('I agree that this transaction will be billed to my John Deere Financial Multi-Use Line', 'john-deere-payment'),
        'desc_tip'    => true,
      ),
      'special_term_limit_line' => array(
        'title'       => __('Special Term Limit Line', 'john-deere-payment'),
        'type'        => 'text',
        'description' => __('This controls the title for Special Term Limit Line field.', 'john-deere-payment'),
        'default'     => __('Special Term Limit Line', 'john-deere-payment'),
        'desc_tip'    => true,
      ),
      'special_term_limit_line_desc' => array(
        'title'       => __('Special Term Limit Line Description', 'john-deere-payment'),
        'type'        => 'textarea',
        'description' => __('This controls the description for Special Term Limit Line field.', 'john-deere-payment'),
        'default'     => __('I agree this transaction will be applied to my John Deere Financial Multi-Use Line', 'john-deere-payment'),
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
      wc_add_notice(__('Please select a payment option.',  'john-deere-payment'), 'error');
      return false;
    }

    if (empty($_POST['jd_account_number'])) {
      wc_add_notice(__('Please enter your John Deere Multi-Use Account Number.',  'john-deere-payment'), 'error');
      return false;
    }

    if (empty($_POST['jd_account_name'])) {
      wc_add_notice(__('Please enter the name on the account.',  'john-deere-payment'), 'error');
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
    $order->update_status($order_status, __('Payment via John Deere Financial Multi-Use Line', 'john-deere-payment'));

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
    $jd_account_number = get_user_meta($user_id, 'jd_account_number', true); // Get the account number
    $jd_account_name = get_user_meta($user_id, 'jd_account_name', true); // Get the account name
    $jd_payment_option = get_user_meta($user_id, 'jd_payment_option', true); // Get the payment option

    // Get the setting values
    $account_name_label = (!empty($this->get_option('account_name'))) ? $this->get_option('account_name') : 'Name on the account';
    $account_number_label = (!empty($this->get_option('account_number'))) ? $this->get_option('account_number') : 'John Deere Multi-Use Account Number';
    $regular_limit_line =   (!empty($this->get_option('regular_limit_line'))) ? $this->get_option('regular_limit_line') : 'Regular Limit Line';
    $special_term_limit_line = (!empty($this->get_option('special_term_limit_line'))) ? $this->get_option('special_term_limit_line') : 'Special Term Limit Line';
    $regular_limit_line_desc =  (!empty($this->get_option('regular_limit_line_desc'))) ? $this->get_option('regular_limit_line_desc') : 'I agree that this transaction will be billed to my John Deere Financial Multi-Use Line';
    $special_term_limit_line_desc =  (!empty($this->get_option('special_term_limit_line_desc'))) ? $this->get_option('special_term_limit_line_desc') : 'I agree this transaction will be applied to my John Deere Financial Multi-Use Line';

    $jd_account_mode = $this->get_option('jd_account_mode'); // Get the John Deere account mode
    $user_id = get_current_user_id(); // Get the ID of the current user
    $jd_account_enabled = get_user_meta($user_id, 'jd_account_enabled', true); // Get the enabled status of the John Deere account

    if ($jd_account_mode == 'preselected_users' && !$jd_account_enabled) {
      // Don't display the payment fields for users who are not preselected
      echo '<p>' . __('The John Deere Payment method is not enabled for your account. Please contact the Admin.', 'john-deere-payment') . '</p>';
      return;
    } elseif ($jd_account_mode == 'enabled_users' && !$jd_account_enabled) {
      // Don't display the payment fields for users who don't have the John Deere account enabled
      echo '<p>' . __('The John Deere Payment method should be enabled for your account. Please login and have it enabled.', 'john-deere-payment') . '</p>';
      return;
    }


?>
    <div id="john_deere_payment_fields">
      <h3><?php _e('John Deere Payment Details', 'john-deere-payment') ?></h3>
      <?php
      // Add the input field for the account name
      woocommerce_form_field('jd_account_name', array(
        'type'          => 'text',
        'class'         => array('jd-account-name form-row-wide'),
        'label'         => __($account_name_label, 'john-deere-payment'),
        'required'      => true,
        'default'       => $jd_account_name ? $jd_account_name : ''
      ));

      // Add the input field for the account number
      woocommerce_form_field('jd_account_number', array(
        'type'          => 'text',
        'class'         => array('jd-account-number form-row-wide'),
        'label'         => __($account_number_label, 'john-deere-payment'),
        'required'      => true,
        'default'       => $jd_account_number ? $jd_account_number : ''
      ));

      // Add the radio buttons for payment options
      echo jd_woocommerce_form_field('jd_payment_option', array(
        'type'          => 'radio',
        'class'         => array('jd-payment-option form-row-wide', 'jd-custom-grid'),
        'label'         => __('Choose your payment option', 'john-deere-payment'),
        'options'       => array(
          'Regular Limit Line' => __($regular_limit_line . '<br><span style="font-size:0.9rem">' . $regular_limit_line_desc . '</span>', 'john-deere-payment'),
          'Special Term Limit Line' => __($special_term_limit_line . '<br><span style="font-size:0.9rem">' . $special_term_limit_line_desc . '</span>', 'john-deere-payment'),
        ),
        'required'      => true,
        'default'       => $jd_payment_option
      ));



      ?>
    </div>
<?php
  }

  /**
   * Outputs instructions for the payment method in the order email.
   *
   * @param WC_Order $order Order instance.
   * @param bool $sent_to_admin Whether the email is for admin.
   * @param bool $plain_text Whether the email is plain text.
   */
  public function email_instructions($order, $sent_to_admin, $plain_text = false)
  {
    if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status('on-hold')) {
      echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
    }
  }
}
