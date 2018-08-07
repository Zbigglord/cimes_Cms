<?php
namespace Core;
/**
 * Creator: BabooJaga
 * Date: 2016-02-06
 * Time: 15:11
 */

class Router extends Model{

    protected $routes = [];
    protected $params = [];

    /**
     * @param $route
     * @param array $params
     */
    public function add($route, $params = []){

    $route = preg_replace('/\//','\\/',$route);//convert route to reg_exp escape slashes (basicaly just changing slashes direction for routing table purpose)
    $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)',$route);//than convert variables (like: {controller})
    $route = preg_replace('/\{([a-z]+):([^\}]+)\}/','(?P<\1>\2)',$route);//than convert custom v like: {id:\d+}
    $route = '/^'.$route.'$/i';//than adds delimiters '/' to it

    $this->routes[$route] = $params;

}//end function add()

    /**
     * @return array
     */
    public function getRoutes(){

    return $this->routes;

}

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }//end function get()

    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }//end function get()

    /**
     * @return array
     */
    public function getParams(){

    return $this->params;

}//end function get()

    /**
     * @param $url
     * @return bool
     */
    public function match($url){//here is where router needed to be rewritten due to seo changes

    foreach($this->routes as $route => $params){
        if (preg_match($route, $url, $matches)) {

            foreach ($matches as $key => $match) {
                if (is_string($key)) {
                    $params[$key] = $match;
                }
            }

            $this->params = $params;
            $action_id = $this->params['id'];
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller_for_function = $controller;
            $controller = $this->getNamespace().$controller;

            if(strpos($controller, 'Admin') !== false){

                //do nothing - we do not need seo links on backend

            }else{

                $action = $this->matchView($controller_for_function, $action_id);

                if($action != ''){
                    $this->setParam('action',$action);
                }


            }

            return true;
        }
    }
    return false;

} //end function match()

    /**
     * @param $url
     */
    public function dispatch($url){

    $url = $this->removeQueryStringVariables($url);

    if($this->match($url)){

    $controller = $this->params['controller'];
    $controller = $this->convertToStudlyCaps($controller);
    $controller = $this->getNamespace().$controller;

        if(class_exists($controller)){

            $controller_object = new $controller($this->params);
            $action = $this->params['action'];
            $action = $this->convertToCamelCase($action);

            if(is_callable([$controller_object, $action])){

                $controller_object->$action();

            }else{

                echo 'Method: '.$action.' in Controller: '.$controller.' not found! (yet to be written?)';

            }

        }else{

            echo 'Controller class: '.$controller. ' not found!';

        }
    }else{

        echo 'No route matched.';

    }

} //end function dispatch()

    /**
     * @param $string
     * @return mixed
     */
    protected function convertToStudlyCaps($string){

    return str_replace(' ','',ucwords(str_replace('-',' ',$string)));

} //end function convertToStudlyCaps()

    /**
     * @param $string
     * @return string
     */
    protected function convertToCamelCase($string){

    return lcfirst($this->convertToStudlyCaps($string));

} //end function convertToStudlyCaps()

    /**
     * @param $url
     * @return string
     */
    protected function removeQueryStringVariables($url){

   if($url != ''){

    $parts = explode('&',$url,2);

       if(strpos($parts[0],'=') === false){
            $url = $parts[0];
       }else{
           $url = '';
       }
   }
    return $url;

} //end function removeQueryStringVariables()

    /**
     * @return string
     */
    protected function getNamespace(){

        $namespace = 'App\Controllers\\';

        if(array_key_exists('namespace', $this->params)){

        $namespace .= $this->params['namespace'].'\\';

        }

        return $namespace;

    } //end function getNamespace()


    public function matchView($controller, $id = 0){

        if(isset($id) && $id != 0){

            $controller = strtolower($controller);

            switch($controller){//first two only because of spelling - other ones may just be treated in default

                //still have to check if Main is not gonna be problem

                case 'galleries':
                    $action = 'gallery';
                    break;
                case 'categories':
                    $action = 'category';
                    break;
                case 'singlecategory':
                    $action = 'single';
                    break;
                default:
                    $action = substr($controller, 0, -1);//just cut off last character

            }

            return $action;

        }else{//if it is just query string without standard id like login or index or so

            return FALSE; //todo: make sure its not gonna be problem with login for instance

        }

    }//END function matchView($controller, $id = NULL)


} //END OF CLASS ROUTER