<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-13
 * Time: 19:25
 */

namespace App\Models\Admin;
use DOMDocument;
use App\Models\Admin\Config;
use Core\Model;
use Core\Session;
use PHPMailer;

class Mail extends Model{

 public static function addNewsletter($data){

  $db = Model::getDB();
  $newsletter_title = $data['newsletter_title'];
  $newsletter_content = htmlentities($data['newsletter_content']);
  $newsletter_active = $data['newsletter_active'];
  $newsletter_images = '';
  $newsletter_cids = '';
  foreach($data['images'] as $image){
   $newsletter_images = $newsletter_images.','.$image;
  }
  foreach($data['cids'] as $cid){
   $newsletter_cids = $newsletter_cids.','.$cid;
  }
  if($newsletter_active == 'yes'){
   $newsletter_active = 1;
  }else{
   $newsletter_active = 0;
  }

  $add_newsletter = ("INSERT INTO newsletter (newsletter_title, newsletter_content, newsletter_images, newsletter_cids, newsletter_active) VALUES ('$newsletter_title','$newsletter_content','$newsletter_images','$newsletter_cids','$newsletter_active')");
   $success = $db->query($add_newsletter);
  if(!$success){
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $db->close();
   Session::setFlash(MAIL_NEWSLETTER_ADDED,'ok');
   return TRUE;
  }

 }//END addNewsletter($data)

