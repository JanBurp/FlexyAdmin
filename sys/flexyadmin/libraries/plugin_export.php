<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class Plugin_export extends Plugin_ {


	public function _admin_api($args=NULL) {
		$this->add_content(h('Export',1));

		$tables=$this->CI->db->list_tables();
		$tables=filter_by($tables,'tbl');
		$tableOptions=array();
		$pre='';
		foreach ($tables as $key=>$table) {
			if ($this->CI->user->has_rights($table)) {
				$tableOptions[$table]=$this->CI->uiNames->get($table);
			}
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
		$form->set_old_templates();

		// Is form validation ok?
		if ($form->validation()) {

			$table=$this->CI->input->post('table');
			$type=$this->CI->input->post('type');

			$out=$this->export($table,$type);

			if ($out) {
				$this->CI->load->helper('download');
				force_download($table.'.'.$type, $out);
			}

		}
		else {
			$validationErrors=validation_errors('<p class="error">', '</p>');
			if (!empty($validationErrors)) $this->add_content($validationErrors);
			$this->add_content( $form->render() );
		}
		
	}


	private function export($table,$type='csv') {
		
		$data=$this->CI->db->get_result($table);
		$out='';
		
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
		
		return $out;
	}


}

?>