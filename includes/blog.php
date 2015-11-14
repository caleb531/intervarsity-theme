<?php
// Functions for outputting blog-related content and data

// Outputs details for the current post (namely, author and date); this function
// may be called outside of The Loop, whereas functions like get_the_author()
// must be used in The Loop, and so we must retrieve the author ID using
// get_queried_object() and work from there
function iv_blog_details( $before = '', $after = '' ) {
	if ( is_single() ) {
		$author_id = get_queried_object()->post_author;
	} else {
		$author_id = get_the_author_meta( 'ID' );
	}
	echo $before;
	?>
	<div class="post-details">
		Posted by <a href="<?php echo get_author_posts_url( $author_id ); ?>"><?php echo get_the_author_meta( 'display_name', $author_id ); ?></a> on <?php echo date_i18n( get_option( 'date_format' ), get_post_time() ); ?>
	</div>
	<?php
	echo $after;
}

// Outputs the current post's assigned categories/tags
function iv_blog_terms( $before = '', $after = '' ) {

	global $post;
	$num_categories = count( wp_get_post_categories( $post->ID ) );
	$has_category = ( 0 !== $num_categories ) && ! ( 1 === $num_categories && has_category( 'uncategorized' ) );
	$has_tag = has_tag();
	?>
	<div class="post-terms">
		<?php if ( $has_category || $has_tag  ): ?>

			<?php echo $before; ?>
			<?php if ( $has_category ): ?>
				<div class="post-categories"><span class="entry-label">Categories:</span> <?php echo get_the_category_list( ', ' ); ?></div>
			<?php endif; ?>

			<?php if ( $has_tag ): ?>
				<div class="post-categories"><span class="entry-label">Tags:</span> <?php echo get_the_tag_list( '', ', ', '' ); ?></div>
			<?php endif; ?>
			<?php echo $after; ?>

		<?php endif; ?>
	</div>
	<?php

}

// Replaces the "more" text for the post excerpt with a link to read more
function iv_excerpt_more( $more ) {
	return '&hellip; <a class="moretag" href="'. get_permalink() . '">(read the full post)</a>';
}
add_filter( 'excerpt_more', 'iv_excerpt_more' );

// Callback for listing comments (used by wp_list_comments in comments.php)
function iv_list_comments( $comment, $args, $depth ) {

	$GLOBALS['comment'] = $comment;
	?>
	<?php if ( 'pingback' === $comment->comment_type || 'trackback' === $comment->comment_type ): ?>

		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'intervarsity' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'intervarsity' ), ' ' ); ?></p>

	<?php else: ?>

		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

			<article id="comment-<?php comment_ID(); ?>" class="comment-body">

				<header class="comment-header">

					<div class="comment-author">

						<?php echo get_avatar( $comment, 60 ); ?>

						<div class="comment-details">

							<span class="author-name"><?php echo get_comment_author_link(); ?></span>

							<span class="comment-date">
								<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time datetime="<?php comment_time( 'c' ); ?>">
								<?php echo get_comment_time( get_option( 'date_format' ) . ' \a\t ' . get_option( 'time_format' ) ); ?>
								</time></a>
							</span>

						</div>

					</div>

				</header>

				<?php if ( 0 === $comment->comment_approved ) : ?>

					<em><?php _e( 'Your comment is awaiting moderation.', 'intervarsity'); ?></em>
					<br />

				<?php endif; ?>

				<div class="comment-content"><?php comment_text(); ?></div>

				<div class="comment-controls">

					<?php comment_reply_link( array_merge( $args, array(
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="comment-control comment-reply-control">',
						'after'     => '</div>'
					) ) ); ?>
					<?php edit_comment_link( __( 'Edit', 'intervarsity' ), '<div class="comment-control comment-edit-control">', '</div>' ); ?>

				</div>

			</article>

	<?php endif;

}

// Outputs HTML links for navigating multiple pages of post comments
function iv_comment_pagination_links( $placement ) {
	?>
	<nav role="navigation" id="comment-nav-<?php echo $placement; ?>" class="comment-navigation">
		<div class="nav-previous"><?php previous_comments_link( __( 'Older Comments', 'intervarsity' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'intervarsity' ) ); ?></div>
	</nav>
	<?php
}
