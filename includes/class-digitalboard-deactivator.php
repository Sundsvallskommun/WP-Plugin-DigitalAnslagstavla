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
	}

}
