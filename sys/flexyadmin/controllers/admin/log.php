<?
require_once(APPPATH."controllers/admin/MY_Controller.php");


class Log extends AdminController {


	function __construct() {
		parent::__construct();
	}

	function index() {
		$this->show();
	}

	function show() {
		if (IS_LOCALHOST) {
			// list logfiles
			$files=read_map('sys/codeigniter/logs','php');
			
			$this->load->library('form_validation');
			$this->lang->load("form");
			$this->load->model("form");
			
			$options=array();
			foreach ($files as $file=>$value) {
				$options[$file]=$file;
			}
			$file=current($options);
			$search='';
			$fromOptions=range(0,23);
			foreach ($fromOptions as $key) {
				$key=sprintf('%02d:00',$key).' - '.sprintf('%02d:59',$key);
				$fromOpts[$key]=$key;
			}
			$data=array( 	"logfiles"	=> array("label"=>'Logfiles:','type'=>'dropdown','options'=>$options,'value'=>$file),
										"from"			=> array('label'=>'time','type'=>'dropdown','options'=>$fromOpts),
										"search"		=> array('type'=>'dropdown','options'=>array(''=>'','FlexyAdmin'=>'FlexyAdmin','[Cfg]'=>'[Cfg]','[Plugin]'=>'[Plugin]'),'value'=>$search));
			$form=new form('admin/log');
			$form->set_data($data,'Logfiles');
			if ($form->validation()) {
				$file=$this->input->post('logfiles');
				$data['logfiles']['value']=$file;
				$from=$this->input->post('from');
				$data['from']['value']=$from;
				$search=$this->input->post('search');
				$data['search']['value']=$search;
			}
			$currentLog=read_file('sys/codeigniter/logs/'.$file);
			$logArr=explode(chr(10),$currentLog);
			foreach ($logArr as $key => $value) {
				if (!empty($search)) 	{if (strpos($value, $search)===FALSE) unset($logArr[$key]);}
				if (!empty($from))		{if (strpos($value, substr($from,0,2).':')===FALSE) unset($logArr[$key]);}
			}
			$currentLog=implode(chr(10),$logArr);

			$form->set_old_templates();
			$this->_add_content($form->render());
			$this->_add_content(div('after_form').h($file,1).'<pre>'.$currentLog.'<pre>'._div());
		}
		$this->_show_type("log");
		$this->_show_all();
	}



}

?>
