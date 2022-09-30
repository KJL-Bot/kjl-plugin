<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gabrielserwas.com
 * @since      1.0.0
 *
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/admin
 * @author     Gabriel Serwas <post@gabrielserwas.com>
 */
class Kjl_Bot_Filter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kjl_Bot_Filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kjl_Bot_Filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kjl-bot-filter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kjl_Bot_Filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kjl_Bot_Filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kjl-bot-filter-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	private function remove_special_char($str): string
	{
		$res = preg_replace('/[^0-9]/', '', $str);
		return $res;
	}

	public function kjl_cron_exec() {
		global $wpdb;
		$json_file = wp_upload_dir(null, false, false)['basedir']. '/kjl-data/recentBooks.json';
		$json = file_get_contents($json_file);
		$books = json_decode($json);
		$table_name = $wpdb->prefix . 'kjl_bot';
		foreach($books as $book) {
			$data = [
				'id' => $book->idn,
				'title' => $book->title,
				'sub_title' => $book->subTitle,
				'title_author' => $book->titleAuthor,
				'keywords' => $book->keywords,
				'publication_place' => $book->publicationPlace,
				'publisher' => $book->publisher,
				'publication_year' => $this->remove_special_char($book->publicationYear),
				'projected_publication_year' => $book->projectedPublicationDate,
				'link_to_dataset' => $book->linkToDataset,
				'isbn_with_dashes' => $book->isbnWithDashes,
				'added_to_sql' => $book->addedToSql,
				'publisher_jlp_nominated' => $book->publisherJLPNominated,
				'publisher_jlp_awarded' => $book->publisherJLPAwarded,
				'publisher_kimi_nominated' => $book->publisherKimiAwarded,
				'cover_url' => $book->coverUrl,
			];
			$values = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
			$result = $wpdb->update('wp_kjl_bot', $data, ['id' => $book->idn], $values, '%s');
			// //If nothing found to update, it will try and create the record.
			if ($result === FALSE || $result < 1) {
				$sql = $wpdb->prepare(
					"INSERT INTO $table_name
					( id, title, sub_title, title_author, keywords, publication_place, publisher, publication_year, projected_publication_year, link_to_dataset, isbn_with_dashes, added_to_sql, publisher_jlp_nominated, publisher_jlp_awarded, publisher_kimi_nominated, cover_url )
					VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '%s' )",
					[
						$book->idn,
						$book->title,
						$book->subTitle,
						$book->titleAuthor,
						$book->keywords,
						$book->publicationPlace,
						$book->publisher,
						$book->publicationYear,
						$book->projectedPublicationDate,
						$book->linkToDataset,
						$book->isbnWithDashes,
						$book->addedToSql,
						$book->publisherJLPNominated,
						$book->publisherJLPAwarded,
						$book->publisherKimiAwarded,
						$book->coverUrl,
					]
				);
				$wpdb->query($sql);
			}
		}
	}

}
