<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-17
 * Time: 10:01
 */
namespace App\Controllers;

use App\Models\Category;
use App\Models\Gallery;
use Core\Controller;
use Core\View;
use Core\Session;


class Galleries extends Controller{

    protected function before(){//FGAL 0001

    }

    protected function after(){//FGAL 0002

    }


    public function listAction(){//FGAL 0004

        $param = $this->route_params;
        $id = $param['id'];
        $type = Category::getCategoryTypeAndView($id);
        $v = 'Galleries/'.$type['view'].'.phtml';
        $list = Gallery::getAllGalleriesLight($id);
        $cat = Category::getCategoryByID($id,2);
        $page_title = $cat['name'];
        $meta_desc = $cat['metadescription'];
        $meta_keys = $cat['metakeys'];
        $message = Session::getFlash();
        Session::unsetFlash();
        View::render($v,['list' => $list, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);

    }//END listAction()

    public function galleryAction(){//FGAL 0005

        $param = $this->route_params;
        $id = $param['id'];
        $gallery = Gallery::getGalleryById($id);
        $items = Gallery::galleryGetItems($id);
        $page_title = $gallery[0]['gallery_name'];
        $meta_desc = $gallery[0]['metadata'];
        $meta_keys = '';
        $message = Session::getFlash();
        Session::unsetFlash();
        View::render('Galleries/items.phtml',['items' => $items, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);

    }//END listAction()

}//END CLASS