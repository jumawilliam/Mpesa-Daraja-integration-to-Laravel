<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class PaymentController extends Controller
{
    public function token(){
        $consumerKey='';
        $consumerSecret='';
        $url='https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response=Http::withBasicAuth($consumerKey,$consumerSecret)->get($url);
        return $response['access_token'];
    }

    public function initiateStkPush(){
        $accessToken=$this->token();
        $url='https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $PassKey='bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $BusinessShortCode=174379;
        $Timestamp=Carbon::now()->format('YmdHis');
        $password=base64_encode($BusinessShortCode.$PassKey.$Timestamp);
        $TransactionType='CustomerPayBillOnline';
        $Amount=1;
        $PartyA='your phone number';
        $PartyB=174379;
        $PhoneNumber='your phone number';
        $CallbackUrl='https://www.princeschool.e-skuli.co.ke/mypayments';
        $AccountReference='Coders base';
        $TransactionDesc='payment for goods';

        $response=Http::withToken($accessToken)->post($url,[
            'BusinessShortCode'=>$BusinessShortCode,
            'Password'=>$password,
            'Timestamp'=>$Timestamp,
            'TransactionType'=>$TransactionType,
            'Amount'=>$Amount,
            'PartyA'=>$PartyA,
            'PartyB'=>$PartyB,
            'PhoneNumber'=>$PhoneNumber,
            'CallBackURL'=>$CallbackUrl,
            'AccountReference'=>$AccountReference,
            'TransactionDesc'=>$TransactionDesc
        ]);

        return $response;

    }

    public function stkCallback(){

    }
}
