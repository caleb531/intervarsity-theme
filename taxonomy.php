<?php
/**
 * The template for displaying all small groups in a particular taxonomy term
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php if ( have_posts() ): ?>

		<?php echo term_description(); ?>

		<?php echo iv_sg_filter_form(); ?>

		<div class="entries <?php echo get_post_type(); ?>">

			<?php while ( have_posts() ): the_post(); ?>

				<?php get_template_part( 'content', get_post_type() ); ?>

			<?php endwhile; ?>

		</div>

		<?php iv_paginate_links(); ?>

	<?php else: ?>

		<?php get_template_part( 'no-results' ); ?>

	<?php endif; wp_reset_query(); ?>

<?php get_footer() ?>
