<?php
/**
 * The template for displaying front page content
 *
 * This template will then load home.php or page.php depending on front page display setting.
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php if ( is_home() ): ?>

		<?php get_template_part( 'home' ); ?>

	<?php else: ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php the_content(); ?>

		<?php endwhile; wp_reset_query(); ?>

		<?php
		$num_home_page_posts = get_theme_mod( 'iv_num_home_posts', IV_DEFAULT_NUM_HOME_POSTS );
		?>

		<?php if ( $num_home_page_posts > 0 ): ?>

			<?php
			query_posts( array(
				'post_type'      => 'post',
				'posts_per_page' => $num_home_page_posts,
				'orderby'        => 'date',
				'order'          => 'DESC'
			) );
			?>

			<div class="entries post">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content' ); ?>

				<?php endwhile; wp_reset_query(); ?>

			</div>

		<?php endif; ?>

	<?php endif; ?>

<?php get_footer(); ?>
