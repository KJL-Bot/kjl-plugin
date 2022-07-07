<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://gabrielserwas.com
 * @since             1.0.0
 * @package           Kjl_Bot_Filter
 *
 * @wordpress-plugin
 * Plugin Name:       KJL Bot Filter
 * Plugin URI:        https://kjl-bot.de/
 * Description:       This plugin shows a filtered books list.
 * Version:           1.0.0
 * Author:            Gabriel Serwas
 * Author URI:        https://gabrielserwas.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kjl-bot-filter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KJL_BOT_FILTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kjl-bot-filter-activator.php
 */
function activate_kjl_bot_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kjl-bot-filter-activator.php';
	Kjl_Bot_Filter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kjl-bot-filter-deactivator.php
 */
function deactivate_kjl_bot_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kjl-bot-filter-deactivator.php';
	Kjl_Bot_Filter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kjl_bot_filter' );
register_deactivation_hook( __FILE__, 'deactivate_kjl_bot_filter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kjl-bot-filter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kjl_bot_filter() {

	$plugin = new Kjl_Bot_Filter();
	$plugin->run();

}
run_kjl_bot_filter();
