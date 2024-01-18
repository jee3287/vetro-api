<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Gets;
use App\Functions\Partials\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Features;
use Log;


class Network  {

    protected $apiPrefix = 'network/';

    
    public function network($apiName, $requestMethod, $args) {
        
        $apiNewName = str_replace('/', '', $apiName);
        //dd($args);
        $this->$apiNewName($apiName, $requestMethod, $args);

            
    }

    protected function networkgeograph($apiName, $requestMethod, $vetro_ids = '') {

        //get the vetro IDs of a given feature or features and pass them as strings of an array
        // $features = new Features();
        // $features = $features->features('featuresquery', $requestMethod, $args);
        // $vetro_ids = new Helpers($features);
        // $vetro_ids = $vetro_ids->vetro_id($features);
        $vetro_ids = '749b0fb3-15bf-4863-be33-ea5aa30e2c45';
        $apiName = "network/geograph/{$vetro_ids}";  
        $response = Vetro::curlAPI($apiName, $requestMethod);
        $response['x-vetro'] = $vetro_ids;
        $response = json_encode($response);

        //Storage::put('test.json', $response);
        dd($response);
        return $response;
    
    }

    protected function networkfibergraph($apiName, $requestMethod, $args) {

        // get the vetro IDs of a given feature or features and pass them as strings of an array
        $features = new Gets();
        $features = $features->featuresQuery();
        $vetro_ids = new Helpers($features);
        $vetro_ids = $vetro_ids->vetro_id($features);

        $apiName = "{$apiName}/00bc5a7a-ab58-4caf-942e-d1e81bd19a38"; 

        $response = Vetro::curlAPI($apiName, $requestMethod);
        dd($response);

        //dd("{$apiName}/{$vetro_ids}");
        //dd($vetro_ids);

        return $response;
    
    }
    


}