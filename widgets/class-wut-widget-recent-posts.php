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
	 * The form helper.
	 *
	 * @var WUT_Form_Helper the form helper to generate the form control.
	 */
	protected $helper;

	/**
	 * Set the name and description of the widget.
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'NEW! List the recent posts and provide some advanced options', 'wut' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( '', __( 'WUT:Recent Posts', 'wut' ), $widget_ops );
		$this->helper = new WUT_Form_Helper( $this );
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
		$date_format      = $this->helper->default( $instance, 'date_format', 'string', $site_date_format );
		if ( 'custom' === $date_format ) {
			$date_format = $instance['custom_format'];
		}

		$tag_args = array(
			'limit'       => $instance['number'],
			'date_format' => $date_format,
			'xformat'     => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>',
		);

		$instance['show_date']          = $this->helper->default( $instance, 'show_date', 'bool', false );
		$instance['show_comment_count'] = $this->helper->default( $instance, 'show_comment_count', 'bool', false );
		$instance['date_before_title']  = $this->helper->default( $instance, 'date_before_title', 'bool', false );
		$tag_args['xformat']           .= ( $instance['show_comment_count'] ? ' (%commentcount%)' : '' );
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
		$instance['title']              = $this->helper->default( $new_instance, 'title', 'string', '' );
		$instance['number']             = $this->helper->default( $new_instance, 'number', 'uint', 5 );
		$instance['show_date']          = $this->helper->default( $new_instance, 'show_date', 'bool', false );
		$instance['date_before_title']  = $this->helper->default( $new_instance, 'date_before_title', 'bool', false );
		$site_date_format               = get_option( 'date_format' );
		$instance['date_format']        = $this->helper->default( $new_instance, 'date_format', 'string', $site_date_format );
		$instance['custom_format']      = $this->helper->default( $new_instance, 'custom_format', 'string', $site_date_format );
		$instance['show_comment_count'] = $this->helper->default( $new_instance, 'show_comment_count', 'bool', false );
		return $instance;
	}

	/**
	 * The widget panel in admin page.
	 *
	 * @param array $instance The settings of this widget instance.
	 */
	public function form( $instance ) {
		$title              = $this->helper->default( $instance, 'title', 'string', '' );
		$number             = $this->helper->default( $instance, 'number', 'uint', 5 );
		$show_date          = $this->helper->default( $instance, 'show_date', 'bool', false );
		$date_before_title  = $this->helper->default( $instance, 'date_before_title', 'bool', false );
		$site_date_format   = get_option( 'date_format' );
		$date_format        = $this->helper->default( $instance, 'date_format', 'string', $site_date_format );
		$custom_format      = $this->helper->default( $instance, 'custom_format', 'string', $site_date_format );
		$show_comment_count = $this->helper->default( $instance, 'show_comment_count', 'bool', false );
		?>
		<?php $this->helper->text( 'title', $title, __( 'Title:' ) ); ?>

		<?php $this->helper->text( 'number', $number, __( 'Number of posts to show:' ), 'number', 'tiny-text' ); ?>

		<?php $this->helper->checkbox( 'show_date', $show_date, __( 'Display post date?' ) ); ?>

		<?php $this->helper->checkbox( 'date_before_title', $date_before_title, __( 'Show date before title?' ) ); ?>

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

		<?php $this->helper->checkbox( 'show_comment_count', $show_comment_count, __( 'Display comment count?', 'wut' ) ); ?>
		<?php
	}
}
