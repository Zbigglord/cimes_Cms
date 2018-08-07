<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-30
 * Time: 14:23
 */

namespace App\Models\Admin;

use \DOMDocument;
use Core\Lang;
use Core\Session;
use Core\Model;

/**
 * Class Post
 * @package App\Models\Admin
 */
class Post extends Model{

    public static function addNewArticle($data = array()){//AMPo-0001
        $db = Model::getDB();
        $post_author = Session::get('user_nick');
        $post_title = $data['post_title'];
        $post_category_id = $data['post_category_id'];
        $post_seo_link = $data['post_seo_link'];
        $post_metakeys = $data['post_metakeys'];
        $post_metadescription = $data['post_metadescription'];
        $post_content = htmlentities($data['post_description']);
        $image_path = $data['post_image'];
        if(!empty($data)){
            $add_query = ("INSERT INTO posts
            (post_category_id, post_title, post_metakeys, post_metadescription, post_content, post_image, post_seo_link, post_date_added, post_date_edited, post_author, post_is_archive)
            VALUES
            ('$post_category_id','$post_title','$post_metakeys','$post_metadescription','$post_content','$image_path','$post_seo_link',NOW(),NOW(),'$post_author',0)");

            $success = $db->query($add_query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }else{
                Session::setFlash(POST_POST_ADDED_PROPERLY,'ok');
                $db->close();
                return TRUE;
            }
        }else{
            Session::setFlash(ATXT_UNKNOWN_ERROR.' AMPo-0001-046','error');
            $db->close();
            return FALSE;
        }
    }

    public static function listAllArticles(){//AMPo-0002
        $db = Model::getDB();
        $posts = array();
        $articles = ("SELECT * FROM posts WHERE post_is_archive = 0");
        $result = $db->query($articles);
        $found = $result-> num_rows;
        if($found > 0){
            while( $post = $result->fetch_assoc() ){
                array_push($posts,$post);
            }

        }else{
            Session::setFlash(ATXT_NO_POSTS,'ok');
            $db->close();
            return FALSE;
        }
        $db->close();
        return $posts;
    }

    public static function uploadImage($images = array()){//AMPo-0003
        if(isset($images) && !empty($images)){
            $img_name = filter_var($images["name"], FILTER_SANITIZE_STRING);//sanitize names
            $img_name = mb_strtolower($img_name, 'UTF-8');//make all lowercase
            $img_name = str_replace(array('ą','ć','ę','ł','ó','ź','ż'), array('a','c','e','l','o','z','z'), $img_name);//replace polish chars in names if exist
            $img_type = $images["type"];
            $img_temp_path = $images["tmp_name"];
            $img_error = $images["error"];
            $img_size = round(($images["size"] / 1024) /1024, 3);
            // $current_file = '../'.__DIR__;
            $new_image = array(
             'name' => $img_name,
             'type' => $img_type,
             'tmp' => $img_temp_path,
             'error' => $img_error,
             'size' => $img_size,
            );
            $new_path = '../public/upload/images/posts/'.$new_image['name'];
            if(!move_uploaded_file($new_image['tmp'], $new_path)){
                Session::setFlash(ATXT_CATEGORY_MOVE_IMAGE_ERROR,'error');
                return FALSE;
            }else{
                Session::setFlash(ATXT_CATEGORY_IMAGE_MOVED,'ok');
                $new_path = '/upload/images/posts/'.$new_image['name'];
                return $new_path;
            }
        }else{
            return NULL;
        }
    }

    public static function getCategoryName($post_category_id){//AMPo-0004
        $db = Model::getDB();
        $name = '';
        $get_query = ("SELECT name FROM categories WHERE id = '$post_category_id' LIMIT 1");
        $result = $db->query($get_query);
        $found = $result-> num_rows;
        if($found > 0){
            while( $post = $result->fetch_assoc() ){
                $name = $post['name'];
            }
          return $name;
        }else{
            Session::setFlash(ATXT_NO_POSTS,'ok');
            $db->close();
            return FALSE;
        }
    }//END GET POST CATEGORY NAME

    public static function deletePost($id){//AMPo-0005
        $db = Model::getDB();
        $delete_query = ("DELETE FROM posts WHERE post_id = '$id'");
        $success = $db->query($delete_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        self::clearArchive($id);
            Session::setFlash(POST_POST_DELETED_PROPERLY,'ok');
            $db->close();
            return TRUE;

    }//END DELETE POST

    public static function getPostById($id){//AMPo-0006
        $db = Model::getDB();
        $post = array();
        $delete_query = ("SELECT * FROM posts WHERE post_id = '$id' AND post_is_archive = 0");
        $success = $db->query($delete_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            while($result = $success->fetch_assoc()){
                foreach($result as $key => $value){
                    $post[$key] = $value;
                }
            }

            $db->close();
            return $post;
        }
    }//END DELETE POST


    public static function editPost($data){//AMPo-0006
        $db = Model::getDB();
        $post_id = $data['post_id'];
        $post_category_id = $data['post_category_id'];
        $post_title = $data['post_name'];
        $post_metakeys = $data['post_metakeys'];
        $post_metadescription = $data['post_metadescription'];
        $post_content = htmlentities($data['post_description']);
        $post_image = $data['post_image'];
        $post_seo_link = $data['post_seo'];
        $post_edited_by = Session::get('user_nick');
        if($post_category_id != 'none'){
            $edit_query = ("UPDATE posts SET post_category_id='$post_category_id',post_title='$post_title',post_metakeys='$post_metakeys',post_metadescription='$post_metadescription',post_content='$post_content',post_image='$post_image',post_seo_link='$post_seo_link',post_date_edited=NOW(),post_edited_by='$post_edited_by' WHERE post_id='$post_id'");
        }else{
            $edit_query = ("UPDATE posts SET post_title='$post_title',post_metakeys='$post_metakeys',post_metadescription='$post_metadescription',post_content='$post_content',post_image='$post_image',post_seo_link='$post_seo_link',post_date_edited=NOW(),post_edited_by='$post_edited_by' WHERE post_id='$post_id'");
        }

        $success = $db->query($edit_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }else{
            Session::setFlash(POST_POST_UPDATED_PROPERLY,'ok');
            $db->close();
            return TRUE;
        }
    }//END DELETE POST

    public static function moveToArchive($post_id){

        $db = Model::getDB();
        $query = ("UPDATE posts SET post_is_archive = 1 WHERE post_id = '$post_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $query = ("INSERT INTO archive (entity_table_name, entity_id, entity_moved_date) VALUES ('posts', '$post_id', NOW())");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        return TRUE;

    }//END function moveToArchive()

    public static function moveFromArchive($post_id){

        $db = Model::getDB();
        $query = ("UPDATE posts SET post_is_archive = 0 WHERE post_id = '$post_id'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        self::clearArchive($post_id);

        return TRUE;

    }//END function moveFromArchive()

    public static function clearArchive($post_id){

        $db = Model::getDB();

        $query = ("SELECT * FROM archive WHERE entity_id = '$post_id' AND entity_table_name = 'posts'");
        $success = $db->query($query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if($found > 0){
            $query = ("DELETE FROM archive WHERE entity_id = '$post_id' AND entity_table_name = 'posts'");
            $success = $db->query($query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }
        }
    }//END function moveToArchive()

} // END CLASS