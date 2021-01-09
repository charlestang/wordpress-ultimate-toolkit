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
	 * Set the name and description of the widget.
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'NEW! List most viewed posts. This need WP Postviews installed first.', 'wut' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( '', __( 'WUT:Most Viewed Posts', 'wut' ), $widget_ops );
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
			'limit'    => $instance['number'],
			'offset'   => 0,
			'before'   => '<li>',
			'after'    => '</li>',
			'type'     => 'post', // 'post' or 'page' or 'both'.
			'skips'    => '', // comma seperated post_ID list.
			'none'     => 'No Posts.', // tips to show when results is empty.
			'password' => 'hide', // show password protected post or not.
			'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>',
			'time_range' => $instance['time_range'] < 0 ? $instance['custom_range'] : $instance['time_range'],
		);

		if ( $instance['show_view_count'] ) {
			$tag_args['xformat'] .= ' (%viewcount% views)';
		}

		if ( $instance['show_date'] ) {
			$tag_args['xformat'] .= ' %postdate%';
		}
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
		return $new_instance;
	}

	/**
	 * The widget panel in admin page.
	 *
	 * @param array $instance The settings of this widget instance.
	 */
	public function form( $instance ) {
		$title           = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number          = isset( $instance['number'] ) ? absint( $instance['number'] ) : 10;
		$show_date       = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
		$excerpt_words   = isset( $instance['excerpt_words'] ) ? absint( $instance['excerpt_words'] ) : 15;
		$time_range      = isset( $instance['time_range'] ) ? intval( $instance['time_range'] ) : 365;
		$custom_range    = isset( $instance['custom_range'] ) ? absint( $instance['custom_range'] ) : 365;
		$show_view_count = isset( $instance['show_view_count'] ) ? (bool) $instance['show_view_count'] : true;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:', 'wut' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php echo __( 'Number of posts to show:', 'wut' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_words' ); ?>"><?php echo __( 'Maximum title length:', 'wut' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'excerpt_words' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_words' ); ?>" type="number" step="1" min="1" value="<?php echo $excerpt_words; ?>" size="3" />
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?', 'wut' ); ?></label>
		</p>
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
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_view_count ); ?> id="<?php echo $this->get_field_id( 'show_view_count' ); ?>" name="<?php echo $this->get_field_name( 'show_view_count' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_view_count' ); ?>"><?php _e( 'Show view count?', 'wut' ); ?></label>
		</p>
		<?php
	}
}

/* vim: set et=off ts=4 sw=4: */
