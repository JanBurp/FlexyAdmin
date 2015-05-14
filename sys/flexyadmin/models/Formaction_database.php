<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dit is een Formaction die de meegegeven data in een tabel in de database stop.
 * Zie bij config/forms.php het voorbeeld 'reservation' en 'shop'
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */
 class Formaction_database extends Formaction {

   var $settings        = array();
   private $return_data = array();

   public function __construct() {
     parent::__construct();
   }
   
   /**
    * Voer de actie uit, in dit geval: stop de data in de database
    *
    * @param string $data data teruggekomen van het formulier
    * @return int id van toegevoegde data in de database
    * @author Jan den Besten
    */
  public function go($data) {
    parent::go($data);

    // Which table?
    if (isset($this->settings['formaction_table']))
      $table=$this->settings['formaction_table'];
    else
      $table=$this->settings['table'];
    
    // tme_ of dat_ field zonder inhoud? Voeg die toe
    $fields=$this->db->list_fields($table);
    $fields=array_values($fields);
    $date_fields=array();
    foreach ($fields as $key => $field) {
      if (in_array(get_prefix($field),$this->config->item('FIELDS_date_fields'))) $date_fields[]=$field;
    }
    foreach ($date_fields as $field) {
      if (!isset($data[$field]) or $data[$field]=='') $data[$field]=unix_to_mysql();
    }
    
    // set
    foreach ($data as $key => $value) {

      // prepare booleans
      if (in_array(get_prefix($key),$this->config->item('FIELDS_bool_fields'))) {
        $value=is_true_value($value);
        $data[$key]=$value;
      }
      if ($value==NULL) $value='';

      // prepare media (strip path)
      if (in_array(get_prefix($key),array('file','media'))) {
        $value=get_suffix($value,'/');
      }

      // save in db
      if ($this->db->field_exists( $key, $table )) $this->db->set($key,$value);
    }
    // insert in db
    $this->db->insert( $table );
    $id=$this->db->insert_id();
    
    $this->return_data = array_merge($data,array('id'=>$id));
    
    return $id;
  }
  
  
  /**
   * Returns the data including the id
   *
   * @return void
   * @author Jan den Besten
   */
  public function return_data() {
    return $this->return_data;
  }
  
  

}
