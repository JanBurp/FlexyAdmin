<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Maakt sitemap.xml aan
 *
 * @author Jan den Besten
 * @internal
 */

class Plugin_sitemap extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	public function _admin_logout() {
    if (!$this->CI->config->item('testmode')) {
  		$this->_create_sitemap();
    }
	}

	public function _admin_api($args=NULL) {
		if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;
		$this->_create_sitemap();
    return $this->show_messages();
	}


	private function _create_sitemap() {
		// create Sitemap
    $this->CI->data->table('tbl_site');
		$url = trim($this->CI->data->get_field('url_url'),'/');

    $this->CI->data->table('tbl_menu');
		$menu = $this->CI->data->get_menu_result();

		$urlset     = array();
		$pageCount  = count($menu);
    $maxlines = 5000-($pageCount*10);
    if ($maxlines<=3) $maxlines=3;
    $first = true;
    $disallowedUrls = array();
		foreach ($menu as $id => $item) {
      if ( !isset($item['b_disallow']) or !$item['b_disallow'] ) {
          $set = array();
          // loc
          $uri = el('full_uri',$item,el('uri',$item));
          $set['loc'] = $url.'/'.htmlentities($uri);
          // priority
          $level    = substr_count($uri,'/');
          $priority = (.8-$level*0.16);
          if ($priority<.16) $priority=.16;
          if ($first) $priority=1;
          $set['priority'] = str_replace(',','.',sprintf('%1.2f',$priority));
          $first           = false;
          // title & content
          if (isset($item['str_title'])) $set['title'] = $item['str_title'];
          if (isset($item['txt_text'])) {
            $set['content'] = preg_replace('/\s\s+/si',' ',htmlentities(replace_linefeeds(strip_nonascii(strip_tags(str_replace(array('<br />','&nbsp;'),' ',$item['txt_text'])))),ENT_QUOTES));
            // prevent very big sitemap.xml
            if ($maxlines==0)
              unset($set['content']);
            else
              $set['content']=max_length($set['content'],$maxlines);
          }
          $urlset[] = $set;
      }
      else {
        if ($item['full_uri']) {
          $disallowedUrls[] = '/'.$item['full_uri'];
        }
      }
		}
		$sitemap['urlset'] = $urlset;

		// create XML and save it
		$XML=array2XML($sitemap,array('urlset','url'),array('urlset'=>array('xmlns'=>"http://www.sitemaps.org/schemas/sitemap/0.9", 'xmlns:xsi'=>"http://www.w3.org/2001/XMLSchema-instance", 'xsi:schemaLocation'=>"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" )));
		$err=write_file('sitemap.xml', $XML);

		if ($err) {
			$this->add_message('<p>sitemap.xml created</p>');
			$this->_create_robots($disallowedUrls);
		}
		else {
			$this->add_message('<p>Could not create sitemap.xml, probably problem with rights: '.$err.'</p>');
    }
	}

	private function _create_robots( $disallowedUrls=array() ) {
		$robots=file_get_contents('robots.txt');

		$url = $this->CI->data->table('tbl_site')->get_field('url_url');
		$newSitemapLine='Sitemap: '.$url.'/sitemap.xml'.PHP_EOL;
		if (strpos($robots,'Sitemap')!==false) {
			// Replace old Sitemap (and extra Disallowed) line with new
			$robots = preg_replace('/Sitemap\:(.*)/s',$newSitemapLine,$robots);
		}
		else {
			// add Sitemap
			$robots.=$newSitemapLine;
		}

    // add Hidden URL's
    foreach ($disallowedUrls as $url) {
      $robots.='Disallow: ' . $url . PHP_EOL;
    }

		// write file
		$err=write_file('robots.txt', $robots);
		if ($err)
			$this->add_message('<p>robots.txt created</p>');
		else
			$this->add_message('<p>could not create robots.txt: '.$err.'</p>');
	}

}

?>
