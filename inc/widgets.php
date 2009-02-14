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

        $tag_args = array(
            'limit'         => $options[$number]['limit'],
            'offset'        => $options[$number]['offset'],
            'before'        => $options[$number]['before'],
            'after'         => $options[$number]['after'],
            'type'          => $options[$number]['type'],
            'skips'         => $options[$number]['skips'],
            'none'          => $options[$number]['none'],
            'password'      => $options[$number]['password'],
            'orderby'       => $options[$number]['orderby'],
            'xformat'       => $options[$number]['xformat']
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
                if ('wut_widget_recent_posts_body' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])){
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("wut-widget-recent-posts-$widget_number",$_POST['widget-id'])){
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
                $options_this['skips'] = $posted['skips'];
                $options_this['none'] = strip_tags(stripslashes($posted['none']));
                $options_this['password'] = $posted['password'];
                $options_this['orderby'] = $posted['orderby'];
                $options_this['xformat'] = stripcslashes($posted['xformat']);
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
            $skips = '';
            $none = 'No Posts.';
            $password = 'hide';
            $orderby = 'post_date';
            $xfomat = '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)';
            $number = '%i%';
        }else{
            $title = attribute_escape($options[$number]['title']);
            $limit = $options[$number]['limit'];
            $offset = $options[$number]['offset'];
            $before = attribute_escape($options[$number]['before']);
            $after = attribute_escape($options[$number]['after']);
            $type = $options[$number]['type'];
            $skips = $options[$number]['skips'];
            $none = attribute_escape($options[$number]['none']);
            $password = $options[$number]['password'];
            $orderby = $options[$number]['orderby'];
            $xformat = attribute_escape($options[$number]['xformat']);
        }
        ?>
        <table>
            <tbody>
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
                <tr>
                    <td class="alignright"><?php _e('Show password protected post?','wut');?></td>
                    <td>
                        <p><input type="radio" id="wut-recent-posts-password-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][password]" value="show" <?php if($password == 'show') echo 'checked="checked"';?> />Show.</p>
                        <p><input type="radio" id="wut-recent-posts-password-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][password]" value="hide" <?php if($password == 'hide') echo 'checked="checked"';?> />Hide.</p>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Order by:','wut');?></td>
                    <td>
                        <p><input type="radio" id="wut-recent-posts-orderby-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][orderby]" value="post_date" <?php if($orderby == 'post_date') echo 'checked="checked"';?> />Post date.</p>
                        <p><input type="radio" id="wut-recent-posts-orderby-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][orderby]" value="post_modified" <?php if($orderby == 'post_modified') echo 'checked="checked"';?> />Post modified date.</p>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('The list item format:', 'wut');?></td>
                    <td><input id="wut-recent-posts-xformat-<?php echo $number;?>" name="wut-recent-posts[<?php echo $number;?>][xformat]" type="text" value="<?php echo $xformat;?>" /></td>
                </tr>
            </tbody>
        </table>
        <input id="wut-recent-posts-submit-<?php echo $number;?>" name="wut-recent-posts-submit-<?php echo $number;?>" type="hidden" value="1"/>
        <?php
    }
    $options = get_option('wut-widget-recent-posts');
    if (!$options){
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
    function wut_widget_random_posts_body($args,$widget_args){
        extract($args, EXTR_SKIP);
        if (is_numeric($widget_args)){
            $widget_args = array('number'=>$widget_args);
        }
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args,EXTR_SKIP);
        
        $options = get_option('wut-widget-random-posts');

        if(!isset($options[$number])){
            return;
        }
        $tag_args = array(
            'limit'         => $options[$number]['limit'],
            'before'        => $options[$number]['before'],
            'after'         => $options[$number]['after'],
            'type'          => $options[$number]['type'],
            'skips'         => $options[$number]['skips'],
            'none'          => $options[$number]['none'],
            'password'      => $options[$number]['password'],
            'xformat'       => $options[$number]['xformat']
        );
        echo $before_widget, $before_title, $options[$number]['title'], $after_title;
        echo '<ul>', wut_random_posts($tag_args), '</ul>';
        echo $after_widget;
    }
    function wut_widget_random_posts_control($widget_args){
        global $wp_registered_widgets;
        static $update = false;
        if(is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);

        $options = get_option('wut-widget-random-posts');
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
                if ('wut_widget_random_posts_body' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])){
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("wut-widget-random-posts-$widget_number", $_POST['widget-id'])){
                        unset($options[$widget_number]);
                    }
                }
            }
            foreach((array)$_POST['wut-random-posts'] as $widget_number => $posted){
                $options_this['title'] = strip_tags(stripslashes($posted['title']));
                $options_this['limit'] = intval($posted['limit']);
                $options_this['none'] = stripslashes($posted['none']);
                $options_this['before'] = stripslashes($posted['before']);
                $options_this['after'] = stripslashes($posted['after']);
                $options_this['type'] = $posted['type'];
                $options_this['skips'] = $posted['skips'];
                $options_this['password'] = $posted['password'];
                $options_this['xformat'] = stripslashes($posted['xformat']);
                $options[$widget_number] = $options_this;
            }
            update_option('wut-widget-random-posts',$options);
            $update = true;
        }
        if ($number == -1){
            $title = 'WUT Random Posts';
            $limit = 10;
            $before = attribute_escape('<li>');
            $after = attribute_escape('</li>');
            $type = 'both';
            $skips = '';
            $none = 'No Posts.';
            $password = 'hide';
            $xformat = attribute_escape('<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)');
            $number = '%i%';
        }else{
            $title = attribute_escape($options[$number]['title']);
            $limit = $options[$number]['limit'];
            $before = attribute_escape($options[$number]['before']);
            $after = attribute_escape($options[$number]['after']);
            $none = attribute_escape($options[$number]['none']);
            $password = $options[$number]['password'];
            $xformat = attribute_escape($options[$number]['xformat']);
            $type = $options[$number]['type'];
            $skips = $options[$number]['skips'];
        }

        ?>
        <table>
            <tbody>
                <tr>
                    <td class="alignright"><?php _e('Widget title:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-title-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][title]" value="<?php echo $title;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Number of posts:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-limit-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][limit]" value="<?php echo $limit;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('HTML tags before a item:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-before-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][before]" value="<?php echo $before;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('HTML tags after a item:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-after-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][after]" value="<?php echo $after;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Posts to exclude:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-skips-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][skips]" value="" /></td>
                </tr>            
                <tr>
                    <td class="alignright"><?php _e('Post type to show:', 'wut');?></td>
                    <td>
                        <p><input type="radio" id="wut-random-posts-type-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][type]" value="both" <?php if($type == 'both') echo 'checked="checked"';?>/>both</p>
                        <p><input type="radio" id="wut-random-posts-type-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][type]" value="page" <?php if($type == 'page') echo 'checked="checked"';?>/>page only</p>
                        <p><input type="radio" id="wut-random-posts-type-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][type]" value="post" <?php if($type == 'post') echo 'checked="checked"';?>/>post only</p>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('If the list is empty, show:','wut');?></td>
                    <td><input type="text" id="wut-random-posts-none-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][none]" value="<?php echo $none;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Show password protected post?','wut');?></td>
                    <td>
                        <p><input type="radio" id="wut-random-posts-password-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][password]" value="show" <?php if($password == 'show') echo 'checked="checked"';?> />Show.</p>
                        <p><input type="radio" id="wut-random-posts-password-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][password]" value="hide" <?php if($password == 'hide') echo 'checked="checked"';?> />Hide.</p>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('The list item format:', 'wut');?></td>
                    <td><input type="text" id="wut-random-posts-xformat-<?php echo $number;?>" name="wut-random-posts[<?php echo $number;?>][xformat]" value="<?php echo $xformat;?>" /></td>
                </tr>
            </tbody>
        </table>
        <input id="wut-random-posts-submit-<?php echo $number;?>" name="wut-random-posts-submit-<?php echo $number;?>" type="hidden" value="1"/>
        <?php
    }
    $options = get_option('wut-widget-random-posts');
    if(!$options){
        $options = array();
    }

    $widget_ops =  array(
        'classname'     => 'wut-widget-random-posts',
        'description'   => __( 'List the random posts', 'wut')
    );
    $control_ops = array(
        'width'     => 400,
        'height'    => 200,
        'id_base'   => 'wut-widget-random-posts'
    );
    $name   = __('WUT Random Posts','wut');
    $widget_cb  = 'wut_widget_random_posts_body';
    $control_cb = 'wut_widget_random_posts_control';
    // Register Widgets
    $registered = false;
    foreach(array_keys($options) as $o){
        if(!isset($options[$o]['title'])){
            continue;
        }
        $id = "wut-widget-random-posts-$o";
        $registered = true;
        wp_register_sidebar_widget($id, $name, $widget_cb, $widget_ops, array('number'=>$o));
        wp_register_widget_control($id, $name, $control_cb, $control_ops, array('number'=>$o));
    }
    if (!$registered){
        wp_register_sidebar_widget("wut-widget-random-posts-1", $name, $widget_cb, $widget_ops, array('number'=>-1));
        wp_register_widget_control("wut-widget-random-posts-1", $name, $control_cb, $control_ops,array('number'=>-1));
    }
}

