<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
  * Uitbreiding op [CI_Pagination](http://codeigniter.com/user_guide/libraries/pagination.html)
  * 
  * Dit zijn de uitbreidingen:
  * 
  * - tags hebben een mooiere default config (list-items)
  * - extra tags: _total_tag_open_ en _total_tag_close_ waarin het totaal aantal items terecht komt
  * - automatisch pagination met in de uri een 'offset' deel waarachter de start van de volgende pagina komt.
  *
  * @package default
  * @author Jan den Besten
  */
class MY_Pagination extends CI_Pagination {

  /**
   * Is auto pagination aan, default=FALSE
   *
   * @var bool
   * @ignore
   */
	var $auto = FALSE;
  
  /**
   * uripart wat wordt gebruikt voor auto pagination, default='offset'
   *
   * @var string
   * @ignore
   */
	var $auto_uripart = 'offset';

  /**
   * Nieuwe tag: total_tag_open = '&lt;span class=&quot;pagination_total&quot;&gt;'
   *
   * @var string
   **/
	var $total_tag_open  = '<span class="pagination_total">';

  /**
   * Nieuwe tag: total_tag_close = '&lt;/span&gt;'
   *
   * @var string
   */
	var $total_tag_close = '</span>';
  
  /**
   * first_link      = '&lt;&lt;'
   *
   * @var string
   */
	var $first_link      = '&lt;&lt;';

  /**
   * last_link       = '&gt;&gt;'
   *
   * @var string
   */
	var $last_link       = '&gt;&gt;';

  /**
   * full_tag_open  = '&lt;ul&gt;'
   *
   * @var string
   */
	var $full_tag_open  = '<ul>';

  /**
   * full_tag_close  = '&lt;/ul&gt;'
   *
   * @var string
   */
	var $full_tag_close  = '</ul>';
	
  /**
   * num_tag_open    = '&lt;li class=&quot;pager&quot;&gt;'
   *
   * @var string
   */
  var $num_tag_open    = '<li class="pager">';
  
  /**
   * num_tag_close   = '&lt;/li&gt;'
   *
   * @var string
   */
	var $num_tag_close   = '</li>';

  /**
   * cur_tag_open    = '&lt;li class=&quot;current&quot;&gt;'
   *
   * @var string
   */
  var $cur_tag_open    = '<li class="current">';
  
  /**
   * cur_tag_close   = '&lt;/li&gt;'
   *
   * @var string
   */
	var $cur_tag_close   = '</li>';
  
  /**
   * first_tag_open  = '&lt;li class=&quot;pager pagination_first&quot;&gt;'
   *
   * @var string
   */
	var $first_tag_open  = '<li class="pager pagination_first">';
  
  /**
   * first_tag_close = '&lt;/li&gt;'
   *
   * @var string
   */
	var $first_tag_close = '</li>';
  
  /**
   * last_tag_open   = '&lt;li class=&quot;pager pagination_last&quot;&gt;'
   *
   * @var string
   */
  var $last_tag_open   = '<li class="pager pagination_last">';
	
  /**
   * last_tag_close  = '&lt;/li&gt;'
   *
   * @var string
   */
  var $last_tag_close  = '</li>';
  
  /**
   * prev_tag_open   = '&lt;li class=&quot;pager pagination_prev&quot;&gt;'
   *
   * @var string
   */
  var $prev_tag_open   = '<li class="pager pagination_prev">';
  
  /**
   * prev_tag_close  = '&lt;/li&gt;'
   *
   * @var string
   */
	var $prev_tag_close  = '</li>';

  /**
   * next_tag_open   = '&lt;li class=&quot;pager pagination_next&quot;&gt;'
   *
   * @var string
   */
  var $next_tag_open   = '<li class="pager pagination_next">';
  
  /**
   * next_tag_close  = '&lt;/li&gt;'
   *
   * @var string
   */
	var $next_tag_close  = '</li>';
	
  /**
   * @param string $params 
   * @author Jan den Besten
   * @ignore
   */
	public function __construct($params = array()) {
		parent::__construct($params);
	}

  /**
   * Zelfde als originele method, maar creert ook nog de total tag en kan ook werken met auto pagination
   *
   * @return string
   * @author Jan den Besten
   */
	public function create_links() 	{
		// auto pagination?
		if ($this->auto) {
			$this->_auto_set();
		}
		// go on with normal method
		$output = parent::create_links();
		// voorkom lege uri
		$output=str_replace('/'.$this->auto_uripart.'/"','/'.$this->auto_uripart.'/0"',$output);
		// extra info
		$output.=$this->total_tag_open.$this->total_rows.$this->total_tag_close;
		return $output;
	}
	
  /**
   * Stelt auto-pagination in
   *
   * @param string $auto[TRUE]
   * @param string $part['offset']
   * @return void
   * @author Jan den Besten
   */
	public function auto($auto=true,$part='offset') {
		$this->auto_uripart=$part;
		$this->auto=$auto;
	}
	
  /**
   * _auto_set()
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _auto_set() {
		$CI=&get_instance();
		$uri=$CI->uri->segment_array();
		// find segment and base_url
		$segment=array_search($this->auto_uripart,$uri);
		if ( ! $segment) $segment=count($uri)+1;
		// create base_url
		$base_url=array_slice($uri,0,$segment-1);
		$base_url=site_url(implode('/',$base_url).'/'.$this->auto_uripart);
		// we need the uri part after the auto_uripart
		$segment++; 
		$this->uri_segment=$segment;
		$this->base_url=$base_url;
		return true;
	}
	
	
}

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */
