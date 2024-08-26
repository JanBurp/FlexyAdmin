<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup libraries
 * Content
 *
 * @author Jan den Besten
 * @copyright , 28 August, 2012
 **/

/**
 * Hiermee kun je HTML bewerken zodat ze geschikter zijn voor je site.
 * Hieronder staan alle opties met hun default waarden:
 *
 * - compress               - [TRUE] de HTML output wordt gecomprimeerd (overbodige spaties en returns worden verwijderd)
 * - safe_emails            - [TRUE] emaillinks worden vervangen door spambot veilige emaillinks
 * - auto_target_links      - [TRUE] alle link-tags naar externe adressen krijgen de attributen `target="_blank"` en `rel="external"` mee.
 * - auto_iframe_https      - [TRUE] bij een https website worden alle iframes met http veranderd in http
 * - site_links             - [FALSE] alle link-tags naar interne adressen worden aangepast met site_url(), zodat eventueel index.php ervoor wordt gezet.
 * - add_classes            - [FALSE] alle div, p, en img tags krijgen extra classes: een nr en 'odd' of 'even'
 * - remove_sizes           - [FALSE] Als TRUE dan worden width en height attributen van img tags verwijderd (zodat met css styling kan worden ingegrepen). Je kunt ook alleen de 'width' of 'height' attributen verwijderen door 'width' of 'height' (of 'style').
 * - replace_language_links - [FALSE] Links die beginnen met een taal, bijvoorbeeld _nl/contact_ worden vervangen worden door links met de juiste taal bv: _en/contact_
 * - replace_soft_hyphens   - [FALSE] Soft Hyphens karakters (standaard [-]) worden vervangen door de HTML entity: &#173;
 * - custom                 - [FALSE] array('search'=>'','replace'=>''). Voeg hier een custom search & replace toe voor de content. Als regex.
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
 * @author Jan den Besten
 */
#[AllowDynamicProperties]
class Content
{

    private $settings = array(
        'compress'          => true,
        'safe_emails'       => true,
        'auto_target_links' => true,
        'auto_iframe_https' => true,
        'site_links'        => true,
        'remove_sizes'      => false,
        'add_classes'       => false,
        'add_popups'        => false,
        // 'add_popups'        => 'popup_',
        'replace_language_links' => false,
        // 'replace_language_links' => array('search'=>'','replace'=>''),
        'replace_soft_hyphens'   => false,
        // 'replace_soft_hyphens'   => '[-]',
        'custom'             => false,
    );

    private $div_count;
    private $img_count;
    private $p_count;
    private $h_count;

    /**
     */
    public function __construct($config = array())
    {
        if ($config) $this->initialize($config);
        $this->CI = @get_instance();
    }


    /**
     * Initialiseer alle opties, zie boven voor alle opties
     *
     * @param array $config
     * @return this
     * @author Jan den Besten
     */
    public function initialize($config = array())
    {
        $this->settings = array_merge($this->settings, $config);
        return $this;
    }

    /**
     * Pas één config item aan
     *
     * @param string $config item
     * @param string $value
     * @return this
     * @author Jan den Besten
     */
    public function set($config, $value)
    {
        $this->settings[$config] = $value;
        return $this;
    }




    /**
     * Maakt automatisch het juiste target attribuut aan in een link tag <a>
     *
     * @param string $match
     * @return string
     * @author Jan den Besten
     * @internal
     */
    private function _auto_target_links($match)
    {
        $res = $match[0];
        if (isset($match[2])) {
            $res = '<a' . preg_replace("/target=\"(.*)?\"/uiUsm", "", $match[1]);
            $url = preg_replace('#^' . $this->CI->config->item('base_url') . '#', '', $match[2]);
            $target = '';
            if (isset($match[3]) and (preg_match("/target=\"([^\"]*)\"/us", $match[3], $target_match))) {
                $target = $target_match[1];
            } else {
                $target = '_self';
                if (substr($url, 0, 4) == 'http') $target = '_blank';
            }
            if (substr($url, 0, 4) == 'file' or substr($url, 0, 4) == 'mail') {
                $target = '';
            }
            $res .= 'href="' . $url . '"';
            if (isset($match[3])) $res .= preg_replace("/target=\"(.*)?\"/uiUsm", "", $match[3]);
            if (!empty($target)) $res .= ' target="' . $target . '" ';
            $res .= '>';
        }
        return $res;
    }

