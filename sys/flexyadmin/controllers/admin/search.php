<?php require_once(APPPATH."core/AdminController.php");

/**
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 *
 */

class Search extends AdminController {

  private $settings=FALSE;

	function __construct() {
		parent::__construct();
    $this->load->config('search_replace',true);
    $this->settings=$this->config->item('search_replace');
	}

	function index() {
		if ($this->user->can_use_tools()) {
			$this->lang->load('help');
			$this->lang->load('form');

			$settings=$this->input->post('settings');
      if ($settings and isset($this->settings[$settings])) {
  			$search=$this->settings[$settings]['search'];
  			$replace=$this->settings[$settings]['replace'];
  			$fields=$this->settings[$settings]['fields'];
        if (!is_array($fields)) $fields=array($fields);
        // $regex=$this->settings[$settings]['regex'];
      }
      else {
  			$search=$this->input->post('search');
  			$replace=$this->input->post('replace');
  			$fields=$this->input->post('fields');
        // $regex=$this->input->post('regex');
      }
			$test=$this->input->post('test');

			if ($search) {
				$htmlTest=h(lang('sr_result'),1);
        $testFields=array();
				foreach ($fields as $key=>$value) {
					$table=get_prefix($value,'.');
					$field=get_suffix($value,'.');
          if ($table=='*') {
            $tables=$this->db->list_tables();
            $tables=filter_by($tables,'tbl');
            foreach ($tables as $table) {
              if ($this->db->field_exists($field,$table)) $testFields[]=array('table'=>$table,'field'=>$field);
            }
          }
          else {
            $testFields[]=array('table'=>$table,'field'=>$field);
          }
        }
        
				foreach ($testFields as $f) {
					$table=$f['table'];
					$field=$f['field'];
					$this->db->select(PRIMARY_KEY);
					$this->db->select($field);
					$result=$this->db->get_result($table);
					$htmlTest.="";
					foreach($result as $id=>$row) {
						unset($row[PRIMARY_KEY]);
						foreach ($row as $key=>$txt) {
              $count=0;
              $matches=FALSE;
							$oldErrorHandler=set_error_handler(array($this,"myErrorHandler"));
							$new=preg_replace("/$search/",$replace,$txt,-1,$count);
              if ($count>0) {
                preg_match_all("/$search/", $txt,$matches);
              }
							set_error_handler($oldErrorHandler);
							if ($new!=$txt) {
								$this->db->as_abstracts();
								$this->db->where(PRIMARY_KEY,$id);
								$abstract=$this->db->get_row($table);
								$abstract=$abstract['abstract'];
                
								$htmlTest.="<h4>".$this->ui->get($table)." - <i>$abstract</i></h4>";
                if ($matches) {
                  $htmlTest.='<ol>';
                  foreach ($matches[0] as $match) {
                    $htmlTest.='<li><span class="searched">'.htmlentities($match).'</span> &gt;&gt; <span class="replaced">'.htmlentities(preg_replace("/$search/",$replace,$match)).'</span></li>';
                  }
                  $htmlTest.='</ol>';
                }
							}
							if (!$test) {
								$this->db->set($key,$new);	
							}
						}
						if (!$test) {
							$this->db->where(PRIMARY_KEY,$id);
							$res=$this->db->update($table);
						}
						// $this->db->as_abstracts(FALSE);
					}
					$htmlTest.="";
				}
			}
			if (!$search or $test) {
				// show form
				$this->load->library('form');
				$this->load->model('flexy_field','ff');
				$form=new form($this->config->item('API_search'));
				
				// fields to search in
				if (is_array($fields))
					$selection=$fields;
				else
					$selection=array('');
				$selection=array_combine($selection,$selection);
				$fields=$this->ff->_dropdown_fields_form();
				$fields=$fields['options'];
				unset($fields['']);
				foreach ($fields as $key => $value) {
					$field=get_suffix($key,'.');
					if ($field=='id' or $field=='user')
						unset($fields[$key]);
					else {
						$type=get_prefix($field);
					}
				}
        ksort($fields);
        
				$data=array();
        if ($this->settings) {
          $options=array_keys($this->settings);
          $options=array_merge(array(''),$options);
          $options=array_combine($options,$options);
          $data["settings"] = array("label"=>lang('sr_settings'),"type"=>'dropdown', "value"=>$settings,'options'=>$options);
        }	
        $data["search"] = array("label"=>lang('sr_search'),"value"=>$search);
        $data["replace"] = array("label"=>lang('sr_replace'),"value"=>$replace);
        // $data["regex"] = array("label"=>lang('sr_regex'),"type"=>"checkbox","value"=>$regex);
        $data["fields"] = array("label"=>lang('sr_fields'),"type"=>"dropdown","multiple"=>"mutliple","options"=>$fields,"value"=>$selection);
        $data["test"] = array("type"=>'checkbox','value'=>1);
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
