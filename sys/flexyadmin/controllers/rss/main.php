<? require_once(APPPATH."controllers/admin/MY_Controller.php");

// - zie http://www.derekallard.com/blog/post/building-an-rss-feed-in-code-igniter/


class Main extends FrontEndController {
	
	function Main()	{
		parent::FrontEndController();
		$this->load->helper('xml');
	}

	function index()	{
		if ($this->db->table_exists('cfg_rss')) {
			$rssCfg=$this->db->get_result('cfg_rss');
			if ($rssCfg) {
				foreach ($rssCfg as $id => $row) {
					foreach ($row as $field => $value) {
						if (get_prefix($field)=='field') $rssCfg[$id][$field]=remove_prefix($value,'.');
					}
				}

				$feeds=array();
				foreach ($rssCfg as $id => $rssInfo) {
					if (!empty($rssInfo['str_where'])) $this->db->where($rssInfo['str_where']);
					$subFeeds=$this->db->get_result($rssInfo['table'],$rssInfo['int_limit']);

					foreach ($subFeeds as $feed) {
						$feeds[]=array( 'title'	=> trim($rssInfo['str_pre_title'].' '.$this->_get_field($feed,$rssInfo['field_title'])),
														'url'		=> trim($rssInfo['str_pre_uri'].'/'.$this->_get_field($feed,$rssInfo['field_uri']),'/'),
														'date'	=> $this->_get_field($feed,$rssInfo['field_date']),
														'body'	=> max_length(strip_tags($this->_get_field($feed,$rssInfo['field_body'])),$rssInfo['int_max_length'])
														);
					}
				}
				$feeds=sort_by($feeds,'date',TRUE);
				
				if ($feeds) {
					$siteInfo=$this->db->get_row('tbl_site');
					$data['encoding'] = 'utf-8';
					$data['feed_name'] = $siteInfo['str_title'];
					$data['feed_url'] = site_url();//$siteInfo['url_url'];
					$data['page_description'] = 'RSS: '.$siteInfo['stx_description'];
					$data['page_language'] = 'nl'; //$config['language'].'_'.$config['language'];
					$data['creator_email'] = str_replace(array('@','.'),array('-at-','-dot-'),$siteInfo['email_email']);
					
					// form dates
					foreach ($feeds as $id => $feed) {
						$feeds[$id]['date']=date("r",mysql_to_unix($feed['date']));
					}
					$data['posts'] = $feeds;

					header("Content-Type: application/rss+xml");
					$this->load->view('rss/feed', $data);
				}
				
				
			}
		}
	}
	
	function _get_field($feed,$cfg) {
		if (!empty($feed[$cfg])) return $feed[$cfg];
		return '';
	}

}




?>