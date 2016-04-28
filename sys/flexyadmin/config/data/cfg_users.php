<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'cfg_users' --- Created @ Thu 28 April 2016, 17:02 */


/**
 * Database tabel waarvoor deze instellingen (en model) geld.
 */
$config['table'] = 'cfg_users';

/**
 * Hier worden de relaties ingesteld die deze tabel heeft.
 * 
 * De array is onderverdeeld per relatiesoort ('many_to_one','many_to_many').
 * 
 * 'many_to_one'
 * -------------
 * 
 * De foreign_key is de key van de array van de relatie.
 * Daarin komen de volgende velden:
 * 
 * - other_table  -> de foreign table
 * - foreign_keys -> nogmaals de foreign_key
 * - result_name  -> onder welke naam de relatie data wordt toegevoegd. Standaard hetzelfde als 'other_table', maar bijvoorbeeld als er meerdere verwijzingen naar dezelfde other_table zijn, dan geeft dat problemen. Dat kan dan worden opgelost door hier een andere naam te geven.
 * 
 * Hieronder enkele voorbeelden voor many_to_one:
 *   
 * array(
 * 
 *  'many_to_one' => array(
 *
 *    'id_links'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_links',
 *      'result_name' => 'tbl_links',
 *    ),
 *
 *    'id_links_extra'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_links_extra',
 *      'result_name' => 'links_extra',
 *    ),
 * 
 *    'id_user_groups' => array(
 *      'other_table' => 'cfg_users_groups',
 *      'foreign_key' => 'id_user_group',
 *      'result_name' => 'cfg_users_groups',
 *    )
 * 
 * 'one_to_many'
 * -------------
 * 
 * De other_table is de key van de array van de relatie.
 * Daarin komen de volgende velden:
 * 
 * - other_table  -> de foreign table (nogmaals)
 * - foreign_keys -> de foreign_key in de other_table
 * - result_name  -> onder welke naam de relatie data wordt toegevoegd. Standaard hetzelfde als 'other_table', maar bijvoorbeeld als er meerdere verwijzingen naar dezelfde other_table zijn, dan geeft dat problemen. Dat kan dan worden opgelost door hier een andere naam te geven.
 * 
 * Hieronder enkele voorbeelden voor many_to_one:
 *   
 * array(
 * 
 *  'one_to_many' => array(
 *
 *    'tbl_links'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_post',
 *      'result_name' => 'tbl_links',
 *    ),
 *
 *    'tbl_links_extra'  => array(
 *      'other_table' => 'tbl_links',
 *      'foreign_key' => 'id_post',
 *      'result_name' => 'links_extra',
 *    ),
 * 
 *    'cfg_user_groups' => array(
 *      'other_table' => 'cfg_users_groups',
 *      'foreign_key' => 'id_user_group',
 *      'result_name' => 'cfg_users_groups',
 *    )
 * 
 * 
 * 'many_to_many'
 * --------------
 * 
 * De relatie tabel is de key van de array van de relatie.
 * Daarin komen de volgende velden:
 * 
 * - other_table  -> de andere table
 * - rel_table    -> de relatie table (nogmaals)
 * - this_key     -> key die verwijst naar de eigen tabel
 * - other_key    -> key die verwijst naar de andere tabel
 * - result_name  -> onder welke naam de relatie data wordt toegevoegd. Standaard hetzelfde als 'other_table', maar bijvoorbeeld als er meerdere verwijzingen naar dezelfde other_table zijn, dan geeft dat problemen. Dat kan dan worden opgelost door hier een andere naam te geven.
 * 
 * Hieronder enkele voorbeelden voor many_to_many:
 * 
 *  'many_to_many' => array(
 *
 *    'rel_menu__links' => array(
 *      'other_table' => 'tbl_links',
 *      'rel_table'   => 'rel_menu__links',
 *      'this_key'    => 'id_menu',
 *      'other_key'   => 'id_links',
 *      'result_name' => 'tbl_links,
 *    ),
 *    
 *    'rel_menu__linksextra' => array(
 *      'other_table' => 'tbl_links',
 *      'rel_table'   => 'rel_menu__linksextra',
 *      'this_key'    => 'id_menu',
 *      'other_key'   => 'id_links',
 *      'result_name' => 'linksextra,
 *    ),
 * 
 *    'cfg_users_groups' => array(
 *      'other_table' => 'cfg_users_groups',
 *      'rel_table'   => 'rel_users__users_groups',
 *      'this_key'    => 'id_user',
 *      'other_key'   => 'id_user_group'
 *      'result_name' => 'cfg_users_groups,
 *    ),
 *    
 */
$config['relations'] = array( 
		'many_to_one' => array( 
				'id_user_group' => array( 
						'other_table' => 'cfg_user_groups', 
						'foreign_key' => 'id_user_group', 
						'result_name' => 'cfg_user_groups', 
					), 
			), 
	);

