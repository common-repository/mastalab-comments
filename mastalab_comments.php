<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://gitlab.com/tom79
 * @since             1.0.0
 * @package           mastalab-comments
 *
 * @wordpress-plugin
 * Plugin Name:       Mastalab comments
 * Plugin URI:        https://mastalab.app
 * Description:       Display comments coming from Mastodon and Peertube related to a URL
 * Version:           1.0.10
 * Author:            Thomas Schneider
 * Author URI:        https://gitlab.com/tom79/mastodon-comments-for-wordpress
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mastalab-comments
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MASTALAB_COMMENT_VERSION', '1.0.10' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mastalab_comments-activator.php
 */
function activate_mastalab_comments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mastalab_comments-activator.php';
	Mastalab_comments_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mastalab_comments-deactivator.php
 */
function deactivate_mastalab_comments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mastalab_comments-deactivator.php';
	Mastalab_comments_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mastalab_comments' );
register_deactivation_hook( __FILE__, 'deactivate_mastalab_comments' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mastalab_comments.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mastalab_comments() {

	$plugin = new Mastalab_comments();
	$plugin->run();

}
run_mastalab_comments();
