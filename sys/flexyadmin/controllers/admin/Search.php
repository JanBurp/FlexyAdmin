<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Search extends AdminController {

  private $settings=FALSE;
  private $regex_error=FALSE;

	function __construct() {
		parent::__construct();
    $this->load->config('search_replace',true);
    $this->settings=$this->config->item('search_replace');
	}

	function index() {
		if ($this->flexy_auth->can_use_tools()) {
			$this->lang->load('help');
			$this->lang->load('form');

			$settings=$this->input->post('settings');
      if ($settings and isset($this->settings[$settings])) {
  			$search=$this->settings[$settings]['search'];
  			$replace=$this->settings[$settings]['replace'];
  			$fields=$this->settings[$settings]['fields'];
        if (!is_array($fields)) $fields=array($fields);
        $regex=$this->settings[$settings]['regex'];
      }
      else {
  			$search=$this->input->post('search');
  			$replace=$this->input->post('replace');
  			$fields=$this->input->post('fields');
        $regex=$this->input->post('regex');
      }
			$test=$this->input->post('test');

			if ($search) {
				$htmlTest=h(lang('sr_result'),1);
        $testFields=array();
        $searchTerm=$search;
        $replaceTerm=$replace;
        if (!$regex) {
          $searchTerm=preg_quote($search,'/');
          // $replaceTerm=preg_quote($replaceTerm,'/');
        }
				foreach ($fields as $key=>$value) {
					$table=get_prefix($value,'.');
					$field=get_suffix($value,'.');
          if ($table=='*') {
            $tables=$this->db->list_tables();
            $tables=not_filter_by($tables,array('cfg','log'));
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
          $result = $this->data->table( $table )
                              ->select(PRIMARY_KEY)
                              ->select($field)
					                    ->get_result();
					$htmlTest.="";
					foreach($result as $id=>$row) {
						unset($row[PRIMARY_KEY]);
						foreach ($row as $key=>$txt) {
              $count=0;
              $matches=FALSE;
              $oldErrorHandler=set_error_handler(array($this,"myErrorHandler"));
							$new=preg_replace("/$searchTerm/",$replaceTerm,$txt,-1,$count);
              if ($count>0) {
                preg_match_all("/$searchTerm/", $txt,$matches);
              }
              set_error_handler($oldErrorHandler);
							if ($new!==$txt) {
                $abstract = $this->data->table($table)
                                        ->where(PRIMARY_KEY,$id)
                                        ->select_abstract()
                                        ->get_row();
								$abstract = $abstract['abstract'];
								$htmlTest.="<h4>".$this->ui->get($table)." - <i>$abstract</i></h4>";
                if ($matches) {
                  $htmlTest.='<ol>';
                  foreach ($matches[0] as $match) {
                    $htmlTest.='<li>"<span class="searched">'.str_replace(' ','&nbsp;',htmlentities($match)).'</span>" &gt;&gt; "<span class="replaced">'.str_replace(' ','&nbsp;',htmlentities(preg_replace("/$searchTerm/",$replaceTerm,$match))).'</span>"</li>';
                  }
                  $htmlTest.='</ol>';
                }
							}
							if (!$test) {
								$this->data->set($key,$new);	
							}
						}
						if (!$test) {
							$this->data->where(PRIMARY_KEY,$id);
							$res = $this->data->update();
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
        $this->load->model( 'Data/Options_Core' );
        $this->load->model( 'Data/Options_Fields');
				$fields=$this->Options_Fields->get_options();
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
        $data["search"]  = array("label"=>lang('sr_search'),"value"=>$search,'validation'=>'required');
        $data["replace"] = array("label"=>lang('sr_replace'),"value"=>$replace,'validation'=>'required');
        $data["regex"]   = array("label"=>lang('sr_regex'),"type"=>"checkbox","value"=>$regex);
        $data["fields"]  = array("label"=>lang('sr_fields'),"type"=>"dropdown","multiple"=>"mutliple","options"=>$fields,"value"=>$selection,'validation'=>'required');
        $data["test"]    = array("type"=>'checkbox','value'=>1);
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

	private function myErrorHandler($errno, $errstr, $errfile, $errline) 	{
    if ($errno==E_WARNING and has_string('preg',$errstr)) {
			if (!$this->regex_error) {
        $this->_add_content(p('error').lang('bad_regex').' : '.$errstr._p());
				$this->regex_error=TRUE;
			}
			return true;
    }
    return false;
	}

}

?>
