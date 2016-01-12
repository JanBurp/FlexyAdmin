<?php
/**
 * Log van activiteit
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Log_activity extends CI_Model {
  
  public function __construct() {
    parent::__construct();
  }
  
  /**
   * Voegt iets toe aan log
   *
   * @param string $type (auth|database|media|email) soort activiteit
   * @param string $activity sql query of technisch omschrijving
   * @param string $description leesbare uitleg voor mensen
   * @return int id
   * @author Jan den Besten
   */
  public function add($type,$activity,$description='') {
    if (!defined('PHPUNIT_TEST')) {
      $user_id = $this->session->userdata("user_id");
      $this->db->set( 'id_user',$user_id );
      $this->db->set( 'str_activity_type',$type );
      $this->db->set( 'stx_activity',$activity );
      if ($description) $this->db->set( 'str_description',$description );
      $this->db->insert( 'log_activity' );
    }
  }
  
  
  public function database( $activity,$description='' ) {
    return $this->add('database',$activity,$description);
  }

  public function auth( $activity='',$description='' ) {
    if (empty($activity)) $activity = 'logged in';
    return $this->add('auth',$activity,$description);
  }

  public function media( $activity,$description='' ) {
    return $this->add('media',$activity,$description);
  }

  public function email( $activity,$description='' ) {
    return $this->add('email',$activity,$description);
  }


  /**
   * Geeft laatste user activiteit
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   */
  public function get_user_activity( $user_id, $limit=5 ) {
		$this->db->select( 'tme_timestamp, str_description' );
		$this->db->where( 'id_user', $user_id);
		$this->db->order_by( 'tme_timestamp DESC' );
		$query = $this->db->get( 'log_activity', $limit );
    return $query->result_array();
  }

}
?>
