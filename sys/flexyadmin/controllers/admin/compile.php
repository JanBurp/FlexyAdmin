<?php require_once(APPPATH."core/AdminController.php");

require_once(APPPATH.'libraries/less/Less.php');

/**
 * LESS compiler and minimizer for frontend styles, use: admin/compile
 *
 * @package FlexyAdmin
 * @author Jan den Besten
 */

class Compile extends AdminController {
  
  var $css_path = 'site/assets/css';
  var $normalize_css = 'normalize.css';
  var $minimized_css = 'styles.min.css';
  var $exclude_css_files = array('admin.css','ie6.css','ie7.css','ie8.css','ie9.css','ie10.css','normalize.css','text.css','layout.css');

	public function __construct() {
		parent::__construct();
	}
  
  public function index() {
    $options=array('compress' => true);
		$this->_set_content(h('Compile LESS files to CSS files'));

    // Start with minimizing normalize.css
    $minimized="/* Minified styles. Created by FlexyAdmin (www.flexyadmin.com) ".date("Y-m-d")." */\n";
    $minimized.=$this->minimize_css( read_file($this->css_path.'/'.$this->normalize_css) );
    // Compile Less files and add them
    $less_files=read_map($this->css_path,'less',FALSE,FALSE);
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
        $parser = new Less_Parser();
        $parser->parseFile($less, site_url());
        $output = $parser->getCss();
        write_file($css,$output);
        // minimized
        $parser = new Less_Parser($options);
        $parser->parseFile($less, site_url());
        $minimized.=$parser->getCss();
        // message
    		$this->_add_content(p().$less.' => '.$css._p());
      }catch(Exception $e){
    		$this->_add_content(p('error').$less.' => Fatal error: ' . $e->getMessage()._p());
      }
    }
    
    // Add and minimize other .css files
    $css_files=read_map($this->css_path,'css',FALSE,FALSE);
    $css_files=array_unset_keys($css_files,$this->exclude_css_files);
    unset($css_files[$this->minimized_css]);
    foreach ($css_files as $css_file) {
      $minimized.=$this->minimize_css( read_file($css_file['path']) );
    }
		$this->_add_content(p().$this->css_path.'/'.$this->minimized_css.' CREATED'._p());
    write_file($this->css_path.'/'.$this->minimized_css,$minimized);
    
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
