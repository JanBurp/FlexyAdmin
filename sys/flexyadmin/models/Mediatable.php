<?php 
/** \ingroup models
 * Met dit model kunnen bestanden worden toegevoegd of verwijderd aan de media tabel (res_media_files)
 *
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */
 
class Mediatable extends CI_Model {
  
  /**
   * Media table
   */
  private $table='res_media_files';
  
  /**
   * Hier wordt bijgehouden of de mediatabel wel bestaat
   */
  private $has_table=FALSE;
  
  /**
   * cache van reeds opgevraagde info uit tabel
   */
  private $info=array();
  

  /**
   */
	public function __construct() {
		parent::__construct();
    $this->has_table=$this->db->table_exists($this->table);
    if ($this->has_table and $this->db->field_exists('b_used',$this->table)) $this->load->model('search_replace');
	}


  /**
   * Is er wel een media tabel?
   *
   * @return boolean TRUE als er een tabel is
   * @author Jan den Besten
   */
  public function exists() {
    return $this->has_table;
  }
  
  /**
   * Geeft alle media mappen die bekend zijn
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_media_folders() {
    $this->db->select('path');
    $result=$this->db->get_result('cfg_media_info');
    $folders=array();
    foreach ($result as $key => $info) {
      $folders[]='site/assets/'.$info['path'];
    }
    return $folders;
  }
  

  /**
   * Test of bestand in media tabel bestaat
   *
   * @param string $file 
   * @param string $path=''
   * @return bool
   * @author Jan den Besten
   */
  public function exists_in_table($file,$path='') {
    $path=str_replace($this->config->item('ASSETS'),'',$path);
    $file=get_suffix($file,'/');
    $this->db->where('path',$path)->where('file',$file);
    $row=$this->db->get_result($this->table);
    return (!empty($row));
  }

  /**
   * Test of een bestand ergens wordt gebruikt
   *
   * @param string $file 
   * @param string $path=''
   * @return bool
   * @author Jan den Besten
   */
  public function is_file_used($file,$path='') {
    $path=str_replace($this->config->item('ASSETS'),'',$path);
    $file=get_suffix($file,'/');
		$cfg=$this->cfg->get('CFG_media_info',$path);
    $found=false;
    if (!empty($cfg['fields_check_if_used_in'])) $found=$this->search_replace->has_text($file,$cfg['fields_check_if_used_in']);
    return $found;
  }


  /**
   * Voeg file toe aan mediatabel
   *
   * @param mixed $file een array met alle file info, of de naam van de file 
   * @param string $path[''] als geen info is meegegeven, moet hier het complete path meegegeven worden 
   * @param int $userId default=FALSE if restricted to users, give the user id here.
   * @return mixed $id id van bestand in de database (of FALSE bij een error of een directory)
   * @author Jan den Besten
   */
  public function add($file,$path='',$userId=FALSE) {
    if (!is_array($file)) $file=get_full_file_info($path.'/'.$file,TRUE,TRUE);
    if ($file['type']!='dir') {
      $set=array(
        'file'      => $file['name'],
        'path'      => remove_assets(remove_suffix($file['path'],'/')),
        'str_type'  => $file['type'],
        'dat_date'  => str_replace(' ','-',$file['rawdate']),
        'int_size'  => $file['size'],
        'str_title' => $file['alt']
      );
      if (isset($file['b_exists'])) {
        $set['b_exists'] = $file['b_exists'];
      }
      if (isset($file['b_used'])) {
        $set['b_used']  = $file['b_used'];
      }
      if (isset($file['width'])) {
        $set['int_img_width']   = $file['width'];
        $set['int_img_height']  = $file['height'];
      }
      if (isset($file['meta']) and $this->db->field_exists('stx_meta',$this->table)) $set['stx_meta']=exif2string($file['meta']);
      if ($userId and $this->db->field_exists('user',$this->table)) $set['user']=$userId;
      $this->db->set($set);
      $this->db->insert($this->table);
      return $this->db->insert_id();
    }
    return false;
  }
  

