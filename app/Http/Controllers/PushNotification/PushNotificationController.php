<?php

namespace App\Http\Controllers\PushNotification;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Models\AuthApi;
use App\Models\Customer;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function inbox(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $page = $param->get('page');
        $limit = 5 ;
        $offset = ( $limit * $page ) - $limit ;
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = PushNotification::select(['push_notification_id','push_notification_text','push_notification_status','push_notification_date','push_notification_title','push_notification_image'])->where('customer_id',$checkCustomer->id)->limit($limit)->offset($offset)->orderBy('push_notification_date','desc')->get();
                if($dataObj) {
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }else{
                    $msg = 'banner not found';
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

    public function detail(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $id = $param->get('id');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $dataObj = PushNotification::select(['push_notification_id','push_notification_text','push_notification_status','push_notification_date','push_notification_title','push_notification_image'])->where('customer_id',$checkCustomer->id)->where('push_notification_id',$id)->first();
                if($dataObj) {
                    $dataObj->push_notification_status = 1;
                    $dataObj->save();
                    $data = $dataObj;
                    $success = true;
                    $msg = 'OK';
                }else{
                    $msg = 'banner not found';
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
