<?php
/**
 * Displays the UI for editing Mu Social Login
 */
?>

<?php
	$active_tab = "msl-options";
	if(isset($_GET["tab"])) {
		if($_GET["tab"] == "google-options") {
			$active_tab = "google-options";
		}elseif($_GET["tab"] == "fb-options"){
			$active_tab = "fb-options";
		}
	}
?>

<div class="wrap">

	<h2>Social Login</h2>

	<!-- wordpress provides the styling for tabs. -->
	<h2 class="nav-tab-wrapper">
		<a href="?page=pan_login&tab=fb-options" class="nav-tab <?php if($active_tab == 'fb-options'){echo 'nav-tab-active';} ?>"><?php echo 'FB 登入';?></a>
		<a href="?page=pan_login&tab=google-options" class="nav-tab <?php if($active_tab == 'google-options'){echo 'nav-tab-active';} ?> "><?php echo 'Google 登入'; ?></a>
	</h2>

	<form method="post" action="options.php">
		<?php
		if( $active_tab == 'fb-options' ) {
			settings_fields( 'msl-fb-section' );
			do_settings_sections( 'msl-fb-section' );
		}elseif ( $active_tab == 'google-options' ) {
			settings_fields( 'msl-google-section' );
			do_settings_sections( 'msl-google-section' );
		}
		submit_button();
		?>
	</form>

</div><!-- .wrap -->