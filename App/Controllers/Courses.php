<?php
/**
 * Created by BBJaga.
 * Date: 2016-09-05
 * Time: 14:27
 */

namespace App\Controllers;

use App\Models\Course;
use Core\Session;
use Core\View;
use Core\Controller;
use App\Models\Category;

class Courses extends Controller{

 protected function before(){

  //nothing comes to mind
  //return false; //if return false next action will not be executed

 }

 protected function after(){

  //nothing comes to mind

 }

 public function indexAction(){

  $clist = Course::listAllCourses();
  $message = Session::getFlash();
  Session::unsetFlash();
  if(!empty($clist)){
   View::render('Courses/index.phtml',['clist'=>$clist, 'message'=>$message]);
  }else{
   View::render('Courses/index.phtml',['message'=>$message]);
  }

 }//END INDEX ACTION

 public function courseAction(){
  $param = $this->route_params;
  $id = $param['id'];
  $course = Course::getCourse($id);
  $page_title = $course['course_name'];
  $meta_desc = $course['courses_display_data_metadata'];
  $meta_keys = $course['courses_display_data_metakey'];
  $message = Session::getFlash();
  Session::unsetFlash();
  View::render('Courses/course.phtml',['course' => $course, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
 }//END COURSE ACTION

 public function listAction(){//list of all courses in course category
  $param = $this->route_params;
  $id = $param['id'];
  $type = Category::getCategoryTypeAndView($id);
  $v = 'Courses/'.$type['view'].'.phtml';
  $courses = Course::getCoursesFromCategory($id);
  $page_title = $courses[0]['course_name'];
  $meta_desc = $courses[0]['courses_display_data_metadata'];
  $meta_keys = $courses[0]['courses_display_data_metakey'];
  if($courses != FALSE){
   $message = Session::getFlash();
   Session::unsetFlash();
   View::render($v,['list' => $courses, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
  }else{
   Session::setFlash(FTXT_COURSES_FROM_CATEGORY_NONE, 'warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   View::render('Courses/courselist.phtml',['message' => $message]);
  }

 }//END COURSE ACTION

}