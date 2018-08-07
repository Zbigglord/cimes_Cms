<?php
/**
 * Creator: BabooJaga
 * Date: 2016-03-26
 * Time: 16:51
 */

namespace App\Controllers\Admin;
use App\Models\Admin\Course;
use App\Models\Admin\File;
use App\Models\Admin\Form;
use App\Models\Admin\madminAjax;
use App\Models\Admin\Category;
use App\Models\Admin\Medium;
use App\Models\Admin\Menu;
use App\Models\Admin\Post;
use Core\Session;
use Core\Filesystem;
use App\Models\Admin\User;
use Core\Lang;

class adminAjax extends \Core\Controller{

     public function ajaxConvertMessage(){
         if(isset($_POST['message'])){
             $message = constant($_POST['message']);
             $code = $_POST['messagecode'];
             Session::setFlash($message, $code);
             $result = Session::getFlash();
             Session::unsetFlash();
             echo $result;
         }
     }

    public function ajaxGetMessage(){
        if(isset($_POST['message'])){
            $message = constant($_POST['message']);
            echo $message;
        }
    }

    public function ajaxSerializeUserdata(){//ACAx-0001

        if(isset($_POST['us_id']) && $_POST['us_id'] !==''){
            $uid = $_POST['us_id'];
        }else{
            $uid = '';
        }

        /* ADRESS PART */
        if(isset($_POST['add_name']) && $_POST['add_name'] !==''){
            $name = filter_var($_POST['add_name'], FILTER_SANITIZE_STRING);
        }else{
            $name = '';
        }
        if(isset($_POST['add_surname']) && $_POST['add_surname'] !==''){
            $surname = filter_var($_POST['add_surname'], FILTER_SANITIZE_STRING);
        }else{
            $surname = '';
        }
        if(isset($_POST['add_street']) && $_POST['add_street'] !==''){
            $street = filter_var($_POST['add_street'], FILTER_SANITIZE_STRING);
        }else{
            $street = '';
        }
        if(isset($_POST['add_postcode']) && $_POST['add_postcode'] !==''){
            $postcode = filter_var($_POST['add_postcode'], FILTER_SANITIZE_STRING);
        }else{
            $postcode = '';
        }
        if(isset($_POST['add_city']) && $_POST['add_city'] !==''){
            $city = filter_var($_POST['add_city'], FILTER_SANITIZE_STRING);
        }else{
            $city = '';
        }

        /* LOGIN PART */
        if(isset($_POST['add_nick']) && $_POST['add_nick'] !==''){
            $nick = filter_var($_POST['add_nick'], FILTER_SANITIZE_STRING);
        }else{
            $nick = '';
        }
        if(isset($_POST['add_email']) && $_POST['add_email'] !==''){
            $email = filter_var($_POST['add_email'], FILTER_SANITIZE_EMAIL);
        }else{
            $email = '';
        }
        if(isset($_POST['add_pass']) && $_POST['add_pass'] !==''){
            $pass = filter_var($_POST['add_pass'], FILTER_SANITIZE_STRING);
        }else{
            $pass = '';
        }
        if(isset($_POST['repeat_pass']) && $_POST['repeat_pass'] !==''){
            $repeat_pass = '';
        }else{
            $repeat_pass = '';
        }

        /* ROLE PART */
        if(isset($_POST['user_groups'])){
            $group_name = $_POST['user_groups'];
            switch($group_name){
                case 'none':
                    $group_id = 100;
                    $change_config = 2;
                    $user_active = 2;
                    $show_admin = 2;
                    $add_user = 2;
                    $delete_user = 2;
                    $edit_user = 2;
                    $add_menu = 2;
                    $delete_menu = 2;
                    $edit_menu = 2;
                    $add_category = 2;
                    $delete_category = 2;
                    $edit_category = 2;
                    $add_post = 2;
                    $delete_post = 2;
                    $edit_post = 2;
                     break;
                case 'super':
                    $group_id = 1;
                    $change_config = 1;
                    $user_active = 1;
                    $show_admin = 1;
                    $add_user = ($_POST['us_add'] == 1) ? 1 : 0;
                    $delete_user = ($_POST['us_delete'] == 1) ? 1 : 0;
                    $edit_user = ($_POST['us_edit'] == 1) ? 1 : 0;
                    $add_menu = ($_POST['menu_add'] == 1) ? 1 : 0;
                    $delete_menu = ($_POST['menu_delete'] == 1) ? 1 : 0;
                    $edit_menu = ($_POST['menu_edit'] == 1) ? 1 : 0;
                    $add_category = ($_POST['category_add'] == 1) ? 1 : 0;
                    $delete_category = ($_POST['category_delete'] == 1) ? 1 : 0;
                    $edit_category = ($_POST['category_edit'] == 1) ? 1 : 0;
                    $add_post = ($_POST['article_add'] == 1) ? 1 : 0;
                    $delete_post = ($_POST['article_delete'] == 1) ? 1 : 0;
                    $edit_post = ($_POST['article_edit'] == 1) ? 1 : 0;
                    break;

                case 'admin':
                    $group_id = 2;
                    $change_config = 1;
                    $user_active = 1;
                    $show_admin = 1;
                    $add_user = ($_POST['us_add'] == 1) ? 1 : 0;
                    $delete_user = ($_POST['us_delete'] == 1) ? 1 : 0;
                    $edit_user = ($_POST['us_edit'] == 1) ? 1 : 0;
                    $add_menu = ($_POST['menu_add'] == 1) ? 1 : 0;
                    $delete_menu = ($_POST['menu_delete'] == 1) ? 1 : 0;
                    $edit_menu = ($_POST['menu_edit'] == 1) ? 1 : 0;
                    $add_category = ($_POST['category_add'] == 1) ? 1 : 0;
                    $delete_category = ($_POST['category_delete'] == 1) ? 1 : 0;
                    $edit_category = ($_POST['category_edit'] == 1) ? 1 : 0;
                    $add_post = ($_POST['article_add'] == 1) ? 1 : 0;
                    $delete_post = ($_POST['article_delete'] == 1) ? 1 : 0;
                    $edit_post = ($_POST['article_edit'] == 1) ? 1 : 0;
                    break;

                case 'moderator':
                    $group_id = 3;
                    $change_config = 0;
                    $user_active = ($_POST['is_active'] == 1) ? 1 : 0;;
                    $show_admin = 1;
                    $add_user = ($_POST['us_add'] == 1) ? 1 : 0;
                    $delete_user = 0;
                    $edit_user = ($_POST['us_edit'] == 1) ? 1 : 0;
                    $add_menu = 0;
                    $delete_menu = 0;
                    $edit_menu = 0;
                    $add_category = 0;
                    $delete_category = 0;
                    $edit_category = 0;
                    $add_post = 0;
                    $delete_post = ($_POST['article_delete'] == 1) ? 1 : 0;
                    $edit_post = ($_POST['article_edit'] == 1) ? 1 : 0;
                    break;

                case 'editor':
                    $group_id = 4;
                    $change_config = 0;
                    $user_active = ($_POST['is_active'] == 1) ? 1 : 0;
                    $show_admin = 1;
                    $add_user = 0;
                    $delete_user = 0;
                    $edit_user = 0;
                    $add_menu = ($_POST['menu_add'] == 1) ? 1 : 0;
                    $delete_menu = ($_POST['menu_delete'] == 1) ? 1 : 0;
                    $edit_menu = ($_POST['menu_edit'] == 1) ? 1 : 0;
                    $add_category = ($_POST['category_add'] == 1) ? 1 : 0;
                    $delete_category = ($_POST['category_delete'] == 1) ? 1 : 0;
                    $edit_category = ($_POST['category_edit'] == 1) ? 1 : 0;
                    $add_post = ($_POST['article_add'] == 1) ? 1 : 0;
                    $delete_post = ($_POST['article_delete'] == 1) ? 1 : 0;
                    $edit_post = ($_POST['article_edit'] == 1) ? 1 : 0;
                    break;

                case 'regular_user':
                    $group_id = 5;
                    $change_config = 0;
                    $user_active = ($_POST['is_active'] == 1) ? 1 : 0;
                    $show_admin = 0;
                    $add_user = 0;
                    $delete_user = 0;
                    $edit_user = 0;
                    $add_menu = 0;
                    $delete_menu = 0;
                    $edit_menu = 0;
                    $add_category = 0;
                    $delete_category = 0;
                    $edit_category = 0;
                    $add_post = ($_POST['article_add'] == 1) ? 1 : 0;
                    $delete_post = ($_POST['article_delete'] == 1) ? 1 : 0;
                    $edit_post = ($_POST['article_edit'] == 1) ? 1 : 0;
                    break;

                case 'random_role':
                    $group_id = 6;
                    $change_config = 0;
                    $user_active = ($_POST['is_active'] == 1) ? 1 : 0;
                    $show_admin = ($_POST['cs_backend'] == 1) ? 1 : 0;
                    $add_user = ($_POST['us_add'] == 1) ? 1 : 0;
                    $delete_user = ($_POST['us_delete'] == 1) ? 1 : 0;
                    $edit_user = ($_POST['us_edit'] == 1) ? 1 : 0;
                    $add_menu = ($_POST['menu_add'] == 1) ? 1 : 0;
                    $delete_menu = ($_POST['menu_delete'] == 1) ? 1 : 0;
                    $edit_menu = ($_POST['menu_edit'] == 1) ? 1 : 0;
                    $add_category = ($_POST['category_add'] == 1) ? 1 : 0;
                    $delete_category = ($_POST['category_delete'] == 1) ? 1 : 0;
                    $edit_category = ($_POST['category_edit'] == 1) ? 1 : 0;
                    $add_post = ($_POST['article_add'] == 1) ? 1 : 0;
                    $delete_post = ($_POST['article_delete'] == 1) ? 1 : 0;
                    $edit_post = ($_POST['article_edit'] == 1) ? 1 : 0;
                    break;
            }
        }

        $user_data = array(
            'uid' => $uid,
            'name' => $name,
            'surname' => $surname,
            'street' => $street,
            'postcode' => $postcode,
            'city' => $city,
            'nick' => $nick,
            'email' =>  $email,
            'pass' => $pass,
            'group_name' => $group_name,
            'group_id' => $group_id,
            'change_config' => $change_config,
            'user_active' => $user_active,
            'show_admin' => $show_admin,
            'add_user' => $add_user,
            'delete_user' => $delete_user,
            'edit_user' => $edit_user,
            'add_menu' => $add_menu,
            'delete_menu' => $delete_menu,
            'edit_menu' => $edit_menu,
            'add_category' => $add_category,
            'delete_category' => $delete_category,
            'edit_category' => $edit_category,
            'add_post' => $add_post,
            'delete_post' => $delete_post,
            'edit_post' => $edit_post
        );
        return $user_data;

    }//end function ajaxAddNew()

