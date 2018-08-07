<?php
/**
 * Created by PhpStorm.
 * User: BBJaga
 * Date: 2016-08-26
 * Time: 11:53
 */

namespace App\Models\Admin;

use \DOMDocument;
use Core\Lang;
use Core\Session;
use Core\Model;
use Core\Filesystem;

class Course extends Model{

    //IMAGES

    public static function uploadImage($images = array()){//AMCou-0001
        if(isset($images) && !empty($images)){
            $img_name = filter_var($images["name"], FILTER_SANITIZE_STRING);//sanitize names
            $img_name = mb_strtolower($img_name, 'UTF-8');//make all lowercase
            $img_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż'), array('a','c','e','l','o','z','z'), $img_name);//replace polish chars in names if exist
            $img_type = $images["type"];
            $img_temp_path = $images["tmp_name"];
            $img_error = $images["error"];
            $img_size = round(($images["size"] / 1024) /1024, 3);
            // $current_file = '../'.__DIR__;
            $new_image = array(
                'name' => $img_name,
                'type' => $img_type,
                'tmp' => $img_temp_path,
                'error' => $img_error,
                'size' => $img_size,
            );
            $new_path = '../public/upload/images/courses/'.$new_image['name'];
            if(!move_uploaded_file($new_image['tmp'], $new_path)){
                Session::setFlash(ATXT_CATEGORY_MOVE_IMAGE_ERROR,'error');
                return FALSE;
            }else{
                Session::setFlash(ATXT_CATEGORY_IMAGE_MOVED,'ok');
                $new_path = '/upload/images/courses/'.$new_image['name'];
                return $new_path;
            }
        }else{
            return NULL;
        }
    }

    public static function getCategoryName($id){//AMCou-0002
        $db = Model::getDB();
        if(isset($id) || !empty($id)){
            $name = '';
            $category_name = ("SELECT name FROM categories WHERE id = '$id' LIMIT 1");
            $result = $db->query($category_name);
            $found = $result-> num_rows;
            if($found < 1){
                Session::setFlash(AMENU_CLASS_ID_BAD,'error');
                $db->close();
                return false;
            }else{
                while( $cat = $result->fetch_assoc() ){
                    $name = $cat['name'];
                }
                $db->close();
                return $name;
            }
        }else{
            Session::setFlash(AMENU_CLASS_ID_BAD,'error');
            $db->close();
            return false;
        }
    }

    public static function listAllCourses(){//AMCou-0003
        $db = Model::getDB();
        $courses = array();
        $courses_list = ("SELECT * FROM courses LEFT JOIN courses_display_data ON courses.id = courses_display_data.courses_display_data_id LEFT JOIN courses_groups ON courses.id = courses_groups.courses_groups_id ORDER BY courses.id ASC");
        $result = $db->query($courses_list);
        $found = $result-> num_rows;
        if($found > 0){
            while( $course = $result->fetch_assoc() ){
                array_push($courses,$course);
            }
        }else{
            Session::setFlash(ATXT_NO_CLASSES,'ok');
            $db->close();
        }
        $db->close();
        return $courses;
    }

    public static function listAllCoursesCategories(){//AMCou-0004
        $db = Model::getDB();
        $cats = array();
        $cats_list = ("SELECT * FROM categories WHERE type = '6' ORDER BY id ASC");
        $result = $db->query($cats_list);
        $found = $result-> num_rows;
        if($found > 0){
            while( $cat = $result->fetch_assoc() ){
                array_push($cats,$cat);
            }
        }else{
            Session::setFlash(ATXT_NO_CLASSES,'ok');
            $db->close();
        }
        $db->close();
        return $cats;
    }

