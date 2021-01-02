<?php

class WUT
{

    /**
     * @var WUT_OptionsManager
     */
    public $options;

    /**
     * @var WUT_QueryBox
     */
    public $query;

    /**
     * @var WUT_Utils
     */
    public $utils;

    /**
     * The root dir of the plugin.
     * 
     * @var string The root dir path of this plugin with slash / appended.
     */
    public $rootDir;

    /**
     * @var string The root url of this plugin base path.
     */
    public $rootUrl;

    /**
     * @var WUT
     */
    public static $me;

    public function __construct($rootDir, $rootUrl)
    {
        $this->rootDir = $rootDir;
        $this->rootUrl = $rootUrl;
    }

    /**
     * The plugin entry point.
     * 
     * @param string $filePath the plugin entry file path.
     */
    public static function run($filePath)
    {
        global $wut;
        $dir      = plugin_dir_path($filePath);
        $url      = plugins_url('', $filePath);
        self::$me = new WUT($dir, $url);
        $wut      = self::$me;
        $wut->load();
        $wut->register();
    }

    /**
     * Register the plugin to WordPress.
     */
    public function register()
    {
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Include all the files.
     */
    public function load()
    {
        require($this->rootDir . 'inc/class.optionsmanager.php');
        require($this->rootDir . 'inc/class.querybox.php');
        require($this->rootDir . 'inc/class.utils.php');
        require($this->rootDir . 'inc/class.admin.php');
        require($this->rootDir . 'inc/tags.php');
        require($this->rootDir . 'inc/widgets.php');
        require($this->rootDir . 'inc/widgets/class-wut-widget-recent-posts.php');
    }

    /**
     * Register other hooks.
     */
    public function init()
    {
        $this->register();
        $this->options = new WUT_OptionsManager();
        $this->query   = new WUT_QueryBox();
        $this->utils   = new WUT_Utils($this->options->get_options());

        //the following lines add all the Widgets
        $widgets = $this->options->get_options("widgets");
        foreach ($widgets['load'] as $callback) {
            add_action('widgets_init', $callback);
        }

        add_action('widgets_init', function() {
            register_widget('WUT_Widget_Recent_Posts');
        });

        //add automatic post excerpt
        add_filter('get_the_excerpt', [$this->utils, 'excerpt'], 9);

        //add exclude pages
        add_filter('wp_list_pages_excludes', [$this->utils, 'exclude_pages'], 9);

        //add custom code
        add_action('wp_head', [$this->utils, 'inject_to_head']);
        add_action('wp_footer', [$this->utils, 'inject_to_footer']);

        if (is_admin()) {
            //add admin menus
            $wut_admin = new WUT_Admin($this->options->get_options());
            add_action('admin_menu', [$wut_admin, 'add_menu_items']);

            //add word count
            add_filter('manage_posts_columns', [$this->utils, 'add_wordcount_manage_columns']);
            add_filter('manage_pages_columns', [$this->utils, 'add_wordcount_manage_columns']);
            add_action('manage_posts_custom_column', [$this->utils, 'display_wordcount']);
            add_action('manage_pages_custom_column', [$this->utils, 'display_wordcount']);
            add_action('admin_head', [$this->utils, 'set_column_width']);
        }
    }

    public static function log($msg)
    {
        if (!WP_DEBUG) {
            return;
        }

        $trace = debug_backtrace();
        $file = basename($trace[0]['file']);
        $line = $trace[0]['line'];
        $func = $trace[1]['function'];
        
        error_log("[$file][$func][$line]:" . $msg);
    }
}
