<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Text_converter {

  var $workflows = array(
    'markdown2dokuwiki' => array(
      'h5'              => array('find'=>'^#{5}\s*(.*$)','replace'=>'== $1 ==','regex'=>true),
      'h4'              => array('find'=>'^#{4}\s*(.*$)','replace'=>'=== $1 ===','regex'=>true),
      'h3'              => array('find'=>'^#{3}\s*(.*$)','replace'=>'==== $1 ====','regex'=>true),
      'h2'              => array('find'=>'^#{2}\s*(.*$)','replace'=>'===== $1 =====','regex'=>true),
      'h1'              => array('find'=>'^#{1}\s*(.*$)','replace'=>'====== $1 =======','regex'=>true),
      // 'Ordered List'    => array('find'=>'^\d+\.\s(.*)$','replace'=>' - $1','regex'=>true),
      // 'Unordered List'  => array('find'=>'^\*\s(.*)$','replace'=>' * $1','regex'=>true),
      // 'Indented Text'   => array('find'=>'^\s{4}(.*)$','replace'=>'  $1','regex'=>true),
      'bold'            => array('find'=>'__(.*?)__','replace'=>'**$1**','regex'=>true),
      'italic'            => array('find'=>'_(.*?)_','replace'=>'//$1//','regex'=>true),
      'italic'            => array('find'=>'\*(.*?)\*','replace'=>'//$1//','regex'=>true),
      
    );
  );
  
  var $settings = array(
    'workflow'  => 'markdown2dokuwiki'
  );

  /**
   * @ignore
   */
  public function __construct($settings=array()) {
    $this->initialize($settings);
    return $this;
  }

  /**
   * Initialiseer alle opties, zie boven voor alle opties
   *
   * @param array $config 
   * @return this
   * @author Jan den Besten
   */
  public function initialize($settings=array()) {
    $this->settings=array_merge($this->settings,$settings);
    return $this;
  }
  
  
  /**
   * Start de conversie
   *
   * @param string $txt te converteren tekst
   * @return string $converted
   * @author Jan den Besten
   */
  public function convert($txt) {
    $steps=$this->workflows[$this->settings['workflow']];
    foreach ($steps as $name => $step) {
      if ($step['regex']) {
        $txt=preg_replace($step['find'],$step['replace'],$txt);
      }
      else {
        $txt=str_replace($step['find'],$step['replace'],$txt);
      }
    }
    return $txt;
  }

	
}

?>
