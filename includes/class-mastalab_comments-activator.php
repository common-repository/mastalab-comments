<?php

/**
 * Fired during plugin activation
 *
 * @link       https://gitlab.com/tom79
 * @since      1.0.0
 *
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/includes
 * @author     Thomas Schneider <tschneider.ac@gmail.com>
 */
class Mastalab_comments_Activator {

    private static $_db_version = '1.0.4';
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        Mastalab_comments_Activator::create_users();
	    //Create cache since version 1.0.4
		//Remove an unused field in table
		Mastalab_comments_Activator::create_cache();
	}


	private static function create_users(){
        global $wpdb;
        global $tom79_mastalab_comments_db_version;
        $tom79_mastalab_comments_db_version = Mastalab_comments_Activator::$_db_version;

        $table_name = $wpdb->prefix . 'mastalab_comments_users';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			mastodon_instance VARCHAR(255) DEFAULT '',
			user_id VARCHAR(255),
			token VARCHAR(255),
			date datetime DEFAULT '0000-00-00 00:00:00',
			UNIQUE KEY id (id)
		) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        add_option( 'tom79_mastalab_comments_db_version', $tom79_mastalab_comments_db_version );
    }


	/**
	 * Cache results
	 *
	 * Calls to instances will be saved in the db
	 * An old field is removed for the table 'mastalab_comments_users'
	 *
	 * @since    1.0.4
	 */
	private static function create_cache()
	{
		global $wpdb;
		global $tom79_mastalab_comments_db_version;
		$tom79_mastalab_comments_db_version = Mastalab_comments_Activator::$_db_version;
		$installed_ver = get_option( "tom79_mastalab_comments_db_version" );

		if ( $installed_ver <= $tom79_mastalab_comments_db_version  ) {
			$table_name = $wpdb->prefix . 'mastalab_comments_cache';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			URL VARCHAR(255) NOT NULL ,
			cache TEXT NOT NULL,
			date datetime DEFAULT '0000-00-00 00:00:00',
			UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			update_option( "tom79_mastalab_comments_db_version", $tom79_mastalab_comments_db_version );
		}
	}
}
