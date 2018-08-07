<?php
/**
 * Created by BBJaga.
 * Date: 2016-08-31
 * Time: 08:48
 */

namespace App\Controllers\Admin;

use App\Models\Admin\Category;
use App\Models\Admin\Menu;
use Core\Session;
use \Core\View;

class Menus extends \Core\Controller {

    public function addAction(){//AMMnu 0001

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['add_menu_name']) && !empty($_POST['add_menu_name']) && isset($_POST['add_menu_title']) && !empty($_POST['add_menu_title'])){
                $send_menu = array();
                foreach($_POST as $key => $value){
                    $send_menu[$key] = $value;
                }
                 Menu::addNew($send_menu);
                 $message = Session::getFlash();
                 Session::unsetFlash();
                View::renderAdmin('Menus/list.phtml',['message' => $message]);
            }else{
                View::renderAdmin('Menus/add.phtml');
            }
        }
    }

    public function listAction(){//AMMnu 0002

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
                $menu_list = Menu::getMenus();
                View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list]);
        }
    }

    public function editAction(){//ACMnu 0003

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['edit_menu_id']) && $_POST['edit_menu_id'] != '' && isset($_POST['edit_menu_name']) && $_POST['edit_menu_name'] != '' && isset($_POST['edit_menu_title']) && $_POST['edit_menu_title'] != ''){
              $data = array();
                foreach($_POST as $key => $value){
                    $data[$key] = $value;
              }

                Menu::editMenu($data);
                $menu_list = Menu::getMenus();
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Menus/list.phtml',['message' => $message, 'menu_list' => $menu_list]);
                Menu::editMenu($data);
            }else if(isset($_POST)){
                for($i=1; $i<1000; $i++){
                foreach($_POST as $key => $value){
                  if($key == 'menu_edit_'.$i){
                      $menu_id = $value;
                      $menu_data = array();
                      $data_temp = Menu::getMenuData($menu_id);
                      foreach($data_temp as $menu){
                          foreach($menu as $key => $value){
                              $menu_data[$key] = $value;
                          }
                      }
                      View::renderAdmin('Menus/edit.phtml',['menu_data' => $menu_data]);
                  }
                }
              }
            }else{
                $menu_data = array();
                Session::setFlash(ATXT_UNKNOWN_ERROR.': AMMnu 0003 069','error');
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Menus/edit.phtml',['menu_data' => $menu_data,'message' => $message]);
            }


        }
    }//end editAction()

    public function deleteAction(){//AMMnu 0004

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST)){ // to do better
                for($i=1; $i<1000; $i++){
                    foreach($_POST as $key => $value){
                        if($key == 'menu_delete_'.$i){
                            $menu_id = $value;
                            $success = Menu::deleteMenu($menu_id);
                            if($success != FALSE){
                                $message = Session::getFlash();
                                Session::unsetFlash();
                                $menu_list = Menu::getMenus();
                                View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message' => $message]);
                            }else{
                                $message = Session::getFlash();
                                Session::unsetFlash();
                                View::renderAdmin('Menus/list.phtml',['message' => $message]);
                            }
                        }
                    }
                }
            }else{
                $menu_list = Menu::getMenus();
                View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list]);
            }
        }
    }//end deleteAction()

    public function newMenuItemAction(){//AMMnu 0005
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST)){
                $data = array();
                foreach($_POST as $key => $value){
                    $root_key = explode('_', $key);
                    $id = $root_key[1];
                }
                $data_dirty = \App\Models\Admin\Menu::getMenuData($id);//boomer
                foreach($data_dirty as $clenup){
                    foreach($clenup as $key => $value){
                        $data[$key] = $value;
                    }
                }
                View::renderAdmin('Menus/newMenuItem.phtml',['data' => $data]);
            }else{
                Session::setFlash(ATXT_UNKNOWN_ERROR.': ACMMnu 0005 139','error');
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Menus/newMenuItem.phtml',['message' => $message]);
            }

        }
    }//end newMenuItemAction()

    public function saveMenuItemAction(){//AMMnu 0006

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['parent_menu_id']) && isset($_POST['menuitem_name']) && isset($_POST['menuitem_set_type']) && isset($_POST['menuitem_setmenuposition_parent'])){
                $menu_data = array();
                $menuitem_type = $_POST['menuitem_set_type'];
                $menu_data['menuitem_setmenuposition_parent'] = $_POST['menuitem_setmenuposition_parent'];
                $menu_data['parent_menu_id'] = $_POST['parent_menu_id'];
                $menu_data['menuitem_title'] = $_POST['menuitem_name'];
                $menu_data['menuitem_type'] = $_POST['menuitem_set_type'];

                $menu_data['menuitem_links_to_what'] = isset($_POST['menuitem_links_to_what']) ? $_POST['menuitem_links_to_what'] : '';
                $menu_data['add_external_link'] = isset($_POST['add_external_link']) ? $_POST['add_external_link'] : '';
                $menu_data['menuitem_links_to_form'] = isset($_POST['menuitem_links_to_form']) ? $_POST['menuitem_links_to_form'] : '';

                switch($menuitem_type){
                    case 1:
                        //wszystkie kategorie ze wszystkich typow - lista -> włąściwie opis typów
                        // link will be: /categories/$type_id/typelist
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                         break;

                    case 3:
                        //wszystkie podkategorie z danej kategorii - lista
                        // link will be: /categories/$parent_category_id/list
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;

                    case 5:
                        //wszystkie posty z danej kategorii - lista
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;

                    case 7:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                    case 8:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                    case 9:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                    case 10:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                    case 11:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                    case 12:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                    case 13:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                    case 14:
                        Menu::addMenuitemCategoriesTypeList($menu_data);
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        $menu_list = Menu::getMenus();
                        View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
                        break;
                }

            }else{
                Session::setFlash(MENUITEM_NOT_ALL_INPUTS,'warning');
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Menus/newMenuItem.phtml',['message' => $message]);
            }

        }
    }//end saveMenuItemAction()

    public function deleteMenuItemAction(){//AMMnu 0006
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['menuitem-delete-id']) && $_POST['menuitem-delete-id'] != '' && isset($_POST['menuitem-delete-parent-change']) && $_POST['menuitem-delete-parent-change'] != '' && isset($_POST['menuitem-delete-menu-change']) && $_POST['menuitem-delete-menu-change'] != ''){
               $menuitem_id = $_POST['menuitem-delete-id'];
               $parent_id = $_POST['menuitem-delete-parent-change'];
                $menu_id = $_POST['menuitem-delete-menu-change'];
                $values = array();
                $values['menuitem_id'] = $menuitem_id;
                $values['parent_id'] = $parent_id;
                $values['menu_id'] = $menu_id;

                Menu::menuitemDelete($menu_id, $parent_id, $menuitem_id);

                $message = Session::getFlash();
                Session::unsetFlash();
                $menu_list = Menu::getMenus();
                View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list, 'message' => $message]);
             }
            }else{
                View::renderAdmin('Menus/add.phtml');
            }

    }//end deleteMenuItemAction()

    public function editMenuItemAction(){//AMMnu 0007
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['menuitem-edit-id']) && $_POST['menuitem-edit-id'] != ''){
                 $menuitemId = $_POST['menuitem-edit-id'];

                $data = Menu::menuItemGetData($menuitemId);

                View::renderAdmin('Menus/editMenuItem.phtml',['data' => $data]);
                }else{
                $message = Session::setFlash('CRITICAL ERROR: AMMnu 0007-310.','error');
                Session::unsetFlash();
                View::renderAdmin('Menus/editMenuItem.phtml',['message' => $message]);
                }
            }else{
                Session::setFlash(ATXT_UNKNOWN_ERROR.': ACMMnu 0007 319','error');
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Menus/newMenuItem.phtml',['message' => $message]);
            }

    }//end editMenuItemAction()


    public function editMenuItemSaveAction(){//AMMnu 0008
        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){
            if(isset($_POST['menuitem_id']) && $_POST['menuitem_id'] != ''){
                $data['menuitem_id'] = $_POST['menuitem_id'];
                $data['menuitem_type'] = $_POST['menuitem_edit_type'];
                $data['menuitem_type_name'] = Menu::getMenuTypeName($_POST['menuitem_edit_type']);
                if(isset($_POST['add_external_link'])){
                    $data['menuitem_link'] = $_POST['add_external_link'];
                }elseif($_POST['menuitem_links_to_form']){
                    $data['menuitem_link'] = $_POST['menuitem_links_to_form'];
                }else{
                    $data['menuitem_link'] = $_POST['menuitem_links_to_what'];
                }

                $data['menuitem_name'] = $_POST['menuitem_name'];
                Menu::editMenuItem($data);
                $message = Session::getFlash();
                Session::unsetFlash();
                $menu_list = Menu::getMenus();
                View::renderAdmin('Menus/list.phtml',['menu_list' => $menu_list,'message'=>$message]);
            }else{
                $message = Session::setFlash('CRITICAL ERROR: ACMnu 0008-337.','error');
                Session::unsetFlash();
                View::renderAdmin('Menus/editMenuItem.phtml',['message' => $message]);
            }

        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.': ACMMnu 0008 347','error');
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Menus/editMenuItem.phtml',['message' => $message]);
        }
    }

}//end CLASS MENUS CONTROLLER