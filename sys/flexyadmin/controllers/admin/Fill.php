<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * Special Controller Class
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Fill extends AdminController {

	function __construct() {
		parent::__construct();
	}

	function index() {
		if ($this->flexy_auth->can_use_tools()) {
			$this->lang->load('help');
			$this->lang->load('form');
      $this->load->library('lorem');
      $lorem = new Lorem();
		
			$aantal=$this->input->post('aantal');
			$addtable=$this->input->post('addtable');
			$fields=get_fields_from_input( $this->input->post('fields'), $addtable );
			$where=$this->input->post('where');
      $fill=$this->input->post('fill');
      $random=$this->input->post('random');
			$test=$this->input->post('test');
      
			$htmlTest='';

			// create rows in table
			if ($aantal and $addtable) {
        $this->data->table($addtable);
				$forbidden_fields=$this->config->item('FIELDS_special');
				$forbidden_fields=array_keys($forbidden_fields);
				$first_field=$this->data->list_fields();
				$first_field=not_filter_by($first_field,$forbidden_fields);
				$first_field=array_shift($first_field);
        if (empty($first_field)) {
          $first_field=$this->data->list_fields();
          $first_field=$first_field[1];
        }
				for ($i=0; $i < $aantal; $i++) { 
					$id='#';
					if (!$test) {
						$this->data->table($addtable)->set($first_field,'');
						$id = $this->data->insert_id();
					}
					$htmlTest.="<li>+ $addtable [$id]</li>";
				}
			}

      if (!empty($addtable) and (empty($fields) or (count($fields)==1 and $fields[0]=='.'))) {
        // Voeg alle velden van gekozen tabel toe
        $fields = $this->data->table($addtable)->list_fields();
        foreach ($fields as $key => $field) {
          $fields[$key]=$addtable.'.'.$field;
          if ($field=='id') unset($fields[$key]);
        }
      }

			// fill fields
			if ($fields and $addtable) {
				foreach($fields as $field) {
					$table=get_prefix($field,'.');
          $field=get_suffix($field,'.');
          // items
          $this->data->table($table)->select('id');
					if (!empty($where)) $this->data->where($where);
					$items = $this->data->get_result();
					foreach ($items as $id => $item) {
						$result=$fill;
            if ($random) {
              $result=$this->db->random_field_value($field,array('table'=>$table));
            }
						if (!$test) {
              $this->data->table($table)->where('id',$id);
							if (!empty($where)) $this->data->where($where);
							$this->data->set($field,$result);
							$this->data->update();
							// trace_($this->db->last_query());
						}
						$htmlTest.="<li>$field [$id] = '$result'</li>";
					}
				}		
			}
			if (!empty($htmlTest)) {
				$htmlTest=h(lang('fill_fill'),1)."<ul>".$htmlTest."</ul>";
			}
			if (!$addtable or $test) {
				// show form
				$this->load->library('form');
        $this->load->model( 'Data/Options_Tables');
        $this->load->model( 'Data/Options_Fields');
				$form=new form($this->config->item('API_fill'));
				$tablesOptions=$this->Options_Tables->get_options();
				$fieldsOptions=$this->Options_Fields->get_options();
				if (empty($fields)) $fields=array();
				else $fields=array_combine($fields,$fields);
				// create form
				$data=array( 	"aantal"	=> array("label"=>lang('fill_aantal'),"value"=>$aantal),
											"addtable"=> array("label"=>lang('fill_tables'),"value"=>$addtable,"type"=>'dropdown','options'=>$tablesOptions),
											"fields"	=> array("label"=>lang('fill_fields'),"value"=>$fields,"type"=>'dropdown','options'=>$fieldsOptions,'multiple'=>'multiple'),
											"where"		=> array("label"=>lang('fill_where'),"value"=>$where),
                      "fill"    => array("label"=>lang('fill_with'),"value"=>$fill),
											"random"	=> array("label"=>lang('fill_use_random'),"type"=>"checkbox","value"=>1),
                      // ""        => array("type"=>"html","value"=>'alt[yes|no|..],[int(min,max)],[str([html|mix|lower|upper],len)]'),
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
