<?php 


/**
 * Eenvoudige blog module
 **/
class Blog extends Module {

  public function __construct() {
    parent::__construct();
  }

  public function index($page) {
    $this->CI->set_page_view('blog');
    $items = $this->CI->data->table('tbl_blog')->get_result();
    $page['items'] = $items;
    return $page;
  }

}

?>