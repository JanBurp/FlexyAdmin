<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['after_update_method']  = '_after_update';

/*
 *--------------------------------------------------------------------------
 * Plugin Update/Delete Triggers
 * Here you need to set when the update and delete methods of you're plugin are called
 *--------------------------------------------------------------------------
 *
 */

$config['trigger'] = array(
	'field_types'			=> array('txt'),
);

?>
