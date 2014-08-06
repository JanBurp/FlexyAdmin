<?php require_once(APPPATH."core/AdminController.php");

require_once(APPPATH.'libraries/less/Less.php');

/**
 * LESS compiler and minimizer for frontend styles, use: admin/compile and set config in config/compile.php
 *
 * @package FlexyAdmin
 * @author Jan den Besten
 */

class Compile extends AdminController {

	public function __construct() {
		parent::__construct();
    $this->load->config('compile',true);
    $this->settings=$this->config->item('compile');
	}
  
  public function index() {
    $options=array('compress' => true);
		$this->_set_content(h('Compile LESS files & Minify CSS files'));

    // 1 - Compile Less files
    $this->_add_content(h('Compiling LESS files',2));
    $less_files=$this->settings['less_files'];
    foreach ($less_files as $less => $css) {
      try{
        $parser = new Less_Parser();
        $parser->parseFile($less, site_url());
        $output = $parser->getCss();
        write_file($css,$output);
        // message
    		$this->_add_content($less.' => '.$css.br());
      }catch(Exception $e){
    		$this->_add_content(p('error').$less.' => Fatal error: ' . $e->getMessage()._p());
      }
    }
    
    // 2 - Combine files
    $this->_add_content(h('Combining CSS files',2));
    $combine=$this->settings['banner'];
    $css_files=$this->settings['css_files'];
    foreach ($css_files as $css_file) {
      $combine.=$this->minimize_css( read_file($css_file) );
    }
		$this->_add_content(implode('<br>',$css_files));
    
    // 3 - Minify
    $minified=$this->minimize_css($combine);
    
    // 4 - Save
    write_file($this->settings['dest_file'], $minified);
    
		$this->_add_content(h('CREATED',2).$this->settings['dest_file']);
    
		$this->_show_all();
  }
  
  
  /**
   * See http://stackoverflow.com/questions/1379277/minify-css-using-preg-replace
   *
   * @param string $input 
   * @return string
   * @author Jan den Besten
   */
  private function minimize_css($input) {
    // Remove comments
    $output = preg_replace('#/\*.*?\*/#s', '', $input);
    // Remove whitespace
    $output = preg_replace('/\s*([{}|:;,])\s+/', '$1', $output);
    // Remove trailing whitespace at the start
    $output = preg_replace('/\s\s+(.*)/', '$1', $output);
    // Remove unnecesairy ;'s
    $output = str_replace(';}', '}', $output);
    return $output;
  }
  
  

}

?>
