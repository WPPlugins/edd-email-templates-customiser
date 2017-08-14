<?php
/**
 * Plugin Name: EDD Email Templates Customiser
 * Plugin URI: http://wordpress.org/extend/plugins/edd-email-templates-customiser
 * Description: An add-on for Easy Digital Downloads to customise the styling of the default email template.
 * Author: Sunny Ratilal
 * Version: 1.2
 * Text Domain: edd_etc
 * Domain Path: languages
 *
 * EDD Email Templates Customiser is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * EDD Email Templates Customiser is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EDD Email Templates Customiser.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EDD Email Templates Customiser
 * @category Core
 * @author Sunny Ratilal
 * @version 1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Email_Templates_Customiser' ) ) :

/**
 * EDD_Email_Templates_Customiser Class
 *
 * @package	EDD_Email_Templates_Customiser
 * @since	1.0
 * @version	1.2
 * @author 	Sunny Ratilal
 */
final class EDD_Email_Templates_Customiser {
	/**
	 * EDD Email Templates Customiser uses many variables, several of which can be filtered to
	 * customize the way it operates. Most of these variables are stored in a
	 * private array that gets updated with the help of PHP magic methods.
	 *
	 * @var array
	 * @see EDD_Email_Templates_Customiser::setup_globals()
	 * @since 1.2
	 */
	private $data;

	/**
	 * Holds the instance
	 *
	 * Ensures that only one instance of EDD Reviews exists in memory at any one
	 * time and it also prevents needing to define globals all over the place.
	 * 
	 * TL;DR This is a static property property that holds the singleton instance.
	 *
	 * @var object
	 * @static
	 * @since 1.2
	 */
	private static $instance;

	/**
	 * Boolean whether or not to use the singleton, comes in handy
	 * when doing testing
	 * 
	 * @var bool
	 * @static
	 * @since 1.2
	 */
	public static $testing = false;

	/**
	 * Holds the version number
	 *
	 * @var string
	 * @since 1.2
	 */
	public $version = '1.2';

	/**
	 * Get the instance and store the class inside it. This plugin utilises
	 * the PHP singleton design pattern.
	 *
	 * @since 1.2
	 * @static
	 * @staticvar array $instance
	 * @access public
	 * @see edd_email_templates_customiser();
	 * @uses EDD_Email_Templates_Customiser::setup_globals() Setup the globals needed
	 * @uses EDD_Email_Templates_Customiser::hooks() Setup hooks and actions
	 * @return object self::$instance Instance
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Email_Templates_Customiser ) || self::$testing ) {
			self::$instance = new EDD_Email_Templates_Customiser;
			self::$instance->setup_globals();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Constructor Function
	 *
	 * @since 1.2
	 * @access protected
	 * @see EDD_Reviews::init()
	 * @see EDD_Reviews::activation()
	 */
	public function __construct() {
		self::$instance = $this;

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Sets up the constants/globals used
	 *
	 * @since 1.2
	 * @static
	 * @access public
	 */
	private function setup_globals() {
		// File Path and URL Information
		$this->file          = __FILE__;
		$this->basename      = apply_filters( 'edd_reviews_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_url    = plugin_dir_url( __FILE__ );
		$this->plugin_path   = plugin_dir_path( __FILE__ );
		$this->lang_dir      = apply_filters( 'edd_etc_lang_dir', trailingslashit( $this->plugin_path . 'languages' ) );
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.2
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'edd-reviews' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.2
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'edd-reviews' ), '1.0' );
	}

