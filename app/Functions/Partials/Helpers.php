<?php

namespace app\Functions\Partials;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Search;
use App\Functions\Gets;
use App\Functions\Posts;
use Log;




class Helpers {

    public function layer($data, $bodyType, $geoType, $x_vetro) {
        //dd($x_vetro['layer_id']);
        if ($x_vetro['layer_id'] == 26) { //service location
            return $this->svcLocation($data, $bodyType, $geoType,$x_vetro);
        }
        if ($x_vetro['layer_id'] == 43) {
            foreach ($data as $key => $value) {
                dd($value);
            }
        }

    }

    //** create a service location *** */
    protected function svcLocation($data, $bodyType, $geoType, $x_vetro) {

        $build = 'Yes';
        $exchange = 'GLVZ';
        //$dropType = 'Underground';
        //$buildingType = 'Residential';
        
        foreach ($data as $key => &$value) {
            $records[$key]['type'] = $bodyType;
            $records[$key]['x-vetro'] = $x_vetro;
            $records[$key]['geometry']['type'] = $geoType;
            $records[$key]['geometry']['coordinates'] = array((float) $value['longitude'], (float) $value['latitude']); 
            $records[$key]['properties']['ID'] = $value['id'];
            $records[$key]['properties']['House ID'] = $value['house_id'];
            $records[$key]['properties']['Build'] = $build;
            $records[$key]['properties']['Address'] = $value['full_address'];
            $records[$key]['properties']['Street Address'] = $value['address'];
            $records[$key]['properties']['City'] = $value['city'];
            $records[$key]['properties']['Zip Code'] = $value['zip'];
            $records[$key]['properties']['Subname'] = $value['subdivsions'];
            //$records[$key]['properties']['Drop Type'] = $dropType;
            $records[$key]['properties']['Building Type'] = $value['building_type'];
            $records[$key]['properties']['Drop Type'] = $value['drop_type'];
            $records[$key]['properties']['State'] = 'LA';
            $records[$key]['properties']['Network Type'] = 'PON';
            //$records[$key]['properties']['Subname'] = 'Ronda Place';
            $records[$key]['properties']['County'] = 'Ascension';
            $records[$key]['properties']['COID'] = $value['coid']; 
            $records[$key]['properties']['COID Desc'] = $value['coid_desc']; 
            $records[$key]['properties']['Exchange'] = $exchange;
            $records[$key]['properties']['Default Terminal'] = trim($value['default_trm']);
            $this->data['features'] = $records; //array_values($data);
            $newData = json_encode($this->data , true);
            //dd($newData);

        } 

        return $newData;
    }

    public function vetro_id($data) {
        //initialize the vetroId array
        $vetroArray = [];
        $getData = new Posts;
        $getData = $getData->features('data');
        if (array_key_exists('result', $data)) { 
            foreach ($data['result'] as $resultKey => &$result) {
                if (array_key_exists('x-vetro', $result)) {
                    $vetroArray[$resultKey]['x-vetro'][] = $result['x-vetro']['vetro_id'];
                    $vetroArray[$resultKey]['properties'][] = $result['properties'];
                   
                }
            } 

        } dd(json_decode($getData));
        return  $vetroArray;
    }

   

}