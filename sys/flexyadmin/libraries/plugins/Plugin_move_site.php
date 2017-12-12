<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Kan alle assets van een (oude) site verplaatsen van map van nieuwe site.
 *
 * @author Jan den Besten
 * @internal
 */
class Plugin_move_site extends Plugin {

  var $old = '';
  var $new = '';
  var $oldDB;

	public function __construct() {
		parent::__construct();
    $this->CI->load->dbforge();
	}

  public function _admin_api($args=false) {
    if ( !$this->CI->flexy_auth->is_super_admin()) return false;
    
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
    // if (file_exists($this->old) and file_exists($this->new)) {
    //   $this->empty_paths();
    //   $this->move();
    //   $this->merge();
    // }

    
    $this->add_message('<h2>Merge old database with fresh database</h2>');
    $old_db = $this->config['db'];
    if ($old_db) {
      $this->oldDB = $this->CI->load->database( $old_db, TRUE);
      $this->truncate_demo_tables();
      $this->import_database();
      $this->create_data_config();
    }
	
  	return $this->view('admin/plugins/plugin');
  }
  
  
  /**
   * Schoon de demo database op
   *
   * @return void
   * @author Jan den Besten
   */
  private function truncate_demo_tables() {
    $ul=array();
    foreach ($this->config['truncate_demo_tables'] as $table) {
      if ($this->CI->db->table_exists($table)) {
        $this->CI->db->truncate( $table );
        $ul[]=$table;
      }
    }
    $this->add_message('<h3>Truncated demo tables:</h3>');
    $this->add_message(ul($ul));
  }
  
  
  /**
   * Merge (and import) old tables
   *
   * @author Jan den Besten
   */
  private function import_database() {
    // Merge some tables
    $tables = $this->config['merge_tables'];
    $this->_merge_tables($tables);
    
    // Merge & complete some tables
    $tables = $this->config['merge_and_complete_tables'];
    $this->_merge_tables($tables,TRUE);
    
    // Import some tables
    $this->_import_missing_tables();
  }
  
  
  private function _import_missing_tables() {
    $tables = $this->CI->data->list_tables();
    $old_tables = $this->oldDB->list_tables();
    $missing_tables = array_diff($old_tables,$tables);
    // only 'tbl_' tables
    $missing_tables = filter_by($missing_tables,'tbl');
    // create them
    foreach ($missing_tables as $table) {
      $this->CI->dbforge->add_field('id');
      $this->CI->dbforge->create_table($table);
    }
    // Now import the data and create missing fields
    $this->_merge_tables( $missing_tables,TRUE, 'Import missing tables');
  }
  

  private function _merge_tables( $tables, $complete=FALSE, $title='' ) {
    if (empty($title)) {
      $title = 'Merged tables:';
      if ($complete) $title='Merged &amp; completed tables:';
    }
    $ul=array();
    foreach ($tables as $table) {
      if (!$this->CI->db->table_exists($table)) {
        $ul[]=span('error').$table.' missing in NEW database'._span();
      }
      else {
        $fields = $this->CI->db->list_fields($table);
        if (!$this->oldDB->table_exists($table)) {
          $ul[]=span('error').$table.' missing in OLD database'._span();
        }
        else {
          // truncate new
          $this->CI->db->truncate( $table );
          // which fields?
          $old_fields = $this->oldDB->list_fields($table);
          $merge_fields = array_intersect( $fields, $old_fields);
          $missing_fields = array_diff( $old_fields,$fields );
          if (!$complete) {
             $this->oldDB->select($merge_fields);
          }
          else {
            if ($missing_fields) {
              // Create the missing fields
              $field_info = $this->oldDB->field_data( $table );
              $field_info = object2array($field_info);
              $names      = array_column($field_info,'name');
              $field_info = array_combine($names, $field_info);
              $field_info = array_keep_keys($field_info,$missing_fields);
              // Create the fields
              foreach ($field_info as $name => $info) {
                $settings = array( $name=>array( 'type' => strtoupper($info['type']) ));
                if ($info['max_length'])     $settings[$name]['constraint'] = $info['max_length'];
                if (isset($info['default'])) $settings[$name]['default'] = $info['default'];
                $this->CI->dbforge->add_column( $table, $settings );
              }
            }
          }
          $data = $this->oldDB->get($table)->result_array();
          foreach ($data as $row) {
            $this->CI->db->set($row);
            $this->CI->db->insert( $table );
          }
        }
        $message = $table.'&nbsp;['.implode(',',$merge_fields).']';
        if ($missing_fields) {
          if ($complete)
            $message.= span('error').' created ['.implode(',',$missing_fields).']'._span();
          else
            $message.= span('error').'&nbsp;missing&nbsp;['.implode(',',$merge_fields).']'._span();
        }
        $ul[]=$message;
      }
    }
    $this->add_message('<h3>'.$title.'</h3>');
    $this->add_message(ul($ul));
  }


