<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dit is een Formaction die de data verstuurd formdata naar het standaard emailadres
 * 
 * Hieronder config velden (met hun defaults):
 * 
 * - to_field             => Het database veld waar het mailadres in staat ('tbl_site.email_email')
 * - subject              => Onderwerp van de mail: Je kunt er codes inzetten die vervangen worden: %URL% = Url van de site, %MAIL% = 1e email veld, of een willekeurig veld %veldnaam%
 * - send_copy_to_sender  => Of er een kopie naar de afzender moet gaan (FALSE)
 * - from_address_field   => Veld in de formulierdata waar het emailadres van de afzender in staat ('email_email')
 * - attachment_folder    => 'downloads'
 * - attachment_types     => 'gif|jpg|png|doc|docx|xls|xlsx|pdf|zip'
 * 
 * 
 * @package default
 * @author Jan den Besten
 */
 class Formaction_mail extends Formaction {
   
   var $settings = array(
     'to_field'             => 'tbl_site.email_email',
     'subject'              => 'Mail from site',
     'send_copy_to_sender'  => FALSE,
     'from_address_field'   => 'email_email',
     'attachment_folder'    => 'downloads',
     'attachment_types'     => 'gif|jpg|png|doc|docx|xls|xlsx|pdf|zip'
   );
   

   public function __construct() {
     parent::__construct();
     $this->load->library('email');
   }
   
   
   /**
    * Voer de actie uit, in dit geval: verstuur de mail met als body de veldnamen met hun gegevens
    *
    * @param string $data data teruggekomen van het formulier
    * @return bool TRUE als de mail is versuurd, anders FALSE
    * @author Jan den Besten
    */
  public function go($data) {
    parent::go($data);
    
    // TO
    if (!isset($this->settings['to']) or empty($this->settings['to'])) {
     $table=get_prefix($this->settings['to_field'],'.');
     $field=get_suffix($this->settings['to_field'],'.');
     $this->settings['to']=$this->db->get_field($table,$field);
    }
    $this->email->to($this->settings['to']);
    // CC
    if ($this->settings['send_copy_to_sender']) $this->email->cc($data[$this->settings['from_address_field']]);
    // FROM
    $this->email->from($this->get_from_addres($data));
    
    // SUBJECT - vervang keys
    $subject=$this->settings['subject'];
    $replace['/%URL%/uiUsm']=trim(str_replace('http://','',$this->site['url_url']),'/');
    $emailfields=filter_by_key($data,'email');
    if (empty($emailfields)) $emailfields=filter_by_key($data,'Email');
    if ($emailfields) $replace['/%MAIL%/uiUsm']=current($emailfields);
    foreach ($data as $key => $value) {
      $replace['/%'.$key.'%/uiUsm']=$value;
    }
    $subject = preg_replace(array_keys($replace),array_values($replace), $subject);
    $this->email->subject($subject);
    
    // BODY
    $body='';
    foreach ($this->fields as $key => $field) {
      $value=$data[$key];
      // Attachment?
      if (get_prefix($key)=='file') {
    		if (isset($_FILES[$key]['name']) and !empty($_FILES[$key]['name']) ) {
    			$this->load->library('upload');
    			$this->load->model('file_manager');
    			$this->file_manager->init( $this->settings['attachment_folder'], $this->settings['attachment_types'] );
    			$result=$this->file_manager->upload_file($key);
    			if (!empty($result['file'])) {
            $file=SITEPATH.'assets/'.$this->attachment_folder.'/'.$result['file'];
    				$data[$key]=$result['file'];
            $value=$result['file'];
    			}
    		}
      }

      // Create body
    	if (substr($key,0,1)!='_' and !empty($value)) {
				$showKey=ucfirst($field['label']);
				$body.="<b>$showKey:&nbsp;</b>";
				$body.="$value<br/><br/>";
				if (isset($data[$key]['options'][$value])) {
					$value=strip_tags($data[$key]['options'][$value]);
				}
    	}
        
    }

    $this->email->message($body);
    if (isset($file) and !empty($file)) $this->email->attach($file);
    if (!$this->email->send()) {
      $this->errors=$this->email->print_debugger();
      return false;
    }
    return true;
  }
  
  
  private function get_from_addres($data) {
    $from='';
    if (isset($this->settings['from_address_field']) and !empty($this->settings['from_address_field'])) {
      $field=$this->settings['from_address_field'];
      if (!isset($data[$field])) {
        // niet gevonden, zoek eerste email veld
        $email_fields=filter_by_key($data,'email');
        if (empty($email_fields)) $email_fields=filter_by_key($data,'Email');
        $field=key($email_fields);
      }
    }
    return $data[$field];
  }

}
