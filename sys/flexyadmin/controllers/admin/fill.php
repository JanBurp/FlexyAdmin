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

class Fill extends AdminController {

	function Fill() {
		parent::AdminController();
	}

	function index() {
		if ($this->_can_use_tools()) {
			$this->lang->load('help');
			$this->lang->load('form');
		
			$fields=$this->input->post('fields');
			$where=$this->input->post('where');
			$fill=$this->input->post('fill');
			$random=$this->input->post('random');
			$test=$this->input->post('test');
		
			if ($fill) {
				$htmlTest=h(lang('fill_fill'),1);
				$htmlTest.="<ul>";
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
				$htmlTest.="</ul>";
			}
			if (!$fill or $test) {
				// show form
				$this->load->model('form');
				$this->load->model('flexy_field','ff');
				$form=new form($this->config->item('API_fill'));
				$fieldsOptions=$this->ff->_dropdown_fields_form();
				$fieldsOptions=$fieldsOptions["options"];
				unset($fieldsOptions[""]);
				$fieldsOptions=combine($fieldsOptions,$fieldsOptions);
				if (empty($fields)) $fields=array();
				else $fields=combine($fields,$fields);
				// create form
				$data=array( 	"fields"	=> array("label"=>lang('fill_fields'),"value"=>$fields,"type"=>'dropdown','options'=>$fieldsOptions,'multiple'=>'multiple'),
											"where"		=> array("label"=>lang('fill_where'),"value"=>$where),
											"fill"		=> array("label"=>lang('fill_with'),"value"=>$fill),
											"random"	=> array("label"=>lang('fill_use_random'),"type"=>"checkbox","value"=>$random),
											""				=> array("type"=>"html","value"=>'alt[yes|no|..],[int(min,max)],[str([html|mix|lower|upper],len)]'),
											"test"		=> array("type"=>'checkbox','value'=>1)
											);
				$form->set_data($data,lang('fill_fill'));
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
