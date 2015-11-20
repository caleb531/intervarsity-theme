<?php
/**
 * The HTML footer template, included at the bottom of of every frontend page
 * Closes container elements found in header.php, and displays the site footer
 *
 * @package InterVarsity
 */
?>

		</div><!-- end #content -->

	</div><!-- end #page -->

	<footer id="site-footer">

		<?php if ( has_nav_menu( 'footer_menu' ) ): ?>

			<?php wp_nav_menu( array(
				'theme_location'  => 'footer_menu',
				'container'       => 'nav',
				'container_id'    => 'site-footer-nav',
				'menu_id'         => 'site-footer-nav-list',
				'depth'           => 1
			) ); ?>

		<?php endif; ?>

		<div id="site-footer-content">
			<?php iv_footer() ?>
		</div>

	</footer>

	<?php wp_footer(); ?>

</body>
</html>
