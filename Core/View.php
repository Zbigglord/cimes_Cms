<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-13
 * Time: 20:26
 */

namespace Core;

use Core\Template;

class View {

    public static function render($view,$args=[]){//render single layout frontend

        extract($args, EXTR_SKIP);//extract $args from model to pass it though controller to view !important

        $template = Template::getTemplateFrontend();

        $head = "../App/Views/Frontend/".$template."/head.phtml";
        $header = "../App/Views/Frontend/".$template."/header.phtml";
        $file = "../App/Views/Frontend/".$template."/".$view; //relative to Core directory
        $footer = "../App/Views/Frontend/".$template."/footer.phtml";
        if(is_readable($head) && is_readable($header) && is_readable($file) && is_readable($footer)){

            require $head;
            require $header;
            require $file;
            require $footer;

        }else{

            echo ATXT_FILE_NOT_FOUND .$file;

        }

    }

    public static function renderChanged($view,$ch_what, $w_file,$args=[]){//render content with random head or header or footer

        extract($args, EXTR_SKIP);//extract $args from model to pass it though controller to view !important

        $template = Template::getTemplateFrontend();

        $head = "../App/Views/Frontend/".$template."/head.phtml";
        $header = "../App/Views/Frontend/".$template."/header.phtml";
        $file = "../App/Views/Frontend/".$template."/".$view; //relative to Core directory
        $footer = "../App/Views/Frontend/".$template."/footer.phtml";

        switch($ch_what){

            case 'head':
                $head = "../App/Views/Frontend/".$template."/".$w_file.".phtml";
                break;
            case 'header':
                $header = "../App/Views/Frontend/".$template."/".$w_file.".phtml";
                break;
            case 'footer':
                $footer = "../App/Views/Frontend/".$template."/".$w_file.".phtml";
                break;
        }

        if(is_readable($head) && is_readable($header) && is_readable($file) && is_readable($footer)){

            require $head;
            require $header;
            require $file;
            require $footer;

        }else{

            echo ATXT_FILE_NOT_FOUND .$file;

        }

    }

    public static function render2layout($view,$args=[]){

        extract($args, EXTR_SKIP);//extract $args from model to pass it though controller to view !important

        $template = Template::getTemplateFrontend();

        $head = "../App/Views/Frontend/".$template."/head.phtml";
        $header = "../App/Views/Frontend/".$template."/header.phtml";
        $left2layout = "../App/Views/Frontend/".$template."/left2layout.phtml";
        $file = "../App/Views/Frontend/".$template."/".$view; //relative to Core directory
        $footer = "../App/Views/Frontend/".$template."/footer.phtml";
        if(is_readable($head) && is_readable($header) && is_readable($file) && is_readable($footer)){

            require $head;
            require $header;
            echo '<div class="row no-margin">';
            echo '<div class="col-lg-3 no-padding">';
            require $left2layout;
            echo '</div>';
            echo '<div class="col-lg-9 no-padding-right">';
            require $file;
            echo '</div>';
            echo '</div>';
            require $footer;

        }else{

            echo ATXT_FILE_NOT_FOUND .$file;

        }

    }

    public static function renderAdmin($view,$args=[])
    {

        extract($args, EXTR_SKIP);//extract $args from model to pass it though controller to view !important

        $template = Template::getTemplateAdmin();

        $head = "../App/Views/Admin/".$template."/head.phtml";
        $header = "../App/Views/Admin/".$template."/header.phtml";
        $file = "../App/Views/Admin/".$template."/".$view; //relative to Core directory
        $footer = "../App/Views/Admin/".$template."/footer.phtml";
        if (is_readable($head) && is_readable($header) && is_readable($file) && is_readable($footer)) {

            require $head;
            require $header;
            require $file;
            require $footer;

        } else {

            echo "View $file not found.";

        }
    }


    public static function renderEmpty($view,$args=[]){//no header

        extract($args, EXTR_SKIP);//extract $args from model to pass it though controller to view !important

        $template = Template::getTemplateAdmin();

        $head = "../App/Views/Admin/".$template."/head.phtml";
        $file = "../App/Views/Admin/".$template."/".$view; //relative to Core directory
        $footer = "../App/Views/Admin/".$template."/footer.phtml";

        if(is_readable($head) && is_readable($file) && is_readable($footer)){

            require $head;
            require $file;
            require $footer;

        }else{

            echo ATXT_FILE_NOT_FOUND .$file;

        }

    }//END function renderEmpty($view,$args=[])

    public static function getViewConfig(){

    }//END function getViewConfig()

} //end class View