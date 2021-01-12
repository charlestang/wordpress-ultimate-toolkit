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
		$title            = $instance['title'];
		$site_date_format = get_option( 'date_format' );
		$date_format      = empty( $instance['date_format'] ) ? $site_date_format : $instance['date_format'];
		if ( 'custom' === $date_format ) {
			$date_format = $instance['custom_format'];
		}

		$tag_args = array(
			'limit'       => $instance['number'],
			'date_format' => $date_format,
			'xformat'     => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>',
		);

		$tag_args['xformat'] .= $instance['show_comment_count'] ? ' (%commentcount%)' : '';
		if ( $instance['show_date'] ) {
			if ( $instance['date_before_title'] ) {
				$tag_args['xformat'] = '<span class="post-date">%postdate%</span>&nbsp;' . $tag_args['xformat'];
			} else {
				$tag_args['xformat'] .= '&nbsp;<span class="post-date">%postdate%</span>';
			}
		}

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
		$instance                       = $old_instance;
		$instance['title']              = sanitize_text_field( $new_instance['title'] );
		$instance['number']             = absint( $new_instance['number'] );
		$instance['show_date']          = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$instance['date_before_title']  = isset( $new_instance['date_before_title'] ) ? (bool) $new_instance['date_before_title'] : false;
		$site_date_format               = get_option( 'date_format' );
		$instance['date_format']        = isset( $new_instance['date_format'] ) ? wp_strip_all_tags( $new_instance['date_format'] ) : $site_date_format;
		$instance['custom_format']      = isset( $new_instance['custom_format'] ) ? wp_strip_all_tags( $new_instance['custom_format'] ) : $site_date_format;
		$instance['show_comment_count'] = isset( $new_instance['show_comment_count'] ) ? (bool) $new_instance['show_comment_count'] : false;
		return $instance;
	}

	/**
	 * The widget panel in admin page.
	 *
	 * @param array $instance The settings of this widget instance.
	 */
	public function form( $instance ) {
		$title              = isset( $instance['title'] ) && ! empty( trim( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		$number             = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date          = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
		$date_before_title  = isset( $instance['date_before_title'] ) ? (bool) $instance['date_before_title'] : false;
		$date_format        = isset( $instance['date_format'] ) ? wp_strip_all_tags( $instance['date_format'] ) : '';
		$site_date_format   = get_option( 'date_format' );
		$custom_format      = isset( $instance['custom_format'] ) ? wp_strip_all_tags( $instance['custom_format'] ) : $site_date_format;
		$show_comment_count = isset( $instance['show_comment_count'] ) ? (bool) $instance['show_comment_count'] : false;
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
			<input class="checkbox" type="checkbox"<?php checked( $date_before_title ); ?> id="<?php echo $this->get_field_id( 'date_before_title' ); ?>" name="<?php echo $this->get_field_name( 'date_before_title' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'date_before_title' ); ?>"><?php _e( 'Show date before title?', 'wut' ); ?></label>
		</p>

		<p>
			<span><?php _e( 'Date format:', 'wut' ); ?></span><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'date_format' ); ?>"<?php checked( $date_format, '' ); ?> value=""/>
				<span style="display:inline-block;min-width:10em;"><?php echo date( $site_date_format ); ?></span>
				<code><?php echo $site_date_format; ?></code>
			</label><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'date_format' ); ?>"<?php checked( $date_format, 'M d' ); ?> value="M d"/>
				<span style="display:inline-block;min-width:10em;"><?php echo date( 'M d' ); ?></span>
				<code>M d</code>
			</label><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'date_format' ); ?>"<?php checked( $date_format, 'd F y' ); ?> value="d F y"/>
				<span style="display:inline-block;min-width:10em;"><?php echo date( 'd F y' ); ?></span>
				<code>d F y</code>
			</label><br/>
			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'date_format' ); ?>"<?php checked( $date_format, 'custom' ); ?> value="custom"/>
				<span style="display:inline-block;min-width:10em;"><?php _e( 'Custom', 'wut' ); ?></span>
				<input class="medium-text" id="<?php echo $this->get_field_id( 'custom_format' ); ?>" name="<?php echo $this->get_field_name( 'custom_format' ); ?>" type="text" step="1" min="1" value="<?php echo $custom_format; ?>" size="6" />
			</label><br/>
			<strong><?php _e( 'Preview: ', 'wut' ); ?></strong><span><?php echo 'custom' === $date_format ? date( $custom_format ) : date( '' === $date_format ? $site_date_format : $date_format ); ?></span>
		</p>

		<p>
			<input class="checkbox" type="checkbox"<?php checked( $show_comment_count ); ?> id="<?php echo $this->get_field_id( 'show_comment_count' ); ?>" name="<?php echo $this->get_field_name( 'show_comment_count' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_comment_count' ); ?>"><?php _e( 'Display comment count?', 'wut' ); ?></label>
		</p>
		<?php
	}
}

/* vim: set et=off ts=4 sw=4 */
