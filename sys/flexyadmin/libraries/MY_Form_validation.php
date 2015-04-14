<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
   
  public function __construct() {
    $this->CI = @get_instance();
    $this->CI->load->helper('email');
  }
  
  /**
   * Geeft validation foutmeldingen terug
   *
   * @return void
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
    // For fetching the labels
    $this->CI->load->model('ui');
    // Set POST so CI's validation run will work
    $_POST = $data;
    // Set rules
		foreach($data as $field=>$value) {
      $thisTable=$table;
      if (has_string('.',$field)) {
        $thisTable=get_prefix($field);
        $field=remove_prefix($field);
      }
      $label      = $this->CI->ui->get($field,$thisTable);
      $validation = $this->get_validations($thisTable,$field);
      // trace_(['set_rules',$thisTable,$field,$validation]);
			$this->set_rules($field, $label, $validation);
		}
    // Run validation
    $result=$this->run();
    // trace_($result);
		return $result;
  }
  
  /**
   * Geeft alle validation rules van een gegeven veld uit een gegeven tabel.
   * Voegt eventueel nog custom mee te geven rules toe.
   *
   * @param string $table Tabel
   * @param string $field Veld
   * @param array $validation[=array('rules'=>'','params'=>'')] Eventueel mee te geven extra validation rules
   * @return string
   * @author Jan den Besten
   */
  public function get_validations($table,$field,$validation=array(),$as_array=FALSE) {
    $validation[]=$this->_get_flexy_cfg_validation($field);
    $validation[]=$this->_get_global_cfg_validation($field);
		$validation[]=$this->_get_cfg_validation($table,$field);
		$validation[]=$this->_get_db_validation($table,$field);
    $validation[]=$this->_get_db_options_validation($table,$field);
		$validations=$this->combine_validations($validation,$as_array);
    // trace_(['get_validations',$table,$field,$validation,$validations]);
    return $validations;
  }

  /**
   * Geeft validation rules die in flexyadmin_config.php ingesteld zijn voor een veld
   *
   * @param string $field 
   * @return array('rules'=>'','params'=>'') Validatie regels komen terug in deze array.
   * @author Jan den Besten
   */
	private function _get_flexy_cfg_validation($field) {
    $pre=get_prefix($field);
    $validation=el( array($field,'validation'), $this->CI->config->item('FIELDS_special'),'');                                             // Special fields
    if (empty($validation)) $validation=el( array($pre,  'validation'), $this->CI->config->item('FIELDS_prefix'), '');                     // By prefix
    if (empty($validation)) $validation=el( array($field,'validation'), $this->CI->config->item('FIELDS_'.$this->CI->db->platform()), ''); // By DB Platform
    if (empty($validation)) $validation=el( array($field,'validation'), $this->CI->config->item('FIELDS_default'), '');                    // Default
    $validation = array(
      'rules'   => $validation,
      'params'  => ''
    );
    return $validation;
	}

  
  /**
   * Geeft validation rules die voor globaal voor een veld zijn ingesteld in cfg_field_info
   *
   * @param string $field 
   * @return array('rules'=>'','params'=>'') Validatie regels komen terug in deze array.
   * @author Jan den Besten
   */
	private function _get_global_cfg_validation($field) {
    $global_validation = array(
      'rules'		=> $this->CI->cfg->get('CFG_field',"*.".$field,'str_validation_rules'),
			'params'	=> $this->CI->cfg->get('CFG_field',"*.".$field,'str_validation_parameters')
    );
    return $global_validation;
	}
  
  /**
   * Geeft validation rules die voor een veld ingesteld zijn in cfg_field_info
   *
   * @param string $table 
   * @param string $field 
   * @return array('rules'=>'','params'=>'') Validatie regels komen terug in deze array.
   * @author Jan den Besten
   */
	private function _get_cfg_validation($table,$field) {
    $validation = array(
      'rules'		=> $this->CI->cfg->get('CFG_field',$table.".".$field,'str_validation_rules'),
			'params'	=> $this->CI->cfg->get('CFG_field',$table.".".$field,'str_validation_parameters')
    );
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
	private function _get_db_validation($table,$field) {
		$validation='';
		$info=$this->CI->cfg->field_data($table,$field);
		if (isset($info['type']))
		switch ($info['type']) {
			case 'varchar':
				if (isset($info['max_length'])) {
					$validation['rules']='max_length[]';
					$validation['params']=$info['max_length'];
				}
        break;
			case 'decimal':
				$validation['rules']='decimal';
				$validation['params']='';
        break;
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
  private function _get_db_options_validation($table,$field) {
    $validation='';
		$options=$this->CI->cfg->get('cfg_field_info',$table.'.'.$field,'str_options');
    if ($options) {
      $validation['rules']='valid_option';
      $validation['params']=str_replace('|',',',$options);
    }
    return $validation;
  }
  
  
  /**
   * Combineerd diverse validatieregels. Verwijderd ook de dubbelingen (op een slimme manier)
   * 
   * Meegegeven parameter kan de volgende formaten hebben:
   * 
   * - een string met een optelling van validatieregels gescheiden door |
   * - een array waar de keys de validatieregels zijn en de values de eventuele paramaters
   * - een array waarbij elke rij de volgende array bevat: array('rules'=>#hier de validatieregels gescheiden door |#, 'params'=>#hier de eventuele paramaters gescheiden door |#)
   *
   * @param mixed $validations
   * @param bool $as_array=FALSE
   * @return array
   * @author Jan den Besten
   */
  public function combine_validations($validations,$as_array=FALSE) {
    // trace_(array('start'=>$validations));
  	$validation=array();
    // Als een string meegegeven, maak er een validatiearray van
    if (is_string($validations)) {
      $vals=explode('|',$validations);
      $validations=array();
      foreach ($vals as $rule) {
        $param='';
  			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match)) {
  				$rule	= $match[1];
  				$param	= $match[2];
  			}
        $validations[$rule]=$param;
      }
    }

    // Splits de rules en de params als dat nodig is
    if ( !isset($validations[0]) or !is_array($validations[0])) {
      $vals=$validations;
      $validations=array();
      $rules='';
      $params='';
      foreach ($vals as $rule => $param) {
        $rules.=$rule.'|';
        $params.=$param.'|';
      }
      $validations[]=array('rules'=>rtrim($rules,'|'),'params'=>rtrim($params,'|'));
    }
    
    // trace_(['combine_validations',$validations]);
    // Maak er rule=>param paren van
  	foreach ($validations as $val) {
  		if (!empty($val) and !empty($val['rules'])) {
  			$rules=explode('|',$val['rules']);
  			$params=explode('|',$val['params']);
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
    if (valid_email($str)) {
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
    if (!is_array($options)) $options = explode(',',$options);
    $result=in_array($str,$options);
    return $result;
  }
  
  
  /**
   * You can use this as normal (cfg_users.str_user_name) for example.
   * Or you can use this to not test on update ()
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
      if($this->CI->input->post($id) > 0):
        $query = $this->CI->db->limit(1)->get_where($table, array($field => $str, $id.' !='=>$this->CI->input->post($id)));
      else:
        $query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
      endif;
    }
    else {
      // Normal operation
      list($table, $field)=explode('.', $field);
      $query = $this->CI->db->limit(1)->get_where($table, array($field => $str));      
    }

    return $query->num_rows() === 0;
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
    * @param    string
    * @param    string
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
    * @param  string $str
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
      $regexs=$this->config->item('valid_regex_rules');
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