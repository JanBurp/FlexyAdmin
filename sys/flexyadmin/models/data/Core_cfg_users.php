<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * cfg_users
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Core_cfg_users extends Data_Core {

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
    $this->groups = $this->flexy_auth->get_user(NULL,'groups');
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
   * Aanpassing voor ->_update_insert():
   * - Leeg wachtwoord veld wordt uit de set gehaald. Hierdoor wordt alleen een wachtwoord aangepast als die een nieuwe waarde heeft en wordt een wachtwoord nooit leeg.
   * - Als email/username niet is veranderd, validatie aanpassen zodat niet wordt gecheck of het een unieke waare is
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
     * Haal huidige waarden op voor email & username om te vergelijken
     */
    $current = FALSE;
    $select = array('str_username','email_email');
    $select = array_intersect($select,array_keys($this->tm_set));
    if (!empty($select)) {
      $id = $this->tm_set[$this->settings['primary_key']];
      if ($id) {
        $sql = 'SELECT `'.implode('`,`',$select).'` FROM `'.$this->settings['table'].'` WHERE `'.$this->settings['primary_key'].'` = '.$id;
        $query = $this->db->query($sql);
        if ($query) $current = $query->row_array();
      }
    }
    
    foreach ( $this->tm_set as $key => $value ) {
      /**
       * Verwijder lege wachtwoorden uit de set, zodat die niet overschreven worden in de db
       */
      if ( empty($value) and in_array(get_prefix($key), $this->config->item('PASSWORD_field_types') ) ) {
        unset( $this->tm_set[$key] );
      }
      
      /**
       * Verwijder email en username als die hetzelfde zijn uit de set, updaten is niet nodig (en validatie ook niet)
       */
      if ($current) {
        if (in_array($key,$select)) {
          if ( $current_value=el($key,$current,NULL) and $current_value===$value) {
            unset( $this->tm_set[$key] );
          }
        }
      }
    }
    
    return parent::_update_insert($type,NULL,$where,$limit);
  }
  
  
  /**
   * Zorg ervoor:
   * - Dat alleen users teruggegeven kunnen worden die dezelfde rechten hebben of meer.
   * - Dat alleen administrators de user_group kunnen inzien.
   *
   * @param string $limit[0] 
   * @param string $offset[0] 
   * @param string $reset[true] 
   * @return object $query
   * @author Jan den Besten
   */
  public function get( $limit=0, $offset=0, $reset = true ) {
    
    if ($this->user_id and $this->tm_as_grid) {

      // Als geen rechten om users aan te passen dan geen id_user_group tonen/aanpassen
      if ( !$this->allowed_to_edit_users ) {
        if (isset($this->tm_with['many_to_many']['rel_users__groups'])) {
          // unselect
          $result_name = $this->settings['relations']['many_to_many']['rel_users__groups']['result_name'];
          $this->unselect($result_name);
          // niet in with
          unset($this->tm_with['many_to_many']['rel_users__groups']);
          if (empty($this->tm_with['many_to_many'])) unset($this->tm_with['many_to_many']);
          if (empty($this->tm_with)) $this->tm_with = FALSE;
          // Ook niet in formset
          $this->settings['form_set']['with'] = $this->tm_with;
          if (isset($this->settings['form_set']['fields'])) {
            $key = array_search($result_name,$this->settings['form_set']['fields']);
            if ($key!==FALSE) unset($this->settings['form_set']['fields'][$key]);
          }
          if (isset($this->settings['form_set']['fieldsets'])) {
            foreach ($this->settings['form_set']['fieldsets'] as $set => $fields) {
              $key = array_search($result_name,$this->settings['form_set']['fieldsets'][$set]);
              if ($key!==FALSE) unset($this->settings['form_set']['fieldsets'][$set][$key]);
            }
          }
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
    $this->select_hidden_password('gpw_password');
    return parent::_select();
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
    $options = parent::get_options($fields,$with,$as_object);
    if ($this->user_id and $options) {
      if (el('table',$options)==='cfg_user_groups') {
        foreach ($options['data'] as $key=>$option) {
          if ( !in_array($option['value'],$this->show_groups) ) {
            unset($options['data'][$key]);
          }
        }
        $options['multiple'] = $this->get_setting('multiple_groups',FALSE);
      }
    }
    return $options;
  }
  
  
  /**
   * Voeg speciale acties toe om users uit te nodigen en/of wachtwoord op te sturen
   *
   * @param int $limit 
   * @param int $offset 
   * @return $this
   * @author Jan den Besten
   */
  public function get_grid( $limit = 20, $offset = FALSE ) {
    $result = parent::get_grid($limit,$offset);
    foreach ($result as $key => $user) {
      $id   = $user['id'];
      $email= $user['email_email'];
      $user = array_add_after($user,'id', array(
        'action_user_invite' => array(
          'uri'   => $user['b_active']?'user?action=new&email='.$email:'user?action=invite&email='.$email,
          'icon'  => 'envelope-o', 
          'text'  => $user['b_active']?lang('action_user_send_password'):lang('action_user_send_invite'),
        ))
      );
      $result[$key] = $user;
    }
    
    // Voeg het veld toe
    $this->settings['grid_set']['fields'] = array_add_after($this->settings['grid_set']['fields'],'id','action_user_invite');
    return $result;
  }

  /**
   * Field info aanpassen in grid_set
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_grid_set() {
    $grid_set = parent::get_setting_grid_set();
    $grid_set['field_info']['action_user_invite'] = array(
      'type'         => 'action',

      'action'       => array(
        'selected_only' => true,
        'name'          => lang('action_user_send_password_selected'),
        'url'           => 'user?action=new',
        'icon'          => 'envelope-o',
      ),

      'name'      => lang('action_users'),
      'grid-type' => 'action',
    );
    return $grid_set;
  }


  /**
   * Zorgt ervoor cfg_user_groups één waarde is (behalve als $config['multiple_groups'] = TRUE)
   *
   * @param mixed $where 
   * @return array
   * @author Jan den Besten
   */
  public function get_form( $where = '' ) {
    $row = parent::get_form($where);
    if ( !el('multiple_groups',$this->settings,FALSE) ) {
      if (isset($row['cfg_user_groups']) and count($row['cfg_user_groups'])<=1) {
        $group = current($row['cfg_user_groups']);
        $group = $group['id'];
        $row['cfg_user_groups'] = $group;
      }
    }
    return $row;
  }
  
  
}
