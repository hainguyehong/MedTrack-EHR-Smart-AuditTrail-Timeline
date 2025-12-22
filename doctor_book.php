<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
islogin([2]);  // bác sĩ (2) truy cập

// --- Lấy dữ liệu đặt lịch từ DB (kèm trạng thái mới nhất từ bảng appointment_status_log) ---
try {
  $sql = "SELECT 
            b.id, 
            b.id_patient, 
            b.date_visit, 
            b.time_visit, 
            b.trieu_chung, 
            b.noi_dung_kham, 
            b.created_at,
            -- lấy thông tin bệnh nhân qua user_patients + patients
            p.patient_name,
            p.phone_number,
            COALESCE(s.status, 'pending') AS current_status,
            s.doctor_note
          FROM book AS b
          JOIN user_patients AS up 
            ON b.id_patient = up.id
          JOIN patients AS p 
            ON up.id_patient = p.id
          LEFT JOIN appointment_status_log AS s
            ON s.id = (
              SELECT MAX(id) 
              FROM appointment_status_log 
              WHERE book_id = b.id
            )
          WHERE b.is_deleted = 0
          ORDER BY 
            TIMESTAMP(b.date_visit, b.time_visit) DESC,
            b.created_at DESC";

  $stmtBookings = $con->prepare($sql);
  $stmtBookings->execute();
  $rows = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);

  $appointmentsForJs = [];
  foreach ($rows as $r) {
    $appointmentsForJs[] = [
      'id'              => (int) $r['id'],
      'patientName'     => $r['patient_name'],
      'appointmentDate' => !empty($r['date_visit']) ? date('d/m/Y', strtotime($r['date_visit'])) : '',
      'appointmentTime' => $r['time_visit'],
      'symptoms'        => $r['trieu_chung'],
      'location'        => $r['noi_dung_kham'],
      'status'          => $r['current_status'] ?? 'pending',
      'bookingDate'     => !empty($r['created_at']) ? date('d/m/Y H:i', strtotime($r['created_at'])) : '',
      'phone'           => $r['phone_number'],
      'doctorNote'      => $r['doctor_note'] ?? ''
    ];
  }
  $appointmentsJson = json_encode($appointmentsForJs, JSON_UNESCAPED_UNICODE);
} catch (Exception $ex) {
  $appointmentsJson = '[]';
}

