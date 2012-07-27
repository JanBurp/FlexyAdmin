<?
require_once(APPPATH."core/AdminController.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @copyright Copyright (c) 2008, Jan den Besten
 * @link http://flexyadmin.com
 * @version V1 0.1
 * @filesource  */

// ------------------------------------------------------------------------


class __ extends AdminController {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
    $this->doc();
	}

  public function doc() {
    $this->_add_content('<h1>Creating documentation</h1>');
    
    $this->load->library('__/doc');
    // $this->load->helper('file_helper');

    $doc=$this->doc->doc();
    
    
    foreach ($doc['functions'] as $file => $functions) {
      $content='';
      $functionsHtml='';
      foreach ($functions as $name => $value) {
        $functionsHtml.=$this->load->view('admin/__/doc_function', array(
          'name'=>$name,
          'lines'=>$value['lines'],
          'params'=>el('param',$value['doc']),
          'return'=>el('return',$value['doc']),
          'description'=>el('description',$value['doc']),
          'author'=>el('author',$value['doc'])
        ),true);
      }
      $content.=$this->load->view('admin/__/doc_file',array('file'=>$file,'functions'=>$functionsHtml),true);
      $fileContent=$this->load->view('admin/__/doc',array('content'=>$content),true);
      $fileName='userguide/FlexyAdmin/helpers/'.str_replace('.php','.html',$file);
      write_file($fileName,$fileContent);
      $this->_add_content('Helper file created: '.$fileName.'</br>');
    }

    // trace_($doc);
    
    $this->_show_all();
  }


}

?>
