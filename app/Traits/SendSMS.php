<?php

namespace App\Traits;


use Twilio\Rest\Client;
use \Modules\GeneralSetting\Entities\BusinessSetting;

trait SendSMS
{
    public function sendIndividualSMS($number, $text)
    {
        $apy_key = env('SMS_API_KEY');

        try {
            $soapClient = new \SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
            $paramArray = array(
                'apiKey' => $apy_key,
                'messageText' =>  $text,
                'numberList' => $number,
                'smsType' => "TEXT",
                'maskName' => '',
                'campaignName' => '',
            );
            $value = $soapClient->__call("NumberSms", array($paramArray));
            return $value;
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }

    function sendSMS($to, $text,$to_name='',$user_email='',$order_tracking_number = '',$secret_code='',$giftcard ='')
    {


        $text = str_replace("{USER_FIRST_NAME}", $to_name, $text);
        $text = str_replace("{USER_EMAIL}",$user_email, $text);
        $text = str_replace("{ORDER_TRACKING_NUMBER}", $order_tracking_number, $text);
        $text = str_replace("{WEBSITE_NAME}", app('general_setting')->site_title, $text);
        $text = str_replace("{GIFT_CARD_NAME}", $giftcard, $text);
        $text = str_replace("{SECRET_CODE}", $secret_code, $text);
        $return = true;

        if (BusinessSetting::where('type', 'Twillo')->first()->status) {
            if ($to) {

                $sid = env("TWILIO_SID"); // Your Account SID from www.twilio.com/console
                $token = env("TWILIO_TOKEN"); // Your Auth Token from www.twilio.com/console

                $client = new Client($sid, $token);
                try {
                    $message = $client->messages->create(
                        $to, // Text this number
                        array(
                            'from' => env('VALID_TWILLO_NUMBER'), // From a valid Twilio number
                            'body' => $text
                            )
                        );
                } catch (\Exception $e) {
                    $return = false;
                }
            }
        } elseif (BusinessSetting::where('type', 'Text to Local')->first()->status) {
            // Account details
            $apiKey = urlencode(env('TEXT_TO_LOCAL_API_KEY'));

            // Message details
            $numbers = array($to);
            $sender = urlencode(env('TEXT_TO_LOCAL_SENDER'));
            $message = rawurlencode($text);

            $numbers = implode(',', $numbers);

            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

            // Send the POST request with cURL
            $ch = curl_init('https://api.txtlocal.com/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // Process your response here
            $return = true;
            // return $response;
        }
        return $return;

    }
}
