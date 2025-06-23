<?php
/**
 * Plugin Name: سیستم پیشنهاد عطر
 * Plugin URI: https://github.com/mohammadnasserhajihashemabad/perfume-recommendation
 * Description: افزونه ووکامرس برای پیشنهاد عطر بر اساس ویژگی‌های کاربر
 * Version: 1.0.0
 * Author: Mohammad Nasser Haji Hashemabad
 * Author URI: https://github.com/mohammadnasserhajihashemabad
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PERFUME_RECOMMENDATION_VERSION', '1.0.0');
define('PERFUME_RECOMMENDATION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PERFUME_RECOMMENDATION_PLUGIN_URL', plugin_dir_url(__FILE__));

// Required files
$required_files = [
    'includes/class-perfume-recommendation.php',
    'includes/class-perfume-recommendation-admin.php',
    'includes/class-perfume-recommendation-frontend.php',
    'includes/class-perfume-recommendation-ajax.php',
    'includes/class-perfume-recommendation-activator.php',
    'includes/class-perfume-recommendation-deactivator.php',
    'includes/class-perfume-recommendation-product.php'
];

foreach ($required_files as $file) {
    if (!file_exists(PERFUME_RECOMMENDATION_PLUGIN_DIR . $file)) {
        wp_die('فایل مورد نیاز یافت نشد: ' . $file);
    }
    require_once PERFUME_RECOMMENDATION_PLUGIN_DIR . $file;
}

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    wp_die('برای کارکرد این افزونه، ووکامرس الزامی است.');
}

// Initialize the plugin
function perfume_recommendation_init() {
    // Initialize main plugin class
    $plugin = new Perfume_Recommendation();
    $plugin->run();
    
    // Initialize product class
    $product = new Perfume_Recommendation_Product('perfume-recommendation', PERFUME_RECOMMENDATION_VERSION);
}
add_action('plugins_loaded', 'perfume_recommendation_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    Perfume_Recommendation_Activator::activate();
    add_option('perfume_recommendation_do_activation_redirect', true);
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    Perfume_Recommendation_Deactivator::deactivate();
});

// Admin notice for activation
add_action('admin_notices', function() {
    if (get_option('perfume_recommendation_do_activation_redirect', false)) {
        delete_option('perfume_recommendation_do_activation_redirect');
        ?>
        <div class="notice notice-success is-dismissible">
            <p>سیستم پیشنهاد عطر با موفقیت فعال شد.</p>
            <p>لطفاً تنظیمات افزونه را پیکربندی کنید.</p>
        </div>
        <?php
    }
}); 