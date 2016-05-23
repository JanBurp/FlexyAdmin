<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/** \ingroup plugins
 * Met deze plugin kun je logfiles (deels) bekijken.
 * Het grote voordeel, ten opzichte van een logfile in z'n geheel bekijken is:
 * 
 * - Je kunt de logfile filteren op tijd: een dagdeel of een uur. Alleen entries in dat tijdsbestek worden getoond.
 * - Je kunt een filter term meegeven. Alleen log entries waar de term in voorkomt worden getoond.
 * 
 * ##Logging
 * 
 * Er wordt gebruik gemaakt van de logbestanden van CodeIgniter, die aangevuld zijn met FlexyAdmin en eventueel eigen entries.
 * Zie [CodeIgniter Error handling](http://ellislab.com/codeigniter/user-guide/general/errors.html) bij `log_message()`.
 * 
 * - Standaard worden logfiles in de map site/cache geplaatst. Deze moet schrijfbaar zijn.
 * - Logging staat standaard uit en kun je aanzetten door:
 *    - in de index.php deze code te uncommenten: `define('ENVIRONMENT','testing');`
 *    - of in site/config/config.php de `log_threshold` in te stellen op 4: `$config['log_threshold'] = 4;`
 *
 * @author Jan den Besten
 */

class Plugin_log extends Plugin {

  private $search='';
  private $from='';
  private $start='';
  private $end='';

  /**
   * @author Jan den Besten
   * @internal
   */
	public function __construct() {
		parent::__construct();
	}

  /**
   * Log activity on homepage
   *
   * @return void
   * @author Jan den Besten
   */
  public function _home() {
    $this->CI->load->model('log_activity');
    $this->CI->log_activity->clean_up();
    
    $log = $this->CI->log_activity->get_grouped_user_activity();
		$grid=new grid();
		foreach($log as $k=>$d) {
      $log[$k]['id_user']       = $this->CI->db->get_field_where('cfg_users','str_username','id',$d['id_user']);
		}
		$grid->set_data($log,langp("home_activity"));
    $grid->set_headings(array(
      'id_user'       => lang('home_user'),
      'tme_timestamp' => lang('home_date'),
      'str_model'     => lang('home_changes'),
    ));
		$renderGrid=$grid->render("html","","grid home");
    return $this->CI->load->view("admin/grid",$renderGrid,true) ;
  }
  
  
  

  /**
   * @author Jan den Besten
   * @internal
   */
	public function _admin_api() {
		if ($this->CI->flexy_auth->is_super_admin()) {
			$this->CI->load->library("form");

			// list logfiles
			$files=read_map($this->CI->config->item('log_path'),'php');
			$options=array();
			foreach ($files as $file=>$value) {
				$options[$file]=$file;
			}
      arsort($options);
			$file=current($options);
      $fromOpts=array(
        '00:00 - 23:59' => 'Whole day (00:00 - 23:59)',
        '00:00 - 05:59' => 'Night     (00:00 - 05:59)',
        '06:00 - 11:59' => 'Morning   (06:00 - 11:59)',
        '12:00 - 17:59' => 'Afternoon (12:00 - 17:59)',
        '18:00 - 23:59' => 'Evening   (18:00 - 23:59)'
      );
			$fromOptions=range(0,23);
			foreach ($fromOptions as $key) {
				$key=sprintf('%02d:00',$key).' - '.sprintf('%02d:59',$key);
				$fromOpts[$key]=$key;
			}
			$data=array( 	"logfiles"	=> array("label"=>'Logfiles','type'=>'dropdown','options'=>$options,'value'=>$file),
										"from"			=> array('label'=>'Timewindow','type'=>'dropdown','options'=>$fromOpts),
										"search"		=> array('label'=>'Filter'));
			$form=new form();
			$form->set_data($data,'Logfiles');

			$file=$this->CI->input->post('logfiles');
			$this->from=$this->CI->input->post('from');
			$this->search=$this->CI->input->post('search');
			$data['logfiles']['value']=$file;
			$data['from']['value']=$this->from;
			$data['search']['value']=$this->search;
      
      // Set params for reading and filtering file
			if (!empty($this->from)) {
        $this->start=substr($this->from,0,5);
        $this->end=substr($this->from,8,5);
      }
      
      // Read and filter logfile
			$currentLog=read_file_filter( $this->CI->config->item('log_path').$file, array($this,'_log_filter')  );
      
			$this->add_message($form->render());
			$this->add_message(div('after_form').h($file,1).'<pre>'.$currentLog.'<pre>'._div());
		}
		return $this->view('admin/plugins/plugin');
	}
  
  
  /**
   * Deze functie wordt meegegeven aan read_file_filter() om een regel te testen
   * 
   * @author Jan den Besten
   * @internal
   */
  public function _log_filter($line) {
    if (!empty($this->search)) {
      if (strpos($line, $this->search)===FALSE) return FALSE;
    }
    if (!empty($this->from)) {
      $time=substr($line,19,5);
      if ($time<$this->start or $time>$this->end) return FALSE;
    }
    return TRUE;
  }


}

?>
