
$(function() {
    moment.locale('vi');
    $('#visit_date').daterangepicker({
        singleDatePicker: true,       // chỉ chọn 1 ngày
        showDropdowns: true,          // hiển thị dropdown chọn tháng/năm
        autoApply: true,              // chọn xong tự đóng
        locale: {
            format: 'DD/MM/YYYY',     // định dạng ngày
            separator: ' - ',
            applyLabel: 'Áp dụng',
            cancelLabel: 'Hủy',
            fromLabel: 'Từ',
            toLabel: 'Đến',
            customRangeLabel: 'Tùy chọn',
            weekLabel: 'Tuần',
            daysOfWeek: [
                'CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'
            ],
            monthNames: [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ],
            firstDay: 1
        }
    });
});

