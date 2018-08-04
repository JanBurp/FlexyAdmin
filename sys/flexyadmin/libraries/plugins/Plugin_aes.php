<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AES decrypt/encrypt
 *
 * Geef: /plugin/aes/decrypt|encrypt/tbl_...
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Plugin_aes extends Plugin {

	private $crypts = array(
		'encrypt' => array(
			'AES' 		=> 'AES_ENCRYPT',
			'message'	=> 'encryped',
		),
		'decrypt' => array(
			'AES' 		=> 'AES_DECRYPT',
			'message'	=> 'decrypted',
		),
	);

  public function __construct() {
    parent::__construct();
  }

	public function _admin_api($args=NULL) {
		if ( !$this->CI->flexy_auth->is_super_admin()) return false;

		if (count($args)==2) {
			$method = array_shift($args);
			$table  = array_shift($args);

			if ($this->CI->data->table_exists($table) and in_array($method,array('encrypt','decrypt'))) {
				$this->_crypt($method,$table);
			}
		}

    return $this->show_messages();
	}

	// http://thinkdiff.net/mysql/encrypt-mysql-data-using-aes-techniques/
	private function _crypt($method,$table) {
		$this->CI->data->table($table);
		$field_info = $this->CI->data->get_setting('field_info');
		$encrypted_fields = array();
		foreach ($field_info as $field => $info) {
			if (isset($info['encrypted']) and $info['encrypted']==true) {
				$encrypted_fields[] = $field;
			}
		}
		if (count($encrypted_fields)>0) {

			// Change fields to BINARY variants?
			if ($method==='encrypt') {
				$field_data = $this->CI->data->field_data();
				$field_data = array_keep_keys($field_data,$encrypted_fields);
				foreach ($field_data as $field => $info) {
					if ($info['type']!=='varbinary') {
						$new_length = ceil(16 * (($info['max_length']/16) + 1));
						$new_length = ceil($new_length / 50) * 50;
						$sql = 'ALTER TABLE `'.$table.'` CHANGE `'.$field.'` `'.$field.'` VARBINARY('.$new_length.')  NOT NULL DEFAULT ""';
						if ($this->CI->db->query($sql)) {
							$this->add_message("<p>`<b>$table`.`$field</b>` field type & length changed</p>");
						}
						else {
							$this->add_message("<p>`<b>$table`.`$field</b>` SQL ERROR WHILE CHANGING FIELD</p>");	
						}
					}
				}
			}

			foreach ($encrypted_fields as $field) {
				$sql = 'UPDATE `'.$table.'` SET `'.$field.'` = '.$this->crypts[$method]['AES'].'(`'.$field.'`,"'.$this->CI->config->item('encryption_key').'")';
				if ($this->CI->db->query($sql)) {
					$this->add_message("<p>`<b>$table`.`$field</b>` is ".$this->crypts[$method]['message']."</p>");
				}
				else {
					$this->add_message("<p>`<b>$table`.`$field</b>` SQL ERROR</p>");	
				}
			}
			$this->CI->data->update();
		}
		else {
			$this->add_message("<p>`<b>$table`</b>` has no ".$this->crypts[$method]['message']." fields set in config.</p>");
		}
	}

}

?>