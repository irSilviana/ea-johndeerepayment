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

  echo '<div class="jd-financial-multi-use-line">';
  echo '<h3>' . __('John Deere Financial Multi-Use Line',  'john-deere-payment') . '</h3>';
  echo '<p><strong>' . __('Payment Option',  'john-deere-payment') . ':</strong> ' . $jd_payment_option . '<br/>';
  echo '<strong>' . __('Account Number',  'john-deere-payment') . ':</strong> ' . $jd_account_number . '<br/>';
  echo '<strong>' . __('Account Name',  'john-deere-payment') . ':</strong> ' . $jd_account_name . '</p>';
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
  <h3><?php _e('John Deere Financial Multi-Use Line', 'john-deere-payment') ?></h3>
  <label for="reg_jd_account_number"><?php _e('John Deere Account Number',  'john-deere-payment'); ?> </label>
  <input type="text" class="input-text" name="jd_account_number" id="reg_jd_account_number" value="<?php if (!empty($_POST['jd_account_number'])) echo esc_attr($_POST['jd_account_number']); ?>" />
  </p>
  <p class="form-row form-row-wide">
    <label for="reg_jd_account_name"><?php _e('John Deere Account Name',  'john-deere-payment'); ?> </label>
    <input type="text" class="input-text" name="jd_account_name" id="reg_jd_account_name" value="<?php if (!empty($_POST['jd_account_name'])) echo esc_attr($_POST['jd_account_name']); ?>" />
  </p>
  <p class="form-row form-row-wide">
    <label><?php _e('John Deere Payment Option',  'john-deere-payment'); ?> </label>
    <input type="radio" name="jd_payment_option" value="Regular Limit Line" checked> <?php _e('Regular Limit Line.', 'john-deere-payment') ?> <br>
    <span style="font-size: 14px;"><?php _e('I agree that this transaction will be billed to my John Deere Financial Multi-Use Line', 'john-deere-payment') ?></span>
    <br><br>
    <input type="radio" name="jd_payment_option" value="Special Term Limit Line"><?php _e(' Special Term Limit Line.', 'john-deere-payment') ?><br>
    <span style="font-size: 14px;"><?php _e('I agree this transaction will be applied to my John Deere Financial Multi-Use Line.', 'john-deere-payment') ?></span>
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
    $validation_errors->add('jd_account_number_error', __('John Deere Account Number is  should be a number!', 'john-deere-payment'));
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

  echo '<h3>' . __('John Deere Financial Multi-Use Line',  'john-deere-payment') . '</h3>';
?>
  <fieldset>
    <legend><?php _e('John Deere Account Details',  'john-deere-payment'); ?></legend>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">

      <input type="checkbox" class="woocommerce-Input woocommerce-Input--checkbox input-checkbox" name="jd_account_enabled" id="jd_account_enabled" value="1" <?php checked($jd_account_enabled, 1); ?> /> <?php _e('Enable / Disable John Deere Account ',  'john-deere-payment') ?>
    </p>
    <div id="jd_account_details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="jd_account_number"><?php _e('Account Number',  'john-deere-payment'); ?></label>
        <input type="number" class="woocommerce-Input woocommerce-Input--text input-text" name="jd_account_number" id="jd_account_number" value="<?php echo esc_attr($jd_account_number); ?>" />
      </p>
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="jd_account_name"><?php _e('Account Name',  'john-deere-payment'); ?></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="jd_account_name" id="jd_account_name" value="<?php echo esc_attr($jd_account_name); ?>" />
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
  <h3><?php _e('John Deere Account Details', 'john-deere-payment'); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="jd_account_enabled"><?php _e('Enable John Deere Account', 'john-deere-payment'); ?></label></th>
      <td><input type="checkbox" name="jd_account_enabled" id="jd_account_enabled" value="1" <?php checked($jd_account_enabled, 1); ?> /></td>
    </tr>
    <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <th><label for="jd_account_number"><?php _e('Account Number', 'john-deere-payment'); ?></label></th>
      <td><input type="number" name="jd_account_number" id="jd_account_number" value="<?php echo esc_attr($jd_account_number); ?>" class="regular-text" /></td>
    </tr>
    <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <th><label for="jd_account_name"><?php _e('Account Name', 'john-deere-payment'); ?></label></th>
      <td><input type="text" name="jd_account_name" id="jd_account_name" value="<?php echo esc_attr($jd_account_name); ?>" class="regular-text" /></td>
    </tr>
    <tr class="jd-account-details" style="display: <?php echo $jd_account_enabled ? '' : 'none'; ?>;">
      <th><label><?php _e('Payment Option', 'john-deere-payment'); ?></label></th>
      <td>
        <input type="radio" name="jd_payment_option" value="Regular Limit Line" <?php checked($jd_payment_option, 'Regular Limit Line'); ?>> <?php _e('Regular Limit Line', 'john-deere-payment') ?><br>
        <input type="radio" name="jd_payment_option" value="Special Term Limit Line" <?php checked($jd_payment_option, 'Special Term Limit Line'); ?>> <?php _e('Special Term Limit Line', 'john-deere-payment') ?>
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
