<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controleer hiermee of een ingevuld formulier spam bevat
 *
 * @author Dr. Peter J. Meyers, Jan den Besten
 */

class Spam {

  /**
   * Instellingen
   *
   * @var string
   */
	private $settings=array(
    'score_low'=>5,
    'score_high'=>10,
    'trigger_words'=>'buy,cheap,offer,discount,viagra,cialis,$,free,f r e e,amazing,billion,cash,cheap,credit,earn,sales,order now,sex,sell',
		'trigger_word_weight'=>5,
		'link_http_weight'=>10,
		'link_url_weight'=>10,
		'text_density_limit'=>70,
		'text_density_weight'=>10,
		'vowel_density_limit'=>10,
		'vowel_density_weight'=>5,
    'spambody_weight'=>5
  );

  
  /**
   * Tekst die gecontroleerd wordt op spam
   *
   * @var string
   */
	private $text='';
  
  /**
   * De tekst zonder html tags
   *
   * @var string
   */
	private $text_no_html='';
  
  /**
   * Spam rapport
   *
   * @var array
   */
	private $rapport=array();
  

  /**
   * @ignore
   */
  public function __construct($settings=array()) {
		$this->settings=array_merge($this->settings,$settings);
	}

  /**
   * Controleer de tekst op spam en maakt rapport
   *
   * @param string $txt tekst die gecontroleerd moet worden
   * @return array rapportage
   * @author Jan den Besten
   */
	public function check_text($txt) {
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

  /**
   * Controleer of de invoer spam is
   *
   * @param array $data Data van een formulier
   * @param string $spamBody['spambody'] Veld wat een spambot ws automatisch vult 
   * @return bool TRUE dan is hoogstwaarschijnlijk spam.
   * @author Jan den Besten
   */
  public function check($data,$spamBody='spambody') {
    // collect txt_ fields, pick first one to check
    $fields=array_keys($data);
    $fields=filter_by($fields,'txt');
    if (!empty($fields)) {
      $textField=current($fields);
  		$this->check_text($data[$textField]);
    }
    // check if robot
    $this->check_if_robot($data,$spamBody);
    $this->create_action();
		return ($this->get_action()>=2);
  }

  /**
   * Wat is het resultaat? Spam of niet?
   *
   * @return int (0=geen spam, 1=misschien wel spam, 2=hoogswaarschijnlijk spam)
   * @author Jan den Besten
   */
	private function create_action() {
		$this->rapport['action']=0;
		if ($this->rapport['score'] >= $this->settings['score_high'])
		  $this->rapport['action'] = 2;
		elseif ($this->rapport['score'] >= $this->settings['score_low'])
			$this->rapport['action'] = 1;
		return $this->rapport['action'];
	}
  
	/**
	 * Geeft rapportage terug
	 *
	 * @return array
	 * @author Jan den Besten
	 */
  public function get_rapport() {
		return $this->rapport;
	}
  
  /**
   * Geeft waarschijnlijkheid dat het spam is
   *
   * @return int 0=geen spam, 1=misschien spam, 2=spam
   * @author Jan den Besten
   */
	public function get_action() {
		return $this->rapport['action'];
	}
  
  /**
   * Geeft spamscore terug
   *
   * @return int
   * @author Jan den Besten
   */
	public function get_score() {
		return $this->rapport['score'];
	}
	
  /**
   * Kijkt of er woorden in voorkomen die veel door spambots worden gebruikt en berekent score
   *
   * @return int score
   * @author Jan den Besten
   */
   private function check_trigger_words() {
    $text=strtolower($this->text);
    $this->rapport['word_count']=0;
    $word_array = explode(",", $this->settings['trigger_words']);
    $word_array_len = count($word_array)-1;
    for ($i=0; $i<=$word_array_len; $i++) {
      $this->rapport['word_count'] += substr_count($text, $word_array[$i]); 
    }
    $score=$this->rapport['word_count'] * $this->settings['trigger_word_weight'];
    $this->rapport['score']+=$score;
    return $score;
	}

  /**
   * Kijkt hoeveel links er voorkomen in de tekst en berekend score
   *
   * @return int score
   * @author Jan den Besten
   */
	private function check_link_count() {
		$this->rapport['link_count_http'] = substr_count($this->text, "http:");
		$this->rapport['link_count_url'] = substr_count($this->text, "url=");
		$score=$this->rapport['link_count_http'] * $this->settings['link_http_weight'];
		$score+=$this->rapport['link_count_url'] * $this->settings['link_url_weight'];
		$this->rapport['score']+=$score;
		return $score;
	}
	
  /**
   * Kijkt hoe 'dicht' de tekst is en berekend score
   *
   * @return int score
   * @author Jan den Besten
   */
	private function check_text_density() {
		$score=0;
		$this->rapport['text_density'] = round((strlen($this->text_no_html)/strlen($this->text)*100), 1);
		if ($this->rapport['text_density'] < $this->settings['text_density_limit']) $score = $this->settings['text_density_weight'];
		$this->rapport['score']+=$score;
		return $score;
	}
	
  /**
   * Berekend de dichtheid van klinkers en berekend score
   *
   * @return int score
   * @author Jan den Besten
   */
	private function check_vowel_density() {
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
	

  /**
   * Kijkt of er HTML in de tekst staat
   *
   * @param string $txt 
   * @return bool TRUE als er html tags in de tekst staan
   * @author Jan den Besten
   */
	public function has_html($txt) {
		return ($txt!=strip_tags($txt));
	}

  /**
   * Kijkt of er url's in de tekst staan
   *
   * @param string $txt 
   * @return array alle urls
   * @author Jan den Besten
   */
	public function has_url($txt) {
		$topdomains='aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|root|tel|travel';
		$urls=preg_match('/(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+('.$topdomains.'|[a-z]{2})/',$txt);
		return $urls;
	}

  /**
   * Controleer of het een robot is
   * 
   * Je kunt een (textarea) veld toevoegen aan je formulier dat je voor bezoekers onzichtbaar maakt.
   * Spambots zullen dit onzichtbare veld hoogstwaarschijnlijk automatisch gaan invullen omdat ze niet weten dat het leeg moet blijven.
   *
   * @param array $data Ingevulde data dat door formulier teruggeven wordt.
   * @param string $field['spambody'] te controleren veld
   * @return bool TRUE als veld niet leeg is en dus hoogstwaarschijnlijk een robot
   * @author Jan den Besten
   */
	public function check_if_robot($data,$field='spambody') {
    $robot=(!empty($data[$field]));
		$this->rapport['robot'] = $robot;
    if ($robot) $this->rapport['score']+=$this->settings['spambody_weight'];
		return $robot;
	}
  
  

}

?>
