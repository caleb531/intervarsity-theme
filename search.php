<?php
/**
 * The template for displaying small group search pages.
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php if ( have_posts() && ! empty( $wp_query->query_vars['s'] ) ): ?>

		<?php
		// Output number of search results
		$n = $wp_query->found_posts;
		$pluralized = _n( '1 small group', number_format_i18n( $n ) . ' small groups', $n, 'intervarsity' );
		?>
		<p>Found <?php echo "$pluralized"; ?> matching <span class="search-query"><?php echo get_search_query(); ?></span>:</p>

		<div class="iv_small_group entries">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content' ); ?>

			<?php endwhile; wp_reset_query(); ?>

		</div>

		<?php iv_paginate_links(); ?>

	<?php else : ?>

		<?php get_template_part( 'no-results' ); ?>

	<?php endif; ?>

<?php get_footer(); ?>
