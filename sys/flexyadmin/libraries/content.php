<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Content
 *
 * @author Jan den Besten
 * @version $Id$
 * @copyright , 28 August, 2012
 * @package default
 **/

/**
 * Hiermee kun je HTML bewerken zodat ze geschikter zijn voor je site.
 * Hieronder staan alle opties met hun default waarden:
 * 
 * - safe_emails            - [TRUE] emaillinks worden vervangen door spambot veilige emaillinks
 * - auto_target_links      - [TRUE] alle link-tags naar externe adressen krijgen de attributen `target="_blank"` en `rel="external"` mee.
 * - site_links             - [TRUE] alle link-tags naar interne adressen worden aangepast met site_url(), zodat eventueel index.php ervoor wordt gezet.
 * - add_classes            - [TRUE] alle div, p, en img tags krijgen extra classes: een nr en 'odd' of 'even'
 * - remove_sizes           - [FALSE] width en height attributen van img tags worden verwijderd (zodat met css styling kan worden ingegrepen)
 * - replace_language_links - [FALSE] Links die beginnen met een taal, bijvoorbeeld _nl/contact_ worden vervangen worden door links met de juiste taal bv: _en/contact_
 * - replace_soft_hyphens   - [FALSE] Soft Hyphens karakters (standaard [-]) worden vervangen door de HTML entity: &#173;
 * 
 * Deze class is standaard geladen in de frontend en wordt door de controller gebruikt bij het renderen van de tekst van een pagina.
 * Zo roep je deze class aan:
 * 
 *      $text = $this->content->render($text);
 * 
 * En zo kun je de instellingen aanpassen:
 * 
 *      $this->content->initialize( array('remove_sizes'=>TRUE, 'replace_soft_hyphens' => TRUE ) );
 *
 * @package default
 * @author Jan den Besten
 */
class Content {

	private $safeEmail=TRUE;
  private $auto_target_links=TRUE;
  private $site_links=TRUE;
	private $addClasses=TRUE;
	private $addPopups=FALSE;
  private $removeSizes=FALSE;
	private $prePopup='popup_';
	private $replaceLanguageLinks=FALSE;
  private $replaceSoftHyphens=FALSE;
  private $replaceHyphen='[-]';
  private $config_array=array('safe_emails'=>'safeEmail','auto_target_links'=>'auto_target_links','site_links'=>'site_links','add_classes'=>'addClasses','add_popups'=>'addPopups','replace_language_links'=>'replaceLanguageLinks','replace_soft_hyphens'=>'replaceSoftHyphens','remove_sizes'=>'removeSizes');
	
	private $div_count;
	private $img_count;
	private $p_count;
	private $h_count;

  /**
   * @ignore
   */
	public function __construct($config=array()) {
    $this->initialize($config);
  }
  

  /**
   * Initialiseer alle opties, zie boven voor alle opties
   *
   * @param array $config 
   * @return this
   * @author Jan den Besten
   */
  public function initialize($config=array()) {
    foreach ($config as $key => $value) {
      if (isset($this->config_array[$key])) {
        $thisVar=$this->config_array[$key];
        if (isset($this->$thisVar)) $this->$thisVar=$value;
      }
    }
    return $this;
  }

  /**
   * Zet het vervangen van email adressen in spambot veilige emailadressen aan of uit
   *
   * @param bool $safe[TRUE] TRUE is aan, FALSE is UIT
   * @return void
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
	public function option_safe_email($safe=TRUE) {
		$this->safeEmail=$safe;
	}
	
  /**
   * Zet het toevoegen van extra classes aan div,p en img tags aan/uit
   *
   * @param bool $classes[TRUE] TRUE is aan, FALSE is UIT
   * @return void
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
	public function add_classes($classes=TRUE) {
		$this->addClasses=$classes;
	}
	
  /**
   * Hiermee kunnen links worden vervangen
   * 
   * Voorbeeld om alle links (nederlandstalige) te verwijzen naar de engelstalige pagina's: 
   * 
   *     $this->content->replace_language_links( array('search'=>'nl','replace'=>'en') );
   *
   * @param array $replace[TRUE]
   * @return void
   * @author Jan den Besten
   */
	public function replace_language_links($replace=TRUE) {
		$this->replaceLanguageLinks=$replace;
	}
	
  
  
  
  /**
   * Voegt een popup link aan img tags toe
   *
   * @param string $pre['popup_'] 
   * @param bool $popups[TRUE]
   * @return void
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
	public function add_popups($pre="popup_",$popups=TRUE) {
		$this->prePopup=$pre;
		$this->addPopups=$popups;
	}
  
  /**
   * Zet het vervangen van hyphen karakters aan/ui
   *
   * @param bool $hyphens[TRUE] TRUE is aan, FALSE is UIT
   * @return void
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
  function replace_soft_hyphens($hyphens=TRUE) {
    $this->replaceSoftHyphens=$hyphens;
  }


  /**
   * Behandelt interne links met site_url()
   *
   * @param string $match 
   * @return string
   * @author Jan den Besten
   * @internal
   * @ignore
   */
  private function _site_links($match) {
    $res=$match[0];
    $url=$match[2];
    if (substr($url,1,4)!='http') {
      $url=site_url($url);
      if (!isset($match[3])) $match[3]='';
      $res='<a '.$match[1].' href="'.$url.'" '.$match[3].'>';
    }
    return $res;
  }


