<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2018-03-20
 * Time: 13:30
 */

namespace App\Controllers\Admin;


use App\Models\Admin\Category;
use App\Models\Admin\File;
use Core\Controller;
use Core\Filesystem;
use Core\Session;
use Core\View;

class Files extends Controller{

    protected function before(){

    }

    protected function after(){

    }

    public function addNewFileAction(){

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_SESSION['user_group']) && $_SESSION['user_group'] == 1 || $_SESSION['user_group'] == 2){//if not super or not admin cannot upload files

                if(isset($_POST['add-file-title'])){

                    if(isset($_FILES) && !empty($_FILES) && isset($_POST['pick-category']) && $_POST['pick-category'] != 0 && isset($_POST['add-file-title']) && $_POST['add-file-title'] != ''){

                        $file = $_FILES['file_add'];
                        $data = array();
                        $data['title'] = filter_var($_POST['add-file-title'],FILTER_SANITIZE_STRING);
                        $data['category_id'] = $_POST['pick-category'];
                        $data['category_name'] = Category::categoryGetName($data['category_id']);
                        $data['description'] = isset($_POST['add-file-description']) ? $_POST['add-file-description'] : '';
                        $data['metaname'] = isset($_POST['add-file-metaname'])? filter_var($_POST['add-file-metaname'],FILTER_SANITIZE_STRING) : '';
                        $data['metadesc'] = isset($_POST['add_file-metadescription']) ? filter_var($_POST['add_file-metadescription'],FILTER_SANITIZE_STRING) : '';
                        $data['metakeys'] = isset($_POST['add_file-metakeys']) ? filter_var($_POST['add_file-metakeys'],FILTER_SANITIZE_STRING) : '';
                        $prep_path = $data['category_id'].'/';

                        if(!file_exists('../public/upload/files/'.$prep_path.$file['name'])){

                        $new_file_path = Filesystem::uploadFile($file,$prep_path);

                        if($new_file_path != FALSE && $new_file_path != NULL){

                            $data['name'] = end(explode('/',$new_file_path));
                            $data['path'] = $new_file_path;
                            $data['type'] = end(explode('.',$data['name']));
                            $data['archive'] = 0;
                            $data['uploaded_by'] = $_SESSION['user_nick'];
                            $data['downloaded'] = 0;

                            $is_done = File::insertNewFile($data);

                            if($is_done == TRUE){

                                Session::setFlash(ATXT_FILE_SAVED,'ok');
                                $message = Session::getFlash();
                                Session::unsetFlash();

                                $files = File::getFileList();
                                if($files != FALSE){

                                    View::renderAdmin('Files/list.phtml',['files' => $files, 'message' => $message]);

                                }else{

                                    Session::setFlash(ATXT_NO_FILES_LIST,'warning');
                                    $message = Session::getFlash();
                                    Session::unsetFlash();
                                    View::renderAdmin('Files/list.phtml',['message' => $message]);

                                }


                            }else{

                                $files = File::getFileList();
                                if($files != FALSE){

                                    $message = Session::getFlash();
                                    Session::unsetFlash();
                                    View::renderAdmin('Files/list.phtml',['files' => $files, 'message' => $message]);

                                }else{

                                    Session::setFlash(ATXT_NO_FILES_LIST,'warning');
                                    $message = Session::getFlash();
                                    Session::unsetFlash();
                                    View::renderAdmin('Files/list.phtml',['message' => $message]);

                                }

                            }

                          }

                        }else{

                            Session::setFlash(ATXT_FILE_EXISTS_NOT_ADDED,'warning');
                            $message = Session::getFlash();
                            Session::unsetFlash();
                            View::renderAdmin('Files/list.phtml',['message' => $message]);

                        }

                    }else{

                        Session::setFlash(MENUITEM_NOT_ALL_INPUTS,'warning');
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        View::renderAdmin('Files/add.phtml',['message' => $message]);

                    }

                }else{

                    View::renderAdmin('Files/add.phtml');

                }

            }else{

                Session::setFlash(TXT_NO_RIGHTS_TO_UPLOAD_FILES,'error');
                header("location: http://".$_SERVER['SERVER_NAME']."/admin");

            }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END add new file

    public function listAction(){

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_GET['data']) && $_GET['data'] != 0){

              $files = File::getFileListByCat($_GET['data']);

                if($files != FALSE){

                    View::renderAdmin('Files/list.phtml',['files' => $files]);

                }else{

                    Session::setFlash(ATXT_NO_FILES_LIST,'warning');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Files/list.phtml',['message' => $message]);

                }

            }else{

                View::renderAdmin('Files/list.phtml');

            }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END add new file


    public function deleteFileAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if (isset($_SESSION['user_group']) && $_SESSION['user_group'] == 1 || $_SESSION['user_group'] == 2){

                if (isset($_POST)) {

                    $ids = array();

                    foreach ($_POST as $key => $value) {

                        $id = explode('_', $key);
                        if ($id[0] == 'file') {

                            $ids[] = $value;

                        }

                    }

                    $not_deleted_names = File::deleteFile($ids);
                    if (!empty($not_deleted_names)) {

                        $msg = '';
                        foreach ($not_deleted_names as $name) {
                            $msg = $msg . $name . ',';
                        }

                        Session::setFlash(ATXT_FILE_NOT_DELETED . $msg . ' ' . ATXT_FILE_DOES_NOT_EXISTS, 'error');
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        View::renderAdmin('Files/list.phtml', ['message' => $message]);

                    } else {

                        Session::setFlash(ATXT_FILE_DELETED, 'ok');
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        View::renderAdmin('Files/list.phtml', ['message' => $message]);

                    }

                }

            }else{

                Session::setFlash(TXT_NO_RIGHTS_TO_UPLOAD_FILES,'error');
                header("location: http://".$_SERVER['SERVER_NAME']."/admin");

            }
        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END deleteFileAction()

    public function editFileAction(){

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_SESSION['user_group']) && $_SESSION['user_group'] == 1 || $_SESSION['user_group'] == 2){

            if(isset($_POST)){

                $ids = array();

                foreach($_POST as $key => $value){//just in case if somebody check more than one checkbox...

                    $id = explode('_',$key);
                    if($id[0] == 'file'){

                        $ids[] = $value;

                    }

                }

                $edit_id = $ids[0];//...we gonna make sure that only first one will be processed

                $data = File::getFileById($edit_id);

                if($data != FALSE){

                    View::renderAdmin('Files/edit.phtml',['data' => $data[0]]);

                }else{

                    Session::setFlash(ATXT_FILE_NOT_EXISTS,'error');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Files/list.phtml',['message' => $message]);

                }

            }
        }else{

                Session::setFlash(TXT_NO_RIGHTS_TO_UPLOAD_FILES,'error');
                header("location: http://".$_SERVER['SERVER_NAME']."/admin");

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END editFileAction()

    public function editSaveFileAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_SESSION['user_group']) && $_SESSION['user_group'] == 1 || $_SESSION['user_group'] == 2){

                if(isset($_POST['file-id'])){

                    $file_old = File::getFileById($_POST['file-id']);
                    $file_new = array();
                    $file_new['id'] = $_POST['file-id'];
                    $file_new['title'] = $_POST['add-file-title'];
                    $file_new['category_id'] = $_POST['pick-category'];
                    $file_new['old_category_id'] = $file_old[0]['category_id'];
                    $file_new['category_name'] = Category::categoryGetName($_POST['pick-category']);
                    $file_new['description'] = isset($_POST['add-file-description']) ? $_POST['add-file-description'] : '';
                    $file_new['metaname'] = isset($_POST['add-file-metaname']) ? $_POST['add-file-metaname'] : '';
                    $file_new['metadesc'] = isset($_POST['add-file-metadescription']) ? $_POST['add-file-metadescription'] : '';
                    $file_new['metakeys'] = isset($_POST['add-file-metakeys']) ? $_POST['add-file-metakeys'] : '';
                    $file_new['edited_by'] = $_SESSION['user_nick'];
                    $file_new['old_path'] = $file_old[0]['path'];//needed for deleting old file in case file is gonna be brand new
                    $prep_path = $file_new['category_id'].'/';//new path
                    $old_path = $file_new['old_category_id'].'/';//old path


                    if(isset($_FILES) && $_FILES['file_add']['name'] != ''){//if new file instead of old

                        if($file_old[0]['name'] == $_FILES['file_add']['name']){//somebody is trying upload already existing file

                            if($file_new['category_id'] == $file_new['old_category_id']){//just replace some lightweight data in database

                              $updated = File::updateFileLight($file_new);

                                if($updated == TRUE){

                                    Session::setFlash(ATXT_FILE_EDITED,'ok');

                                }

                                $message = Session::getFlash();
                                Session::unsetFlash();
                                View::renderAdmin('Files/list.phtml',['message' => $message]);


                            }else{//means need to just move file to different folder

                                if(file_exists('../public/upload/files/'.$old_path.$_FILES['file_add']['name'])){

                                    if (!file_exists('../public/upload/files/'.$prep_path)) {
                                        mkdir('../public/upload/files/'.$prep_path, 0777, true);
                                    }

                                    if(!rename('../public/upload/files/'.$old_path.$_FILES['file_add']['name'], '../public/upload/files/'.$prep_path.$_FILES['file_add']['name'])){

                                        Session::setFlash(ATXT_FILE_MOVE_PROBLEM,'error');
                                        $message = Session::getFlash();
                                        Session::unsetFlash();
                                        View::renderAdmin('Files/list.phtml',['message' => $message]);

                                    }else{//here we addnew path to database

                                        $file_new['new_path'] = '/upload/files/'.$prep_path.$_FILES['file_add']['name'];
                                        $file_moved = File::changeFileCategory($file_new);
                                        if($file_moved != TRUE){//if some problem with update in database

                                            Session::setFlash(ATXT_FILE_MOVE_PROBLEM,'error');
                                            $message = Session::getFlash();
                                            Session::unsetFlash();
                                            View::renderAdmin('Files/list.phtml',['message' => $message]);

                                        }else{

                                            Session::setFlash(ATXT_FILE_EDITED,'ok');
                                            $message = Session::getFlash();
                                            Session::unsetFlash();
                                            View::renderAdmin('Files/list.phtml',['message' => $message]);

                                        }

                                    }//ALL DONE

                                }else{//if file does not exists

                                    Session::setFlash(ATXT_FILE_DOES_NOT_EXISTS,'error');
                                    $message = Session::getFlash();
                                    Session::unsetFlash();
                                    View::renderAdmin('Files/list.phtml',['message' => $message]);

                                }//ALL DONE


                            }//ALL DONE

                        }else{//this is gonna be brand new file instead of old one

                            if (!file_exists('../public/upload/files/'.$prep_path)) {//if there is no folder - prepare it
                                mkdir('../public/upload/files/'.$prep_path, 0777, true);
                            }

                            $file = $_FILES['file_add'];

                            if(!file_exists('../public/upload/files/'.$prep_path.$file['name'])){//if there is no same file in the path

                                $new_file_path = Filesystem::uploadFile($file,$prep_path);

                                if($new_file_path != FALSE && $new_file_path != NULL){

                                    $file_new['name'] = end(explode('/',$new_file_path));
                                    $file_new['path'] = $new_file_path;
                                    $file_new['type'] = end(explode('.',$file_new['name']));
                                    $file_new['downloaded'] = 0;

                                    $is_changed = File::changeFile($file_new);
                                    if($is_changed != TRUE){

                                        Session::setFlash(ATXT_FILE_MOVE_PROBLEM,'error');
                                        $message = Session::getFlash();
                                        Session::unsetFlash();
                                        $files = File::getFileListByCat($file_new['old_category_id']);
                                        View::renderAdmin('Files/list.phtml',['message' => $message, 'files' => $files]);

                                    }else{

                                        Session::setFlash(ATXT_FILE_EDITED,'ok');
                                        $message = Session::getFlash();
                                        Session::unsetFlash();
                                        $files = File::getFileListByCat($file_new['category_id']);
                                        View::renderAdmin('Files/list.phtml',['message' => $message, 'files' => $files]);

                                    }//ALL DONE

                                }else{//model problem - message set in model

                                    $message = Session::getFlash();
                                    Session::unsetFlash();
                                    $files = File::getFileListByCat($file_new['old_category_id']);
                                    View::renderAdmin('Files/list.phtml',['message' => $message, 'files' => $files]);

                                }//ALL DONE

                            }else{//file already exists

                                Session::setFlash(ATXT_FILE_EXISTS,'error');
                                $message = Session::getFlash();
                                Session::unsetFlash();
                                $files = File::getFileListByCat($file_new['old_category_id']);
                                View::renderAdmin('Files/list.phtml',['message' => $message, 'files' => $files]);

                            }//ALL DONE


                        }//ALL DONE


                    }else{//if there is no new file - moving to other category may be needed

                        if($file_new['category_id'] == $file_new['old_category_id']){//just replace some lightweight data in database

                            $updated = File::updateFileLight($file_new);

                            if($updated == TRUE){

                                Session::setFlash(ATXT_FILE_EDITED,'ok');

                            }

                            $message = Session::getFlash();
                            Session::unsetFlash();
                            View::renderAdmin('Files/list.phtml',['message' => $message]);


                        }else{//moving to other category may be needed

                            if(file_exists('../public/upload/files/'.$old_path.$_FILES['file_add']['name'])){

                                if (!file_exists('../public/upload/files/'.$prep_path)) {
                                    mkdir('../public/upload/files/'.$prep_path, 0777, true);
                                }

                                if(!rename('../public/upload/files/'.$old_path.$file_old[0]['name'], '../public/upload/files/'.$prep_path.$file_old[0]['name'])){

                                    Session::setFlash(ATXT_FILE_MOVE_PROBLEM,'error');
                                    $message = Session::getFlash();
                                    Session::unsetFlash();
                                    View::renderAdmin('Files/list.phtml',['message' => $message]);

                                }else{

                                    $file_new['new_path'] = '/upload/files/'.$prep_path.$file_old[0]['name'];
                                    $file_moved = File::changeFileCategory($file_new);
                                    if($file_moved != TRUE){//if some problem with update in database

                                        Session::setFlash(ATXT_FILE_MOVE_PROBLEM,'error');
                                        $message = Session::getFlash();
                                        Session::unsetFlash();
                                        View::renderAdmin('Files/list.phtml',['message' => $message]);

                                    }else{

                                        Session::setFlash(ATXT_FILE_EDITED,'ok');
                                        $message = Session::getFlash();
                                        Session::unsetFlash();
                                        View::renderAdmin('Files/list.phtml',['message' => $message]);

                                    }

                                }

                            }else{//if file does not exists

                                Session::setFlash(ATXT_FILE_DOES_NOT_EXISTS,'error');
                                $message = Session::getFlash();
                                Session::unsetFlash();
                                View::renderAdmin('Files/list.phtml',['message' => $message]);

                            }//ALL DONE

                        }

                    }

                }

            }else{

                Session::setFlash(TXT_NO_RIGHTS_TO_UPLOAD_FILES,'error');
                header("location: http://".$_SERVER['SERVER_NAME']."/admin");

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END editFileAction()



}//END CLASS