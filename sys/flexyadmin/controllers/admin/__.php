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
  private $userguide='userguide/FlexyAdmin/__doc/5_api/';
  private $tinyMCElibs='../FlexyAdmin_DocsLibs/Libraries/tinyMCE';
  private $work='FlexyAdminDEMO';
  private $tags='TAGS';
  private $revision;
  
  private $upload_path = '/test_afbeeldingen/test_groot';
  
  
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
    $this->upload_path = $_SERVER['DOCUMENT_ROOT'].$this->upload_path;
	}

	public function index() {
    $this->_add_content('<h1>Build processes</h1>');
    $menuArray=array(
      array( 'uri'=>'admin/__/doc', 'name' => 'Create Documentation' ),
      array( 'uri'=>'admin/__/apidoc', 'name' => 'Create API Documentation' ),
      array( 'uri'=>'admin/__/minify', 'name' => 'Minify JS & CSS' ),
      array( 'uri'=>'admin/__/tinymce', 'name' => 'Update tinyMCE' ),
      array( 'uri'=>'admin/__/clean_assets', 'name' => 'Clean assets' ),
      array( 'uri'=>'admin/__/process_svnlog', 'name' => 'Process SVN log' ),
      array( 'uri'=>'admin/__/build', 'name' => 'Build revision: '.$this->revision ),
      array( 'uri'=>'admin/__/ajax_upload_text', 'name' => 'API/Ajax upload test' ),
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
  public function doc($actions='toc|render') {
    $actions=explode('|',$actions);
    foreach ($actions as $action) {
      switch ($action) {
        case 'toc':
          $this->doc_toc();
          break;
        case 'render':
          $this->doc_render();
          break;
      }
    }
    $this->_show_all();
  }
  
  private function doc_toc() {
    // Make sure everything is loaded, to make documentation for everything...
    // Load all core libraries that are not standard loaded
    $this->load->dbutil();
    // load all helpers
    $this->load->helper('video');

    // load all libraries
    $libraries=read_map('sys/flexyadmin/libraries','php',FALSE,FALSE);
    unset($libraries['ion_auth.php']); // exclude allready inherited libraries
    unset($libraries['ciunit.php']);
    unset($libraries['editor_lists.php']);
    unset($libraries['jsmin.php']);
    unset($libraries['old_menu.php']);
    
    $modules=read_map('site/libraries','php',FALSE,FALSE); // Frontend libraries (modules)
    $libraries=array_merge($libraries,$modules);
    foreach ($libraries as $file=>$library) {
      $this->load->library(str_replace('my_','',$file));
    }

    // load all models
    $models=read_map('sys/flexyadmin/models','php',FALSE,FALSE);
    $frontend=read_map('site/models','php',FALSE,FALSE);
    $models=array_merge($models,$frontend);
    unset($models['builder.php']);
    unset($models['crud']);
    unset($models['flexy_field.php']);
    unset($models['graph.php']);
    unset($models['grid_set.php']);
    unset($models['ion_auth_model.php']);
    unset($models['login_log.php']);
    unset($models['order.php']);
    unset($models['plugin_handler.php']);
    unset($models['svn.php']);
    foreach ($models as $file=>$model) {
      $file=str_replace('.php','',$file);
      if (!$this->load->exist('model',$file)) {
        $this->load->model($file);
      }
    }
    
    // Ok, start
    $this->load->library('__/doc');
    $doc=$this->doc->doc();
    
    // Now create toc
    // Start with general documents
    $toc=$this->_add_markdown_docs('userguide/FlexyAdmin/__doc');

    // Classes
    foreach ($doc['classes'] as $file => $class) {
      // revision of file
      $revision=$this->svn->get_revision_of($class['file']);
      if ($revision) $class['revision']=$revision;
      
      // determine the kind of file
      $path=explode('/',$class['file']);
      $classPath=$path[count($path)-2];
      $classType=$classPath;
      if ($path[0]=='site') {
        if ($classType=='libraries') {
          $classType='libraries (site)';
        }
        elseif ($classType=='models') {
          $classType='models (site)';
        }
      }
      if ($path[0]=='sys') {
        if (has_string('plugin',strtolower($file))) $classType='plugins (sys)';
      }
      $toc[$classType][$file]=$class;
      $this->_add_content('Class -'.$file.'- added to toc ('.$classPath.')</br>');
    }
    
    // Helpers (functions)
    foreach ($doc['functions'] as $file => $functions) {
      if (!empty($functions)) {
        // get file docblock directly from file...
        $first_func=current($functions);
        $full_file=$first_func['file'];
        // revision of file
        $revision=$this->svn->get_revision_of($full_file);
        // file
        $f=file_get_contents($full_file);
        preg_match("/\/\*\*(.*)\*\//uUsm", $f,$matches);
        if (isset($matches[0])) {
          $docBlock=$matches[0];
          $p = new Parser($docBlock);
          $p->parse();
          $tags = $p->getParams();
          $description = $p->getDesc();
          $shortdescription = $p->getShortDesc();
          if (!isset($tags['ignore'])) {
            $name=str_replace('.php','',$file);
            $toc['helpers'][$name]=array(
              'file' => $file,
              'revision' => $revision,
              'doc' => array(
                'shortdescription'=>$shortdescription,
                'description'=>$description,
                'tags'=>$tags,
              ),
              'functions'=>$functions
            );
            $this->_add_content('Helper -'.$name.'- added to toc</br>');
          }
        }
      }
    }
    
    unset($toc['less']);

    $this->toc_order=array('start','gevorderden','database','modules_en_plugins','api','libraries (site)','models (site)','helpers','plugins (sys)','libraries','models','core');
    $otoc=array();
    foreach ($this->toc_order as $key) {
      if ($key=='|')
        $otoc[]='|';
      else {
        if (!empty($toc[$key])) {
          $otoc[$key]=$toc[$key];
        }
        else {
          $otoc[$key]=array();
        }
      }
    }
    
    // Markdown all (short(description))
    $otoc=$this->markdown_descriptions($otoc);

    $json_toc=array2json($otoc);
    $json_toc=json_encode($otoc);
    $tocfile='userguide/FlexyAdmin/toc.json';
    write_file($tocfile,$json_toc);
    $this->_add_content('TOC file created.</br>');
  }
  
  private function nice_methods($methods) {
    foreach ($methods as $name => $method) {
      $nice_name='<code>'.$name.'(';
      $first=true;
      if (isset($method['doc']['param'])) {
        foreach ($method['doc']['param'] as $key => $param) {
          // nice param
          $methods[$name]['doc']['param'][$key]['nice_param']=$this->nice_param($param);
          // nice name
          if (!$first) $nice_name.=', ';
          if (isset($param['default'])) $nice_name.='[';
          $nice_name.='('.$param['type'].') $'.str_replace(array('[',']'),array('=',''),$param['param']);
          if (isset($param['default'])) $nice_name.=']';
          $first=false;
        }
      }
      $nice_name.=')</code>';
      $methods[$name]['nice_name']=highlight_code_if_needed($nice_name);
      if (empty($methods[$name]['return']))
        $methods[$name]['nice_return']=highlight_code_if_needed('<code>(void)</code>');
      else
        $methods[$name]['nice_return']=$this->nice_param($methods[$name]['return']);
    }
    return $methods;
  }
  private function nice_param($param) {
    $nice='<code>('.$param['type'].') $'.$param['param'].' // '.$param['desc'].'</code>';
    return highlight_code_if_needed($nice);
  }
  private function nice_properties($properties) {
    foreach ($properties as $name => $property) {
      $nice='<code>';
      if (isset($property['var'][0])) $nice.='('.trim(strip_tags($property['var'][0])).')';
      $nice.=' '.$name.'</code>';
      $properties[$name]['nice_property']=highlight_code_if_needed($nice);
    }
    return $properties;
  }
  
  private function markdown_descriptions($items) {
    foreach ($items as $key => $value) {
      if (is_array($value)) {
        $items[$key]=$this->markdown_descriptions($value);
      }
      else {
        if ($key=='description' or $key=='shortdescription') {
          $items[$key]=highlight_code_if_needed(Markdown($value));
        }
      }
    }
    return $items;
  }
  
  private function doc_render() {
    // Load toc
    $json_toc=file_get_contents('userguide/FlexyAdmin/toc.json');
    $toc=json_decode($json_toc,true);
    
    // Render as big HTML file
    $userguide=array();
    // $toc=array_slice($toc,0,4);
    foreach ($toc as $head => $sub) {
      $title=ucfirst($head);
      $userguide[$title]=array();
      foreach ($sub as $name => $item) {
        $doc=$item['doc'];
        $view_data=$item;
        $view_data['id']=safe_string($name);
        $view_data['name']=$name;
        $view_data['properties']=null;
        $view_data['methods']=null;
        $view_data['functions']=null;
        if (isset($item['properties'])) {
          $item['properties']=$this->nice_properties($item['properties']);
          $view_data['properties']=$this->load->view('admin/__/properties',array('title'=>$title,'properties'=>$item['properties']),true);
        }
        if (isset($item['methods']))    {
          $item['methods']=$this->nice_methods($item['methods']);
          $view_data['methods']=$this->load->view('admin/__/methods',array('title'=>$name,'methods'=>$item['methods']),true);
        }
        if (isset($item['functions']))  {
          $item['functions']=$this->nice_methods($item['functions']);
          $view_data['functions']=$this->load->view('admin/__/methods',array('title'=>$name,'methods'=>$item['functions']),true);
        }
        $html=$this->load->view('admin/__/item',$view_data,true);
        $this->_add_content($name.' added.</br>');
        $userguide[$title][$name]=$html;
      }
    }
    
    $html=$this->load->view('admin/__/userguide',array('items'=>$userguide),true);
    // Glyphicons
    $html = preg_replace("/\{glyphicon-(.*)\}/um", '<span class="glyphicon glyphicon-$1"></span>', $html);
    write_file('userguide/FlexyAdmin/userguide.html',$html);
    
    $index=$this->load->view('admin/__/index',array('root'=>'./','userguide'=>$html,'revision'=>$this->revision),true);
    write_file('userguide/FlexyAdmin/index.html',$index);
    
    $this->_add_content('<br>Userguide.html created.</br>');
    $this->_add_content('<br>index.html created.</br>');
 
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
    $toc=array();
    foreach ($files as $name  => $file) {
      if ($file['type']=='dir') {
        $dir=str_replace('__doc/','',$file['path']);
        $dir=preg_replace("/\/(\d_)/u", "/", $dir);
        // if (!file_exists($dir)) mkdir($dir);
        $fname=remove_prefix($name,'_');
        $toc[$fname]=$this->_add_markdown_docs($path.'/'.$name);
      }
      elseif ($file['type']=='md')  {
        $name=ucfirst(str_replace(array('_','.html','.md'),array(' ',''),remove_prefix($name,'-')));
        $path=explode('/',$file['path']);
        $path=$path[count($path)-2];
        $type=remove_prefix($path,'_');
        $markdown=file_get_contents($file['path']);
        $toc[$name]=array(
          'file'=> $file['path'],
          'doc' => array(
            'description' => $markdown,
          ),
        );
  
      }
    }
    return $toc;
  }

  
  
  /**
   * Update the tinyMCE editor
   *
   * @return void
   * @author Jan den Besten
   */
  public function tinymce() {
    $versionFile='sys/tinymce/version.txt';
    $currentVersion=file_get_contents($versionFile);
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
    
    $old_changelog=file_get_contents('changelog.txt');
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
    copy_directory( $this->path.$this->work, $this->path.$tags, array('/.svn','/__','/_test','/node_modules','/bower_components') );
    
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
  
  
  
  public function ajax_upload_text() {
    $this->_add_content('<h1>API/Ajax upload test</h1>');

    // UPLOADING files
    // $upload_files=scandir($this->upload_path);
    // $upload_files=array_slice($upload_files,2,2);
    // foreach ($upload_files as $file) {
    //   trace_($this->upload_path.'/'.$file);
    //   $_FILES      = array( 'file' => array(
    //     'name'     => $file,
    //     'tmp_name' => '/tmp/php42up23',
    //     // 'type'     => 'text/plain',
    //     // 'size'     => 42,
    //     // 'error'    => 0
    //   ));
    //
    // };
    
    
    $this->_show_all();
  }
  
  
  
  public function apidoc() {
    $this->_add_content('<h1>Create API documentation</h1>');
    
    $apiMapBackend=APPPATH.'models/api';
    $apiMapFrontend=SITEPATH.'models/api';
    
    $this->_apidoc($apiMapBackend,'2-admin_api');
    $this->_apidoc($apiMapFrontend,'3-frontend_api');
    
    $this->_show_all();
  }
  
  private function _apidoc($map,$destination) {
    $files=read_map($map,'php',false,false);
    unset($files['api_model.php']);
    
    $doc = '';
    foreach ($files as $name => $file) {
      $text=file_get_contents($file['path']);
      if (preg_match("/\/\*\*(.*)\*\//uUsm", $text,$matches)) {
        $md=$matches[1];
        $md = preg_replace("/^\s\* /uUsm", "", $md);
        $md = preg_replace("/- /uUsm", " - ", $md);
        $md = preg_replace("/^@(.*)\n/um", "", $md);
        $api="`_api/".str_replace('.php','',$name).'`';
        $doc.=$api."\n".str_repeat("-",strlen($api))."\n".$md."\n---------------------------------------\n\n";
      }
    }
    
    $filename=$map.'/api.md';
    $filename=$this->userguide.$destination.'.md';
    write_file($filename,$doc);
    $this->_add_content('<p>'.$filename.' created.</>');
  }
  
  
  
    


}

?>
