// Enables needed and useful UI behaviors with JavaScript
(function ( window, $, FastClick ) {
$( document ).ready(function () {

// Retrieve most useful jQuery elements for use throughout the script
var $$ = {};
$$.siteHeader = $( '#site-header' );
$$.navControlResponsive = $( '#nav-control-responsive' );
$$.siteHeaderNavList = $( '#site-header-nav-list' );
$$.siteHeaderSearch = $( '#site-header-search' );
$$.searchForm = $$.siteHeaderSearch.find( '.searchform' );
$$.searchInput = $$.searchForm.find( 'input' );
$$.sgFilter = $( '#sg-filter' );

// Detect if search field is currently focused
function detectSearchState() {
	$$.siteHeader.toggleClass( 'site-header-search-active', $$.searchInput[0].value !== '' || $$.searchInput[0] === document.activeElement );
}

// Make mobile navigation functional
$$.navControlResponsive.on( 'click', function () {
	// Toggle open/close state of compact navigation
	$$.siteHeader.toggleClass( 'site-header-nav-open' );
	// Close any open sub menus
	$( '.sub-menu-open' ).removeClass( 'sub-menu-open' );
});

// Bind intuitive behavior to search icon
$( '.search-icon' ).on( 'mousedown touchstart', function () {
	var $searchIcon = $( this ),
		$searchInput = $searchIcon.next( '.search' ),
		$searchSubmit = $searchInput.next( '.searchsubmit' );
	// If search field is focused
	if ( $searchInput[0] === document.activeElement ) {
		// Submit search form
		$searchSubmit.trigger( 'click' );
	} else {
		// Otherwise, focus search fild
		$searchInput.trigger( 'focus' );
	}
	return false;
});

// Prevent any search form from submitting if search field is empty
$( '.searchform' ).on( 'submit', function () {
	if ( '' === $( this ).find( '.search' ).val() ) {
		return false;
	}
})
.eq( 0 )
// Attach floating tooltip to search form for improved user experience
.attr({
	'data-tooltip': 'data-tooltip',
	'data-tooltip-title': 'Search'
});

$$.searchInput
// Ensure search field remains expanded if query is not empty
.on( 'change', function () {
	this.setAttribute( 'value', this.value );
})
// Make other elements aware of the search field's focus state
.on( 'focus blur', detectSearchState);
detectSearchState();

// Add tooltips to the designated elements
$$.siteHeader.find( '[data-tooltip]' ).each(function ( e, elem ) {
	var $parent, $tooltip;
	$parent = $( elem );
	// Create general tooltip container
	$tooltip = $( '<div class="tooltip">' );
	$tooltip.html( $parent.attr( 'data-tooltip-title' ) );
	$parent.append( $tooltip );
});

// Focus search input when search tooltip is clicked
$$.searchForm.on( 'click', '.tooltip', function () {
	$$.searchInput.trigger( 'focus' );
});

// Do not visit on first click nav items which have submenus
$$.siteHeaderNavList.on( 'click', 'a', function () {
	var $link = $(this),
		$subMenu = $link.next( '.sub-menu' );
	// If nav menu is open and sub menu exists and sub menu is not open
	if ( $$.siteHeader.hasClass( 'site-header-nav-open' ) && 1 === $subMenu.length && false === $subMenu.hasClass( 'sub-menu-open' ) ) {
		// Close any open submenus
		$( '.sub-menu-open' ).removeClass( 'sub-menu-open' );
		// Open submenu for clicked link
		$subMenu.addClass('sub-menu-open');
		return false;
	}
});

// Update page when item is chosen from small group filter
$$.sgFilter.on( 'change', 'select', function () {
	$$.sgFilter.trigger( 'submit' );
});

// Enable FastClick for all elements on the page
FastClick.attach( document.body );

});
}( window, jQuery, window.FastClick ));
