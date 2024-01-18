<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Search;
use App\Functions\Gets;
use App\Functions\Partials\Helpers;
use Log;

class Posts {

    protected $data;
    /*
    public function __construct(
        protected $file
    ) {

    }
    */
    protected $requestMethod = 'POST';

    public function features($action = '') {

        //CREATE FEATURES endpoint by name, hand off to process() 
        $processCsvFeatures = new Helpers();
        $processCsvFeatures = $processCsvFeatures->processCSV($action);
        return $processCsvFeatures;

    }


    protected function featuresArray($data, $action) {

        $apiName = 'features';

        /** build array for the create features API */
        $bodyType = 'Feature';
        $geoType = 'Point';
        $x_vetro = array("layer_id"=> 26, "plan_id"=>36);
        
        $partials = new Helpers();
        $partials = $partials->layer($data, $bodyType, $geoType, $x_vetro);
        
        if (! $action == 'data') {
            $curl = new Vetro();
            $curl = $curl->curlAPI($apiName, $this->requestMethod, $partials);
        }
        return $partials;
        
    }


    public function featuresQuery() {

        //$layer = "\"Lateral\"";
        //$layer = 2;
        $ID = "\"150973\"";
        $planID = "8, 6";
        $layerID = 44;
        $body = '';
        dd($ID);
        $apiName = "features/query?offset=0&limit=100&plan_ids={$planID}&layer_ids={$layerID}=filter=ID={$ID}";
        
        //dd($request);

        return Vetro::curlAPI($apiName, $this->requestMethod);
       
    }

    public function search() {

        return Search::search([8], "ID=150973");
    }
	
    public function polygonAddress($args) {

        $address['address'] = $args[0]; //'603 Pine Hollow Dr Gonzales, LA 70737';
        $apiName = "features/polygon/intersection/address";

        $data = json_encode($address);

        return Vetro::curlAPI($apiName, $this->requestMethod, $data);
    }


    public function networkGeograph($args) {

        $get_vetro_id = new Gets();
        $get_vetro_id = $get_vetro_id->featuresQuery('');
        $translate_vetro_id = new Helpers();
        $translate_vetro_id = $translate_vetro_id->vetro_id($get_vetro_id);
        //dd($translate_vetro_id);
        $apiName = "network/geograph/{$translate_vetro_id}";

        $curl = new Vetro();
        $curl = $curl->curlAPI($apiName, $this->requestMethod);

        
        dd($curl);

    }
 
    


}




