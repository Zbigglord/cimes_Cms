<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-06-28
 * Time: 13:21
 */

namespace Core;


class Template extends Model
{

    public static function getTemplateAdmin(){
        $db = Model::getDB();
        $template = '';
        $query = ("SELECT * FROM site_template WHERE template_is_admin = 1 AND template_active = 1");
        $template_folder = $db->query($query);
        if (!$template_folder) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($type = $template_folder->fetch_assoc()){
                $template = $type['template_folder'];
            }
        }
        $db->close();
        return $template;
    }

    public static function getTemplateFrontend(){
        $db = Model::getDB();
        $template = '';
        $query = ("SELECT * FROM site_template WHERE template_is_admin = 0 AND template_active = 1");
        $template_folder = $db->query($query);
        if (!$template_folder) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($type = $template_folder->fetch_assoc()){
                $template = $type['template_folder'];
            }
        }
        $db->close();
        return $template;
    }

    public static function getTemplates(){

        $db = Model::getDB();
        $templates = array();
        $query = ("SELECT * FROM site_template ORDER BY template_is_admin");
        $success = $db->query($query);
        if (!$success) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success->fetch_assoc()){
                $templates[] = $result;
            }
        }
        $db->close();
        return $templates;

    }

    public static function checkTemplate($folder, $name){

        $db = Model::getDB();
        $query = ("SELECT * FROM site_template WHERE template_name = '$name' AND template_folder = '$folder'");
        $success = $db->query($query);
        if (!$success) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
          $found = $success->num_rows;
            if($found > 0){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    public static function newTemplate($data = array()){

        $db = Model::getDB();
        $has_one = self::checkTemplate($data['folder'], $data['name']);

        if($has_one == TRUE){
            return FALSE;
        }else{
            $template_name = $data['name'];
            $template_folder = $data['folder'];
            $template_author = $data['author'];
            $template_is_admin = $data['is_admin'];
            $template_active = $data['is_active'];

            if($template_active == 1 && $template_is_admin == 1){//if new admin template gonna be active

                self::switchTemplate(1);

            }

            if($template_active == 1 && $template_is_admin == 0){//if new frontend template gonna be active

                self::switchTemplate(0);
            }

            $query = ("INSERT INTO site_template (template_name, template_folder, template_date, template_author, template_is_admin, template_active) VALUES ('$template_name','$template_folder',NOW(),'$template_author','$template_is_admin','$template_active')");
            $success = $db->query($query);
            if (!$success) {
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }
            $db->close();
            return TRUE;
        }

    }//END newTemplate()

    public static function getTemplateById($id){

        $db = Model::getDB();
        $template = array();
        $query = ("SELECT * FROM site_template WHERE id = '$id'");
        $success = $db->query($query);
        if (!$success) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success->fetch_assoc()){
                $template[] = $result;
            }
        }

        $db->close();
        return $template[0];

    }//END getTemplateById($id)

    public static function updateTemplate($data = array()){

        $db = Model::getDB();
        $template_name = $data['template_name'];
        $template_folder = $data['template_folder'];
        $template_author = $data['template_author'];
        $template_is_admin = $data['template_is_admin'];
        $template_active = $data['template_active'];
        $template_id = $data['id'];
        if($template_active == 1 && $template_is_admin == 1){//if new admin template gonna be active

            self::switchTemplate(1);

        }

        if($template_active == 1 && $template_is_admin == 0){//if new frontend template gonna be active

            self::switchTemplate(0);
        }

        $query = ("UPDATE site_template SET template_name = '$template_name', template_folder = '$template_folder',template_author = '$template_author',template_is_admin = '$template_is_admin', template_active = '$template_active' WHERE id = '$template_id'");
        $success = $db->query($query);
        if (!$success) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $db->close();
        return TRUE;

    }//END updateTemplate($data = array())

    public static function switchTemplate($is_admin){//switches off all templates ($is_admin = 1 admin templates, $is_admin = 0 frontend)

        $db = Model::getDB();
        $query = ("UPDATE site_template SET template_active = 0 WHERE template_is_admin = '$is_admin'");
        $success = $db->query($query);
        if (!$success) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

    }//END switchTemplate()

    public static function deleteTemplate($id){

        $db = Model::getDB();
        $query = ("DELETE FROM site_template WHERE id = '$id'");
        $success = $db->query($query);
        if (!$success) {
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
//todo: figure out how to make sure that template is not only one
        $db->close();
        return TRUE;

    }//END getTemplateById($id)

}//END CLASS