<?php
require_once(dirname(dirname(__FILE__)) . '/libs/multibyte.php');

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
            $fragmentnum = 3;//$this->options['excerpt_paragraphs_number'];
            //文字数
            $wordnum = 250; //$this->options['excerpt_words_number'];
            $words = explode("\n", $text, $fragmentnum+1);
            $num = 0;
            $output = '';
            do {
                $output = $output . $words[$num] . "\n" . "\n";
                $num++;
            } while ( (mb_strlen($output, 'UTF-8') < $wordnum) and ($num < min(count($words), $fragmentnum)) );

            if (mb_strlen($output, 'UTF-8') < mb_strlen($text, 'UTF-8')) {
                $output .= '<span class="readmore"><a href="' . get_permalink() . '" title="' . strip_tags(get_the_title()) . '">';
                $output .= __('Read More: ','wut') . mb_strlen(preg_replace('/\s/','',html_entity_decode(strip_tags($post->post_content))),'UTF-8') . __(' Words Totally','wut') . '</a></span>';
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
}