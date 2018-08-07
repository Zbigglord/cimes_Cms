<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-20
 * Time: 11:41
 */

namespace App\Models\Admin;
use \DOMDocument;
use Core\Lang;
use Core\Session;
use Core\Model;
use App\Models\Admin\Course;

class User extends Model{

    /**
     * @param $data
     */
    public static function getUsergroupName($data){ // function nr AMUS-0001

    switch ($data){
        case 'super':
            echo AMENU_SUPERADMIN;
            break;
        case 'admin':
            echo AMENU_ADMIN;
            break;
        case 'moderator':
            echo AMENU_MODERATOR;
            break;
        case 'editor':
            echo AMENU_EDITOR;
            break;
        case 'regular_user':
            echo AMENU_REGULAR_USER;
            break;
        case 'random_role':
            echo AMENU_RANDOM_ROLE;
            break;
        default:
            echo '???';
            break;
    }
}//END function getUsergroupName($data)

    /**
     * @return array
     */
    public static function selectUser(){// function nr AMUS-0002
    $names = array();
        $db = Model::getDB();
        $db_user = ("SELECT * FROM users");
        $result = $db->query($db_user);
        if(!$result){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            if($result-> num_rows > 0){
                while($user = $result->fetch_assoc()){
                    array_push($names, $user);
                }
            }
        }
    $db->close();
    return $names;
}//END function selectUser()

