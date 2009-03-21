<?php
class WUT_OptionsManager{
    var $version = 1.0;
    var $options;
    function WUT_OptionsManager(){
        $this->options = get_option('wordpress-ultimate-toolkit-options');

        //when the plugin updated, this will be true
        if (empty($this->options) || $this->version > $this->options['version']){
            $this->set_defaults();
        }
    }

    function set_defaults(){
        $defaults = array(
            'hide-pages'       => '',
            'widgets-control'  => array(
                'show' => array(
                    'wut_widget_recent_posts_init',
                    'wut_widget_random_posts_init',
                    'wut_widget_related_posts_init',
                    'wut_widget_posts_by_category_init',
                    'wut_widget_most_commented_posts_init',
                    'wut_widget_recent_comments_init',
                    'wut_widget_active_commentators_init',
                    'wut_widget_recent_commentators_init'
                ),
                'hide' => array()
            ),
            'excerpt'          => array()
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
}

?>
