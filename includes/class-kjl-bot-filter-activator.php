<?php

/**
 * Fired during plugin activation
 *
 * @link       https://gabrielserwas.com
 * @since      1.0.0
 *
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/includes
 * @author     Gabriel Serwas <post@gabrielserwas.com>
 */
class Kjl_Bot_Filter_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		   if ( ! wp_next_scheduled( 'kjl_cron_hook' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'kjl_cron_hook' );
		}
	}

}