    /**
     * Behandelt interne links met site_url()
     *
     * @param string $match
     * @return string
     * @author Jan den Besten
     * @internal
     */
    private function _site_links($match)
    {
        $res = $match[0];
        $url = $match[2];
        $index = $this->CI->config->item('index_page');
        if ((substr($url, 0, 4) != 'http') and (substr($url, 0, 6) != 'mailto') and !has_string($index, $url)) {
            $url = site_url($url);
            if (!isset($match[3])) $match[3] = '';
            $res = '<a ' . $match[1] . ' href="' . $url . '" ' . $match[3] . '>';
        }
        return $res;
    }

    /**
     * Callback voor het vervangen van classes
     *
     * @param array $matches
     * @return string
     * @author Jan den Besten
     * @internal
     */
    private function _countCallBack($matches)
    {
        $class = "";
        // is there a class allready?
        if (preg_match("/class=\"([^<]*)\"/uiUsm", $matches[3], $cMatch))
            $class = $cMatch[1] . " ";
        if ($matches[1] == "p") {
            $class .= "p$this->p_count";
            if ($this->p_count++ % 2) $class .= " odd";
            else $class .= " even";
        } elseif ($matches[1] == "div") {
            $class .= "div$this->div_count";
            if ($this->div_count++ % 2) $class .= " odd";
            else $class .= " even";
        } elseif ($matches[1] == "img") {
            $class .= "img$this->img_count";
            if ($this->img_count++ % 2) $class .= " odd";
            else $class .= " even";
        } else {
            $h = $matches[2];
            $class .= "h" . $h . $this->h_count[$h];
            if ($this->h_count[$h]++ % 2) $class .= " odd";
            else $class .= " even";
        }
        $result = "<" . $matches[1] . " class=\"$class\"" . $matches[3] . ">";
        return $result;
    }

    /**
     * 	Callback voor popup
     *
     * @param array $matches
     * @return string
     * @author Jan den Besten
     * @internal
     */
    private function _popupCallBack($matches)
    {
        $src = $matches[2];
        $info = get_path_and_file($src);
        $popup = $info['path'] . $this->settings['pre_popup'] . $info["file"];
        if (file_exists($popup)) {
            $result = "<img" . $matches[1] . " longdesc=\"$popup\" src=\"" . $src . "\"" . $matches[3] . " />";
        } else
            $result = "<img" . $matches[1] . " src=\"" . $src . "\"" . $matches[3] . " />";
        return $result;
    }

    /**
     * Reset tellers voor classes
     *
     * @return void
     * @author Jan den Besten
     * @internal
     */
    private function reset_counters()
    {
        $this->div_count = 1;
        $this->img_count = 1;
        $this->p_count = 1;
        $this->h_count = array(1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1);
    }

    /**
     * Compresses HTML
     *
     * @param string $html
     * @return string
     * @author Jan den Besten
     */
    private function compress($html)
    {
        ini_set("pcre.recursion_limit", "16777");
        /*
    $re = '%# Collapse whitespace everywhere but in blacklisted elements.
      (?>             # Match all whitespans other than single space.
        [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
      | \s{2,}        # or two or more consecutive-any-whitespace.
      ) # Note: The remaining regex consumes no text at all...
      (?=             # Ensure we are not in a blacklist tag.
        [^<]*+        # Either zero or more non-"<" {normal*}
        (?:           # Begin {(special normal*)*} construct
          <           # or a < starting a non-blacklist tag.
          (?!/?(?:textarea|pre|script)\b)
          [^<]*+      # more non-"<" {normal*}
        )*+           # Finish "unrolling-the-loop"
        (?:           # Begin alternation group.
          <           # Either a blacklist start tag.
          (?>textarea|pre|script)\b
        | \z          # or end of file.
        )             # End alternation group.
      )  # If we made it here, we are not in a blacklist tag.
      %Six';
  */
        $re = '%(?>[^\S ]\s*| \s{2,})(?=[^<]*+(?:<(?!/?(?:textarea|pre|script)\b)[^<]*+)*+(?:<(?>textarea|pre|script)\b|\z))%Six';
        $new_html = preg_replace($re, " ", $html);
        $new_html = preg_replace('~>\s+<~', '><', $new_html);
        if ($new_html === null) $new_html = $html;
        return $new_html;
    }


