<?php
/**
 * Created by BBJaga
 * Date: 2016-12-05
 * Time: 10:11
 */

namespace App\Models;

use Core\Model;
use Core\Session;
use Core\Logs;
use App\Models\Customer;
use DateTime;
use Core\Time;

class Hosting extends Model
{

 public static function checkHostingAvaiability($data = array()){
  $db = Model::getDB();
  $needle = $data['add_domain'];
  $check_query = ("SELECT * FROM hostings WHERE hosting_name = '$needle'");
  $success = $db->query($check_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
     return FALSE;
   }else{
    return TRUE;
   }
  }

 }//end function checkHostingAvaiability()

 public static function addNewHosting($data = array()){
  $db = Model::getDB();
  $hosting_name = $data['add_domain'];
  $hosting_register_place = $data['add_registered_where'];
  $hosting_is_our = $data['outer_inner'];
  if($hosting_is_our == 'zew'){
   $hosting_is_our = 0;
  }else{
   $hosting_is_our = 1;
  }
  $hosting_end_date = $data['add_date_end'];
  $hosting_customer_id = $data['pick-hosting-client'];
  $hosting_added_by = Session::get('user');
  $hosting_added_by_ip = Session::get('remote_ip');

  $is_avaiable = self::checkHostingAvaiability($data);
  if($is_avaiable != FALSE){

   $add_query = ("INSERT INTO hostings (
                  hosting_name,
                  hosting_register_place,
                  hosting_is_our,
                  hosting_end_date,
                  hosting_customer_id,
                  hosting_date_added,
                  hosting_added_by,
                  hosting_added_by_ip
                 ) VALUES (
                  '$hosting_name',
                  '$hosting_register_place',
                  '$hosting_is_our',
                  '$hosting_end_date',
                  '$hosting_customer_id',
                   NOW(),
                  '$hosting_added_by',
                  '$hosting_added_by_ip'
                 )");
   $success = $db->query($add_query);
   if(!$success){
    Logs::createLog('errors', Session::get('user'), 'Nieudana próba dodania hostingu '.$hosting_name.': '.$db->error);
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Logs::createLog('log', Session::get('user'), 'Dodano hosting: '.$hosting_name);
    Session::setFlash('Hosting dodany poprawnie','ok');
    $db->close();
    return TRUE;
   }
  }else{
   Session::setFlash('Taka domena już istnieje w naszej bazie. Hosting nie został dodany.','warning');
   return FALSE;
  }
 }//end function addNewHosting()

 public static function editHosting($id, $data = array()){
  $db = Model::getDB();
  $hosting_name = $data['add_domain'];
  $hosting_register_place = $data['add_registered_where'];
  $hosting_is_our = $data['outer_inner'];
  if($hosting_is_our == 'zew'){
   $hosting_is_our = 0;
  }else{
   $hosting_is_our = 1;
  }
  $hosting_end_date = $data['add_date_end'];
  $hosting_customer_id = $data['pick-hosting-client'];
  $hosting_edited_by = Session::get('user');
  $hosting_edited_by_ip = Session::get('remote_ip');

   $add_query = ("UPDATE hostings SET
                  hosting_name='$hosting_name',
                  hosting_register_place='$hosting_register_place',
                  hosting_is_our='$hosting_is_our',
                  hosting_end_date='$hosting_end_date',
                  hosting_customer_id='$hosting_customer_id',
                  hosting_date_edited=NOW(),
                  hosting_edited_by='$hosting_edited_by',
                  hosting_edited_by_ip='$hosting_edited_by_ip'
                  WHERE hosting_id='$id'");
   $success = $db->query($add_query);
   if(!$success){
    Logs::createLog('errors', Session::get('user'), 'Nieudana próba edycji hostingu '.$hosting_name.': '.$db->error);
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Logs::createLog('log', Session::get('user'), 'Edytowano hosting: '.$hosting_name);
    Session::setFlash('Hosting wyedytowany poprawnie','ok');
    $db->close();
    return TRUE;
   }
 }//end function editHosting()

 public static function getHostingsListById($direction){
  $db = Model::getDB();
  $hostings = array();
   $list_query = ("SELECT * FROM hostings ORDER BY hosting_id $direction");
  $success = $db->query($list_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
      array_push($hostings, $result);
    }
    $db->close();
    return $hostings;
   }else{
    Session::setFlash('Lista hostingów jest pusta.','error');
    $db->close();
    return FALSE;
   }
  }
 }//getHostingsListById()

 public static function getHostingsListBy($sort_of_data, $data, $sort_by, $direction){ //for instance: getHostingsListBy('hosting_name','juzek','hosting_id','ASC');
  $db = Model::getDB();
  $hostings = array();
  $list_query = ("SELECT * FROM hostings WHERE $sort_of_data=$data ORDER BY $sort_by $direction");
  $success = $db->query($list_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($hostings, $result);
    }
    $db->close();
    return $hostings;
   }else{
    Session::setFlash('Lista hostingów jest pusta.','error');
    $db->close();
    return FALSE;
   }
  }
 }//getHostingsListById()

 public static function deleteHosting($id){
  $db = Model::getDB();

  $databases = self::getDatabases($id,'hostings_database_id','ASC');
  if($databases == TRUE){
   $has_database = TRUE;
  }else{
   $has_database = FALSE;
  }

  $ftps = self::getFtps($id,'hostings_ftp_id','ASC');
  if($ftps == TRUE){
   $has_ftp = TRUE;
  }else{
   $has_ftp = FALSE;
  }

  $emails = self::getEmails($id,'hostings_email_id','ASC');
  if($emails == TRUE){
   $has_email = TRUE;
  }else{
   $has_email = FALSE;
  }

  $wwws = self::getWwws($id,'hostings_www_id','ASC');
  if($wwws == TRUE){
   $has_www = TRUE;
  }else{
   $has_www = FALSE;
  }

  if($has_database == TRUE || $has_ftp == TRUE || $has_email == TRUE || $has_www == TRUE){

   Session::setFlash('Hosting ma przypisane wartości: strony www lub inne. Aby usunąć hosting, musisz usunąć wcześniej wspomniane wartości.','error');
   Logs::createLog('errors', Session::get('user'), 'Nieudana próba usunięcia hostingu.');
   $db->close();
   return FALSE;

  }else{
   $hosting = array();
   $hst = self::getHostingBy('hosting_id',$id);
   foreach($hst as $h){
    $hosting['name'] = $h['hosting_name'];
    $hosting['registered'] = $h['hosting_register_place'];
   }
   $delete_query = ("DELETE FROM hostings WHERE hosting_id = $id");
   $success = $db->query($delete_query);
   if(!$success){
    Logs::createLog('errors', Session::get('user'), $db->error);
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Logs::createLog('errors', Session::get('user'), 'Hosting: '.$hosting['name'].' zarejestrowany: '.$hosting['registered'].' został usunięty!');
    Session::setFlash('Hosting został usunięty.','ok');
    $db->close();
    return TRUE;
   }
  }
 }//end function deleteHosting()

 public static function getHostingBy($sort_of_data, $data){//for instance: getHostingBy('id', 1) or getHostingBy('name', 'juzek')
  $db = Model::getDB();
  $hosting_data = array();
  $serch_query = ("SELECT * FROM hostings WHERE $sort_of_data = '$data' LIMIT 1");
  $success = $db->query($serch_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($hosting_data, $result);
    }
    $db->close();
    return $hosting_data;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end function getCustomerBy($sort_of_data, $data)

 public static function getFtps($hosting_id, $sort_of_data, $direction){
  $db = Model::getDB();
  $ftps = array();
  $list_query = ("SELECT * FROM hostings_ftp WHERE hostings_ftp_hosting_id = '$hosting_id' ORDER BY $sort_of_data $direction");
  $success = $db->query($list_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($ftps, $result);
    }
    $db->close();
    return $ftps;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end

 public static function getDatabases($hosting_id, $sort_of_data, $direction){
  $db = Model::getDB();
  $databases = array();
  $list_query = ("SELECT * FROM hostings_database WHERE hostings_database_hosting_id = '$hosting_id' ORDER BY $sort_of_data $direction");
  $success = $db->query($list_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($databases, $result);
    }
    $db->close();
    return $databases;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end

 public static function getEmails($hosting_id, $sort_of_data, $direction){
  $db = Model::getDB();
  $emails = array();
  $list_query = ("SELECT * FROM hostings_email WHERE hostings_email_hosting_id = '$hosting_id' ORDER BY $sort_of_data $direction");
  $success = $db->query($list_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($emails, $result);
    }
    $db->close();
    return $emails;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end

 public static function getWwws($hosting_id, $sort_of_data, $direction){
  $db = Model::getDB();
  $wwws = array();
  $list_query = ("SELECT * FROM hostings_www WHERE hostings_www_hosting_id = '$hosting_id' ORDER BY $sort_of_data $direction");
  $success = $db->query($list_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($wwws, $result);
    }
    $db->close();
    return $wwws;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end

 ////////////////////////////////////////////////////////////////////////////////// DATES FUNCTIONS /////////////////////////////////////////////////////////////

 public static function getHostingEndDate($hosting_id){
  $db = Model::getDB();
  $get_query = ("SELECT * FROM hostings WHERE hosting_id = '$hosting_id'");
  $success = $db->query($get_query);
  $date_end = '';
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
    $date_end = $result['hosting_end_date'];
   }
  }
  return $date_end;
 }//end function getHostingEndDate($hosting_id)

 ////////////////////////////////////////////////////////////////////////////////// END DATES FUNCTIONS /////////////////////////////////////////////////////////////

 public static function getHostingName($hosting_id){
  $db = Model::getDB();
  $get_query = ("SELECT * FROM hostings WHERE hosting_id = '$hosting_id'");
  $success = $db->query($get_query);
  $hosting_name = '';
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
    $hosting_name = $result['hosting_name'];
   }
  }
  return $hosting_name;
 }//end function getHostingName($hosting_id)

 public static function searchHostingBy($column, $frase){

  $db = Model::getDB();
  $hostings = array();
  $search_query = ("SELECT * FROM hostings WHERE $column LIKE '%$frase%'");
  $success = $db->query($search_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . ' function: searchHostingBy($column, $frase) [' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if ($found > 0) {
    while ($result = $success->fetch_assoc()) {
     array_push($hostings, $result);
    }
    Session::setFlash('Znaleziono: '.sizeof($hostings).' wyników','ok');
   }
  }
  $db->close();
  return $hostings;
 }//end function searchHostingBy($column, $frase)

 public static function searchHostingByClientAnd($column, $frase, $customer_id){

  $db = Model::getDB();
  $hostings = array();
  $search_query = ("SELECT * FROM hostings WHERE $column LIKE '%$frase%' AND hosting_customer_id = '$customer_id'");
  $success = $db->query($search_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . ' function: searchHostingByClientAnd($column, $frase, $customer_id) [' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if ($found > 0) {
    while ($result = $success->fetch_assoc()) {
     array_push($hostings, $result);
    }
    Session::setFlash('Znaleziono: '.sizeof($hostings).' wyników','ok');
   }
  }
  $db->close();
  return $hostings;
 }//end function searchHostingByClientAnd($column, $frase)

 public static function searchHostingByClient($customer_id){

  $db = Model::getDB();
  $hostings = array();
  $search_query = ("SELECT * FROM hostings WHERE hosting_customer_id = '$customer_id'");
  $success = $db->query($search_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . ' function: searchHostingByClientAnd($column, $frase, $customer_id) [' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if ($found > 0) {
    while ($result = $success->fetch_assoc()) {
     array_push($hostings, $result);
    }
    Session::setFlash('Znaleziono: '.sizeof($hostings).' wyników','ok');
   }
  }
  $db->close();
  return $hostings;

 }//end function searchHostingByClient($customer_id)


}//END CLASS HOSTING