    public function ajaxAddNew(){//AMAx-0002
        $user_data = self::ajaxSerializeUserdata();
        madminAjax::addNewUser($user_data);
        $message = Session::getFlash();
        echo $message;
    }//end function ajaxAddNew()

    public function ajaxEditUser(){//AMAx-0003
        $user_data = self::ajaxSerializeUserdata();
        madminAjax::editUser($user_data);
        $message = Session::getFlash();
        echo $message;
    }//end function ajaxEditUser()

    /**
     * only to get category type id from select and set it in session, so we can list categories depends on type in select
     */
    public function ajaxReturnCtypeId(){//AMAx-0004
        if(isset($_POST['typeid'])){
            $ctype_id = $_POST['typeid'];
        }else{
            $ctype_id = 0;
        }
        $_SESSION['cat_type_id'] = $ctype_id;
    }

    public function ajaxUploadFile(){//AMAx-0005
        if($_FILES){
            $data = $_FILES['images'];
            $img = Filesystem::uploadImage($data);
            if($img != NULL && $img != FALSE){
                $image_path = $img;
                echo $image_path;
            }else{
                $image_path = '';
                echo $image_path;
            }
        }
    }

    public function ajaxUploadFileCourse(){//AMAx-0006
        if($_FILES){
            $data = $_FILES['images'];
            $img = Filesystem::uploadImageCourse($data);
            if($img != NULL && $img != FALSE){
                $image_path = $img;
                echo $image_path;
            }else{
                $image_path = '';
                echo $image_path;
            }
        }
    }

