<?php
// Thiết lập múi giờ mặc định cho toàn bộ project
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Hàm lấy thời gian hiện tại (theo định dạng chuẩn)
function getCurrentDateTime($format = "Y-m-d H:i:s") {
    return date($format);
}
?>