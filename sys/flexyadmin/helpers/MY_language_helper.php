<?php /**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/language_helper.html" target="_blank">Language_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/language_helper.html
 */



 /**
  * Maakt van velden in een array die een language suffix hebben, neutrale velden (verwijderd de suffix)
  * 
  * @param array $item
  * @param array $fields[array('str_title','txt_text')]
  * @param string $lang[''] Als geen taal wordt meegegeven, dan wordt de standaard taal genomen
  * @return array
  * @author Jan den Besten
  */
function set_language_neutral_fields($item,$fields=array('str_title','txt_text'),$lang='') {
	// set lang
	if (empty($lang)) {
		$CI=&get_instance();
		if (isset($CI->site['language']))
			$lang=$CI->site['language'];
		else
			$lang=$CI->config->item('language');
	}
	// set fields
	if (!is_array($fields)) $fields=array($fields);
	$orig_fields=$fields;
	foreach ($orig_fields as $key => $field) {
		$orig_fields[$key]=$field.'_'.$lang;
	}
	
	// set language neutral fields (recursivly)
	if (is_array($item)) {
		foreach ($item as $field => $value) {
			if (is_array($value)) {
				$item[$field]=set_language_neutral_fields($value,$fields,$lang);
			}
			elseif (in_array($field,$orig_fields)) {
				$item[str_replace('_'.$lang,'',$field)]=$value;
			}
		}
	}
	
	return $item;
}

/**
 * Geeft een taalwoord terug met %s vervangen door de meegegeven argumenten
 *
 * @param string argument(1)
 * @param string argument(n), etc
 * @return void
 * @author Jan den Besten
 */
function langp() {
	$args=func_get_args();
	$line=array_shift($args);
  $line=lang($line);
  $numArgs=count($args);
  $out='';
  switch ($numArgs) {
    case 1:
      $out=sprintf($line,$args[0]);
      break;
    case 2:
      $out=sprintf($line,$args[0],$args[1]);
      break;
    case 3:
      $out=sprintf($line,$args[0],$args[1],$args[2]);
      break;
    case 4:
      $out=sprintf($line,$args[0],$args[1],$args[2],$args[3]);
      break;
    default:
      $out=$line;
  }
	return $out;
}

/**
 * Geeft een array met namen van alle landen van de wereld (op dit moment alleen nog in 'nl')
 *
 * @param string $language 
 * @return array
 * @author Jan den Besten
 */
