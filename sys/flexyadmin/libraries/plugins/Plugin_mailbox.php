<?php

class Plugin_mailbox extends Plugin {

	public function __construct()	{
		parent::__construct();
	}

  public function _admin_api($args) {
    $action = 'list';
    if (isset($args[0])) {
      $action = $args[0];
    }
    $emails = $this->CI->log_activity->get_mailbox();


    switch ($action) {
      case 'export':
        $out=array2csv($emails);
        $this->CI->load->helper('download');
        force_download('emailbox-'.date('Y-m-d').'.csv',$out);
        return $out;
        break;
      case 'show':
        $key = $args[1];
        $this->add_content( $this->view('admin/plugins/mail',array('email'=>$emails[$key])) );
        break;
      case 'list':
      default:
        $this->add_content( $this->view('admin/plugins/mailbox',array('emails'=>$emails)) );
        break;
    }

    return $this->content;
  }


}

?>
