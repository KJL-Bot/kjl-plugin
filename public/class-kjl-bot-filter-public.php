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

	public const REVIEWS = [
		'AJuM'	=> '', 
		'bv_K'	=> '', 
		'bv_J'	=> '', 
		'FAZ'	=> 'Frankfurter Allgemeine Zeitung', 
		'KBC'	=> '', 
		'JBC'	=> ''
	];

	public const UMLAUTS = [
		'ö' => 'oe'
	];

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
		// return $this->get_books_data($this->get_parameters_for_books_filter());
		wp_die();

	}

	public function kjl_bot_filter_shortcode($attributes): string
	{
		$parameters = $this->get_parameters_for_books_filter();
		shortcode_atts($parameters, $attributes, 'kjl-bot-filter');
		extract( shortcode_atts( [
			'kjl_bot_filter_id' => 'kjl_bot_filter_id',

		], $attributes ), EXTR_SKIP );
		
		return $this->get_html_for_filter();
	}

	private function get_parameters_for_books_filter(): array
	{
		return [
			'djlp_filter' 	=> isset($_GET['djlp_filter']) ? sanitize_key($_GET['djlp_filter']) : 'on',
			'kimi_filter' 	=> isset($_GET['kimi_filter']) ? sanitize_key($_GET['kimi_filter']) : 'off',
			'kjl_author' 	=> isset($_GET['kjl_author']) ? sanitize_key($_GET['kjl_author']) : '',
			'kjl_publisher' => isset($_GET['kjl_publisher']) ? sanitize_key($_GET['kjl_publisher']) : '',
			'kjl_title' 	=> isset($_GET['kjl_title']) ? sanitize_key($_GET['kjl_title']) : '',
			'kjl_location' 	=> isset($_GET['kjl_location']) ? sanitize_key($_GET['kjl_location']) : '',
			'kjl_date' 		=> isset($_GET['kjl_date']) ? sanitize_key($_GET['kjl_date']) : 'on',
			'sort_direction'=> isset($_GET['sort_direction']) ? sanitize_key($_GET['sort_direction']) : '',
			'kjl_search'    => isset($_GET['kjl_search']) ? sanitize_text_field($_GET['kjl_search']) : '',
			// 'kjl_limit'		=> isset($_GET['kjl_limit']) ? sanitize_key($_GET['kjl_limit']) : '12',
			// 'kjl_offset'	=> isset($_GET['kjl_offset']) ? sanitize_key($_GET['kjl_offset']) : '0',
		];
	}

	private function get_html_for_filter()
	{
		// echo get_search_form();
		$atts = $this->get_parameters_for_books_filter();
		$content = '<div class="books-filter-container">';
		$content .= '<h3 class="filter-title">Sortiere KJL-Veröffentlichungen nach:</h3>';
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
		// $content .= '</div>';
		// $content .= '<div class="filter-option">';
		// $content .= '<input type="text" name="kjl_search" value="'.$atts['kjl_search'].'" class="filter" placeholder="Suche">';
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
		$content .= '<span class="roundbutton kimi"></span>';
		$content .= '</label>';
		$content .= '<input id="toggleswitch_kimi_input" type="hidden" name="kimi_filter" value="'.$atts['kimi_filter'].'">';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '<div class="slider">';
		$content .= '<select id="sort_direction" name="sort_direction" class="filter">';
		$content .= '<option value="ASC" ' . ($atts['sort_direction'] === 'asc' ? 'selected' : '') . '>'.($atts['kjl_date'] === 'on' ? 'Neueste zuerst' : 'A-Z').'</option>';
		$content .= '<option value="DESC" ' . ($atts['sort_direction'] === 'desc' ? 'selected' : '') . '>'.($atts['kjl_date'] === 'on' ? 'Älteste zuerst' : 'Z-A').'</option>';
		$content .= '</select>';
		$content .= '</div>';
		$content .= '</div><!-- slider-container -->';
		// $content .= '<input type="hidden" name="kjl_offset" value="'.$atts['kjl_offset'].'" />';
		// $content .= '<input type="hidden" name="kjl_limit" value="'.$atts['kjl_limit'].'" />';
		$content .= '</form>';
		$content .= '</div><!-- books-filter-container -->';
		$args = $this->get_sorting_data();
		
		$the_query = new WP_Query( $args );

		$content .= '<div class="books" id="books">';
		$content .= $this->get_html_for_books($the_query);
		$content .= '</div><!-- books -->';

		$content .= '<div class="pagination">';
		
		$big = 999999999; // need an unlikely integer
		$content .= paginate_links( array(
            'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'total'        => $the_query->max_num_pages,
            'current'      => max( 1, get_query_var( 'page' ) ),
            'format'       => '?paged=%#%',
            'show_all'     => false,
            'type'         => 'plain',
			'start_size'   => 10,	
            'end_size'     => 2,
            'mid_size'     => 4,
            'prev_next'    => true,
            'prev_text'    => sprintf( '<i></i> %1$s', __( 'Zurück', 'kjl-bot' ) ),
            'next_text'    => sprintf( '%1$s <i></i>', __( 'Weiter', 'kjl-bot' ) ),
            'add_args'     => false,
            'add_fragment' => '',
        ) );
		$content .= '</div>';
		wp_reset_postdata();

		return $content;
	}

	private function get_sorting_data(): array
	{
		$atts = $this->get_parameters_for_books_filter();
		
		$sort_direction = 'ASC';
		$order_by = 'meta_value_num';
		$count = get_option('posts_per_page', 12);
		$paged = get_query_var('page') ? get_query_var('page') : 1;
		$offset = ($paged - 1) * $count;
		$meta_key = 'projected_publication_date';
		$order_by = 'meta_value_datetime';
		$djlp_value = "0";
		$kimi_value = '0';
		$args = [
			'orderby' => $order_by,
			'order' => $sort_direction,
			'post_type' => 'kjl-bot-book',
			'posts_per_page' => $count,
			'paged' => $paged,
			'offset' => $offset,
			'post_status' => 'publish',
			'paged' => $paged,
		];
		if(strtolower($atts['sort_direction']) === 'asc') {
			$sort_direction = 'ASC';
		}
		if(strtolower($atts['sort_direction']) === 'desc') {
			$sort_direction = 'DESC';
		}
		
		if($atts['kimi_filter'] === 'on') {
			$kimi_value = '1';
		}
		if($atts['kjl_author'] === 'on') {
			$meta_key = 'author_name';
			$order_by = 'meta_value';
		}
		if($atts['kjl_publisher'] === 'on') {
			$meta_key = 'publisher';
			$order_by = 'meta_value';
		}
		if($atts['kjl_title'] === 'on') {
			$meta_key = 'title_to_sort';
			$order_by = 'meta_value';
		}
		if($atts['kjl_location'] === 'on') {
			$meta_key = 'publication_place';
			$order_by = 'meta_value';
		}
		if($atts['kjl_date'] === 'on') {
			$meta_key = 'projected_publication_date';
			$order_by = ['meta_value' => ($sort_direction === 'ASC' ? 'DESC' : 'ASC'), 'menu_order' => ($sort_direction === 'ASC' ? 'DESC' : 'ASC')];
			$args['meta_type'] = 'NUMERIC'; // DATETIME
		}
		if($atts['djlp_filter'] === 'on') {
			$djlp_value = "1";
		}
		if($atts['kjl_search'] !== '') {
			$args['s'] = urlencode($atts['kjl_search']);
		}
		$args['order'] = $sort_direction;
		$args['meta_key'] = $meta_key;
		$args['orderby'] = $order_by;
		$args['meta_query'] = [
			'relation' => 'AND',
			[
				'key' => 'publisher_kimi_nominated',
				'value' => $kimi_value,
			],
			[
				'relation' => 'OR',
				[
					'key' => 'publisher_jlp_nominated',
					'value' => $djlp_value,
				],
				[
					'key' => 'publisher_jlp_awarded',
					'value' => $djlp_value,
				],
			],
		];
		
		// if($args['s'] !== '') {
		// 	$args['meta_query'] = [
		// 			'relation' => 'OR',
		// 			[
		// 				'key' => 'publisher_jlp_nominated',
		// 				'value' => $djlp_value,
		// 			],
		// 			[
		// 				'key' => 'publisher_jlp_awarded',
		// 				'value' => $djlp_value,
		// 			],
		// 	];
		// }

		return $args;
	}

	public function get_html_for_books($the_query) 
	{
		$content = 'Keine Bücher für diese Filterauswahl vorhanden.';
		if(isset($_GET['test']) && $_GET['test'] === 'test') {
			$url = 'https://portal.dnb.de/opac/mvb/cover?isbn=978-3-7575-5673-0&size=l';
			$file_headers = @get_headers($url);
		}
		if ( $the_query->have_posts() )  {
			$content = '';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$content .= '<div class="book">';
				$content .= '<img class="book-cover" src="'.(!empty(get_post_meta(get_the_ID(), 'cover_url')) ? get_post_meta(get_the_ID(), 'cover_url')[0] : plugin_dir_url( __FILE__ ).'images/empty_cover.jpg').'" loading="lazy" />';
				$content .= '<div class="book-info">';
				$content .= '<b>Autor(in):</b> '.(get_post_meta(get_the_ID(), 'author_name')[0] !== '' ? get_post_meta(get_the_ID(), 'author_name')[0] : '-').'<br>';
				$content .= '<b>Titel:</b> '.get_the_title().'<br>';
				$content .= '<b>Verlag:</b> '.get_post_meta(get_the_ID(), 'publisher')[0];
				$content .=  get_post_meta(get_the_ID(), 'publisher_jlp_nominated')[0] == "1" ? ' <small>(<span title="Deutscher Jugendliteraturpreis">DJLP</span>)</small>' : '';
				$content .=  get_post_meta(get_the_ID(), 'publisher_kimi_nominated')[0] == "1" ? ' <small>(<span title="Siegel für Vielfalt in Kinder- und Jugendliteratur">KIMI</span>)</small>' : '';
				$content .= '<br>';
				$content .= '<b>Erscheinungsort:</b> '.get_post_meta(get_the_ID(), 'publication_place')[0].'<br>';
				$content .= '<b>Erscheinungsdatum:</b> '.$this->get_month_name_by_number(date('n', strtotime(get_post_meta(get_the_ID(), 'projected_publication_date')[0]))).' '.date('Y', strtotime(get_post_meta(get_the_ID(), 'projected_publication_date')[0])).'<br>';
				$content .= '<b>Schlagwörter:</b> '.(get_post_meta(get_the_ID(), 'keywords')[0] !== '' ? get_post_meta(get_the_ID(), 'author_name')[0] : '-').'<br>';
				$content .= '<b>Rezension(en):</b> ';
				if(!empty(get_post_meta(get_the_ID(), 'reviews'))) {
					$reviews = unserialize(get_post_meta(get_the_ID(), 'reviews')[0]);
					$content .= '(';
					foreach($reviews as $review) {
						$content .= '<a target="_blank" href="'.$review->reviewUrl.'" title="Frankfurter Allgemeine Zeitung">FAZ</a>';
						if(next($reviews)) {
							$content .= ', ';
						}
					}
					$content .= ')';
				} else {
					$content .= '-';
				}
				$content .= '<br>';
				$content .= '<a href="'.get_post_meta(get_the_ID(), 'link_to_dataset')[0].'" target="_blank">Link zur DNB</a>';
				$content .= '</div>';
				$content .= '</div>';
			}
		}
		
		return $content;
	}

}
