<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-13
 * Time: 19:25
 */

namespace App\Controllers\Admin;

use DOMDocument;
use App\Models\Admin\Mail;
use Core\Controller;
use Core\View;
use Core\Session;

class Mails extends Controller{

  public function addNewsletterAction(){//ACMail-0001
   if (!isset($_SESSION['user_nick'])) {
    Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
    header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
   } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

    if(isset($_POST['newsletter_name']) && $_POST['newsletter_name'] != ''){
     $data = array();
     $data['newsletter_title'] = $_POST['newsletter_name'];
     $data['newsletter_content'] = $_POST['newsletter_content'];
     $data['newsletter_active'] = $_POST['newsletter_active'];
     //must get all images data from content and than modify data for cid and images name than modify model
     $html = html_entity_decode($data['newsletter_content']);
     $i = 1;//counter
     $cid = '00';//cid for dom element (need to replace img src by cids for sending img attachement inline with mailer)
     $doc = new DOMDocument();//get all images and cids for database and later replacement
     @$doc->loadHTML($html);
     $tags = $doc->getElementsByTagName('img');
     $img_array = array();
     $cid_array = array();
     foreach ($tags as $tag) {
      $cid = $cid.''.$i;
      array_push($img_array,$tag->getAttribute('src'));//geting all images
      $tag->setAttribute('src',$cid);//temprarely replaces with cids(no neccessary ?)
      array_push($cid_array,$tag->getAttribute('src'));//grouping cids in images order
      $i++;
      $cid = '00';
     }
     $data['images'] = $img_array;//add to data array
     $data['cids'] = $cid_array;//add to data array
     //end must get all images data from content
     $success = Mail::addNewsletter($data);
     $message = Session::getFlash();
     Session::unsetFlash();
     $nlist = Mail::getNewsletterList();
     View::renderAdmin('Mail/newsletterlist.phtml', ['message' => $message, 'nlist' => $nlist]);
    }else{
     View::renderAdmin('Mail/newsletteradd.phtml');
    }

   } else {
    Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
    header("location: http://".$_SERVER['SERVER_NAME']);
   }
 }//END addNewsletter()

 public function listNewsletterAction(){//ACMail-0002
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

   $nlist = Mail::getNewsletterList();
   View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist]);

  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END listNewsletter()

 public function editNewsletterAction(){//ACMail-0003
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

   if(isset($_POST['newsletter-edit-id']) && $_POST['newsletter-edit-id'] != ''){
    $newsletter = Mail::getNewsletterById($_POST['newsletter-edit-id']);
    View::renderAdmin('Mail/newsletteredit.phtml', ['newsletter' => $newsletter]);

   }elseif(isset($_POST['newsletter_name']) && $_POST['newsletter_name'] != '' && isset($_POST['newsletter_active']) && $_POST['newsletter_active'] != ''){
    $data = array();
    $data['newsletter_id'] = $_POST['newsletter_id'];
    $data['newsletter_title'] = $_POST['newsletter_name'];
    $data['newsletter_content'] = $_POST['newsletter_content'];
    $data['newsletter_active'] = $_POST['newsletter_active'];
    //must get all images data from content and than modify data for cid and images name than modify model
    $html = html_entity_decode($data['newsletter_content']);
    $i = 1;//counter
    $cid = '00';//cid for dom element (need to replace img src by cids for sending img attachement inline with mailer)
    $doc = new DOMDocument();//get all images and cids for database and later replacement
    @$doc->loadHTML($html);
    $tags = $doc->getElementsByTagName('img');
    $img_array = array();
    $cid_array = array();
    foreach ($tags as $tag) {
     $cid = $cid.''.$i;
     array_push($img_array,$tag->getAttribute('src'));//geting all images
     $tag->setAttribute('src',$cid);//temprarely replaces with cids(no neccessary ?)
     array_push($cid_array,$tag->getAttribute('src'));//grouping cids in images order
     $i++;
     $cid = '00';
    }
    $data['images'] = $img_array;//add to data array
    $data['cids'] = $cid_array;//add to data array
    //end must get all images data from content
    $success = Mail::updateNewsletter($data);
    $message = Session::getFlash();
    Session::unsetFlash();
    $nlist = Mail::getNewsletterList();
    View::renderAdmin('Mail/newsletterlist.phtml', ['message' => $message, 'nlist' => $nlist]);
   }else{
    $nlist = Mail::getNewsletterList();
    View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist]);
   }
  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END function editNewsletterAction()

 public function sendNewsletterAction(){//ACMail-0004
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

    if(isset($_POST['newsletter-send']) && $_POST['newsletter-send'] != ''){
     $newsletter = $_POST['newsletter-send'];
     $sended = Mail::sendNewsletter($newsletter);
     $nlist = Mail::getNewsletterList();
     $message = Session::getFlash();
     Session::unsetFlash();
     View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist, 'message' => $message]);
    }else{
     Session::setFlash(ATXT_UNKNOWN_ERROR.'ACMail-0004 100','error');
     $message = Session::getFlash();
     Session::unsetFlash();
     $nlist = Mail::getNewsletterList();
     View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist, 'message' => $message]);
    }

  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END sendNewsletterAction()

 public function recipientListAction(){//ACMail-0005
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

   if(isset($_POST['show-recipients-id']) && $_POST['show-recipients-id'] != ''){

    $rlist = Mail::getRecipientListByNewsletter($_POST['show-recipients-id']);
    if(!empty($rlist) || $rlist != FALSE){
     View::renderAdmin('Mail/recipientlist.phtml', ['rlist' => $rlist]);
    }else{
     $message = Session::getFlash();
     Session::unsetFlash();
     $nlist = Mail::getNewsletterList();
     View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist, 'message' => $message]);
    }
   }else{
    $nlist = Mail::getNewsletterList();
    View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist]);
   }

  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END function recipientListAction()

 public function recipientDeleteAction(){//ACMail-0006
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

    if(isset($_POST['recipient-delete']) && $_POST['recipient-delete'] != ''){
      Mail::deleteRecipient($_POST['recipient-delete']);
     $message = Session::getFlash();
     Session::unsetFlash();
     $rlist = Mail::getRecipientListByNewsletter($_POST['newsletter-id']);
     if (!empty($rlist) || $rlist != FALSE) {
      View::renderAdmin('Mail/recipientlist.phtml', ['rlist' => $rlist, 'message' => $message]);
     }else{
      Session::setFlash(ARC_LIST_EMPTY_ALL,'warning');
      $message = Session::getFlash();
      Session::unsetFlash();
      View::renderAdmin('Mail/recipientlist.phtml', ['message' => $message]);
     }
    }else {
     $rlist = Mail::getRecipientListAll();
     if (!empty($rlist) || $rlist != FALSE) {
      View::renderAdmin('Mail/recipientlist.phtml', ['rlist' => $rlist]);
     }else{
      Session::setFlash(ARC_LIST_EMPTY_ALL,'warning');
      $message = Session::getFlash();
      Session::unsetFlash();
      View::renderAdmin('Mail/recipientlist.phtml', ['message' => $message]);
     }
    }

  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END function recipientListAction()

 public function recipientListAllAction(){//ACMail-0007
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
    $rlist = Mail::getRecipientListAll();
    if(!empty($rlist) || $rlist != FALSE){
     View::renderAdmin('Mail/recipientlist.phtml', ['rlist' => $rlist]);
    }else{
     Session::setFlash(ARC_LIST_EMPTY_ALL,'warning');
     $message = Session::getFlash();
      Session::unsetFlash();
     View::renderAdmin('Mail/recipientlist.phtml',['message' => $message]);
    }
  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END function recipientListAction()

 public function addRecipientAction(){//ACMail-0008
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");

  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

   if(isset($_POST['users-list']) && $_POST['users-list'] != ''){
    $users = array();
    $students = array();
    $final = array();
    foreach($_POST['users-list'] as $option){
     $id = explode('_',$option);
     if($id[0] == 'u'){
      array_push($users, $id[1]);
     }else{
      array_push($students, $id[1]);
     }
    }
    $final['users'] = $users;
    $final['students'] = $students;
    $newsletter_id = $_POST['pick-newsletter'];
    Mail::addRecipientMulti($final,$newsletter_id);
    $message = Session::getFlash();
    Session::unsetFlash();
    $rlist = Mail::getRecipientListAll();
    View::renderAdmin('Mail/recipientlist.phtml',['message' => $message,'rlist' => $rlist]);
   }else{//if is single new recipient
    if(isset($_POST['recipient-name']) && $_POST['recipient-name'] != '' && isset($_POST['recipient-surname']) && $_POST['recipient-surname'] != '' && isset($_POST['recipient-email']) && $_POST['recipient-email'] != ''){
     if(isset($_POST['pick-newsletter']) && $_POST['pick-newsletter'] != ''){
      $data = array();
         foreach($_POST as $key => $value){
           $data[$key] = $value;
         }
       $added = Mail::addRecipient($data);
      $message = Session::getFlash();
      Session::unsetFlash();
      $rlist = Mail::getRecipientListAll();
      View::renderAdmin('Mail/recipientlist.phtml',['message' => $message,'rlist' => $rlist]);
     }else{
      Session::setFlash(MENUITEM_NOT_ALL_INPUTS,'error');
      $message = Session::getFlash();
      Session::unsetFlash();
      View::renderAdmin('Mail/addrecipient.phtml',['message' => $message]);
     }
    }else{

     $message = Session::getFlash();
     Session::unsetFlash();
     View::renderAdmin('Mail/addrecipient.phtml',['message' => $message]);
    }
   }//END single new recipient
  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END function addRecipientAction()

 public function deleteNewsletterAction(){//ACMail-0009
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
    //delete newsletter
    if(isset($_POST['newsletter-delete']) && $_POST['newsletter-delete'] != ''){
     $success = Mail::deleteNewsletter($_POST['newsletter-delete']);
     $message = Session::getFlash();
     Session::unsetFlash();
     $nlist = Mail::getNewsletterList();
     View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist, 'message' => $message]);
    }else{
     Session::setFlash(ATXT_UNKNOWN_ERROR.'ACMail-0009 313','error');
     $message = Session::getFlash();
     Session::unsetFlash();
     $nlist = Mail::getNewsletterList();
     View::renderAdmin('Mail/newsletterlist.phtml', ['nlist' => $nlist, 'message' => $message]);
    }
  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END function recipientListAction()

 public function editRecipientAction(){//ACMail-0010
  if (!isset($_SESSION['user_nick'])) {
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN, 'warning');
   header("location: http://" . $_SERVER['SERVER_NAME'] . "/admin");
  } else if (isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
   if(isset($_POST['recipient-edit-id']) && $_POST['recipient-edit-id'] != ''){
    $rlist = Mail::getRecipientById($_POST['recipient-edit-id']);
    View::renderAdmin('Mail/editrecipient.phtml',['rlist' => $rlist]);

   }elseif(isset($_POST['recipient-id']) && $_POST['recipient-id'] != '' && isset($_POST['recipient-newsletter-id']) && $_POST['recipient-newsletter-id'] != '' && isset($_POST['recipient-name']) && $_POST['recipient-name'] != '' && isset($_POST['recipient-surname']) && $_POST['recipient-surname'] != '' && isset($_POST['recipient-email']) && $_POST['recipient-email'] != ''){
    $data = array();
    foreach($_POST as $key => $value){
     $data[$key] = $value;
    }
    $success = Mail::editRecipient($data);
    $message = Session::getFlash();
    Session::unsetFlash();
    $rlist = Mail::getRecipientListAll();
    View::renderAdmin('Mail/recipientlist.phtml',['message' => $message,'rlist' => $rlist]);
   }else{
    Session::setFlash(ATXT_UNKNOWN_ERROR.'ACMail-0010 338','error');
    $message = Session::getFlash();
    Session::unsetFlash();
    $rlist = Mail::getRecipientListAll();
    View::renderAdmin('Mail/recipientlist.phtml',['message' => $message,'rlist' => $rlist]);
   }
  } else {
   Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
   header("location: http://".$_SERVER['SERVER_NAME']);
  }
 }//END function recipientListAction()

}//END CLASS MAILS