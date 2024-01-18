<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use app\Console\Commands\Vetro;
use App\Http\Controllers\Network;
use App\Http\Controllers\Features;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function __construct($apiName, $requestMethod, $args = '') {

        $this->apiName = $apiName; // something like 'network/geograph/'
        $this->requestMethod = $requestMethod; // 'GET'
        $this->args = $args; /// optional arguments

        $apiPrefix = current(explode('/', $this->apiName ));
        $apiName = explode('/', $apiName );
        $apiName = implode('',$apiName);
        $this->router($apiPrefix, $apiName, $requestMethod, $args);
        

    }

    public function router($apiPrefix, $apiName, $requestMethod, $args = []) {

        $apiPrefix = ucfirst($apiPrefix); 
        $className = __NAMESPACE__ . '\\' . $apiPrefix; //dd($className); //"App\Http\Controllers\\" . 
        // $apiName = '';
        //dd($apiName);
        $call = new $className($apiName, $requestMethod, $args); //::$apiPrefix($this->apiName, $requestMethod, $args);
        $call = $call->$apiPrefix($apiName, $requestMethod, $args);
        
        return $call;
    }


}
