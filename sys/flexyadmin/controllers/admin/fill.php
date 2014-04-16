<?php require_once(APPPATH."core/AdminController.php");

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

      if (!empty($addtable) and (empty($fields) or (count($fields)==1 and $fields[0]=='.'))) {
        // Voeg alle velden van gekozen tabel toe
        $fields=$this->db->list_fields($addtable);
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
					$this->db->select('id');
					if (!empty($where)) $this->db->where($where);
					$items=$this->db->get_result($table);
          $year=(int) date('Y');
					foreach ($items as $id => $item) {
						$result=$fill;
            if ($random) {
              $pre=get_prefix($field);
              switch($pre) {
                case 'id' :
                  if (!isset($data[$field])) {
                    $ftable=foreign_table_from_key($field);
                    $data[$field]=$this->db->get_result($ftable);
                  }
                  $result=random_element($data[$field]);
                  $result=$result['id'];
                  break;
                case 'txt':
                  $result=$lorem->getContent(rand(50,500),'html');
                  break;
                case 'stx':
                  $result=$lorem->getContent(rand(10,50),'plain');
                  break;
                case 'medias':
                case 'media':
                  $result='';
                  $path=$this->cfg->get('cfg_media_info',$table.'.'.$field,'path');
                  if (!isset($files[$path])) $files[$path]=$this->mediatable->get_files($path,FALSE);
                  if ($pre=='media') {
                    if (rand(1,4)>2) {
                      $result=random_element($files[$path]);
                      $result=$result['file'];
                    }
                  }
                  else {
                    $result='';
                    for ($i=0; $i < rand(0,4); $i++) { 
                      $media=random_element($files[$path]);
                      $result=add_string($result,$media['file'],'|');
                    }
                  }
                  break;
                case 'url' :
                  $result='';
                  if (rand(1,4)>2) {
                    // Link from link table
                    if (!isset($links_table)) $links_table=$this->db->get_result('tbl_links');
                    $url=random_element($links_table);
                    $result=$url['url_url'];
                  }
                  break;
                case 'int':
                  $result=rand(0,100);
                  break;
                case 'dec':
                  $result=rand(10,99).'.'.rand(10,99);
                  break;
                  case 'date':
                case 'dat':
                  $result=rand($year,$year+1).'-'.rand(1,12).'-'.rand(1,31);
                  break;
                case 'tme':
                  $result=rand($year,$year+1).'-'.rand(1,12).'-'.rand(1,31). ' '.rand(0,23).':'.rand(0,59).':'.rand(0,59);
                  break;
                case 'time':
                  $result=rand(0,23).':'.rand(0,59).':'.rand(0,59);
                  break;
                case 'str':
                  $result='';
                  if ($field=='str_video') {
                    if (rand(1,4)>2) {
                      // Get youtube homepage, and alle the youtube links from them
                      if (!isset($YouTubeHTML)) {
                        $YouTubeHTML=file_get_contents('https://www.youtube.com/');
                        if (preg_match_all("/href=\"\\/watch\\?v=(.*)\"/uiUsm", $YouTubeHTML,$matches)) {
                          $YouTubeCodes=$matches[1];
                        }
                      }
                      $result=random_element($YouTubeCodes);
                    }
                  }
                  else {
                    $options=$this->cfg->get('cfg_field_info',$table.'.'.$field,'str_options');
                    if (!empty($options)) {
                      $options=explode('|',$options);
                      $result=random_element($options);
                    }
                    else {
                      $result=str_replace(array('.',','),'',$lorem->getContent(rand(1,5),'plain'));  
                    }
                  }
                  break;
                default:
                  $result=random_string();
                  break;
                default:
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
			if (!$addtable or $test) {
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
