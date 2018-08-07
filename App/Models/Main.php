<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-14
 * Time: 16:11
 */

namespace App\Models;


use Core\Model;
use Core\Session;

class Main extends Model{

 public static function menuitemGetList($level, $parent_menu_id){//FrMMain-0001
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
 }//END function menuitemGetList($level, $parent_menu_id)


 public static function getAllChildren($id){
  $db = Model::getDB();
  $kids = array();
  $children_querry = ("SELECT * FROM menu_item WHERE menu_parent_item_id = '$id'");
  $success = $db->query($children_querry);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($results = $success->fetch_assoc()){
     array_push($kids, $results);
    }
    return $kids;
   }else{
     return FALSE;
   }
  }
 }//END getAllChildren($id)

 public static function getAllParents($menu_id){
  $db = Model::getDB();
  $parents = array();
  $parent_querry = ("SELECT * FROM menu_item WHERE menu_parent_menu_id = '$menu_id' AND menu_level = 0");
  $success = $db->query($parent_querry);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($results = $success->fetch_assoc()){
     array_push($parents, $results);
    }
    return $parents;
   }else{
    return FALSE;
   }
  }
 }//END getAllParents($menu_id)

 public static function printKids($parent_id){
  $children = self::getAllChildren($parent_id);
  foreach($children as $child){
   if($child["menu_has_child"] == 1){
    echo '<li class="dropdown">';
    echo '<a href="'.$child["menu_type_link"].'" class="dropdown-toggle" >'.$child["menu_item_name"].'<span class="glyphicon glyphicon-menu-right"></span></a>';
    echo '<ul class="dropdown-menu dropdown-menu-right">';
    self::printKids($child["menu_item_id"]);
    echo '</ul>';
    echo '</li>';
   }else{
    echo '<li><a href="'.$child["menu_type_link"].'" ';
    if($child["menu_type_id"] == 8){
     echo 'target = "_blank" ';
    }
    echo '>'.$child["menu_item_name"].'</a></li>';
   }
  }
 }

 public static function printMenuG($array){
  foreach($array as $parent){
   if($parent["menu_has_child"] == 1){
    echo '<li class="dropdown">';
    echo '<a href="'.$parent["menu_type_link"].'" class="dropdown-toggle" >'.$parent["menu_item_name"].'<span class="caret"></span></a>';
    echo '<ul class="dropdown-menu">';
    self::printKids($parent["menu_item_id"]);
    echo '</ul>';
    echo '</li>';
   }else{
    if($parent["menu_item_name"] == ''){

    }else{
     echo '<li><a href="'.$parent["menu_type_link"].'" ';
     if($parent["menu_type_id"] == 8){
      echo 'target = "_blank" ';
     }
     echo '>'.$parent["menu_item_name"].'</a></li>';
    }
   }
  }
 }//END function printMenuG($array)

 public static function printMenuV($menu){

  $weight = count($menu);
  $iterator = 0;

  foreach($menu as $item){

   echo '<div class="card">';
   echo '<div class="card-header" role="tab" id="heading_'.$item['menu_item_id'].'">';
   echo '<h5 class="tab-header';
   if($iterator >= $weight-1){
    echo ' last-element';
   }
   echo '">';
   if($item['children']){

    echo '<a data-toggle="collapse" data-parent="#accordion" href="#collapse_'.$item['menu_item_id'].'" aria-expanded="false" aria-controls="collapse_'.$item['menu_item_id'].'">'.$item['menu_item_name'].'</a>';

   }else{

    echo '<a href="'.$item['menu_type_link'].'">'.$item['menu_item_name'].'</a>';

   }
   echo '</h5></div>';

   if($item['children']){
    echo '<div id="collapse_'.$item['menu_item_id'].'" class="collapse" role="tabpanel" aria-labelledby="heading_'.$item['menu_item_id'].'">';

    echo '<div class="card-block">';

    self::printMenuV($item['children']);

    echo '</div></div>';
   }
   echo '</div>';
   $iterator++;

  }
 }//END function printMenuV($menu)

 public static function menuGetSimple($menu_id){

  $db = Model::getDB();
  $list = array();
  $name = '';
  $name_query = ("SELECT menu_title FROM menus WHERE id = '$menu_id'");
  $success = $db->query($name_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }

  $found = $success->num_rows;
  if($found > 0){

   while($result = $success->fetch_assoc()){

    $name = $result['menu_title'];

   }

  }

  $items_query = ("SELECT * FROM menu_item WHERE menu_parent_menu_id = '$menu_id'");
  $success = $db->query($items_query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }

  $found = $success->num_rows;
  if($found > 0){

   while($result = $success->fetch_assoc()){

    $list[$name][] = $result;

   }

  }

  return $list;

 }//END function menuGetSimple($menu_id)

 public static function menuGetIsVertical($menu_id){

  $db = Model::getDB();
  $is_vertical = FALSE;
  $query = ("SELECT * FROM menus WHERE id = '$menu_id'");
  $success = $db->query($query);
  if (!$success) {
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }

  while($result = $success->fetch_assoc()){

   if($result['menu_is_vertical'] == 1){
    $is_vertical = TRUE;
   }

  }

  $db->close();
  return $is_vertical;

 }

} //END CLASS