function countries($language='nl') {
	$countries=array('nl'=>array(
											"Afghanistan",
											"Åland",
											"Albanië",
											"Algerije",
											"Amerikaanse Maagdeneilanden",
											"Amerikaans",
											"Andorra",
											"Angola",
											"Anguilla",
											"Antarctica",
											"Antigua en Barbuda",
											"Argentinië",
											"Armenië",
											"Aruba",
											"Australië",
											"Azerbeidzjan",
											"Bahama's",
											"Bahrein",
											"Bangladesh",
											"Barbados",
											"België",
											"Belize",
											"Benin",
											"Bermuda",
											"Bhutan",
											"Bolivia",
											"Bosnië en Herzegovina",
											"Botswana",
											"Noorwegen",
											"Brazilië",
											"Brits Territorium in de Indische Oceaan",
											"Britse Maagdeneilanden",
											"Brunei",
											"Bulgarije",
											"Burkina Faso",
											"Burundi",
											"Cambodja",
											"Canada",
											"Centraal-Afrikaanse Republiek",
											"Chili",
											"China",
											"Christmaseiland",
											"Cocoseilanden",
											"Colombia",
											"de Comoren",
											"Congo-Brazzaville",
											"Congo-Kinshasa",
											"Cookeilanden",
											"Costa Rica",
											"Cuba",
											"Cyprus",
											"Denemarken",
											"Djibouti",
											"Dominica",
											"Dominicaanse Republiek",
											"Duitsland",
											"Ecuador",
											"Egypte",
											"El Salvador",
											"Equatoriaal-Guinea",
											"Eritrea",
											"Estland",
											"Ethiopië",
											"de Faeröer",
											"Falklandeilanden",
											"Fiji",
											"Filipijnen",
											"Finland",
											"Frankrijk",
											"Franse Zuidelijke en Antarctische Gebieden",
											"Frans-Guyana",
											"Frans-Polynesië",
											"Gabon",
											"Gambia",
											"Georgië",
											"Ghana",
											"Gibraltar",
											"Grenada",
											"Griekenland",
											"Groenland",
											"Frankrijk",
											"Guam",
											"Guatemala",
											"Guernsey",
											"Guinee",
											"Guinee-Bissau",
											"Guyana",
											"Haïti",
											"Australië Heard en McDonaldeilanden",
											"Honduras",
											"Hongarije",
											"Hongkong",
											"Ierland",
											"IJsland",
											"India",
											"Indonesië",
											"Irak",
											"Iran",
											"Isle of Man",
											"Israël",
											"Italië",
											"Ivoorkust",
											"Jamaica",
											"Japan",
											"Jemen",
											"Jersey",
											"Jordanië",
											"Kaaimaneilanden",
											"Kaapverdië",
											"Kameroen",
											"Kazachstan",
											"Kenia",
											"Kirgizië",
											"Kiribati",
											"de Verenigde Staten Kleine Pacifische eilanden",
											"Koeweit",
											"Kroatië",
											"Laos",
											"Lesotho",
											"Letland",
											"Libanon",
											"Liberia",
											"Libië",
											"Liechtenstein",
											"Litouwen",
											"Luxemburg",
											"Macau",
											"Macedonië",
											"Madagaskar",
											"Malawi",
											"Maldiven",
											"Maleisië",
											"Mali",
											"Malta",
											"Marokko",
											"Marshalleilanden",
											"Frankrijk",
											"Mauritanië",
											"Mauritius",
											"Mayotte",
											"Mexico",
											"Micronesia",
											"Moldavië",
											"Monaco",
											"Mongolië",
											"Montenegro",
											"Montserrat",
											"Mozambique",
											"Myanmar",
											"Namibië",
											"Nauru",
											"Nederland",
											"Nederlandse Antillen",
											"Nepal",
											"Nicaragua",
											"Nieuw-Caledonië",
											"Nieuw",
											"Niger",
											"Nigeria",
											"Niue",
											"Noordelijke Marianen",
											"Noord-Korea",
											"Noorwegen",
											"Norfolk",
											"Oekraïne",
											"Oezbekistan",
											"Oman",
											"Oostenrijk",
											"Oost-Timor",
											"Pakistan",
											"Palau",
											"Palestina",
											"Panama",
											"Papoea-Nieuw-Guinea",
											"Paraguay",
											"Peru",
											"Pitcairneilanden",
											"Polen",
											"Portugal",
											"Puerto Rico",
											"Qatar",
											"Réunion",
											"Roemenië",
											"Rusland",
											"Rwanda",
											"Saint Kitts en Nevis",
											"Saint Lucia",
											"Saint Vincent en de Grenadines",
											"Saint-Barthélemy",
											"Saint-Pierre en Miquelon",
											"Salomonseilanden",
											"Samoa",
											"San Marino",
											"Sao Tomé en Principe",
											"Saoedi-Arabië",
											"Senegal",
											"Servië",
											"Seychellen",
											"Sierra Leone",
											"Singapore",
											"Sint-Helena, Ascension en Tristan da Cunha",
											"Sint Maarten",
											"Slovenië",
											"Slowakije",
											"Soedan",
											"Somalië",
											"Spanje",
											"Spitsbergen en Jan Mayen",
											"Sri Lanka",
											"Suriname",
											"Swaziland",
											"Syrië",
											"Tadzjikistan",
											"Taiwan",
											"Tanzania",
											"Thailand",
											"Togo",
											"Tokelau-eilanden",
											"Tonga",
											"Trinidad en Tobago",
											"Tsjaad",
											"Tsjechië",
											"Tunesië",
											"Turkije",
											"Turkmenistan",
											"Turks- en Caicoseilanden",
											"Tuvalu",
											"Uganda",
											"Uruguay",
											"Vanuatu",
											"Vaticaanstad",
											"Venezuela",
											"Verenigd Koninkrijk",
											"Verenigde Arabische Emiraten",
											"Verenigde Staten",
											"Vietnam",
											"Wallis en Futuna",
											"Westelijke Sahara",
											"Wit-Rusland",
											"Zambia",
											"Zimbabwe",
											"Zuid-Afrika",
											"Zuid-Georgië en de Zuidelijke Sandwicheilanden",
											"Zuid-Korea",
											"Zweden",
											"Zwitserland"));
	if (isset($countries[$language])) return $countries[$language];
	return $countries['nl'];
}

?>
