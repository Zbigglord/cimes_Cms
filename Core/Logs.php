<?php
/**
 * Created by BBJaga
 * Date: 2016-12-06
 * Time: 09:26
 */

namespace Core;
use DateTime;

class Logs extends Model
{

 public static function createLog($sort, $who, $message){
  $file_path = '../logs/'.$sort.'.log';
  $file = fopen($file_path,'a');
  $datetime = new DateTime();
  $finput = $datetime->format('Y-m-d H:i:s')." - user: ".$who." - ".$message."\n";
  fwrite($file,$finput);
  fclose($file);
 }

}