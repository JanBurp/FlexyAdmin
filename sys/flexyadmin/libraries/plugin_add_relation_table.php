<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Maakt een relatie tabel
 *  
 * Deze plugin maakt een relatie tabel tussen de twee meegegeven tabellen.
 * Stel je wilt een relatie tabel tussen _tbl_menu_ en _tbl_links_ dan type je de volgende url in:
 * 
 *      .../admin/plugin/add_relation_table/tbl_menu/tbl_links
 * 
 * @package default
 * @author Jan den Besten
 */
 
class Plugin_add_relation_table extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	
  /**
   * @ignore
   */
   function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $table1=$args[0];
				if (isset($args[1])) $table2=$args[1];
				if (isset($table1) and isset($table2)) $goodArgs=true;
				if ($goodArgs) {
					$relTable='rel_'.remove_prefix($table1).'__'.remove_prefix($table2);
					if ($table1==$table2) $table2=$table2.'_';
					$this->CI->dbforge->add_field('id');
					$fields=array(	'id_'.remove_prefix($table1)	=>	array('type'=>'INT','unsigned'=>TRUE),
													'id_'.remove_prefix($table2)	=>	array('type'=>'INT','unsigned'=>TRUE));
					$this->CI->dbforge->add_field($fields);
					$this->CI->dbforge->create_table($relTable,TRUE);
					$this->add_content(h("Created '$relTable' from '$table1' and '$table2'.",2));
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Add relation table, for which table(s)?</br></br>Give: /tbl_xxx/tbl_xxx</p>');
			}
      return $this->content;
		}
	}
	
}

?>