// Lấy message từ session (nếu có) để show toast
$message = '';
if (isset($_SESSION['success_message'])) {
  $message = $_SESSION['success_message'];
  unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
  $message = $_SESSION['error_message'];
  unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php'; ?>
    <!-- Tailwind (play CDN) cho UI React -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- React / ReactDOM UMD + Babel -->
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Xác nhận lịch khám - MedTrack</title>
    <!-- favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <style>
    * {
    font-family: sans-serif;
}
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" style="background: #f8fafc;">
    <div class="wrapper">
        <?php
    include './config/header.php';
    include './config/sidebar.php';
    ?>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid px-4 py-6">
                    <!-- React app root -->
                    <div id="doctor-app"></div>
                </div>
            </section>
        </div>

        <?php include './config/footer.php'; ?>

        <!-- script chung (menu, v.v.) -->
        <?php include './config/site_js_links.php'; ?>
        <?php include './config/data_tables_js.php'; ?>
    </div>

    <!-- Helper toast SweetAlert2 -->
    <!-- <script>
    const toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
    });

    if (typeof showMenuSelected === 'function') {
        showMenuSelected("#mnu_dashboard", "");
    }
    </script> -->

    <!-- React component (JSX) -->
    <script type="text/babel">
        // dữ liệu từ PHP
    const initialAppointments = <?php echo $appointmentsJson; ?>;

    const { useState } = React;

    function DoctorAppointmentConfirmation() {
      const [appointments, setAppointments] = useState(initialAppointments);
      const [selectedAppointment, setSelectedAppointment] = useState(null);
      const [filterStatus, setFilterStatus] = useState("all");
      const [doctorNote, setDoctorNote] = useState("");
      const [searchTerm, setSearchTerm] = useState("");
      const PAGE_SIZE = 5;

      const [currentPage, setCurrentPage] = useState(1);

      // API lưu trạng thái lịch hẹn
      const saveStatusToServer = async (id, action, note) => {
        const res = await fetch("doctor_appointment_action.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            id,
            action,
            note,
          }),
        });

        const data = await res.json();
        if (!data.success) {
          throw new Error(data.message || "Không thể lưu trạng thái");
        }
        return data;
      };

      // const handleConfirm = async (id) => {
      //   try {
      //     await saveStatusToServer(id, "confirmed", doctorNote);

      //     setAppointments(
      //       appointments.map((apt) =>
      //         apt.id === id ? { ...apt, status: "confirmed", doctorNote } : apt
      //       )
      //     );

      //     setSelectedAppointment(null);
      //     setDoctorNote("");

      //    toast.fire({
      //       icon: "success",
      //       title: "Đã xác nhận lịch khám",
      //     }); 
      //   } catch (err) {
      //     Swal.fire({
      //       icon: "error",
      //       title: "Lỗi",
      //       text: err.message,
      //     });
      //   }
      // };
      const handleConfirm = async (id) => {
  try {
    await saveStatusToServer(id, "confirmed", doctorNote);

    setAppointments(
      appointments.map((apt) =>
        apt.id === id ? { ...apt, status: "confirmed", doctorNote } : apt
      )
    );

    setSelectedAppointment(null);
    setDoctorNote("");

    Swal.fire({
      icon: "success",
      title: "Đã xác nhận lịch khám",
      timer: 1500,
      showConfirmButton: false,
    });
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Lỗi",
      text: err.message,
      timer: 1500,
      showConfirmButton: false,
    });
  }
};


      // const handleReject = async (id) => {
      //   if (!doctorNote.trim()) {
      //     Swal.fire({
      //       icon: "warning",
      //       title: "Thiếu lý do",
      //       text: "Vui lòng nhập lý do từ chối lịch hẹn.",
      //     });
      //     return;
      //   }
      //   try {
      //     await saveStatusToServer(id, "rejected", doctorNote);

      //     setAppointments(
      //       appointments.map((apt) =>
      //         apt.id === id
      //           ? {
      //               ...apt,
      //               status: "rejected",
      //               rejectionReason: doctorNote,
      //               doctorNote,
      //             }
      //           : apt
      //       )
      //     );

      //     setSelectedAppointment(null);
      //     setDoctorNote("");

      //     toast.fire({
      //       icon: "success",
      //       title: "Đã từ chối lịch khám",
      //     });
      //   } catch (err) {
      //     Swal.fire({
      //       icon: "error",
      //       title: "Lỗi",
      //       text: err.message,
      //     });
      //   }
      // };
