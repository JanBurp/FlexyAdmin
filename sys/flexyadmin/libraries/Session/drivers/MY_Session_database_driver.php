<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Uitbreiding op CI_Session_database_driver, puur om #[\ReturnTypeWillChange] toe
 * te voegen aan de SessionHandlerInterface methodes (open/read/write/close/destroy/gc),
 * ivm PHP 8.1+ deprecation notice. Was voorheen direct in
 * sys/codeigniter/libraries/Session/drivers/Session_database_driver.php gepatcht, maar
 * dat bestand zit nu in de pocketarc/codeigniter Composer package en wordt overschreven
 * bij elke update. $config['sess_driver'] = 'database', dus deze driver is actief.
 *
 * @author Jan den Besten
 */
class MY_Session_database_driver extends CI_Session_database_driver {

	#[\ReturnTypeWillChange]
	public function open($save_path, $name)
	{
		return parent::open($save_path, $name);
	}

	#[\ReturnTypeWillChange]
	public function read($session_id)
	{
		return parent::read($session_id);
	}

	#[\ReturnTypeWillChange]
	public function write($session_id, $session_data)
	{
		return parent::write($session_id, $session_data);
	}

	#[\ReturnTypeWillChange]
	public function close()
	{
		return parent::close();
	}

	#[\ReturnTypeWillChange]
	public function destroy($session_id)
	{
		return parent::destroy($session_id);
	}

	#[\ReturnTypeWillChange]
	public function gc($maxlifetime)
	{
		return parent::gc($maxlifetime);
	}

}
