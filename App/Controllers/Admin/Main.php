<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-13
 * Time: 19:25
 */

namespace App\Controllers\Admin;
use \Core\View;

class Main extends \Core\Controller{

    protected function before(){


    }

    protected function after(){

        //silence

    }

    public function indexAction(){

       View::renderAdmin('Login/index.phtml');

    }

} //end class Main