    public function ajaxSerializeCategoryData(){//AMAx-0007
        $data = array();
        if($_POST){
            foreach($_POST as $key => $value){
                $data[$key] = $value;
            }
        }
          return $data;
    }

    public function ajaxEditCategory(){//AMAx-0008
        $data = self::ajaxSerializeCategoryData();
        $updated = Category::editCategory($data);
        if($updated == true){
            echo 'ok';
        }
    }

    public function ajaxSetMenuitemType(){//AMAx-0009
        $categories_type = array(1,2);//no more array just 1,3 and 5
        $categories_tree = array(3,4);
        $single_category = array(5,6);
        $single_article = 7;
        $external_link = 8;
        $single_class = 9;
        $submenu = 10;
        $controller = 11;
        $form = 12;
        $gallery = 13;
        $file = 14;
       if(isset($_POST['data']) && $_POST['data'] != 'none'){
           $needed_id = $_POST['data'];
           if(in_array($needed_id,$categories_type)){$needed_id = 'cat_type';}
           if(in_array($needed_id,$categories_tree)){$needed_id = 'cat_tree';}
           if(in_array($needed_id,$single_category)){$needed_id = 'single_cat';}
           switch($needed_id){
               case 'cat_type': //only shows first(parent) categories from given type on frontend
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $list = madminAjax::loadCategoryParentListByTypeId();
                   if($list != FALSE){
                       foreach($list as $element){
                           $ct_name = madminAjax::categoryFormatName($element["type_name"]);
                           echo '<option data-subtext="" value="'.$element["type_id"].'" data-tokens="">'.$ct_name.'</option>';
                       }
                   }else{
                       echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_MENU_EMPTY.'</option>';
                   }
                   echo '</select>';
                   echo '</label>';
                   break;
               case 'cat_tree':
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $list_all = Category::getAllCategories();
                   Category::recursivePrint($list_all);
                   echo '</select>';
                   echo '</label>';
                   break;
               case 'single_cat'://shows list or grid of all articles from single category
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $list_all = Category::getAllCategories();
                   Category::recursivePrint($list_all);
                   echo '</select>';
                   echo '</label>';
                   break;
               case $single_article://you can pick any single post or course or so
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $art_list = Post::listAllArticles();
                   if($art_list != FALSE){
                       foreach($art_list as $post){
                           $category_name = Post::getCategoryName($post['post_category_id']);
                           echo '<option data-subtext="'.$category_name.'" value="'.$post["post_id"].'" data-tokens="'.$post["post_title"].'">'.$post["post_title"].'</option>';
                       }
                   }
                   echo '</select>';
                   echo '</label>';
                   break;
               case $external_link:
                    echo '<div class="form-group no-margin">';
                    echo '<label for="add_external_link" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_TYPE_EXTERNAL_LINK.'</label>';
                    echo '<input type="text" class="form-control" id="add_external_link" name="add_external_link"  placeholder="http://"/>';
                    echo '</div>';
                   break;
               case $single_class://you can pick single coursecimesCMS
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $course_list = Course::listAllCourses();
                   if($course_list != FALSE){
                       foreach($course_list as $course){
                           $category_name = Course::getCategoryName($course['course_category_id']);
                           echo '<option data-subtext="'.$category_name.'" value="'.$course["courses_display_data_id"].'" data-tokens="'.$course["courses_display_data_title"].'">'.$course["courses_display_data_title"].'</option>';
                       }
                   }
                   echo '</select>';
                   echo '</label>';
                   break;
               case $submenu://you can pick single course
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   echo '<option data-subtext="'.MENUITEM_TYPE_NO_LINK.'" value="submenu" data-tokens="">'.MENUITEM_TYPE_NO_LINK.'</option>';
                   echo '</select>';
                   echo '</label>';
                   break;
               case $controller: //direct link to controller
                   echo '<div class="form-group no-margin">';
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_TYPE_CONTROLLER.'</label>';
                   echo '<input type="text" class="form-control" id="menuitem_links_to_what" name="menuitem_links_to_what"  placeholder="/users/dashboard/"/>';
                   echo '</div>';
                   break;
               case $form: //direct link to form
                   echo '<label for="menuitem_links_to_form" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_form" name="menuitem_links_to_form" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $forms_list = Form::getForms();
                   if($forms_list != FALSE){
                       foreach($forms_list as $frm){
                           echo '<option data-subtext="'.$frm['form_id_char'].'" value="'.$frm['form_id'].'" data-tokens="'.$frm['form_title'].'">'.$frm['form_title'].'</option>';
                       }
                   }
                   echo '</select>';
                   echo '</label>';
                   break;
               case $gallery: //direct link to single gallery
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $galleries_list = Medium::getGalleriesList();
                   if($galleries_list != FALSE){
                       foreach($galleries_list as $gal){

                           $gal_cat_name = Category::getCategoryByID($gal['gallery_category_id'],2);
                           echo '<option data-subtext="'.$gal_cat_name['name'].'" value="'.$gal['gallery_id'].'" data-tokens="'.$gal['gallery_name'].','.$gal_cat_name['name'].'">'.$gal['gallery_name'].'</option>';
                       }
                   }
                   echo '</select>';
                   echo '</label>';
                   break;
               case $file: //direct link to single file
                   echo '<label for="menuitem_links_to_what" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.MENUITEM_SELECT_LINK.'</label>';
                   echo '<select class="selectpicker" id="menuitem_links_to_what" name="menuitem_links_to_what" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
                   echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
                   $file_list = File::getFileList();
                   var_dump($file_list);
                   if($file_list != FALSE){
                       foreach($file_list as $file){
                           echo '<option data-subtext="'.$file['category_name'].'" value="'.$file['id'].'" data-tokens="'.$file['title'].','.$file['name'].'">'.$file['name'].'</option>';
                       }
                   }
                   echo '</select>';
                   echo '</label>';
                   break;
               default:
                   echo 'error';
                   break;
           }
       }
    }//end public function ajaxSetMenuitemType()

