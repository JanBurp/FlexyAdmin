<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['admin_api_method'] = '_admin_api';


/**
 * Stel hier in welke acties kunnen worden gebruikt
 */

$config['actions'] = array(
  'deny'          => TRUE,
  'accept'        => TRUE,
  'accept_send'   => FALSE,
  'all'           => TRUE
);


$config['active_actions'] = array(
  'send_new_password'  => TRUE,
);



/**
 * Als een inlog meerdere emailadressen heeft, in welke tabel is die dan te vinden?
 * - Die tabel moet een veld id_user hebben
 * - De emailadressen zijn te vinden in alle velden die met email_ beginnen
 */
$config['extra_email_table'] = '';


/**
 * De minimale user_group die deze plugin mag aanroepen
 * 1 - super_admin
 * 2 - admin
 * 3 - user
 * 4 - visiter
 */

$config['user_group'] = 1;



?>