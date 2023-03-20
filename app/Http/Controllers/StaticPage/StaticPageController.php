<?php

namespace App\Http\Controllers\StaticPage;

use App\Http\Controllers\Controller;
use App\Models\AuthApi;
use App\Models\Customer;
use App\Models\StaticPage;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function staticPage(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $type = $param->get('type');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = StaticPage::where('type',$type)->where('status',1)->orderBy('ordering', 'asc')->get();
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
}
