<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.bvnode.com/plugins/dynamic-keyword-insertion-for-wp/
 * @since             1.0.0
 * @package           dki4wp
 *
 * @wordpress-plugin
 * Plugin Name:       Dynamic Keyword Insertion for WP
 * Plugin URI:        https://www.bvnode.com/plugins/dynamic-keyword-insertion-for-wp/
 * Description:       Innovative tool to customize web page text based on the search terms used by visitors, ensuring a personalized and engaging user experience. 
 * Version:           1.0.4
 * Author:            BVNode
 * Author URI:        https://www.bvnode.com/
 * License:           GNU General Public License v2.0 or later
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * Text Domain:       dynamic-keyword-insertion-for-wp
 * Domain Path:       /languages
 * 
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( function_exists( 'dki4wp_fs' ) ) {
    dki4wp_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'dki4wp_fs' ) ) {
        // Create a helper function for easy SDK access.
        function dki4wp_fs() {
            global $dki4wp_fs;
            if ( !isset( $dki4wp_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $dki4wp_fs = fs_dynamic_init( array(
                    'id'             => '14960',
                    'slug'           => 'dynamic-keyword-insertion-for-wp',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_0fdf7fb256aa87d89a0e4aa82215d',
                    'is_premium'     => false,
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 14,
                        'is_require_payment' => false,
                    ),
                    'menu'           => array(
                        'slug'    => 'dynamic-keyword-insertion-for-wp',
                        'contact' => false,
                        'support' => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $dki4wp_fs;
        }

        // Init Freemius.
        dki4wp_fs();
        // Signal that SDK was initiated.
        do_action( 'dki4wp_fs_loaded' );
    }
    /**
     * Currently plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    define( 'dki4wp_VERSION', '1.0.4' );
    define( 'dki4wp_PATH', plugin_dir_path( __FILE__ ) );
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-dynamic-keyword-insertion-for-wp-activator.php
     */
    function dki4wp_activate() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-dynamic-keyword-insertion-for-wp-activator.php';
        dki4wp_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-dynamic-keyword-insertion-for-wp-deactivator.php
     */
    function dki4wp_deactivate() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-dynamic-keyword-insertion-for-wp-deactivator.php';
        dki4wp_Deactivator::deactivate();
    }

    register_activation_hook( __FILE__, 'dki4wp_activate' );
    register_deactivation_hook( __FILE__, 'dki4wp_deactivate' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-dynamic-keyword-insertion-for-wp.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function dki4wp_run() {
        $plugin = new dki4wp();
        $plugin->run();
    }

    dki4wp_run();
    /*
    	// Not like register_uninstall_hook(), you do NOT have to use a static function.
    	dki4wp_fs()->add_action('after_uninstall', 'dki4wp_fs_uninstall_cleanup');
    */
}