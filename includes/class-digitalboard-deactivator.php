<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Digitalboard
 * @subpackage Digitalboard/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Digitalboard
 * @subpackage Digitalboard/includes
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Digitalboard_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'digitalboard_status' );
		self::remove_role();

	}

	/**
	 * Remove custom role.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public static function remove_role(){

		// remove the custom role
		remove_role( 'digitalboard_manager' );

		$caps = array(
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

		// remove custom caps from administrator
		$role = get_role( 'administrator');
		foreach ( $caps as $cap => $value ) {
			$role->remove_cap( $cap );
		}

		// remove custom caps from editor
		$role = get_role( 'editor');
		foreach ( $caps as $cap => $value ) {
			$role->remove_cap( $cap );
		}


	}

}
