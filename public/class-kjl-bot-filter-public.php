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
		$qvars = [
			'djlp' 			=> 'on',
			'djlp_filter' 	=> 'on',
			'kimi' 			=> 'on',
			'kimi_filter' 	=> 'on',
			'kjl_date'		=> 'on'
		];
		return $qvars;
	}

	public function kjl_bot_filter_shortcode($attributes): string
	{
		global $wpdb;
		$atts = shortcode_atts([
			'djlp_filter' 	=> isset($_GET['djlp_filter']) ? sanitize_key($_GET['djlp_filter']) : '',
			'kimi_filter' 	=> isset($_GET['kimi_filter']) ? sanitize_key($_GET['kimi_filter']) : '',
			'kjl_author' 	=> isset($_GET['kjl_author']) ? sanitize_key($_GET['kjl_author']) : '',
			'kjl_publisher' => isset($_GET['kjl_publisher']) ? sanitize_key($_GET['kjl_publisher']) : '',
			'kjl_title' 	=> isset($_GET['kjl_title']) ? sanitize_key($_GET['kjl_title']) : '',
			'kjl_location' 	=> isset($_GET['kjl_location']) ? sanitize_key($_GET['kjl_location']) : '',
			'kjl_date' 		=> isset($_GET['kjl_date']) ? sanitize_key($_GET['kjl_date']) : '',

		], $attributes, 'kjl-bot-filter');
		extract( shortcode_atts( [
			'kjl_bot_filter_id' => 'kjl_bot_filter_id',

		], $attributes ), EXTR_SKIP );

		$table_name = $wpdb->prefix . "kjl_bot";
		
		$query = "SELECT * FROM " . $table_name . ' WHERE 1 = 1';
		$djlp_checked = '';
		$djlp_value = '';
		$kimi_value = '';
		$kimi_checked = '';
		$author_active = '';
		$author_value = '';
		$publisher_active = '';
		$title_active = '';
		$location_active = '';
		$date_active = '';
		if($atts['djlp_filter'] === 'on') {
			$djlp_value = 'on';
			$djlp_checked = 'checked';
			$query .= ' AND publisher_jlp_nominated = 1 OR publisher_jlp_awarded = 1';
		}
		if($atts['kimi_filter'] === 'on') {
			$kimi_value = 'on';
			$kimi_checked = 'checked';
			$query .= ' AND publisher_kimi_nominated = 1';
		}
		if($atts['kjl_author'] === 'on') {
			$author_value = 'on';
			$author_active = 'active';
			$query .= ' ORDER BY title_author';

		}
		if($atts['kjl_publisher'] === 'on') {
			$publisher_active = 'active';
			$query .= ' ORDER BY publisher';
		}
		if($atts['kjl_title'] === 'on') {
			$title_active = 'active';
			$query .= ' ORDER BY title';
		}
		if($atts['kjl_location'] === 'on') {
			$location_active = 'active';
			$query .= ' ORDER BY publication_place';
		}
		if($atts['kjl_date'] === 'on') {
			$date_active = 'active';
			$query .= ' ORDER BY projected_publication_year ASC';
		}
		$query .= ' LIMIT 20 ';

		$books = $wpdb->get_results( $query );
		$content = '<div class="books">';
		$content = '<div class="books-filter-container">';
		$content .= '<h3 class="filter-title">Sortiere KJL-Veröffentlichungen alphabetisch nach:</h3>';
		$content .= '<form action="/" method="GET" name="kjl-bot">';
		$content .= '<div class="books-filter">';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_author" class="filter '.$author_active.'">Autor*in</button>';
		$content .= '<input id="author_input" type="hidden" name="kjl_author" value="'.$author_value.'">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_publisher" class="filter '.$publisher_active.'">Verlag</button>';
		$content .= '<input id="publisher_input" type="hidden" name="kjl_publisher" value="">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_title" class="filter '.$title_active.'">Titel</button>';
		$content .= '<input id="title_input" type="hidden" name="kjl_title" value="">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_location" class="filter '.$location_active.'">Erscheinungsort</button>';
		$content .= '<input id="location_input" type="hidden" name="kjl_location" value="">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_date" class="filter '.$date_active.'">Erscheinungsdatum</button>';
		$content .= '<input id="date_input" type="hidden" name="kjl_date" value="">';
		$content .= '</div>';
		$content .= '</div><!-- books-filter -->';
		$content .= '<div class="slider-container">';
		$content .= '<div class="slider">';
		$content .= '<div class="slider-wrap">';
		$content .= '<span>DJLP</span>';
		$content .= '<label class="toggle">';
		$content .= '<input id="toggleswitch_djlp" class="toggleswitch" type="checkbox" name="djlp" value="'.$djlp_value.'" '.$djlp_checked.'>';
		$content .= '<span class="roundbutton"></span>';
		$content .= '</label>';
		$content .= '<input id="toggleswitch_djlp_input" type="hidden" name="djlp_filter" value="'.$djlp_value.'">';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '<div class="slider">';
		$content .= '<div class="slider-wrap">';
		$content .= '<span>KIMI</span>';
		$content .= '<label class="toggle">';
		$content .= '<input id="toggleswitch_kimi" class="toggleswitch" type="checkbox" name="kimi" '.$kimi_checked.'>';
		$content .= '<span class="roundbutton"></span>';
		$content .= '</label>';
		$content .= '<input id="toggleswitch_kimi_input" type="hidden" name="kimi_filter" value="'.$kimi_value.'">';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div><!-- slider-container -->';
		$content .= '</form>';
		$content .= '</div><!-- books-filter-container -->';
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
		$content .= '</div><!-- books -->';
		
		return $content;
	}

}
