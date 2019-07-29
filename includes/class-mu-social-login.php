<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Mu_Social_Login
 * @subpackage Mu_Social_Login/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mu_Social_Login
 * @subpackage Mu_Social_Login/includes
 * @author     Audi Lu <khl0327@gmail.com>
 */
class Mu_Social_Login {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mu_Social_Login_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MU_SOCIAL_LOGIN_VERSION' ) ) {
			$this->version = MU_SOCIAL_LOGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mu-social-login';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mu_Social_Login_Loader. Orchestrates the hooks of the plugin.
	 * - Mu_Social_Login_i18n. Defines internationalization functionality.
	 * - Mu_Social_Login_Admin. Defines all hooks for the admin area.
	 * - Mu_Social_Login_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mu-social-login-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mu-social-login-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mu-social-login-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mu-social-login-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mu-social-login-public.php';

		$this->loader = new Mu_Social_Login_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mu_Social_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mu_Social_Login_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mu_Social_Login_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_items');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_settings');
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mu_Social_Login_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// $this->loader->add_action( 'pan_login_button', $this->plugin_public, 'print_button' );
		// //$this->loader->add_action( 'pan_login_button', $this->plugin_public, 'add_pan_scripts' );
		// $this->loader->add_action( 'wp', $this->plugin_public, 'get_token_by_code');
		// $this->loader->add_action( 'wp_ajax_wpl_pan_login', $this->plugin_public, 'login_or_register_user' );
		// $this->loader->add_action( 'wp_ajax_nopriv_wpl_pan_login', $this->plugin_public, 'login_or_register_user' );

		$this->loader->add_action( 'pan_login_form', $this->plugin_public, 'add_fb_scripts' );
		$this->loader->add_action( 'wp_ajax_msl_facebook_login', $this->plugin_public, 'fb_login_or_register_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_msl_facebook_login', $this->plugin_public, 'fb_login_or_register_user' );
		$this->loader->add_action( 'wp_ajax_msl_google_login', $this->plugin_public, 'google_login_or_register_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_msl_google_login', $this->plugin_public, 'google_login_or_register_user' );
		$this->loader->add_action( 'wp_head', $this->plugin_public, 'add_google_meta_tags');

		// $this->loader->add_action( 'wp_ajax_wpl_normal_login', $this->plugin_public, 'normal_login' );
		// $this->loader->add_action( 'wp_ajax_nopriv_wpl_normal_login', $this->plugin_public, 'normal_login' );

		// $this->loader->add_action( 'wp_ajax_wpl_do_pwd_login', $this->plugin_public, 'do_pwd_login' );
		// $this->loader->add_action( 'wp_ajax_nopriv_wpl_do_pwd_login', $this->plugin_public, 'do_pwd_login' );

		// $this->loader->add_action( 'wp_ajax_fv_login', $this->plugin_public, 'fv_login' );
		// $this->loader->add_action( 'wp_ajax_nopriv_fv_login', $this->plugin_public, 'fv_login' );

		// $this->loader->add_action( 'wp_ajax_wpl_normal_reg', $this->plugin_public, 'normal_reg' );
		// $this->loader->add_action( 'wp_ajax_nopriv_wpl_normal_reg', $this->plugin_public, 'normal_reg' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mu_Social_Login_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
