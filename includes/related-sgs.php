<?php
// Functions for determining related small groups

// Regular expresison patterns used to determinw gender-specific small groups
define( 'IV_SG_GENDER_MEN_PATT', '/\b((men|man|guy)s?)\b/' );
define( 'IV_SG_GENDER_WOMEN_PATT', '/\b((women|woman|girl)s?|(ladys?|ladies))\b/' );
// The amount added to the relevance factor for a related gender-specific SG
define( 'IV_SG_GENDER_MATCH_WEIGHT', 10 );

// Retrieve the gender of a small group if it has one by examining its
// categories; return null if small group is found to be co-ed
function iv_get_sg_gender( $sg_terms ) {

	foreach ( $sg_terms as $sg_term ) {
		if ( preg_match( IV_SG_GENDER_MEN_PATT, $sg_term ) ) {
			return 'men';
		} else if ( preg_match( IV_SG_GENDER_WOMEN_PATT, $sg_term ) ) {
			return 'women';
		}
	}

	// A gender of null indicates a co-ed small group
	return null;

}

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
		// If target SG has an assigned campus, restrict related SGs to that
		// campus; otherwise, SGs from any campus could be potential candidates
		'sg_campus'      => ( ! empty( $campus ) ? $campus->slug : null),
		'orderby'        => 'title',
		'order'          => 'ASC'
	) );
	// Retrieve array of target SG's associated categories
	$target_terms = wp_get_post_terms( $target_sg->ID, 'sg_category', array(
		'fields' => 'slugs'
	) );
	// Retrieve gender of target SG (if any) to help determine SG relevance
	$target_gender = iv_get_sg_gender( $target_terms );

	// SGs are grouped by their respective relevance factors
	$sg_groups = array();

	foreach ( $campus_sgs as $sg ) {

		// Retrieve SG data for comparison
		$sg_terms = wp_get_post_terms( $sg->ID, 'sg_category', array(
			'fields' => 'slugs'
		) );
		$sg_gender = iv_get_sg_gender( $sg_terms );

		// The relevance factor for a SG is calculated from the number of
		// categories it shares with that of the target SG
		$relevance_factor = count( array_intersect( $target_terms, $sg_terms ) );

		// If SG is gender-specific and its gender matches that of the target
		// SG, increase its precedence
		if ( null !== $sg_gender && $target_gender === $sg_gender ) {
			$relevance_factor += IV_SG_GENDER_MATCH_WEIGHT;
		}

		if ( 0 !== $relevance_factor ) {

			// Place key into groups container according to its relevance
			if ( ! array_key_exists( $relevance_factor, $sg_groups ) ) {
				$sg_groups[ $relevance_factor ] = array();
			}
			array_push( $sg_groups[ $relevance_factor ], $sg );

		}

	}

	// The most relevant gender-specific SGs have greater precedence than the
	// most relevant co-ed SGs
	if ( 0 !== count( $sg_groups ) ) {
		// Store all gender-specific SGs with the same gender as a flat array
		krsort( $sg_groups );
		foreach ( $sg_groups as $sg_group ) {
			foreach ( $sg_group as $sg ) {
				$related_sgs[] = $sg;
			}
		}
	}

	// Cap list of related SGs as the defined size
	$related_sgs = array_slice( $related_sgs, 0, get_theme_mod( 'iv_max_related_sgs', IV_DEFAULT_MAX_RELATED_SGS ) );

	return $related_sgs;

}
