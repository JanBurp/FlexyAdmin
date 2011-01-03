<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class plugin_sitemap extends plugin_ {

	function init($init=array()) {
		parent::init($init);
	}
	
	function _admin_logout() {
		$this->_create_sitemap();
	}
	
	
	function _admin_api($args=NULL) {
		$this->CI->_add_content(h($this->plugin,1));
		$this->_create_sitemap();
	}


	function _create_sitemap() {
		if ($this->CI->db->table_exists('res_menu_result'))
			$menuTable='res_menu_result';
		else
			$menuTable=$this->CI->cfg->get('cfg_configurations','str_menu_table');

		// create Sitemap
		$url=trim($this->CI->db->get_field('tbl_site','url_url'),'/');
		if ($this->CI->db->field_exists('self_parent',$menuTable)) $this->CI->db->uri_as_full_uri();
		$menu=$this->CI->db->get_result($menuTable);
		$urlset=array();
		foreach ($menu as $id => $item) {
			$set=array();
			$set['loc']=$url.'/'.htmlentities($item['uri']);
			if (isset($item['str_title'])) $set['title']=$item['str_title'];
			if (isset($item['txt_text'])) $set['content']=htmlentities(replace_linefeeds(strip_nonascii(strip_tags(str_replace('<br />',' ',$item['txt_text'])))),ENT_QUOTES);
			$urlset[]=$set;
		}
		$sitemap['urlset']=$urlset;
		
		// create XML and save it
		$XML=array2XML($sitemap,array('urlset','url'),array('urlset'=>array('xmlns'=>"http://www.sitemaps.org/schemas/sitemap/0.9")));
		$err=write_file('sitemap.xml', $XML);
		
		if ($err) {
			$this->CI->_add_content('<p>sitemap.xml created</p>');
			$this->_create_robots();
		}
		else
			$this->CI->_add_content('<p>could not create sitemap.xml: '.$err.'</p>');
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
			$this->CI->_add_content('<p>robots.txt created</p>');
		else
			$this->CI->_add_content('<p>could not create robots.txt: '.$err.'</p>');
	}
	
}

?>