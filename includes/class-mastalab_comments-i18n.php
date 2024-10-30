<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://gitlab.com/tom79
 * @since      1.0.0
 *
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/includes
 * @author     Thomas Schneider <tschneider.ac@gmail.com>
 */
class Mastalab_comments_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mastalab_comments',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
