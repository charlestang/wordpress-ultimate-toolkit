<?php
/**
 * Widget: WUT_Widget_Recent_Comments class.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage widgets
 */

/**
 * Define a widget to show recent comments on index, single, and page.
 */
class WUT_Widget_Recent_Comments extends WP_Widget {

	/**
	 * To set the name and description of this widget.
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'NEW! List recent comments.', 'wut' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( '', __( 'WUT:Recent Comments', 'wut' ), $widget_ops );
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
			'limit'       => $instance['number'],
			'before'      => '<li>',
			'after'       => '</li>',
			'length'      => 50,
			'posttype'    => 'post',
			'commenttype' => 'comment',
			'skipusers'   => '',
			'avatarsize'  => $instance['avatar_size'],
			'none'        => __( 'No comments.', 'wut' ),
			'password'    => 'hide',
			'xformat'     => '<a class="commentator" href="%permalink%" >%commentauthor%</a>',
		);

		if ( $instance['show_avatar'] ) {
			$tag_args['xformat'] = '%gravatar%' . $tag_args['xformat'];
		}

		if ( $instance['show_content'] ) {
			$tag_args['xformat'] .= ' : %commentexcerpt%';
		} else {
			$tag_args['xformat'] .= __( ' on ', 'wut' ) . '<<%posttile>>';
		}

		echo $args['before_widget'];
		echo $args['before_title'], $title, $args['after_title'];
		echo '<ul>', wut_recent_comments( $tag_args ), '</ul>';
		echo $args['after_widget'];
	}

	/**
	 * Update the settings of this instance.
	 *
	 * @param array $new_instance New set settings.
	 * @param array $old_instance Original set settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['number']       = intval( $new_instance['number'] );
		$instance['show_content'] = (bool) $new_instance['show_content'];
		$instance['show_avatar']  = (bool) $new_instance['show_avatar'];
		$instance['avatar_size']  = intval( $new_instance['avatar_size'] );
		return $new_instance;
	}

	/**
	 * The widget panel in admin page.
	 *
	 * @param array $instance The settings of this widget instance.
	 */
	public function form( $instance ) {
		$title        = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number       = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_content = isset( $instance['show_content'] ) ? (bool) $instance['show_content'] : true;
		$show_avatar  = isset( $instance['show_avatar'] ) ? (bool) $instance['show_avatar'] : false;
		$avatar_size  = isset( $instance['avatar_size'] ) ? absint( $instance['avatar_size'] ) : 16; ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:', 'wut' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php echo __( 'Number of comments to show:', 'wut' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_content ); ?> id="<?php echo $this->get_field_id( 'show_content' ); ?>" name="<?php echo $this->get_field_name( 'show_content' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php echo __( 'Display comment content?', 'wut' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_avatar ); ?> id="<?php echo $this->get_field_id( 'show_avatar' ); ?>" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>"><?php echo __( 'Display avatar?', 'wut' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'avatar_size' ); ?>"><?php echo __( 'The size of avatar: ', 'wut' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'avatar_size' ); ?>" name="<?php echo $this->get_field_name( 'avatar_size' ); ?>" type="number" step="1" min="1" value="<?php echo $avatar_size; ?>" size="3" />
		</p>
		<?php
	}
}

/* vim: set et=off ts=4 sw=4 */
