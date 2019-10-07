<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
	* Plugin_cleantable
	*
	* Schoont tabellen op die in editor zijn aangemaakt (verwijderd alle style attributen)
  * Als 'parse_content'
	*
	* @author Jan den Besten
	*/
 class Plugin_cleantable extends Plugin {

   public function __construct() {
     parent::__construct();
   }

  public function _after_update() {
    foreach ($this->newData as $field => $value) {
      $type = get_prefix($field,'_');
      if ($type=='txt') {
        $this->newData[$field] = $this->_clean($value);
      }
    }
		return $this->newData;
	}

  private function _clean($text) {
    $parse_content = $this->CI->config->item('parse_content');
    if ( isset($parse_content['clean_tables']) and $parse_content['clean_tables'] ) {
      $text = preg_replace('/<td\sstyle=\".*;\"/uiU', '<td', $text);
      $text = preg_replace('/<tr\sstyle=\".*;\"/uiU', '<tr', $text);
    }
    return $text;
  }


}

?>
