<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Res_media_files - autogenerated Table_model for table res_assets
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Res_assets extends Data_Core {
  
  // Some methods use the global set path
  private $media_path = '';
  
  // Error message if uploading has error
  private $error_message = '';
  
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
    $this->lang->load('update_delete');
    // Load assets config
    $this->autoset['assets'] = array();
    $this->config->load('assets',true);
    $this->settings = $this->config->get_item('assets');
    parent::__construct('res_assets');
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
      // Depricated: uit cfg_media_info & cfg_img_info
      // $path_info = $this->cfg->get( 'cfg_media_info', $path );
      // if ($path_info) {
      //   $assets[$path]['types']            = el('str_types',$path_info, $assets[$path]['types']);
      //   $assets[$path]['encrypt_name']     = el('b_encrypt_name',$path_info, $assets[$path]['encrypt_name']);
      //   $assets[$path]['media_fields']     = el('fields_media_fields',$path_info, $assets[$path]['media_fields']);
      //   $assets[$path]['autofill']         = el('str_autofill',$path_info, $assets[$path]['autofill']);
      //   $assets[$path]['autofill_fields']  = el('fields_autofill_fields',$path_info, $assets[$path]['autofill_fields']);
      //   $assets[$path]['in_link_list']     = el('b_in_link_list',$path_info, $assets[$path]['in_link_list']);
      //   $assets[$path]['user_restricted']  = el('b_user_restricted',$path_info, $assets[$path]['user_restricted']);
      //   $assets[$path]['serve_restricted'] = el('b_serve_restricted',$path_info, $assets[$path]['serve_restricted']);
      // }
      // $img_info = $this->cfg->get( 'cfg_img_info', $path );
      // if ($img_info) {
      //   $assets[$path]['min_width']  = el('int_min_width',$img_info, $assets[$path]['min_width']);
      //   $assets[$path]['min_height'] = el('int_min_height',$img_info, $assets[$path]['min_height']);
      //   $assets[$path]['resize_img'] = el('b_resize_img',$img_info, $assets[$path]['resize_img']);
      //   $assets[$path]['img_width']  = el('int_img_width',$img_info, $assets[$path]['img_width']);
      //   $assets[$path]['img_height'] = el('int_img_height',$img_info, $assets[$path]['img_height']);
      //   $assets[$path]['create_1']   = el('b_create_1',$img_info, $assets[$path]['create_1']);
      //   $assets[$path]['width_1']    = el('int_width_1',$img_info, $assets[$path]['width_1']);
      //   $assets[$path]['height_1']   = el('int_height_1',$img_info, $assets[$path]['height_1']);
      //   $assets[$path]['prefix_1']   = el('str_prefix_1',$img_info, $assets[$path]['prefix_1']);
      //   $assets[$path]['suffix_1']   = el('str_suffix_1',$img_info, $assets[$path]['suffix_1']);
      //   $assets[$path]['create_2']   = el('b_create_2',$img_info, $assets[$path]['create_2']);
      //   $assets[$path]['width_2']    = el('int_width_2',$img_info, $assets[$path]['width_2']);
      //   $assets[$path]['height_2']   = el('int_height_2',$img_info, $assets[$path]['height_2']);
      //   $assets[$path]['prefix_2']   = el('str_prefix_2',$img_info, $assets[$path]['prefix_2']);
      //   $assets[$path]['suffix_2']   = el('str_suffix_2',$img_info, $assets[$path]['suffix_2']);
      // }
    }
    
    if (!defined('PHPUNIT_TEST')) {
      $this->load->model('data/data_create');
      $this->data_create->save_config( 'assets', $this->config->item('SYS').'flexyadmin/config/assets.php', $this->config->item('SITE').'config/assets.php', array('assets'=>$assets) );
    }
    return $assets;
  }
  
  /**
   * Stel de assets map in
   *
   * @param string $path 
   * @return $this
   * @author Jan den Besten
   */
  public function set_folder($path) {
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
      foreach ($files as $file => $info) {
        if (is_visible_file($file)) {
          $existingInfo = $this->get_file_info($path,$file);
          if ($existingInfo) $info = array_merge($info,$existingInfo);
          $info['path'] = $path;
          $info['file'] = str_replace($path,'',$file);
          $info['b_exists'] = true;
          $info['date'] = str_replace(' ','-',$info['rawdate']);
          if ($hasMetaInfo and isset($info['meta'])) $info['meta'] = json_encode($info['meta']);
          if ($hasUsedInfo) $info['b_used'] = $this->is_file_used($path,$file);
          if ($clean or !$existingInfo) {
            $this->insert_file($path,$file,$info);
          }
          else {
            $this->update_file($path,$file,$info);
          }
        }
      }
    }
    
    // Remove unused files
    if ( $remove and $hasUsedInfo ) {
      $this->delete( array('b_used'=>false) );
    }
    
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
			
        // /**
        //  * Remove this file from other fields in data
        //  */
        //         $searchedFields=array();
        //         // txt & media fields
        //         $tables = $this->data->list_tables();
        //         $tables = filter_by($tables,'tbl');
        // foreach ($tables as $table) {
        //   $fields = $this->data->table($table)->list_fields();
        //   foreach ($fields as $field) {
        //     $pre=get_prefix($field);
        //     if ($pre=="txt") $searchedFields[]=$table.'.'.$field;
        //             if ($pre=="media") $searchedFields[]=$table.'.'.$field;
        //             if ($pre=="medias") $searchedFields[]=$table.'.'.$field;
        //   }
        //         }
        if (!defined('PHPUNIT_TEST')) $this->_remove_file_from_fields($path,$file);
      }      
    }

		if ($result) {
			log_("info","[FM] delete file/dir '$name'");
		}
		else {
			log_("info","[FM] ERROR deleting file/dir '$name'");
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
    // $name = $path."/".$file;
    $this->search_replace->media($file,'');
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
    if ( !$this->upload->upload_file( $file_field ) ) {
      $file = $this->upload->get_file();
			$this->error_message = $this->upload->get_error();
			log_("info","[FM] error while uploading: '$file' [$this->error_message]");
      return false;
    }
    
		// Rename
    $file = $this->upload->get_file();
		$ext = get_file_extension($file);
    $saveName = clean_file_name($file);
    if ($file!==$saveName) {
      if (rename($folder.'/'.$file, $folder.'/'.$saveName));
    }
    $file = $saveName;

    // Image? Check size
    if ( in_array(strtolower($ext),$this->config->item('FILE_types_img')) ) {
      
      // restore orientation
      $this->upload->restore_orientation($folder,$file);

      // check minimal size, if too small: delete and error
      if ( !$this->upload->check_size($path,$file,$path_settings) ) {
        $this->delete_file($path,$file);
        $this->error_message = langp('upload_img_too_small',$file);
        return FALSE;
      }
    }
    
    // Resize
    if ( !$this->upload->resize_image( $path,$file,$path_settings ) ) {
      $this->delete_file($path,$file);
      $this->error_message = langp('upload_resize_error',$file);
      return FALSE;
    }
    
    // Stop in database
    $this->insert_file($path,$file);

    // Auto fill
    // $this->upload->auto_fill_fields($file,$this->map);

    return $file;
	}
  

  /**
   * Grid set aanpassen als er een map is ingesteld en er dus files worden opgevraagd (ip2longv ruwe data)
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
    }
    return $grid_set;
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
      $info = $this->where('file',$file)->where('path',$path)->cache()->get_row();
      $this->file_info[$name] = $info;
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
    if ($path==='_thumbcache') {
      $path = get_prefix($file,'___');
    }
    // Mag het bestand zowiezo worden getoond?
    if ( !$this->is_restricted($path) ) return true;
    // Heeft de user zowiezo geen rechten voor deze map?
    $this->load->library('flexy_auth');
    if (!$this->flexy_auth->has_rights('media_'.$path)) return false;
    // Is de user niet gekoppeld aan dit bestand? Dan mag het.
    $info = $this->get_file_info($map.'/'.$file);
    if (!isset($info['user'])) return true;
    // Is het de goed user?
    if ( $this->flexy_auth->get_user()['id'] == $info['user']) return true;
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
   * @param array $filter[]
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
      // recent uploads?
      $number_of_recent_uploads = $this->get_setting('number_of_recent_uploads');
      if ($number_of_recent_uploads>0 ) {
        $this->reset();
        $this->select('file')->select_abstract()->order_by('date','DESC');
        $query = $this->_get_files( $path,$filter, $number_of_recent_uploads);
        $recent_uploads_options = $this->_make_options_result($query,'file');
        if ($recent_uploads_options) {
          $options = array(
            langp('form_dropdown_sort_on_last_upload',$number_of_recent_uploads) => $recent_uploads_options,
            lang('form_dropdown_sort_on_name')                                   => $options,
          );
        }
        $this->reset();
      }
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
    $this->group_start();
      $this->where( 'b_exists', TRUE );
      // Bestanden van bepaalde map
      $this->where( 'path', $path );
      // Standaard filters
      // $filter = array_rename_keys($filter,array('type'=>'str_type'));
      if ($filter) {
        if (!is_array($filter)) {
          $this->find($filter);
        }
        else {
          // find array of mulitple where's?
          $first = current($filter);
          if ( isset($first['field']) or isset($first['group']) ) {
            $this->find_multiple( $filter );
          }
          else {
            foreach ($filter as $field=>$value) {
              $this->where( $field, $value);
            }
          }
        }
      }
    $this->group_end();
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
