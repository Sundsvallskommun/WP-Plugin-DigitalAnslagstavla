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

	private $email_headers;

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



		$this->email_headers = array(
			'From: Digitala anslagstavlan - sundsvall.se <webbgruppen@sundsvall.se>',
			'Content-Type:text/html;charset=UTF-8'
		);
	}


	public function admin_init(){
		if ( isset( $_GET['update_digitalboard'] ) && $_GET['update_digitalboard'] === 'asfdwej2' ) {
			$this->update_status();
		}
	}

	/**
	 * Adding custom mce toolbar to ACF.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $toolbars
	 *
	 * @return mixed
	 */
	 public function acf_tiny_mce_settings($toolbars){
		 $toolbars['Digital anslagstavla'] = array();
		 $toolbars['Digital anslagstavla'][1] = explode(',','formatselect, bullist, link, unlink, pastetext');
		 return $toolbars;
	 }

	/**
	 * Modifying the format mce selector.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $formats
	 *
	 * @return string
	 */
	 public function tiny_mce_custom_formats( $formats ){
		 $screen = get_current_screen();
		 if( $screen->parent_file !== 'edit.php?post_type=digitalboard' ){
		 	return $formats;
		 }

		 $formats['block_formats'] = 'Paragraph=p;Heading 3=h3;';
		 return $formats;

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
				update_post_meta( $post->ID, 'digitalreport_archived_at', date_i18n('Y-m-d H:i:s') );
				$this->send_notice( $post->ID );
			}
		}

	}

	/**
	 * Send notice/logg on email that the post is
	 * sent to archive.
	 * 
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param string $post_id
	 * @return void
	 */
	private function send_notice( $post_id = '' ){
		
		$department_id = get_field( 'digitalboard_department', $post_id );
		if( empty ( $department_id ) ){
			error_log( $this->plugin_name . ' - cannot send notice, missing department_id for post_id ' . $post_id );
			return false;
		}

		$receiver = get_field('digitalboard_organ_email', 'digitalboard-department_' . $department_id );
		if( empty ( $receiver ) ){
			error_log( $this->plugin_name . ' - cannot send notice, missing email for department_id ' . $department_id );
			return false;
		}

		$this->send_notice_on_email( $receiver, $post_id  );

	}



	/**
	 * Undocumented function
	 *
	 * @param [type] $email
	 * @param [type] $post_id
	 * @return void
	 */
	private function send_notice_on_email( $email, $post_id, $trigger = 'cron' ){

		// Build email.
		$subject = 'Arkivering: ' . Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_type', $post_id ), 'digitalboard-notice' )->name . ' : ' .  get_the_title( $post_id );
		$body    = 'Detta meddelande skickas i samband med att ett anslag har arkiverats från den digitala anslagstavlan. Följande anslag har arkiverats.<br><br>';

		$headers = implode( "\r\n", $this->email_headers );

		$items = $this->get_data( $post_id );
		if( !empty($items)){
			foreach( $items as $item ){
				$body .= $item['label'] . ': ' . $item['value'] . '<br>';
			}
		}

		$body .= '<br><br>';

		// log emails that are sent
		$current_log = get_post_meta( $post_id, 'digitalboard_email_notice_log', true ); 
		
		$hash = md5( $email . date_i18n('Y-m-d H:i') );
		$log[0]['trigger'] = $trigger;
		$log[0]['email'] = $email;
		$log[0]['time'] = date_i18n('Y-m-d H:i:s');
		$log[0]['hash'] = $hash;
	
		// check to prevent duplicate emails when
		// wordpress save_post is called twice.
		$flag = false;
		if( !empty( $current_log )){
			
			foreach( $current_log as $current ){
				if(in_array( $hash, $current)){
					//return false;
					$flag = true;
				}
			}
		}	
	
		if( !$flag){
			if(!empty($current_log)){
				$log = array_merge( $current_log, $log );
			}
			
			wp_mail( $email, $subject, $body, $headers );
			update_post_meta( $post_id, 'digitalboard_email_notice_log', $log );
		}	
	
	}


	public function get_data( $post_id ){

		$post = get_post( $post_id );
		$content = array();		

		$content[0]['label'] = __( 'Anslagets titel', 'digitalboard_textdomain' );
		$content[0]['value'] = Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_type', $post_id ), 'digitalboard-notice' )->name . ' : ' .  get_the_title( $post_id );
		
		$content[1]['label'] = __( 'Organ', 'digitalboard_textdomain' );
		$content[1]['value'] = Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_department', $post_id ), 'digitalboard-department' )->name;
		
		$content[2]['label'] = __( 'Sammanträdesdatum', 'digitalboard_textdomain' );
		$content[2]['value'] = get_field( 'digitalboard_date', $post_id );

		$content[3]['label'] = __( 'Tid', 'digitalboard_textdomain' );
		$content[3]['value'] = get_field( 'digitalboard_time', $post_id );

		$content[4]['label'] = __( 'Plats', 'digitalboard_textdomain' );
		$content[4]['value'] = get_field( 'digitalboard_place', $post_id );

		$content[5]['label'] = __( 'Paragrafer', 'digitalboard_textdomain' );
		$content[5]['value'] = get_field( 'digitalboard_paragraph', $post_id );

		$content[6]['label'] = __( 'Justeringsdatum', 'digitalboard_textdomain' );
		$content[6]['value'] = get_field( 'digitalboard_date_adjust', $post_id );

		$content[7]['label'] = __( 'Datum då anslaget sätts upp', 'digitalboard_textdomain' );
		$content[7]['value'] = get_field( 'digitalboard_date_up', $post_id);

		$content[8]['label'] = __( 'Datum då anslaget tas ned', 'digitalboard_textdomain' );
		$content[8]['value'] = get_field( 'digitalboard_date_down', $post_id );

		$content[9]['label'] = __( 'Möjlighet att överklaga beslut under perioden', 'digitalboard_textdomain' );
		$content[9]['value'] = sprintf('%s till och med %s', get_field( 'digitalboard_date_appeal_from', $post_id ),get_field( 'digitalboard_date_appeal_to', $post_id ) ) ;

		$content[10]['label'] = __( 'Förvaringsplats för protokollet', 'digitalboard_textdomain' );
		$content[10]['value'] = get_field( 'digitalboard_storage', $post_id );

		$content[11]['label'] = __( 'Ansvarig', 'digitalboard_textdomain' );
		$content[11]['value'] = get_field( 'digitalboard_responsible', $post_id );

		$content[12]['label'] = __( 'Sekreterare', 'digitalboard_textdomain' );
		$content[12]['value'] = get_field( 'digitalboard_secretary', $post_id );

		$content[13]['label'] = __( 'Kontakt', 'digitalboard_textdomain' );
		$content[13]['value'] = Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_contact', $post_id ), 'digitalboard-department', true );

		$_files = get_field('digitalboard_related_docs', $post_id);
		$file['title'] = '';
		$file['url'] = '';
		if( !empty( $_files ) ){
			foreach( $_files as $_file ){
				if( !empty( $_file['digitalboard_related_docs_link'] )){
					$file['url'] = $_file['digitalboard_related_docs_link'];
				}else{
					$file['url'] = $_file['digitalboard_related_docs_file'];
				}

				if( !empty( $_file['digitalboard_related_docs_title'] )){
					$file['title'] = $_file['digitalboard_related_docs_title'];
				}
			}
		}

		$content[14]['label'] = __( 'Relaterade dokument', 'digitalboard_textdomain' );
		$content[14]['value'] = $file['title'] . ', ' . $file['url'];

		$content[15]['label'] = __( 'Information', 'digitalboard_textdomain' );
		$content[15]['value'] = get_field( 'digitalboard_text', $post_id );



		$content[16]['label'] = __( 'Anslaget publicerat', 'digitalboard_textdomain');
		$content[16]['value'] = $post->post_date;

		$content[17]['label'] = __( 'Anslaget arkiverat', 'digitalboard_textdomain');
		$content[17]['value'] = get_post_meta($post_id, 'digitalreport_archived_at', true);

		return $content;

	}

	public function add_meta_box(){
		add_meta_box(
			'email-notice',
			__( 'Skicka anslag på e-post', 'digitalboard_textdomain' ),
			array( $this, 'email_notice_meta_box_callback' ),
			'digitalboard',
			'side'
		);
	}

	public function email_notice_meta_box_callback(){
		global $post;
		$logs = get_post_meta( $post->ID, 'digitalboard_email_notice_log', true);
		?>	
		<div class="digitalboard-email-notice">
			<label for="digitalboard-email-notice"><?php _e('E-postadress', 'digitalboard_textdomain');?></label>
			<input id="digitalboard-email-notice" type="email" name="digitalboard_email_notice">
			<p class="howto"><?php _e('Ange en e-postadress och klicka på uppdatera för att skicka anslaget till den angivna e-postadressen.', 'digitalboard_textdomain');?></p>
			
			<hr>
			<?php if( !empty( $logs )) : ?>
			<h4><?php _e( 'Historik över utskick', 'digitalboard_textdomain' ); ?></h4>
			<?php foreach( $logs as $log ) : ?>
			<p><?php echo $log['time']; ?> (<?php echo $log['trigger'] === 'cron' ? 'Automatisk' : 'Manuellt'; ?>)<br><?php echo $log['email']; ?></p>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php
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

		if( wp_is_post_revision( $post_id) || wp_is_post_autosave( $post_id ) ) {
			return false;
		}

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
			update_post_meta( $post_id, 'digitalreport_archived_at', date_i18n('Y-m-d H:i:s') );
		}else{
			update_post_meta( $post_id, 'digitalreport_active', '1' );
			delete_post_meta( $post_id, 'digitalreport_archived_at' );
		}

		// update the post.
		wp_set_post_terms( $post_id, array( $type ), 'digitalboard-notice' );
		wp_set_post_terms( $post_id, array( $organ ), 'digitalboard-department' );

		if( $_POST['digitalboard_email_notice'] ){
			error_log( 'svae');
			$email = $_POST['digitalboard_email_notice'];
			if( is_email( $email )){
				$current_user = wp_get_current_user();
				$trigger = 'ID: ' . $current_user->ID;
				$this->send_notice_on_email( $email, $post_id, $trigger );


			}else{
				error_log( $this->plugin_name . ' - cannot resend notice, email not valid: '. $email .' '. $post_id );
			}
		}	

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
