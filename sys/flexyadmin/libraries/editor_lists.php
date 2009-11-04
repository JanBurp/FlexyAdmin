<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Editor_lists {

	var $type;

	function Editor_lists($type="img")
	{
		$this->set_type($type);
	}

	function set_type($type="img") {
		$types=array();
		$types["img"]				=array(	"boolean_field" => "b_in_img_list",
																"array_name"		=> "tinyMCEImageList",
																"file_name"			=> "img_list");
		$types["media"]			=array(	"boolean_field" => "b_in_media_list",
																"array_name"		=> "tinyMCEMediaList",
																"file_name"			=> "media_list");
		$types["downloads"]	=array(	"boolean_field" => "b_in_link_list",
																"array_name"		=> "tinyMCELinkList",
																"file_name"			=> "link_list");
		$types["links"]			=array(	"array_name"		=> "tinyMCELinkList",
																"file_name"			=> "link_list");
		$this->type=$types[$type];
	}

	function create_list($type="") {
		if ($type=="links") $type="downloads";
		if (!empty($type)) $this->set_type($type);
		$t=$this->type;
		$boolField=el("boolean_field",$t);
		$jsArray	=$t["array_name"];
		$jsFile		=$t["file_name"];
		$CI =& get_instance();

		$data=array();

		if ($type=="downloads") {
			
			// add special links from tbl_site
			$data['-- info -----------------']=NULL;
			$tblSite=$CI->db->get_row('tbl_site');
			if (isset($tblSite['url_url'])) {
				$name=str_replace('http://','',$tblSite['url_url']);
				$data[$name]=array('name'=>$name,'url'=>$tblSite['url_url']);
			}
			if (isset($tblSite['email_email'])) {
				$name=$tblSite['email_email'];
				$data[$name]=array('name'=>$name,'url'=>'mailto:'.$tblSite['email_email']);
			}

			// add links from links table
			$data['-- links ----------------']=NULL;
			$table=$CI->cfg->get('CFG_editor','table');
			if ($CI->db->table_exists($table)) {
	 			$CI->db->select("str_title,url_url");
				$CI->db->order_by("str_title");
				$query=$CI->db->get($table);
				foreach($query->result_array() as $row) {
					$data[$row["str_title"]]=array("url"=>$row["url_url"],"name"=>$row["str_title"]);
				}
			}
			
			// add if asked for, internal links from menu table
			if ($CI->cfg->get('CFG_editor','b_add_internal_links')) {
				$data['-- site links -----------']=NULL;
				$menuTable=$CI->cfg->get('CFG_configurations','str_menu_table');
				if ($CI->db->table_exists($menuTable.'_result')) $menuTable=$menuTable.'_result'; // for menu automation
				if (!empty($menuTable) and $CI->db->table_exists($menuTable)) {
					$menuFields=$CI->db->list_fields($menuTable);
					$CI->db->select('id,uri,order');
					if (in_array('self_parent',$menuFields)) {
						$CI->db->select('self_parent');
						$CI->db->order_as_tree();
						$CI->db->uri_as_full_uri();
					}
					$CI->db->select_first('str');
					$results=$CI->db->get_results($menuTable);
					// add results to link list
					$nameField=$CI->db->get_select_first(0);
					foreach ($results as $key => $row) {
						$url=$row["uri"];
						$name=$row[$nameField];
						if (isset($row['self_parent']) and $row['self_parent']!=0) $name=' └ '.$name;
						$data[$name]=array("url"=>$url,"name"=>$name);
					}
				}
			}
		}

		// Media files (for download links)
		$mediaTbl=$CI->config->item('CFG_table_prefix')."_".$CI->config->item('CFG_media_info');
		if ($CI->db->table_exists($mediaTbl)) {
			$data['-- downloads ----------']=NULL;
			$CI->db->select("path");
			$CI->db->where($boolField,1);
			$query=$CI->db->get($mediaTbl);
			foreach($query->result_array() as $row) {
				$path=$row["path"];
				$map=$CI->config->item('ASSETS').$path;
				$subFiles=read_map($map);
				$subFiles=not_filter_by($subFiles,"_");
				$data=$data + $subFiles;
			}
		}

		// ignorecase_ksort($data);
		
		// trace_($data);


		// set list
		$list="var $jsArray = new Array(";
		if ($type=="links" or $type=="downloads") {
			foreach($data as $name=>$link) {
				if (empty($link)) {
					$list.='[""],';
					$list.='["'.$name.'"],';
				}
				else {
					if ($type=="downloads" and isset($link['type']))
						$list.='["'.$link['type'].': '.$link["name"].'","'.$link["path"].'"],';
					else
						$list.='["'.$link["name"].'","'.$link["url"].'"],';
				}
			}
		}
		else {
			foreach($data as $name=>$file) {
				$name=nice_string(get_file_without_extension($file["name"]));
				$list.='["'.$name.'","'.$file["path"].'"],';
			}
		}
		$list=substr($list,0,strlen($list)-1);
		$list.=");";
		$ListFile="site/assets/lists/$jsFile.js";
		$result=write_file($ListFile, $list);
		// trace_($result);
		// trace_($list);
		// trace_($ListFile);
		return $result;
	}

	function delete($file,$type="") {
		$this->create_list($type);
	}

	function add($file,$type="") {
		$this->create_list($type);
	}

}
?>
