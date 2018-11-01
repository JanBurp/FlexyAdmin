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
        $this->view_data['path'] = $this->input->get('path');  
        $this->view_data['src']  = '';
        $this->view_data['alt']  = '';

        $selection = $this->input->get('selected');
        if ($selection && $selection!=="false" && substr($selection,0,1)==='{' ) {
            $selection = json2array($selection);
            if (isset($selection['path']) and isset($selection['src'])) {
                $this->view_data['path'] = $selection['path'];  
                $this->view_data['src']  = $selection['src'];
                $this->view_data['alt']  = el('alt',$selection,$selection['src']);
            }
        }
        $this->load->view('admin/editor_image',$this->view_data);
        return $this;
	}
  

}

?>
