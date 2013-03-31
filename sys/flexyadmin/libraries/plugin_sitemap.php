<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Creates sitemap.xml
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 * @internal
 */

class Plugin_sitemap extends Plugin {

	public function __construct() {
		parent::__construct();
	}


	function _admin_logout() {
		$this->_create_sitemap();
	}
	
	function _admin_api($args=NULL) {
		$this->add_content('<h1>Sitemap</h1>');
		$this->_create_sitemap();
    return $this->content;
	}


	function _create_sitemap() {
		$menuTable=get_menu_table();

		// create Sitemap
		$url=trim($this->CI->db->get_field('tbl_site','url_url'),'/');
		if ($this->CI->db->field_exists('self_parent',$menuTable)) $this->CI->db->uri_as_full_uri();
    if ($this->CI->db->field_exists('b_restricted',$menuTable)) $this->CI->db->where('b_restricted',false);
    if ($this->CI->db->field_exists('b_visible',$menuTable)) $this->CI->db->where('b_visible',true);
    $this->CI->db->order_as_tree();
		$menu=$this->CI->db->get_result($menuTable);

		$urlset=array();
		$pageCount=count($menu);
    $maxlines=5000-($pageCount*10);
    if ($maxlines<=3) $maxlines=3;
		foreach ($menu as $id => $item) {
			$set=array();
			$set['loc']=$url.'/'.htmlentities($item['uri']);
			if (isset($item['str_title'])) $set['title']=$item['str_title'];
			if (isset($item['txt_text'])) {
        $set['content']=preg_replace('/\s\s+/si',' ',htmlentities(replace_linefeeds(strip_nonascii(strip_tags(str_replace(array('<br />','&nbsp;'),' ',$item['txt_text'])))),ENT_QUOTES));
  			// prevent very big sitemap.xml
        if ($maxlines==0)
          unset($set['content']);
        else
  			  $set['content']=max_length($set['content'],$maxlines);
			}
			$urlset[]=$set;
		}
		$sitemap['urlset']=$urlset;
		
		// create XML and save it
		$XML=array2XML($sitemap,array('urlset','url'),array('urlset'=>array('xmlns'=>"http://www.sitemaps.org/schemas/sitemap/0.9", 'xmlns:xsi'=>"http://www.w3.org/2001/XMLSchema-instance", 'xsi:schemaLocation'=>"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" )));
		$err=write_file('sitemap.xml', $XML);
		
		if ($err) {
			$this->add_content('<p>sitemap.xml created</p>');
			$this->_create_robots();
		}
		else {
			$this->add_content('<p>could not create sitemap.xml: '.$err.'</p>');
    }
	}
	
	function _create_robots() {
		$robots=read_file('robots.txt');
		$url=$this->CI->db->get_field('tbl_site','url_url');
		$newSitemapLine='Sitemap: '.$url.'/sitemap.xml';
		if (strpos($robots,'Sitemap')!==false) {
			// Replace old Sitemap line with new
			$robots=preg_replace('/sitemap(.*)\w/i',$newSitemapLine,$robots);
		}
		else {
			// add Sitemap
			$robots.=$newSitemapLine;
		}
		// write file
		$err=write_file('robots.txt', $robots);
		if ($err)
			$this->add_content('<p>robots.txt created</p>');
		else
			$this->add_content('<p>could not create robots.txt: '.$err.'</p>');
	}
	
}

?>