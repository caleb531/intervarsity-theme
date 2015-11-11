<?php
/**
 * The template for displaying 404 (Not Found) pages
 *
 * @package InterVarsity
 */

get_header(); ?>

	<h1><?php echo get_theme_mod( 'iv_404_heading', IV_DEFAULT_404_HEADING ); ?></h1>

	<?php
	echo wpautop( get_theme_mod( 'iv_404_message', IV_DEFAULT_404_MESSAGE ) );
	?>
	<?php get_search_form(); ?>

<?php get_footer(); ?>
