<?php

/**
 * Fired during plugin activation
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Digitalboard
 * @subpackage Digitalboard/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Digitalboard
 * @subpackage Digitalboard/includes
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Digitalboard_Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if (! wp_next_scheduled ( 'digitalboard_status' )) {
			wp_schedule_event(time(), 'hourly', 'digitalboard_status');
			self::add_role();
		}

	}

	/**
	 * Adding custom role to handle posts for digital board.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public static function add_role(){

		$caps = array(
			'read'                           => true,
			'read_digitalboard'              => true,
			'read_private_digitalboards'     => true,
			'edit_digitalboard'              => true,
			'edit_digitalboards'             => true,
			'edit_others_digitalboards'      => true,
			'edit_published_digitalboards'   => true,
			'publish_digitalboards'          => true,
			'delete_digitalboard'            => true,
			'delete_digitalboards'           => true,
			'delete_private_digitalboards'   => true,
			'delete_published_digitalboards' => true,
			'delete_others_digitalboards'    => true,
			'assign_digitalboard-notice'     => true,
			'edit_digitalboard-notice'       => true,
			'manage_digitalboard-notice'     => true,
			'delete_digitalboard-notice'     => true,
			'assign_digitalboard-department' => true,
			'edit_digitalboard-department'   => true,
			'manage_digitalboard-department' => true,
			'delete_digitalboard-department' => true,
		);

		// add role and caps.
		add_role('digitalboard_manager', 'Hanterare av digitala anslag', $caps );

		// adding cap to administrator.
		$role = get_role( 'administrator' );
		foreach ( $caps as $cap => $value ) {
			$role->add_cap( $cap );
		}

		// adding cap to editor.
		$role = get_role( 'editor' );
		foreach ( $caps as $cap => $value ) {
			$role->add_cap( $cap );
		}

	}

}