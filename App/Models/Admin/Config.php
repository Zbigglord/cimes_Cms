<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-13
 * Time: 19:25
 */

namespace App\Models\Admin;


use Core\Model;
use Core\Session;

class Config extends Model{

 public static function getConfigParams(){//AMConf-0001
  $db = Model::getDB();
  $site_params = array();
  $get_query = ("SELECT * FROM site_config");
  $success = $db->query($get_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
    foreach($result as $key => $value){
     $site_params[$key] = $value;
    }
   }
   return $site_params;
  }
 }//END function getConfigParam()

 public static function saveSiteConfig($data){//AMConf-0002

$db = Model::getDB();
$site_logo = $data['logo'];
$site_email = $data['site_email'];
$site_web_address = $data['site_web_address'];
$site_phone = $data['site_phone'];
  $save_query = ("UPDATE site_config SET site_email='$site_email', site_web_address = '$site_web_address', site_logo = '$site_logo', site_phone = '$site_phone' WHERE id = 1");
$success = $db->query($save_query);
if (!$success) {
die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
}else{
 $db->close();
 Session::setFlash(ATXT_DATA_SAVED, 'ok');
 return TRUE;
}
}//END function saveSiteConfig($data)

 public static function editSiteConfig($data){//AMConf-0003

  $db = Model::getDB();
  $site_logo = $data['logo'];
  $site_email = $data['site_email_edit'];
  $site_web_address = $data['site_web_address_edit'];
  $site_phone = $data['site_phone_edit'];
  $save_query = ("UPDATE site_config SET site_email='$site_email', site_web_address = '$site_web_address', site_logo = '$site_logo', site_phone = '$site_phone' WHERE id = 1");
  $success = $db->query($save_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $db->close();
   Session::setFlash(ATXT_DATA_SAVED, 'ok');
   return TRUE;
  }
 }//END function editSiteConfig($data)

 public static function saveOwnerConfig($data){//AMConf-0004

  $db = Model::getDB();
  $owner_name = $data['owner_name'];
  $owner_surname = $data['owner_surname'];
  $owner_firm = $data['owner_firm'];
  $owner_title = $data['owner_title'];
  $owner_street = $data['owner_street'];
  $owner_postcode = $data['owner_postcode'];
  $owner_city = $data['owner_city'];
  $owner_country = $data['owner_country'];
  $owner_nip = $data['owner_nip'];
  $owner_regon = $data['owner_regon'];
  $owner_phone = $data['owner_phone'];
  $owner_email = $data['owner_email'];

  $save_query = ("UPDATE site_config SET
   owner_name='$owner_name',
   owner_surname = '$owner_surname',
   owner_firm = '$owner_firm',
   owner_title = '$owner_title',
   owner_street = '$owner_street',
   owner_postcode = '$owner_postcode',
   owner_city = '$owner_city',
   owner_country = '$owner_country',
   owner_nip = '$owner_nip',
   owner_regon = '$owner_regon',
   owner_phone = '$owner_phone',
   owner_email = '$owner_email'
    WHERE id = 1");

  $success = $db->query($save_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $db->close();
   Session::setFlash(ATXT_DATA_SAVED, 'ok');
   return TRUE;
  }
 }//END function saveSOwnerConfig($data)

 public static function editOwnerConfig($data){//AMConf-0005

  $db = Model::getDB();
  $owner_name = $data['owner_name_edit'];
  $owner_surname = $data['owner_surname_edit'];
  $owner_firm = $data['owner_firm_edit'];
  $owner_title = $data['owner_title_edit'];
  $owner_street = $data['owner_street_edit'];
  $owner_postcode = $data['owner_postcode_edit'];
  $owner_city = $data['owner_city_edit'];
  $owner_country = $data['owner_country_edit'];
  $owner_nip = $data['owner_nip_edit'];
  $owner_regon = $data['owner_regon_edit'];
  $owner_phone = $data['owner_phone_edit'];
  $owner_email = $data['owner_email_edit'];

  $save_query = ("UPDATE site_config SET
   owner_name='$owner_name',
   owner_surname = '$owner_surname',
   owner_firm = '$owner_firm',
   owner_title = '$owner_title',
   owner_street = '$owner_street',
   owner_postcode = '$owner_postcode',
   owner_city = '$owner_city',
   owner_country = '$owner_country',
   owner_nip = '$owner_nip',
   owner_regon = '$owner_regon',
   owner_phone = '$owner_phone',
   owner_email = '$owner_email'
    WHERE id = 1");

  $success = $db->query($save_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $db->close();
   Session::setFlash(ATXT_DATA_SAVED, 'ok');
   return TRUE;
  }
 }//END function saveSOwnerConfig($data)

 public static function saveMailConfig($data){//AMConf-0006

  $db = Model::getDB();
  $mail_connection = $data['mail_connection'];
  $mail_smtp_host = $data['mail_smtp_host'];
  $mail_smtp_pass = $data['mail_smtp_pass'];
  $mail_smtp_port = $data['mail_smtp_port'];
  $mail_smtp_user = $data['mail_smtp_user'];
  $mail_smtp_user = filter_var($mail_smtp_user, FILTER_SANITIZE_STRING);
  $mail_smtp_pass = filter_var($mail_smtp_pass, FILTER_SANITIZE_STRING);

   if($mail_connection == 'smtp'){
    $save_query = ("UPDATE site_config SET mail_is_smtp = 1, mail_smtp_host = '$mail_smtp_host', mail_smtp_port = '$mail_smtp_port', mail_smtp_user = '$mail_smtp_user', mail_smtp_pass = '$mail_smtp_pass' WHERE id = 1");
   }else{
    $save_query = ("UPDATE site_config SET mail_is_smtp = 0 WHERE id = 1");
   }
  $success = $db->query($save_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $db->close();
   Session::setFlash(ATXT_DATA_SAVED, 'ok');
   return TRUE;
  }
 }//END function editSiteConfig($data)

 /*
 * getConfigParam($param = '')
 * possible params:
 * owner_name
 * owner_surname
 * owner_title
 * owner_firm
 * owner_street
 * owner_postcode
 * owner_city
 * owner_country
 * owner_nip
 * owner_regon
 * owner_phone
 * owner_email
 * site_email
  * site_web_address
  * site_logo (returns part of path to image)
  * site_phone
  * mail_is_smtp
  * mail_smtp_host
  * mail_smtp_port
  * mail_smtp_user
  * mail_smtp_pass (no hashed just string)
 */
 public static function getConfigParam($param = ''){//AMConf-0007
  $db = Model::getDB();
  $val = '';
  $get_query = ("SELECT $param FROM site_config");
  $success = $db->query($get_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_array()){
    $val = $result[$param];
   }
   return $val;
  }
 }//END function getConfigParam()

}//END CLASS CONFIG