    public static function coursesAddNew($data = array()){//AMCou-0005
        $db = Model::getDB();
        if(!empty($data)){//if array of values if filled in nicely
            //courses
            $course_name = $data['course_name'];
            $course_category_id = $data['course_category_id'];
            //references course id
            $course_student_group_id = '';//to be set later
            $course_student_group_name = '';//to be set later
            $course_date_start = $data['course_date_start'];
            $course_date_end = $data['course_date_end'];
            //courses_display_data
            $courses_display_data_title = !empty($data['courses_display_data_title']) ? $data['courses_display_data_title'] : $course_name;//to be set later
            $courses_display_data_id = '';//to be set later
            $courses_display_data_description = $data['courses_display_data_description'];
            $courses_display_data_seo = $data['courses_display_data_seo'];
            $courses_display_data_metakey = $data['courses_display_data_metakey'];
            $courses_display_data_metadata = $data['courses_display_data_metadata'];
            $courses_display_data_image = $data['courses_display_data_image'];
            //courses_groups
            $courses_groups_id = '';//to be set later
            $courses_groups_name = '';//to be set later
            $courses_groups_foder = '';//to be set later
            $is_archive = 0;

            $make_group = $data['make_group'];

            //first must insert basic data end generate id for course for further usage
           $add_first_data = ("INSERT INTO courses (course_name, course_category_id, course_date_added, course_date_updated, course_date_start, course_date_end,is_archive) VALUES ('$course_name','$course_category_id',NOW(),NOW(),'$course_date_start','$course_date_end','$is_archive')");
           $success = $db->query($add_first_data);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{
                //now we must find this last added max id and get it into variables
                $last_id = 0;//just to make interpretter happy
                $get_last_id = ("SELECT MAX(id) FROM courses");
                $result = $db->query($get_last_id);
                while($found = $result->fetch_row()){
                    $last_id = $found[0];
                }

                $courses_display_data_id = $last_id;
                $course_student_group_id = ($make_group == 'yes') ? $courses_display_data_id : ''; // if make group of it is selected
                $courses_groups_id = $course_student_group_id;

                //now it is time to set names for groups and folders
                $group_name = filter_var($course_name, FILTER_SANITIZE_STRING);//sanitize names
                $group_name = stripslashes($group_name);
                $group_name = mb_strtolower($group_name, 'UTF-8');//make all lowercase
                $group_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż','ś'), array('a','c','e','l','o','z','z','s'), $group_name);//replace polish chars in names if exist remember to check if page coding is utf
                $group_name = explode(' ',$group_name);
                $temp_name= '';
                for($i=0; $i <= count($group_name); $i++){
                    if(empty($temp_name)){
                        $temp_name =  $group_name[$i];
                    }else{
                        $temp_name = $temp_name.'_'.$group_name[$i];
                    }
                }
                $course_student_group_name = ($make_group == 'yes') ? $temp_name.'_'.$course_date_start : ''; // if make group of it is selected
                $courses_groups_name = $course_student_group_name;
                $courses_groups_foder = $course_student_group_name;
                //end set names for groups and folders

                if($make_group == 'yes'){
                    $add_rest_of_first_data = ("UPDATE courses SET course_student_group_id = '$course_student_group_id', course_student_group_name = '$course_student_group_name' WHERE id = '$courses_display_data_id'");
                    $success1 = $db->query($add_rest_of_first_data);
                    if(!$success1){
                        die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                    }
                }
                $add_display_data = ("INSERT INTO courses_display_data (courses_display_data_title,courses_display_data_id,courses_display_data_description,courses_display_data_seo,courses_display_data_metakey,courses_display_data_metadata,courses_display_data_image ) VALUES ('$courses_display_data_title','$courses_display_data_id','$courses_display_data_description','$courses_display_data_seo','$courses_display_data_metakey','$courses_display_data_metadata','$courses_display_data_image')");
                $success2 = $db->query($add_display_data);
                if(!$success2){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }else{
                    if($make_group == 'yes'){
                        $root = dirname(dirname(dirname(dirname(__FILE__))));
                        $courses_groups_foder = $root.'/'.'public/upload/kursy/'.$courses_groups_foder;
                        mkdir($courses_groups_foder, 0777);
                        $add_groups_data = ("INSERT INTO courses_groups (courses_groups_id, courses_groups_name, courses_groups_folder) VALUES ('$courses_groups_id','$courses_groups_name','$courses_groups_foder')");
                        $success3 = $db->query($add_groups_data);
                        if(!$success3){
                            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                        }else{
                            $db->close();
                            return true;
                        }
                    }else{
                        $db->close();
                        return true;
                    }

                }
            }
        }
       return false;
    }//end coursesAddNew() function

    public static function courseLoadData($id){//AMCou-0006
      $db = Model::getDB();
      if(isset($id) || !empty($id)){
         $course = array();
         $edited_id = $id;
         $course_data = ("SELECT * FROM courses LEFT JOIN courses_display_data ON courses.id = courses_display_data.courses_display_data_id LEFT JOIN courses_groups ON courses.id = courses_groups.courses_groups_id WHERE courses.id = '$edited_id'");
          $result = $db->query($course_data);
          $found = $result-> num_rows;
          if(!$found){
              Session::setFlash(AMENU_CLASS_ID_BAD,'error');
              $db->close();
              return false;
          }else{
              while( $row = $result->fetch_assoc() ){
                foreach($row as $key => $value){
                    $course[$key] =  $value;
                }
              }
              $db->close();
              return $course;
          }
      }else{
          Session::setFlash(AMENU_CLASS_ID_BAD,'error');
          $db->close();
          return false;
      }
    }//end function courseLoadData($id)

