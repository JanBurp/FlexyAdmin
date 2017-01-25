<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'cfg_users' --- Created @ Thu 28 April 2016, 17:02 */

$config['table']    = 'cfg_users';
$config['order_by'] = 'str_username,id';

$config['relations'] = array(
  'many_to_many' => array (
    'rel_users__groups' => array(
      'this_table'  => 'cfg_users',
      'other_table' => 'cfg_user_groups',
      'rel_table'   => 'rel_users__groups',
      'this_key'    => 'id_user',
      'other_key'   => 'id_user_group',
      'result_name' => 'rel_users__groups'
    ),
  ),
);

$config['grid_set'] = array(
  'fields'    => array( 'id','str_username', 'rel_users__groups', 'email_email', 'str_language','b_active'),
  'order_by'  => 'rel_users__groups.id, str_username',
  'with'      => array( 'many_to_many' ),
);

$config['form_set'] = array(
  'fieldsets' => array('Users'=>array( 'id','str_username', 'rel_users__groups', 'gpw_password', 'email_email', 'str_language')),
  'with'      => array( 'many_to_many' ),
);


$config['multiple_groups'] = FALSE;