  /**
   * Voeg info toe aan bestand in de tabel
   *
   * @param string $info Complte info van de file
   * @param int $userId default=FALSE if restricted to users, give the user id here.
   * @return object $this
   * @author Jan den Besten
   */
  public function add_info($info,$userId=FALSE) {
    $set=array(
      'file'      => $info['name'],
      'path'      => remove_assets(remove_suffix($info['path'],'/')),
      'str_type'  => $info['type'],
      'int_size'  => $info['size'],
      'str_title' => $info['alt']
    );
    if (isset($info['rawdate'])) {
      $set['dat_date'] = str_replace(' ','-',$info['rawdate']);
    }
    if (isset($info['b_exists'])) {
      $set['b_exists']   = $info['b_exists'];
    }
    if (isset($info['b_used'])) {
      $set['b_used']  = $info['b_used'];
    }
    if (isset($info['width'])) {
      $set['int_img_width']   = $info['width'];
      $set['int_img_height']  = $info['height'];
    }
    if (isset($info['meta'])) {
      $set['stx_meta']=$info['meta'];
      if (is_array($set['stx_meta'])) {
        $set['stx_meta']=array2json($set['stx_meta']);
      }
    }
    
    // Preserve title
    $oldTitle=$this->db->get_field_where($this->table,'str_title','file',$set['file']);
    if (!empty($oldTitle)) unset($set['str_title']);
      
    if ($userId and $this->db->field_exists('user',$this->table)) $set['user']=$userId;

    // Update!
    foreach ($set as $field => $value) {
      if (!$this->db->field_exists($field,$this->table)) unset($set[$field]);
    }
    if ($set) {
      $this->db->set($set);
      $this->db->where('file',$set['file'])->where('path',$set['path']);
      $this->db->update($this->table);
    }
    return $this;
  }
  
  
  
  
  /**
   * Verwijder bestand uit mediatabel
   *
   * @param string $file 
   * @param string $path 
   * @return bool
   * @author Jan den Besten
   */
  public function delete($file,$path='') {
    $file=remove_assets($file);
    if (empty($path)) {
      $path=remove_suffix($file,'/');
      $file=get_suffix($file,'/');
    }
    $this->db->where('file',$file)->where('path',$path);
    $this->db->delete($this->table);
    return ($this->db->affected_rows()>0);
  }
  
  
  /**
   * Refresh de hele mediatabel
   *
   * @param string $paths default=''
   * @param bool $clean default=TRUE Als TRUE dan wordt tabel helemaal leeggehaald, anders wordt gekeken wat er al bestaat en daar de data van aangevuld
   * @param bool $remove  default=FALSE
   * @return array $paths
   * @author Jan den Besten
   */
  public function refresh($paths='',$clean=TRUE, $remove=FALSE) {
    if (empty($paths)) {
      $paths=$this->cfg->get('cfg_media_info');
      $paths=array_keys($paths);
    }
    if (!is_array($paths)) $paths=array($paths);

    if ($clean) {
      $this->db->truncate($this->table);
    }
    else {
      $this->db->set('b_exists',false);
      $this->db->update($this->table);
    }
    
    foreach ($paths as $key=>$path) {
      $info=$this->cfg->get('cfg_media_info',$path);
      $path=add_assets($path);
      $paths[$key]=$path;
      $files=read_map($path,'',FALSE,TRUE,$this->db->field_exists('stx_meta',$this->table)); // Get header info for jpg
      $files=not_filter_by($files,'_'); // remove hidden files
      foreach ($files as $file => $info) {
        if (is_visible_file($file)) {
          if ($this->db->field_exists('b_used',$this->table)) $info['b_used']=$this->is_file_used($file,$path);
          $info['b_exists']=true;
          if ($clean or !$this->exists_in_table($file,$path)) {
            $this->add($info);
          }
          else {
            $this->add_info($info);
          }
        }
      }
    }
    
    // Remove unused files?
    if ($remove) {
      $this->load->model('file_manager');
      $this->db->where('b_used',false);
      $not_used=$this->db->get_result('res_media_files');
      foreach ($not_used as $id => $row) {
        $path=$row['path'];
        $file=$row['file'];
        // remove file
        $this->file_manager->set_path($path);
        $result=$this->file_manager->delete_file($file);
        // remove from db
        $this->delete($file,$path);
      }
    }
    
    return $paths;
  }
  
  /**
   * Geeft alle bestanden en info in een map terug als een array
   *
   * @param string $path  default=''
   * @param bool $asReadMap default=TRUE als TRUE dan wordt het resultaat nog wat opgeleukt zodat het hetzelfde is als een read_map() resultaat
   * @param bool $full_path default=TRUE
   * @return array $files
   * @author Jan den Besten
   */
  public function get_files($path='',$asReadMap=TRUE,$full_path=TRUE) {
    return $this->_get_files($path,$asReadMap,$full_path);
  }

