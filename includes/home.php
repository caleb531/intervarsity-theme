<?php
// Functions for outputting homepage-specific content

// Outputs the content (excluding link) for a single home content box
function iv_home_box_content( $icon, $title, $desc ) {

	?>
	<div class="home-box-header">
		<div class="home-box-icon">
			<?php if ( ! empty( $icon ) ): ?>
				<?php iv_icon( $icon ); ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="home-box-content">
		<h3 class="home-box-title"><?php echo $title; ?></h3>
		<p class="home-box-desc"><?php echo $desc; ?></p>
	</div>
	<?php

}

// Outputs all home content boxes
function iv_home_boxes() {

	ob_start();
	?>

	<div id="home-boxes">

		<?php $enabled_boxes = 0; ?>
		<?php for ( $i = 1; $i <= IV_NUM_HOME_BOXES; $i += 1 ): ?>

			<?php $id_base = "iv_home_box{$i}"; ?>
			<?php if ( get_theme_mod( "{$id_base}_enabled", IV_DEFAULT_HOME_BOX_ENABLED ) ): ?>

				<?php
				$enabled_boxes += 1;

				$box_icon = get_theme_mod( "{$id_base}_icon" );

				$box_title = get_theme_mod( "{$id_base}_title" );
				if ( empty( $box_title ) ) {
					$box_title = "Title $i";
				}

				$box_desc = get_theme_mod( "{$id_base}_desc" );
				if ( empty( $box_desc ) ) {
					$box_desc = "This is the description for content box $i.";
				}

				$box_page_id = get_theme_mod( "{$id_base}_page" );
				if ( ! empty( $box_page_id ) ) {
					$box_link = get_permalink( $box_page_id );
				} else {
					$box_link = null;
				}

				?>

				<div class="home-box">

					<?php if ( ! empty( $box_link ) ): ?>
						<a href="<?php echo esc_url( $box_link ); ?>">
							<?php iv_home_box_content( $box_icon, $box_title, $box_desc ); ?>
						</a>
					<?php else: ?>
						<?php iv_home_box_content( $box_icon, $box_title, $box_desc ); ?>
					<?php endif; ?>

				</div>

			<?php endif; ?>

		<?php endfor; ?>

	</div>

	<?php
	// Only output home box container if at least one home box is enabled
	if ( $enabled_boxes !== 0 ) {
		echo trim( ob_get_clean() );
	} else {
		ob_clean();
	}

}
