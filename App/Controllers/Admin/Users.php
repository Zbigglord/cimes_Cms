<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-20
 * Time: 11:35
 */

namespace App\Controllers\Admin;

use App\Models\Admin\User;
use Core\Lang;
use Core\Session;
use \Core\View;
use Core\Filesystem;

/**
 * Class Users
 * @package App\Controllers\Admin
 */
class Users extends \Core\Controller{

    protected function before(){//ACUS-0001

    }

    protected function after(){//ACUS-0002


    }

    public function indexAction(){//ACUS-0003
       if(!isset($_SESSION['user_nick'])){
            if (isset($_POST['nick']) && isset($_POST['pass'])) {
                $user = User::login($_POST['nick'], $_POST['pass']);
                $message = Session::getFlash();
                if ($user === FALSE) {
                    View::renderEmpty('Login/index.phtml', ['message' => $message]);
                } else {
                      if(isset($_SESSION['show_admin']) && $_SESSION['show_admin'] == 1) {
                            unset($_POST['nick']);
                            unset($_POST['pass']);
                            Session::unsetFlash();
                            View::renderAdmin('Main/index.phtml');

                    }else{
                        Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
                        $message = Session::getFlash();
                        Session::delete('user_nick');
                        Session::delete('user_group');
                          Session::unsetFlash();
                        View::renderEmpty('Login/index.phtml',['message' => $message]);
                    }
                }
            } else {
                Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderEmpty('Login/index.phtml',['message' => $message]);
            }
        }else{
            Session::unsetFlash();
            View::renderAdmin('Main/index.phtml');
        }
    }//end index action

    public function usersListAction(){//ACUS-0004
        if(!isset($_SESSION['user_nick']) || $_SESSION['show_admin'] != 1){
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_POST['btn_xml']) && $_POST['btn_xml'] == 'xml_export'){
            $ulist = User::listAll();
            Filesystem::dataSaveToXml('users','user',$ulist);
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Users/list.phtml', ['ulist' => $ulist, 'message' => $message]);
        }else{
            $ulist = User::listAll();
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Users/list.phtml', ['ulist' => $ulist,'message' => $message]);
        }
    }//end usersListAction()

    public function usersAddAction(){//ACUS-0005
        if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Users/add.phtml', ['message' => $message]);
        }else {
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }
    }//usersAddAction()

    public function userEditAction(){//ACUS-0006
        if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Users/edit.phtml', ['message' => $message]);
        }else {
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }
    }//usersEditAction()

    public function userDeleteAction(){//ACUS-0007
        if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['delete_user_by_id']) && $_POST['delete_user_by_id'] != ''){
                User::deleteUser($_POST['delete_user_by_id'],'delete');
                $message = Session::getFlash();
                Session::unsetFlash();
                $ulist = User::listAll();
                View::renderAdmin('Users/delete.phtml', ['ulist' => $ulist, 'message' => $message]);
            }elseif(isset($_POST['deactivate_user_by_id']) && $_POST['deactivate_user_by_id'] != ''){
                User::deleteUser($_POST['deactivate_user_by_id'],'deactivate');
                $message = Session::getFlash();
                Session::unsetFlash();
                $ulist = User::listAll();
                View::renderAdmin('Users/delete.phtml', ['ulist' => $ulist, 'message' => $message]);
            }elseif(isset($_POST['activate_user_by_id']) && $_POST['activate_user_by_id'] != ''){
                User::deleteUser($_POST['activate_user_by_id'],'activate');
                $message = Session::getFlash();
                Session::unsetFlash();
                $ulist = User::listAll();
                View::renderAdmin('Users/delete.phtml', ['ulist' => $ulist, 'message' => $message]);
            }else{
                $ulist = User::listAll();
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Users/delete.phtml', ['ulist' => $ulist, 'message' => $message]);
            }
        }else {
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }
    }//usersAddAction()

    public function logout(){//ACUS-0008

    foreach($_SESSION as $key=>$value){
        Session::delete($key);
    }
    Session::unsetFlash();
    Session::destroy();
    header("location: http://".$_SERVER['SERVER_NAME']."/admin");
   }//END function logout()


    public function addStudentAction(){//ACUS-0009
        if(!isset($_SESSION['user_nick']) || $_SESSION['show_admin'] != 1){
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else{
            if(isset($_POST['student-name']) && $_POST['student-name'] != '' && isset($_POST['student-surname']) && $_POST['student-surname'] != '' && isset($_POST['student-email']) && $_POST['student-email'] != ''){
                $student = array();
                foreach($_POST as $key => $value){
                    $student[$key] = $value;
                }
                $success = User::addStudent($student);
                $slist = User::studentList();
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Users/studentlist.phtml',['slist' => $slist,'message' => $message]);
            }else{
                View::renderAdmin('Users/studentadd.phtml');
            }
        }
    }//END function addStudentAction()

    public function studentsListAction(){//ACUS-0010
        if(!isset($_SESSION['user_nick']) || $_SESSION['show_admin'] != 1){
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else{
                 $slist = User::studentList();
                 $message = Session::getFlash();
                 Session::unsetFlash();
                 View::renderAdmin('Users/studentlist.phtml',['slist' => $slist,'message' => $message]);

            }
    }//END function studentsListAction()

    public function editStudentAction(){//ACUS-0011
        if(!isset($_SESSION['user_nick']) || $_SESSION['show_admin'] != 1){
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else{
            if(isset($_POST['student_edit_id']) && $_POST['student_edit_id'] != '' ){
                $student = User::getStudentById($_POST['student_edit_id']);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Users/studentedit.phtml',['student' => $student,'message' => $message]);
            }elseif(isset($_POST['student-id']) && $_POST['student-id'] != ''){
                if(isset($_POST['student-name']) && $_POST['student-name'] != '' && isset($_POST['student-surname']) && $_POST['student-surname'] != '' && isset($_POST['student-email']) && $_POST['student-email'] != ''){
                    $student = array();
                    foreach($_POST as $key => $value){
                        $student[$key] = $value;
                    }

                    $edited = User::editStudent($student);
                    $slist = User::studentList();
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Users/studentlist.phtml',['slist' => $slist,'message' => $message]);
                }else{
                    $student = User::getStudentById($_POST['student-id']);
                    Session::setFlash(MENUITEM_NOT_ALL_INPUTS,'error');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Users/studentedit.phtml',['student' => $student,'message' => $message]);
                }
            }else{
                $slist = User::studentList();
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Users/studentlist.phtml',['slist' => $slist,'message' => $message]);
            }
        }
    }//END function addStudentAction()

    public function deleteStudentAction(){//ACUS-0012
        if(!isset($_SESSION['user_nick']) || $_SESSION['show_admin'] != 1){
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else{

            if(isset($_POST['student_delete_id']) && $_POST['student_delete_id'] !=''){

                $deleted = User::deleteStudent($_POST['student_delete_id']);
                $slist = User::studentList();
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Users/studentlist.phtml',['slist' => $slist,'message' => $message]);

            }

        }
    }//END function studentsListAction()


} //end class Users