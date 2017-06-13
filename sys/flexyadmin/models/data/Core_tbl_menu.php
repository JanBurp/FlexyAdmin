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
   * Geeft alle tabellen die gebruikt worden voor het menu
   *
   * @return     array  The menu tables.
   */
  public function get_menu_tables() {
    $tables = array();
    foreach ($this->_menu_config as $key => $item) {
      if (isset($item['table'])) array_unshift($tables,$item['table']);
    }
    return $tables;
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
    // trace_($menu_result);
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

        case 'items':
          $this->_add_menu_items($menu_item);
          break;

        case 'table':
          $this->_add_menu_table($menu_item);
          break;

        case 'model':
          $this->_add_menu_from_model($menu_item);
          break;
      }
    }
    
    // Reorder merged menu & zorg dat children b_visible en b_restricted overnemen (als nodig)
    $order = 0;
    $b_visible    = array();
    $b_restricted = array();
    foreach ($this->_menu as $key => $item) {
      
      // Visible & Restricted
      $item['b_visible']    = el('b_visible',$item,true);
      $item['b_restricted'] = el('b_restricted',$item,false);
      
      // Test if parent is visible/restricted
      $uri      = $item['uri'];
      $full_uri = $item['full_uri'];
      if ( $full_uri!=$item['uri'] ) {
        $parent_uri = remove_suffix($item['full_uri'],'/');
        if ($parent_uri!='') {
          $item['b_visible']    = el($parent_uri,$b_visible,true);
          $item['b_restricted'] = el($parent_uri,$b_restricted,false);
        }
      }
      $b_visible[$full_uri] = $item['b_visible'];
      $b_restricted[$full_uri] = $item['b_restricted'];

      // Add Order
      $item['order'] = $order++;

      $this->_menu[$key] = $item;      
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
  private function _determine_menu_item_places($item) {
    $place = el('place',$item,FALSE);

    // Standaard aan het einde
    if (empty($place) or $place===FALSE) {
      $keys = array_keys($this->_menu);
      end($keys);
      $found_uris = current($keys);
    }
    else {
    
      // place = [field => value]
      if (is_array($place)) {
        $place_field = key($place);
        $place_value = $place[$place_field];
        $key = find_row_by_value($this->_menu,$place_value,$place_field);
        $found_uris = array_keys($key);
      }
    
      // place =  uri
      else {
        $keys = filter_by_key( $this->_menu, $place );
        end($keys);
        $found_uris = key($keys);
      }
    
    }


    if (!is_array($found_uris)) $found_uris = array($found_uris);


    $result = array();
    foreach ($found_uris as $key => $found_uri) {
      $pre_uri = '';
      if (isset($this->_menu[$found_uri]) and $place) {
        $pre_uri = el('full_uri',$this->_menu[$found_uri], el('uri',$this->_menu[$found_uri],'') );
      }
      $result[] = array(
        'key'     => $found_uri,
        'pre_uri' => $pre_uri,
      );
    }
    
    return $result;
  }
  
  
  /**
   * Voeg één item toe aan menu
   *
   * @param array $item 
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_item($item,$places=FALSE) {
    if (!$places) $places = $this->_determine_menu_item_places($item);
    if (!is_array($places)) $places = array($places);

    foreach ($places as $place) {
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
  }
  
  
  /**
   * Voeg meerdere items toe aan menu
   *
   * @param array $item 
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_items($item) {
    $places = $this->_determine_menu_item_places($item);
    foreach ($item['items'] as $sub_item) {
      $this->_add_menu_item($sub_item,$places);
    }
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
    $data_items = $this->data->get_result( el('limit',$item,0), el('offset',$item,0) );
    
    // restore
    $this->data->set_result_key($result_key);
    $this->data->table('tbl_menu');
    
    // Add
    $places = $this->_determine_menu_item_places($item);
    foreach ($places as $place) {
      $items = $data_items;
      if ($place['pre_uri']) {
        foreach ($items as $key => $row) {
          unset($items[$key]);
          if (isset($item['item'])) $row = array_merge($row,$item['item']);
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
  
/**
   * Voeg menu items toe dmv extern model
   *
   * @param array $item 
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_from_model($item) {
    $method = get_suffix($item['model'],'.');
    $model = get_prefix($item['model'],'.');;
    $this->load->model($model);
    $data_items = $this->$model->$method($item);
    
    // Add
    $this->data->table('tbl_menu');
    $places = $this->_determine_menu_item_places($item);
    foreach ($places as $place) {
      $items = $data_items;
      if ($place['pre_uri']) {
        foreach ($items as $key => $row) {
          unset($items[$key]);
          if (isset($item['item'])) $row = array_merge($row,$item['item']);
          $full_uri         = $place['pre_uri'].'/'.el('full_uri',$row, el('uri',$row));
          $row['full_uri']  = $full_uri;
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

  

}
