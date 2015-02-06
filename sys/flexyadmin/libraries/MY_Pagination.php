<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
  * Uitbreiding op [CI_Pagination](http://codeigniter.com/user_guide/libraries/pagination.html)
  * 
  * ##Dit zijn de uitbreidingen op CodeIgniter:
  * 
  * - tags hebben een mooiere default config (list-items)
  * - extra tags: _total_tag_open_ en _total_tag_close_ waarin het totaal aantal items terecht komt
  * - automatisch pagination met in de uri een 'offset' deel waarachter de start van de volgende pagina komt.
  * 
  * ## Snelle manier om pagination in een eigen module toe te passen
  * 
  * 1. In 'site/config/config.php' de volgende instelling de waarde TRUE geven:
  * 
  *         $config['auto_pagination']	= TRUE;
  * 
  * 2. In je module de volgende code plaatsen om de pagination library te laden en 'offset' uit de URI te halen:
  * 
  *         $this->CI->load->library('pagination');
  *         $offset = $this->CI->uri->get_pagination();           // $offset is het item waarmee je lijst begint
  *         $per_page = 10;                                       // Het aantal items per pagina
  * 
  * 3. Je items, een voorbeeld
  * 
  *         $items = $this->CI->db->get_result( 'tbl_links', $per_page, $offset );
  *         $total_rows = $this->CI->db->last_num_rows_no_limit();
  * 
  * 4. In je module de pagination links genereren:
  * 
  *         $config['total_rows'] = $total_rows;                  // Totaal aantal items
  *         $config['per_page'] = $per_page;                      // Aantal items per pagina
  *         $this->CI->pagination->initialize($config);
  *         $this->CI->pagination->auto(); 
  *         $pagination = $this->CI->pagination->create_links();  // $pagination bevat nu de HTML met pagination links
  * 
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
  var $num_tag_open    = '<li class="pager"><span class="btn btn-default">';
  
  /**
   * num_tag_close   = '&lt;/li&gt;'
   *
   * @var string
   */
	var $num_tag_close   = '</span></li>';

  /**
   * cur_tag_open    = '&lt;li class=&quot;current&quot;&gt;'
   *
   * @var string
   */
  var $cur_tag_open    = '<li class="active"><span class="btn btn-primary">';
  
  /**
   * cur_tag_close   = '&lt;/li&gt;'
   *
   * @var string
   */
	var $cur_tag_close   = '</span></li>';
  
  /**
   * first_tag_open  = '&lt;li class=&quot;pager pagination_first&quot;&gt;'
   *
   * @var string
   */
	var $first_tag_open  = '<li class="pager pagination_first"><span class="btn btn-default">';
  
  /**
   * first_tag_close = '&lt;/li&gt;'
   *
   * @var string
   */
	var $first_tag_close = '</span></li>';
  
  /**
   * last_tag_open   = '&lt;li class=&quot;pager pagination_last&quot;&gt;'
   *
   * @var string
   */
  var $last_tag_open   = '<li class="pager pagination_last"><span class="btn btn-default">';
	
  /**
   * last_tag_close  = '&lt;/li&gt;'
   *
   * @var string
   */
  var $last_tag_close  = '</span></li>';
  
  /**
   * prev_tag_open   = '&lt;li class=&quot;pager pagination_prev&quot;&gt;'
   *
   * @var string
   */
  var $prev_tag_open   = '<li class="pager pagination_prev"><span class="btn btn-default">';
  
  /**
   * prev_tag_close  = '&lt;/li&gt;'
   *
   * @var string
   */
	var $prev_tag_close  = '</span></li>';

  /**
   * next_tag_open   = '&lt;li class=&quot;pager pagination_next&quot;&gt;'
   *
   * @var string
   */
  var $next_tag_open   = '<li class="pager pagination_next"><span class="btn btn-default">';
  
  /**
   * next_tag_close  = '&lt;/li&gt;'
   *
   * @var string
   */
	var $next_tag_close  = '</span></li>';
	
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
  	$CI =& get_instance();
		// auto pagination?
		if ($this->auto) {
			$this->_auto_set();
		}
		// go on with normal method
		$output = parent::create_links();
    $output = str_replace($CI->config->item('url_suffix'),'',$output);

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
