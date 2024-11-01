<?php 
/*
Plugin Name: Text Mutator
Plugin URI: http://blfriedman.com/text-mutator
Description: Highlight, redact, style and modify text blocks
Version: 0.5
Author: BL Friedman Creative
Author URI: http://blfriedman.com
*/
/**
 * Main Class - 
 */
define('TXT_MUTR_DYNAMICSCRIPTVERSION', '0.1.1');

class TXT_MUTR_Theme_Options {
  
    /*--------------------------------------------*
     * Attributes
     *--------------------------------------------*/
  
    /** Refers to a single instance of this class. */
    private static $instance = null;
     
    /* Saved options */
    public $options;
  
    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/
  
    /**
     * Creates or returns an instance of this class.
     *
     * @return  TXT_MUTR_Theme_Options A single instance of this class.
     */
    public static function txt_mutr_get_instance() {
  
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
  
        return self::$instance;
  
    } // end txt_mutr_get_instance;
  
	
	/**
     * $shortcode_tag
     * holds the name of the shortcode tag
     * @var string
     */
    public $shortcode_tag = 'text_mutator';

	
	
	
	
    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     */
    private function __construct() { 
 
		// Add the page to the admin menu
		add_action( 'admin_menu', array( &$this, 'txt_mutr_add_page' ) );

		// Register page options
		add_action( 'admin_init', array( &$this, 'txt_mutr_register_page_options') );

		// Css rules for Color Picker
		wp_enqueue_style( 'wp-color-picker' );
		
		
		//Register Core CSS
		//add_action( 'wp_enqueue_scripts', array( $this, 'text_mutator_css' ) );
		
		//Register Dynamic CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'txt_mutr_custom_css' ) );
		add_action( 'wp_ajax_dynamic_css_action', array( $this, 'txt_mutr_dynamic_css_loader' ) );
		add_action( 'wp_ajax_nopriv_dynamic_css_action', array( $this, 'txt_mutr_dynamic_css_loader' ) );
		
		
		// Register javascript
		add_action('admin_enqueue_scripts', array( $this, 'txt_mutr_enqueue_admin_js' ) );
		

		// Get registered option
		$this->options = get_option( 'tm_settings_options' );
		
		//Add shortcode
		add_shortcode( $this->shortcode_tag, array( $this, 'txt_mutr_shortcode_display' ) );
		
	}

  
    /*--------------------------------------------*
     * Functions
     *--------------------------------------------*/
	
	
	/**
	* Function that will display shortcode using options
	**/
	public function txt_mutr_shortcode_display( $atts, $content = "") {
		$class = 'text-mutator';
		if($this->options['txt_strike']) $class .= ' strike1';
		$textclass = 'text-mutator-text';
		$textcontent = $content;
		if($this->options['title']) $textcontent = $this->options['title'];
		if($this->options['txt_jumble']) $textcontent = str_shuffle($content);
		return "<span class='$class'><span class='$textclass'>$textcontent</span></span>";
	}
	
      
    /*** Function that will add the options page under Setting Menu.
 */
	public function txt_mutr_add_page() { 
		// $page_title, $menu_title, $capability, $menu_slug, $callback_function
		add_options_page( 'Text Mutator', 'Text Mutator Settings', 'manage_options', __FILE__, array( $this, 'txt_mutr_display_page' ) );
	}
 
   
	/**
	 * Function that will display the options page.
	 */
	public function txt_mutr_display_page() { 
		?>
		<div class="wrap">
			<h2>Text Mutator</h2>
			<h3>Usage</h3>
			<p>[text_mutator]Sample phrase[/text_mutator]</p>
			<p><em>Use shortcode in posts and pages to apply changes.</em></p>
			<hr/>
			
			<form method="post" action="options.php">     
			<?php 
				settings_fields(__FILE__);      
				do_settings_sections(__FILE__);
				submit_button();
			?>
			</form>
		</div> <!-- /wrap -->
		<?php    
	}


	
	   /**
	 * Function that will register admin page options.
	 */
	public function txt_mutr_register_page_options() { 

		// Add Section for option fields
		add_settings_section( 'tm_section', 'Options', array( $this, 'txt_mutr_display_section' ), __FILE__ ); // id, title, display cb, page

		// Add Text Strikeout Option
		add_settings_field( 'tm_txt_strike_field', 'Strikeout', array( $this, 'txt_mutr_txt_strike_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Text Color Field
		add_settings_field( 'tm_txt_color_field', 'Text Color', array( $this, 'txt_mutr_txt_color_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Selection Color Field
		add_settings_field( 'tm_select_color_field', 'Selection Text Color', array( $this, 'txt_mutr_select_color_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Selection Background Color Field
		add_settings_field( 'tm_select_bgd_color_field', 'Selection Background Color', array( $this, 'txt_mutr_select_bgd_color_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Link Color Field
		add_settings_field( 'tm_link_color_field', 'Link Color', array( $this, 'txt_mutr_link_color_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Link Hover Color Field
		add_settings_field( 'tm_link_hover_color_field', 'Link Hover Color', array( $this, 'txt_mutr_link_hover_color_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Background Image Field
		add_settings_field( 'tm_bg_image', 'Background Image', array( $this, 'txt_mutr_bg_image_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Background Color Field
		add_settings_field( 'tm_bg_field', 'Background Color', array( $this, 'txt_mutr_bg_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Add Title Field
		add_settings_field( 'tm_title_field', 'Text Replace', array( $this, 'txt_mutr_title_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section

		// Add Text Jumble Option
		add_settings_field( 'tm_txt_jumble_field', 'Text Jumble', array( $this, 'txt_mutr_txt_jumble_settings_field' ), __FILE__, 'tm_section' ); // id, title, display cb, page, section
		
		// Register Settings
		register_setting( __FILE__, 'tm_settings_options', array( $this, 'txt_mutr_validate_options' ) ); // option group, option name, sanitize cb 
	}
    
     
		/**
	 * Function that will validate all fields.
	 */
	public function txt_mutr_validate_options( $fields ) { 

		$valid_fields = array();

		// Validate Custom Text
		$title = trim( $fields['title'] );
		$valid_fields['title'] = strip_tags( stripslashes( $title ) );

		// Validate Background Color
		$background = trim( $fields['background'] );
		$background = strip_tags( stripslashes( $background ) );
		
		//Validate Background Image 1
		$background_img = trim( $fields['background_img'] );
		$valid_fields['background_img'] = $background_img;
		
		
		// Validate Text Color
		$txt_color = trim( $fields['txt_color'] );
		$txt_color = strip_tags( stripslashes( $txt_color ) );
		
		// Validate Select Color
		$select_color = trim( $fields['select_color'] );
		$select_color = strip_tags( stripslashes( $select_color ) );
		
		// Validate Select Background Color
		$select_bgd_color = trim( $fields['select_bgd_color'] );
		$select_bgd_color = strip_tags( stripslashes( $select_bgd_color ) );
		
		// Validate Link Color
		$link_color = trim( $fields['link_color'] );
		$link_color = strip_tags( stripslashes( $link_color ) );
		
		// Validate Link Hover Color
		$link_hover_color = trim( $fields['link_hover_color'] );
		$link_hover_color = strip_tags( stripslashes( $link_hover_color ) );
		
		
		
		
		
		//Validate Checkboxes
		$txt_strike = trim( $fields['txt_strike'] );
		$valid_fields['txt_strike'] = $txt_strike;
		
		$txt_jumble = trim( $fields['txt_jumble'] );
		$valid_fields['txt_jumble'] = $txt_jumble;
		
		
		
		// Check if background color is a valid hex color
		if( FALSE === $this->txt_mutr_check_color( $background )) {
			//set background to transparent
			$background = 'transparent';
			$valid_fields['background'] = $background;

		} else {

			$valid_fields['background'] = $background;  

		}

		
		// Check if txt color is a valid hex color
		if( FALSE === $this->txt_mutr_check_color( $txt_color )) {

			$txt_color = '';
			$valid_fields['txt_color'] = $txt_color;

		} else {

			$valid_fields['txt_color'] = $txt_color;  

		}
		
		
		// Check if select color is a valid hex color
		if( FALSE === $this->txt_mutr_check_color( $select_color )) {

			$select_color = '';
			$valid_fields['select_color'] = $select_color;

		} else {

			$valid_fields['select_color'] = $select_color;  

		}
		
		
		// Check if select background color is a valid hex color
		if( FALSE === $this->txt_mutr_check_color( $select_bgd_color )) {

			$select_bgd_color = '';
			$valid_fields['select_bgd_color'] = $select_bgd_color;

		} else {

			$valid_fields['select_bgd_color'] = $select_bgd_color;  

		}
		
		
		// Check if link color is a valid hex color
		if( FALSE === $this->txt_mutr_check_color( $link_color )) {

			$link_color = '';
			$valid_fields['link_color'] = $link_color;

		} else {

			$valid_fields['link_color'] = $link_color;  

		}
		
		// Check if link hover color is a valid hex color
		if( FALSE === $this->txt_mutr_check_color( $link_hover_color )) {

			$link_hover_color = '';
			$valid_fields['link_hover_color'] = $link_hover_color;

		} else {

			$valid_fields['link_hover_color'] = $link_hover_color;  

		}
		
		
		
		return apply_filters( 'txt_mutr_validate_options', $valid_fields, $fields);
	}
 
	
	
	
     
	   /**
	 * Function that will check if value is a valid HEX color.
	 */
	public function txt_mutr_check_color( $value ) { 

		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #     
			return true;
		}

		return false;
	}

     
    /**
     * Callback function for settings section
     */
    public function txt_mutr_display_section() { /* Leave blank */ } 
     
	
	
	   /**
	 * Functions that display the fields.
	 */
	public function txt_mutr_title_settings_field() { 

		$val = ( isset( $this->options['title'] ) ) ? $this->options['title'] : '';
		echo '<input type="text" name="tm_settings_options[title]" value="' . $val . '" />';
		echo '<p><em>Replace the enclosed text with this text.</em></p>';
	}   

	public function txt_mutr_bg_settings_field() { 

		$val = ( isset( $this->options['background'] ) ) ? $this->options['background'] : '';
		echo '<input type="text" name="tm_settings_options[background]" value="' . $val . '" class="st-color-picker" >';
		echo '<p><em>Change the background color. Leave alone to use transparent.</em></p>';

	}
	
	public function txt_mutr_bg_image_field() { 
		$checked1 = '';
		$checked2 = '';
		$checked3 = '';
		$checked4 = '';
		$checked5 = '';
		if ( $this->options['background_img'] == null) :
			$checked1 = 'checked';
			$checked2 = '';
			$checked3 = '';
			$checked4 = '';
			$checked5 = '';
		elseif ( $this->options['background_img'] == 'none') : 
			$checked1 = 'checked';
		elseif ( $this->options['background_img'] == 'marker') : 
			$checked2 = 'checked';
		elseif ( $this->options['background_img'] == 'highlighter') : 
			$checked3 = 'checked';
		elseif ( $this->options['background_img'] == 'mutate_black') : 
			$checked4 = 'checked';
		elseif ( $this->options['background_img'] == 'mutate_white') : 
			$checked5 = 'checked';
		endif;
	
		echo '<input type="radio" id="none" value="none" name="tm_settings_options[background_img]" ' . $checked1 . '/><label for = "none">None</label>';
		echo '<input type="radio" id="mutate_black" value="mutate_black" name="tm_settings_options[background_img]" ' . $checked4 . '/><label for = "mutate_black">Mutate Black</label>';
		echo '<input type="radio" id="mutate_white" value="mutate_white" name="tm_settings_options[background_img]" ' . $checked5 . '/><label for = "mutate_white">Mutate White</label>';
		echo '<input type="radio" id="marker" value="marker" name="tm_settings_options[background_img]" ' . $checked2 . '/><label for = "marker">Black Marker (Redaction)</label>';
		echo '<input type="radio" id="highlighter" value="highlighter" name="tm_settings_options[background_img]" ' . $checked3 . '/><label for = "highlighter">Yellow Highlighter</label>';
		
		echo '<p><em>Add a background image behind the text.</em></p>';

	}
	
	public function txt_mutr_txt_color_settings_field() { 

		$val = ( isset( $this->options['txt_color'] ) ) ? $this->options['txt_color'] : '';
		echo '<input type="text" name="tm_settings_options[txt_color]" value="' . $val . '" class="st-color-picker" >';
		echo '<p><em>Change the text color. Leave alone to inherit text color of active theme.</em></p>';

	}
	
	public function txt_mutr_select_color_settings_field() { 

		$val = ( isset( $this->options['select_color'] ) ) ? $this->options['select_color'] : '';
		echo '<input type="text" name="tm_settings_options[select_color]" value="' . $val . '" class="st-color-picker" >';
		echo '<p><em>Change the selection text color. Leave alone to inherit selection text color of active theme.</em></p>';

	}
	
	public function txt_mutr_select_bgd_color_settings_field() { 

		$val = ( isset( $this->options['select_bgd_color'] ) ) ? $this->options['select_bgd_color'] : '';
		echo '<input type="text" name="tm_settings_options[select_bgd_color]" value="' . $val . '" class="st-color-picker" >';
		echo '<p><em>Change the selection background color. Leave alone to inherit selection background color of active theme or browser.</em></p>';

	}

	public function txt_mutr_link_color_settings_field() { 

		$val = ( isset( $this->options['link_color'] ) ) ? $this->options['link_color'] : '';
		echo '<input type="text" name="tm_settings_options[link_color]" value="' . $val . '" class="st-color-picker" >';
		echo '<p><em>Change the link color. Leave alone to inherit link color of active theme.</em></p>';

	}
	
	public function txt_mutr_link_hover_color_settings_field() { 

		$val = ( isset( $this->options['link_hover_color'] ) ) ? $this->options['link_hover_color'] : '';
		echo '<input type="text" name="tm_settings_options[link_hover_color]" value="' . $val . '" class="st-color-picker" >';
		echo '<p><em>Change the link hover color. Leave alone to inherit link_hover color of active theme.</em></p>';

	}
	

	public function txt_mutr_txt_strike_settings_field() { 

		$checked = '';
		if ( $this->options['txt_strike'] == false) :
			$checked = '';
		else: 
			$checked = 'checked';
		endif;
		echo '<input type="checkbox" name="tm_settings_options[txt_strike]"' . $checked .  '/>';
		echo '<p><em>Add a single line through the center of the text. Inherits color from theme text color.</em></p>';

	}
	
	public function txt_mutr_txt_jumble_settings_field() { 
		$checked = '';
		if ( $this->options['txt_jumble'] == false) :
			$checked = '';
		else: 
			$checked = 'checked';
		endif;
		echo '<input type="checkbox" name="tm_settings_options[txt_jumble]"' . $checked .  '/>';
		echo '<p><em>Rearrange the letters and spaces of the enclosed text.</em></p>';

	}
	
	
	/**
	 * Function that will add javascript file for Color Piker.
	 */
	public function txt_mutr_enqueue_admin_js() { 

		// Make sure to add the wp-color-picker dependecy to js file
		wp_enqueue_script( 'tm_custom_js', plugins_url( 'js/text-mutator.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );
	}

	
	public function txt_mutr_custom_css() {

		wp_enqueue_style(
			'dynamic-css', //handle
			admin_url( 'admin-ajax.php' ) . '?action=dynamic_css_action&wpnonce=' . wp_create_nonce( 'dynamic-css-nonce' ), // src
			array(), // dependencies
			TXT_MUTR_DYNAMICSCRIPTVERSION // version number
    	);
	}
	
	public function txt_mutr_dynamic_css_loader() {
    	$nonce = $_REQUEST['wpnonce'];
    	if ( ! wp_verify_nonce( $nonce, 'dynamic-css-nonce' ) ) {
			die( 'invalid nonce' );
		} else {
        	require_once dirname( __FILE__ ) . '/css/text-mutator-css.php';
		}
		exit;
	}
		
	
	
} // end class
  
TXT_MUTR_Theme_Options::txt_mutr_get_instance();	

?>