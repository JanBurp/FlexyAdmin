<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class Plugin_create_indexes extends Plugin {

	public function _admin_api($args=NULL) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$this->_create_indexes();
	 	}
    return $this->content;
	}


	private function _create_indexes() {
		$tables=$this->CI->db->list_tables();

		foreach ($tables as $table) {
			$pre=get_prefix($table);
			if ($pre!='log' and !in_array($table,array('cfg_sessions'))) {

				$this->add_content(h('Creating indexes for: '.$table,2));
				$fields = $this->CI->db->field_data($table);
				$fields=object2array($fields);
				foreach ($fields as $key => $field) { $fields[$key]['pre']=get_prefix($field['name']); }

				foreach ($fields as $key => $field) {
					if ( ! $field['primary_key'] and ( 
									(in_array($field['name'],array('uri','self_parent','order')))
							or 	($field['pre']=='id') 
							or  ($table=='res_menu_result' and in_array($field['name'],array('int_id','str_uri','str_table')))
									)) {
								
						// DROP index if exists
						$sql='SHOW INDEX FROM `'.$table.'`; ';
						$query=$this->CI->db->query($sql);
						$res=$query->result_array();
						foreach ($res as $key => $r) {
							if ($r['Key_name']==$field['name']) {
								$sql="ALTER TABLE `$table` DROP INDEX `".$field['name'].'`; ';
								$this->CI->db->query($sql);
							}
						}
						// CREATE index
						$index=$field['name'];
						$column=$field['name'];
						// if ($field['name']=='uri') $index='uri(20)';
						$sql="ALTER TABLE `$table` ADD INDEX `".$index.'`(`'.$column.'`);';
						if ($this->CI->db->query($sql))
							$this->add_content('Creating index for: '.$table.'.'.$index.'('.$column.')<br/>');
						else
							$this->add_content('ERROR creating index for: '.$table.'.'.$field['name'].'<br/>');
					}
				}
				
				
				

				
				switch ($pre) {
					case 'tbl':
					case 'cfg':
						# code...
						break;
					case 'rel':
						# code...
						break;
					case 'res':
						# code...
						break;
					case 'log':
						# code...
						break;
				}
			}
		}
		
	}


	
}

?>