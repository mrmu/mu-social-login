<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Mu_Social_Login
 * @subpackage Mu_Social_Login/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mu_Social_Login
 * @subpackage Mu_Social_Login/admin
 * @author     Audi Lu <khl0327@gmail.com>
 */
class Mu_Social_Login_Admin {

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
		$this->views_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views' );
	}

	public function add_menu_items() {
		add_submenu_page(
			'options-general.php',
			'Social Login',
			'Social Login',
			'manage_options',
			'msl_login',
			array( $this, 'display_settings_page' )
		);
	}

	public function display_settings_page() {
		include_once $this->views_dir . 'settings-page.php';
	}

	public function create_settings() {
		$settings = new Mu_Social_Login_Settings( $this->plugin_name, $this->version);
		$settings->register();
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
		 * defined in Mu_Social_Login_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mu_Social_Login_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/mu-social-login-admin.css', 
			array(), 
			filemtime( (dirname( __FILE__ )) . '/css/mu-social-login-admin.css' ), 
			'all' 
		);

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
		 * defined in Mu_Social_Login_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mu_Social_Login_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/mu-social-login-admin.js', 
			array( 'jquery' ), 
			filemtime( (dirname( __FILE__ )) . '/js/mu-social-login-admin.js' ), 
			false 
		);

	}

}
