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
			$table=$CI->cfg->get('CFG_editor','table');
			$CI->db->select("str_title,url_url");
			$query=$CI->db->get($table);
			foreach($query->result_array() as $row) {
				$data[$row["str_title"]]=array("url"=>$row["url_url"],"name"=>$row["str_title"]);
			}
		}
		//
		$mediaTbl=$CI->config->item('CFG_table_prefix')."_".$CI->config->item('CFG_media_info');
		if ($CI->db->table_exists($mediaTbl)) {
			$CI->db->select("str_path");
			$CI->db->where($boolField,1);
			$query=$CI->db->get($mediaTbl);
			foreach($query->result_array() as $row) {
				$path=$row["str_path"];
				$map=$CI->config->item('ASSETS').$path;
				$subFiles=read_map($map);
				$subFiles=not_filter_by($subFiles,"_");
				$data=$data + $subFiles;
			}
		}

		ignorecase_ksort($data);
		// trace_($data);

		// set list
		$list="var $jsArray = new Array(";
		if ($type=="links" or $type=="downloads") {
			foreach($data as $name=>$link) {
				if ($type=="downloads" and isset($link['type']))
					$list.='["'.$link['type'].': '.$link["name"].'","'.$link["path"].'"],';
				else
					$list.='["'.$link["name"].'","'.$link["url"].'"],';
			}
		}
		else {
			foreach($data as $name=>$file) {
				$list.='["'.$file["name"].'","'.$file["path"].'"],';
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
