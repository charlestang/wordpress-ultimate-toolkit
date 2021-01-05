<?php
/**
 * Widget: WUT_Widget_Recent_Posts class.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage widgets
 */

/**
 * Define a widget show recent posts in home, post and page.
 */
class WUT_Widget_Recent_Posts extends WP_Widget {

	/**
	 * Set the name and description of the widget.
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'NEW! List the recent posts and provide some advanced options', 'wut' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( '', __( 'WUT:Recent Posts', 'wut' ), $widget_ops );
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
			'limit'   => $instance['number'],
			'offset'  => 0,
			'before'  => '<li>',
			'after'   => '</li>',
			'type'    => 'post',
			'skips'   => '',
			'none'    => __( 'No Posts.' ),
			'orderby' => 'post_date',
			'xformat' => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>',
		);

		$tag_args['xformat'] .= $instance['show_comment_count'] ? '(%commentcount%)' : '';
		$tag_args['xformat'] .= $instance['show_date'] ? '<span class="post-date">%postdate%</span>' : '';

		$tag_args['password'] = $instance['show_password_protected'] ? 'show' : 'hide';

		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
		echo '<ul>', wut_recent_posts( $tag_args ), '</ul>';
		echo $args['after_widget'];
	}

	/**
	 * Update the settings of this instance.
	 *
	 * @param array $new_instance New set settings.
	 * @param array $old_instance Original set settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                            = $old_instance;
		$instance['title']                   = sanitize_text_field( $new_instance['title'] );
		$instance['number']                  = absint( $new_instance['number'] );
		$instance['show_date']               = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$instance['show_comment_count']      = isset( $new_instance['show_comment_count'] ) ? (bool) $new_instance['show_comment_count'] : false;
		$instance['show_password_protected'] = isset( $new_instance['show_password_protected'] ) ? (bool) $new_instance['show_password_protected'] : false;
		return $instance;
	}

	/**
	 * The widget panel in admin page.
	 *
	 * @param array $instance The settings of this widget instance.
	 */
	public function form( $instance ) {
		$title                   = isset( $instance['title'] ) && ! empty( trim( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		$number                  = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date               = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
		$show_comment_count      = isset( $instance['show_comment_count'] ) ? (bool) $instance['show_comment_count'] : false;
		$show_password_protected = isset( $instance['show_password_protected'] ) ? (bool) $instance['show_password_protected'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_comment_count ); ?> id="<?php echo $this->get_field_id( 'show_comment_count' ); ?>" name="<?php echo $this->get_field_name( 'show_comment_count' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_comment_count' ); ?>"><?php _e( 'Display comment count?' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_password_protected ); ?> id="<?php echo $this->get_field_id( 'show_password_protected' ); ?>" name="<?php echo $this->get_field_name( 'show_password_protected' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_password_protected' ); ?>"><?php _e( 'Display password protected posts?' ); ?></label>
		</p>
		<?php
	}
}

/* vim: set et=off ts=4 sw=4 */
