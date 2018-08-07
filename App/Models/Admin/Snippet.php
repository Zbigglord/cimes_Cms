<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-17
 * Time: 10:01
 */
namespace App\Models\Admin;

use Core\Model;
use Core\Session;

class Snippet extends Model{


    public static function getSnippets($type){

        $db = Model::getDB();
        $snips = array();
        $db_query = ("SELECT * FROM snippets WHERE snippet_type = '$type'");
        $result = $db->query($db_query);
        if(!$result){
            die('There was an error running the query [' . $db->error . ']');
        }else{
            $found = $result->num_rows;
            if($found > 0){
                while($row = $result->fetch_assoc()){
                    array_push($snips, $row);
                }
                $db->close();
                return $snips;
            }else{
                Session::setFlash(ATXT_NO_SNIPPETS,'error');
                $db->close();
                return FALSE;
            }
        }

    }//end function getSnippets($type)

    public static function addSnippet($type, $name){

        $db = Model::getDB();

        $author = Session::get('user_nick');

        if($type == 'js'){

            $link = 'App/Files/js/'.$name.'.snp';

        }else{

            $link = 'App/Files/php/'.$name.'.snp';

        }

        $db_query = ("INSERT INTO snippets (snippet_type, snippet_name, snippet_link, snippet_author, date_added) VALUES ('$type', '$name', '$link', '$author', NOW())");

        $result = $db->query($db_query);

        if(!$result){

            Session::setFlash('There was an error running the query [' . $db->error . ']','error');
            $db->close();
            return FALSE;

        }else{

            $db->close();
            return TRUE;

        }

    }//END function addSnippet()


}//END CLASS Snippet