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

// Check if the bundled InterVarsity fonts (Avenir) exist in the theme directory
function iv_if_bundled_fonts_exist() {
	$font_dir = IV_THEME_DIR . '/styles/fonts';
	// The array of bundled fonts that must be present for this check to pass
	$required_font_names = array(
		"AvenirLTStd-Heavy",
		"AvenirLTStd-HeavyOblique",
		"AvenirLTStd-Light",
		"AvenirLTStd-LightOblique",
		"AvenirLTStd-Oblique",
		"AvenirLTStd-Roman"
	);
	// Only assume that the .otf font files are bundled
	$required_font_ext = 'otf';
	$fonts_exist = true;
	foreach ( $required_font_names as $font_name ) {
		$fonts_exist = $fonts_exist && file_exists( "$font_dir/$font_name.$required_font_ext" );
	}
	return $fonts_exist;
}

// Define a constant so multiple functions can check if the bundled fonts are
// available
define( 'IV_HAS_BUNDLED_FONTS', iv_if_bundled_fonts_exist() );

// Enqueue all frontend stylesheets
function iv_load_site_styles() {

	// Enqueue fonts chosen by user
	if ( iv_get_font_family_theme_mod() === 'Avenir' ) {
		wp_enqueue_style(
			'iv-fonts',
			IV_THEME_DIR_URI . '/styles/css/fonts.css',
			array()
		);
	} else {
		wp_enqueue_style(
			'iv-fonts',
			iv_get_frontend_font_url(),
			array()
		);
	}
	// Enqueue main frontend stylesheet
	wp_enqueue_style(
		'iv-styles',
		IV_THEME_DIR_URI . '/styles/css/site.css',
		array( 'iv-fonts' )
	);

}
add_action( 'wp_enqueue_scripts', 'iv_load_site_styles', 10 );

// Adjusts a hexadecimal color value by an absolute step value
function iv_adjust_color( $color, $steps ) {

	// Remove hash from color code to isolate components
	$color = str_replace( '#', '', $color );

	// Split color code into red, green, and blue components
	$color_comps = str_split( $color, 2 );
	$adjusted_color = '#';

	foreach ( $color_comps as $comp ) {
		// Convert hexidecimal component to decimal to perform conversion
		$comp = hexdec( $comp );
		$comp = max( 0, min( 255, $comp + $steps ) );
		// Append adjusted component to adjusted color code
		// Ensure that adjusted component is padded with zeroes if neceesary
		$adjusted_color .= str_pad( dechex( $comp ), 2, '0', STR_PAD_LEFT );
	}

	return $adjusted_color;
}

// Determines if the given string is a 6-digit hexadecimal color code with a
// leading hash (#)
function iv_is_hex_color( $str ) {
	return preg_match( '/^#[0-9a-f]{6}$/', strtolower( $str ) );
}

// Retrieves the user-chosen theme colors as an array of style variables
function iv_get_color_vars() {

	// Retrieve chosen base accent color
	$accent_mid = get_theme_mod( 'iv_color_accent', IV_DEFAULT_ACCENT_COLOR );

	// If set color is not a valid hex color, use default value
	if ( ! iv_is_hex_color( $accent_mid ) ) {
		$accent_mid = IV_DEFAULT_ACCENT_COLOR;
	}

	// Calculate dark and light shades of base accent color
	$accent_dark = iv_adjust_color( $accent_mid, -IV_COLOR_OFFSET );
	$accent_light = iv_adjust_color( $accent_mid, IV_COLOR_OFFSET );

	$background_body = '#' . get_theme_mod( 'background_color', IV_DEFAULT_BG_COLOR );

	return array(
		'color-accent-dark'        => $accent_dark,
		'color-accent-mid'         => $accent_mid,
		'color-accent-light'       => $accent_light,
		'color-background-body'    => $background_body,
		'color-background-default' => IV_DEFAULT_BG_COLOR
	);

}

// Retrieves the user-chosen font families as unmodified values; this function
// is used for both constructing the fonts URL as well as creating font family
// style variables (see below)
function iv_get_font_family_theme_mod() {

	$family = get_theme_mod( 'iv_font_primary_family', IV_DEFAULT_PRIMARY_FONT_FAMILY );

	// If font family is not set, use default
	if ( empty( $family ) ) {
		$family = IV_DEFAULT_PRIMARY_FONT_FAMILY;
	}

	return $family;

}

