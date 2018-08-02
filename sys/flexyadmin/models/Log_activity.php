<?php
/**
 * Log van activiteit
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Log_activity extends CI_Model {
  
  /**
   * Bewaarperiode voor log-items (in unixtime seconds)
   */
  private $remember_period = TIME_YEAR; // Standaard, verderop ingezet op half jaar
  
  public function __construct() {
    parent::__construct();
    $this->load->library('session');
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
    if ( !defined('PHPUNIT_TEST') and $this->db->table_exists('log_activity') ) {
      if (!$user_id) $user_id = $this->session->userdata("user_id");
      if (!$user_id) $user_id = 0;
      $activity = str_replace( 'AES_ENCRYPT(', '', $activity);
      $activity = str_replace( ',"'.$this->config->item('encryption_key').'")', '', $activity);
      $activity = str_replace( $this->config->item('encryption_key'), '***', $activity);
      $this->db->set( 'id_user',$user_id );
      $this->db->set( 'str_activity_type',$type );
      $this->db->set( 'stx_activity',$activity );
      $this->db->set( 'str_model',$model );
      $this->db->set( 'str_key',$key );
      if ($this->db->field_exists('ip_address','log_activity')) $this->db->set( 'ip_address', $this->input->ip_address() );
      $this->db->insert( 'log_activity' );
    }
  }
  
  public function database( $activity,$model='',$key='' ) {
    return $this->add('database',$activity,$model,$key);
  }

  public function api($args,$user) {
    return $this->add('api',array2json($args),$this->uri->uri_string(),$user);
  }

  public function auth( $activity='', $user_id = FALSE ) {
    if (empty($activity)) $activity = 'login';
    return $this->add('auth',$activity,'','',$user_id);
  }

  public function media( $activity, $path='',$key='' ) {
    return $this->add('media',$activity,$path,$key);
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

  /**
   * Geeft laatste user activiteit gegroupeerd weer (één rij per user per dag)
   *
   * @param string $user_id 
   * @param string $limit 
   * @return void
   * @author Jan den Besten
   */
  public function get_grouped_user_activity( $user_id=FALSE, $limit=10 ) {
    if (!$this->db->table_exists('log_activity')) return array();
    if (!$user_id) $user_id = $this->session->userdata("user_id");
    $query = $this->db->query( "SELECT DISTINCT `id_user`, DATE_FORMAT( `tme_timestamp`, '%Y-%m-%d %H:%i') AS `tme_timestamp`, `str_model`,`str_activity_type` FROM `log_activity` WHERE (`str_activity_type`='database' OR `str_activity_type`='media') AND SUBSTRING(`str_model`,1,3)!='log' ORDER BY `tme_timestamp` DESC LIMIT ".$limit*20 );
    $result = $query->result_array();
    $user = FALSE;
    $user_row_id = 0;
    foreach ($result as $id => $row) {
      $model = $row['str_model'];
      if ($row['str_activity_type']==='media') $model = 'media_'.$model;
      if ($row['id_user']===$user) {
        if (is_string($result[$user_row_id]['str_model'])) $result[$user_row_id]['str_model'] = array();
        $result[$user_row_id]['str_model'][] = $model;
        unset($result[$id]);
      }
      else {
        $user = $row['id_user'];
        $user_row_id = $id;
        if (is_string($result[$user_row_id]['str_model'])) $result[$user_row_id]['str_model'] = array();
        $result[$id]['str_model'][] = $model;
      }
      unset($result[$id]['str_activity_type']);
    }
    foreach ($result as $id => $row) {
      $result[$id]['str_model'] = implode(' | ',$this->lang->ui( array_unique($row['str_model'])) );
    }
    return $result;
  }
  
  

}
?>
