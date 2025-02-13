<?php
// Functions for enqueing necessary frontend and backend scripts

// Enqueue all frontend scripts
function iv_load_site_scripts() {

	// Enqueue main frontend script
	wp_enqueue_script(
		'iv-frontend',
		IV_THEME_DIR_URI . '/scripts/site.min.js',
		array( 'jquery' ),
		false,
		true
	);
	// The Comment Reply script conveniently moves the reply form below a
	// comment when its Reply link is clicked
	if ( is_singular( 'post' ) && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	// Make REST API details available to JavaScript client
	wp_enqueue_script( 'wp-api' );
}
add_action( 'wp_enqueue_scripts', 'iv_load_site_scripts', 10 );

// Enqueue Customizer preview script for updating live preview when designated settings (those with 'transport' => 'postMessage') change
function iv_enqueue_customize_preview_scripts() {

	wp_enqueue_script(
		'iv-customize-preview',
		IV_THEME_DIR_URI . '/scripts/customize-preview.min.js',
		array( 'jquery', 'customize-preview' )
	);

}
add_action( 'customize_preview_init', 'iv_enqueue_customize_preview_scripts', 10 );
