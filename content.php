<?php
/**
 * The template used for displaying small groups and blog posts
 *
 * @package InterVarsity
 */
?>

<?php
$post_type = get_post_type();
$is_small_group = ( 'iv_small_group' === $post_type );
$is_post = ( 'post' === $post_type );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry-tile' ); ?>>

	<?php $url = get_permalink(); ?>

	<header class="entry-header">

		<h3 class="entry-title">
			<?php if ( is_sticky() ): ?>
				<span class="pin-icon"><?php iv_icon( 'pin' ); ?></span>
			<?php endif; ?>
			<a href="<?php echo $url; ?>"><?php the_title(); ?></a>
		</h3>
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

		<?php if ( ! post_password_required() ): ?>

			<?php if ( $is_small_group ): ?>

				<?php the_sg_details( '<hr /><div class="sg-details">', '</div>' ); ?>

				<?php the_sg_contact( '<hr /><div class="sg-contact"><span class="entry-label">Contact:</span> ', '</div>' ); ?>

			<?php elseif ( $is_post ): ?>

				<?php iv_blog_details( '<hr />' ); ?>
				<?php iv_blog_terms( '<hr />' ); ?>

			<?php endif; ?>

		<?php endif; ?>

	</div>

</article>
