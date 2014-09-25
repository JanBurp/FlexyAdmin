<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model dat als basis dient voor formactions in de frontend
 * 
 * @package default
 * @author Jan den Besten
 */
 class Formaction extends CI_Model {
   
   
   /**
    * Alle instellingen voor deze formactie, hier de defaults
    *
    * @var string
    */
   var $settings = array();
   
   /**
    * het Form id
    *
    * @var int
    */
   var $form_id = FALSE;
   
   /**
    * Velden van het formulier, zodat de labels etc bekend zijn
    *
    * @var string
    */
   var $fields=array();
   
   /**
    * Eventuel gegenereerde errorteksten komen hier
    *
    * @var string
    */
    var $errors='';
   


    /**
     * __construct
     *
     * @author Jan den Besten
     * @internal
     * @ignore
     */
    public function __construct() {
      parent::__construct();
    }
   

    /**
     * Initialiseer met alle meegegeven config waarden
     *
     * @param string $settings 
     * @return object self
     * @author Jan den Besten
     */
    public function initialize($settings) {
      $this->settings=array_merge($this->settings,$settings);
      return $this;
    }
   
    /**
     * Steld form id in
     *
     * @param int $form_id 
     * @return void
     * @author Jan den Besten
     */
    public function set_form_id($form_id) {
      $this->form_id=$form_id;
      return $this;
    }
   

    /**
     * Geef hier de formuliervelden mee, zodat de labels etc bekend zijn
     *
     * @param array $fields 
     * @return object self
     * @author Jan den Besten
     */
    public function fields($fields) {
      $this->fields=$fields;
      return $this;
    }



   /**
    * Voer de actie uit
    *
    * @param string $data data teruggekomen van het formulier
    * @return bool TRUE als actie goed is verlopen, anders FALSE
    * @author Jan den Besten
    */
  public function go($data) {
    $this->errors='';
    return true;
  }


  /**
   * Voegt error toe
   *
   * @param string $error 
   * @return void
   * @author Jan den Besten
   */
  public function add_error($error) {
    $this->errors.=$error;
    return $this;
  }


  /**
    * Als de actie niet goed is verlopen, dan kun je hiermee de foutmeldingen verkrijgen.
    *
    * @return string
    * @author Jan den Besten
    */
  public function get_errors() {
   return $this->errors;
  }

}
