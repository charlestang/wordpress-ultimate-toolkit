<?php

class WUT_Widget_Recent_Posts extends WP_Widget {


	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'NEW! List the recent posts and provide some advanced options', 'wut' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( '', __( 'WUT Recent Posts', 'wut' ), $widget_ops );
	}

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

		$tag_args['xformat'] .= $instance['showCommentCount'] ? '(%commentcount%)' : '';
		$tag_args['xformat'] .= $instance['showDate'] ? '<span class="post-date">%postdate%</span>' : '';

		$tag_args['password'] = $instance['showPasswordProtected'] ? 'show' : 'hide';

		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
		echo '<ul>', wut_recent_posts( $tag_args ), '</ul>';
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance                          = $old_instance;
		$instance['title']                 = sanitize_text_field( $new_instance['title'] );
		$instance['number']                = absint( $new_instance['number'] );
		$instance['showDate']              = isset( $new_instance['showDate'] ) ? (bool) $new_instance['showDate'] : false;
		$instance['showCommentCount']      = isset( $new_instance['showCommentCount'] ) ? (bool) $new_instance['showCommentCount'] : false;
		$instance['showPasswordProtected'] = isset( $new_instance['showPasswordProtected'] ) ? (bool) $new_instance['showPasswordProtected'] : false;
		return $instance;
	}

	public function form( $instance ) {
		$title                 = isset( $instance['title'] ) && ! empty( trim( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		$number                = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$showDate              = isset( $instance['showDate'] ) ? (bool) $instance['showDate'] : false;
		$showCommentCount      = isset( $instance['showCommentCount'] ) ? (bool) $instance['showCommentCount'] : false;
		$showPasswordProtected = isset( $instance['showPasswordProtected'] ) ? (bool) $instance['showPasswordProtected'] : false;
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
			<input class="checkbox" type="checkbox"<?php checked( $showDate ); ?> id="<?php echo $this->get_field_id( 'showDate' ); ?>" name="<?php echo $this->get_field_name( 'showDate' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'showDate' ); ?>"><?php _e( 'Display post date?' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $showCommentCount ); ?> id="<?php echo $this->get_field_id( 'showCommentCount' ); ?>" name="<?php echo $this->get_field_name( 'showCommentCount' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'showCommentCount' ); ?>"><?php _e( 'Display comment count?' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $showPasswordProtected ); ?> id="<?php echo $this->get_field_id( 'showPasswordProtected' ); ?>" name="<?php echo $this->get_field_name( 'showPasswordProtected' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'showPasswordProtected' ); ?>"><?php _e( 'Display password protected posts?' ); ?></label>
		</p>
		<?php
	}
}
