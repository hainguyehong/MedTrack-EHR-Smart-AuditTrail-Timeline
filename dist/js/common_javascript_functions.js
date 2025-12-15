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


// function showCustomMessage(msg, type = "info", title = "Thông báo") { // hàm thông báo bằng popup
//     // type: "success" | "error" | "warning" | "info"
//     if (typeof window.Swal === "function") {
//         Swal.fire({
//             title: title,
//             text: msg,
//             icon: type,
//             // confirmButtonText: "OK",
//             timer: type === "success" ? 1200 : undefined,
//             timerProgressBar: type === "success",
//         });
//         return;
//     }

//     // fallback nếu chưa có Swal
//     alert(msg);
// }

function showCustomMessage(msg, type = "info", title = "") {
    if (typeof window.Swal === "function") {

        // ✅ THÀNH CÔNG: KHÔNG NÚT OK, TỰ ĐÓNG
        if (type === "success") {
            Swal.fire({
                icon: "success",
                title: title || msg,
                showConfirmButton: false, // ❌ KHÔNG OK
                timer: 1500,
                timerProgressBar: true
            });
        }
        // ❌ LỖI / CẢNH BÁO / THÔNG TIN: CÓ NÚT OK
        else {
            Swal.fire({
                icon: type,
                title: title || "Thông báo",
                text: msg,
                confirmButtonText: "OK"
            });
        }

        return;
    }

    alert(msg);
}

