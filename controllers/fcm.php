<?php

function send_message($token, $title, $body, $clickAction) {
    $url = "https://fcm.googleapis.com/fcm/send";
    $serverKey = 'AAAAckBCIKU:APA91bF8yKTSWvRLuYpCe2rbQYrZKsn9Lmg5iRGMc6oXaSlTw1xUcv9E-xCyweIhFlK_CzgoxOe0T4qOoEdi0xodNJhQHGrxfzQJkQG3BMASB7k3MSYaat83V2WF8JNQoUmcJg-r9TnC';
    $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1', 'click_action' => $clickAction);
    $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    //Send the request
    $response = curl_exec($ch);
    //Close request
    if ($response === FALSE) {
    }
    curl_close($ch);
}