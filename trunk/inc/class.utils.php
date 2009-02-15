<?php
if(!function_exists('mb_strlen')){
    function mb_strlen ($text, $encode)
	{
		if ($encode=='UTF-8') {
			return preg_match_all('%(?:
					  [\x09\x0A\x0D\x20-\x7E]           # ASCII
					| [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
					|  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					|  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
					|  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
					|  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
					)%xs',$text,$out);
		}else{
			return strlen($text);
		}
	}
}
class WUT_Utils{
    function excerpt($text)
	{
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
}