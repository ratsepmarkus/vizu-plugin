<?php
/**
 * Plugin Name:       Vizu Plugin
 * Plugin URI:        https://vizu.ee
 * Description:       Custom utility plugin for Vizu Disain websites. Contains useful shortcodes, Elementor tweaks, and other common functionality.
 * Version:           1.0.2
 * Author:            Markus Rätsep
 * Author URI:        https://vizu.ee
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
	 * The plugin update checker instance.
	 * @var \YahnisElsts\PluginUpdateChecker\v5\UpdateChecker
	 * @since 1.0.2
	 */
	protected $update_checker = null;

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
		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
	}

	/**
	 * Define Plugin Constants.
	 */
	private function define_constants() {
		define( 'VIZU_PLUGIN_VERSION', '1.0.2' );
		define( 'VIZU_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'VIZU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Add the core actions and filters for the plugin.
	 */
	private function add_actions_and_filters() {
		// Add shortcodes
		add_shortcode( 'vizu_year', [ $this, 'shortcode_current_year' ] );
		add_shortcode( 'vizu_hello', [ $this, 'shortcode_hello_world' ] );

		// Add a custom category to Elementor
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_category' ] );

		// WooCommerce Tweaks - only run if WooCommerce is active
		if ( class_exists( 'WooCommerce' ) ) {
			// Example: Change "Add to Cart" button text on single product pages
			add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'custom_add_to_cart_text' ] );
		}
	}

	/**
	 * Initialize the plugin by including files and setting up the updater.
	 * This runs after all plugins are loaded.
	 */
	public function init_plugin() {
		// In the future, you can include other files here, like for custom widgets or post types.
		// e.g., require_once VIZU_PLUGIN_PATH . 'includes/my-custom-widgets.php';
		require_once VIZU_PLUGIN_PATH . 'lib/plugin-update-checker/load-v5p6.php';

		// Setup the custom plugin updater.
		// Use the v5 factory.
		$this->update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
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
	 * [vizu_hello] shortcode.
	 * Displays a simple "Hello World!" message.
	 * @return string
	 */
	public function shortcode_hello_world() {
		return 'Hello World! The Vizu Plugin is working.';
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