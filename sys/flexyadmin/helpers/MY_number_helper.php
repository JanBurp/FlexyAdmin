<?php 

/** \ingroup helpers
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/number_helper.html" target="_blank">Number_helper van CodeIgniter</a>.
 * 
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 * @file
 */


/**
 * Geeft een waarde terug die past binnen de min en max waarden. Als de waarde kleiner is dan het minimun dan wordt het maximun teruggegeven, en andersom.
 * Hiermee kun je eenvoudig een teller maken die rond gaat.
 *
 * @param int $value 
 * @param int $min
 * @param int $max 
 * @return int
 * @author Jan den Besten
 */
function round_counter($value,$min,$max) {
  if ($value<$min) {
    $value=$max;
  }
  if ($value>$max) {
    $value=$min;
  }
  return $value;
}

