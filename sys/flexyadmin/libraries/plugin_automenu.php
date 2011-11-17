<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class Plugin_automenu extends Plugin_ {

	var $menuTable;
	var $automationTable;
	var $resultMenu;
	
	var $newMenu;
	var $lastId;
	var $parentIDs;
	var $languages;
	
	var $automationData;


	public function __construct() {
		parent::__construct();
		$this->automationTable='cfg_auto_menu';
		$this->resultMenu='res_menu_result';
	}

	public function _trigger() {
		$trigger=NULL;
		if ($this->CI->db->table_exists($this->automationTable)) {
			$checkTables=NULL;
			$checkTables[]=$this->automationTable;
			$checkTables[]=$this->resultMenu;
			// get tables from automation data
			$this->automationData=$this->CI->db->get_results($this->automationTable);
			foreach ($this->automationData as $aData) {
				if (!in_array($aData['table'], $checkTables))	$checkTables[]=$aData['table'];
				$foreignTable=$aData['field_group_by'];
				$foreignTable=explode('.',$foreignTable);
				if (isset($foreignTable[1])) {
					$foreignTable=$foreignTable[1];
					$foreignTable=foreign_table_from_key($foreignTable);
					if (!in_array($foreignTable, $checkTables))	$checkTables[]=$foreignTable;
				}
			}
			$trigger=array('tables'=>$checkTables);
		}
		return $trigger;
	}
	

	public function _after_update() {
		$this->_create_auto_menu();
		return $this->newData;
	}

	public function _after_delete() {
		$this->_create_auto_menu();
		return false;
	}

	public function _admin_api($args=NULL) {
		$this->add_content(h($this->name,1));
		$this->_create_auto_menu();
		$this->add_content('<p>Menu reset ready.');
	}
	

	/**
	 * Make sure that data has newData!!
	 */
	private function _get_current_data($table,$where='',$limit=0) {
		if (!empty($where)) $this->CI->db->where($where);
		$data=$this->CI->db->get_results($table,$limit);
		if ($table==$this->table and isset($this->newData['id'])) {
			$data[$this->newData['id']]=$this->newData;
		}
		return $data;
	}
	
	private function _create_auto_menu() {
		$lastOrder=0;
		$this->newMenu=array();
		$this->parentIDs=array();
		$this->lastId=1;

		// Loop through all options in Auto Menu
		
		foreach ($this->automationData as $autoKey => $autoValue) {

			switch($autoValue['str_type']) {
				

				case 'menu item':
					$name=$autoValue['str_description'];
					$uri=safe_string($name);
					$item=array('uri'=>$uri,'str_title'=>$name);
					$item=$this->_setResultMenuItem($item,true);
					$this->_insertItem($item);
					// $this->_moveChildren();
					break;

				
				case 'from menu table':
					$data=$this->_get_current_data($autoValue['table']);
					foreach ($data as $item) {
						$item=$this->_setResultMenuItem($item,true);
						$item['str_table']=$autoValue['table'];
						$item['str_uri']=$item['uri'];
						$item['int_id']=$item['id'];
						$this->_insertItem($item);
					}
					$this->_moveChildren();
					break;
		
		
				case 'from submenu table':
					$where='';
					if (isset($autoValue['str_where']) and !empty($autoValue['str_where'])) {
						$where=$this->CI->db->where($autoValue['str_where']);
					}
					$limit=0;
					if (isset($autoValue['int_limit'])) {
						$limit=$autoValue['int_limit'];
					}
					$data=$this->_get_current_data($autoValue['table'],$where,$limit);
					
					$order=0;
					$parent=0;
					if (!empty($autoValue['str_parent_where'])) {
						$parent=$this->_get_where_parent($autoValue);
					}
					// trace_($autoValue);
					// trace_($parent);
					// trace_($data);
					// is er al een sub? Gebruik die order
					$sub=$this->newMenu;
					$sub=find_row_by_value($sub,$parent,'self_parent');
					if ($sub) {
						$sub=current($sub);
						$order=$sub['order']+1;
					} 

					// stop ze er eerst allemaal in met zelfde parent en onthou ondertussen dat ze eventueel andere parent moeten krijgen
					$parIDs=array(); // array met id's die andere parent moeten krijgen
					$oldIDs=array(); // onthou hier de originele id's
					foreach ($data as $item) {
						$this->_setResultMenuItem($item);
						$item['str_table']=$autoValue['table'];
						$item['str_uri']=$item['uri'];
						$item['int_id']=$item['id'];
						if (!isset($item['self_parent'])) $item['self_parent']=0;
						if (isset($parent) and $item['self_parent']==0) {
							$item['self_parent']=$parent;
						}
						elseif ($item['self_parent']>0) {
							$item['old_parent']=$item['self_parent'];
							$item['self_parent']=$parent;
						}
						if (!isset($item['order'])) $item['order']=$order++;
						if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
							if (isset($this->newMenu[$item['self_parent']][$this->config['module_field']])) {
								$parentModule=$this->newMenu[$item['self_parent']][$this->config['module_field']];
								if (isset($item[$this->config['module_field']]))
									$item[$this->config['module_field']].=' '.$parentModule;
								else
									$item[$this->config['module_field']]=$parentModule;
							}
						}
						$item=$this->_insertItem($item);
						if (isset($item['old_parent'])) {
							$parIDs[$item['id']]=$item['old_parent'];
							unset($item['old_parent']);
						}
						$oldIDs[$item['int_id']]=$item['id'];
					}
					// ok, verplaats nu naar goede parents nu ze allemaal bekend zijn
					foreach ($parIDs as $id => $oldParent) {
						$newParent=$oldIDs[$oldParent];
						$this->newMenu[$id]['self_parent']=$newParent;
					}
					break;
		
		
				case 'from category table':
					$data=$this->_get_current_data($autoValue['table']);
					$self_parents=array();
					if (!empty($autoValue['str_parent_uri'])) {
						// On multiple places?
						if (strpos($autoValue['str_parent_uri'],'/*')!==false) {
							$topUri=str_replace('/*','',$autoValue['str_parent_uri']);
							$topItem=find_row_by_value($this->newMenu,$topUri,'uri');
							$topItem=current($topItem);
							$parentItems=find_row_by_value($this->newMenu,$topItem['id'],'self_parent');
						}
						else {
							$parentItems=find_row_by_value($this->newMenu,$autoValue['str_parent_uri'],'uri');
						}
						$self_parents=array_keys($parentItems);
					}
					$lastOrder=0;
					if (empty($self_parents)) {
						$self_parents[]=0;
						$last=find_row_by_value($this->newMenu,0,'self_parent');
						if ($last) {
							$last=sort_by($last,'order',TRUE);
							$last=current($last);
							$lastOrder=$last['order']+1;
						}
						else {
							$lastOrder=0;
						}
					}
					foreach ($data as $key=>$item) {
						foreach ($self_parents as $self_parent) {
							$item=$this->_setResultMenuItem($item);
							$item['order']=$lastOrder++;
							$item['self_parent']=$self_parent;
							$item['str_table']=$autoValue['table'];
							$item['str_uri']=$item['uri'];
							$item['int_id']=$item['id'];
							if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
								if (isset($this->newMenu[$item['self_parent']][$this->config['module_field']])) {
									$parentModule=$this->newMenu[$item['self_parent']][$this->config['module_field']];
									if (isset($item[$this->config['module_field']]))
										$item[$this->config['module_field']].=' '.$parentModule;
									else
										$item[$this->config['module_field']]=$parentModule;
								}
							}
							$this->_insertItem($item);
						}
					}
					break;
		
		
				case 'from table group by category':
					// trace_($autoValue);
					$pagination=(int)$autoValue['str_parameters'];
					// check if table is many table
					$fromRel=false;
					$preTable=get_prefix($autoValue['field_group_by']);
					if ($preTable=='rel') {
						// yes many table
						$fromRel=true;
						$groupField=get_suffix($autoValue['field_group_by'],'.');
						$groupTable='tbl_'.remove_prefix($groupField);
					}
					else {
						// foreign table
						$groupField=remove_prefix($autoValue['field_group_by'],'.');
						$groupTable=foreign_table_from_key($groupField);
					}
					$groupData=$this->_get_current_data($groupTable);
					
					foreach ($groupData as $groupId=>$groupData) {
						$titleField='str_title';
						if (!isset($groupData[$titleField])) {
							$possibleFields=array_keys($groupData);
							$possibleFields=filter_by($possibleFields,'str_title');
							$titleField=current($possibleFields);
						}
						if ($fromRel) {
							$this->CI->db->add_many();
							$this->CI->db->where($autoValue['field_group_by'],$groupId);
						}
						else {
							$this->CI->db->where($autoValue['field_group_by'],$groupId);
						}
						$data=$this->_get_current_data($autoValue['table']);
						
						// trace_('#SHOW# '.$this->CI->db->ar_last_query);
						// trace_($groupId);
						// trace_($groupData);
						
						if ($data) {
							// trace_('Pagination: '.$pagination);
							// trace_('Count Data:'.count($data));
							$parentData=find_row_by_value($this->newMenu,$groupData[$titleField],$titleField);
							$parentData=current($parentData);
							// trace_($parentData);
							$selfParent=$parentData['id'];
							$lastOrder=0;
							$subData=find_row_by_value($this->newMenu,$selfParent,'self_parent');
							if ($subData) {
								// lastOrder is not 0
								$subData=array_slice($subData,count($subData)-1);
								$subData=current($subData);
								$lastOrder=$subData['order']+1;
							}
							if ($pagination) {
								$subOrder=$lastOrder;
								$lastOrder=0;
								$subSelfParent=$selfParent;
							}
							$nr=0;
							foreach ($data as $item) {
								if ($pagination and ($nr%$pagination==0)) {
									// add subpage if needed
									$page=round($nr/$pagination)+1;
									// trace_('Add subpage ['.$page.']');
									$subItem=array('uri'=>$page, $groupData[$titleField]=>$page, 'order'=>$subOrder++, 'self_parent'=>$subSelfParent);
									if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
										if (isset($this->newMenu[$subItem['self_parent']][$this->config['module_field']])) {
											$parentModule=$this->newMenu[$subItem['self_parent']][$this->config['module_field']];
											if (isset($subItem[$this->config['module_field']]))
												$subItem[$this->config['module_field']].=' '.$parentModule;
											else
												$subItem[$this->config['module_field']]=$parentModule;
										}
									}
									$self=$this->_insertItem( $subItem );
									$selfParent=$self['id'];
								}
								$nr++;
								
								$this->_setResultMenuItem($item);
								$item['order']=$lastOrder++;
								$item['self_parent']=$selfParent;
								$item['str_table']=$autoValue['table'];
								$item['str_uri']=$item['uri'];
								$item['int_id']=$item['id'];
								if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
									if (isset($this->newMenu[$item['self_parent']][$this->config['module_field']])) {
										$parentModule=$this->newMenu[$item['self_parent']][$this->config['module_field']];
										if (isset($item[$this->config['module_field']]))
											$item[$this->config['module_field']].=' '.$parentModule;
										else
											$item[$this->config['module_field']]=$parentModule;
									}
								}
								$this->_insertItem($item);
							}
						}
					}
					break;
					
					
				case 'split by language':
					$this->languages=$autoValue['str_parameters'];
					$this->languages=explode('|',$this->languages);
					$order=0;
					$beforeMenu=$this->newMenu;
					foreach ($this->languages as $lang) {
						// add language
						$item=array('uri'=>$lang,'order'=>$order++,'self_parent'=>-1,'str_title'=>$lang,'str_title_'.$lang=>$lang);
						if ($order==1) {
							$item=$this->_insertItem($item,1);
							$langID=$item['id'];
							// first language, just move current menu under it
							foreach ($this->newMenu as $id => $item) {
								if ($id!=$langID and $item['self_parent']==0)	$this->newMenu[$id]['self_parent']=$langID;
							}
						}
						else {
							$item=$this->_insertItem($item);
							$langID=$item['id'];
						 	$this->_addBranch($item,$beforeMenu);
						}
					}
					break;
					
				
				case 'by module':
					// call module
					$module=$autoValue['str_parameters'];
					$moduleFile=SITEPATH.'libraries/'.$module.'.php';
					if (file_exists($moduleFile)) {
						include_once($moduleFile);						// if function exists, call it
						$moduleFunction=$module;
						if (function_exists($moduleFunction)) {
							$items=$moduleFunction($autoValue);
							// Produce menu from returned items
							if ($items) {
								$order=0;
								$parent=$this->_get_where_parent($autoValue);
								if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
									if (isset($this->newMenu[$parent][$this->config['module_field']])) {
										$parentModule=$this->newMenu[$parent][$this->config['module_field']];
									}
								}
								foreach ($items as $item) {
									$item=$this->_setResultMenuItem($item,true);
									$item['self_parent']=$parent;
									$item['order']=$order;
									$order++;
									if (isset($parentModule)) $item[$this->config['module_field']]=$parentModule;
									$this->_insertItem($item);
								}
							}
						}
					}
					break;	
			}
			
		}

		// change some things
		foreach ($this->newMenu as $id => $item) {
		  // if self_parent -1 (language) replace with 0
			if (isset($item['self_parent']) and $item['self_parent']==-1) $item['self_parent']=0;
			$languages=$this->languages;
			if (empty($languages)) {
				$languages[]='';
			}
			foreach ($languages as $lang) {
				if (!empty($lang)) $lang='_'.$lang;
				if (!isset($item['str_title'.$lang])) $item['str_title'.$lang]='';
				if (isset($item['uri']) and empty($item['str_title'.$lang])) $item['str_title'.$lang]=$item['uri'];
			}
			$this->newMenu[$id]=$item;
		}
		ksort($this->newMenu);

		// put in db
		$this->CI->db->trans_start();
		
		$this->CI->db->truncate($this->resultMenu);
		$fields=$this->CI->db->list_fields($this->resultMenu);
		$lang='';
		
		foreach ($this->newMenu as $row) {
			if (isset($row['self_parent']) and isset($this->languages) and in_array($row['uri'],$this->languages)) $lang=$row['uri'];
			foreach ($row as $field => $value) {
				if (in_array($field,$fields)) {
					$this->CI->db->set($field,$value);
				}
				elseif ($lang!='') {
					$post=get_suffix($field);
					$langField=str_replace('_'.$lang,'',$field);
					if (in_array($langField,$fields)) {
						$this->CI->db->set($langField,$value);
					}
				}
			}
			$this->CI->db->insert($this->resultMenu);
		}
		
		$this->CI->db->trans_complete();
		if ($this->CI->db->trans_status() === FALSE) {
			trace_('Sorry, transaction error');
		}

		// update linklist etc
		if (!isset($this->CI->editor_lists)) $this->CI->load->library('editor_lists');
		$this->CI->queu->add_call(@$this->CI->editor_lists,'create_list','links');
	}


	private function _get_where_parent($autoValue) {
		// parse where...
		$whenParser=preg_split('/\s*(<>|!=|=|>|<)\s*/',$autoValue['str_parent_where'],-1,PREG_SPLIT_DELIM_CAPTURE);
		// TODO: only '=' operator works now
		$parent=find_row_by_value($this->newMenu,str_replace(array('"',"'"),'',$whenParser[2]),$whenParser[0]);
		if ($parent) {
			$parent=current($parent);
			$parent=$parent['id'];
		}
		return $parent;
	}

	private function _setResultMenuItem($item,$setId=false) {
		return $item;
	}
	
	private function _insertItem($item,$id='') {
		if (empty($id)) {
			$this->lastId++;
			if (!isset($item['id'])) $item['id']=$this->lastId;
			$this->parentIDs[$item['id']]=$this->lastId;
			$item['id']=$this->lastId;
			$this->newMenu[$this->lastId]=$item;
		}
		else {
			$item['id']=$id;
			$this->newMenu[$id]=$item;
		}
		return $item;
	}
	
	private function _moveChildren($fromID=-1) {
		$parentIDs=$this->parentIDs;
		foreach ($this->newMenu as $id => $item) {
			if ($id>$fromID and isset($item['self_parent']) and $item['self_parent']>0 and isset($parentIDs[$item['self_parent']])) {
				$this->newMenu[$id]['self_parent']=$parentIDs[$item['self_parent']];
			}
		}
	}
	
	private function _addBranch($topItem,$branch) {
		$fromId=$this->lastId;
		$parentIDs=array();
		foreach ($branch as $oldId => $item) {
			if ($item['self_parent']==0) $item['self_parent']=$topItem['id'];
			$newItem=$this->_insertItem($item);
		}
		$this->_moveChildren($fromId);
	}
	
	
	
}

?>