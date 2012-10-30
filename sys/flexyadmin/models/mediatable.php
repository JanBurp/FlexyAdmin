<?

/**
 * Met dit model kunnen bestanden worden toegevoegd of verwijderd aan de media tabel (res_media_files)
 *
 * @package default
 * @author Jan den Besten
 */
 
class Mediatable Extends CI_Model {
  
  /**
   * Media table
   *
   * @var string
   * @ignore
   */
  private $table='res_media_files';
  
  /**
   * Hier wordt bijgehouden of de mediatabel wel bestaat
   *
   * @var boolean
   * @ignore
   */
  private $has_table=FALSE;
  
  /**
   * cache van reeds opgevraagde info uit tabel
   *
   * @var string
   * @ignore
   */
  private $info=array();
  

  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
    $this->has_table=$this->db->table_exists($this->table);
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
   * Voeg file toe aan mediatabel
   *
   * @param mixed $file een array met alle file info, of de naam van de file 
   * @param string $path[''] als geen info is meegegeven, moet hier het complete path meegegeven worden 
   * @param int $userId[FALSE] if restricted to users, give the user id here.
   * @return int $id id van bestand in de database
   * @author Jan den Besten
   */
  public function add($file,$path='',$userId=FALSE) {
    if (!is_array($file)) $file=get_file_info($path.'/'.$file);
    $set=array(
      'file'      => $file['name'],
      'path'      => remove_assets(remove_suffix($file['path'],'/')),
      'str_type'  => $file['type'],
      'dat_date'  => str_replace(' ','-',$file['rawdate']),
      'int_size'  => $file['size'],
      'str_title' => $file['alt']
    );
    if (isset($file['width'])) {
      $set['int_img_width']   = $file['width'];
      $set['int_img_height']  = $file['height'];
    }
    if ($userId and $this->db->field_exists('user',$this->table)) $set['user']=$userId;
    $this->db->set($set);
    $this->db->insert($this->table);
    return $this->db->insert_id();
  }
  
  
  
  /**
   * Verwijder bestand uit mediatabel
   *
   * @param string $file 
   * @param string $path 
   * @return object $this
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
    return $this;
  }
  
  /**
   * Refresh de hele mediatabel
   *
   * @param string $paths['']
   * @return array $paths
   * @author Jan den Besten
   */
  public function refresh($paths='') {
    if (empty($paths)) {
      $paths=$this->cfg->get('cfg_media_info');
      $paths=array_keys($paths);
    }
    if (!is_array($paths)) $paths=array($paths);

    $this->db->truncate($this->table);
    foreach ($paths as $key=>$path) {
      $path=add_assets($path);
      $paths[$key]=$path;
      $files=read_map($path);
      foreach ($files as $file => $info) {
        if (is_visible_file($file)) $this->add($info);
      }
    }
    return $paths;
  }
  

  /**
   * Geeft alle bestanden en info in een map terug als een array
   *
   * @param string $path 
   * @param bool $asReadMap[TRUE] als TRUE dan wordt het resultaat nog wat opgeleukt zodat het hetzelfde is als een read_map() resultaat
   * @return array $files
   * @author Jan den Besten
   */
  public function get_files($path='',$asReadMap=TRUE) {
    $path=remove_assets($path);
    if ($asReadMap) $this->db->set_key('file');
    $files=$this->db->where('path',$path)->get_result($this->table);
    if ($asReadMap) {
      foreach ($files as $file => $info) {
        unset($files[$file]['id']);
        $files[$file]['name']=$info['file'];      unset($files[$file]['file']);
        $files[$file]['path']=add_assets($info['path']);
        $files[$file]['type']=$info['str_type'];  unset($files[$file]['str_type']);
        $files[$file]['alt']=$info['str_title'];
        $files[$file]['size']=$info['int_size']. 'k';  unset($files[$file]['int_size']);
        $files[$file]['rawdate']=str_replace('-',' ',$info['dat_date']);
        $date=explode('-',$info['dat_date']);
        $files[$file]['date']=date('j M Y',mktime(0,0,0,$date[1],$date[2],$date[0]));
        unset($files[$file]['dat_date']);
        $files[$file]['width']=$info['int_img_width'];  unset($files[$file]['int_img_width']);
        $files[$file]['height']=$info['int_img_height'];unset($files[$file]['int_img_height']);
      }
    }
    return $files;
  }


  /**
   * Edit info in database
   *
   * @param string $file
   * @param string $item
   * @param string $data 
   * @return bool TRUE als gelukt
   * @author Jan den Besten
   */
  public function edit_info($file,$item,$data) {
    if ($this->db->table_exists($this->table)) {
      $name=get_suffix($file,'/');
      $path=remove_assets(remove_suffix($file,'/'));
      $this->db->where('file',$name)->where('path',$path)->set($item,$data)->update($this->table);
      return true;
    }
    return false;
  }


  /**
   * Geeft alle info van een afbeelding uit de tabel
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
    if ($info)
      $title=$info['str_title'];
    else
      $title=remove_suffix(get_suffix($file,'/'),'.');
    return $title;
  }
  
  
  /**
   * Zijn bestanden in pad gekoppeld aan een user?
   *
   * @param string $path 
   * @return bool
   * @author Jan den Besten
   * @internal
   * @ignore
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
   * @ignore
   */
	public function get_unrestricted_files($user) {
    if ($this->db->field_exists('user',$this->table)) $this->db->where('user',$user);
		$this->db->set_key('file'); 
		return $this->db->get_result($this->table);
	}
  
  /**
   * filters bestandsarray zo dat alleen files terugkomen van user
   *
   * @param array $files 
   * @param int $user 
   * @return array
   * @author Jan den Besten
   * @internal
   * @ignore
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

}

?>