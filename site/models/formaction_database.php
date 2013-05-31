<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dit is een Formaction die de meegegeven data in een tabel in de database stop.
 * Zie bij config/forms.php het voorbeeld 'reservation' en 'shop'
 * 
 * @package default
 * @author Jan den Besten
 */
 class Formaction_database extends Formaction {

   var $config = array(
   );

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
