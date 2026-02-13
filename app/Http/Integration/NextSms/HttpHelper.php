<?php

namespace App\Http\Integration\NextSms;

use App\Http\Integration\NextSms\Constants;

class HttpHelper
{

    public function send($phone, $message, $sender)
    {

        $data = array(
            'from' => $sender,
            'to' => $phone,
            'text' => $message
        );
        $url = Constants::BASE_URL;


        $data_string = json_encode($data);
        $ch = curl_init();
        set_time_limit(60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Basic aHlwZXR6Omh5cGUyMDE3',
            'Content-Type: application/json',
            'Accept: application/json'
        ));



        $response = curl_exec($ch);
        
        curl_close($ch);


        return $response;


//         $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => Constants::BASE_URL,
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS =>'{"from":"N-SMS", "to":"255716718040",  "text": "Your message"}',
//   CURLOPT_HTTPHEADER => array(
//     'Authorization:Basic aHlwZXR6Omh5cGUyMDE3',
//     'Content-Type: application/json',
//     'Accept: application/json'
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;
//     }

    }

}
