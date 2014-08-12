<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/less/Less.php');

class Builder extends CI_Model {
  
  var $settings=array();
  var $report='';
  var $errors=array();
  
	public function __construct($settings=false) {
		parent::__construct();
    if (!$settings) {
      $this->load->config('build',true);
      $settings=$this->config->item('build');
    }
    if (empty($settings)) {
      show_error("site/config/build.php doesn't exists",200);
    }
    $this->initialize($settings);
    $this->load->library('parser');
	}
  
  public function initialize($settings) {
    $this->settings=$settings;
  }
  
	public function go() {
    $this->time_start = microtime(true);
    
    // LESS & CSS
    
    $this->settings['less_files']=$this->get_files('less');
    $this->settings['css_files']=$this->get_files('css');
    
    $needs_compiling=true;
    if (file_exists(el('dest_file',$this->settings))) {
      $needs_compiling=false;
      // Check if some file is changed, so compile is needed
      $last_changed=filemtime($this->settings['dest_file']);
      $files_to_check=array_keys($this->settings['less_files']);
      $files_to_check=array_merge($files_to_check,$this->settings['css_files']);
      $files_to_check=array_combine($files_to_check,$files_to_check);
      foreach ($files_to_check as $key => $file) {
        $diff_time=filemtime($file)-$last_changed;
        $files_to_check[$key]=($diff_time>0);
        $needs_compiling=($needs_compiling or $files_to_check[$key]);
      }
    }

    $this->report=h('Compile LESS files & Minify CSS files');
    
    if ($needs_compiling) {
      // 1 - Compile Less files
      $this->report.=h('Compiling LESS files',2);
      $less_files=$this->settings['less_files'];
      foreach ($less_files as $less => $css) {
        try{
          $parser = new Less_Parser();
          $parser->parseFile($less, site_url());
          $output = $parser->getCss();
          write_file($css,$output);
      		$this->report.=$less.' => '.$css.br();
        } catch(Exception $e){
          $message=$e->getMessage();
          $char=$e->index;
          $less_=read_file($less);
          $less_=substr($less_,0,$char);
          $line=substr_count($less_,"\n") + 1;
          $error='Fatal error: ' . $message. ' at line '.$line;
      		$this->report.=p('error').$error._p();
          $this->errors[]=$error;
        }
      }
      
      // 2 - Combine files
      $this->report.=h('Combining CSS files',2);
      $combine='';
      $css_files=$this->settings['css_files'];
      foreach ($css_files as $css_file) {
        $combine.=$this->minimize_css( read_file($css_file) );
      }
  		$this->report.=implode('<br>',$css_files);
      // 3 - Minify
      $minified=$this->minimize_css($combine);
      // 4 - Add banner
      $banner = el('banner',$this->settings,'');
      $data=array(
        'date'           => date('j M Y'),
        'execution_time' => number_format($this->execution_time(),5)
      );
      $banner = $this->parser->parse_string($banner,$data,true);
      $minified=$banner.$minified;
      // 5 - Save
      write_file(el('dest_file',$this->settings,false), $minified);
  		$this->report.=h('CREATED',2).el('dest_file',$this->settings,false);
    }
    else {
      $this->report.=h('No less or css file changed',2);
    }
    
    $js_start = microtime(true);
    // JS
    $this->settings['js_files']=$this->get_files('js');
    $needs_uglify=true;
    if (file_exists(el('js_dest_file',$this->settings,''))) {
      $needs_uglify=false;
      // Check if some file is changed, so compile is needed
      $last_changed=filemtime($this->settings['js_dest_file']);
      $files_to_check=$this->settings['js_files'];
      $files_to_check=array_combine($files_to_check,$files_to_check);
      foreach ($files_to_check as $key => $file) {
        $diff_time=filemtime($file)-$last_changed;
        $files_to_check[$key]=($diff_time>0);
        $needs_uglify=($needs_uglify or $files_to_check[$key]);
      }
    }
    
    if ($needs_uglify) {
      $this->report.=h('Combine & Uglify JS files',2);
      // 6 Combine js
      $this->load->library('jsmin');
      $ugly='';
      foreach ($this->settings['js_files'] as $file) {
        if (file_exists($file)) $ugly.=file_get_contents($file);
      }
      $this->report.=implode('<br>',$this->settings['js_files']);

      // 7 Uglify js
      $ugly=JSMin::minify($ugly);
      
      // 8 Add banner
      $banner = el('js_banner',$this->settings,'');
      $data=array(
        'date'           => date('j M Y'),
        'execution_time' => number_format($this->execution_time($js_start),5)
      );
      $banner = $this->parser->parse_string($banner,$data,true);
      $ugly=$banner.$ugly;
      
      // 9 SAVE
      write_file(el('js_dest_file',$this->settings,false), $ugly);
  		$this->report.=h('CREATED',2).el('js_dest_file',$this->settings);
    }
    else {
      $this->report.=h('No Javascript file changed',2);
    }

    $version=$needs_compiling or $needs_uglify;
    if ($version and $this->db->field_exists('int_version','tbl_site')) {
      $sql="UPDATE `tbl_site` SET `int_version`=LAST_INSERT_ID(`int_version`+1)";
      $this->db->query($sql);
      $version=$this->db->insert_id();
    }
    
    $this->report.=h('Total Execution Time',2).number_format($this->execution_time(),5).' Secs'.br();
    if ($version) $this->report.='Version: '.$version;

    return $version;
	}
  
  private function get_files($type) {
    $files=el($type.'_files',$this->settings,'auto');
    if (is_string($files)) {
      if ($files=='auto')
        $files=$this->find_files($type);
      else
        $files=array($files);
    }
    return $files;
  }
  
  private function find_files($type) {
    $site=read_file('site/views/site.php');
    $files=array();
    $tag='src';
    if ($type=='css') $tag='href';
    if (preg_match_all("/".$tag."=[\"|'](.*\.".$type.").*[\"|']/uiUm", $site,$matches)) {
      foreach ($matches[1] as $file) {
        $file=str_replace(array('<?=$assets?>','<?=$assets;?>'),$this->config->item('ASSETS'),$file);
        if (substr($file,0,4)!='http') $files[]=$file;
      }
    }
    switch($type) {
      case 'css':
        $found=array_search(el('dest_file',$this->settings,''),$files);
        if ($found!==false) {
          unset($files[$found]);
        }
        break;
      case 'js':
        $found=array_search(el('js_dest_file',$this->settings,''),$files);
        if ($found!==false) {
          unset($files[$found]);
        }
        break;
    }
    return $files;
  }
  
  
  public function report() {
    return $this->report;
  }
  
  public function errors() {
    return $this->errors;
  }
  
  
  private function execution_time($start=0) {
    if ($start==0) $start=$this->time_start;
    $time_end = microtime(true);
    return ($time_end - $start);
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

