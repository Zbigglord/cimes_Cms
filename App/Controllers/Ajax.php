<?php
/**
 * Created by PhpStorm.
 * User: BBJaga
 * Date: 2016-12-02
 * Time: 11:50
 */

namespace App\Controllers;


use App\Models\Admin\madminAjax;
use Core\Controller;
use App\Models\Customer;
use App\Models\Document;

class Ajax extends Controller
{

 public function checkIfCustomerExists(){
  if(isset($_POST['nip']) && $_POST['nip'] != ''){
   $is_already = Customer::getCustomerBy('cust_nip',$_POST['nip']);
   if($is_already != FALSE){
    echo 'Klient o takim numerze NIP już istnieje';
   }else{

   }
  }elseif(isset($_POST['name']) && $_POST['name'] != ''){
   $is_already = Customer::getCustomerBy('cust_name',$_POST['name']);
   if($is_already != FALSE){
    echo 'Klient o takim Nazwisku/Imieniu już istnieje';
   }else{

   }
  }elseif((isset($_POST['name']) && $_POST['name'] != '') && (isset($_POST['name']) && $_POST['name'] != '')){
   $is_already = Customer::getCustomerBy('cust_nip',$_POST['nip']);
   if($is_already != FALSE){
    echo 'Klient o takim numerze NIP już istnieje';
   }else{

   }
  }else{
    echo 'Jedno z pól: NIP albo NAZWISKO nie zostało wypełnione. Klient nie zostanie dodany!';
  }
 }//end checkIfCustomerExists()

 public function returnDocumentModelContent(){

  if(isset($_POST['id'])){
   $data = html_entity_decode(Document::getDocumentContent($_POST['id'], 'documents_model', 'documents_model_html', 'documents_model_id'));
   echo $data;
  }

 }//end function getDocumentContent()

 public function registerDownload(){

  if(isset($_POST['file_id']) && $_POST['file_id'] != 0){

   $clicked = madminAjax::downloadRegister($_POST['file_id']);

   if($clicked != FALSE){

    echo 'yes';

   }else{

    echo 'no';

   }

  }

 }//END function registerDownload()

}//END FRONTEND AJAX CLASS