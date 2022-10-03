<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://gabrielserwas.com
 * @since      1.0.0
 *
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Kjl_Bot_Filter
 * @subpackage Kjl_Bot_Filter/public
 * @author     Gabriel Serwas <post@gabrielserwas.com>
 */
class Kjl_Bot_Filter_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kjl-bot-filter-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kjl-bot-filter-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '_loadmore', plugin_dir_url( __FILE__ ) . 'js/loadmore.js', array( 'jquery' ), $this->version, true );

		wp_localize_script( 
			$this->plugin_name . '_loadmore', 
			'siteConfig', [
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'loadmore_kjl_nonce' ),
		 ] );


	}

	public function kjl_bot_form_handler_action()
	{
		if( isset( $_REQUEST["action"] ) && $_REQUEST["action"] == "GET" ) {
			echo "Response!!!";
		}
	}

	private function get_month_name_by_number(int $number): string 
	{
		$month_names = [
			1	=> "Januar",
			2	=> "Februar",
			3	=> "März",
			4	=> "April",
			5	=> "Mai",
			6	=> "Juni",
			7	=> "Juli",
			8	=> "August",
			9	=> "September",
			10	=> "Oktober",
			11	=> "November",
			12	=> "Dezember"
		];
		return $month_names[$number];
	}

	private function remove_special_char($str): string
	{
		$res = preg_replace('/[0-9\@\.\;\" "]+/', '', $str);
		return $res;
	}

	public function add_get_val() 
	{
		$url = site_url();
		
		$qvars = [
			'djlp' 				=> 'on',
			'djlp_filter' 		=> 'on',
			'kimi' 				=> 'on',
			'kimi_filter' 		=> 'on',
			'kjl_date'			=> 'on',
			'sort_direction' 	=> 'ASC',
		];

		$url_with_query_args = add_query_arg($qvars, $url);
		if ( shortcode_exists( 'kjl-bot-filter' )  && empty( $_GET['sort_direction'])) {
			// wp_safe_redirect($url_with_query_args);
		}
	}

	public function kjl_load_more(bool $initial_request = false)
	{
		echo $this->get_html_for_books($this->get_books_data($this->get_parameters_for_books_filter()));
		wp_die();

	}

	public function kjl_bot_filter_shortcode($attributes): string
	{
		$parameters = $this->get_parameters_for_books_filter();
		shortcode_atts($parameters, $attributes, 'kjl-bot-filter');
		extract( shortcode_atts( [
			'kjl_bot_filter_id' => 'kjl_bot_filter_id',

		], $attributes ), EXTR_SKIP );
		
		return $this->get_html_for_filter($parameters);
	}

	private function get_parameters_for_books_filter(): array
	{
		return [
			'djlp_filter' 	=> isset($_GET['djlp_filter']) ? sanitize_key($_GET['djlp_filter']) : 'on',
			'kimi_filter' 	=> isset($_GET['kimi_filter']) ? sanitize_key($_GET['kimi_filter']) : 'on',
			'kjl_author' 	=> isset($_GET['kjl_author']) ? sanitize_key($_GET['kjl_author']) : '',
			'kjl_publisher' => isset($_GET['kjl_publisher']) ? sanitize_key($_GET['kjl_publisher']) : '',
			'kjl_title' 	=> isset($_GET['kjl_title']) ? sanitize_key($_GET['kjl_title']) : '',
			'kjl_location' 	=> isset($_GET['kjl_location']) ? sanitize_key($_GET['kjl_location']) : '',
			'kjl_date' 		=> isset($_GET['kjl_date']) ? sanitize_key($_GET['kjl_date']) : 'on',
			'sort_direction'=> isset($_GET['sort_direction']) ? sanitize_key($_GET['sort_direction']) : '',
			'limit'			=> isset($_GET['limit']) ? (int) sanitize_key($_GET['limit']) : 20,
			'offset'		=> isset($_GET['offset']) ? (int) sanitize_key($_GET['offset']) : 0,

		];
	}

	private function get_html_for_filter($atts)
	{
		$content = '<div class="books-filter-container">';
		$content .= '<h3 class="filter-title">Sortiere KJL-Veröffentlichungen alphabetisch nach:</h3>';
		$content .= '<form action="/" method="GET" name="kjl-bot">';
		$content .= '<div class="books-filter">';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_author" class="filter '.$atts['kjl_author'].'">Autor*in</button>';
		$content .= '<input id="author_input" type="hidden" name="kjl_author" value="'.$atts['kjl_author'].'">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_publisher" class="filter '.$atts['kjl_publisher'].'">Verlag</button>';
		$content .= '<input id="publisher_input" type="hidden" name="kjl_publisher" value="'.$atts['kjl_publisher'].'">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_title" class="filter '.$atts['kjl_title'].'">Titel</button>';
		$content .= '<input id="title_input" type="hidden" name="kjl_title" value="'.$atts['kjl_title'].'">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_location" class="filter '.$atts['kjl_location'].'">Erscheinungsort</button>';
		$content .= '<input id="location_input" type="hidden" name="kjl_location" value="'.$atts['kjl_location'].'">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_date" class="filter '.$atts['kjl_date'].'">Erscheinungsdatum</button>';
		$content .= '<input id="date_input" type="hidden" name="kjl_date" value="'.$atts['kjl_date'].'">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<select id="sort_direction" name="sort_direction" class="filter">';
		$content .= '<option value="ASC" ' . ($atts['sort_direction'] === 'asc' ? 'selected' : '') . '>Sortierung: A-Z</option>';
		$content .= '<option value="DESC" ' . ($atts['sort_direction'] === 'desc' ? 'selected' : '') . '>Sortierung: Z-A</option>';
		$content .= '</select>';
		$content .= '</div>';
		$content .= '</div><!-- books-filter -->';
		$content .= '<div class="slider-container">';
		$content .= '<div class="slider">';
		$content .= '<div class="slider-wrap">';
		$content .= '<span>DJLP</span>';
		$content .= '<label class="toggle">';
		$content .= '<input id="toggleswitch_djlp" class="toggleswitch" type="checkbox" name="djlp" value="'.$atts['djlp_filter'].'" '.($atts['djlp_filter'] === 'on' ? 'checked' : '').'>';
		$content .= '<span class="roundbutton"></span>';
		$content .= '</label>';
		$content .= '<input id="toggleswitch_djlp_input" type="hidden" name="djlp_filter" value="'.$atts['djlp_filter'].'">';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '<div class="slider">';
		$content .= '<div class="slider-wrap">';
		$content .= '<span>KIMI</span>';
		$content .= '<label class="toggle">';
		$content .= '<input id="toggleswitch_kimi" class="toggleswitch" type="checkbox" name="kimi" value="'.$atts['kimi_filter'].'" '.($atts['kimi_filter'] === 'on' ? 'checked' : '').'>';
		$content .= '<span class="roundbutton"></span>';
		$content .= '</label>';
		$content .= '<input id="toggleswitch_kimi_input" type="hidden" name="kimi_filter" value="'.$atts['kimi_filter'].'">';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div><!-- slider-container -->';
		$content .= '</form>';
		$content .= '</div><!-- books-filter-container -->';
		$content .= '<div class="books" id="books">';
		$content .= '</div><!-- books -->';
		$content .= '<div id="spinner" class="spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';

		return $content;
	}

	private function get_books_data($atts)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "kjl_bot";
		$limit = $atts['limit'];
		$offset = $atts['offset'];
		
		$query = "SELECT * FROM " . $table_name . ' WHERE 1 = 1';
		$sort_direction = 'ASC';
		if(strtolower($atts['sort_direction']) === 'asc') {
			$sort_direction = 'ASC';
		}
		if(strtolower($atts['sort_direction']) === 'desc') {
			$sort_direction = 'DESC';
		}
		if($atts['djlp_filter'] === 'on') {
			$query .= ' AND publisher_jlp_nominated = 1 OR publisher_jlp_awarded = 1';
		}
		if($atts['kimi_filter'] === 'on') {
			$query .= ' AND publisher_kimi_nominated = 1';
		}
		if($atts['kjl_author'] === 'on') {
			$query .= ' ORDER BY title_author ' . $sort_direction;

		}
		if($atts['kjl_publisher'] === 'on') {
			$query .= ' ORDER BY publisher ' . $sort_direction;
		}
		if($atts['kjl_title'] === 'on') {
			$query .= ' ORDER BY title ' . $sort_direction;
		}
		if($atts['kjl_location'] === 'on') {
			$query .= ' ORDER BY publication_place ' . $sort_direction;
		}
		if($atts['kjl_date'] === 'on') {
			$query .= ' ORDER BY projected_publication_year ' . $sort_direction;
		}
	
		$query .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
	
		return $wpdb->get_results( $query );
	}

	public function get_html_for_books($books) 
	{
		$content = '';
		foreach($books as $book) {
			$content .= '<div class="book">';
			$cover_url = plugin_dir_url( __FILE__ ).'images/empty_cover.jpg';
			$file = $book->cover_url;
			$file_headers = @get_headers($file);
			if($file_headers[0] !== 'HTTP/1.1 404 Not Found') {
				$cover_url = $file;
			}
			$content .= '<img class="book-cover" src="'.$cover_url.'" />';
			$content .= '<div class="book-info">';
			$content .= '<b>Autor(in):</b> '.(isset($book->title_author) ? $book->title_author : '-').'<br>';
			$content .= '<b>Titel:</b> '.$book->title.'<br>';
			$content .= '<b>Verlag:</b> '.$book->publisher.'<br>';
			$content .= '<b>Erscheinungsort:</b> '.$book->publication_place.'<br>';
			$content .= '<b>Erscheinungsdatum:</b> '.$this->get_month_name_by_number(date('n', strtotime($book->projected_publication_year))).' '.date('Y', strtotime($book->projected_publication_year)).'<br>';
			$content .= '<b>Schlagwörter:</b> '.($book->keywords !== '' ? $book->keywords : '-').'<br>';
			$content .= '<a href="'.$book->link_to_dataset.'" target="_blank">Link zu DNB</a>';
			$content .= '</div>';
			$content .= '</div>';
		}
		return $content;
	}

}
