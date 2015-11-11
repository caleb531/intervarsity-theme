	<?php
/**
 * The template for displaying a message that small groups cannot be found or
 * simply do not exist
 *
 * @package InterVarsity
 */
?>

<div id="no-results">

	<?php if ( is_search() ) : ?>

		<h2><?php echo get_theme_mod( 'iv_search_null_heading', IV_DEFAULT_NULL_SEARCH_HEADING ) ?></h2>

		<?php
		echo wpautop( get_theme_mod( 'iv_search_null_message', IV_DEFAULT_NULL_SEARCH_MESSAGE ) );
		?>
		<?php get_search_form(); ?>

	<?php elseif ( is_tax( 'sg_campus' ) ) : ?>

		<?php if ( ! empty( $_GET['sg_day'] ) ): ?>

			<h2><?php echo get_theme_mod( 'iv_search_null_heading', IV_DEFAULT_NULL_SEARCH_HEADING ) ?></h2>

			<?php
			echo wpautop( get_theme_mod( 'iv_search_null_message', IV_DEFAULT_NULL_SEARCH_MESSAGE ) );
			?>

			<?php echo iv_sg_filter_form(); ?>

		<?php else: ?>

			<h2><?php echo get_theme_mod( 'iv_no_sg_heading', 'No Small Groups Listed' ); ?></h2>

			<?php
			$campus_name = single_term_title( '', false );
			$no_sg_message = get_theme_mod( 'iv_no_sg_message', IV_DEFAULT_NO_SG_MESSAGE );
			$no_sg_message = str_replace( '{{campus}}', $campus_name, $no_sg_message );
			echo wpautop( $no_sg_message );
			?>

		<?php endif; ?>

	<?php endif; ?>

</div>