    /**
     * @param $nick
     * @param $email
     * @param $pass
     */
    public static function addNew($nick, $email, $pass){// function nr AMUS-0003

        $nick = filter_var($nick, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

       $options = [
      'cost' => 11,
     ];

     $hash = password_hash($pass, PASSWORD_BCRYPT, $options);

        $db = Model::getDB();

        $db_search = ("SELECT * FROM users WHERE user_nick = '$nick' OR user_email = '$email'");
        $result = $db->query($db_search);

        if(!$result){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            if($result-> num_rows > 0){
                while($same_user = $result->fetch_assoc()){
                    if($same_user['user_email'] === $email && $same_user['user_nick'] === $nick){
                        Session::setFlash(ATXT_USER_WITH_EMAIL_AND_LOGIN_ALREADY_EXIST,'error');
                    }elseif($same_user['user_email'] === $email){
                        Session::setFlash(ATXT_USER_WITH_EMAIL_ALREADY_EXIST,'error');
                    }elseif($same_user['user_nick'] === $nick){
                        Session::setFlash(ATXT_USER_WITH_LOGIN_ALREADY_EXIST,'error');
                    }
                }
                  $db->close();
                  return FALSE;
            }else{
                $db_addUser = ("INSERT INTO users (user_nick, user_email, user_pass) VALUES ('$nick','$email','$hash')");
                $result = $db->query($db_addUser);
                if(!$result){
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                }else{
                    Session::setFlash(ATXT_USER_ADDED_TO_DATABASE,'ok');
                    $db->close();
                }

                return TRUE;
            }
        }


    }//END function addNew($nick, $email, $pass)

    /**
     * @param $nick
     * @param $pass
     * @return bool
     */
    public static function login($nick, $pass){// function nr AMUS-0004

        $nick = filter_var($nick, FILTER_SANITIZE_STRING);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        $db = Model::getDB();
        $db_login = ("SELECT * FROM users WHERE user_nick = '$nick'");
        if(!$result = $db->query($db_login)){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            $found = $result-> num_rows;
            if($found > 0){
                while ($found = $result->fetch_assoc()) {
                    $user_pass = $found['user_pass'];
                    $user_id = $found['id'];
                }
                if (!password_verify($pass, $user_pass)) {
                    Session::setFlash(ATXT_NO_SUCH_PASS,'error');
                    $db->close();
                    return FALSE;
                } else {
                    $db_role = ("SELECT * FROM users LEFT JOIN users_group ON users.id = users_group.u_id WHERE users.id = '$user_id'");
                    $result = $db->query($db_role);
                    $found = $result-> num_rows;
                    if($found > 0){
                        while ($found = $result->fetch_assoc()){//set all user related data to session variables
                            foreach($found as $key=>$value){
                                Session::set($key,$value);
                            }
                        }
                    }

                    if(isset($_SESSION['user_pass'])){
                        unset($_SESSION['user_pass']);
                    }

                     if(isset($_SESSION['id'])){//update last active date
                        $us_is = $_SESSION['id'];
                        $db_lastActive = ("UPDATE users SET user_lastactive = NOW() WHERE id='$us_is'");
                         $db->query($db_lastActive);
                     }
                    $db->close();
                    return TRUE;
                }
            }else{
                Session::setFlash(ATXT_NO_SUCH_USER . $nick,'error');
                $db->close();
                return FALSE;
            }
        }

    }//END function login($nick, $pass)

    /**
     * @return array
     */
    public static function listAll(){// function nr AMUS-0005

        $db = Model::getDB();
        $db_showAll = ("SELECT *, NULL AS user_pass FROM users LEFT JOIN users_data ON users.id = users_data.user_id LEFT JOIN users_group ON users_data.user_id = users_group.u_id");
        /*NULL AS user_pass is hack to not pull password for users and still could use SELECT ALL clause*/
        $result_show = $db->query($db_showAll);
        if(!$result_show){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            $users_list = array();
            $found = $result_show-> num_rows;
            if($found > 0){
                while ($found = $result_show->fetch_assoc()){
                    $group = $found['user_group'];
                    $i = $found['id'];
                    if($_SESSION['user_group'] != 1) {
                        if ($group != 1) {
                            $users_list[$i] = $found;
                        } else {

                        }
                    }else{
                        $users_list[$i] = $found;
                    }
                }

            }else{

                Session::setFlash(ATXT_USERS_LIST_IS_EMPTY,'error');
                $users_list = array();

            }

        }
        $db->close();
        return $users_list;
    }//END function listAll()

    /**
     * @param $id
     * @return array
     */
    public static function listUserByID($id){// function nr AMUS-0006

        $db = Model::getDB();
        $db_showUser = ("SELECT *, NULL AS user_pass FROM users LEFT JOIN users_data ON users.id = users_data.user_id LEFT JOIN users_group ON users_data.user_id = users_group.u_id WHERE users.id='$id'");
        /*NULL AS user_pass is hack to not pull password for users and still could use SELECT ALL clause*/
        $result_show = $db->query($db_showUser);
        if(!$result_show){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            $users_data = array();
            $found = $result_show-> num_rows;
            if($found > 0){
                while ($found = $result_show->fetch_assoc()){
                    $group = $found['user_group'];
                    $i = $found['id'];
                    if($_SESSION['user_group'] != 1) {
                        if ($group != 1) {
                            $users_data[$i] = $found;
                        } else {

                        }
                    }else{
                        $users_data[$i] = $found;
                    }
                }

            }else{

                Session::setFlash(ATXT_NO_AVAIABLE_DATA,'error');
                $users_data = array();

            }

        }
        $db->close();
        return $users_data;
    }//END function listUserByID($id)

    /**
     * @param $user_id
     * @param $action
     * @return array
     */
    public static function deleteUser($user_id,$action){// function nr AMUS-0007
        $db = Model::getDB();
        if(isset($user_id)){
            if($action == 'delete'){
                $uCheck = ("SELECT id FROM users WHERE id='$user_id'");
                $result = $db->query($uCheck);
                $found = $result-> num_rows;
                if($found == 0){
                    Session::setFlash(ATXT_NO_SUCH_USER.' '.ATXT_NOT_POSSIBLE_TO_DELETE_USER,'error');
                    $db->close();
                    return false;
                }else{
                    $uDelete_first = ("DELETE FROM users_group WHERE u_id='$user_id'");
                    $uDelete_second = ("DELETE FROM users_data WHERE user_id='$user_id'");
                    $uDelete_last = ("DELETE FROM users WHERE id='$user_id'");
                    $result = $db->query($uDelete_first);
                    if(!$result){
                        die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                    }else{
                        $result = $db->query($uDelete_second);
                        if(!$result){
                            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                        }else{
                            $result = $db->query($uDelete_last);
                            if(!$result){
                                die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                            }else{
                                Session::setFlash(ATXT_USER_DELETED_SUCCESSFULY,'ok');
                                $db->close();
                                return true;
                            }
                        }
                    }
                }
            }elseif($action == 'deactivate'){
                $qDeactivate = ("UPDATE users SET user_active = 0 WHERE id='$user_id'");
                if(!$result = $db->query($qDeactivate) ){
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                }else{
                    Session::setFlash(ATXT_USER_DEACTIVATED_SUCCESSFULY,'ok');
                    $db->close();
                    return true;
                }
            }elseif($action == 'activate'){
                $qActivate = ("UPDATE users SET user_active = 1 WHERE id='$user_id'");
                if(!$result = $db->query($qActivate) ){
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                }else{
                    Session::setFlash(ATXT_USER_ACTIVATED_SUCCESSFULY,'ok');
                    $db->close();
                    return true;
                }
            }else{
                Session::setFlash(ATXT_UNKNOWN_ERROR.' AMUS-0007-276','error'); //for some reason no action isset
                $db->close();
                return false;
            }
        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.' AMUS-0007-279','error');//for some reason no user id isset
            $db->close();
            return false;
        }
    }//END function deleteUser($user_id,$action)

