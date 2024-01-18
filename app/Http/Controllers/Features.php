<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Search;
use App\Functions\Gets;
use App\Functions\Partials\Helpers;
use App\Http\Controllers\Layers;
use App\Http\Controllers\Controller;
use Log;


class Features  {

    protected $apiPrefix = 'features/';

    
    public function features($apiName, $requestMethod, $args) {
        
        if ($apiName == 'features' && $requestMethod == 'PATCH') {
            return $this->featurespatch($apiName, $requestMethod, $args);
        } elseif ($apiName == 'features' && $requestMethod == 'POST') { 
            return $this->featuresPOST($apiName, $requestMethod, $args);
        } 
        $this->featuresquery($apiName, $requestMethod, $args);
        return $this->$apiName($apiName, $requestMethod, $args);

    }
    
    protected function featuresquery($apiName, $requestMethod, $args = '') {

        
        $ID = [754,2422,2462,7158,10889,13021,15859,17160,23542,24237,30733,31239,31347,31457,34853,36487,43082,43088,43158,43171,45624,45865,46496,48844,51808,52487,55318,55777,55792,55921,56828,60040,60427,63371,149914,274829,280343];
        //$ID = 754;
        //$address = str_replace (' ', '%20','603 Pine Hollow Dr Gonzales, LA');
        //dd($ID);
        $x_vetroArray = [];
        foreach ($ID as $houseKey => $houseid) {
            $getPlans = Vetro::curlAPI('plans', 'GET'); 
            $plans = [];
            foreach ($getPlans['result']['plans'] as $planKey => $planValue) {
                if(!preg_match("/demo/i", $planValue['label'])) {
                    $plans[] = $planValue['id'];
                }
            }
            $plans = implode(",",$plans);
            $layerID = 26;
            $helpers = new Helpers(); 
            $vetro_ids = [];
            // foreach($data as $key => $value) { //dd($data);
            //     $ID = $value['id'];
                $apiName = "features/query?offset=0&limit=50000&order=ID:desc&layer_ids={$layerID}&plan_ids={$plans}"; //plan_ids={$planID}&layer_ids={$layerID}&
                $request = Vetro::curlAPI($apiName, $requestMethod); 
                $response = new Helpers();
                $vetro_ids = $response->vetro_id($request); 
                $x_vetroArray[] = $vetro_ids;
            //} 
        }
        
        // function below used to associate locations with exchange polygons
        //$this->polygonIntersection($x_vetroArray); exit;
       
        $vetro_ids_imploded = implode(',',$vetro_ids);
        file_put_contents('storage/app/existing_v_locations.csv', $vetro_ids_imploded);
        $vetro_ids = json_encode($vetro_ids); dd($vetro_ids);
        // $apiName2 = "features/delete";
        // $request2 = Vetro::curlAPIdelete($apiName2, 'POST', $vetro_ids);
        //$apiName = "features/query?offset=0&limit=500&plan_ids={$planID}&layer_ids={$layerID}&filter=ID={$ID}"; //plan_ids={$planID}&layer_ids={$layerID}&
        //dd($apiName);
        //$request = Vetro::curlAPI($apiName, $requestMethod);

        return $request; 
       
    }

    protected function polygonIntersection($array) {
        $requestMethod = 'POST';
        $apiName = 'features/polygon/intersection/position';
        
        $body2 = [];
        foreach ($array as $arrayKey => $arrayValue) { //dd($array);
            $body = [];
            $body['position'] = $arrayValue['geometry']['coordinates'];
            $body['polygon_layer_ids'] = [723];
            $body = json_encode($body); //dd($body);

            $request = Vetro::curlAPI($apiName, $requestMethod, $body); 
            $vetroString = []; 
            //$getData = new Posts;
            //$getData = $getData->features('data');
            if (array_key_exists('result', $request)) { //dd($request);
                foreach ($request['result'] as $resultKey => &$result) { 
                    if (array_key_exists('properties', $result)) {
                        $body2['locations'][$arrayKey]['properties'] = $arrayValue['properties'];
                        $body2['locations'][$arrayKey]['productRegion'][] = $result['properties']['PRODUCT REGION'];
                    }
                } 
            }
        }   dd(json_encode($body2, true));
    }

    protected function featurespatch($apiName, $requestMethod, $args) {

        //update features that already exists
        //needs body that must include vetro_id and then pass properties to update features
        $file = Storage::disk('local')->get('test.json');
        $file = array(json_decode($file, true)); dd($file);

        $vetro_ids = new Helpers($file);
        $vetro_ids = $vetro_ids->vetroid_from_node($file); 
        $filetest = Storage::disk('local')->get('test copy.json');
        $this->buildMoveFeaturesArray($vetro_ids);
        dd(json_decode($filetest, true));
        dd($vetro_ids);


    }

    protected function buildMoveFeaturesArray($vetroIds): array {

        //iniitialize features to be updated array
        $newFeatures['features'] = [];
        $vetroIdString = [];
        foreach ($vetroIds as $vetro => $vid) {
            if (array_key_exists('x-vetro', $vid)) {
                $vetroIdString[] = $vid['x-vetro'];
            }
            
            //$vetroIdString = $vid['x-vetro'];
            
        } //dd($vetroIdString);
        $vetroIdString = implode(",", $vetroIdString); 
        $vetroFeatureArray = Vetro::curlAPI("features/{$vetroIdString}", 'GET');
        //dd($vetroFeatureArray);

        $vetroLayerById = new Layers($vetroFeatureArray);
        $vetroLayerById = $vetroLayerById->layers($vetroFeatureArray, $vetroIds);
        //dd($vetroLayerById);
        $test = Vetro::curlAPI('features', 'PATCH', $vetroLayerById);
        dd($test);
        
        //return 


    }

    public function featuresPOST($apiName, $requestMethod, $args) {

        //CREATE FEATURES endpoint by name, hand off to process() 
        $helpers = new Helpers();
        $data = $helpers->processCSV($requestMethod);
        //dd($data);
        $apiName = 'features';

        /** build array for the create features API */
        $bodyType = 'Feature';
        $geoType = 'Point';
        $x_vetro = array("layer_id"=> 26, "plan_id"=>36);
        
        $buildJSON = $helpers->layer($data, $bodyType, $geoType, $x_vetro);

        $curl = new Vetro();
        $curl = $curl->curlAPI($apiName, $requestMethod, $buildJSON);
        
        return $buildJSON;


    }

    public function deleteFeatures($apiName, $requestMethod, $args) {

        //delete what has been created recently :)

        //get the vetro-ids

    }
    


}