<?php
/**
 * Creator: BabooJaga
 * Date: 2016-02-07
 * Time: 15:47
 */

namespace Core;
use Core\Lang;
/**
 * Class Controller
 * @package Core
 * Basic controller to pass variables to all of controllers.
 * Other controllers inherits from it and extends it
 */

abstract class Controller {

    protected $route_params = [];

    public function __construct($route_params){

        $this->route_params = $route_params;

    }

    public function __call($name, $args){//magic method for filtering actions (used to run function before call main function and than function after)

        $method = $name.'Action';
        if(method_exists($this,$method)){
            if($this->before() !== false){//to run next method before must not be false (but may be empty)
                call_user_func_array([$this,$method],$args);
                $this->after();//may be anything - ? to think of ?
            }
        }else{
            echo "Method $method not found in controller: ".get_class($this);
        }

    }

    protected function before(){

    }

    protected function after(){
        //let's just leave it empty for now
    }

} // END  BASIC CONTROLLER CLASS