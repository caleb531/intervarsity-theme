<?php
/**
 * The template for displaying individual (single) small group entries
 *
 * @package InterVarsity
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry-single' ); ?>>

			<div class="entry-main entry-row">

				<?php if ( has_post_thumbnail() ): ?>
					<div class="entry-thumbnail entry-cell">
						<?php the_post_thumbnail( 'single-1x' ); ?>
					</div>
				<?php endif; ?>

				<div class="entry-cell">

					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<?php the_sg_details( '<h3>Details</h3><p class="sg-details">', '</p>' ); ?>

					<?php the_sg_contact( '<h3>Contact</h3><p class="sg-contact">For questions, contact ', '.</p>' ); ?>

				</div>

			</div>

		</article>

		<?php
		global $post;
		require_once IV_THEME_DIR . '/includes/related-sgs.php';
		$iv_related_sgs = iv_get_related_sgs( $post );
		if ( get_theme_mod( 'iv_related_sgs_enabled', IV_DEFAULT_RELATED_SGS_ENABLED ) && 0 !== count( $iv_related_sgs ) ):
		?>

			<hr />

			<section class="sg-related">

				<h3>Related Small Groups</h3>

				<p>Click the small group name for more details.</p>

				<table>
					<thead>
						<tr>
							<th class="sg-name">Name</th>
							<th class="sg-time">Time</th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach ( $iv_related_sgs as $post ): setup_postdata( $post ); ?>

						<tr>
							<td class="sg-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
							<td class="sg-time"><?php the_sg_time(); ?></td>
						</tr>

					<?php endforeach; wp_reset_postdata(); ?>
					</tbody>
				</table>

			</section>
		<?php endif; ?>

	<?php endwhile; wp_reset_query(); ?>

<?php get_footer(); ?>
