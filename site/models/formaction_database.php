<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dit is een Formaction die de meegegeven data in een tabel in de database stop.
 * Zie bij config/forms.php het voorbeeld 'reservation' en 'shop'
 * 
 * @package default
 * @author Jan den Besten
 */
 class Formaction_database extends Formaction {

   var $settings = array();

   /**
    * @author Jan den Besten
    * @ignore
    */
   public function __construct() {
     parent::__construct();
   }
   
   /**
    * Voer de actie uit, in dit geval: stop de data in de database
    *
    * @param string $data data teruggekomen van het formulier
    * @return int id van toegevoegde data in de database
    * @author Jan den Besten
    * @ignore
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
      $data[$field]=unix_to_mysql();
    }
    
    // set
    foreach ($data as $key => $value) {
      if ($this->db->field_exists( $key, $table )) $this->db->set($key,$value);
    }
    // insert in db
    $this->db->insert( $table );
    $id=$this->db->insert_id();
    
    return $id;
  }

}
