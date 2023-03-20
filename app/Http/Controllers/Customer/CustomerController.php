<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AuthApi;
use App\Models\Customer;
use App\Models\PushNotification;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function profile(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $bubble = [];
        $information = [];
        $param = $request->json();
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                    $information['phone_send_proof'] = '6288290172712';
                    $data = $checkCustomer;
                    $bubble = [ 'notification' => PushNotification::where('customer_id',$checkCustomer->id)->where('push_notification_status',0)->count() ];
                    $success = true;
                    $msg = 'OK';
            }else{
                $msg = 'You Not Valid';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data, 'bubble' => $bubble , 'information' => $information ]
        );
    }

    public function address(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $is_primary = $param->get('is_primary');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                if($is_primary) {
                    $dataObj = CustomerAddress::where('customer_id',$checkCustomer->id)->where('is_primary',true)->orderBy('is_primary', 'desc')->orderBy('created_date', 'desc')->get();

                }else{
                    $dataObj = CustomerAddress::where('customer_id',$checkCustomer->id)->orderBy('is_primary', 'desc')->orderBy('created_date', 'desc')->get();
                }
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
            ['success' => $success , 'msg' => $msg , 'data' => $data  ]
        );
    }

    public function addressAdd(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $name = $param->get('name');
        $address = $param->get('address');
        $longitude = $param->get('longitude');
        $latitude = $param->get('latitude');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $checkCustomerAddres = CustomerAddress::where('customer_id',$checkCustomer->id)->where('name',$name)->first();
                if($checkCustomerAddres){
                     $msg = 'You already hacve this address';
                }else{
                    $CustomerAddress =  CustomerAddress::create([
                                    'name' =>  $name ,
                                    'address' =>  $address ,
                                    'longitude' =>  $longitude ,
                                    'latitude' =>  $latitude ,
                                    'customer_id' =>  $checkCustomer->id ,
                                    'is_primary' =>  0 ,
                                    'status' => 1
                                ]);
                    if($CustomerAddress) {
                        $data = $CustomerAddress;
                        $success = true;
                        $msg = 'OK';
                    }
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

    public function addressEdit(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $id = $param->get('id');
        $name = $param->get('name');
        $address = $param->get('address');
        $longitude = $param->get('longitude');
        $latitude = $param->get('latitude');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $checkCustomerAddres = CustomerAddress::where('customer_id',$checkCustomer->id)->where('id',$id)->first();
                if($checkCustomerAddres){
                    $checkCustomerAddres->name =  $name ;
                    $checkCustomerAddres->address =  $address ;
                    $checkCustomerAddres->longitude =  $longitude ;
                    $checkCustomerAddres->latitude =  $latitude ;
                    if($checkCustomerAddres->save()) {
                        $data = $checkCustomerAddres;
                        $success = true;
                        $msg = 'OK';
                    }
                }else{
                    $msg = 'address not Found';
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

    public function addressDelete(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $id = $param->get('id');
        $name = $param->get('name');
        $address = $param->get('address');
        $longitude = $param->get('longitude');
        $latitude = $param->get('latitude');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $checkCustomerAddres = CustomerAddress::where('customer_id',$checkCustomer->id)->where('id',$id)->first();
                if($checkCustomerAddres){ 
                    if($checkCustomerAddres->delete()) {
                        $data = null;
                        $success = true;
                        $msg = 'DELETED';
                    }
                }else{
                    $msg = 'address not Found';
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

    public function addressPrimary(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $id = $param->get('id');
        $name = $param->get('name');
        $address = $param->get('address');
        $longitude = $param->get('longitude');
        $latitude = $param->get('latitude');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $checkCustomerAddres = CustomerAddress::where('customer_id',$checkCustomer->id)->where('id',$id)->first();
                if($checkCustomerAddres){
                    $checkCustomerAddresPrimary = CustomerAddress::where('customer_id',$checkCustomer->id)->where('is_primary',true)->first();
                    if($checkCustomerAddresPrimary){
                        $checkCustomerAddresPrimary->is_primary = false;
                        $checkCustomerAddresPrimary->save();
                    }
                    $checkCustomerAddres->is_primary =  1 ;
                    if($checkCustomerAddres->save()) {
                        $data = $checkCustomerAddres;
                        $success = true;
                        $msg = 'OK';
                    }
                }else{
                    $msg = 'address not Found';
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

    public function profileEdit(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $email = $param->get('email');
        $fullname = $param->get('fullname');
        $phone = $param->get('phone');
        $gender = $param->get('gender');
        $dob = $param->get('dob');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                    if($email) $checkCustomer->email = $email;
                    if($fullname) $checkCustomer->fullname = $fullname;
                    if($phone) $checkCustomer->phone = $phone;
                    if($gender) $checkCustomer->gender = $gender;
                    if($dob) $checkCustomer->dob = $dob;
                    $checkCustomer->save();
                    $data = $checkCustomer;
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
    public function disactiveAccount(Request $request)
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
                    $checkCustomer->user_token = '';
                    $checkCustomer->status = 9;
                    $checkCustomer->save();
                    $data = null;
                    $success = true;
                    $msg = 'This user already disactive, to activate please contact our customer services';
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
