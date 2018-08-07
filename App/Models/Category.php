<?php
/**
 * Created by PhpStorm.
 * User: BBJaga
 * Date: 2016-09-08
 * Time: 13:56
 */

namespace App\Models;

use Core\Model;
use Core\Session;

class Category extends Model{

    public static function getSubcategories($parent_id, $show_parent = 0){//if show parent = 0, will get no parent data to display, if 1 will get parent data and will display it as main
        $db = Model::getDB();
        $subcategories = array();

        if($show_parent == 1){
            $query = ("SELECT * FROM categories WHERE id = '$parent_id'");
            $success = $db->query($query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }
            $found = $success-> num_rows;
            if($found > 0){
                while( $result = $success->fetch_assoc() ){
                    $subcategories['parent'] = $result;
                }
            }
        }

        $query = ("SELECT * FROM categories WHERE parent_id = '$parent_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success-> num_rows;
        if($found > 0){

            while( $result = $success->fetch_assoc() ){
                array_push($subcategories,$result);
            }
            $db->close();
            return $subcategories;

        }else{

            $db->close();
            return FALSE;
        }

    }//END function getSubcategories($parent_id)

    public static function getCategoryName($cat_id){
        $db = Model::getDB();
        $query = ("SELECT name FROM categories WHERE id = '$cat_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $result = $success->fetch_row();
        return $result;
    }//END function getCategoryName($cat_id)

    public static function getCategoryTypeAndView($cat_id){

        $db = Model::getDB();

        $data = array();
        $query = ("SELECT type FROM categories WHERE id = '$cat_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $result = $success->fetch_row();
        $type = $result[0];

        $query = ("SELECT type_view FROM categories_type WHERE id = '$type[0]'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }


        $result = $success->fetch_row();
        $data['view'] = $result[0];
        $data['type'] = $type;

        return $data;
    }//END function getCategoryTypeAndView($cat_id)

    public static function getCategoryByID($cat_id, $cat_type){//  - returns existing category all data
        $db = Model::getDB();
        if(isset($cat_id) && isset($cat_type)){
            $cat_show = ("SELECT * FROM categories WHERE id = '$cat_id' AND type = '$cat_type' LIMIT 1");
            $result = $db->query($cat_show);
            if(!$result){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{
                if($result->num_rows > 0){
                    $category_data = $result->fetch_array();
                    $db->close();
                    return $category_data;
                }else{
                    Session::setFlash(ATXT_NO_CATEGORIES,'error');
                    $db->close();
                    return FALSE;
                }
            } //end if(!$result)
        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.' AMCA-012-344','error');
            $db->close();
            return FALSE;
        }

    }//END getCategoryByID FUNCTION


}//END CLASS