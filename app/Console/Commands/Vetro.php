<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use App\Functions\Gets;
use App\Functions\Posts;
use App\Functions\Patches;
use App\Http\Controllers\Controller;


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
                            {requestMethod : the request method};
                            {vetro_ids? : the vetro id (optional)};
                            {args?* : the arguments (optional)}';

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
            $vetro_ids = $this->argument('vetro_ids');
            $args = $this->argument('args');
            
            $api = new Controller($apiName, $requestMethod, $vetro_ids, $args); 
            
            $postResult = $api->router($apiName, $requestMethod, $vetro_ids, $args);

            return $postResult;
            /*
            //** split it up in post vs get vs other requests 
            if ($requestMethod == 'POST') {
                $response = $this->postRequests($apiName, $args);
            } elseif ($requestMethod == 'GET') {
                $response = $this->getRequests($apiName, $args);
            } elseif ($requestMethod == 'PATCH') {
                $response = $this->patchRequests($apiName, $args);
            } else {
                dd("Please enter a valid request type");
            } */

        }
    }


    //** handle post requests redirection */
    public function request($apiName, $requestMethod, $args) {

        $api = new Posts; //dd($api);
        $postResult = $api->{$apiName}($args);

        return $postResult;
    }

    public static function curlAPI($apiName, $requestMethod, $apiInput = '') {

        $curl = curl_init();
        $base_path = 'https://fibermap.vetro.io/v2/';
        $apiToken = getenv('VETRO_API_TOKEN');
        //$apiInput = json_encode($apiInput); dd($apiInput);
        //dd($apiInput);
        $vetroId = null; // Initialize the variable to hold the vetro ID
        //** use curl to set up endpoint */
        try {
            curl_setopt_array($curl, 
            array(
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
        } catch(Exception $e) {
            return [
                'status' => 'error', 
                'message' => $e->getMessage()
            ];
        }

        $response = curl_exec($curl); //dd($response);
        $response = json_decode($response, true);

        curl_close($curl);
        //dd($response);
        
    
        return $response;
    
    }

    public static function curlAPIdelete($apiName, $requestMethod, $apiInput = '') {

        $curl = curl_init();
        $base_path = 'https://fibermap.vetro.io/v2/';
        $apiToken = getenv('VETRO_API_TOKEN');

        $vetroId = null; // Initialize the variable to hold the vetro ID
        //** use curl to set up endpoint */
        //is_array($curl_opt)
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
        //dd($curl);
        $response = curl_exec($curl); //dd($response);
        $response = json_decode($response, true);

        curl_close($curl);
        //dd($response);
        
    
        return $response;
    
    }


    //** handle post requests redirection */
    public function postRequests($apiName, $args) {

        $api = new Posts; //dd($api);
        $postResult = $api->{$apiName}($args);

        return $postResult;
    }

    //** handle get requests redirection */
    public function getRequests($apiName, $args) {

        $api = new Gets; //dd($api);
        $getResult = $api->{$apiName}($args);

        return $getResult;
    }

    //** handle patches requests redirection */
    public function patchRequests($apiName, $args) {

        $api = new Patches; //dd($api);
        $getResult = $api->{$apiName}();

        return $getResult;
    }
    

}
