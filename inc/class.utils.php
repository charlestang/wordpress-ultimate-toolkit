<?php

class WUT_Utils{

    var $options;
    function WUT_Utils($opt){
        $this->options = $opt;
    }

    function excerpt($text){
        global $post;

        remove_filter('the_excerpt', 'mul_excerpt');
        remove_filter('the_excerpt_rss', 'mul_excerpt');

        if ( '' == $text ) {
            $text = $post->post_content;
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = strip_tags($text);

            $text = trim($text);

            //段落数
            //modified temporarily
            $options = $this->options['excerpt'];
            $fragmentnum = $options['paragraphs'];
            //文字数
            $wordnum = $options['words'];
            $words = explode("\n", $text);
            
            
            $num = 0;
            $word_count = 0;
            $output = '';


            do {
                $output .= $words[$num] . "\n" . "\n";
                $num++;
            } while ( (mb_strlen($output, 'UTF-8') < $wordnum) and ($num < min(count($words), $fragmentnum)) );
            $output = substr($output, 0, -2);
            if (mb_strlen($output, 'UTF-8') < mb_strlen($text, 'UTF-8')) {
                $permalink = get_permalink();
                $title = strip_tags(get_the_title());
                $total_num = $this->words_count(preg_replace('/\s/','',html_entity_decode(strip_tags($post->post_content))));

                $tips = str_replace('%permalink%', $permalink, stripcslashes($options['tip_template']));
                $tips = str_replace('%title%', $title, $tips);
                $tips = str_replace('%total_words%', $total_num, $tips);

                $output .= $tips;
            }
            return $output;
        }
        return $text;
    }

    function exclude_pages($excludes){
        $hidepages = $this->options['hide-pages'];
        $custom_excludes = explode(',',$hidepages);
        $excludes = array_unique(array_merge($excludes,$custom_excludes));
        return $excludes;
    }

    function _select_code_snippets($hook){
        $codesnippets = $this->options['customcode'];
        if(!is_array($codesnippets) || empty($codesnippets)) return '';
        $codetoprint = '';
        
        if ( $hook == 'wp_head'):
            foreach ($codesnippets as $codesnippet){
                if ($codesnippet['hookto'] == 'wp_head'){
                    $codetoprint .= $codesnippet['source'];
                }
            }
        elseif ($hook == 'wp_footer') :
            foreach ($codesnippets as $codesnippet){
                if ($codesnippet['hookto'] == 'wp_footer'){
                    $codetoprint .= $codesnippet['source'];
                }
            }
        endif;
        return $codetoprint;
    }

    function inject_to_head(){
        echo "\n\n <!--This Piece of Code is Injected by WUT Custom Code-->\n";
        echo $this->_select_code_snippets('wp_head');
        echo "\n<!--The End of WUT Custom Code-->\n";
    }

    function inject_to_footer(){
        echo "\n\n <!--This Piece of Code is Injected by WUT Custom Code-->\n";
        echo $this->_select_code_snippets('wp_footer');
        echo "\n<!--The End of WUT Custom Code-->\n";
    }

    function add_wordcount_manage_columns($post_columns){
        $post_columns['wordcount'] = __('Words','wut');
        return $post_columns;
    }

    function display_wordcount($column_name){
        global $post;
        if($column_name == 'wordcount'){
            $content = strip_tags($post->post_content);
            $len = $this->words_count($content);
            $style = '';
            if ($len > 1000) $style = 'color:#00f;font-weight:bold';
            if ($len > 2000) $style = 'color:#f00;font-weight:bold';
            echo "<span style=\"$style\">",$len,'</span>';
        }
    }

    function set_column_width(){
    ?>
        <style type="text/css">
        .column-wordcount {width:6%;}
        </style>
    <?php
    }

    /**
     * Count the words in a string.
     *
     * This function treat a multibyte charactor as 1, and a English like
     * language WORD as 1.
     *
     * So, this function can count mixed Chinese and English relatively exactly.
     *
     * @since 1.0.0
     * @param string $content
     * @return int the number of words in this string
     * @access private
     */
    function words_count($content){
        $matches = array();
        preg_match_all('~[-a-z0-9,.!?\'":;@/ ()\+\_]+~im', $content, $matches);
        $content = preg_replace('~[-a-z0-9,.!?\'":;@/ ()\+\_]+~im', '', $content);
        $ch_char_count = mb_strlen(trim($content));
        $en_word_count = 0;
        foreach($matches[0] as $str){
            $str = trim($str, ',.!?;:@ \'"/()');
            if(!empty($str)) {
                $temp = explode(' ', $str);
                $en_word_count += count($temp);
            }
        }
        return $ch_char_count + $en_word_count;
    }
}

