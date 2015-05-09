<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Exporteer een tabel
 * 
 * Je kunt de tabel kiezen en het formaat van de export.
 * Daarna start een download van de export
 * 
 * Of met de url:
 * - table
 * - type
 * - full/json geeft een volledige dump
 *
 * @author Jan den Besten
 */
class Plugin_export extends Plugin {

	public function __construct() {
		parent::__construct();
	}

  /**
   */
	public function _admin_api($args=NULL) {
		$this->add_content(h('Export',1));

		// What are possible tables to export?
		$tables=$this->config('tables');
		if (empty($tables)) {
			$tables=$this->CI->db->list_tables();
		}
		// only tables which user has rights for
		foreach ($tables as $key=>$table) {
			if (!$this->CI->user->has_rights($table)) unset($tables[$key]);
		}
    
		// Check if args are set, and if so, do the export
		if (isset($args[0])) {
			$table=$args[0];
			$type='csv';
			if (isset($args[1])) $type=$args[1];
			
			if (in_array($table,$tables)) {
				$this->export($table,$type);
				return $this->content;
			}
      else {
        // Specials
        if ($table=='full' and $type=='json') {
          $out="{\n";
          foreach ($tables as $table) {
            if ($table!='cfg_sessions') {
              $out.="'$table':".$this->export($table,$type,FALSE).",\n";
            }
          }
          $out.="}";
          $this->_download('ful_dump',$type,$out);

          $this->add_content(h('Fulldump!',2));
        }
      }
		}

		
		// No args, show the form:
		$tableOptions=array();
		foreach ($tables as $key=>$table) {
			if ($this->config('use_ui_names'))
				$tableOptions[$table]=$this->CI->ui->get($table);
			else
				$tableOptions[$table]=$table;
		}
		
		$typeOptions=array('csv','xml','json');
		if ($this->CI->user->is_super_admin()) $typeOptions[]='php';
		$typeOptions=array_combine($typeOptions,$typeOptions);

		$formData=array("table"	=> array("validation"=>"required",'type'=>'dropdown','options'=>$tableOptions),
										"type"	=> array("validation"=>"required",'type'=>'dropdown','options'=>$typeOptions) );
		$formButtons=array('submit'=>array("submit"=>"submit","value"=>"download"));

		$this->CI->load->library('form');
		$form=new form($this->CI->uri->get());
		$form->set_data($formData,"Export");
		$form->set_buttons($formButtons);

		// Is form validation ok?
		if ($form->validation()) {
			$table=$this->CI->input->post('table');
			$type=$this->CI->input->post('type');

			$this->export($table,$type);
		}
		else {
			$validationErrors=validation_errors('<p class="error">', '</p>');
			if (!empty($validationErrors)) $this->add_content($validationErrors);
			$this->add_content( $form->render() );
		}

    return $this->content;
	}

  /**
   */
	private function export($table,$type='csv',$download=true) {
		
		if ($this->config('add_foreigns')) {
			$this->CI->db->add_foreigns( $this->config('add_foreigns') );	
			if ($this->config('add_foreigns_as_abstracts')) $this->CI->db->add_foreigns_as_abstracts( $this->config('add_foreigns') );
		}
		if ($this->config('add_many')) {
			$this->CI->db->add_many( $this->config('add_many') );	
		}
		
		$data=$this->CI->db->get_result($table);

		// Keep only the abstract data
		if ($this->config('add_foreigns_as_abstracts')) {
			foreach ($data as $id => $row) {
				foreach ($row as $field => $value) {
					if (get_postfix($field,'__')=='abstract') {
						$foreign_field=remove_postfix($field,'__');
						$data[$id][$foreign_field]=$value;
						unset($data[$id][$field]);
					}
				}
			}
		}

		// Many data
		if ($this->config('add_many')) {
			foreach ($data as $id => $row) {
				foreach ($row as $field => $value) {
					if (is_array($value)) {
						if ($this->config('add_foreigns_as_abstracts')) {
							$val='';
							foreach ($value as $k => $v) {
								$val=add_string($val,$v['abstract'],'|');
							}
							$value=$val;
						}
						else {
							$value=array_keys($value);
							$value=implode('|',$value);
						}
						$data[$id][$field]=$value;
					}
				}
			}
		}


		// Nice names of fields and tables
		if ($this->config('use_ui_names')) {
			$ui_data=array();
			foreach ($data as $id => $row) {
				foreach ($row as $field => $value) {
					$ui_data[$id][$this->CI->ui->get($field)]=$value;
				}
			}
			$data=$ui_data;
		}

		// trace_($data);

		$out='';
    if ($data) {
  		switch ($type) {

  			case 'xml':
  				$out=array2xml($data);
  				break;

  			case 'json':
  				$out=array2json($data);
  				break;

  			case 'php':
  				$out=array2php($data);
  				break;
				
  			case 'csv':
  			default:
  				$out=array2csv($data);
  				break;

  		}
    }
		
		if (!empty($out)) {
      if ($download) {
  			$filename=$table;
  			if ($this->config('use_ui_names')) $filename=$this->CI->ui->get($filename);
        $this->_download($filename,$type,$out);
      }
  		return $out;
		}
    else {
      $this->add_content(p().'Nothing to export'._p());
    }
		
		return FALSE;
	}
  
  
  private function _download($filename,$type,$data) {
		$this->CI->load->helper('download');
		force_download($filename.'.'.$type, $data);	
  }


}

?>