<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2018-03-20
 * Time: 13:32
 */

namespace App\Models\Admin;

use Core\Model;
use Core\Session;

class File extends Model{

    public static function getFCategories(){

        $db = Model::getDB();
        $clist = array();
        $query = ("SELECT * FROM categories WHERE type = 7 ORDER BY id ASC");
        $result = $db->query($query);
        if (!$result) {
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{

            $found = $result->num_rows;
            if($found > 0){
                while ($type = $result->fetch_assoc()) {
                    array_push($clist, $type);
                }
                $db->close();
                return $clist;
            }else{

                $db->close();
                return FALSE;

            }

        }

    }//END function getFCategories()

    public static function getFileList(){

        $db = Model::getDB();
        $flist = array();
        $query = ("SELECT * FROM files ORDER BY date_added ASC");
        $result = $db->query($query);
        if (!$result) {
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $result->num_rows;
            if($found > 0){
                while ($files = $result->fetch_assoc()) {
                    array_push($flist, $files);
                }
                $db->close();
                return $flist;
            }else{

                $db->close();
                return FALSE;

            }

        }

    }//END function getFileList()

    public static function getFileById($file_id){

        $db = Model::getDB();
        $file = array();
        $query = ("SELECT * FROM files WHERE id = '$file_id'");
        $success = $db->query($query);
        if (!$success) {
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){

            while ($files = $success->fetch_assoc()) {
                array_push($file, $files);
            }

            $db->close();
            return $file;

        }else{

            $db->close();
            return FALSE;

        }

    }//END function getFileById($file_id)

    public static function getFileListByCat($category_id){

        $db = Model::getDB();
        $flist = array();
        $query = ("SELECT * FROM files where category_id='$category_id' ORDER BY date_added ASC");
        $result = $db->query($query);
        if (!$result) {
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $result->num_rows;
            if($found > 0){
                while ($files = $result->fetch_assoc()) {
                    array_push($flist, $files);
                }
                $db->close();
                return $flist;
            }else{

                $db->close();
                return FALSE;

            }

        }

    }//END function getFileList()

    public static function insertNewFile($data){

        $db = Model::getDB();

        $title = $data['title'];
        $category_id = $data['category_id'];
        $category_name = $data['category_name'];
        $description = $data['description'];
        $metaname = $data['metaname'];
        $metadesc = $data['metadesc'];
        $metakeys = $data['metakeys'];
        $name = $data['name'];
        $path = $data['path'];
        $type = $data['type'];
        $archive = $data['archive'];
        $uploaded_by = $data['uploaded_by'];
        $downloaded = $data['downloaded'];


        $query = ("INSERT INTO files (
                    title,
                    category_id,
                    category_name,
                    description,
                    metaname,
                    metadesc,
                    metakeys,
                    name,
                    path,
                    type,
                    archive,
                    date_added,
                    uploaded_by,
                    downloaded
                  )  VALUES (
                     '$title',
                     '$category_id',
                     '$category_name',
                     '$description',
                     '$metaname',
                     '$metadesc',
                     '$metakeys',
                     '$name',
                     '$path',
                     '$type',
                     '$archive',
                     NOW(),
                     '$uploaded_by',
                     '$downloaded'
                  )");

        $success = $db->query($query);
        if(!$success){
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        return TRUE;

    }//END function insertNewFile($data)

    public static function deleteFile($ids){

        $db = Model::getDB();
        $not_deleted_files = array();
        $flag = 0;

            foreach($ids as $key => $value){

                $file_id = $value;
                $query = ("SELECT * FROM files WHERE id = '$file_id'");
                $result = $db->query($query);
                if (!$result) {
                    Session::setFlash($db->error ,'error');
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }

                $found = $result->num_rows;
                if($found > 0){

                    while ($file = $result->fetch_assoc()) {

                        $path = $file['path'];

                       if(is_file(ROOT_DIR.$path)){

                          unlink(ROOT_DIR.$path);
                          $flag = 0;

                        }else{

                           $flag = 1;

                       }

                        if($flag == 0){

                            $del_query = ("DELETE FROM files WHERE id = '$file_id'");

                            $success = $db->query($del_query);
                            if (!$success) {
                                Session::setFlash($db->error ,'error');
                                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                            }

                        }else{

                            $not_deleted_files[] = $file['name'];

                        }

                    }

                }

            }

        $db->close();
        return $not_deleted_files;

    }//END function deleteFile()

    public static function updateFileLight($data){

        $db = Model::getDB();
        $id = $data['id'];
        $title = $data['title'];
        $description = $data['description'];
        $metaname = $data['metaname'];
        $metadesc = $data['metadesc'];
        $metakeys = $data['metakeys'];
        $edited_by = $data['edited_by'];

        $update_query = ("UPDATE files SET
                          title = '$title',
                          description = '$description',
                          metaname = '$metaname',
                          metadesc = '$metadesc',
                          metakeys = '$metakeys',
                          date_edited = NOW(),
                          edited_by = '$edited_by'
                          WHERE id = '$id'
                          ");

        $success = $db->query($update_query);
        if(!$success){
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        return TRUE;

    }//END function updateFileLight()

    public static function changeFileCategory($data){

        $db = Model::getDB();
        $id = $data['id'];
        $title = $data['title'];
        $category_id = $data['category_id'];
        $category_name = Category::categoryGetName($category_id);
        $description = $data['description'];
        $metaname = $data['metaname'];
        $metadesc = $data['metadesc'];
        $metakeys = $data['metakeys'];
        $edited_by = $data['edited_by'];
        $path = $data['new_path'];

        $update_query = ("UPDATE files SET
                          title = '$title',
                          category_id = '$category_id',
                          category_name = '$category_name',
                          description = '$description',
                          metaname = '$metaname',
                          metadesc = '$metadesc',
                          metakeys = '$metakeys',
                          path = '$path',
                          date_edited = NOW(),
                          edited_by = '$edited_by'
                          WHERE id = '$id'
                          ");

        $success = $db->query($update_query);
        if(!$success){
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        return TRUE;

    }//END function changeFileCategory($data)

    public static function changeFile($data){

        $db = Model::getDB();
        $id = $data['id'];
        $title = $data['title'];
        $category_id = $data['category_id'];
        $category_name = $data['category_name'];
        $description = $data['description'];
        $metaname = $data['metaname'];
        $metadesc = $data['metadesc'];
        $metakeys = $data['metakeys'];
        $name = $data['name'];
        $path = $data['path'];
        $type = $data['type'];
        $edited_by = $data['edited_by'];
        $downloaded = $data['downloaded'];
        $old_path = $data['edited_by'];

        if(is_file(ROOT_DIR.$old_path)){

            unlink(ROOT_DIR.$old_path);

        }

        $update_query = ("UPDATE files SET
                          title = '$title',
                          category_id = '$category_id',
                          category_name = '$category_name',
                          description = '$description',
                          metaname = '$metaname',
                          metadesc = '$metadesc',
                          metakeys = '$metakeys',
                          name = '$name',
                          path = '$path',
                          type = '$type',
                          date_edited = NOW(),
                          downloaded = '$downloaded',
                          edited_by = '$edited_by'
                          WHERE id = '$id'
                          ");

        $success = $db->query($update_query);
        if(!$success){
            Session::setFlash($db->error ,'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

    }//END function changeFile($data)

}//END CLASS