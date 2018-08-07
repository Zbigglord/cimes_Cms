<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2018-03-16
 * Time: 08:50
 */

namespace App\Controllers;

use App\Models\Admin\Form;
use Core\Session;
use \Core\View;

class Forms extends \Core\Controller{

    protected function before(){

        //emptiness

    }

    protected function after(){

        //emptiness

    }

    public function formAction(){

        $param = $this->route_params;
        $form_id = $param['id'];
        $form = Form::getFormInputs($form_id);
        $page_title = $form['form_title'];
        $meta_desc = $form['metadescription'];
        $meta_keys = $form['metakeys'];
        $message = Session::getFlash();
        Session::unsetFlash();
        View::render('Forms/form.phtml',['form' => $form, 'message' => $message,'page_title' => $page_title, 'meta_desc' => $meta_desc, 'meta_keys' => $meta_keys]);

    }//End function formAction()

}//END CLASS