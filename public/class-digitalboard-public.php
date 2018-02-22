<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Digitalboard
 * @subpackage Digitalboard/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Digitalboard
 * @subpackage Digitalboard/public
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Digitalboard_Public {

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

	public static $filter = array();

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
	 * Start session if it not exists.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function session_start() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * Break into breadcrumb trail for sundsvall.se to fix custom breadcrumbs for cpt.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $bc
	 *
	 * @return mixed
	 */
	public function breadcrumbs( $bc ) {

		if ( is_post_type_archive( 'digitalboard' ) || is_singular( 'digitalboard' ) ) {
			global $post;

			$page_id  = get_field( 'digitalboard_settings_page_id', 'options' );
			$home_url = get_option( 'home' );

			$custom_bc = ''; // Breadcrumb string to return

			// start building custom breadcrumb.
			$front_page_title = get_the_title( get_option( 'page_on_front' ) );
			$custom_bc .= bc_item( $front_page_title, $home_url );

			$ancestors = get_ancestors( $page_id, 'page' );
			foreach ( array_reverse( $ancestors ) as $ancestor ) {
				$custom_bc .= bc_item( get_the_title( $ancestor ), get_the_permalink( $ancestor ) );
			}

			$custom_bc .= bc_item( get_the_title( $page_id ), get_the_permalink( $page_id ) );

			// archive or single item
			if ( is_archive() ) {
				$custom_bc .= bc_item( 'Arkiverade anslag' );
			} else {
				$custom_bc .= bc_item( get_the_title( $post->ID ) );
			}

			$bc = preg_replace( '/<ol[^>]*>.*?<\/ol>/i', '<ol class="breadcrumb">' . $custom_bc . '</ol>', $bc );

			return $bc;

		}

		return $bc;

	}

	/**
	 * Register the short code
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function add_shortcode() {
		add_shortcode( 'digital-anslagstavla', array( $this, 'output' ) );
	}

	public function output( $atts ){

		$atts = shortcode_atts( array(
			'anslagstyp'    => 'anslagbevis',
			'titel'         => false,
			'beskrivning'   => false
		), $atts );


		$posts = $this->get_filtered_posts( $atts );
		$title = ! empty( $atts['titel'] ) ? $atts['titel'] : null;
		$desc  = ! empty( $atts['beskrivning'] ) ? $atts['beskrivning'] : null;

		//start buffering
		ob_start();
		include (plugin_dir_path( __DIR__ ) . 'templates/list-digitalboard.php');
		$output = ob_get_contents();
		ob_get_clean();

		return $output;
	}


	/**
	 * Filter posts.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $filter
	 *
	 * @return array
	 */
	private function get_filtered_posts( $filter ){

		// get filtered posts
		$args = array(
			'post_type'      => 'digitalboard',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'meta_key'       => 'digitalreport_active',
			'meta_value'     => '1',
			'tax_query' => array(
				array(
					'taxonomy' => 'digitalboard-notice',
					'field' => 'name',
					'terms' => $filter['anslagstyp']
				)
			)

		);


		$posts = get_posts( $args );



		foreach ($posts as $key => $post ){

			$type  = wp_get_post_terms( $post->ID, 'digitalboard-notice' );
			$organ = wp_get_post_terms( $post->ID, 'digitalboard-department' );

			$post->meta = array(
				'type'        => !empty( $type[0]->name ) ? $type[0]->name : null,
				'department'  => !empty( $organ[0]->name ) ? $organ[0]->name : null,
				'date'        => get_field( 'digitalboard_date', $post->ID ),
				'paragraph'   => get_field( 'digitalboard_paragraph', $post->ID ),
				'date_up'     => get_field( 'digitalboard_date_up', $post->ID ),
				'date_down'   => get_field( 'digitalboard_date_down', $post->ID ),
				'storage'     => get_field( 'digitalboard_storage', $post->ID ),
				'responsible' => get_field( 'digitalboard_responsible', $post->ID ),
				'text'        => get_field( 'digitalboard_text', $post->ID ),
			);
		}

		return $posts;

	}


	/**
	 * Get custom taxonomy data
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param bool $term_id
	 * @param string $taxonomy_name
	 * @param bool $linked
	 *
	 * @return mixed
	 */
	public static function get_taxonomy_name( $term_id = false, $taxonomy_name = '', $linked = false ){

		$taxonomy = get_term( $term_id, $taxonomy_name );

		if ( !empty( $taxonomy ) ) {

			if( $linked === true ){
				$linked = sprintf( '%s <a href="mailto:%s">%2$s</a>', !empty( get_field( 'digitalboard_organ_email', $taxonomy_name . '_' . $taxonomy->term_id ) ) ? $taxonomy->name . ',' : $taxonomy->name, get_field( 'digitalboard_organ_email', $taxonomy_name . '_' . $taxonomy->term_id ) );
				return $linked;
			}

			return $taxonomy;
		}

		return false;
	}

	/**
	 * get_custom_terms
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param string $taxonomy
	 *
	 * @return array|bool|int|WP_Error
	 */
	public static function get_custom_terms( $taxonomy = '' ){
		//$taxonomy = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );

		$terms = get_terms( array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		) );

		if ( ! empty( $terms ) ) {
			return $terms;
		}



		return false;
	}


	/**
	 * Get posts in archive.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return WP_Query
	 */
	public static function get_archive_posts(){

		$meta_query[] =
			array(
				'key'     => 'digitalboard_date',
				'value'   => array( date( 'Ymd', strtotime( '-1 year' ) ), date( 'Ymd', strtotime( date( 'Ymd' ) ) ) ),
				'type'    => 'date',
				'compare' => 'BETWEEN'
			);

		$tax_query = array();

		$orderby = 'digitalboard_date';
		$order   = 'DESC';

		if ( isset( $_POST['digitalboard_filter'] ) && is_post_type_archive( 'digitalboard' ) ) {
			$meta_query = array();

			if( isset( $_SESSION['digitalboard-filter'])) {
				unset( $_SESSION['digitalboard-filter'] );
			}

			if ( isset( $_POST['digitalboard_date_from'] ) && isset( $_POST['digitalboard_date_to'] ) ) {

				$meta_query[] =
					array(
						'key'     => 'digitalboard_date',
						'value'   => array(
							date( 'Ymd', strtotime( $_POST['digitalboard_date_from'] ) ),
							date( 'Ymd', strtotime( $_POST['digitalboard_date_to'] ) )
						),
						'type'    => 'date',
						'compare' => 'BETWEEN'
					);

			}


			if ( isset( $_POST['digitalboard_type'] ) && ! empty( $_POST['digitalboard_type'] ) ) {

				$tax_query[] =
					array(
						'taxonomy'     => 'digitalboard-notice',
						'field' => 'slug',
						'terms'    => $_POST['digitalboard_type']
					);

			}

			$_SESSION['digitalboard-filter']['meta'] = $meta_query;
			$_SESSION['digitalboard-filter']['tax'] = $tax_query;

		}

		if ( empty( $_SESSION['digitalboard-filter']['meta'] ) ) {
			$_SESSION['digitalboard-filter']['meta'] = $meta_query;
		}

		if ( empty( $_SESSION['digitalboard-filter']['tax'] ) ) {
			$_SESSION['digitalboard-filter']['tax'] = $tax_query;
		}


		foreach ( $_SESSION['digitalboard-filter']['meta'] as $item ) {
			self::$filter[ $item['key'] ] = $item['value'];
		}

		foreach ( $_SESSION['digitalboard-filter']['tax'] as $item ) {
			self::$filter[ $item['taxonomy'] ] = $item['terms'];
		}


		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args  = array(
			'posts_per_page' => 10,
			'post_type'      => 'digitalboard',
			'paged'          => $paged,
			'meta_query'     => $meta_query,
			'tax_query'        => $tax_query,

		);


		$reports = new WP_Query( $args );
		return $reports;
	}


	/**
	 * Alter the query in archive.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $query
	 */
	function digitalboard_archive( $query ) {

		if ( $query->is_archive() && is_post_type_archive( 'digitalboard' ) && ! is_admin() ) {

			$post_per_page = 10;
			$query->set( 'posts_per_page', $post_per_page );
			$query->set( 'meta_query', isset( $_SESSION['digitalboard-filter']['meta'] ) ? $_SESSION['digitalboard-filter']['meta'] : null );
			$query->set( 'tax_query', isset( $_SESSION['digitalboard-filter']['tax'] ) ? $_SESSION['digitalboard-filter']['tax'] : null );

		}

	}


	/**
	 * Adding single template for post type.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $single_template
	 *
	 * @return string
	 */
	public function single_template( $single_template ) {

		// check for post type
		if ( is_singular( 'digitalboard' ) ) {
			$single_template = plugin_dir_path( __DIR__ ) . 'templates/single-digitalboard.php';
		}

		return $single_template;

	}


	/**
	 * Adding template for archive.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $archive_template
	 *
	 * @return string
	 */
	public function archive_template( $archive_template ) {
		if ( is_post_type_archive( 'digitalboard' ) ) {
			$archive_template = plugin_dir_path( __DIR__ ) . 'templates/archive-digitalboard.php';
		}

		return $archive_template;
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
		 * defined in Digitalboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Digitalboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/digitalboard-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-datepicker', plugin_dir_url( __FILE__ ) . 'css/datepicker/datepicker.css', array(), $this->version, 'all' );

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
		 * defined in Digitalboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Digitalboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/digitalboard-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-datepicker', plugin_dir_url( __FILE__ ) . 'js/datepicker/bootstrap-datepicker.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-datepicker-locale', plugin_dir_url( __FILE__ ) . 'js/datepicker/locales/bootstrap-datepicker.sv.js', array( 'jquery' ), $this->version, false );

	}

}
