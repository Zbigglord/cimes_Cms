<?php
/**
 * Created by BBJaga.
 * Date: 2016-11-30
 * Time: 15:05
 */
namespace App\Controllers;
use App\Models\User;
use Core\Controller;
use Core\Lang;
use Core\Logs;
use Core\View;
use Core\Session;

class Users extends Controller{

 public function indexAction(){
 if(isset($_SESSION['user']) && $_SESSION['user'] != '' && isset($_SESSION['ugroup']) && $_SESSION['ugroup'] != ''){
  View::render('Main/__main.phtml');
  //todo: this is gonna be student zone
 }else{

  if(isset($_POST['fe-login-login']) && $_POST['fe-login-login'] != '' && isset($_POST['fe-login-pass']) && $_POST['fe-login-pass'] != ''){
   $_isUser = User::userLogin($_POST['fe-login-login'], $_POST['fe-login-pass']);
   if($_isUser != FALSE){
    $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    Session::set('remote_ip',$remote_ip);
    Session::unsetFlash();
    $who = Session::get('user').' ip: '.Session::get('remote_ip');
    $msg = 'logowanie przebiegło pomyślnie.';
    Logs::createLog('log', $who, $msg);
    View::render('Main/__main.phtml');
   }else{
    $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    Session::set('remote_ip',$remote_ip);
    $message = Session::getFlash();
    Session::unsetFlash();
    $who = Session::get('user').' ip: '.Session::get('remote_ip');
    $msg = 'nieudana próba logowania.';
    Logs::createLog('log', $who, $msg);
    View::render('Users/login.phtml',['message' => $message]);
   }
  }else{
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   $who = Session::get('user').' ip: '.Session::get('remote_ip');
   $msg = 'połączenie przychodzące.';
   Logs::createLog('log', $who, $msg);
   View::render('Users/login.phtml');
  }
 }

 }//end indexAction()

 public function logoutAction(){
  $sort = 'log';
  $who = Session::get('user').' ip: '.Session::get('remote_ip');
  $msg = 'użytkownik wylogowany.';
  Logs::createLog($sort, $who, $msg);
  foreach($_SESSION as $key=>$value){
   Session::delete($key);
  }
  Session::setFlash(FTXT_LOGOUT_SUCCESS,'ok');
  $message = Session::getFlash();
  Session::unsetFlash();
  Session::destroy();
  View::render('Users/login.phtml',['message' => $message]);
 }//END function logout()


}//end USERS CONTROLLER