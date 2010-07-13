<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */

class plugin_ {
	
	var $plugin;
	var $CI;
	
	var $table;
	var $id;
	var $oldData;
	var $newData;
	var $fields;
	var $types;
	
	var $actOn;
	var $act;
	
	function plugin_($name='plugin') {
		$this->plugin=$name;
		$this->CI=&get_instance();
	}
	
	function init($init=array()) {
		// strace_('========= '.$this->plugin.' ============');
		$default=array('table'=>'$table','id'=>'','oldData'=>NULL,'newData'=>NULL);
		$init=array_merge($default,$init);
		
		$this->table=$init['table'];
		$this->id=$init['id'];
		$this->oldData=$init['oldData'];
		$this->newData=$init['newData'];
		$this->fields=array();
		if (isset($this->oldData)) $this->fields=array_keys($this->oldData);
		foreach ($this->fields as $field) {
			$this->types[]=get_prefix($field);
		}
		// strace_(array('old'=>$this->oldData,'new'=>$this->newData));
	}
	
	function act_on($acts=array()) {
		$default=array('existingTables'=>NULL,'tables'=>NULL,'id'=>'','fields'=>NULL,'changedFields'=>NULL,'types'=>NULL,'changedTypes'=>NULL);
		$actOn=array_merge($default,$acts);
		$this->actOn=array();
		foreach ($actOn as $key => $value) {
			if ($key!='id') {
				if (!empty($value))	$value=explode(',',$value);
			}
			$this->actOn[$key]=array('value'=>$value,'act'=>false);
		}
		
		// check if action is needed:
		$this->act=false;
		$check=true;
		if (!empty($this->actOn['existingTables']['value'])) {
			foreach ($this->actOn['existingTables']['value'] as $table) {
				$check=($check and ($this->CI->db->table_exists($table)) );
			}
			$this->actOn['existingTables']['act']=$check;
		}
		
		if ($check) {
			foreach ($this->actOn as $key => $value) {
				switch($key) {
					case 'id'			:
						$id=$value['value'];
						if (!empty($this->id) and $this->id==$id) {
							$this->actOn[$key]['act']=true;
							$this->act=true;
						}
						break;
					case 'tables' :
						$tables=$value['value'];
						if (!empty($tables) and in_array($this->table,$tables)) {
							$this->actOn[$key]['act']=true;
							$this->act=true;
						}
						break;
					case 'fields' :
						$fields=$value['value'];
						if (!empty($fields)) {
							$intersect=array_intersect($this->fields, $fields);
							if (!empty($intersect)) {
								$this->actOn[$key]['act']=true;
								$this->act=true;
							}
						}
						break;
					case 'types' 	:
						$types=$value['value'];
						if (!empty($types)) {
							$intersect=array_intersect($this->types, $types);
							if (!empty($intersect)) {
								$this->actOn[$key]['act']=true;
								$this->act=true;
							}
						}
						break;
					case 'changedFields'	:
						$fields=$value['value'];
						if (!empty($fields)) {
							$changedFields=false;
							foreach ($fields as $field) {
								if ( (empty($this->newData) and isset($this->oldData[$field])) or (isset($this->newData[$field]) and $this->newData[$field]!=$this->oldData[$field]) ) $changedFields=TRUE;
							}
							if ($changedFields) {
								$this->actOn[$key]['act']=true;
								$this->act=true;
							}
						}
						break;
					case 'changedTypes'		:
						$types=$value['value'];
						if (!empty($types)) {
							$changedFields=false;
							foreach ($this->fields as $field) {
								$pre=get_prefix($field);
								if (in_array($pre,$types) and (empty($this->newData) or (isset($this->newData[$field]) and $this->newData[$field]!=$this->oldData[$field])) ) $changedFields=true;
							}
							if ($changedFields) {
								$this->actOn[$key]['act']=true;
								$this->act=true;
							}
						}
						break;
				}
			}
		}
		// strace_($this->actOn);
		// strace_($this->act);
		return $this->act;
	}

	function _update_data() {
		// strace_("'$this->plugin ->_update_data'");
		foreach ($this->newData as $key => $value) {
			if ($key!='id')	$this->CI->db->set($key,$value);
		}
		$this->CI->db->where('id',$this->id);
		$this->CI->db->update($this->table);
	}

	function after_update($init) {
		$this->init($init);
		$data=$this->newData;
		if ($this->act)	{
			$changed=$this->_after_update();
			// strace_("'$this->plugin ->_after_update'");
			if ($changed and $this->oldData!=$changed) {
				$this->_update_data();
				$data=$changed;
				// strace_(array('changed'=>$data));
			}
		}
		// strace_(array('plugin'=>$this->plugin,'act'=>$this->act,'changed'=>($this->oldData!=$data),'diff'=>array_diff($this->oldData,$data)));
		return $data;
	}

	function after_delete($init) {
		$this->init($init);
		if ($this->act) {
			// strace_("'$this->plugin ->_after_delete'");
			$this->_after_delete();
		}
		return $this->act;
	}

}

?>