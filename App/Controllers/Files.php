<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2018-03-26
 * Time: 09:01
 */

namespace App\Controllers;


use App\Models\File;
use Core\Controller;
use Core\Session;
use \Core\View;

class Files extends Controller
{

    protected function before(){

        //emptiness

    }

    protected function after(){

        //emptiness

    }

    public function listAction(){

        $param = $this->route_params;
        $id = $param['id'];
        $file_list = File::getFilesFromCategory($id,1);//1 if show parent category description
        if($file_list != FALSE){
            $page_title = $file_list['parent']['name'];
            $meta_desc = $file_list['parent']['metadescription'];
            $meta_keys = $file_list['parent']['metakeys'];
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Files/list.phtml',['list' => $file_list, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
        }else{
            Session::setFlash(ATXT_NO_POSTS,'warning');
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Files/list.phtml',['message' => $message]);
        }

    }//END filelistAction()

    public function gridAction(){

        $param = $this->route_params;
        $id = $param['id'];
        $file_list = File::getFilesFromCategory($id,1);//1 if show parent category description
        if($file_list != FALSE){
            $page_title = $file_list['parent']['name'];
            $meta_desc = $file_list['parent']['metadescription'];
            $meta_keys = $file_list['parent']['metakeys'];
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Files/filegrid.phtml',['list' => $file_list, 'message' => $message, 'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);
        }else{
            Session::setFlash(ATXT_NO_POSTS,'warning');
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Files/grid.phtml',['message' => $message]);
        }

    }//END filegridAction()

    public function fileAction(){

        $param = $this->route_params;
        $id = $param['id'];
        $plix = File::getFileById($id);//
        if($plix != FALSE){

            $page_title = $plix[0]['title'];
            $meta_desc = $plix[0]['metadesc'];
            $meta_keys = $plix[0]['metakeys'];
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Files/file.phtml',['plix' => $plix, 'message' => $message, 'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);

        }else{
            Session::setFlash(ATXT_NO_POSTS,'warning');
            $message = Session::getFlash();
            Session::unsetFlash();
            View::render('Files/file.phtml',['message' => $message]);
        }

    }//END filegridAction()

}//END CLASS FRONTEND FILES