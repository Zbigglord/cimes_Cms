<?php
/**
 * Created by BBJaga.
 * Date: 2016-08-31
 * Time: 08:49
 */

namespace App\Models\Admin;

use Core\Model;
use Core\Session;
use App\Models\Admin\Category;
use App\Models\Admin\Course;

class Menu extends Model{
//here we reference menu as just container for MENUITEMS - so it is crucial to distinct difference!

    public static function addNew($data = array()){//AMMnu 0001
        $db = Model::getDB();
        if(!empty($data)){
            $menu_name = $data['add_menu_name'];
            $menu_title = $data['add_menu_title'];
            $menu_is_vertical = isset($data['menu_is_vertical']) ? 1 : 0;
            $menu_show_title = isset($data['menu_show_title']) ? 1 : 0;

            $menu_has_items = 0;

            $add_query = ("INSERT INTO menus (menu_name, menu_title, menu_show_title, menu_is_vertical, menu_has_items) VALUES ('$menu_name','$menu_title','$menu_show_title','$menu_is_vertical','$menu_has_items')");
            $success = $db->query($add_query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{
                Session::setFlash(AMENU_ADDED_TO_DATABASE,'ok');
                $db->close();
                return true;
            }
        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.': error nbr.: AMMnu 0001 043','error');
            $db->close();
            return false;
        }
    }// end function addNew($data = array())

