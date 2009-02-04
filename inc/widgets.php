<?php
/**
 * This file is for all the widgets included in this plugin.
 */

function wut_widget_recent_posts_init(){
    function wut_widget_recent_posts_body($args, $widget_args = 1){
        extract($args, EXTR_SKIP);
        if (is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);
        
        $options = get_option('wut-widget-recent-posts');

        if(!isset($options[$number]))
            return;
        $title = $options[$number]['title'];
        $limit = $options[$number]['limit'];
        $offset = $options[$number]['offset'];
        $before = $options[$number]['before'];
        $after = $options[$number]['after'];
        $type = $options[$number]['type'];

        $tag_args = array(
            'limit'         => $limit,           //how many items should be show
            'offset'        => $offset,
            'before'        => $before,
            'after'         => $after,
            'type'          => $type,      //'post' or 'page' or 'both'
            'skips'         => '',          //comma seperated post_ID list
            'none'          => '',          //tips to show when results is empty
            'echo'          => 1
        );
        echo $before_widget, $before_title, $title, $after_title;
        echo '<ul>', wut_recent_posts($tag_args), '</ul>';
        echo $after_widget;
    }
    function wut_widget_recent_posts_control($widget_args){
        global $wp_registered_widgets;
        static $update = false;
        if(is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);

        $widget_args = wp_parse_args($widget_args, array('number' => -1));

        extract($widget_args, EXTR_SKIP);

        $options = get_option('wut-widget-recent-posts');

        if(!is_array($options))
            $options = array();

        if(!$update && !empty($_POST['sidebar'])){
            $sidebar = (string) $_POST['sidebar'];
            $sidebars_widgets = wp_get_sidebars_widgets();
            if(isset($sidebars_widgets[$sidebar])){
                $this_sidebar =& $sidebars_widgets[$sidebar];
            }else{
                $this_sidebar = array();
            }
            foreach ($this_sidebar as $_widget_id){
                if ('wut_get_recent_posts_body' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])){
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("wut-widget-recent-posts-$widget_number")){
                        unset($options[$widget_number]);
                    }
                }
            }
            foreach((array)$_POST['wut-recent-posts'] as $widget_number => $posted){
                $options_this['title'] = strip_tags(stripslashes($posted['title']));
                $options_this['limit'] = intval($posted['limit']);
                $options_this['offset'] = intval($posted['offset']);
                $options_this['before'] = stripslashes($posted['before']);
                $options_this['after'] = stripslashes($posted['after']);
                $options_this['type'] = $posted['type'];
                $options[$widget_number] = $options_this;
            }
            update_option('wut-widget-recent-posts',$options);
            $update = true;
        }
        if ($number == -1){
            $title = 'WUT Recent Posts';
            $limit = 10;
            $offset = 0;
            $before = '<li>';
            $after = '</li>';
            $type = 'both';
            $number = '%i%';
        }else{
            $title = attribute_escape($options[$number]['title']);
            $limit = $options[$number]['limit'];
            $offset = $options[$number]['offset'];
            $before = attribute_escape($options[$number]['before']);
            $after = attribute_escape($options[$number]['after']);
            $type = $options[$number]['type'];
        }
        ?>
        <table>
            <tr>
                <td class="alignright"><?php _e('Widget title:', 'wut');?></td>
                <td><input id="wut-recent-posts-title-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][title]" type="text" value="<?php echo $title;?>"/></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Number of posts:', 'wut');?></td>
                <td><input id="wut-recent-posts-limit-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][limit]" type="text" value="<?php echo $limit;?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Offset:', 'wut');?></td>
                <td><input id="wut-recent-posts-offset-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][offset]" type="text" value="<?php echo $offset;?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('HTML tags before a item:', 'wut');?></td>
                <td><input id="wut-recent-posts-before-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][before]" type="text" value="<?php echo $before;?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('HTML tags after a item:', 'wut');?></td>
                <td><input type="text" id="wut-recent-posts-after-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][after]" value="<?php echo $after;?>" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Post type to show:', 'wut');?></td>
                <td>
                    <p><input type="radio" id="wut-recent-posts-type-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][type]" value="both" <?php if($type == 'both') echo 'checked="checked"';?>/>both</p>
                    <p><input type="radio" id="wut-recent-posts-type-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][type]" value="page" <?php if($type == 'page') echo 'checked="checked"';?>/>page only</p>
                    <p><input type="radio" id="wut-recent-posts-type-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][type]" value="post" <?php if($type == 'post') echo 'checked="checked"';?>/>post only</p>
                </td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('Posts to exclude:', 'wut');?></td>
                <td><input type="text" id="wut-recent-posts-skips-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][skips]" value="" /></td>
            </tr>
            <tr>
                <td class="alignright"><?php _e('If the list is empty, show:','wut');?></td>
                <td><input type="text" id="wut-recent-posts-none-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][none]" value="No recent posts" /></td>
            </tr>
        </table>
        <input id="wut-recent-posts-submit-<?php echo $number;?>" name="wut-recent-posts-submit-<?php echo $number;?>" type="hidden" value="1"/>
        <?php
    }

    if (!$options = get_option('wut-widget-recent-posts')){
        $options = array();
    }
    $widget_ops =  array(
        'classname'     => 'wut-widget-recent-posts',
        'description'   => __('List the recent posts and provide some advanced options', 'wut')
    );
    $control_ops = array(
        'width'     => 400,
        'height'    => 200,
        'id_base'   => 'wut-widget-recent-posts'
    );

    $name   = __('WUT Recent Posts','wut');
    $widget_cb  = 'wut_widget_recent_posts_body';
    $control_cb = 'wut_widget_recent_posts_control';
	// Register Widgets
    $registerd = false;
    foreach(array_keys($options) as $o){
        if(!isset($options[$o]['title'])){
            continue;
        }
        $id = "wut-widget-recent-posts-$o";
        $registerd = true;
        wp_register_sidebar_widget($id, $name, $widget_cb, $widget_ops, array('number'=>$o));
        wp_register_widget_control($id, $name, $control_cb, $control_ops, array('number'=>$o));
    }
	if(!$registered){
        wp_register_sidebar_widget("wut-widget-recent-posts-1", $name, $widget_cb, $widget_ops, array('number'=>-1));
        wp_register_widget_control("wut-widget-recent-posts-1", $name, $control_cb, $control_ops, array('number'=>-1));
    }
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
