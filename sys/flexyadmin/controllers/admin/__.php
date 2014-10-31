<?php 
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
  private $tinyMCElibs='../FlexyAdmin_DocsLibs/Libraries/tinyMCE';
  private $work='FlexyAdminDEMO';
  private $tags='TAGS';
  private $revision;
  
  private $tinyMCEkeep=array(
    'maps'  => array('plugins/advhr','plugins/advlink','plugins/advlist','plugins/autolink','plugins/autoresize','plugins/autosave','plugins/bbcode','plugins/contextmenu','plugins/directionality','plugins/emotions','plugins/example','plugins/fullpage','plugins/iespell','plugins/insertdatetime','plugins/layer','plugins/legacyoutput','plugins/lists','plugins/nonbreaking','plugins/noneditable','plugins/pagebreak','plugins/print','plugins/save','plugins/searchreplace','plugins/spellchecker','plugins/tabfocus','plugins/template','plugins/visualblocks','plugins/visualchars','plugins/wordcount','plugins/xhtmlxtras'),
    'files' => array('advimage.css','advimage/image.htm','template.htm')
  );
  
  private $stripTagsWithClasses=array('doc_info','doc_param_type','doc_label');
  private $stripWords=array('(string)', '(array)', '(void)', '(bool)', '(mixed)', '(object)',
                            'CI','CodeIgniter','PHP','FlexyAdmin','class','parameters', 'functions', 'function', '__construct', 'methods', 'properties', 'true','false', 'array','return:', 'global','instance',
                            'en','een','of','de','het', 'dat','als','met','voor' ,'in','je','wat','over','om','is','aan','uit','die','te','ze','op','deze','kun',
                            'if','the','and','or','name','content','config','use', 'this', 'to', 'own', 'see', 'also', 'file' ,'you','your','re', 'code', 'from', 'which');
  private $allTags='';

	public function __construct() {
		parent::__construct();
    $this->load->model('svn');
    $this->load->helper('markdown');
    $this->revision=$this->svn->get_revision();
    
	}

	public function index() {
    $this->_add_content('<h1>Build processes</h1>');
    $menuArray=array(
      array( 'uri'=>'admin/__/doc', 'name' => 'Create Documentation' ),
      array( 'uri'=>'admin/__/minify', 'name' => 'Minify JS & CSS' ),
      array( 'uri'=>'admin/__/tinymce', 'name' => 'Update tinyMCE' ),
      array( 'uri'=>'admin/__/clean_assets', 'name' => 'Clean assets' ),
      array( 'uri'=>'admin/__/process_svnlog', 'name' => 'Process SVN log' ),
      array( 'uri'=>'admin/__/build', 'name' => 'Build revision: '.$this->revision ),
    );
    $menu = new Menu();
    $menu->set_menu($menuArray);
    $this->_add_content($menu->render());
    $this->_show_all();
	}


  /**
   * Create documentation of the FlexyAdmin API
   *
   * @return void
   * @author Jan den Besten
   */
  public function doc() {
    $this->_add_content('<h1>Creating documentation</h1>');

    // Cleanup current doc folders
    $folders=array('algemeen','core','database','helpers','libraries','meer','models','tutorials','uitbreiden');
    foreach ($folders as $folder) {
      $map=$this->path.$this->work.'/userguide/FlexyAdmin/'.$folder;
      $files=read_map($map,'html',FALSE,FALSE);
      foreach ($files as $file => $info) {
        unlink($info['path']);
      }
      $this->_add_content('Emptied: '.$map.'<br/>');
    }

    // Make sure everything is loaded, to make documentation for everything...
    // Load all core libraries that are not standard loaded
    $this->load->dbutil();
    // load all helpers
    $this->load->helper('video');

    // load all libraries
    $libraries=read_map('sys/flexyadmin/libraries','php',FALSE,FALSE);
    unset($libraries['ion_auth.php']); // exclude allready inherited libraries
    unset($libraries['gmap.php']); // exclude allready inherited libraries
    $modules=read_map('site/libraries','php',FALSE,FALSE); // Frontend libraries (modules)
    $libraries=array_merge($libraries,$modules);
    foreach ($libraries as $file=>$library) {
      $this->load->library(str_replace('my_','',$file));
    }

    // load all models
    $models=read_map('sys/flexyadmin/models','php',FALSE,FALSE);
    $frontend=read_map('site/models','php',FALSE,FALSE);
    $models=array_merge($models,$frontend);
    foreach ($models as $file=>$model) {
      $file=str_replace('.php','',$file);
      if (!$this->load->exist('model',$file)) {
        $this->load->model($file);
      }
    }

    // Include general documents
    $this->_add_markdown_docs('userguide/FlexyAdmin/__doc');

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
          $classType='libraries (site)';
          // if (has_string('Plugin',$file)) $classType='plugins (site)';
        }
        elseif ($classType=='models') {
          $classType='models (site)';
        }
      }
      // else {
      //   if ($classType=='libraries') {
      //     if (has_string('Plugin',$file) and $file!='Plugin') $classType='plugins';
      //   }
      // }

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
        'parent'=>$class['parent'],
        'path'=>$class['file'],
        'shortdescription'=>el('shortdescription',$class['doc']),
        'description'=>el('description',$class['doc']),
        'properties'=>$propertiesHtml,
        'methods'=>$methodsHtml,
        'doc'=>$class['doc']
      ),true);
      $content.=highlight_code_if_needed($html);
      $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../','revision'=>$this->revision),true);

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
            'doc'=>$value['doc']
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
        if (!isset($tags['ignore'])) {
          $name=str_replace('.php','',$file);
          $html=$this->load->view('admin/__/doc_file',array(
            'file'=>$name,
            'path'=>$path,
            'shortdescription'=>$shortdescription,
            'description'=>$description,
            'doc'=>$tags,
            'functions'=>$functionsHtml
          ),true);
          $content.=highlight_code_if_needed( $html);
          $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../','revision'=>$this->revision),true);
          $fileName='userguide/FlexyAdmin/helpers/'.str_replace('.php','.html',$file);
          write_file($fileName,$fileContent);
          $this->_add_content('Helper file created: '.$fileName.'</br>');
          $this->toc['helpers'][$name]=$fileName;
          $this->_add_to_tipue($fileName,$html,$fileName);
        }
      }

    }

    // trace_($doc);
    // trace_($this->toc);

    $this->toc_order=array('algemeen','meer','tutorials','uitbreiden','database','libraries (site)','models (site)','helpers','libraries','models','core');
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
    $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'','revision'=>$this->revision),true);
    $fileName='userguide/FlexyAdmin/index.html';
    write_file($fileName,$fileContent);
    //
    $json_toc=$this->load->view('admin/__/doc_toc_json',array('toc'=>$otoc,'html'=>trim(str_replace(array(PHP_EOL,"\r",'../userguide/FlexyAdmin/'),'',$content))),true);
    write_file('userguide/FlexyAdmin/assets/js/toc.js',$json_toc);
    $this->_add_content('TOC file created.</br>');

    // remove tags from normal search texts
    // $tags=$this->_clean_tags($this->allTags);
    // $tags=explode(' ',$tags);
    // $tags=array_unique($tags);
    // foreach ($this->tipue as $key => $value) {
    //   foreach ($tags as $tag) {
    //     $tag=str_replace(array('(',')'),'',$tag);
    //     $this->tipue[$key]['text'] = preg_replace("/".$tag."/ui", "", $value['text']); // remove tags
    //   }
    // }
    
    $tipue_search=$this->load->view('admin/__/doc_tipue_json',array('data'=>$this->tipue),true);
    write_file('userguide/FlexyAdmin/assets/tipuedrop/data.js',$tipue_search);
    $this->_add_content('SEARCH file created.</br>');

    $this->_show_all();
  }

  private function _clean_tags($tags) {
    foreach ($this->stripWords as $word) {
      $tags = preg_replace("/\b".$word."\b/ui", "", $tags); // remove some words
    }
    $tags=explode(' ',$tags);
    // remove tags shorter than 3 and without () and the end
    foreach ($tags as $key => $tag) {
      $len=strlen($tag);
      if (substr($tag,$len-2,2)!='()' or $len<3)
        unset($tags[$key]);
    }
    $tags=implode(' ',$tags);
    return $tags;
  }

  private function _add_markdown_docs($path) {
    $files=read_map($path,'',TRUE,FALSE);
    foreach ($files as $name  => $file) {
      if ($file['type']=='dir') {
        $dir=str_replace('__doc/','',$file['path']);
        $dir=preg_replace("/\/(\d_)/u", "/", $dir);
        if (!file_exists($dir)) mkdir($dir);
        $this->_add_markdown_docs($path.'/'.$name);
      }
      elseif ($file['type']=='md')  {
        $name=ucfirst(str_replace(array('_','.html','.md'),array(' ',''),remove_prefix($name,'-')));
        $path=explode('/',$file['path']);
        $path=$path[count($path)-2];
        $type=remove_prefix($path,'_');

        $markdown=read_file($file['path']);
        $html = Markdown($markdown);
        // replace local links /3-link with /link
        $html = preg_replace("/(href=\")(\d-)([^\"]*?)\"/us", "$1$3\"", $html);
        $html = preg_replace("/(href=\"([^\"]*?)\\/)\\d-(.*?)\"/us", "$1$3\"", $html);
        $html = preg_replace("/(href=\"\.\.\/)(\d_)([^\"]*)\"/us", "$1$3\"", $html);

        $fileName=str_replace('__doc/','',$file['path']);
        $fileName=str_replace('.md','.html',$fileName);
        $fileName=preg_replace("/\/(\d_)/u", "/", $fileName);
        $fileName=preg_replace("/\/(\d-)/u", "/", $fileName);

        $content=highlight_code_if_needed( $this->load->view('admin/__/doc_file',array('file'=>$name,'functions'=>$html),true) );
        $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../','revision'=>$this->revision),true);
        write_file($fileName,$fileContent);
        $this->_add_content('DOC created: '.$fileName.'</br>');
        $this->toc[$type][$name]=$fileName;
        $this->_add_to_tipue($name,$html,$fileName);
      }
    }
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
        $fileContent=$this->load->view('admin/__/doc',array('content'=>$content,'root'=>'../','revision'=>$this->revision),true);
        write_file($fileName,$fileContent);
        $this->_add_content('DOC created: '.$fileName.'</br>');
        $this->toc[$type][$name]=$fileName;
        $this->_add_to_tipue($name,$html,$fileName);
      }
    }
  }

  private function _add_to_tipue($name,$html,$fileName) {
    $tags='';
    if (preg_match_all("/<h[1-3]([^>]*)>(.*?)<\/h\d>/uis", $html, $matches)) {
      if (isset($matches[2])) {
        foreach ($matches[2] as $key => $match) {
          $match=strip_tags($match);
          $match = str_replace(array("\r","\n"),' ',$match); // remove linebreaks
          $match = trim(preg_replace("/(\s+)/", " ", $match)); // remove double spaces
          foreach ($this->stripWords as $word) {
            $match = preg_replace("/\b".$word."\b/ui", " ", $match); // remove some words
          }
          $match = preg_replace("/\(.*?\)/uiUs", "()", $match);
          $match = trim($match);
          if (!empty($match)) {
            $tags=add_string($tags,$match,' ');
            $tags=$this->_clean_tags($tags);
            $this->allTags=add_string($this->allTags,$tags,' ');
          }
        }
      }
    }

    $html = preg_replace("/(<code>(.*?)<\\/code>)/us", " ", $html);  // remove <code> tags and all in it
    foreach ($this->stripTagsWithClasses as $class) {
      $html=preg_replace("/(<p(\s*)class=\"".$class."(.*?)\">(.*?)<\/p>)/us", " ", $html); // remove <p> tags with some classes
    }
    // Only get imported text (headers)
    if (preg_match_all("/<h[1-3]([^>]*)>(.*?)<\/h\d>/uis", $html, $matches)) {
      if (isset($matches[2])) {
        $html='';
        foreach ($matches[2] as $key => $match) {
          // $match=strip_tags($match);
          $match = str_replace(array("\r","\n"),' ',$match); // remove linebreaks
          // $match = trim(preg_replace("/(\s+)/", " ", $match)); // remove double spaces
          foreach ($this->stripWords as $word) {
            $match = preg_replace("/\b".$word."\b/ui", " ", $match); // remove some words
          }
          $match = preg_replace("/\(.*?\)/uiUs", "", $match); // remove all between ()
          $match = trim($match);
          if (!empty($match)) {
            $html=add_string($html,$match," ");
          }
        }
      }
    }
    else {
      $html='';
    }

    foreach ($this->stripWords as $word) {
      $html = preg_replace("/\b".$word."\b/ui", " ", $html); // remove some words
    }
    $html = strip_tags($html);
    $html = html_entity_decode($html);
    $html = str_replace(array("\r","\n"),' ',$html); // remove linebreaks
    $html = trim(preg_replace("/(\s+)/", " ", $html)); // remove double spaces

    $this->tipue[]=array(
      "title"=>get_suffix(str_replace(array('.html','.php'),'',$name),'/'),
      "text"=>addslashes($html),
      "loc"=>str_replace('userguide/FlexyAdmin/','',$fileName),
      "tags"=>$tags
    );
  }
  
  
  
  /**
   * Update the tinyMCE editor
   *
   * @return void
   * @author Jan den Besten
   */
  public function tinymce() {
    $versionFile='sys/tinymce/version.txt';
    $currentVersion=read_file($versionFile);
    $match=array();
    if (preg_match("/\\s(.*)\\s/uiU", $currentVersion,$match)) {
      $currentVersion=$match[1];
      $tinyMCEversions=read_map($this->tinyMCElibs,'dir',FALSE,FALSE,FALSE);
      krsort($tinyMCEversions);
      $versionText=str_replace('.','',$currentVersion);
      foreach ($tinyMCEversions as $key => $value) {
        if (preg_match("/tinymce_(.*)?_jquery/uiU", $key,$match)) {
          $v=str_replace('_','',$match[1]);
          if ($v<=$versionText) unset($tinyMCEversions[$key]);
        }
        else {
          unset($tinyMCEversions[$key]);
        }
      }
      $newVersion=current($tinyMCEversions);
      $newVersionMap=$newVersion['name'];
      
      if (preg_match("/tinymce_(.*)?_jquery/uiU", $newVersionMap,$match)) {
        $newVersion=$match[1];
        if ($newVersion>$currentVersion) {
          $this->_add_content('<h1>Update tinyMCE: from '.$currentVersion.' to '.$newVersion.'</h1>');

          $currentPath='sys/tinymce/jscripts/tiny_mce';
          $files=read_map($this->tinyMCElibs.'/'.$newVersionMap.'/jscripts','',TRUE,FALSE,FALSE,FALSE);
          $changedFiles=array();
          $newFiles=array();
          foreach ($files as $key => $value) {
            $oldName=str_replace(array(strtolower($this->tinyMCElibs.'/'.$newVersionMap)),array('sys/tinymce'),$key);
            if (file_exists($oldName)) {
              if (!has_string($this->tinyMCEkeep['files'],$oldName)) {
                if (is_newer_than($key,$oldName)) {
                  // Ok filedate is newer, but is file realy changed?
                  if (is_different($key,$oldName)) {
                    $changedFiles[]=array('current'=>$oldName,'new'=>$key);
                  }
                }
              }
            }
            else {
              if (!has_string($this->tinyMCEkeep['maps'],$key)) {
                $newFiles[]=array('new'=>$key);  
              }
            }
          }

          if ($newFiles) {
            // move them
            $this->_show_files($newFiles,'New Files:');
            $this->_add_content('<p class="error">TODO: Moving new files</p>');
          }
          
          if ($changedFiles) {
            // copy them
            $this->_show_files($changedFiles,'Changed Files:');
            foreach ($changedFiles as $key => $value) {
              if (!copy($value['new'],$value['current'])) $this->_add_content('<p class="error">Error moving "'.$value['new'].'"</p>');
            }
          }

        }
        
      }
    }
    $this->_show_all();
  }
  

  function _show_files($files,$title) {
    $this->_add_content('<h3>'.$title.'</h3><ul>');
    foreach ($files as $key => $value) {
      $file=$value['new'];
      $file=str_replace(array(strtolower($this->tinyMCElibs)),array(''),$file);
      $this->_add_content('<li>'.$file.'</li>');
    }
    $this->_add_content('</ul><p>['.count($files).']</p>');
  }
  
  /**
   * Remove all files from assets
   *
   * @return void
   * @author Jan den Besten
   */
  public function clean_assets() {
    $this->_add_content('<h1>Clean assets</h1>');

		$assets=$this->config->item('ASSETS');
		// set user maps
		$maps=read_map($assets,'dir',FALSE,FALSE);
    $maps=array_unset_keys($maps,array('css','js','img','lists'));
		foreach ($maps as $map => $value) {
			$path=$assets.$map;
      $this->_add_content('<p>'.$path.'</p>');
      empty_map($path);
		}
    $this->_show_all();
  }
  
  
  public function process_svnlog() {
    $this->_add_content('<h1>Process SVN log</h1>');
    
    $log=$this->input->post('svnlog');
    $from=(int)$this->input->post('from');
    if (empty($log) or empty($from)) {
      $this->load->library('form');
      $fields=array('svnlog'=>array('type'=>'textarea'), 'from'=>array());
      $form=new Form();
      $form->set_data($fields);
      $this->_add_content($form->render('Log'));
      $this->_show_all();
      return;
    }
    
    // Fetch
    $svn=array();
    if (preg_match_all("/(\\d.\\d\\d\\d)\\n((.*)\\ncopy\\nchanges(\\d*)\\n)(.*)\\n/uiUsmx", $log,$matches)) {
      // trace_($matches);
      foreach ($matches[1] as $key => $value) {
        $value = (int)str_replace('.','',$value);
        $matches[1][$key]=$value;
        if ($value<=$from) {
          unset($matches[0][$key]);
          unset($matches[1][$key]);
          unset($matches[2][$key]);
          unset($matches[3][$key]);
          unset($matches[4][$key]);
          unset($matches[5][$key]);
        }
      }
      
      foreach ($matches[1] as $key => $value) {
        $rev=$value;
        $log=$matches[3][$key];
        $log=explode("\n",$log);
        // Clean logs
        foreach ($log as $key => $value) {
          $log[$key]=trim(ltrim($value,'-'));
        }
        // Combine some logs with :
        if ($keys=array_ereg_search(':$',$log)) {
          // trace_($keys);
          // trace_($log);
          $newlog=$log;
          foreach ($keys as $kk) {
            $combined=$log[$kk];
            $k=$kk+1;
            $end=false;
            while (isset($log[$k]) and !$end) {
              $line=$log[$k];
              if (in_array(substr($line,0,1),array('-','.','*'))) {
                $combined.="\n  * ".trim(substr($line,1));
                unset($log[$k]);
              }
              else {
                $end=true;
              }
              $k++;
            }
            // trace_($combined);
            $log[$kk]=$combined;
          }
        }
        $svn[$rev]=array(
          'rev'=>$rev,
          'date'=>$matches[5][$key],
          'log'=>$log
        );
      }
    }
    // trace_($svn);
    // Combi

    // Auto create new Changelog
    $changes=array(
      'MYSQL'=>array('sql'),
      'UPDATE'=>array('update','updated'),
      'FRONTEND'=>array('controller','module'),
      'USERGUIDE'=>array('userguide','docs'),
      'NEW'=>array('new','added','add'),
      'BUGS'=>array('bug','bugs','problem','problems','error'),
      'OTHERS'=>array(),
    );

    $newchangelog=$changes;
    foreach ($newchangelog as $key => $value) {
      $newchangelog[$key]=array();
    }
    if ($svn) {
      foreach ($svn as $rev => $item) {
        foreach ($item['log'] as $log) {
          $fit=FALSE;
          foreach ($changes as $key => $triggers) {
            if (!$fit) {
              if (has_string($triggers,$log,FALSE)) {
                if (!in_array($log,$newchangelog[$key])) {
                  $newchangelog[$key][]=$log;
                }
                $fit=TRUE;
              }
            }
          }
          if (!$fit and !in_array($log,$newchangelog['OTHERS'])) $newchangelog['OTHERS'][]=$log;
        }
      }
    }
    
    $this->_add_content('<h1>New Changelog - Added</h1>');
    $changelog='Changes '.$this->svn->get_revision()."\n============\n\n";
    foreach ($newchangelog as $key => $value) {
      $changelog.=$key.":\n";
      foreach ($value as $entry) {
        $changelog.='- '.$entry."\n";
      }
      $changelog.="\n";
    }
    $this->_add_content('<pre>'.htmlentities($changelog).'</pre>');
    
    $old_changelog=read_file('changelog.txt');
    $new_changelog=$changelog."\n\n".$old_changelog;
    write_file('changelog.txt',$new_changelog);
    
    $this->_show_all();
  }
  
  
  /**
   * Build an .zip package of this version
   *
   * @return void
   * @author Jan den Besten
   **/
  public function build() {
    $revision=$this->svn->get_revision() + 1;
    $tags=$this->tags.'/FlexyAdmin_r'.$revision;
    $this->_add_content('<h1>Build: r_'.$revision.'</h1>');

    // Copy alles behalve hidden files en files/mappen met __ en _test (dat zijn build processen en autodoc bronbestanden) en node_modules
    $this->_add_content('<p>Copy all</p>');
    copy_directory( $this->path.$this->work, $this->path.$tags, array('/.svn','/__','/_test','/node_modules') );
    
    // - maak lege db instelling bestand
    unlink($this->path.$tags.'/site/config/database_local.php');
    rename($this->path.$tags.'/site/config/database_local_empty.php', $this->path.$tags.'/site/config/database_local.php');

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
  
  
  /**
   * Minify all JavaScript and CSS files (admin)
   *
   * @return void
   * @author Jan den Besten
   **/
  public function minify() {
    $this->_add_content('<h1>Minify</h1>');
    
    $this->load->library('jsmin');

    $path = str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']).'sys/flexyadmin/assets/';
    
    $jsFiles=read_map($path.'js','js',TRUE,FALSE);
    $cssFiles=read_map($path.'css','css',TRUE,FALSE);
    $files=array_merge($cssFiles,$jsFiles);
    // exclude some
    foreach ($files as $key => $value) {
      if (has_string(array('.min.','ie','__','nospam','swfobject'),$key)) unset($files[$key]);
    }

    foreach ($files as $file) {
      if ($file['type']=='js') {
        $minFile=str_replace('.js','.min.js',$file['path']);
        $minified = JSMin::minify(file_get_contents($file['path']));
      }
      elseif ($file['type']=='css') {
        $minFile=str_replace('.css','.min.css',$file['path']);
        $minified = $this->minimize_css(file_get_contents($file['path']));
      }
      write_file($minFile,$minified);
      $this->_add_content('<p>'.$minFile.'</p>');
    }
    
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
