<?
require_once(APPPATH."core/AdminController.php");

/**
 * Special Controller Class
 *
 * This Controller shows a grid or form
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 *
 */

class Fill extends AdminController {

	function __construct() {
		parent::__construct();
	}

	function index() {
		if ($this->user->can_use_tools()) {
			$this->lang->load('help');
			$this->lang->load('form');
		
			$aantal=$this->input->post('aantal');
			$addtable=$this->input->post('addtable');
			$fields=$this->input->post('fields');
			$where=$this->input->post('where');
			$fill=$this->input->post('fill');
			$random=$this->input->post('random');
			$test=$this->input->post('test');
		
			$htmlTest='';

			// create rows in table
			if ($aantal and $addtable) {
				$forbidden_fields=$this->config->item('FIELDS_special');
				$forbidden_fields=array_keys($forbidden_fields);
				$first_field=$this->db->list_fields($addtable);
				$first_field=not_filter_by($first_field,$forbidden_fields);
				$first_field=array_shift($first_field);
				for ($i=0; $i < $aantal; $i++) { 
					$id='#';
					if (!$test) {
						$this->db->insert($addtable,array($first_field=>''));
						$id=$this->db->insert_id();
					}
					$htmlTest.="<li>+ $addtable [$id]</li>";
				}
			}

			if ($fields and $fill) {
				// decode random
				if ($random) {
					$expression=preg_split('[\[|\]]',$fill,-1,PREG_SPLIT_NO_EMPTY);
					// strace_($expression);
					foreach ($expression as $key=>$text) {
						$params='';
						$e=$text;
						// what rnd type?
						$types=array('alt','int','str');
						$type=substr($e,0,3);
						if (!in_array($type,$types))
							$type='text';
						else {
							$params=preg_split('.[(|,|)].',$e,-1,PREG_SPLIT_NO_EMPTY);
							array_shift($params);
							if ($type!='text') $text="[$text]";
						}
						$expression[$key]=array('text'=>$text,'type'=>$type,'params'=>$params);
					}
					// strace_($expression);
				}
				// fille fields
				foreach($fields as $field) {
					$table=get_prefix($field,'.');
					$this->db->select('id');
					if (!empty($where)) $this->db->where($where);
					$items=$this->db->get_result($table);
					foreach ($items as $id => $row) {
						$result=$fill;
						if ($random and !empty($expression)) {
							$result='';
							foreach ($expression as $key => $exp) {
								$params=$exp['params'];
								switch($exp['type']) {
									case 'alt':
										$result.=random_element($exp['params']);
										break;
									case 'int':
										if (isset($params[1])) $result.=rand($params[0],$params[1]);
										else $result.=rand($params[0],$params[1]);
										break;
									case 'str':
										$html=$this->load->view('admin/html_lorum',array(),true);
										if ($params[0]=='html') {
											$paragraphs=explode('<h2>',$html);
											array_shift($paragraphs);
											foreach ($paragraphs as $key=>$par) {$paragraphs[$key]=trim('<h2>'.$par);}
											$rndString='';
											for ($p=0;$p<$params[1];$p++) $rndString.=random_element($paragraphs);
										}
										else {
											if ($params[1]<=8) {
												$rndString=random_string('alfa',$params[1]);
											}
											else {
												$lines=explode('.',str_replace(',','',strip_tags($html)));
												foreach ($lines as $key => $line) {
													if (empty($line)) unset($lines[$key]);
												}
												$rndString=trim(substr(random_element($lines),0,$params[1]));
											}
											switch ($params[0]) {
												case 'lower': $rndString=strtolower($rndString);break;
												case 'upper': $rndString=strtoupper($rndString);break;
											}
										}
										$result.=$rndString;
										break;
									default:
									case 'text':
										$result.=$exp['text'];
										break;
								}
							}
						}
						if (!$test) {
							$this->db->where('id',$id);
							$this->db->set($field,$result);
							if (!empty($where)) $this->db->where($where);
							$this->db->update($table);
							// trace_($this->db->last_query());
						}
						$htmlTest.="<li>$field [$id] = '$result'</li>";
					}
				}		
			}
			if (!empty($htmlTest)) {
				$htmlTest=h(lang('fill_fill'),1)."<ul>".$htmlTest."</ul>";
			}
			if (!$fill or $test) {
				// show form
				$this->load->library('form');
				$this->load->model('flexy_field','ff');
				$form=new form($this->config->item('API_fill'));
				$tablesOptions=$this->ff->_dropdown_tables_form();
				$tablesOptions=$tablesOptions['options'];
				$fieldsOptions=$this->ff->_dropdown_fields_form();
				$fieldsOptions=$fieldsOptions["options"];
				// unset($fieldsOptions[""]);
				$fieldsOptions=array_combine($fieldsOptions,$fieldsOptions);
				if (empty($fields)) $fields=array();
				else $fields=array_combine($fields,$fields);
				// create form
				$data=array( 	"aantal"	=> array("label"=>lang('fill_aantal'),"value"=>$aantal),
											"addtable"=> array("label"=>lang('fill_tables'),"value"=>$addtable,"type"=>'dropdown','options'=>$tablesOptions),
											"fields"	=> array("label"=>lang('fill_fields'),"value"=>$fields,"type"=>'dropdown','options'=>$fieldsOptions,'multiple'=>'multiple'),
											"where"		=> array("label"=>lang('fill_where'),"value"=>$where),
											"fill"		=> array("label"=>lang('fill_with'),"value"=>$fill),
											"random"	=> array("label"=>lang('fill_use_random'),"type"=>"checkbox","value"=>$random),
											""				=> array("type"=>"html","value"=>'alt[yes|no|..],[int(min,max)],[str([html|mix|lower|upper],len)]'),
											"test"		=> array("type"=>'checkbox','value'=>1)
											);
				$form->set_data($data,lang('fill_fill'));
				$this->_add_content($form->render());			
			}
			$this->_add_content(div('after_form').$htmlTest._div());	
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
