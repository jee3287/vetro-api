<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Search;
use App\Functions\Get;
use App\Functions\Partials\Helpers;
use Log;

class Post {

    protected $data;
    /*
    public function __construct(
        protected $file
    ) {

    }
    */
    protected $requestMethod = 'POST';

    public function features() {
        //CREATE FEATURES endpoint by name, hand off to process() for consumption
        return $this->process();
    }

    //** process csv file and push array to buildJSON() */
    protected function process() {

        $file = fopen("storage/app/address_import2.csv", "r");
        $data = fgetcsv($file, 1000, ",");
        $data = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data);

        $array = [];
        while (($row = fgetcsv($file, 500, ",")) !== false) {
            $array[] = array_combine($data, $row);

        }       
        fclose($file);
        $array = $this->featuresArray($array);


        return $array;

    }

    protected function featuresArray($data) {

        $apiName = 'features';
        $requestMethod = 'POST';

        /** build array for the create features API */
        //instantiate data array 

        $bodyType = 'Feature';
        $geoType = 'Point';
        $x_vetro = array("layer_id"=> 26, "plan_id"=>8);
        $build = 'Yes';
        $dropType = 'Underground';
        $buildingType = 'Residential';

        $partials = new Helpers();
        $partials = $partials->buildJSON($x_vetro['layer_id']);
        

        foreach ($data as $key => &$value) {
            $records[$key]['type'] = $bodyType;
            $records[$key]['x-vetro'] = $x_vetro;
            $records[$key]['geometry']['type'] = $geoType;
            $records[$key]['geometry']['coordinates'] = array((float) $value['longitude'], (float) $value['latitude']); 
            $records[$key]['properties']['ID'] = $value['house_id'];
            $records[$key]['properties']['Build'] = $build;
            $records[$key]['properties']['Address'] = $value['address'];
            $records[$key]['properties']['Drop Type'] = $dropType;
            $records[$key]['properties']['Building Type'] = $buildingType;
            $this->data['features'] = $records; //array_values($data);
            $newData = json_encode($this->data , true);

        } 
        $curl = new Vetro();
        $curl = $curl->curlAPI($apiName, $requestMethod, $newData);
        
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

    public function search() {

        return Search::search([8], "ID=150973");
    }
	
    public function polygonAddress() {

        $address['address'] = '603 Pine Hollow Dr Gonzales, LA 70737';
        $apiName = "features/polygon/intersection/address";

        $data = json_encode($address);

        return Vetro::curlAPI($apiName, $this->requestMethod, $data);
    }
 
    


}




