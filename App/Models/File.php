<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2018-03-26
 * Time: 09:08
 */

namespace App\Models;


use Core\Model;
use Core\Session;

class File extends Model
{

    public static function getFilesFromCategory($category_id,$show_parent = 0){//FrMPo 0003

        $db = Model::getDB();
        $files = array();

        if($show_parent == 1){
            $query = ("SELECT * FROM categories WHERE id = '$category_id'");
            $success = $db->query($query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }
            $found = $success-> num_rows;
            if($found > 0){
                while( $result = $success->fetch_assoc() ){
                    $files['parent'] = $result;
                }
            }
        }

        $db_query = ("SELECT * FROM files WHERE category_id = '$category_id' ORDER BY date_edited DESC");
        $success = $db->query($db_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                array_push($files, $result);
            }
            $db->close();
            return $files;
        }else{
            $db->close();
            return FALSE;
        }

    }//END function getPostsFromCategory($category_id)

    public static function getFileById($file_id){

        $db = Model::getDB();
        $file = array();

        $db_query = ("SELECT * FROM files WHERE id = '$file_id'");
        $success = $db->query($db_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                array_push($file, $result);
            }
            $db->close();
            return $file;
        }else{
            $db->close();
            return FALSE;
        }

    }//END function getFileById($file_id)

}//END CLASS