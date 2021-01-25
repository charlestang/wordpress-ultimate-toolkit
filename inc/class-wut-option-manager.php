<?php
/**
 * Options manager.
 *
 * @package WordPress_Ultimate_Toolkit
 */

/**
 * Options manager class.
 */
class WUT_Option_Manager {
	/**
	 * The version of options layout.
	 *
	 * @var string Semantic version.
	 */
	public $version = '1.0.2';

	/**
	 * The options array.
	 *
	 * @var array Options key value table.
	 */
	public $options;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->options = get_option( 'wordpress-ultimate-toolkit-options' );

		if ( empty( $this->options ) ) {
			$this->set_defaults();
			return;
		}

		// when the first time install, the options array will be empty.
		if ( isset( $this->options['widgets'] ) && isset( $this->options['widgets']['all'] ) ) {
			$this->options['widgets']['all'] = array_filter(
				$this->options['widgets']['all'],
				function ( $value ) {
					if ( in_array(
						$value['callback'],
						array(
							'wut_widget_recent_posts_init',
							'wut_widget_recent_comments_init',
							'wut_widget_related_posts_init',
						),
						true
					) ) {
						return false;
					}
					return true;
				}
			);
		}

		// when the plugin updated, this will be true.
		if ( $this->version > $this->options['version'] ) {
			$this->set_defaults();
		}
	}

	/**
	 * Initialize all options.
	 *
	 * @return void
	 */
	public function set_defaults() {
		$defaults = array(
			'widgets'    => array(
				'load' => array(
					'wut_widget_random_posts_init',
					'wut_widget_posts_by_category_init',
					'wut_widget_most_commented_posts_init',
					'wut_widget_active_commentators_init',
					'wut_widget_recent_commentators_init',
				),
				'all'  => array(
					array(
						'name'     => __( 'Random Posts', 'wut' ),
						'descript' => __( 'Display a list of random posts.', 'wut' ),
						'callback' => 'wut_widget_random_posts_init',
					),
					array(
						'name'     => __( 'In Category Posts Widget', 'wut' ),
						'descript' => __( 'Display a list of posts in a certain category.', 'wuts' ),
						'callback' => 'wut_widget_posts_by_category_init',
					),
					array(
						'name'     => __( 'Most Commented Posts', 'wut' ),
						'descript' => __( 'Display a list of most commented posts.', 'wut' ),
						'callback' => 'wut_widget_most_commented_posts_init',
					),
					array(
						'name'     => __( 'Active Commentators', 'wut' ),
						'descript' => __( 'Display active commentators in a certain days limit.', 'wut' ),
						'callback' => 'wut_widget_active_commentators_init',
					),
					array(
						'name'     => __( 'Recent Commentators', 'wut' ),
						'descript' => __( 'Display this week or this month\'s commentators.', 'wut' ),
						'callback' => 'wut_widget_recent_commentators_init',
					),
				),

			),
			'excerpt'    => array(
				'enabled'      => true,
				'paragraphs'   => 3,
				'words'        => 250,
				'tip_template' => '<br/><br/><span class="readmore"><a href="%permalink%" title="%title%">Continue Reading--%total_words% words totally</a></span>',
			),
			'related'    => array(
				'enabled' => true,
			),
			'other'      => array(
				'enabled'      => 1,
				'wphome'       => get_option( 'home' ),
				'perma_struct' => get_option( 'permalink_structure' ),
			),
			'customcode' => array(),
		);
		$defaults['version'] = $this->version;
		if ( empty( $this->options ) ) {
			$this->options = $defaults;
			$this->save_options();
			return;
		}
	}

	/**
	 * Retrieve all options.
	 *
	 * @param string $key Options key.
	 * @return mixed
	 */
	public function &get_options( $key = '' ) {
		if ( empty( $key ) ) {
			return $this->options;
		}
		$vlaue = null;
		if ( isset( $this->options[ $key ] ) ) {
			$value = $this->options[ $key ];
		} else {
			$value = false;
		}
		return $value;
	}

	/**
	 * Save all options.
	 *
	 * @return void
	 */
	public function save_options() {
		if ( isset( $this->options['hide-pages'] ) ) {
			unset( $this->options['hide-pages'] );
		}
		delete_option( 'wut-widget-recent-posts' );
		delete_option( 'wut-widget-recent-comments' );
		delete_option( 'wut-widget-related-posts' );
		update_option( 'wordpress-ultimate-toolkit-options', $this->options );
	}

	/**
	 * Delete all options to clean the site database.
	 *
	 * @return void
	 */
	public function delete_options() {
		delete_option( 'wordpress-ultimate-toolkit-options' );
		delete_option( 'wut-widget-recent-posts' );
		delete_option( 'wut-widget-random-posts' );
		delete_option( 'wut-widget-related-posts' );
		delete_option( 'wut-widget-posts-by-category' );
		delete_option( 'wut-widget-most-commented-posts' );
		delete_option( 'wut-widget-recent-comments' );
		delete_option( 'wut-widget-active-commentators' );
		delete_option( 'wut-widget-recent-commentators' );
		$widget = new WUT_Widget_Recent_Posts();
		delete_option( $widget->option_name );
		$widget = new WUT_Widget_Recent_Comments();
		delete_option( $widget->option_name );
		$widget = new WUT_Widget_Most_Viewed_Posts();
		delete_option( $widget->option_name );
		$widget = new WUT_Widget_Related_Posts();
		delete_option( $widget->option_name );
	}
}
