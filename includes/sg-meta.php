<?php
// Functions for displaying small group meta data (e.g. time and location)

// Outputs the details meta data for the given small group
function the_sg_details( $before = '', $after = '' ) {
	global $post;

	$time = get_the_sg_time();
	$location = get_the_sg_location();
	if ( $time || $location ) {
		echo $before;
		if ( $time ) {
			?>
			<span class="entry-label">Time:</span>
			<span class="sg-time"><?php echo $time; ?></span>
			<?php
		}
		if ( $location ) {
			?>
			<br />
			<span class="entry-label">Location:</span>
			<span class="sg-location"><?php echo $location; ?></span>
			<?php
		}
		// If entry is not being viewed from its campus page
		if ( ! is_tax( 'sg_campus' ) && ! is_singular( 'iv_small_group' ) ) {
			// Display campus name within small group details to reduce
			// ambiguity for small groups with same title
			$campuses = get_the_term_list( $post->ID, 'sg_campus', '', ', ', '' );
			?>
			<?php if ( false !== $campuses ): ?>
				<br />
				<span class="entry-label">Campuses:</span>
				<span class="sg-campuses"><?php echo $campuses; ?></span>
			<?php endif; ?>
			<?php
		}
		echo $after;
	}

}

// Formats phone number for display on frontend
function iv_format_phone_number( $phone ) {

	// Ensure phone number does not wrap lines by replacing
	// every hyphen with a non-breaking hyphen
	$phone = str_replace( '-', '&#x2011;', $phone );
	return $phone;

}

// Outputs the contact meta data for the given small group (name, phone, email)
function the_sg_contact( $before = '', $after = '' ) {

	// Retrieve contact meta data
	$name = get_the_sg_contact_name();
	$phone = get_the_sg_contact_phone();
	$email = get_the_sg_contact_email();
	// If any of the meta data was entered
	if ( $name || $phone || $email ) {
		// Display contact meta data
		echo $before;
		if ( $name && ( $phone || $email ) ) {
			?><span class="sg-contact-name"><?php echo $name; ?></span><?php
		}
		if ( $phone ) {
			$phone = iv_format_phone_number( $phone );
			?> at <span class="sg-contact-phone"><?php echo $phone; ?></span><?php
		}
		if ( $email ) {
			// Encode random characters in the email address to prevent spam
			// bots from parsing it
			$email = antispambot( $email, 1 );
			?> (<span class="sg-contact-email"><a href="mailto:<?php echo $email; ?>">Email</a></span>)<?php
		}
		echo $after;
	}

}

// Return select options for Day filter
function iv_filter_day_options() {
	return array(
		array(
			'label' => 'Any Day',
			'value' => ''
		),
		array(
			'label' => 'Monday',
			'value' => 'monday'
		),
		array(
			'label' => 'Tuesday',
			'value' => 'tuesday'
		),
		array(
			'label' => 'Wednesday',
			'value' => 'wednesday'
		),
		array(
			'label' => 'Thursday',
			'value' => 'thursday'
		),
		array(
			'label' => 'Friday',
			'value' => 'friday'
		)
	);
}

function iv_filter_campus_options() {
	$campuss = get_terms( 'sg_campus' );
	$options = array(
		array(
			'label' => 'Any Campus',
			'value' => ''
		)
	);
	foreach ( $campuss as $campus ) {
		array_push( $options, array(
			'label' => $campus->name,
			'value' => $campus->slug
		) );
	}
	return $options;
}
function iv_filter_category_options() {
	$campuss = get_terms( 'sg_category' );
	$options = array(
		array(
			'label' => 'Any Category',
			'value' => ''
		)
	);
	foreach ( $campuss as $campus ) {
		array_push( $options, array(
			'label' => $campus->name,
			'value' => $campus->slug
		) );
	}
	return $options;
}

// Outputs day selection control for small groups
function iv_sg_filter_select( $key, $options_callback ) {
	global $iv_filter_options, $wp_query;

	$options = call_user_func_array( $options_callback, array( $key ) );
	?>
	<select name="<?php echo $key; ?>" id="iv-day-filter">
		<?php foreach ( $options as $option ): ?>

			<?php if ( $option['value'] === $wp_query->get( $key ) ): ?>
				<option value="<?php echo $option['value']; ?>" selected><?php echo $option['label']; ?></option>
			<?php else: ?>
				<option value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option>
			<?php endif; ?>

		<?php endforeach; ?>
	</select>
	<?php

}

// Outputs the form for all small group filter controls
function iv_sg_filter_form() {

	$archive_url = get_post_type_archive_link( 'iv_small_group' );
	?>
	<form method="get" action="<?php echo $archive_url; ?>" id="sg-filter">
		<label>Filter By:</label>
		<?php iv_sg_filter_select( 'sg_day', 'iv_filter_day_options' ); ?>
		<?php iv_sg_filter_select( 'sg_campus', 'iv_filter_campus_options' ); ?>
		<?php iv_sg_filter_select( 'sg_category', 'iv_filter_category_options' ); ?>
		<input type="submit" value="Filter" />
	</form>
	<?php

}
