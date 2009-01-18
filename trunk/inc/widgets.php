<?php
/**
 * This file is for all the widgets included in this plugin.
 */

function wut_widget_recent_posts_init(){
    function wut_widget_recent_posts_body($args){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_recent_posts_control(){
        $defaults = array(
            'title'     => 'WUT Recent Posts'
        );
        $options = get_option('wut-widget-recent-posts');
        $options = wp_parse_args($options, $defaults);
        if ($_POST['wut-recent-posts-submit']){
            $options['title'] = $_POST['wut-recent-posts-title'];
            update_option('wut-widget-recent-posts',$options);
        }
    }
    
    $widget_ops =  array(
        'classname'     => 'wut-widget-recent-posts',
        'description'   => __( 'List the recent posts and provide some advanced options', 'wut')
    );
    $control_ops = array(
        'width'     => 400,
        'height'    => 200
    );
    $id     = 'wut-widget-recent-posts';
    $name   = __('WUT Recent Posts','wut');
    $widget_cb  = 'wut_widget_recent_posts_body';
    $control_cb = 'wut_widget_recent_posts_control';
	// Register Widgets
	wp_register_sidebar_widget($id, $name, $widget_cb, $widget_ops);
	wp_register_widget_control($id,$name, $control_cb, $control_ops);
}

function wut_widget_random_posts_init(){

}

function wut_widget_related_posts_init(){

}

function wut_widget_same_classified_posts_init(){

}

function wut_widget_most_commented_posts_init(){

}

function wut_widget_recent_comments_init(){

}

function wut_widget_active_commentators_init(){

}

function wut_widget_recent_commentators_init(){

}

function wut_widget_advanced_blogroll_init(){

}
?>
