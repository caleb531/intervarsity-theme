<?php
/**
 * The template for displaying blog posts by a specific author
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php $author_name = get_the_author(); ?>
	<?php if ( ! empty( $author_name ) && 0 !== get_the_author_posts() ): ?>

		<p>These are all of the blog posts by the user <strong><?php echo $author_name; ?></strong>.</p>

	<?php else: ?>

		<p>There are no posts for the user <strong><?php echo $author_name; ?></strong>.</p>

	<?php endif; ?>

	<div class="entries post">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', get_post_type() ); ?>

		<?php endwhile; wp_reset_query(); ?>

	</div>

	<?php iv_paginate_links(); ?>

<?php get_footer(); ?>