	/**
	 * Magic method for checking if custom variables have been set
	 *
	 * @since 1.2
	 * @access protected
	 * @return void
	 */
	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}

	/**
	 * Magic method for getting variables
	 *
	 * @since 1.2
	 * @access protected
	 * @return void
	 */
	public function __get( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}

	/**
	 * Magic method for setting variables
	 *
	 * @since 1.2
	 * @access protected
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->data[ $key ] = $value;
	}

	/**
	 * Magic method for unsetting variables
	 *
	 * @since 1.2
	 * @access protected
	 * @return void
	 */
	public function __unset( $key ) {
		if ( isset( $this->data[ $key ] ) )
			unset( $this->data[ $key ] );
	}

	/**
	 * Magic method to prevent notices and errors from invalid method calls
	 *
	 * @since 1.2
	 * @access public
	 *
	 * @param string $name
	 * @param array $args 
	 *
	 * @return void
	 */
	public function __call( $name = '', $args = array() ) {
		unset( $name, $args );
		return null;
	}

	/**
	 * Reset the instance of the class
	 *
	 * @since 1.2
	 * @access public
	 * @static
	 */
	public static function reset() {
		self::$instance = null;
	}

	/**
	 * Function fired on init
	 *
	 * This function is called on WordPress 'init'. It's triggered from the
	 * constructor function.
	 *
	 * @since 1.2
	 * @access public
	 *
	 * @uses EDD_Email_Templates_Customiser::load_plugin_textdomain()
	 *
	 * @return void
	 */
	public function init() {
		do_action( 'edd_etc_before_init' );

		$this->load_plugin_textdomain();

		do_action( 'edd_etc_after_init' );
	}

	/**
	 * Load Plugin Text Domain
	 *
	 * Looks for the plugin translation files in certain directories and loads
	 * them to allow the plugin to be localised
	 *
	 * @since 1.2
	 * @access public
	 * @return bool True on success, false on failure
	 */
	public function load_plugin_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale',  get_locale(), 'edd_etc' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'edd_etc', $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;

		if ( file_exists( $mofile_local ) ) {
			// Look in the /wp-content/plugins/edd-email-templates-customiser/languages/ folder
			load_textdomain( 'edd_etc', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'edd_etc', false, $this->lang_dir );
		}

		return false;
	}

	/**
	 * Adds all the hooks/filters
	 *
	 * The plugin relies heavily on the use of hooks and filters and modifies
	 * default WordPress behaviour by the use of actions and filters which are
	 * provided by WordPress.
	 *
	 * Actions are provided to hook on this function, before the hooks and filters
	 * are added and after they are added. The class object is passed via the action.
	 *
	 * @since 1.2
	 * @access public
	 * @return void
	 */
	public function hooks() {
		do_action_ref_array( 'edd_etc_before_setup_actions', array( &$this ) );

		add_filter( 'edd_email_templates',             array( $this, 'edd_etc_register_templates' ) );
		add_filter( 'edd_settings_emails',             array( $this, 'edd_etc_register_colorpickers' ) );
		add_filter( 'edd_email_template_customised',   array( $this, 'edd_etc_customised_email_template' ) );
		add_filter( 'edd_purchase_receipt_customised', array( $this, 'edd_etc_customised_email_template_extra_styling' ) );

		add_action( 'admin_enqueue_scripts',           array( $this, 'edd_etc_load_admin_scripts' ), 100 );

		do_action_ref_array( 'edd_etc_after_setup_actions', array( &$this ) );
	}

	/**
	 * Register Email Template
	 *
	 * Registers the email template but appending to the email templates array
	 *
	 * @access public
	 * @since 1.2
	 *
	 * @param array $email_templates EDD email templates
	 * @return array $email_templates A merged array with EDD email templates and the customised email template
	 */
	public function edd_etc_register_templates( $edd_templates ) {
		$edd_etc_email_templates = array(
			'customised' => __( 'Customised Template', 'edd_etc' ),
		);

		return array_merge( $edd_templates, $edd_etc_email_templates );
	}

	/**
	 * Register Colorpickers as Settings
	 *
	 * Registers the colorpickers as settings by merging with the EDD settings
	 *
	 * @access      public
	 * @since       1.0
	 *
	 * @param       array $settings EDD Settings
	 * @return      array $settings A merged array with the plugin settings and the EDD settings to be displayed on the Email settings page
	 */
	public function edd_etc_register_colorpickers( $settings ) {
		global $edd_options;

		if ( isset( $edd_options['email_template'] ) && $edd_options['email_template'] == 'customised' ) {
			$edd_etc_settings = array(
				array(
					'id'         => 'edd_etc_email_body_background_color',
					'name'       => __( 'Email Body Background Color', 'edd_etc' ),
					'desc'       => __( 'The background color for the email body wrapper.', 'edd_etc' ),
					'type'       => 'colorpicker',
					'std'        => 'ffffff'
				),
				array(
					'id'         => 'edd_etc_email_border_color',
					'name'       => __( 'Email Body Border Color', 'edd_etc' ),
					'desc'       => __( 'The border color for the email body wrapper.', 'edd_etc' ),
					'type'       => 'colorpicker',
					'std'        => '505050'
				),
				array(
					'id'         => 'edd_etc_heading_text',
					'name'       => __( 'Heading Text Color', 'edd_etc' ),
					'desc'       => __( 'The color of the headings (e.g. h1, h2, h3, etc.).', 'edd_etc' ),
					'type'       => 'colorpicker',
					'std'        => '2f3f57'
				),
				array(
					'id'         => 'edd_etc_body_text',
					'name'       => __( 'Body Text Color', 'edd_etc' ),
					'desc'       => __( 'The color of the body text.', 'edd_etc' ),
					'type'       => 'colorpicker',
					'std'        => '333333'
				),
				array(
					'id'         => 'edd_etc_link_color',
					'name'       => __( 'Links Color', 'edd_etc' ),
					'desc'       => __( 'The color of any links in the email body.', 'edd_etc' ),
					'type'       => 'colorpicker',
					'std'        => '4183c4'
				),
				array(
					'id'         => 'edd_etc_heading_font_weight',
					'name'       => __( 'Headings Font Weight', 'edd_etc' ),
					'desc'       => __( 'The font weight for all of the headings. Default is normal.', 'edd_etc' ),
					'type'       => 'select',
					'options'    => array(
						'normal' => __( 'Normal', 'edd_etc' ),
						'bold'   => __( 'Bold', 'edd_etc' )
					),
					'std' => 'normal'
				)
			);
		} else {
			$edd_etc_settings = array();
		}

		return array_merge( $settings, $edd_etc_settings );
	}

	/**
	 * Customised Email Template
	 *
	 * Renders the customised email template
	 *
	 * @access private
	 * @since 1.2
	 */
	public function edd_etc_customised_email_template() {
		global $edd_options;

		if ( isset( $edd_options['edd_etc_heading_font_weight'] ) ) { $value = $edd_options['edd_etc_heading_font_weight']; } else { $value = 'normal'; }

		echo '<div style="width: 550px; background: #'. esc_attr( $edd_options['edd_etc_email_body_background_color'] ) .'; border: 1px solid #'. esc_attr( $edd_options['edd_etc_email_border_color'] ) .'; margin: 0 auto; padding: 4px; outline: none;">';
			echo '<div style="padding: 1px;"">';
				echo '<div id="edd-email-content" style="padding: 10px;">';
					if( isset( $edd_options['email_logo']) ) {
						echo '<img src="' . $edd_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
					} else if( isset( $edd_options['eddpdfi_email_logo'] ) ) {
						echo '<img src="' . $edd_options['eddpdfi_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
					}
					echo '<h1 style="color: #'. esc_attr( $edd_options['edd_etc_heading_text'] ) .'; line-height: 24px; font-weight: '. esc_attr( $value ) .'; font-size: 24px;">' . __('Receipt', 'eddpdfi') .'</h1>';
					echo '{email}';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

	/**
	 * Extra Styling
	 *
	 * Apply extra styling using str_replace for the customised email template
	 *
	 * @access public
	 * @since 1.2
	 *
	 * @param string $email_body The body text of the Purchase Receipt email sent by EDD
	 * @return string $email_body The body text of the Purchase Receipt email sent by EDD with all the styling applied
	 */
	public function edd_etc_customised_email_template_extra_styling( $email_body ) {
		global $edd_options;

		if ( isset( $edd_options['edd_etc_heading_font_weight'] ) ) { $value = $edd_options['edd_etc_heading_font_weight']; } else { $value = 'normal'; }

		$email_body = str_replace( '<h1>', '<h1 style="color: #'. esc_attr( $edd_options['edd_etc_heading_text'] ) .'; line-height: 24px; font-weight: '. esc_attr( $value ) .'; font-size: 24px;">', $email_body );
		$email_body = str_replace( '<h2>', '<h2 style="color: #'. esc_attr( $edd_options['edd_etc_heading_text'] ) .'; line-height: 20px; font-weight: '. esc_attr( $value ) .'; font-size: 20px;">', $email_body );
		$email_body = str_replace( '<h3>', '<h3 style="color: #'. esc_attr( $edd_options['edd_etc_heading_text'] ) .'; line-height: 18px; font-weight: '. esc_attr( $value ) .'; font-size: 18px;">', $email_body );
		$email_body = str_replace( '<h4>', '<h4 style="color: #'. esc_attr( $edd_options['edd_etc_heading_text'] ) .'; line-height: 16px; font-weight: '. esc_attr( $value ) .'; font-size: 16px;">', $email_body );
		$email_body = str_replace( '<h5>', '<h5 style="color: #'. esc_attr( $edd_options['edd_etc_heading_text'] ) .'; line-height: 14px; font-weight: '. esc_attr( $value ) .'; font-size: 15px;">', $email_body );
		$email_body = str_replace( '<h6>', '<h6 style="color: #'. esc_attr( $edd_options['edd_etc_heading_text'] ) .'; line-height: 20px; font-weight: '. esc_attr( $value ) .'; font-size: 15px; text-transform: uppercase;">', $email_body );
		$email_body = str_replace( '<a',   '<a style="color: #'. esc_attr( $edd_options['edd_etc_link_color'] ) .'; text-decoration: none;"', $email_body );
		$email_body = str_replace( '<ul>', '<ul style="margin: 0 0 0 20px; padding: 0;">', $email_body );
		$email_body = str_replace( '<li>', '<li style="color: #'. esc_attr( $edd_options['edd_etc_body_text'] ) .'; list-style: square;">', $email_body );
		$email_body = str_replace( '<p>',  '<p style="color: #'. esc_attr( $edd_options['edd_etc_body_text'] ) .';">', $email_body );

		return $email_body;
	}

	/**
	 * Load Admin Scripts
	 *
	 * @access public
	 * @since 1.2
	 * @param array $hook
	 */
	function edd_etc_load_admin_scripts( $hook ) {
		global $post, $pagenow, $edd_settings_page, $edd_options;

		$edd_pages = array( $edd_settings_page );

		if ( ! in_array( $hook, $edd_pages ) && ! is_object( $post ) )
			return;

		if ( $hook == $edd_settings_page ) {
			wp_register_script( 'edd_etc_colorpicker_js', $this->plugin_url . 'includes/colorpicker/js/colorpicker.js', array( 'jquery' ), $this->version, false );
			wp_register_script( 'edd_etc_admin_js', $this->plugin_url . 'includes/js/admin.js', array( 'jquery', 'edd_etc_colorpicker_js' ), $this->version, false );
			wp_register_style( 'edd_etc_colorpicker_style', $this->plugin_url . 'includes/colorpicker/css/colorpicker.css', array(  ) , $this->version, false );

			wp_enqueue_script( 'edd_etc_colorpicker_js' );
			wp_enqueue_script( 'edd_etc_admin_js' );
			wp_enqueue_style( 'edd_etc_colorpicker_style' );
		}
	}
}

/**
 * Loads a single instance of EDD Email Templates Customiser 
 *
 * This follows the PHP singleton design pattern.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example <?php $edd_email_templates_customiser = edd_email_templates_customiser(); ?>
 *
 * @since 1.2
 *
 * @see EDD_Email_Templates_Customiser::get_instance()
 *
 * @return object Returns an instance of the EDD_Email_Templates_Customiser class
 */
function edd_email_templates_customiser() {
	return EDD_Email_Templates_Customiser::get_instance();
}

/**
 * Loads plugin after all the others have loaded and have registered their
 * hooks and filters
 */
add_action( 'plugins_loaded', 'edd_email_templates_customiser', apply_filters( 'edd_email_templates_customiser_action_priority', 10 ) );

/**
 * Color Picker Callback
 *
 * Callback function for the colorpicker setting type. It has to be outside
 * the class because the settings currently don't allow $this to be passed
 * as a callback.
 *
 * @since 1.2
 * @param array $args All the values from the settings array
 */
function edd_colorpicker_callback( $args ) {
	global $edd_options;

	if( isset( $edd_options[ $args['id'] ] ) ) { $value = $edd_options[$args['id']]; } else { $value = isset($args['std']) ? $args['std'] : ''; }
	$html = '<input type"text" id="edd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="edd_settings_' . $args['section'] . '[' . $args['id'] . ']" maxlength="6" size="6" value="'. esc_attr( $value ) .'" class="edd_etc_colorpicker" />';
	$html .= '<label for="edd_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';  

	echo $html;
}

endif;  // End if class_exists check