<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://gabrielserwas.com
 * @since      1.0.0
 *
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/includes
 * @author     Gabriel Serwas <post@gabrielserwas.com>
 */
class Kjl_Bot_Filter_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$timestamp = wp_next_scheduled( 'kjl_cron_hook' );
		wp_unschedule_event( $timestamp, 'kjl_cron_hook' );
	}

}
