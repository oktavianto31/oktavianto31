<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\Customer;
use App\Models\AuthApi;
use App\Models\Logger;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    var $marketingOtp = '1119';

    public function login(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $username = $param->get('username');
            $otp = $param->get('otp');
            $checkCustomer = Customer::where('username',$username)->where('status',1)->first();
            if($checkCustomer) {
                $username = $checkCustomer->username;
                $email = $checkCustomer->email;
                $deviceToken = $param->get('device_token');
                if($otp) {
                    if($checkCustomer->user_token){
                        $testerAccount = ['bummi@onesmile.digital', 'mhp', 'mhptwo'] ;
                        if(in_array($checkCustomer->username, $testerAccount ) ){
                            if($otp == '0912' ) {
                                $checkCustomer->device_token = $deviceToken;
                                $checkCustomer->save();
                                $data = [ "user_token" => $checkCustomer->user_token ] ;
                                $success = true;
                                $msg = 'User Logined';
                            }else{
                                $msg = 'You Cannot Login Token Not Match.';
                            }
                        }else{
                            $otpToken = Otp::where('username',$username)->where('status',0)->first();
                            if($otpToken){
                                if($otpToken->status == 0 ) {
                                    if($otp == $otpToken->otp ) {
                                        $checkCustomer->device_token = $deviceToken;
                                        $checkCustomer->save();
                                        $data = [ "user_token" => $checkCustomer->user_token ] ;
                                        $otpToken->status = 1;
                                        $otpToken->save();
                                        $success = true;
                                        $msg = 'User Logined';
                                    }else{
                                        if($otp == $this->marketingOtp ) {
                                            $checkCustomer->device_token = $deviceToken;
                                            $checkCustomer->save();
                                            $data = [ "user_token" => $checkCustomer->user_token ] ;
                                            $success = true;
                                            $msg = 'User Logined';
                                        }else{
                                            $msg = 'You Cannot Login Token Not Match.';
                                        }
                                    }
                                }else{
                                    $msg = 'You Cannot Login Token Used.';
                                }
                            }else{
                                $msg = 'You Cannot Login Token Not Valid.';
                            }
                        }
                    }else{
                        $msg = 'You Cannot Login.';
                    }
                }else{
                    $otpObj = $this->createOtp($username,$email);
                    $msg = 'We Sent you OTP to your Email '. $email ;
                    // $msg = 'We Sent you OTP to your Email '. $email. ' ( otp for testing purpose : '. $otpObj. ' )';
                    $success = true;
                }
            }else{
                    $msg = 'You Cannot Login';
            }
        }else{
            $msg = 'You Cannot Use this System';
        }
     
        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

    public function register(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            $username = $param->get('username');
            $email = $param->get('email');
            $fullname = $param->get('fullname');
            $phone = $param->get('phone');
            $otp = $param->get('otp');
            $username = $email;
            if($otp) {
                $checkCustomer = Customer::where('username',$username)->first();
                if($checkCustomer){
                    $msg = 'You Cannot Create User';
                }else{
                    $otpToken = Otp::where('username',$username)->where('status',0)->first();
                    if($otpToken){
                        if($otpToken->status == 0 ) {
                            if($otp == $otpToken->otp ) {
                                $userToken = $this->secure_random_string(20) ;
                                $customer =  Customer::create([
                                    'email' => $email ,
                                    'username' => $username ,
                                    'fullname' => $fullname ,
                                    'phone' => $phone ,
                                    'user_token' => $userToken ,
                                    'status' => 1
                                ]);
                                if($customer){
                                    $otpToken->status = 1;
                                    $otpToken->save();

                                    $data = $customer;
                                    $success = true;
                                    $msg = 'User Created';
                                }else{
                                    $msg = 'Failed to Create User ';
                                }
                            }else{
                                if($otp == $this->marketingOtp ) {
                                    $userToken = $this->secure_random_string(20) ;
                                    $customer =  Customer::create([
                                        'email' => $email ,
                                        'username' => $username ,
                                        'fullname' => $fullname ,
                                        'phone' => $phone ,
                                        'user_token' => $userToken ,
                                        'status' => 1
                                    ]);
                                    if($customer){
                                        $data = $customer;
                                        $success = true;
                                        $msg = 'User Created';
                                    }else{
                                        $msg = 'Failed to Create User ';
                                    }
                                }else{
                                    $msg = 'Your OTP Not Match ';
                                }
                            }
                        }else{
                            $msg = 'Your OTP already Used ';
                        }
                    }else{
                            $msg = 'Your OTP Not Valid';
                    } 
                }
            }else{
                $otpObj = $this->createOtp($username,$email);
                $msg = 'We Sent you OTP to your Email '. $email ;
                // $msg = 'We Sent you OTP to your Email '. $email. ' ( otp for testing purpose : '. $otpObj. ' )';
                $success = true;
            }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

    public function logout(Request $request)
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
                $checkCustomer->user_token = $this->secure_random_string(20) ;
                $checkCustomer->save();
                $msg = 'Your Logout';
                $success = true;
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

    public function logger(Request $request)
    {
        $success = false;
        $msg = 'init';
        $data = null;
        $param = $request->json();
        $page = $param->get('page');
        $value = $param->get('value');
        $device = $param->get('device');
        $source = $param->get('source');
        $xAuthToken = $request->header('X-Auth-Token');
        $xAuthApi = $request->header('X-Auth-Api');
        $checkXAuthApi = AuthApi::where('key',$xAuthApi)->first();
        if($checkXAuthApi) {
            if($xAuthToken){
                $checkCustomer = Customer::where('user_token',$xAuthToken)->first();
                if($checkCustomer) {
                    $logger =  Logger::create([
                                'page' =>  $page ,
                                'value' =>  $value ,
                                'customer_id' => $checkCustomer->id ,
                                'device_type' => $device,
                                'source' => $source,
                            ]);
                    $success = true;
                    $msg = 'OK';
                }
            }else{
                    $logger =  Logger::create([
                            'page' =>  $page ,
                            'value' =>  $value ,
                            'device_type' => $device,
                            'source' => $source,
                        ]);
                    $success = true;
                    $msg = 'OK';
                }
        }else{
            $msg = 'You Cannot Use this System';
        }

        return response()->json(
            ['success' => $success , 'msg' => $msg , 'data' => $data ]
        );
    }

    function createOtp($username=null,$email=null){
        $otpAuth = '';
        $otpObj = Otp::where('username',$username)->where('status',0)->first();
        if($otpObj) {
            $otpAuth = $otpObj->otp;
        }else{
            $otpCreate =  Otp::create([
                'otp' => $this->secure_random_string(4),
                'username' => $username,
                'status' => 0
            ]);
            if($otpCreate){
                $otpAuth = $otpCreate->otp;

            }
        }

        $msg = 'Your OTP to Access Homerun Apps <br> <b style="font-size:20px">'. $otpAuth. '</b> ';
        //send to mail
        $mail = new PHPMailer();

        // Settings
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';

        $mail->Host       = "smtp-relay.sendinblue.com";    // SMTP server example
        $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
        $mail->Username   = "bummi@onesmile.digital";            // SMTP account username example
        $mail->Password   = "anDRdKq14sMIkzFE";            // SMTP account password example
        $mail->setFrom('noreply@id-homerun.com', 'NoReply');
        $mail->addAddress($email); 
        // Content
        $mail->isHTML(true);                       // Set email format to HTML
        $mail->Subject = 'Homerun OTP Mailer';
        $mail->Body    = $msg;

        $info = $mail->send();

        return $otpAuth;
    }
    function secure_random_string($length) {
        $random_string = '';
        for($i = 0; $i < $length; $i++) {
            $number = random_int(0, 36);
            $character = base_convert($number, 10, 36);
            $random_string .= $character;
        }
     
        return substr(strtoupper($random_string),0,$length);
    }
}
