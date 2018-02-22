<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Digitalboard
 * @subpackage Digitalboard/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Digitalboard
 * @subpackage Digitalboard/admin
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Digitalboard_Admin {

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

		if ( isset( $_GET['update_digitalboard'] ) && $_GET['update_digitalboard'] === 'asfdwej2' ) {
			$this->update_status();
		}

	}

	/**
	 * Adding settings page ACF
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function add_options_page() {
		if ( function_exists( 'acf_add_options_sub_page' ) ) {
			acf_add_options_sub_page( array(
				'title' => __( 'Inställningar', 'digitalboard-textdomain' ),
				'parent'     => 'edit.php?post_type=digitalboard',
				'capability' => 'edit_pages'
			) );
		}
	}


	/**
	 * Update status for the posts by changing a post_meta value.
	 * Usually triggered by cron.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function update_status(){

		// get the posts for digitalboard that are active
		$args = array(
			'post_type'      => 'digitalboard',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'meta_key'       => 'digitalreport_active',
			'meta_value'     => '1',
		);
		$posts = get_posts( $args );
		$now   = time();


		if ( empty( $posts ) ) {
			return false;
		}

		foreach ( $posts as $post ) {
			$end_date = get_field( 'digitalboard_date_down', $post->ID );

			// check current time vs end time and update post_meta to inactivate a post.
			if( $now > strtotime( $end_date . ' +1 day' ) ){
				update_post_meta( $post->ID, 'digitalreport_active', '0' );
			}
		}

	}


	/**
	 * Register the post type.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function register_post_type() {

		$archive_slug = ! empty( get_field( 'digitalboard_settings_archive_slug', 'options' ) ) ? get_field( 'digitalboard_settings_archive_slug', 'options' ) : 'arkiverade-anslag';
		$cpt_slug     = ! empty( get_field( 'digitalboard_settings_cpt_slug', 'options' ) ) ? get_field( 'digitalboard_settings_cpt_slug', 'options' ) : 'anslag';

		register_post_type( 'digitalboard',
			array(
				'labels'          => array(
					'name'          => __( 'Digital anslagstavla', 'digitalboard_textdomain' ),
					'singular_name' => __( 'Digital anslagstavla', 'digitalboard_textdomain' ),
					'add_new'       => __( 'Nytt anslag', 'digitalboard_textdomain' ),
					'add_new_item'  => __( 'Skapa nytt anslag', 'digitalboard_textdomain' ),
					'edit_item'     => __( 'Redigera anslag', 'digitalboard_textdomain' ),
				),
				'public'          => true,
				'show_ui'         => true,
				'menu_position'   => 30,
				'menu_icon'       => 'dashicons-list-view',
				'has_archive'     => $archive_slug,
				'hierarchical'    => false,
				'rewrite'         => array( 'slug' => $cpt_slug, 'with_front' => false ),
				'capability_type' => array('post','digitalboard', 'digitalboards'),
				'capabilities' => array(
					'edit_post'          => 'edit_digitalboard',
					'edit_posts'         => 'edit_digitalboards',
					'edit_others_posts'  => 'edit_others_digitalboards',
					'publish_posts'      => 'publish_digitalboards',
					'read_post'          => 'read_digitalboard',
					'read_private_posts' => 'read_private_digitalboards',
					'delete_post'        => 'delete_digitalboard',
					'delete_posts'       => 'delete_digitalboards'
				),

				'supports'        => array( 'title' )
			)
		);
	}


	/**
	 * Register custom taxonomies.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function register_taxonomy() {

		$labels = array(
			'name'              => _x( 'Typ av anslag', 'taxonomy general name', 'digitalboard_textdomain' ),
			'singular_name'     => _x( 'Anslagstyp', 'taxonomy singular name', 'digitalboard_textdomain' ),
			'search_items'      => __( 'Sök alla typer av anslag', 'digitalboard_textdomain' ),
			'all_items'         => __( 'Alla anslagstyper', 'digitalboard_textdomain' ),
			'edit_item'         => __( 'Ändra anslagstyp', 'digitalboard_textdomain' ),
			'update_item'       => __( 'Uppdatera', 'digitalboard_textdomain' ),
			'add_new_item'      => __( 'Lägg till en ny typ av anslag', 'digitalboard_textdomain' )
		);

		register_taxonomy(
			'digitalboard-notice',
			'digitalboard',
			array(
				'labels'        => $labels,
				'public'       => true,
				'show_ui'      => true,
				'hierarchical' => false,
				'parent_item'  => null,
				'parent_item_colon' => null,
				'capabilities' => array(
					'assign_terms' => 'assign_digitalboard-notice',
					'edit_terms'   => 'edit_digitalboard-notice',
					'manage_terms' => 'manage_digitalboard-notice',
					'delete_terms' => 'delete_digitalboard-notice',
				)
			)
		);

		$labels = array(
			'name'              => _x( 'Avdelning/organ', 'taxonomy general name', 'digitalboard_textdomain' ),
			'singular_name'     => _x( 'Avdelning/organ', 'taxonomy singular name', 'digitalboard_textdomain' ),
			'search_items'      => __( 'Sök alla avdelningar/organ', 'digitalboard_textdomain' ),
			'all_items'         => __( 'Alla avdelningar/organ', 'digitalboard_textdomain' ),
			'edit_item'         => __( 'Ändra avdelning/organ', 'digitalboard_textdomain' ),
			'update_item'       => __( 'Uppdatera', 'digitalboard_textdomain' ),
			'add_new_item'      => __( 'Lägg till ny avdelning/organ', 'digitalboard_textdomain' )
		);

		register_taxonomy(
			'digitalboard-department',
			'digitalboard',
			array(
				'labels'        => $labels,
				'public'       => true,
				'show_ui'      => true,
				'hierarchical' => false,
				'parent_item'  => null,
				'parent_item_colon' => null,
				'capabilities' => array(
					'assign_terms' => 'assign_digitalboard-department',
					'edit_terms'   => 'edit_digitalboard-department',
					'manage_terms' => 'manage_digitalboard-department',
					'delete_terms' => 'delete_digitalboard-department',
				)
			)
		);

	}


	/**
	 * Adding custom column to wp admin list.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	function custom_admin_columns( $columns ) {
		$custom_columns = array();
		foreach ( $columns as $key => $column ) {
			switch ( $key ) {
				case 'date' :
					unset( $columns['date'] );
					$custom_columns['type_of_notice'] = __( 'Typ av anslag', 'digitalboard_textdomain' );
					$custom_columns['end_date'] = __( 'Tas ned', 'digitalboard_textdomain' );

					break;
			}

			$custom_columns[ $key ] = $column;

		}

		return $custom_columns;
	}

	/**
	 * Populate a custom value to wp admin list.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $column
	 * @param $post_id
	 */
	function custom_admin_column( $column, $post_id ) {

		switch ( $column ) {

			case 'type_of_notice' :
				$type = wp_get_post_terms( $post_id, 'digitalboard-notice' );
				echo !empty( $type[0]->name ) ? $type[0]->name : null;
				break;

			case 'end_date' :
				$type = get_field( 'digitalboard_date_down', $post_id);
				echo !empty( $type ) ? $type : null;
				break;

		}
	}

	/**
	 * Remove the standard meta box panels to prevent multiple choices.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'tagsdiv-digitalboard-notice', 'digitalboard', 'side' );
		remove_meta_box( 'tagsdiv-digitalboard-department', 'digitalboard', 'side' );
	}


	/**
	 * Adding custom term to post on save_post.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function save_post( $post_id ) {

		$post_type = get_post_type( $post_id );
		if ( $post_type !== 'digitalboard' ) {
			return false;
		}

		$type = get_field('digitalboard_type', $post_id );
		$organ = get_field('digitalboard_department', $post_id );
		$end_date = get_field( 'digitalboard_date_down', $post_id );

		// prevent inifity loop by remove hook.
		remove_action( 'save_post_digitalboard', array( $this, 'save_post' ) );

		if( time() > strtotime( $end_date . ' +1 day' ) ){
			update_post_meta( $post_id, 'digitalreport_active', '0' );
		}else{
			update_post_meta( $post_id, 'digitalreport_active', '1' );
		}

		// update the post.
		wp_set_post_terms( $post_id, array( $type ), 'digitalboard-notice' );
		wp_set_post_terms( $post_id, array( $organ ), 'digitalboard-department' );
		// adding hook back.
		add_action( 'save_post_digitalboard', array( $this, 'save_post' ) );

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
		 * defined in Digitalboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Digitalboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/digitalboard-admin.css', array(), $this->version, 'all' );

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
		 * defined in Digitalboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Digitalboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/digitalboard-admin.js', array( 'jquery' ), $this->version, false );

	}

}