// Retrieves the user-chosen font weights in a simple array format
function iv_get_font_weight_theme_mods() {

	return array(
		'normal' => get_theme_mod( 'iv_font_primary_weight', IV_DEFAULT_PRIMARY_FONT_WEIGHT ),
		'bold'   => get_theme_mod( 'iv_font_primary_bold_weight', IV_DEFAULT_PRIMARY_FONT_BOLD_WEIGHT )
	);

}

// Retrieves the user-chosen font families as an array of style variables
function iv_get_font_family_vars() {

	$family = iv_get_font_family_theme_mod();
	return array(
		// Use the platform-specific sans-serif font family as a fallback for
		// the primary font family
		'font-primary-family' => "'$family', sans-serif",
	);

}

// Retrieve the user-chosen font weights as an array of style variables
function iv_get_font_weight_vars() {

	$weights = iv_get_font_weight_theme_mods();
	return array(
		'font-primary-weight-normal' => $weights['normal'],
		'font-primary-weight-bold'   => $weights['bold']
	);

}

// Constructs the font URL used to load Google fonts using the given array of font data
function iv_get_font_url( $font_data ) {

	$font_strs = array();
	foreach ( $font_data as $font ) {
		$font_str = urlencode( $font['family'] ) . ':' . implode( '%2C', $font['weights'] );
		$font_strs[] = $font_str;
	}

	return IV_BASE_FONT_URL . implode( '%7C', $font_strs );

}

// Constructs the font URL used to load Google fonts on the frontend (using all
// user-chosen font families and font weights)
function iv_get_frontend_font_url() {

	$family = iv_get_font_family_theme_mod();
	$weights = iv_get_font_weight_theme_mods();

	$font_data = array(
		array(
			'family'  => $family,
			'weights' => array(
				$weights['normal'],
				$weights['normal'] . 'italic', $weights['bold'],
				$weights['bold'] . 'italic'
			)
		)
	);

	return iv_get_font_url( $font_data );

}

// Replaces all style variable placeholders with the respective values provided
// in the given array
function iv_substitute_style_vars( $stylesheet, $style_vars ) {

	foreach ( $style_vars as $name => $value ) {
		$stylesheet = str_replace( '\'' . IV_STYLE_VAR_PREFIX . $name . '\'', $value, $stylesheet );
	}

	return $stylesheet;

}

// Replaces all defined theme style variables in the given stylesheet string
// with their respective values
function iv_substitute_all_style_vars( $stylesheet ) {

	$stylesheet = iv_substitute_style_vars( $stylesheet, iv_get_color_vars() );
	$stylesheet = iv_substitute_style_vars( $stylesheet, iv_get_font_family_vars() );
	$stylesheet = iv_substitute_style_vars( $stylesheet, iv_get_font_weight_vars() );

	return $stylesheet;

}

// Retrieves the contents for the bundled theme stylesheet at the given path
function iv_get_stylesheet_contents( $stylesheet_name ) {
	// wp_remote_get() does not work for local files when running WordPress
	// locally, and the Theme Check plugin prohibits file_get_contents(); as a
	// workaround, retrieve output of stylesheet by requiring it
	ob_start();
	require_once IV_THEME_DIR . '/styles/css/' . $stylesheet_name . '.css';
	return ob_get_clean();
}

// Enqueue customization stylesheet with user-chosen styles
function iv_add_customization_styles() {

	$stylesheet = iv_get_stylesheet_contents( 'customizations' );
	$stylesheet = iv_substitute_all_style_vars( $stylesheet );

	// Handle must be that of a stylesheet which has already been registered
	wp_add_inline_style( 'iv-styles', $stylesheet );

}
add_action( 'wp_enqueue_scripts', 'iv_add_customization_styles', 10 );

// Add styles for WP Maintenance Mode page
function iv_add_maintenance_color_styles() {

	$stylesheet = iv_get_stylesheet_contents( 'maintenance' );
	$stylesheet = iv_substitute_all_style_vars( $stylesheet );

	?>
	<style>
		<?php echo $stylesheet; ?>
	</style>
	<?php

}
add_action( 'wpmm_head', 'iv_add_maintenance_color_styles', 10 );
