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
