<?php
namespace App\Controllers;
use App\Models\Post;
use \Core\View;
/**
 * Creator: BabooJaga
 * Date: 2016-02-07
 * Time: 14:36
 */

class Main extends \Core\Controller{

    public function indexAction(){

        View::render('Main/main.phtml');

    }

    protected function before(){

        //nothing comes to mind
        //return false; //if return false next action will not be executed

    }

    protected function after(){

        //nothing comes to mind

    }

} 