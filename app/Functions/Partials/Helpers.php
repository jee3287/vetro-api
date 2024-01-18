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
        $exchange = '';
        //$dropType = 'Underground';
        //$buildingType = 'Residential';
        $records = [];
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
            $records[$key]['properties']['Subname'] = $value['subdivisions'];
            //$records[$key]['properties']['Drop Type'] = $dropType;
            $records[$key]['properties']['Building Type'] = ucfirst(strtolower($value['building_type']));
            if(empty($value['drop_type'])) { 
                $records[$key]['properties']['Drop Type'] = 'Combo';
            } else { 
                $records[$key]['properties']['Drop Type'] = ucfirst(strtolower($value['drop_type']));
            };
            //$records[$key]['properties']['Drop Type'] = 
            $records[$key]['properties']['State'] = 'LA';
            $records[$key]['properties']['Network Type'] = $value['network_type'];
            //$records[$key]['properties']['Subname'] = 'Ronda Place';
            $records[$key]['properties']['County'] = $value['county']; 
            $records[$key]['properties']['COID'] = $value['coid']; 
            $records[$key]['properties']['COID Desc'] = $value['coid_desc']; 
            $records[$key]['properties']['Exchange'] = $exchange;
            $records[$key]['properties']['Default Terminal'] = trim($value['default_trm']);
            $this->data['features'] = $records; //array_values($data);
            $newData = json_encode($this->data);
            //dd($newData);
            if( $records[$key]['properties']['Drop Type'] <> 'Aerial' && $records[$key]['properties']['Drop Type'] <> 'Combo' && $records[$key]['properties']['Drop Type'] <> 'Buried' && 
            $records[$key]['properties']['Drop Type'] <> 'null'  && $records[$key]['properties']['Drop Type'] <> 'Cable'   ) {
                dd($records[$key]);
            }

        } //dd($newData);

        return $newData; //$this->data['features'];
    }

    public function vetro_id($data) {
        //initialize the vetroId array
        $vetroArray = []; 
        //$getData = new Posts;
        //$getData = $getData->features('data');
        if (array_key_exists('result', $data)) { 
            foreach ($data['result'] as $resultKey => &$result) { 
                if (array_key_exists('x-vetro', $result) && array_key_exists('ID',$result['properties'])) {
                    //$vetroString[$resultKey]['vetro_id'] = $result['x-vetro']['vetro_id'];
                    $vetroArray[$resultKey]['properties']['x-vetro'] = $result['x-vetro']['vetro_id'];
                    $vetroArray[$resultKey]['properties']['ID'] = $result['properties']['ID'];
                    $vetroArray[$resultKey]['properties']['plan_id'] = $result['x-vetro']['plan_id'];
                    if (array_key_exists('coordinates', $result['properties'])) {
                        $vetroArray[$resultKey]['geometry']['coordinates'] = $result['geometry']['coordinates'];
                    }
                    if (array_key_exists('House ID', $result['properties'])) {
                        $vetroArray[$resultKey]['properties']['House ID'] = $result['properties']['House ID'];
                    }
                    if (array_key_exists('Census GEOID20', $result['properties'])) {
                        $vetroArray[$resultKey]['properties']['Census GEOID20'] = $result['properties']['Census GEOID20'];
                    }
                    $vetroArray[$resultKey]['properties']['last_edited_time'] = date("Y-m-d H:i:s", strtotime($result['x-vetro']['last_edited_time']));
                } //dd($vetroArray);
            }
        } 

        return  $vetroArray;
    }


    //** process a csv file and push array to buildJSON() */
    public function processCSV($action) {

        $file = fopen("storage/app/address_import11.csv", "r");
        $data = fgetcsv($file, 1000, ",");
        $data = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data);

        $array = [];
        while (($row = fgetcsv($file, 500, ",")) !== false) {
            $array[] = array_combine($data, $row);

        }       
        fclose($file); 
        //$array = $this->featuresArray($array, $action);

        return $array;

    }

    public function vetroid_from_node($data) { 

        $edges = [];
        $vetroArray = [];
        $x_vetro = [];

        //get the vetroID being moved/changed with coincident line 
        if (array_key_exists('x-vetro', $data[0])) { 
            $x_vetro['main']['vetro_id'] = $data[0]['x-vetro'];
        } //dd($x_vetro);

        if (array_key_exists('result', $data[0])) { 
            if(array_key_exists('nodes', $data[0]['result'])) {
                foreach ($data[0]['result']['nodes'] as $resultKey => &$result) { 

                    $vetroArray[$resultKey]['id'] = $result['id'];
                    $vetroArray[$resultKey]['vetro_id'] = $result['data']['vetro_id'];
                    
                    if ($vetroArray[$resultKey]['vetro_id'] == $x_vetro['main']['vetro_id'])
                    {
                        $x_vetro['main']['id'] = $result['id'];
                    }
                } 
            } //dd($x_vetro);

            $connectedFeats = [];

            if(array_key_exists('edges', $data[0]['result'])) {
                foreach ($data[0]['result']['edges'] as $edgesKey => $edge)
                { 
                    $edges[$edgesKey]['from_id'] = $edge['from_id'];
                    $edges[$edgesKey]['to_id'] = $edge['to_id'];
                } 

                $connectedVetros = [];

                foreach ($edges as $key => $value) { 
                    if (in_array($x_vetro['main']['id'], $value)) 
                    { 
                        $connectedFeats[] = $value; 
                        $connectedFeatsKeys = array_keys($connectedFeats);

                        for ($i = 0; $i < count($connectedFeatsKeys); $i++) {
                            foreach ($vetroArray as $itemKey => $item) { 
                                if( $item['id'] == $connectedFeats[$i]['from_id'] || $item['id'] == $connectedFeats[$i]['to_id']) 
                                { 
                                    $connectedVetros[$itemKey]['x-vetro'] = $item['vetro_id'];
                                }
                            } 
                        }
                    }
                }
            }
            $connectedVetros['x-vetro']['main_vetro'] = $x_vetro['main']['vetro_id'];
        }
        $connectedVetros = array_values($connectedVetros); //dd($connectedVetros);
        return $connectedVetros;
    }


    public function vetro_layer($request, $data, $vetroIds) {
        //initialize the vetroId array
        $vetroArray['features'] = [];
        $layerTypes = []; 
        $mainVetroId = '';
        $args = [-90.926783719384,30.234575039701]; 
        foreach ($vetroIds as $mainKey => $mainValue) { 
            if (array_key_exists('main_vetro', $mainValue)) {
                $mainVetroId = $mainValue['main_vetro'];
            }
        } 
        $props = new stdClass();
        if (array_key_exists('result', $data)) {
            foreach ($data['result'] as $resultKey => $result) { 
                if ($result['x-vetro']['vetro_id'] == $mainVetroId) {
                    $vetroArray['features'][$resultKey]['x-vetro']['vetro_id'] = $result['x-vetro']['vetro_id'];
                    $vetroArray['features'][$resultKey]['properties'] = $props;
                    $vetroArray['features'][$resultKey]['geometry']['type'] = $result['geometry']['type'];
                    $vetroArray['features'][$resultKey]['geometry']['coordinates'] = $args; //change the coordinates here
                    $originalCoords = $result['geometry']['coordinates']; //grab old coordinates and use later
                } else {
                    // add new coordinates to connected points
                    $connectedCoords = [];
                    foreach($result['geometry']['coordinates'] as $coordKey => $coord) { 
                        if ($coord == $originalCoords) {
                            $vetroArray['features'][$resultKey]['x-vetro']['vetro_id'] = $result['x-vetro']['vetro_id'];
                            $vetroArray['features'][$resultKey]['properties'] = $props;
                            $vetroArray['features'][$resultKey]['geometry']['type'] = $result['geometry']['type'];
                            $vetroArray['features'][$resultKey]['geometry']['coordinates'][$coordKey] = $args; 
 
                            
                        } else {
                            $vetroArray['features'][$resultKey]['x-vetro']['vetro_id'] = $result['x-vetro']['vetro_id'];
                            $vetroArray['features'][$resultKey]['properties'] = $props;
                            $vetroArray['features'][$resultKey]['geometry']['type'] = $result['geometry']['type'];
                            $vetroArray['features'][$resultKey]['geometry']['coordinates'][$coordKey] = $coord; 
                        }
                    } 
                }
               
            }  $vetroArray = json_encode($vetroArray);
        } dd($vetroArray);

        return  $vetroArray;
    }


}



   


