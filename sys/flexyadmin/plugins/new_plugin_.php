<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */

class Plugin_ {
	
	var $CI;
	var $CI;
	var $config;
	var $name;
	var $oldData;
	var $newData;

	public function __construct($name='plugin') {
		$this->CI=
		$this->name=$name;
		$this->load_config($name);
	}

	// if method of module can't be found, print a simple warning
	public function __call($function, $args) {
		echo '<div class="warning">PluginMethod: `'.ucfirst($function)."` doesn't exists.<div>";
	}

	// Methods for loading and setting config
	function load_config($name) {
		// $this->CI->config->load($name);
		$this->config=$this->CI->config->item($name);
	}

	function set_config($config,$merge=TRUE) {
		if ($merge)
			$this->config=array_merge($this->config,$config);
		else
			$this->config=$config;
	}



	function load_config($name) {
		$this->config=
		if ( $this->config->load($name,true) ) {
			$this->_cfg=$this->config->item($name);
			// trace_($this->_cfg);
		}
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
		if (isset($this->oldData) and is_array($this->oldData)) $this->fields=array_keys($this->oldData);
		foreach ($this->fields as $field) {
			$this->types[]=get_prefix($field);
		}
		// strace_(array('old'=>$this->oldData,'new'=>$this->newData));
	}
	
	function _get_content() {
		return $this->content;
	}
	
	// Same as in MyController:
	function _add_content($add) {
		$this->content.=$add;
	}
	function _show_type($type) {
		$this->showType=add_string($this->showType,$type,' ');
	}
	/////
	
	
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
				$check=($check and ($this->db->table_exists($table)) );
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
			if ($key!='id')	$this->db->set($key,$value);
		}
		$this->db->where('id',$this->id);
		$this->db->update($this->table);
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