<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;


/** \ingroup plugins
 * Backup / Restore database
 * 
 *
 * @author Jan den Besten
 */
class Plugin_db extends Plugin {

	public function __construct() {
		parent::__construct();
		$this->CI->load->dbutil();
		$this->CI->load->driver('cache', array('adapter' => 'file'));
		$this->CI->load->helper('download');
		$this->CI->load->library('zip');
	}

  /**
   */
	public function _admin_api($args=NULL) {
		$action = array_shift($args);
		if (method_exists($this,$action)) {
			return $this->$action($args);
		}
		return '';
	}

  private function _filename() {
		$name=$this->CI->data->table('tbl_site')->get_field("url_url");
		$name=str_replace(array('http://','https://','www.'),'',$name);
		$name=explode(".",$name);
		$name=$name[0];
		return date("Y-m-d").'_'.$name;
  }


	private function backup($args=NULL) {
		if (!$this->CI->flexy_auth->can_backup()) return 'No rights';

		$tablesWithRights=$this->CI->flexy_auth->get_table_rights(RIGHTS_DELETE);

		// select all tbl_, res_ and most of the cfg_ tables
		$tablesWithRights=array_combine($tablesWithRights,$tablesWithRights);
		$tablesWithRights=not_filter_by($tablesWithRights,"log");
		unset($tablesWithRights["cfg_sessions"]);

		// create backup
		$prefs = array('tables'=> $tablesWithRights,'format'=>'sql');
		$sql = $this->CI->dbutil->backup($prefs);
		$sql = $this->CI->dbutil->clean_sql($sql);
		$sql = "# FlexyAdmin backup\n# User: '".$this->CI->flexy_auth->get_user(null,'str_username')."'  \n# Date: ".date("d F Y")."\n\n".$sql;
		$filename = $this->_filename().'_backup'.'.sql';

		// Ecnrypt
		$key = Key::loadFromAsciiSafeString( $this->CI->config->item('encryption_key') );
		$sql = Crypto::encrypt($sql, $key);
		$filename .= '.txt';

		$this->CI->zip->add_data($filename, $sql);
		$this->CI->zip->download($filename);
	}


  public function export($args=NULL) {
		if (!$this->CI->flexy_auth->can_use_tools() or !$this->CI->flexy_auth->can_backup()) return 'No rights';

		$sql = '';
		$backup_prefs = array('format' => 'sql');
		$type = array_shift($args);
		$file = array_shift($args);
		$hash = array_shift($args);
		switch ($type) {
		  case 'complete':
			$sql = $this->CI->dbutil->backup($backup_prefs);
			break;
		  case 'all':
			$tables = $this->CI->data->list_tables();
			$tablesWithData = not_filter_by($tables,array('log','cfg_sessions'));
			$backup_prefs = array('tables'=> $tablesWithData, 'format'=>'sql');
			$sql = $this->CI->dbutil->backup($backup_prefs);
			$tablesWithStructure  = array_diff($tables,$tablesWithData);
			$backup_prefs = array( 'tables'=> $tablesWithStructure, 'format'=>'sql' ,'add_insert'  => FALSE);
			$sql .= $this->CI->dbutil->backup($backup_prefs);
			break;
		  case 'data':
			$tables = $this->CI->data->list_tables();
			$tables = not_filter_by($tables,array('log','cfg'));
			$backup_prefs = array('tables'=> $tables, 'format'=>'sql');
			$sql = $this->CI->dbutil->backup($backup_prefs);
			break;
		  case 'select':
			$tables = $args;
			$backup_prefs = array('tables'=> $tables, 'format'=>'sql');
			$sql = $this->CI->dbutil->backup($backup_prefs);
			break;
		}
		$sql = "# FlexyAdmin backup\n# User: '".$this->CI->flexy_auth->get_user(null,'str_username')."'  \n# Date: ".date("d F Y")."\n\n" . $sql;
		$filename = $this->_filename().'_'.$type.'.sql';

		if ($hash==='true') {
		  $key = Key::loadFromAsciiSafeString( $this->CI->config->item('encryption_key') );
		  $sql = Crypto::encrypt($sql, $key);
		  $filename .= '.txt';
		}

		switch ($file) {
		  case 'zip':
			$this->CI->zip->add_data($filename, $sql);
			$this->CI->zip->download($filename);
			break;
		  case 'sql':
			force_download($filename, $sql);
			break;
		}
  }







  
}

?>