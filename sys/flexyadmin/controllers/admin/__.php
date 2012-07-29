<?
require_once(APPPATH."core/AdminController.php");
require_once(APPPATH."core/FrontendController.php");  // Load this also, so PHP can build documentation for this one also


/**
 * Build proces, maakt automatisch documentatie.
 *
 * @package default
 * @author Jan den Besten
 */
 
class __ extends AdminController {
  
  private $toc=array();

	public function __construct() {
		parent::__construct();
	}

	public function index() {
    $this->doc();
	}

  /**
   * Create documentation of the FlexyAdmin API
   *
   * @return void
   * @author Jan den Besten
   */
  public function doc() {
    $this->_add_content('<h1>Creating documentation</h1>');

    // Make sure everything is loaded, to make documentation for everything...
    // Load all core libraries that are not standard loaded
    $this->load->dbutil();
    // load all helpers
    $this->load->helper('video');
    // load all libraries
    $libraries=read_map('sys/flexyadmin/libraries','php');
    unset($libraries['ion_auth.php']); // exclude allready inherited libraries
    $modules=read_map('site/libraries','php'); // Frontend libraries (modules)
    $libraries=array_merge($libraries,$modules);
    foreach ($libraries as $file=>$library) {
      $this->load->library(str_replace('my_','',$file));
    }
    // load all models
    $models=read_map('sys/flexyadmin/models','php');
    $frontend=read_map('site/models','php');
    $models=array_merge($models,$frontend);
    foreach ($models as $file=>$model) {
      $file=str_replace('.php','',$file);
      if (!$this->load->exist('model',$file)) {
        $this->load->model($file);
      }
    }
    

    // Include HTML documents
    $this->_add_html_docs('userguide/FlexyAdmin/__doc');

    // Ok, start
    $this->load->library('__/doc');
    $doc=$this->doc->doc();
    
    // Classes
    foreach ($doc['classes'] as $file => $class) {
      // determine the kind of file
      $path=explode('/',$class['file']);
      $classPath=$path[count($path)-2];
      $classType=$classPath;
      if ($path[0]=='site') {
        if ($classType=='libraries') {
          $classType='modules (site)';
          if (has_string('Plugin',$file)) $classType='plugins (site)';
        }
        elseif ($classType=='models') {
          $classType='models (site)';
        }
      }
      else {
        if ($classType=='libraries') {
          if (has_string('Plugin',$file)) $classType='plugins';
        }
      }

      $content='';
      
      // properties
      $propertiesHtml='';
      foreach ($class['properties'] as $name => $value) {
        $propertiesHtml.=$this->load->view('admin/__/doc_property', array(
          'name'=>$name,
          'type'=>el('var',$value),
          'shortdescription'=>el('shortdescription',$value),
          'description'=>el('description',$value),
        ),true);
      }
      
      $methodsHtml='';
      foreach ($class['methods'] as $name => $value) {
        $methodsHtml.=$this->load->view('admin/__/doc_function', array(
          'name'=>$name,
          'lines'=>$value['lines'],
          'params'=>el('param',$value['doc']),
          'return'=>el('return',$value['doc']),
          'shortdescription'=>el('shortdescription',$value['doc']),
          'description'=>el('description',$value['doc']),
          'author'=>el('author',$value['doc'])
        ),true);
      }
      $CIparent='';
      if (substr($file,0,2)=='MY') $CIparent='../../codeigniter/'.$classType.'/'.str_replace(array('MY_','.php'),array('','.html'),$file);
      $content.=$this->load->view('admin/__/doc_class',array(
        'file'=>$file,
        'path'=>$class['file'],
        'CIparent'=>$CIparent,
        'shortdescription'=>el('shortdescription',$class['doc']),
        'description'=>el('description',$class['doc']),
        'properties'=>$propertiesHtml,
        'methods'=>$methodsHtml
      ),true);
      $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../'),true);
      
      $fileName='userguide/FlexyAdmin/'.$classPath.'/'.$file.'.html';
      write_file($fileName,$fileContent);
      // group in toc
      $this->toc[$classType][$file]=$fileName;
      
      $this->_add_content('Class file created ('.$classPath.'): '.$fileName.'</br>');
    }
    
    
    
    // Helpers (functions)
    foreach ($doc['functions'] as $file => $functions) {
      $content='';
      $functionsHtml='';
      foreach ($functions as $name => $value) {
        if (!isset($value['doc']['ignore'])) {
          $path=$value['file'];
          $functionsHtml.=$this->load->view('admin/__/doc_function', array(
            'name'=>$name,
            'lines'=>$value['lines'],
            'params'=>el('param',$value['doc']),
            'return'=>el('return',$value['doc']),
            'shortdescription'=>el('shortdescription',$value['doc']),
            'description'=>el('description',$value['doc']),
            'author'=>el('author',$value['doc'])
          ),true);
        }
      }
      
      if (!empty($functionsHtml)) {
        $CIparent='';
        if (substr($file,0,2)=='MY') $CIparent='../../codeigniter/helpers/'.str_replace(array('MY_','.php'),array('','.html'),$file);
        $content.=$this->load->view('admin/__/doc_file',array(
          'file'=>$file,
          'path'=>$path,
          'CIparent'=>$CIparent,
          'functions'=>$functionsHtml
        ),true);
        $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../'),true);
        $fileName='userguide/FlexyAdmin/helpers/'.str_replace('.php','.html',$file);
        write_file($fileName,$fileContent);
        $this->_add_content('Helper file created: '.$fileName.'</br>');
        $this->toc['helpers'][$file]=$fileName;
      }

    }

    // trace_($doc);
    // trace_($this->toc);

    $this->toc_order=array('algemeen','uitbreiden','database','|','modules (site)','plugins (site)','models (site)','|','plugins','libraries','|','core','models','|','helpers');
    $otoc=array();
    foreach ($this->toc_order as $key) {
      if ($key=='|')
        $otoc[]='|';
      else {
        if (!empty($this->toc[$key])) {
          // asort($this->toc[$key]);
          $otoc[$key]=$this->toc[$key];
        }
        else {
          $otoc[$key]=array();
        }
      }
        
    }
    
    $content=$this->load->view('admin/__/doc_toc',array('toc'=>$otoc),true);
    $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>''),true);
    $fileName='userguide/FlexyAdmin/index.html';
    write_file($fileName,$fileContent);
    //
    $json_toc=$this->load->view('admin/__/doc_toc_json',array('toc'=>$otoc,'html'=>trim(str_replace(array(PHP_EOL,"\r",'../userguide/FlexyAdmin/'),'',$content))),true);
    write_file('userguide/FlexyAdmin/js/toc.js',$json_toc);
    $this->_add_content('TOC file created: '.$fileName.'</br>');
    
    $this->_show_all();
  }
  
  
  private function _add_html_docs($path) {
    $files=read_map($path);
    foreach ($files as $name  => $file) {
      if ($file['type']=='dir') {
        $dir=str_replace('__doc/','',$file['path']);
        $dir=preg_replace("/\/(\d_)/u", "/", $dir);
        if (!file_exists($dir)) mkdir($dir);
        $this->_add_html_docs($path.'/'.$name);
      }
      else {
        $name=ucfirst(str_replace(array('_','.html'),array(' ',''),remove_prefix($name,'-')));
        $path=explode('/',$file['path']);
        $path=$path[count($path)-2];
        $type=remove_prefix($path,'_');
        
        $html=read_file($file['path']);
        $fileName=str_replace('__doc/','',$file['path']);
        $fileName=preg_replace("/\/(\d_)/u", "/", $fileName);
        $fileName=preg_replace("/\/(\d-)/u", "/", $fileName);
        $content=highlight_code_if_needed( $this->load->view('admin/__/doc_file',array('file'=>$name,'functions'=>$html),true) );
        $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../'),true);
        write_file($fileName,$fileContent);
        $this->_add_content('DOC created: '.$fileName.'</br>');
        $this->toc[$type][$name]=$fileName;
      }
    }
  }


}

?>
