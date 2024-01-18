<?php

namespace app\Helpers;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Search;
use App\Functions\Gets;
use App\Functions\Posts;
use Log;


class ConnectedFeatures {


    public function getConnectedFeatures($data) {  
        
        $edges = [];
        $vetroArray = [];
        $x_vetro = [];

        //get the vetroID being moved/changed with coincident line 
        if (array_key_exists('x-vetro', $data)) { 
            $x_vetro['main']['vetro_id'] = $data['x-vetro'];
        } //dd($x_vetro);

        if (array_key_exists('result', $data)) { //dd($data);
            if(array_key_exists('nodes', $data['result'])) { 
                foreach ($data['result']['nodes'] as $resultKey => &$result) { //dd($result);

                    $vetroArray[$resultKey]['id'] = $result['id'];
                    $vetroArray[$resultKey]['plan_id'] = $result['data']['plan_id'];
                    $vetroArray[$resultKey]['feature_type'] = $result['data']['feature_type'];
                    $vetroArray[$resultKey]['vetro_id'] = $result['data']['vetro_id'];
                    
                    if ($vetroArray[$resultKey]['vetro_id'] == $x_vetro['main']['vetro_id'])
                    {
                        $x_vetro['main']['id'] = $result['id'];
                    }
                } 
            } //dd($vetroArray);

            $connectedFeats = [];

            if(array_key_exists('edges', $data['result'])) {
                foreach ($data['result']['edges'] as $edgesKey => $edge) { 
                    $edges[$edgesKey]['from_id'] = $edge['from_id'];
                    $edges[$edgesKey]['to_id'] = $edge['to_id'];
                    $connectedFeats = [];
                    foreach($vetroArray as $vetroKey => $vetroValue) {
                        if($vetroValue['id'] == $edge['from_id']) {
                            $connectedFeats[$vetroKey]['x_vetro'] = $vetroValue['vetro_id'];
                            $connectedFeats[$vetroKey]['plan_id'] = $vetroValue['plan_id'];
                            $connectedFeats[$vetroKey]['feature_type'] = $vetroValue['feature_type'];
                            $connectedFeats[$vetroKey]['line_side'] = 'start';
                        } else {
                            $connectedFeats[$vetroKey]['x_vetro'] = $vetroValue['vetro_id'];
                            $connectedFeats[$vetroKey]['plan_id'] = $vetroValue['plan_id'];
                            $connectedFeats[$vetroKey]['feature_type'] = $vetroValue['feature_type'];
                            $connectedFeats[$vetroKey]['line_side'] = 'end';
                        }
                    }   dd($connectedFeats);
                    
                } 

                $connectedVetros = [];

                foreach ($edges as $key => $value) { dd($edges);
                    // if (in_array($x_vetro['main']['id'], $value)) 
                    // { 
                        // $connectedFeats[] = $value; 
                        // $connectedFeatsKeys = array_keys($connectedFeats); //dd($connectedFeats);

                        for ($i = 0; $i < count($connectedFeatsKeys); $i++) {
                            foreach ($vetroArray as $itemKey => $item) {
                                // dd($item['id']);
                                // dd($connectedFeats[$i]);
                                // if( $item['id'] == $connectedFeats[$i]['from_id'] || $item['id'] == $connectedFeats[$i]['to_id']) 
                                // { 
                                if ($item['id'] == $connectedFeats[$i]['from_id']) {
                                    $connectedVetros[$itemKey]['x-vetro'] = $item;
                                    $connectedVetros[$itemKey]['line_side'] = $value;
                                } else {
                                    $connectedVetros[$itemKey]['x-vetro'] = $item;
                                    $connectedVetros[$itemKey]['line_side'] = $value;
                                }
                                    
                                //}
                            } 
                        }
                    //}
                }
            }
            //$connectedVetros['x-vetro']['main_vetro'] = $x_vetro['main']['vetro_id'];
        }
        $connectedVetros = array_values($connectedVetros); dd($connectedVetros);
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



   


