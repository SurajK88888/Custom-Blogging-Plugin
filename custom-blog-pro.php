<?php
/**
 * Plugin Name:       Custom Blog Pro (CBP)
 * Plugin URI:        https://example.com/custom-blog-pro
 * Description:       A Production-Level WordPress Blog Management Plugin with Email Marketing, Ad Management, Analytics, Role-Based Access Control, Modern UI, and Fully Customizable Design.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Suraj Kumar
 * Author URI:        https://surajtechservice.vercel.app/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-blog-pro
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define Plugin Constants
define( 'CBP_VERSION', '1.0.0' );
define( 'CBP_PLUGIN_FILE', __FILE__ );
define( 'CBP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CBP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader will go here
if ( file_exists( CBP_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once CBP_PLUGIN_DIR . 'vendor/autoload.php';
}

// Bootstrap the plugin
function cbp_init() {
    return \CBP\core\Plugin::get_instance();
}
// Initialize the plugin instance immediately so its hooks are registered before plugins_loaded fires.
cbp_init();
