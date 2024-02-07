<?php

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

  if (!empty($jd_payment_option) || !empty($jd_account_number) || !empty($jd_account_name)) {
    echo '<div class="jd-financial-multi-use-line">';
    echo '<h3>' . __('John Deere Financial Multi-Use Line',  'john-deere-payment') . '</h3>';
    if (!empty($jd_account_name)) {
      echo '<p><strong>' . __('Account Name',  'john-deere-payment') . ':</strong> ' . $jd_account_name . '<br/>';
    }
    if (!empty($jd_account_number)) {
      echo '<strong>' . __('Account Number',  'john-deere-payment') . ':</strong> ' . $jd_account_number . '<br/>';
    }
    if (!empty($jd_payment_option)) {
      echo '<strong>' . __('Payment Option',  'john-deere-payment') . ':</strong> ' . $jd_payment_option . '</p>';
    }
    echo '</div>';
  }
}

/**
 * Add custom fields to the registration form
 */
add_action('woocommerce_register_form', 'add_john_deere_custom_field_to_registration_form');
function add_john_deere_custom_field_to_registration_form()
{
?>
  <div class="john-deere-register-form">
    <p class="form-row form-row-wide">
    <h3><?php _e('John Deere Financial Multi-Use Line', 'john-deere-payment') ?></h3>
    <p class="form-row form-row-wide">
      <label for="reg_jd_account_name"><?php _e('John Deere Account Name',  'john-deere-payment'); ?> </label>
      <input type="text" class="input-text" name="jd_account_name" id="reg_jd_account_name" value="<?php if (!empty($_POST['jd_account_name'])) echo esc_attr($_POST['jd_account_name']); ?>" />
    </p>
    <p class="form-row form-row-wide">
      <label for="reg_jd_account_number"><?php _e('John Deere Account Number',  'john-deere-payment'); ?> </label>
      <input type="text" class="input-text" name="jd_account_number" id="reg_jd_account_number" value="<?php if (!empty($_POST['jd_account_number'])) echo esc_attr($_POST['jd_account_number']); ?>" />
    </p>
    <p class="form-row form-row-wide">
      <label><?php _e('John Deere Payment Option',  'john-deere-payment'); ?> </label>
    <div class="john-deere-options">
      <input type="radio" name="jd_payment_option" value="Regular Limit Line" checked> <?php _e('Regular Limit Line.', 'john-deere-payment') ?> <span style="font-size: 14px; display:inline-block"><?php _e('I agree that this transaction will be billed to my John Deere Financial Multi-Use Line', 'john-deere-payment') ?></span>
      <input type="radio" name="jd_payment_option" value="Special Term Limit Line"><?php _e(' Special Term Limit Line.', 'john-deere-payment') ?><span style="font-size: 14px; display:inline-block"><?php _e('I agree this transaction will be applied to my John Deere Financial Multi-Use Line.', 'john-deere-payment') ?></span>
    </div>
    </p>
  </div>
<?php
}

/**
 * Validate custom fields in the registration form
 */
