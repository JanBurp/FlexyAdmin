<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /** \ingroup libraries
  * Uitbreiding op [CI_Pagination](http://codeigniter.com/user_guide/libraries/pagination.html)
  * 
  * ##Dit zijn de uitbreidingen op CodeIgniter:
  * 
  * - tags hebben een mooiere templates die te vinden zijn in SITEPATH.config/pagination.php
  * - extra tags: _total_tag_open_ en _total_tag_close_ waarin het totaal aantal items terecht komt. Als deze templates leeg zijn wordt dit niet getoond.
  * - automatisch pagination met in de uri een 'offset' deel waarachter de start van de volgende pagina komt.
  * 
  * ## Snelle manier om pagination in een eigen module toe te passen
  * 
  * 1. In SITEPATH.'config/config.php' de volgende instelling de waarde TRUE geven:
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
  *         $items = $this->CI->data->table('tbl_links')->get_result( $per_page, $offset );
  *         $total_rows = $this->CI->data->total_rows();
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
  * @author: Jan den Besten
   * @copyright: (c) Jan den Besten
  */
class MY_Pagination extends CI_Pagination {

  /**
   * Is auto pagination aan, default=FALSE
   */
  var $auto = FALSE;

  /**
   * uripart wat wordt gebruikt voor auto pagination, default='offset'
   */
  var $auto_uripart = 'offset';

  /**
   * Extra tags
   */
  protected $total_tag_open  = '<span class="pagination_total">';
  protected $total_tag_close = '</span>';

  /**
   * @param string $params 
   * @author Jan den Besten
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
    if (!empty($this->total_tag_open) and !empty($this->total_tag_close)) $output.=$this->total_tag_open.$this->total_rows.$this->total_tag_close;
		return $output;
	}
	
  /**
   * Stelt auto-pagination in
   *
   * @param string $auto default=TRUE
   * @param string $part default='offset'
   * @return void
   * @author Jan den Besten
   */
	public function auto($auto=true,$part='offset') {
		$this->auto_uripart=$part;
		$this->auto=$auto;
    return $this;
	}
	
  /**
   * _auto_set()
   *
   * @return void
   * @author Jan den Besten
   * @internal
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
