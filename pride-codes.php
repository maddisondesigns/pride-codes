<?php
/*
Plugin Name: Pride Codes
Plugin URI: http://maddisondesigns.com/woocommerce-breadcrumbs
Description: Show your support for your fellow LGBTQI+ colleagues
Version: 1.0.0
Author: Anthony Hortin
Author URI: http://maddisondesigns.com
Text Domain: pride-codes
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class pride_codes_plugin {

	private $options;
	private $pridecodes_default;
	private $pridecodes_choices;

	public function __construct() {

		$this->pridecodes_default = 'voteyesright';

		$this->pridecodes_choices = array(
			'pridecodes_voteyesleft' => 'voteyesleft',
			'pridecodes_voteyesright' => 'voteyesright',
			'pridecodes_voteyesbar' => 'voteyesbar',
			'pridecodes_voteyescode' => 'voteyescode',
			'pridecodes_voteyesrainbow' => 'voteyesrainbow',
			);

		add_action( 'admin_menu', array( $this, 'pridecodes_create_menu_option' ) );
		add_action( 'admin_init', array( $this, 'pridecodes_admin_init' ) );
		add_filter( 'plugin_action_links', array( $this, 'pridecodes_add_settings_link'), 10, 2);
		add_action( 'admin_enqueue_scripts', array( $this, 'pridecodes_admin_wp_enqueue_script' ) );
		//add_action( 'head', 'woocommerce_breadcrumb', 20, 0);

		$this->options = ( get_option( 'pridecodes_option' ) === false ? $this->pridecodes_default : get_option( 'pridecodes_option' ) );

		if( !empty( $this->options['pridecodes_option'] ) ) {
			// Show our widget
			//add_action( 'init', array( $this, 'wcb_remove_woocommerce_breadcrumb' ) );
		}
	}

	/**
	 * Add a new option to the Settings menu
	 */
	public function pridecodes_create_menu_option() {
		add_options_page( 'Pride Codes', 'Pride Codes', 'manage_options', 'pride-codes', array( $this, 'pridecodes_plugin_settings_page' ) );
	}

	/**
	 * Add a settings link to plugin page
	 */
	public function pridecodes_add_settings_link( $links, $file ) {
		static $this_plugin;

		if( !$this_plugin ) {
			$this_plugin = plugin_basename( __FILE__ );
		}

		if( $file == $this_plugin ) {
			$settings_link = '<a href="options-general.php?page=pride-codes">' . __( 'Settings', 'pridecodes' ) . '</a>';
			array_unshift( $links, $settings_link ) ;
		}

		return $links;
	}

	public function pridecodes_admin_wp_enqueue_script( $hook ) {
		// Load only on ?page=mypluginname
		if( $hook != 'settings_page_pride-codes' ) {
			return;
		}
		wp_enqueue_style( 'pridecodes_wp_admin_css', plugins_url( 'css/pride-codes.css', __FILE__ ) );
	}

	/**
	 * Create our settings page
	 */
	public function pridecodes_plugin_settings_page() {
		$this->options = ( get_option( 'pridecodes_option' ) === false ? $this->pridecodes_default : get_option( 'pridecodes_option' ) );

		//settings_errors( 'woocommerce-breadcrumb-warnings' );

		echo '<div class="wrap">';
			screen_icon();
			echo '<h2>Pride Codes</h2>';
			echo '<form action="options.php" method="post">';
				settings_fields( 'pridecodes_options' );
				do_settings_sections( 'pride-codes' );
				echo '<p>';
					submit_button( 'Save Changes', 'primary', 'submit', false  );
				echo '</p>';
			echo '</form>';
		echo '</div>';

	}

	/**
	 * Register and define the settings
	 */
	public function pridecodes_admin_init() {
		register_setting( 'pridecodes_options', 'pridecodes_option', array( $this, 'pridecodes_plugin_sanitize_options' ) );
		add_settings_section( 'pridecodes_general_settings', 'Fly The Flag', array( $this, 'pridecodes_plugin_section_message_callback' ), 'pride-codes' );
		add_settings_field( 'pridecodes_enable_widget', 'Select Widget', array( $this, 'pridecodes_enable_widget_callback' ), 'pride-codes', 'pridecodes_general_settings' );
	}

	/**
	 * Display a section message
	 */
	public function pridecodes_plugin_section_message_callback() {
		printf( '<p><a href="%1$s" target="_blank">Pride.Codes</a> %2$s</p>' ,
			esc_url( esc_html__( 'https://pride.codes', 'pridecodes' ) ),
			__('provides a collection of simple widgets for businesses to add to their sites. We want businesses to actively show their support for their LGBTIQ+ colleagues.', 'pridecodes')
		);
		printf( '<p>%1$s <a href="%2$s" target="_blank">%3$s</a> %4$s</p>' ,
			__( 'Visit the', 'pridecodes' ),
			esc_url( esc_html__( 'http://www.equalitycampaign.org.au/planyourvote?splash=1', 'pridecodes' ) ),
			__( 'Equality Campaign', 'pridecodes' ),
			__( 'page for more information.', 'pridecodes' )
		);
	}

	/**
	 * Display and fill the form field for the delimeter setting
	 */
	public function pridecodes_enable_widget_callback() {
		$enable_widget = ( isset( $this->options['pridecodes_option'] ) ? $this->options['pridecodes_option'] : '0' );

		echo '<div class="image_radio_button_control">';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesleft" type="radio" name="pridecodes_option[pridecodes_option]" value="%1$s" %2$s/>',
			$this->pridecodes_choices['pridecodes_voteyesleft'],
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesleft'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/voteyes_corner_left.png" />';
		echo __( '<p>Support Australia’s #VoteYes Marriage Equality campaign with a #VoteYes pride corner. (Left Aligned)</p>', 'pridecodes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesright" type="radio" name="pridecodes_option[pridecodes_option]" value="%1$s" %2$s/>',
			$this->pridecodes_choices['pridecodes_voteyesright'],
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesright'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/voteyes_corner_right.png" />';
		echo __( '<p>Support Australia’s #VoteYes Marriage Equality campaign with a #VoteYes pride corner. (Right Aligned)</p>', 'pridecodes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesbar" type="radio" name="pridecodes_option[pridecodes_option]" value="%1$s" %2$s/>',
			$this->pridecodes_choices['pridecodes_voteyesbar'],
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesbar'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/pride_bars.png" />';
		echo __( '<p>Add a simple pride strip at the top of your website or an element on your page.</p>', 'pridecodes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyescode" type="radio" name="pridecodes_option[pridecodes_option]" value="%1$s" %2$s/>',
			$this->pridecodes_choices['pridecodes_voteyescode'],
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyescode'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/code_corner.png" />';
		echo __( '<p>Add a simple “Pride Codes” corner to your website.</p>', 'pridecodes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesrainbow" type="radio" name="pridecodes_option[pridecodes_option]" value="%1$s" %2$s/>',
			$this->pridecodes_choices['pridecodes_voteyesrainbow'],
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesrainbow'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/rainbow_corner.png" />';
		echo __( '<p>Add a simple pride corner to your website.</p>', 'pridecodes' );
		echo '</div>';
		echo '</label>';

		echo '</div>';
	}

	/**
	 * Validate and sanitize each of our options
	 */
	public function pridecodes_plugin_sanitize_options( $input ) {
		$valid = array();

		// Validate the inputs
		$valid['wcb_enable_breadcrumbs'] = ( isset( $input['wcb_enable_breadcrumbs'] ) ? '1' : '0' );

		$valid['wcb_breadcrumb_delimiter'] = wp_kses_data( $input['wcb_breadcrumb_delimiter'] );

		$valid['wcb_wrap_before'] = wp_kses_post( $input['wcb_wrap_before'] );

		$valid['wcb_wrap_after'] = wp_kses_post( $input['wcb_wrap_after'] );

		$valid['wcb_before'] = wp_kses_post( $input['wcb_before'] );

		$valid['wcb_after'] = wp_kses_post( $input['wcb_after'] );

		$valid['wcb_home_text'] = sanitize_text_field( $input['wcb_home_text'] );

		$valid['wcb_home_url'] = esc_url( $input['wcb_home_url'] );

		return $valid;
	}

	/**
	* Remove the WooCommerce Breadcrumbs
	*/
	public function wcb_remove_woocommerce_breadcrumb() {
		if ( $this->wootheme_theme ) {
			remove_filter( 'woo_main_before', 'woo_display_breadcrumbs', 10 );
		}
		else {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		}
	}

	/**
	* Change the Home link for the Breadrumbs
	*/
	public function wcb_woocommerce_breadcrumb_home_url() {
		return $this->options['wcb_home_url'];
	}

	/**
	* Set the breadcrumbs
	*/
	public function wcb_woocommerce_set_breadcrumbs() {

		if ( $this->wootheme_theme ) {
			return array(
				'separator' => $this->options['wcb_breadcrumb_delimiter'],
				'before' => $this->options['wcb_wrap_before'],
				'after' => $this->options['wcb_wrap_after'],
				'show_home' => _x( $this->options['wcb_home_text'], 'breadcrumb', 'woocommerce-breadcrumbs' )
			);
		}
		else {
			return array(
				'delimiter' => $this->options['wcb_breadcrumb_delimiter'],
				'wrap_before' => $this->options['wcb_wrap_before'],
				'wrap_after' => $this->options['wcb_wrap_after'],
				'before' => $this->options['wcb_before'],
				'after' => $this->options['wcb_after'],
				'home' => _x( $this->options['wcb_home_text'], 'breadcrumb', 'woocommerce-breadcrumbs' )
			);
		}
	}
}

$pride_codes = new pride_codes_plugin();
