<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-30
 * Time: 17:19
 */

namespace App\Models\Admin;

use \DOMDocument;
use Core\Lang;
use Core\Session;
use Core\Model;

/**
 * Class Category
 * @package App\Models\Admin
 */
class Category extends Model{


    /**
     * @param $data
     * @return string
     */
    public static function getCategoryTypeName($data){//AMCA-001

        $ct_name = '';
        switch ($data){
            case 1:
                $ct_name = AMENU_CATEGORY_TYPE_POSTS;
                break;
            case 2:
                $ct_name = AMENU_CATEGORY_TYPE_GALLERY;
                break;
            case 3:
                $ct_name = AMENU_CATEGORY_TYPE_FORMS;
                break;
            case 4:
                $ct_name = AMENU_CATEGORY_TYPE_ARCHIVES;
                break;
            case 5:
                $ct_name = AMENU_CATEGORY_TYPE_NEWS;
                break;
            case 6:
                $ct_name = AMENU_CATEGORY_TYPE_CLASSES;
                break;
            case 7:
                $ct_name = AMENU_CATEGORY_TYPE_FILES;
                break;
            default:
                $ct_name = '???';
                break;
        }
        return $ct_name;
    }

    /**
     * @return array
     * gets list of category types from types table
     */
    public static function getType(){//AMCA-002
        $db = Model::getDB();
        $tlist = array();
        $ctypes = ("SELECT * FROM categories_type ORDER BY type_name ASC");
        $result = $db->query($ctypes);
        if (!$result) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while ($type = $result->fetch_assoc()) {
                array_push($tlist, $type);
            }
        }
        $db->close();
        return $tlist;
    }

    /**
     * @param $parent_id
     * @return bool
     * changes categories haschildren during adding new category into a database
     */
    public static function makeParent($parent_id){//AMCA-003
        $db = Model::getDB();
        $make_parent = ("SELECT * FROM categories WHERE parent_id = '$parent_id'");
        $result = $db->query($make_parent);
        if (!$result) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        } else {
            if ($result->num_rows > 0){
                $change = ("UPDATE categories SET haschildren = 1 WHERE id='$parent_id'");
                $run = $db->query($change);
                if (!$run){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }else{
                    $updated = TRUE;
                }
            }else{

                $updated = FALSE;

            }
        }
        return $updated;
    }


    public static function addNewCategory($data=array(),$image_path = ''){//AMCA-004
        $db = Model::getDB();
       $name = $data['cat_name'];
       $parent_id = ($data['cat_parent'] != 'none' && $data['cat_parent'] != 'main') ? $data['cat_parent'] : 0;
        if($parent_id == 0){
            $level = 0;
        }else{
            $query_level = ("SELECT * FROM categories WHERE id = '$parent_id'");
            $result = $db->query($query_level);
            while($l = $result->fetch_assoc()){
                $level = $l['level']+1;
            }
        }
        $type = $data['cat_type'];
        $haschildren = 0;
        $description = $data['cat_description'];
        $metatitle = $data['cat_seo_link'];
        $metakeys = $data['cat_metakeys'];
        $metadescription = $data['cat_metadescription'];
       $category = ("INSERT INTO categories (name, parent_id, type, haschildren, level,description, metatitle, metakeys, metadescription, image) VALUES ('$name', '$parent_id', '$type', '$haschildren', '$level','$description', '$metatitle', '$metakeys', '$metadescription', '$image_path')");
       $result = $db->query($category);
        if(!$result) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            self::setAvaiability($type, 1);
            self::makeParent($parent_id);
            $messasge = AMENU_CATEGORY.' '.$name.' '.ATXT_CATEGORY_ADDED;
            Session::setFlash($messasge, 'ok');
            $db->close();
        }
    }

    /**
     * @param $level
     * @return array
     * returns multidimensional array of categories - parent and children elements each as an array
     */
    public static function categoryGetList($level, $ctype_id){//AMCA-005
        $db = Model::getDB();
        $children = array();
        if(isset($level)){
            $sub_cat = ("SELECT * FROM categories WHERE parent_id = '$level' AND type = '$ctype_id'");
            $result = $db->query($sub_cat);
            if (!$result) {
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            } else{
                if ($result->num_rows > 0){
                    while ($sub = $result->fetch_assoc()) {
                        if($sub['haschildren'] == 1){
                            array_push($children, $sub);
                            array_push($children, self::categoryGetList($sub['id'], $ctype_id));
                        }else{
                            array_push($children, $sub);
                        }
                    }

                }else{
                    Session::setFlash(ATXT_NO_CATEGORIES, 'ok');
                }
            }
        }
        $db->close();
        return $children;
    }

    /**
     * @param $Array
     * @param int $count
     * @return int
     * count depth of multidimensional array (just in case)
     */
    public static function countArrayLevels($Array, $count = 1){//AMCA-006
        if(is_array($Array)) {
            return self::countArrayLevels(current($Array), ++$count);
        } else {
            return $count;
        }
    }

    /**
     * @param $array //for categories select - displays list of categories
     * @param $position is for "selected" option
     * need to be embraced in select and form
     */
    public static function recursivePrint($array){//AMCA-007
        $printed = array(); // to not show multiple times same values
            foreach($array as $key => $value){
            //If $value is an array.
            if(is_array($value)){
                //We need to loop through it.
               self::recursivePrint($value);
            } else{
                //It is in array means it's printed already so do nothing.
                 if(in_array($array['id'], $printed)){

                 }else{
                     //It is not in array, so print it out.
                     $cat_type_name = self::getCategoryTypeName($array["type"]);
                     echo '<option data-subtext="'.$cat_type_name.'" value="'.$array["id"].'" data-tokens="'.$array["name"].'">'.str_repeat("- ", $array["level"]).''.$array["name"].'</option>';
                     array_push($printed, $array['id']);
                 }
            }
        }
    }//END recursivePrint($array) FUNCTION


    /**
     * @param $array
     * @param $i
     * for I did not find better method to display category list recursively
     *
     */
    public static function recursivePrintList($array, $i){//AMCA-008
        $printed = array(); // to not show multiple times same values
        foreach($array as $key => $value){
            //If $value is an array.
            if(is_array($value)){
                //We need to loop through it.
                self::recursivePrintList($value, $i++);
            } else{
                //It is not an array, so print it out.
                if(in_array($array['id'], $printed)){

                }else{
                    $margin = $array["level"] * 2;
                    if($i % 2 == 0){
                        echo '<div class="row second-row" style="border-left: 1px solid silver;">';
                    }else{
                        echo '<div class="row" style="border-left: 1px solid silver;">';
                    }
                    echo '
                            <div class="col-lg-1 table-cell-left">
                                <img class="img-responsive thumbnail" src="'.$array["image"].'" />
                            </div>
                            <div class="col-lg-2 table-cell-left">';
                               echo str_repeat("- ", $array["level"]).''.$array["name"];
                     echo ' </div>
                            <div class="col-lg-4 table-cell-left">';
                                if(strlen($array["description"]) > 240){
                                                echo '<div class="description-in-list"><i>[...]</i> '.filter_var(substr($array["description"],20,240), FILTER_SANITIZE_STRING).' <i>[...]</i></div>';
                                 }else{
                                   echo  '<div class="description-in-list">'.$array["description"].'</div>';
                                }
                     echo ' </div>
                            <div class="col-lg-1 table-cell-left">';
                                echo $array["metatitle"];
                      echo '</div>
                            <div class="col-lg-2 table-cell">';
                               if(!empty($array["metadescription"])){
                                   echo '<span class="span-green">tak</span>';
                               }else{
                                   echo '<span class="span-red">brak</span>';
                               }
                      echo '</div>
                            <div class="col-lg-1 table-cell">';
                                if(!empty($array["metakeys"])){
                                    echo '<span class="span-green">tak</span>';
                                }else{
                                    echo '<span class="span-red">brak</span>';
                                }
                      echo '</div>';
                               //if category has children we cannot delete it, so we'll not print button delete. Delete and edit start with click and ajax call(cimesCms.js)
                            if($array['haschildren'] == 1){
                                echo '<div class="col-lg-1 table-cell">';
                                echo '<form name="cat-edit-form" role="form" action="../categories/edit" method="post" id="cat-edit-form">';
                                echo '<input type="hidden" name="cat_id" id="cat_id" value="'.$array['id'].'">';
                                echo '<input type="hidden" name="cat_type" id="cat_type" value="'.$array['type'].'">';
                                echo '<div class="button-group"><button type="submit" class="btn-cat-edit" id="catedit_'.$array['id'].'_'.$array['type'].'">'.AMENU_EDIT.'</button></div>';
                                echo '</form>';
                                echo '</div>';
                            }else{
                                echo '<div class="col-lg-1 table-cell">';
                                echo '<form name="cat-edit-form" role="form" action="../categories/edit" method="post" id="cat-edit-form">';
                                echo '<input type="hidden" name="cat_id" id="cat_id" value="'.$array['id'].'">';
                                echo '<input type="hidden" name="cat_type" id="cat_type" value="'.$array['type'].'">';
                                echo '<div class="button-group"><button type="submit" class="btn-cat-edit" id="catedit_'.$array['id'].'_'.$array['type'].'">'.AMENU_EDIT.'</button></div>';
                                echo '</form>';

                                echo '<form name="cat-delete-form" role="form" action="../categories/delete" method="post" id="cat-delete-form">';
                                echo '<input type="hidden" name="cat_id" id="cat_id" value="'.$array['id'].'">';
                                echo '<input type="hidden" name="cat_type" id="cat_type" value="'.$array['type'].'">';
                                echo '<div class="button-group"><button type="submit" class="btn-cat-delete" id="catdel_'.$array['id'].'_'.$array['type'].'">'.AMENU_DELETE.'</button></div>';
                                echo '</form>';
                                echo '</div>';
                            }
                       echo '</div>';

                    array_push($printed, $array['id']);
                    $i++;
                }
            }
        }
    }// END RECURSIVE PRINT LIST FUNCTION

    public static function deleteCategory($cat_id, $cat_type){//AMCA-009
        $db = Model::getDB();
        if(isset($cat_id) && isset($cat_type)){
            $cat_delete = ("DELETE FROM categories WHERE id = '$cat_id' AND type = '$cat_type'");
            $result = $db->query($cat_delete);
            if(!$result){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{//after we have it deleted must check now how children-parent relations changed
                $check_children = ("SELECT * FROM categories");//so we select all categories and loop through them
                $r = $db->query($check_children);
                while($loop = $r->fetch_assoc()){//and during loop must check if there is other category that has parent_id equal to currently checked category id
                    $has_child = self::categoryCheckChildren($loop['id']);
                    if($has_child == TRUE){//if has it we must put it into haschildren column value 1
                        self::categoryUpdateChildren($loop['id'], 1);
                    }else{//if has it we must put it into haschildren column value 0
                        self::categoryUpdateChildren($loop['id'], 0);
                    }
                }
                //if its don now it's time to update categories_type avaiability (check if ist is any child in this particular type)
                $type_has_children = self::checkAvaiability($cat_type);
                if($type_has_children > 0){
                    Session::setFlash(ATXT_CATEGORY_DELETED,'ok');
                    $db->close();
                    return TRUE;
                }else{
                    self::setAvaiability($cat_type,0);
                    $db->close();
                    return TRUE;
                }
            }
        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.' AMCA-009-250','error');
            $db->close();
            return FALSE;
        }
    }// END deleteCategory FUNCTION


    public static function categoryCheckChildren($cat_id){//AMCA-010   - checks if category has children
        $db = Model::getDB();
        $check = ("SELECT * FROM categories WHERE parent_id = '$cat_id'");
        $has_it = $db->query($check);
        if($has_it->num_rows > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }//END categoryCheckChildren FUNCTION

    public static function categoryUpdateChildren($cat_id, $value){//AMCA-011   - update has children if changed
        $db = Model::getDB();
        $change = ("UPDATE categories SET haschildren = '$value' WHERE id='$cat_id'");
        $run_q = $db->query($change);
        if(!$run_q){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            return TRUE;
        }
    }//END categoryUpdateChildren FUNCTION

    public static function getCategoryByID($cat_id, $cat_type){//AMCA-012   - returns existing category all data
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

    public static function getCategoryParentName($parent_id){//AMCA-013 gets name of parent category
      $db = Model::getDB();
      $parent = ("SELECT name FROM categories WHERE id = '$parent_id' LIMIT 1");
        $result = $db->query($parent);
        if(!$result){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            if($result->num_rows > 0){
                $category_data = $result->fetch_row();
                $db->close();
                return $category_data;
            }else{
                Session::setFlash(ATXT_NO_CATEGORIES,'error');
                $db->close();
                return FALSE;
            }
        }
    }//END getCategoryParentName($parent_id)

    public static function editCategory($data=array()){//AMCA-014 edits category image, name etc
        $db = Model::getDB();
        $id = $data['admin_edit_category_id'];
        $name = $data['edit_catname'];
        $description = $data['edit_catdescription'];
        $metatitle = $data['edit_metacatname'];
        $metadescription = $data['edit_metacatdescription'];
        $metakeys = $data['edit_metacatkeys'];
        $image = $data['category_image'];

        $upuser_queue = ("
          UPDATE categories
          SET name = '$name',
          description = '$description',
          metatitle = '$metatitle',
          metakeys = '$metakeys',
          metadescription = '$metadescription',
          image = '$image'
          WHERE id = '$id'
         ");

        $result = $db->query($upuser_queue);
        if(!$result){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            Session::setFlash(ATXT_CATEGORY_EDITED,'ok');
            $db->close();
            return true;
        }
    }// END editCategory()

    public static function setAvaiability($id, $value = 0){//AMCA-015 //need to be called after every new category was added - for us to know if display this position in menu add option - if has no items than displaing will be pointless
        $db = Model::getDB();
        $set_query = ("UPDATE categories_type SET type_has_items = '$value' WHERE type_id = '$id'");
        $success = $db->query($set_query);
        if(!$success){
            Session::setFlash(ATXT_UNKNOWN_ERROR.' AMCA-015-430','error');
            $db->close();
            return FALSE;
        }else{
            $db->close();
            return TRUE;
        }
    }//end setAvaiability($id, $value = 0)

    public static function checkAvaiability($id){//AMCA-016 TO DO FIRST //checks if there is other categories this type before changing flag as no-avaiable (useful after delete some category)
        $db = Model::getDB();
        $childrens_count = 0;
        $check_query = ("SELECT COUNT(*) AS categoriesCount FROM categories WHERE type = '$id'");
        $success = $db->query($check_query);
        if(!$success){
            Session::setFlash(ATXT_UNKNOWN_ERROR.' AMCA-016-470','error');
            $db->close();
            return FALSE;
        }else{
            $found = $success->fetch_assoc();
                $childrens_count = $found['categoriesCount'];
                $db->close();
                return $childrens_count;
        }
    }//end scheckAvaiability()

    public static function checkForEmpty(){////AMCA-017 if even just one of types not empty - return true
        $db = Model::getDB();
        $check_query = ("SELECT COUNT(*) AS notEmpty FROM categories_type WHERE type_has_items !=0 ");
        $success = $db->query($check_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $success->fetch_assoc();
            $childrens_count = $found['notEmpty'];
            $db->close();
            return $childrens_count;
        }
    }//end checkForEmpty()

    public static function getParentsList(){////AMCA-017
        $db = Model::getDB();
        $data = array();
        $list_query = ("SELECT * FROM categories WHERE parent_id = 0");
        $success = $db->query($list_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $success->num_rows;
            if($found > 0){
                while($result = $success->fetch_assoc()){
                    array_push($data, $result);
                }
                $db->close();
                return $data;
            }else{
                $db->close();
                return FALSE;
            }
        }
    }//end getParentsList()

    public static function getAllCategories(){////AMCA-018 gets list aff all avaiable categories of each type and than print it out
        $cat_types_avaiable = array();
        $cat_types = self::getType();//first get all types
        foreach($cat_types as $type){//than checks if there are items in type and if so pushes it to avaiable types
            if($type['type_has_items'] > 0){
                array_push($cat_types_avaiable, $type);
            }
        }
        $categories_tree = array();
        foreach($cat_types_avaiable as $type_avaiable){//than gets list of categories for every given (avaiable) type and pushes it to the list
            $single_cat_tree = self::categoryGetList(0,$type_avaiable['type_id']);
            array_push($categories_tree,$single_cat_tree);
        }
      return $categories_tree;

    }//END getAllCategories()

    public static function categoryGetName($id){////AMCA-019
        $db = Model::getDB();
        $cname = '';
        $get_name = ("SELECT name FROM categories WHERE id = '$id'");
        $success = $db->query($get_name);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success->fetch_assoc()){
                $cname = $result['name'];
            }

            return $cname;
        }
    }//END getAllCategories()

    public static function setTypeView($data){

        $db = Model::getDB();

        foreach($data as $key => $value){
            $temp_name = $key;
            if($value == 1){
                $default_view = 'grid';
            }else{
                $default_view = 'list';
            }

            $query = ("UPDATE categories_type SET type_view = '$default_view' WHERE type_name = '$temp_name'");
            $success = $db->query($query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }
        }

     return TRUE;

    }//end function setTypeView($data)

}// END CATEGORY CLASS