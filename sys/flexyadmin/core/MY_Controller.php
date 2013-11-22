<?

/**
 * MY_Controller Class
 *
 * This Controller Class handles authentication, loading basic data class
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 * @ignore
 * @internal
 */

class MY_Controller extends CI_Controller {


	function __construct($isAdmin=false) {
		parent::__construct();
		
		if ($this->_check_if_flexy_database_exists())
			$this->_init_flexy_admin($isAdmin);
		else {
			// database login correct, but no database found, try to load the demodatabase
			$succes=false;
			// try to load latest demodatabase
			if (file_exists('db')) {
				$demoDB=read_map('db','sql',FALSE,FALSE);
				$demoDB=filter_by($demoDB,'flexyadmin_demo_');
				if ($demoDB) {
					$demoDB=current($demoDB);
					$demoDB=$demoDB['path'];
					// trace_($demoDB);
					$SQL=read_file($demoDB);
					if ($SQL) {
						$lines=explode("\n",$SQL);
						$comments="";
						foreach ($lines as $k=>$l) {
							if (substr($l,0,1)=="#")	{
								if (strlen($l)>2)	$comments.=$l.br();
								unset($lines[$k]);
							}
						}
						$sql=implode("\n",$lines);
						$lines=preg_split('/;\n+/',$sql); // split at ; with EOL

						foreach ($lines as $key => $line) {
							$line=trim($line);
							if (!empty($line)) {
								$query=$this->db->query($line);
							}
						}
						$succes=TRUE;
						redirect('admin');
					}
				}
			}

			if (!$succes) {
				show_error('Database login: correct.<br/>No tables (for flexyadmin) found.<br/>Tried to load demodatabase, no succes.');
			}
		}
	}

	function _check_if_flexy_database_exists() {
		return $this->db->table_exists('cfg_configurations');
	}

	function _init_flexy_admin($isAdmin=false) {
    // $this->output->enable_profiler(TRUE);
		$this->load->model('cfg');
		$this->cfg->set_if_admin($isAdmin);
	}
	


  
  /**
   * Geeft de uri van een pagina met de gevraagde module
   *
   * @param string $module 
   * @param bool $full_uri[true]
   * @return string uri
   * @author Jan den Besten
   */
	function find_module_uri($module,$full_uri=true) {
		$this->db->select('id,uri');
		if ($full_uri) {
			$this->db->select('order,self_parent');
			$this->db->uri_as_full_uri();
		}
		if (get_prefix($this->config->item('module_field'))=='id') {
			// Modules from foreign table
			$foreign_key=$this->config->item('module_field');
			$foreign_field='str_'.get_suffix($this->config->item('module_field'));
			$foreign_table=foreign_table_from_key($foreign_key);
			$this->db->add_foreigns();
			$like_field=$foreign_table.'__'.$foreign_field;
		}
		else {
			// Modules direct from field
			$like_field=$this->config->item('module_field');
		}
		$this->db->like($this->config->item('module_field'),$module);
    $this->db->order_by('id');
		$items=$this->db->get_result(get_menu_table());
    reset($items);
    $item=current($items);
		return $item['uri'];
	}
  
  
	

   /**
    * Form validation rule voor rgb_ velden. Controleert of er echt een kleurcode in staat
    *
    * @param string $rgb 
    * @return mixed
    * @author Jan den Besten
    */
	public function valid_rgb($rgb) {
		$rgb=trim($rgb);
		if (empty($rgb)) {
			return TRUE;
		}
		$rgb=str_replace("#","",$rgb);
		$len=strlen($rgb);
		if ($len!=3 and $len!=6) {
			$this->lang->load("form_validation");
			$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
			return FALSE;
		}
		$rgb=strtoupper($rgb);
		if (ctype_xdigit($rgb))
			return "#$rgb";
		else {
			$this->lang->load("form_validation");
			$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
			return FALSE;
		}
	}
  
  /**
   * Form validation rule voor str_google_analytics. Controleert of een goede code, en als het een javascript is, haal de code eruit.
   *
   * @param string $code 
   * @return mixed
   * @author Jan den Besten
   */

  public function valid_google_analytics($code) {
    $match=array();
    // Empty is ok
    if ($code=='') {
      return $code;
    }
    // Or a match is ok
    elseif (preg_match("/UA-\\d{8}-\\d/uiUsm", $code,$match)) {
      return $match[0];
    }
    // Not ok!
    else {
      $this->form_validation->set_message('valid_google_analytics',lang('valid_google_analytics'));
      return FALSE;
    }
  }

  /**
   * Input moet hetzelfde zijn als...
   * @param  string $str
   * @return mixed
   */
  public function valid_same($input,$same) {
    $is_same=($input==$same);
    if (!$is_same) $this->form_validation->set_message('valid_same',lang('valid_same'));
    return $is_same;
  }


  /**
   * Wachtwoord moet tussen 8-40 tekens lang zijn,
   * - minimaal 1 letter
   * - minimaal 1 hoofdletter
   * - minimaal 1 nummer
   * @param  string $str
   * @return mixed
   */
  public function valid_password($password) {
    $match=array();
    // Match
    if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#",$password,$match)) {
      return TRUE;
    }
    return FALSE;
  }

	
  /**
   * Form validation rule die de invoer in het formulier checkt tegen een regex waarde.
   * De regex waarden moeten worden ingesteld in de config bij $config['valid_regex_rules'].
   * De error_key verwijst naar een key in de language file regex_validation_lang
   * @param  string $str
   * @param  string $regex_rule
   * @return mixed
   */
  public function valid_regex($str, $regex_rule) {
    // $this->lang->load("regex_validation");
    $regexs=$this->config->item('valid_regex_rules');
    if (!isset($regexs[$regex_rule])) {
       $this->form_validation->set_message('valid_regex', langp('valid_regex_rule',$regex_rule));
       return FALSE;
    }
    $regex=$regexs[$regex_rule]['regex'];
  	$result = preg_match($regex, $str);
    if ($result == 1) {
      return TRUE;
    } else {
		  $this->form_validation->set_message('valid_regex', lang($regexs[$regex_rule]['error_key']));
		  return FALSE;
	  }
  }
  

  /**
   * Valideerd een veld aan de hand van meegegeven model.method
   *
   * @param string $str 
   * @param string $model_method 
   * @return bool
   * @author Jan den Besten
   */
  public function valid_model_method($str, $model_method) {
    $model=get_prefix($model_method,'.');
    $method=get_suffix($model_method,'.');
    $this->load->model($model);
    $result=$this->$model->$method($str);
    if (is_string($result)) {
      $this->form_validation->set_message('valid_model_method',lang($result));
      return FALSE;
    }
    return TRUE;
  }
  
  

}


?>