<?php
/**
 * The HTML header template, included at the top of every frontend page
 * Displays the HTML <head> and everything up until the #content container
 *
 * @package InterVarsity
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<!--[if lt IE 9]>
<script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class( 'hfeed site' ); ?>>

	<header id="site-header">

		<a href="#content" class="screen-reader-text skip-link">Skip to content</a>

		<div id="site-logo">

			<a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo ( has_header_image() ? get_header_image() : IV_DEFAULT_HEADER_IMAGE_URL ); ?>" alt="<?php bloginfo( 'name' ) ; ?>" /></a>
			<h1 class="screen-reader-text"><?php bloginfo( 'name' ); ?></h1>

		</div>

		<div id="site-header-core">

			<?php if ( has_nav_menu( 'header_menu' ) ): ?>

				<?php
				// Add responsive navigation list
				wp_nav_menu( array(
					'theme_location' => 'header_menu',
					'items_wrap'	 => '<h2 class="screen-reader-text">Site Navigation (Header)</h2><button id="nav-control-responsive"><span class="iv-icon iv-icon-list"></span></button><ul id="%1$s" class="%2$s">%3$s</ul>',
					'container'		 => 'nav',
					'container_id'	 => 'site-header-nav',
					'menu_id'        => 'site-header-nav-list',
					'depth'          => 2
				) );
				?>

			<?php endif; ?>

			<?php iv_social_header(); ?>

			<div id="site-header-search">
				<?php get_search_form(); ?>
			</div>

		</div><!-- end #site-header-core -->

	</header>

	<section id="page">

		<?php
		// Hide page header (via CSS class):
		//   1. On the front page if the front page is set to show latest posts
		//      OR if the page header is disabled for the front page
		//   2. On 404 pages
		if ( ! is_404() ): ?>

			<header id="page-header" class="<?php echo ( is_front_page() && ! get_theme_mod( 'iv_home_page_header_enabled', IV_DEFAULT_HOME_PAGE_HEADER_ENABLED ) ? 'screen-reader-text' : '' ); ?>">
				<div id="page-heading">
					<h2><?php iv_page_header_title(); ?></h2>
					<?php if ( is_singular( 'post' ) && ! post_password_required() ): ?>
						<?php iv_blog_details(); ?>
					<?php endif; ?>
				</div>
				<?php iv_page_breadcrumbs(); ?>
			</header>

		<?php endif; ?>

		<?php if ( is_page() || ( is_front_page() && ! is_home() ) ): ?>

			<?php if ( has_post_thumbnail() ): ?>
				<div id="page-thumbnail"><?php the_post_thumbnail(); ?></div>
			<?php else: ?>
				<?php iv_header_slider(); ?>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( is_front_page() && ! is_home() ): ?>
			<?php require_once IV_THEME_DIR . '/includes/home.php'; ?>
			<?php iv_home_boxes(); ?>
		<?php endif; ?>

		<div id="content">
