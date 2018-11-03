<?php
// Functions for enqueing necessary frontend and backend stylesheets

// URL used to load website fonts
define( 'IV_BASE_FONT_URL', 'https://fonts.googleapis.com/css?family=' );

// Offset used to calculate light and dark shades of any base color
define( 'IV_COLOR_OFFSET', 51 );
// Prefix for style variables used in customization stylesheet; these style
// variables are used as placeholders, and are replaced by the corresponding
// user-chosen values when the customization stylesheet is enqueuedz
define( 'IV_STYLE_VAR_PREFIX', 'iv-' );

// Check if the Avenir InterVarsity font files exist in the theme directory
function iv_if_avenir_fonts_exist() {
	$font_dir = IV_THEME_DIR . '/fonts';
	// The array of  fonts that must be present for this check to pass
	$required_font_names = array(
		"AvenirLTStd-Heavy",
		"AvenirLTStd-HeavyOblique",
		"AvenirLTStd-Light",
		"AvenirLTStd-LightOblique",
		"AvenirLTStd-Oblique",
		"AvenirLTStd-Roman"
	);
	// Only look for the .otf font files
	$required_font_ext = 'otf';
	$fonts_exist = true;
	foreach ( $required_font_names as $font_name ) {
		$fonts_exist = $fonts_exist && file_exists( "$font_dir/$font_name.$required_font_ext" );
	}
	return $fonts_exist;
}

// Define a constant so multiple functions can check if the Avenir fonts are
// available
define( 'IV_HAS_AVENIR_FONTS', iv_if_avenir_fonts_exist() );

// Enqueue all frontend stylesheets
function iv_load_site_styles() {

	// Enqueue fonts chosen by user
	wp_enqueue_style(
		'iv-fonts',
		iv_get_font_url(),
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

function iv_get_font_url() {
	// Enqueue fonts chosen by user
	if ( IV_HAS_AVENIR_FONTS ) {
		return IV_THEME_DIR_URI . '/styles/css/fonts.css';
	} else {
		return IV_BASE_FONT_URL . 'Nunito:400,400italic,500,500italic';
	}
}
