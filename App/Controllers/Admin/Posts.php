<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-13
 * Time: 21:05
 */

namespace App\Controllers\Admin;

use App\Models\Admin\Category;
use App\Models\Admin\Post;
use Core\Lang;
use Core\Session;
use \Core\View;

class Posts extends \Core\Controller{

    protected function before(){//ACPO-0001

//silent

    }

    protected function after(){//ACPO-0002

//silent

    }

    public function listAction(){//ACPO-0003

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
            }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
                $alist = Post::listAllArticles();
                $message = Session::getFlash();
                View::renderAdmin('Posts/list.phtml',['alist'=>$alist, 'message'=>$message]);
            }else{
                Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
                header("location: http://".$_SERVER['SERVER_NAME']);
            }

        }//END LIST ACTION

    public function addAction(){//ACPO-0004

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['add_post_name']) && $_POST['add_post_name'] != '' && isset($_POST['post_set_cat_type']) && $_POST['post_set_cat_type'] != ''){
                $image_path = '';//just for interpreter to not complain that may or may not be definied
                $data = array();
                $post_title = $_POST['add_post_name'];
                $post_category_id = $_POST['post_set_cat_type'];
                $post_seo_link = isset($_POST['add_post_seo']) ? $_POST['add_post_seo'] : '';
                $post_metakeys = isset($_POST['add_post_metakeys']) ? $_POST['add_post_metakeys'] : '';
                $post_metadescription = isset($_POST['add_post_metadescription']) ? $_POST['add_post_metadescription'] : '';
                $post_description = isset($_POST['add_post_description']) ? $_POST['add_post_description'] : '';

                $images=array();
                if(isset($_FILES['post_image_add'])){
                    $images = $_FILES['post_image_add'];
                    $img = Post::uploadImage($images);
                    if($img != NULL && $img != FALSE){
                        $image_path = $img;
                    }else{
                        $image_path = '';
                    }
                }

                $data['post_title'] = $post_title;
                $data['post_category_id'] = $post_category_id;
                $data['post_seo_link'] = $post_seo_link;
                $data['post_metakeys'] = $post_metakeys;
                $data['post_metadescription'] = $post_metadescription;
                $data['post_description'] = $post_description;
                $data['post_image'] = $image_path;

                $success = Post::addNewArticle($data);
                if($success === TRUE){
                    $alist = Post::listAllArticles();
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Posts/list.phtml',['alist'=>$alist, 'message'=>$message]);
                }else{
                    $clist = Category::categoryGetList(0,1);
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Posts/add.phtml',['clist'=>$clist, 'message'=>$message]);
                }

            }else{
                $clist = Category::categoryGetList(0,1);
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Posts/add.phtml',['clist'=>$clist, 'message'=>$message]);
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }
    }//END ADD ACTION

    public function deleteAction(){//ACPO-0005

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['post_delete_id']) && $_POST['post_delete_id'] != ''){
                Post::deletePost($_POST['post_delete_id']);
                $message = Session::getFlash();
                $alist = Post::listAllArticles();
                Session::unsetFlash();
                View::renderAdmin('Posts/list.phtml',['alist'=>$alist, 'message'=>$message]);
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END DELETE ACTION

    public function editAction(){//ACPO-0006

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['post_edit_id']) && $_POST['post_edit_id'] != ''){
                $post = Post::getPostById($_POST['post_edit_id']);
                $cat_name = Post::getCategoryName($post['post_category_id']);
                View::renderAdmin('Posts/edit.phtml',['post'=>$post, 'cat_name' => $cat_name]);
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END DELETE ACTION

    public function editSaveAction(){//ACPO-0007

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['post_id']) && $_POST['post_id'] != ''){
                $data = array();
                $data['post_id'] = $_POST['post_id'];
                $data['post_image'] = $_POST['category_image'];
                $data['post_name'] = $_POST['edit_post_name'];
                $data['post_category_id'] = $_POST['post_set_cat_type'];
                $data['post_seo'] = $_POST['edit_post_seo'];
                $data['post_metakeys'] = $_POST['edit_post_metakeys'];
                $data['post_metadescription'] = $_POST['edit_post_metadescription'];
                $data['post_description'] = $_POST['edit_post_description'];
                $success = Post::editPost($data);
                if($success === TRUE){
                    $message = Session::getFlash();
                    $alist = Post::listAllArticles();
                    Session::unsetFlash();
                    View::renderAdmin('Posts/list.phtml',['alist'=>$alist, 'message'=>$message]);
                }else{
                    Session::setFlash(ATXT_UNKNOWN_ERROR.' ACPO-0007 167!','error');
                    $message = Session::getFlash();
                    $alist = Post::listAllArticles();
                    Session::unsetFlash();
                    View::renderAdmin('Posts/list.phtml',['alist'=>$alist, 'message'=>$message]);
                }
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END DELETE ACTION

    public function archiveAction(){//ACPO-0008

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {
            if(isset($_POST['post_archive_id']) && $_POST['post_archive_id'] != ''){

                $moved = Post::moveToArchive($_POST['post_archive_id']);

                if($moved == TRUE){
                    Session::setFlash(POST_MOVED_TO_ARCHIVE,'ok');
                }else{
                    Session::setFlash(ATXT_UNKNOWN_ERROR.' ACPO-0008 194!','error');
                }
                $message = Session::getFlash();
                $alist = Post::listAllArticles();
                Session::unsetFlash();
                View::renderAdmin('Posts/list.phtml',['alist'=>$alist, 'message'=>$message]);
            }
        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END DELETE ACTION

}// END CLASS Posts