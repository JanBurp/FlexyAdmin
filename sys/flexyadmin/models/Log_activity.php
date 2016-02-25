<?php
/**
 * Log van activiteit
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Log_activity extends CI_Model {
  
  /**
   * Bewaarperiode voor log-items (in unixtime seconds)
   */
  private $remember_period = TIME_YEAR; // Standaard, verderop ingezet op half jaar
  
  public function __construct() {
    parent::__construct();
    $this->remember_period = TIME_YEAR/4;
  }
  
  /**
   * Voegt iets toe aan log
   *
   * @param string $type (auth|database|media|email) soort activiteit
   * @param string $activity sql query of technisch omschrijving
   * @param string $model ['']
   * @param string $key ['']
   * @param int $user_id [FALSE]
   * @return int id
   * @author Jan den Besten
   */
  public function add($type,$activity,$model='',$key='',$user_id=FALSE) {
    if (!defined('PHPUNIT_TEST') and $this->db->table_exists('log_activity')) {
      if (!$user_id) $user_id = $this->session->userdata("user_id");
      if (!$user_id) $user_id = 0;
      $this->db->set( 'id_user',$user_id );
      $this->db->set( 'str_activity_type',$type );
      $this->db->set( 'stx_activity',$activity );
      if ($model)       $this->db->set( 'str_model',$model );
      if ($key)         $this->db->set( 'str_key',$key );
      $this->db->insert( 'log_activity' );
      //
      $this->clean_up();
    }
  }
  
  public function database( $activity,$model='',$key='' ) {
    return $this->add('database',$activity,$model,$key);
  }

  public function auth( $activity='', $user_id = FALSE ) {
    if (empty($activity)) $activity = 'login';
    return $this->add('auth',$activity,'','',$user_id);
  }

  public function media( $activity,$model='',$key='' ) {
    return $this->add('media',$activity,$model,$key);
  }

  public function email( $activity,$model='',$key='' ) {
    return $this->add('email',$activity,$model,$key);
  }
  
  /**
   * Verwijderd items ouder de ingestelde bewaarperiod
   *
   * @return int Aantal verwijderde items
   * @author Jan den Besten
   */
  public function clean_up() {
    $this->db->where( 'tme_timestamp <', unix_to_mysql(time()-$this->remember_period) );
    $this->db->delete( 'log_activity' );
    $deleted = $this->db->affected_rows();
    return $deleted;
  }


  /**
   * Geeft laatste user activiteit
   *
   * @param string $user_id 
   * @param int $limit [10] 
   * @return void
   * @author Jan den Besten
   */
  public function get_user_activity( $user_id=FALSE, $limit=10 ) {
    if (!$this->db->table_exists('log_activity')) return array();
    if (!$user_id) $user_id = $this->session->userdata("user_id");
    $query = $this->db->query("SELECT DISTINCT `id_user`,`tme_timestamp`, `str_model` FROM `log_activity` WHERE `str_activity_type`='database' OR `str_activity_type`='media'  ORDER BY `tme_timestamp` DESC LIMIT ".$limit);
    return $query->result_array();
  }

}
?>
