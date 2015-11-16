<?php

// Define a recommended header size
if ( ! defined( 'HEADER_IMAGE_WIDTH' ) ) {
	define( 'HEADER_IMAGE_WIDTH', 1600 );
}
if ( ! defined( 'HEADER_IMAGE_HEIGHT' ) ) {
	define( 'HEADER_IMAGE_HEIGHT', 280 );
}

// Augment the logic for selecting the title in the page header
function iv_page_header_title() {
	if ( is_tax() ) {
		// Handle taxonomy archives
		printf( '%1$s Small Groups', single_term_title() );
	} else if ( is_search() ) {
		// Indicate that search is excluded to small groups
		echo 'Small Group Search';
	} else if ( is_author() ) {
		echo 'Posts by ' . get_queried_object()->display_name;
	} else if ( is_front_page() && is_home() ) {
		bloginfo( 'name' );
	} else if ( is_category() ) {
		echo "Category: " . get_queried_object()->cat_name;
	} else if ( is_tag() ) {
		echo "Tag: " . get_queried_object()->name;
	} else {
		single_post_title();
	}
}

// Output breadcrumb link with the given title
function iv_breadcrumb_link( $title, $link ) {
	?>
	<a href="<?php echo $link; ?>" class="page-breadcrumb"><?php echo $title; ?></a>
	<?php
}

// Output a "static" breadcrumb (no link)
function iv_static_breadcrumb( $title ) {
	?>
	<span class="page-breadcrumb"><?php echo $title; ?></span>
	<?php
}

// Output a single breadcrumb delimiter
function iv_breadcrumb_delimiter() {
	?>
	<span class="page-breadcrumb-delimiter">
		<span class="iv-icon iv-icon-chevron-right"></span>
	</span>
	<?php
}

// Output page breadcrumbs
function iv_page_breadcrumbs() {
	global $post;
	?>
	<div id="page-breadcrumbs">
		<?php
		if ( is_singular( 'iv_small_group' ) || is_tax() ) {
			// If this is a small group campus/category page or an individual
			// small group page

			if ( is_tax() ) {
				// Retrieve campus object if this is a term page
				$sg_term = get_queried_object();
			} else {
				// Otherwise, get campus for this small group if it exists
				$sg_term = iv_get_sg_campus( $post );
				if ( empty( $sg_term ) ) {
					// If it doesn't exist, try fetching category
					$sg_term = iv_get_sg_category( $post );
				}
			}
			if ( ! empty( $sg_term ) ) {
				$sg_index_id = get_theme_mod( 'iv_sg_index_page' );
				if ( ! empty( $sg_index_id ) && null !== get_post( $sg_index_id ) ) {
					// If small group index exists, show breadcrumb for small
					// groups index page
					iv_breadcrumb_link(
						get_the_title( $sg_index_id ),
						get_permalink( $sg_index_id )
					);
				} else {
					// Otherwise, still indicate some sort of a hierarchy
					iv_static_breadcrumb( 'Small Groups' );
				}
				// Indicate what taxonomy this is (i.e. Campus or Category)
				$sg_tax = get_taxonomy( $sg_term->taxonomy );
				iv_breadcrumb_delimiter();
				iv_static_breadcrumb( $sg_tax->labels->name );
				if ( is_single() ) {
					iv_breadcrumb_delimiter();
					// Show breadcrumb for campus page
					iv_breadcrumb_link(
						$sg_term->name,
						get_term_link( $sg_term )
					);
				}
			}

		} else if ( is_page() || ( is_home() && ! is_front_page() ) ) {
			// If this is a page (even the Posts page) show parent pages as
			// breadcrumbs

			$parent_ids = get_post_ancestors( get_queried_object()->ID );
			$last_parent_id = end( $parent_ids );
			foreach ( $parent_ids as $parent_id ) {
				$parent = get_post( $parent_id );
				// Breadcrumb for each parent page
				iv_breadcrumb_link(
					get_the_title( $parent ),
					get_permalink( $parent )
				);
				// Add delimiter between breadcrumbs
				if ( $last_parent_id !== $parent_id ) {
					iv_breadcrumb_delimiter();
				}
			}

		} else if ( is_singular( 'post' ) || is_author() || is_category() || is_tag() ) {
			// If this is some other Blog-related page, show the Posts page as
			// the only breadcrumb

			if ( 'page' == get_option( 'show_on_front' ) ) {
				// If Front Page is set to show static page, get title and
				// permalink for that page
				$blog_page = get_post( get_option( 'page_for_posts' ) );
				if ( ! empty( $blog_page ) ) {
					iv_breadcrumb_link(
						$blog_page->post_title,
						get_permalink( $blog_page->ID )
					);
				}
			} else {
				// If Front Page is set to show latest posts, Blog page is same as Front Page
				iv_breadcrumb_link(
					'Blog',
					esc_url( home_url( '/' ) )
				);
			}

		}
		?>
	</div>
	<?php
}

