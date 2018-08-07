<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-14
 * Time: 16:12
 */

namespace App\Models;


use Core\Model;
use Core\Session;

class Post extends Model{

    public static function getPost($id){//FrMPo 0001

        $db = Model::getDB();
        $post = array();
        $db_query = ("SELECT * FROM posts WHERE post_id = '$id' LIMIT 1");
        $result = $db->query($db_query);
        if(!$result){
            die('There was an error running the query [' . $db->error . ']');
        }else{
           $found = $result->num_rows;
            if($found > 0){
                while($row = $result->fetch_assoc()){
                    array_push($post, $row);
                }
                $db->close();
                return $post;
            }else{
                Session::setFlash(ATXT_NO_POSTS,'error');
                $db->close();
                return FALSE;
            }
        }
    }//END function getPost($id)

    public static function getCategoryName($post_category_id){//FrMPo 0002
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
    }//END function getCategoryName($post_category_id)

    public static function getPostsFromCategory($category_id,$show_parent = 0){//FrMPo 0003

        $db = Model::getDB();
        $posts = array();

        if($show_parent == 1){
            $query = ("SELECT * FROM categories WHERE id = '$category_id'");
            $success = $db->query($query);
            if(!$success){
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }
            $found = $success-> num_rows;
            if($found > 0){
                while( $result = $success->fetch_assoc() ){
                    $posts['parent'] = $result;
                }
            }
        }

        $db_query = ("SELECT * FROM posts WHERE post_category_id = '$category_id' ORDER BY post_date_edited DESC");
        $success = $db->query($db_query);
        if(!$success){
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
            $found = $success->num_rows;
            if($found > 0){
                while($result = $success->fetch_assoc()){
                    array_push($posts, $result);
                }
                $db->close();
                return $posts;
            }else{
                $db->close();
                return FALSE;
            }

    }//END function getPostsFromCategory($category_id)


    public static function findSnippetOccur($content, $phrase, $offset = 0, $results = array()) {
        //recursively finding occurences of snippet tags (or something) in content - checking if they even exist - if they are - getSnippets function is called

        $offset = strpos($content, $phrase, $offset);
        if($offset === false) {
            return $results;
        } else {
            $results[] = $offset;
            return self::findSnippetOccur($content, $phrase, ($offset + 2), $results);
        }

    }//end function findSnippetOccur($content, $phrase, $offset = 0, $results = array())


    public static function getSnippets($content, $pos_array = array()){

        $occurrences = array();
        $temp = array();

        foreach($pos_array as $key => $value){

            $start = $value;
            $end = strpos($content, "}}", $start+2);//end of snippet tag
            $how_long = ($end) - ($start+2);//exact length of string between tags needed in substr function
            $sub = substr($content,$start+2, $how_long);//substring
            $sub_sub = explode(':',$sub);//array of what code and name of snippet
            $temp['start'] = $start;
            $temp['end'] = $end;
            $temp['howlong'] = $how_long;
            $temp['snippet'] = $sub_sub[0];
            $temp['name'] = $sub_sub[1];
            array_push($occurrences,$temp);
            $temp = array();

        }

        return $occurrences;

    }//end function findSnippet()

} //end of class POST