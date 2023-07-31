<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use Log;

class Get {

    protected $requestMethod = 'GET';

    public function addressMatch() {

        $address = '603 Pine Hollow Dr Gonzales, LA';
        $apiName = "address/match/{$address}";

        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function geocodeAddress($address = '') {

        if ($address <> '') {
            $address = '600 Pine Hollow Dr Gonzales, LA 70737';
        }
        $apiName = "address/geocode/{$address}";
        
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function fibergraph() {

        $point_a = "\"46ce7861-b8f3-425f-8d37-6552148a96f2\"";  //hardcode the first one just to test
        $point_z = "\"bdc26a4a-9565-4824-b4b7-5a1adefe7073\"";
        $apiName  = "network/fibergraph/[{$point_a},{$point_z}]";
        dd($apiName);
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function fiberTrace() {

        $point_a = '46ce7861-b8f3-425f-8d37-6552148a96f2';  //hardcode the first one just to test
        $point_z = 'bdc26a4a-9565-4824-b4b7-5a1adefe7073';
        $apiName  = "network/trace/{$point_a}/{$point_z}";
        //dd($apiName);
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function featuresQuery() {

        //$layer = "\"Lateral\"";
        //$layer = 2;
        $ID = "\"GNZL CO\"";
        $address = str_replace (' ', '%20','603 Pine Hollow Dr Gonzales, LA');
        $planID = (int) 8;
        $layerID = (int) 21;
        
        $apiName = "features/query?offset=0&limit=100&plan_ids={$planID}&layer_ids={$layerID}&filter=ID={$ID}";
        //dd($apiName);
        
        //dd($request);

        return Vetro::curlAPI($apiName, $this->requestMethod);
       
    }

    public function layers() {

        $apiName = "layers";
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function features() {

        $x_vetro_id = '46ce7861-b8f3-425f-8d37-6552148a96f2';
        $apiName = "features/{$x_vetro_id}";
        
        return Vetro::curlAPI($apiName, $this->requestMethod);
    }

    

}