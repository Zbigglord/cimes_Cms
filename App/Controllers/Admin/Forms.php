<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2018-01-11
 * Time: 09:19
 */

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\Admin\Form;

class Forms extends Controller{

  public static function addNewFormAction(){

      if(!isset($_SESSION['user_nick'])){

          Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
          header("location: http://".$_SERVER['SERVER_NAME']."/admin");

      }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

          if(isset($_POST['new-form-title']) && $_POST['new-form-title'] != '' && isset($_POST['new-form-id']) && $_POST['new-form-id'] != ''){

              $new_form = array();
              $new_form['form_title'] = $_POST['new-form-title'];
              $new_form['form_id_char'] = $_POST['new-form-id'];
              $new_form['form_name'] = isset($_POST['new-form-name']) ? $_POST['new-form-name'] : '';
              $new_form['form_class'] = isset($_POST['new-form-class']) ? $_POST['new-form-class'] : '';
              $new_form['form_action'] = isset($_POST['new-form-action']) ? $_POST['new-form-action'] : '';
              $new_form['form_method'] = isset($_POST['new-form-method']) ? $_POST['new-form-method'] : 1;
              $new_form['form_text_before'] = isset($_POST['new-form-text-before']) ? $_POST['new-form-text-before'] : 0;
              $new_form['form_text_after'] = isset($_POST['new-form-text-after']) ? $_POST['new-form-text-after'] : 0;
              $new_form['form_category'] = isset($_POST['new-form-category']) ? $_POST['new-form-category'] : 1;

              $form_saved = Form::insertForm($new_form);
              if($form_saved > 0){

                  $form = Form::getEmptyForm($form_saved);
                  Session::setFlash(ATXT_FORMS_CREATE_EMPTY_SUCCESS, 'ok');
                  $message = Session::getFlash();
                  Session::unsetFlash();

                  View::renderAdmin('Forms/Inputs/new.phtml',['form' => $form, 'message' => $message]);

              }else{

                  $new_form_char_id = 'form-generated_';
                  $last_id = Form::getLastFormId();

                  if($last_id != 0){
                      $last_id++;
                      $new_form_char_id = $new_form_char_id.$last_id;

                  }else{

                      $new_form_char_id = $new_form_char_id.'1';

                  }

                  Session::setFlash(ATXT_FORMS_EXISTS.' '.ATXT_FORMS_EXISTS_TRY_AGAIN,'error');
                  $message = Session::getFlash();
                  Session::unsetFlash();

                  View::renderAdmin('Forms/new.phtml',['new_form_char_id' => $new_form_char_id,'message' => $message]);

              }

          }else{

              $new_form_char_id = 'form-generated_';
              $last_id = Form::getLastFormId();

              if($last_id != 0){

                  $last_id++;
                  $new_form_char_id = $new_form_char_id.$last_id;

              }else{

                  $new_form_char_id = $new_form_char_id.'1';

              }

              View::renderAdmin('Forms/new.phtml',['new_form_char_id' => $new_form_char_id]);

          }

      }else{

          Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
          header("location: http://".$_SERVER['SERVER_NAME']);

      }

  }//END function addNewFormAction()

    public function editFormAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

                foreach($_POST as $key => $value){
                    $edit_form_id = $value;
                }

            if(isset($edit_form_id) && is_numeric($edit_form_id)){

                $form = Form::getEmptyForm($edit_form_id);
                View::renderAdmin('Forms/edit.phtml',['edit_form' => $form]);

            }



        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END editFormAction()

    public function saveEditedFormAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['form-id']) && $_POST['form-id'] != ''){

              $form = array();
                $form['form_id'] = $_POST['form-id'];
                $form['form_id_char'] = $_POST['edit-form-id'];
                $form['form_title'] = isset($_POST['edit-form-title']) ? $_POST['edit-form-title'] : '';
                $form['form_name'] = isset($_POST['edit-form-name']) ? $_POST['edit-form-name'] : '';
                $form['form_class'] = isset($_POST['edit-form-class']) ? $_POST['edit-form-class'] : '';
                $form['form_action'] = isset($_POST['edit-form-action']) ? $_POST['edit-form-action'] : '';
                $form['form_method'] = isset($_POST['edit-form-method']) ? $_POST['edit-form-method'] : 1;
                $form['form_text_before'] = isset($_POST['edit-form-text-before']) ? $_POST['edit-form-text-before'] : 0;
                $form['form_text_after'] = isset($_POST['edit-form-text-after']) ? $_POST['edit-form-text-after'] : 0;
                $form['form_category'] = isset($_POST['edit-form-category']) ? $_POST['edit-form-category'] : 1;

                $edited = Form::editForm($form);

                if($edited == TRUE){
                    Session::setFlash(ATXT_FORMS_EDITED,'ok');
                }

                $message = Session::getFlash();
                Session::unsetFlash();
                $forms = Form::getForms();
                View::renderAdmin('Forms/list.phtml',['forms' => $forms, 'message' => $message]);

            }else{

                View::renderAdmin('Forms/list.phtml');

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END editFormAction()



    public function formsListAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            $forms = Form::getForms();
            if(!empty($forms[0])){

                View::renderAdmin('Forms/list.phtml',['forms' => $forms]);

            }else{

                Session::setFlash(ATXT_FORMS_LIST_EMPTY,'warning');
                $message = Session::getFlash();
                Session::unsetFlash();
                View::renderAdmin('Forms/list.phtml',['forms' => $forms, 'message' => $message]);

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END function formsListAction()

    public function formInputsAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST) && !empty($_POST)){

                $form_ids = array();

                foreach($_POST as $key => $value){

                    $part = explode('_',$key);

                    if($part[0] == 'form'){

                        array_push($form_ids, $value);

                    }
                }

                if(count($form_ids) > 1){

                    $forms = Form::getForms();
                    Session::setFlash(AMENU_FORMS_CHECKBOX_CHECKED_MORE,'warning');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Forms/list.phtml',['forms' => $forms, 'message' => $message]);

                }else{

                    $form = Form::getFormInputs($form_ids[0]);
                    if($form['form_has_inputs'] != 0){

                        View::renderAdmin('Forms/Inputs/list.phtml',['form' => $form]);

                    }else{

                        Session::setFlash(ATXT_FORMS_HAS_NO_INPUTS,'warning');
                        $message = Session::getFlash();
                        Session::unsetFlash();
                        View::renderAdmin('Forms/Inputs/new.phtml',['form' => $form, 'message' => $message]);

                    }

                }
            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END formInputsAction()

    public static function editFormInputAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['inputs_form_id']) && $_POST['inputs_form_id'] != ''){

                $form_id = $_POST['inputs_form_id'];
                $input_id = '';

                foreach($_POST as $key => $value){

                    $part = explode('_',$key);

                    if($part[0] == 'input'){

                        $input_id = $value;

                    }
                }

                $input = Form::getInputById($form_id,$input_id);
                $hidden_types = array(1);
                $standart_types = array(2,3,4,5,6,7,8,10,11);
                $number_types = array(9);
                $radio_types = array(12);
                $select_types = array(13);
                $file_types = array(14);
                $submit_types = array(15);

                $picked_type = $input[0]['input_type'];

                switch($picked_type){

                    case in_array($picked_type,$hidden_types):

                        View::renderAdmin('Forms/Inputs/Edit/hidden.phtml',['input' => $input]);

                        break;

                    case in_array($picked_type,$standart_types):

                        View::renderAdmin('Forms/Inputs/Edit/standart.phtml',['input' => $input]);

                        break;

                    case in_array($picked_type,$number_types):

                        View::renderAdmin('Forms/Inputs/Edit/number.phtml',['input' => $input]);

                        break;

                    case in_array($picked_type,$radio_types):


                        break;

                    case in_array($picked_type,$select_types):

                        View::renderAdmin('Forms/Inputs/Edit/select.phtml',['input' => $input]);

                        break;

                    case in_array($picked_type,$file_types):

                        break;

                    case in_array($picked_type,$submit_types):

                        View::renderAdmin('Forms/Inputs/Edit/submit.phtml',['input' => $input]);

                        break;

                }

            }elseif($_POST['edit-input-id'] && $_POST['edit-input-id'] != ''){

                $data = array();
                $form_id = $_POST['form-input-id'];//needed just to get form inputs after
                $data['input-id'] = $_POST['edit-input-id'];
                $data['input-label'] = isset($_POST['input-label']) ? $_POST['input-label'] : '';
                if($data['input-label'] != ''){

                    $data['input-has-label'] = 1;

                }else{

                    $data['input-has-label'] = 0;

                }

                $data['input-label-class'] = isset($_POST['input-label-class']) ? $_POST['input-label-class'] : '';
                $data['input-label-data-placement'] = isset($_POST['input-label-data-placement']) ? $_POST['input-label-data-placement'] : '';
                $data['input-label-data-toggle'] = isset($_POST['input-label-data-toggle']) ? $_POST['input-label-data-toggle'] : '';
                $data['input-label-data-original-title'] = isset($_POST['input-label-data-original-title']) ? $_POST['input-label-data-original-title'] : '';
                $data['input-label-title'] = isset($_POST['input-label-title']) ? $_POST['input-label-title'] : '';
                $data['input-name'] = $_POST['input-name'];
                $data['input-id-char'] = isset($_POST['input-id']) ? $_POST['input-id'] : '';
                $data['input-class'] = isset($_POST['input-class']) ? $_POST['input-class'] : '';
                $data['input-value'] = isset($_POST['input-value']) ? $_POST['input-value'] : '';
                $data['input-placeholder'] = isset($_POST['input-placeholder']) ? $_POST['input-placeholder'] : '';
                $data['input-maxlength'] = isset($_POST['input-maxlength']) ? $_POST['input-maxlength'] : '';
                $data['input-required'] = isset($_POST['input-required']) ? 1 : 0;
                $data['input-disabled'] = isset($_POST['input-disabled']) ? 1 : 0;
                $data['input-checked'] = isset($_POST['input-checked']) ? 1 : 0;
                $data['input-display'] = isset($_POST['input-display-type']) ? $_POST['input-display-type'] : 1;
                $data['input-values'] = isset($_POST['input-values']) ? $_POST['input-values'] : '';
                $data['input-rows'] = isset($_POST['input-rows']) ? $_POST['input-rows'] : '';
                $data['input-data-show-tick'] = isset($_POST['input-data-show-tick']) ? $_POST['input-data-show-tick'] : 0;
                $data['input-data-live-search'] = isset($_POST['input-data-live-search']) ? $_POST['input-data-live-search'] : 0;
                $data['input-data-live-search-placeholder'] = isset($_POST['input-data-live-search-placeholder']) ? $_POST['input-data-live-search-placeholder'] : '';
                $data['input-min'] = isset($_POST['input-min']) ? $_POST['input-min'] : 0;
                $data['input-max'] = isset($_POST['input-max']) ? $_POST['input-max'] : 10;
                $data['input-step'] = isset($_POST['input-step']) ? $_POST['input-step'] : 1;
                $data['input-datepicker'] = isset($_POST['input-datepicker']) ? $_POST['input-datepicker'] : 0;
                $data['input-onclick'] = isset($_POST['input-onclick']) ? $_POST['input-onclick'] : '';

                $edited = Form::editInputForm($data);
                if($edited == TRUE){
                    Session::setFlash(ATXT_FORMS_INPUT_EDITED,'ok');
                }

                $message = Session::getFlash();
                Session::unsetFlash();
                $form = Form::getFormInputs($form_id);
                View::renderAdmin('Forms/Inputs/list.phtml',['form' => $form, 'message' => $message]);

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END function formEditInputAction()

    public function formInputsSaveAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST) && !empty($_POST)){

                $form_input = array();

                foreach($_POST as $key => $value){

                    $part = explode('_',$key);
                    $form_input[$part[0]] = $value;

                }

                $hidden_types = array(1);
                $standart_types = array(2,3,4,5,6,7,8,10,11);
                $number_types = array(9);
                $radio_types = array(12);
                $select_types = array(13);
                $file_types = array(14);
                $submit_types = array(15);

                $picked_type = $form_input['new-form-input-type'];
                $inserted = FALSE;

                switch($picked_type){

                    case in_array($picked_type,$hidden_types):

                        $inserted = Form::inputSaveHidden($form_input);

                        break;

                    case in_array($picked_type,$standart_types):

                        $inserted = Form::inputSaveStandard($form_input);

                        break;

                    case in_array($picked_type,$number_types):

                        $inserted = Form::inputSaveNumber($form_input);

                        break;

                    case in_array($picked_type,$radio_types):

                        break;

                    case in_array($picked_type,$select_types):

                        $inserted = Form::inputSaveStandard($form_input);

                        break;

                    case in_array($picked_type,$file_types):

                        break;

                    case in_array($picked_type,$submit_types):

                        break;

                }


                if($inserted != FALSE){

                    $form_id = $form_input['new-form-created-id'];
                    $form = Form::getFormInputs($form_id);
                    Session::setFlash(ATXT_FORMS_INPUT_INSERTED,'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    //todo maybe redirect to inputs list?
                    View::renderAdmin('Forms/Inputs/new.phtml',['form' => $form, 'message' => $message]);

                }else{

                    $form_id = $form_input['new-form-created-id'];
                    $form = Form::getFormInputs($form_id);
                    Session::setFlash(ATXT_FORMS_INPUT_NOT_INSERTED,'error');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Forms/Inputs/new.phtml',['form' => $form, 'message' => $message]);

                }

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END formInputsAction()

    public function cancelFormInputsAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

           if(isset($_POST['inputs_form_id'])){

               $form = Form::getFormInputs($_POST['inputs_form_id']);
               if($form['form_has_inputs'] != 0){

                   Session::setFlash(AMENU_FORMS_INPUTS_CHECKBOX_NOT_CHECKED,'warning');
                   $message = Session::getFlash();
                   Session::unsetFlash();
                   View::renderAdmin('Forms/Inputs/list.phtml',['form' => $form, 'message' => $message]);

               }else{

                   Session::setFlash(ATXT_FORMS_HAS_NO_INPUTS,'warning');
                   $message = Session::getFlash();
                   Session::unsetFlash();
                   View::renderAdmin('Forms/Inputs/new.phtml',['form' => $form, 'message' => $message]);

               }

           }elseif(isset($_GET['data'])){

               $form = Form::getFormInputs($_GET['data']);
               View::renderAdmin('Forms/Inputs/list.phtml',['form' => $form]);

           }


        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END function formsListAction()

    public function addFormInputsAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['inputs_form_id']) && $_POST['inputs_form_id'] != ''){

                    $form = Form::getFormInputs($_POST['inputs_form_id']);

                        View::renderAdmin('Forms/Inputs/new.phtml',['form' => $form]);

            }else if(isset($_GET['data']) && !empty($_GET['data'])){

                $form = Form::getFormInputs($_GET['data']);
                if($form['form_has_inputs'] != 0){

                    View::renderAdmin('Forms/Inputs/list.phtml',['form' => $form]);

                }else{

                    $forms = Form::getForms();
                    View::renderAdmin('Forms/list.phtml',['forms' => $forms]);

                }

            }elseif(isset($_GET['radio_data']) && $_GET['radio_data'] != ''){

                $form = Form::getFormInputs($_GET['radio_data']);

                View::renderAdmin('Forms/Inputs/Radio/new.phtml',['form' => $form]);

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END formInputsAction()

    public function optionsListAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['inputs_form_id']) && $_POST['inputs_form_id'] != ''){

                $form_id = $_POST['inputs_form_id'];
                $select_id = '';

                foreach($_POST as $key => $value){

                    if($key != 'inputs_form_id'){

                        $temp_parts = explode('_',$key);

                        if($temp_parts[0] == 'input' && $temp_parts[1] == $form_id){

                            $select_id = $value;

                        }

                    }

                }

                $select = Form::getSelectOptions($form_id, $select_id);

                View::renderAdmin('Forms/Inputs/Options/list.phtml',['select' => $select]);

            }elseif(isset($_POST['select_id']) && $_POST['select_id'] != ''){

                $select_id = $_POST['select_id'];
                $select = Form::getSelectOptionsBySelect($select_id);

                View::renderAdmin('Forms/Inputs/Options/list.phtml',['select' => $select]);

            }elseif(isset($_GET['data'])){

                $select_id = $_GET['data'];
                $select = Form::getSelectOptionsBySelect($select_id);

                View::renderAdmin('Forms/Inputs/Options/list.phtml',['select' => $select]);

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END formInputsAction()

    public function addSelectOptionAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['select_id']) && $_POST['select_id'] != ''){

                $select_id = $_POST['select_id'];
                $select = Form::getSelectOptionsBySelect($select_id);

                View::renderAdmin('Forms/Inputs/Options/new.phtml',['select' => $select]);

            }elseif(isset($_POST['new-option-select-id']) && $_POST['new-option-select-id'] != ''){

                $options = array();
                $options['option_select_id'] = $_POST['new-option-select-id'];
                $options['option_value'] = $_POST['new-option-value'];
                $options['option_data_subtext'] = isset($_POST['new-option-subtext']) ? $_POST['new-option-subtext'] : '';
                $options['option_data_tokens'] = isset($_POST['new-option-tokens']) ? $_POST['new-option-tokens'] : '';
                $options['option_text'] = $_POST['new-option-title'];

                $inserted = Form::selectInsertNewOption($options);

                if($inserted == TRUE){
                    Session::setFlash(ATXT_FORMS_SELECT_OPTION_ADDED,'ok');
                }

                $message = Session::getFlash();
                Session::unsetFlash();
                $select = Form::getSelectOptionsBySelect($options['option_select_id']);

                View::renderAdmin('Forms/Inputs/Options/list.phtml',['message' => $message,'select' => $select]);

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END formInputsAction()

    public function deleteSelectOptionAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['select_id']) && $_POST['select_id'] != ''){

                $select_id = $_POST['select_id'];
                foreach($_POST as $key => $value){

                        $temp_parts = explode('_',$key);

                        if($temp_parts[0] == 'option'){

                            $option_id = $temp_parts[2];

                        }

                }

                $deleted = Form::deleteSelectOption($select_id, $option_id);

                if($deleted == TRUE){

                    Session::setFlash(ATXT_FORMS_SELECT_OPTION_DELETED,'ok');

                }

                $message = Session::getFlash();
                Session::unsetFlash();
                $select = Form::getSelectOptionsBySelect($select_id);

                View::renderAdmin('Forms/Inputs/Options/list.phtml',['message' => $message,'select' => $select]);

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END formInputsAction()

    public function editSelectOptionAction(){

        if(!isset($_SESSION['user_nick'])){

            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");

        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1){

            if(isset($_POST['select_id']) && $_POST['select_id'] != ''){

                $select_id = $_POST['select_id'];
                $option_id = '';
                foreach($_POST as $key => $value){

                    $temp_parts = explode('_',$key);

                    if($temp_parts[0] == 'option'){

                        $option_id = $temp_parts[2];

                    }

                }

                $select = Form::getSelectOptionsBySelect($select_id);
                $option = Form::getSelectOptionById($option_id);

                View::renderAdmin('Forms/Inputs/Options/edit.phtml',['option' => $option,'select' => $select]);

            }elseif(isset($_POST['edit-option-select-id']) && $_POST['edit-option-select-id'] != ''){

                $data = array();
                $data['option_select_id'] = $_POST['edit-option-select-id'];
                $data['option_id'] = $_POST['edit-option-id'];
                $data['option_value'] = $_POST['edit-option-value'];
                $data['option_data_subtext'] = $_POST['edit-option-subtext'];
                $data['option_data_tokens'] = $_POST['edit-option-tokens'];
                $data['option_text'] = $_POST['edit-option-title'];

                $edited = Form::selectEditOption($data);
                if($edited != FALSE){

                    Session::setFlash(ATXT_FORMS_SELECT_OPTION_EDITED,'ok');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    $select = Form::getSelectOptionsBySelect($_POST['edit-option-select-id']);

                    View::renderAdmin('Forms/Inputs/Options/list.phtml',['message' => $message,'select' => $select]);

                }else{

                    $message = Session::getFlash();
                    Session::unsetFlash();
                    $select = Form::getSelectOptionsBySelect($_POST['edit-option-select-id']);

                    View::renderAdmin('Forms/Inputs/Options/list.phtml',['message' => $message,'select' => $select]);

                }

            }

        }else{

            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);

        }

    }//END formInputsAction()

}//END CLASS