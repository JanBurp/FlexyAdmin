<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Maakt een relatie tabel
 *  
 * Deze plugin maakt een relatie tabel tussen de twee meegegeven tabellen.
 * /add_relation_table/tbl_this/tbl_foreign
 * 
 * @author Jan den Besten
 */
class Plugin_add_relation_table extends Plugin {

	public function __construct() {
		parent::__construct();
	}
	
  /**
   */
  public function _admin_api($args=false,$help='') {
		if ($this->CI->flexy_auth->is_super_admin()) {
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
      return $this->content;
		}
	}
	
}

?>