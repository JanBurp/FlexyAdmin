<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cfg_email
 *
 * @author Jan den Besten
 */
class cfg_email extends _crud {
  
  /**
   * Taal waarvan de templates moeten worden opgevraagd
   */
  private $lang='';
  
	public function __construct() {
		parent::__construct();
		$this->table('cfg_email');
    $this->set_language();
	}


  /**
   * Geeft de template terug met ingestelde of meegegeven taal, of FALSE als die niet is gevonden
   *
   * @param string $template 
   * @param string $lang default is ingestelde taal
   * @return mixed FALSE of array('subject'=>'','body'=>'')
   * @author Jan den Besten
   */
  public function get_template($template,$lang='') {
    if (empty($lang)) $lang=$this->lang;
    $result=$this->get_row(array(
      'select' => 'str_subject_'.$lang.' AS str_subject, txt_email_'.$lang.' AS txt_email',
      'where'  => array('key'=>$template)
    ));
    if ($result) {
      return array(
        'subject' => $result['str_subject'],
        'body'    => $result['txt_email'],
      );
    }
    return false;
  }
  
  
  /**
   * Stel de taal in voor alle templates die worden opgevraagd
   *
   * @param string $lang 
   * @return this
   * @author Jan den Besten
   */
  public function set_language($lang='') {
    if (empty($lang)) {
      if (isset($this->session->userdata['language'])) {
        $lang=$this->session->userdata['language'];
      }
      else {
        $lang=$this->config->item('language');
      }
    }
    $this->lang=$lang;
    return $this;
  }
  

}
