<?php
/**
 * Created by BBJaga
 * Date: 2016-12-15
 * Time: 08:31
 */

namespace Core;


/**
 * Class Time
 * @package Core
 */
class Time extends Model
{

 /**
  * @param $date1
  * @param $date2
  * @param $format
  * @return string
  */
 public static function getDifference($date1, $date2, $format){//get difference between dates in $format (days months years etc)
 //accepts format: %y-Year, %m-Month, %d-Day, %h-Hours, %i-Minute, %s-Seconds. If added: %r should return - if its negative (its still date so aritmetics not apply)
  $date1 = date_create($date1);
  $date2 = date_create($date2);
  $interval = date_diff($date1, $date2, FALSE);

  return $interval->format($format);
 }//end function compareDate($date1, $date2, $format)

 /**
  * @param $date1
  * @param $date2
  * @return bool
  */
 public static function getSmaller($date1, $date2){//checks if date1 is smaler or biger than date2 (if smaler returns true)

  $date1 = date_create($date1);
  $date2 = date_create($date2);

  if ($date1 < $date2) {
   return TRUE;
  } else {
   return FALSE;
  }

 }//end function getSmaller($date1, $date2)

}//END TIME CLASS