  private function create_data_config() {
    // assets (from cfg_media_info & cfg_img_info)
    $this->CI->load->model('data/data_create');
    $this->CI->assets->_create_assets_settings(TRUE,$this->oldDB);
    $this->add_message('<h2>Created config file `assets.php`</h2>');
    
    // table & field info
    $this->add_message('<h2>Created data config file for:</h2>');
    $ul = '<ul>';
    $tables = $this->CI->db->list_tables();
    foreach ($tables as $table) {

      $table_info = $this->oldDB->where('table',$table)->get('cfg_table_info');
      if ($table_info) {
        $table_info = $table_info->row_array();
        if ($table_info) {
          $fields = $this->oldDB->list_fields($table);
          $data = array();
          $data['table'] = $table;
          // $data['fields'] = $fields;
          if ($table_info['str_order_by'])            $data['order_by'] = $table_info['str_order_by'];
          if ($table_info['int_max_rows']>0)          $data['max_rows'] = $table_info['int_max_rows'];
          if ($table_info['str_abstract_fields'])     $data['abstract_fields'] = explode(',',$table_info['str_abstract_fields']);
          if ($table_info['b_freeze_uris'])           $data['update_uris'] = FALSE;

          $grid_set = array(
            'fields'     => $fields,
            'pagination' => true,
          );
          if ($table_info['b_grid_add_many']) $grid_set['with']   = array('many_to_one','many_to_many');

          $form_set = array(
            'fieldsets'  => array($table=>$fields),
          );
          if ($table_info['str_fieldsets'])   {
            $fieldsets = explode(',',$table_info['str_fieldsets']);
            $fieldsets = array_combine($fieldsets,$fieldsets);
            foreach ($fieldsets as $key => $set) {
              $fieldsets[$key] = array();
            }
            $form_set['fieldsets'] = array_merge($form_set['fieldsets'],$fieldsets);
          }
          if ($table_info['b_form_add_many']) $form_set['with']      = array('many_to_one','many_to_many');

          // Field info
          foreach ($fields as $field) {
            $field_info = $this->oldDB->where('field_field',$table.'.'.$field)->get('cfg_field_info');
            if ($field_info) $field_info = $field_info->row_array();
            if (!is_array($field_info) or empty($field_info)) {
              $field_info = $this->oldDB->where('field_field','*.'.$field)->get('cfg_field_info');
              if ($field_info) $field_info = $field_info->row_array();
            }
            if ($field_info) {
              // trace_($field);
              // trace_($field_info);
              
              // Validation
              if (!empty($field_info['str_validation_rules'])) {
                if (!isset($data['field_info'])) $data['field_info'] = array();
                $data['field_info'][$field]['validation'] = $field_info['str_validation_rules'];
              } 
              
              // Options
              if (!empty($field_info['str_options'])) {
                if (!isset($data['options'])) $data['options'] = array();
                $options = explode('|',$field_info['str_options']);
                $data['options'][$field] = array(
                  'data'     => array_combine($options,$options),
                  'multiple' => $field_info['b_multi_options'],
                );
              }

              // Grid fields
              if ( !$field_info['b_show_in_grid'] ) {
                if ($found = array_search($field,$grid_set['fields'])) {
                  unset($grid_set['fields'][$found]);
                }
              }
              
              // Form fieldsets/fields
              if ( !$field_info['b_show_in_form'] ) {
                if ($found = array_search($field,$form_set['fields'])) {
                  unset($form_set['fields'][$found]);
                }
              }
              if ( !empty($field_info['str_fieldset']) ) {
                $fieldset=trim($field_info['str_fieldset']);
                $form_set['fieldsets'][$fieldset][] = $field;
                if ($found = array_search($field,$form_set['fieldsets'][$table])) {
                  unset($form_set['fieldsets'][$table][$found]);
                }
              }

            }
          }

          $data['grid_set'] = $grid_set;
          $data['form_set'] = $form_set;

          // Current data?
          $data_file = $this->CI->config->item('SITE').'config/data/'.$table.'.php';
          $this->CI->config->load('data/'.$table.'.php',true);
          $current_data = $this->CI->config->config['data/'.$table];
          if ($current_data) {
            $data = array_merge($current_data,$data);
          }

          // Write
          $this->CI->data_create->save_config( $table, $this->CI->config->item('SYS').'flexyadmin/config/data/data.php', $data_file, $data );

          $ul.='<li>'.$table.'</li>';
        }
      }

    }
    $ul.='</ul>';
    $this->add_message($ul);
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
      $map = $this->CI->config->item('ASSETSFOLDER').$path;
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
    $move_paths = $this->config['move'];
    $moved=array();
    $error=array();
    foreach ($move_paths as $type => $paths) {
      foreach ($paths as $path) {
        $old = $this->old.'site/assets/'.$path;
        $new = $this->CI->config->item('ASSETSFOLDER').$path;
        if ($type=='public') {
          $new = $this->CI->config->item('PUBLICASSETS').$path;
        }

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
      $old = $this->old.'site/'.$path;
      $new = '../site/'.$path;

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