  /**
   * Geeft alle recente bestanden en info in een map terug als een array
   *
   * @param string $path  default=''
   * @param int $nr default=10 aantal
   * @param bool $asReadMap default=TRUE als TRUE dan wordt het resultaat nog wat opgeleukt zodat het hetzelfde is als een read_map() resultaat
   * @return array $files
   * @author Jan den Besten
   */
  public function get_recent_files($path='',$nr=10, $asReadMap=TRUE) {
    return $this->_get_files($path,$asReadMap,TRUE,$nr);
  }
    
  private function _get_files($map='',$asReadMap=TRUE,$full_path=TRUE,$recent_numbers=0) {
    $path=remove_assets($map);
    $info=$this->cfg->get('cfg_media_info',$path);
    // if ($asReadMap) $this->db->set_key('file');
    
    // select fields
    $fields=$this->db->list_fields($this->table);
    unset($fields[array_search('b_exists',$fields)]);
    if ($full_path) array_splice($fields,array_search('path',$fields),0, 'CONCAT("_media/",`path`,"/",`file`) AS `full_path`' );
    $this->db->select($fields);
    // where exists and set path
    $this->db->where('b_exists',true);
    $this->db->where('path',$path);
    // user restricted where
    if (el('b_user_restricted',$info,false) and $this->db->field_exists('user',$this->table) and !$this->user->rights['b_all_users']) {
      $this->db->where('user',$this->user->user_id);
    }
    // get files
    $files=$this->db->get_result($this->table,$recent_numbers);
    
    if (empty($files)) {
      // not in database, read from filesystem if set so
      $files=read_map($map,$info['str_types'],FALSE,TRUE,FALSE,FALSE);
      $asReadMap=false;
    }
    if ($asReadMap) {
      $map_files=array();
      foreach ($files as $file => $info) {
        $date=explode('-',$info['dat_date']);
        $map_files[$file]=array(
          'name'  => $info['file'],
          'path'  => add_assets($info['path']),
          'type'  => $info['str_type'],
          'alt'   => $info['str_title'],
          'size'  => $info['int_size'],
          'rawdate' => str_replace('-',' ',$info['dat_date']),
          'date'    => date('j M Y',mktime(0,0,0,$date[1],$date[2],$date[0])),
          'width'   => $info['int_img_width'],
          'height'  => $info['int_img_height']
        );
        if (isset($info['user'])) {
          $map_files[$file]['id_user']=$this->db->get_field_where('cfg_users','str_username','id',$info['user']);
        }
        // extra fields
        $extra=array_unset_keys($info,array('id','b_exists','path','file','str_type','int_size','dat_date','int_img_height','int_img_width','str_title'));
        if ($extra) $map_files[$file]=array_merge($map_files[$file],$extra);
      }
      $files=$map_files;
    }
    return $files;
  }


  /**
   * Edit info in database
   *
   * @param string $file
   * @param mixed $item
   * @param mixed $data 
   * @return bool TRUE als gelukt
   * @author Jan den Besten
   */
  public function edit_info($file,$item,$data='') {
    if ($this->db->table_exists($this->table)) {
      $name=get_suffix($file,'/');
      $path=remove_assets(remove_suffix($file,'/'));
      if (!is_array($item))
        $data=array($item=>$data);
      else
        $data=$item;
      $this->db->where('file',$name)->where('path',$path)->set($data)->update($this->table);
      return ($this->db->affected_rows()>0);
    }
    return false;
  }


  /**
   * Geeft alle info van een afbeelding uit de tabel
   * 
   * Als er meta/exif data bekend is van een afbeelding, dan komt dat terug in het veld stx_meta als een JSON, met json2array() is dat eenvoudig om te zetten naar een PHP array
   *
   * @param string $file 
   * @return array
   * @author Jan den Besten
   */
  public function get_info($file) {
    if (!isset($this->info[$file])) {
      $name=get_suffix($file,'/');
      if ($this->db->table_exists($this->table)) {
        $path=remove_assets(remove_suffix($file,'/'));
        $this->info[$file]=$this->db->where('file',$name)->where('path',$path)->get_row($this->table);
      }
      else $this->info[$file]=FALSE;
    }
    return $this->info[$file];
  }
  