  /**
   * Maakt automatisch het juiste target attribuut aan in een link tag <a>
   *
   * @param string $match 
   * @return string
   * @author Jan den Besten
   * @internal
   * @ignore
   */
  private function _auto_target_links($match) {
    $res='<a'.preg_replace("/target=\"(.*)?\"/uiUsm", "", $match[1]);
    $url=$match[2];
    $target='_self';
    if (substr($url,0,4)=='http') $target='_blank';
    if (substr($url,0,4)=='file') $target='';
    if (substr($url,0,4)=='mail') $target='';
    $res.='href="'.$url.'"';
    if (isset($match[3])) $res.=preg_replace("/target=\"(.*)?\"/uiUsm", "", $match[3]);
    if (!empty($target)) $res.=' target="'.$target.'" ';
    $res.=' rel="external">';
    return $res;
  }


  /**
   * Callback voor het vervangen van classes
   *
   * @param array $matches 
   * @return string
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _countCallBack($matches) {
		$class="";
		// is there a class allready?
		if (preg_match("/class=\"([^<]*)\"/",$matches[3],$cMatch))
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

  /**
   * 	Callback voor popup
   *
   * @param array $matches 
   * @return string
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _popupCallBack($matches) {
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

  /**
   * Reset tellers voor classes
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function reset_counters() {
		$this->div_count=1;
		$this->img_count=1;
		$this->p_count=1;
		$this->h_count=array(1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1);
	}

  /**
   * Zelfde als render()
   *
   * @param string $txt 
   * @return string
   * @author Jan den Besten
   */
  public function parse($txt) {
    return $this->render($txt);
  }

  /**
   * Dit voert alle acties uit met meegegeven (HTML) tekst
   *
   * @param string $txt De HTML waarop de acties moeten worden uitgevoerd
   * @return string De HTML waarop de acties zijn uitgevoerd
   * @author Jan den Besten
   */
	public function render($txt) {
		$this->reset_counters();
		
    if ($this->site_links) {
      $txt=preg_replace_callback("/<a(.*)?href=\"(.*)?\"(.*)?>/uiUsm",array($this,"_site_links"),$txt);
    }

    if ($this->auto_target_links) {
      $txt=preg_replace_callback("/<a(.*)?href=\"(.*)?\"(.*)?>/uiUsm",array($this,"_auto_target_links"),$txt);
    }
    
		if ($this->addClasses) {
			$txt=preg_replace_callback("/<(div|img|p|h(\d))([^<]*)>/",array($this,"_countCallBack"),$txt);
		}

		if ($this->replaceLanguageLinks) {
			$txt=preg_replace('/<a[\s]*href=\"'.$this->replaceLanguageLinks['search'].'\/(.*)\">(.*)<\/a>/','<a href="'.$this->replaceLanguageLinks['replace'].'/$1">$2</a>',$txt);
		}

		
		if ($this->addPopups) {
			$txt=preg_replace_callback("/<img([^<]*)src=['|\"](.*?)['|\"]([^>]*)>/",array($this,"_popupCallBack"),$txt);
		}
		
		if ($this->safeEmail) {
			if (preg_match_all("/<a([^<]*)href=\"mailto:(.*?)\"([^>]*)>(.*?)<\/a>/",$txt,$matches)) { 	//<a[\s]*href="(.*)">(.*)</a>
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
            if (!empty($value)) {
              $value=explode('=',$value);
              if (is_array($value) and isset($value[1])) {
                $attr[$value[0]]=trim($value[1],'"');
              }
            }
          }
          $replace[]=safe_mailto($adres,$show,$attr);
				}
				$txt=str_replace($search,$replace,$txt);
			}
		}
    
    if ($this->replaceSoftHyphens) {
      $txt=str_replace($this->replaceHyphen,'&#173;',$txt);
    }
    
    if ($this->removeSizes) {
      $txt = preg_replace("/<img(.*)(\swidth=\"\d*\")/uiUsm", "<img$1", $txt);
      $txt = preg_replace("/<img(.*)(\sheight=\"\d*\")/uiUsm", "<img$1", $txt);
    }
    
		return $txt;
	}
	
	
}

?>
