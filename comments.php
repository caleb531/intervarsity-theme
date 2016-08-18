<?php
/**
 * The template for displaying blog post comments
 *
 * @package InterVarsity
 */
?>

<?php
// Exit if the post is password protected & user is not logged in
if ( post_password_required() ) {
	return;
}
?>

	<div id="comments" class="comments-area">

		<?php if ( have_comments() ): ?>

			<div id="comments-title">
				<h3>
					<?php
						printf( _n( '1 Comment', '%1$s Comments', get_comments_number(), 'intervarsity' ),
							number_format_i18n( get_comments_number() ) );
					?>
				</h3>
				<span class="sep"><span class="sep-core"></span></span>
			</div>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ): ?>

				<?php iv_comment_pagination_links( 'above' ); ?>

			<?php endif;?>

				<ol class="commentlist">
					<?php wp_list_comments( array(
						'callback' => 'iv_list_comments'
					) ); ?>
				</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ): ?>

				<?php iv_comment_pagination_links( 'below' ); ?>

			<?php endif; ?>

		<?php endif; ?>

		<?php
			// Message to display when comments are closed
			if ( ! comments_open() && 0 !== get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ):
		?>

			<div id="nocomments" class="notification info">
				<div class="icon"><?php echo 'Comments are closed.'; ?></div>
			</div>

	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments .comments-area -->
