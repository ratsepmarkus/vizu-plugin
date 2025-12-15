<?php
/**
 * Plugin Name:       Vizu Plugin
 * Plugin URI:        https://vizu.disain
 * Description:       Custom utility plugin for Vizu Disain websites. Contains useful shortcodes, Elementor tweaks, and other common functionality.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://vizu.disain
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vizu-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Main Vizu Plugin Class
 *
 * @since 1.0.0
 */
final class Vizu_Plugin {

	/**
	 * The single instance of the class.
	 * @var Vizu_Plugin
	 * @since 1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Vizu_Plugin Instance.
	 * Ensures only one instance of the plugin is loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Vizu_Plugin - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Vizu_Plugin Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->add_actions_and_filters();
		$this->include_files();
		$this->setup_updater();
	}

	/**
	 * Define Plugin Constants.
	 */
	private function define_constants() {
		define( 'VIZU_PLUGIN_VERSION', '1.0.0' );
		define( 'VIZU_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'VIZU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Add the core actions and filters for the plugin.
	 */
	private function add_actions_and_filters() {
		// Add shortcodes
		add_shortcode( 'vizu_year', [ $this, 'shortcode_current_year' ] );

		// Add a custom category to Elementor
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_category' ] );

		// WooCommerce Tweaks - only run if WooCommerce is active
		if ( class_exists( 'WooCommerce' ) ) {
			// Example: Change "Add to Cart" button text on single product pages
			add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'custom_add_to_cart_text' ] );
		}
	}

	/**
	 * Include required files.
	 */
	private function include_files() {
		// In the future, you can include other files here, like for custom widgets or post types.
		// e.g., require_once VIZU_PLUGIN_PATH . 'includes/my-custom-widgets.php';
		require_once VIZU_PLUGIN_PATH . 'lib/plugin-update-checker/plugin-update-checker.php';
	}

	/**
	 * Setup the custom plugin updater.
	 */
	private function setup_updater() {
		// Exit if the update checker class is not available.
		if ( ! class_exists( 'Puc_v4_Factory' ) ) {
			return;
		}

		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://raw.githubusercontent.com/ratsepmarkus/vizu-plugin/main/vizu-plugin-info.json', // URL to the JSON file on GitHub.
			__FILE__, // Full path to the main plugin file.
			'vizu-plugin' // The plugin's slug.
		);
	}

	/**
	 * [vizu_year] shortcode.
	 * Displays the current year. Useful for copyright notices in footers.
	 * @return string Current year.
	 */
	public function shortcode_current_year() {
		return date( 'Y' );
	}

	/**
	 * Add a custom widget category to Elementor.
	 * This helps organize your custom widgets.
	 */
	public function add_elementor_widget_category( $elements_manager ) {
		$elements_manager->add_category(
			'vizu-widgets',
			[
				'title' => __( 'Vizu Widgets', 'vizu-plugin' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	/**
	 * Change the "Add to Cart" button text on single product pages.
	 * @return string New button text.
	 */
	public function custom_add_to_cart_text() {
		return __( 'Add to Basket', 'vizu-plugin' );
	}
}

/**
 * Begins execution of the plugin.
 * @since 1.0.0
 */
function vizu_plugin_run() {
	return Vizu_Plugin::instance();
}

// Let's get this party started
vizu_plugin_run();