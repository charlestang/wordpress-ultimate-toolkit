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
            'title'     => 'WUT Recent Posts',
            'limit'     => 10,
            'offset'    => 0,
            'before'    => '<li>',
            'after'     => '</li>',
            'type'      => 'both'
        );
        $options = get_option('wut-widget-recent-posts');
        $options = wp_parse_args($options, $defaults);
        if ($_POST['wut-recent-posts-submit']){
            $options['title'] = strip_tags($_POST['wut-recent-posts-title']);
            $options['limit'] = intval($_POST['wut-recent-posts-limit']);
            $options['offset'] = intval($_POST['wut-recent-posts-offset']);
            $options['before'] = stripslashes($_POST['wut-recent-posts-before']);
            $options['after'] = stripslashes($_POST['wut-recent-posts-after']);
            $options['type'] = $_POST['wut-recent-posts-type'];
            update_option('wut-widget-recent-posts',$options);
        }
        ?>
        <table>
            <tr>
                <td class="alignright"><?php _e('Widget title:', 'wut');?></td>
                <td><input id="wut-recent-posts-title" name="wut-recent-posts-title" type="text" value="<?php echo htmlspecialchars(stripslashes($options['title']));?>"/></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Number of posts:', 'wut');?></td>
                <td><input id="wut-recent-posts-limit" name="wut-recent-posts-limit" type="text" value="<?php echo $options['limit'];?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Offset:', 'wut');?></td>
                <td><input id="wut-recent-posts-offset" name="wut-recent-posts-offset" type="text" value="<?php echo $options['offset'];?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('HTML tags before a item:', 'wut');?></td>
                <td><input id="wut-recent-posts-before" name="wut-recent-posts-before" type="text" value="<?php echo htmlspecialchars($options['before']);?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('HTML tags after a item:', 'wut');?></td>
                <td><input type="text" id="wut-recent-posts-after" name="wut-recent-posts-after" value="<?php echo htmlspecialchars($options['after']);?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Post type to show:', 'wut');?></td>
                <td>
                    <p><input type="radio" id="wut-recent-posts-type" name="wut-recent-posts-type" value="both" <?php if($options['type'] == 'both') echo 'checked="checked"';?>/>both</p>
                    <p><input type="radio" id="wut-recent-posts-type" name="wut-recent-posts-type" value="page" <?php if($options['type'] == 'page') echo 'checked="checked"';?>/>page only</p>
                    <p><input type="radio" id="wut-recent-posts-type" name="wut-recent-posts-type" value="post" <?php if($options['type'] == 'post') echo 'checked="checked"';?>/>post only</p>
                </td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Posts to exclude:', 'wut');?></td>
                <td><input type="text" id="wut-recent-posts-skips" name="wut-recent-posts-skips" value="" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('If the list is empty, show:','wut');?></td>
                <td><input type="text" id="wut-recent-posts-none" name="wut-recent-posts-none" value="No recent posts" /></td>
            </tr>
        </table>
        <input id="wut-recent-posts-submit" name="wut-recent-posts-submit" type="hidden" value="1"/>
        <?php
    }
    
    $widget_ops =  array(
        'classname'     => 'wut-widget-recent-posts',
        'description'   => __('List the recent posts and provide some advanced options', 'wut')
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
	wp_register_widget_control($id, $name, $control_cb, $control_ops);
}

function wut_widget_random_posts_init(){
    function wut_widget_random_posts_body($args){
        extract($args);
        $options = get_option('wut-widget-random-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_random_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_random_posts_control(){
        $defaults = array(
            'title'     => 'WUT Random Posts',
            'limit'     => 10,
            'before'    => '<li>',
            'after'     => '</li>',
            'showexcerpt'   => '1'
        );
        $options = get_option('wut-widget-random-posts');
        $options = wp_parse_args($options, $defaults);
        if ($_POST['wut-random-posts-submit']){
            $options['title'] = $_POST['wut-random-posts-title'];
            $options['limit'] = intval($_POST['wut-random-posts-limit']);
            $options['before'] = stripslashes($_POST['wut-random-posts-before']);
            $options['after'] = stripslashes($_POST['wut-random-posts-after']);
            $options['showexcerpt'] = $_POST['wut-random-posts-showexcerpt'];
            update_option('wut-widget-random-posts',$options);
        }
        ?>
        <table>
            <tbody>
                <tr>
                    <td class="alignright"><?php _e('Widget title:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-title" name="wut-random-posts-title" value="<?php echo $options['title'];?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Number of posts:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-limit" name="wut-random-posts-limit" value="<?php echo $options['limit'];?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('HTML tags before a item:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-before" name="wut-random-posts-before" value="<?php echo htmlspecialchars($options['before']);?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('HTML tags after a item:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-after" name="wut-random-posts-after" value="<?php echo htmlspecialchars($options['after']);?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Show excerpt in link\'s title:', 'wut');?></td>
                    <td>
                        <input type="hidden" id="wut-random-posts-showexcerpt" name="wut-random-posts-showexcerpt" value="0" />
                        <input type="checkbox" id="wut-random-posts-showexcerpt" name="wut-random-posts-showexcerpt" value="1" <?php if($options['showexcerpt']) echo 'checked="checked"';?> /><?php _e('Check to enable.','wut');?>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Posts to exclude:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-skips" name="wut-random-posts-skips" value="" /></td>
                </tr>
            </tbody>
        </table>
        <input id="wut-random-posts-submit" name="wut-random-posts-submit" type="hidden" value="1"/>
        <?php
    }

    $widget_ops =  array(
        'classname'     => 'wut-widget-random-posts',
        'description'   => __( 'List the random posts', 'wut')
    );
    $control_ops = array(
        'width'     => 400,
        'height'    => 200
    );
    $id     = 'wut-widget-random-posts';
    $name   = __('WUT Random Posts','wut');
    $widget_cb  = 'wut_widget_random_posts_body';
    $control_cb = 'wut_widget_random_posts_control';
	// Register Widgets
	wp_register_sidebar_widget($id, $name, $widget_cb, $widget_ops);
	wp_register_widget_control($id, $name, $control_cb, $control_ops);
}

function wut_widget_related_posts_init(){
    function wut_widget_related_posts_body(){
        extract($args);
        $options = get_option('wut-widget-related-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_relateds_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_related_posts_control(){
        
    }
}

function wut_widget_same_classified_posts_init(){
    function wut_widget_same_classified_posts_body(){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_same_classified_posts_control(){

    }
}

function wut_widget_most_commented_posts_init(){
    function wut_widget_most_commented_posts_body(){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_most_commented_posts_control(){

    }
}

function wut_widget_recent_comments_init(){
    function wut_widget_recent_comments_body(){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_recent_comments_control(){

    }
}

function wut_widget_active_commentators_init(){
    function wut_widget_active_commentators_body(){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_active_commentators_control(){

    }
}

function wut_widget_recent_commentators_init(){
    function wut_widget_recent_commentators_body(){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_recent_commentators_control(){

    }
}

function wut_widget_advanced_blogroll_init(){
    function wut_widget_advanced_blogroll_body(){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_advanced_blogroll_control(){

    }
}
?>
