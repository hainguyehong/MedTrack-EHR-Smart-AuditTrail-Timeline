$(document).ready(function(e) {

    $('.alphaonly').bind('keyup blur', function() {
        var node = $(this);
        node.val(node.val().replace(/[^a-z A-Z ]/g, ''));
    });


    $('.integeronly').keypress(function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }

    });


});


function showMenuSelected(menuId, pageId) {

    $(menuId).addClass("menu-open");

    if (pageId !== '') {
        $(pageId).addClass("active");
    }

    $(menuId).children().first().addClass("active");

}


function showCustomMessage(msg, type = "info", title = "Thông báo") { // hàm thông báo bằng popup
    // type: "success" | "error" | "warning" | "info"
    if (typeof window.Swal === "function") {
        Swal.fire({
            title: title,
            text: msg,
            icon: type,
            confirmButtonText: "OK",
            timer: type === "success" ? 1000 : undefined,
            timerProgressBar: type === "success",
        });
        return;
    }

    // fallback nếu chưa có Swal
    alert(msg);
}
