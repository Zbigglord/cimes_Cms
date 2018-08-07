<?php
/**
 * Created by PhpStorm.
 * User: BBJaga
 * Date: 2016-09-08
 * Time: 14:00
 */

namespace App\Models;


use Core\Model;
use Core\Session;

class Student extends Model{

   public static function studentLogin($login, $pass){

    $db = Model::getDB();
    $student_group_id = 0;
    $student_group_name= '';
    $student_group_folder = '';
    $hash= '';

     $pass_query = ("SELECT * FROM courses_groups WHERE courses_groups_login = '$login'");
     $success = $db->query($pass_query);
     if(!$success){
      die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
     }else {

       $found = $success->num_rows;
       if($found > 0){

        while ($result = $success->fetch_assoc()) {
         $hash = $result['courses_groups_pass'];
         $student_group_name = $result['courses_groups_name'];
         $student_group_id = $result['courses_groups_id'];
         $student_group_folder = $result['courses_groups_folder'];
        }

        if(!password_verify($pass, $hash)){
         $db->close();
         Session::setFlash(FTXT_PASSWORD_FAILED,'error');
         return FALSE;
        }else{
         $db->close();
         Session::set('fstudent',$login);
         Session::set('fgroup_name',$student_group_name);
         Session::set('fgroup_id',$student_group_id);
         Session::set('fgroup_folder',$student_group_folder);
         return TRUE;
        }

     }else{
     $db->close();
     Session::setFlash(FTXT_LOGIN_FAILED,'error');
     return FALSE;
    }

     }
   }//END function studentLogin($nick, $pass);


}//END CLASS STUDENT