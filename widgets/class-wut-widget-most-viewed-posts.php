<?php
/**
 * Widget: WUT_Widget_Most_Viewed_Posts class.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage widgets
 */

/**
 * Define a widget show most viewed posts in home, post and page.
 *
 * This plugin need WP PostViews plugin installed first.
 *
 * @link https://wordpress.org/plugins/wp-postviews/
 */
class WUT_Widget_Most_Viewed_Posts extends WP_Widget {

	/**
	 * The helper to build widget form.
	 *
	 * @var WUT_Form_Helper
	 */
	protected $helper;
	/**
	 * Set the name and description of the widget.
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'NEW! List most viewed posts. This need WP Postviews installed first.', 'wut' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( '', __( 'WUT:Most Viewed Posts', 'wut' ), $widget_ops );
		$this->helper = new WUT_Form_Helper( $this );
	}

	/**
	 * Generate this widget.
	 *
	 * @param array $args Inherit from parent class.
	 * @param array $instance Settings of this widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = $instance['title'];

		$tag_args = array(
			'limit'      => $instance['number'],
			'offset'     => 0,
			'before'     => '<li>',
			'after'      => '</li>',
			'type'       => 'post', // 'post' or 'page' or 'both'.
			'skips'      => '', // comma seperated post_ID list.
			'none'       => 'No Posts.', // tips to show when results is empty.
			'password'   => 'hide', // show password protected post or not.
			'xformat'    => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>',
			'time_range' => $instance['time_range'] < 0 ? $instance['custom_range'] : $instance['time_range'],
		);

		if ( $instance['show_view_count'] ) {
			$tag_args['xformat'] .= ' (%viewcount% views)';
		}

		if ( $instance['show_date'] ) {
			if ( $instance['date_front'] ) {
				$tag_args['xformat'] = '%postdate% ' . $tag_args['xformat'];
			} else {
				$tag_args['xformat'] .= ' %postdate%';
			}
		}

		// TODO: change with helper print method.
		echo $args['before_widget'];
		echo $args['before_title'], $title, $args['after_title'];
		echo '<ul>', wut_most_viewed_posts( $tag_args ), '</ul>';
		echo $args['after_widget'];
	}

	/**
	 * Update the settings of this instance.
	 *
	 * @param array $new_instance New set settings.
	 * @param array $old_instance Original set settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                    = $old_instance;
		$instance['title']           = sanitize_text_field( $new_instance['title'] );
		$instance['number']          = intval( $new_instance['number'] );
		$instance['show_date']       = (bool) $new_instance['show_date'];
		$instance['date_front']      = (bool) $new_instance['date_front'];
		$instance['excerpt_words']   = intval( $new_instance['excerpt_words'] );
		$instance['time_range']      = intval( $new_instance['time_range'] );
		$instance['custom_range']    = intval( $new_instance['custom_range'] );
		$instance['show_view_count'] = (bool) $new_instance['show_view_count'];
		return $instance;
	}

	/**
	 * The widget panel in admin page.
	 *
	 * @param array $instance The settings of this widget instance.
	 */
	public function form( $instance ) {
		$title           = $this->helper->default( $instance, 'title', 'string', '' );
		$number          = $this->helper->default( $instance, 'number', 'uint', 10 );
		$show_date       = $this->helper->default( $instance, 'show_date', 'bool', false );
		$date_front      = $this->helper->default( $instance, 'date_front', 'bool', false );
		$excerpt_words   = $this->helper->default( $instance, 'excerpt_words', 'uint', 15 );
		$time_range      = $this->helper->default( $instance, 'time_range', 'int', 365 );
		$custom_range    = $this->helper->default( $instance, 'custom_range', 'uint', 365 );
		$show_view_count = $this->helper->default( $instance, 'show_view_count', 'bool', true );

		$this->helper->text( 'title', $title, __( 'Title:' ) );
		$this->helper->text( 'number', $number, __( 'Number of posts to show:' ), 'number', 'tiny-text' );
		$this->helper->text( 'excerpt_words', $excerpt_words, __( 'Maximum title length:', 'wut' ), 'number', 'tiny-text' );
		$this->helper->checkbox( 'show_date', $show_date, __( 'Display post date?', 'wut' ) );
		$this->helper->checkbox( 'date_front', $date_front, __( 'Put date in front of post title?', 'wut' ) );
		?>
		<p>
			<span><?php _e( 'Time frame of posts:', 'wut' ); ?></span><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'time_range' ); ?>"<?php checked( $time_range, 7 ); ?> value="7"/>
				<span><?php _e( 'Past Week', 'wut' ); ?></span>
			</label><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'time_range' ); ?>"<?php checked( $time_range, 30 ); ?> value="30"/>
				<span><?php _e( 'Past Month', 'wut' ); ?></span>
			</label><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'time_range' ); ?>"<?php checked( $time_range, 365 ); ?> value="365"/>
				<span><?php _e( 'Past Year', 'wut' ); ?></span>
			</label><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'time_range' ); ?>"<?php checked( $time_range, -1 ); ?> value="-1"/>
				<span><?php _e( 'Custom', 'wut' ); ?></span>
				<input class="small-text" id="<?php echo $this->get_field_id( 'custom_range' ); ?>" name="<?php echo $this->get_field_name( 'custom_range' ); ?>" type="number" step="1" min="1" value="<?php echo $custom_range; ?>" size="4" />
			</label>
		</p>
		<?php
			$this->helper->checkbox( 'show_view_count', $show_view_count, __( 'Show view count?', 'wut' ) );
		// TODO: Add date format support for this posts list.
	}
}
