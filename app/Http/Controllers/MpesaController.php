<?php

namespace App\Http\Controllers;

use App\Models\Stkrequest;

use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
class MpesaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *

     */
public function token(){
$consumerKey='RzLye0l4Hzx3x4rDQxbHgOZpWsfn4eT7';
$consumerSecret='TFrGhfF36JcTPVbp';
$credentials = base64_encode($consumerKey.":".$consumerSecret);
$url='https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$response=Http::withBasicAuth($consumerKey,$consumerSecret)->get($url);
return $response['access_token'];


}


    public function initiateStkPush(Request $request)
    {        $accessToken=$this->token();
        $url='https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $PassKey='bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $BusinessShortCode=174379;
        $Timestamp=Carbon::now()->format('YmdHis');
        //$Password=base64_encode($BusinessShortCode.$PassKey.$Timestamp);
        $TransactionType='CustomerPayBillOnline';
        $Amount=1;
        $PartyA=254708xxx014;
        $PartyB=174379;
        $PhoneNumber=254708xxx014;
        $CallbackUrl='https://72c0-102-135-169-117.eu.ngrok.io/stkCallback';
        $AccountRefrence='Quickoffice';
        $TransactionDescription='Payment e-service';

        try {

$response=Http::withToken($accessToken)->post($url,[
    'BusinessShortCode'=>$BusinessShortCode,
    'Password'=>$Password,
    'Timestamp'=>$Timestamp,
    'TransactionType'=>$TransactionType,
    'Amount'=>$Amount,
    'PartyA'=>$PartyA,
    'PartyB'=>$PartyB,
    'PhoneNumber'=>$PhoneNumber,
'CallBackURL'=>$CallbackUrl,
'AccountReference'=>$AccountRefrence,
'TransactionDesc'=>$TransactionDescription
]);
if ($response->getStatusCode() !== 200) {
    throw new \Exception('Error in payment request. Response status code: ' . $response->getStatusCode());
}

$res = json_decode($response->body());

$ResponseCode = $res->ResponseCode;

if ($ResponseCode==0) {
    $MerchantRequestID = $res->MerchantRequestID;
    $CheckoutRequestID = $res->CheckoutRequestID;
    $CustomerMessage = $res->CustomerMessage;

    // Save the response to the database
    $payment = new Mpesa();
    $payment->phone = $PhoneNumber;
    $payment->amount = $Amount;
    $payment->reference = $AccountRefrence;
    $payment->description = $TransactionDescription;
    $payment->MerchantRequestID = $MerchantRequestID;
    $payment->CheckoutRequestID = $CheckoutRequestID;
    $payment->status = 'Requested';
    $payment->response = json_encode($res); // save the entire response in json format
    $payment->save();

    return $CustomerMessage;
}
} catch (\Throwable $e) {
return redirect('/error')->with(['error' => $e->getMessage()]);
}
}

Public function stkCallback(){
    $data=file_get_contents('php://input');
    Storage::disk('local')->put('stk.txt',$data);

    $response=json_decode($data, true);

    $ResultCode=$response['Body']['stkCallback']['ResultCode'];

    if($ResultCode==0){
        $MerchantRequestID=$response['Body']['stkCallback']['MerchantRequestID'];
        $CheckoutRequestID=$response['Body']['stkCallback']['CheckoutRequestID'];
        $ResultDesc=$response['Body']['stkCallback']['ResultDesc'];
        $Amount=$response['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $MpesaReceiptNumber=$response['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
        //$Balance=$response['Body']['stkCallback']['CallbackMetadata']['Item'][2]['Value'];
        $TransactionDate=$response['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
        $PhoneNumber=$response['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

        $payment=Stkrequest::where('CheckoutRequestID',$CheckoutRequestID)->first();
        $payment->status='Paid';
        $payment->TransactionDate=$TransactionDate;
        $payment->MpesaReceiptNumber=$MpesaReceiptNumber;
        $payment->ResultDesc=$ResultDesc;
        $payment->save();

    }else{

    $CheckoutRequestID=$response['Body']['stkCallback']['CheckoutRequestID'];
    $ResultDesc=$response['Body']['stkCallback']['ResultDesc'];
    $payment=Stkrequest::where('CheckoutRequestID',$CheckoutRequestID)->first();

    $payment->ResultDesc=$ResultDesc;
    $payment->status='Failed';
    $payment->save();

    }
}



    public function stkQuery(){
        $accessToken=$this->token();
        $BusinessShortCode=174379;
        $PassKey='bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $url='https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
        $Timestamp=Carbon::now()->format('YmdHis');
        $Password=base64_encode($BusinessShortCode.$PassKey.$Timestamp);
        $CheckoutRequestID='ws_CO_16012023154742960708112014';

        $response=Http::withToken($accessToken)->post($url,[

            'BusinessShortCode'=>$BusinessShortCode,
            'Timestamp'=>$Timestamp,
            'Password'=>$Password,
            'CheckoutRequestID'=>$CheckoutRequestID
        ]);

        return $response;
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mpesa  $mpesa
     * @return \Illuminate\Http\Response
     */
    public function show(Mpesa $mpesa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mpesa  $mpesa
     * @return \Illuminate\Http\Response
     */
    public function edit(Mpesa $mpesa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mpesa  $mpesa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mpesa $mpesa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mpesa  $mpesa
     * @return \Illuminate\Http\Response
     */

}