    public static function courseEdit($data = array()){//AMCou-0007
        $db = Model::getDB();
        if(!empty($data)){
         $id = $data['edit_class_id'];
         $name = $data['class_name_edit'];
         $start = $data['class_start_edit'];
         $end = $data['class_end_edit'];
            //if make new group
            if($data['make_class_group'] == 'yes'){
                $student_group_id = $id;
                //now it is time to set names for groups and folders
                $group_name = filter_var($name, FILTER_SANITIZE_STRING);//sanitize names
                $group_name = stripslashes($group_name);
                $group_name = mb_strtolower($group_name, 'UTF-8');//make all lowercase
                $group_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż','ś'), array('a','c','e','l','o','z','z','s'), $group_name);//replace polish chars in names if exist remember to check if page coding is utf
                $group_name = explode(' ',$group_name);
                $temp_name= '';
                for($i=0; $i <= count($group_name); $i++){
                    if(empty($temp_name)){
                        $temp_name =  $group_name[$i];
                    }else{
                        $temp_name = $temp_name.'_'.$group_name[$i];
                    }
                }
                $group_name = $temp_name.'_'.$start; // if make group of it is selected
                $root = dirname(dirname(dirname(dirname(__FILE__))));
                $student_group_folder = $root.'/'.'public/upload/kursy/'.$group_name;
                mkdir($student_group_folder, 0777);
                $student_group_name = $group_name;
            }//end first if make new group

            $title = $data['class_title_edit'];
            $seo = $data['class_seo_edit'];
            $metkeys = $data['class_metakeys_edit'];
            $metadata = $data['class_metadescription_edit'];
            $description = $data['class_description_edit'];
            $image = $data['class_image'];
            if($data['make_class_group'] == 'yes'){
                $update_query = ("UPDATE courses SET course_name='$name', course_student_group_id = '$student_group_id', course_student_group_name = '$student_group_name', course_date_updated = NOW(), course_date_start = '$start', course_date_end = '$end' WHERE id = '$id'");
            }else{
                $update_query = ("UPDATE courses SET course_name='$name', course_date_updated = NOW(), course_date_start = '$start', course_date_end = '$end' WHERE id = '$id'");
            }
            $success = $db->query($update_query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . 'query1[' . $db->error . ']');
            }else{
                $update_query1 = ("UPDATE courses_display_data SET courses_display_data_title = '$title', courses_display_data_description = '$description', courses_display_data_seo = '$seo', courses_display_data_metakey='$metkeys', courses_display_data_metadata='$metadata', courses_display_data_image='$image' WHERE courses_display_data_id = '$id'");
                $success2 = $db->query($update_query1);
                if(!$success2){
                    die(ATXT_ERROR_RUNNING_QUERY . 'query2[' . $db->error . ']');
                }else{
                    if($data['make_class_group'] == 'yes'){
                        $insert_query = ("INSERT INTO courses_groups (courses_groups_id, courses_groups_name, courses_groups_folder) VALUES ('$id','$student_group_name','$student_group_folder')");
                        $success3 = $db->query($insert_query);
                        if(!$success3){
                            die(ATXT_ERROR_RUNNING_QUERY . 'query3[' . $db->error . ']');
                        }else{
                            $db->close();
                            Session::setFlash(AMENU_CLASS_UPDATED,'ok');
                            return true;
                        }
                    }else{
                        $db->close();
                        Session::setFlash(AMENU_CLASS_UPDATED,'ok');
                        return true;
                    }
                }
            }
        }else{

            $db->close();
            Session::setFlash(ATXT_UNKNOWN_ERROR.' AMCou-0006-309','error');
            return false;
        }
    }// END function courseEdit($data = array()


    public static function deleteCourse($id){//AMCou-0008

        $db = Model::getDB();
        $folder = '';
        $files_deleted = TRUE;
        $group_deleted = TRUE;
        $prepare_delete = ("SELECT * FROM courses_groups WHERE courses_groups_id = '$id'");
        $success = $db->query($prepare_delete);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']'); 
        }else{
            $found = $success->num_rows;
            if($found <= 0){//if there is no group - no worries - probably was not created during process

                $group_deleted = FALSE;

            }else{

                while($result = $success->fetch_assoc() ){
                    $folder = $result['courses_groups_folder'];
                }
                //delete folder and files
                $f_deleted = Filesystem::delTree($folder);
                if(!$f_deleted){
                    $files_deleted = FALSE;
                }

            }

            if($group_deleted === TRUE){
                //delete query 1
                $delete_groups = ("DELETE FROM courses_groups WHERE courses_groups_id = '$id'");
                $success = $db->query($delete_groups);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }
            }

                        //delete query 2
                        $delete_display_data = ("DELETE FROM courses_display_data WHERE courses_display_data_id = '$id'");
                        $success = $db->query($delete_display_data);
                        if(!$success){
                            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                        }else{
                            $delete_course = ("DELETE FROM courses WHERE id = '$id'");
                            $success = $db->query($delete_course);
                            if(!$success){
                                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                            }else{
                                if($files_deleted === TRUE){
                                    Session::setFlash(AMENU_CLASS_DELETED,'ok');
                                }else{
                                    Session::setFlash(AMENU_CLASS_DELETED_BUT_NO_FILES,'warning');
                                }

                                $db->close();
                                return TRUE;
                            }
                        }

        }// end first wrapper query
    }// END function deleteCourse($id)

    public static function listAllGroups(){//AMCou-0009
        $db = Model::getDB();
            $groups = array();
            $groups_list = ("SELECT * FROM courses_groups ");
            $success = $db->query($groups_list);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success->fetch_assoc()){
                array_push($groups, $result);
            }
                $db->close();
                return $groups;
            }

    }//end function courseLoadData($id)

    public static function getCourseNameById($id){//AMCou-0010
        $db = Model::getDB();
        $name = '';
        $course_name = ("SELECT course_name FROM courses WHERE id = '$id'");
        $success = $db->query($course_name);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success->fetch_assoc()){
                $name = $result['course_name'];
            }
            $db->close();
            return $name;
        }
    }//end function getCourseNameById($id)

    public static function getGroupById($id){//AMCou-0010
        $db = Model::getDB();
        $group = array();
        $course_name = ("SELECT * FROM courses_groups WHERE courses_groups_id = '$id'");
        $success = $db->query($course_name);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success->fetch_assoc()){
                foreach($result as $key => $value){
                    $group[$key] = $value;
                }
            }
            $db->close();
            return $group;
        }
    }//end function getCourseNameById($id)

    public static function editGroup($data){//AMCou-0011
        $db = Model::getDB();

        $options = [
         'cost' => 11,
        ];

        $courses_groups_id = $data['group-course-id'];
        $courses_groups_name = $data['group-name'];
        $courses_groups_new_folder = $data['group-folder'];
        $courses_groups_old_folder = $data['group-old-folder'];
        $courses_groups_login = $data['group-curse-login'];
        if($data['group-curse-pass'] != ''){
            $courses_groups_pass = $data['group-curse-pass'];
            $hash = password_hash($courses_groups_pass, PASSWORD_BCRYPT, $options);
        }else{
            $courses_groups_pass = 0;
        }

//here
        $path = Filesystem::changeGroupFolderName($courses_groups_old_folder, $courses_groups_new_folder);
        if($path === FALSE){
            Session::setFlash(ATXT_GROUPS_ERROR_RENAME_FOLDER, 'error');
            $db->close();
            return FALSE;
        }else{
            if($courses_groups_pass === 0){

                if($courses_groups_login != ''){
                    $course_edit = ("UPDATE courses_groups SET courses_groups_name='$courses_groups_name', courses_groups_folder = '$path',courses_groups_login='$courses_groups_login' WHERE courses_groups_id = '$courses_groups_id'");
                }else{
                    $course_edit = ("UPDATE courses_groups SET courses_groups_name='$courses_groups_name', courses_groups_folder = '$path' WHERE courses_groups_id = '$courses_groups_id'");
                }

            }else{

                if($courses_groups_login != ''){
                    $course_edit = ("UPDATE courses_groups SET courses_groups_name='$courses_groups_name', courses_groups_folder = '$path',courses_groups_login='$courses_groups_login',courses_groups_pass='$hash' WHERE courses_groups_id = '$courses_groups_id'");
                }else{
                    $course_edit = ("UPDATE courses_groups SET courses_groups_name='$courses_groups_name', courses_groups_folder = '$path',courses_groups_pass='$hash' WHERE courses_groups_id = '$courses_groups_id'");
                }

            }

            $success = $db->query($course_edit);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{

                $change_students_group_name = ("UPDATE courses SET course_student_group_name = '$courses_groups_name' WHERE id = '$courses_groups_id'");
                $success = $db->query($change_students_group_name);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }else{
                    Session::setFlash(ATXT_GROUPS_CHANGED, 'ok');
                    $db->close();
                    return TRUE;
                }
            }

        }

//END HERE


    }//end function editGroup($data)

}//END CLASS COURSE