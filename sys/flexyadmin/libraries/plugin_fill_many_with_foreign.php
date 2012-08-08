<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Plugin_fill_many_with_foreign extends Plugin {

	function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $relTable=$args[0];
				if (isset($args[1])) {
					$foreignKey=$args[1];
					$table=get_prefix($foreignKey,'.');
					$foreignKey=get_suffix($foreignKey,'.');
					$thisKey='id_'.get_suffix($table);
				}
				if (isset($relTable) and isset($table) and isset($foreignKey)) $goodArgs=true;
				$this->add_content(h("Filling '$relTable' from '$table.$foreignKey'.",2));
				// first emtpy many table
				$this->CI->db->truncate($relTable);
				// now fill
				$this->CI->db->select('id,'.$foreignKey);
				$data=$this->CI->db->get_result($table);
				foreach ($data as $id => $row) {
					$this->CI->db->set($thisKey,$id);
					$this->CI->db->set($foreignKey,$row[$foreignKey]);
					$this->CI->db->insert($relTable);
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Which many table and foreign key?</br></br>Give: /rel_xxxx__xxxx/tbl_xxx.id_xxx</p>');
			}
		}
	}


}

?>