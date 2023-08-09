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
        return $this->process($action);
    }

    //** process csv file and push array to buildJSON() */
    protected function process($action) {

        $file = fopen("storage/app/address_import5.csv", "r");
        $data = fgetcsv($file, 1000, ",");
        $data = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data);

        $array = [];
        while (($row = fgetcsv($file, 500, ",")) !== false) {
            $array[] = array_combine($data, $row);

        }       
        fclose($file);
        $array = $this->featuresArray($array, $action);

        return $array;

    }

    protected function featuresArray($data, $action) {

        $apiName = 'features';
        //$requestMethod = 'POST';

        /** build array for the create features API */
        //instantiate data array 

        $bodyType = 'Feature';
        $geoType = 'Point';
        $x_vetro = array("layer_id"=> 26, "plan_id"=>6);
        
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
        
        $apiName = "features/query?offset=0&limit=100&plan_ids={$planID}&layer_ids={$layerID}";//filter=ID={$ID}";
        
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
 
    


}




