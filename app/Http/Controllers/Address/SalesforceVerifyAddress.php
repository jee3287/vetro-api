<?php

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


class SalesforceVerifyAddress extends Controller
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
        dd($addressQuery);
        $addressQuery = (array) $addressQuery;
        
        foreach ($addressQuery as $addressValue) {
            $addressValue = (array) $addressValue; 
            if ($addressValue['addr_cnt'] > 0) {
                Log::info("Address Exists! Creating json response.");
                $response['status'] = 'success';
                $response['validationResult'] = 'valid';
            } elseif ($addressValue['addr_cnt'] > 0) {
                Log::info("Address Exists and is serviceable! Creating json response.");
                $response['status'] = 'Success';
                $response['technology'] = $count['serviceability'];
                $response['serviceability status'] = $count['serviceability_type'];
                $response['geographic region'] = $count['product_region'];
            }
            
            dd(json_encode($response));
        }

    }

    protected function addressTranslate($value) {

            switch ($value) {
                case 'United States': 
                    $v = 'USA'; 
                    break;
                case 'Alabama': 
                    $v = 'AL';
                    break;
                case 'Alaska': 
                    $v = 'AK';
                    break;
                case 'Arizona': 
                    $v = 'AZ';
                    break;
                case 'Arkansas': 
                    $v = 'AR';
                    break;
                case 'California': 
                    $v = 'CA';
                    break;
                case 'Colorado': 
                    $v = 'CO';
                    break;
                case 'Connecticut': 
                    $v = 'CT';
                    break;
                case 'Delaware': 
                    $v = 'DE';
                    break;
                case 'District of Columbia': 
                    $v = 'DC';
                    break;
                case 'Florida': 
                    $v = 'FL';
                    break;
                case 'Georgia': 
                    $v = 'GA';
                    break;
                case 'Hawaii': 
                    $v = 'HI';
                    break;
                case 'Idaho': 
                    $v = 'ID';
                    break;
                case 'Illinois': 
                    $v = 'IL';
                    break;
                case 'Indiana': 
                    $v = 'IN';
                    break;
                case 'Iowa': 
                    $v = 'IA';
                    break;
                case 'Kansas': 
                    $v = 'KS';
                    break;
                case 'Kentucky': 
                    $v = 'KY';
                    break;
                case 'Louisiana': 
                    $v = 'LA';
                    break;
                case 'Maine': 
                    $v = 'ME';
                    break;
                case 'Maryland': 
                    $v = 'MD';
                    break;
                case 'Massachusetts': 
                    $v = 'MA';
                    break;
                case 'Michigan': 
                    $v = 'MI';
                    break;
                case 'Minnesota': 
                    $v = 'MN';
                    break;
                case 'Mississippi': 
                    $v = 'MS';
                    break;
                case 'Missouri': 
                    $v = 'MO';
                    break;
                case 'Montana': 
                    $v = 'MT';
                    break;
                case 'Nebraska': 
                    $v = 'NE';
                    break;
                case 'Nevada': 
                    $v = 'NV';
                    break;
                case 'New Hampshire': 
                    $v = 'NH';
                    break;
                case 'New Jersey': 
                    $v = 'NJ';
                    break;
                case 'New Mexico': 
                    $v = 'NM';
                    break;
                case 'New York': 
                    $v = 'NY';
                    break;
                case 'North Carolina': 
                    $v = 'NC';
                    break;
                case 'North Dakota': 
                    $v = 'ND';
                    break;
                case 'Ohio': 
                    $v = 'OH';
                    break;
                case 'Oklahoma': 
                    $v = 'OK';
                    break;
                case 'Oregon': 
                    $v = 'OR';
                    break;
                case 'Pennsylvania': 
                    $v = 'PA';
                    break;
                case 'Rhode Island': 
                    $v = 'RI';
                    break;
                case 'South Carolina': 
                    $v = 'SC';
                    break;
                case 'South Dakota': 
                    $v = 'SD';
                    break;
                case 'Tennessee': 
                    $v = 'TN';
                    break;
                case 'Texas': 
                    $v = 'TX';
                    break;
                case 'Utah': 
                    $v = 'UT';
                    break;
                case 'Vermont': 
                    $v = 'VT';
                    break;
                case 'Virginia': 
                    $v = 'VA';
                    break;
                case 'Washington': 
                    $v = 'WA';
                    break;
                case 'West Virginia': 
                    $v = 'WV';
                    break;
                case 'Wisconsin': 
                    $v = 'WI';
                    break;
                case 'Wyoming': 
                    $v = 'WY';
                    break;
    
            }
            return $v;  
        }
    
}
