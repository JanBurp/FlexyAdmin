<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stel hier in of er een samengesteld menu moet komen, er zijn verschillende opties:
 * 
 * - Extra items aan het menu toevoegen
 * - Extra items vanuit een andere tabel toevoegen
 * - Extra items toevoegen met een model.method
 * - Splitsen op taal
 * 
 * Eén item toevoegen
 * ==================
 * 
 * Onderstaand item komt aan het eind van het menu, in de root:
 * 
 *   array(
 *    'where'      => FALSE,
 *    'type'       => 'item',
 *    'item'       => array(
 *      'uri'       => 'extra',
 *      'str_title' => 'Extra Pagina',
 *      'txt_text'  => '<p>Het einde</p>',
 *    ),
 *  )
 * 
 * 
 * Tabel toevoegen
 * ===============
 * 
 * Extra menu-items worden uit de tabel tbl_blog gehaald en als subpagina van de pagina met de uri 'blog' geplaatst.
 * 
 *  array(
 *    'where'      => 'blog,
 *    'type'       => 'table',
 *    'item'       => array(
 *      'table'    => 'tbl_blog',
 *      'order_by' => 'dat_date',
 *      'limit'    => 10,
 *      'offset'   => 0,
 *      'where'    => NULL,
 *    )
 *  )
 * 
 * Tabel verdeel over subpagina's toevoegen
 * ========================================
 * 
 * ...
 */


/**
 * Bewaar cache van menu.
 * Tijdens developen kan het handig zijn dit uit te zetten
 */

$config['caching'] = TRUE;

/**
 * Splits het menu in meerdere talen
 */
// $config['languages'] 				= array('nl','en');
// $config['language_fields']		= array('str_title','txt_text');



/**
 * Default: Alleen tbl_menu
 * (deze variant hoeft zelfs niet ingesteld te worden, zo default is die ;-)
 */

// $config['menu'] = array(
//   array(
//     'type'  => 'table',
//     'table' => 'tbl_menu',
//   ),
// );
//


// /**
//  * Simpel extra menu-item, naast tbl_menu
//  */
// $config['menu'] = array(
//   array(
//     'type'  => 'table',
//     'table' => 'tbl_menu',
//   ),
//   array(
//     'type'      => 'item',
//     'uri'       => 'laatste',
//     'str_title' => 'Laatste Pagina',
//     'txt_text'  => '<p>Het einde</p>',
//   ),
// );


/**
 * Voorbeeld waarbij blog-items als subpagina's onder de pagina met uri='blog' komen
 * (Met wat extra parameters die meegegeven kunnen worden. Hier met hun defaukt instellingen)
 */
// $config['menu'] = array(
//   array(
//     'type'  => 'table',
//     'table' => 'tbl_menu',
//   ),
//   array(
//     'place'    => 'blog',
//     'type'     => 'table',
//     'table'    => 'tbl_blog',
//     'order_by' => 'dat_date DESC',
//     'limit'    => 0,
//     'offset'   => 0,
//     'where'    => NULL,
//   ),
// );


/**
 * Zelfde als hierboven, maar nu als subpagina's onder de pagina met str_module='blog'
 */
// $config['menu'] = array(
//   array(
//     'type'  => 'table',
//     'table' => 'tbl_menu',
//   ),
//   array(
//     'place'    => array('str_module'=>'blog'),
//     'type'     => 'table',
//     'table'    => 'tbl_blog',
//     'order_by' => 'dat_date DESC',
//     'limit'    => 0,
//     'offset'   => 0,
//     'where'    => NULL,
//   ),
// );


/**
 * Voorbeeld waarbij items vanuit een andere tabel als subpagina's kan worden toegevoegd, maar dan afhankelijk van het bestaande menu-item.
 * Ander tabel geeft dan een koppeling met id_menu.
 */
// $config['menu'] = array(
//   array(
//     'type'  => 'table',
//     'table' => 'tbl_menu',
//   ),
// 	array(
//   	'place'    => array( 'str_module'=>'category' ),
//   	'type'     => 'grouped',
//   	'table'    => 'tbl_projecten',
//   	'grouped_by' => array( 'id' => 'tbl_projecten.id_menu' ),
// 	),
// );

/**
 * Voorbeeld waarbij items doormiddel van een model worden toegevoegd
 */
// $config['menu'] = array(
//   array(
//     'type'  => 'table',
//     'table' => 'tbl_menu',
//   ),
// 	 array(
// 	   'type'       => 'model',
// 	   'place'      => array( 'str_module' => 'example' ),
// 	   'model'      => 'example.method',
// 	 ),
//   
// );


