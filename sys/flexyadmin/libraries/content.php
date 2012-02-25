<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin V1
 *
 * frontend_menu.php Created on 9-dec-2008
 *
 * @author Jan den Besten
 */


/**
 * Content Class
 *
 */

class Content {

	var $safeEmail;
	var $addClasses;
	var $addPopups;
	var $prePopup;
	var $replaceLanguageLinks;
	
	var $div_count;
	var $img_count;
	var $p_count;
	var $h_count;

	function __construct() {
		$this->option_safe_email();
		$this->add_classes();
		$this->add_popups("popup_",FALSE);
	}

	function option_safe_email($safe=TRUE) {
		$this->safeEmail=$safe;
	}
	
	function add_classes($classes=TRUE) {
		$this->addClasses=$classes;
	}
	
	// example: $this->content->replace_language_links( array('search'=>'nl','replace'=>'en') );
	function replace_language_links($replace=FALSE) {
		$this->replaceLanguageLinks=$replace;
	}
	
	function add_popups($pre="popup_",$popups=TRUE) {
		$this->prePopup=$pre;
		$this->addPopups=$popups;
	}

	// callback for class replacing
	function _countCallBack($matches) {
		$class="";
		// is there a class allready?
		if (preg_match("/class=\"(.*?)\"/",$matches[3],$cMatch))
			$class=$cMatch[1]." ";
		if ($matches[1]=="p") {
			$class.="p$this->p_count";
			if ($this->p_count++%2) $class.=" odd"; else $class.=" even";
		}
		elseif ($matches[1]=="div") {
			$class.="div$this->div_count";
			if ($this->div_count++%2) $class.=" odd"; else $class.=" even";
		}
		elseif ($matches[1]=="img") {
			$class.="img$this->img_count";
			if ($this->img_count++%2) $class.=" odd"; else $class.=" even";
		}
		else {
			$h=$matches[2];
			$class.="h".$h.$this->h_count[$h];
			if ($this->h_count[$h]++%2) $class.=" odd"; else $class.=" even";
		}
		$result="<".$matches[1]." class=\"$class\"".$matches[3].">";
		return $result;
	}

	// callback for popup adding replacing
	function _popupCallBack($matches) {
		$src=$matches[2];
		$info=get_path_and_file($src);
		$popup=$info['path']."/popup_".$info["file"];
		if (file_exists($popup)) {
			$result="<img".$matches[1]." longdesc=\"$popup\" src=\"".$src."\"".$matches[3]." />";
		}
		else
			$result="<img".$matches[1]." src=\"".$src."\"".$matches[3]." />";
		return $result;
	}

	function reset_counters() {
		$this->div_count=1;
		$this->img_count=1;
		$this->p_count=1;
		$this->h_count=array(1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1);
	}

	function render($txt) {
		
		$this->reset_counters();
		
		if ($this->addClasses) {
			$txt=preg_replace_callback("/<(div|img|p|h(\d))(.*?)>/",array($this,"_countCallBack"),$txt);
		}

		if ($this->replaceLanguageLinks) {
			$txt=preg_replace('/<a[\s]*href=\"'.$this->replaceLanguageLinks['search'].'\/(.*)\">(.*)<\/a>/','<a href="'.$this->replaceLanguageLinks['replace'].'/$1">$2</a>',$txt);
		}
		
		if ($this->addPopups) {
			$txt=preg_replace_callback("/<img(.*?)src=['|\"](.*?)['|\"](.*?)>/",array($this,"_popupCallBack"),$txt);
		}
		
		if ($this->safeEmail) {
			if (preg_match_all("/<a(.*?)href=\"mailto:(.*?)\"(.*?)>(.*?)<\/a>/",$txt,$matches)) { 	//<a[\s]*href="(.*)">(.*)</a>
				$search=array();
				$replace=array();
				foreach ($matches[2] as $key=>$adres) {
					$show=str_replace('"',"'",$matches[4][$key]);
          $search[]=$matches[0][$key];
					// classes, id's etc
					$extra='';
					if (isset($matches[1][$key])) $extra.=$matches[1][$key];
					if (isset($matches[3][$key])) $extra.=$matches[3][$key];
          $extra=trim($extra);
          $extra=explode(' ',$extra);
          $attr=array();
          foreach ($extra as $value) {
            $value=explode('=',$value);
            if (is_array($value)) {
              $attr[$value[0]]=trim($value[1],'"');
            }
          }
          $replace[]=safe_mailto($adres,$show,$attr);
				}
				$txt=str_replace($search,$replace,$txt);
			}
		}
		return $txt;
	}
	
	
}

?>
