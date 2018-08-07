<?php
/**
 * Creator: BabooJaga
 * Date: 2016-04-17
 * Time: 10:01
 */

namespace App\Controllers\Admin;

use App\Models\Admin\Snippet;
use Core\Session;
use \Core\View;

class Snippets extends \Core\Controller{

    public function snphpAction(){//ACSNI-0001

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            $sniplist = Snippet::getSnippets('php');
            $message = Session::getFlash();
            Session::unsetFlash();
            View::renderAdmin('Snippets/php.phtml',['sniplist'=>$sniplist, 'message'=>$message]);

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END LIST ACTION


    public function snjsAction(){//ACSNI-0002

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            $sniplist = Snippet::getSnippets('js');
            $message = Session::getFlash();
            View::renderAdmin('Snippets/js.phtml',['sniplist'=>$sniplist, 'message'=>$message]);

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END LIST ACTION

    public function addAction(){//ACSNI-0003

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_POST['snip_type']) && $_POST['snip_type'] == 'js'){

                View::renderAdmin('Snippets/add.phtml',['sniptype'=>'js']);

            }elseif(isset($_POST['snip_type']) && $_POST['snip_type'] == 'php'){

                View::renderAdmin('Snippets/add.phtml',['sniptype'=>'php']);

            }else{


            }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END LIST ACTION

    public static function saveAction(){

        if(!isset($_SESSION['user_nick'])){
            Session::setFlash(ATXT_MUST_BE_LOGGED_TO_SEE_ADMIN,'warning');
            header("location: http://".$_SERVER['SERVER_NAME']."/admin");
        }else if(isset($_SESSION['user_nick']) && $_SESSION['show_admin'] == 1) {

            if(isset($_POST['add_snip_type']) && isset($_POST['add_snip_name']) && $_POST['add_snip_name'] != '' && isset($_POST['add_snip_code']) && $_POST['add_snip_code'] != ''){

                $type = $_POST['add_snip_type'];
                $name = $_POST['add_snip_name'];
                $code = $_POST['add_snip_code'];

                $root_path = dirname(dirname(dirname(__FILE__)));
                $dir_path = $root_path.'/Files/'.$type;

                if (!file_exists($dir_path)) {
                    mkdir($dir_path, 0777, true);
                }

                $file = fopen($dir_path.'/'.$name.'.snp','w');

                if(!$file){

                    Session::setFlash(ATXT_ERROR_CREATE_FILE,'error');
                    $message = Session::getFlash();
                    Session::unsetFlash();
                    View::renderAdmin('Snippets/add.phtml',['sniptype'=>$type, 'message'=>$message]);

                }else{

                    fwrite($file,$code);
                    fclose($file);

                    $success = Snippet::addSnippet($type,$name);

                    if($success == TRUE){

                        Session::setFlash(SNIP_SAVED_PROPERLY,'ok');
                        $message = Session::getFlash();
                        Session::unsetFlash();

                    }else{

                        $message = Session::getFlash();
                        Session::unsetFlash();

                    }

                        $sniplist = Snippet::getSnippets($type);

                    View::renderAdmin('Snippets/'.$type.'.phtml',['sniplist'=>$sniplist, 'message'=>$message]);

                }


            }

        }else{
            Session::setFlash(ATXT_NO_RIGHTS_TO_SEE_ADMIN,'error');
            header("location: http://".$_SERVER['SERVER_NAME']);
        }

    }//END function saveAction()




}//END CLASS  Snippets
