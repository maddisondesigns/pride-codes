<?php
/*
Plugin Name: Pride Codes
Plugin URI: https://pride.codes
Description: Show your support for your fellow LGBTQI+ friends & colleagues
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

		$this->options = get_option( 'pridecodes_option', $this->pridecodes_default );

		if( !empty( $this->options['pridecodes_selected_widget'] ) ) {
			// Enqueue our widget script/style, if one's been selected
			add_action( 'wp_enqueue_scripts', array( $this, 'pridecodes_wp_enqueue_scripts' ) );
		}
	}

	/**
	 * Add a new option to the Settings menu
	 */
	public function pridecodes_create_menu_option() {
		add_options_page( 'Pride Codes', 'Pride Codes', 'manage_options', 'pride-codes', array( $this, 'pridecodes_plugin_settings_page' ) );
	}

	/**
	 * Add a settings link to the plugin page
	 */
	public function pridecodes_add_settings_link( $links, $file ) {
		static $this_plugin;

		if( !$this_plugin ) {
			$this_plugin = plugin_basename( __FILE__ );
		}

		if( $file == $this_plugin ) {
			$settings_link = '<a href="options-general.php?page=pride-codes">' . esc_html__( 'Settings', 'pride-codes' ) . '</a>';
			array_unshift( $links, $settings_link ) ;
		}

		return $links;
	}

	public function pridecodes_admin_wp_enqueue_script( $hook ) {
		// Load only on ?page=pride-codes
		if( $hook != 'settings_page_pride-codes' ) {
			return;
		}
		wp_enqueue_style( 'pridecodes_wp_admin_css', plugins_url( 'css/pride-codes.css', __FILE__ ) );
	}

	/**
	 * Create our settings page
	 */
	public function pridecodes_plugin_settings_page() {
		$this->options = get_option( 'pridecodes_option', $this->pridecodes_default );

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
			esc_url( 'https://pride.codes' ),
			esc_html__('provides a collection of simple widgets for businesses to add to their sites. We want businesses to actively show their support for their LGBTIQ+ friends &amp; colleagues.', 'pride-codes')
		);
		printf( '<p>%1$s <a href="%2$s" target="_blank">%3$s</a> %4$s</p>' ,
			esc_html__( 'Visit the', 'pride-codes' ),
			esc_url( 'http://www.equalitycampaign.org.au/planyourvote?splash=1' ),
			esc_html__( 'Equality Campaign', 'pride-codes' ),
			esc_html__( 'page for more information.', 'pride-codes' )
		);
	}

	/**
	 * Display and fill the radio button for selecting the widget
	 */
	public function pridecodes_enable_widget_callback() {
		$enable_widget = ( isset( $this->options['pridecodes_selected_widget'] ) ? $this->options['pridecodes_selected_widget'] : '' );

		echo '<div class="image_radio_button_control">';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesleft" type="radio" name="pridecodes_option[pridecodes_selected_widget]" value="%1$s" %2$s/>',
			esc_attr( $this->pridecodes_choices['pridecodes_voteyesleft'] ),
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesleft'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/voteyes_corner_left.png" />';
		echo esc_html__( '<p>Support Australia’s #VoteYes Marriage Equality campaign with a #VoteYes pride corner. (Left Aligned)</p>', 'pride-codes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesright" type="radio" name="pridecodes_option[pridecodes_selected_widget]" value="%1$s" %2$s/>',
			esc_attr( $this->pridecodes_choices['pridecodes_voteyesright'] ),
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesright'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/voteyes_corner_right.png" />';
		echo esc_html__( '<p>Support Australia’s #VoteYes Marriage Equality campaign with a #VoteYes pride corner. (Right Aligned)</p>', 'pride-codes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesbar" type="radio" name="pridecodes_option[pridecodes_selected_widget]" value="%1$s" %2$s/>',
			esc_attr( $this->pridecodes_choices['pridecodes_voteyesbar'] ),
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesbar'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/pride_bars.png" />';
		echo esc_html__( '<p>Add a simple pride strip at the top of your website or an element on your page.</p>', 'pride-codes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyescode" type="radio" name="pridecodes_option[pridecodes_selected_widget]" value="%1$s" %2$s/>',
			esc_attr( $this->pridecodes_choices['pridecodes_voteyescode'] ),
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyescode'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/code_corner.png" />';
		echo esc_html__( '<p>Add a simple “Pride Codes” corner to your website.</p>', 'pride-codes' );
		echo '</div>';
		echo '</label>';

		echo '<label class="radio-button-label">';
		printf( '<input id="pridecodes_voteyesrainbow" type="radio" name="pridecodes_option[pridecodes_selected_widget]" value="%1$s" %2$s/>',
			esc_attr( $this->pridecodes_choices['pridecodes_voteyesrainbow'] ),
			checked( $enable_widget, $this->pridecodes_choices['pridecodes_voteyesrainbow'], false ) );
		echo '<div class="singlebutton">';
		echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/rainbow_corner.png" />';
		echo esc_html__( '<p>Add a simple pride corner to your website.</p>', 'pride-codes' );
		echo '</div>';
		echo '</label>';

		echo '</div>';
	}

	/**
	 * Validate and sanitize our option
	 */
	public function pridecodes_plugin_sanitize_options( $input ) {
		$valid = array();

		// Validate the input. If the value isn't found for some reason, return the default value
		if ( in_array( $input['pridecodes_selected_widget'], $this->pridecodes_choices, true ) ) {
			$valid['pridecodes_selected_widget'] = $input['pridecodes_selected_widget'];
		} else {
			$valid['pridecodes_selected_widget'] = $this->pridecodes_default;
		}

		return $valid;
	}

	/**
	 * Enqueue our scripts or styles based on the selected widget
	 */
	public function pridecodes_wp_enqueue_scripts() {

		switch ( $this->options['pridecodes_selected_widget'] ) {
			case $this->pridecodes_choices['pridecodes_voteyesleft'] :
				wp_enqueue_script( 'voteyes', 'https://cdn.pride.codes/js/voteyes-left.js', array(), '1.0.0', true );
				break;

			case $this->pridecodes_choices['pridecodes_voteyesright'] :
				wp_enqueue_script( 'voteyes', 'https://cdn.pride.codes/js/voteyes.js', array(), '1.0.0', true );
				break;

			case $this->pridecodes_choices['pridecodes_voteyesbar'] :
				wp_enqueue_style( 'voteyes', 'https://cdn.pride.codes/css/bar_body.css', array(), '1.0.0', 'all' );
				break;

			case $this->pridecodes_choices['pridecodes_voteyescode'] :
				wp_enqueue_script( 'voteyes', 'https://cdn.pride.codes/js/codecorner.js', array(), '1.0.0', true );
				break;

			case $this->pridecodes_choices['pridecodes_voteyesrainbow'] :
				wp_enqueue_script( 'voteyes', 'https://cdn.pride.codes/js/rainbowcorner.js', array(), '1.0.0', true );
				break;

			default:
				return;
		}

		return;
	}
}

$pride_codes = new pride_codes_plugin();