    public function ajaxSetMenuitemParent(){//AMAx-0010
       if(isset($_POST['parent_menu_id']) && $_POST['parent_menu_id'] != ''){
           $menuitems_list = Menu::menuitemGetList(0,$_POST['parent_menu_id']);
           echo '<label for="menuitem_setmenuposition_parent" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.AMENU_MENUITEM_MENU_POSITION_SELECT.'</label>';
           echo '<select class="selectpicker" id="menuitem_setmenuposition_parent" name="menuitem_setmenuposition_parent" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true">';
           echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
           echo '<option data-subtext="" value="main" data-tokens="">'.AMENU_MAIN_ITEM.'</option>';
           Menu::printMenuList($menuitems_list);
          echo '</select>';
           echo '</label>';
       }
    }//END function ajaxSetMenuitemParent()

    public function ajaxUploadFileLogo(){//AMAx-0011
        if($_FILES){
            $data = $_FILES['images'];
            $img = Filesystem::uploadImageLogo($data);
            if($img != NULL && $img != FALSE){
                $image_path = $img;
                echo $image_path;
            }else{
                $image_path = '';
                echo $image_path;
            }
        }
    }//END function ajaxUploadFileLogo()

    public function ajaxCheckStudentAvai(){//AMAx-0012
        if($_POST){
            $data = array();
            foreach($_POST as $key => $value){
                $data[$key] = $value;
            }
            $student_avaiable = User::checkStudentEx($data);
            if($student_avaiable === TRUE){
              echo 'true';
            }else{
                echo 'false';
            }
        }
    }//END function ajaxCheckStudentAvai()

    public function ajaxGetAllUsers(){//AMAx-0012
     $users = User::listAll();
     $students = User::studentList();
        $used = array(); //may be multiple same students due to different group issue so need to check if already showed in select
        echo '<label for="users-list" class="tooltip-labels" data-toggle="tooltip" data-placement="top" title="'.TEXT_REQUIRED.'"> * '.AMENU_MENUITEM_MENU_POSITION_SELECT.'</label>';
        echo '<select class="selectpicker" id="users-list" name="users-list[]" data-live-search="true" data-live-search-placeholder="'.AMENU_SEARCH.'" data-show-tick="true" multiple data-selected-text-format="count" data-actionsBox="true">';
        echo '<option data-subtext="" value="none" data-tokens="">'.AMENU_PICK_ONE.'</option>';
        echo '<optgroup label="users">';
         foreach($users as $user){
             if($user['id'] == 1){

             }else{
                 $name = ($user['user_name'] == '') ? 'no data' : $user['user_name'];
                 $surname = ($user['user_surname'] == '') ? 'no data' : $user['user_surname'];
                 echo '<option data-subtext="'.$user['user_email'].'" value="u_'.$user['id'].'" data-tokens="'.$name.''.$surname.' '.$user['user_email'].'">'.$surname.' '.$name.'</option>';
             }
         }
        echo '</optgroup>';
        echo '<optgroup label="students">';
        foreach($students as $student){
            if(in_array($student['student_email'],$used)){

            }else{
                $name = ($student['student_name'] == '') ? 'no data' : $student['student_name'];
                $surname = ($student['student_surname'] == '') ? 'no data' : $student['student_surname'];
                echo '<option data-subtext="'.$student['student_email'].'" value="s_'.$student['id'].'" data-tokens="'.$name.''.$surname.' '.$student['student_email'].'">'.$surname.' '.$name.'</option>';
                array_push($used, $student['student_email']);
            }
        }
        echo '</optgroup>';
        echo '</select>';
        echo '</label>';
    }//END function ajaxCheckStudentAvai()

