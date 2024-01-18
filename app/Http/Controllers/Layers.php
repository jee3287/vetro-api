<?php

namespace App\Http\Controllers;

use app\Console\Commands\Vetro;
use App\Functions\Partials\Helpers;


class Layers {

    public function layers($data, $vetroIds) {

        $request = Vetro::curlAPI('layers', 'GET');

        $vetroData = new Helpers($request);
        $vetroData = $vetroData->vetro_layer($request, $data, $vetroIds);
        
        return $vetroData;

    }
}