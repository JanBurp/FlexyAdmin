<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'cfg_users' --- Created @ Thu 28 April 2016, 17:02 */

$config['table']           = 'cfg_users';
$config['cache_group']     = array('cfg_users','cfg_user_groups','rel_users__groups');
$config['fields']          = array( 'id','str_username', 'email_email', 'str_language','str_filemanager_view','b_active');
$config['order_by']        = 'str_username,id';
$config['abstract_fields'] = array('str_username');

$config['options'] = array( 
  'str_language' => array(
    'data' => array_combine($this->config['languages'],$this->config['languages']),
  ),
);

$config['relations'] = array(
  'many_to_many' => array (
    'rel_users__groups' => array(
      'this_table'  => 'cfg_users',
      'other_table' => 'cfg_user_groups',
      'rel_table'   => 'rel_users__groups',
      'this_key'    => 'id_user',
      'other_key'   => 'id_user_group',
      'result_name' => 'cfg_user_groups'
    ),
  ),
);

$config['grid_set'] = array(
  'fields'    => array( 'id','str_username', 'email_email','cfg_user_groups', 'b_active' ),
  'order_by'  => 'cfg_user_groups.id, str_username',
  'with'      => array( 'many_to_many' ),
);

$config['form_set'] = array(
  'fieldsets' => array(
    'cfg_users' => array( 'id','str_username', 'email_email', 'gpw_password', 'cfg_user_groups' )),
  'with'      => array( 'many_to_many' ),
);

// Taal veld alleen als nodig
if (count($this->config['languages'])>1) {
  if (isset($config['grid_set']['fields'])) array_push($config['grid_set']['fields'],'str_language' );
  if (isset($config['form_set']['fieldsets']['cfg_users'])) array_push($config['form_set']['fieldsets']['cfg_users'],'str_language' );
}


$config['multiple_groups'] = FALSE;
