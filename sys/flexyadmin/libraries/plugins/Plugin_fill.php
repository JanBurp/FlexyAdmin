<?php

/** \ingroup controllers
 * Special Controller Class
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Plugin_fill extends Plugin {
  
  public function __construct() {
		parent::__construct();
	}

  public function _admin_api() {
		if ($this->CI->flexy_auth->can_use_tools()) {
			$this->CI->lang->load('help');
			$this->CI->lang->load('form');
      $this->CI->load->library('lorem');
      $lorem = new Lorem();
		
			$aantal          = $this->CI->input->post('aantal');
			$addtable        = $this->CI->input->post('addtable');
			$fields          = $this->CI->input->post('fields');
			$where           = $this->CI->input->post('where');
      $fill            = $this->CI->input->post('fill');
      $random          = $this->CI->input->post('random');
      $many_to_many    = $this->CI->input->post('many_to_many');
			$test            = $this->CI->input->post('test');
      
			$htmlTest='';

			if (!is_array($fields)) $fields=explode(',',$fields);

			// create rows in table
			if ($aantal and $addtable) {
        $this->CI->data->table($addtable);
				$first_field = remove_prefix(current($fields),'.');
				for ($i=0; $i < $aantal; $i++) { 
					$id='#';
					if (!$test) $id = $this->CI->data->table($addtable)->set($first_field,random_string())->insert();
					$htmlTest.="<li>+ $addtable [$id]</li>";
				}
			}

      if (!empty($addtable) and (empty($fields) or (count($fields)==1 and $fields[0]=='.'))) {
        // Voeg alle velden van gekozen tabel toe
        $fields = $this->CI->data->table($addtable)->list_fields();
        foreach ($fields as $key => $field) {
          $fields[$key]=$addtable.'.'.$field;
          if ($field=='id') unset($fields[$key]);
        }
      }
      // Voeg many_to_many velden toe
      if ($many_to_many) {
        $relations = $this->CI->data->table( $addtable )->get_setting(array('relations','many_to_many'));
        if ($relations) {
          foreach ($relations as $relation) {
            array_push($fields,$addtable.'.rel_'.$relation['result_name']);
          }
        }
      }
      
			// fill fields
			if ($fields and $addtable) {
				foreach($fields as $field) {
					$table = get_prefix($field,'.');
          $field = get_suffix($field,'.');
          // items
          $this->CI->data->table($table)->select('id');
					if (!empty($where)) $this->CI->data->where($where);
					$items = $this->CI->data->get_result();
					foreach ($items as $id => $item) {
						$result = $fill;
            if ($random) {
              $result = $this->CI->data->table($table)->random_field_value( $field, $id );
            }
            if (!$test and isset($result)) {
              $this->CI->data->table($table)->where('id',$id);
              if (!empty($where)) $this->CI->data->where($where);
              $this->CI->data->set($field,$result);
              $this->CI->data->update();
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
        $this->CI->load->model( 'Data/Options_Tables');
        $this->CI->load->model( 'Data/Options_Fields');
				$tablesOptions=$this->CI->Options_Tables->get_options();
				$fieldsOptions=$this->CI->Options_Fields->get_options();
				if (empty($fields)) $fields=array();
				else $fields=array_combine($fields,$fields);
				// create form
				$data=array(
					'aantal'       => array( 'label' => lang('fill_aantal'), 'value' => $aantal ),
					'addtable'     => array( 'label' => lang('fill_tables'), 'value' => $addtable, 'type' => 'select', 'options' => $tablesOptions ),
					'fields'       => array( 'label' => lang('fill_fields'), 'value' => $fields, 'type' => 'select', 'options' => $fieldsOptions, 'multiple' => 'multiple' ),
					'where'        => array( 'label' => lang('fill_where'), 'value' => $where ),
					'fill'         => array( 'label' => lang('fill_with'), 'value' => $fill ),
					'random'       => array( 'label' => lang('fill_use_random'),'type' => "checkbox", 'value' => 1 ),
					'many_to_many' => array( 'label' => 'Many to Many',	'type' => "checkbox", 'value' => 1 ),
					'test'         => array( 'label' => 'test',	'type' => 'checkbox', 'value' => 1 )
				);
				
				$this->CI->load->library('vueform');
				$form=new vueform(array(
					'fields'=>$data,
					'title' => lang('fill_fill')
				));
				$this->content .= $form->render();
			}
			$this->content .= div('after_form').$htmlTest._div();	
		}

		return $this->content;
		// $this->CI->load->view('admin/plugins/plugin',array('title'=>'Fill','content'=>$this->content));
	}

}

?>
