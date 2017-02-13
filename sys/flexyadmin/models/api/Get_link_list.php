<?php

/**
 * API: Geeft een lijst van links voor de TinyMCE editor
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Get_link_list extends Api_Model {
  
  /**
   */
	public function __construct($name='') {
		parent::__construct(TRUE,TRUE);
    return $this;
	}
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();

    $site_links = $this->data->table('tbl_site')->select('str_title,url_url,email_email')->cache()->get_row();
    
    $links = array(
      array( 'title'=>'Algemeen',    'menu' => array(
        array( 'title'=>$site_links['str_title'].' ('.str_replace(array('http://','https://'),'',$site_links['url_url']).')',   'value' => $site_links['url_url'] ),
        array( 'title'=>$site_links['email_email']. ' ('.$site_links['email_email'].')', 'value' => 'mailto:'.$site_links['email_email'] ),
      )),
      array( 'title'=>'Pagina\'s',   'menu' => $this->_menu() ),
      array( 'title'=>'Links',       'menu' => $this->_links() ),
      array( 'title'=>'Downloads',   'menu' => $this->_downloads() ),
    );
    header("Content-Type: application/json");
    return json_encode($links);
  }
  
  private function _menu() {
    $this->data->table('tbl_menu')->select('uri,str_title AS title,order,self_parent')->tree('uri');
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
      $result[$key]['title'] = str_repeat("-",$level-1) . $item['title'];
    }
    return $this->_result_as_links($result);
  }
  
  private function _links() {
    $this->data->table('tbl_links')->select('url_url AS link, CONCAT(`str_title`," (",`url_url`,")") AS title');
    $result = $this->data->cache()->get_result();
    return $this->_result_as_links($result);
  }
  
  private function _downloads() {
    $this->data->table('res_assets')->select('CONCAT_WS("/","_media/download",`path`,`file`) AS link, alt AS title')->where('path','downloads');
    $result = $this->data->cache()->get_result();
    return $this->_result_as_links($result);
  }
  
  private function _result_as_links($result) {
    $links = array();
    foreach ($result as $id => $item) {
      $links[] = array(
        'title' => $item['title'],
        'value' => el('link',$item,el('uri',$item)),
      );
    }
    return $links;
  }

}


?>
