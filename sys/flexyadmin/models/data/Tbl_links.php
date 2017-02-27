<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * tbl_links
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Tbl_links extends Data_Core {
  
  private $checked_links = array();

  public function __construct() {
    parent::__construct();
  }
  
  
  /**
   * Voeg actie toe om link te checken
   *
   * @param int $limit 
   * @param int $offset 
   * @return $this
   * @author Jan den Besten
   */
  public function get_grid( $limit = 20, $offset = FALSE ) {
    $result = parent::get_grid($limit,$offset);
    foreach ($result as $key => $link) {
      $id   = $link['id'];
      $link = array_add_after($link,'id', array(
        'action_check_link' => array(
          'uri'   => 'link_checker?where='.$id,
          'icon'  => 'chain-broken', 
          'text'  => lang('link_check')
        ))
      );
      $result[$key] = $link;
    }
    
    // Voeg het veld toe
    $this->settings['grid_set']['fields'] = array_add_after($this->settings['grid_set']['fields'],'id','action_check_link');
    return $result;
  }
  
  
  /**
   * Voeg linkchecker actie's toe
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_grid_set() {
    $grid_set = parent::get_setting_grid_set();
    $grid_set['actions'] = array(
      array(
        'name' => lang('link_checker'),
        'icon' => 'chain-broken',
        'url'  => 'link_checker',
      ),
    );
    $grid_set['field_info']['action_check_link'] = array(
      'name'      => lang('link_checker'),
      'grid-type' => 'action',
    );
    
    return $grid_set;
  }
  
  /**
   * Controleert alle links
   *
   * @return array
   * @author Jan den Besten
   */
  public function check_links() {
    $this->select(array('url_url'));
    $this->like('url_url','http','after');
    $result = $this->get_result();
    foreach ($result as $id => $link) {
      $url = $link['url_url'];
      $result[$id]['checked'] = $this->_check_link( $url );
    }
    $this->checked_links = $result;
    return $result;
  }
  
  /**
   * Geeft een leesbaar resulaat terug na check_links
   *
   * @return string
   * @author Jan den Besten
   */
  public function get_message() {
    $good = array();
    $false = array();
    $message_type = '';
    foreach ($this->checked_links as $id => $link) {
      if ($link['checked']) {
        $good[] = '<span class="text-primary"><span class="fa fa-check-square-o"></span>&nbsp;<a href="'.$link['url_url'].'" target="_blank">'.$link['url_url'].'</a></span>';
      }
      else {
        $false[] = '<span class="text-danger"><span class="fa fa-square-o"></span>&nbsp;<a class="text-danger" href="'.$link['url_url'].'" target="_blank">'.$link['url_url'].'</a></span>';
      }
    }
    $message = count($this->checked_links) . ' gecontroleerd. '.count($good).' in orde.';
    if (count($false)>0) {
      $message_type = 'danger';
      $message .= ' <span class="text-danger">'.count($false).' niet in orde.';
      $message .= '<hr>';
      $message .= implode('<br>',$false).'<br>';
      $message .= implode('<br>',$good).'<br>';
    }
    return array(
      'text' => $message,
      'type' => $message_type
    );
  }
  
  
  /**
   * Controleer één link
   * https://stackoverflow.com/questions/15770903/check-if-links-are-broken-in-php
   *
   * @author Jan den Besten
   */
  private function _check_link( $url ) {
    $headers = @get_headers( $url);
    $headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;
    return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
  }
  
  
}
