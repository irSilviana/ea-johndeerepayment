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
- [Support](#support)
- [License](#license)

## Description

The John Deere Payment Plugin adds a new payment method to your WooCommerce store. Customers can enter their John Deere account details at checkout and choose this payment method to pay for their orders. The payment method is not connected to the John Deere API, so the admin has to update the order status if it's not set as "completed". The default value is "Processing". Remember! Except "Completed", the admin is required to update the order status manually.

## Features

- **Custom Payment Gateway**: Adds a new payment gateway that can be enabled from the WooCommerce settings.

- **Provides three modes of payment methods**:

  - **Preselected Users**: Only users who are preselected will be able to use the John Deere payment method.
  - **Enabled Users**: All users who have the John Deere account enabled will be able to use the John Deere payment method.
  - **All Users**: All users will be able to use the John Deere payment method.

- **Account Details Input**: Allows customers to enter their John Deere account number and other details during checkout.

- **Customization**:

  - The admin can customize the default order status, and change the default labels, instructions, and descriptions.
  - Allows the admin to set a custom email to get notification for John Deere payments request.
  - Validates the admin email before saving it.

- **Validation**: Validates the account number input during checkout, registration and edit user account detail.

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

## Support

If you have any issues or questions, please open an issue on the plugin's GitHub page.

## License

This plugin is licensed under the GPL v2 or later.
