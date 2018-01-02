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
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if (! wp_next_scheduled ( 'digitalboard_status' )) {
			wp_schedule_event(time(), 'hourly', 'digitalboard_status');
		}

	}

}
