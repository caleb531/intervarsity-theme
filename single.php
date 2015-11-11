<?php
/**
 * The template for displaying individual post entries other than small groups or blog posts, such as attachments
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php the_content(); ?>

		</article>

	<?php endwhile; wp_reset_query(); ?>

<?php get_footer(); ?>
