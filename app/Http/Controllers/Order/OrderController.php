<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AuthApi;
use App\Models\Customer;
use App\Models\Services;
use App\Models\ServicesCategory;
use App\Models\ServicesInformation;
use App\Models\OrderDetail;
use App\Models\OrderReview;
use App\Models\OrderDetailInformation;
use App\Models\CustomerAddress;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function activity(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $page = $param->get('page');
        $limit = 10 ;
        $offset = ( $limit * $page ) - $limit ;
        $sort = $param->get('sort');
        $filter = $param->get('filter');
        $search = $param->get('search');

        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $order = Order::where('customer_id',$checkCustomer->id)->orderBy('created_date','desc')->orderBy('status','desc')->limit($limit)->offset($offset)->get();
                if($order) {
                    foreach($order as &$val){
                        if( $val->status == 0 ) {
                            if($val->expired_date < date("Y-m-d H:i:s")) {
                                $val->status = 4;
                                $val->save();
                            }
                        }
                    }
                    $statusColor[0] = '#262626';
                    $statusColor[1] = '#74eb34';
                    $statusColor[2] = '#3465eb';
                    $statusColor[3] = '#eb4034';
                    $statusColor[4] = '#ffffff';

                    $statusBgColor[0] = '#eb4034';
                    $statusBgColor[1] = '#3465eb';
                    $statusBgColor[2] = '#74eb34';
                    $statusBgColor[3] = '#262626';
                    $statusBgColor[4] = '#780096';

                    $statuslabel[0] = 'Ordered';
                    $statuslabel[1] = 'Paid';
                    $statuslabel[2] = 'Process';
                    $statuslabel[3] = 'Completed';
                    $statuslabel[4] = 'Expired';
                    foreach($order as &$val){
                        //get detail
                        $order_detail = OrderDetail::where('order_id',$val->id)->get();

                        //get information 
                        $order_information = OrderDetailInformation::where('order_id',$val->id)->get();

                        //get review
                        $order_review = OrderReview::where('order_id',$val->id)->get();
                        if($val->status== 0) {
                            $val->expired_date =  $val->expired_date;
                        }else{
                            $val->expired_date = null;
                        }
                        $val->status_label = $statuslabel[$val->status];
                        $val->status_color = $statusColor[$val->status];
                        $val->status_bg_color = $statusBgColor[$val->status];
                        $val->created_date = date("d M Y H:i", strtotime($val->created_date));
                        $val->order_detail = $order_detail;
                        $val->order_information = $order_information;
                        $val->order_review = $order_review;
                    }
                    $data = $order;
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

    public function activitySummary(Request $request)
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
                $dataObj = Order::where('customer_id',$checkCustomer->id)->limit(3)->orderBy('created_date','desc')->orderBy('status','desc')->get();
                $statusColor[0] = '#262626';
                $statusColor[1] = '#74eb34';
                $statusColor[2] = '#3465eb';
                $statusColor[3] = '#eb4034';
                $statusColor[4] = '#ffffff';

                $statusBgColor[0] = '#eb4034';
                $statusBgColor[1] = '#3465eb';
                $statusBgColor[2] = '#74eb34';
                $statusBgColor[3] = '#262626';
                $statusBgColor[4] = '#780096';

                $statuslabel[0] = 'Ordered';
                $statuslabel[1] = 'Paid';
                $statuslabel[2] = 'Process';
                $statuslabel[3] = 'Completed';
                $statuslabel[4] = 'Expired';
                if($dataObj) {
                    foreach($dataObj as &$val){
                        if( $val->status == 0 ) {
                            if($val->expired_date < date("Y-m-d H:i:s")) {
                                $val->status = 4;
                                $val->save();
                            }
                        }
                    }
                    foreach($dataObj as &$val){
                        $val->status_label = $statuslabel[$val->status];
                        $val->status_color = $statusColor[$val->status];
                        $val->status_bg_color = $statusBgColor[$val->status];
                        if($val->status== 0) {
                            $val->expired_date =  $val->expired_date;
                        }else{
                            $val->expired_date = null;
                        }
                        $val->created_date = date("d M Y H:i", strtotime($val->created_date));
                    }
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

    public function create(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $order = null;
        $order_detail = [];
        $order_information = [];
        $isOk = true;

        $name = $param->get('name');
        $address_id = $param->get('address_id');
        $services_id = $param->get('services_id');
        $services_information_id = $param->get('services_information_id');
        $services_category = $param->get('services_category');
        $services_note = $param->get('services_note');
        $date_info = $param->get('date_info');

        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                 $services = Services::where('id',$services_id)->first();
                 if($services) {

                    //compiling data
                    $order_name = $services->services_name;
                    $servicesInformationParam = [];
                    $invoices_number =  $this->createInvoiceNumber($checkCustomer->id) ;
                    $address =  '';
                    $longitude =  '';
                    $latitude =  '';
                    $address_name =  '';
                    $amount = 0;
                    $discount = 0;
                    $order_type = $services->services_type;
                    $customer_id = $checkCustomer->id;
                    $fullname = $checkCustomer->fullname;
                    $phone = $checkCustomer->phone;

                    //get service detail
                    if(count($services_category) > 0 ) {
                        foreach($services_category as &$scv){
                             $serviceCategory = ServicesCategory::where('services_id',$services->id)->where('id',$scv['id'])->first();
                             if($serviceCategory) {
                                 $scv['name'] = $serviceCategory->name;
                                 $scv['price'] = $serviceCategory->price;
                                 $scv['discount'] = $serviceCategory->discount;
                                 $scv['sub_discount'] = $serviceCategory->discount - $serviceCategory->price ;
                                 $scv['image'] = $serviceCategory->image;
                                 $scv['icon'] = $serviceCategory->icon;
                                 $scv['description'] = $serviceCategory->description;
                                 $scv['type_quantity'] = " ".$serviceCategory->type_quantity;
                                 $amount+= ($scv['price']*$scv['quantity']);
                                 $discount+= ($scv['sub_discount']*$scv['quantity']);
                             }else{
                                $isOk = false;
                                $msg = "item not found";
                             }
                        }
                    }
                    //service information
                    if(count($services_information_id) > 0 ) {
                        foreach($services_information_id as $siv){
                            $servicesInformationParam[$siv] = $siv;
                        }
                    }

                    //get address
                    $customerAddress = CustomerAddress::where('id',$address_id)->first();
                    if($customerAddress){
                        $address = $customerAddress->address;
                        $longitude = $customerAddress->longitude;
                        $latitude = $customerAddress->latitude;
                        $address_name = $customerAddress->address_name;
                    }else{
                        $isOk = false;
                        $msg = "address not found";
                    }

                    //calculate amount
                    $final_amount = $amount;
                    $final_discount = $discount;
                    if($isOk) {
                        //order
                        $order =  Order::create([
                                    'invoices_number' => $invoices_number ,
                                    'amount' => $amount ,
                                    'order_name' => $order_name ,
                                    'order_type' => $order_type ,
                                    'discount' => $final_discount ,
                                    'final_amount' => $final_amount ,
                                    'status' => 0 ,
                                    'customer_id' => $customer_id ,
                                    'fullname' => $fullname ,
                                    'address' => $address ,
                                    'phone' => $phone ,
                                    'customer_note' => $services_note ,
                                    'longitude' => $longitude ,
                                    'latitude' => $latitude ,
                                    'address_name' => $address_name ,
                                    'date_info' => $date_info ,
                                    'expired_date' => date("Y-m-d H:i:s", strtotime(   date("Y-m-d H:i:s") . " +1 days")) ,
                                ]);
                        if($order){
                            //orde detail
                            if(count($services_category) > 0 ) {
                                foreach($services_category as $scvv){
                                    $order_detail[] = OrderDetail::create([
                                        'order_id' => $order->id ,
                                        'product_name' => $scvv['name'] ,
                                        'product_description' => $scvv['description'] ,
                                        'product_amount' => $scvv['price'] ,
                                        'product_discount' => $scvv['discount'] ,
                                        'product_icon' => $scvv['icon'] ,
                                        'product_image' => $scvv['image'] ,
                                        'product_service_type' => $services->services_type ,
                                        'product_quantity' => $scvv['quantity'] ,
                                        'product_service_id' => $services->id,
                                        'type_quantity' => $scvv['type_quantity'],
                                    ]);
                                }
                            }
                            //order information
                            $servicesInformation = ServicesInformation::where('services_id',$services->id)->get();
                            if($servicesInformation){
                                foreach($servicesInformation as $siv ){
                                    $order_information[] = OrderDetailInformation::create([
                                        'order_id' =>  $order->id,
                                        'information_id' =>  $siv->id,
                                        'status' =>  (isset($servicesInformationParam[$siv->id]) ? true : false ),
                                    ]);
                                }
                            }

                            $data = [ 'order' => $order, 'order_detail' => $order_detail , 'order_information' => $order_information ];
                            $success = true;
                            $msg = 'OK';
                        }
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

    public function detail(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $order_id = $param->get('id');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $order = Order::where('id',$order_id)->where('customer_id',$checkCustomer->id)->first();
                if($order) {
                    $statusColor[0] = '#262626';
                    $statusColor[1] = '#74eb34';
                    $statusColor[2] = '#3465eb';
                    $statusColor[3] = '#eb4034';
                    $statusColor[4] = '#ffffff';

                    $statusBgColor[0] = '#eb4034';
                    $statusBgColor[1] = '#3465eb';
                    $statusBgColor[2] = '#74eb34';
                    $statusBgColor[3] = '#262626';
                    $statusBgColor[4] = '#780096';

                    $statuslabel[0] = 'Ordered';
                    $statuslabel[1] = 'Paid';
                    $statuslabel[2] = 'Process';
                    $statuslabel[3] = 'Completed';
                    $statuslabel[4] = 'Expired';

                    $order->status_label = $statuslabel[$order->status];
                    $order->status_color = $statusColor[$order->status];
                    $order->status_bg_color = $statusBgColor[$order->status];

                    if($order->status== 0) {
                        $order->expired_date = $order->expired_date;
                    }else{
                        $order->expired_date = null;
                    }
                    $order->created_date = date("d F Y H:i", strtotime($order->created_date));
                    //get detail
                    $order_detail = OrderDetail::where('order_id',$order->id)->get();

                    //get information 
                    $order_information = OrderDetailInformation::select(['order_detail_information.*','service_information.information_name'])
                    ->leftJoin('service_information', 'service_information.id', '=', 'order_detail_information.information_id')
                    ->where('order_detail_information.status',true)
                    ->where('order_id',$order->id)->get();

                    //get review
                    $order_review = OrderReview::select(['order_review.id','order_review.star','order_review.review','order_review.order_id','order_review.created_date','order_review.customer_id','customer.fullname'])
                    ->leftJoin('customer', 'customer.id', '=', 'order_review.customer_id')
                    ->where('order_id',$order->id)
                    ->get(); 

                    $data = [ 'order' => $order, 'order_detail' => $order_detail , 'order_information' => $order_information , 'order_review' => $order_review  ];
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

    public function payment(Request $request)
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
                $paymentMethod = PaymentMethod::where('code','BCATRANSFER')->first();
                if($paymentMethod) {
                    $data = $paymentMethod;
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

    public function createReview(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $star = $param->get('star');
        $review = $param->get('review');
        $order_id = $param->get('order_id');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
            if($checkCustomer) {
                $order = Order::where('id',$order_id)->first();
                if($order){
                    $checkOrderReview = OrderReview::where('order_id',$order->id)->where('customer_id',$checkCustomer->id)->first();
                    if($checkOrderReview){
                         $msg = 'You already review this order';
                    }else{
                        $orderReview =  OrderReview::create([
                                            'star' => $star, 
                                            'review' => $review, 
                                            'order_id' => $order->id, 
                                            'customer_id' => $checkCustomer->id, 
                                    ]);
                        if($orderReview) {
                            $data = $orderReview;
                            $success = true;
                            $msg = 'OK';
                        }
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


    function createInvoiceNumber($customerId=null){
        $invoice['prefix'] = "INV/HMRN";
        $invoice['datetime'] = date("Y/md");
        $invoice['customer'] = $customerId;
        $invoice['hhmmss'] = date("his");
        return implode('/',$invoice);

    }

}
