<?php

require 'vendor/autoload.php';

$siteUrl = 'https://www.vndirect.com.vn';
$authUrl = 'https://auth-api.vndirect.com.vn/auth';
$customerDetailsUrl = 'https://trade-api.vndirect.com.vn/customer';
$user = '';
$pass = '';

$step1 = new GuzzleHttp\Client();
$res = $step1->request('POST', $authUrl, [
    'form_params' => [
        'username' => $user,
        'password' => $pass,
    ]
]);

echo '<pre>Step 1: Getting the auth token required for the user/credentials combination<br>';
echo $res->getBody() . '<br><br>';
$authToken = json_decode($res->getBody());

echo 'Step 2: Getting the customer account details:<br>';
$step2 = new GuzzleHttp\Client();
$res = $step2->request('GET', $customerDetailsUrl, [
    'headers' => [
        'X-AUTH-TOKEN' => $authToken->token,
    ]
]);
echo $res->getBody();
