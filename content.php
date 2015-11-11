<?php
/**
 * The template used for displaying small groups and blog posts
 *
 * @package InterVarsity
 */
?>

<?php
$is_small_group = ( is_tax( 'sg_campus' ) || is_search() );
$is_post = ( is_home() || is_archive() );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry-tile' ); ?>>

	<?php $url = get_permalink(); ?>

	<header class="entry-header">

		<h2 class="entry-title">
			<?php if ( is_sticky() ): ?>
				<span class="iv-icon iv-icon-pin"></span>
			<?php endif; ?>
			<a href="<?php echo $url; ?>"><?php the_title(); ?></a>
		</h2>
		<?php if ( current_user_can( 'edit_posts' ) ): ?>
			<a class="entry-edit-link dashicons-before dashicons-edit" href="<?php echo get_edit_post_link(); ?>" target="_blank">Edit Post</a>
		<?php endif; ?>

	</header>

	<div class="entry-main">

		<div class="entry-row">

			<?php if ( has_post_thumbnail() ): ?>
				<div class="entry-thumbnail entry-cell">
					<a href="<?php echo $url; ?>"><?php the_post_thumbnail( 'tile-1x' ); ?></a>
				</div>
			<?php endif; ?>

			<div class="entry-content entry-cell">
				<?php if ( $is_small_group ): ?>
					<?php the_content(); ?>
				<?php elseif ( $is_post ): ?>
					<?php the_excerpt(); ?>
				<?php endif; ?>
			</div>

		</div>

		<?php if ( $is_small_group ): ?>

			<?php the_sg_details( '<hr /><div class="sg-details">', '</div>' ); ?>

			<?php the_sg_contact( '<hr /><div class="sg-contact"><span class="entry-label">Contact:</span> ', '</div>' ); ?>

		<?php elseif ( $is_post ): ?>

			<?php if ( ! post_password_required() ): ?>

				<?php iv_blog_details( '<hr />' ); ?>
				<?php iv_blog_terms( '<hr />' ); ?>

			<?php endif; ?>

		<?php endif; ?>

	</div>

</article>
