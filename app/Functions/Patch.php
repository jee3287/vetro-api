<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Search;
use App\Functions\Gets;
use Log;


class Patch {

    protected $data;

    public function features() {

        $apiName = 'features';
        $requestMethod = 'PATCH';

        $body['type'] = 'Feature';
        //$body['geometry'] = array('type' => 'Point', 'coordinates' => array(-90.92679553, 30.23473042));
        $body['x-vetro']['vetro_id'] = 'db5934fa-2aa9-4e7a-bfa4-614881f87e38'; // 46ce7861-b8f3-425f-8d37-6552148a96f2
        //$body['x-vetro']['layer_id'] = 26;
        //$body['x-vetro']['plan_id'] = 6;
        //$body['properties']['Name']= 'Joe\'s House';
        //$body['properties']['City']= 'Gonzales';
        //$body['properties']['Network Type']= 'PON';
        $body['properties']['Number of Ports']= 4;
        // $body['x-vetro']['child']['x-vetro']['layer_id'] = 39;
        // $body['x-vetro']['child']['x-vetro']['plan_id'] = 6;
        // $body['x-vetro']['child']['x-vetro']['parent_vetro_id'] = '46ce7861-b8f3-425f-8d37-6552148a96f2';

        $this->data['features'][] = $body;
        $result = json_encode($this->data);

        $curl = new Vetro();
        $curl = $curl->curlAPI($apiName, $requestMethod, $result);



    }


}