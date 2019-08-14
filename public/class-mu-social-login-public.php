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

		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/mu-social-login-public.css', 
			array(), 
			filemtime( (dirname( __FILE__ )) . '/css/mu-social-login-public.css' ), 
			'all' 
		);

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

		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/mu-social-login-public.js', 
			array( 'jquery' ), 
			filemtime( (dirname( __FILE__ )) . '/js/mu-social-login-public.js' ), 
			false 
		);

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
					'l18n' 		=> array(
						//'chrome_ios_alert'      => 'Please login into Pan and then click connect button again',
					)
				)
			)
		);
	}

	public function add_google_meta_tags() {
		?>
		<meta name="google-signin-client_id" content="<?php echo $this->google_opts['google_client_id'];?>">
		<script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>
		<?php
	}

	// ajax func: FB 登入按鈕
	public function fb_login_or_register_user() {

		check_ajax_referer( 'facebook-nonce', 'security' );
		$fb_access_token = isset( $_POST['fb_response']['authResponse']['accessToken'] ) ? $_POST['fb_response']['authResponse']['accessToken'] : '';
		$this->do_fb_login($fb_access_token);
	}

	// ajax func: Google 登入按鈕
	public function google_login_or_register_user() {

		check_ajax_referer( 'google-nonce', 'security' );
		$google_access_token = isset( $_POST['google_token'] ) ? sanitize_text_field($_POST['google_token']) : '';
		$google_user_id = isset( $_POST['google_user_id'] ) ? sanitize_text_field($_POST['google_user_id']) : '';
		$this->do_pan_google_login($google_access_token, $google_user_id);
	}

	private function do_fb_login($fb_access_token) {
		$ary_args = array(
			'access_token' => $fb_access_token
		);
		$status = $this->do_login ('fb', $ary_args);
		$this->ajax_response( $status );		
	}
	private function do_pan_google_login($google_access_token, $google_user_id) {
		$ary_args = array(
			'access_token' => $google_access_token
		);
		$status = $this->do_login ('google', $ary_args, $google_user_id);
		$this->ajax_response( $status );		
	}
	private function do_login($type, $args, $google_user_id = '') {
		$myuser = '';
		if ($type === 'fb') {
			check_ajax_referer( 'facebook-nonce', 'security' );
			$fb_access_token = isset( $_POST['fb_response']['authResponse']['accessToken'] ) ? $_POST['fb_response']['authResponse']['accessToken'] : '';
			// 純 FB 登入
			$myuser = $this->do_origin_fb_login($fb_access_token);
		}
		elseif ($type === 'google') {
			$gClient = new Google_Client();
			$gClient->setAuthConfigFile(dirname(__FILE__).'/js/secret.json');
			$token = $args['access_token'];
			$payload = $gClient->verifyIdToken($token);
			$user_email = '';
			if (!empty($payload)) {
				$user_email = $payload['email'];
				$myuser = array(
					'user_email' => $user_email
				);
			}else{
				$status = array( 'error' => '無法從 Google 取得會員資料授權。' );
				return $status;
			}
		}else{
			$status = array( 'error' => '抱歉，暫不支援此種登入方式。' );
			return $status;
		}
		$ary_rtn = array();
		$user_obj = $this->getUserBy( $myuser );

		// 目前登入的user or 已是會員的user
		if ( is_email($myuser['user_email']) && !empty($user_obj) ) {
			$user_id = $user_obj->ID;
			$login_mode = 'login';
			$ary_rtn = array(
				'code' => 1,
				'method' => $login_mode,
				'user_id' => $user_id,
				'status' => '登入成功。'
			);
			$user_id = $ary_rtn['user_id'];
			$method = $ary_rtn['method'];
			$status = array( 'success' => $user_id, 'method' => $method);
		}else{
			$status = array( 'error' => '無此會員資料。' );
		}

		// 進行登入並跳轉
		if( is_numeric( $user_id ) ) {
			// set the WP login cookie
			$secure_cookie = is_ssl() ? true : false;
			wp_set_auth_cookie($user_id, true, $secure_cookie);

			if (isset($_GET['state'])){
				$state_url = esc_url(urldecode($_GET['state']));
				$current_url = $state_url;
			}
			if ($do_redirect) {
				$current_url = add_query_arg( array('mt' => $login_mode), $current_url );
				wp_safe_redirect($current_url); //ensure local
				exit;
			}
		}
		return $status;
	}

	private function do_origin_fb_login($access_token) {

		// Get user from Facebook with given access token
		$fb_url = add_query_arg(
			apply_filters( 'msl/fb_js_auth_data',
				array(
					'fields'            =>  'id,first_name,last_name,email,link',
					'access_token'      =>  $access_token,
				)
			),
			'https://graph.facebook.com/v3.3/'.$_POST['fb_response']['authResponse']['userID']
		);
		//
		if( !empty( $this->opts['fb_app_secret'] ) ) {
			$appsecret_proof = hash_hmac('sha256', $access_token, trim( $this->opts['fb_app_secret'] ) );
			$fb_url = add_query_arg(
				array(
					'appsecret_proof' => $appsecret_proof
				),
				$fb_url
			);
		}

		$fb_response = wp_remote_get( esc_url_raw( $fb_url ), array( 'timeout' => 30 ) );

		if( is_wp_error( $fb_response ) )
			$this->ajax_response( array( 'error' => $fb_response->get_error_message() ) );

		$fb_user = apply_filters( 'msl/fb_auth_data', json_decode( wp_remote_retrieve_body( $fb_response ), true ) );

		if( isset( $fb_user['error'] ) )
			$this->ajax_response( array( 'error' => 'Error code: '. $fb_user['error']['code'] . ' - ' . $fb_user['error']['message'] ) );

		//check if user at least provided email
		if( empty( $fb_user['email'] ) )
			$this->ajax_response( array( 'error' => 'We need your email in order to continue. Please try loging again. ' ) );

		// Map our FB response fields to the correct user fields as found in wp_update_user
		$user = apply_filters( 'msl/fb_user_data_login', array(
			'fb_user_id' => $fb_user['id'],
			'first_name' => $fb_user['first_name'],
			'last_name'  => $fb_user['last_name'],
			'user_email' => $fb_user['email'],
			'user_url'   => $fb_user['link'],
			'user_pass'  => wp_generate_password(),
		));
		return $user;
	}

	private function getUserBy( $user, $id_type = '' ) {
		// if the user is logged in, pass curent user
		if( is_user_logged_in() ) {
			return wp_get_current_user();
		}
		$user_data = get_user_by('email', $user['user_email']);
		return $user_data;
	}

	private function ajax_response( $status ) {
		wp_send_json( $status );
		die();
	}

	// Usgae: 
	// [msl_btn type="fb" redirect_to="'.$redirect_to.'"]以 FB 登入[/msl_btn]
	// [msl_btn type="google" redirect_to="'.$redirect_to.'"]以 Google 登入[/msl_btn]
	public function msl_btn_shortcode($attr, $content) {
		extract( 
			shortcode_atts( 
				array(
					'type' => '0',
					'redirect_to' => '',
					'class' => 'btn'
				), 
				$attr 
			) 
		);
		
		$fbnonce = wp_create_nonce( 'facebook-nonce' );
		$btn = '';
		if ($type === 'fb'){ 
			$nonce = wp_create_nonce( 'facebook-nonce' );
			$btn = '<button type="button" class="btn_fb_login '.$class.'" data-fb_nonce="'. $nonce . '" data-redirect="'.$redirect_to.'">'.$content.'</button>';
		}elseif ($type === 'google') { 
			$nonce = wp_create_nonce( 'google-nonce' );
			$btn = '<button type="button" class="btn_google_login '.$class.'" data-google_nonce="'. $nonce.'" data-redirect="'.$redirect_to.'">'. $content.'</button>';
		}
		return $btn;
	}
}
