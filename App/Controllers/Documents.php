<?php
/**
 * Created by BBJaga
 * Date: 2016-12-14
 * Time: 15:01
 */

namespace App\Controllers;

use App\Models\Document;
use Core\Controller;
use Core\View;
use Core\Session;

class Documents extends Controller
{

 public function mnewAction(){//WZORY DOKUMENTÓW
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){

    if(isset($_POST['add_document_model_name']) && $_POST['add_document_model_name'] != '' ){
     $data = array();
     foreach($_POST as $key => $value){
      $data[$key] = $value;
     }
     Document::addNewDocumentMode($data);
     $message = Session::getFlash();
     Session::unsetFlash();
     View::render('Documents/new_model.phtml',['message' => $message]);
    }else{
     View::render('Documents/new_model.phtml');
    }

   }else{
    Session::setFlash('Nie masz uprawnień do dodawania nowych wzorów','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Documents/list_model.phtml',['message' => $message]);
   }
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end mnewAction()

 public function meditAction(){//WZORY DOKUMENTÓW
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){

    if(isset($_POST['add_document_model_name']) && $_POST['add_document_model_name'] != '' ){
     $data = array();
     foreach($_POST as $key => $value){
      $data[$key] = $value;
     }
     Document::editDocumentModel($_POST['document_edit_id'],$data);
     $message = Session::getFlash();
     Session::unsetFlash();
     $models = Document::getDocumentsList('documents_model','documents_model_category','ASC');
     View::render('Documents/list_model.phtml',['message' => $message, 'models' => $models]);
    }elseif(isset($_GET['id'])){
     $model_id = $_GET['id'];
     $models = Document::getDocumentBy('documents_model','documents_model_id',$model_id);
     View::render('Documents/edit_model.phtml',['models' => $models]);
    }else{
     //View::render('Documents/new_model.phtml');
    }
   }else{
    Session::setFlash('Nie masz uprawnień do dodawania nowych wzorów','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    $models = Document::getDocumentsList('documents_model','documents_model_category','ASC');
    View::render('Documents/list_model.phtml',['message' => $message, 'models' => $models]);
   }
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end meditAction()

 public function mlistAction(){//WZORY DOKUMENTÓW
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   $models = Document::getDocumentsList('documents_model','documents_model_id','ASC');
   if(!empty($models)){
    View::render('Documents/list_model.phtml',['models' => $models]);
   }else{
    Session::setFlash('Lista wzorów dokumentów jest pusta.','error');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Documents/list_model.phtml',['message' => $message]);
   }

  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end mlistAction()

 public function mdeleteAction(){//WDOKUMENTY
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){
   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){
   if(isset($_GET['id'])){
    Document::deleteDocument('documents_model','documents_model_id',$_GET['id']);
    $models = Document::getDocumentsList('documents_model','documents_model_category','ASC');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Documents/list_model.phtml',['message' => $message,'models' => $models]);
   }else{
    $models = Document::getDocumentsList('documents_model','documents_model_category','ASC');
    if(!empty($models)){
     View::render('Documents/list_model.phtml',['models' => $models]);
    }else{
     Session::setFlash('Lista wzorów dokumentów jest pusta.','error');
     $message = Session::getFlash();
     Session::unsetFlash();
     View::render('Documents/list_model.phtml',['message' => $message]);
    }
   }
  }else{
    Session::setFlash('Nie masz uprawnień do usuwania wzorów','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    $models = Document::getDocumentsList('documents_model','documents_model_category','ASC');
    View::render('Documents/list_model.phtml',['message' => $message, 'models' => $models]);
   }
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end mdeleteAction()
//======================================================================= END WZORY
 public function newAction(){//DOKUMENTY
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){


   }else{
    Session::setFlash('Nie masz uprawnień do dodawania nowych wzorów','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Documents/list.phtml',['message' => $message]);
   }
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end mnewAction()

 public function listAction(){//DOKUMENTY
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   View::render('Documents/list_document.phtml');

  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end listAction()



}//END CLASS DOCUMENTS