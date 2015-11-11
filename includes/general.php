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

// Simplifies small group title by removing non-alphanumeric characters
function iv_simplify_sg_title( $title ) {

	return strtolower( preg_replace( '/[^A-Za-z0-9 ]/', '', $title ) );

}

// Retrieves array of words in the given small group title
function iv_get_words_in_title( $title ) {

	$words = str_word_count( $title, 1 );
	return $words;

}

// Parses the gender from the given (presumably-simplified) small group title
function iv_get_sg_gender( $title ) {

	// Match gender in title using regular expressions
	if ( preg_match( '/\b((men|man|guy)s?)\b/', $title ) ) {
		return 'men';
	} else if ( preg_match( '/\b((women|woman|girl)s?|(ladys?|ladies))\b/', $title ) ) {
		return 'women';
	} else {
		// If gender is not found, assume small group is co-ed
		return null;
	}

}

// Retrieve related small groups for the given small group
function iv_get_related_sgs( $target_sg ) {
	// The target SG is the small group for which to find related SGs

	// Retrieve associated campus object for target SG
	$campus = iv_get_campus( $target_sg );
	// Keep array of related small groups
	$related_sgs = array();
	// Retrieve all other small groups from same campus
	$campus_sgs = get_posts( array(
		'post_type'      => 'iv_small_group',
		'posts_per_page' => -1,
		// Don't include target SG in list of other SGs
		'post__not_in'   => array( $target_sg->ID ),
		'sg_campus'      => $campus->slug,
		'orderby'        => 'title',
		'order'          => 'ASC'
	) );
	// Format SG titles to ensure casing and punctiation do not interfere
	$target_title = iv_simplify_sg_title( $target_sg->post_title );
	// Retrieve array of words in target SG title
	$target_words = iv_get_words_in_title( $target_title );
	// Parse out specific gender (if any) from SG title
	$target_gender = iv_get_sg_gender( $target_title );

	// Loop through small groups in same campus
	foreach ( $campus_sgs as $sg ) {
		// Retrieve SG data for comparison
		$sg_title = iv_simplify_sg_title( $sg->post_title );
		$sg_words = iv_get_words_in_title( $sg_title );
		$sg_gender = iv_get_sg_gender( $sg_title );
		// If small group pertains to a specific gender
		if ( null !== $sg_gender ) {
			// ...and if genders match
			if ( $sg_gender === $target_gender ) {
				// ...then SG is related
				$related_sgs[] = $sg;
			}
		} else if ( null === $sg_gender ) {
			// If two or more words in titles match, SG is related
			$matching_words = array_intersect( $target_words, $sg_words );
			if ( count( $matching_words ) >= 2 ) {
				$related_sgs[] = $sg;
			}
		}
	}

	return $related_sgs;

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