    public function ajaxUpladMultiCheck(){//FAJAx 00001

        if($_FILES){//check if filesize is ok as mimetype is checked by form itself and filenames will be chenged during upload
            $data = $_FILES['images'];
            $temptype = $data['type'];
            $tempname = $data['name'];
            $type = explode('/',$temptype);
            $size = $data['size'];

            if($type[0] == 'image'){//check file as image

                if($size < 0){//break all operations - file is too big

                    echo '1#'.$tempname.'#'.$type[0].'#'.$_POST['tmppath'].'#'.$size;
                    exit;

                }else{//continue uploading

                    echo '0#'.$tempname.'#'.$type[0].'#'.$_POST['tmppath'].'#'.$size;
                    exit;

                }

            }

            if($type[0] == 'video'){//check file as video

                if($size < 0){//break all operations - file is too big

                    echo '1#'.$tempname.'#'.$type[0].'#'.$_POST['tmppath'].'#'.$size;
                    exit;

                }else{

                    echo '0#'.$tempname.'#'.$type[0].'#'.$_POST['tmppath'].'#'.$size;
                    exit;

                }

            }//end checking


        }
    }//END ajaxCheckFiles()

    public function ajaxUploadFileSlider(){//AMAx-0020
        if($_FILES){
            $data = $_FILES['images'];
            $path = 'sliders/'.$_POST['slider_id'];
            $img = Filesystem::uploadImageVarious($data,$path);
            if($img != NULL && $img != FALSE){
                $image_path = $img;
                echo $image_path;
            }else{
                $image_path = '';
                echo $image_path;
            }
        }
    }//END ajaxUploadFileSlider()

    public function ajaxUploadFileTinyMCE(){//AMAx-0020a
        if($_FILES){
            $data = $_FILES['images'];
            $path = 'editor';
            $img = Filesystem::uploadImageVarious($data,$path);
            if($img != NULL && $img != FALSE){
                $image_path = $img;
                echo $image_path;
            }else{
                $image_path = '';
                echo $image_path;
            }
        }
    }//END ajaxUploadFileSlider()

    public function ajaxUploadFileGallery(){//AMAx-0021
        if($_FILES){
            $data = $_FILES['images'];
            $path = 'galleries/'.$_POST['gallery_category_id'].'/'.$_POST['gallery_id'];
            $img = Filesystem::uploadImageVarious($data,$path);
            if($img != NULL && $img != FALSE){
                $image_path = $img;
                echo $image_path;
            }else{
                $image_path = '';
                echo $image_path;
            }
        }
    }//END ajaxUploadFileGallery()


    public function ajaxLoadInputHtml(){//AMAx-0022

        if(isset($_POST['input_type']) && $_POST['input_type'] != 0){

          $type = $_POST['input_type'];
          $number =  $_POST['input_number'];

            switch($type){

                case 1://hidden

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_HIDDEN.' (hidden)</legend>';
                        self::hiddenInputToHTML($number);
                    echo '</fieldset>';

                    break;

                case 2://text

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_TXT.' (text)</legend>';
                    self::standartInputToHTML($number);
                    echo '</fieldset>';

                    break;

                case 3://email

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_EMAIL.' (email)</legend>';
                    self::standartInputToHTML($number,'email');
                    echo '</fieldset>';

                    break;

                case 4://password

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_PASSWORD.' (password)</legend>';
                    self::standartInputToHTML($number,'password');
                    echo '</fieldset>';

                    break;

                case 5://textarea

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_TEXTAREA.' (textarea)</legend>';
                    self::standartInputToHTML($number,'textarea');
                    echo '</fieldset>';

                    break;

                case 6://tel

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_TEL.' (tel)</legend>';
                    self::standartInputToHTML($number,'tel');
                    echo '</fieldset>';

                    break;

                case 7://url

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_URL.' (url)</legend>';
                    self::standartInputToHTML($number,'url');
                    echo '</fieldset>';

                    break;

                case 8://color

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_COLOR.' (color)</legend>';
                    self::colorInputToHTML($number);
                    echo '</fieldset>';

                    break;

                case 9://number

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_NUMBER.' (number)</legend>';
                    self::numberInputToHTML($number);
                    echo '</fieldset>';

                    break;

                case 10://date

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_DATE.' (date)</legend>';
                    self::standartInputToHTML($number,'date');
                    echo '</fieldset>';

                    break;

                case 11://checkbox

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_CHECKBOX.' (checkbox)</legend>';
                    self::standartInputToHTML($number,'checkbox');
                    echo '</fieldset>';

                    break;

                case 12://radio

                    break;

                case 13://select

                    echo '<fieldset>';
                    echo '<legend>'.ATXT_FORMS_INPUT_TYPE_SELECT.' (select)</legend>';
                    self::selectInputToHTML($number);
                    echo '</fieldset>';

                    break;

                case 14://file

                    break;

                case 15://submit

                    break;

            }


        }else{

            echo 'dupa';

        }

    }//END function ajaxLoadInputHtml()

