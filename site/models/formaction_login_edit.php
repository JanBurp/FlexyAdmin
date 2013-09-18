<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 class Formaction_login_edit extends Formaction {

   var $user_info;

   public function __construct() {
     parent::__construct();
     $this->load->library('forms');
     $this->user_info=$this->user->get_user();
   }


   public function edit($page) {
     return $this->forms->login_edit($page);
   }

   
   public function fields() {
     $fields = array(
                 'str_username'		=> array( 'label'=>lang('username'), 'validation'=>'required|min_length[4]|max_length[20]',     'value'=>$this->user_info->str_username ),
                 'email_email'	  => array( 'label'=>lang('email'),    'validation'=>'trim|valid_email|max_length[100]',          'value'=>$this->user_info->email_email ),
                 // 'gpw_password'   => array( 'type'=>'password', 'label'=>lang('password'), 'validation'=>'trim|callback_valid_password|max_length[40]'),
               );
     return $fields;
   }

   /**
    * Pas de gebruiker aan
    *
    * @param string $data data teruggekomen van het formulier
    * @return int id van toegevoegde data in de database
    * @author Jan den Besten
    * @ignore
    */
  public function go($data) {
    parent::go($data);
    $table='cfg_users';
    
    // set
    foreach ($data as $key => $value) {
      if ($this->db->field_exists( $key, $table )) $this->db->set($key,$value);
    }
    // insert in db
    $this->db->where('id',$this->user_info->id);
    $this->db->update( $table );

    return $this->user_info->id;
  }




}
