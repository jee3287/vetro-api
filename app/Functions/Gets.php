<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Partials\Helpers;
use Log;

class Gets {

    protected $requestMethod = 'GET';

    public function addressMatch() {

        $address = 'Hollow Ridge Ave';
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

        //initialize the array

        // $point_a = "\"46ce7861-b8f3-425f-8d37-6552148a96f2\"";  //hardcode the first one just to test
        // $point_z = "\"bdc26a4a-9565-4824-b4b7-5a1adefe7073\"";
        $point_a = 'a8f3b59c-74e5-498a-b090-fd534c8ac2eb';  //hardcode the first one just to test
        $point_z = 'bdc26a4a-9565-4824-b4b7-5a1adefe7073';
        $apiName  = "network/fibergraph/{$point_a},{$point_z}";
        //dd($apiName);
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function fiberTrace() {

        $point_a = '46ce7861-b8f3-425f-8d37-6552148a96f2';  //hardcode the first one just to test
        $point_z = 'bdc26a4a-9565-4824-b4b7-5a1adefe7073';
        $apiName  = "network/trace/{$point_a}/{$point_z}";
        //dd($apiName);
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }

    public function featuresQuery($args = '') {

        //$ID = $args[0];  
        //$ID = "\"GNZL CO\"";
        $ID = "\"150973\"";
        $address = str_replace (' ', '%20','603 Pine Hollow Dr Gonzales, LA');
        $planID =   '8,6,32'; 
        $layerID = (int) 26;
        
        $apiName = "features/query?offset=0&limit=200&plan_ids={$planID}&layer_ids={$layerID}";
        $request = Vetro::curlAPI($apiName, $this->requestMethod);

        dd($request);
        return $request; 
       
    }

    public function layers() {

        $apiName = "layers";
        $curl = Vetro::curlAPI($apiName, $this->requestMethod);
        dd($curl);
        $partials = new Helpers();
        $partials = $partials->layer($curl, $bodyType = '', $geoType = '', array('', 43));
        dd($curl);

    }

    public function features() {

        $x_vetro_id = '5343aae4-6771-4275-a876-ab9fc1f2cc90';
        $apiName = "features/{$x_vetro_id}";
        
        return Vetro::curlAPI($apiName, $this->requestMethod);
    }
    

    public function fibergraphPaths() {
        
        $point_a = '46ce7861-b8f3-425f-8d37-6552148a96f2';  //hardcode the first one just to test
        $point_z = 'bdc26a4a-9565-4824-b4b7-5a1adefe7073';
        $apiName  = "network/fibergraph/paths?vetro_ids={$point_z},{$point_a}";
        //dd($apiName);
        return Vetro::curlAPI($apiName, $this->requestMethod);

    }
    

}