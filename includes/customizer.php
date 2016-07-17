<?php
// Initializes/configures the Customizer with all customizable theme settings

// Base URL for Fonts API access
define( 'IV_FONT_API_BASE_URL', 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=' );
// Expiration time of cached font list (set to 1 week)
define( 'IV_FONT_LIST_EXPIRATION', WEEK_IN_SECONDS );

// Class for creating Customizer settings, panels, sections, and controls
class InterVarsity_Customize {

	function __construct( $wp_customize ) {

		$this->add_color_options( $wp_customize );
		$this->add_font_options( $wp_customize );
		$this->add_social_options( $wp_customize );
		$this->add_homepage_options( $wp_customize );
		$this->add_sg_options( $wp_customize );
		$this->add_special_page_options( $wp_customize );
		$this->add_footer_options( $wp_customize );

	}

	// Sanitizes a value by always returning a boolean; mostly used for checkbox
	// controls
	public function sanitize_boolean( $value ) {

		return (bool) $value;

	}

	// Sanitizes a value by always returning an integer; mostly used for page
	// dropdowns and image selection controls (as those return post IDs)
	public function sanitize_integer( $value ) {

		return (int) $value;

	}

	public function add_color_options( $wp_customize ) {

		$wp_customize->add_setting( 'iv_color_accent', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'default'           => IV_DEFAULT_ACCENT_COLOR,
			'sanitize_callback' => 'sanitize_hex_color'
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'iv_color_accent', array(
			'section'     => 'colors',
			'label'       => 'Accent Color',
			'description' => 'The accent color used for links, content box icons, small group headers, etc.'
		) ) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
			'section'     => 'colors',
			'label'       => 'Background Color',
			'description' => 'The background color for the entire page; a dark color is recommended. Note that you may need to update header and footer images to match.'
		) ) );

		$wp_customize->add_setting( 'iv_logo_bg_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_logo_bg_enabled', array(
			'section'     => 'colors',
			'type'        => 'checkbox',
			'label'       => 'Use accent color as logo background',
			'description' => 'When enabled, the background color behind the header image will be the chosen accent color. In effect, the accent color will "shine through" transparent regions of the image. This allows the header image to adapt to the current accent color.'
		) );

	}

	// Retrieves list of all available Google fonts via API; list is cached for
	// a period of time after initial retrieval (see IV_FONT_LIST_EXPIRATION)
	public function get_font_family_choices() {

		$choices = array();
		$families = get_transient( 'iv_font_families' );

		if ( ! empty( $families ) ) {
			// If font family list is in cache, use it

			foreach ( $families as $family ) {
				$choices[ $family ] = $family;
			}

		} else {
			// Otherwise, retrieve current font list from Google and cache it

			$api_key = get_theme_mod( 'iv_font_api_key' );
			if ( ! empty( $api_key ) ) {
				// API returns string of JSON data
				// Fetch it and parse it into a stdClass object
				$data = json_decode( wp_remote_retrieve_body( wp_remote_get( IV_FONT_API_BASE_URL . $api_key ) ) );
			}

			if ( ! empty( $data ) && ! empty( $data->items ) ) {

				// Add font families as dropdown menu choices
				foreach ( $data->items as $item ) {
					// Only add fonts which support the Latin character set
					if ( in_array( 'latin', $item->subsets ) ) {
						$choices[ $item->family ] = $item->family;
					}
				}

				set_transient( 'iv_font_families', array_values( $choices ), IV_FONT_LIST_EXPIRATION );

			} else {

				// If loading of all fonts fails, at least add options for
				// font family theme mods (and if those aren't set, use default
				// font families)

				// Function originally defined in styles.php
				$families = iv_get_font_family_theme_mods();

				$choices[ $families['primary'] ] = $families['primary'];
				$choices[ $families['accent'] ] = $families['accent'];

				// If default font families differ from font families set by
				// user, then display those also
				if ( IV_DEFAULT_PRIMARY_FONT_FAMILY !== $families['primary'] ) {
					$choices[ IV_DEFAULT_PRIMARY_FONT_FAMILY ] = IV_DEFAULT_PRIMARY_FONT_FAMILY;
				}
				if ( IV_DEFAULT_ACCENT_FONT_FAMILY !== $families['accent'] ) {
					$choices[ IV_DEFAULT_ACCENT_FONT_FAMILY ] = IV_DEFAULT_ACCENT_FONT_FAMILY;
				}

			}

		}

		return $choices;

	}

	// Checks if the given font family value exists within the fetched list of
	// valid font families; if it is not, the value will not be saved to the
	// database
	public function sanitize_font_family( $family ) {

		$font_family_choices = $this->get_font_family_choices();
		if ( ! empty( $font_family_choices[ $family ] ) ) {
			return $family;
		} else {
			wp_die( "Invalid font family: $family" );
		}

	}

	public function add_font_options( $wp_customize ) {

		$font_family_choices = $this->get_font_family_choices();

		$wp_customize->add_section( 'iv_font_options', array(
			'title'       => 'Fonts',
			// Place section between "Colors" and "Header Image" sections
			'priority'    => 50,
			'description' => 'This section allows you to customize the fonts used on the site. Visit <a href="https://www.google.com/fonts" target="_blank">Google Fonts</a> to browse through all available fonts.'
		) );

		$wp_customize->add_setting( 'iv_font_api_key', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field'
		) );
		$wp_customize->add_control( 'iv_font_api_key', array(
			'section'     => 'iv_font_options',
			'type'        => 'text',
			'label'       => 'Google Fonts API Key',
			'description' => 'The key which allows this theme to access Google Fonts. Watch <a href="https://youtu.be/mqI-dfysvxs" target="_blank">this video</a> to learn how to create one. When you do, paste it here, click "Save &amp; Publish", and refresh the page.'
		) );

		$wp_customize->add_setting( 'iv_font_primary_family', array(
			'type'      => 'theme_mod',
			'transport' => 'refresh',
			'default'   => IV_DEFAULT_PRIMARY_FONT_FAMILY,
			'sanitize_callback' => array( $this, 'sanitize_font_family' )
		) );
		$wp_customize->add_control( 'iv_font_primary_family', array(
			'section'     => 'iv_font_options',
			'type'        => 'select',
			'label'       => 'Primary Font Family',
			'choices'     => $font_family_choices,
			'description' => 'The font family used for body text, the site header/footer, etc.'
		) );

		$wp_customize->add_setting( 'iv_font_primary_thin', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_font_primary_thin', array(
			'section'     => 'iv_font_options',
			'type'        => 'checkbox',
			'label'       => 'Thin primary font',
			'description' => 'Use a thin font weight for the selected primary font (if available)'
		) );

		$wp_customize->add_setting( 'iv_font_accent_family', array(
			'type'      => 'theme_mod',
			'transport' => 'refresh',
			'default'   => IV_DEFAULT_ACCENT_FONT_FAMILY,
			'sanitize_callback' => array( $this, 'sanitize_font_family' )
		) );
		$wp_customize->add_control( 'iv_font_accent_family', array(
			'section'     => 'iv_font_options',
			'type'        => 'select',
			'label'       => 'Accent Font Family',
			'choices'     => $font_family_choices,
			'description' => 'The font family used for page and section headings (including small group titles)'
		) );

		$wp_customize->add_setting( 'iv_font_accent_bold', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_font_accent_bold', array(
			'section'     => 'iv_font_options',
			'type'        => 'checkbox',
			'label'       => 'Bold accent font',
			'description' => 'Use a bold font weight for the selected accent font (if available)'
		) );

	}

	public function add_social_options( $wp_customize ) {

		$wp_customize->add_panel( 'iv_social_panel', array(
			'title'       => 'Social Header',
			// Place panel just above "Menus" panel
			'priority'    => 100,
			'description' => 'This panel allows you to customize links and text in the social header below the site header image, to the right.'
		) );

		$wp_customize->add_section( 'iv_social_message_options', array(
			'title'       => 'Social Message',
			'description' => 'This section allows you to customize the message displayed to the left of the social header icons.',
			'panel'       => 'iv_social_panel'
		) );
		$wp_customize->add_setting( 'iv_social_message_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_social_message_enabled', array(
			'section' => 'iv_social_message_options',
			'type'    => 'checkbox',
			'label'   => 'Enabled'
		) );
		$wp_customize->add_setting( 'iv_social_message', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'default'           => 'Connect',
			'sanitize_callback' => 'sanitize_text_field'
		) );
		$wp_customize->add_control( 'iv_social_message', array(
			'section'     => 'iv_social_message_options',
			'type'        => 'text',
			'label'       => 'Social Message',
			'description' => 'The short message to display (e.g. "Connect")'
		) );

		$wp_customize->add_section( 'iv_facebook_panel', array(
			'title'       => 'Facebook',
			'description' => 'This section allows you to customize the Facebook link in the social header.',
			'panel'       => 'iv_social_panel'
		) );
		$wp_customize->add_setting( 'iv_facebook_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_facebook_enabled', array(
			'section' => 'iv_facebook_panel',
			'type'    => 'checkbox',
			'label'   => 'Enabled'
		) );
		$wp_customize->add_setting( 'iv_facebook_link', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'esc_url_raw'
		) );
		$wp_customize->add_control( 'iv_facebook_link', array(
			'section' => 'iv_facebook_panel',
			'type'    => 'url',
			'label'   => 'Link'
		) );

		$wp_customize->add_section( 'iv_twitter_panel', array(
			'title'       => 'Twitter',
			'description' => 'This section allows you to customize the Twitter link in the social header.',
			'panel'       => 'iv_social_panel'
		) );
		$wp_customize->add_setting( 'iv_twitter_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_twitter_enabled', array(
			'section' => 'iv_twitter_panel',
			'type'    => 'checkbox',
			'label'   => 'Enabled'
		) );
		$wp_customize->add_setting( 'iv_twitter_link', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'esc_url_raw'
		) );
		$wp_customize->add_control( 'iv_twitter_link', array(
			'section' => 'iv_twitter_panel',
			'type'    => 'url',
			'label'   => 'Link'
		) );

		$wp_customize->add_section( 'iv_instagram_panel', array(
			'title'       => 'Instagram',
			'description' => 'This section allows you to customize the Instagram link in the social header.',
			'panel'       => 'iv_social_panel'
		) );
		$wp_customize->add_setting( 'iv_instagram_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_instagram_enabled', array(
			'section' => 'iv_instagram_panel',
			'type'    => 'checkbox',
			'label'   => 'Enabled'
		) );
		$wp_customize->add_setting( 'iv_instagram_link', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'esc_url_raw'
		) );
		$wp_customize->add_control( 'iv_instagram_link', array(
			'section' => 'iv_instagram_panel',
			'type'    => 'url',
			'label'   => 'Link'
		) );

		$wp_customize->add_section( 'iv_email_options', array(
			'title'       => 'Email',
			'description' => 'This section allows you to customize the site email link in the social header.',
			'panel'       => 'iv_social_panel'
		) );
		$wp_customize->add_setting( 'iv_email_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_email_enabled', array(
			'section' => 'iv_email_options',
			'type'    => 'checkbox',
			'label'   => 'Enabled'
		) );
		$wp_customize->add_setting( 'iv_email_address', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_email',
			'default'           => get_bloginfo( 'admin_email' )
		) );
		$wp_customize->add_control( 'iv_email_address', array(
			'section' => 'iv_email_options',
			'type'    => 'email',
			'label'   => 'Email Address'
		) );

		$wp_customize->selective_refresh->add_partial( 'iv_social', array(
			'settings'         => array(
				'iv_social_message_enabled',
				'iv_facebook_enabled',
				'iv_twitter_enabled',
				'iv_instagram_enabled',
				'iv_email_enabled'
			),
			'selector'         => '#site-header-social ul',
			'render_callback'  => 'iv_social_header_icons',
			'fallback_refresh' => true
		) );

	}

	public $home_box_icon_choices = array(
		''          => '(no icon)',
		'book'      => 'Book',
		'cross'     => 'Cross',
		'group'     => 'Group',
		'globe'     => 'Globe',
		'facebook'  => 'Facebook',
		'twitter'   => 'Twitter',
		'instagram' => 'Instagram',
		'email'     => 'Email',
		'search'    => 'Search',
		'quote'     => 'Quote'
	);

	// Similar to sanitize_font_family() in that this checks if the given value
	// exists in the list of valid values (in this case, the list of possible
	// icon choices)
	public function sanitize_home_box_icon( $icon ) {

		if ( ! empty( $this->home_box_icon_choices[ $icon ] ) ) {
			return $icon;
		} else {
			wp_die( "Invalid icon: $icon" );
		}

	}

	public function add_homepage_options( $wp_customize ) {

		$wp_customize->add_panel( 'iv_home_panel', array(
			'title'       => 'Front Page',
			// Place panel beneath "Menus" panel
			'priority'    => 105,
			'description' => 'This panel allows you to customize the content boxes which appear on the front page. The theme allows for up to three content boxes.'
		) );

		$wp_customize->add_section( 'iv_home_general_options', array(
			'panel'       => 'iv_home_panel',
			'title'       => 'General',
			// Place section above all other sections in "Homepage" panel
			'priority'    => 20,
			'description' => 'This section allows you to customize general display options pertaining to the front page.'
		) );

		$wp_customize->add_setting( 'iv_home_page_header_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'default'           => IV_DEFAULT_HOME_PAGE_HEADER_ENABLED,
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_home_page_header_enabled', array(
			'section'     => 'iv_home_general_options',
			'type'        => 'checkbox',
			'label'       => 'Show page header',
			'description' => 'When disabled, hides the page header and title on the front page'
		) );

		$wp_customize->add_setting( 'iv_num_home_posts', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_integer' ),
			'default'           => IV_DEFAULT_NUM_HOME_POSTS
		) );
		$wp_customize->add_control( 'iv_num_home_posts', array(
			'section'     => 'iv_home_general_options',
			'type'        => 'select',
			'label'       => 'Blog posts to show on static front page',
			'description' => 'The number of recent blog posts to show on the front page. This setting only takes effect when the front page is set to display a static page (see the "Static Front Page" section).',
			'choices'     => array(
				'0'  => '0',
				'4'  => '4',
				'6'  => '6',
				'8'  => '8',
				'10' => '10'
			)
		) );

		for ( $i = 1; $i <= IV_NUM_HOME_BOXES; $i += 1 ) {
			$this->add_home_box_settings( $wp_customize, $i );
		}

		// Move "Static Front Page" section under "Homepage" panel
		$static_front_page = $wp_customize->get_section( 'static_front_page' );
		$static_front_page->panel = 'iv_home_panel';
		// Place section beneath "Content Box" sections of "Homepage" panel
		$static_front_page->priority = 60;

	}

	// Add the necessary section, settings, and controls for a home content box
	// with the given index (this method is used within a loop for the sake of
	// code reuse)
	public function add_home_box_settings( $wp_customize, $index ) {

		$id_base = "iv_home_box{$index}";

		$wp_customize->add_section( "{$id_base}_options", array(
			'panel'       => 'iv_home_panel',
			'title'       => "Content Box $index",
			// Place section beneath "General" section in "Homepage" panel
			'priority'    => 40,
			'description' => "This section allows you to customize content box $index on the front page."
		) );

		$wp_customize->add_setting( "{$id_base}_enabled", array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'default'           => IV_DEFAULT_HOME_BOX_ENABLED,
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( "{$id_base}_enabled", array(
			'section' => "{$id_base}_options",
			'type'    => 'checkbox',
			'label'   => 'Enabled'
		) );

		$wp_customize->add_setting( "{$id_base}_icon", array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_home_box_icon' )
		) );
		$wp_customize->add_control( "{$id_base}_icon", array(
			'section' => "{$id_base}_options",
			'type'    => 'select',
			'label'   => 'Icon',
			'choices' => $this->home_box_icon_choices
		) );

		$wp_customize->add_setting( "{$id_base}_title", array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_text_field'
		) );
		$wp_customize->add_control( "{$id_base}_title", array(
			'section' => "{$id_base}_options",
			'type'    => 'text',
			'label'   => 'Title'
		) );

		$wp_customize->add_setting( "{$id_base}_desc", array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_text_field'
		) );
		$wp_customize->add_control( "{$id_base}_desc", array(
			'section' => "{$id_base}_options",
			'type'    => 'textarea',
			'label'   => 'Description'
		) );

		$wp_customize->add_setting( "{$id_base}_page", array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_integer' )
		) );
		$wp_customize->add_control( "{$id_base}_page", array(
			'section' => "{$id_base}_options",
			'type'    => 'dropdown-pages',
			'label'   => 'Page to link to'
		) );

	}

	public function add_sg_options( $wp_customize ) {

		$wp_customize->add_panel( 'iv_sg_panel', array(
			'title'       => 'Small Groups',
			// Place panel beneath "Homepage" panel
			'priority'    => 110,
			'description' => 'This panel allows you to customize all settings pertaining to small groups.'
		) );

		$this->add_general_sg_options( $wp_customize );
		$this->add_related_sg_options( $wp_customize );

	}

	public function add_general_sg_options( $wp_customize ) {

		$wp_customize->add_section( 'iv_sg_general_options', array(
			'title'       => 'General',
			'description' => 'This section allows you to customize general settings pertaining to small groups.',
			'panel'       => 'iv_sg_panel'
		) );

		$wp_customize->add_setting( 'iv_sg_index_page', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_integer' )
		) );
		$wp_customize->add_control( 'iv_sg_index_page', array(
			'section'     => 'iv_sg_general_options',
			'type'        => 'dropdown-pages',
			'label'       => 'Small Group Index Page',
			'description' => 'The main index page where campuses with small groups are listed'
		) );

		$wp_customize->add_setting( 'iv_sgs_per_page', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_integer' ),
			'default'           => IV_DEFAULT_SGS_PER_PAGE
		) );
		$wp_customize->add_control( 'iv_sgs_per_page', array(
			'section'     => 'iv_sg_general_options',
			'type'        => 'select',
			'label'       => 'Small Groups Per Page',
			'description' => 'The maximum number of small groups to show per page',
			'choices'     => array(
				'4'  => '4',
				'6'  => '6',
				'8'  => '8',
				'10' => '10'
			)
		) );

	}

	public function add_related_sg_options( $wp_customize ) {

		$wp_customize->add_section( 'iv_related_sg_options', array(
			'title'       => 'Related Small Groups',
			'description' => 'This section allows you to customize settings pertaining to the related small groups feature.',
			'panel'       => 'iv_sg_panel'
		) );

		$wp_customize->add_setting( 'iv_related_sgs_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_boolean' ),
			'default'           => IV_DEFAULT_RELATED_SGS_ENABLED
		) );
		$wp_customize->add_control( 'iv_related_sgs_enabled', array(
			'section' => 'iv_related_sg_options',
			'type'    => 'checkbox',
			'label'   => 'List related small groups',
			'description' => 'When enabled, a list of related small groups will appear below any individual small group'
		) );

		$wp_customize->add_setting( 'iv_max_related_sgs', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => array( $this, 'sanitize_integer' ),
			'default'           => IV_DEFAULT_MAX_RELATED_SGS
		) );
		$wp_customize->add_control( 'iv_max_related_sgs', array(
			'section'     => 'iv_related_sg_options',
			'type'        => 'select',
			'label'       => 'Maximum Related Small Groups',
			'description' => 'The maximum number of related small groups to show for any individual small group',
			'choices'     => array(
				'4'  => '4',
				'6'  => '6',
				'8'  => '8',
				'10' => '10'
			)
		) );

	}

	public function add_special_page_options( $wp_customize ) {

		$wp_customize->add_panel( 'iv_special_page_panel', array(
			'title'       => 'Special Pages',
			// Place panel beneath "Small Groups" panel
			'priority'    => 115,
			'description' => 'This panel allows you to customize headings and messages for various special pages.'
		) );

		$this->add_no_sg_options( $wp_customize );
		$this->add_search_null_options( $wp_customize );
		$this->add_404_options( $wp_customize );

	}

	public function add_no_sg_options( $wp_customize ) {

		$wp_customize->add_section( 'iv_no_sg_options', array(
			'title'       => 'No Small Groups Listed',
			'description' => 'This section allows you to customize headings and messages for campus pages with no small groups listed.',
			'panel'       => 'iv_special_page_panel'
		) );

		$wp_customize->add_setting( 'iv_no_sg_heading', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => IV_DEFAULT_NO_SG_HEADING
		) );
		$wp_customize->add_control( 'iv_no_sg_heading', array(
			'section' => 'iv_no_sg_options',
			'type'    => 'text',
			'label'   => 'Heading'
		) );

		$wp_customize->add_setting( 'iv_no_sg_message', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_kses_post',
			'default'           => IV_DEFAULT_NO_SG_MESSAGE
		) );
		$wp_customize->add_control( 'iv_no_sg_message', array(
			'section'     => 'iv_no_sg_options',
			'type'        => 'textarea',
			'label'       => 'Message',
			'description' => 'Type {{campus}} to insert the current campus name'
		) );

	}

	public function add_search_null_options( $wp_customize ) {

		$wp_customize->add_section( 'iv_search_null_options', array(
			'title'       => 'No Small Groups Found',
			'description' => 'This section allows you to customize headings and messages for search result pages where no small groups were found.',
			'panel'       => 'iv_special_page_panel'
		) );

		$wp_customize->add_setting( 'iv_search_null_heading', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => IV_DEFAULT_NULL_SEARCH_HEADING
		) );
		$wp_customize->add_control( 'iv_search_null_heading', array(
			'section' => 'iv_search_null_options',
			'type'    => 'text',
			'label'   => 'Heading'
		) );

		$wp_customize->add_setting( 'iv_search_null_message', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_kses_post',
			'default'           => IV_DEFAULT_NULL_SEARCH_MESSAGE
		) );
		$wp_customize->add_control( 'iv_search_null_message', array(
			'section'     => 'iv_search_null_options',
			'type'        => 'textarea',
			'label'       => 'Message',
			'description' => 'A search box will appear below this message'
		) );

	}

	public function add_404_options( $wp_customize ) {

		$wp_customize->add_section( 'iv_404_options', array(
			'title'       => 'Page Not Found',
			'description' => 'This section allows you to customize headings and messages for 404 "Page Not Found" pages.',
			'panel'       => 'iv_special_page_panel'
		) );

		$wp_customize->add_setting( 'iv_404_heading', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => IV_DEFAULT_404_HEADING
		) );
		$wp_customize->add_control( 'iv_404_heading', array(
			'section' => 'iv_404_options',
			'type'    => 'text',
			'label'   => 'Heading'
		) );

		$wp_customize->add_setting( 'iv_404_message', array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_kses_post',
			'default'           => IV_DEFAULT_404_MESSAGE
		) );
		$wp_customize->add_control( 'iv_404_message', array(
			'section'     => 'iv_404_options',
			'type'        => 'textarea',
			'label'       => 'Message',
			'description' => 'A search box will appear below this message'
		) );

	}

	public function add_footer_options( $wp_customize ) {

		$wp_customize->add_panel( 'iv_footer_panel', array(
			'title'       => 'Footer',
			// Place panel beneath "404 Page" section
			'priority'    => 120,
			'description' => 'This panel allows you to customize links and text in the site footer.'
		) );

		$this->add_ivcf_link_options( $wp_customize );
		$this->add_copyright_text_options( $wp_customize );

		$wp_customize->selective_refresh->add_partial( 'iv_footer', array(
			'settings'         => array(
				'iv_footer_ivcf_enabled',
				'iv_footer_ivcf_image',
				'iv_footer_copyright_enabled',
				'iv_footer_copyright_text'
			),
			'selector'         => '#site-footer-content',
			'render_callback'  => 'iv_footer_content',
			'fallback_refresh' => true
		) );

	}

	public function add_ivcf_link_options( $wp_customize ) {

		$wp_customize->add_section( 'iv_footer_ivcf_options', array(
			'title'       => 'IVCF Website Link',
			'description' => 'This section allows you to customize the link to the national InterVarsity Christian Fellowship/USA website in the site footer.',
			'panel'       => 'iv_footer_panel'
		) );

		$wp_customize->add_setting( 'iv_footer_ivcf_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_footer_ivcf_enabled', array(
			'section' => 'iv_footer_ivcf_options',
			'type'    => 'checkbox',
			'label'   => 'Show link to IVCF website'
		) );

		$wp_customize->add_setting( 'iv_footer_ivcf_link', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => 'http://intervarsity.org/'
		) );
		$wp_customize->add_control( 'iv_footer_ivcf_link', array(
			'section'     => 'iv_footer_ivcf_options',
			'type'        => 'url',
			'label'       => 'IVCF Website Link',
			'description' => 'The web address of the InterVarsity Christian Fellowship/USA website'
		) );

		$wp_customize->add_setting( 'iv_footer_ivcf_text', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => IV_DEFAULT_FOOTER_IVCF_TEXT
		) );
		$wp_customize->add_control( 'iv_footer_ivcf_text', array(
			'section'     => 'iv_footer_ivcf_options',
			'label'       => 'IVCF Website Link Text',
			'description' => 'The text used for the IVCF link (if no image is set)'
		) );

		$wp_customize->add_setting( 'iv_footer_ivcf_image', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_integer' )
		) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'iv_footer_ivcf_image', array(
			'section'     => 'iv_footer_ivcf_options',
			'label'       => 'IVCF Image',
			'mime_type'   => 'image',
			'description' => 'The image used for the IVCF link (this shows in place of the chosen link text)'
		) ) );

	}

	public function add_copyright_text_options( $wp_customize ) {

		$wp_customize->add_section( 'iv_footer_copyright_options', array(
			'title'       => 'Copyright Text',
			'description' => 'This section allows you to customize the copyright text in the site footer.',
			'panel'       => 'iv_footer_panel'
		) );

		$wp_customize->add_setting( 'iv_footer_copyright_enabled', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( $this, 'sanitize_boolean' )
		) );
		$wp_customize->add_control( 'iv_footer_copyright_enabled', array(
			'section' => 'iv_footer_copyright_options',
			'type'    => 'checkbox',
			'label'   => 'Show copyright text'
		) );

		$wp_customize->add_setting( 'iv_footer_copyright_text', array(
			'type'              => 'theme_mod',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wp_kses_post'
		) );
		$wp_customize->add_control( 'iv_footer_copyright_text', array(
			'section'     => 'iv_footer_copyright_options',
			'type'        => 'textarea',
			'label'       => 'Copyright Text',
			'description' => 'Type &amp;copy; to insert a copyright symbol. Type {{year}} to insert the current year.'
		) );

	}

}
