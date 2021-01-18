<?php
/**
 * This file is the class
 *
 * @package wut
 */

/**
 * The plugin entry point class.
 */
class WUT {


	/**
	 * The option manager instance.
	 *
	 * @var WUT_OptionsManager
	 */
	public $options;

	/**
	 * The query object instance.
	 *
	 * @var WUT_Query_Box
	 */
	public $query;

	/**
	 * Utils functionality instance.
	 *
	 * @var WUT_Utils
	 */
	public $utils;

	/**
	 * The root dir of the plugin.
	 *
	 * @var string The root dir path of this plugin with slash / appended.
	 */
	public $root_dir;

	/**
	 * The root url path of the plugin.
	 *
	 * @var string The root url of this plugin base path.
	 */
	public $root_url;

	/**
	 * Global object of the plugin.
	 *
	 * @var WUT
	 */
	public static $me;

	/**
	 * The constructor.
	 *
	 * @param string $root_dir The plugin root directory.
	 * @param string $root_url The plugin root path.
	 */
	public function __construct( $root_dir, $root_url ) {
		$this->root_dir = $root_dir;
		$this->root_url = $root_url;
	}

	/**
	 * The plugin entry point.
	 *
	 * @param string $file_path the plugin entry file path.
	 */
	public static function run( $file_path ) {
		global $wut;
		$dir      = plugin_dir_path( $file_path );
		$url      = plugins_url( '', $file_path );
		self::$me = new WUT( $dir, $url );
		$wut      = self::$me;
		$wut->load();
		$wut->register();
	}

	/**
	 * Register the plugin to WordPress.
	 */
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Include all the files.
	 */
	public function load() {
		require $this->root_dir . 'inc/class.optionsmanager.php';
		require $this->root_dir . 'inc/class-wut-query-box.php';
		require $this->root_dir . 'inc/class-wut-form-helper.php';
		require $this->root_dir . 'inc/class.utils.php';
		require $this->root_dir . 'inc/class.admin.php';
		require $this->root_dir . 'inc/tags.php';
		require $this->root_dir . 'inc/widgets.php';
		require $this->root_dir . 'widgets/class-wut-widget-recent-posts.php';
		require $this->root_dir . 'widgets/class-wut-widget-recent-comments.php';
		require $this->root_dir . 'widgets/class-wut-widget-most-viewed-posts.php';
	}

	/**
	 * Register other hooks.
	 */
	public function init() {
		$this->register();
		$this->options = new WUT_OptionsManager();
		$this->query   = new WUT_Query_Box();
		$this->utils   = new WUT_Utils( $this->options->get_options() );

		// the following lines add all the Widgets.
		$widgets = $this->options->get_options( 'widgets' );
		foreach ( $widgets['load'] as $callback ) {
			if ( 'wut_widget_recent_posts_init' === $callback
				|| 'wut_widget_recent_comments_init' === $callback ) {
				continue;
			}
			add_action( 'widgets_init', $callback );
		}

		add_action(
			'widgets_init',
			function () {
				register_widget( 'WUT_Widget_Recent_Posts' );
				register_widget( 'WUT_Widget_Recent_Comments' );
				if ( in_array( 'wp-postviews/wp-postviews.php', get_option( 'active_plugins' ) ) ) {
					register_widget( 'WUT_Widget_Most_Viewed_Posts' );
				}
			}
		);

		$excerpt = $this->options->get_options( 'excerpt' );
		if ( ! isset( $excerpt['enabled'] ) ) {
			$excerpt['enabled'] = true;
		}

		if ( $excerpt['enabled'] ) {
			// add automatic post excerpt.
			add_filter( 'get_the_excerpt', array( $this->utils, 'excerpt' ), 9 );
		}

		// add custom code.
		add_action( 'wp_head', array( $this->utils, 'inject_to_head' ) );
		add_action( 'wp_footer', array( $this->utils, 'inject_to_footer' ) );

		if ( is_admin() ) {
			// add admin menus.
			$wut_admin = new WUT_Admin( $this->options->get_options() );
			add_action( 'admin_menu', array( $wut_admin, 'add_menu_items' ) );

			// add word count.
			add_filter( 'manage_posts_columns', array( $this->utils, 'add_wordcount_manage_columns' ) );
			add_filter( 'manage_pages_columns', array( $this->utils, 'add_wordcount_manage_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this->utils, 'display_wordcount' ) );
			add_action( 'manage_pages_custom_column', array( $this->utils, 'display_wordcount' ) );
			add_action( 'admin_head', array( $this->utils, 'set_column_width' ) );
		}
	}

	public static function log() {
		if ( ! WP_DEBUG ) {
			return;
		}

		$args = func_get_args();
		$msg  = '';
		foreach ( $args as $arg ) {
			if ( is_string( $arg ) || is_numeric( $arg ) ) {
				$msg .= $arg;
			} else {
				$msg .= var_export( $arg, true );
			}
		}

		$trace = debug_backtrace();
		$file  = basename( $trace[0]['file'] );
		$line  = $trace[0]['line'];
		$func  = $trace[1]['function'];

		error_log( "[$file][$func][$line]:" . $msg );
	}
}