    public static function getMenus(){//AMMnu 0002
        $db = Model::getDB();
        $menu_list = array();
        $list_query = ("SELECT * FROM menus ORDER BY id ASC");
        $success = $db->query($list_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success -> fetch_assoc()){
                array_push($menu_list,$result);
            }
            $db->close();
            return $menu_list;
        }
    }// end function getMenus()


    public static function editMenu($data = array()){//AMMnu 0003
        $db = Model::getDB();
        if(!empty($data)){
            $menu_id = $data['edit_menu_id'];
            $menu_name = $data['edit_menu_name'];
            $menu_title = $data['edit_menu_title'];
            $menu_is_vertical = isset($data['menu_is_vertical']) ? 1 : 0;
            $menu_show_title = isset($data['menu_show_title']) ? 1 : 0;

            $edit_query = ("UPDATE menus SET menu_name = '$menu_name', menu_title = '$menu_title', menu_show_title = '$menu_show_title', menu_is_vertical = '$menu_is_vertical' WHERE id = '$menu_id'");
            $success = $db->query($edit_query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{
                Session::setFlash(AMENU_ADDED_TO_DATABASE,'ok');
                $db->close();
                return true;
            }
        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.': error nbr.: AMMnu 0003 092','error');
            $db->close();
            return false;
        }
    }// end function addNew($data = array())

    public static function getMenuData($id){//AMMnu 0004
        $db = Model::getDB();
        $menu_data = array();
        $data_query = ("SELECT * FROM menus WHERE id = '$id' LIMIT 1");
        $success = $db->query($data_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success -> fetch_assoc()){
                array_push($menu_data,$result);
            }
            $db->close();
            return $menu_data;
        }
    }//end function getMenuData($id)

    public static function deleteMenu($id){//AMMnu 0005
        $db = Model::getDB();
        $has_items = TRUE;
        if(isset($id) && $id != 0){
            $check_items = ("SELECT menu_has_items FROM menus WHERE id = '$id'");
            $success = $db->query($check_items);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{
                $found = $success->num_rows;
                if($found > 0){
                    while($result = $success->fetch_assoc()){
                       if($result['menu_has_items'] > 0){
                           $has_items = TRUE;
                       }else{
                           $has_items = FALSE;
                       }
                    }
                }
                if($has_items != TRUE){
                    $delete_query = ("DELETE FROM menus WHERE id = '$id'");
                    $success = $db->query($delete_query);
                    if(!$success){
                        die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                    }else{
                        Session::setFlash(AMENU_MENU_DELETED,'ok');
                        $db->close();
                        return true;
                    }
                }else{//this one is just for 'in case' type situation as button to delete menu that has items will be disabled
                    Session::setFlash(ATXT_UNKNOWN_ERROR.': error nbr.: AMMnu 0004 134','error');
                    $db->close();
                    return false;
                }
            }
        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.': error nbr.: AMMnu 0004 120','error');
            $db->close();
            return false;
        }
    }//end deleteMenu($id)

    public static function getMenuTypes(){//AMMnu 0006
        $db = Model::getDB();
        $menu_data = array();
        $select_query = ("SELECT * FROM menu_type_list");
        $success = $db->query($select_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $success->num_rows;
            if($found > 0){
                while($result = $success -> fetch_assoc()){
                    array_push($menu_data,$result);
                }
                $db->close();
                return $menu_data;
            }else{
                $db->close();
                return false;
            }
        }
    }//end getMenuTypes()

    public static function getMenuTypeName($id){//AMMnu 0007
        $db = Model::getDB();
        $type_name = '';
        $select_query = ("SELECT * FROM menu_type_list WHERE menu_type_list_id = '$id'");
        $success = $db->query($select_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $success->num_rows;
            if($found > 0){
                while($result = $success -> fetch_assoc()){
                    $type_name = $result['menu_type_list_name'];
                }
                $db->close();
                return $type_name;
            }else{
                $db->close();
                return false;
            }
        }
    }//end getMenuTypeName()

    public static function getTypeAvaiability($id){//AMMnu 0008 - checks if category type has some inserts like articles or so - if not - make is unavaiable in select till type has no children
        $is_avaiable = Category::checkAvaiability($id);
        return $is_avaiable;//value will be 0 for no and number for number of children in this type
    }//end getTypeAvaiability()

    public static function getMenuitemListByMenu($parent_menu_id){//AMMnu 0008
        $db = Model::getDB();
        $menu_data = array();
        $get_query = ("SELECT * FROM menu_item WHERE menu_parent_menu_id = '$parent_menu_id'");
        $success = $db->query($get_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $success->num_rows;
            if($found > 0){
                while($result = $success -> fetch_assoc()){
                    array_push($menu_data,$result);
                }
                $db->close();
                return $menu_data;
            }else{
                $db->close();
                return false;
            }
        }
    }
    /**
     * @param $level
     * @return array
     * returns multidimensional array of categories - parent and children elements each as an array
     */
    public static function menuitemGetList($level, $parent_menu_id){//AMCA-0009
        $db = Model::getDB();
        $children = array();
        $ready = array();
        if(isset($level)){
            $sub_cat = ("SELECT * FROM menu_item WHERE menu_parent_item_id = '$level' AND menu_parent_menu_id = '$parent_menu_id'");
            $result = $db->query($sub_cat);
            if (!$result) {
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            } else{
                if ($result->num_rows > 0){
                    while ($sub = $result->fetch_assoc()) {
                        if($sub['menu_has_child'] == 1){
                            array_push($children, $sub);
                            array_push($children, self::menuitemGetList($sub['menu_item_id'], $parent_menu_id));
                        }else{
                            array_push($children, $sub);
                        }
                    }

                }else{
                    Session::setFlash(MENUITEM_NO_MENUITEM, 'ok');
                    $db->close();
                    return FALSE;
                }
            }
            $db->close();
            return $children;
        }
        $db->close();
        return FALSE;

    }

    public static function printMenuList($array, $printed = array()){
        foreach($array as  $item => $value){
            if(is_array($value)){
                    self::printMenuList($value, $printed);
            }else{
                if($array["menu_item_id"] != ''){
                    if(in_array($array["menu_item_id"], $printed)){

                    }else{
                        echo '<option data-subtext="'.constant($array['menu_type_name']).'" value="'.$array["menu_item_id"].'" data-tokens="'.$array["menu_item_name"].'">'.str_repeat('- ',$array["menu_level"]).''.$array["menu_item_name"].'</option>';
                        array_push($printed, $array["menu_item_id"]);
                    }
                }
            }
        }
    }

    public static function printMenuListFlat($array, $printed = array()){
        foreach($array as  $item => $value){
            if(is_array($value)){
                self::printMenuListFlat($value, $printed);
            }else{
                if($array["menu_item_id"] != ''){
                    if(in_array($array["menu_item_id"], $printed)){

                    }else{
                        echo '<div class="row menuitem-list-row">';
                        echo '<div class="col-lg-3">';
                        if($array["menu_level"] == 0){
                            echo '<strong>';
                        }
                        echo str_repeat('- ',$array["menu_level"]).''.$array["menu_item_name"];
                        if($array["menu_level"] == 0){
                            echo '</strong>';
                        }
                        echo '</div>';
                        echo '<div class="col-lg-3">'.constant($array['menu_type_name']).'</div>';
                        echo '<div class="col-lg-1">';
                        echo '<form role="form" method="post" name="form-menuitem-edit" action="/admin/menus/editmenuitem" id="form-menuitem-edit_'.$array["menu_item_id"].'">';
                        echo '<input type="hidden" id="menuitem-edit-id_'.$array["menu_item_id"].'" name="menuitem-edit-id" value="'.$array["menu_item_id"].'" />';
                        echo '<button type="submit" class="btn-cat-edit" id="edit-menuitem-id_'.$array["menu_item_id"].'">'.TEXT_EDIT.'</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '<div class="col-lg-1">';
                        if($array["menu_has_child"] != 0){

                        }else{
                            echo '<form role="form" method="post" name="form-menuitem-delete" action="/admin/menus/deletemenuitem" id="form-menuitem-delete_'.$array["menu_item_id"].'">';
                            echo '<input type="hidden" id="menuitem-delete-id_'.$array["menu_item_id"].'" name="menuitem-delete-id" value="'.$array["menu_item_id"].'" />';
                            echo '<input type="hidden" id="menuitem-delete-parent-change_'.$array["menu_parent_item_id"].'" name="menuitem-delete-parent-change" value="'.$array["menu_parent_item_id"].'" />';
                            echo '<input type="hidden" id="menuitem-delete-menu-change_'.$array["menu_parent_menu_id"].'" name="menuitem-delete-menu-change" value="'.$array["menu_parent_menu_id"].'" />';
                            echo '<button type="submit" class="btn-cat-edit" id="delete-menuitem-id_'.$array["menu_item_id"].'">'.TEXT_DELETE.'</button>';
                            echo '</form>';
                        }
                        echo '</div>';
                        echo '<div class="col-lg-4"></div>';
                        echo '</div>';
                        array_push($printed, $array["menu_item_id"]);
                    }
                }
            }
        }
    }
    
    public static function addMenuitemCategoriesTypeList($data = array()){//AMMnu 0011
        $db = Model::getDB();
        $menu_parent_menu_id = $data['parent_menu_id'];//what menu it belongs
        $menu_item_name = $data['menuitem_title'];//title of menuitem in menu
        if($data['menuitem_setmenuposition_parent'] == 'main'){
            $menu_parent_item_id = 0;
        }else{
            $menu_parent_item_id = $data['menuitem_setmenuposition_parent'];
        }

        if($menu_parent_item_id == 0){
            $menu_level = 0;
            $menu_has_parent = 0;//we must set has parent flag
        }else{//if menu has a parent we must find out what is parent level.....
            $menu_has_parent = 1;
            $check_parent_level = ("SELECT * FROM menu_item WHERE menu_item_id = '$menu_parent_item_id'");
            $success = $db->query($check_parent_level);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{
                $found = $success->num_rows;
                if($found > 0){
                    while($result = $success -> fetch_assoc()){
                        $menu_level = $result['menu_level'] + 1;//....and increment it by 1
                    }
                }else{
                    $menu_level = 0;//just in case if there was a problem and did not find a parent
                }
            }
        }

        $menu_has_child = '';//need to set it on very end of query - in row of PARENT of currently added menu item - so no need to include it in query for now
        $menu_type_id = $data['menuitem_type'];//
        $menu_type_name = self::getMenuTypeName($menu_type_id);
        $links_to_what = isset($data['menuitem_links_to_what']) ? $data['menuitem_links_to_what'] : '';
        $external_link = isset($data['add_external_link']) ? $data['add_external_link'] : '';
        $links_to_form = isset($data['menuitem_links_to_form']) ? $data['menuitem_links_to_form'] : '';

        //now we have to get category type name by menu item_link
        $type = self::getCategoryTypeName($links_to_what);
        $type_id = self::getCategoryTypeId($links_to_what);
        //depends on what menuitem shows - get it from $data['menuitem_liks_to_what'] and combine with menuitem_type - so:
        switch($menu_type_id){
            case 1:
                $menu_type_link = '/categories/'.$links_to_what.'/type';//todo
                break;
            case 3:
                    $metaname = self::getCategorySeoLink($links_to_what);
                    $menu_type_link = '/categories/'.$links_to_what.'/'.$metaname;
                break;

            case 5:
                    $metaname = self::getCategorySeoLink($links_to_what);
                    $menu_type_link = '/singlecategory/'.$links_to_what.'/'.$metaname;
                break;

            case 7:
                $metaname = self::getPostSeoLink($links_to_what);
                $menu_type_link = '/posts/'.$links_to_what.'/'.$metaname;
                break;
            case 8:
                $menu_type_link = 'http://'.$external_link;
                break;
            case 9:
                $metaname = self::getCourseSeoLink($links_to_what);
                $menu_type_link = '/courses/'.$links_to_what.'/'.$metaname;
                break;
            case 10:
                $menu_type_link = '';
                break;
            case 11:
                $menu_type_link = $links_to_what;
                break;
            case 12:
                $menu_type_link = '/forms/'.$links_to_form.'/form';
                break;
            case 13:
                $metaname = self::getGallerySeoLink($links_to_what);
                $menu_type_link = '/galleries/'.$links_to_what.'/'.$metaname;
                break;
            case 14:
                $metaname = self::getFileSeoLink($links_to_what);
                $menu_type_link = '/files/'.$links_to_what.'/'.$metaname;
                break;
        }

        $insert_query = ("INSERT INTO menu_item
        (menu_parent_menu_id,menu_item_name,menu_parent_item_id,menu_level,menu_has_parent,menu_type_id,menu_type_name,menu_type_link)
        VALUES
        ('$menu_parent_menu_id','$menu_item_name','$menu_parent_item_id','$menu_level','$menu_has_parent','$menu_type_id','$menu_type_name','$menu_type_link')");

        $success_main = $db->query($insert_query);
        if(!$success_main){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            //now we must set parent flag: has_child if menuitem we just inserted has some parent
            if($menu_has_parent == 1){
                $set_parent_has_child = ("UPDATE menu_item SET menu_has_child = '1' WHERE menu_item_id = '$menu_parent_item_id'");
                $success = $db->query($set_parent_has_child);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }else{
                    //must add menu_has_items to parent menu
                    $add_menu_has_tems = ("UPDATE menus SET menu_has_items = 1 WHERE id='$menu_parent_menu_id'");
                    $success = $db->query($add_menu_has_tems);
                    if(!$success){
                        die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                    }else{
                        $db->close();
                        Session::setFlash(AMENU_MENUITEM_ADDED,'ok');
                    }
                }
            }else{
                //must add menu_has_items to parent menu
                $add_menu_has_tems = ("UPDATE menus SET menu_has_items = 1 WHERE id='$menu_parent_menu_id'");
                $success = $db->query($add_menu_has_tems);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }else{
                    $db->close();
                    Session::setFlash(AMENU_MENUITEM_ADDED,'ok');
                }
            }
        }

 }//END function addMenuitemCategoriesTypeList($data = array(), $view = '')

    public static function makeSeoFromString($string){//cleanses string (changes spaces for "-", removes digits, changes polish letters and cleanses first and last character)

        $not_correct = array('_',' ','-','?','.',',','/','"','\'','>','<','!','@','#','$','%','^','&','*','(',')','+','[',']','{','}',':',';','|');
        $str = str_replace(' ','-',str_replace('.','',strtolower($string)));
        $str = preg_replace("/[0-9]+/",'',$str);
        $str = self::changePolish($str);
        $last = substr($str, -1);
        $first = mb_substr($str, 0, 1, 'utf-8');
        if(in_array($last,$not_correct)){
            $str = mb_substr($str, 0, -1);
        }

        $str_length = strlen($str);

        if(in_array($first,$not_correct)){
            $str = mb_substr($str, 1, $str_length);
        }

        return $str;

    }//end

    public static function changePolish($string){
        $array = Array(
            //WIN
            "\xb9" => "a", "\xa5" => "A", "\xe6" => "c", "\xc6" => "C",
            "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
            "\xf3" => "o", "\xd3" => "O", "\x9c" => "s", "\x8c" => "S",
            "\x9f" => "z", "\xaf" => "Z", "\xbf" => "z", "\xac" => "Z",
            "\xf1" => "n", "\xd1" => "N",
            //UTF
            "\xc4\x85" => "a", "\xc4\x84" => "A", "\xc4\x87" => "c", "\xc4\x86" => "C",
            "\xc4\x99" => "e", "\xc4\x98" => "E", "\xc5\x82" => "l", "\xc5\x81" => "L",
            "\xc3\xb3" => "o", "\xc3\x93" => "O", "\xc5\x9b" => "s", "\xc5\x9a" => "S",
            "\xc5\xbc" => "z", "\xc5\xbb" => "Z", "\xc5\xba" => "z", "\xc5\xb9" => "Z",
            "\xc5\x84" => "n", "\xc5\x83" => "N",
            //ISO
            "\xb1" => "a", "\xa1" => "A", "\xe6" => "c", "\xc6" => "C",
            "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
            "\xf3" => "o", "\xd3" => "O", "\xb6" => "s", "\xa6" => "S",
            "\xbc" => "z", "\xac" => "Z", "\xbf" => "z", "\xaf" => "Z",
            "\xf1" => "n", "\xd1" => "N");

        return strtr($string,$array);

    }//end

    //due to inconsistency in column names need to write same function multiple times:

    public static function getCategorySeoLink($id){

        $db = Model::getDB();
        $metaname = '';
        $name = '';
        $query = ("SELECT * FROM categories WHERE id = '$id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;
        if($found > 0){
          while($result = $success->fetch_assoc()){
              $metaname = $result['metatitle'];
              $metaname = self::makeSeoFromString($metaname);
              $name = $result['name'];
          }
        }

        if($metaname == ''){$metaname = self::makeSeoFromString($name);}
        return $metaname;

    }

    public static function getGallerySeoLink($id){

        $db = Model::getDB();
        $metaname = '';
        $name = '';
        $query = ("SELECT * FROM galleries WHERE gallery_id = '$id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $metaname = $result['gallery_metaname'];
                $metaname = self::makeSeoFromString($metaname);
                $name = $result['gallery_name'];
            }
        }

        if($metaname == ''){$metaname = self::makeSeoFromString($name);}
        return $metaname;

    }

    public static function getPostSeoLink($id){

        $db = Model::getDB();
        $metaname = '';
        $name = '';
        $query = ("SELECT * FROM posts WHERE post_id = '$id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $metaname = $result['post_seolink'];
                $metaname = self::makeSeoFromString($metaname);
                $name = $result['post_title'];
            }
        }

        if($metaname == ''){$metaname = self::makeSeoFromString($name);}
        return $metaname;

    }

    public static function getCourseSeoLink($id){

        $db = Model::getDB();
        $metaname = '';
        $name = '';
        $query = ("SELECT * FROM courses_display_data WHERE courses_display_data_id = '$id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $metaname = $result['course_display_data_seo'];
                $metaname = self::makeSeoFromString($metaname);
            }
        }

        if($metaname == ''){

            $query = ("SELECT * FROM courses WHERE id = '$id'");
            $success = $db->query($query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            while($result = $success->fetch_assoc()){
                $metaname = $result['course_name'];
            }

            $metaname = self::makeSeoFromString($metaname);
        }

        return $metaname;

    }

    public static function getFileSeoLink($id){

        $db = Model::getDB();
        $metaname = '';
        $name = '';
        $query = ("SELECT * FROM files WHERE id = '$id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $metaname = $result['metaname'];
                $metaname = self::makeSeoFromString($metaname);
                $name = $result['title'];
            }
        }

        if($metaname == ''){$metaname = self::makeSeoFromString($name);}
        return $metaname;

    }


    public static function menuitemDelete($menu_id, $parent_id, $menuitem_id){
        $db = Model::getDB();
        $delete_query = ("DELETE FROM menu_item WHERE menu_item_id = '$menuitem_id'");
        $success = $db->query($delete_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            if($parent_id != 0){
                $check_parent = ("SELECT * FROM menu_item WHERE menu_parent_item_id = '$parent_id'");
                $success = $db->query($check_parent);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }else{
                    $found = $success->num_rows;
                    if($found > 0){

                        self::changeMenuHasItems($menu_id);

                    }else{
                        $change_parent = ("UPDATE menu_item SET menu_has_child = 0 WHERE menu_item_id = '$parent_id'");
                        $success = $db->query($change_parent);
                        if(!$success){
                            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                        }else{
                            self::changeMenuHasItems($menu_id);
                        }
                    }
                }
            }else{
                self::changeMenuHasItems($menu_id);
            }
        }
    }//END function menuitemDelete($array, $printed = array())

    public static function changeMenuHasItems($menu_id){
        $db = Model::getDB();
        $change_menu = ("SELECT * FROM menu_item WHERE menu_parent_menu_id = '$menu_id'");
        $success = $db->query($change_menu);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            $found = $success->num_rows;
            if($found > 0){
                $db->close();
                Session::setFlash(AMENU_MENUITEM_DELETED,'ok');
            }else{
                $change_menu_has_items = ("UPDATE menus SET menu_has_items = 0 WHERE id = '$menu_id'");
                $success = $db->query($change_menu_has_items);
                if(!$success){
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }else{
                    $db->close();
                    Session::setFlash(AMENU_MENUITEM_DELETED,'ok');
                }
            }
        }
    }//END function changeMenuHasItems($menu_id)

    public static function menuItemGetData($menuitem_id){
        $db = Model::getDB();
        $data = array();
        $get_data = ("SELECT * FROM menu_item WHERE menu_item_id = '$menuitem_id'");
        $success = $db->query($get_data);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
         while($result = $success->fetch_assoc()){
             array_push($data, $result);
         }
            return $data;
        }
    }//END function menuItemGetData($$menuitem_id)

    public static function editMenuItem($data = array()){
        $db = Model::getDB();
        $view_list = array(2,4,6);
        $menu_item_id = $data['menuitem_id'];
        $menu_item_name = $data['menuitem_name'];
        $menu_type_id = $data['menuitem_type'];
        $menu_type_name = $data['menuitem_type_name'];
        $menu_link = $data['menuitem_link'];
        if(in_array($menu_type_id, $view_list)){
         $view = 'grid';
        }else{
            $view = 'list';
        }

        $type = self::getCategoryTypeName($menu_link);
        $type_id = self::getCategoryTypeId($menu_link);

        switch($menu_type_id){
            case 1:

                $menu_type_link = '/categories/'.$menu_link.'/type';//todo;
                break;

            case 3:

                $metaname = self::getCategorySeoLink($menu_link);
                $menu_type_link = '/categories/'.$menu_link.'/'.$metaname;
                break;

            case 5:

                $metaname = self::getCategorySeoLink($menu_link);
                $menu_type_link = '/singlecategory/'.$menu_link.'/'.$metaname;

                break;

            case 7:

                $metaname = self::getPostSeoLink($menu_link);
                $menu_type_link = '/posts/'.$menu_link.'/'.$metaname;
                break;

            case 8:

                $menu_type_link = 'http://'.$menu_link;
                break;

            case 9:

                $metaname = self::getCourseSeoLink($menu_link);
                $menu_type_link = '/courses/'.$menu_link.'/'.$metaname;
                break;

            case 10:

                $menu_type_link = '';
                break;

            case 11:

                $menu_type_link = $menu_link;
                break;

            case 12:

                $menu_type_link = '/forms/'.$menu_link.'/form';
                break;

            case 13:

                $metaname = self::getGallerySeoLink($menu_link);
                $menu_type_link = '/galleries/'.$menu_link.'/'.$metaname;
                break;

            case 14:
                $metaname = self::getFileSeoLink($menu_link);
                $menu_type_link = '/files/'.$menu_link.'/'.$metaname;
                break;

            default:
                $menu_type_link = '';
                break;
        }
        $change_menu = ("UPDATE menu_item SET menu_item_name='$menu_item_name',menu_type_id='$menu_type_id',menu_type_name='$menu_type_name',menu_type_link='$menu_type_link',menu_type_view='$view' WHERE menu_item_id = '$menu_item_id'");
        $success = $db->query($change_menu);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            Session::setFlash(AMENU_MENUITEM_UPDATED,'ok');
            $db->close();
            return TRUE;
        }
    }//END function changeMenuHasItems($menu_id)

    public static function getCategoryTypeName($category_id){

        $db = Model::getDB();
        $data = array();
        $type_id = '';
        $get_data = ("SELECT * FROM categories WHERE id = '$category_id'");
        $success = $db->query($get_data);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $result = $success->fetch_assoc();

        foreach($result as $key => $value){
            if($key == 'type'){
                $type_id = $value;
            }
        }

        $query = ("SELECT * FROM categories_type WHERE type_id = '$type_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $result = $success->fetch_assoc();
        return $result;
    }

    public static function getCategoryTypeId($category_id){

        $db = Model::getDB();
        $type_id = 0;
        $get_data = ("SELECT * FROM categories WHERE id = '$category_id'");
        $success = $db->query($get_data);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $result = $success->fetch_assoc();

        foreach($result as $key => $value){
            if($key == 'type'){
                $type_id = $value;
            }
        }
        return $type_id;
    }

}//end class Menu