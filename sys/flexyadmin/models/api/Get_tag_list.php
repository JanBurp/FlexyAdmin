<?php

/**
 * API: Geeft een lijst van tags
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Get_tag_list extends Api_Model {
  
  /**
   */
	public function __construct($name='') {
    parent::__construct();
    return $this;
  }
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();
    
    $this->data->table('tbl_tags');
    $tags = $this->data->get_tag_list($this->args['tag']);

    header("Content-Type: application/json");
    if ($tags) {
      return json_encode($tags);
    }

    return "{}";
  }

}


?>
