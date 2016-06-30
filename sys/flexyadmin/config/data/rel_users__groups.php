<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'rel_users__groups' --- Created @ Mon 23 May 2016, 12:23 */


$config['table'] = 'rel_users__groups';
$config['primary_key'] = 'id';
$config['result_key'] = 'id';
$config['fields'] = array('id','id_user','id_user_group');
$config['field_info'] = array(
    'id'            => array(
        'default'    => -1,
        'validation' => array('trim','integer','required'),
      ),
    'id_user'       => array(
        'default'    => NULL,
        'validation' => array('trim','integer','required'),
      ),
    'id_user_group' => array(
        'default'    => NULL,
        'validation' => array('trim','integer'),
      ),
  );
$config['order_by'] = 'id_user';
$config['abstract_fields'] = array('id_user','id_user_group');

$config['relations'] = array(
  'many_to_one'  => array(
      'id_user'       => array(
          'other_table' => 'cfg_users',
          'foreign_key' => 'id_user',
          'result_name' => 'cfg_users',
        ),
      'id_user_group' => array(
          'other_table' => 'cfg_user_groups',
          'foreign_key' => 'id_user_group',
          'result_name' => 'cfg_user_groups',
        ),
    ),
);