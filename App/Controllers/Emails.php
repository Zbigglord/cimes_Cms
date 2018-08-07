<?php
/**
 * Created by BBJaga
 * Date: 2016-12-07
 * Time: 12:12
 */

namespace App\Controllers;
use App\Models\Email;
use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\Hosting;

class Emails extends Controller
{

 public function newAction(){

  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){

    if(isset($_POST['add_email_address']) && isset($_POST['pick-page-hosting']) && $_POST['pick-page-hosting'] != 'none'){

      $emails = array();
      foreach($_POST as $key => $value){
      $emails[$key] = $value;
     }

     Email::addNewEmail($emails);
     $message = Session::getFlash();
     Session::unsetFlash();
     $hostings_list = Hosting::getHostingsListById('ASC');
     View::render('Emails/new.phtml',['hostings' => $hostings_list, 'message' => $message]);

    }else{
     $hostings_list = Hosting::getHostingsListById('ASC');
     View::render('Emails/new.phtml',['hostings' => $hostings_list]);
    }
   }else{
    Session::setFlash('Nie masz uprawnień do dodawania nowych kont email','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Emails/list.phtml',['message' => $message]);
   }
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end newAction

 public function listAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   $emails = Email::getEmailsList('hostings_email_id','ASC');
   View::render('Emails/list.phtml',['emails' => $emails]);

  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end no logged in
 }//end listAction()

 public function editAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){
   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){
    if(isset($_POST['add_email_address']) && isset($_POST['add_email_login']) && isset($_POST['pick-page-hosting']) && $_POST['pick-page-hosting'] != 'none'){
     $data = array();
     foreach($_POST as $key => $value){
      $data[$key] = $value;
     }
     Email::editEmail($_POST['email_id'],$data);
     $message = Session::getFlash();
     Session::unsetFlash();
     $emails = Email::getEmailsList('hostings_email_id','ASC');
     View::render('Emails/list.phtml',['emails' => $emails, 'message' => $message]);

    }else{
     View::render('Emails/edit.phtml');
    }
   }else{
    Session::setFlash('Nie masz uprawnień do edycji danych','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    $emails = Email::getEmailsList('hostings_email_id','ASC');
    View::render('Emails/list.phtml',['emails' => $emails, 'message' => $message]);
   }
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end no logged in
 }//end editAction()

 public function deleteAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){
   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){

    if(isset($_POST)){
     $to_deletes = array();

     foreach($_POST as $key => $value){
      $to_deletes[$key] = $value;
     }
     Email::deleteEmailsById($to_deletes);
     $message = Session::getFlash();
     Session::unsetFlash();
     $emails = Email::getEmailsList('hostings_email_id','ASC');
     View::render('Emails/delete.phtml',['emails' => $emails, 'message' => $message]);
    }else{
     $emails = Email::getEmailsList('hostings_email_id','ASC');
     View::render('Emails/delete.phtml',['emails' => $emails]);
    }

   }else{
    Session::setFlash('Nie masz uprawnień do usuwania danych','error');
    $message = Session::getFlash();
    Session::unsetFlash();
    $emails = Email::getEmailsList('hostings_email_id','ASC');
    View::render('Emails/delete.phtml',['emails' => $emails, 'message' => $message]);
   }//end no rights
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end no logged in
 }//end deleteAction()

}//END CLASS EMAILS