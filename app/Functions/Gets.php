<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use Log;

class Gets {

    protected $requestMethod = 'GET';

    public function addressMatch() {

        $address = '603 Pine Hollow Dr Gonzales, LA';
        $apiName = "address/match/{$address}";

        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function geocodeAddress() {

        $address = '600 Pine Hollow Dr Gonzales, LA 70737';
        $apiName = "address/geocode/{$address}";
        
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function fibergraph($vetroId) {

        $point_a = 'e4a49525-dcf6-4661-a763-d003068ea489';  //hardcode the first one just to test
        $request  = "/network/fibergraph/paths?vetro_ids={$point_a},{$vetroId}";

        return $result = $request;
    }

    public function featuresQuery() {

        //$layer = "\"Lateral\"";
        //$layer = 2;
        $ID = "\"150973\"";
        $planID = "8, 6";
        $layerID = 44;
        
        $apiName = "features/query?offset=0&limit=100&plan_ids={$planID}&layer_ids={$layerID}";//filter=ID={$ID}";
        
        //dd($request);

        return Vetro::curlAPI($apiName, $this->requestMethod);
       
    }

    public function layers() {

        $apiName = "layers";
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }


}