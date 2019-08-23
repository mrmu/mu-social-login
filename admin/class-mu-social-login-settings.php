<?php
class Mu_Social_Login_Settings {

	public function __construct() {
		// $this->views_dir    = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/' );
		$this->fb_fields   = array(
			'fb_client_id' 	=> 'Client ID',
			'fb_app_secret' => 'Secret Key'
		);
		$this->google_fields   = array(
			'google_client_id' 	=> 'Client ID',
			'google_app_secret' => 'Secret Key'
		);
		$this->general_fields   = array(
			'general_login_page_slug' 	=> 'Login Page Slug',
		);
	}

	/**
	 * Register sections fields and settings
	 */
	public function register() {

		if(isset($_GET["tab"]) && $_GET["tab"] == "google-options") {
			add_settings_section(
				'msl-google-main',				// ID of the settings section
				'Google登入 一般設定',  			// Title of the section
				'',
				'msl-google-section'			// ID of the page
			);

			foreach( $this->google_fields as $key => $name) {
				add_settings_field(
					$key, 							// The ID of the settings field
					$name, 							// The name of the field of setting(s)
					array( $this, 'display_'.$key ),
					'msl-google-section', 			// ID of the page on which to display these fields
					'msl-google-main' 				// The ID of the setting section
				);
			}
		}else if (isset($_GET["tab"]) && $_GET["tab"] == "fb-options") {
			add_settings_section(
				'msl-fb-main',				// ID of the settings section
				'FB登入 一般設定',  			// Title of the section
				'',
				'msl-fb-section'			// ID of the page
			);

			foreach( $this->fb_fields as $key => $name) {
				add_settings_field(
					$key, 					// The ID of the settings field
					$name, 					// The name of the field of setting(s)
					array( $this, 'display_'.$key ),
					'msl-fb-section', 			// ID of the page on which to display these fields
					'msl-fb-main' 				// The ID of the setting section
				);
			}
		}else{
			add_settings_section(
				'msl-general-main',				// ID of the settings section
				'一般設定',  			// Title of the section
				'',
				'msl-general-section'			// ID of the page
			);

			foreach( $this->general_fields as $key => $name) {
				add_settings_field(
					$key, 					// The ID of the settings field
					$name, 					// The name of the field of setting(s)
					array( $this, 'display_'.$key ),
					'msl-general-section', 			// ID of the page on which to display these fields
					'msl-general-main' 				// The ID of the setting section
				);
			}
		}

		register_setting(
			'msl-fb-section', 			// Group of options
			'msl_fb_settings', 			// Name of options
			array( $this, 'sanitize' )	// Sanitization function
		);
		register_setting(
			'msl-google-section', 			// Group of options
			'msl_google_settings', 			// Name of options
			array( $this, 'sanitize' )	// Sanitization function
		);
		register_setting(
			'msl-general-section', 			// Group of options
			'msl_general_settings', 			// Name of options
			array( $this, 'sanitize' )	// Sanitization function
		);
	}

	/**
	 * Display FB Client id field
	 */
	public function display_fb_client_id() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'msl_fb_settings' );
		$fb_client_id = isset( $opts['fb_client_id'] ) ? $opts['fb_client_id'] : '';
		// And display the view
		?>
		<input type="text" size="80" name="msl_fb_settings[fb_client_id]" value="<?php echo $fb_client_id; ?>" placeholder="FB Client ID" />
		<p class="description" ><?php echo 'Paste your FB Client ID';?></p>
		<?php
	}

	/**
	 * Display FB Secret key field
	 */
	public function display_fb_app_secret() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'msl_fb_settings' );
		$fb_app_secret = isset( $opts['fb_app_secret'] ) ? $opts['fb_app_secret'] : '';
		// And display the view
		?>
		<input type="text" size="80" name="msl_fb_settings[fb_app_secret]" value="<?php echo $fb_app_secret; ?>" placeholder="FB App Secret key" />
		<p class="description" ><?php echo 'Paste your FB App secret key';?></p>
		<?php
	}

	public function display_general_login_page_slug() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'msl_general_settings' );
		$login_page_slug = isset( $opts['login_page_slug'] ) ? $opts['login_page_slug'] : '';
		// And display the view
		?>
		<input type="text" size="80" name="msl_general_settings[login_page_slug]" value="<?php echo $login_page_slug; ?>" placeholder="Login Page Slug" />
		<p class="description" ><?php echo 'Paste the login page slug name';?></p>
		<?php
	}

	/**
	 * Display Google Client id field
	 */
	public function display_google_client_id() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'msl_google_settings' );
		$google_client_id = isset( $opts['google_client_id'] ) ? $opts['google_client_id'] : '';
		// And display the view
		?>
		<input type="text" size="80" name="msl_google_settings[google_client_id]" value="<?php echo $google_client_id; ?>" placeholder="Google Client ID" />
		<p class="description" ><?php echo 'Paste your Google Client ID';?></p>
		<?php
	}

	/**
	 * Display Google Secret key field
	 */
	public function display_google_app_secret() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'msl_google_settings' );
		$google_app_secret = isset( $opts['google_app_secret'] ) ? $opts['google_app_secret'] : '';
		// And display the view
		?>
		<input type="text" size="80" name="msl_google_settings[google_app_secret]" value="<?php echo $google_app_secret; ?>" placeholder="Google App Secret key" />
		<p class="description" ><?php echo 'Paste your Google App secret key';?></p>
		<?php
	}

	/**
	 * Simple sanitize function
	 * @param $input
	 *
	 * @return array
	 */
	public function sanitize( $input ) {

		$new_input = array();

		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {
			$new_input[ $key ] = sanitize_text_field( $val );
		}

		return $new_input;
	}
}