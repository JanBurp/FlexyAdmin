<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*	Thanks to Dr. Peter J. Meyers
*/

class Spam {

	var $settings;
	var $text;
	var $text_no_html;
	var $rapport;

	function __construct() {
		$this->init();
	}

	function init($settings=array()) {
		$default=array(	'score_low'=>5,'score_high'=>10,
										'trigger_words'=>'buy,cheap,offer,discount,viagra,cialis,$,free,f r e e,amazing,billion,cash,cheap,credit,earn,sales,order now',
										'trigger_word_weight'=>5,
										'link_http_weight'=>5,
										'link_url_weight'=>10,
										'text_density_limit'=>70,
										'text_density_weight'=>10,
										'vowel_density_limit'=>15,
										'vowel_density_weight'=>5);
		$this->settings=array_merge($default,$settings);
		$this->text='';
		$this->text_no_html='';
		$this->rapport=array();
	}


	function check_text($txt) {
		if (empty($txt)) $txt=' ';
		$this->text=$txt;
		$this->text_no_html=strip_tags($txt);
		$this->rapport['score']=0;
		$this->rapport['action']=0;
		
		$this->check_trigger_words();
		$this->check_link_count();
		$this->check_text_density();
		$this->check_vowel_density();
		
		$this->create_action();
		return $this->rapport;
	}

	function create_action() {
		$this->rapport['action']=0;
		if($this->rapport['score'] >= $this->settings['score_high'])
			$this->rapport['action'] = 2;
		elseif($this->rapport['score'] >= $this->settings['score_low'])
			$this->rapport['action'] = 1;
		return $this->rapport['action'];
	}

	function get_rapport() {
		return $this->rapport;
	}
	function get_action() {
		return $this->rapport['action'];
	}
	function get_score() {
		return $this->rapport['score'];
	}
	
	function check_trigger_words() {
		$text=strtolower($this->text);
		$this->rapport['word_count']=0;
		$word_array = explode(",", $this->settings['trigger_words']);
		$word_array_len = count($word_array)-1;
		for($i=0; $i<=$word_array_len; $i++) $this->rapport['word_count'] += substr_count($text, $word_array[$i]);
		$score=$this->rapport['word_count'] * $this->settings['trigger_word_weight'];
		$this->rapport['score']+=$score;
		return $score;
	}

	function check_link_count() {
		$this->rapport['link_count_http'] = substr_count($this->text, "http:");
		$this->rapport['link_count_url'] = substr_count($this->text, "url=");
		$score=$this->rapport['link_count_http'] * $this->settings['link_http_weight'];
		$score+=$this->rapport['link_count_url'] * $this->settings['link_url_weight'];
		$this->rapport['score']+=$score;
		return $score;
	}
	
	function check_text_density() {
		$score=0;
		$this->rapport['text_density'] = round((strlen($this->text_no_html)/strlen($this->text)*100), 1);
		if ($this->rapport['text_density'] < $this->settings['text_density_limit']) $score = $this->settings['text_density_weight'];
		$this->rapport['score']+=$score;
		return $score;
	}
	
	function check_vowel_density() {
		$score=0;
		$vowel_count = substr_count($this->text_no_html, "a");
		$vowel_count += substr_count($this->text_no_html, "e");
		$vowel_count += substr_count($this->text_no_html, "i");
		$vowel_count += substr_count($this->text_no_html, "o");
		$vowel_count += substr_count($this->text_no_html, "u");
		$this->rapport['vowel_density'] = round(($vowel_count/strlen($this->text_no_html)*100), 1);
		if($this->rapport['vowel_density'] < $this->settings['vowel_density_limit']) $score = $this->settings['vowel_density_weight'];
		$this->rapport['score']+=$score;
		return $score;
	}
	
	// extra checks
	function has_html($txt) {
		return ($txt!=strip_tags($txt));
	}

	function has_url($txt) {
		$topdomains='aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|root|tel|travel';
		$urls=preg_match('/(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+('.$topdomains.'|[a-z]{2})/',$txt);
		return $urls;
	}

}

?>