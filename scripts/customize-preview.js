// Enables live previews of some settings within Customizer when values change
( function( wp, $ ) {

	// The maximum number of home content boxes allowed by the theme
	var NUM_HOME_BOXES = 3;

	wp.customize( 'iv_social_message', function( oldMessage ) {
		oldMessage.bind( function( newMessage ) {
			$( '#site-header' ).find( '.social.message' ).html( newMessage );
		});
	});

	wp.customize( 'iv_facebook_link', function( oldLink ) {
		oldLink.bind( function( newLink ) {
			$( '#site-header' ).find( '.social.facebook' ).find( 'a' ).prop({
				href: newLink
			});
		});
	});

	wp.customize( 'iv_twitter_link', function( oldLink ) {
		oldLink.bind( function( newLink ) {
			$( '#site-header' ).find( '.social.twitter' ).find( 'a' ).prop({
				href: newLink
			});
		});
	});

	wp.customize( 'iv_instagram_link', function( oldLink ) {
		oldLink.bind( function( newLink ) {
			$( '#site-header' ).find( '.social.instagram' ).find( 'a' ).prop({
				href: newLink
			});
		});
	});

	wp.customize( 'iv_email_address', function( oldEmail ) {
		oldEmail.bind( function( newEmail ) {
			$( '#site-header' ).find( '.social.email' ).find( 'a' ).prop({
				href: 'mailto:' + newEmail
			});
		});
	});

	function add_home_content_box( index ) {

		var id_base = 'iv_home_box' + index;

		wp.customize( id_base + '_icon', function( oldIcon ) {
			oldIcon.bind( function( newIcon ) {
				var $homeBox = $( '.home-box' ).eq( index - 1 );
				var $boxIcon = $homeBox.find('.home-box-icon');
				if ( newIcon !== '' ) {
					var svgUrl = '/wp-content/themes/intervarsity/icons/' + newIcon + '.svg';
					$.get(svgUrl, function (data) {
						var $svg = $(data).find('svg');
						$boxIcon.empty().append($svg);
					});
				} else {
					$boxIcon.empty();
				}
			});
		});
		wp.customize( id_base + '_title', function( oldTitle ) {
			oldTitle.bind( function( newTitle ) {
				if ( ! newTitle ) {
					newTitle = 'Title ' + index;
				}
				$( '.home-box' )
					.eq( index - 1 )
					.find( 'h3' )
					.html( newTitle );
			});
		});
		wp.customize( id_base + '_desc', function( oldDesc ) {
			oldDesc.bind( function( newDesc ) {
				if ( ! newDesc ) {
					newDesc = 'This is the description for content box ' + index + '.';
				}
				$( '.home-box' )
					.eq( index - 1 )
					.find( 'p' )
					.html( newDesc );
			});
		});

	}

	for ( var i = 1; i <= NUM_HOME_BOXES; i += 1 ) {
		add_home_content_box( i );
	}

	wp.customize( 'iv_footer_ivcf_link', function( oldLink ) {
		oldLink.bind( function( newLink ) {
			$( '#site-footer' ).find( '.ivcf-link' ).prop({
				href: newLink
			});
		});
	});

	wp.customize( 'iv_footer_ivcf_text', function( oldText ) {
		oldText.bind( function( newText ) {
			var $link = $( '#site-footer' ).find( '.ivcf-link' );
			if ( 0 === $link.find('img').length ) {
				$link.text( newText );
			}
		});
	});

}( window.wp, jQuery ));
