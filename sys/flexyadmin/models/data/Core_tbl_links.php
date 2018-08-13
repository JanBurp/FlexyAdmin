<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * tbl_links
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Core_tbl_links extends Data_Core {
  
  private $checked_links      = false;
  private $checked_cache_name = 'tbl_links_checked';
  private $cache_expire       = TIME_MINUTE;

  public function __construct() {
    parent::__construct();
    $this->checked_links = $this->get_cached_result($this->checked_cache_name);
  }

  /**
   * Dit geeft de linklijst als options
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_options_link_list() {
    $this->data->table('tbl_menu');
    $links = $this->data->get_menu_result();
    $links = array_combine(array_keys($links),array_column($links,'full_title'));
    $this->data->table('tbl_links');
    return $links;
  }

  
  /**
   * Dit geeft de linklijst aan de API call get_link_list
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_link_list() {
    $site_links = $this->data->table('tbl_site')->select('str_title,url_url,email_email')->cache()->get_row();
    $links = array(
      array( 'title'=>'Algemeen',    'menu' => array(
        array( 'title'=>$site_links['str_title'].' ('.str_replace(array('http://','https://'),'',$site_links['url_url']).')',   'value' => $site_links['url_url'] ),
        array( 'title'=>$site_links['email_email']. ' ('.$site_links['email_email'].')', 'value' => 'mailto:'.$site_links['email_email'] ),
      )),
      array( 'title'=>'Pagina\'s',   'menu' => $this->_menu_link_list() ),
      array( 'title'=>'Links',       'menu' => $this->_links_link_list() ),
      array( 'title'=>'Downloads',   'menu' => $this->_downloads_link_list() ),
    );
    $this->data->table('tbl_links');
    return $links;
  }
  
  /**
   * Link lijst van alle uri's in het menu
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _menu_link_list() {
    $this->data->table('tbl_menu');
    $result = $this->data->get_menu_result();
    $parents = array();
    $level = 0;
    foreach ($result as $key => $item) {
      $level = count(explode('/',$key));
      $result[$key]['title'] = str_repeat(" ",$level-1) . $item['str_title'];
      $result[$key]['uri']   = $key;
    }
    return $this->_result_as_link_list($result);
  }
  
  /**
   * Link lijst van alle links uit de tabel tbl_links
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _links_link_list() {
    $this->data->table('tbl_links')->select('url_url AS link, CONCAT(`str_title`," - ",REPLACE(REPLACE(`url_url`,"https://",""),"http://","")) AS title');
    $this->data->where('url_url !=""')->like('url_url','http','after');
    $result = $this->data->cache()->get_result();
    return $this->_result_as_link_list($result);
  }
  
  /**
   * Link lijst van alle download bestanden
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _downloads_link_list() {
    $this->data->table('res_assets')->select('CONCAT_WS("/","_media/download",`path`,`file`) AS link, alt AS title')->where('path','downloads');
    $result = $this->data->cache()->get_result();
    return $this->_result_as_link_list($result);
  }
  

  /**
   * Maakt een link lijst resultaat
   *
   * @param array $result 
   * @return array
   * @author Jan den Besten
   */
  protected function _result_as_link_list($result) {
    $links = array();
    foreach ($result as $id => $item) {
      $links[] = array(
        'title' => $item['title'],
        'value' => el('link',$item,el('uri',$item)),
      );
    }
    return $links;
  }
  
  
  
  /**
   * Voeg actie toe om link te checken (aan row)
   *
   * @param int $limit 
   * @param int $offset 
   * @return $this
   * @author Jan den Besten
   */
  public function get_grid( $limit = 20, $offset = FALSE ) {
    $checked_order = FALSE;
    if (strpos($this->tm_order_by[0],'checked')===0) {
      $checked_order = 'ASC';
      if (strpos($this->tm_order_by[0],'DESC')>0) $checked_order = 'DESC';
      $this->tm_order_by = array();
      $result = parent::get_grid(null,0);
      $this->tm_limit = $limit;
      $this->tm_offset = $offset;
    }
    else {
      $result = parent::get_grid($limit,$offset);
    }

    foreach ($result as $key => $link) {
      $id    = $link['id'];
      $icon  = 'question';
      $class = '';
      $text  = lang('link_check');

      if ($checked_order) $link['_checked'] = 0;
      if ($this->checked_links and isset($this->checked_links[$id])) {
        if ($this->checked_links[$id]['checked']) {
          if ($checked_order) $link['_checked'] = 1;
          $icon  = 'chain';
          $class = 'btn-outline-primary';
          $text  = lang('link_check_ok');
        }
        else {
          if ($checked_order) $link['_checked'] = -1;
          $icon = 'chain-broken'; 
          $class = 'btn-outline-danger';
          $text  = lang('link_check_bad');
        }
      }
      $link = array_add_after($link,'id', array(
        'action_check_link' => array(
          'uri'     => 'link_checker?where='.$id,
          'icon'    => $icon, 
          'text'    => $text,
          'class'   => $class,
          'reload'  => array('offset'=>0,'order'=>'checked'),
        ))
      );

      $result[$key] = $link;
    }

    // Checked order?
    if ($checked_order) {
      function cmp($a,$b) {
        return ($a['_checked'] > $b['_checked']);
      }
      // Query Info aanpassen
      $this->query_info['total_rows']   = count($result);
      $this->query_info['offset']       = $this->tm_offset;
      $this->query_info['limit']        = $this->tm_limit;
      // Sorteren
      uasort($result,'cmp');
      $result = array_slice($result,$this->tm_offset,$this->tm_limit,true);
      $result = array_unset_keys($result,array('_checked'),true);
      // Query Info aanpassen
      $this->query_info['num_rows']   = count($result);
      $this->query_info['page']       = (int) floor($this->tm_offset / $this->tm_limit);
      $this->query_info['num_pages']  = (int) ceil($this->query_info['total_rows'] / $this->tm_limit);
    }
    
    // Voeg het veld toe
    $this->settings['grid_set']['fields'] = array_add_after($this->settings['grid_set']['fields'],'id','action_check_link');
    return $result;
  }
  
  
  /**
   * Voeg linkchecker actie's toe aan head(er)
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_grid_set() {
    $grid_set = parent::get_setting_grid_set();
    $grid_set['field_info']['action_check_link'] = array(
      'type'         => 'action',

      'action'       => array(
        'name_all'    => lang('link_checker_all'),
        'name_select' => lang('link_checker_select'),
        'url'         => 'link_checker',
        'icon'        => 'chain',
        'reload'      => array('offset'=>0,'order'=>'checked'),
      ),

      'name'      => lang('link_checker'),
      'grid-type' => 'action',
      'sortable'  => false,
    );
    
    return $grid_set;
  }


  /**
   * Zorg ervoor dat de cache van checke links wordt bijgewerkt
   */
  protected function _update_insert( $type, $set = NULL, $where = NULL, $limit = NULL ) {
    $id = parent::_update_insert($type, $set, $where, $limit);
    if ($this->checked_links and isset($this->checked_links[$id])) {
      unset($this->checked_links[$id]);
      $this->cache_result($this->checked_links,$this->checked_cache_name,$this->cache_expire);
    }
    return $id;
  }

  public function delete( $where = '', $limit = NULL, $reset_data = TRUE ) {
    $deleted_items = parent::delete($where, $limit, $reset_data);
    if ($deleted_items and $this->checked_links) {
      foreach ($deleted_items as $item) {
        unset($this->checked_links[$item[$this->settings['primary_key']]]);
      }
      $this->cache_result($this->checked_links,$this->checked_cache_name,$this->cache_expire); 
    }
    return $ids;
  }
  

  /**
   * Controleert alle links
   *
   * @return array
   * @author Jan den Besten
   */
  public function check_links($where=false) {

    if (is_array($where)) {
      $this->data->where_in( 'id', $where );
    }
    elseif ($where!==false) {
      $this->data->where( $where );
    }

    $this->select(array('str_title,url_url'));
    $this->like('url_url','http','after');
    $result = $this->get_result();
    foreach ($result as $id => $link) {
      $url = $link['url_url'];
      $result[$id]['checked'] = $this->_check_link( $url );
      if ($this->checked_links) {
        $this->checked_links[$id] = $result[$id];
      }
    }
    if ($this->checked_links) {
      $this->cache_result($this->checked_links,$this->checked_cache_name,$this->cache_expire);
    }
    else {
      $this->cache_result($result,$this->checked_cache_name,$this->cache_expire);
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
    $this->lang->load('ui');
    $good = array();
    $false = array();
    foreach ($this->checked_links as $id => $link) {
      if ($link['checked']) {
        $good[] = '<span class="text-primary"><span class="fa fa-check-square-o"></span>&nbsp;<a href="'.$link['url_url'].'" target="_blank">'.$link['url_url'].'</a></span>';
      }
      else {
        $false[] = '<span class="text-danger"><span class="fa fa-square-o"></span>&nbsp;<a class="text-danger" href="'.$link['url_url'].'" target="_blank">'.$link['url_url'].'</a></span>';
      }
    }
    if (count($this->checked_links)>1) {
      $message = h(langp('link_check_checked',count($this->checked_links)));
    }
    else {
      $message = h(langp('link_check_checked_one'));
    }
    if (count($false)>0) {
      $message .= '<span class="text-danger">'.langp('link_check_checked_false',count($false)).'</span>';
      // $message .= '<hr>';
      // $message .= implode('<br>',$false).'<br>';
    }
    // if (count($good)>0) {
    //   $message .= implode('<br>',$good).'<br>';
    // }
    $message .= '<hr>'.langp('link_check_help');
    return $message;
  }
  
  
  /**
   * Controleer één link
   * https://stackoverflow.com/questions/15770903/check-if-links-are-broken-in-php
   *
   * @author Jan den Besten
   */
  private function _check_link( $url ) {
    // return rand(0,100)>95?false:true;
    $headers = @get_headers( $url);
    $headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;
    return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
  }


  /**
   * Voeg niewe link toe
   */
  public function insert_new_link( $set, $extra=false ) {
    return $this->set($set)->insert();
  }

  
  
}
