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

    // $images = $this->_folder('pictures');
    $images = array(
      array( 'title'=>'Afbeeldingen',  'menu' => $this->_folder('pictures') ),
    );
    header("Content-Type: application/json");
    return json_encode($images);
  }
  
  private function _folder($path) {
    $this->data->table('res_assets')->select('CONCAT_WS("/","_media/'.$path.'",`file`) AS link, CONCAT(`alt`," (",`file`,")") AS title')->where('path',$path);
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
