<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * tbl_links
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Core_tbl_links extends Data_Core {
  
  private $checked_links = array();

  public function __construct() {
    parent::__construct();
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
    $this->data->table('tbl_menu')->select('uri,str_title AS title,order,self_parent')->tree('uri');
    $this->data->where('uri !=""');
    $result = $this->data->cache()->get_result();
    $parents = array();
    $level = 0;
    foreach ($result as $key => $item) {
      if ($item['self_parent']!==0) {
        if (isset($parents[$item['self_parent']])) {
          $level = $parents[$item['self_parent']];
        }
        else {
          $level++;
          $parents[$item['self_parent']] = $level;
        }
      }
      $result[$key]['title'] = str_repeat(" ",$level-1) . $item['title'];
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
    $this->lang->load('ui');
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
    if (count($this->checked_links)>1) {
      $message = langp('link_check_checked',count($this->checked_links)) . '<br>';
    }
    else {
      $message = langp('link_check_checked_one') . '<br>';
    }
    if (count($false)>0) {
      $message_type = 'danger';
      $message .= '<span class="text-danger">'.langp('link_check_checked_false',count($false));
      $message .= '<hr>';
      $message .= implode('<br>',$false).'<br>';
    }
    if (count($good)>0) {
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
