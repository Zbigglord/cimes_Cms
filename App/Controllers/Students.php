<?php
/**
 * Created by PhpStorm.
 * User: BBJaga
 * Date: 2016-09-08
 * Time: 13:53
 */

namespace App\Controllers;

use App\Models\Student;
use Core\Controller;
use Core\View;
use Core\Session;
use App\Models\Course;

class Students extends Controller{

 protected function before(){

  //nothing comes to mind
  //return false; //if return false next action will not be executed
  //check if logged

 }

 protected function after(){

  //nothing comes to mind

 }

 public function loginAction(){//in this case not needed
  if(isset($_POST['fe-login-login']) && $_POST['fe-login-login'] != '' && isset($_POST['fe-login-pass']) && $_POST['fe-login-pass'] != ''){
   $login = filter_var($_POST['fe-login-login'], FILTER_SANITIZE_STRING);
   $pass = filter_var($_POST['fe-login-pass'], FILTER_SANITIZE_STRING);
   $student = Student::studentLogin($login, $pass);
   $clist = Course::listAllCourses();
    $message = Session::getFlash();
   Session::unsetFlash();
   if(!empty($clist)){
    View::render('Courses/index.phtml',['clist'=>$clist, 'message'=>$message]);
   }else{
    View::render('Courses/index.phtml',['message'=>$message]);
   }
  }else{
   echo 'no data has been send';
  }
 }//end function loginAction()

 public function logoutAction(){
  Session::delete('fstudent');
  Session::delete('fgroup_name');
  Session::delete('fgroup_id');
  Session::delete('fgroup_folder');
  Session::setFlash(FTXT_LOGOUT_SUCCESS,'ok');
  $message = Session::getFlash();
  Session::unsetFlash();
   View::render('Main/index.phtml',['message'=>$message]);
 }//END logoutAction()

 public function grouploginAction(){
  if(isset($_POST['strefa-login']) && $_POST['strefa-login'] != '' && isset($_POST['strefa-password']) && $_POST['strefa-password'] != ''){
   $login = filter_var($_POST['strefa-login'], FILTER_SANITIZE_STRING);
   $pass = filter_var($_POST['strefa-password'], FILTER_SANITIZE_STRING);
   $student = Student::studentLogin($login, $pass);
   $message = Session::getFlash();
   Session::unsetFlash();
   View::render2layout('Students/account.phtml',['message'=>$message]);
  }else{
   echo 'no data has been send';
  }
 }//end function loginAction()

 public function groupaccountAction(){
  $student_logged = Session::fStudentLoggedIn();
  if($student_logged === TRUE){
   View::render2layout('Students/account.phtml');
  }else{
   View::render('Main/__main.phtml');
  }
 }//end function groupaccountAction()

}//END CLASS STUDENTS