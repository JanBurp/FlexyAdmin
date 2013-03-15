<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * Stopt formdata in een tabel in de database
 * 
 * @package default
 * @author Jan den Besten
 */
 class Formaction_database extends Formaction {
   
   var $config = array(
   );

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

    // set
    foreach ($data as $key => $value) {
      if ($this->db->field_exists( $key, $this->config['table'] )) $this->db->set($key,$value);
    }
    // insert in db
    $this->db->insert($this->config['table']);
    $id=$this->db->insert_id();
    
    return $id;
  }

}
