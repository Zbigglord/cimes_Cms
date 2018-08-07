<?php
/**
 * Created by BBJaga
 * Date: 2016-12-07
 * Time: 12:12
 */

namespace App\Models;
use Core\Model;
use Core\Session;
use Core\Logs;


class Email extends Model
{

 public static function checkEmailAvaiability($data = array()){
  $db = Model::getDB();
  $needle1 = $data['add_email_address'];
  $check_query = ("SELECT * FROM hostings_email WHERE hostings_email_address = '$needle1'");
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
 }//end function checkEmailAvaiability()

 public static function addNewEmail($data = array()){
  $db = Model::getDB();
  $hostings_email_address = $data['add_email_address'];
  $hostings_email_login = $data['add_email_login'];
  $hostings_email_pass = $data['add_email_pass'];
  $hostings_email_hosting_id = $data['pick-page-hosting'];

  $is_avaiable = self::checkEmailAvaiability($data);
  if($is_avaiable != FALSE){

   $add_query = ("INSERT INTO hostings_email (
                  hostings_email_address,
                  hostings_email_login,
                  hostings_email_pass,
                  hostings_email_hosting_id,
                  hostings_email_date
                 ) VALUES (
                  '$hostings_email_address',
                  '$hostings_email_login',
                  '$hostings_email_pass',
                  '$hostings_email_hosting_id',
                   NOW()
                 )");
   $success = $db->query($add_query);
   if(!$success){
    Logs::createLog('errors', Session::get('user'), 'Nieudana próba dodania adresu EMAIL: '.$hostings_email_address.': '.$db->error);
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Logs::createLog('log', Session::get('user'), 'Dodano adres EMAIL: '.$hostings_email_address);
    Session::setFlash('Dane Email dodano poprawnie','ok');
    $db->close();
    return TRUE;
   }
  }else{
   Session::setFlash('Konto EMAIL o takim adresie już istnieje w bazie danych. Konto nie zostało dodane.','warning');
   $db->close();
   return FALSE;
  }
 }//end function addNewEmail()

 public static function editEmail($id,$data = array()){
  $db = Model::getDB();
  $hostings_email_address = $data['add_email_address'];
  $hostings_email_login = $data['add_email_login'];
  $hostings_email_pass = $data['add_email_pass'];
  $hostings_email_hosting_id = $data['pick-page-hosting'];
  $hostings_email_edited_by = Session::get('user');
  $hostings_email_edited_by_ip = Session::get('remote_ip');

  $add_query = ("UPDATE hostings_email SET
                  hostings_email_address='$hostings_email_address',
                  hostings_email_login='$hostings_email_login',
                  hostings_email_pass='$hostings_email_pass',
                  hostings_email_hosting_id='$hostings_email_hosting_id',
                  hostings_email_edited=NOW(),
                  hostings_email_edited_by='$hostings_email_edited_by',
                  hostings_email_edited_by_ip='$hostings_email_edited_by_ip'
                  WHERE hostings_email_id='$id'");
  $success = $db->query($add_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), 'Nieudana próba dedycji adresu EMAIL: '.$hostings_email_address.': '.$db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   Logs::createLog('log', Session::get('user'), 'Edytowano adres EMAIL: '.$hostings_email_address);
   Session::setFlash('Konto email wyedytowano poprawnie','ok');
   $db->close();
   return TRUE;
  }
 }//end function editEmail()

 public static function getEmailBy($sort_of_data, $data){//for instance: getEmailBy('id', 1) or getEmailBy('name', 'juzek')
  $db = Model::getDB();
  $email_data = array();
  $serch_query = ("SELECT * FROM hostings_email WHERE $sort_of_data = '$data' LIMIT 1");
  $success = $db->query($serch_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($email_data, $result);
    }
    $db->close();
    return $email_data;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end function getEmailBy($sort_of_data, $data,)

 public static function getEmailsList($sort_of_data, $direction){//for instance: getDatabaseList('ASC') or getDatabaseList('DESC')
  $db = Model::getDB();
  $emails_data = array();
  $serch_query = ("SELECT * FROM hostings_email ORDER BY $sort_of_data $direction");
  $success = $db->query($serch_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($emails_data, $result);
    }
    $db->close();
    return $emails_data;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end function getEmailsList($sort_of_data, $data, $direction)

 public static function getEmailsListBy($sort_of_data, $data, $direction){//for instance: getDatabaseList('name','juzek','ASC') - may be for example by customer or by hosting
  $db = Model::getDB();
  $emails_data = array();
  $serch_query = ("SELECT * FROM hostings_email WHERE $sort_of_data = '$data' ORDER BY $sort_of_data $direction");
  $success = $db->query($serch_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($emails_data, $result);
    }
    $db->close();
    return $emails_data;
   }else{
    $db->close();
    return FALSE;
   }
  }
 }//end function getEmailsListBy($sort_of_data, $data, $direction)

 public static function deleteEmailsById($data = array()){
  $db = Model::getDB();
  foreach($data as $key => $value){
   if($value == 'on'){
    $delete_query = ("DELETE FROM hostings_email WHERE hostings_email_id='$key'");
    $success = $db->query($delete_query);
    if(!$success){
     Logs::createLog('errors', Session::get('user'), $db->error);
     die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
    }else{
     Session::setFlash('Usunięcie kont(a) email wykonano poprawnie','ok');

    }
   }
  }
  $db->close();
  return TRUE;
 }//end function deleteEmailsById()

}//END CLASS EMAIL