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
      array( 'title'=>$site_links['str_title'],   'value' => $site_links['url_url'] ),
      array( 'title'=>$site_links['email_email'], 'value' => 'mailto:'.$site_links['email_email'] ),
      
      array( 'title'=>'Menu',        'menu' => $this->_menu() ),
      array( 'title'=>'Links',       'menu' => $this->_links() ),
      array( 'title'=>'Downloads',   'menu' => $this->_downloads() ),
    );
    return json_encode($links);
  }
  
  private function _menu() {
    $this->data->table('tbl_menu')->select('uri,str_title AS title,order,self_parent')->path('uri');
    $result = $this->data->cache()->get_result();
    return $this->_result_as_links($result);
  }
  
  private function _links() {
    $this->data->table('tbl_links')->select('url_url AS link,str_title AS title');
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
