<?php
/**
 * The template for displaying individual (single) blog post entries
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry-single' ); ?>>

			<?php if ( has_post_thumbnail() ): ?>
				<div class="entry-thumbnail">
					<?php the_post_thumbnail( 'iv-single-1x' ); ?>
				</div>
			<?php endif; ?>

			<div class="post-content">
				<?php the_content(); ?>
			</div>

			<?php if ( ! post_password_required() ): ?>
				<?php iv_blog_terms( '<hr />', '' ); ?>
			<?php endif; ?>

		</article>

		<?php if ( ! post_password_required() ): ?>

			<div class="pagination">
				<?php wp_link_pages( array(
					'before'      => '<hr />',
					'after'       => '',
					'link_before' => '<span>',
					'link_after'  => '</span>'
				) ); ?>
			</div>

			<?php comments_template(); ?>

		<?php endif; ?>

	<?php endwhile; wp_reset_query(); ?>

<?php get_footer(); ?>