// Output social header if enabled
function iv_social_header() {

	$icons = array(
		array(
			'id'        => 'iv_facebook_link',
			'toggle_id' => 'iv_facebook_enabled',
			'title'     => 'Facebook',
			'icon'      => 'iv-icon-facebook',
			'class'     => 'facebook'
		),
		array(
			'id'        => 'iv_twitter_link',
			'toggle_id' => 'iv_twitter_enabled',
			'title'     => 'Twitter',
			'icon'      => 'iv-icon-twitter',
			'class'     => 'twitter'
		),
		array(
			'id'        => 'iv_instagram_link',
			'toggle_id' => 'iv_instagram_enabled',
			'title'     => 'Instagram',
			'icon'      => 'iv-icon-instagram',
			'class'     => 'instagram'
		),
		array(
			'id'        => 'iv_email_address',
			'toggle_id' => 'iv_email_enabled',
			'title'     => 'Email',
			'icon'      => 'iv-icon-mail',
			'class'     => 'email'
		)
	);

	// Output social header
	?>
	<div id="site-header-social"><ul>
	<?php
	$social_message_enabled = get_theme_mod( 'iv_social_message_enabled', false );
	// Output social header message
	if ( $social_message_enabled ) {
		$social_message = get_theme_mod( 'iv_social_message' );
		?>
		<li class="social message"><?php echo $social_message; ?></li>
		<?php
	}
	// Output social icons
	foreach ( $icons as $icon ) {
		$enabled = get_theme_mod( $icon['toggle_id'], false );
		if ( $enabled ) {
			$link = get_theme_mod( $icon['id'] );
			// Obscure email address in HTML to deter harvesting
			if ( is_email( $link ) ) {
				$link = "mailto:" . antispambot( $link, 1 );
			}
			// Output social icon
			?>
			<li class="social <?php echo $icon['class']; ?>">
				<a href="<?php echo $link; ?>" data-tooltip data-tooltip-title="<?php echo $icon['title'];?>">
					<span class="iv-icon <?php echo $icon['icon']; ?>"></span>
				</a>
			</li>
			<?php
		}
	}
	?>
	</ul></div>
	<?php

}

// Output page slider
function iv_header_slider() {

	// Sliders are only supported on static pages
	$page = get_queried_object();
	$slider_id = get_post_meta( $page->ID, '_iv_slider_id', true );
	// If slider shortcode was provided
	if ( $slider_id ) {
		// Retrieve slider data from ID
		$slider = get_post( $slider_id );
		if ( $slider ) {
			// Evaluate slider shortcode
			$slider_shortcode = "[cycloneslider id='{$slider->post_name}']";
			$slider_eval = do_shortcode( $slider_shortcode );
			?>
			<div id="page-slider">
				<?php echo $slider_eval; ?>
			</div>
			<?php
		}
	}

}

// Trigger responsive viewport on front-end pages and Maintenance Mode page
function iv_enable_responsive_viewport() {

	?>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no" />
	<?php

}
add_action( 'wp_head', 'iv_enable_responsive_viewport', 10 );
// WP Maintenance Mode page
add_action( 'wpmm_head', 'iv_enable_responsive_viewport', 10 );

// Add site icons to login screen and Maintenance Mode
if ( function_exists( 'wp_site_icon' ) ) {
	add_action( 'login_head', 'wp_site_icon', 10 );
	add_action( 'wpmm_head', 'wp_site_icon', 10 );
}
