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
		global $wpdb;

   		$table_name = $wpdb->prefix . "kjl_bot"; 

		   $charset_collate = $wpdb->get_charset_collate();

		   $sql = "CREATE TABLE $table_name (
			 id int NOT NULL,
			 title varchar(255) DEFAULT '' NOT NULL,
			 sub_title varchar(255) DEFAULT '' NOT NULL,
			 title_author varchar(255) DEFAULT '' NOT NULL,
			 keywords text DEFAULT '' NOT NULL,
			 publication_place varchar(255) DEFAULT '' NOT NULL,
			 publisher varchar(255) DEFAULT '' NOT NULL,
			 publication_year varchar(4) DEFAULT '' NOT NULL,
			 projected_publication_year varchar(10) DEFAULT '' NOT NULL,
			 projected_publication_date varchar(10) DEFAULT '' NOT NULL,
			 link_to_dataset varchar(255) DEFAULT '' NOT NULL,
			 isbn_with_dashes varchar(255) DEFAULT '' NOT NULL,
			 added_to_sql varchar(255) DEFAULT '' NOT NULL,
			 publisher_jlp_nominated boolean DEFAULT 0 NOT NULL,
			 publisher_jlp_awarded boolean DEFAULT 0 NOT NULL,
			 publisher_kimi_nominated boolean DEFAULT 0 NOT NULL,
			 cover_url varchar(255) DEFAULT '' NOT NULL,
			 PRIMARY KEY  (id)
		   ) $charset_collate;";
		   
		   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		   dbDelta( $sql );

		   if ( ! wp_next_scheduled( 'kjl_cron_hook' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'kjl_cron_hook' );
		}
	}

}
