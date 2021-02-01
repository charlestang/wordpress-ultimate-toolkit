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
	 * @var WUT_Option_Manager
	 */
	public $options;

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
		$dir      = plugin_dir_path( $file_path );
		$url      = plugins_url( '', $file_path );
		self::$me = new WUT( $dir, $url );
		self::$me->load();
		self::$me->register();
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
		require $this->root_dir . 'inc/class-wut-option-manager.php';
		require $this->root_dir . 'inc/class-wut-admin-panel.php';
		require $this->root_dir . 'inc/class-wut-admin-excerption.php';
		require $this->root_dir . 'inc/class-wut-admin-related-list.php';
		require $this->root_dir . 'inc/class-wut-admin-custom-code.php';
		require $this->root_dir . 'inc/class.utils.php';
		require $this->root_dir . 'inc/tags.php';
		require $this->root_dir . 'inc/widgets.php';
		require $this->root_dir . 'inc/class-wut-form-helper.php';
		require $this->root_dir . 'widgets/class-wut-widget-recent-posts.php';
		require $this->root_dir . 'widgets/class-wut-widget-recent-comments.php';
		require $this->root_dir . 'widgets/class-wut-widget-most-viewed-posts.php';
		require $this->root_dir . 'widgets/class-wut-widget-related-posts.php';
		if ( is_admin() ) {
			require $this->root_dir . 'inc/class-wut-admin.php';
		}
	}

	/**
	 * Register other hooks.
	 */
	public function init() {
		$this->register();
		$this->options = new WUT_Option_Manager();
		$this->utils   = new WUT_Utils( $this->options->get_options() );

		// the following lines add all the Widgets.
		$widgets = $this->options->get_options( 'widgets' );
		foreach ( $widgets['load'] as $callback ) {
			if ( in_array(
				$callback,
				array(
					'wut_widget_recent_posts_init',
					'wut_widget_recent_comments_init',
					'wut_widget_related_posts_init',
					'wut_widget_active_commentators_init',
					'wut_widget_recent_commentators_init',
				),
				true
			) ) {
				continue;
			}
			add_action( 'widgets_init', $callback );
		}

		add_action(
			'widgets_init',
			function () {
				register_widget( 'WUT_Widget_Recent_Posts' );
				register_widget( 'WUT_Widget_Recent_Comments' );
				register_widget( 'WUT_Widget_Related_Posts' );
				if ( in_array( 'wp-postviews/wp-postviews.php', get_option( 'active_plugins' ), true ) ) {
					register_widget( 'WUT_Widget_Most_Viewed_Posts' );
				}
			}
		);

		$excerpt = $this->options->get_options( 'excerpt' );
		if ( ! isset( $excerpt['enabled'] ) ) {
			$excerpt['enabled'] = true;
		}

		if ( $excerpt['enabled'] ) {
			// the priority should be 9, before the official `wp_trim_excerpt` filter.
			add_filter( 'get_the_excerpt', array( $this->utils, 'auto_excerption' ), 9 );
			add_filter( 'the_content', array( $this->utils, 'auto_excerption' ), 10 );
		}

		// add custom code.
		add_action( 'wp_head', array( $this->utils, 'inject_to_head' ) );
		add_action( 'wp_footer', array( $this->utils, 'inject_to_footer' ) );

		if ( is_admin() ) {
			// add admin menus.
			$wut_admin = new WUT_Admin( $this->options->get_options() );
			$wut_admin->register_admin_entry();
			add_action( 'admin_menu', array( $wut_admin, 'add_menu_items' ) );

			// add word count.
			add_filter( 'manage_posts_columns', array( $this->utils, 'add_wordcount_manage_columns' ) );
			add_filter( 'manage_pages_columns', array( $this->utils, 'add_wordcount_manage_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this->utils, 'display_wordcount' ) );
			add_action( 'manage_pages_custom_column', array( $this->utils, 'display_wordcount' ) );
			add_action( 'admin_head', array( $this->utils, 'set_column_width' ) );
		}

		// Add related posts list to end of a post or page.
		add_filter( 'wp_link_pages', array( $this->utils, 'display_related_posts' ), 10, 2 );

		// Register uninstall feature.
		register_uninstall_hook( $this->root_dir . '/wordpress-ultimate-toolkit.php', array( 'WUT_Option_Manager', 'delete_options' ) );
	}

	/**
	 * Output debug log.
	 *
	 * @return void
	 */
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

	/**
	 * Output backtrace of this method call inserted point.
	 *
	 * @return void
	 */
	public static function trace() {
		if ( ! WP_DEBUG ) {
			return;
		}

		$trace = debug_backtrace();
		$msg   = "\n";
		$depth = 0;
		foreach ( $trace as $stack ) {
			$msg .= '#' . ( $depth ++ ) . ' ';
			$msg .= str_replace( rtrim( ABSPATH, '\/' ), '', $stack['file'] );
			$msg .= '(' . $stack['line'] . '):';
			if ( isset( $stack['class'] ) ) {
				$msg .= $stack['class'] . $stack['type'];
			}
			$msg .= $stack['function'] . '( ';
			if ( ! empty( $stack['args'] ) ) {
				foreach ( $stack['args'] as $arg ) {
					if ( is_numeric( $arg ) ) {
						$msg .= $arg . ', ';
					} elseif ( is_string( $arg ) ) {
						if ( in_array( $stack['function'], array( 'require', 'require_once', 'include', 'include_once' ), true ) ) {
							$arg = str_replace( rtrim( ABSPATH, '\/' ), '', $arg );
						}
						$msg .= '"' . $arg . '", ';
					} elseif ( is_array( $arg ) ) {
						$msg .= json_encode( $arg ) . ', ';
					} elseif ( is_null( $arg ) ) {
						$msg .= 'NULL, ';
					} else {
						$msg .= '<obj>, ';
					}
				}
				$msg = substr( $msg, 0, -2 );
			}
			$msg .= ' )';
			$msg .= "\n";
		}

		error_log( $msg );
	}
}