    /**
     * Zelfde als render()
     *
     * @param string $txt
     * @return string
     * @author Jan den Besten
     */
    public function parse($txt)
    {
        return $this->render($txt);
    }

    /**
     * Dit voert alle acties uit met meegegeven (HTML) tekst
     *
     * @param string $txt De HTML waarop de acties moeten worden uitgevoerd
     * @param $full  default=false
     * @return string De HTML waarop de acties zijn uitgevoerd
     * @author Jan den Besten
     */
    public function render($txt, $full = false)
    {
        $this->reset_counters();

        if (!$full) {

            // rendering in content of page

            if ($this->settings['site_links'] and $this->CI->config->item('index_page') != '') {
                $txt = preg_replace_callback("/<a(.*)?href=\"(.*)?\"(.*)?>/uiUsm", array($this, "_site_links"), $txt);
            }

            if ($this->settings['add_classes']) {
                $txt = preg_replace_callback("/<(div|img|p|h(\d))([^<]*)>/", array($this, "_countCallBack"), $txt);
            }

            if ($this->settings['add_popups']) {
                $txt = preg_replace_callback("/<img([^<]*)src=['|\"](.*?)['|\"]([^>]*)>/", array($this, "_popupCallBack"), $txt);
            }

            if ($this->settings['replace_soft_hyphens']) {
                $txt = str_replace($this->settings['replace_soft_hyphens'], '&#173;', $txt);
            }

            if ($this->settings['remove_sizes']) {
                $remove = $this->settings['remove_sizes'];
                if ($remove === true or $remove === 'width') {
                    $txt = preg_replace("/<img(.*)(\swidth=\"\d*\")/uiUsm", "<img$1", $txt);
                    $txt = preg_replace("/(<img[^>]*style=['|\"].*)(width:.*;)/uiUm", "$1", $txt);
                }
                if ($remove === true or $remove === 'height') {
                    $txt = preg_replace("/<img(.*)(\sheight=\"\d*\")/uiUsm", "<img$1", $txt);
                    $txt = preg_replace("/(<img[^>]*style=['|\"].*)(height:.*;)/uiUm", "$1", $txt);
                }
                if ($remove === true or $remove === 'style') {
                    $txt = preg_replace("/(<img[^>]*)(style=['|\"]\s['|\"])/uiUm", "$1", $txt);
                }
            }

            if ($this->settings['custom']) {
                $search = $this->settings['custom']['search'];
                $replace = $this->settings['custom']['replace'];
                $txt = str_replace($search, $replace, $txt);
            }
        } else {

            // render on full HTML page

            if ($this->settings['replace_language_links'] and isset($this->settings['replace_language_links']['search']) and isset($this->settings['replace_language_links']['replace'])) {
                $txt = preg_replace('/<a[\s]*href=\"' . $this->settings['replace_language_links']['search'] . '\/(.*)\">(.*)<\/a>/', '<a href="' . $this->settings['replace_language_links']['replace'] . '/$1">$2</a>', $txt);
            }

            if ($this->settings['auto_target_links']) {
                $txt = preg_replace_callback("/<a(.*)?href=\"(.*)?\"(.*)?>/uiUsm", array($this, "_auto_target_links"), $txt);
            }

            if ($this->settings['auto_iframe_https'] and PROTOCOL === 'https') {
                $txt = preg_replace('/<iframe\ssrc=\"http:/uiUm', '<iframe src="https:', $txt);
            }


            if ($this->settings['safe_emails']) {
                if (preg_match_all("/<a([^<]*)href=\"mailto:(.*?)\"([^>]*)>(.*?)<\/a>/", $txt, $matches)) {     //<a[\s]*href="(.*)">(.*)</a>
                    $search = array();
                    $replace = array();
                    foreach ($matches[2] as $key => $adres) {
                        $show = str_replace('"', "'", $matches[4][$key]);
                        $show = html_entity_decode($show);
                        $search[] = $matches[0][$key];
                        // classes, id's etc
                        $extra = '';
                        if (isset($matches[1][$key])) $extra .= $matches[1][$key];
                        if (isset($matches[3][$key])) $extra .= $matches[3][$key];
                        $extra = trim($extra);
                        $attr = explode_attributes($extra);
                        $replace[] = safe_mailto($adres, $show, $attr);
                    }
                    $txt = str_replace($search, $replace, $txt);
                }
            }

            if ($this->settings['compress']) {
                $txt = $this->compress($txt);
            }
        }

        return $txt;
    }
}
