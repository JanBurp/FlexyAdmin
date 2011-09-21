<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Plugin_add_relation_table extends Plugin_ {

	function init($init=array()) {
		parent::init($init);
	}
	
	function _admin_api($args=false) {
		if ($this->user->is_super_admin()) {
			$this->_add_content(h($this->plugin,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $table1=$args[0];
				if (isset($args[1])) $table2=$args[1];
				if (isset($table1) and isset($table2)) $goodArgs=true;
				if ($goodArgs) {
					$relTable='rel_'.remove_prefix($table1).'__'.remove_prefix($table2);
					if ($table1==$table2) $table2=$table2.'_';
					$this->dbforge->add_field('id');
					$fields=array(	'id_'.remove_prefix($table1)	=>	array('type'=>'INT','unsigned'=>TRUE),
													'id_'.remove_prefix($table2)	=>	array('type'=>'INT','unsigned'=>TRUE));
					$this->dbforge->add_field($fields);
					$this->dbforge->create_table($relTable,TRUE);
					$this->_add_content(h("Created '$relTable' from '$table1' and '$table2'.",2));
				}
			}
			if (!$goodArgs) {
				$this->_add_content('<p>Add relation table, for which table(s)?</br></br>Give: /tbl_xxx/tbl_xxx</p>');
			}
		}
	}
	
}

?>