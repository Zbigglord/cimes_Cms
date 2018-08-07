<?php
namespace App\Controllers;
use App\Models\Category;
use App\Models\Post;
use Core\Session;
use \Core\View;
/**
 * Creator: BabooJaga Studio
 * Date: 2016-02-07
 * Time: 13:15
 */

class Posts extends \Core\Controller{

    protected function before(){

        //emptiness

    }

    protected function after(){

        //emptiness

    }

    public function postAction(){
        $param = $this->route_params;
        $id = $param['id'];
       $post = Post::getPost($id);
        $page_title = $post[0]['post_title'];
        $meta_desc = $post[0]['post_metadescription'];
        $meta_keys = $post[0]['post_metakeys'];
        $message = Session::getFlash();
        Session::unsetFlash();
        View::render('Posts/post.phtml',['post' => $post, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
    }

    public function listAction(){//list of all posts in posts category
        $param = $this->route_params;
        $id = $param['id'];
        $posts = Post::getPostsFromCategory($id,1);//1 if show parent category description
        $cat = \App\Models\Admin\Category::getCategoryByID($id,1);
        $page_title = $cat['name'];
        $meta_desc = $cat['metadescription'];
        $meta_keys = $cat['metakeys'];
        if($posts != FALSE){
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Posts/list.phtml',['list' => $posts, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
        }else{
            Session::setFlash(ATXT_NO_POSTS,'warning');
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Posts/list.phtml',['message' => $message]);
        }

    }//END COURSE ACTION

    public function gridAction(){//list of all posts in posts category
        $param = $this->route_params;
        $id = $param['id'];
        $posts = Post::getPostsFromCategory($id,1);//1 if show parent category description
        $cat = \App\Models\Admin\Category::getCategoryByID($id,1);
        $page_title = $cat['name'];
        $meta_desc = $cat['metadescription'];
        $meta_keys = $cat['metakeys'];
        if($posts != FALSE){
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Posts/grid.phtml',['list' => $posts, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
        }else{
            Session::setFlash(ATXT_NO_POSTS,'warning');
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Posts/grid.phtml',['message' => $message]);
        }

    }//END COURSE ACTION

} //END CLASS POST