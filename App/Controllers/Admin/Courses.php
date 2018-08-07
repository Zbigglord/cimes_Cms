<?php
/**
 * Created by BBJaga
 * Date: 2016-08-26
 * Time: 11:49
 */

namespace App\Controllers\Admin;

use App\Models\Admin\Course;
use Core\Filesystem;
use Core\Lang;
use Core\Session;
use \Core\View;

class Courses extends \Core\Controller{

    protected function before(){//ACCU-0001

//silent

    }

    protected function after(){//ACCU-0002

//silent

    }

    public function listAction(){//ACCU-0003

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            $clist = Course::listAllCourses();
            $message = Session::getFlash();
            Session::unsetFlash();
            if(!empty($clist)){
                View::renderAdmin('Courses/list.phtml',['clist'=>$clist, 'message'=>$message]);
            }else{
                View::renderAdmin('Courses/list.phtml',['message'=>$message]);
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END LIST ACTION

    public function addAction(){//ACCU-0004

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            //if is set data to save in database
              if(isset($_POST['add_classname']) || isset($_POST['class_cat']) || isset($_POST['class_start']) || isset($_POST['class_end'])){
                  $image_path = '';//just for interpreter to not complain that may or may not be definied
                  $data = array();
                  $course_name = $_POST['add_classname'];
                  $course_category_id = $_POST['class_cat'];
                  $course_date_start = $_POST['class_start'];
                  $course_date_end = $_POST['class_end'];

                  $images=array();
                  if(isset($_FILES['class_image_add'])){
                      $images = $_FILES['class_image_add'];
                      $img = Course::uploadImage($images);
                      if($img != NULL && $img != FALSE){
                          $image_path = $img;
                      }else{
                          $image_path = '';
                      }
                  }

                  $courses_display_data_title = isset($_POST['class_title']) ? $_POST['class_title'] : '';
                  $courses_display_data_seo = isset($_POST['class_seo']) ? $_POST['class_seo'] : '';
                  $courses_display_data_metakey = isset($_POST['class_metakeys']) ? $_POST['class_metakeys'] : '';
                  $courses_display_data_metadata = isset($_POST['class_metadescription']) ? $_POST['class_metadescription'] : '';
                  $courses_display_data_description = isset($_POST['class_description']) ? $_POST['class_description'] : '';
                  $make_group = isset($_POST['make_class_group']) ? $_POST['make_class_group'] : 'no';

                 //insert all into data array
                  $data['course_name'] = $course_name;
                  $data['course_category_id'] = $course_category_id;
                  $data['course_date_start'] = $course_date_start;
                  $data['course_date_end'] = $course_date_end;
                  $data['courses_display_data_image'] = $image_path;
                  $data['courses_display_data_title'] = $courses_display_data_title;
                  $data['courses_display_data_seo'] = $courses_display_data_seo;
                  $data['courses_display_data_metakey'] = $courses_display_data_metakey;
                  $data['courses_display_data_metadata'] = $courses_display_data_metadata;
                  $data['courses_display_data_description'] = $courses_display_data_description;
                  $data['make_group'] = $make_group;

                  //send it to model
                  $add_new = Course::coursesAddNew($data);
                  if($add_new != true){
                      Session::setFlash('something went wrong','error');
                  }else{
                      Session::setFlash('new class added properly','ok');
                  }
                  $message = Session::getFlash();
                  Session::unsetFlash();
                  View::renderAdmin('Courses/add.phtml', ['message'=>$message]);
              }else{
                  //if is just regular display of new add class form
                  $courses_cats = Course::listAllCoursesCategories();
                  $message = Session::getFlash();
                  Session::unsetFlash();
                  View::renderAdmin('Courses/add.phtml', ['c_cats' => $courses_cats, 'message'=>$message]);
              }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END ADD ACTION

    public function folderAction(){//ACCU-0005
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
          if(isset($_GET['data'])){
              $last = $_GET['data'];
              $path = dirname(dirname(dirname(dirname(__FILE__))));
              $folder_path = $path.'/public/upload/kursy/'.$last;
              $folder = Filesystem::browseDir($folder_path);
              View::renderAdmin('Courses/folder.phtml', ['folder' => $folder]);
          }
        }
    }

    public function editAction(){//ACCU-0006
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['course_edit_id'])){
                $course_id = $_POST['course_edit_id'];
                $course_data = Course::courseLoadData($course_id);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Courses/edit.phtml', ['course_data' => $course_data,'message' => $message]);
            }
        }
    }

    public function saveAction(){//ACCU-0007
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['edit_class_id'])){
                $post = array();
                foreach($_POST as $key => $value){
                    $post[$key] = htmlentities($value);
                }
                 Course::courseEdit($post);
                }
            $clist = Course::listAllCourses();
            $message = Session::getFlash();
            Session::unsetFlash();
                View::renderAdmin('Courses/list.phtml', ['clist' => $clist, 'message' => $message]);
            }
        }

    public function deleteAction(){//ACCU-0008
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['course-delete']) && $_POST['course-delete'] != ''){
                $delete_id = $_POST['course-delete'];
                $is_deleted = Course::deleteCourse($delete_id);
                if($is_deleted != FALSE){
                    $clist = Course::listAllCourses();
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Courses/list.phtml', ['clist' => $clist, 'message' => $message]);
                }else{
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Courses/list.phtml', ['message' => $message]);
                }
            }
        }
    }

    public function listGroupAction(){//ACCU-0009
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            $glist = Course::listAllGroups();
            View::renderAdmin('Courses/groups.phtml',['glist' => $glist]);
        }
    }//END listGroupAction()

    public function editGroupAction(){//ACCU-0010
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['group-edit-id']) && $_POST['group-edit-id'] != ''){
                $data = Course::getGroupById($_POST['group-edit-id']);
                View::renderAdmin('Courses/groupedit.phtml',['data' => $data]);
            }elseif(isset($_POST['group-course-id']) && isset($_POST['group-name']) && $_POST['group-name'] != '' && isset($_POST['group-folder']) && $_POST['group-folder'] != '' && isset($_POST['group-curse-login']) && $_POST['group-curse-login'] != '' && isset($_POST['group-curse-pass']) && $_POST['group-curse-pass'] != ''){
                $data = array();
                foreach($_POST as $key => $value){
                    $data[$key] = $value;
                }
                $success = Course::editGroup($data);
                $message = Session::getFlash();
                Session::unsetFlash();
                $glist = Course::listAllGroups();
                View::renderAdmin('Courses/groups.phtml',['message' => $message, 'glist' => $glist]);
            }else{
                Session::setFlash(MENUITEM_NOT_ALL_INPUTS,'error');
                $message = Session::getFlash();
                Session::unsetFlash();
                $glist = Course::listAllGroups();
                View::renderAdmin('Courses/groups.phtml',['message' => $message,'glist' => $glist]);
            }

        }
    }//END editGroupAction()

}// END CLASS Curses