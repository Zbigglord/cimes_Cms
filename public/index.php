<?php
/**
 * Creator: BabooJaga Studio
 * Date: 2016-02-06
 * Time: 14:12
 */

session_start();
require 'vendor/mailer/PHPMailerAutoload.php';
define ('ROOT_DIR',$_SERVER['DOCUMENT_ROOT']);//will be like: /var/www/vhosts/hellux.pl/zbigg.hellux.pl/public
define ('APP_DIR',dirname(__DIR__));//will be like: /var/www/vhosts/hellux.pl/zbigg.hellux.pl
define ('TEMP_DIR',$_SERVER['DOCUMENT_ROOT'].'/session/temp/');
define ('ROOT_ADDRESS',$_SERVER['SERVER_NAME']);
/**
 * AUTOLOADER
 */

spl_autoload_register(function($class){

    $root = dirname(__DIR__);
    $file = $root.'/'.str_replace('\\','/',$class).'.php';

    if(is_readable($file)){

        require $root.'/'.str_replace('\\','/',$class).'.php';

    }
});
////////////////////////////////////////////////// CRON JOBS //////////////////////////////////////////////////
/*if(isset($_GET['job'])){

    $job = $_GET['job'];
    if($job == 'cron'){
        \Core\Cron::createJob();
    }
}*/
//////////////////////////////////////////////// END Cron ///////////////////////////////////////////////
if(isset($_POST['set_lang'])){
    \Core\Lang::changeLang($_POST['set_lang']);
}

$lang = Core\Lang::getLang();
foreach($lang as $l => $v){
    $lang_id = $v['id'];
    $lang_code = $v['lang_code'];
}
if($lang_code == ''){
    $lang_code = 'pl';
}
\Core\Lang::load($lang_code,$lang_code);

/**
 * ROUTING
 */

$router = new Core\Router();
//match the route
$router->add('',['controller' => 'Main', 'action' => 'index']);//default so far controller - class
$router->add('admin',['controller' => 'Admin\Users', 'action' => 'index']);//default for admin area
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$router->dispatch($_SERVER['QUERY_STRING']);