const handleReject = async (id) => {
  if (!doctorNote.trim()) {
    Swal.fire({
      icon: "warning",
      title: "Thiếu lý do",
      text: "Vui lòng nhập lý do từ chối lịch hẹn.",
      timer: 1500,
      showConfirmButton: false,
    });
    return;
  }

  try {
    await saveStatusToServer(id, "rejected", doctorNote);

    setAppointments(
      appointments.map((apt) =>
        apt.id === id
          ? { ...apt, status: "rejected", doctorNote }
          : apt
      )
    );

    setSelectedAppointment(null);
    setDoctorNote("");

    Swal.fire({
      icon: "success",
      title: "Đã từ chối lịch khám",
      timer: 1500,
      showConfirmButton: false,
    });
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Lỗi",
      text: err.message,
      timer: 1500,
      showConfirmButton: false,
    });
  }
};

      // const handleSetPending = async (id) => {
      //   try {
      //     await saveStatusToServer(id, "pending", doctorNote);

      //     setAppointments(
      //       appointments.map((apt) =>
      //         apt.id === id ? { ...apt, status: "pending", doctorNote } : apt
      //       )
      //     );

      //     setSelectedAppointment(null);
      //     setDoctorNote("");

      //     toast.fire({
      //       icon: "success",
      //       title: "Đã đưa lịch về trạng thái chờ xác nhận",
      //     });
      //   } catch (err) {
      //     Swal.fire({
      //       icon: "error",
      //       title: "Lỗi",
      //       text: err.message,
      //     });
      //   }
      // };
      const handleSetPending = async (id) => {
  try {
    await saveStatusToServer(id, "pending", doctorNote);

    setAppointments(
      appointments.map((apt) =>
        apt.id === id ? { ...apt, status: "pending", doctorNote } : apt
      )
    );

    setSelectedAppointment(null);
    setDoctorNote("");

    Swal.fire({
      icon: "success",
      title: "Đã đưa lịch về trạng thái chờ xác nhận",
      timer: 1500,
      showConfirmButton: false,
    });
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Lỗi",
      text: err.message,
      timer: 1500,
      showConfirmButton: false,
    });
  }
};


      // lọc + tìm kiếm
      const filteredAppointments = appointments.filter((apt) => {
        // lọc theo trạng thái
        if (filterStatus !== "all" && apt.status !== filterStatus) return false;

        // không tìm kiếm thì cho qua
        if (!searchTerm.trim()) return true;

        const keyword = searchTerm.toLowerCase();

        return (
          (apt.patientName || "").toLowerCase().includes(keyword) ||
          (apt.phone || "").toLowerCase().includes(keyword) ||
          (apt.symptoms || "").toLowerCase().includes(keyword) ||
          (apt.location || "").toLowerCase().includes(keyword) ||
          (apt.appointmentDate || "").toLowerCase().includes(keyword)
        );
      });

      const totalPages = Math.ceil(filteredAppointments.length / PAGE_SIZE);

      const currentAppointments = filteredAppointments.slice(
        (currentPage - 1) * PAGE_SIZE,
        currentPage * PAGE_SIZE
      );

      const getStatusBadge = (status) => {
        switch (status) {
          case "pending":
            return (
              <span className="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                Chờ xác nhận
              </span>
            );
          case "confirmed":
            return (
              <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                Đã xác nhận
              </span>
            );
          case "rejected":
            return (
              <span className="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                Đã từ chối
              </span>
            );
          default:
            return null;
        }
      };

      const pendingCount = appointments.filter(
        (apt) => apt.status === "pending"
      ).length;

      return (
        <div className="min-h-screen bg-gray-50">
          {/* header */}
          <div className="bg-white shadow-sm border-b">
            <div className="max-w-7xl mx-auto px-4 py-4">
              <div className="flex items-center justify-between">
                <div>
                  <h1 className="text-2xl font-bold text-gray-800">
                    Quản lý Lịch khám
                  </h1>
                  <p className="text-gray-600 mt-1">
                    Xác nhận và quản lý lịch hẹn của bệnh nhân
                  </p>
                </div>
                <div className="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg">
                  <i className="fa fa-clock text-blue-600 w-5 h-5"></i>
                  <span className="text-blue-600 font-semibold">
                    {pendingCount} lịch chờ xác nhận
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* nội dung chính */}
          <div className="max-w-7xl mx-auto px-4 py-6">
            {/* filter + search */}
            <div className="bg-white rounded-lg shadow-sm p-4 mb-6">
              <div className="flex flex-col lg:flex-row lg:items-center gap-4">
                <div className="flex items-center gap-2">
                  <i className="fa fa-filter text-gray-500 w-5 h-5"></i>
                  <div className="flex gap-2 flex-wrap">
                    <button
                      onClick={() => {
                        setFilterStatus("all");
                        setCurrentPage(1);
                      }}
                      className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                        filterStatus === "all"
                          ? "bg-blue-500 text-white"
                          : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                      }`}
                    >
                      Tất cả ({appointments.length})
                    </button>

                    <button
                      onClick={() => {
                        setFilterStatus("pending");
                        setCurrentPage(1);
                      }}
                      className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                        filterStatus === "pending"
                          ? "bg-yellow-500 text-white"
                          : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                      }`}
                    >
                      Chờ xác nhận ({pendingCount})
                    </button>

                    <button
                      onClick={() => {
                        setFilterStatus("confirmed");
                        setCurrentPage(1);
                      }}
                      className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                        filterStatus === "confirmed"
                          ? "bg-green-500 text-white"
                          : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                      }`}
                    >
                      Đã xác nhận (
                      {
                        appointments.filter(
                          (apt) => apt.status === "confirmed"
                        ).length
                      }
                      )
                    </button>

                    <button
                      onClick={() => {
                        setFilterStatus("rejected");
                        setCurrentPage(1);
                      }}
                      className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                        filterStatus === "rejected"
                          ? "bg-red-500 text-white"
                          : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                      }`}
                    >
                      Đã từ chối (
                      {
                        appointments.filter(
                          (apt) => apt.status === "rejected"
                        ).length
                      }
                      )
                    </button>
                  </div>
                </div>

                {/* thanh tìm kiếm */}
                <div className="flex-1">
                  <div className="relative">
                    <span className="absolute inset-y-0 left-0 flex items-center pl-3">
                      <i className="fa fa-search text-gray-400"></i>
                    </span>
                    <input
                      type="text"
                      value={searchTerm}
                      onChange={(e) => {
                        setSearchTerm(e.target.value);
                        setCurrentPage(1);
                      }}
                      placeholder="Tìm theo tên, SĐT, triệu chứng, lý do, ngày khám..."
                      className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    />
                  </div>
                </div>
              </div>
            </div>

            {/* danh sách lịch hẹn */}
            <div className="grid gap-4">
              {currentAppointments.map((appointment) => (
                <div
                  key={appointment.id}
                  className="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow"
                >
                  <div className="p-6">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center gap-3 mb-3">
                          <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i className="fa fa-user text-blue-600"></i>
                          </div>
                          <div>
                            <h3 className="text-lg font-semibold text-gray-800">
                              {appointment.patientName}
                            </h3>
                            <p className="text-sm text-gray-500">
                              SĐT: {appointment.phone}
                            </p>
                          </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                          <div className="flex items-center gap-2">
                            <i className="fa fa-calendar text-gray-400"></i>
                            <div>
                              <p className="text-sm text-gray-500">
                                Ngày khám
                              </p>
                              <p className="font-medium text-gray-800">
                                {appointment.appointmentDate} -{" "}
                                {appointment.appointmentTime}
                              </p>
                            </div>
                          </div>

                          <div className="flex items-center gap-2">
                            <i className="fa fa-file-alt text-gray-400"></i>
                            <div>
                              <p className="text-sm text-gray-500">
                                Lý do khám
                              </p>
                              <p className="font-medium text-gray-800">
                                {appointment.location}
                              </p>
                            </div>
                          </div>
                        </div>

                        <div className="bg-gray-50 rounded-lg p-4 mb-4">
                          <p className="text-sm text-gray-500 mb-1">
                            Triệu chứng / Lý do khám:
                          </p>
                          <p className="text-gray-800 whitespace-pre-wrap">
                            {appointment.symptoms}
                          </p>
                        </div>

                        {appointment.status === "rejected" &&
                          appointment.doctorNote && (
                            <div className="bg-red-50 rounded-lg p-4 mb-4 border border-red-100">
                              <p className="text-sm text-red-600 font-medium mb-1">
                                Lý do từ chối:
                              </p>
                              <p className="text-red-700 whitespace-pre-wrap">
                                {appointment.doctorNote}
                              </p>
                            </div>
                          )}

                        <div className="flex items-center gap-4 text-sm text-gray-500 flex-wrap">
                          <span>Đặt lúc: {appointment.bookingDate}</span>
                          {getStatusBadge(appointment.status)}
                        </div>
                      </div>

                      <div className="ml-6 flex flex-col gap-2">
                        <button
                          onClick={() => {
                            setSelectedAppointment(appointment);
                            setDoctorNote(appointment.doctorNote || "");
                          }}
                          className={`px-6 py-2 rounded-lg transition-colors font-medium flex items-center gap-2 ${
                            appointment.status === "pending"
                              ? "bg-blue-500 hover:bg-blue-600 text-white"
                              : "bg-purple-500 hover:bg-purple-600 text-white"
                          }`}
                        >
                          <i className="fa fa-file-alt w-4 h-4"></i>
                          {appointment.status === "pending"
                            ? "Xem chi tiết"
                            : "Cập nhật trạng thái"}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}

              {filteredAppointments.length === 0 && (
                <div className="bg-white rounded-lg shadow-sm p-12 text-center">
                  <i className="fa fa-calendar fa-4x text-gray-300 mx-auto mb-4"></i>
                  <p className="text-gray-500 text-lg">
                    Không có lịch hẹn nào
                  </p>
                </div>
              )}
            </div>
          </div>

          {/* phân trang */}
          {filteredAppointments.length > 0 && (
            <div className="flex items-center justify-center gap-2 mt-4 flex-wrap">
              <button
                onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
                disabled={currentPage === 1}
                className={`px-3 py-1 rounded-lg border text-sm ${
                  currentPage === 1
                    ? "bg-gray-100 text-gray-400 cursor-not-allowed"
                    : "bg-white text-gray-700 hover:bg-gray-100"
                }`}
              >
                Trước
              </button>

              {Array.from({ length: totalPages }, (_, idx) => idx + 1).map(
                (page) => (
                  <button
                    key={page}
                    onClick={() => setCurrentPage(page)}
                    className={`px-3 py-1 rounded-lg border text-sm ${
                      currentPage === page
                        ? "bg-blue-500 text-white border-blue-500"
                        : "bg-white text-gray-700 hover:bg-gray-100"
                    }`}
                  >
                    {page}
                  </button>
                )
              )}

              <button
                onClick={() =>
                  setCurrentPage((p) => Math.min(totalPages, p + 1))
                }
                disabled={currentPage === totalPages || totalPages === 0}
                className={`px-3 py-1 rounded-lg border text-sm ${
                  currentPage === totalPages || totalPages === 0
                    ? "bg-gray-100 text-gray-400 cursor-not-allowed"
                    : "bg-white text-gray-700 hover:bg-gray-100"
                }`}
              >
                Sau
              </button>
            </div>
          )}

          {/* modal chi tiết / chỉnh sửa, CSS đã sửa cho gọn & giữa màn hình */}
          {selectedAppointment && (
            <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
              <div className="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[80vh] flex flex-col overflow-hidden">
                {/* header modal */}
                <div className="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                  <h2 className="text-2xl font-bold">Xác nhận Lịch khám</h2>
                  <p className="text-blue-100 mt-1">
                    Vui lòng xem xét và cập nhật trạng thái lịch hẹn
                  </p>
                </div>

                {/* body modal cuộn riêng */}
                <div className="p-6 overflow-y-auto">
                  <div className="space-y-4 mb-6">
                    <div className="flex items-center gap-3 pb-4 border-b">
                      <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i className="fa fa-user text-blue-600 w-8 h-8"></i>
                      </div>
                      <div>
                        <h3 className="text-xl font-bold text-gray-800">
                          {selectedAppointment.patientName}
                        </h3>
                        <p className="text-gray-600">
                          SĐT: {selectedAppointment.phone}
                        </p>
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div className="bg-blue-50 rounded-lg p-4">
                        <p className="text-sm text-blue-600 font-medium mb-1">
                          Ngày khám
                        </p>
                        <p className="text-lg font-semibold text-gray-800">
                          {selectedAppointment.appointmentDate}
                        </p>
                      </div>
                      <div className="bg-blue-50 rounded-lg p-4">
                        <p className="text-sm text-blue-600 font-medium mb-1">
                          Giờ khám
                        </p>
                        <p className="text-lg font-semibold text-gray-800">
                          {selectedAppointment.appointmentTime}
                        </p>
                      </div>
                    </div>

                    <div className="bg-gray-50 rounded-lg p-4">
                      <p className="text-sm text-gray-600 font-medium mb-2">
                        Nơi khám:
                      </p>
                      <p className="text-gray-800 font-medium">
                        {selectedAppointment.location}
                      </p>
                    </div>

                    <div className="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                      <p className="text-sm text-yellow-700 font-medium mb-2">
                        Triệu chứng / Lý do khám:
                      </p>
                      <p className="text-gray-800 whitespace-pre-wrap">
                        {selectedAppointment.symptoms}
                      </p>
                    </div>

                    <div className="bg-gray-50 rounded-lg p-4">
                      <p className="text-sm text-gray-600 mb-2">
                        Thời gian đặt lịch:
                      </p>
                      <p className="text-gray-800 font-medium">
                        {selectedAppointment.bookingDate}
                      </p>
                    </div>
                  </div>

                  <div className="mb-6">
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Ghi chú của bác sĩ (tùy chọn, bắt buộc nếu từ chối):
                    </label>
                    <textarea
                      value={doctorNote}
                      onChange={(e) => setDoctorNote(e.target.value)}
                      placeholder="Nhập ghi chú hoặc lý do từ chối (bắt buộc nếu từ chối)..."
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                      rows="4"
                    />
                  </div>

                  <div className="flex flex-col md:flex-row gap-3">
                    {selectedAppointment.status !== "pending" && (
                      <button
                        onClick={() => handleSetPending(selectedAppointment.id)}
                        className="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-semibold flex items-center justify-center gap-2"
                      >
                        <i className="fa fa-undo w-5 h-5"></i>
                        Đưa về Chờ xác nhận
                      </button>
                    )}

                    <button
                      onClick={() => handleConfirm(selectedAppointment.id)}
                      className="flex-1 bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors font-semibold flex items-center justify-center gap-2"
                    >
                      <i className="fa fa-check-circle w-5 h-5"></i>
                      Xác nhận
                    </button>

                    <button
                      onClick={() => handleReject(selectedAppointment.id)}
                      className="flex-1 bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors font-semibold flex items-center justify-center gap-2"
                    >
                      <i className="fa fa-times-circle w-5 h-5"></i>
                      Từ chối
                    </button>
                  </div>

                  <button
                    onClick={() => {
                      setSelectedAppointment(null);
                      setDoctorNote("");
                    }}
                    className="w-full mt-3 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                  >
                    Đóng
                  </button>
                </div>
              </div>
            </div>
          )}
        </div>
      );
    }

    // mount React
    const rootEl = document.getElementById("doctor-app");
    if (rootEl) {
      const root = ReactDOM.createRoot(rootEl);
      root.render(<DoctorAppointmentConfirmation />);
    }
  </script>

    <!-- Hiển thị message từ session bằng SweetAlert2 toast -->
    <!-- <script>
    (function() {
        var message = <?php echo json_encode($message, JSON_UNESCAPED_UNICODE); ?>;
        if (message) {
            toast.fire({
                icon: "info",
                title: message
            });
        }
    })();
    </script> -->
    <script>
(function () {
    var message = <?php echo json_encode($message, JSON_UNESCAPED_UNICODE); ?>;
    if (message) {
        Swal.fire({
            icon: "info",
            title: message,
            timer: 1500,
            showConfirmButton: false
        });
    }
})();
</script>

</body>

</html>