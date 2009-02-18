<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Editor_lists {

	var $type;

	function Editor_lists($type="img")
	{
		$this->set_type($type);
	}

	function set_type($type="img") {
		$types=array();
		$types["img"]		=array(	"boolean_field" => "b_in_img_list",
														"array_name"		=> "tinyMCEImageList",
														"file_name"			=> "img_list");
		$types["media"]	=array(	"boolean_field" => "b_in_media_list",
														"array_name"		=> "tinyMCEMediaList",
														"file_name"			=> "media_list");
		$types["links"]	=array(	"array_name"		=> "tinyMCELinkList",
														"file_name"			=> "link_list");
		$this->type=$types[$type];
	}

	function create_list($type="") {
		if (!empty($type)) $this->set_type($type);
		$t=$this->type;
		$boolField=el("boolean_field",$t);
		$jsArray	=$t["array_name"];
		$jsFile		=$t["file_name"];
		$CI =& get_instance();

		$files=array();
		if ($type=="links") {
			$table=$CI->cfg->get('CFG_editor','table');
			$CI->db->select("str_title,url_url");
			$query=$CI->db->get($table);
			foreach($query->result_array() as $row) {
				$files[$row["str_title"]]=array("path"=>$row["url_url"],"alt"=>$row["str_title"]);
			}
			//$files=array_unique($files);
		}
		else {
			$mediaTbl=$CI->config->item('CFG_table_prefix')."_".$CI->config->item('CFG_media_info');
			if ($CI->db->table_exists($mediaTbl)) {
				$CI->db->select("str_path");
				$CI->db->where($boolField,1);
				$query=$CI->db->get($mediaTbl);
				foreach($query->result_array() as $row) {
					$path=$row["str_path"];
					$map=$CI->config->item('ASSETS').$path;
					$subFiles=read_map($map);
					$files=$files + $subFiles;
				}
			}
		}

		ignorecase_ksort($files);

		// set list
		$img_list="var $jsArray = new Array(";
		foreach($files as $name=>$file) {
			$img_list.='["'.$file["alt"].'","'.$file["path"].'"],';
		}
		$img_list=substr($img_list,0,strlen($img_list)-1);
		$img_list.=");";
		$imgListFile="site/assets/lists/$jsFile.js";
		$result=write_file($imgListFile, $img_list);
//		trace_($result);
//		trace_($imgListFile);
//		trace_(file_exists($imgListFile));
	}

	function delete($file,$type="") {
		$this->create_list($type);
	}

	function add($file,$type="") {
		$this->create_list($type);
	}

}
?>
