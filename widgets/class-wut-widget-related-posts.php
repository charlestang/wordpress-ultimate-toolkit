<?php
/**
 * Widget: WUT_Widget_Related_Posts class.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage widgets
 */

/**
 * Define a widget show related posts in single post and page.
 *
 * This related posts are calculated by taxonomy data.
 */
class WUT_Widget_Related_Posts extends WP_Widget {

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
			'description'                 => __( 'NEW! List the related posts in SINGLE POST PAGE ONLY.', 'wut' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( '', __( 'WUT:Related Posts', 'wut' ), $widget_ops );
		$this->helper = new WUT_Form_Helper( $this );
	}

	/**
	 * Printed related posts list on single page.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance User set arguments.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		// related posts list is shown only in single page.
		if ( ! is_single() ) {
			return;
		}

		$tag_args = array(
			'limit'      => $instance['number'],
			'before'     => '<li>',
			'after'      => '</li>',
			'type'       => 'post',
			'skips'      => '',
			'leastshare' => true,
			'password'   => 'hide',
			'orderby'    => 'post_date',
			'order'      => 'DESC',
			'xformat'    => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
			'none'       => __( 'No related posts.', 'wut' ),
		);

		echo $args['before_widget'], $args['before_title'], $instance['title'], $args['after_title'];
		echo '<ul>', wut_related_posts( $tag_args ), '</ul>';
		echo $args['after_widget'];
	}

	/**
	 * Update arguments of this widget.
	 *
	 * @param array $new_instance New submitted arguments.
	 * @param array $old_instance Original arguments.
	 * @return array Filtered arguments to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		return $new_instance;
	}

	/**
	 * Widget panel.
	 *
	 * @param array $instance Current arguments.
	 * @return void
	 */
	public function form( $instance ) {
		$title              = $this->helper->default( $instance, 'title', 'string', '' );
		$number             = $this->helper->default( $instance, 'number', 'uint', 5 );
		$show_comment_count = $this->helper->default( $instance, 'show_comment_count', 'bool', true );
		$this->helper->text( 'title', $title, __( 'Title:' ) );
		$this->helper->text( 'number', $number, __( 'Number of posts to show:' ), 'number', 'tiny-text' );
		$this->helper->checkbox( 'show_comment_count', $show_comment_count, __( 'Show comment count:', 'wut' ) );
	}
}
