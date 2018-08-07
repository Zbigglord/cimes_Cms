<?php
/**
 * Created by BBJaga.
 * Date: 2016-09-05
 * Time: 14:25
 */

namespace App\Controllers;

use App\Models\Category;
use Core\Controller;
use Core\View;
use Core\Session;

class Categories extends Controller{

 protected function before(){//FrECCA-0001

 }

 protected function after(){//FrECCA-0002

 }

 public function typeAction(){//ACCA-0003

  View::render('Categories/typelist.phtml');
 }

 public function categoryAction(){//ACCA-0004
  $param = $this->route_params;
  $id = $param['id'];
  $list = Category::getSubcategories($id,1);//1 indicates that we gonna display parent category data as well
  $type = Category::getCategoryTypeAndView($id);
  $v = 'Categories/'.$type['view'].'.phtml';
  if($list != FALSE){

   $page_title = $list['parent']['name'];
   $meta_desc = $list['parent']['metadescription'];
   $meta_keys = $list['parent']['metakeys'];

   $message = Session::getFlash();
   Session::unsetFlash();


   View::render($v,['list' => $list, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
  }else{
   Session::setFlash(FTXT_CATEGORIES_SUB_NONE,'warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   View::render('Categories/grid.phtml',['message' => $message]);
  }
 }

/*
 public function categoryAction(){//ACCA-0007
  $param = $this->route_params;
  $id = $param['id'];
  $list = array();
  $v = '';
  $type = Category::getCategoryTypeAndView($id);

  switch($type['type']){//type view gets from config - files still 2 types list and grid to not to get into mess in one view file

   case 1:

    $list = Post::getPostsFromCategory($id,1);
    $v = 'Posts/'.$type['view'].'.phtml';

          break;
   case 2:

    $list = Gallery::getAllGalleriesLight($id);
    $v = 'Galleries/'.$type['view'].'.phtml';

    break;
   case 3:

    //todo: during form creation need to pick category (make forms category)

    break;
   case 4:

    //todo: archives

    break;
   case '5':

    //todo: delete this category type - news gonna be post just under category NEWS

    break;
   case 6:

    $list = Category::getSubcategories($id,1);//1 indicates that we gonna display parent category data as well
    $v = 'Courses/'.$type['view'].'.phtml';

    break;
   case 7:

    $list = File::getFilesFromCategory($id, 1);
    $v = 'Files/'.$type['view'].'.phtml';

    break;
   default:

    $v = 'Main/main.phtml';

    break;

  }

  if($list != FALSE && !empty($list)){

   $page_title = $list['parent']['name'];
   $meta_desc = $list['parent']['metadescription'];
   $meta_keys = $list['parent']['metakeys'];

   $message = Session::getFlash();
   Session::unsetFlash();
   View::render($v,['list' => $list, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
  }else{
   Session::setFlash(FTXT_CATEGORIES_SUB_NONE,'warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   View::render('Categories/grid.phtml',['message' => $message]);
  }
 }
*/
}// END CLASS CATEGORIES