<?php
require 'vendor/autoload.php';

$siteUrl = 'https://www.vndirect.com.vn';
$baseAuthUrl = 'https://auth-api.vndirect.com.vn';
$customerDetailsUrl = 'https://trade-api.vndirect.com.vn/customer';
$user = '';
$pass = '';
$accessMatrix = array(
    'a1' => 'm', 'a2' => 'e', 'a3' => 'q', 'a4' => 'v', 'a5' => 'y', 'a6' => 'v', 'a7' => '7',
    'b1' => 'k', 'b2' => 'x', 'b3' => '0', 'b4' => 'e', 'b5' => '0', 'b6' => 'e', 'b7' => '4',
    'c1' => '2', 'c2' => 'q', 'c3' => '2', 'c4' => 'r', 'c5' => 'f', 'c6' => '5', 'c7' => 'e',
    'd1' => 'c', 'd2' => '5', 'd3' => 'h', 'd4' => 'p', 'd5' => '4', 'd6' => 'j', 'd7' => '2',
    'e1' => 'w', 'e2' => 'y', 'e3' => 'm', 'e4' => 'm', 'e5' => 'h', 'e6' => 'v', 'e7' => 'y',
    'f1' => '6', 'f2' => '9', 'f3' => 'h', 'f4' => '6', 'f5' => 'r', 'f6' => 'd', 'f7' => '7',
    'g1' => '1', 'g2' => '5', 'g3' => 'v', 'g4' => 'q', 'g5' => '1', 'g6' => 'p', 'g7' => 'c',
);


$step1 = new GuzzleHttp\Client();
$res = $step1->request('POST', $baseAuthUrl . '/auth', [
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

echo '<br><br>Step 3: Getting the challenges array:<br>';
$step3 = new GuzzleHttp\Client();
$res = $step3->request('GET', $baseAuthUrl . '/vtos', [
    'headers' => [
        'X-AUTH-TOKEN' => $authToken->token,
    ]
]);
echo $res->getBody();

$parsedResponse = json_decode($res->getBody());
$first = strtolower($parsedResponse->challenges[0]);
$second = strtolower($parsedResponse->challenges[1]);
$third = strtolower($parsedResponse->challenges[2]);
echo "<br><br>Step 4: POST the values from the the challenges array requested (to the auth API): <strong>($accessMatrix[$first], $accessMatrix[$second], $accessMatrix[$third])</strong><br>";
$step4 = new GuzzleHttp\Client();
$res = $step4->request('POST', $baseAuthUrl . '/vtos/auth', [
//$res = $step4->request('POST', 'https://httpbin.org/post', [
    'headers' => [
        'X-AUTH-TOKEN' => $authToken->token,
    ],
    'json' => [
        'codes' => "$accessMatrix[$first],$accessMatrix[$second],$accessMatrix[$third]"
    ]
]);
echo 'Response body:<br>';
echo $res->getBody();
