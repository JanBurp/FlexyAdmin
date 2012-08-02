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
  private $tipue=array();
  
  private $path='/Users/jan/Sites/FlexyAdmin/';
  private $work='FlexyAdminDEMO';
  private $tags='TAGS';
  
  private $stripTagsWithClasses=array('doc_info','doc_param_type','doc_label');
  private $stripWords=array('(string)', '(array)', '(void)', '(bool)', '(mixed)', '(object)', 
                            'CI','CodeIgniter','PHP','FlexyAdmin','class','parameters', 'functions', 'function', '__construct', 'methods', 'properties', 'true','false', 'array','return:', 'global','instance',
                            'en','een','of','de','het', 'dat','als','met','voor' ,'in','je','wat','over','om','is','aan','uit','die','te','ze','op','deze','kun',
                            'if','the','and','or','name','content','config','use', 'this', 'to', 'own', 'see', 'also', 'file' ,'you','your','re', 'add', 'code', 'set', 'from', 'which');

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
        $comm=el('doc',$value);
        $propertiesHtml.=$this->load->view('admin/__/doc_property', array(
          'name'=>$name,
          'inherited'=>el('inherited',$comm),
          'type'=>el('var',$value),
          'shortdescription'=>el('shortdescription',$value),
          'description'=>el('description',$value),
        ),true);
      }
      
      $methodsHtml='';
      foreach ($class['methods'] as $name => $value) {
        $methodsHtml.=$this->load->view('admin/__/doc_function', array(
          'name'=>$name,
          'inherited'=>el('inherited',$value['doc']),
          'lines'=>$value['lines'],
          'params'=>el('param',$value['doc']),
          'return'=>el('return',$value['doc']),
          'shortdescription'=>el('shortdescription',$value['doc']),
          'description'=>el('description',$value['doc']),
          'author'=>el('author',$value['doc'])
        ),true);
      }
      $html=$this->load->view('admin/__/doc_class',array(
        'file'=>$file,
        'path'=>$class['file'],
        'shortdescription'=>el('shortdescription',$class['doc']),
        'description'=>el('description',$class['doc']),
        'properties'=>$propertiesHtml,
        'methods'=>$methodsHtml
      ),true);
      $content.=highlight_code_if_needed($html);
      $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../'),true);
      
      $fileName='userguide/FlexyAdmin/'.$classPath.'/'.$file.'.html';
      write_file($fileName,$fileContent);
      // group in toc
      $this->toc[$classType][$file]=$fileName;
      $this->_add_to_tipue($fileName,$html,$fileName);
      
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
        
        // get file docblock directly from file...
        $description='';
        $shortdescription='';
        $tags='';
        $f=read_file($path);
        preg_match("/\/\*\*(.*)\*\//uUsm", $f,$matches);
        if (isset($matches[0])) {
          $docBlock=$matches[0];
          $p = new Parser($docBlock); 
          $p->parse();
          $tags = $p->getParams();
          $description=$p->getDesc();
          $shortdescription=$p->getShortDesc();
        }
        $html=$this->load->view('admin/__/doc_file',array(
          'file'=>$file,
          'path'=>$path,
          'shortdescription'=>$shortdescription,
          'description'=>$description,
          'tags'=>$tags,
          'functions'=>$functionsHtml
        ),true);
        $content.=highlight_code_if_needed( $html);
        $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../'),true);
        $fileName='userguide/FlexyAdmin/helpers/'.str_replace('.php','.html',$file);
        write_file($fileName,$fileContent);
        $this->_add_content('Helper file created: '.$fileName.'</br>');
        $this->toc['helpers'][$file]=$fileName;
        $this->_add_to_tipue($fileName,$html,$fileName);
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
    write_file('userguide/FlexyAdmin/assets/js/toc.js',$json_toc);
    $this->_add_content('TOC file created.</br>');
    //
    $tipue_search=$this->load->view('admin/__/doc_tipue_json',array('data'=>$this->tipue),true);
    write_file('userguide/FlexyAdmin/assets/tipuedrop/data.js',$tipue_search);
    $this->_add_content('SEARCH file created.</br>');
    
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
        // if <body> exists, get only all in body tag
        preg_match("/<body>(.*)<\/body>/", $html, $matches);
        if (isset($matches[1])) $html=$matches[1];
        // replace local links /3-link with /link
        $html = preg_replace("/(href=\")(\d-)([^\"]*?)\"/us", "$1$3\"", $html);
        $html = preg_replace("/(href=\"([^\"]*?)\\/)\\d-(.*?)\"/us", "$1$3\"", $html);
        $html = preg_replace("/(href=\"\.\.\/)(\d_)([^\"]*)\"/us", "$1$3\"", $html);
        
        $fileName=str_replace('__doc/','',$file['path']);
        $fileName=preg_replace("/\/(\d_)/u", "/", $fileName);
        $fileName=preg_replace("/\/(\d-)/u", "/", $fileName);
        $content=highlight_code_if_needed( $this->load->view('admin/__/doc_file',array('file'=>$name,'functions'=>$html),true) );
        $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../'),true);
        write_file($fileName,$fileContent);
        $this->_add_content('DOC created: '.$fileName.'</br>');
        $this->toc[$type][$name]=$fileName;
        $this->_add_to_tipue($name,$html,$fileName);
      }
    }
  }

  private function _add_to_tipue($name,$html,$fileName) {
    $html = preg_replace("/(<code>(.*?)<\\/code>)/us", "", $html);  // remove <code> tags and all in it
    foreach ($this->stripTagsWithClasses as $class) {
      $html=preg_replace("/(<p(\s*)class=\"".$class."(.*?)\">(.*?)<\/p>)/us", "", $html); // remove <p> tags with some classes
    }
    foreach ($this->stripWords as $word) {
      $html = preg_replace("/\b".$word."\b/ui", "", $html); // remove some words
    }
    $html = strip_tags($html);
    $html = html_entity_decode($html);
    $html = str_replace(array("\r","\n"),' ',$html); // remove linebreaks
    $html = trim(preg_replace("/(\s+)/", " ", $html)); // remove double spaces
    
    $this->tipue[]=array(
      "title"=>get_suffix(str_replace(array('.html','.php'),'',$name),'/'),
      "text"=>addslashes($html),
      "loc"=>str_replace('userguide/FlexyAdmin/','',$fileName)
    );
  }
  
  
  /**
   * Build an .zip package of this version
   *
   * @return void
   * @author Jan den Besten
   **/
  public function build() {
    $revision=$this->get_revision();
    $tags=$this->tags.'/FlexyAdmin_r'.$revision;
    $this->_add_content('<h1>Build: r_'.$revision.'</h1>');

    // Copy alles
    $this->_add_content('<p>Copy all</p>');
    copy_directory( $this->path.$this->work, $this->path.$tags );
    
    // - verwijder alle bestanden die niet nodig meer zijn (__* van build proces en doc source) 
    // - maak lege db instelling bestand

    // - maak zip, geef dit de naam met revisie nr
    $zip=$this->path.$this->tags.'/FlexyAdmin_r'.$revision.'.zip';
    $this->_add_content('<p>Create:'.$zip.'</p>');
    $this->load->library('zip');
    $this->zip->read_dir($this->path.$tags.'/',FALSE); 
    $this->zip->archive($zip);

    // Cleanup
    $this->_add_content('<p>Cleanup</p>');
    empty_map($this->path.$tags,TRUE,TRUE);
    
    
    $this->_show_all();
  }


}

?>
