<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cfg_users
 * 
 * @author: Jan den Besten
 * %Generated: Thu 28 April 2016, 17:02
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

Class cfg_users extends Data_Core {

  private $allowed_to_edit_users;
  private $groups;
  private $show_groups=FALSE;
  private $only_these_users = array();
  

  public function __construct() {
    parent::__construct();
    $this->load->library('flexy_auth');
    
    // Zorg ervoor dat huidige user is ingesteld
    $this->set_user_id();
    
    // Kijk of de gebruiker andere gebruikers mag aanpassen
    $this->allowed_to_edit_users = $this->flexy_auth->allowed_to_edit_users();
    $this->groups = $this->flexy_auth->get_user()['groups'];
    if ($this->groups) {
      $group_ids = array_keys($this->groups);
      $all_groups = $this->flexy_auth->groups()->result_array();

      // Welke andere user_groups mag deze user bekijken?
      $this->show_groups = array();
      foreach ($all_groups as $key => $group) {
        $show=FALSE;
        foreach ($group_ids as $group_id) {
          if ($group_id<=$group['id']) $show=TRUE;
        }
        if ($show) $this->show_groups[$group['id']]=$group['id'];
      }

      // Welke andere users mag deze user bekijken?
      $query = $this->db->query( 'SELECT `id_user` FROM `rel_users__groups` WHERE `id_user_group` IN ('.implode(',',$this->show_groups).')');
      if ($query) {
        $this->only_these_users = $query->result_array();
        foreach ($this->only_these_users as $key => $value) {
          $this->only_these_users[$key]=$value['id_user'];
        }
      }
    }
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
    if ($key==='current' and isset($this->user_id)) {
      $value = $this->user_id;
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
    if (empty( $this->tm_set )) {
      $this->reset();
      return FALSE;
    }
    
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
   * Zorg ervoor dat alleen users teruggegeven kunnen worden die dezelfde rechten hebben of meer.
   * En dat alleen administrators de user_group kunnen inzien.
   *
   * @param string $limit[0] 
   * @param string $offset[0] 
   * @param string $reset[true] 
   * @return object $query
   * @author Jan den Besten
   */
  public function get( $limit=0, $offset=0, $reset = true ) {
    if ($this->user_id) {
      
      // Als geen rechten om users aan te passen dan geen id_user_group tonen/aanpassen
      if ( !$this->allowed_to_edit_users ) {
        if (isset($this->tm_with['many_to_many']['rel_users__groups'])) {
          $result_name = $this->settings['relations']['many_to_many']['rel_users__groups']['result_name'];
          unset($this->tm_with['many_to_many']['rel_users__groups']);
          $this->unselect($result_name);
        }
      };

      // Alleen users tonen met minimaal zelfde rechten: usergroup id minimaal hetzelfde
      if (!$this->flexy_auth->is_super_admin()) {
        $this->where( $this->settings['table'].'.'.$this->settings['primary_key'], $this->only_these_users );
      }
    }
    return parent::get($limit,$offset,$reset);
  }
  
  /**
   * Zorg ervoor dat het password veld altijd als een leeg veld in een resultaat terecht komt
   *
   * @return $this
   * @author Jan den Besten
   */
  protected function _select() {
    parent::_select();
    if (isset($this->tm_select['gpw_password'])) {
      $this->tm_select['gpw_password'] = 'SPACE(0) AS `gpw_password`';
    }
    return $this;
  }
  
  
  /**
   * Zorg ervoor dat alleen de 'id_user_group' als opties teruggegeven worden waarvoor de gebruiker rechten heeft.
   *
   * @param string $field 
   * @param string $with 
   * @return void
   * @author Jan den Besten
   */
  public function get_options( $fields='', $with=array('many_to_many'), $as_object = TRUE ) {
    $options=parent::get_options($field,$with);
    if ($this->user_id) {
      if ( array_key_exists('rel_users__groups',$options) ) {
        foreach ($options['rel_users__groups']['data'] as $key=>$option) {
          if ( !in_array($key,$this->show_groups) ) {
            unset($options['rel_users__groups']['data'][$key]);
          }
        }
        $options['rel_users__groups']['multiple'] = $this->get_setting('multiple_groups',el('multiple',$options['rel_users__groups'],FALSE));
      }
    }
    return $options;
  }
  
  
}
