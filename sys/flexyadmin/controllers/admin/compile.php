<?php require_once(APPPATH."core/AdminController.php");

require_once(APPPATH.'libraries/less/Less.php');

/**
 * LESS compiler for frontend styles, use: /compile or /compile/compress
 *
 * @package FlexyAdmin
 * @author Jan den Besten
 */

class Compile extends AdminController {

	public function __construct() {
		parent::__construct();
	}
  
  public function index($compress='') {
    $options=array(
      'compress' => ($compress!='')
    );
		$this->_set_content(h('Compile LESS files to CSS files'));
    $less_files=read_map('site/assets/css','less',FALSE,FALSE);
    // always text.less first!
    if (isset($less_files['text.less'])) {
      $text=$less_files['text.less'];
      unset($less_files['text.less']);
      array_unshift($less_files,$text);
    }
    foreach ($less_files as $less_file) {
      $less=$less_file['path'];
      $css=str_replace('.less','.css',$less);
      try{
        $parser = new Less_Parser($options);
        $parser->parseFile($less, site_url());
        $output = $parser->getCss();
        write_file($css,$output);
    		$this->_add_content(p().$less.' => '.$css._p());
      }catch(Exception $e){
    		$this->_add_content(p('error').$less.' => Fatal error: ' . $e->getMessage()._p());
      }
    }
		$this->_show_all();
  }
  
  public function compress() {
    return $this->index('compress');
  }
  

}

?>
