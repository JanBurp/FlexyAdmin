<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * Uitbreiding op [CI_Router](http://codeigniter.com/user_guide/general/routing.html)
 *
 * Puur om #[AllowDynamicProperties] toe te voegen, ivm PHP 8.2+ deprecation notice.
 * Was voorheen direct in sys/codeigniter/core/Router.php gepatcht, maar dat bestand
 * zit nu in de pocketarc/codeigniter Composer package en wordt overschreven bij elke update.
 *
 * @author Jan den Besten
 */

#[AllowDynamicProperties]
class MY_Router extends CI_Router
{

}
