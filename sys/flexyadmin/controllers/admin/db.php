<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * DB Controller Class
 *
 * This Controller handles database import/export
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Db extends AdminController {

	function Db() {
		parent::AdminController();
	}

	function index() {
		$this->_show_all();
	}

	function _export() {
		$this->load->model('form');
		$this->lang->load('help');
		$this->lang->load("update_delete");
		$this->lang->load("form");

		$form=new form($this->config->item('API_db_export'));
		$tablesWithRights=$this->_get_table_rights();
		$options=combine($tablesWithRights,$tablesWithRights);
		$valuesData=$options;
		unset($valuesData['cfg_sessions']);
		unset($valuesData['log_login']);
		unset($valuesData['log_stats']);
		$valuesStructure=array_diff($options,$valuesData);

		$name="export_".$this->_filename()."_".date("Y-m-d");
		$data=array(
								"data"			=> array("label"=>"Tables (with data)","type"=>"dropdown","multiple"=>"multiple","options"=>$options,"value"=>$valuesData),
								"structure"	=> array("label"=>"Tables (structure)","type"=>"dropdown","multiple"=>"multiple","options"=>$options,"value"=>$valuesStructure),
								// "type"			=> array("type"=>"dropdown","options"=>array("txt"=>"txt","gz"=>"gz")),
								"filename"	=> array("label"=>"Filename","value"=>$name)
								);
		$form->set_data($data,"Choose tables to export");
		$this->_add_content($form->render());
	}

	function export() {
		if ($this->_is_super_admin()) {
			$dataTables=$this->input->post('data');
			$structureTables=$this->input->post('structure');
			// $type=$this->input->post('type');
			$type="txt";
			$name=$this->input->post('filename');
			if (!$dataTables) {
				$this->_export();
			}
			else {
				$this->load->dbutil();
				$this->load->helper('download');

				$prefs = array('tables'=> $dataTables,'format'=>'txt');
				$backup=$this->dbutil->backup($prefs);
				if ($structureTables) {
					$prefs = array('tables'=> $structureTables,'format'=>'txt','add_insert'  => FALSE);
					$backup=$backup.$this->dbutil->backup($prefs);				
				}
			
				if ($type=="gzip") {
					$backup=gzencode($backup);
				}
				force_download($name.'.'.$type, $backup);
			}
		}
		$this->_show_all();
	}

	function _clean_sql($sql) {
		// Clean up comments
		$sql=preg_replace("/#(.*?)\n/","",$sql);
		// replace DROP TABLE (and CREATE TABLE) with TRUNCATE TABLE
		$sql=preg_replace("/DROP TABLE(.*) (.*?);/","# Empty $2\nTRUNCATE TABLE $2;",$sql);
		$sql=preg_replace("/CREATE TABLE (.*?) (.|\n)*?;\n/","# Inserts for $1",$sql);
		return $sql;
	}
	
	function _is_safe_sql($sql) {
		$safe=TRUE;
		// Check on DROP/ALTER/RENAME statements ;
		if (preg_match("/(DROP|ALTER|RENAME|REPLACE|LOAD\sDATA|SET)/i",$sql)>0)	$safe=FALSE;
		// Check on TRUNCATE / CREATE table names, if it has rights for tables
		if ($safe) {
			if (preg_match_all("/(TRUNCATE\sTABLE|CREATE\sTABLE|INSERT\sINTO|DELETE\sFROM|UPDATE)\s(.*?)(;|\s)/i",$sql,$matches)>0) {
				$tables=$matches[2];
				$tables=array_unique($tables);
				$tables=not_filter_by($tables,'rel');
				// check if rights for found tables
				foreach ($tables as $table) {
					if ($this->has_rights($table) < RIGHTS_ALL) $safe=FALSE;
				}
			}
		}
		return $safe;
	}

	function _filename() {
		$name=$this->db->get_field("tbl_site","url_url");
		$name=str_replace(array('http://','www.'),'',$name);
		$name=explode(".",$name);
		$name=$name[0];
		return $name;
	}

	function backup() {
		if ($this->_can_backup()) {
			$this->load->dbutil();
			$this->load->helper('download');
		
			$tablesWithRights=$this->_get_table_rights();
			// select only data (not config)
			$tablesWithRights=combine($tablesWithRights,$tablesWithRights);
			$tablesWithRights=not_filter_by($tablesWithRights,"cfg");
			$tablesWithRights=not_filter_by($tablesWithRights,"log");
			unset($tablesWithRights["rel_users__rights"]);
		
			// create backup
			$prefs = array('tables'=> $tablesWithRights,'format'=>'txt');
			$sql = $this->dbutil->backup($prefs);
			// clean backup
			$sql=$this->_clean_sql($sql);
			$sql="# FlexyAdmin backup\n# User: '".$this->user."'  \n# Date: ".date("d F Y")."\n\n".$sql;
		
			$backup=$sql;
			$filename='backup_'.$this->_filename().'_'.date("Y-m-d").'.txt';
			force_download($filename, $backup);
			$this->_add_content(h(1,"Backup"));
		}
		$this->_show_all();
	}

	function restore() {
		if ($this->_can_backup()) {
			if (!isset($_FILES["userfile"])) {
				$this->load->model('form');
				$this->lang->load('help');
				$this->lang->load('form');
				$form=new form($this->config->item('API_db_restore'));
				$data=array( "userfile"	=> array("type"=>"file","label"=>lang('file')) );
										 // "sure"=> array("type"=>"hidden","value"=>$this->cfg->get('CFG_configurations','key')) );
				$form->set_data($data,lang('db_restore'));
				$this->_add_content($form->render());
			}
			else {
				// $sure=$this->input->post('sure');
				// if ($sure and ($sure==$this->cfg->get('CFG_configurations','key'))) {
					$sql=$this->_upload_sql();
					if ($sql) {
						$this->_sql($sql,"Restore","Restoring ...");			
					}
				// }
			}
		}
		$this->_show_all(lang('db_restore'));
	}
	

	function _upload_sql() {
		// upload (to list path, 'coz this exists!)
		$sql=FALSE;
		$config['upload_path'] = 'site/assets/lists';
		$config['allowed_types'] = 'txt|sql';
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload()) {
			$error = array('error' => $this->upload->display_errors());
			print $error["error"];
			trace_($this->upload->data());
		}	
		else	{
			$data = array('upload_data' => $this->upload->data());
			$data=$data["upload_data"];
			// import (ungzip if needed)
			if ($data["file_ext"]==".gz" or $data["file_ext"]==".gzip") {
				$sql=gzfile($data["full_path"]);
			}
			else {
				$sql=file($data["full_path"]);
			}
			// delete file
			unlink($data["full_path"]);
			
			if (is_array($sql)) {
				$s="";
				foreach ($sql as $line) {
					$s.=$line;
				}
				$sql=$s;
			}
		}
		return $sql;
	}

	function _import() {
		$this->load->model('form');
		$this->lang->load('help');
		$this->lang->load('form');
		$form=new form($this->config->item('API_db_import'));
		$data=array( 	"userfile"	=> array("type"=>"file","label"=>"File (txt,sql)"),
		 							"sql"				=> array("type"=>"textarea","label"=>"Or (update) SQL"));
		$form->set_data($data,"Choose File to upload and import");
		$this->_add_content($form->render());
	}

	function import() {
		if ($this->_is_super_admin()) {
			$sql=$this->input->post('sql');
			if (!isset($_FILES["userfile"]) and !$sql) {
				$this->_import();
			}
			else {
				// trace_($sql);
				if (!$sql) $sql=$this->_upload_sql();
				if ($sql) {
					// do the actual import..
					$this->load->model('form');
					$this->lang->load('help');
					$this->lang->load('form');
					$form=new form($this->config->item('API_db_sql'));
					$data=array( 	"sql" => array("type"=>"textarea","value"=>$sql),
					 							"sure"=> array("type"=>"hidden","value"=>$this->cfg->get('CFG_configurations','key')) // insert license here!!
											);
					$form->set_data($data,"Are you sure to run this Query?");
					$this->_add_content($form->render());
				}
			}
		}
		$this->_show_all(lang('db_import'));					
	}
	
	function _sql($sql,$title,$action) {
		$this->_add_content(h($title));
		$safe=$this->_is_safe_sql($sql);
		if ($safe)
			$this->_add_content(p()."Checking safety ... ok"._p());
		else {
			$rights=current($this->rights);
			if ($rights["str_name"]=="super_admin" and $rights["rights"]=="*" and $rights["b_all_users"]) {
				$safe=TRUE;
				$this->_add_content(p()."Checking safety ... Risky SQL, but Super Admin Rights."._p());
			}
			else
				$this->_add_content(p()."Checking safety ... Unsafe SQL. Import aborted."._p());
		}
		if ($safe) {
			$lines=explode("\n",$sql);
			$comments="";
			foreach ($lines as $k=>$l) {
				if (substr($l,0,1)=="#")	{
					if (strlen($l)>2)	$comments.=$l.br();
					unset($lines[$k]);
				}
			}
			$sql=implode("\n",$lines);
			$lines=preg_split('/;\n+/',$sql); // split at ; with EOL
			
			$this->_add_content(p().$action.br(2));//.$comments);
			
			foreach ($lines as $key => $line) {
				$line=trim($line);
				if (!empty($line)) {
					$query=$this->db->query($line);
				}
			}
			$this->_add_content(_p());
		}
	}
	
	function sql() {
		if ($this->_is_super_admin()) {
			$sql=$this->input->post('sql');
			// $sure=$this->input->post('sure');
			$this->lang->load('help');
			// if ($sql and (IS_LOCALHOST or ($sure and $sure==$this->cfg->get('CFG_configurations','key')) ) ) {
			if ($sql) {
				$this->_sql($sql,"Import","Importing ...");
			}
		}
		$this->_show_all(lang('db_import'));				
	}
	
	
}

?>
