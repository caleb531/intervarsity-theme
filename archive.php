<?php
/**
 * The template for displaying blog posts
 *
 * This template is used for the front page if latest posts are set to show or
 * on the designated Posts page.
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php if ( ! ( is_home() && ! is_front_page() && post_password_required( get_queried_object()->ID ) ) ): ?>

		<?php if ( is_home() && ! is_front_page() ): ?>

			<?php echo apply_filters( 'the_content', get_queried_object()->post_content ); ?>

		<?php endif; ?>

		<div class="blog-posts entries">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content' ); ?>

			<?php endwhile; wp_reset_query(); ?>

		</div>

		<?php iv_paginate_links(); ?>

	<?php else: ?>

		<?php echo get_the_password_form(); ?>

	<?php endif; ?>

<?php get_footer(); ?>
