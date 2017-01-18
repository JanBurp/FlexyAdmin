<?php 
require_once(APPPATH."core/AdminController.php");
// require_once(APPPATH."core/FrontendController.php");  // Load this also, so PHP can build documentation for this one also

/**
 * Build proces
 *
 * @author Jan den Besten
 */
 
class __ extends AdminController {
  
  private $path='';
  private $userguid='';
  private $tinyMCElibs='../FlexyAdmin_DocsLibs/Libraries/tinyMCE';
  private $work='';
  private $tags='../zips';
  private $hash;
  
  private $upload_path = '/test_afbeeldingen/test_groot';
  
  
  private $tinyMCEkeep=array(
    'maps'  => array('plugins/advhr','plugins/advlink','plugins/advlist','plugins/autolink','plugins/autoresize','plugins/autosave','plugins/bbcode','plugins/contextmenu','plugins/directionality','plugins/emotions','plugins/example','plugins/fullpage','plugins/iespell','plugins/insertdatetime','plugins/layer','plugins/legacyoutput','plugins/lists','plugins/nonbreaking','plugins/noneditable','plugins/pagebreak','plugins/print','plugins/save','plugins/searchreplace','plugins/spellchecker','plugins/tabfocus','plugins/template','plugins/visualblocks','plugins/visualchars','plugins/wordcount','plugins/xhtmlxtras'),
    'files' => array('advimage.css','advimage/image.htm','template.htm')
  );
  
	public function __construct() {
		parent::__construct();
    $this->path       = str_replace('sys/flexyadmin/','',APPPATH);
    $this->userguide  = $this->path.'/userguide/FlexyAdmin/';
    
    $this->upload_path = $_SERVER['DOCUMENT_ROOT'].$this->upload_path;
    // $doxygen = file_get_contents('userguide/doxygen.cfg');
    // $doxygen = preg_replace("/(PROJECT_NUMBER\s*=)(.*)/uim", "$1 ".$this->version->get_version().'&nbsp;('.$this->version->get_revision().')', $doxygen);
    // file_put_contents('userguide/doxygen.cfg',$doxygen);
	}

	public function index() {
    $this->_add_content('<h1>Build processes</h1>');
    $menuArray=array(
      array( 'uri'=>'admin/__/minify', 'name' => 'Minify JS & CSS' ),
      array( 'uri'=>'admin/__/tinymce', 'name' => 'Update tinyMCE' ),
      // array( 'uri'=>'admin/__/clean_assets', 'name' => 'Clean assets' ),
      array( 'uri'=>'admin/__/apidoc', 'name' => 'Create Api doc' ),
      array( 'uri'=>'admin/__/process_svnlog', 'name' => 'Process SVN log' ),
      array( 'uri'=>'admin/__/build', 'name' => 'Build version: '.$this->version->get_version().' ('.$this->version->get_revision().')' ),
      // array( 'uri'=>'admin/__/ajax_upload_text', 'name' => 'API/Ajax upload test' ),
    );
    $menu = new Menu();
    $menu->set_menu($menuArray);
    $this->_add_content($menu->render());
    $this->view_admin();
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
    $this->view_admin();
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
    $this->view_admin();
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
      $this->view_admin();
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
    $changelog='Changes '.$this->version->get_version()->get_version()."\n============\n\n";
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
    
    $this->view_admin();
  }
  
  
  /**
   * Build an .zip package of this version
   *
   * @return void
   * @author Jan den Besten
   **/
  public function build() {
    $tags=$this->tags.'/FlexyAdmin_'.$this->version->get_version();
    $this->_add_content('<h1>Build: '.$this->version->get_version().'</h1>');

    // Copy alles behalve hidden files en files/mappen met __ en _test (dat zijn build processen en autodoc bronbestanden) en node_modules
    $this->_add_content('<p>Copy all</p>');
    copy_directory( $this->path.$this->work, $this->path.$tags, array('/.svn','/__','/_test','/node_modules') );
    
    // - maak lege db instelling bestand
    unlink($this->path.$tags.'/'.SITEPATH.'config/database_local.php');
    rename($this->path.$tags.'/'.SITEPATH.'config/database_local_empty.php', $this->path.$tags.'/'.SITEPATH.'config/database_local.php');

    // - maak zip, geef dit de naam met revisie nr
    $zip= $this->path.$this->tags.'/FlexyAdmin_'.$this->version->get_version().'_r'.$this->version->get_revision().'.zip';
    $this->_add_content('<p>Create:'.$zip.'</p>');
    $this->load->library('zip');
    $this->zip->read_dir($this->path.$tags.'/',FALSE); 
    $this->zip->archive($zip);

    // Cleanup
    $this->_add_content('<p>Cleanup</p>');
    empty_map($this->path.$tags,TRUE,TRUE);
    
    $this->view_admin();
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
    
    $this->view_admin();
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
  
  public function apidoc() {
    $this->_add_content('<h1>Create API documentation</h1>');
    
    $apiMapBackend=APPPATH.'models/api';
    $apiMapFrontend=SITEPATH.'models/api';
    
    // Algemene api doc
    $api=file_get_contents($this->userguide.'__doc/5_api/1-algemeen.dox');
    $api=str_replace('/*! \page algemeen Algemeen','',$api);
    write_file($this->userguide.'api/algemeen.md',$api);
    
    $this->_apidoc($apiMapBackend,'admin_api');
    $this->_apidoc($apiMapFrontend,'frontend_api');
    
    
    $this->view_admin();
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
        $doc.=$api."\n".repeater("-",strlen($api))."\n".$md."\n---------------------------------------\n\n";
      }
    }
    
    $filename=$map.'api.md';
    $filename=$this->userguide.'api/'.$destination.'.md';
    write_file($filename,$doc);
    $this->_add_content('<p>'.$filename.' created.</>');
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
    
    
    $this->view_admin();
  }


}

?>
