<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-30
 * Time: 17:20
 */

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Lang;
use Core\Session;
use \Core\View;
use Core\Filesystem;
use App\Models\Admin\Category;

/**
 * Class Categories
 * @package App\Controllers\Admin
 */
class Categories extends Controller{

    protected function before(){//ACCA-0001

    }

    protected function after(){//ACCA-0002

    }

    public function listAction(){//ACCA-0003

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['list_set_cat_type']) && $_POST['list_set_cat_type'] != 0){
                $ctype = $_POST['list_set_cat_type'];
                $cat_list = Category::categoryGetList(0, $ctype);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Categories/list.phtml',['cat_list' => $cat_list, 'ctype'=>$ctype, 'message'=>$message]);
            }else{
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Categories/list.phtml',['message'=>$message]);
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END LIST ACTION

    public function deleteAction(){//ACCA-0004

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['cat_id']) && isset($_POST['cat_type'])){
                $type = $_POST['cat_type'];
                Category::deleteCategory($_POST['cat_id'], $_POST['cat_type']);
                $message = Session::getFlash();
                Session::unsetFlash();
                $cat_list = Category::categoryGetList(0, $type);
                View::renderAdmin('Categories/list.phtml',['cat_list' => $cat_list, 'message'=>$message]);
            }else{
                Session::setFlash(ATXT_UNKNOWN_ERROR.' AMCA-0004-68','error');
                $message = Session::getFlash();
                Session::unsetFlash();
                $cat_list = Category::categoryGetList(0, 1);
                View::renderAdmin('Categories/list.phtml',['cat_list' => $cat_list, 'message'=>$message]);
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END LIST DELETE

    public function addAction(){//ACCA-0005
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            //if new category form was posted
            if((isset($_POST['add_catname']) && $_POST['add_catname'] != '') && isset($_POST['set_cat_parent']) && isset($_POST['set_cat_type'])){
//if set_cat_type = none default is articles and default for set_cat_parent (if=none) will be main
                $cat_description = isset($_POST['add_catname']) ? $_POST['add_catname'] : '';
                $cat_seo_link = isset($_POST['cat_seo_link']) ? $_POST['cat_seo_link'] : '';
                $cat_metakeys = isset($_POST['cat_metakeys']) ? $_POST['cat_metakeys'] : '';
                $cat_metadescription = isset($_POST['cat_metadescription']) ? $_POST['cat_metadescription'] : '';

               $data = array(
                   'cat_name' => $_POST['add_catname'],
                   'cat_parent' => $_POST['set_cat_parent'],
                   'cat_type' => $_POST['set_cat_type'],
                   'cat_description' => $_POST['add_catdescription'],
                   'cat_seo_link' => $_POST['add_metacatname'],
                   'cat_metakeys' => $_POST['add_metacatkeys'],
                   'cat_metadescription' => $_POST['add_metacatdescription']
               );

                $images=array();
                if(isset($_FILES['category_image_add'])){
                    $images = $_FILES['category_image_add'];
                   $img = Filesystem::uploadImage($images);
                    if($img != NULL && $img != FALSE){
                        $image_path = $img;
                    }else{
                        $image_path = '';
                    }
                }

                Category::addNewCategory($data, $image_path);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Categories/list.phtml',['message'=>$message]);
            }else{
                View::renderAdmin('Categories/add.phtml');
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }
    }
//to do:
    public function editAction(){//ACCA-0006
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
         //edit part
            if(isset($_POST['cat_id']) && isset($_POST['cat_type'])){
                $cat_id = $_POST['cat_id'];
                $cat_type = $_POST['cat_type'];
                $category_data = Category::getCategoryByID($cat_id, $cat_type);
                View::renderAdmin('Categories/edit.phtml',['category_data'=>$category_data]);
            }else{
                View::renderAdmin('Categories/list.phtml');
            }
         //END edit part
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }


    }//END ADD CATEGORY ACTION

} //END CLASS CATEGORIES