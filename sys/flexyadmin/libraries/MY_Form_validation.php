<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
   
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
   $CI =& get_instance();
   $model=get_prefix($model_method,'.');
   $method=get_suffix($model_method,'.');
   $CI->load->model($model);
   $result=$CI->$model->$method($str);
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