  /**
   * Haalt title van image op uit tabel of maakt die zelf
   *
   * @param string $file 
   * @return string
   * @author Jan den Besten
   */
  public function get_img_title($file) {
    $info=$this->get_info($file);
    if ($info) {
      $title=$info['str_title'];
    }
    else {
      $title=get_suffix($file,'/');
      $title=remove_suffix($title,'.');
      $title=preg_replace("/_\\d{8,}/usm", "", $title); // remove timestamp
      $title=nice_string($title);
    }
    $title=ascii_to_entities($title);
    return $title;
  }
  
  /**
   * Geeft omvang van een afbeelding
   *
   * @param string $file 
   * @return mixed
   * @author Jan den Besten
   */
  public function get_img_size($file) {
    $info=$this->get_info($file);
    if (isset($info['int_img_width']) and isset($info['int_img_height'])) return array(
      0        => $info['int_img_width'],
      1        => $info['int_img_height'],
      'width'  => $info['int_img_width'],
      'height' => $info['int_img_height']
    );
    // not in mediatable, try on file:
  	$size=FALSE;
  	if (file_exists($file) and is_file($file)) {
  		$errorReporting=error_reporting(E_ALL);
  		error_reporting($errorReporting - E_WARNING - E_NOTICE);
  		$size=getimagesize($i);
  		error_reporting($errorReporting);
    }
  	return $size;
  }

  /**
   * Test of een afbeelding liggen of staand is
   *
   * @param string $file afbeelding
   * @return string = 'landscape', 'portrait' of 'unknown'
   * @author Jan den Besten
   */
  public function portrait_or_landscape($file) {
    $file=$this->_file($file);
    $size=$this->get_img_size($file);
  	if ($size) {
  		if ($size['width']>$size['height'])
  			return 'landscape';
  		else
  			return 'portrait';
  	}
  	return 'unknown';
  }
  
  
  /**
   * Zijn bestanden in pad gekoppeld aan een user?
   *
   * @param string $path 
   * @return bool
   * @author Jan den Besten
   * @internal
   */
  public function is_user_restricted($path) {
    return $this->cfg->get('cfg_media_info',$path,'b_user_restricted');
  }
  
  
  /**
   * Geeft array resultaat met bestanden die toebehoren aan user
   *
   * @param int $user 
   * @return array
   * @author Jan den Besten
   * @internal
   */
	public function get_unrestricted_files($user) {
    if ($this->db->field_exists('user',$this->table)) $this->db->where('user',$user);
		$files=$this->db->get_result($this->table);
    $unrestrictedFiles=array();
    foreach ($files as $file) {
      $unrestrictedFiles[$file['path'].'/'.$file['file']]=$file;
    }
    return $unrestrictedFiles;
	}
  
  /**
   * filters bestandsarray zo dat alleen files terugkomen van meegegevenuser
   *
   * @param array $files 
   * @param int $user 
   * @return array
   * @author Jan den Besten
   * @internal
   */
	public function filter_restricted_files($files,$user) {
		if ($this->exists()) {
			if ($user) {
				$unrestrictedFiles=$this->get_unrestricted_files($user);
				$unrestrictedFiles=array_keys($unrestrictedFiles);
				$assetsPath=assets();
				foreach ($files as $name => $file) {
					$file=str_replace($assetsPath,"",$file['path']);
					if (!in_array($file,$unrestrictedFiles)) unset($files[$name]);
				}
			}
		}
		return $files;
	}
  
  /**
   * Checkt of een bestand rechten heeft om getoond te mogen worden
   *
   * @param string $path 
   * @param string $file 
   * @return bool
   * @author Jan den Besten
   */
  public function has_serve_rights($path,$file) {
    $map=get_suffix($path,'/');
    $serve_restricted=$this->cfg->get('cfg_media_info',$map,'b_serve_restricted');
    // Alleen verder testen als deze map restricted is, anders gewoon true
    if (!$serve_restricted) return true;
    // Heeft de user zowiezo geen rechten voor deze map: false
    $this->load->library('user');
    if (!$this->user->has_rights($map)) return false;
    // Is de user gekoppeld aan dit bestand?
    $info=$this->get_info($map.'/'.$file);
    if (!isset($info['user'])) return true;
    if ($this->user->user_id == $info['user']) return true;
    return false;
  }
  
  /**
   * Geeft echte bestandsnaam terug als er met '_media' wordt gewerk bijvoorbeeld.
   *
   * @param string $file 
   * @return string $file
   * @author Jan den Besten
   */
  private function _file($file) {
    $file = str_replace('_media/','site/assets/',$file);
    return $file;
  }

}

?>
