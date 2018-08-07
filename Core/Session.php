<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-21
 * Time: 12:42
 */

namespace Core;



class Session {

    public static function set($key, $value){

        $_SESSION[$key] = $value;

    }


    public static function get($key){

        if(isset($_SESSION[$key])){

            return $_SESSION[$key];

        }
        return FALSE;

    }

    public static function delete($key){

        if(isset($_SESSION[$key])){

            unset($_SESSION[$key]);

        }

    }

    public static function destroy(){

        session_destroy();

    }

    public static function getParams(){

        $params = array();
        if(isset($_SESSION)){

            foreach($_SESSION as $key => $value){
                $params[$key] = $value;
            }

        }

        return $params;

    }

    public static function setParams($params = array()){

        if(isset($_SESSION)){

            foreach($params as $key => $value){
                $_SESSION[$key] = $value;
            }

        }

    }

    public static function unsetParams($params = array()){

        if(isset($_SESSION)){

            foreach($params as $key){

                if(isset($_SESSION[$key])){

                    unset($_SESSION[$key]);

                }
            }

        }

    }

    public static function isUser(){

        if(isset($_SESSION)){

            if(isset($_SESSION['user_email']) && $_SESSION['user_email'] != '' && isset($_SESSION['user_active']) && $_SESSION['user_active'] === 1){

                return TRUE;

            }

        }

        return FALSE;

    }

    public static function fUserLoggedIn(){

        if(isset($_SESSION)){

            if(isset($_SESSION['fuser_logged']) && $_SESSION['fuser_logged'] != ''){

                return TRUE;

            }

        }

        return FALSE;

    }

    public static function fStudentLoggedIn(){

        if(isset($_SESSION)){

            if(isset($_SESSION['fstudent']) && $_SESSION['fstudent'] != ''){

                return TRUE;

            }

        }

        return FALSE;

    }

    public static function setFlash($message,$messagetype = ''){

        if($messagetype == 'error'){
            $mess = '<div class="message message-error">'.$message.'</div>';
        }elseif($messagetype == 'warning'){
            $mess = '<div class="message message-warning">'.$message.'</div>';
        }elseif($messagetype == 'advice'){
            $mess = '<div class="message message-advice">'.$message.'</div>';
        }elseif($messagetype == 'ok'){
            $mess = '<div class="message message-ok">'.$message.'</div>';
        }else{
            $mess='';
        }

        $_SESSION['flash'] = $mess;
        $message = $_SESSION['flash'];
        return $message;
    }

    public static function unsetFlash(){
        if(isset($_SESSION['flash'])){
            unset($_SESSION['flash']);
            Session::setFlash('','');
        }
    }

    public static function getFlash(){

        if(isset($_SESSION['flash'])){
            $message = $_SESSION['flash'];
        }else{
            $message = '';
        }

        return $message;

    }

} //end Session Class
