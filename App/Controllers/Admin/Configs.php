<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-13
 * Time: 19:25
 */

namespace App\Controllers\Admin;


use App\Models\Admin\Config;
use Core\Controller;
use Core\Session;
use Core\View;

class Configs extends Controller{

 public function configureMailAction(){//ACConf-0001
  if(!isset($_SESSION['user_nick'])){
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
   header("location: http://".$_SERVER['SERVER_NAME']."/admin");
  }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1 && $_SESSION['change_config'] == 1){

   if(isset($_POST['mail_connection']) && $_POST['mail_connection'] != 'none'){//smtp connection
    $data = array();
    foreach($_POST as $key => $value){
     $data[$key] = $value;
    }
    $success = Config::saveMailConfig($data);
    $message = Session::getFlash();
    Session::unsetFlash();
    $site = Config::getConfigParams();
    View::renderAdmin('Config/configmail.phtml',['message' => $message, 'site' => $site]);
   }else{
    // else
    $site = Config::getConfigParams();
    $message = Session::getFlash();
    Session::unsetFlash();
    View::renderAdmin('Config/configmail.phtml',['message' => $message, 'site' => $site]);
   }
  }else{
   Session::setFlash(ATXT_NO_CONFIG_RIGHTS,'error');
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/configmail.phtml', ['message' => $message]);
  }
 }//END configureMailAction()

 public function configureSiteAction(){//ACConf-0003
if(!isset($_SESSION['user_nick'])){
Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
header("location: http://".$_SERVER['SERVER_NAME']."/admin");
}else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1 && $_SESSION['change_config'] == 1){
  if(isset($_POST['site_email']) && $_POST['site_email'] != '' && isset($_POST['site_web_address']) && $_POST['site_web_address'] != ''){
   $data = array();
     foreach($_POST as $key => $value){
      $data[$key] = $value;
     }
    $success = Config::saveSiteConfig($data);
   $param = 'list';
   $site = Config::getConfigParams();
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/site.phtml',['param' => $param, 'message' => $message, 'site' => $site]);
  }elseif(isset($_POST['param']) && $_POST['param'] = 'edit_site'){
   $param = 'edit';
   $site = Config::getConfigParams();
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/site.phtml',['param' => $param, 'message' => $message, 'site' => $site]);
  }else{
   $param = 'add';
   View::renderAdmin('Config/site.phtml',['param' => $param]);
  }
}else{
 Session::setFlash(ATXT_NO_CONFIG_RIGHTS,'error');
 $message = Session::getFlash();
 Session::unsetFlash();
 View::renderAdmin('Config/site.phtml', ['message' => $message]);
}
}//END configureSiteAction()

public function editSiteAction(){//ACConf-0004
 if(!isset($_SESSION['user_nick'])){
  Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
  header("location: http://".$_SERVER['SERVER_NAME']."/admin");
 }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1 && $_SESSION['change_config'] == 1){
  if(isset($_POST['site_email_edit']) && $_POST['site_email_edit'] != '' && isset($_POST['site_web_address_edit']) && $_POST['site_web_address_edit'] != ''){
   $data = array();
   foreach($_POST as $key => $value){
    $data[$key] = $value;
   }
   $success = Config::editSiteConfig($data);
   $param = 'list';
   $site = Config::getConfigParams();
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/site.phtml',['param' => $param, 'message' => $message, 'site' => $site]);
  }else{
   $param = 'edit';
   $site = Config::getConfigParams();
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/site.phtml',['param' => $param, 'message' => $message, 'site' => $site]);
  }
 }else{
  Session::setFlash(ATXT_NO_CONFIG_RIGHTS,'error');
  $message = Session::getFlash();
  Session::unsetFlash();
  View::renderAdmin('Config/site.phtml', ['message' => $message]);
 }
}//END editMSiteAction()

 public function configureOwnerAction(){//ACConf-0005
  if(!isset($_SESSION['user_nick'])){
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
   header("location: http://".$_SERVER['SERVER_NAME']."/admin");
  }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1 && $_SESSION['change_config'] == 1){
    if(isset($_POST['param']) && $_POST['param'] == 'save'){
     $data = array();
     foreach($_POST as $key => $value){
      $data[$key] = $value;
     }
     $success = Config::saveOwnerConfig($data);
     $site = Config::getConfigParams();
     $message = Session::getFlash();
     Session::unsetFlash();
     $param = 'edit';
     View::renderAdmin('Config/owneredit.phtml',['message' => $message, 'site' => $site, 'param' => $param]);
    }elseif(isset($_POST['param']) && $_POST['param'] == 'edit'){
     $data = array();
     foreach($_POST as $key => $value){
      $data[$key] = $value;
     }
     $success = Config::editOwnerConfig($data);
     $site = Config::getConfigParams();
     $message = Session::getFlash();
     Session::unsetFlash();
     $param = 'edit';
     View::renderAdmin('Config/owneredit.phtml',['message' => $message, 'site' => $site, 'param' => $param]);
    }else{
     View::renderAdmin('Config/owneradd.phtml');
    }
  }else{
   Session::setFlash(ATXT_NO_CONFIG_RIGHTS,'error');
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/owner.phtml', ['message' => $message]);
  }
 }//END configureOwnerAction()

 public function editOwnerAction(){//ACConf-0006
  if(!isset($_SESSION['user_nick'])){
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
   header("location: http://".$_SERVER['SERVER_NAME']."/admin");
  }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1 && $_SESSION['change_config'] == 1){
   $site = Config::getConfigParams();
   $message = Session::getFlash();
   Session::unsetFlash();
   $param = 'edit';
   View::renderAdmin('Config/owneredit.phtml',['message' => $message, 'site' => $site, 'param' => $param]);
  }else{
   Session::setFlash(ATXT_NO_CONFIG_RIGHTS,'error');
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/owner.phtml', ['message' => $message]);
  }
 }//END editOwnerAction()

 public function saveOwnerAction(){//ACConf-0007 - used to edit save
  if(!isset($_SESSION['user_nick'])){
   Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
   header("location: http://".$_SERVER['SERVER_NAME']."/admin");
  }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1 && $_SESSION['change_config'] == 1){
   if(isset($_POST['param']) && $_POST['param'] == 'edit'){
    $data = array();
    foreach($_POST as $key => $value){
     $data[$key] = $value;
    }
    $success = Config::editOwnerConfig($data);
    $site = Config::getConfigParams();
    $message = Session::getFlash();
    Session::unsetFlash();
    $param = 'edit';
    View::renderAdmin('Config/owneredit.phtml',['message' => $message, 'site' => $site, 'param' => $param]);
   }else{
    View::renderAdmin('Config/owneradd.phtml');
   }
  }else{
   Session::setFlash(ATXT_NO_CONFIG_RIGHTS,'error');
   $message = Session::getFlash();
   Session::unsetFlash();
   View::renderAdmin('Config/owner.phtml', ['message' => $message]);
  }
 }//END saveOwnerAction()

}//END CLASS CONFIGS