 public static function updateNewsletter($data){

  $db = Model::getDB();
  $newsletter_id = $data['newsletter_id'];
  $newsletter_title = $data['newsletter_title'];
  $newsletter_content = htmlentities($data['newsletter_content']);
  $newsletter_active = $data['newsletter_active'];
  $newsletter_images = '';
  $newsletter_cids = '';
  foreach($data['images'] as $image){
   $newsletter_images = $newsletter_images.','.$image;
  }
  foreach($data['cids'] as $cid){
   $newsletter_cids = $newsletter_cids.','.$cid;
  }
  if($newsletter_active == 'yes'){
   $newsletter_active = 1;
  }else{
   $newsletter_active = 0;
  }

  $add_newsletter = ("UPDATE newsletter SET newsletter_title = '$newsletter_title', newsletter_content = '$newsletter_content', newsletter_images = '$newsletter_images', newsletter_cids = '$newsletter_cids',newsletter_active = '$newsletter_active' WHERE newsletter_id = '$newsletter_id'");
  $success = $db->query($add_newsletter);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $db->close();
   Session::setFlash(MAIL_NEWSLETTER_ADDED,'ok');
   return TRUE;
  }

 }//END addNewsletter($data)


 public static function getNewsletterList(){

$db = Model::getDB();
$nlist = array();
$list_query = ("SELECT * FROM newsletter");
$success = $db->query($list_query);
if(!$success){
die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
}else{
 while($result = $success->fetch_assoc() ){
  array_push($nlist, $result);
 }

 return $nlist;
}

}//END getNewsletterList()

 public static function getNewsletterById($id){

  $db = Model::getDB();
  $newsletter = array();
  $list_query = ("SELECT * FROM newsletter WHERE newsletter_id = '$id'");
  $success = $db->query($list_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc() ){
    foreach($result as $key => $value){
     $newsletter[$key] = $value;
    }
   }

   return $newsletter;
  }

 }//END function getNewsletterById($id)

 public static function getRecipients($id){
//written by accident and used by accident - so let's not change it:)
  $db = Model::getDB();
  $recipients = array();
  $not_delivered_list = array();
  $list_query = ("SELECT * FROM nesletter_recipients  WHERE nrec_nesletter_id = '$id'");
  $success = $db->query($list_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc() ){
     array_push($recipients, $result);
   }
   return $recipients;
  }
 }//END function getRecipients($id)

 public static function getRecipientById($id){
  $db = Model::getDB();
  $recipient = array();
  $list_query = ("SELECT * FROM nesletter_recipients WHERE nrec_id = '$id'");
  $success = $db->query($list_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc() ){
    array_push($recipient, $result);
   }
   return $recipient;
  }
 }//END function getRecipientById($id)

 public static function updateNewsletterList($id){

  $db = Model::getDB();
  $how_many_times = 0;
  $how_many_query = ("SELECT * FROM newsletter WHERE newsletter_id = '$id'");
  $success = $db->query($how_many_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
    $how_many_times = $result['newsletter_posted_times'];
   }
  }
  $how_many_times+=1;
  $sender = Session::get('user_nick');

  $list_query = ("UPDATE newsletter SET newsletter_posted_times='$how_many_times', newsletter_last_posted=NOW(), posted_by='$sender' WHERE newsletter_id = '$id'");
  $success = $db->query($list_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $db->close();
  }
 }//END updateNewsletterList($id)

 public static function sendNewsletter($id){

  $db = Model::getDB();
  $config = Config::getConfigParams();
  $newsletter = self::getNewsletterById($id);
  $recipients = self::getRecipients($id);

  $mail = new PHPMailer;
  $config = Config::getConfigParams();
  if($config['mail_is_smtp'] != 0){
   //$mail->SMTPDebug = 3;//Enable SMTP debugging.
   $mail->isSMTP();//Set PHPMailer to use SMTP.
   $mail->Host = $config['mail_smtp_host'];
   //$mail->SMTPSecure = "tls";//If SMTP requires TLS encryption then set it
   $mail->SMTPAuth = true;//Set this to true if SMTP host requires authentication to send email
   $mail->Username = $config['mail_smtp_user'];
   //to do: work on password encode
   $mail->Password = $config['mail_smtp_pass'];
   $mail->Port = $config['mail_smtp_port'];
   $mail->SMTPKeepAlive = true;
  }
  $body = html_entity_decode($newsletter['newsletter_content']);

  $i = 1;//counter
  $cid = 'cid:00';//cid for dom element (need to replace img src by cids for sending img attachement inline with mailer)
  $doc = new DOMDocument();//get all images and cids for database and later replacement
  @$doc->loadHTML($body);
  $tags = $doc->getElementsByTagName('img');
  //$images = array();
  //$temp = array();
  $host = $_SERVER['HTTP_HOST'];
  //for now we will not use cid as it does not work properly on most mail clients - so we use just link to email
  //witch is not secure but works everywhere
  foreach ($tags as $tag) {
  // $cid = $cid.''.$i;
   $filepath = $tag->getAttribute('src');
   $fileparts = explode('/',$filepath);
   $filename = end($fileparts);
   $filepath = $fileparts[1].'/'.$fileparts[2].'/'.$fileparts[3].'/'.$filename;
  // $tag->setAttribute('src',$cid);
   $tag->setAttribute('src','http://'.$host.'/'.$filepath);
  // $temp['filepath'] = $filepath;
  //$temp['cid'] = $cid;
   //$temp['filename'] = $filename;
   //array_push($images, $temp);
  //$i++;
   //$cid = 'cid:00';
  }
  $doc->saveHTMLFile("upload/temp.html");

  $mail->setFrom($config['site_email'], $config['site_web_address']);
  $mail->addReplyTo($config['site_email'], $config['site_web_address']);
  $mail->Subject = $newsletter['newsletter_title'];
  $mail->msgHTML(file_get_contents('upload/temp.html'));
  //foreach($images as $image){
  // $fp = $image['filepath'];
  // $fc = $image['cid'];
 //  $fn = $image['filename'];
 //  $mail->AddEmbeddedImage($fp,$fc,$fn);
   //$mail->AddAttachment($fp, $fn);
 // }
  //$mail->AddEmbeddedImage($fp,$fc,$fn);
  //$mail->AddAttachment($fp, $fn);

 //recipients
  foreach ($recipients as $recipient) { //This iterator syntax only works in PHP 5.4+
   $mail->addAddress($recipient['nrec_email'], $recipient['nrec_surname']);
   if (!$mail->send()) {
    array_push($not_delivered_list, $recipient['nrec_email']);
    break; //Abandon sending
   }else{
    self::updateNewsletterList($id);
   }
   $mail->clearAddresses();// clear all for next loop
  }

  if(!empty($not_delivered_list)){
   $db->close();
   Session::setFlash(MAIL_NEWSLETTER_SENDED_PARTIALLY,'warning');
   return $not_delivered_list;
  }else{
   Session::setFlash(MAIL_NEWSLETTER_SENDED,'ok');
   return TRUE;
  }

 }//END getNewsletterList()

 public static function getRecipientListByNewsletter($newsletter_id){
  $db = Model::getDB();
  $recipients = array();
  $list_query = ("SELECT * FROM nesletter_recipients WHERE nrec_nesletter_id = '$newsletter_id'");
  $success = $db->query($list_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
     while($result = $success->fetch_assoc()){
        array_push($recipients,$result);
     }
     $db->close();
     return $recipients;
   }else{
    Session::setFlash(ARC_LIST_EMPTY,'warning');
    $db->close();
    return FALSE;
   }
  }
 }//END getRecipientListByNewsletter($newsletter_id)

 public static function getNewsletterName($newsletter_id){
  $db = Model::getDB();
  $newsletter_name = '';
  $name_query = ("SELECT * FROM newsletter WHERE newsletter_id = '$newsletter_id'");
  $success = $db->query($name_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
    $newsletter_name = $result['newsletter_title'];
   }
   return $newsletter_name;
  }
 }//END function getNewsletterName($newsletter_id)

 public static function deleteRecipient($recipient_id){
  $db = Model::getDB();
  $delete_query = ("DELETE FROM nesletter_recipients WHERE nrec_id='$recipient_id'");
  $success = $db->query($delete_query);
  if(!$success){
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
    Session::setFlash(ARC_RECIPIENT_DELETED,'ok');
   $db->close();
   return TRUE;
  }
 }//END function deleteRecipient($recipient_id)

 public static function addRecipientMulti($data,$newsletter_id){
  $db = Model::getDB();
  $counter = 0;
  foreach($data as $recipients => $ids){//data gonna be array of arrays - user id and students id
   if($recipients == 'users'){//if recipient group is users
     foreach($ids as $id => $value){
      $users_query = ("SELECT *, NULL AS user_pass FROM users LEFT JOIN users_data ON users.id = users_data.user_id WHERE users.id='$value'");
      $success = $db->query($users_query);
      if(!$success){
       die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
      }else{
        while($result = $success->fetch_assoc()){//now we check if user email is not on recipient list already
         $search_email = $result['user_email'];
         $check_exists = ("SELECT * FROM nesletter_recipients WHERE nrec_email = '$search_email' AND nrec_nesletter_id = '$newsletter_id'");
         $q = $db->query($check_exists);
         $found = $q->num_rows;
         if($found > 0){//if exists do nothing

         }else{
          $email = $result['user_email'];
          $name = $result['user_name'];
          $surname = $result['user_surname'];
          $street = $result['user_street'];
          $postcode = $result['user_kod'];
          $city = $result['user_city'];

          $add_query  = ("INSERT INTO nesletter_recipients
                          (nrec_nesletter_id,
                           nrec_email,
                           nrec_name,
                           nrec_surname,
                           nrec_street,
                           nrec_postcode,
                           nrec_city,
                           nrec_region,
                           nrec_country,
                           nrec_title)
                          VALUES (
                           '$newsletter_id',
                           '$email',
                           '$name',
                           '$surname',
                           '$street',
                           '$postcode',
                           '$city',
                           '',
                           '',
                           ''
                          )");
          $add = $db->query($add_query);
          if(!$add){
           die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
          }else{
           $counter++;
           continue;
          }
         }
        }
      }
    }
   }elseif($recipients == 'students'){//if recipient is in students group
    foreach($ids as $id => $value){
      $student = User::getStudentById($value);
       $search_email = $student['student_email'];
       $check_exists = ("SELECT * FROM nesletter_recipients WHERE nrec_email = '$search_email' AND nrec_nesletter_id = '$newsletter_id'");
      $q = $db->query($check_exists);
      $found = $q->num_rows;
     if($found > 0){//if exists do nothing

     }else{
      $email = $student['student_email'];
      $name = $student['student_name'];
      $surname = $student['student_surname'];
      $street = $student['student_street'];
      $postcode = $student['student_postcode'];
      $city = $student['student_city'];
      $region = $student['student_region'];
      $country = $student['student_country'];
      $title = $student['student_ctitle'];
      $add_query  = ("INSERT INTO nesletter_recipients
                          (nrec_nesletter_id,
                           nrec_email,
                           nrec_name,
                           nrec_surname,
                           nrec_street,
                           nrec_postcode,
                           nrec_city,
                           nrec_region,
                           nrec_country,
                           nrec_title)
                          VALUES (
                           '$newsletter_id',
                           '$email',
                           '$name',
                           '$surname',
                           '$street',
                           '$postcode',
                           '$city',
                           '$region',
                           '$country',
                           '$title'
                          )");
      $add = $db->query($add_query);
      if(!$add){
       die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
      }else{
       $counter++;
       continue;
      }
     }
    }
   }//end if data
  }//endforeach
   Session::setFlash(ARC_RECIPIENTS_ADDED.' '.ARC_RECIPIENTS_AFECTED.' '.$counter,'ok');
 }//END function addRecipientMulti($data)

 public static function getRecipientListAll(){
  $db = Model::getDB();
  $recipients_list = array();
  $list_query = ("SELECT * FROM nesletter_recipients ORDER BY nrec_nesletter_id ASC");
  $success = $db->query($list_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
     array_push($recipients_list,$result);
   }
   $db->close();
   return $recipients_list;
  }
 }//END function getRecipientListAll()

 public static function addRecipient($data){
  $db = Model::getDB();
  $newsletter_id = $data['pick-newsletter'];
  $email = $data['recipient-email'];
  $name = $data['recipient-name'];
  $surname = $data['recipient-surname'];
  $street = $data['recipient-street'];
  $postcode = $data['recipient-postcode'];
  $city = $data['recipient-city'];
  $region = $data['recipient-region'];
  $country = $data['recipient-country'];
  $title = $data['recipient-title'];

   $check_query = ("SELECT * FROM nesletter_recipients WHERE nrec_email = '$email' AND nrec_nesletter_id = '$newsletter_id'");
   $q = $db->query($check_query);
   $found = $q->num_rows;
   if($found > 0){
     Session::setFlash(ARC_RECIPIENTS_EXISTS_NEWSLETTER,'warning');
     $db->close();
    return FALSE;
   }else{
    $add_query  = ("INSERT INTO nesletter_recipients
                          (nrec_nesletter_id,
                           nrec_email,
                           nrec_name,
                           nrec_surname,
                           nrec_street,
                           nrec_postcode,
                           nrec_city,
                           nrec_region,
                           nrec_country,
                           nrec_title)
                          VALUES (
                           '$newsletter_id',
                           '$email',
                           '$name',
                           '$surname',
                           '$street',
                           '$postcode',
                           '$city',
                           '$region',
                           '$country',
                           '$title'
                          )");
    $add = $db->query($add_query);
    if(!$add){
     die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
    }else{
     Session::setFlash(ARC_RECIPIENT_ADDED,'ok');
     $db->close();
     return TRUE;
    }
   }

 }//END function deleteRecipient($recipient_id)

 public static function deleteNewsletter($newsletter_id){
  $db = Model::getDB();
  $delete_query = ("DELETE FROM nesletter_recipients WHERE nrec_nesletter_id='$newsletter_id'");
  $success = $db->query($delete_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $delete_newsletter = ("DELETE FROM newsletter WHERE newsletter_id='$newsletter_id'");
   $success = $db->query($delete_newsletter);
   if(!$success){
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Session::setFlash(MAIL_NEWSLETTER_DELETED,'ok');
    $db->close();
    return TRUE;
   }
  }
 }//END function deleteNewsletter($newsletter_id)

 public static function editRecipient($data){
  $db = Model::getDB();
  $nrec_id = $data['recipient-id'];
  $nrec_nesletter_id = $data['recipient-newsletter-id'];
  $nrec_email = $data['recipient-email'];
  $nrec_name = $data['recipient-name'];
  $nrec_surname = $data['recipient-surname'];
  $nrec_street = $data['recipient-street'];
  $nrec_postcode = $data['recipient-postcode'];
  $nrec_city = $data['recipient-city'];
  $nrec_region = $data['recipient-region'];
  $nrec_country = $data['recipient-country'];
  $nrec_title = $data['recipient-title'];

  $recipient_check = ("SELECT * FROM nesletter_recipients WHERE nrec_id = '$nrec_id' AND nrec_nesletter_id = '$nrec_nesletter_id'");
  $success = $db->query($recipient_check);
  $found = $success->num_rows;
  if($found > 0){
   $update_query = ("UPDATE nesletter_recipients SET
                     nrec_email='$nrec_email',
                     nrec_name='$nrec_name',
                     nrec_surname='$nrec_surname',
                     nrec_street='$nrec_street',
                     nrec_postcode='$nrec_postcode',
                     nrec_city='$nrec_city',
                     nrec_region='$nrec_region',
                     nrec_country='$nrec_country',
                     nrec_title='$nrec_title'
                     WHERE nrec_id = '$nrec_id' AND nrec_nesletter_id = '$nrec_nesletter_id'");

    $success = $db->query($update_query);
   if(!$success){
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Session::setFlash(ARC_RECIPIENT_ADDED,'ok');
    $db->close();
    return TRUE;
   }
  }else{
   Session::setFlash(ARC_RECIPIENT_NOT_EXISTS,'error');
   $db->close();
   return FALSE;
  }
 }//END function deleteNewsletter($newsletter_id)

}// END CLASS MAIL