<?php
require_once 'vendor/autoload.php';
include_once __DIR__ . '/common_service/env_loader.php';
use Twilio\Rest\Client;

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
    echo "Số điện thoại quốc tế: " . $phoneIntl . "<br>";
    // exit; // bật để test, tắt khi gửi thật

    if (!$phoneIntl) return "Số điện thoại không hợp lệ hoặc rỗng";

    // Cấu hình Twilio
    $account_sid = getenv('TWILIO_ACCOUNT_SID');
    $auth_token  = getenv('TWILIO_AUTH_TOKEN');
    $from_number = '+15073796690'; // số Twilio đã mua

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