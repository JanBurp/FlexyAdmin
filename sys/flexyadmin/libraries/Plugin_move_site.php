<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @internal
 */


class Plugin_move_site extends Plugin {

  var $old = '';
  var $new = '';

	public function __construct() {
		parent::__construct();
	}

  public function _admin_api($args=false) {
		$this->add_message('<h2>Move Old site (essentials) to Fresh Checkout</h2>');
    
    $this->old=$this->config['old'];
    $this->new=$this->config['new'];
    
    // Check
    if (empty($this->old)) {
       $this->old='<span class="error">-- please fill new path in `config/plugin_move_site.php` --</span>'; 
    }
    else {
      if (!file_exists($this->old)) $this->old='<span class="error">-- `'.$this->old.'` seems not to exist. --</span>';
      if (!file_exists($this->new)) $this->new='<span class="error">-- `'.$this->new.'` seems not to exist. --</span>';
    }
    
    $this->add_message('<pre><strong>Old site: </strong> '.$this->old.'</pre>');
    $this->add_message('<pre><strong>New site: </strong> '.$this->new.'</pre>');

    // Actions
    $this->empty_paths();
    $this->move();
    $this->merge();
	
  	return $this->view('admin/plugins/plugin');
  }
  
  
  /**
   * Empty paths
   *
   * @return void
   * @author Jan den Besten
   */
  private function empty_paths() {
    $paths=$this->config['empty'];
    $ul=array();
    foreach ($paths as $path) {
      $map=$this->new.$path;
      empty_map($map);
      $ul[]=str_replace($this->new,'',$map);
    }
    $this->add_message('<h3>Paths that are emptied:</h3>');
    $this->add_message(ul($ul));
  }

  
  /**
   * Move paths & files (without check)
   *
   * @return void
   * @author Jan den Besten
   */
  private function move() {
    $paths=$this->config['move'];
    $moved=array();
    $error=array();
    foreach ($paths as $path) {
      $old=$this->old.$path;
      $new=$this->new.$path;

      // Collect files
      $move_files=array();
      if (is_dir($old)) {
        // Files in (sub)folder
        $files=read_map($old,'',TRUE,FALSE,FALSE,FALSE);
        foreach ($files as $file) {
          if (is_file($file['path'])) {
            $full_name=str_replace($old,'',$file['path']);
            $move_files[$old.$full_name] = $new.$full_name;
          }
        }
      }
      else {
        // File
        $move_files[$old]=$new;
      }
      
      // Move them
      foreach ($move_files as $from => $to) {
        $li=str_replace($this->new,'',$to);
        $dir=remove_suffix($to,'/');
        if (!file_exists($dir)) mkdir($dir,0777,true);
        if (file_exists($from) and copy($from,$to)) {
          $moved[]=$li;
        }
        else {
          $error[]='<span class="error">'.$li.'</span>';
        }
      }
    }

    $this->add_message('<h3>Files that are moved:</h3>');
    $this->add_message('<h4>Moved</h4>');
    $this->add_message(ul($moved));
    $this->add_message('<h4>Errors</h4>');
    $this->add_message(ul($error));
  }
  
  
  /**
   * Merge paths & files (keep newest)
   *
   * @return void
   * @author Jan den Besten
   */
  private function merge() {
    $paths=$this->config['merge'];
    $kept=array();
    $copied=array();
    $replaced=array();
    $error=array();
    foreach ($paths as $path) {
      $old=$this->old.$path;
      $new=$this->new.$path;

      // Collect files
      $move_files=array();
      if (is_dir($old)) {
        // Files in (sub)folder
        $files=read_map($old,'',TRUE,FALSE,FALSE,FALSE);
        foreach ($files as $file) {
          if (is_file($file['path'])) {
            $full_name=str_replace($old,'',$file['path']);
            $move_files[$old.$full_name] = $new.$full_name;
          }
        }
      }
      else {
        // File
        $move_files[$old]=$new;
      }
      
      // Merge them
      foreach ($move_files as $from => $to) {
        $li=str_replace($this->new,'',$to);
        if (file_exists($from)) {
          if (!file_exists($to)) {
            $dir=remove_suffix($to,'/');
            if (!file_exists($dir)) mkdir($dir,0777,true);
            if (copy($from,$to)) {
              $copied[]=$li;
            }
            else {
              $error[]='<span class="error">'.$li.'</span>';
            }
          }
          else {
            // which one is newest?
            $from_time = filemtime($from);
            $to_time = filemtime($to);
            if ($from_time>$to_time) {
              $dir=remove_suffix($to,'/');
              if (!file_exists($dir)) mkdir($dir,0777,true);
              if (copy($from,$to)) {
                $replaced[]=$li;
              }
              else {
                $error[]='<span class="error">'.$li.'</span>';
              }
            }
            else {
              $kept[]=$li;
            }
          }
        }
        else {
          $error[]='<span class="error">'.$li.'</span>';
        }
      }
    }

    $this->add_message('<h3>Files that are merged:</h3>');
    $this->add_message('<h4>Copied</h4>');
    $this->add_message(ul($copied));
    $this->add_message('<h4>Replaced</h4>');
    $this->add_message(ul($replaced));
    $this->add_message('<h4>Kept</h4>');
    $this->add_message(ul($kept));
    $this->add_message('<h4>Errors</h4>');
    $this->add_message(ul($error));
  }
  
  
  
  
}

?>