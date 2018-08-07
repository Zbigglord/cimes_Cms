<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-17
 * Time: 10:01
 */
namespace App\Controllers\Admin;

use App\Models\Admin\Category;
use Core\Controller;
use Core\Session;
use Core\Template;
use Core\View;

class Templates extends Controller{

    public function listAction(){//ACTE-0001

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            $templates = Template::getTemplates();
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Templates/list.phtml',['templates'=>$templates, 'message'=>$message]);
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END listAction()

    public function newAction(){//ACTE-0002

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            View::renderAdmin('Templates/new.phtml');
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END listAction()

    public function saveAction(){//ACTE-0003

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['template_name']) && $_POST['template_name'] != '' && isset($_POST['template_folder']) && $_POST['template_folder'] != ''){

                $new_template = array();
                $new_template['name'] = strtolower(filter_var($_POST['template_name'],FILTER_SANITIZE_STRING));
                $new_template['folder'] = strtolower(filter_var($_POST['template_folder'],FILTER_SANITIZE_STRING));
                $new_template['author'] = isset($_POST['template_author']) ? filter_var($_POST['template_author'],FILTER_SANITIZE_STRING) : '';
                $new_template['is_admin'] = isset($_POST['template_admin']) ? 1 : 0;
                $new_template['is_active'] = isset($_POST['template_active']) ? 1 : 0;

                $success = Template::newTemplate($new_template);

                if($success == TRUE){
                    $templates = Template::getTemplates();
                    Session::setFlash(ATXT_TEMPLATE_ADDED,'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Templates/list.phtml',['templates'=>$templates, 'message'=>$message]);
                }else{
                    $templates = Template::getTemplates();
                    Session::setFlash(ATXT_TEMPLATE_EXISTS,'error');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Templates/list.phtml',['templates'=>$templates, 'message'=>$message]);
                }
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END listAction()

    public function editAction(){//ACTE-0004

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_POST['template_edit_id']) && $_POST['template_edit_id'] != ''){

                $template = Template::getTemplateById($_POST['template_edit_id']);

                View::renderAdmin('Templates/edit.phtml',['templates'=>$template]);

            }else if(isset($_POST['template_id']) && $_POST['template_id'] != ''){

                $edit_template = array();
                $edit_template['template_name'] = strtolower(filter_var($_POST['template_name'],FILTER_SANITIZE_STRING));
                $edit_template['template_folder'] = strtolower(filter_var($_POST['template_folder'],FILTER_SANITIZE_STRING));
                $edit_template['id'] = $_POST['template_id'];
                $edit_template['template_author'] = isset($_POST['template_author']) ? filter_var($_POST['template_author'],FILTER_SANITIZE_STRING) : '';
                $edit_template['template_is_admin'] = isset($_POST['template_admin']) ? 1 : 0;
                $edit_template['template_active'] = isset($_POST['template_active']) ? 1 : 0;

                $is_updated = Template::updateTemplate($edit_template);
                if($is_updated == TRUE){

                    $templates = Template::getTemplates();
                    Session::setFlash(ATXT_TEMPLATE_ADDED,'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Templates/list.phtml',['templates'=>$templates, 'message'=>$message]);

                }else{

                    $templates = Template::getTemplates();
                    Session::setFlash(ATXT_ERROR_UNKNOWN,'ęrror');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Templates/list.phtml',['templates'=>$templates, 'message'=>$message]);

                }

            }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END listAction()

    public function deleteAction(){//ACTE-0005

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_POST['template_delete_id']) && $_POST['template_delete_id'] != ''){

                $check_template = Template::getTemplateById($_POST['template_delete_id']);
                if($check_template['template_active'] == 1){//if template is current cannot delete

                    $templates = Template::getTemplates();
                    Session::setFlash(ATXT_TEMPLATE_DELETE_ACTIVE,'warning');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Templates/list.phtml',['templates'=>$templates, 'message'=>$message]);

                }else{

                    $deleted = Template::deleteTemplate($_POST['template_delete_id']);
                    $templates = Template::getTemplates();
                    Session::setFlash(ATXT_TEMPLATE_DELETED,'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Templates/list.phtml',['templates'=>$templates, 'message'=>$message]);

                }

            }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END listAction()

    public function viewAction(){//ACTE-0006

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            $views = Category::getType();
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Config/view.phtml',['views'=>$views, 'message'=>$message]);

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END viewAction()

    public function saveviewAction(){//ACTE-0007

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

        if(isset($_POST)){

            $data = array();
            foreach($_POST as $key => $value){
                $data[$key] = $value;
            }

           $success = Category::setTypeView($data);

            if($success == TRUE){
                Session::setFlash(ATXT_CATEGORIES_VIEW_CHANGED,'ok');
            }else{
                Session::setFlash(ATXT_CATEGORIES_VIEW_NOT_CHANGED,'error');
            }

            $views = Category::getType();
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Config/view.phtml',['views'=>$views, 'message'=>$message]);

        }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END saveviewAction()

}//END CLASS
