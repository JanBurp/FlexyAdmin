<?

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


function langp() {
	$args=func_get_args();
	// trace_($args);
	$line=lang($args[0]);
	if (func_num_args()>1) {
		array_shift($args);
		if (count($args)<=1) {
			return str_replace("%s",$args[0],$line);
		}
		else {
			$nr=0;
			foreach ($args as $value) {
				$line=str_replace("%".$nr,$value,$line);
			}
		}
	}
	return $line;
}

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
