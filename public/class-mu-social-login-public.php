<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Mu_Social_Login
 * @subpackage Mu_Social_Login/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mu_Social_Login
 * @subpackage Mu_Social_Login/public
 * @author     Audi Lu <khl0327@gmail.com>
 */
class Mu_Social_Login_Public {

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
	private $fb_opts;
	private $google_opts;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->fb_opts = get_option('msl_fb_settings');
		$this->google_opts = get_option('msl_google_settings');
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mu-social-login-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mu-social-login-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( 
			$this->plugin_name, 
			'msl', 
			apply_filters( 
				'msl/js_vars', 
				array(
					'ajaxurl'	=> admin_url('admin-ajax.php'),
					'site_url'	=> home_url(),
					// 'current_url' => $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
					'fb_scopes'       => 'email,public_profile',
					'fb_app_id'      => $this->fb_opts['fb_client_id'],
					// 'api_url_fv_auth' 	=> $this->fv_opts['api_url_fv_auth'],
					// 'fv_client_id'      => $this->fv_opts['fv_client_id'],
					// 'google_client_id'        => $this->google_opts['google_client_id'],
					// 'cur_url' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ,
					// 'client_id' 	=> $this->opts['client_id'],
					// 'api_url_reg' => $this->opts['api_url_reg'],
					// 'api_url_login' => $this->opts['api_url_login'],
					// 'api_url_login_normal' => $this->opts['api_url_login_normal'],
					// 'api_url_profile' => $this->opts['api_url_profile'],
					// 'api_url_token' => $this->opts['api_url_token'],
					// 'api_url_redirect' => $this->opts['api_url_redirect'],
					// 'api_url_unread' => $this->opts['api_url_unread'],
					'l18n' 		=> array(
						//'chrome_ios_alert'      => 'Please login into Pan and then click connect button again',
					)
				)
			)
		);
	}

	/**
	 * Prints fb script in login head
	 * @since   1.0.0
	 */
	public function add_fb_scripts(){
		?>
		<script>

			window.fbAsyncInit = function() {
				FB.init({
					appId      : '<?php echo trim( $this->fb_opts['fb_client_id'] );?>',
					cookie     : true,  // enable cookies to allow the server to access
					xfbml      : true,  // parse social plugins on this page
					version    : 'v3.3'
				});

			};

			// Load the SDK asynchronously
			(function(d, s, id){
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));

		</script><?php

	}

	public function add_google_meta_tags() {
		?>
		<meta name="google-signin-client_id" content="<?php echo $this->google_opts['google_client_id'];?>">
		<script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>
		<?php
	}
}
