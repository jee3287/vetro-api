<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use App\Functions\Get;
use App\Functions\Post;
use App\Functions\Patch;


require 'vendor/autoload.php';


class Vetro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:test-vetro-api
                            {apiName : the api endpoint name}; 
                            {requestMethod : the request method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(
    ) {
        parent::__construct();
        $this->_logHeader = "[make:test-vetro-api]";
    }
    public function handle()
    {
        {   
            $apiName = $this->argument('apiName');
            $requestMethod = $this->argument('requestMethod');

            //** split it up in post vs get vs other requests */
            if ($requestMethod == 'POST') {
                $response = $this->postRequests($apiName, $requestMethod);
            } elseif ($requestMethod == 'GET') {
                $response = $this->getRequests($apiName, $requestMethod);
            } elseif ($requestMethod == 'PATCH') {
                $response = $this->patchRequests($apiName, $requestMethod);
            } else {
                dd("Please enter a valid request type");
            }
            /*
            foreach ($response as $key => $value) {
                if (array_key_exists('resultXYXXX', $response)) {
                    if ($key == 'result') {
                        foreach ($value as $k => $v) { 
                            foreach ($v as $a => $b) { dd($k);
                                if($v == 'layers') { 
                                    dd($b);
                                }
                                if ($a =='x-vetro'){  
                                    $vetroId = $b['vetro_id'];
                                }
                            }
                        }
                    }
                }
            } */
            //return Command::SUCCESS;
        }
    }

    public static function curlAPI($apiName, $requestMethod, $apiInput = '') {

        $curl = curl_init();
        $base_path = 'https://fibermap.vetro.io/v2/';
        $apiToken = getenv('VETRO_API_TOKEN');

        $vetroId = null; // Initialize the variable to hold the vetro ID

        //** use curl to set up endpoint */
        curl_setopt_array($curl, array(
        CURLOPT_URL => str_replace (' ', '%20',"{$base_path}{$apiName}"),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $requestMethod, 
        CURLOPT_POSTFIELDS => $apiInput,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            "token: {$apiToken}")
        ));
    
        $response = curl_exec($curl); //dd($response);
        $response = json_decode($response, true);
        
        curl_close($curl);
        //dd($response);
    
        return $response;
    
    }

    //** handle post requests redirection */
    public function postRequests($apiName) {

        $api = new Post; //dd($api);
        $postResult = $api->{$apiName}();

        return $postResult;
    }

    //** handle get requests redirection */
    public function getRequests($apiName) {

        $api = new Get; //dd($api);
        $getResult = $api->{$apiName}();

        return $getResult;
    }

    //** handle patches requests redirection */
    public function patchRequests($apiName) {

        $api = new Patch; //dd($api);
        $getResult = $api->{$apiName}();

        return $getResult;
    }
    

}
