<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cfg_admin_menu - autogenerated Table_model for table cfg_admin_menu
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class cfg_admin_menu extends Data_Core {
  
  private $types=array('tbl'=>'table','cfg'=>'config','rel'=>'rel','log'=>'log','res'=>'result');
  

  public function __construct() {
    parent::__construct();
    $this->load->model('ui');
    $this->load->helper('language');
    $this->lang->load('help');
  }
  
  /**
   * Speciale get om gegenereerd menu op te halen 
   *
   * @return void
   * @author Jan den Besten
   */
  public function get_menu() {
    $user  = $this->flexy_auth->get_user();
    if ( !$user ) return FALSE;
    
    $this->where( array(
      'b_visible'=>true,
      'id_user_group >=' => current(array_keys($user['groups'])),
      'order >=' => 4,
      'api !='=> 'API_plugin_stats'
    ));
    $result = $this->get_result();

    $sidebar=$this->_process_menu($result);
    

    $header = array(
      array( 'name' => lang('help'), 'uri'=>'help/index', 'type' => 'info' ),
      array( 'name' => $user['username'], 'uri'=>'form/cfg_users/current', 'type' => 'form', 'args' => array('table'=>'cfg_users','id'=>$user['id'] )),
      array( 'name' => lang('logout'), 'uri'=>'logout', 'type' => 'logout' )
    );

    $footer = array(
      array( 'name' => lang('settings'), 'uri'=>'form/tbl_site/1', 'type' => 'form', 'args' => array('table'=>'tbl_site')),
      array( 'name' => lang('statistics'), 'uri'=>'plugin/stats', 'type' => 'plugin', 'args' => array('plugin'=>'stats')),
    );
    return array('header'=>$header,'sidebar'=>$sidebar,'footer'=>$footer);
  }
  
  /**
   * Speciale get om gegenereerd menu op te halen 
   *
   * @return void
   * @author Jan den Besten
   */
  public function get_menus() {
    $user = $this->flexy_auth->get_user();
    
    $this->where( array(
      'b_visible'=>true,
      'id_user_group >=' => current(array_keys($user['groups'])),
      'order >=' => 4,
      'api !='=> 'API_plugin_stats'
    ));
    $result = $this->get_result();

    $sidebar=$this->_process_menu($result);
    
    $headerMenu = new Menu();
    $headerMenu->add( array( 'name' => lang('help'), 'uri'=>'_admin/help/index', 'glyphicon' => 'info' ));
    $headerMenu->add( array( 'name' => $user['username'], 'uri'=>'_admin/form/cfg_users/'.$user['id'], 'glyphicon' => 'user') );
    $headerMenu->add( array( 'name' => lang('logout'), 'uri'=>'_admin/logout', 'glyphicon' => 'logout' ));

    $footerMenu = new Menu();
    $footerMenu->add( array( 'name' => lang('settings'), 'uri'=>'_admin/form/tbl_site/1', 'glyphicon' => 'settings'));
    $footerMenu->add( array( 'name' => lang('statistics'), 'uri'=>'_admin/plugin/stats', 'glyphicon' => 'stats'));

    return array('headermenu'=>$headerMenu->render(),'sidemenu'=>$sidebar,'footermenu'=>$footerMenu->render());
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
    $user=$this->flexy_auth->get_user();
    $user_group=current(array_keys($user['groups']));
    
    $menu=array();
		foreach ($db_menu as $item) {
			switch($item['str_type']) {
        case 'api' :
          if (!isset($item['id_user_group']) or $item['id_user_group']===0 or $item['id_user_group']>=$user_group) {
            $menu[] = array(
              'name'    => lang(trim($item['str_uri']   ,'_')),
              'uri'     => $item['str_uri']   ,
              'type'    => 'api',
              'args'    => array('api'=>api_uri($item['api']),'path'=>$item['path'],'table'=>$item['table'],'str_table_where'=>$item['str_table_where'])
            );
          }
          break;

        case 'seperator' :
          $menu[]=array('type'=>'seperator');
          break;

        case 'tools':
          // Database import/export tools
          if ($this->flexy_auth->is_super_admin()) {
            $menu[] = array(
              'name'    => lang('db_export'),
              'uri'     => 'tools/db_export',
              'type'    => 'tools',
              'args'    => array('api'=>api_uri('API_db_export'))
            );
            $menu[] = array(
              'name'    => lang('db_import'),
              'uri'     => 'tools/db_import',
              'type'    => 'tools',
              'args'    => array('api'=>api_uri('API_db_import'))
            );
          }
          elseif ($this->flexy_auth->can_backup()) {
            $menu[] = array(
              'name'    => lang('db_backup'),
              'uri'     => 'tools/db_backup',
              'type'    => 'tools',
              'args'    => array('api'=>api_uri('API_db_backup'))
            );
            $menu[] = array(
              'name'    => lang('db_restore'),
              'uri'     => 'tools/db_restore',
              'type'    => 'tools',
              'args'    => array('api'=>api_uri('API_db_restore'))
            );
          }
          // Search&Replace AND Bulkupload tools
          if ($this->flexy_auth->can_use_tools()) {
            $menu[] = array(
              'name'    => lang('sr_search_replace'),
              'uri'     => 'tools/search',
              'type'    => 'tools',
              'args'    => array('api'=>api_uri('API_search'))
            );
            $menu[] = array(
              'name'    => lang('fill_fill'),
              'uri'     => 'tools/fill',
              'type'    => 'tools',
              'args'    => array('api'=>api_uri('API_fill'))
            );
          }
          break;

        // case 'table' :
        //   $uri=api_uri('API_view_grid',$item['table']);
        //   $uri.='/info/'.$item['id'];
        //   $menu[$uri]=array("uri"=>$uri,'name'=>$item['str_uri']   ,"class"=>'tbl '.$item['table']);
        //   break;

        case 'all_tbl_tables' :
          $tables=$this->list_tables();
          $key=array_search('tbl_site',$tables);
          if ($key) unset($tables[$key]);
          $menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('TABLE_prefix')));
          break;

        case 'all_cfg_tables' :
          $tables=$this->list_tables();
          $menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('CFG_table_prefix')));
          $menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('LOG_table_prefix')));
          $menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('REL_table_prefix')));
          break;

        case 'all_res_tables' :
          $tables=$this->list_tables();
          $menu=array_merge($menu,$this->_show_table_menu($tables,$this->config->item('RES_table_prefix')));
          break;

        // case 'media' :
        //   $uri=api_uri('API_filemanager','show',pathencode($item['path']));
        //   $menu[$uri]=array("uri"=>$uri,'name'=>$item['str_uri']   ,"class"=>'media ');
        //   break;

        // TODO: hier moet op een andere manier de cfg_media_info uitgelezen gaan worden
        case 'all_media':
          $cfg_media_info = $this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_media_info');
          if ($this->db->table_exists($cfg_media_info)) {
            $this->db->order_by("order");
            $query=$this->db->get($cfg_media_info);
            foreach($query->result_array() as $mediaInfo) {
              if (!isset($mediaInfo['b_visible']) or $mediaInfo['b_visible']) {
                if (!isset($mediaInfo['path']) and isset($mediaInfo['str_path'])) $mediaInfo['path']=$mediaInfo['str_path'];
                $menu[] = array(
                  'name'    => $this->ui->get($mediaInfo['path']),
                  'uri'     => 'media/'.$mediaInfo['path'],
                  'type'    => 'media',
                  'args'    => array('path'=>$mediaInfo['path']),
                  'help'    => $this->ui->get_help($mediaInfo["path"]),
                );
              }
            }
            $query->free_result();
          }
          break;

			}
		}

    // remove first and last seperators
    while ($menu[0]['type']=='seperator') { array_shift($menu); }
    while ($menu[count($menu)-1]['type']=='seperator') { array_pop($menu); }
		// remove double seperators
		$firstSeperator=false;
		foreach ($menu as $key => $item) {
			$isSeperator = empty($item) or ($item['type']=='seperator');
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
		$excluded=$this->config->item('MENU_excluded');
		$cfgTables=$this->cfg->get("CFG_table");
		$cfgTables=filter_by($cfgTables,$type);
    $cfgTables=sort_by($cfgTables,"order");
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
      if (!in_array($table,$excluded) and $this->flexy_auth->has_rights($table)) {
        $menu[]=array(
          'name'    => $this->ui->get($table),
          'uri'     => 'table/'.$table,
          'type'    => $this->types[get_prefix($table)],
          'args'    => array('table' => $table),
          'help'    => $this->ui->get_help($table) 
        );
      }
		}
		return $menu;
	}
  

}
