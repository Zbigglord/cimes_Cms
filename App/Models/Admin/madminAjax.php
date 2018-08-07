<?php
/**
 * Creator: BabooJaga
 * Date: 2016-03-26
 * Time: 16:38
 */

namespace App\Models\Admin;
use Core\Session;
use Core\Model;

class madminAjax extends \Core\Model{

    public static function addNewUser($userData){//AMAx-0001
        $db = Model::getDB();
        $options = [
            'cost' => 11,
        ];

        foreach($userData as $key => $value){

            $name = array_key_exists('name', $userData) ? $userData['name'] : '';
            $surname = array_key_exists('surname', $userData) ? $userData['surname'] : '';
            $street = array_key_exists('street', $userData) ? $userData['street'] : '';
            $postcode = array_key_exists('postcode', $userData) ? $userData['postcode'] : '';
            $city = array_key_exists('city', $userData) ? $userData['city'] : '';
            $nick = array_key_exists('nick', $userData) ? $userData['nick'] : '';
            $email = array_key_exists('email', $userData) ? $userData['email'] : '';
            $hash = array_key_exists('pass', $userData) ? password_hash($userData['pass'], PASSWORD_BCRYPT, $options) : '';
            $group_name = array_key_exists('group_name', $userData) ? $userData['group_name'] : '';
            $group_id = array_key_exists('group_id', $userData) ? $userData['group_id'] : '';
            $change_config = array_key_exists('change_config', $userData) ? $userData['change_config'] : 0;
            $user_active = array_key_exists('user_active', $userData) ? $userData['user_active'] : 0;
            $show_admin = array_key_exists('show_admin', $userData) ? $userData['show_admin'] : 0;
            $add_user = array_key_exists('add_user', $userData) ? $userData['add_user'] : 0;
            $delete_user = array_key_exists('delete_user', $userData) ? $userData['delete_user'] : 0;
            $edit_user = array_key_exists('edit_user', $userData) ? $userData['edit_user'] : 0;
            $add_menu = array_key_exists('add_menu', $userData) ? $userData['add_menu'] : 0;
            $delete_menu = array_key_exists('delete_menu', $userData) ? $userData['delete_menu'] : 0;
            $edit_menu = array_key_exists('edit_menu', $userData) ? $userData['edit_menu'] : 0;
            $add_category = array_key_exists('add_category', $userData) ? $userData['add_category'] : 0;
            $delete_category = array_key_exists('delete_category', $userData) ? $userData['delete_category'] : 0;
            $edit_category = array_key_exists('edit_category', $userData) ? $userData['edit_category'] : 0;
            $add_post = array_key_exists('add_post', $userData) ? $userData['add_post'] : 0;
            $delete_post = array_key_exists('delete_post', $userData) ? $userData['delete_post'] : 0;
            $edit_post = array_key_exists('edit_post', $userData) ? $userData['edit_post'] : 0;

        }//end foreach

   $db_addNew1 = ("
                        INSERT INTO users (
                        user_nick,
                        user_pass,
                        user_group,
                        user_email,
                        user_regdate,
                        user_active
                        ) VALUES (
                         '$nick',
                         '$hash',
                         '$group_id',
                         '$email',
                          NOW(),
                          $user_active
                        ) ");

         if(!$db->query($db_addNew1)){
             die($db->error);
         }else{
        //now we have to find out what last inserted id was to know what id to insert into other tables (they dependant through foreign key)
         $find_id = ("SELECT MAX(id) FROM users");
         $result = $db->query($find_id);
          while ($found = $result->fetch_row()) {
                    $last_id = $found[0];
          }

            $db_addNew2 = ("INSERT INTO users_data(
                          user_id,
                          user_name,
                          user_surname,
                          user_street,
                          user_kod,
                          user_city
                        ) VALUES (
                          '$last_id',
                          '$name',
                          '$surname',
                          '$street',
                          '$postcode',
                          '$city'
                        )");

        if(!$db->query($db_addNew2)){
            die($db->error);
        }else {
            $db_addNew3 = ("INSERT INTO users_group(
                          u_id,
                          group_id,
                          group_name,
                          change_config,
                          add_user,
                          edit_user,
                          delete_user,
                          add_menu,
                          edit_menu,
                          delete_menu,
                          add_category,
                          edit_category,
                          delete_category,
                          add_post,
                          edit_post,
                          delete_post,
                          show_admin
                        ) VALUES (
                          '$last_id',
                          '$group_id',
                          '$group_name',
                          '$change_config',
                          '$add_user',
                          '$edit_user',
                          '$delete_user',
                          '$add_menu',
                          '$edit_menu',
                          '$delete_menu',
                          '$add_category',
                          '$edit_category',
                          '$delete_category',
                          '$add_post',
                          '$edit_post',
                          '$delete_post',
                          '$show_admin'
                        )
                      ");
            if(!$db->query($db_addNew3)){
                die($db->error);
            }else{
                Session::setFlash(ATXT_USER_ADDED_TO_DATABASE,'ok');
            }
        }
      }
  }//end addNewUser($userData);

    public static function editUser($userData){//AMAx-0002
        $db = Model::getDB();
        $options = [
            'cost' => 11,
        ];

        if(array_key_exists('uid', $userData)){
            $id = $userData['uid'];
            $db_oldData = ("SELECT *, NULL AS user_pass FROM users LEFT JOIN users_data ON users.id = users_data.user_id LEFT JOIN users_group ON users_data.user_id = users_group.u_id WHERE users.id='$id'");
            $result = $db->query($db_oldData);
            if(!$result){
                die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
            }else{
                $found = $result-> num_rows;
                if($found > 0){
                    $old_userData = $result->fetch_assoc();
                }else{
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                }
            }
        }else{
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }

        foreach($userData as $key => $value){//check if key is in array, than if exists ok if not

            $name = array_key_exists('name', $userData) ? $userData['name'] : $old_userData['user_name'];
            $surname = array_key_exists('surname', $userData) ? $userData['surname'] : $old_userData['user_surname'];
            $street = array_key_exists('street', $userData) ? $userData['street'] : $old_userData['user_street'];
            $postcode = array_key_exists('postcode', $userData) ? $userData['postcode'] : $old_userData['user_kod'];
            $city = array_key_exists('city', $userData) ? $userData['city'] : $old_userData['user_city'];
            $nick = array_key_exists('nick', $userData) ? $userData['nick'] : $old_userData['user_nick'];
            $email = array_key_exists('email', $userData) ? $userData['email'] : $old_userData['user_email'];
            $hash = array_key_exists('pass', $userData) ? password_hash($userData['pass'], PASSWORD_BCRYPT, $options) : '';//than if hash empty wil not change password
            $group_name = array_key_exists('group_name', $userData) ? $userData['group_name'] : $old_userData['group_name'];
            $group_id = array_key_exists('group_id', $userData) ? $userData['group_id'] : $old_userData['group_id']; //than if is 100 means is empty so do not change values
            $change_config = array_key_exists('change_config', $userData) ? $userData['change_config'] : $old_userData['change_config'];
            $user_active = array_key_exists('user_active', $userData) ? $userData['user_active'] : $old_userData['user_active'];
            $show_admin = array_key_exists('show_admin', $userData) ? $userData['show_admin'] : $old_userData['show_admin'];
            $add_user = array_key_exists('add_user', $userData) ? $userData['add_user'] : $old_userData['add_user'];
            $delete_user = array_key_exists('delete_user', $userData) ? $userData['delete_user'] : $old_userData['delete_user'];
            $edit_user = array_key_exists('edit_user', $userData) ? $userData['edit_user'] : $old_userData['edit_user'];
            $add_menu = array_key_exists('add_menu', $userData) ? $userData['add_menu'] : $old_userData['add_menu'];
            $delete_menu = array_key_exists('delete_menu', $userData) ? $userData['delete_menu'] : $old_userData['delete_menu'];
            $edit_menu = array_key_exists('edit_menu', $userData) ? $userData['edit_menu'] : $old_userData['edit_menu'];
            $add_category = array_key_exists('add_category', $userData) ? $userData['add_category'] : $old_userData['add_category'];
            $delete_category = array_key_exists('delete_category', $userData) ? $userData['delete_category'] : $old_userData['delete_category'];
            $edit_category = array_key_exists('edit_category', $userData) ? $userData['edit_category'] : $old_userData['edit_category'];
            $add_post = array_key_exists('add_post', $userData) ? $userData['add_post'] : $old_userData['add_post'];
            $delete_post = array_key_exists('delete_post', $userData) ? $userData['delete_post'] : $old_userData['delete_post'];
            $edit_post = array_key_exists('edit_post', $userData) ? $userData['edit_post'] : $old_userData['edit_post'];

        }//end foreach

        if($group_id == 100){//if user group and associated data will not change
            if($hash != ''){//if password has to change
            $update_query = ("
                UPDATE users
                LEFT JOIN users_data ON users.id = users_data.user_id
                SET users_data.user_name = '$name',
                users_data.user_surname = '$surname',
                users_data.user_street = '$street',
                users_data.user_kod = '$postcode',
                users_data.user_city = '$city',
                users.user_nick = '$nick',
                users.user_pass = '$hash',
                users.user_email = '$email'
                WHERE users.id = '$id'
               ");
          }else{//if password will not change
                $update_query = ("
                UPDATE users
                LEFT JOIN users_data ON users.id = users_data.user_id
                SET users_data.user_name = '$name',
                users_data.user_surname = '$surname',
                users_data.user_street = '$street',
                users_data.user_kod = '$postcode',
                users_data.user_city = '$city',
                users.user_nick = '$nick',
                users.user_email = '$email'
                WHERE users.id = '$id'
               ");
            }
        }else{//if group id changed or values in group changed (for random groups)
            if($hash != ''){//if password has to change
                $update_query = ("
                UPDATE users
                LEFT JOIN users_data ON users.id = users_data.user_id
                LEFT JOIN users_group ON users_data.user_id = users_group.u_id
                SET users_data.user_name = '$name',
                users_data.user_surname = '$surname',
                users_data.user_street = '$street',
                users_data.user_kod = '$postcode',
                users_data.user_city = '$city',
                users.user_nick = '$nick',
                users.user_pass = '$hash',
                users.user_group = '$group_id',
                users.user_email = '$email',
                users_group.group_id = '$group_id',
                users_group.group_name = '$group_name',
                users_group.change_config = '$change_config',
                users_group.add_user = '$add_user',
                users_group.edit_user = '$edit_user',
                users_group.delete_user = '$delete_user',
                users_group.add_menu = '$add_menu',
                users_group.edit_menu = '$edit_menu',
                users_group.delete_menu = '$delete_menu',
                users_group.add_category = '$add_category',
                users_group.edit_category = '$edit_category',
                users_group.delete_category = '$delete_category',
                users_group.add_post = '$add_post',
                users_group.edit_post = '$edit_post',
                users_group.delete_post = '$delete_post',
                users_group.show_admin = '$show_admin'
                WHERE users.id = '$id'
               ");
            }else{//if password will not change
                $update_query = ("
                UPDATE users
                LEFT JOIN users_data ON users.id = users_data.user_id
                LEFT JOIN users_group ON users_data.user_id = users_group.u_id
                SET users_data.user_name = '$name',
                users_data.user_surname = '$surname',
                users_data.user_street = '$street',
                users_data.user_kod = '$postcode',
                users_data.user_city = '$city',
                users.user_nick = '$nick',
                users.user_group = '$group_id',
                users.user_email = '$email',
                users_group.group_id = '$group_id',
                users_group.group_name = '$group_name',
                users_group.change_config = '$change_config',
                users_group.add_user = '$add_user',
                users_group.edit_user = '$edit_user',
                users_group.delete_user = '$delete_user',
                users_group.add_menu = '$add_menu',
                users_group.edit_menu = '$edit_menu',
                users_group.delete_menu = '$delete_menu',
                users_group.add_category = '$add_category',
                users_group.edit_category = '$edit_category',
                users_group.delete_category = '$delete_category',
                users_group.add_post = '$add_post',
                users_group.edit_post = '$edit_post',
                users_group.delete_post = '$delete_post',
                users_group.show_admin = '$show_admin'
                WHERE users.id = '$id'
               ");
            }
        }
        if(!$db->query($update_query)){
            die($db->error);
        }else{
            Session::setFlash(ATXT_USER_EDITED_PROPERLY,'ok');
            $db->close();
        }

    }//END editUser($userData);

  public static function loadCategoryParentListByTypeId(){//AMAx-0003
   $db = Model::getDB();
   $data = array();
   $list_query = ("SELECT * FROM categories_type WHERE type_has_items > 0");
   $success = $db->query($list_query);
   if(!$success){
    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
   }else{
     $found = $success->num_rows;
    if($found > 0){
      while($result = $success->fetch_assoc()){
       array_push($data, $result);
      }
      $db->close();
      return $data;
    }else{
     $db->close();
     return FALSE;
    }
   }
  }//end loadCategoryParentListByTypeId($id)

 public static function categoryFormatName($name = ''){//AMAx-0004
  $ct_name = '';
  switch ($name){
   case 'posts':
    $ct_name = AMENU_CATEGORY_TYPE_POSTS;
    break;
   case 'gallery':
    $ct_name = AMENU_CATEGORY_TYPE_GALLERY;
    break;
   case 'forms':
    $ct_name = AMENU_CATEGORY_TYPE_FORMS;
    break;
   case 'archives':
    $ct_name = AMENU_CATEGORY_TYPE_ARCHIVES;
    break;
   case 'news':
    $ct_name = AMENU_CATEGORY_TYPE_NEWS;
    break;
   case 'classes':
    $ct_name = AMENU_CATEGORY_TYPE_CLASSES;
    break;
   default:
    $ct_name = '???';
    break;
  }
  return $ct_name;
 }//END categoryFormatName($name = '')

    public static function downloadRegister($file_id){

        $db = Model::getDB();
        $check_query = ("SELECT * FROM files WHERE id = '$file_id'");
        $success = $db->query($check_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
        }else{
            $found = $success->num_rows;
            if($found > 0){

                $register_query = ("UPDATE files SET downloaded = downloaded+1 WHERE id = '$file_id'");
                $success = $db->query($register_query);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY.'[' . $db->error . ']');
                }

                return TRUE;

            }else{
                $db->close();
                return FALSE;
            }
        }

    }//END function downloadRegister($file_id)


}//end CLASS