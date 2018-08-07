<?php
/**
 * Created by BBJaga
 * Date: 2016-12-01
 * Time: 13:14
 */

namespace App\Controllers;

use App\Models\Customer;
use App\Models\Hosting;
use App\Models\User;
use Core\Controller;
use Core\Session;
use Core\View;

class Customers extends Controller
{
 public function newAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){
   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){
  if((isset($_POST['add_firm']) && $_POST['add_firm'] != '' && isset($_POST['add_email']) && $_POST['add_email'] != '') || (isset($_POST['add_surname']) && $_POST['add_surname'] != '' && isset($_POST['add_email']) && $_POST['add_email'] != '')){
    $new_customer = array();
    foreach($_POST as $key => $value){
     $new_customer[$key] = $value;
    }
    Customer::addCustomer($new_customer);
   $customers = Customer::getCustomersList();
   $message = Session::getFlash();
   Session::unsetFlash();
   View::render('Customers/list.phtml',['message' => $message, 'customers' => $customers]);
  }else{
   View::render('Customers/new.phtml');
  }
   }else{
    $customers = Customer::getCustomersList();
    Session::setFlash('Nie masz uprawnień do dodawania nowych klientów','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Customers/list.phtml',['message' => $message, 'customers' => $customers]);
   }//end if user has no rights to create new customer
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end if is session user
 }//end newAction()

 public function editAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){
   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){
    if((isset($_POST['add_firm']) && $_POST['add_firm'] != '' && isset($_POST['add_email']) && $_POST['add_email'] != '') || (isset($_POST['add_surname']) && $_POST['add_surname'] != '' && isset($_POST['add_email']) && $_POST['add_email'] != '')){

     $id = $_POST['customer_id'];
     $edit_customer = array();
     foreach($_POST as $key => $value){
      $edit_customer[$key] = $value;
     }
     Customer::editCustomer($id, $edit_customer);
     $message = Session::getFlash();
     Session::unsetFlash();
     $customers = Customer::getCustomersList();
     View::render('Customers/list.phtml',['customers' => $customers,'message' => $message]);

    }else{
     $customer_id = $_GET['id'];
     $customer = Customer::getCustomerBy('id',$customer_id);
     View::render('Customers/edit.phtml',['customer' => $customer]);
    }
   }else{
    $customers = Customer::getCustomersList();
    Session::setFlash('Nie masz uprawnień do edycji danych klientów','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Customers/list.phtml',['message' => $message, 'customers' => $customers]);
   }//end if user has no rights to create new customer
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end if is session user
 }//end editAction()

 public function listAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){
    $customers = Customer::getCustomersList();
  $message = Session::getFlash();
  Session::unsetFlash();
  View::render('Customers/list.phtml',['message' => $message, 'customers' => $customers]);
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }
 }//end listAction()

 public function customerAction(){

  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){
   if(isset($_GET['id'])){
    $customer = Customer::getCustomerBy('id',$_GET['id']);
    $message = Session::getFlash();
    View::render('Customers/customer.phtml',['customer' => $customer, 'message' => $message]);
   }else{
    //to do info if not get is set
   }
  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end no logged in
 }//end hostingAction()

 public function deleteAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   if(isset($_SESSION['ugroup']) && $_SESSION['ugroup'] <= 2){
    if(isset($_GET['id'])){

     $delete_customer_id = $_GET['id'];
     Customer::deleteCustomer($delete_customer_id);
     $message = Session::getFlash();
     Session::unsetFlash();
     View::render('Customers/list.phtml',['message' => $message]);
    }
   }else{
    Session::setFlash('Nie masz uprawnień do usuwania klientów','warning');
    $message = Session::getFlash();
    Session::unsetFlash();
    View::render('Customers/list.phtml',['message' => $message]);
   }//end No rights encapsulated

  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end no logged in
 }//end newAction()

 public function searchAction(){
  if(isset($_SESSION['user']) && $_SESSION['user'] != ''){

   if(isset($_POST['search_client'])){
    $by = '';
    $frase = $_POST['search_client'];
    if(isset($_POST['search_firm'])){

     $by = 'cust_firm';

    }else if(isset($_POST['search_name'])){

     $by = 'cust_name';

    }else if(isset($_POST['search_nbr'])){

     $by = 'cust_numer';

    }else if(isset($_POST['search_miasto'])){

     $by = 'cust_city';

    }else if(isset($_POST['search_ulica'])){

     $by = 'cust_street';

    }else if(isset($_POST['search_email'])){

     $by = 'cust_email';

    }else if(isset($_POST['search_nip'])){

     $by = 'cust_nip';

    }

    $custo = Customer::searchCustomerBy($by,$frase);
    if(!empty($custo)){
     $message = Session::getFlash();
     Session::unsetFlash();
     View::render('Customers/searchlist.phtml',['custo' => $custo,'message' => $message]);
    }else{
     Session::setFlash('Brak wyników wyszukiwania','ok');
     $message = Session::getFlash();
     Session::unsetFlash();
     View::render('Customers/list.phtml',['message' => $message]);
    }

   }

  }else{
   Session::setFlash('Sesja zakończona. Zaloguj się ponownie.','warning');
   $message = Session::getFlash();
   Session::unsetFlash();
   $remote_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
   Session::set('remote_ip',$remote_ip);
   View::render('Users/login.phtml',['message' => $message]);
  }//end no logged in
 }//end searchAction()

}//end Class CUSTOMERS