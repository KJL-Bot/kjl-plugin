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

	public function kjl_bot_filter_shortcode($attributes): string
	{
		extract( shortcode_atts( [
			'kjl_bot_filter_id' => 'kjl_bot_filter_id',
		], $attributes ), EXTR_SKIP );
		$json_file =  wp_upload_dir(null, false, false)['basedir']. '/kjl-data/recentBooks.json';
		$json = file_get_contents($json_file);
		$books = json_decode($json);
		$i = 0;

		$djlp_checked = '';
		$djlp_value = '';
		$kimi_value = '';
		$kimi_checked = '';
		$author_active = '';
		$author_value = '';
		$publisher_active = '';
		$title_active = '';
		$location_active = '';
		if(isset($_GET['djlp_filter']) && $_GET['djlp_filter'] === 'on') {
			$djlp_value = 'on';
			$djlp_checked = 'checked';
		}
		if(isset($_GET['kimi_filter']) && $_GET['kimi_filter'] === 'on') {
			$kimi_value = 'on';
			$kimi_checked = 'checked';
		}
		if(isset($_GET['author']) && $_GET['author'] === 'on') {
			$author_value = 'on';
			$author_active = 'active';
			usort($books,function($a,$b) { return strnatcasecmp($a->titleAuthor,$b->titleAuthor);});

		}
		if(isset($_GET['publisher']) && $_GET['publisher'] === 'on') {
			$publisher_active = 'active';
			usort($books,function($a,$b) {return strnatcasecmp($a->publisher,$b->publisher);});

		}
		if(isset($_GET['title']) && $_GET['title'] === 'on') {
			$title_active = 'active';
			usort($books,function($a,$b) {return strnatcasecmp($a->title,$b->title);});

		}
		if(isset($_GET['location']) && $_GET['location'] === 'on') {
			$location_active = 'active';
			usort($books,function($a,$b) {return strnatcasecmp($a->publicationPlace,$b->publicationPlace);});

		}
		$content = '<div class="books">';
		$content = '<div class="books-filter-container">';
		$content .= '<h3 class="filter-title">Sortiere KJL-Veröffentlichungen alphabetisch nach:</h3>';
		$content .= '<form action="" method="GET">';
		$content .= '<div class="books-filter">';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_author" class="filter '.$author_active.'">Autor*in</button>';
		$content .= '<input id="author_input" type="hidden" name="author" value="'.$author_value.'">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_publisher" class="filter '.$publisher_active.'">Verlag</button>';
		$content .= '<input id="publisher_input" type="hidden" name="publisher" value="">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_title" class="filter '.$title_active.'">Titel</button>';
		$content .= '<input id="title_input" type="hidden" name="title" value="">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_location" class="filter '.$location_active.'">Erscheinungsort</button>';
		$content .= '<input id="location_input" type="hidden" name="location" value="">';
		$content .= '</div>';
		$content .= '<div class="filter-option">';
		$content .= '<button id="filter_date" class="filter '.$location_active.'">Erscheinungsdatum</button>';
		$content .= '<input id="date_input" type="hidden" name="date" value="">';
		$content .= '</div>';
		$content .= '</div><!-- books-filter -->';
		$content .= '<div class="slider-container">';
		$content .= '<div class="slider">';
		$content .= '<span>DJLP</span>';
		$content .= '<label class="toggle">';
		$content .= '<input id="toggleswitch_djlp" class="toggleswitch" type="checkbox" name="djlp" value="'.$djlp_value.'" '.$djlp_checked.'>';
		$content .= '<span class="roundbutton"></span>';
		$content .= '</label>';
		$content .= '<input id="toggleswitch_djlp_input" type="hidden" name="djlp_filter" value="'.$djlp_value.'">';
		$content .= '</div>';
		$content .= '<div class="slider">';
		$content .= '<span>KIMI</span>';
		$content .= '<label class="toggle">';
		$content .= '<input id="toggleswitch_kimi" class="toggleswitch" type="checkbox" name="kimi" '.$kimi_checked.'>';
		$content .= '<span class="roundbutton"></span>';
		$content .= '</label>';
		$content .= '<input id="toggleswitch_kimi_input" type="hidden" name="kimi_filter" value="'.$kimi_value.'">';
		$content .= '</div>';
		$content .= '</div><!-- slider-container -->';
		$content .= '</form>';
		$content .= '</div><!-- books-filter-container -->';
		foreach($books as $book) {
			if($djlp_value === 'on' && ($book->publisherJLPAwarded !== 1 || $book->publisherJLPNominated !== 1)) {
				continue;
			}
			if($kimi_value === 'on' && $book->publisherKimiAwarded !== 1) {
				continue;
			}
			$content .= '<div class="book">';
			$cover_url = plugin_dir_url( __FILE__ ).'images/empty_cover.jpg';
			$file = $book->coverUrl;
			$file_headers = @get_headers($file);
			// var_dump($file_headers);
			if($file_headers[0] !== 'HTTP/1.1 404 Not Found') {
				$cover_url = $file;
			}
			$content .= '<img class="book-cover" src="'.$cover_url.'" />';
			$content .= '<div class="book-info">';
			$content .= '<b>Autor(in):</b> '.(isset($book->titleAuthor) ? $book->titleAuthor : '-').'<br>';
			$content .= '<b>Titel:</b> '.$book->title.'<br>';
			$content .= '<b>Verlag:</b> '.$book->publisher.'<br>';
			$content .= '<b>Erscheinungsort:</b> '.$book->publicationPlace.'<br>';
			$content .= '<b>Erscheinungsdatum:</b> '.date('M Y', strtotime($book->projectedPublicationDate)).'<br>';
			$content .= '<b>Schlagwörter:</b> '.($book->keywords !== '' ? $book->keywords : '-').'<br>';
			$content .= '<a href="linkToDataset">Link zu DNB</a>';
			$content .= '</div>';
			$content .= '</div>';
			if($i === 20) {
				break;
			}
			++$i;
		}
		$content .= '</div><!-- books -->';
		
		return $content;
	}

}
