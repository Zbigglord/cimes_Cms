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

class Course extends Model{

 public static function listAllCourses(){//FrMCou-0001
  $db = Model::getDB();
  $courses = array();
  $courses_list = ("SELECT * FROM courses LEFT JOIN courses_display_data ON courses.id = courses_display_data.courses_display_data_id LEFT JOIN courses_groups ON courses.id = courses_groups.courses_groups_id WHERE courses.is_archive = 0 ORDER BY courses.id ASC");
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
 }//end courses list frondend

 public static function getCourse($id){//FrMCou-0002
  $db = Model::getDB();
  if(isset($id) || !empty($id)){
   $course = array();
   $course_data = ("SELECT * FROM courses LEFT JOIN courses_display_data ON courses.id = courses_display_data.courses_display_data_id LEFT JOIN courses_groups ON courses.id = courses_groups.courses_groups_id WHERE courses.id = '$id' AND courses.is_archive = 0");
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
 }//end get course frondend

 public static function getCourseCategoryName($id){//FrMCou-0003
  $db = Model::getDB();
  $name = '';
  $cat_name = ("SELECT name FROM categories WHERE id = '$id'");
  $success = $db->query($cat_name);
  if(!$success){
   die('There was an error running the query [' . $db->error . ']');
  }else{
    $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc() ){
     $name = $result['name'];
    }
    $db->close();
    return $name;
   }else{
    $db->close();
    return $name;
   }
  }
 }//end get getCourseCategoryName frondend

 public static function getCoursesFromCategory($id){//get all courses from one courses category

  $db = Model::getDB();
  $courses = array();
  $query = ("SELECT * FROM courses LEFT JOIN courses_display_data ON courses.id = courses_display_data.courses_display_data_id LEFT JOIN courses_groups ON courses.id = courses_groups.courses_groups_id WHERE courses.course_category_id = '$id' AND courses.is_archive = 0 ORDER BY courses.course_date_updated DESC");
  $success = $db->query($query);
  if(!$success){
   die('There was an error running the query [' . $db->error . ']');
  }

  $found = $success->num_rows;
  if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($courses, $result);
    }

   $db->close();
   return $courses;
  }else{
   $db->close();
   return FALSE;
  }

 }//END getCoursesFromCategory($id)

}//END CLASS