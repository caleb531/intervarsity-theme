<?php
/**
 * The template for displaying blog posts
 *
 * This template is used for the front page if latest posts are set to show or
 * on the designated Posts page. It is also used for author pages.
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php if ( ! ( is_home() && ! is_front_page() && post_password_required( get_queried_object()->ID ) ) ): ?>

		<?php if ( is_author() ): ?>

			<?php $author_name = get_the_author(); ?>
			<?php if ( ! empty( $author_name ) && 0 !== get_the_author_posts() ): ?>

				<p>These are all of the blog posts by the user <strong><?php echo $author_name; ?></strong>.</p>

			<?php else: ?>

				<p>There are no posts for the user <strong><?php echo $author_name; ?></strong>.</p>

			<?php endif; ?>

		<?php else: ?>

			<?php if ( ! empty( $obj ) ): ?>

				<?php echo wpautop( $obj->post_content ); ?>

			<?php endif; ?>

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
