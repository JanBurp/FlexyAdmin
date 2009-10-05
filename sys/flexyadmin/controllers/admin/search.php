<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * Special Controller Class
 *
 * This Controller shows a grid or form
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Search extends AdminController {

	function Search() {
		parent::AdminController();
	}

	function index() {
		if ($this->_can_use_tools()) {
			$this->lang->load('help');
			$this->lang->load('form');
		
			$search=$this->input->post('search');
			$replace=$this->input->post('replace');
			$tables=$this->input->post('tables');
			$types=$this->input->post('types');
			$regex=$this->input->post('regex');
			$test=$this->input->post('test');
		
			if ($search) {
				$htmlTest=h(lang('sr_result'),1);
				foreach($tables as $table) {
					$fields=$this->db->list_fields($table);
					$not_fields=$fields;
					foreach ($types as $type) {
						$not_fields=not_filter_by($not_fields,$type);
					}
					$fields=array_diff($fields,$not_fields);
					// $fields=array_values($fields);
					// trace_(array('table'=>$table,'types'=>$types,'fields'=>$fields));
					$this->db->select(pk());
					$this->db->select($fields);
					$result=$this->db->get_result($table);
					$htmlTest.="<ul>";
					foreach($result as $id=>$row) {
						unset($row[pk()]);
						foreach ($row as $key=>$txt) {
							if ($regex) {
								$oldErrorHandler=set_error_handler(array($this,"myErrorHandler"));
								$new=preg_replace("/$search/",$replace,$txt);
								set_error_handler($oldErrorHandler);
							}
							else {
								$new=str_replace($search,$replace,$txt);
							}
							if ($new!=$txt) {
								$this->db->as_abstracts();
								$this->db->where(pk(),$id);
								$abstract=$this->db->get_row($table);
								$abstract=$abstract['abstract'];
								$htmlTest.="<li>".$this->uiNames->get($table)." '$abstract'".' : <textarea>'.$txt.'</textarea> =&gt; <textarea>'.$new.'</textarea></li>';
							}
							if (!$test) $this->db->set($key,$new);
						}
						if (!$test) {
							$this->db->where(pk(),$id);
							$res=$this->db->update($table);
						}
						$this->db->as_abstracts(FALSE);
					}
					$htmlTest.="</ul>";
				}		
			}
			if (!$search or $test) {
				// show form
				$this->load->model('form');
				$this->load->model('flexy_field','ff');
				$form=new form($this->config->item('API_search'));
				// table options/value
				$tablesOptions=$this->ff->_dropdown_tables_form();
				$tablesOptions=$tablesOptions["options"];
				unset($tablesOptions[""]);
				if (!$tables) {
					$tables[]=$this->cfg->get('CFG_configurations','str_menu_table');
				}
				$tables=combine($tables,$tables);
				// types options/value
				$typesOptions=$this->config->item('FIELDS_prefix');
				$typesOptions=array_keys($typesOptions);
				sort($typesOptions);
				$typesOptions=combine($typesOptions,$typesOptions);
				unset($typesOptions['id']);
				unset($typesOptions['self']);
				unset($typesOptions['rel']);
				unset($typesOptions['b']);
				if (!$types) {
					$types=array('txt','stx');
				}
				$types=combine($types,$types);
				// create form
				$data=array( 	"search"	=> array("label"=>lang('sr_search'),"value"=>$search),
											"replace"	=> array("label"=>lang('sr_replace'),"value"=>$replace),
											"regex"		=> array("label"=>lang('sr_regex'),"type"=>"checkbox","value"=>$regex),
											"types"		=> array("label"=>lang('sr_field_types'),"type"=>"dropdown","multiple"=>"mutliple","options"=>$typesOptions,"value"=>$types),
				 							"tables"	=> array("label"=>lang('sr_tables'),"type"=>"dropdown","multiple"=>"mutliple","options"=>$tablesOptions,"value"=>$tables),
											"test"		=> array("type"=>'checkbox','value'=>1)
											);
				$form->set_data($data,lang('sr_search_replace'));
				$this->_add_content($form->render());			
			}
			if (!empty($htmlTest)) {
				if ($test) $class="after_form"; else $class="";
				$this->_add_content(div($class).$htmlTest._div());	
			}
		}
		$this->_show_all();
	}

	function myErrorHandler($errno, $errstr, $errfile, $errline) 	{
		static $WarnedAllready=FALSE;
    if ($errno==E_WARNING and strpos($errstr,'REG_BADRPT')) {
			if (!$WarnedAllready) {
				$this->_add_content(p('error').lang('bad_regex')._p());
				$WarnedAllready=TRUE;
			}
			return true;
    }
    return false;
	}

}

?>
