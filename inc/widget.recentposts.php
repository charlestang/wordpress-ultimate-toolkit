<?php

class WUT_Widget_Recent_Posts extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array(
            'classname'                   => 'wut-widget-recent-posts',
            'description'                 => __('List the recent posts and provide some advanced options', 'wut'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('wut-widget-recent-posts', __('WUT Recent Posts', 'wut'), $widget_ops);
    }

    public function widget($args, $instance)
    {
        if (is_numeric($widget_args)) {
            $widget_args = array('number' => $widget_args);
        }
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);

        $options = get_option('wut-widget-recent-posts');

        if (!isset($options[$number])) {
            return;
        }
        $title = $options[$number]['title'];

        $tag_args = array(
            'limit'    => $options[$number]['limit'],
            'offset'   => $options[$number]['offset'],
            'before'   => $options[$number]['before'],
            'after'    => $options[$number]['after'],
            'type'     => $options[$number]['type'],
            'skips'    => $options[$number]['skips'],
            'none'     => $options[$number]['none'],
            'password' => $options[$number]['password'],
            'orderby'  => $options[$number]['orderby'],
            'xformat'  => $options[$number]['xformat']
        );

        $paged = intval(get_query_var('paged'));

        if (empty($paged) || $paged == 0) {
            $paged = 1;
        }

        if ($paged > 1) {
            $tag_args['offset'] = 0;
        }

        echo $args['before_widget'];
        echo $args['before_title'] . $title . $args['after_title'];
        echo '<ul>', wut_recent_posts($tag_args), '</ul>';
        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    public function form($instance)
    {
        echo '<p class="no-options-widget">' . __('There are no options for this widget.') . '</p>';
        return 'noform';
    }
}
