<?php
/**
 * Created by: BBJaga
 * Date: 2016-12-01
 * Time: 08:51
 */

namespace App\Models;
use Core\Model;
use Core\Session;

class User extends Model{

 public static function userLogin($login, $pass){

  $db = Model::getDB();
  $user_group = 0;
  $pass_query = ("SELECT * FROM users WHERE user_nick = '$login'");
  $success = $db->query($pass_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){

    while ($result = $success->fetch_assoc()) {
     $hash = $result['user_pass'];
     $user_group = $result['user_group'];
    }

    if(!password_verify($pass, $hash)){
     $db->close();
     Session::setFlash(FTXT_PASSWORD_FAILED,'error');
     return FALSE;
    }else{
     $db->close();
     Session::set('user',$login);
     Session::set('ugroup',$user_group);
     return TRUE;
    }

   }else{
    $db->close();
    Session::setFlash(FTXT_LOGIN_FAILED,'error');
    return FALSE;
   }
  }
 }//end function userLogin

}