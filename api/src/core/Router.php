<?php
namespace Api\Core;
//use Api\Routes;
use Api\Core\HttpStatusCode;
use Api\Local\Auth;
use \ReflectionClass;

final class Router{
    private static $instance;
    private $request;
    private $response;

    public static function getInstance(){
        if ( !isset(self::$instance) ) {
            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    private function __construct(/*$method, $request, $token, $format = 'application/json', $parameters = []*/){
    }

    public function handleRequest() {
        //create new request and response every time?
        $this->request = new Request();
        $this->response = new Response();

        //check user data and permission level
        if( $this->needsAuthentication() ){
            if( !(new Auth())->authorizeRequest($this->request) ){
                $this->response->error('user not authorized', HttpStatusCode::FORBIDDEN);
            }
        }

        //check request and action
        if( $this->request->getResource() === null ){
            $this->response->error('No resource specified', HttpStatusCode::BAD_REQUEST);
        }
        $controller_full_name = 'Api\\Routes\\'.ucfirst($this->request->getResource());
        $controller = new $controller_full_name;
        $method = $this->request->getMethod();

        if( !method_exists($controller, $method) ){
            $this->response->error('No action found', HttpStatusCode::NOT_FOUND);
        }
        
        //make request and return data
        $this->response->setContent($controller->$method($this->request->getParameters()));

        unset($controller_full_name);
        unset($controller);
        //make response
    }
    
    public function send(){
        exit( $this->response->isContent()
            ? $this->response->ok($this->request)
            : $this->response->error()
        ); 
    }

    private function needsAuthentication() : bool {
        global $exclude_from_auth;    //declared in config.php file

        return !(in_array($this->request->getResource(), array_keys($exclude_from_auth)) && in_array($this->request->getMethod(), array_keys($exclude_from_auth[$this->request->getResource()])));
    }
}