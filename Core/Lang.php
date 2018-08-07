<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-09
 * Time: 15:54
 */

namespace Core;
class Lang extends Model{

    protected static $data;

    public static function getLang(){

        $lang = array();
        $l_db = Model::getDB();
        $get_lang = ("SELECT * FROM languages WHERE active = 1");
        $result = $l_db->query($get_lang);
        if(!$result){
            $id = '1';
            $code = 'pl';
         array_push($lang,$id);
         array_push($lang,$code);
        }else{
            while($l_code = $result->fetch_assoc()){
              array_push($lang, $l_code);
            }
        }
        $l_db->close();
        return $lang;
    }


    public static function load($lang_code, $adm_lang_code){

        $root = dirname(__DIR__);
        $admin_file_path = $root.'/App/Languages/Admin/'.$adm_lang_code.'.php';
        $file_path = $root.'/App/Languages/'.$lang_code.'.php';

        if(is_readable($admin_file_path)){
            require($admin_file_path);
        }else{
            require($root.'/App/Languages/Admin/pl.php');
        }
        if(is_readable($file_path)){
            require($file_path);
        }else{
            require($root.'/App/Languages/pl.php');
        }

    }

    public static function changeLang($new_code){
        if(isset($new_code)){
            $db = Model::getDB();
            $get_lang = ("SELECT lang_code FROM languages WHERE active = 1");
            $old_lang = $db->query($get_lang);
            $res = $old_lang->fetch_assoc();
            if($res['lang_code'] != $new_code){
             $unset_active = ("UPDATE languages SET active = 0 WHERE active = 1");
                $done = $db->query($unset_active);
                if(!$done){
                    die(mysql_error());
                }else{
                    $set_active = ("UPDATE languages SET active = 1 WHERE lang_code = '$new_code'");
                    $new_set = $db->query($set_active);
                    if(!$new_set){
                        die(mysql_error());
                    }else{
                        //set flash?
                    }
                }
            }else{
// set flash?
            }
        }
    }

} 