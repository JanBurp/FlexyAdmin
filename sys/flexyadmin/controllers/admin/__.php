<?
require_once(APPPATH."core/AdminController.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------


class __ extends BasicController {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
    $this->doc();
	}

  public function doc() {
    $this->load->library('doqumentor/doqumentor');

    $doc=$this->doqumentor->doc();
    trace_($doc);
    
    // $doc=$this->doqumentor->display();
    // $this->load->view('admin/__/doc',array('doc'=>$doc));
  }


}

?>
