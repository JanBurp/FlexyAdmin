<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup libraries
 * Uitbreiding op Form Validation van CodeIgniter
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class MY_Form_validation extends CI_Form_validation {
  
  private $schemaform = array();
   
  public function __construct() {
    parent::__construct();
    $this->CI = @get_instance();
    $this->CI->load->helper('email');
    $this->CI->lang->load('regex_validation');
    $this->CI->config->load('schemaform',true);
    $this->schemaform = $this->CI->config->get_item('schemaform');
  }
  
  /**
   * Extra test om te zorgen dat lege validation rules echt leeg zijn.
   *
   * @param string $field 
   * @param string $label 
   * @param string $rules 
   * @param string $errors 
   * @return void
   * @author Jan den Besten
   */
	public function set_rules($field, $label = '', $rules = array(), $errors = array()) {
    if (is_string($rules)) {
      $rules=trim(trim($rules),'|');
      if (empty($rules)) $rules = array(); // if an empty string...
    }
    // clean up empty rules
    if (!empty($rules) and is_array($rules)) {
      foreach ($rules as $key => $rule) {
        if (empty($rule)) unset($rules[$key]);
      }
    }
    return parent::set_rules($field,$label,$rules,$errors);
  }
  
  
	/**
	 * Run the Validator
	 *
	 * JDB Change: als er geen validation data is, dan is er geen validatie nodig en is het waar.
	 *
	 * @param	string	$group
	 * @return	bool
	 */
	public function run($group = '') {
		// Do we even have any data to process?  Mm?
		$validation_array = empty($this->validation_data) ? $_POST : $this->validation_data;
		if (count($validation_array) === 0)
		{
			return FALSE;
		}

		// Does the _field_data array containing the validation rules exist?
		// If not, we look to see if they were assigned via a config file
		if (count($this->_field_data) === 0)
		{
			// No validation rules?  We're done...
			if (count($this->_config_rules) === 0)
			{
				return TRUE;
			}
    }
    
    return parent::run($group);
  }


  
  /**
   * Geeft validation foutmeldingen terug
   *
   * @return array of strings
   * @author Jan den Besten
   */ 
  public function get_error_messages() {
    $messages=$this->_error_messages;
    if (empty($messages) and !empty($this->_field_data)) {
      foreach ($this->_field_data as $key => $data) {
        if ($data['error']) {
          $messages[$key]=$data['error'];
        }
      }
    }
    return $messages;
  }
  
  
  /**
   * Validate given date for a table
   *
   * @param string $data 
   * @param string $table[''] // if not given every field needs to be prefixed with a table: tbl_menu.str_title
   * @return bool
   * @author Jan den Besten
   */
  public function validate_data($data,$table='') {
    // (re)Set data
    $this->set_data($data);
    $this->_field_data     = array();
    $this->_config_rules   = array();
    $this->_error_array    = array();
    $this->_error_messages = array();
    // Set rules
		foreach($data as $field=>$value) {
      $thisTable=$table;
      if (has_string('.',$field)) {
        $thisTable=get_prefix($field);
        $field=remove_prefix($field);
      }
      $label      = $this->CI->lang->ui($thisTable.'.'.$field);
      $validation = $this->get_rules($thisTable,$field);
      $this->set_rules($field, $label, $validation);
		}
    // Run validation
    $result=$this->run();
		return $result;
  }
  
  
  /**
   * Geeft alle validation rules van een gegeven veld uit een gegeven tabel.
   * Voegt eventueel nog custom mee te geven rules toe.
   *
   * @param string $table Tabel
   * @param string $field Veld
   * @param array $validation =array('rules'=>'','params'=>'') Eventueel mee te geven extra validation rules
   * @param bool $as_array default=FALSE
   * @return string
   * @author Jan den Besten
   */
  public function get_rules($table,$field,$validation=array(),$as_array=FALSE) {
    $validation[]=$this->_get_schemaform_rules($field);
    $validation[]=$this->_get_data_settings_rules($table,$field);
    $validation[]=$this->_get_db_rules($table,$field);
    $validation[]=$this->_get_db_options_rules($table,$field);
		$validations=$this->combine_rules($validation,$as_array);
    // trace_(['get_validations',$table,$field,$validation,$validations]);
    return $validations;
  }

  /**
   * Geeft validation rules die in schemaform.php ingesteld zijn voor een veld
   *
   * @param string $field 
   * @return array('rules'=>'','params'=>'') Validatie regels komen terug in deze array.
   * @author Jan den Besten
   */
	private function _get_schemaform_rules($field) {
    // Default
    $validation = el( array('FIELDS_default','validation'), $this->schemaform, '');
    $validation = explode('|',$validation);
    // Prefix
    $pre_validation = el( array('FIELDS_prefix', get_prefix($field), 'validation'), $this->schemaform, '');
    $pre_validation = explode('|',$pre_validation);
    // Special
    $special_validation = el( array('FIELDS_special',$field,'validation'), $this->schemaform,'');
    $special_validation = explode('|',$special_validation);
    // Merge
    $validation = array_merge($validation,$pre_validation,$special_validation);
    $validation = array(
      'rules'   => implode('|',$validation),
      'params'  => ''
    );
    return $validation;
	}

  
    /**
     * Geeft validation rules die voor een veld ingesteld zijn in data settings van een tabel
     *
     * @param string $table
     * @param string $field
     * @return array('rules'=>'','params'=>'') Validatie regels komen terug in deze array.
     * @author Jan den Besten
     */
  private function _get_data_settings_rules($table,$field) {
    $this->CI->data->table($table);
    $validation = $this->CI->data->get_setting( array('field_info',$field,'validation') );
    $validation = $this->split_rules_parameters($validation);
    return $validation;
  }

  /**
   * Geeft validation rules die standaard uit de veld informatie uit database gehaald kan worden.
   * Bijvoorbeeld een VARCHAR 255 geeft max_length[255]
   *
   * @param string $table 
   * @param string $field 
   * @return array('rules'=>'','params'=>'') Validatie regels komen terug in deze array.
   * @author Jan den Besten
   */
	private function _get_db_rules($table,$field) {
		$validation=array();
		$info=$this->CI->data->table($table)->field_data($field);
		if (isset($info['type'])) {
  		switch ($info['type']) {
  			case 'varchar':
  				if (isset($info['max_length'])) {
  					$validation['rules']='max_length';
  					$validation['params']=$info['max_length'];
  				}
          break;
  			case 'decimal':
  				$validation['rules']='decimal';
  				$validation['params']='';
          break;
  		}
		}
		return $validation;
	}
  
  
  /**
   * Geeft validation rule 'is_option[]' als het gegeven veld opties heeft ingesteld
   *
   * @param string $table
   * @param string $field
   * @return string
   * @author Jan den Besten
   */
  private function _get_db_options_rules($table,$field) {
    $validation=array();
    $this->CI->config->load('data/'.$table,true);
    $options = $this->CI->config->get_item(array('data/'.$table,'options',$field));
    if ($options) {
      if (el('multiple',$options,false))
        $validation['rules']='valid_options';
      else
        $validation['rules']='valid_option';
      // options
      $validation['params']=array();
      if (isset($options['data'])) {
        foreach ($options['data'] as $option) {
          $validation['params'][] = el('value',$option,$option);
        }
        $validation['params'] = implode(',',$validation['params']);
      }
    }
    return $validation;
  }
  
  
  /**
   * Splits validatie regels in een string naar een assoc array waar de key de rule is en de value de paramater (als beschikbaar)
   *
   * @param string $validation 
   * @return array
   * @author Jan den Besten
   */
  public function to_validation_array($validations) {
    $vals        = explode('|',$validations);
    $validations = array();
    foreach ($vals as $rule) {
      $param = '';
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match)) {
				$rule  = $match[1];
				$param = $match[2];
			}
      $validations[$rule]=$param;
    }
    return $validations;
  }
  
  /**
   * Splits validatie regels naar een assoc array('rules'=>.., 'params'=>..)
   *
   * @param mixed $validation 
   * @return array
   * @author Jan den Besten
   */
  public function split_rules_parameters($validations) {
    if (is_string($validations)) {
      $validations = $this->to_validation_array($validations);
    }
    if (!isset($validations)) {
      return array( 'rules' => '', 'params' => '' );
    }
    if (isset($validations[0])) {
      if (is_array($validations[0])) {
        $validations = array_combine(array_values($validations),array_fill(0,count($validations),''));
      }
      else {
        // Check if values are strings
        $checked_validations = array();
        foreach ($validations as $key => $value) {
          $validation = $this->to_validation_array($value);
          $checked_validations = array_merge($checked_validations,$validation);
        }
        $validations = $checked_validations;
      }
    }
    $rules  = array_keys($validations);
    $params = array_values($validations);
    return array( 'rules' => implode('|',$rules), 'params' => implode('|',$params) );
  }
  
  
  /**
   * Combineerd diverse validatieregels. Verwijderd ook de dubbelingen (op een slimme manier)
   * 
   * Meegegeven parameter kan de volgende formaten hebben:
   * 
   * - een string met een optelling van validatieregels gescheiden door |
   * - een array waar de keys de validatieregels zijn en de values de eventuele paramaters
   * - een array waarbij elke rij de volgende array bevat: array('rules'=>hier de validatieregels gescheiden door |, 'params'=>hier de eventuele paramaters gescheiden door |)
   *
   * @param mixed $validations
   * @param bool $as_array default=FALSE
   * @return array
   * @author Jan den Besten
   */
  public function combine_rules($validations,$as_array=FALSE) {
    return $this->combine_validations($validations,$as_array=FALSE);
  }
  public function combine_validations($validations,$as_array=FALSE) {
  	$validation = array();
    
    // Splits de rules en de params als dat nog nodig is
    if (is_string($validations)) {
      $validations = $this->split_rules_parameters($validations);
    }
    // Maak er een array van arrays van
    if ( !isset($validations[0])) {
      $validations = array($validations);
    }
    
    // Maak er rule=>param paren van
  	foreach ($validations as $val) {
  		if (!empty($val) and !empty($val['rules'])) {
  			$rules  = $val['rules'];
  			$params = $val['params'];
  			if (!is_array($rules))  $rules  = explode('|',$rules);
  			if (!is_array($params)) $params = explode('|',$params);
  			foreach ($rules as $key => $rule) {
  				$param='';
  				if (isset($validation[$rule])) 	$param=$validation[$rule];
  				if (isset($params[$key])) 			$param=$params[$key];
  				if (isset($validation[$rule])) {
  					switch($rule) {
  						case 'max_length':
  							if ($validation[$rule]<$param) $param=$validation[$rule]; // get smallest
  							break;
  						case 'min_length':
  							if ($validation[$rule]>$param) $param=$validation[$rule]; // get biggest
  							break;
  					}
  				}
  				$validation[$rule]=$param;
  			}
  		}
  	}
    // Cleanup double
    foreach ($validation as $rule => $param) {
      switch ($rule) {
        case 'valid_emails':
          if (isset($validation['valid_email'])) unset($validation['valid_email']);
          break;
      }
    }
    // trace_(['combine_validations 2',$validation]);
    if (!$as_array) {
      $vals=$validation;
      $validation='';
      foreach ($vals as $rule => $param) {
        $rule=str_replace('[]','',$rule);
        if (!empty($param)) $rule=$rule.'['.$param.']';
        $validation=add_string($validation,$rule,'|');
      }
    }
    // trace_(array('result'=>$validation));
  	return $validation;
  }
  
  
  /**
   * Prepareert een link. Plakt er mailto: voor als het een emailadres is, en anders http://
   *
   * @param string $str 
   * @param string $field 
   * @return string
   * @author Jan den Besten
   */
  public function prep_url_mail($str,$field) {
    $str=str_replace(array('http://','https://','mailto:'),'',$str);
    if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
      $str='mailto:'.$str;
    }
    else {
      $str=prep_url($str,$field);
    }
    return $str;
  }
  
  
  /**
   * Test of de waarde een waarde is uit de lijst met opties.
   *
   * @param string $str
   * @param string $options: 'opties1|optie2|optie3|....'
   * @return bool
   * @author Jan den Besten
   */
  public function valid_option($str,$options) {
    if (empty($options)) return TRUE;
    if (!is_array($options)) $options = explode(',',$options);
    if (empty($str) and empty($options[0])) return TRUE; // mag leeg zijn
    $result=in_array($str,$options);
    return $result;
  }

  /**
   * Test of de waarde een of meer waarden bevat uit de lijst met opties.
   *
   * @param string $str
   * @param string $options: 'opties1|optie2|optie3|....'
   * @return bool
   * @author Jan den Besten
   */
  public function valid_options($str,$options) {
    if (empty($options)) return TRUE;
    if (!is_array($options)) $options = explode(',',$options);
    if (empty($str) and empty($options[0])) return TRUE; // mag leeg zijn
    $data=explode('|',$str);
    foreach ($data as $value) {
      $result=in_array($value,$options);
      if (!$result) return FALSE;
    }
    return TRUE;
  }

  
  
  /**
   * Test of een waarde niet hetzelfde is als de paramater
   *
   * @param string $str 
   * @param string $forbidden 
   * @return bool
   * @author Jan den Besten
   */
  public function is_not($str,$forbidden) {
    return $str!==$forbidden;
  }
  
  
  /**
   * Je kunt dit op de normale manier gebruiken (CI), voorbeeld 'cfg_users.str_username'.
   * Of door als extra de 'id' waarde mee te van de rij uit de tabel. De waarde van het veld op die rij wordt ook geaccepteerd (meestal zichzelf): 'cfg_users.str_username.3'
   * Als de id in de POST data meekomt, kun je ook volstaan met 'id': 'cfg_users.str_username.id'
   *
   * @param string $str 
   * @param string $field 
   * @return void
   * @author Jan den Besten
   */
  public function is_unique($str, $field) {
    if (substr_count($field, '.')>=2) {
      // Not checking the row where $id=...
      list($table,$field,$id)=explode('.', $field);
      if ($id==='id' and isset($_POST['id'])) {
        $id=$this->CI->input->post('id');
      }
      if ($id>0) {
        $sql = 'SELECT `'.$field.'` FROM `'.$table.'` WHERE `id`='.$id;
        $query = $this->CI->db->query($sql);
        $possible_value = $query->row_array();
        $possible_value = $possible_value[$field];
        if ($str===$possible_value) return true;
      }
    }
    // Normal operation
    else {
      list($table, $field)=explode('.', $field);
    }
    
    if ( !isset($this->CI->db) ) return FALSE;
    
    $sql = 'SELECT `'.$field.'` FROM `'.$table.'` WHERE `'.$field.'`="'.$str.'" LIMIT 1';
    $query = $this->CI->db->query($sql);
    return ($query->num_rows()===0);
    //
    // return isset($this->CI->db)
    //   ? ($this->CI->db->limit(1)->get_where($table, array($field => $str))->num_rows() === 0)
    //   : FALSE;
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
  		return FALSE;
  	}
  	$rgb=strtoupper($rgb);
  	if (ctype_xdigit($rgb))
  		return "#$rgb";
  	else {
  		return FALSE;
  	}
  }
 
 
  /**
    * Check if given date is valid dd-mm-yyyy
    *
    * Excepts dashes, spaces, forward slashes and dots as seperators.
    * Leadings zeroes for days and months are optional.
    * Excepts a format parameter, turning this method into a prepper.
    * Use standard php date formats (ie. Y-m-d) for this.
    *
    * @param    string $str
    * @param    mixed $format default=FALSE
    * @return    bool / obj
    */
  function valid_date($str, $format=FALSE) {
    $pattern = '/^(?<day>0?[1-9]|[12][0-9]|3[01])[- \/.](?<month>0?[1-9]|1[012])[- \/.](?<year>(19|20)[0-9]{2})$/';
    if( preg_match($pattern, $str, $match) && checkdate($match['month'], $match['day'], $match['year']) ) {
      if ( $format ) {
        // prep date
        return date($format, mktime(0, 0, 0, $match['month'], $match['day'], $match['year']));
      }
      return TRUE;
    }
    return FALSE;        
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
       return FALSE;
     }
   }
   

   /**
    * Input moet hetzelfde zijn als...
    * 
    * @param  string $input
    * @param string $same
    * @return mixed
    */
   public function valid_same($input,$same) {
     $is_same=($input==$same);
     return $is_same;
   }


   /**
    * Wachtwoord moet tussen 8-40 tekens lang zijn,
    * - minimaal 1 letter
    * - minimaal 1 hoofdletter
    * - minimaal 1 nummer
    * 
    * Als er een id in de postdata zit dan mag het nieuwe wachtwoord leeg zijn (als er al een wachtwoord is ingesteld en de gebruiker aktief is)
    * 
    * @param  string $password
    * @return mixed
    */
   public function valid_password($password) {
     // Als id bekend in postwaarde, kijk dan of het passwoord leeg mag zijn.
     $id=$this->CI->input->post('id');
     if (isset($id) and !empty($id) and $id>0) {
       $sql = 'SELECT `id`,`gpw_password`,`b_active` FROM `cfg_users` WHERE `id`='.$id;
       $query = $this->CI->db->query($sql);
       $user = $query->row_array();
       // $user=$this->CI->db->select('id,gpw_password,b_active')->where('id',$id)->get_row('cfg_users');
       if ($user['b_active']===true && !empty($user['gpw_password'])) return TRUE;
     }
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
    * 
    * @param  string $str
    * @param  string $regex_rule
    * @return mixed
    */
    public function valid_regex($str, $regex_rule) {
      $regexs=$this->CI->config->item('valid_regex_rules');
      if (!isset($regexs[$regex_rule])) {
        $this->set_message('valid_regex', langp('valid_regex_rule',$regex_rule));
        return FALSE;
      }
      $regex=$regexs[$regex_rule]['regex'];
      $result = preg_match($regex, $str);
      if ($result == 1) {
        return TRUE;
      } else {
        $this->set_message('valid_regex', lang($regexs[$regex_rule]['error_key']));
        return FALSE;
      }
    }
 

   /**
    * Valideerd een veld aan de hand van meegegeven model.method
    *
    * @param string $str 
    * @param string $model_method 
    * @return mixed
    * @author Jan den Besten
    */
   public function valid_model_method($str, $model_method) {
     $model=get_prefix($model_method,'.');
     $method=get_suffix($model_method,'.');
     $this->CI->load->model($model);
     $result=$this->CI->$model->$method($str);
     if (is_string($result)) {
       $this->set_message('valid_model_method',lang($result));
       return FALSE;
     }
     return $result;
   }
  
}
// END MY Form Validation Class

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */  