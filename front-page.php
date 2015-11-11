<?php
/**
 * The template for displaying front page content
 *
 * This template will then load home.php or page.php depending on front page display setting.
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php
	if ( is_home() ) {
		// If front page is set to show latest posts, include home template
		get_template_part( 'home' );
	} else {
		// Otherwise, front page is set to show static page
		get_template_part( 'page' );
	}
	?>

<?php get_footer(); ?>
