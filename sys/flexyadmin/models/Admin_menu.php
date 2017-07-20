<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Admin Menu
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Admin_menu extends CI_Model {
  
  private $hidden_tables = array('tbl_site','cfg_version','cfg_sessions');
  private $ui_config     = array();
  private $user          = false;

  public function __construct() {
    parent::__construct();
    $this->config->load('admin_ui',true);
    $this->ui_config = $this->config->item('admin_ui');
    $this->load->library('menu');
    $this->load->helper('language');
    $this->lang->load('help');
    $this->lang->load('ui');
    $this->user = $this->flexy_auth->get_user();
  }
  
  private function _get_config_item($name) {
    $item = $this->ui_config[$name];
    if (isset($this->ui_config[$name.'_replace'])) $item = $this->ui_config[$name.'_replace'];
    return $item;
  }
  
  /**
   * Speciale get om gegenereerd menu op te halen 
   *
   * @return void
   * @author Jan den Besten
   */
  public function get_menus( $base_url, $current_uri ) {
    
    /**
     * Home items
     */
    $homeMenu = new Menu();
    $homeMenu->set('view_path','admin/menu-home');
    $homeMenu->set_current($current_uri);
    $homeMenu->add_items( $this->_process_items('', $this->_get_config_item('home_menu') ) );
    
    /**
     * Headermenu
     */
    $headerMenu = new Menu();
    $headerMenu->set('view_path','admin/menu-horizontal');
    $headerMenu->set('framework','bootstrap');
    $headerMenu->set_current($current_uri);
    $headerMenu->add_items( $this->_process_items('', $this->_get_config_item('header_menu') ) );
    // Headermenu Help
    $headerMenu->menu['help']['uri']          ='';
    $headerMenu->menu['help']['html']         = '@click.stop.prevent="global.toggleHelp()" :class="{\'active\':state.help_on}"';
    $headerMenu->menu['help']['class']        = 'help-button';
    $headerMenu->menu['help']['active_icon']  = 'chevron-right';

    /**
     * Side menu
     */
    $sideMenu = new Menu();
    $sideMenu->set('view_path','admin/menu-vertical');
    $sideMenu->set('framework','bootstrap');
    $sideMenu->set_current($current_uri);
    $first=true;
    $side = $this->_get_config_item('side_menu');
    foreach ($side as $group) {
      $items = $this->_process_items('',$group);
      if ($items) {
        if (!$first) $sideMenu->add_split();
        $sideMenu->add_items( $this->_process_items('',$group) );
      }
      $first=false;
    }

    // trace_($sideMenu->menu);

    return array(
      'homemenu'   =>$homeMenu->render(),
      'headermenu' =>$headerMenu->render(),
      'sidemenu'   =>$sideMenu->render()
    );
  }
  
  /**
   * Vertaal menu item
   *
   * @param string $base_url 
   * @param array $item 
   * @return array
   * @author Jan den Besten
   */
  private function _process_item( $base_url, $item ) {
    $menuItem = array(
      'name'       => $item['name'],
      'uri'        => $base_url.str_replace('{user_id}',$this->user['id'],$item['uri']),
      'icon'       => el('icon',$item,''),
      'iconactive' => el('iconactive',$item,''),
      'class'      => el('class',$item,''),
    );
    return $menuItem;
  }
  
  /**
   * Vertaal menu items van config naar echte menu items
   *
   * @param string $base_url 
   * @param array $items 
   * @return array
   * @author Jan den Besten
   */
  private function _process_items( $base_url, $items ) {
    $menuItems = array();
    foreach ($items as $key=>$item) {
      if ($this->has_group_rights($item)) {
        if (isset($item['type'])) {
          switch ($item['type']) {

            // Seperator
            case 'seperator':
              $menuItems[$key] = 'seperator';
              break;

            // Split
            case 'split':
              $menuItems[$key] = 'split';
              break;

            // One table
            case 'table':
              $table = el('table',$item,$key);
              if ($this->flexy_auth->has_rights($table)) {
                $menuItems[$table] = $this->_process_item('', array(
                  'name'       => el('name',$item,$this->lang->ui($table)),
                  'uri'        => 'grid/'.$table,
                  'icon'       => el('icon',$item,''),
                  'iconactive' => el('iconactive',$item,''),
                  'class'      => el('class',$item,''),
                ));
              }
              break;
            
            // Multiple tables
            case 'tables':
              $tables = $this->data->list_tables();
              $tables = filter_by($tables,$item['pre']);
              foreach ($tables as $table) {
                if (!in_array($table,$this->hidden_tables)) {
                  if (!isset($menuItems[$table])) {
                    $menuItems[$table] = $this->_process_item('', array(
                      'name'       => $this->lang->ui($table),
                      'uri'        => 'grid/'.$table,
                      'icon'       => el('icon',$item,''),
                      'iconactive' => el('iconactive',$item,''),
                      'class'      => el('class',$item,''),
                    ));
                  }
                }
              }
              break;

            // Media
            case 'media':
              $path = el('path',$item,$key);
              $menuItems['media_'.$path] = $this->_process_item('', array(
                'name'       => el('name',$item,$this->lang->ui('media_'.$path)),
                'uri'        => 'media/'.$path,
                'icon'       => el('icon',$item,''),
                'iconactive' => el('iconactive',$item,''),
                'class'      => el('class',$item,''),
              ));
              break;

            // Multiple media
            case 'medias':
              $medias = $this->assets->get_assets_folders(false);
              foreach ($medias as $media) {
                if (!isset($menuItems['media_'.$media])) {
                  $menuItems['media_'.$media] = $this->_process_item('', array(
                    'name'       => $this->lang->ui('media_'.$media),
                    'uri'        => 'media/'.$media,
                    'icon'       => el('icon',$item,''),
                    'iconactive' => el('iconactive',$item,''),
                    'class'      => el('class',$item,''),
                  ));
                }
              }
              break;

          }
        
        }
        else {
          // Just one standard item
          $item['name'] = $this->lang($item['name']);
          $menuItems[$key]  = $this->_process_item($base_url,$item);
        }
      }
    }
    // Cleanup redundant splits & seperators
    end($menuItems);
    $item=current($menuItems);
    while ($item) {
      if ($item=='' or $item=='seperator' or $item=='split') {
        $key = key($menuItems);
        unset($menuItems[$key]);
        $item=prev($menuItems);
      }
      else $item=false;
    }
    reset($menuItems);
    if (count($menuItems)==1) {
      $item = current($menuItems);
      if ($item=='' or $item=='seperator' or $item=='split') {
        $menuItems = array();
      }
    }
    reset($menuItems);
    return $menuItems;
  }
  
  private function has_group_rights($item) {
    // All
    if (!isset($item['user_group'])) return true;
    // Tools
    if ($item['user_group']==='[b_tools]' && $this->user['rights']['tools']) return true;
    // Backup
    if ($item['user_group']==='[b_backup]' && $this->user['rights']['backup']) return true;
    // Group?
    return $this->flexy_auth->at_least_in_group( $item['user_group'] );
  }
  
  
  /**
   * Uitbreiding op normale lang, met extra variabelen om te parsen
   *
   * @param string $key 
   * @return string
   * @author Jan den Besten
   */
  private function lang($key) {
    $lang = $key;
    switch ($key) {
      case '{username}':
        $lang = $this->user['username'];
        break;
      default:
        $lang = $this->lang->line($key);
        break;
    }
    return $lang;
  }
  

}
