<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
	* Loop langs tabel en voer actie uit.
  * Pas code aan in Plugin_foreach.php -> action()
	*
	*
	* @author Jan den Besten
	*/
 class Plugin_foreach extends Plugin {
   

	public function _admin_api($args=NULL) {
		if ( !$this->CI->flexy_auth->is_super_admin()) return false;

		$table = $this->config['table'];
		$this->CI->data->table($table);
		// $this->CI->data->with('many_to_many');
		$items = $this->CI->data->get_result();
		
		$this->add_message('<ul>');
		foreach ($items as $id => $item) {
			$this->action($item);
		}
		$this->add_message('</ul>');

    return $this->show_messages();
	}

	private function action($item) {

		// if (!$item['cfg_user_groups']) {
		// 	$this->CI->data->table('rel_users__groups')
		// 								->set( array(
		// 									'id_user' 			=> $item['id'],
		// 									'id_user_group' => 4,
		// 									)
		// 								)
		// 								->insert();

		// 	$this->add_message('<li>'.$item['str_username'].' -> USER GROUP </li>');

		// }

	$this->add_message('<li> action for '.$item['id'].'</li>');


	}



}

?>