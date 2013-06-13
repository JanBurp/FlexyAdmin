<?

/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/date_helper.html" target="_blank">Date_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * @link http://codeigniter.com/user_guide/helpers/date_helper.html
 */


/**
 * Geeft datum/tijd in MySQL formaat, klaar om in de database te stoppen
 *
 * @param int $unix[''] Unix timestamp, als leeg dan wordt huidig moment genomen.
 * @return string
 * @author Jan den Besten
 */
function unix_to_mysql($unix='') {
  if (empty($unix)) $unix=time();
  return date('Y-m-d H:i',$unix);
}


/**
 * Give an array from a datestring
 *
 * @param string $date (format: yyyy-mm-dd)
 * @return array ('year'=>yyyy, 'month'=>mm, 'day'=>dd)
 * @author Jan den Besten
 */
function date_to_array($date) {
  return array(
    'year'  =>substr($date,0,4),
    'month' =>substr($date,5,2),
    'day'   =>substr($date,8,2)
  );
}


/**
 * Tel een dag bij een unix-timestamp op
 *
 * @param int $unix unix timestamp
 * @param int $days[1] aantal dagen
 * @return int unix timestamp
 * @author Jan den Besten
 */
function unixdate_add_days($unix,$days=1) {
  $add_day=24 * 60 * 60;
  return $unix+($days*$add_day);
}

/**
 * Test of een dag (unix-timestamp) in een weekend valt
 *
 * @param int $unix De dag als unix-timestamp
 * @return bool TRUE als het een weekenddag is
 * @author Jan den Besten
 */
function unixdate_is_weekend($unix) {
  $weekday = date('w', $unix);
  return ($weekday == 0 || $weekday == 6);
}


/**
 * Test of een datum (unix-timestamp) een bepaalde dag is
 *
 * @param int $unix 
 * @param int $day[0] - 0=zondag etc. 
 * @return bool
 * @author Jan den Besten
 */
function unixdate_is_day($unix,$day=0) {
  $weekday = date('w', $unix);
  return ($weekday==$day);
}


/**
 * Geeft volgende werkdag (geen weekend, en geen vakantie)
 *
 * @param string $unix 
 * @param string $holidays 
 * @return void
 * @author Jan den Besten
 */
function get_next_workday($unix,$holidays) {
  while (unixdate_is_weekend($unix) or unixdate_is_holiday($unix,$holidays)) {
    $unix=unixdate_add_days($unix,1);
  }
  return $unix;
}


/**
 * Test of een dag (unix-timestamp) een vakantiedag is.
 *
 * @param int $unix De dag als unix-timestamp
 * @param array $holidays een array van alle vakantiedagen (zie get_holidays())
 * @return bool TRUE als het een vakantiedag is
 * @author Jan den Besten
 */
function unixdate_is_holiday($unix,$holidays=array()) {
  $holiday=false;
  while (!$holiday and $h=each($holidays)) {
    $h = strtotime($h['key']);
    $next = unixdate_add_days($h,1);
    $holiday=($unix>=$h and $unix<$next);
  }
  return $holiday;
}


/**
 * Geeft een array van alle (vrije) feestdagen van een land.
 * NB Op dit moment alleen nog maar van Nederland 'nl'.
 *
 * @param string $country, bijvoorbeeld 'nl'
 * @return array
 * @author Jan den Besten
 */
function get_holidays($country='') {
  $holidays=array();
	$year = date('Y');
  
  switch($country) {
    case 'nl':
    default:
      // vast
  		$holidays[date('d-m-Y',mktime (1,1,1,1,1,$year))] = 'nieuwjaarsdag';
      if ($year<=2013)
        $holidays[date('d-m-Y',mktime (0,0,0,04,30,$year))] = 'koninginnedag';
      else
        $holidays[date('d-m-Y',mktime (0,0,0,04,27,$year))] = 'koningsdag';
  		$holidays[date('d-m-Y',mktime (0,0,0,05,05,$year))] = 'bevrijdingsdag';
  		$holidays[date('d-m-Y',mktime (0,0,0,12,25,$year))] = 'eerste kerstdag';
  		$holidays[date('d-m-Y',mktime (0,0,0,12,26,$year))] = 'tweede kerstdag';
      // variabel
  		$a = $year % 19;
  		$b = intval($year/100);
  		$c = $year % 100;
  		$d = intval($b/4);
  		$e = $b % 4;
  		$g = intval((8 *  $b + 13) / 25);
  		$theta = intval((11 * ($b - $d - $g) - 4) / 30);
  		$phi = intval((7 * $a + $theta + 6) / 11);
  		$psi = (19 * $a + ($b - $d - $g) + 15 -$phi) % 29;
  		$i = intval($c / 4);
  		$k = $c % 4;
  		$lamda = ((32 + 2 * $e) + 2 * $i - $k - $psi) % 7;
  		$month = intval((90 + ($psi + $lamda)) / 25);
  		$day = (19 + ($psi + $lamda) + $month) % 32;
			$holidays[date('d-m-Y',mktime (0,0,0,$month,$day,$year))] = 'eerste paasdag';
			$holidays[date('d-m-Y',mktime (0,0,0,$month,$day+1,$year))] = 'tweede paasdag';
			$holidays[date('d-m-Y',mktime (0,0,0,$month,$day+39,$year))] = 'hemelvaart';
			$holidays[date('d-m-Y',mktime (0,0,0,$month,$day+49,$year))] = 'eerste pinksterdag';
			$holidays[date('d-m-Y',mktime (0,0,0,$month,$day+50,$year))] = 'tweede pinksterdag';
    break;
  }
  return $holidays;
}



