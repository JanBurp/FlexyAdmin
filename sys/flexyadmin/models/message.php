<?

/**
 * Message
 *
 * @package default
 * @author Jan den Besten
 */

class Message extends CI_Model {
	
	public function __construct() {
		parent::__construct();
	}

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
  }

  // Messages (general)

  public function reset($type='messages') {
    $this->session->unset_userdata($type);
  }

  public function add($message,$type='messages') {
    $messages=$this->get();
    if (!is_array($messages)) $messages=array();
    array_unshift($messages, $this->ui->replace_ui_names($message) );
		$this->session->set_userdata($type,$messages);
  }
  
  public function get($type='messages') {
		return $this->session->userdata($type);
  }


  // Errors

  public function reset_errors() {
    $this->reset('errors');
  }

  public function add_error($error) {
    $this->add($error,'errors');
  }
  
  public function get_errors() {
    return $this->get('errors');
  }
  
  
  // Show
  
  public function show($view=FALSE) {
    $messages=$this->get();
    $errors=$this->get_errors();
    return $this->load->view('admin/messages', array('messages'=>$messages,'errors'=>$errors),$view);
  }
  


}
	
?>