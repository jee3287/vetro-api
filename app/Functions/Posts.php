<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use Log;

class Posts {

    protected $data;
    /*
    public function __construct(
        protected $file
    ) {

    }
    */

    public function features() {
        
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
        $array = $this->buildJSON($array);


        return $array;

    }

    protected function buildJSON($data) {

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
	
 
    


}