    public static function addStudent($data){// function nr AMUS-0009
      $db = Model::getDB();
        $student_group_id = ($data['student-group-select'] != 'none') ? $data['student-group-select'] : 0;
        if($student_group_id != 0){
            $group = Course::getGroupById($student_group_id);
            $student_group_name = $group['courses_groups_name'];
        }else{
            $student_group_name = '';
        }

        $student_name = $data['student-name'];
        $student_surname = $data['student-surname'];
        $student_title = $data['student-title'];
        $student_street = $data['student-street'];
        $student_postcode = $data['student-postcode'];
        $student_city = $data['student-city'];
        $student_region = $data['student-region'];
        $student_country = $data['student-country'];
        $student_email = $data['student-email'];
        $student_date_added = '';
        $student_lastactive = '';
        if($data['student-newsletter-select'] != 'none'){
            $student_newsletter = 1;
            $student_newsletter_id = $data['student-newsletter-select'];
        }else{
            $student_newsletter = 0;
            $student_newsletter_id = 0;
        }
        $student_additional = $data['student-additional'];
        $add_student = ("INSERT INTO students (
                         student_group_id,
                         student_group_name,
                         student_name,
                         student_surname,
                         student_title,
                         student_street,
                         student_postcode,
                         student_city,
                         student_region,
                         student_country,
                         student_email,
                         student_date_added,
                         student_newsletter,
                         student_newsletter_id,
                         student_additional
                        ) VALUES (
                          '$student_group_id',
                          '$student_group_name',
                          '$student_name',
                          '$student_surname',
                          '$student_title',
                          '$student_street',
                          '$student_postcode',
                          '$student_city',
                          '$student_region',
                          '$student_country',
                          '$student_email',
                          NOW(),
                          '$student_newsletter',
                          '$student_newsletter_id',
                          '$student_additional'
                        )
                        ");

        $success = $db->query($add_student);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            if($student_newsletter == 0){
                $db->close();
                Session::setFlash(ATXT_STUDENT_ADDED,'ok');
                return TRUE;
            }else{
             //check if student exists in recipients
             $check_query = ("SELECT * FROM nesletter_recipients WHERE nrec_nesletter_id = '$student_newsletter_id' AND nrec_email='$student_email'");
             $success = $db->query($check_query);
             if(!$success){
              die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
             }else{
               $found = $success->num_rows;
               if($found > 0){
                $db->close();
                Session::setFlash(ATXT_STUDENT_ADDED,'ok');
                return TRUE;
               }else{
                $recipient_query = ("INSERT INTO nesletter_recipients (
                                     nrec_nesletter_id,
                                     nrec_email,
                                     nrec_name,
                                     nrec_surname,
                                     nrec_street,
                                     nrec_postcode,
                                     nrec_city,
                                     nrec_region,
                                     nrec_country,
                                     nrec_title
                                    ) VALUES (
                                     '$student_newsletter_id',
                                     '$student_email',
                                     '$student_name',
                                     '$student_surname',
                                     '$student_street',
                                     '$student_postcode',
                                     '$student_city',
                                     '$student_region',
                                     '$student_country',
                                     '$student_title'
                                    )
                                    ");

                $success = $db->query($recipient_query);
                if(!$success){
                 die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                }else{
                 $db->close();
                 Session::setFlash(ATXT_STUDENT_ADDED,'ok');
                 return TRUE;
                }
               }

             }
            }
        }

    }//END function addStudent($data)

 public static function studentList(){// function nr AMUS-0010
  $db = Model::getDB();
  $slist = array();
  $list_query = ("SELECT * FROM students");
  $success = $db->query($list_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
    array_push($slist,$result);
   }
   return $slist;
  }
 }//END function studentList()

 public static function getStudentById($id){// function nr AMUS-0011
  $db = Model::getDB();
  $studentData = array();
  $student_query = ("SELECT * FROM students WHERE id='$id'");
  $success = $db->query($student_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
  }else{
   while($result = $success->fetch_assoc()){
    foreach($result as $key => $value){
     $studentData[$key] = $value;
    }
   }
   return $studentData;
  }
 }//END getStudentById($id)

 public static function checkStudentEx($data){// function nr AMUS-0012
  $db = Model::getDB();
  $email = $data['email'];
  $name = $data['name'];
  $surname = $data['surname'];
  $group = $data['group'];
  $student_query = ("SELECT * FROM students WHERE student_email='$email' AND student_name='$name' AND student_surname='$surname' AND student_group_id='$group'");
  $success = $db->query($student_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found >0){
    $db->close();
    return FALSE;
   }else{
    $db->close();
    return TRUE;
   }
  }
 }//END checkStudentAvail($data)

 public static function deleteStudent($id){// function nr AMUS-0013
  $db = Model::getDB();
   $get_data_query = ("SELECT * FROM students WHERE id = '$id'");
  $success = $db->query($get_data_query);
  if(!$success){
   die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
  }

    $found = $success->num_rows;
    if($found > 0){
      //won't delete recipient for now
       $delete_student = ("DELETE FROM students WHERE id='$id'");
        $success = $db->query($delete_student);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }
        Session::setFlash(ATXT_STUDENT_DELETED,'ok');
        $db->close();
        return TRUE;
    }else{
     Session::setFlash(ATXT_STUDENT_NOT_EXISTS,'error');
     $db->close();
     return FALSE;
    }


 }//END deleteStudent($id)

    public static function editStudent($data){// function nr AMUS-0014
        $db = Model::getDB();
        $student_group_id = ($data['student-group-select'] != 'none') ? $data['student-group-select'] : 0;
        if($student_group_id != 0){
            $group = Course::getGroupById($student_group_id);
            $student_group_name = $group['courses_groups_name'];
        }else{
            $student_group_name = '';
        }
        $student_id = $data['student-id'];
        $student_name = $data['student-name'];
        $student_surname = $data['student-surname'];
        $student_title = $data['student-title'];
        $student_street = $data['student-street'];
        $student_postcode = $data['student-postcode'];
        $student_city = $data['student-city'];
        $student_region = $data['student-region'];
        $student_country = $data['student-country'];
        $student_email = $data['student-email'];
        if($data['student-newsletter-select'] != 'none'){
            $student_newsletter = 1;
            $student_newsletter_id = $data['student-newsletter-select'];
        }else{
            $student_newsletter = 0;
            $student_newsletter_id = 0;
        }
        $student_additional = $data['student-additional'];
        $update_student = ("UPDATE students SET
                         student_group_id ='$student_group_id',
                         student_group_name ='$student_group_name',
                         student_name ='$student_name',
                         student_surname ='$student_surname',
                         student_title ='$student_title',
                         student_street ='$student_street',
                         student_postcode ='$student_postcode',
                         student_city ='$student_city',
                         student_region ='$student_region',
                         student_country ='$student_country',
                         student_email ='$student_email',
                         student_newsletter ='$student_newsletter',
                         student_newsletter_id ='$student_newsletter_id',
                         student_additional ='$student_additional'
                         WHERE id = '$student_id'
                        ");

        $success = $db->query($update_student);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            if($student_newsletter == 0){
                $db->close();
                Session::setFlash(ATXT_STUDENT_EDITED,'ok');
                return TRUE;
            }else{
                //check if student exists in recipients
                $check_query = ("SELECT * FROM nesletter_recipients WHERE nrec_nesletter_id = '$student_newsletter_id' AND nrec_email='$student_email'");
                $success = $db->query($check_query);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                }else{
                    $found = $success->num_rows;
                    if($found > 0){
                        $db->close();
                        Session::setFlash(ATXT_STUDENT_EDITED,'ok');
                        return TRUE;
                    }else{
                        $recipient_query = ("INSERT INTO nesletter_recipients (
                                     nrec_nesletter_id,
                                     nrec_email,
                                     nrec_name,
                                     nrec_surname,
                                     nrec_street,
                                     nrec_postcode,
                                     nrec_city,
                                     nrec_region,
                                     nrec_country,
                                     nrec_title
                                    ) VALUES (
                                     '$student_newsletter_id',
                                     '$student_email',
                                     '$student_name',
                                     '$student_surname',
                                     '$student_street',
                                     '$student_postcode',
                                     '$student_city',
                                     '$student_region',
                                     '$student_country',
                                     '$student_title'
                                    )
                                    ");

                        $success = $db->query($recipient_query);
                        if(!$success){
                            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                        }else{
                            $db->close();
                            Session::setFlash(ATXT_STUDENT_EDITED,'ok');
                            return TRUE;
                        }
                    }

                }
            }
        }

    }//END function editStudent($data)


} //end class User