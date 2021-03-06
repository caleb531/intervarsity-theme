<?php
// Functions for enqueing necessary frontend and backend stylesheets

// URL used to load website fonts
define( 'IV_FONT_URL', 'https://fonts.googleapis.com/css?family=Nunito:400,400italic,500,500italic' );

// Enqueue all frontend stylesheets
function iv_load_site_styles() {

	// Enqueue frontend fonts
	wp_enqueue_style(
		'iv-fonts',
		IV_FONT_URL,
		array()
	);
	// Enqueue main frontend stylesheet
	wp_enqueue_style(
		'iv-styles',
		IV_THEME_DIR_URI . '/styles/css/site.css',
		array( 'iv-fonts' )
	);

}
add_action( 'wp_enqueue_scripts', 'iv_load_site_styles', 10 );

// Add styles for WP Maintenance Mode page
function iv_add_maintenance_color_styles() {
	?>
	<link rel="stylesheet" href="<?php echo IV_FONT_URL; ?>" />
	<link rel="stylesheet" href="<?php echo IV_THEME_DIR_URI . '/styles/css/maintenance.css'; ?>" />
	<?php

}
add_action( 'wpmm_head', 'iv_add_maintenance_color_styles', 10 );
