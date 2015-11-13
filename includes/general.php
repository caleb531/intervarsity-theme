<?php
// General functions for both data retrieval and HTML output

// Generates pagination links
function iv_paginate_links() {
	global $wp_query;
	// If entries are broken across multiple pages
	if ( $wp_query->max_num_pages > 1 ) {
		// Output pagination links
		?>
		<hr />
		<div class="pagination">
		<?php
		echo paginate_links( array(
			'base'       => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
			'format'     => '?paged=%#%',
			'current'    => max( 1, get_query_var('paged') ),
			'total'      => $wp_query->max_num_pages,
			'prev_text'  => '<span class="iv-icon iv-icon-chevron-left"></span>',
			'next_text'  => '<span class="iv-icon iv-icon-chevron-right"></span>',
			'show_all'	 => true
		) );
	}
	?>
	</div>
	<?php
}

// Retrieves array of post types associated with the current taxonomy
function iv_get_taxonomy_post_types() {

	$taxonomy_name = get_query_var( 'taxonomy' );
	// If current taxonomy exists
	if ( $taxonomy_name ) {
		// Return post types for taxonomy
		$taxonomy = get_taxonomy( $taxonomy_name );
		$post_types = $taxonomy->object_type;
		return $post_types;
	} else {
		// Otherwise, taxonomy doesn't exist
		return null;
	}

}

// Retrieves the campus object for the given small group
// Small groups can technically have multiple associated campuses, but
// it is only practical to have one campus per small group
function iv_get_campus( $sg ) {
	// Retrieve all campuses associated with this small group
	$campuses = get_the_terms( $sg->ID, 'sg_campus' );
	// If one or more campuses is associated with this SG
	if ( is_array( $campuses ) && count( $campuses ) > 0 ) {
		// Get first campus in array
		// Keys correspond to term IDs, so it's not guaranteed to be at index 0
		$campus = current( $campuses );
	} else {
		// Otherwise, indicate no campus
		$campus = null;
	}
	return $campus;
}

// Outputs the custom footer for the site
function iv_footer() {
	$ivcf_enabled = get_theme_mod( 'iv_footer_ivcf_enabled' );
	$ivcf_link = get_theme_mod( 'iv_footer_ivcf_link' );
	$ivcf_text = get_theme_mod( 'iv_footer_ivcf_text', IV_DEFAULT_FOOTER_IVCF_TEXT );
	$ivcf_image_id = get_theme_mod( 'iv_footer_ivcf_image' );
	// Output image link to IVCF website if enabled
	if ( ! empty( $ivcf_enabled ) ) {
		?>
		<a href="<?php echo esc_url( $ivcf_link ); ?>" class="ivcf-link">
			<?php if ( ! empty( $ivcf_image_id ) ): ?>
				<img src="<?php echo wp_get_attachment_url( $ivcf_image_id ); ?>" alt="<?php $ivcf_text; ?>" class="ivcf-image" />
			<?php else: ?>
				<?php echo $ivcf_text; ?>
			<?php endif; ?>
		</a>
		<?php
	}
	// Output custom copyright text
	$copyright_enabled = get_theme_mod( 'iv_footer_copyright_enabled' );
	$copyright_text = get_theme_mod( 'iv_footer_copyright_text', '' );
	if ( ! empty( $copyright_enabled ) ) {
		?>
		<div class="copyright"><?php echo wpautop( $copyright_text ); ?></div>
		<?php
	}
}
