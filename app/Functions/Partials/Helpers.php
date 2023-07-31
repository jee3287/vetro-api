<?php

namespace app\Functions\Partials;

use Illuminate\Support\Facades\Storage;
use app\Console\Commands\Vetro;
use App\Functions\Search;
use App\Functions\Get;
use Log;




class Helpers {

    public function buildJSON($data) {

        $curl = new Vetro();
        $curl = $curl->curlAPI('layers', 'GET');
        //dd($curl);
        foreach ($curl['result']['layers'] as $key => $value) {
            if ($value['label'] == 'Service Location') {
                dd($value);
            }
        }
        //dd($curl);
    }

}