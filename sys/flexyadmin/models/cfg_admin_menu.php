<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cfg_admin_menu extends Crud {

	public function __construct() {
		parent::__construct();
		$this->table('cfg_admin_menu');
    $this->load->model('ui');
    $this->lang->load('help');
	}
  
  public function get($args=array()) {
    $result=parent::get(array(
      'where'   => array(
                    'b_visible'=>true,
                    'id_user_group >='=>$this->user->group_id,
                    'order >=' => 4,
                    'str_type !=' => 'seperator',
                    'api !='=> 'API_plugin_stats'
                    ),
      'order'   => 'order'
    ));
    $main_menu=$this->_process_menu($result);

    $header_menu = array(
      'help'    => array( 'title' => lang('help'), 'type' => 'info' ),
      'user'    => array( 'title' => $this->user->user_name, 'type' => 'form', 'args' => array('table'=>'cfg_users','id'=>$this->user->user_id)),
      'logout'  => array( 'title' => lang('logout'), 'type' => 'logout' )
    );

    $footer_menu = array(
      'settings'    => array( 'title' => lang('settings'), 'type' => 'form', 'args' => array('table'=>'tbl_site')),
      'statistics'  => array( 'title' => lang('statistics'), 'type' => 'plugin', 'args' => array('plugin'=>'stats')),
    );
    return array('header_menu'=>$header_menu,'main_menu'=>$main_menu,'footer_menu'=>$footer_menu);
  }
  
  
  /**
   * Process database result from cfg_admin_menu to a menu array
   *
   * @param string $db_menu 
   * @param string $currentMenuItem 
   * @return void
   * @author Jan den Besten
   */
	private function _process_menu($db_menu,$currentMenuItem="") {
    $user=$this->user->get_user();
    $user_group=$user->id_user_group;

    $menu=array();
		foreach ($db_menu as $item) {
			switch($item['str_type']) {
				case 'api' :
          if (!isset($item['id_user_group']) or $item['id_user_group']>=$user_group) {
            // TODO
            $menu[$item['str_ui_name']] = array(
              'title' => lang(trim($item['str_ui_name'],'_')),
              'type'  => 'api',
              'args'  => array('api'=>api_uri($item['api']),'path'=>$item['path'],'table'=>$item['table'],'str_table_where'=>$item['str_table_where'])
            );
          }
					break;
					
				case 'seperator' :
					$menu[]=array();
					break;

				case 'tools':
					// Database import/export tools
          if ($this->user->is_super_admin()) {
            $menu['tools-db_export'] = array(
              'title' => lang('db_export'),
              'type'  => 'tools',
              'args'  => array('api'=>api_uri('API_db_export'))
            );
            $menu['tools-db_import'] = array(
              'title' => lang('db_import'),
              'type'  => 'tools',
              'args'  => array('api'=>api_uri('API_db_import'))
            );
          }
          elseif ($this->user->can_backup()) {
            $menu['tools-db_backup'] = array(
              'title' => lang('db_backup'),
              'type'  => 'tools',
              'args'  => array('api'=>api_uri('API_db_backup'))
            );
            $menu['tools-db_restore'] = array(
              'title' => lang('db_restore'),
              'type'  => 'tools',
              'args'  => array('api'=>api_uri('API_db_restore'))
            );
          }
          // Search&Replace AND Bulkupload tools
          if ($this->user->can_use_tools()) {
            $menu['tools-search'] = array(
              'title' => lang('sr_search_replace'),
              'type'  => 'tools',
              'args'  => array('api'=>api_uri('API_search'))
            );
            $menu['tools-fill'] = array(
              'title' => lang('fill_fill'),
              'type'  => 'tools',
              'args'  => array('api'=>api_uri('API_fill'))
            );
          }
          break;
				
        // case 'table' :
        //   $uri=api_uri('API_view_grid',$item['table']);
        //   $uri.='/info/'.$item['id'];
        //   $menu[$uri]=array("uri"=>$uri,'name'=>$item['str_ui_name'],"class"=>'tbl '.$item['table']);
        //   break;
					
				case 'all_tbl_tables' :
					$tables=$this->db->list_tables();
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('TABLE_prefix')));
					break;

				case 'all_cfg_tables' :
					$tables=$this->db->list_tables();
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('CFG_table_prefix')));
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('LOG_table_prefix')));
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('REL_table_prefix')));
					break;

				case 'all_res_tables' :
					$tables=$this->db->list_tables();
					$menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('RES_table_prefix')));
					break;
				
        // case 'media' :
        //   $uri=api_uri('API_filemanager','show',pathencode($item['path']));
        //   $menu[$uri]=array("uri"=>$uri,'name'=>$item['str_ui_name'],"class"=>'media ');
        //   break;
					
				case 'all_media':
					$mediaInfoTbl=$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_media_info');
					if ($this->db->table_exists($mediaInfoTbl)) {
						$this->db->order_by("order");
						$query=$this->db->get($mediaInfoTbl);
						foreach($query->result_array() as $mediaInfo) {
              if (!isset($mediaInfo['b_visible']) or $mediaInfo['b_visible']) {
  							if (!isset($mediaInfo['path']) and isset($mediaInfo['str_path'])) $mediaInfo['path']=$mediaInfo['str_path'];
                $menu['media-'.$mediaInfo['path']] = array(
                  'title' => $this->ui->get($mediaInfo['path']),
                  'type'  => 'media',
                  'args'  => array('path'=>$mediaInfo['path']),
                  'help'  => $this->ui->get_help($mediaInfo["path"]),
                );
              }
						}
						$query->free_result();
					}
					break;
				
			}
		}

		// remove double seperators
		$firstSeperator=false;
		foreach ($menu as $key => $item) {
			$isSeperator = empty($item);
			if ($isSeperator) {
				if ( ! $firstSeperator)
					$firstSeperator=true;
				else
					unset($menu[$key]);
			}
			else
				$firstSeperator=false;
		}
		
    
    return $menu;
	}
  
  
  
	private function _show_table_menu($tables,$type) {
		$menu=array();
		$tables=filter_by($tables,$type."_");
		// trace_($tables);
		$excluded=$this->config->item('MENU_excluded');
		$cfgTables=$this->cfg->get("CFG_table");
		$cfgTables=filter_by($cfgTables,$type);
    // order and show tables according to cfg_table_info
		$oTables=array();
		foreach ($cfgTables as $row) {
			if (in_array($row["table"],$tables)) {
				unset($tables[array_search($row["table"],$tables)]);
        if (!isset($row['b_visible']) or $row['b_visible'])
          $oTables[]=$row["table"];
			}
		}
		$oTables=array_merge($oTables,$tables);
		foreach ($oTables as $table) {
      if (!in_array($table,$excluded) and $this->user->has_rights($table)) {
        $menu['grid-'.$table]=array(
          'title' => $this->ui->get($table),
          'type'  => 'grid',
          'args'  => array('table' => $table),
          'help'  => $this->ui->get_help($table) 
        );
      }
		}
		return $menu;
	}
  
  
  

}



/* End of file crud.php */
/* Location: ./system/application/models/crud.php */
