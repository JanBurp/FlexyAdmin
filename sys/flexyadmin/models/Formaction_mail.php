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
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
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
    unset($data['int_spamscore']);
    
    // TO
    if (!isset($this->settings['to']) or empty($this->settings['to'])) {
     $table=get_prefix($this->settings['to_field'],'.');
     $field=get_suffix($this->settings['to_field'],'.');
     $this->settings['to']=$this->data->table($table)->get_field($field);
    }
    $this->email->to($this->settings['to']);
    // FROM
    $from = $this->get_from_addres($data);
    $this->email->from( $from );
    
    // COPY TO SENDER?
    if ($this->settings['send_copy_to_sender']) {
      $this->email->cc( $from );
    }
   
    // SEND With a template from cfg_email, or standard
    $send=false;
    // Prepare Replace array
    $replace=array();
    if (isset($this->site)) {
      $site=$this->site;
    }
    else {
      $site = $this->data->table('tbl_site')->get_row();
    }
    $replace=array_merge($replace,$site);
    $replace['URL']=trim(str_replace('http://','',$site['url_url']),'/');
    $replace['PAGE']=current_url();
    $emailfields=filter_by_key($data,'email');
    if (empty($emailfields)) $emailfields=filter_by_key($data,'Email');
    if ($emailfields) $replace['MAIL']=current($emailfields);
    $replace=array_merge($replace,$data);
    if (is_array(el('body_data',$this->settings,FALSE))) {
      $replace=array_merge($replace,el('body_data',$this->settings));
    }
    // Template?
    $template=el('template',$this->settings,'');
    if ($template) {
      // Send with template
      $this->_attach_files($data);
      $this->email->send_with_pdf( el('add_pdf',$this->settings,FALSE) );
      $send = $this->email->send_lang($template,$replace);
      $body = $this->email->get_body();
    }
    else {
      // Send standard
      $this->load->model('ui');
      $this->load->library('parser');
      // old replace -- for backward compatibility
      $old_replace=array();
      foreach ($replace as $key => $value) {
        if (!is_array($value)) $old_replace['/%'.$key.'%/uiUsm']=$value;
      }
      $subject=el('subject',$this->settings,'Email from {URL}');
      $subject=$this->parser->parse_string($subject,$replace,true);
      $subject = preg_replace(array_keys($old_replace),array_values($old_replace), $subject);
      $this->email->subject($subject);
      $body='';
      // STANDARD BODY
      foreach ($data as $key => $field) {
        $value=el('value',$field,'');
      	if (substr($key,0,1)!='_' and !empty($value)) {
    			$showKey=$this->ui->get($key);
    			$body.="<b>$showKey:&nbsp;</b>";
    			$body.="$value<br/><br/>";
    			if (isset($data[$key]['options'][$value])) {
    				$value=strip_tags($data[$key]['options'][$value]);
    			}
      	}
      }
      $this->email->message($body);
      $this->_attach_files($data);
      $send = $this->email->send();
    }
    
    if ( ! $send) {
      $this->errors=$this->email->print_debugger();
      return false;
    }
    return true;
  }
  
  
  private function _attach_files($data) {
    foreach ($data as $field => $value) {
      if (in_array(get_prefix($field),array('file','media'))) {
        // add path if needed
        $file=$value;
        if (get_suffix($file,'/')===$file) {
          $file=SITEPATH.'assets/'.$this->settings['upload_path'].'/'.$value;
        }
        $this->email->attach($file);
      }
    }
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
