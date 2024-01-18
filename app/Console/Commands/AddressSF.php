<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Address\SalesforceVerifyAddress;
use App\Http\Controllers\Address\Serviceability;


require 'vendor/autoload.php';


class AddressSF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:addressVerification
                            {inputJSON : the input file from Boomi};';

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
        $this->_logHeader = "[make:addressVerification]";
    }
    public function handle()
    {
        
            //$inputJSON = $this->argument('inputJSON');
            if (Storage::fileExists($this->argument('inputJSON'))) {
                $inputJSON = Storage::get($this->argument('inputJSON'));
                $addressQuery = new SalesforceVerifyAddress(inputJSON: $inputJSON);
                $addressQuery = $addressQuery->validateAddress();
                // $addressQuery = new Serviceability(inputJSON: $inputJSON);  //test additional "serviceability endpoint in this place for now ///
                // $addressQuery = $addressQuery->serviceable();
                dd($addresses);
            

        }
    }

}
