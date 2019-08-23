<?php
/**
 * Displays the UI for editing Mu Social Login
 */
?>

<?php
	$active_tab = "general-options";
	if(isset($_GET["tab"])) {
		if ($_GET["tab"] == "google-options") {
			$active_tab = "google-options";
		}elseif ($_GET["tab"] == "fb-options"){
			$active_tab = "fb-options";
		}else{
			$active_tab = "general-options";
		}
	}
?>

<div class="wrap">

	<h2>Social Login</h2>

	<!-- wordpress provides the styling for tabs. -->
	<h2 class="nav-tab-wrapper">
		<a href="?page=msl_login&tab=general-options" class="nav-tab <?php if($active_tab == 'general-options'){echo 'nav-tab-active';} ?> "><?php echo '一般設定'; ?></a>
		<a href="?page=msl_login&tab=fb-options" class="nav-tab <?php if($active_tab == 'fb-options'){echo 'nav-tab-active';} ?>"><?php echo 'FB 登入';?></a>
		<a href="?page=msl_login&tab=google-options" class="nav-tab <?php if($active_tab == 'google-options'){echo 'nav-tab-active';} ?> "><?php echo 'Google 登入'; ?></a>
	</h2>

	<form method="post" action="options.php">
		<?php
		if( $active_tab == 'fb-options' ) {
			settings_fields( 'msl-fb-section' );
			do_settings_sections( 'msl-fb-section' );
		}elseif ( $active_tab == 'google-options' ) {
			settings_fields( 'msl-google-section' );
			do_settings_sections( 'msl-google-section' );
		}else{
			settings_fields( 'msl-general-section' );
			do_settings_sections( 'msl-general-section' );
		}
		submit_button();
		?>
	</form>

</div><!-- .wrap -->