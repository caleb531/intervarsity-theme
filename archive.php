<?php
/**
 * The template for displaying blog post and small group archives
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

		<?php if ( have_posts() ): ?>

			<?php if ( 'iv_small_group' === get_post_type() ): ?>
				<?php iv_sg_filter_form(); ?>
			<?php endif; ?>

			<div class="entries <?php echo get_post_type(); ?>">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content' ); ?>

				<?php endwhile; wp_reset_query(); ?>

			</div>

			<?php iv_paginate_links(); ?>

		<?php else: ?>

			<?php get_template_part( 'no-results' ); ?>

		<?php endif; ?>

	<?php else: ?>

		<?php echo get_the_password_form(); ?>

	<?php endif; ?>

<?php get_footer(); ?>
