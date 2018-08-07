<?php
/**
 * Created by BBJaga
 * Date: 2016-12-14
 * Time: 15:11
 */

namespace App\Models;


use Core\Model;
use Core\Session;
use Core\Logs;

class Document extends Model
{

 public static function addNewDocumentMode($data = array()){
  $db = Model::getDB();
  $documents_model_name = $data['add_document_model_name'];
  $documents_model_category = $data['add_document_model_category'];
  $documents_model_html = htmlentities($data['add_document_model_html']);
  $documents_model_added_by = Session::get('user');
  $documents_model_added_by_ip = Session::get('remote_ip');
  $is_avaiable = self::checkAvaiability('documents_model','documents_model_name',$documents_model_name);
  if($is_avaiable != FALSE){

   $add_query = ("INSERT INTO documents_model (
                  documents_model_category,
                  documents_model_html,
                  documents_model_date_added,
                  documents_model_added_by,
                  documents_model_added_by_ip,
                  documents_model_name
                 ) VALUES (
                  '$documents_model_category',
                  '$documents_model_html',
                   NOW(),
                  '$documents_model_added_by',
                  '$documents_model_added_by_ip',
                  '$documents_model_name'
                 )");

   $success = $db->query($add_query);
   if(!$success){
    Logs::createLog('errors', Session::get('user'), 'Nieudana próba dodania wzoru dokumentu: '.$documents_model_name.': '.$db->error);
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Logs::createLog('log', Session::get('user'), 'Dodano wzór dokumentu: '.$documents_model_name);
    Session::setFlash('Nowy wzór dokumentu dodano poprawnie.','ok');
    $db->close();
    return TRUE;
   }
  }else{
   Session::setFlash('Wzór dokumentu o takiej nazwie już istnieje. Dokument nie został dodany.','warning');
   $db->close();
   return FALSE;
  }
 }//end function addNewDocumentMode()

 public static function editDocumentModel($id, $data = array()){
  $db = Model::getDB();
  $documents_model_name = $data['add_document_model_name'];
  $documents_model_category = $data['add_document_model_category'];
  $documents_model_html = htmlentities($data['add_document_model_html']);
  $documents_model_edited_by = Session::get('user');
  $documents_model_edited_by_ip = Session::get('remote_ip');

   $edit_query = ("UPDATE documents_model SET
                  documents_model_category='$documents_model_category',
                  documents_model_html='$documents_model_html',
                  documents_model_date_edited=NOW(),
                  documents_model_edited_by='$documents_model_edited_by',
                  documents_model_edited_by_ip='$documents_model_edited_by_ip',
                  documents_model_name='$documents_model_name'
                  WHERE documents_model_id = '$id'");

   $success = $db->query($edit_query);
   if(!$success){
    Logs::createLog('errors', Session::get('user'), 'Nieudana próba edycji wzoru dokumentu: '.$documents_model_name.': '.$db->error);
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Logs::createLog('log', Session::get('user'), 'Edytowano wzór dokumentu: '.$documents_model_name);
    Session::setFlash('Wzór dokumentu wyedytowano poprawnie.','ok');
    $db->close();
    return TRUE;
   }

 }//end function function editDocumentModel($data = array())

 public static function deleteDocument($table,$cell, $id){
  $db = Model::getDB();
   $delete_query = ("DELETE FROM $table WHERE $cell = '$id'");
   $success = $db->query($delete_query);
   if(!$success){
    Logs::createLog('errors', Session::get('user'),'FUNKCJA: deleteDocument($table,$cell, $id), '. $db->error);
    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
   }else{
    Session::setFlash('Wzór dokumentu usunięty poprawnie!','ok');
    $db->close();
    return TRUE;
   }
 }//end deleteDocument($table,$cell, $id)

 public static function checkAvaiability($table,$cell, $needle){
  $db = Model::getDB();
  $is_avaiable = FALSE;
  $check_query = ("SELECT * FROM $table WHERE $cell = '$needle'");
  $success = $db->query($check_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'), $db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    $db->close();
    return $is_avaiable;
   }else{
    $db->close();
    return $is_avaiable;
   }
  }
 }//end checkAvaiability($name)

 public static function getDocumentsList($table,$sort_by, $direction){//UNIVERSAL
  $db = Model::getDB();
  $models = array();
  $get_query = ("SELECT * FROM $table ORDER BY $sort_by $direction");
  $success = $db->query($get_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'),'Function:  getDocumentsList($table,$sort_by, $direction) '.$db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($models,$result);
    }
   }
  }
  $db->close();
  return $models;
 }//end function getDocumentsList($table,$sort_by, $direction)

 public static function getDocumentBy($table,$cell,$data){//UNIVERSAL
  $db = Model::getDB();
  $documents = array();
  $get_query = ("SELECT * FROM $table WHERE $cell = '$data'");
  $success = $db->query($get_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'),'Function:  getDocumentsList($table,$sort_by, $direction) '.$db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     array_push($documents,$result);
    }
   }
  }
  $db->close();
  return $documents;
 }//end function getDocumentById($table,$cell,$data)

 public static function getDocumentContent($id,$table,$cell,$document_id){//UNIVERSAL
  $db = Model::getDB();
  $content = '';
  $get_query = ("SELECT * FROM $table WHERE $document_id = '$id'");
  $success = $db->query($get_query);
  if(!$success){
   Logs::createLog('errors', Session::get('user'),'Function: getDocumentContent($id,$table,$cell,$document_id) '.$db->error);
   die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
  }else{
   $found = $success->num_rows;
   if($found > 0){
    while($result = $success->fetch_assoc()){
     $content = $result[$cell];
    }
   }
  }
  $db->close();
  return $content;
 }//end function getDocumentContent($table,$cell,$data)

}//END CLASS DOCUMENTS