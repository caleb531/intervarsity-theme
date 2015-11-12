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

// The minimum relevance factor a SG must have in order to be deemed similar to
// the target SG
define( 'IV_MIN_SG_RELEVANCE_FACTOR', 2 );

// Retrieve related small groups for the given small group (SG)
function iv_get_related_sgs( $target_sg ) {
	// The target SG is the SG for which to find related SGs

	// Retrieve associated campus object for target SG
	$campus = iv_get_campus( $target_sg );
	// Keep array of related SGs
	$related_sgs = array();
	// Retrieve all other SGs from same campus
	$campus_sgs = get_posts( array(
		'post_type'      => 'iv_small_group',
		'posts_per_page' => -1,
		// Don't include target SG in list of other SGs
		'post__not_in'   => array( $target_sg->ID ),
		'sg_campus'      => ( ! empty( $campus ) ? $campus->slug : null),
		'orderby'        => 'title',
		'order'          => 'ASC'
	) );
	// Format SG titles to ensure casing and punctuation are irrelevant
	$target_title = iv_simplify_sg_title( $target_sg->post_title );
	// Retrieve array of words in target SG title
	$target_words = iv_get_words_in_title( $target_title );
	// Parse out specific gender (if any) from SG title
	$target_gender = iv_get_sg_gender( $target_title );

	// SGs are grouped by the number of common words they share with the
	// title of the target SG
	$coed_sg_groups = array();
	$gender_sg_groups = array();

	foreach ( $campus_sgs as $sg ) {

		// Retrieve SG data for comparison
		$sg_title = iv_simplify_sg_title( $sg->post_title );
		$sg_words = iv_get_words_in_title( $sg_title );
		$sg_gender = iv_get_sg_gender( $sg_title );

		// The relevance factor for a SG is calculated from the number of
		// words its title shares with the target SG's title
		$relevance_factor = count( array_intersect( $target_words, $sg_words ) );

		// Co-ed SGs must have a relevance factor greater than or equal to
		// the minimum in order to be considered related
		if ( null === $sg_gender && $relevance_factor >= IV_MIN_SG_RELEVANCE_FACTOR ) {
			// Dereference array to avoid repeating below logic
			$sg_groups = &$coed_sg_groups;
		} else if ( null !== $sg_gender && $target_gender === $sg_gender ) {
			// Otherwise, if the SG is gender-specific, the SG gender must match
			// the target gender
			$sg_groups = &$gender_sg_groups;
		} else {
			// Reset the pointer on each iteration to avoid re-using the value
			// from the previous iteration
			unset( $sg_groups );
		}

		// If either of the above two conditions evaluated to true
		if ( isset( $sg_groups ) ) {

			// Place key into groups container according to its relevance
			if ( ! array_key_exists( $relevance_factor, $sg_groups ) ) {
				$sg_groups[ $relevance_factor ] = array();
			}
			array_push( $sg_groups[ $relevance_factor ], $sg );

		}

	}

	// The most relevant gender-specific SGs have greater precedence than the
	// most relevant co-ed SGs
	if ( 0 !== count( $gender_sg_groups ) ) {
		// Store all gender-specific SGs with the same gender as a flat array
		krsort( $gender_sg_groups );
		foreach ( $gender_sg_groups as $sg_group ) {
			foreach ( $sg_group as $sg ) {
				$related_gender_sgs[] = $sg;
			}
		}
	} else {
		$related_gender_sgs = array();
	}

	// Store only the most relevant co-ed SGs as a flat array
	if ( 0 !== count( $coed_sg_groups ) ) {
		$largest_relevance_factor = max( array_keys( $coed_sg_groups ) );
		$related_coed_sgs = $coed_sg_groups[ $largest_relevance_factor ];
	} else {
		$related_coed_sgs = array();
	}

	// Merge the two arrays into the final list of related SGs (capping the size
	// thereof at the defined max size)
	$related_sgs = array_slice( array_merge( $related_gender_sgs, $related_coed_sgs ), 0, IV_DEFAULT_MAX_RELATED_SGS );

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
