<?php
require_once 'vendor/autoload.php';
// include_once __DIR__ . '/common_service/env_loader.php';

use Infobip\Api\SmsApi;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;

/**
 * Chuyển số điện thoại sang định dạng quốc tế (+84)
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
 * Gửi SMS qua Infobip
 * @param string $phone  Số điện thoại nội địa hoặc quốc tế
 * @param string $message Nội dung SMS
 * @param string $country 'VN' hoặc 'OTHER'
 * @return true nếu gửi thành công, chuỗi lỗi nếu thất bại
 */
function sendSMS($phone, $message, $country = 'VN') {
    // Chuyển số sang định dạng quốc tế
    $phoneIntl = convertToInternational($phone, $country);

    if (empty($phoneIntl)) return "Số điện thoại không hợp lệ hoặc rỗng";

    $base_url = "jj1ken.api.infobip.com";   
    $api_key  = "42faeaee8ebcfc33505c431d43427e73-4c192c90-8cfd-4078-995d-0e9c9f8fbe66";   
    
    
    if (empty($base_url) || empty($api_key)) {
        return "Thiếu cấu hình Infobip (base_url hoặc api_key)";
    }

    // ⚙️ Tạo cấu hình SDK
    $config = new Configuration(
        host: $base_url,
        apiKey: $api_key
    );

    $api = new SmsApi(config: $config);

    // ⚙️ Tạo nội dung tin nhắn
    $destination = new SmsDestination(to: $phoneIntl);
    $message_obj = new SmsTextualMessage(
        destinations: [$destination],
        text: $message,
        from: "MedTrack" // hiển thị ở đầu SMS (nếu được phép)
    );

    $request = new SmsAdvancedTextualRequest(messages: [$message_obj]);

    try {
        $response = $api->sendSmsMessage($request);

        // 📝 Ghi log gửi thành công
        file_put_contents(
            'infobip_sms_log.txt',
            date('Y-m-d H:i:s') . " | $phoneIntl | $message | SUCCESS\n",
            FILE_APPEND
        );

        return true;
    } catch (Exception $e) {
        // 🧾 Ghi log lỗi
        file_put_contents(
            'infobip_sms_log.txt',
            date('Y-m-d H:i:s') . " | $phoneIntl | $message | Error: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
        return $e->getMessage();
    }
}