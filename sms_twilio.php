<?php
// Hiển thị tất cả lỗi PHP
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// // Tạm tạo file log nếu muốn
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/php_error.log');    

// echo "PHP đã chạy tới đây.<br>";
?>
<?php

require_once 'vendor/autoload.php';
include_once __DIR__ . '/common_service/env_loader.php';
// echo "TWILIO_ACCOUNT_SID: " . getenv('TWILIO_ACCOUNT_SID') . "<br>";
// echo "TWILIO_AUTH_TOKEN: " . getenv('TWILIO_AUTH_TOKEN') . "<br>";
// echo "TWILIO_PHONE_NUMBER: " . getenv('TWILIO_PHONE_NUMBER') . "<br>";
use Twilio\Rest\Client;
// exit();
/**
 * Chuyển số điện thoại VN sang định dạng quốc tế (+84)
 */
function convertToInternational($phone, $country = 'OTHER') {
    // Loại bỏ ký tự không phải số
    $phone = preg_replace('/\D/', '', $phone);

    if ($country === 'VN' || substr($phone, 0, 1) === '0') {
        // Nếu số bắt đầu bằng 0 → chuyển sang +84
        $phone = '+84' . substr($phone, 1);
    } else {
        // Nếu nước ngoài, thêm + nếu chưa có
        if (substr($phone, 0, 1) !== '+') $phone = '+' . $phone;
    }

    return $phone;
}

/**
 * Gửi SMS qua Twilio
 * @param string $phone Số điện thoại nội địa hoặc quốc tế
 * @param string $message Nội dung SMS
 * @return true nếu gửi thành công, chuỗi lỗi nếu thất bại
 */
function sendSMS($phone, $message, $country = 'VN') {
    // Chuyển số sang định dạng quốc tế
    $phoneIntl = convertToInternational($phone, $country);

    // DEBUG: xem số trước khi gửi
    // echo "Số điện thoại quốc tế: " . $phoneIntl . "<br>";
    // exit; // bật để test, tắt khi gửi thật

    if (!$phoneIntl) return "Số điện thoại không hợp lệ hoặc rỗng";

    // Cấu hình Twilio
    $account_sid = $_ENV['TWILIO_ACCOUNT_SID'];
$auth_token  = $_ENV['TWILIO_AUTH_TOKEN'];
    // $from_number = getenv('TWILIO_PHONE_NUMBER');
    // $account_sid = "ACeade15de74800c85e7718ad24cd0146d";
    // $auth_token  = "46c370a84d737bf691307dd07003655e";
    $from_number = '+15073796690'; // số Twilio đã mua
    // if (!$account_sid || !$auth_token || !$from_number) {
    //     die("Vui lòng cấu hình TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN và TWILIO_PHONE_NUMBER trong .env");
    // }
    $client = new Client($account_sid, $auth_token);

    try {
        $sms = $client->messages->create(
            $phoneIntl,
            [
                'from' => $from_number,
                'body' => $message
            ]
        );

        // Ghi log
        file_put_contents(
            'twilio_sms_log.txt',
            date('Y-m-d H:i:s') . " | $phoneIntl | $message | SID: " . $sms->sid . "\n",
            FILE_APPEND
        );

        return true;
    } catch (\Twilio\Exceptions\TwilioException $e) {
        file_put_contents(
            'twilio_sms_log.txt',
            date('Y-m-d H:i:s') . " | $phoneIntl | $message | Error: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
        return $e->getMessage();
    }
}