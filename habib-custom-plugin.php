<?php
/*
 * Plugin Name:       Hide Woo Categories and Show for Particular Users
 * Plugin URI:        https://github.com/weblearnerhabib/
 * Description:       Go to Setting then navigate Hide Category Settings, then set users and exclude categories easily. You can add multiple categories by , and also can add multiple user using , Comma.
 * Version:           1.2.1
 * Requires at least: 5.3
 * Requires PHP:      7.2
 * Author:            Freelancer Habib
 * Author URI:        https://freelancer.com/u/csehabiburr183/
 * Update URI:        https://github.com/weblearnerhabib/
 * Text Domain:       hwcspu
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Add a settings page to the WordPress dashboard.
function hide_woocommerce_category_settings_page() {
    add_options_page(
        'Hide WooCommerce Category Settings',
        'Hide Category Settings',
        'manage_options',
        'hide_woocommerce_category_settings',
        'hide_woocommerce_category_settings_page_callback'
    );
}
add_action('admin_menu', 'hide_woocommerce_category_settings_page');

// Callback function to display the settings page.
function hide_woocommerce_category_settings_page_callback() {
    ?>
    <div class="wrap">
        <h2>Hide WooCommerce Category Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('hide_woocommerce_category_settings');
            do_settings_sections('hide_woocommerce_category_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Allowed Usernames:</th>
                    <td>
                        <input type="text" name="allowed_usernames" value="<?php echo esc_attr(get_option('allowed_usernames', '')); ?>" />
                        <p class="description">Enter usernames separated by commas (e.g., username1, username2).</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Excluded Category IDs:</th>
                    <td>
                        <input type="text" name="excluded_category_ids" value="<?php echo esc_attr(get_option('excluded_category_ids', '')); ?>" />
                        <p class="description">Enter category IDs separated by commas (e.g., 23, 45).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register the settings.
function hide_woocommerce_category_register_settings() {
    register_setting('hide_woocommerce_category_settings', 'allowed_usernames');
    register_setting('hide_woocommerce_category_settings', 'excluded_category_ids');
}
add_action('admin_init', 'hide_woocommerce_category_register_settings');

// Function to hide specific WooCommerce product categories.
function hide_woocommerce_category_exclude_category($query) {
    if ((is_shop() || is_product_category()) && !is_admin()) {
        // Get the excluded category IDs.
        $excluded_category_ids = get_option('excluded_category_ids', '');

        // Get the allowed usernames from the settings.
        $allowed_usernames = get_option('allowed_usernames', '');

        // Check if the current user is allowed to view products.
        $current_user = wp_get_current_user();
        $allowed_usernames_array = array_map('trim', explode(',', $allowed_usernames));

        if (!in_array($current_user->user_login, $allowed_usernames_array)) {
            // Exclude the specified product categories.
            $query->set('product_cat', '-' . $excluded_category_ids);
        }
    }
}

// Hook the function to the pre_get_posts action.
add_action('pre_get_posts', 'hide_woocommerce_category_exclude_category');
