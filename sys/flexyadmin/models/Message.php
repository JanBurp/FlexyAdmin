<?php 
/** \ingroup models
 * Wordt gebruikt voor interne messages in het admin deel
 *
 * @author Jan den Besten
 */

class Message extends CI_Model {
  
  var $uiNames=FALSE;
	
  /**
   */
	public function __construct() {
		parent::__construct();
    if ($this->uri->get(1)=='admin') $this->uiNames=TRUE;
	}

  /**
   * Initialiseer
   *
   * @return object $this;
   * @author Jan den Besten
   */
  public function init() {
		$messages=$this->session->flashdata('messages');
    if ($messages) {
      foreach ($messages as $messages) {
        $this->add($message);
      }
    }
		$errors=$this->session->flashdata('errors');
    if ($errors) {
      foreach ($errors as $error) {
        $this->add_error($error);
      }
    }
		$ajax=$this->session->flashdata('ajax');
    if ($ajax) {
      foreach ($ajax as $jx) {
        $this->add_ajax($jx);
      }
    }
    return $this;
  }

  /**
   * Reset
   *
   * @param string $type default='messages'
   * @return object $this;
   * @author Jan den Besten
   */
  public function reset($type='messages') {
    if ($this->session->userdata($type)) $this->session->unset_userdata($type);
    return $this;
  }

  /**
   * Voeg bericht toe
   *
   * @param string $message 
   * @param string $type default='message'
   * @return object $this;
   * @author Jan den Besten
   */
  public function add($message,$type='messages') {
    if (!empty($message)) {
      $messages=$this->get($type);
      if (!is_array($messages)) $messages=array();
      // if ($this->uiNames) $message=$this->lang->replace_ui($message);
      array_unshift($messages, $message );
  		$this->session->set_userdata($type,$messages);
    }
    return $this;
  }
  
  /**
   * Geeft bericht
   *
   * @param string $type default='message'
   * @return mixed FALSE als geen bericht, anders een array van berichten
   * @author Jan den Besten
   */
  public function get($type='messages') {
    $messages=$this->session->userdata($type);
    // Verwijder lege messages
    if ($messages and is_array($messages)) {
      foreach ($messages as $key => $message) {
        if (empty($message)) unset($messages[$key]);
      }
    } 
    if (empty($messages)) $messages=FALSE;
		return $messages;
  }

  /**
   * Reset errors
   *
   * @return object $this;
   * @author Jan den Besten
   */
  public function reset_errors() {
    return $this->reset('errors');
  }

  /**
   * Voeg error toe
   *
   * @param string $error 
   * @return object $this;
   * @author Jan den Besten
   */
  public function add_error($error) {
    return $this->add($error,'errors');
  }
  
  /**
   * Geeft error terug
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function get_errors() {
    return $this->get('errors');
  }
  
  /**
   * Voegt ajax message toe
   *
   * @param string $ajax 
   * @return object $this
   * @author Jan den Besten
   */
  public function add_ajax($ajax) {
    return $this->add($ajax,'ajax');
  }
  
  /**
   * Geeft Ajax messages
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function get_ajax() {
    return $this->get('ajax');
  }
  
  /**
   * Reset ajax messeges
   *
   * @return object $this
   * @author Jan den Besten
   */
  public function reset_ajax() {
    return $this->reset('ajax');
  }
  
  
  /**
   * Geeft alle messages/errors terug als HTML
   *
   * @param bool $view default=FALSE
   * @return string
   * @author Jan den Besten
   */
  public function show($view=FALSE) {
    $messages=$this->get();
    $errors=$this->get_errors();
    return $this->load->view('admin/messages', array('messages'=>$messages,'errors'=>$errors),$view);
  }
  


}
	
?>