<?php
/**
 * Plugin Name: PDF viewer for Elementor
 * Description: This plugin helps you embed PDF documents to Elementor quickly and easily. 
 * Plugin URI:  https://github.com/kazbekkadalashvili/elementor-pdf-viewer
 * Version:     2.9
 * Author:      Kazbek Kadalashvili
 * Author URI:  https://www.upwork.com/o/profiles/users/_~01800759f61b8ffa73/
 * Text Domain: elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ElementorPDFViewer {

	/**
	 * Plugin Version
	 *
	 * @since 1.2.0
	 * @var string The plugin version.
	 */
	const VERSION = '2.9.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.2.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.2.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Load translation
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'elementor' );
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'plugin.php' );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor' ),
			'<strong>' . esc_html__( 'Elementor PDF Viewer', 'elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor' ),
			'<strong>' . esc_html__( 'Elementor PDF Viewer', 'elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor' ),
			'<strong>' . esc_html__( 'Elementor PDF Viewer', 'elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}

new ElementorPDFViewer;


// add plugin activation time
function void_activation_time(){
    $get_activation_time = strtotime("now");
    add_option('pdf_elementor_activation_time', $get_activation_time );
}
register_activation_hook( __FILE__, 'void_activation_time' );

//check if review notice should be shown or not
function void_check_installation_time() {   
    
	$install_date = get_option( 'pdf_elementor_activation_time' );
	$past_date = strtotime( '-3 days' );
 
	if ( $past_date >= $install_date ) {
 
		add_action( 'admin_notices', 'void_display_admin_notice' );
 
	}

}
add_action( 'admin_init', 'void_check_installation_time' );

/**
* Display Admin Notice, asking for a review
**/
function void_display_admin_notice() {
	$dont_disturb = esc_url( get_admin_url() . '?spare_me=1' );
	$plugin_info = get_plugin_data( __FILE__ , true, true );       
	$reviewurl = esc_url( 'https://wordpress.org/support/plugin/'. sanitize_title( $plugin_info['Name'] ) . '/reviews/' );
	if( !get_option('void_spare_me') ){
		printf(__('<div class="notice notice-success" style="padding: 10px;">You have been using <b> %s </b> for a while. We hope you liked it! Please give us a quick rating, it works as a boost for us to keep working on the plugin!<br><div class="void-review-btn"><a href="%s" style="margin-top: 10px; display: inline-block; margin-right: 5px;" class="button button-primary" target=
		"_blank">Rate Now!</a><a href="%s" style="margin-top: 10px; display: inline-block; margin-right: 5px;" class="button button-secondary">Already Done !</a></div></div>', $plugin_info['TextDomain']), $plugin_info['Name'], $reviewurl, $dont_disturb );
	}
}

// remove the notice for the user if review already done or if the user does not want to
function void_spare_me(){    
    if( isset( $_GET['spare_me'] ) && !empty( $_GET['spare_me'] ) ){
        $spare_me = $_GET['spare_me'];
        if( $spare_me == 1 ){
            add_option( 'void_spare_me' , TRUE );
        }
    }
}
add_action( 'admin_init', 'void_spare_me', 5 );