function wut_widget_related_posts_init(){
    function wut_widget_related_posts_body($args, $widget_args){
        if(!is_single()) return;
        extract($args, EXTR_SKIP);
        if (is_numeric($widget_args)){
            $widget_args = array('number'=>$widget_args);
        }
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args,EXTR_SKIP);

        $options = get_option('wut-widget-related-posts');

        if(!isset($options[$number])){
            return;
        }

        $tag_args = array(
            'limit'             => $options[$number]['limit'],
            'before'            => $options[$number]['before'],
            'after'             => $options[$number]['after'],
            'type'              => 'post',
            'skips'             => $options[$number]['skips'],
            'leastshare'        => $options[$number]['leastshare'],
            'password'          => $options[$number]['password'],
            'orderby'           => $options[$number]['orderby'],
            'order'             => $options[$number]['order'],
            'xformat'           => $options[$number]['xformat'],
            'none'              => $options[$number]['none']
        );

        echo $before_widget, $before_title, $options[$number]['title'], $after_title;
        echo '<ul>', wut_related_posts($tag_args), '</ul>';
        echo $after_widget;
    }
    function wut_widget_related_posts_control($widget_args){
        global $wp_registered_widgets;
        static $update = false;
        if(is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);

        $options = get_option('wut-widget-related-posts');
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
                if ('wut_widget_related_posts_body' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])){
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("wut-widget-related-posts-$widget_number", $_POST['widget-id'])){
                        unset($options[$widget_number]);
                    }
                }
            }

            foreach((array)$_POST['wut-related-posts'] as $widget_number => $posted){
                $options_this['title'] = strip_tags(stripslashes($posted['title']));
                $options_this['limit'] = intval($posted['limit']);
                $options_this['before'] = stripslashes($posted['before']);
                $options_this['after'] = stripslashes($posted['after']);
                $options_this['skips'] = $posted['skips'];
                $options_this['leastshare'] = intval($posted['leastshare']);
                $options_this['password'] = $posted['password'];
                $options_this['orderby'] = $posted['orderby'];
                $options_this['order'] = $posted['order'];
                $options_this['xformat'] = stripslashes($posted['xformat']);
                $options_this['none'] = stripslashes($posted['none']);
                $options[$widget_number] = $options_this;
            }
            update_option('wut-widget-related-posts',$options);
            $update = true;
        }
        if ($number == -1){
            $title = 'WUT Related Posts';
            $limit = 10;
            $before = attribute_escape('<li>');
            $after = attribute_escape('</li>');
            $skips = '';
            $leastshare = 1;
            $none = 'No Posts.';
            $password = 'hide';
            $orderby = 'post_date';
            $order = 'desc';
            $xformat = attribute_escape('<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)');
            $number = '%i%';
        }else{
            $title = attribute_escape($options[$number]['title']);
            $limit = $options[$number]['limit'];
            $before = attribute_escape($options[$number]['before']);
            $after = attribute_escape($options[$number]['after']);
            $skips = $options[$number]['skips'];
            $leastshare = $options[$number]['leastshare'];
            $none = attribute_escape($options[$number]['none']);
            $password = $options[$number]['password'];
            $orderby = $options[$number]['orderby'];
            $order = $options[$number]['order'];
            $xformat = attribute_escape($options[$number]['xformat']);            
        }
        ?>
        <table>
            <tbody>
                <tr>
                    <td class="alignright"><?php _e('Widget title:', 'wut');?></td>
                    <td><input type="text" id="wut-related-posts-title-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][title]" value="<?php echo $title;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Number of posts:', 'wut');?></td>
                    <td><input type="text" id="wut-related-posts-limit-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][limit]" value="<?php echo $limit;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('HTML tags before a item:', 'wut');?></td>
                    <td><input type="text" id="wut-related-posts-before-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][before]" value="<?php echo $before;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('HTML tags after a item:', 'wut');?></td>
                    <td><input type="text" id="wut-related-posts-after-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][after]" value="<?php echo $after;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Posts to exclude:', 'wut');?></td>
                    <td><input type="text" id="wut-related-posts-skips-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][skips]" value="" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('How many tags should posts share at least?', 'wut');?></td>
                    <td><input type="text" id="wut-related-posts-leastshare-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][leastshare]" value="<?php echo $leastshare;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Order by:','wut');?></td>
                    <td>
                        <p><input type="radio" id="wut-related-posts-orderby-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][orderby]" value="post_date" <?php if($orderby == 'post_date') echo 'checked="checked"';?> />Post date.</p>
                        <p><input type="radio" id="wut-related-posts-orderby-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][orderby]" value="post_modified" <?php if($orderby == 'post_modified') echo 'checked="checked"';?> />Post modified date.</p>
                    </td>
                </tr>
                <tr>
                <td class="alignright"><?php _e('Order:','wut');?></td>
                    <td>
                        <p><input type="radio" id="wut-related-posts-order-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][order]" value="desc" <?php if($order == 'desc') echo 'checked="checked"';?> />Descend.</p>
                        <p><input type="radio" id="wut-related-posts-order-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][order]" value="asc" <?php if($order == 'asc') echo 'checked="checked"';?> />Ascend.</p>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('If the list is empty, show:','wut');?></td>
                    <td><input type="text" id="wut-related-posts-none-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][none]" value="<?php echo $none;?>" /></td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('Show password protected post?','wut');?></td>
                    <td>
                        <p><input type="radio" id="wut-related-posts-password-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][password]" value="show" <?php if($password == 'show') echo 'checked="checked"';?> />Show.</p>
                        <p><input type="radio" id="wut-related-posts-password-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][password]" value="hide" <?php if($password == 'hide') echo 'checked="checked"';?> />Hide.</p>
                    </td>
                </tr>
                <tr>
                    <td class="alignright"><?php _e('The list item format:', 'wut');?></td>
                    <td><input type="text" id="wut-related-posts-xformat-<?php echo $number;?>" name="wut-related-posts[<?php echo $number;?>][xformat]" value="<?php echo $xformat;?>" /></td>
                </tr>
            </tbody>
        </table>
        <input id="wut-related-posts-submit-<?php echo $number;?>" name="wut-related-posts-submit-<?php echo $number;?>" type="hidden" value="1"/>
        <?php
    }
    $options = get_option('wut-widget-related-posts');
    if(!$options){
        $options = array();
    }

    $widget_ops =  array(
        'classname'     => 'wut-widget-related-posts',
        'description'   => __( 'List the related posts on SINGLE POST PAGE ONLY.', 'wut')
    );
    $control_ops = array(
        'width'     => 400,
        'height'    => 200,
        'id_base'   => 'wut-widget-related-posts'
    );
    $name   = __('WUT Related Posts','wut');
    $widget_cb  = 'wut_widget_related_posts_body';
    $control_cb = 'wut_widget_related_posts_control';
    // Register Widgets
    $registered = false;
    foreach(array_keys($options) as $o){
        if(!isset($options[$o]['title'])){
            continue;
        }
        $id = "wut-widget-related-posts-$o";
        $registered = true;
        wp_register_sidebar_widget($id, $name, $widget_cb, $widget_ops, array('number'=>$o));
        wp_register_widget_control($id, $name, $control_cb, $control_ops, array('number'=>$o));
    }
    if (!$registered){
        wp_register_sidebar_widget("wut-widget-related-posts-1", $name, $widget_cb, $widget_ops, array('number'=>-1));
        wp_register_widget_control("wut-widget-related-posts-1", $name, $control_cb, $control_ops,array('number'=>-1));
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
    function wut_widget_most_commented_posts_body($args, $widget_args){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_most_commented_posts_control($widget_args){

    }
}

function wut_widget_recent_comments_init(){
    function wut_widget_recent_comments_body($args, $widget_args){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_recent_comments_control($widget_args){

    }
}

function wut_widget_active_commentators_init(){
    function wut_widget_active_commentators_body($args, $widget_args){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_active_commentators_control($widget_args){

    }
}

function wut_widget_recent_commentators_init(){
    function wut_widget_recent_commentators_body($args, $widget_args){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_recent_commentators_control($widget_args){

    }
}

function wut_widget_advanced_blogroll_init(){
    function wut_widget_advanced_blogroll_body($args, $widget_args){
        extract($args);
        $options = get_option('wut-widget-recent-posts');
        echo $before_widget, $before_title, $options['title'], $after_title;
        echo '<ul>', wut_recent_posts(), '</ul>';
        echo $after_widget;
    }
    function wut_widget_advanced_blogroll_control($widget_args){

    }
}
?>