    public function standartInputToHTML($number, $type = 'text'){

        echo '<div class="new-input-row">';
        echo '<div class="row">';
        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label_'.$number.'" name="input-label_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-class_'.$number.'" name="input-label-class_'.$number.'" value="tooltip-labels" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-placement_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-placement_'.$number.'" name="input-label-data-placement_'.$number.'" value="top" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-toggle_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TOGGLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-toggle_'.$number.'" name="input-label-data-toggle_'.$number.'" value="tooltip" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-original-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-original-title_'.$number.'" name="input-label-data-original-title_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-title_'.$number.'" name="input-label-title_'.$number.'" value="" />';
        echo '</div>';
        echo '</div>';

        echo '<div class="row margin-top">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-name_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_NAME.'" data-original-title="'.ATXT_FORMS_INPUT_NAME.'">';
        echo ''.ATXT_FORMS_INPUT_NAME.' *';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-name_'.$number.'" name="input-name_'.$number.'" value="" required/>';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-id_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_ID.'" data-original-title="'.ATXT_FORMS_INPUT_ID.'">';
        echo ''.ATXT_FORMS_INPUT_ID.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-id_'.$number.'" name="input-id_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-class_'.$number.'" name="input-class_'.$number.'" value="form-control" />';
        echo '</div>';
        if($type != 'checkbox'){
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-value_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_VALUE.'" data-original-title="'.ATXT_FORMS_INPUT_VALUE.'">';
        echo ''.ATXT_FORMS_INPUT_VALUE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-value_'.$number.'" name="input-value_'.$number.'" value="" />';
        echo '</div>';
        echo '</div>';
        }
        if($type != 'checkbox'){
        echo '<div class="row margin-top">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-placeholder_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_PLACEHOLDER.'" data-original-title="'.ATXT_FORMS_INPUT_PLACEHOLDER.'">';
        echo ''.ATXT_FORMS_INPUT_PLACEHOLDER.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-placeholder_'.$number.'" name="input-placeholder_'.$number.'" value="" />';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-maxlength_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_MAXLENGTH.'" data-original-title="'.ATXT_FORMS_INPUT_MAXLENGTH.'">';
        echo ''.ATXT_FORMS_INPUT_MAXLENGTH.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-maxlength_'.$number.'" name="input-maxlength_'.$number.'" value="" />';
        echo '</div>';
        }else{
            echo '</div>';
            echo '<div class="row">';
        }

        if($type != 'checkbox'){

            echo '<div class="col-lg-3 no-padding-left padded-top-printed-checkbox">';
            echo '<input type="checkbox" class="" id="input-required_'.$number.'" name="input-required_'.$number.'" style="float: left; margin-right: 5px;" />';
            echo '<label for="input-required_'.$number.'">'.ATXT_FORMS_INPUT_IS_REQUIRED.'</label>';
            echo '</div>';
            echo '<div class="col-lg-3 no-padding-left padded-top-printed-checkbox">';
            echo '<input type="checkbox" class="" id="input-disabled_'.$number.'" name="input-disabled_'.$number.'" style="float: left; margin-right: 5px;" />';
            echo '<label for="input-disabled_'.$number.'">'.ATXT_FORMS_INPUT_DISABLED.'</label>';
            echo '</div>';

        }else{

            echo '<div class="col-lg-2 no-padding-left padded-top-printed-checkbox">';
            echo '<input type="checkbox" class="" id="input-required_'.$number.'" name="input-required_'.$number.'" style="float: left; margin-right: 5px;" />';
            echo '<label for="input-required_'.$number.'">'.ATXT_FORMS_INPUT_IS_REQUIRED.'</label>';
            echo '</div>';
            echo '<div class="col-lg-2 no-padding-left padded-top-printed-checkbox">';
            echo '<input type="checkbox" class="" id="input-checked_'.$number.'" name="input-checked_'.$number.'" style="float: left; margin-right: 5px;" />';
            echo '<label for="input-checked_'.$number.'">'.ATXT_FORMS_INPUT_CHECKED.'</label>';
            echo '</div>';
            echo '<div class="col-lg-2 no-padding-left padded-top-printed-checkbox">';
            echo '<input type="checkbox" class="" id="input-disabled_'.$number.'" name="input-disabled_'.$number.'" style="float: left; margin-right: 5px;" />';
            echo '<label for="input-disabled_'.$number.'">'.ATXT_FORMS_INPUT_DISABLED.'</label>';
            echo '</div>';

        }

        echo '</div>';

        echo '</div>';

        if($type == 'checkbox'){
            echo '</div>';
        }

            echo '<div class="row margin-top">';
            echo '<div class="col-lg-12 no-padding-left">';
            echo '<label for="input-display-type" style="display: block;">'.ATXT_FORMS_INPUT_DISPLAY_PICK.'</label>';
            echo '<select class="select-picker input-100-padded" id="input-display-type" name="input-display-type" data-live-search="false" data-show-tick="true">';
            echo '<option data-subtext="inline" value="1" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_INLINE.'</option>';
            echo '<option data-subtext="block" value="2" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_BLOCK.'</option>';
            echo '</select>';
            echo '</div>';
            echo '</div>';

    }//END

    public function hiddenInputToHTML($number){

        echo '<div class="new-input-row">';
        echo '<div class="row">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-name_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_NAME.'" data-original-title="'.ATXT_FORMS_INPUT_NAME.'">';
        echo ''.ATXT_FORMS_INPUT_NAME.' *';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-name_'.$number.'" name="input-name_'.$number.'" value="" required/>';
        echo '</div>';
        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-id_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_ID.'" data-original-title="'.ATXT_FORMS_INPUT_ID.'">';
        echo ''.ATXT_FORMS_INPUT_ID.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-id_'.$number.'" name="input-id_'.$number.'" value="" />';
        echo '</div>';
        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-class_'.$number.'" name="input-class_'.$number.'" value="form-control" />';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-value_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_VALUE.'" data-original-title="'.ATXT_FORMS_INPUT_VALUE.'">';
        echo ''.ATXT_FORMS_INPUT_VALUE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-value_'.$number.'" name="input-value_'.$number.'" value="" />';
        echo '</div>';
        echo '</div>';
        echo '</div>';

    }//END

