<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * tbl_menu - Uitbreiding om samengesteld menu's te maken, zie site/config/data/tbl_menu.php
 * 
 * TODO: 
 * - language split
 * - portfolio sites
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Core_tbl_menu extends Data_Core {
  
  /**
   * Bewaar het menu
   */
  private $_menu                = array();
  
  
  /**
   * Instellingen voor menu
   */
  private $_menu_caching        = TRUE;
  private $_menu_config         = NULL;
  private $_menu_config_default = array(
                                    array(
                                      'type'  => 'table',
                                      'table' => 'tbl_menu',
                                    ),
                                  );

  
  public function __construct() {
    parent::__construct();
    $this->config->load('menu',true);
    $this->_menu_caching = $this->config->get_item(array('menu','caching'));
    $this->_menu_config  = $this->config->get_item(array('menu','menu'));
    if (empty($this->_menu_config)) {
      $this->_menu_config = $this->_menu_config_default;
    }
  }
  
  
  /**
   * Maak het menu en geef dat als database array terug
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_menu_result() {
    $menu_result = FALSE;
    if ($this->_menu_caching) {
      $cache_name = 'data_result_'.$this->settings['table'].'_menu_result';
      $menu_result = $this->_get_cached_result( $cache_name );
    }
    if (!$menu_result) {
      $menu_result = $this->_create_menu_result();
      if ($this->_menu_caching) $this->_cache_result($menu_result,$cache_name);
    }
    return $menu_result;
  }
  
  
  /**
   * Geeft één item uit (samengesteld) menu
   *
   * @param string $uri 
   * @return array
   * @author Jan den Besten
   */
  public function get_menu_item( $uri ) {
    $items = $this->get_menu_result();
    return el($uri,$items,FALSE);
  }
  
  
  /**
   * Maak het menu aan de hand van de 'merged_menu' instellingen in site/config/tbl_menu
   * 
   * Resultaat is een array met als key de 'full_uri'
   *
   * @return array
   * @author Jan den Besten
   */
  private function _create_menu_result() {
    if (!empty($this->_menu)) return $this->_menu;

    // Voeg menu samen
    foreach ($this->_menu_config as $menu_item) {
      switch ($menu_item['type']) {
        case 'item':
          $this->_add_menu_item($menu_item);
          break;
        case 'table':
          $this->_add_menu_table($menu_item);
          break;
      }
    }
    
    // Reorder merged menu
    $order = 0;
    foreach ($this->_menu as $key => $item) {
      $this->_menu[$key]['order'] = $order++;
    }
    
    return $this->_menu;
  }
  
  /**
   * Op welke plaats komt het (sub)menu(item)?
   *
   * @param string $item 
   * @return array
   * @author Jan den Besten
   */
  private function _determine_menu_item_place($item) {
    $place = el('place',$item,FALSE);
    // Standaard aan het einde
    if (empty($place) or $place===FALSE) {
      $keys = array_keys($this->_menu);
      end($keys);
      $key = current($keys);
    }
    else {
      // place = [field => value]
      if (is_array($place)) {
        $place_field = key($place);
        $place_value = $place[$place_field];
        $keys = array_column( $this->_menu, $place_field,'full_uri' );
        $key = array_search($place_value,$keys);
      }
      // place =  uri
      else {
        $keys = filter_by_key( $this->_menu, $place );
        end($keys);
        $key = key($keys);
      }
    }
    
    $pre_uri = '';
    if (isset($this->_menu[$key]) and $place) {
      $pre_uri = el('full_uri',$this->_menu[$key], el('uri',$this->_menu[$key],'') );
    }
    
    $result = array(
      'key'     => $key,
      'pre_uri' => $pre_uri,
    );
    return $result;
  }
  
  
  /**
   * Voeg één item toe aan menu
   *
   * @param array $item 
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_item($item) {
    $place = $this->_determine_menu_item_place($item);

    if ($place['pre_uri']) {
      $item['full_uri'] = $place['pre_uri'].'/'.$item['uri'];
      $menu_item = array( $item['full_uri'] => $item );
    }
    else {
      $menu_item = array( $item['uri'] => $item );
    }
    
    if ($place['key'])
      $this->_menu = array_add_after( $this->_menu, $place['key'], $menu_item );
    else
      $this->_menu = $menu_item;
  }
  
  /**
   * Voeg hele tabel toe aan het menu
   *
   * @param array $item 
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_table($item) {
    $table = $item['table'];
    
    $this->data->table($table);
    // Set result_key to full_uri
    $result_key = $this->data->get_setting('result_key');
    if ($this->data->field_exists('self_parent')) {
      $this->data->tree('full_uri','uri');
      $this->data->set_result_key('full_uri');
    }
    else {
      $this->data->set_result_key('uri');
    }
    // get data
    if (isset($item['where']))    $this->data->where($item['where']);
    if (isset($item['order_by'])) $this->data->order_by($item['order_by']);
    $items = $this->data->get_result( el('limit',$item,0), el('offset',$item,0) );
    // restore
    $this->data->set_result_key($result_key);
    $this->data->table('tbl_menu');
    
    
    $place = $this->_determine_menu_item_place($item);
    if ($place['pre_uri']) {
      foreach ($items as $key => $row) {
        unset($items[$key]);
        $full_uri         = $place['pre_uri'].'/'.el('full_uri',$row, el('uri',$row));
        $row['full_uri']  = $full_uri;
        $row['_table']    = $table;
        
        $items[$full_uri] = $row;
      }
    }

    if ($place['key']) {
      $this->_menu = array_add_after( $this->_menu, $place['key'], $items );
    }
    else
      $this->_menu = $items;
  }
  
  

}
