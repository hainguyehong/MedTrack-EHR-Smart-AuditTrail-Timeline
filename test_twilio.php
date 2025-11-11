<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/common_service/env_loader.php';

echo "Twilio SID: " . getenv('TWILIO_ACCOUNT_SID') . "<br>";
echo "Twilio Token: " . getenv('TWILIO_AUTH_TOKEN') . "<br>";
echo "Twilio Number: " . getenv('TWILIO_PHONE_NUMBER') . "<br>";

use Twilio\Rest\Client;

$twilio = new Client(getenv('TWILIO_ACCOUNT_SID'), getenv('TWILIO_AUTH_TOKEN'));

$result = $twilio->messages->create(
    '+84362111351',
    [
        'from' => getenv('TWILIO_PHONE_NUMBER'),
        'body' => 'Test SMS'
    ]
);

echo "Message SID: " . $result->sid;