    public function colorInputToHTML($number){

        echo '<div class="new-input-row">';
        echo '<div class="row">';
        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label_'.$number.'" name="input-label_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-class_'.$number.'" name="input-label-class_'.$number.'" value="tooltip-labels" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-placement_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-placement_'.$number.'" name="input-label-data-placement_'.$number.'" value="top" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-toggle_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TOGGLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-toggle_'.$number.'" name="input-label-data-toggle_'.$number.'" value="tooltip" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-original-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-original-title_'.$number.'" name="input-label-data-original-title_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-title_'.$number.'" name="input-label-title_'.$number.'" value="" />';
        echo '</div>';
        echo '</div>';

        echo '<div class="row margin-top">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-name_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_NAME.'" data-original-title="'.ATXT_FORMS_INPUT_NAME.'">';
        echo ''.ATXT_FORMS_INPUT_NAME.' *';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-name_'.$number.'" name="input-name_'.$number.'" value="" required/>';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-id_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_ID.'" data-original-title="'.ATXT_FORMS_INPUT_ID.'">';
        echo ''.ATXT_FORMS_INPUT_ID.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-id_'.$number.'" name="input-id_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-class_'.$number.'" name="input-class_'.$number.'" value="form-control" />';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-value_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_VALUE.'" data-original-title="'.ATXT_FORMS_INPUT_VALUE.'">';
        echo ''.ATXT_FORMS_INPUT_VALUE.'';
        echo '</label>';
        echo '<input type="color" class="form-control" id="input-value_'.$number.'" name="input-value_'.$number.'" value="#D5E9F9" />';
        echo '</div>';
        echo '</div>';

        echo '</div>';

        echo '<div class="new-input-row">';
        echo '<div class="row">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-display-type">'.ATXT_FORMS_INPUT_DISPLAY_PICK.'</label>';
        echo '<select class="select-picker" id="input-display-type" name="input-display-type" data-live-search="false" data-show-tick="true">';
        echo '<option data-subtext="inline" value="1" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_INLINE.'</option>';
        echo '<option data-subtext="block" value="2" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_BLOCK.'</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

    }//END

    public function numberInputToHTML($number){

        echo '<div class="new-input-row">';
        echo '<div class="row">';
        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label_'.$number.'" name="input-label_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-class_'.$number.'" name="input-label-class_'.$number.'" value="tooltip-labels" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-placement_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-placement_'.$number.'" name="input-label-data-placement_'.$number.'" value="top" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-toggle_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TOGGLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-toggle_'.$number.'" name="input-label-data-toggle_'.$number.'" value="tooltip" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-original-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-original-title_'.$number.'" name="input-label-data-original-title_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-title_'.$number.'" name="input-label-title_'.$number.'" value="" />';
        echo '</div>';
        echo '</div>';

        echo '<div class="row margin-top">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-name_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_NAME.'" data-original-title="'.ATXT_FORMS_INPUT_NAME.'">';
        echo ''.ATXT_FORMS_INPUT_NAME.' *';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-name_'.$number.'" name="input-name_'.$number.'" value="" required />';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-id_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_ID.'" data-original-title="'.ATXT_FORMS_INPUT_ID.'">';
        echo ''.ATXT_FORMS_INPUT_ID.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-id_'.$number.'" name="input-id_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-class_'.$number.'" name="input-class_'.$number.'" value="form-control" />';
        echo '</div>';

        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-value_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_VALUE.'" data-original-title="'.ATXT_FORMS_INPUT_VALUE.'">';
        echo ''.ATXT_FORMS_INPUT_VALUE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-value_'.$number.'" name="input-value_'.$number.'" value="1" />';
        echo '</div>';
        echo '</div>';

        echo '<div class="row margin-top">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-min_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_MIN.'" data-original-title="'.ATXT_FORMS_INPUT_MIN.'">';
        echo ''.ATXT_FORMS_INPUT_MIN.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-min_'.$number.'" name="input-min_'.$number.'" value="1" />';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-max_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_MAX.'" data-original-title="'.ATXT_FORMS_INPUT_MAX.'">';
        echo ''.ATXT_FORMS_INPUT_MAX.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-max_'.$number.'" name="input-max_'.$number.'" value="10" />';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="inputstep_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_STEP.'" data-original-title="'.ATXT_FORMS_INPUT_STEP.'">';
        echo ''.ATXT_FORMS_INPUT_STEP.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-step_'.$number.'" name="input-step_'.$number.'" value="1" />';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left padded-top-printed-checkbox">';
        echo '<input type="checkbox" class="" id="input-required_'.$number.'" name="input-required_'.$number.'" style="float: left; margin-right: 5px;" />';
        echo '<label for="input-required">'.ATXT_FORMS_INPUT_IS_REQUIRED.'</label>';
        echo '</div>';
        echo '</div>';

        echo '</div>';

        echo '<div class="new-input-row">';
        echo '<div class="row">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-display-type">'.ATXT_FORMS_INPUT_DISPLAY_PICK.'</label>';
        echo '<select class="select-picker" id="input-display-type" name="input-display-type" data-live-search="false" data-show-tick="true">';
        echo '<option data-subtext="inline" value="1" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_INLINE.'</option>';
        echo '<option data-subtext="block" value="2" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_BLOCK.'</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

    }//END

