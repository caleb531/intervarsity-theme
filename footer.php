<?php
/**
 * The HTML footer template, included at the bottom of of every frontend page
 * Closes container elements found in header.php, and displays the site footer
 *
 * @package InterVarsity
 */
?>

		</div><!-- end #content -->

	</main><!-- end #page -->

	<footer id="site-footer">

		<?php if ( has_nav_menu( 'footer_menu' ) ): ?>

			<?php wp_nav_menu( array(
				'theme_location'  => 'footer_menu',
				'items_wrap'	 => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'container'       => false,
				'menu_id'         => 'site-footer-nav-list',
				'depth'           => 1
			) ); ?>

		<?php endif; ?>

		<div id="site-footer-content">
			<?php iv_footer_content() ?>
		</div>

	</footer>

	<?php wp_footer(); ?>

</body>
</html>
