<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */

class plugin {
	
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
	
	function plugin($name='plugin') {
		$this->plugin=$name;
		$this->CI=&get_instance();
	}
	
	function init($init=array()) {
		strace_('========= '.$this->plugin.' ============');
		$default=array('table'=>'$table','id'=>'','oldData'=>NULL,'newData'=>NULL);
		$init=array_merge($default,$init);
		
		$this->table=$init['table'];
		$this->id=$init['id'];
		$this->oldData=$init['oldData'];
		$this->newData=$init['newData'];
		$this->fields=array_keys($this->oldData);
		foreach ($this->fields as $field) {
			$this->types[]=get_prefix($field);
		}
		strace_(array('old'=>$this->oldData,'new'=>$this->newData));
	}
	
	function act_on($acts=array()) {
		$default=array('tables'=>NULL,'id'=>'','fields'=>NULL,'changedFields'=>NULL,'types'=>NULL,'changedTypes'=>NULL);
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
							if (empty($this->newData) or $this->newData[$field]!=$this->oldData[$field]) $changedFields=TRUE;
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
							if (in_array($pre,$types) and (empty($this->newData) or $this->newData[$field]!=$this->oldData[$field]) ) $changedFields=true;
						}
						if ($changedFields) {
							$this->actOn[$key]['act']=true;
							$this->act=true;
						}
					}
					break;
			}
		}
		// strace_($this->actOn);
		strace_($this->act);
		return $this->act;
	}

	function _update_data() {
		strace_("'$this->plugin ->_update_data'");
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
			$data=$this->_after_update();
			strace_("'$this->plugin ->_after_update'");
			if ($data and $this->oldData!=$data) {
				$this->_update_data();
				// strace_(array('changed'=>$data));
			}
		}
		// strace_(array('plugin'=>$this->plugin,'act'=>$this->act,'changed'=>($this->oldData!=$data),'diff'=>array_diff($this->oldData,$data)));
		return $data;
	}

	function after_delete($init) {
		$this->init($init);
		if ($this->act) {
			strace_("'$this->plugin ->_after_delete'");
			$this->_after_delete();
		}
		return $this->act;
	}

}

?>