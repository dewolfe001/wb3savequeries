<?php

/**
 * Plugin Name:     Save Queries
 * Plugin URI:      https://web321.co/plugins/wb3savequeries
 * Description:     Store SQL queries to debug issues with a WordPress installation. For use in development, not production.
 * Version:         1.0.0
 * Author:          dewolfe001
 * Author URI:      https://shawndewolfe.com/
 * Donate:          https://www.paypal.com/paypalme/web321co/20/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     wb3savequeries
 * Domain Path:     /languages/
 *
 * @package         Wb3savequeries
 * @category Core
 * @author dewolfe001
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'SWSWQ_NAME', plugin_basename( __FILE__ ));

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WB3SAVEQUERIES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wb3savequeries-activator.php
 */
function activate_wb3savequeries() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wb3savequeries-activator.php';
	Wb3savequeries_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wb3savequeries-deactivator.php
 */
function deactivate_wb3savequeries() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wb3savequeries-deactivator.php';
	Wb3savequeries_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wb3savequeries' );
register_deactivation_hook( __FILE__, 'deactivate_wb3savequeries' );

// donation link
add_filter( 'plugin_row_meta', 'wb3savequeries_row_meta', 10, 2 );
function wb3savequeries_row_meta( $links, $file ) {    
    if (SWSWQ_NAME == $file ) {
        $row_meta = array(
          'donate'    => '<a href="' . esc_url( 'https://www.paypal.com/paypalme/web321co/20/' ) . '" target="_blank" aria-label="' . esc_attr__( 'Donate', 'wb3savequeries' ) . '" >' . esc_html__( 'Donate', 'wb3savequeries' ) . '</a>'
        );

        return array_merge( $links, $row_meta );
    }
    return (array) $links;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wb3savequeries.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wb3savequeries() {
	add_action('shutdown', 'run_wb3savequeries_action_logger');
    add_filter( 'log_query_custom_data', 'run_wb3savequeries_filter_logger', 10, 5 );
    
}


function run_wb3savequeries_action_logger() {
    global $wpdb;
    $log_file = fopen(ABSPATH.'/sql_log.txt', 'a');
    fwrite($log_file, "//////////////////////////////////////////\n\n" . date("F j, Y, g:i:s a")."\n");
    foreach($wpdb->queries as $q) {
        fwrite($log_file, $q[0] . " - ($q[1] s)" . "\n\n");
    }
    fclose($log_file);
}

function run_wb3savequeries_filter_logger( $query_data, $query, $query_time, $query_callstack, $query_start ) {
    global $wpdb;
    $log_file = fopen(ABSPATH.'/sql_log.txt', 'a');
    fwrite($log_file, "//////////////////////////////////////////\n\n" . date("F j, Y, g:i:s a")."\n");

    fwrite($log_file, $query . ' ' . $query_data . " - ($query_time s) " . print_r($query_callstack, TRUE) . ' - ' . print_r($query_start, TRUE) . "\n\n");
    fclose($log_file);
}

run_wb3savequeries();
