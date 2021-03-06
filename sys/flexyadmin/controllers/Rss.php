<?php require_once(APPPATH."core/FrontendController.php");

// - zie http://www.derekallard.com/blog/post/building-an-rss-feed-in-code-igniter/


class Rss extends FrontEndController {
	
	public function __construct()	{
		parent::__construct();
    $this->load->helper('xml');
	}

  /**
   * Maakt een RSS feed van de data die een RSS_model teruggeeft
   *
   * @param string $args [null]
   * @return void
   * @author Jan den Besten
   */
  public function index($args=null)	{
    $model=$this->config->item('rss_model');
    $this->load->model($model,'rss');
    
    $data=$this->rss->index($args);
    $feed=array();
		foreach ($data as $row) {
      if (isset($row['uri']) and isset($row['str_title'])) {
        $post=array();
        $post['url']   = $row['uri'];
        $post['title'] = preg_replace('/[\x00-\x09\x0B-\x1F\x7F]/u', '', ascii_to_entities($row['str_title']) );
        $post['date']  = date('r');
        $post['body']  = '';
        if (isset($row['dat_date'])) $post['date']  = date(DATE_ATOM, date_to_unix( $row['dat_date']));
        if (isset($row['txt_text'])) $post['body']  = preg_replace('/[\x00-\x09\x0B-\x1F\x7F]/u', '', ascii_to_entities(character_limiter( strip_tags($row['txt_text']), 250)) );
        $feed[]=$post;
      }
		}
    
		$siteInfo = $this->data->table('tbl_site')->get_row();
    $data = array();
    $data['encoding']         = 'utf-8';
    $data['feed_name']        = $siteInfo['str_title'];
    $data['feed_url']         = site_url();
    $data['updated']          = date(DATE_ATOM);
    $data['page_description'] = 'RSS: '.$siteInfo['stx_description'];
    $data['page_language']    = $this->site['language'];
    $data['creator_email']    = str_replace(array('@','.'),array('-at-','-dot-'),$siteInfo['email_email']);
    $data['author']           = $siteInfo['email_email'].' ('.$siteInfo['str_author'].')';
    $data['posts']            = $feed;

		header("Content-Type: application/rss+xml");
		$this->load->view('rss/feed', $data);
  }
  
}




?>