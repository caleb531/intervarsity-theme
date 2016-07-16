<?php
/**
 * The template for displaying the site search form (which appears in the
 * header, on 404 pages, and on pages where the search yielded no results)
 *
 * @package InterVarsity
 */
?>
<form method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<span class="search-icon"><?php iv_icon( 'search' ); ?></span>
	<input type="search" class="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search small groups" />
	<input type="submit" class="searchsubmit" value="Search" />
</form>
