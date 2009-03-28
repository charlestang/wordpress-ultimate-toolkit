<?php
class WUT_OptionsManager{
    var $version = 1.0;
    var $options;
    function WUT_OptionsManager(){
        //update_option('wordpress-ultimate-toolkit-options','');
        $this->options = get_option('wordpress-ultimate-toolkit-options');

        //when the plugin updated, this will be true
        if (empty($this->options) || $this->version > $this->options['version']){
            $this->set_defaults();
        }
    }

    function set_defaults(){
        $defaults = array(
            'hide-pages'        => '',
            'widgets'           => array(
                'load'          => array(
                    'wut_widget_recent_posts_init',
                    'wut_widget_random_posts_init',
                    'wut_widget_related_posts_init',
                    'wut_widget_posts_by_category_init',
                    'wut_widget_most_commented_posts_init',
                    'wut_widget_recent_comments_init',
                    'wut_widget_active_commentators_init',
                    'wut_widget_recent_commentators_init'
                ),
                'all'           => array(
                    array(
                    'name'      => __('Recent Posts', 'wut'),
                    'descript'  => __('Display a list of recent posts.', 'wut'),
                    'callback'  => 'wut_widget_recent_posts_init'
                    ),
                    array(
                   'name'      => __('Random Posts', 'wut'),
                   'descript'  => __('Display a list of random posts.', 'wut'),
                   'callback'  => 'wut_widget_random_posts_init'
                    ),
                    array(
                   'name'      => __('Related Posts', 'wut'),
                   'descript'  => __('Display a list of related posts of a certain post.', 'wut'),
                   'callback'  => 'wut_widget_related_posts_init'
                    ),
                    array(
                   'name'      => __('In Category Posts Widget', 'wut'),
                   'descript'  => __('Display a list of posts in a certain category.', 'wuts'),
                   'callback'  => 'wut_widget_posts_by_category_init'
                    ),
                    array(
                   'name'      => __('Most Commented Posts', 'wut'),
                   'descript'  => __('Display a list of most commented posts.', 'wut'),
                   'callback'  => 'wut_widget_most_commented_posts_init'
                    ),
                    array(
                   'name'      => __('Recent Comments', 'wut'),
                   'descript'  => __('Display recent comments.', 'wut'),
                   'callback'  => 'wut_widget_recent_comments_init'
                    ),
                    array(
                   'name'      => __('Active Commentators', 'wut'),
                   'descript'  => __('Display active commentators in a certain days limit.', 'wut'),
                   'callback'  => 'wut_widget_active_commentators_init'
                    ),
                    array(
                   'name'      => __('Recent Commentators', 'wut'),
                   'descript'  => __('Display this week or this month\'s commentators.', 'wut'),
                   'callback'  => 'wut_widget_recent_commentators_init'
                    )
                ),
                    
            ),
            'excerpt'          => array(),
            'other'            => array(
                'enabled'        => 1,
                'wphome'         => get_option('home'),
                'perma_struct'   => get_option('permalink_structure')
            )
        );
        $defaults['version'] = $this->version;
        if (empty($this->options)) {
            $this->options = $defaults;
            $this->save_options();
            return;
        }
    }

    function &get_options($key = ''){
        if(empty($key)) return $this->options;
        return isset($this->options[$key])?$this->options[$key]:false;
    }

    function save_options(){
        update_option('wordpress-ultimate-toolkit-options',$this->options);
    }

    function delete_options(){
        delete_option('wordpress-ultimate-toolkit-options');
        delete_option('wut-widget-recent-posts');
        delete_option('wut-widget-random-posts');
        delete_option('wut-widget-related-posts');
        delete_option('wut-widget-posts-by-category');
        delete_option('wut-widget-most-commented-posts');
        delete_option('wut-widget-recent-comments');
        delete_option('wut-widget-active-commentators');
        delete_option('wut-widget-recent-commentators');
    }
}

?>
