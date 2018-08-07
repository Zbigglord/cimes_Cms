<?php
/**
 * Created by BBJaga.
 * Date: 2016-09-05
 * Time: 14:25
 */

namespace App\Controllers;

use App\Models\Admin\Form;
use App\Models\Category;
use App\Models\Course;
use App\Models\Post;
use App\Models\Gallery;
use App\Models\File;
use Core\Controller;
use Core\View;
use Core\Session;

class Singlecategory extends Controller{

    protected function before(){//FrECCA-0001

    }

    protected function after(){//FrECCA-0002

    }


     public function singleAction(){//ACCA-0007
      $param = $this->route_params;
      $id = $param['id'];
      $list = array();
      $v = '';
      $type = Category::getCategoryTypeAndView($id);

      switch($type['type']){//type view gets from config - files still 2 types list and grid to not to get into mess in one view file

       case 1:

        $list = Post::getPostsFromCategory($id,1);
        $v = 'Posts/'.$type['view'].'.phtml';
        $page_title = $list['parent']['name'];
        $meta_desc = $list['parent']['metadescription'];
        $meta_keys = $list['parent']['metakeys'];

              break;
       case 2:

        $list = Gallery::getAllGalleriesLight($id);
        $category = Category::getCategoryByID($id,2);
        $v = 'Galleries/'.$type['view'].'.phtml';
        $page_title = $category['name'];
        $meta_desc = $category['metadescription'];
        $meta_keys = $category['metakeys'];

        break;
       case 3:

         $list = Form::getFormsByCategory($id, 1);
         $v = 'Forms/'.$type['view'].'.phtml';
        $category = Category::getCategoryByID($id,3);
        $page_title = $category['name'];
        $meta_desc = $category['metadescription'];
        $meta_keys = $category['metakeys'];

        break;
       case 4:

        //todo: archives

        break;
       case '5':

        //not needed anymore - was news

        break;
       case 6:

        $list = Course::getCoursesFromCategory($id);//1 indicates that we gonna display parent category data as well
        $v = 'Courses/'.$type['view'].'.phtml';
        $page_title = $list[0]['course_name'];
        $meta_desc = $list[0]['courses_display_data_metadata'];
        $meta_keys = $list[0]['courses_display_data_metakey'];

        break;
       case 7:

        $list = File::getFilesFromCategory($id, 1);
        $v = 'Files/'.$type['view'].'.phtml';
        $page_title = $list['parent']['name'];
        $meta_desc = $list['parent']['metadescription'];
        $meta_keys = $list['parent']['metakeys'];

        break;
       default:

        $v = 'Main/main.phtml';

        break;

      }

      if($list != FALSE && !empty($list)){
       $message = Session::getFlash();
       Session::unsetFlash();
       View::render($v,['list' => $list, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
      }else{
       Session::setFlash(FTXT_CATEGORIES_ITEMS_NONE,'warning');
       $message = Session::getFlash();
       Session::unsetFlash();
       View::render('Categories/grid.phtml',['message' => $message]);
      }
     }

}// END CLASS CATEGORIES