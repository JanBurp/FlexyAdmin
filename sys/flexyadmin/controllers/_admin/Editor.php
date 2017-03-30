<?php require_once(APPPATH."core/AdminController.php");

/**
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Editor extends AdminController {

	public function __construct() {
    parent::__construct();
	}

	public function image() {
    $this->_prepare_view_data();
    $this->view_data = array_merge($this->view_data);
    $selection = $this->input->get('selected');
    if ($selection && $selection!=="false") {
      if (!is_array($selection)) $selection=array($selection);
      // $this->view_data['selection'] = $selection;
    }
    $this->load->view('admin/editor_image',$this->view_data);
    return $this;
	}
  

}

?>
