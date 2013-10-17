<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dit is een Model & Formaction voor comments
 * 
 * @package default
 * @author Jan den Besten
 */
 class Formaction_comments extends Formaction {
   
   /**
    * @author Jan den Besten
    * @ignore
    */
   public function __construct() {
     parent::__construct();
   }
   

   /**
    * Geef velden die nodig zijn om comments form te maken
    *
    * @return array
    * @author Jan den Besten
    */
   public function get_fields() {
     $fields=$this->db->list_fields( $this->settings['table'] );
     $fields=array2formfields($fields,array('str'=>'required|max_length[255]','txt'=>'required'));
     unset($fields['id']);
     unset($fields[$this->settings['key_id']]);
     unset($fields[$this->settings['field_date']]);
     unset($fields['int_spamscore']);
     return $fields;
   }


   /**
    * Geeft alle comments van dit item
    *
    * @return array
    * @author Jan den Besten
    */
   public function get_comments($id=false) {
     if (!$id) $id=$this->settings['id'];
     $this->db->where($this->settings['key_id'],$id);
     $comments=$this->db->get_results($this->settings['table']);
     // make nice date format
     foreach ($comments as $id => $comment) {
        $comments[$id]['niceDate']=strftime($this->settings['date_format'],mysql_to_unix($comment[$this->settings['field_date']]));
     }
     return $comments;
   }


   /**
    * Geeft aantal comments van dit item
    *
    * @return array
    * @author Jan den Besten
    */
   public function count_comments($id,$key='') {
     if (!$id) $id=$this->settings['id'];
     $key=$this->settings['key_id'];
     $this->db->where($key,$id);
     $this->db->select('id');
     $comments=$this->db->get_results($this->settings['table']);
     $count=count($comments);
     return $count;
   }
   

   /**
    * Voeg comment toe
    *
    * @param string $data data teruggekomen van het formulier
    * @return int id van toegevoegde data in de database
    * @author Jan den Besten
    */
  public function go($data) {
    parent::go($data);
    $id=false;

    // Vul data aan met id, datum en spamscore
    $data[$this->settings['key_id']]=$this->settings['id'];
    $data[$this->settings['field_date']]=date(DATE_ISO8601);
    $data[$this->settings['field_spamscore']]=$this->settings['spam_rapport']['score'];
    
    // Place comment in database
    $this->db->set($data);
    $this->db->insert($this->settings['table']);
    $id=$this->db->insert_id();

    // send email that a comment has been placed to the sites owner
    if ($this->settings['mail_owner'] or $this->settings['mail_others']) $this->load->library('email');

    if ($this->settings['mail_owner']) {
      $this->email->to( $this->site['email_email'] );
      $this->email->from( $this->site['email_email'] );
      $this->email->subject( langp('comments_'.'mail_to_owner_subject',$this->site['url_url']) );
      $this->email->message( langp('comments_'.'mail_to_owner_body', site_url($this->uri->get())."\n\n".$data[$this->settings['field_text']]) );
      if ( ! $this->email->send() ) log_message('error', $this->email->print_debugger() );
      $this->email->clear();
    }
    if ($this->settings['mail_others']) {
      $subject=langp('comments_'.'mail_to_others_subject',$this->site['url_url']);
      $body=langp('comments_'.'mail_to_others_body', site_url($this->uri->get())."\n\n".$data[$this->settings['field_text']]);
      $this->db->select( $this->settings['field_email'] );
      $this->db->where( $this->settings['key_id'], $id );
      $emails=$this->db->get_results( $this->settings['table'] );
      foreach ($emails as $key => $value) {
        $this->email->to( $value[$this->settings['field_email']] );
        $this->email->from( $this->site['email_email'] );
        $this->email->subject( $subject );
        $this->email->message( $body );
        if ( ! $this->email->send() )  $errorHtml.=$this->email->print_debugger();
        $this->email->clear();
      }
    }
    
    // redirect zodat $_POST leeg is en geen dubbele reacties kunnen worden geplaatst
    $redirect=site_url($this->uri->get());
    redirect($redirect);
    
    return $id;
  }
}
