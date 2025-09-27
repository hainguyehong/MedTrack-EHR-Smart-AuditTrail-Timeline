<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- JS của Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.setupSelect2').select2({
        placeholder: "Chọn bệnh nhân hoặc tìm kiếm theo tên ",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Không có bệnh nhân";
            }
        }
    });
});
</script>