<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * DB Controller Class
 * This Controller handles database import/export
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Db extends AdminController {

  var $types = array(
    'data'      => array('name'=>'All Tables & Data, except log & session Tables','data'=>'*,-cfg_sessions,-log_activity,-log_stats,-log_login_attempts','structure'=>''),
    'all'       => array('name'=>'All Tables & Data, except log & session Data','data'=>'*,-cfg_sessions,-log_activity,-log_stats,-log_login_attempts','structure'=>'cfg_sessions,log_activity,log_stats,log_login_attempts'),
    'complete'  => array('name'=>'All Tables & Data','data'=>'*','structure'=>''),
    'tbl'       => array('name'=>'Normal Tables & Data','data'=>'tbl_*,res_*','structure'=>''),
    'cfg_clean' => array('name'=>'Config Tables & Data, except session table','data'=>'cfg_*,-cfg_sessions','structure'=>''),
    'cfg'       => array('name'=>'Config Tables & Data','data'=>'cfg_*','structure'=>''),
    'select'    => array('name'=>'Select Tables to export','data'=>'','structure'=>'')
  );

	function __construct() {
		parent::__construct();
    $this->load->model('version');
    $this->load->dbutil();
    $extra_export_types=$this->config->item('extra_export_types','plugin_db_export');
    if ($extra_export_types) {
      $this->types['-']=array();
      $this->types=array_merge($this->types,$extra_export_types);
    }
	}

	function index() {
		$this->view_admin();
	}

	function _export() {
		$this->load->library('form');
		$this->lang->load('help');
		$this->lang->load("update_delete");
		$this->lang->load("form");

		$form=new form($this->config->item('API_db_export'));
		$tablesWithRights=$this->flexy_auth->get_table_rights();
		$options=array_combine($tablesWithRights,$tablesWithRights);
		$valuesData=$options;
		unset($valuesData['cfg_sessions']);
		unset($valuesData['log_login']);
		unset($valuesData['log_activity']);
		unset($valuesData['log_login_attempts']);
		$valuesStructure=array_diff($options,$valuesData);
    
    $types=$this->types;
    $types=array_flatten($types);

    $name="export_".$this->_filename()."_".date("Y-m-d").'.data';
    $data=array(
      "type"        => array('label'=>'Selection','type'=>'dropdown','options'=>$types,'value'=>'data'),
      "data"        => array("label"=>"Tables (with data)","type"=>"dropdown","multiple"=>"multiple","options"=>$options,"value"=>'','class'=>'hidden'),
      "structure"   => array("label"=>"Tables (structure)","type"=>"dropdown","multiple"=>"multiple","options"=>$options,"value"=>'','class'=>'hidden'),
      "filename"    => array('label'=>"Filename",'value'=>$name)
    );
		$form->set_data($data,"Export");
    $this->view_admin('plugins/plugin',array('title'=>'DB Export','content'=>$form->render('db_export')));
	}

	function export() {
		if ($this->flexy_auth->is_super_admin()) {
      $type=$this->input->post('type');
			$ext="txt";
			$name=$this->input->post('filename');
			if (!$type) {
				$this->_export();
			}
			else {
        if ($type=='select') {
          $dataTables=$this->input->post('data');
          $structureTables=$this->input->post('structure');
        }
        else {
          $dataTables=$this->_set_tables( $this->types[$type]['data'] );
          $structureTables=$this->_set_tables( $this->types[$type]['structure'] );
        }
        
				$this->load->dbutil();
				$this->load->helper('download');
        
        $backup="#\n";
        $backup.='# FlexyAdmin DB-Export '.date("Y-m-d"). "\n";
        $backup.="#\n";
        if (is_array($dataTables))      $backup.='# DATA TABLES: '.implode(', ',$dataTables)."\n";
        if (is_array($structureTables)) $backup.='# STRUCTURE TABLES: '.implode(', ',$structureTables)."\n";
        $backup.="#\n\n\n";
        
        if ($dataTables) {
  				$prefs = array('tables'=> $dataTables,'format'=>'txt');
  				$backup.=$this->dbutil->backup($prefs);
        }
        if ($structureTables) {
          $prefs = array('tables'=> $structureTables,'format'=>'txt','add_insert'  => FALSE);
          $backup.=$this->dbutil->backup($prefs);
        }

				if ($ext=="gzip") {
					$backup=gzencode($backup);
				}
				force_download($name.'.'.$ext, $backup);
			}
		}
		$this->view_admin();
	}

  private function _set_tables($expressions) {
    if (empty($expressions)) return '';
    $tables=array();
    $expressions=explode(',',$expressions);
    foreach ($expressions as $expression) {
      switch ($expression) {
        case '*':
          $add=$this->data->list_tables();
          $add=array_combine($add,$add);
          $tables=array_merge($tables,$add);
          break;
        case 'tbl_*':
          $add=$this->data->list_tables();
          $add=filter_by($add,'tbl_');
          $add=array_combine($add,$add);
          $tables=array_merge($tables,$add);
          break;
        case 'cfg_*':
          $add=$this->data->list_tables();
          $add=filter_by($add,'cfg_');
          $add=array_combine($add,$add);
          $tables=array_merge($tables,$add);
          break;
        default:
          if (substr($expression,0,1)=='-') {
            $table=substr($expression,1);
            unset($tables[$table]);
          }
          elseif (has_string('*',$expression)) {
            $like=get_prefix($expression,'*');
            $add=$this->data->list_tables();
            $add=filter_by($add,$like);
            $add=array_combine($add,$add);
            $tables=array_merge($tables,$add);
          }
          else {
            $tables[$expression]=$expression;
          }
          break;
      }
    }
    return array_keys($tables);
  }

	function _clean_sql($sql) {
		// Clean up comments
		$sql=preg_replace("/#(.*?)\n/","",$sql);
		// replace DROP TABLE (and CREATE TABLE) with TRUNCATE TABLE
		$sql=preg_replace("/DROP TABLE(.*) (.*?);/","# Empty $2\nTRUNCATE TABLE $2;",$sql);
		$sql=preg_replace("/CREATE TABLE (.*?) (.|\n)*?;\n/","# Inserts for $1",$sql);
		return $sql;
	}
	
	function _filename() {
		$name=$this->data->table('tbl_site')->get_field("url_url");
		$name=str_replace(array('http://','www.'),'',$name);
		$name=explode(".",$name);
		$name=$name[0];
		return $name;
	}

	function backup() {
		if ($this->flexy_auth->can_backup()) {
			$this->load->dbutil();
			$this->load->helper('download');
		
			$tablesWithRights=$this->flexy_auth->get_table_rights();
			// select only data (not config)
			$tablesWithRights=array_combine($tablesWithRights,$tablesWithRights);
			$tablesWithRights=not_filter_by($tablesWithRights,"cfg");
			$tablesWithRights=not_filter_by($tablesWithRights,"log");
			unset($tablesWithRights["rel_users__rights"]);
		
			// create backup
			$prefs = array('tables'=> $tablesWithRights,'format'=>'txt');
			$sql = $this->dbutil->backup($prefs);
			// clean backup
			$sql=$this->_clean_sql($sql);
			$sql="# FlexyAdmin backup\n# User: '".$this->user_name."'  \n# Date: ".date("d F Y")."\n\n".$sql;
		
			$backup=$sql;
			$filename='backup_'.$this->_filename().'_'.date("Y-m-d").'.txt';
			force_download($filename, $backup);
			$this->_add_content(h(1,"Backup"));
		}
		$this->view_admin();
	}

	function restore() {
		if ($this->flexy_auth->can_backup()) {
			if (!isset($_FILES["userfile"])) {
				$this->load->library('form');
				$this->lang->load('help');
				$this->lang->load('form');
				$form=new form($this->config->item('API_db_restore'));
				$data=array( "userfile"	=> array("type"=>"file","label"=>lang('file')) );
				$form->set_data($data,lang('db_restore'));
				$this->_add_content($form->render());
			}
			else {
				$sql=$this->_upload_sql();
				if ($sql) {
					$this->_sql($sql,"Restore","Restoring ...");			
				}
			}
		}
		$this->view_admin(lang('db_restore'));
	}
	

	function _upload_sql() {
		// upload (to list path, 'coz this exists!)
		$sql=FALSE;
		$config['upload_path'] = SITEPATH.'cache';
		$config['allowed_types'] = 'txt|sql';
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload()) {
			$error = array('error' => $this->upload->display_errors());
			print $error["error"];
      // trace_($config);
      // trace_($this->upload->data());
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
		$this->load->library('form');
		$this->lang->load('help');
		$this->lang->load('form');
		$form=new form($this->config->item('API_db_import'));
		$data=array( 	"userfile"	=> array("type"=>"file","label"=>"File (txt,sql)"),
		 							"sql"				=> array("type"=>"textarea","label"=>"SQL"));
		if ($this->flexy_auth->has_rights('cfg_configurations')) {
			$data['update']=array('label'=>'Update DB from r');
		}
		$form->set_data($data,"Choose File to upload and import");
		$this->_add_content($form->render());
	}

	function import() {
		if ($this->flexy_auth->is_super_admin()) {
      // What form is filled?
			$sql=$this->input->post('sql');
      
			if (!isset($_FILES["userfile"]) and empty($sql)) {
        // Maybe some update sqls?
        $update=$this->input->post('update');
        if (!$update) {
          // Show start form
          $this->_import();
        }
        else {
          // Do the actual update
          $data=$_POST;
          unset($data['submit']);
          unset($data['update']);
          unset($data['__form_id']);
          $sql='';
          foreach ($data as $rev => $nop) {
						// load SQL
            $file='update_r'.$rev.'.sql';
						$usql=file_get_contents('db/'.$file);
            $sql.="\n".$usql;
          }
          $this->_sql($sql,'Updating...','updated');
          $this->_add_content(form_textarea('output',$sql));
        }
			}
			else {
				$update=(int) $this->input->post('update');
				if ($update) {
					$sql='';
					$latestVersion=(int) $this->version->get_version();
					$this->_add_content(h('Update from '.$update.' to r'.$latestVersion));
					// load all update sql files
					$updates=read_map('db','sql',FALSE,FALSE);
					$updates=array_keys($updates);
					$updates=filter_by($updates,'update_');
					foreach ($updates as $key=>$file) {
						$fileRev=(int) substr($file,8,4);
						if ($fileRev<=$update)
							unset($updates[$key]);
						else {
							// load SQL
							$usql=file_get_contents('db/'.$file);
              // $usql.="# $file\n".$usql."\n\n";
              $lines=explode("\n",$usql);
              $info=current($lines);
              $info=trim(trim($info,'#'));
              $updates[$key]=array(
                'rev'   =>$fileRev,
                'file'  =>$file,
                'sql'   =>$usql,
                'info'  =>$info,
                'value' =>true,
                'class' =>''
              );
              $question=next($lines);
              if (has_string('UPDATE_IF:',$question)) {
                $question=substr($question,strpos($question,':')+1);
                $updates[$key]['value']=false;
                $updates[$key]['info']=$question;
                $updates[$key]['class']="warning";
              }
						}
					}
          // show update form selections
					$this->load->library('form');
					$form=new form($this->config->item('API_db_import'));
          $data['update']=array('type'=>'hidden','value'=>'update');
          foreach ($updates as $key => $file) {
            $data[$file['rev']]=array('type'=>'checkbox','label'=>'update '.$file['rev'],'html'=>'<span class="help" title="'.safe_quotes($file['sql']).'">'.$file['info'].'</span>','value'=>$file['value'],'class'=>$file['class']);
          }
					$form->set_data($data,"Select the updates you wan't");
					$this->_add_content($form->render());
        }
				else {
					if (!$sql) $sql=$this->_upload_sql();
				}
				if ($sql) {
					// do the actual import..
					$this->load->library('form');
					$this->lang->load('help');
					$this->lang->load('form');
					$form=new form($this->config->item('API_db_sql'));
					$data=array( "sql" => array("type"=>"textarea","value"=>$sql)  );
					$form->set_data($data,"Are you sure to run this Query?");
					$this->_add_content($form->render());
				}
			}
		}
		$this->view_admin(lang('db_import'));					
	}
	
	function _sql($sql,$title,$action) {
		$this->_add_content(h($title));
		$safe=$this->dbutil->is_safe_sql($sql);
		if ($safe)
			$this->_add_content(p()."Checking safety ... ok"._p());
		else {
			if ($this->flexy_auth->is_super_admin()) {
				$safe=TRUE;
				$this->_add_content(p()."Checking safety ... Risky SQL, but Super Admin Rights."._p());
			}
			else
				$this->_add_content(p()."Checking safety ... Unsafe SQL. Import aborted."._p());
		}
		if ($safe) {
      $result=$this->dbutil->import($sql);
      if (isset($result['errors'])) {
        foreach ($result['errors'] as $error) {
          $this->_add_content(p('error').$error._p());
        }
      }
			$this->_add_content(p().$action.br(2)._p());//.$comments);
		}
	}
	
	function sql() {
		if ($this->flexy_auth->is_super_admin()) {
			$sql=$this->input->post('sql');
			$this->lang->load('help');
			if ($sql) {
				$this->_sql($sql,"Import","Importing ...");
			}
		}
		$this->view_admin(lang('db_import'));				
	}
	
}

?>