    public function selectInputToHTML($number){

        echo '<div class="new-input-row">';

        echo '<div class="row margin-bottom">';
        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label_'.$number.'" name="input-label_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-class_'.$number.'" name="input-label-class_'.$number.'" value="tooltip-labels" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-placement_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_PLACEMENT.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-placement_'.$number.'" name="input-label-data-placement_'.$number.'" value="top" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-toggle_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TOGGLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TOGGLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-toggle_'.$number.'" name="input-label-data-toggle_'.$number.'" value="tooltip" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-data-original-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_ORIGINAL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-data-original-title_'.$number.'" name="input-label-data-original-title_'.$number.'" value="" />';
        echo '</div>';

        echo '<div class="col-lg-2 no-padding-left">';
        echo '<label for="input-label-title_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'" data-original-title="'.ATXT_FORMS_INPUT_LABEL_TITLE.'">';
        echo ''.ATXT_FORMS_INPUT_LABEL_TITLE.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-label-title_'.$number.'" name="input-label-title_'.$number.'" value="" />';
        echo '</div>';
        echo '</div>';

        echo '<div class="row">';
        echo '<div class="col-lg-4 no-padding-left">';
        echo '<label for="input-name_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_NAME.'" data-original-title="'.ATXT_FORMS_INPUT_NAME.'">';
        echo ''.ATXT_FORMS_INPUT_NAME.' *';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-name_'.$number.'" name="input-name_'.$number.'" value="" required />';
        echo '</div>';
        echo '<div class="col-lg-4 no-padding-left">';
        echo '<label for="input-id_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_ID.'" data-original-title="'.ATXT_FORMS_INPUT_ID.'">';
        echo ''.ATXT_FORMS_INPUT_ID.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-id_'.$number.'" name="input-id_'.$number.'" value="" required/>';
        echo '</div>';
        echo '<div class="col-lg-4 no-padding-left">';
        echo '<label for="input-class_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_CLASS.'" data-original-title="'.ATXT_FORMS_INPUT_CLASS.'">';
        echo ''.ATXT_FORMS_INPUT_CLASS.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-class_'.$number.'" name="input-class_'.$number.'" value="selectpicker" />';
        echo '</div>';
        echo '</div>';

        echo '<div class="row margin-top">';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-tick_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_TICK.'" data-original-title="'.ATXT_FORMS_INPUT_TICK.'">';
        echo ''.ATXT_FORMS_INPUT_TICK.'';
        echo '<select class="input-100-padded" id="input-tick_'.$number.'" name="input-tick_'.$number.'">';
        echo '<option value="0">'.AMENU_SELECT.'</option>';
         echo '<option value="1" selected>'.TEXT_YES.'</option>';
        echo '<option value="2">'.TEXT_NO.'</option>';
        echo '</select>';
        echo '</label>';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-live-search_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LIVE_SEARCH.'" data-original-title="'.ATXT_FORMS_INPUT_LIVE_SEARCH.'">';
        echo ''.ATXT_FORMS_INPUT_LIVE_SEARCH.'';
        echo '<select class="input-100-padded" id="input-live-search_'.$number.'" name="input-live-search_'.$number.'">';
        echo '<option value="0">'.AMENU_SELECT.'</option>';
        echo '<option value="1" selected>'.TEXT_YES.'</option>';
        echo '<option value="2">'.TEXT_NO.'</option>';
        echo '</label>';
        echo '</select>';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-display-type" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_DISPLAY_PICK.'" data-original-title="'.ATXT_FORMS_INPUT_DISPLAY_PICK.'">'.ATXT_FORMS_INPUT_DISPLAY_PICK;
        echo '<select class="select-picker input-100-padded" id="input-display-type" name="input-display-type" data-live-search="false" data-show-tick="true">';
        echo '<option data-subtext="inline" value="1" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_INLINE.'</option>';
        echo '<option data-subtext="block" value="2" data-tokens="">'.ATXT_FORMS_INPUT_DISPLAY_BLOCK.'</option>';
        echo '</label>';
        echo '</select>';
        echo '</div>';
        echo '<div class="col-lg-3 no-padding-left">';
        echo '<label for="input-live-search-placeholder_'.$number.'" class="tooltip-labels" data-placement="top" data-toggle="tooltip" title="'.ATXT_FORMS_INPUT_LIVE_SEARCH_PLACEHOLDER.'" data-original-title="'.ATXT_FORMS_INPUT_LIVE_SEARCH_PLACEHOLDER.'">';
        echo ''.ATXT_FORMS_INPUT_LIVE_SEARCH_PLACEHOLDER.'';
        echo '</label>';
        echo '<input type="text" class="form-control" id="input-live-search-placeholder_'.$number.'" name="input-live-search-placeholder_'.$number.'" value="search..." />';
        echo '</div>';
        echo '</div>';

    }//END

    public static function matchExt(){

        if(isset($_POST['filename'])){

        $file_name = $_POST['filename'];
        $temp = explode('.',$file_name);
        $ext = end($temp);
            $ext = Filesystem::matchImageToExt($ext);//if not in array of known name this will return 'file'
        echo '<img class="img-responsive thumbnail file-ico" src="/images/mime/'.strtolower($ext).'.png">'; 

        }

    }//END function matchExt()

    public function testPath(){
        $word = Filesystem::generateCaptcha();
        echo $word;
    }



} //end class
