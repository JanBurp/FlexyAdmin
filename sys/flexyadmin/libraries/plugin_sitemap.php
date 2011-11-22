<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Plugin_sitemap extends Plugin_ {

	function _admin_logout() {
		$this->add_content(h($this->name,1));
		$this->_create_sitemap();
		return true;
	}
	
	
	function _admin_api($args=NULL) {
		$this->add_content(h($this->name,1));
		$this->_create_sitemap();
	}


	function _create_sitemap() {
		$menuTable=get_menu_table();

		// create Sitemap
		$url=trim($this->CI->db->get_field('tbl_site','url_url'),'/');
		if ($this->CI->db->field_exists('self_parent',$menuTable)) $this->CI->db->uri_as_full_uri();
		$this->CI->db->order_as_tree();
		$menu=$this->CI->db->get_result($menuTable);
		$urlset=array();
		$pageCount=count($menu);
		foreach ($menu as $id => $item) {
			$set=array();
			$set['loc']=$url.'/'.htmlentities($item['uri']);
			if (isset($item['str_title'])) $set['title']=$item['str_title'];
			if (isset($item['txt_text'])) $set['content']=preg_replace('/\s\s+/si',' ',htmlentities(replace_linefeeds(strip_nonascii(strip_tags(str_replace(array('<br />','&nbsp;'),' ',$item['txt_text'])))),ENT_QUOTES));
			// prevent very big sitemap.xml
			if ($pageCount>500) $set['content']=max_length($set['content'],1000);
			if ($pageCount>5000) $set['content']=max_length($set['content'],250);
			if ($pageCount>10000) unset($set['content']);
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
		else
			$this->add_content('<p>could not create sitemap.xml: '.$err.'</p>');
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