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

  /**
   * Create documentation of the FlexyAdmin API
   *
   * @return void
   * @author Jan den Besten
   */
  public function doc() {
    $this->_add_content('<h1>Creating documentation</h1>');

    // load all helpers, libraries, plugins, models etc to make it work
    $helpers=read_map('sys/flexyadmin/helpers','php');
    foreach ($helpers as $file=>$helper) {
      $file=str_replace('_helper.php','',$file);
      if ($this->load->exist('helper',$file)) {
        $this->load->helper($file);
      }
    }
    $models=read_map('sys/flexyadmin/models','php');
    foreach ($models as $file=>$model) {
      $file=str_replace('.php','',$file);
      if ($this->load->exist('model',$file)) {
        $this->load->model($file);
        trace_($file);
      }
    }
    // $libraries=read_map('sys/flexyadmin/libraries','php');
    // foreach ($libraries as $file=>$library) {
    //   trace_($file);
    //   trace_(class_exists($file));
    //   if (!isset($this->$file)) {
    //     $this->load->library($file);
    //   }
    // }


    $this->load->library('__/doc');

    $doc=$this->doc->doc();
    $toc=array();
    
    
    // Classes
    // $classTypes=array('core','models','libraries');
    foreach ($doc['classes'] as $file => $class) {
      $classType=explode('/',$class['file']);
      $classType=$classType[2];

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
        'CIparent'=>$CIparent,
        'shortdescription'=>el('shortdescription',$class['doc']),
        'description'=>el('description',$class['doc']),
        'properties'=>$propertiesHtml,
        'methods'=>$methodsHtml
      ),true);
      $fileContent=$this->load->view('admin/__/doc',array('content'=>$content),true);
      
      $fileName='userguide/FlexyAdmin/'.$classType.'/'.$file.'.html';
      write_file($fileName,$fileContent);
      // group in toc
      $toc[$classType][$file]=$fileName;
      
      $this->_add_content('Class file created ('.$classType.'): '.$fileName.'</br>');
    }
    
    
    
    // Helpers (functions)
    foreach ($doc['functions'] as $file => $functions) {
      $content='';
      $functionsHtml='';
      foreach ($functions as $name => $value) {
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
      $CIparent='';
      if (substr($file,0,2)=='MY') $CIparent='../../codeigniter/helpers/'.str_replace(array('MY_','.php'),array('','.html'),$file);
      $content.=$this->load->view('admin/__/doc_file',array('file'=>$file,'CIparent'=>$CIparent,'functions'=>$functionsHtml),true);
      $fileContent=$this->load->view('admin/__/doc',array('content'=>$content),true);
      $fileName='userguide/FlexyAdmin/helpers/'.str_replace('.php','.html',$file);
      write_file($fileName,$fileContent);
      $this->_add_content('Helper file created: '.$fileName.'</br>');
      $toc['helpers'][$file]=$fileName;
    }

    // trace_($doc);
    // trace_($toc);
    $toc_order=array('helpers','|','libraries','|','models','core');
    $otoc=array();
    foreach ($toc_order as $key) {
      if ($key=='|')
        $otoc[]='|';
      else {
        asort($toc[$key]);
        $otoc[$key]=$toc[$key];
      }
        
    }
    
    $content=$this->load->view('admin/__/doc_toc',array('toc'=>$otoc),true);
    $fileContent=$this->load->view('admin/__/doc',array('content'=>$content),true);
    $fileName='userguide/FlexyAdmin/index.html';
    write_file($fileName,$fileContent);
    $this->_add_content('TOC file created: '.$fileName.'</br>');
    
    $this->_show_all();
  }


}

?>
