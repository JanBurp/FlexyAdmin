<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cfg_users - autogenerated Table_model for table cfg_users
 * 
 * @author: Jan den Besten
 * %Generated: Thu 28 April 2016, 17:02
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

Class cfg_users extends Data_Core {

  public function __construct() {
    parent::__construct();
    // Zorg ervoor dat huidige user is ingesteld
    $this->set_user_id();
  }

  /**
   * Aanpassing voor ->where() zodat ook 'current' opgevraagd kan worden
   *
   * @param string $key 
   * @param string $value 
   * @param string $escape 
   * @return void
   * @author Jan den Besten
   */
  public function where($key, $value = NULL, $escape = NULL) {
    if ($key==='current' and isset($this->user->user_id)) {
      $value = $this->user->user_id;
      $key = $this->settings['primary_key'];
    }
    return parent::where($key,$value,$escape);
  }
  
  
  /**
   * Aanpassing voor ->_update_insert(): leeg wachtwoord veld wordt uit de set gehaald.
   * Hierdoor wordt alleen een wachtwoord aangepast als die een nieuwe waarde heeft en wordt een wachtwoord nooit leeg.
   *
   * @param string $type 
   * @param string $set 
   * @param string $where 
   * @param string $limit 
   * @return void
   * @author Jan den Besten
   */
	protected function _update_insert( $type, $set = NULL, $where = NULL, $limit = NULL ) {
    // Geef de set alvast door en test deze ook alvast
    if (isset($set)) $this->set($set);
    if (empty( $this->tm_set )) return FALSE;
    
    /**
     * Verwijder lege wachtwoorden uit de set, zodat die niet overschreven worden in de db
     */
    foreach ( $this->tm_set as $key => $value ) {
      if ( empty($value) and in_array(get_prefix($key), $this->config->item('PASSWORD_field_types') ) ) {
        unset( $this->tm_set[$key] );
      }
    }
    
    return parent::_update_insert($type,NULL,$where,$limit);
  }
  
  
  /**
   * Zorg ervoor dat alleen users teruggegeven kunnen worden die dezelfde rechten hebben of meer
   *
   * @param string $limit[0] 
   * @param string $offset[0] 
   * @param string $reset[true] 
   * @return object $query
   * @author Jan den Besten
   */
  public function get( $limit=0, $offset=0, $reset = true ) {
    if ($this->user_id) {
      $group_id = $this->user->group_id;
      $this->data->where( '`cfg_users`.`id_user_group` >=', $group_id );
    }
    return parent::get($limit,$offset,$reset);
  }
  
  

}
