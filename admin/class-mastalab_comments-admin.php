<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gitlab.com/tom79
 * @since      1.0.0
 *
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/admin
 * @author     Thomas Schneider <tschneider.ac@gmail.com>
 */
class Mastalab_comments_Admin {

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
		 * defined in Mastalab_comments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mastalab_comments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mastalab_comments-admin.css', array(), $this->version, 'all' );

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
		 * defined in Mastalab_comments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mastalab_comments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mastalab_comments-admin.js', array( 'jquery' ), $this->version, false );
	}


    public function add_plugin_admin_menu() {
        add_options_page(esc_attr('Settings', $this->plugin_name), esc_attr('Mastalab Comments', $this->plugin_name), 'manage_options', str_replace(' ', '_', $this->plugin_name), array($this, 'display_plugin_pages'));
    }


    public function display_plugin_pages() {

        global $active_tab;
        $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'settings';
        include_once( 'partials/mastalab_comments-admin-display.php' );
    }

    public function register_session() {
        if (!session_id())
            session_start();
    }


    public function options_update_site() {
        register_setting($this->plugin_name.'settings', $this->plugin_name.'settings', array($this, 'set_account'));
	    register_setting($this->plugin_name.'account', $this->plugin_name.'account', array($this, 'set_manage_account'));
	    register_setting($this->plugin_name.'preferences', $this->plugin_name.'preferences', array($this, 'set_preferences'));
    }

	function mw_enqueue_color_picker( $hook_suffix ) {
		// first check that $hook_suffix is appropriate for your admin page
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'my-script-handle', plugins_url('js/mastalab_comments-admin.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}

}
