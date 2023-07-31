<?php

namespace app\Functions;

use Illuminate\Support\Facades\Storage;
use App\Console\Commands\Vetro;
use App\Functions\Gets;
use Log;


class Search {

    protected $requestMethodPost = 'POST';
    protected $data;

    public static function search(array $active_plan_ids, string $query) {

        $requestMethod = 'POST';
        //initialize data array and convert to json 
        $data['active_plan_ids'] = $active_plan_ids;
        $data['query'] = $query;
        $data = json_encode($data);

        $apiName = 'search';

        Vetro::curlAPI($apiName, $requestMethod, $data);
        
    }

}