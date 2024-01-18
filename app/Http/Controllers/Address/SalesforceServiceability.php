<?php
//// dont use!!!!
namespace App\Http\Controllers\MasterAddress;

use App\Helpers\MasterAddress\AddressLookupHelperV2;
use App\Helpers\MasterAddress\AutoCompleteHelper;
use App\Helpers\MasterAddress\AutoCompleteHelperV2;
use App\Helpers\MasterAddress\CoordinateDataHelper;
use App\Helpers\MasterAddress\Errors\MasterAddressNotFound;
use App\Helpers\MasterAddress\Errors\MultipleServiceAddressesFound;
use App\Helpers\MasterAddress\Errors\NoServiceAvailable;
use App\Helpers\ServiceCheck\AddressServiceCheckHelper; //Partially updated. Not adding new addresses currently.
use App\Http\Controllers\Controller;
use App\Models\MasterAddressPG\MongoMasterGeoLink;
use App\Models\MasterAddressPG\OssBssServiceAddress;
use App\Models\MasterAddressV2\MasterAddressAutoCompleteMetric;
use App\Models\MasterAddressV2\MasterAddressLookupTypeMetric;
use App\Models\OneTouch\OneTouchApiLog;
use App\Models\ProductCatalog\BusinessZone;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Response;



class SalesforceServiceability extends Controller
{
    public function __construct($inputJSON) {

        $this->inputJSON = $inputJSON;

    }
    public function serviceable() {

       
        $addressData = new VerifyAddress($this->inputJSON);
        $addressData = $addressData->validateAddress(true); 
        
    }
    
}



////////////////////////////////////////////////////////////////


// <?php

// namespace App\Http\Controllers\Address;

// use Log;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\DB;
// use Illuminate\View\View;

class VerifyAddress extends Controller
{
    public function __construct($inputJSON) {

        $this->inputJSON = $inputJSON;

    }
    public function validateAddress($serviceabilityFlag = false) {

        $address = json_decode($this->inputJSON, true);
        $response = [];
        foreach ($address as $addrKey => $addrValue) {
            if (array_key_exists('streetName', $addrValue) && array_key_exists('state', $addrValue) && array_key_exists('city', $addrValue)) {
                $streetNumb = substr($addrValue['streetName'],0, strpos($addrValue['streetName'], ", "));
                $addressSteet = preg_replace("/[\s,]+/", " ", $addrValue['streetName']);
                $addressConcat = strtoupper($addressSteet . ', ' . $addrValue['city'] . ', ' . $this->addressTranslate($addrValue['state']) . ' ' . $addrValue['postcode']);
            } else {
                Log::info("Input JSON is missing all required fields!");
                $response['status'] = 'Failure';
                $response['validationResult'] = 'Invalid';
            }
        } 
        try {
            $addressQuery = DB::select("select count(r.full_address_no_unit) addr_cnt, s.serviceability, s.product_region, s.serviceability_type 
                                        from madb_v2.master_address_records r
                                        left join madb_v2.master_address_to_serviceability s on s.master_address_id = r.id
                                        where trim(upper(r.full_address_no_unit)) = '{$addressConcat}'
                                        and r.primary_number = '{$streetNumb}'
                                        group by s.serviceability, s.product_region, s.serviceability_type");
        } catch (\Exception $e) {
            $__errorMessage = "Caught exception connecting to Master Address DB: " . $e->getMessage();
            echo $__errorMessage;
        }
        $addressQuery = (array) $addressQuery;
        
        foreach ($addressQuery as $count) {
            $count = (array) $count; 
            $serviceabilityCheck = strlen($count['serviceability']);
            $product_regionCheck = strlen($count['product_region']);
            $serviceability_typeCheck = strlen($count['serviceability_type']);
            if ($count['addr_cnt'] > 0 && $serviceabilityFlag === false) {
                Log::info("Address Exists! Creating json response.");
                $response['status'] = 'Success';
                $response['validationResult'] = 'Valid';
            } elseif ($count['addr_cnt'] > 0 && $serviceabilityFlag === true) {
                if ($serviceabilityCheck <= 1) {
                    Log::info("This address does not contain a valid serviceablity value."); 
                }
                if ($product_regionCheck <= 1) {
                    Log::info("This address does not contain a valid product region value."); 
                }
                if ($serviceability_typeCheck <= 1) {
                    Log::info("This address does not contain a valid serviceablity status value."); 
                }
                Log::info("Address Exists and is serviceable! Creating json response.");
                $response['status'] = 'Success';
                $response['technology'] = $count['serviceability'];
                $response['serviceability status'] = $count['serviceability_type'];
                $response['geographic region'] = $count['product_region'];
            }
            
            dd(json_encode($response));
        }

    }

    
}
