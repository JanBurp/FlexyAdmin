<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * tbl_menu - Uitbreiding om samengesteld menu's te maken, zie site/config/data/tbl_menu.php
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class tbl_menu extends Data_Core {

  public function __construct() {
    parent::__construct();
  }
  
  
  /**
   * Maak het menu en geef dat als database array terug
   * 
   * 
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_menu_result() {
    $cache_name = 'data_result_'.$this->settings['table'].'_menu_result';
    $menu_result = $this->_get_cached_result( $cache_name );
    if (!$menu_result) {
      $this->tree('full_uri','uri');
      $result_key = $this->settings['result_key'];
      $menu_result = $this->set_result_key('full_uri')->get_result();
      $this->_cache_result($menu_result,$cache_name);
      $this->set_result_key($result_key);
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

}
