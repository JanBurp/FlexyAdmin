<?php

/**
 * API: Geeft een lijst van links voor de TinyMCE editor
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Get_image_list extends Api_Model {
  
  /**
   */
	public function __construct($name='') {
		parent::__construct(TRUE,TRUE);
    return $this;
	}
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();

    // $images = array(
    //   array( 'title'=>'Afbeeldingen',  'menu' => $this->_folder('pictures') ),
    // );
    $images = $this->_images( 'pictures' );
    header("Content-Type: application/json");
    return json_encode($images);
  }
  
  private function _images($path) {
    $filters = array(
      array( 'title' => 'Vandaag toegevoegd',         'order' => 'date', 'filter' => ' `date` > SUBDATE( CURDATE(), INTERVAL 1 DAY ) ' ),
      array( 'title' => 'Afgelopen week toegevoegd',  'order' => 'date', 'filter' => ' `date` > SUBDATE( CURDATE(), INTERVAL 7 DAY ) AND `date` < SUBDATE( CURDATE(), INTERVAL 1 DAY )' ),
      array( 'title' => 'Op datum',                   'order' => 'date', 'filter' => '' ),
      array( 'title' => 'Op alfabet',                 'order' => 'alt',  'filter' => '' ),
    );
    
    $images = array();
    foreach ($filters as $filter) {
      $items = $this->_folder($path,$filter['filter'],$filter['order']);
      if ($items) {
        $images[] = array( 'title'=>$filter['title'], 'menu'=>$items );
      }
    }
    return $images;
  }
  
  private function _folder($path,$filter='',$order='') {
    $this->data->table('res_assets')->select('CONCAT_WS("/","_media/'.$path.'",`file`) AS link, CONCAT(`alt`," (",`file`,")") AS title')->where('path',$path);
    if ($filter) $this->data->where($filter);
    if ($order) $this->data->order_by($order);
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
