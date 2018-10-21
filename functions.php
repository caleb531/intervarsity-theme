<?php
/**
 * Initialize necessary theme functions and configuration
 *
 * @package InterVarsity
 */

// Server path and public URL to theme directory
if ( ! defined( 'IV_THEME_DIR' ) ) {
	define( 'IV_THEME_DIR', get_stylesheet_directory() );
}
if ( ! defined( 'IV_THEME_DIR_URI' ) ) {
	define( 'IV_THEME_DIR_URI', get_stylesheet_directory_uri() );
}

// Define number of content boxes to allow on homepage
define( 'IV_NUM_HOME_BOXES', 3 );

// Define maximum content width for embedded content (e.g. images and videos)
if ( ! isset( $content_width ) ) {
	$content_width = 900;
}

// See the respective included file for a description of its purpose
require_once IV_THEME_DIR . '/includes/defaults.php';
require_once IV_THEME_DIR . '/includes/general.php';
require_once IV_THEME_DIR . '/includes/header.php';
require_once IV_THEME_DIR . '/includes/sg-meta.php';
require_once IV_THEME_DIR . '/includes/blog.php';
require_once IV_THEME_DIR . '/includes/styles.php';
require_once IV_THEME_DIR . '/includes/scripts.php';

// Code to run after theme setup
function iv_after_setup_theme() {

	// Add menus in header and footer for site navigation
	register_nav_menus( array(
		'header_menu' => 'Header Menu',
		'footer_menu' => 'Footer Menu'
	) );

	// Add support for featured images
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'iv-tile-1x', 150, 150 );
	add_image_size( 'iv-tile-2x', 300, 300 );
	add_image_size( 'iv-single-1x', 200, 200 );
	add_image_size( 'iv-single-2x', 400, 400 );

	// Add support for custom header image
	add_theme_support( 'custom-header', array(
		'header-text'   => false,
		'default-image' => IV_THEME_DIR_URI . '/images/header-image-default.png'
	) );

	// Allow plugins to modify the <title> tag
	add_theme_support( 'title-tag' );
	// Add theme support for blog post RSS feed links
	add_theme_support( 'automatic-feed-links' );

	// Add styles to visual editor
	add_editor_style( iv_get_font_url( array(
		array(
			'family'  => 'Open Sans',
			'weights' => array( '400', '400italic', '600', '600italic' )
		)
	) ) );
	add_editor_style( IV_THEME_DIR_URI . '/styles/css/editor.css' );

	// Enable WordPress to recognize sg_day as query parameter for filtering
	// small groups
	add_rewrite_tag( '%sg_day%', '([\w\-]+)' );

}
add_action( 'after_setup_theme', 'iv_after_setup_theme', 99 );

// Flush rewrite rules when activating this theme
add_action( 'after_switch_theme', 'flush_rewrite_rules', 0 );
// Flush rewrite rules when deactivating this theme
add_action( 'switch_theme', 'flush_rewrite_rules', 0 );

// Display error message in admin if InterVarsity plugin is not installed
function iv_display_dependency_notice() {
	?>
	<div class="error">
		<p>This plugin requires the <a href="https://github.com/caleb531/intervarsity-plugin" target="_blank" rel="noopener">InterVarsity plugin</a> to function. Please install and activate it.</p>
	</div>
	<?php
}
if ( ! defined( 'INTERVARSITY_PLUGIN' ) ) {
	add_action( 'admin_notices', 'iv_display_dependency_notice', 10 );
}

// Modify the posts query for specific templates
function iv_modify_posts_query( $query ) {

	// Ensure query is not a secondary (nested) query
	if ( $query->is_main_query() && ! is_admin() ) {
		// If page displays small group entries on the frontend
		if ( $query->is_post_type_archive( 'iv_small_group' ) || $query->is_tax( 'sg_campus' ) || $query->is_tax( 'sg_category' ) || $query->is_search() ) {

			// Always display small groups for frontend search results
			$query->set( 'post_type', 'iv_small_group' );
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
			$query->set( 'posts_per_page', intval( get_theme_mod( 'iv_sgs_per_page', IV_DEFAULT_SGS_PER_PAGE ) ) );

			// Filter small groups by day if the sg_day parameter is given
			if ( $query->get( 'sg_day' ) ) {
				$query->set( 'meta_query', array(
					array(
						'key'     => '_sg_time',
						'value'   => $query->get( 'sg_day' ),
						'compare' => 'LIKE'
					)
				) );
			}

		}
	}

}
add_action( 'pre_get_posts', 'iv_modify_posts_query', 10 );

// Initiate theme customizer
function iv_customizer_setup( $wp_customize ) {

	require_once IV_THEME_DIR . '/includes/customizer.php';
	$iv_customize = new InterVarsity_Customize( $wp_customize );

}
add_action( 'customize_register', 'iv_customizer_setup', 10 );

// Modify password form for password protected pages
function iv_password_form() {

	ob_start();
	?>
	<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post" class="post-password-form">

		<p>To view this protected page, enter the password below:</p>

		<input type="password" name="post_password" size="20" maxlength="20" placeholder="Enter password" /> <input type="submit" name="Submit" value="Submit" />

	</form>
	<?php
	return ob_get_clean();

}
add_filter( 'the_password_form', 'iv_password_form', 10 );
