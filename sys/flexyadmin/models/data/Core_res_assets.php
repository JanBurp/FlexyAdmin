<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Res_media_files - autogenerated Table_model for table res_assets
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Core_res_assets extends Data_Core {
  
  // Some methods use the global set path
  private $media_path = '';
  
  // (Error) message after uploading (error)
  private $error_message = '';
  private $message       = '';

  private $upload_data   = array(); 
  
  // Bestanden die na uploading extra zijn aangemaakt
  private $created_files = array();
  
  // Caching
  private $file_info      = array();
  private $find_in_fields = array();
  
  private $default_assets = array(
    'types'            => array('jpg','jpeg','gif','png','pdf','doc','docx','xls','xlsx'),
    'encrypt_name'     => FALSE,
    'media_fields'     => array(),
    'autofill'         => '',
    'autofill_fields'  => array(),
    'in_link_list'     => FALSE,
    'user_restricted'  => FALSE,
    'serve_restricted' => FALSE,
  );
  private $default_assets_sizes = array(
    'min_width'  => 0,
    'min_height' => 0,
    'resize_img' => 0,
    'img_width'  => 0,
    'img_height' => 0,
    'create_1'   => 0,
    'width_1'    => 0,
    'height_1'   => 0,
    'prefix_1'   => '_thumb_',
    'suffix_1'   => '',
    'create_2'   => 0,
    'width_2'    => 0,
    'height_2'   => 0,
    'prefix_2'   => '_small_',
    'suffix_2'   => '',
  );

  private $system_assets = array('_thumbcache/','img/','css/','fonts/','js/','less-default/','less-bootstrap/');
  
  
  public function __construct() {
    $this->load->model('log_activity');
    $this->lang->load('update_delete');
    // Load assets config
    $this->autoset['assets'] = array();
    $this->config->load('assets',true);
    $settings = $this->config->get_item('assets');
    $this->settings = $settings;
    parent::__construct('res_assets');
    $this->settings = array_merge($this->settings,$settings);
    // Add current filemanager_view setting:
    $user_id = $this->get_user_id();
    if ($user_id) {
      $this->settings['grid_set']['grid_view'] = $this->db->query( 'SELECT `str_filemanager_view` FROM `cfg_users` WHERE `id`='.$user_id )->row_object()->str_filemanager_view;
    }
  }
  
  /**
   * Autoset informatie over alle assets
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _autoset_assets() {
    $this->load->helper('directory');
    $paths = directory_map($this->config->item('ASSETSFOLDER'),1);
    $paths = array_diff($paths,$this->system_assets);
    $assets = array();
    foreach ($paths as $path) {
      $path = trim($path,'/');
      // Default
      $assets[$path] = $this->default_assets;
      if ($path==='pictures') {
        $assets[$path]['types'] = array_slice($assets[$path]['types'],0,4);
        $assets[$path] = array_merge($assets[$path],$this->default_assets_sizes);
      }
    }
    
    if (!defined('PHPUNIT_TEST')) {
      $this->load->model('data/data_create');
      $this->data_create->save_config( 'assets', $this->config->item('SYS').'flexyadmin/config/assets.php', $this->config->item('SITE').'config/assets.php', array('assets'=>$assets) );
    }
    return $assets;
  }

  /**
   * Haalt assets uit (oude) database tabellen
   *
   * @return void
   * @author Jan den Besten
   */
  public function _create_assets_settings($old=FALSE, $DB = NULL) {
    if ($DB===NULL) $DB = $this->db;
    $info = $DB->select('`path`,`str_types` AS `types`,`b_encrypt_name` AS `encrypt_name`,`fields_media_fields` AS `media_fields`,`str_autofill` AS `autofill`,`fields_autofill_fields` AS `autofill_fields`,`b_in_link_list` AS `in_link_list`,`b_user_restricted` AS `user_restricted`,`b_serve_restricted` AS `serve_restricted`')->get('cfg_media_info')->result_array();

    $assets = array();
    foreach ($info as $path_info) {
      $path = $path_info['path'];
      unset($path_info['path']);

      $img_info = $DB->select('`int_min_width` AS `min_width`,`int_min_height` AS `min_height`,`b_resize_img` AS `resize_img`,`int_img_width` AS `img_width`,`int_img_height` AS `img_height`,`b_create_1` AS `create_1`,`int_width_1` AS `width_1`,`int_height_1` AS `height_1`,`str_prefix_1` AS `prefix_1`,`str_suffix_1` AS `suffix_1`,`b_create_2` AS `create_2`,`int_width_2` AS `width_2`,`int_height_2` AS `height_2`,`str_prefix_2` AS `prefix_2`,`str_suffix_2` AS `suffix_2`')->where('path',$path)->get('cfg_img_info');
      if ($img_info) {
        $img_info = $img_info->row_array();
        if (is_array($img_info)) $path_info = array_merge($path_info,$img_info);
      }
      $assets[$path] = $path_info;
    }

    $this->load->model('data/data_create');
    $this->data_create->save_config( 'assets', $this->config->item('SYS').'flexyadmin/config/assets.php', $this->config->item('SITE').'config/assets.php', array('assets'=>$assets) );
  }
  
  /**
   * Stel de assets map in
   *
   * @param string $path 
   * @return $this
   * @author Jan den Besten
   */
  public function set_path($path) {
    $this->media_path = $path;
    return $this;
  }
  
  
  
  /**
   * Refresh de hele mediatabel
   *
   * @param string $paths default=''
   * @param bool $clean[FALSE] Als TRUE dan wordt tabel helemaal leeggehaald, anders wordt gekeken wat er al bestaat en daar de data van aangevuld.
   * @param bool $remove[FALSE] Als TRUE dan worden niet gebruikte bestanden verwijderd.
   * @return array $paths
   * @author Jan den Besten
   */
  public function refresh( $paths='', $clean=FALSE, $remove=FALSE) {
    if (empty($paths)) $paths = $this->get_assets_folders(FALSE);
    if (!is_array($paths)) $paths=array($paths);
    
    // Verwijder alles?
    if ($clean) $this->db->truncate( $this->settings['table'] );

    // Reset b_exists
    $this->where('b_exists',true);
    $this->set('b_exists',false);
    $this->update();

    $hasMetaInfo = $this->db->field_exists( 'meta', $this->settings['table'] );
    $hasUsedInfo = $this->db->field_exists( 'b_used', $this->settings['table'] );
    
    foreach ($paths as $key=>$path) {
      $assetsPath = add_assets($path);
      $files = read_map($assetsPath,'',FALSE,TRUE,$hasMetaInfo);
      $files = not_filter_by($files,'_');
      foreach ($files as $file) {
        $name = $file['name'];
        if (is_visible_file($name)) {
          $existingInfo = $this->get_file_info($path,$name);
          if ($existingInfo) $file = array_merge($file,$existingInfo);
          $file['path'] = $path;
          $file['file'] = str_replace($path,'',$name);
          $file['b_exists'] = true;
          $file['date'] = str_replace(' ','-',$file['rawdate']);
          if ($hasMetaInfo and isset($file['meta'])) $file['meta'] = json_encode($file['meta']);
          if ($hasUsedInfo) $file['b_used'] = $this->is_file_used($path,$name);
          if ($clean or !$existingInfo) {
            $this->insert_file($path,$name,$file);
          }
          else {
            $this->update_file($path,$name,$file);
          }
        }
      }
    }
    
    // Remove unused files
    if ( $remove and $hasUsedInfo ) {
      $this->delete( array('b_used'=>false) );
    }

    // Remove old thumbs of files that not exists anymore
    $this->delete( array('b_exists'=>false) );
    
    return $paths;
  }
  
  
  
  
  /**
   * Als rows worden verwijderd uit 'res_assets', verwijder dan ook de desbetreffende bestanden.
   *
   * @param mixed $where ['']
   * @param int $limit [NULL]
   * @param bool $reset_data 
   * @return mixed FALSE als niet gelukt, anders array_result van verwijderde data
   * @author Jan den Besten
   */
	public function delete( $where = '', $limit = NULL, $reset_data = TRUE ) {
    $deleted_data = parent::delete($where,$limit,$reset_data);
    return $this->delete_files( $deleted_data );
  }
  
  /**
   * Verwijderd fysieke bestanden van de rows die uit res_assets zijn verwijderd
   *
   * @param string $deleted_data 
   * @return mixed array van verwijderde bestanden
   * @author Jan den Besten
   */
  private function delete_files( $deleted_data ) {
    if (is_array($deleted_data)) {
      foreach ($deleted_data as $id => $file_row) {
        $path = $file_row['path'];
        $file = $file_row['file'];
        $this->delete_file($path,$file);
        unset($deleted_data[$id]);
      }
    }
    return $deleted_data;
  }
  
  /**
   * Verwijder fysiek bestand (en de diverse omvangen)
   *
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
  public function delete_file($path,$file) {
		$name = $this->config->item('ASSETSFOLDER').$path.'/'.$file;
    $result=true;
    // A folder
    if (is_dir($name)) {
      if (file_exists($name) and !defined('PHPUNIT_TEST')) {
        $result=rmdir($name);
      }
    }
    // A file
    else {
      $result=false;
      // Remove file
      if (file_exists($name) and !defined('PHPUNIT_TEST')) {
        @chmod($name,0777);
        $result=unlink($name);
      }
      else {
        $result=true;
      }
      // Remove other sizes
  		if ($result) {
        // Thumb
  			$thumb = $this->config->item('THUMBCACHE').pathencode( $path.'/'.$file );
  			if (file_exists($thumb) and !defined('PHPUNIT_TEST')) {
          @chmod($thumb,0777);
          unlink($thumb);
  			}
        // Check if other sizes exists and if they are hidden, delete them
  			$info = $this->get_setting(array('assets',$path));
  			$nr=1;
  			$files=array();
  			while (isset($info["prefix_$nr"])) {
  				if (!empty($info["prefix_$nr"])) $files[] = $info["prefix_$nr"].$file;
  				if (!empty($info["suffix_$nr"])) $files[] = get_file_without_extension($file).$info["suffix_$nr"].get_file_extension($file);
  				$nr++;
  			}
        // $files=filter_by($files,"_");
  			foreach( $files as $sizedFile ) {
          $sizedName = $this->config->item('ASSETSFOLDER').$path.'/'.$sizedFile;
          if (file_exists($sizedName) and !defined('PHPUNIT_TEST')) {
            @chmod($sizedName,0777);
            unlink($sizedName);
          }
  			}
			
        if (!defined('PHPUNIT_TEST')) $this->_remove_file_from_fields($path,$file);
      }      
    }

		if ($result) {
			log_("info","[FM] delete file/dir '$name'");
      $this->log_activity->media('deleted',$path,$name );
		}
		else {
			log_("info","[FM] ERROR deleting file/dir '$name'");
      $this->log_activity->media( 'ERROR could not delete',$path,$name );
		}
		return $result;
  }
  
  /**
   * Verwijder verwijzingen van bestand uit de database (in andere tabellen)
   *
   * @param array $fields 
   * @param string $path 
   * @param string $file 
   * @return void
   * @author Jan den Besten
   */
  private function _remove_file_from_fields($path,$file) {
    $this->load->model('search_replace');
    $this->search_replace->media($path,$file,'');
  }
  
  
  /**
   * Upload file van meegegeven (form) file-veld, ook worden meteen thumbs etc. aangemaakt voor geuploade bestanden en wordt de minimale omvang gecheckt.
   *
   * @param string $path 
   * @param string $file_field 
   * @param array $extra_config (zelfde als de config van de upload class) 
   * @return array $result
   * @author Jan den Besten
   */
	public function upload_file( $path, $file_field='file', $extra_config=array()) {
    $this->load->library('upload');

    // Initialize
    $this->error_message = '';
    $folder = $this->config->item('ASSETSFOLDER') . $path;
    $config = array(
      'upload_path'   => $folder,
      'allowed_types' => $this->get_setting(array('assets',$path,'types')),
      'encrypt_name'  => $this->get_setting(array('assets',$path,'encrypt_name')),
    );
    $config = array_merge($config,$extra_config);
    $path_settings = $this->get_folder_settings($path);

    // Start upload
		$this->upload->config($config);

    $this->log_activity->media( array2json($config).' '.array2json($_FILES), $path, 'start_upload' );
    if ( !$this->upload->upload_file( $file_field ) ) {
      $file = $this->upload->get_file();
			$this->error_message = $this->upload->get_error();
			log_("info","[FM] error while uploading: '$file' [$this->error_message]");
      $this->log_activity->media( $this->error_message, $path, 'UPLOAD ERROR '.$file );
      return false;
    }
    
		// Rename
    $this->upload_data = $this->upload->data();
    $file = $this->upload_data['file_name'];
    if (strtolower($file) != strtolower($this->upload_data['orig_name'])) {
      $this->log_activity->media( array2json($config), $path, 'upload renamed `'.$this->upload_data['orig_name'].'` => `'.$file.'`' );
      $this->message = langp('rename_succes',$this->upload_data['orig_name'],$file);
    }
		$ext = get_file_extension($file);
    $saveName = clean_file_name($file);
    if ($file!==$saveName) {
      if (rename($folder.'/'.$file, $folder.'/'.$saveName));
      $this->log_activity->media( array2json($config), $path, 'upload renamed `'.$file.'` => `'.$saveName.'`' );
    }
    $file = $saveName;
    $this->log_activity->media( array2json($config),$path, 'upload success '.$file );

    // Image? Check size
    if ( in_array(strtolower($ext),$this->config->item('FILE_types_img')) ) {
      
      // restore orientation
      $this->upload->restore_orientation($folder,$file);

      // check minimal size, if too small: delete and error
      if ( !$this->upload->check_size($path,$file,$path_settings) ) {
        $this->delete_file($path,$file);
        $this->error_message = langp('upload_img_too_small',$file, $path_settings['min_width'].' x '.$path_settings['min_height']);
        $this->log_activity->media( array2json($path_settings), $path, 'upload_img_too_small '.$file );
        return FALSE;
      }

      // Resize
      if ( !$this->upload->resize_image( $path,$file,$path_settings ) ) {
        $this->delete_file($path,$file);
        $this->error_message = langp('upload_resize_error',$file);
        $this->log_activity->media( array2json($path_settings),$path,'upload_resize_error '.$file );
        return FALSE;
      }

    }
    
    
    // Stop in database
    $this->insert_file($path,$file);

    // Auto fill
    // $this->upload->auto_fill_fields($file,$this->map);

    return $file;
	}

  /**
   * Resize image file.
   *
   * @param string $path 
   * @param string $file 
   * @return array $result
   * @author Jan den Besten
   */
  public function resize_file( $path, $file) {
    $this->load->library('upload');
    $path_settings = $this->get_folder_settings($path);
    return $this->upload->resize_image( $path,$file,$path_settings );
  }

  /**
   * Resize images in path
   *
   * @param string $path 
   * @param string $file 
   * @return array $result
   * @author Jan den Besten
   */
  public function resize_path( $path ) {
    ini_set('max_execution_time', 3600); // 60 minuten
    $this->load->library('upload');
    $path_settings = $this->get_folder_settings($path);
    $files = $this->get_files($path);
    foreach ($files as $file) {
      $this->upload->resize_image( $path,$file['file'],$path_settings );
    }
    return $path;
  }

  /**
   * Resize all images
   *
   * @param string $path 
   * @param string $file 
   * @return array $result
   * @author Jan den Besten
   */
  public function resize_all() {
    $paths = $this->get_setting('assets');
    foreach ($paths as $path => $settings) {
      if (isset($settings['resize_img'])) {
        $this->resize_path($path);
      }
      else {
        unset($paths[$path]);
      }
    }
    return array_keys($paths);
  }
  

  /**
   * Grid set aanpassen als er een map is ingesteld en er dus files worden opgevraagd (ip2longv ruwe data)
   * En res_assets acties
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_grid_set() {
    if ($this->media_path) {
      $grid_set = el('grid_set',$this->settings);
      $grid_set['fields'] = $this->settings['files']['thumb_select'];
      $field_info = $this->get_setting_field_info_extended($grid_set['fields'],array('path'=>$this->media_path));
      $grid_set['field_info'] = $field_info;
      $searchable_fields = array_combine($grid_set['fields'],$grid_set['fields']);
      $searchable_fields = array_unset_keys($searchable_fields,array('id','media_thumb'));
      $grid_set['searchable_fields'] = array_values($searchable_fields);
    }
    else {
      $grid_set=parent::get_setting_grid_set();

      // Assets Actions
      $grid_set['actions'] = array(
        array(
          'name'  => 'Refresh Assets',
          'icon'  => 'refresh',
          'url'   => 'assets_actions?action=refresh',
          'class' => 'text-warning',
        ),
        array(
          'name'  => 'Resize Images',
          'icon'  => 'arrows-alt',
          'url'   => 'assets_actions?action=resize',
          'class' => 'text-danger',
        ),
      );

    }



    return $grid_set;
  }
  

  /**
   * Geeft aantal bestanden in de map
   *
   * @param string $path ['']
   * @return int
   * @author Jan den Besten
   */
  public function count_all($table='') {
    $query = $this->db->where('path',$table)->select('id')->get($this->settings['table']);
    return $query->num_rows();
  }

  
  
  /**
   * Geeft foutmelding (als uploaden een fout geeft)
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_error() {
    return $this->error_message;
  }
  
  /**
   * Geeft eventuele melding (bijvoorbeeld nieuwe naam na uploaden)
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_message() {
    return $this->message;
  }


  
  /**
   * Geeft extra aangemaakt bestandsnamen terug na uploaden
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_created_files() {
    return $this->created_files;
  }

  
  /**
   * Geeft alle media mappen die bekend zijn
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_assets_folders( $include_assets=TRUE, $prefix='' ) {
    $assets = $this->get_setting('assets');
    $paths = array_keys($assets);
    foreach ($paths as $key => $path) {
      if ($include_assets) $paths[$key] = $this->config->item('ASSETSFOLDER').$path;
      if ($prefix) $paths[$key] = $prefix.$paths[$key];
    }
    return $paths;
  }
  
  /**
   * Geeft de diverse formaten ingesteld voor gegeven map
   *
   * @param mixed $path 
   * @return array
   * @author Jan den Besten
   */
  public function get_folder_settings($path) {
    if (!is_array($path)) $path=array($path);
    array_unshift($path,'assets');
    return $this->get_setting($path);
  }
  
  /**
   * Test of meegegeven folder bestaat als assets map
   *
   * @param string $folder 
   * @return bool
   * @author Jan den Besten
   */
  public function assets_folder_exists( $folder ) {
    $paths = $this->get_assets_folders(FALSE);
    return in_array($folder,$paths);
  }
  
  
  /**
   * Geeft informatie van een bestand
   *
   * @param string $path
   * @param string $file
   * @return array
   * @author Jan den Besten
   */
  public function get_file_info($path,$file) {
    $name = $path.'/'.$file;
    $info = el($name,$this->file_info,FALSE);
    if (!$info) {
      $info = $this->where('file',$file)->where('path',$path)->get_row();
      $this->file_info[$name] = $info;
    }
    if ($this->upload_data and $this->upload_data['file_name']==$file) {
      $info['orig_name'] = $this->upload_data['orig_name'];
    }
    return $info;
  }
  
  public function is_file_used($path,$file) {
    if (empty($this->find_in_fields)) {
      $tables = $this->list_tables();
      $tables = filter_by($tables,'tbl_');
      foreach ($tables as $table) {
        $fields = $this->db->list_fields( $table );
        foreach ($fields as $field) {
          if (in_array(get_prefix($field),array('txt','media','medias'))) {
            array_push($this->find_in_fields, $table.'.'.$field);
          }
        }
      }
    }
    $this->load->model('search_replace');
    $found = $this->search_replace->has_text($file,$this->find_in_fields);
    return $found;
  }
  

  /**
   * Voeg bestand met info toe aan database
   *
   * @param string $path
   * @param string $file
   * @param mixed $data 
   * @return bool TRUE als gelukt
   * @author Jan den Besten
   */
  public function insert_file($path,$file,$data=array()) {
    // Default data
    $this->load->helper('date');
    $name = $this->config->item('ASSETSFOLDER').$path.'/'.$file;
    $ext=strtolower(get_suffix($file,'.'));
    $file_stats = @stat($name);
    $default_data = array(
      'path'  => $path,
      'file'  => $file,
      'alt'   => get_prefix($file,'.'),
      'type'  => $ext,
      'size'  => (int) floor($file_stats['size'] / 1024),
      'date'  => unix_to_mysql($file_stats['mtime']),
    );
    // img sizes
    if ( in_array($ext,$this->config->item('FILE_types_img')) ) {
      $sizes = @getimagesize( $name );
      if ($sizes) {
        $default_data['width'] = $sizes[0];
        $default_data['height'] = $sizes[1];
      }
    }
    
    // Data
    $data = array_merge($default_data,$data);
    // Insert
    return $this->set($data)->insert();
  }
  
  /**
   * Edit info in database
   *
   * @param string $path
   * @param string $file
   * @param mixed $data 
   * @return bool TRUE als gelukt
   * @author Jan den Besten
   */
  public function update_file($path,$file,$data=array()) {
    return $this->where('file',$file)->where('path',$path)->set($data)->update();
  }
  
  
  /**
   * Geeft titel van image
   *
   * @param string $path 
   * @param string $file 
   * @return string
   * @author Jan den Besten
   */
  public function get_img_title($path,$file) {
    $info =  $this->get_file_info($path,$file);
    return ascii_to_entities($info['alt']);
  }
  
  /**
   * Geeft omvang van een afbeelding
   *
   * @param string $path 
   * @param string $file 
   * @return mixed
   * @author Jan den Besten
   */
  public function get_img_size($path,$file,$from_file=FALSE) {
    $size = FALSE;
    if (!$from_file) {
      $info = $this->get_file_info($path,$file);
      if (isset($info['width']) and isset($info['height'])) $size = array(
        0        => $info['width'],
        1        => $info['height'],
        'width'  => $info['width'],
        'height' => $info['height']
      );
    }
    else {
    	if (file_exists($path.'/'.$file) and is_file($path.'/'.$file)) {
    		$errorReporting=error_reporting(E_ALL);
    		error_reporting($errorReporting - E_WARNING - E_NOTICE);
    		$size = getimagesize($path.'/'.$file);
    		error_reporting($errorReporting);
      }
    }
  	return $size;
  }
  
  
  /**
   * Test of een afbeelding liggen of staand is
   *
   * @param string $path 
   * @param string $file 
   * @return string = 'landscape', 'portrait' of 'unknown'
   * @author Jan den Besten
   */
  public function portrait_or_landscape($path,$file) {
    $size=$this->get_img_size($path,$file);
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
    return $this->get_setting(array('assets',$path,'user_restricted'));
  }
  
  /**
   * Zijn bestanden in pad beveiligd?
   *
   * @param string $path 
   * @return bool
   * @author Jan den Besten
   * @internal
   */
  public function is_restricted($path) {
    return $this->get_setting(array('assets',$path,'serve_restricted'));
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
    if ($path='_tmp') return true;
    if ($path==='_thumbcache') {
      $path = get_prefix($file,'___');
    }
    // Mag het bestand zowiezo worden getoond?
    if ( !$this->is_restricted($path) ) return true;
    // Heeft de user zowiezo geen rechten voor deze map?
    $this->load->library('flexy_auth');
    if (!$this->flexy_auth->has_rights('media_'.$path)) return false;
    // Is de user niet gekoppeld aan dit bestand? Dan mag het.
    $info = $this->get_file_info($path,$file);
    if (!isset($info['user'])) return true;
    // Is het de goed user?
    if ( $this->flexy_auth->get_user(null,'id') == $info['user']) return true;
    return false;
  }
  
  
  
  

  /**
   * Geeft informatie terug van alle bestanden in een bepaalde map.
   * 
   * Eventueel gefilterd op:
   * - user   - alleen bestanden van een bepaalde gebruiker (als het user veld bestaat)
   * - type   - alleen bestanden bepaalde type(n) bestanden
   * - ...    - nog meer eigen filters: vergelijkbaar zoals je aan ->where() mee kunt geven
   * 
   * Het resultaat is wat anders dan een standaard database resultaat, de veldnamen wijken af en zijn als volgt:
   * 
   * - id               = id in de tabel res_assets
   * - file/media_thumb = bestandsnaam
   * - path             = map van bestand inclusief assets map
   * - full_path        = complete beveiligde pad van bestand, deze kun je het best gebruiken om een bestand te tonen of niet
   * - type             = type bestand
   * - alt              = titel
   * - size             = omvang in kb
   * - date             = datum
   * - width            = breedte in pixels, als het bestand een afbeelding is
   * - height           = hoogte in pixels, als het bestand een afbeelding is
   * [- user            = user id als dit veld bestaat]
   *
   * @param string $path Geef hier de directory waar je de bestanden van wilt.
   * @param array $filter[] zoals eerste argument van ->find()
   * @param int $limit [0]
   * @param int $offset [0] 
   * @param bool $with_thumb [FALSE] 
   * @return array
   * @author Jan den Besten
   */
  public function get_files( $path, $filter=array(), $limit=0, $offset=0, $with_thumb = FALSE ) {
    
    // Veldnamen
    if ($with_thumb) {
      $select = $this->get_setting(array('files','thumb_select'));
      $select = str_replace('media_thumb','`file` AS `media_thumb`',$select);
    }
    else {
      $select = $this->get_setting(array('files','select'));
    }
    $this->select( $select );

    if (isset($this->settings['extra_fields'])) {
      $this->select( el('extra_fields',$this->settings) );      
    }

    
    // Van bepaalde user?
    if ( $this->field_exists('user') ) {
      $this->select( 'user' );
    }
    else {
      if (is_array($filter)) unset($filter['user']);
    }
    
    return $this->_get_files_result($path,$filter,$limit,$offset);
  }
  
  /**
   * Geeft een abstract van de bestanden uit een bepaalde map.
   *
   * @param string $path 
   * @param string $filter 
   * @param string $limit 
   * @param string $offset 
   * @return array
   * @author Jan den Besten
   */
  public function get_files_abstract( $path,$filter=array(),$limit=0,$offset=0) {
    // Veldnamen
    $this->select('file')->select_abstract()->set_result_key('file');
    return $this->_get_files_result($path,$filter,$limit,$offset);
  }
  
  
  /**
   * Geeft bestanden als opties terug. Eventueel gegroupeerd.
   *
   * @param string $path 
   * @param array $filter 
   * @param int $limit 
   * @param int $offset 
   * @return array
   * @author Jan den Besten
   */
  public function get_files_as_options( $path,$filter=array(), $limit=0, $offset=0 ) {
    $options = array();
    $order = $this->get_setting('order_by');
    $this->order_by($order);
    $this->select('file')->select_abstract();
    $query = $this->_get_files($path,$filter,$limit,$offset);
    if ($query) {
      $options = $this->_make_options_result($query,'file');
      $query->free_result();
    }
    return $options;
  }


  /**
   * Basis voor get_files... methods
   *
   * @param string $path 
   * @param array $filter 
   * @param int $limit 
   * @param int $offset 
   * @return object $query
   * @author Jan den Besten
   */
  private function _get_files( $path, $filter=array(), $limit=0, $offset=0 ) {
    // Alleen de bestanden die bestaan
    $this->where( 'b_exists', TRUE );
    // Bestanden van bepaalde map
    $this->where( 'path', $path );
    // Standaard filters
    if ($filter) {
      if (!is_array($filter)) {
        $this->find($filter,array(),array('and'=>'AND'));
      }
      else {
        // find array of mulitple where's?
        $first = current($filter);
        if ( isset($first['field']) ) {
          $this->find( $filter, array(),array('and'=>'AND') );
        }
        else {
          foreach ($filter as $field=>$value) {
            $this->where( $field, $value);
          }
        }
      }
    }
    $query = $this->get( $limit, $offset, FALSE );
    return $query;
  }
  
  
  /**
   * Basis voor get_files_result... methods
   *
   * @param string $path 
   * @param string $filter 
   * @param string $limit 
   * @param string $offset 
   * @return array
   * @author Jan den Besten
   */
  private function _get_files_result( $path, $filter=array(), $limit=0, $offset=0 ) {
    $query = $this->_get_files( $path, $filter, $limit, $offset );
    if ($query) {
      $result = $this->_make_result_array( $query );
      $query->free_result();
    }
    $this->reset();
    return $result;
  }
  
}
