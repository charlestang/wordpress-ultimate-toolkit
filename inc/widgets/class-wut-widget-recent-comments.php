<?php

class WUT_Widget_Recent_Comments extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array(
            'description'   => __('NEW! List recent comments.', 'wut'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('', __('WUT Recent Comments', 'wut'), $widget_ops);
    }

    public function widget($args, $instance)
    {
        $title = $instance['title'];

        $tag_args = array(
            'limit'         => $instance['number'],
            'before'        => '<li>',
            'after'         => '</li>',
            'length'        => 50,
            'posttype'      => 'post',
            'commenttype'   => 'comment',
            'skipusers'     => '',
            'avatarsize'    => $instance['avatarSize'],
            'none'          => __('No comments.', 'wut'),
            'password'      => 'hide',
            'xformat'       => '<a class="commentator" href="%permalink%" >%commentauthor%</a>',
        );

        if ($instance['showAvatar']) {
            $tag_args['xformat'] = '%gravatar%' . $tag_args['xformat'];
        }

        if ($instance['showContent']) {
            $tag_args['xformat'] .= ' : %commentexcerpt%';
        } else {
            $tag_args['xformat'] .= __(' on ', 'wut') . '<<%posttile>>';
        }

        echo $args['before_widget'], $args['before_title'], $title, $args['after_title'];
        echo '<ul>', wut_recent_comments($tag_args), '</ul>';
        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = intval($new_instance['number']);
        $instance['showContent'] = (bool) $new_instance['showContent'];
        $instance['showAvatar'] = (bool) $new_instance['showAvatar'];
        $instance['avatarSize'] = intval($new_instance['avatarSize']);
        return $new_instance;
    }

    public function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $showContent = isset($instance['showContent']) ? (bool) $instance['showContent'] : true;
        $showAvatar = isset($instance['showAvatar']) ? (bool) $instance['showAvatar'] : false;
        $avatarSize = isset($instance['avatarSize']) ? absint($instance['avatarSize']) : 16; ?>
        <p>
            <label for="<?= $this->get_field_id('title')?>"><?= __('Title:', 'wut')?></label>
			<input class="widefat" id="<?= $this->get_field_id('title')?>" name="<?= $this->get_field_name('title')?>" type="text" value="<?= $title?>" />
        </p>
        <p>
			<label for="<?= $this->get_field_id('number')?>"><?= __('Number of comments to show:', 'wut')?></label>
			<input class="tiny-text" id="<?= $this->get_field_id('number')?>" name="<?= $this->get_field_name('number')?>" type="number" step="1" min="1" value="<?= $number?>" size="3" />
        </p>
        <p>
			<input class="checkbox" type="checkbox"<?php checked($showContent); ?> id="<?= $this->get_field_id('showContent')?>" name="<?= $this->get_field_name('showContent')?>" />
			<label for="<?= $this->get_field_id('showContent')?>"><?= __('Display comment content?', 'wut')?></label>
        </p>
        <p>
			<input class="checkbox" type="checkbox"<?php checked($showAvatar)?> id="<?= $this->get_field_id('showAvatar')?>" name="<?= $this->get_field_name('showAvatar')?>" />
			<label for="<?= $this->get_field_id('showAvatar')?>"><?= __('Display avatar?', 'wut')?></label>
        </p>
        <p>
			<label for="<?= $this->get_field_id('avatarSize')?>"><?= __('The size of avatar: ', 'wut')?></label>
			<input class="tiny-text" id="<?= $this->get_field_id('avatarSize')?>" name="<?= $this->get_field_name('avatarSize')?>" type="number" step="1" min="1" value="<?= $avatarSize?>" size="3" />
        </p>
        <?php
    }
}