add_action('woocommerce_register_post', 'validate_custom_field_in_registration_form', 10, 3);
function validate_custom_field_in_registration_form($username, $email, $validation_errors)
{
  if (!is_numeric($_POST['jd_account_number'])) {
    $validation_errors->add('jd_account_number_error', __('John Deere Account Number should be a number!', 'john-deere-payment'));
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
  $jd_account_mode = get_option('woocommerce_john_deere_payment_settings')['jd_account_mode']; // Get the John Deere account mode
  $user_id = get_current_user_id();
  $jd_account_enabled = get_user_meta($user_id, 'jd_account_enabled', true);
  $jd_account_number = get_user_meta($user_id, 'jd_account_number', true);
  $jd_account_name = get_user_meta($user_id, 'jd_account_name', true);
  $jd_payment_option = get_user_meta($user_id, 'jd_payment_option', true);
  $jd_account_request_sent = get_user_meta($user_id, 'jd_account_request_sent', true); // Get the request sent status

  echo '<h3>' . __('John Deere Financial Multi-Use Line',  'john-deere-payment') . '</h3>';
?>
  <fieldset>
    <legend><?php _e('John Deere Account Details',  'john-deere-payment'); ?></legend>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
      <?php
      $checked = checked($jd_account_enabled, 1, false);
      $disabled = ($jd_account_mode === 'preselected_users') ? 'disabled' : '';


      if ($jd_account_mode === 'preselected_users') {
        $status_text = $jd_account_enabled ? __('Enabled', 'john-deere-payment') : __('Disabled', 'john-deere-payment');
        echo '<p>' . __('John Deere Account Status: ', 'john-deere-payment') . '<strong>' . $status_text . '</strong></p>';
      } else {
        $checked = checked($jd_account_enabled, 1, false);
        echo '<input type="checkbox" class="woocommerce-Input woocommerce-Input--checkbox input-checkbox" name="jd_account_enabled" id="jd_account_enabled" value="1" ' . $checked . ' /> ' . __('Enable / Disable John Deere Account ', 'john-deere-payment');
      }

      // Check if a request has been sent and if the account status has been changed
      $button_disabled = $jd_account_request_sent   ? ' disabled' : '';
      $button_text_disable = $jd_account_enabled   ? 'disable' : 'enable';
      $button_text = $jd_account_request_sent  ? __('Wait for the admin response', 'john-deere-payment') : __('Request to ' . $button_text_disable . ' John Deere Payment', 'john-deere-payment');

      if ($jd_account_mode === 'preselected_users') {
        echo '<button type="submit" name="jd_account_request" value="1" style="font-size: 14px; padding:5px;"' . $button_disabled .
          '>' . $button_text . '</button>';
      }

      // If the account status has been changed, remove the user meta value
      if ($jd_account_request_sent && $jd_account_enabled) {
        delete_user_meta($user_id, 'jd_account_request_sent');
      }

      ?>
    </p>
    <div id="jd_account_details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="jd_account_name"><?php _e('Account Name',  'john-deere-payment'); ?></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="jd_account_name" id="jd_account_name" value="<?php echo esc_attr($jd_account_name); ?>" />
      </p>
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="jd_account_number"><?php _e('Account Number',  'john-deere-payment'); ?></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="jd_account_number" id="jd_account_number" value="<?php echo esc_attr($jd_account_number); ?>" />
      </p>
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label><?php _e('Payment Option',  'john-deere-payment'); ?></label>
        <input type="radio" name="jd_payment_option" value="Regular Limit Line" <?php checked($jd_payment_option, 'Regular Limit Line'); ?>><?php _e(' Regular Limit Line', 'john-deere-payment') ?><br>
        <input type="radio" name="jd_payment_option" value="Special Term Limit Line" <?php checked($jd_payment_option, 'Special Term Limit Line'); ?>> <?php _e('Special Term Limit Line', 'john-deere-payment') ?>
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
 * Handle the John Deere account request
 */
add_action('woocommerce_save_account_details', 'handle_jd_account_request');
function handle_jd_account_request()
{
  // If the jd_account_request button was not clicked, return immediately
  if (!isset($_POST['jd_account_request'])) {
    return;
  }

  $user_id = get_current_user_id(); // Get the ID of the current user
  $user_info = get_userdata($user_id);
  $username = $user_info->user_login;

  // Retrieve the value of the jd_account_enabled checkbox
  $jd_account_enabled = get_user_meta($user_id, 'jd_account_enabled', true);
  $status = $jd_account_enabled ? 'disable' : 'enable';

  // Send an email to the admin
  send_jd_account_request_email($user_id, $username, $status);

  // Add a success notice
  wc_add_notice(__('Your request has been successfully sent to the admin.', 'john-deere-payment'), 'success');

  // Set a new user meta value
  update_user_meta(get_current_user_id(), 'jd_account_request_sent', true);

  // Store the user's ID in the jd_account_request_pending option
  update_option('jd_account_request_pending', get_current_user_id());
}

/**
 * Save custom fields from the User edit account form
 */
add_action('woocommerce_save_account_details', 'save_jd_account_enabled');
function save_jd_account_enabled($user_id)
{
  $jd_account_mode = get_option('woocommerce_john_deere_payment_settings')['jd_account_mode']; // Get the John Deere account mode
  if ($jd_account_mode !== 'preselected_users') {
    if (isset($_POST['jd_account_enabled'])) {
      update_user_meta($user_id, 'jd_account_enabled', 1);
    } else {
      delete_user_meta($user_id, 'jd_account_enabled');
    }
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
 * Display a notification in the admin area when a user requests to enable / disable their John Deere account
 */
add_action('admin_notices', 'display_jd_account_request_notification');
function display_jd_account_request_notification()
{
  // If the jd_account_request_pending option is set, display a warning message
  $user_id = get_option('jd_account_request_pending');

  // Retrieve the value of the jd_account_enabled checkbox
  $jd_account_enabled = get_user_meta($user_id, 'jd_account_enabled', true);

  $status = $jd_account_enabled ? 'disable' : 'enable';

  if ($user_id) {
    $user = get_userdata($user_id);
  ?>
    <div class="notice notice-warning is-dismissible">
      <p>
        <?php printf(__('User <a href="%s">%s</a> (%s) has requested to <strong><a href="%s">%s</a></strong> their John Deere account. Please check your email for more details.', 'john-deere-payment'), get_edit_user_link($user_id), $user->display_name, $user->user_email, admin_url('user-edit.php?user_id=' . $user_id . '#section-payment-john-deere'), $status); ?>
      </p>
    </div>
  <?php
  }

  // If the jd_account_request_updated option is set, display a success message and delete the option
  if (get_option('jd_account_request_updated')) {
    delete_option('jd_account_request_updated');
  ?>
    <div class="notice notice-success is-dismissible">
      <p><?php _e('The John Deere account status has been successfully updated.', 'john-deere-payment'); ?></p>
    </div>
  <?php
  }
}

/**
 * Send an email to the admin when a user requests to enable / disable their John Deere account
 */
function send_jd_account_request_email($user_id, $username, $status)
{
  $settings = get_option('woocommerce_john_deere_payment_settings'); // Get the plugin settings
  $jd_admin_email = $settings['jd_admin_email'];  // Get the admin email

  // If the admin email is not valid or not exist, fall back to the default admin email
  if (!is_email($jd_admin_email) || !isset($settings['jd_admin_email'])) {
    $jd_admin_email = get_option('admin_email');
  }

  $subject = __('John Deere Account Request', 'john-deere-payment');

  // Load the email template
  ob_start();
  include plugin_dir_path(__FILE__) . 'email-template.php';
  $template = ob_get_clean();

  // Replace the placeholders with the actual values
  $message = str_replace(array('{user_id}', '{username}', '{status}'), array($user_id, $username, $status), $template);

  // Set the email headers
  $headers = array('Content-Type: text/html; charset=UTF-8');

  // Send the email
  wp_mail($jd_admin_email, $subject, $message, $headers);
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
  <div id="section-payment-john-deere">
    <h3><?php _e('John Deere Account Details', 'john-deere-payment'); ?></h3>
    <table class="form-table">
      <tr>
        <th><label for="jd_account_enabled"><?php _e('Enable / Disable John Deere Account', 'john-deere-payment'); ?></label></th>
        <td><input type="checkbox" name="jd_account_enabled" id="jd_account_enabled" value="1" <?php checked($jd_account_enabled, 1); ?> /></td>
      </tr>
      <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
        <th><label for="jd_account_name"><?php _e('Account Name', 'john-deere-payment'); ?></label></th>
        <td><input type="text" name="jd_account_name" id="jd_account_name" value="<?php echo esc_attr($jd_account_name); ?>" class="regular-text" /></td>
      </tr>
      <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
        <th><label for="jd_account_number"><?php _e('Account Number', 'john-deere-payment'); ?></label></th>
        <td><input type="text" name="jd_account_number" id="jd_account_number" value="<?php echo esc_attr($jd_account_number); ?>" class="regular-text" /></td>
      </tr>
      <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
        <th><label><?php _e('Payment Option', 'john-deere-payment'); ?></label></th>
        <td>
          <input type="radio" name="jd_payment_option" value="Regular Limit Line" <?php checked($jd_payment_option, 'Regular Limit Line'); ?>> <?php _e('Regular Limit Line', 'john-deere-payment') ?><br>
          <input type="radio" name="jd_payment_option" value="Special Term Limit Line" <?php checked($jd_payment_option, 'Special Term Limit Line'); ?>> <?php _e('Special Term Limit Line', 'john-deere-payment') ?>
        </td>
      </tr>
    </table>
  </div>
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
add_action('personal_options_update', 'save_john_deere_fields_from_user_admin_page');
add_action('edit_user_profile_update', 'save_john_deere_fields_from_user_admin_page');
function save_john_deere_fields_from_user_admin_page($user_id)
{
  if (current_user_can('edit_user', $user_id)) {
    // Update the jd_account_enabled value
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

    // Set a new option to indicate that the jd_account_enabled value has been updated
    update_option('jd_account_request_updated', true);
    update_option('jd_account_request_pending', false);
  }
}

/**
 * Customise the form field
 */
function jd_woocommerce_form_field($key, $args, $value = null)
{
  $args = wp_parse_args($args, array(
    'type'          => 'text',
    'label'         => '',
    'placeholder'   => '',
    'class'         => array(),
    'required'      => false,
    'options'       => array(),
    'default'       => '',
  ));

  $field = '';

  if ($args['type'] === 'radio') {
    $field .= '<fieldset id="' . esc_attr($key) . '" class="form-row ' . esc_attr(implode(' ', $args['class'])) . '">';
    $field .= '<legend>' . wp_kses_post($args['label']) . '</legend>';

    foreach ($args['options'] as $option_key => $option_value) {
      $field .= '<label><input type="radio" name="' . esc_attr($key) . '" value="' . esc_attr($option_key) . '"' . checked($args['default'], $option_key, false) . ' /> ' . $option_value . '</label><br />';
    }

    $field .= '</fieldset>';
  }

  // Add other field types here...

  return $field;
}

/**
 * Add custom fields to the order email
 */
add_action('woocommerce_email_order_meta', 'add_john_deere_payment_details_to_email', 10, 4);
function add_john_deere_payment_details_to_email($order, $sent_to_admin, $plain_text, $email)
{
  // Check if the order has the John Deere payment details
  $jd_payment_option = get_post_meta($order->get_id(), 'jd_payment_option', true);
  $jd_account_number = get_post_meta($order->get_id(), 'jd_account_number', true);
  $jd_account_name = get_post_meta($order->get_id(), 'jd_account_name', true);

  if ($jd_payment_option || $jd_account_number || $jd_account_name) {
    echo '<h2>John Deere Payment Details</h2>';
    if ($jd_account_name) {
      echo '<p><strong>Account Name:</strong> ' . esc_html($jd_account_name) . '<br/>';
    }
    if ($jd_account_number) {
      echo '<strong>Account Number:</strong> ' . esc_html($jd_account_number) . '<br/>';
    }
    if ($jd_payment_option) {
      echo '<strong>Payment Option:</strong> ' . esc_html($jd_payment_option) . '</p>';
    }
  }
}

/**
 * Display custom fields in the order details page
 */
add_action('woocommerce_order_details_after_order_table', 'display_john_deere_details_on_order_view');
function display_john_deere_details_on_order_view($order)
{
  // Check if the order has the John Deere details
  $jd_payment_option = get_post_meta($order->get_id(), 'jd_payment_option', true);
  $jd_account_number = get_post_meta($order->get_id(), 'jd_account_number', true);
  $jd_account_name = get_post_meta($order->get_id(), 'jd_account_name', true);

  if ($jd_payment_option || $jd_account_number || $jd_account_name) {
    echo '<h2>John Deere Details</h2>';

    if ($jd_account_name) {
      echo '<p><strong>Account Name:</strong> ' . esc_html($jd_account_name) . '<br/>';
    }
    if ($jd_account_number) {
      echo '<strong>Account Number:</strong> ' . esc_html($jd_account_number) . '<br/>';
    }
    if ($jd_payment_option) {
      echo '<strong>Payment Option:</strong> ' . esc_html($jd_payment_option) . '</p>';
    }
  }
}

/**
 * Extra setting for preselected_users mode
 * Payment method only visible for the preselected users
 */
add_filter('woocommerce_available_payment_gateways', 'filter_payment_gateways');
function filter_payment_gateways($gateways)
{
  $jd_account_mode = get_option('woocommerce_john_deere_payment_settings')['jd_account_mode']; // Get the John Deere account mode

  if ($jd_account_mode == 'preselected_users') {
    $user_id = get_current_user_id(); // Get the ID of the current user
    $jd_account_enabled = get_user_meta($user_id, 'jd_account_enabled', true); // Get the enabled status of the John Deere account

    // If the John Deere account is not enabled for the user, unset the John Deere payment gateway
    if (!$jd_account_enabled && isset($gateways['john_deere_payment'])) {
      unset($gateways['john_deere_payment']);
    }
  }

  return $gateways;
}

/**
 * Add a custom bulk action to the users list
 */
add_filter('bulk_actions-users', 'add_bulk_action');
function add_bulk_action($bulk_actions)
{
  $bulk_actions['enable_john_deere_account'] = __('Enable John Deere Account', 'john-deere-payment');
  $bulk_actions['disable_john_deere_account'] = __('Disable John Deere Account', 'john-deere-payment');
  return $bulk_actions;
}

/**
 * Handle the custom bulk action
 */
add_filter('handle_bulk_actions-users', 'handle_bulk_action', 10, 3);
function handle_bulk_action($redirect_to, $doaction, $user_ids)
{
  if ($doaction === 'enable_john_deere_account') {
    foreach ($user_ids as $user_id) {
      // Enable the John Deere account for the user
      update_user_meta($user_id, 'jd_account_enabled', true);
    }
  } elseif ($doaction === 'disable_john_deere_account') {
    foreach ($user_ids as $user_id) {
      // Disable the John Deere account for the user
      update_user_meta($user_id, 'jd_account_enabled', false);
    }
  }

  return $redirect_to;
}

/**
 * Add a custom column to the users list
 */
function add_john_deere_column($columns)
{
  $columns['john_deere_payment_status'] = 'John Deere Payment';
  return $columns;
}
add_filter('manage_users_columns', 'add_john_deere_column');

// Fill the new column with data
function fill_john_deere_column($value, $column_name, $user_id)
{
  if ('john_deere_payment_status' == $column_name) {
    // Replace this with the actual status of the John Deere payment for the user
    $status = get_user_meta($user_id, 'jd_account_enabled', true);
    return $status ? 'Enabled' : 'Disabled';
  }
  return $value;
}
add_filter('manage_users_custom_column', 'fill_john_deere_column', 10, 3);
