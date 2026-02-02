<?php
namespace App\Services\Whatsapp;

class WhatsAppApi {

    protected $authkey;
    protected $appkey;

    public function __construct() {
        $this->authkey =  env('authkey', 'FzLBCJBdsS0S7Ea08AixvW0uAdzd0wPelsDBw9YsROReOBnHaU');
        $this->appkey = env('appkey', '0cfd6cc3-ecdd-4576-aec0-0a96e5bdce29');
    }

    public function SendMsgOnly($device, $to, $msg)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://botsmsg.com/api/create-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => $this->appkey,
                'authkey' => $this->authkey,
                'to' => $to,
                'message' => $msg,
                'sandbox' => 'false'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    // Text Message Only
    public function SendMsgFile($device, $to, $msg, $file)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://botsmsg.com/api/create-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => $this->appkey,
                'authkey' => $this->authkey,
                'to' => $to,
                'message' => $msg,
                'file' => $file,
                'sandbox' => 'false'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

}