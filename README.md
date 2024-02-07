# John Deere Payment Plugin for WooCommerce

This is a custom payment gateway for WooCommerce that allows users to pay with their John Deere account.

<p align="center">
  <img src="assets\images\john-deere-logo.png" alt="Logo John Deere Multi-Use Account">
</p>

## Table of Contents

- [Description](#description)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Screenshots](#screenshots)
- [Internationalization](#Internationalization)
- [Support](#support)
- [License](#license)

## Description

The John Deere Payment Plugin adds a new payment method to your WooCommerce store. Customers can enter their John Deere account details at checkout and choose this payment method to pay for their orders. The payment method is not connected to the John Deere API, so the admin has to update the order status if it's not set as "completed". The default value is "Processing". Remember! Except for "Completed", the admin is required to update the order status manually.

## Features

- **Custom Payment Gateway**: Adds a new payment gateway that can be enabled from the WooCommerce settings.

- **Provides three modes of payment methods**:

  - **Preselected Users**: Only users who are preselected will be able to use the John Deere payment method.
  - **Enabled Users**: All users who have the John Deere account enabled will be able to use the John Deere payment method.
  - **All Users**: All users will be able to use the John Deere payment method.

- **Account Details Input**: Allows customers to enter their John Deere account number and other details during checkout.

- **Customization**:

  - The admin can customize the default order status, and change the default labels, instructions, and descriptions.
  - Allows the admin to set a custom email to get notifications for John Deere payment requests.
  - Validate the admin email before saving it.

- **Validation**: Validates the account number input during checkout, registration, and edit user account details.

- **Email Instructions**: Adds instructions for the John Deere payment method to the order confirmation email.

- **Order View Details**: Displays the John Deere account details on the order view page in the account section of your site.

- **User Profile Integration**: Allows administrators to manage John Deere account details (account enabled status, account number, account name, and payment option) from the user profile page in the WordPress admin area.

- **Bulk Action**:
  - Adds a new bulk action to the users list to enable or disable John Deere accounts for multiple users at once.
  - Adds a new column to the users list to display the status of the John Deere payment for each user.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/john-deere-payment` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the WooCommerce->Settings->Payments screen to configure the plugin.

## Usage

### Setting the Admin Email

After installation and activation, go to WooCommerce->Settings->Payments. You should see "John Deere Payment" as an option. Click "Manage" to configure the payment gateway. Here you can set the "Admin Email" option to the email address that should receive notifications about John Deere payments.

### Enabling or Disabling John Deere Accounts

In the users list in the WordPress admin, select the users for whom you want to enable or disable the John Deere account. Then, from the "Bulk actions" dropdown, select "Enable John Deere Account" or "Disable John Deere Account" and click "Apply".

### Viewing the John Deere Payment Status

In the users list in the WordPress admin, there is a new column "John Deere Payment Status". This column displays the status of the John Deere payment for each user.

### Selecting the Payment Method Mode

In the WooCommerce settings, under the "Payments" tab, click on "John Deere Payment". Here you can select the mode of the payment method: "Preselected Users", "Enabled Users", or "All Users".

## Screenshots

<div align="center">
  <img src="assets\images\JD-financial-multi-use-line-settings.png" alt="John Deere settings">
   <p>John Deere Multi-Use Line Settings</p>

  <img src="assets\images\JD-bulk-users.png" alt="John Deere bulk edit users">
  <p>John Deere bulk edit users</p>

  <img src="assets\images\JD-Payment method - checkout page.png" alt="John Deere payment method checkout page">
   <p>John Deere Checkout Page</p>
  
  <img src="assets\images\JD-PreSelected User Mode-user account detail.png" alt="John Deere user account detail">
  <p>John Deere User Account Detail</p>
</div>

## Internationalization

This plugin is ready for internationalization and can be translated into other languages. The `.pot` file is included in the `languages` directory of this plugin. You can use a tool like [Poedit](https://poedit.net/) or a WordPress plugin like [Loco Translate](https://wordpress.org/plugins/loco-translate/) to create translations in your language.

If you create a translation, please consider contributing it back to the plugin so it can be shared with everyone. You can submit your translation files as a pull request on the plugin's GitHub page.

## Support

If you have any issues or questions, please open an issue on the plugin's GitHub page.

## License

This plugin is licensed under the GPL v2 or later.
