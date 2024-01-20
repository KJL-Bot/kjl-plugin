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

	private $wpdb;

	private $wp_query;

	private $imageTypes = [
		1	=> '.gif',
		2	=> '.jpg',
		3	=> '.png',
		4	=> IMAGETYPE_SWF,
		5	=> IMAGETYPE_PSD,
		6	=> IMAGETYPE_BMP,
		7	=> IMAGETYPE_TIFF_II,
		8	=> IMAGETYPE_TIFF_MM,
		9	=> IMAGETYPE_JPC,
		10	=> IMAGETYPE_JP2,
		11	=> IMAGETYPE_JPX,
		12	=> IMAGETYPE_JB2,
		13	=> IMAGETYPE_SWC,
		14	=> IMAGETYPE_IFF,
		15	=> IMAGETYPE_WBMP,
		16	=> IMAGETYPE_XBM,
		17	=> IMAGETYPE_ICO,
		18	=> '.webp',
	];

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

		global $wpdb;
		global $wp_query;

		$this->wpdb = $wpdb;
		$this->wp_query = $wp_query;

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

	/**
	 * Function to create custom post type.
	 */
	public function create_posttype()
	{
		register_post_type( 'kjl-bot-book',
			[
				'labels' => [
					'name' 			=> __( 'KJL Bot Books', 'kjl-bot-books' ),
					'singular_name' => __( 'KJL Bot Book', 'kjl-bot-book' )
				],
				'public' 			=> true,
				'has_archive' 		=> true,
				'menu_position'     => 5,
				'menu_icon'			=> 'dashicons-book',
				'delete_with_user' 	=> false,
				'rewrite' 			=> ['slug' => 'kjl-bot-book'],
				'supports' 			=> ['title', 'editor', 'revisions', 'custom-fields', 'thumbnail'],
				'taxonomies' 		=> [
					__('Autor', 'kjl-bot-book'),
					__('Verlag', 'kjl-bot-book'),
					__('Erscheinungsort', 'kjl-bot-book'),
					__('Erscheinungsdatum', 'kjl-bot-book'),
					__('Schlagwörter', 'kjl-bot-book'),
					__('Link zur DNB', 'kjl-bot-book'),
				]
			]
		);
	}

	public function register_taxonomies()
	{
		register_taxonomy(
			'kjl-bot-book-categorie',
			'kjl-bot-book',
			[
				'labels' => [
					'name' => __('KJL Bot Book Categories', 'kjl-bot-book'),
					'add_new_item' => __('Add New KJL Bot Category', 'kjl-bot-book'),
					'new_item_name' => __('New KJL Bot Book Category', 'kjl-bot-book'),
				],
				'show_ui' => false,
				'show_tagcloud' => false,
				'hierarchical' => true,
			]
		);
	}
	
	private function remove_special_char($str): string
	{
		$res = preg_replace('/[^0-9]/', '', $str);
		return $res;
	}

	private function save_file_and_return_filepath($post_id, $url, $filename)
	{
		include_once( ABSPATH . '/wp-admin/includes/image.php' );
		$cover_url = plugin_dir_url( __FILE__ ).'images/empty_cover.jpg';
		$file_headers = @get_headers($url);
		
		if($file_headers[0] !== 'HTTP/1.1 404 Not Found') {
		
			$uploaddir = wp_upload_dir();
			$uploadfile = $uploaddir['path'] . '/' . $filename;

			$contents= file_get_contents($url);
			$savefile = fopen($uploadfile, 'w');
			fwrite($savefile, $contents);
			fclose($savefile);

			$wp_filetype = wp_check_filetype(basename($filename), null );

			$attachment = [
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => $filename,
				'post_content' => '',
				'post_status' => 'inherit'
			];

			$attach_id = wp_insert_attachment( $attachment, $uploadfile, $post_id );

			$imagenew = get_post( $attach_id );
			$fullsizepath = get_attached_file( $imagenew->ID );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			$cover_url = wp_get_attachment_url($imagenew->ID);
			set_post_thumbnail( $post_id, $attach_id );
		}
		return $cover_url;
	}

	/**
	 * @param $post_meta_data
	 * @param $post
	 * @param $post_id
	 */
	public function save_post_meta($post_meta_data, $post, $post_id)
	{
		foreach ($post_meta_data as $key => $value) {
			if ('revision' === $post->post_type) {
				wp_die();
			}

			if (get_post_meta($post_id, $key, false)) {
				update_post_meta($post_id, $key, $value);
			} else {
				add_post_meta($post_id, $key, $value);
			}

			if (!$value) {
				delete_post_meta($post_id, $key);
			}
		}
	}

	public function kjl_cron_exec() {
		global $wpdb;
		$json_file = wp_upload_dir(null, false, false)['basedir']. '/kjl-data/recentBooks.json';
		$json = file_get_contents($json_file);
		$books = json_decode($json);
		

		foreach($books as $book) {
			$the_post = $wpdb->get_row( "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_value = '" . $book->idn . "' AND meta_key = 'idn'" );
			$post_status = 'publish';
			$post_id = 0;
			if(
				get_post_meta($the_post->post_id, 'projected_publication_date', true) <= date("Y-m",strtotime("-2 month",strtotime(date("Y-m",strtotime("now") ) )))
		 		|| get_post_meta($the_post->post_id, 'projected_publication_date', true) > date("Y-m",strtotime("+1 month",strtotime(date("Y-m",strtotime("now") ) )))
				) {
				$post_status = 'private';
				$post_id = $the_post->post_id;
			}
			$post_array = [
				'post_title' 	=> trim(trim($book->title), '\'".;,@#$%^&*()-_=+[]{}\\|?<>«»:~'),
				'post_status'   => $post_status,
				'post_type'     => 'kjl-bot-book',
				'post_author'   => 1,
			];
			if($the_post !== NULL) {
				$post_array['ID'] = $the_post->post_id;
				$post_id = $the_post->post_id;
				wp_update_post($post_array);
				update_post_meta($the_post->post_id, 'publisher_jlp_nominated', $book->publisherJLPNominated);
				update_post_meta($the_post->post_id, 'publisher_jlp_awarded', $book->publisherJLPAwarded);
				update_post_meta($the_post->post_id, 'publisher_kimi_nominated', $book->publisherKimiAwarded);
				update_post_meta($the_post->post_id, 'cover_url', str_replace('size=l', 'size=m', $book->coverUrl));
				if(isset($book->reviews)) {
					update_post_meta($the_post->post_id, 'reviews', serialize($book->reviews));
					// foreach($book->reviews as $review) {
					// 	update_post_meta($the_post->post_id, $review->reviewSite, $review->reviewUrl);
					// }
				}
				continue;
			} else {
				$post_id = wp_insert_post($post_array);
				add_post_meta($post_id, 'idn', $book->idn);
				add_post_meta($post_id, 'title_author', trim(trim($book->titleAuthor),' \'".;,@#$%^&*()-_=+[]{}\\|?<>«»:~'));
				add_post_meta($post_id, 'author_name', $book->sortingAuthor);
				add_post_meta($post_id, 'keywords', $book->keywords);
				add_post_meta($post_id, 'publication_place', $book->publicationPlace);
				add_post_meta($post_id, 'publisher', trim($book->publisher,'\'".;,@#$%^&*()-_=+[]{}\\|?<>«»:~'));
				add_post_meta($post_id, 'publication_year',  $this->remove_special_char($book->publicationYear));
				add_post_meta($post_id, 'projected_publication_date', $book->projectedPublicationDate);
				add_post_meta($post_id, 'link_to_dataset', $book->linkToDataset);
				add_post_meta($post_id, 'isbn_with_dashes', $book->isbnWithDashes);
				add_post_meta($post_id, 'added_to_sql', $book->addedToSql);
				add_post_meta($post_id, 'publisher_jlp_nominated', $book->publisherJLPNominated);
				add_post_meta($post_id, 'publisher_jlp_awarded', $book->publisherJLPAwarded);
				add_post_meta($post_id, 'publisher_kimi_nominated', $book->publisherKimiAwarded);
				add_post_meta($post_id, 'title_to_sort', $book->sortingTitle);
				add_post_meta($post_id, 'cover_url', $book->coverUrl);
				if(isset($book->reviews)) {
					add_post_meta($post_id, 'reviews', $book->reviews);
				
				}
			}
			// $this->save_file_and_return_filepath($post_id, $book->coverUrl, $book->idn . '.jpg');			
		}

		$this->kjl_cleanup();
	}

	public function kjl_cleanup()
	{
		$posts = get_posts([
			'post_type' => 'kjl-bot-book',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		  ]);

		$privatePosts = get_posts([
			'post_type' => 'kjl-bot-book',
			'post_status' => 'private',
			'posts_per_page' => -1,
			]);

		foreach($posts as $post) {
			$postTitles = '';
			$post_status = 'publish';
			
			if(
				get_post_meta($post->ID, 'projected_publication_date', true) <= date("Y-m", strtotime("-2 month", strtotime(date("Y-m", strtotime("now") ) )))
		 		|| get_post_meta($post->ID, 'projected_publication_date', true) > date("Y-m", strtotime("+1 month", strtotime(date("Y-m", strtotime("now") ) )))
				) {
					$postTitles .= $post->post_title . '<br>';
					$post_status = 'private';
			}
			$post_array = [
				'ID'			=> $post->ID,
				'post_status'   => $post_status,
				'post_type'     => 'kjl-bot-book',
				'post_author'   => 1,
			];
			wp_update_post($post_array);
		}

		foreach($privatePosts as $private_post) {
			$postTitles = '';
			$private_post_status = 'publish';
			if(get_post_meta($private_post->ID, 'projected_publication_date', true) <= date("Y-m", strtotime("-2 month", strtotime(date("Y-m", strtotime("now") ) )))) {
				if( has_post_thumbnail( $private_post->ID ) ) {
					$attachment_id = get_post_thumbnail_id($private_post->ID);
					wp_delete_attachment($attachment_id, true);
				}
				wp_delete_post($private_post->ID, true);
			}
			if(get_post_meta($private_post->ID, 'projected_publication_date', true) > date("Y-m", strtotime("+1 month", strtotime(date("Y-m", strtotime("now") ) )))
				) {
					$postTitles .= $private_post->post_title . '<br>';
					$private_post_status = 'private';
			}
			$private_post_array = [
				'ID'			=> $private_post->ID,
				'post_status'   => $private_post_status,
				'post_type'     => 'kjl-bot-book',
				'post_author'   => 1,
			];
			wp_update_post($private_post_array);
		}

		wp_mail('post@gabrielserwas.com', 'kjl-bot', 'Update for posts ('.count($posts).'): <br>' . $postTitles . 'done.');
	}

}
