<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\AuthApi;
use App\Models\Customer;
use App\Models\Services;
use App\Models\ServicesCategory;
use App\Models\ServicesInformation;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
   
    public function index(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = Services::where('services_status', 1)->get();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

    public function category(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $servicesId = $param->get('services_id'); 
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = ServicesCategory::where('status', 1)->where('services_id',$servicesId)->get();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

    public function information(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $servicesId = $param->get('services_id'); 
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = ServicesInformation::where('services_id',$servicesId)->orderBy('ordering','asc')->get();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

    public function menu(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = Services::where('services_status', 1)->get();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

    public function timeslot(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $servicesId = $param->get('services_id'); 
        $date = $param->get('date'); 
        $month = $param->get('month'); 
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dateMonth =[];
                $startDate = date("Y-m-d"); 
                $endDate = date("Y-m-d", strtotime(date("Y-m-d").' +1 month ')) ; 
                $date1 = new \DateTime($startDate);
                $date2 = new \DateTime($endDate);
                $interval = $date1->diff($date2);
                $daysNumber = $interval->days;
                $dateResp = [];
                $monthYearResp = [];
                $startFrom = 1;
                if($servicesId) {
                    $services = Services::where('id',$servicesId)->first();
                    if($services){
                        $startFrom=$services->services_min_day;
                    }
                }

                for( $i=$startFrom;$i<=$daysNumber;$i++){
                    $thedate= date("Y-m-d", strtotime(date("Y-m-d").' +'.$i.' day ')) ; 
                    $d = intval(date("d", strtotime($thedate))); 
                    $dateMonth[$d] = [];
                }
                for( $i=$startFrom;$i<=$daysNumber;$i++){
                    $thedate= date("Y-m-d", strtotime(date("Y-m-d").' +'.$i.' day ')) ; 
                    $dmy = date("Y-m-d", strtotime($thedate)); 
                    $d = intval(date("d", strtotime($thedate))); 
                    $ym = date("F'y", strtotime($thedate)); 
                    $dateMonth[$d][$dmy] = $ym;
                }
                for( $i=$startFrom;$i<=$daysNumber;$i++){
                    $thedate= date("Y-m-d", strtotime(date("Y-m-d").' +'.$i.' day ')) ; 
                    $d = intval(date("d", strtotime($thedate))); 
                    $d2 = date("d", strtotime($thedate)); 
                    $ym = date("F'y", strtotime($thedate)); 
                    $dateResp[$d] = $d2 ; 
                    $monthYearResp[$ym] = $ym ;
                } 
                if(isset($date)) {
                    if(isset($dateMonth[intval($date)])) {
                        $monthYearResp = [];
                        foreach($dateMonth[intval($date)] as $dmvs){
                            $monthYearResp[$dmvs] = $dmvs ;
                        }   
                    }   
                }

                ksort($dateResp);
                //check time from now to tommorow
                //open time
                $time = ['Pagi ( 08:00 - 12:00 )','Siang ( 12:00 - 15:00 )','Sore ( 15:00 - 18:00 )'  ];
                $data = [ 'date' => $dateResp, 'month_year' => $monthYearResp , 'time' => $time ];
                $success = true;
                $msg = 'OK';
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

}
