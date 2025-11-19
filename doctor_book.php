<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
islogin();

// --- Thêm: lấy dữ liệu đặt lịch thực tế từ DB để truyền vào React ---
try {
    $sql = "SELECT b.id, b.id_patient, b.date_visit, b.time_visit, b.trieu_chung, b.noi_dung_kham, b.created_at,
                   p.patient_name, p.phone_number
            FROM book b
            JOIN patients p ON b.id_patient = p.id
            WHERE b.is_deleted = 0
            ORDER BY b.date_visit ASC, b.time_visit ASC";
    $stmtBookings = $con->prepare($sql);
    $stmtBookings->execute();
    $rows = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);

    $appointmentsForJs = [];
    foreach ($rows as $r) {
        $appointmentsForJs[] = [
            'id' => (int)$r['id'],
            'patientName' => $r['patient_name'],
            'appointmentDate' => !empty($r['date_visit']) ? date('d/m/Y', strtotime($r['date_visit'])) : '',
            'appointmentTime' => $r['time_visit'],
            'symptoms' => $r['trieu_chung'],
            'location' => $r['noi_dung_kham'],
            'status' => 'pending', // hiện chưa có cột trạng thái trong book.php -> giữ pending mặc định
            'bookingDate' => !empty($r['created_at']) ? date('d/m/Y H:i', strtotime($r['created_at'])) : '',
            'phone' => $r['phone_number']
        ];
    }
    $appointmentsJson = json_encode($appointmentsForJs, JSON_UNESCAPED_UNICODE);
} catch (Exception $ex) {
    $appointmentsJson = '[]';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './config/site_css_links.php';?>
    <!-- Tailwind (play CDN) for the React UI classes used -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons used in the component -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- React / ReactDOM UMD builds and Babel for in-browser JSX transpile -->
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <title>Xác nhận lịch khám - MedTrack</title>
    <style>
      /* nhỏ gọn: nếu cần tùy chỉnh style thêm ở đây */
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" style="background: #f8fafc;">
  <div class="wrapper">
    <?php include './config/header.php'; 
    include './config/sidebar.php';?>

    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid px-4 py-6">
          <!-- React app root -->
          <div id="doctor-app"></div>
        </div>
      </section>
    </div>

    <?php include './config/footer.php';?>

    <?php
    // Lấy message từ session (nếu có) để JS hiển thị bằng showCustomMessage
    $message = '';
    if (isset($_SESSION['success_message'])) {
        $message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    } elseif (isset($_SESSION['error_message'])) {
        $message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
    }
    ?>

   <!-- Thêm các script chung giống medicines.php để có hàm showMenuSelected / showCustomMessage -->
   <?php include './config/site_js_links.php'; ?>
   <?php include './config/data_tables_js.php'; ?>

  </div>

  <!-- React component (JSX) transpiled by Babel in browser -->
  <script type="text/babel">
    // thay dữ liệu giả bằng dữ liệu thật từ PHP
    const initialAppointments = <?php echo $appointmentsJson; ?>;

    const { useState } = React;
    function DoctorAppointmentConfirmation() {
      const [appointments, setAppointments] = useState(initialAppointments);
      const [selectedAppointment, setSelectedAppointment] = useState(null);
      const [filterStatus, setFilterStatus] = useState("all");
      const [doctorNote, setDoctorNote] = useState("");

      const handleConfirm = (id) => {
        setAppointments(appointments.map(apt =>
          apt.id === id ? { ...apt, status: "confirmed" } : apt
        ));
        setSelectedAppointment(null);
        setDoctorNote("");
      };

      const handleReject = (id) => {
        if (!doctorNote.trim()) {
          alert("Vui lòng nhập lý do từ chối lịch hẹn");
          return;
        }
        setAppointments(appointments.map(apt =>
          apt.id === id ? { ...apt, status: "rejected", rejectionReason: doctorNote } : apt
        ));
        setSelectedAppointment(null);
        setDoctorNote("");
      };

      const filteredAppointments = appointments.filter(apt => {
        if (filterStatus === "all") return true;
        return apt.status === filterStatus;
      });

      const getStatusBadge = (status) => {
        switch(status) {
          case "pending":
            return <span className="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">Chờ xác nhận</span>;
          case "confirmed":
            return <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Đã xác nhận</span>;
          case "rejected":
            return <span className="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">Đã từ chối</span>;
          default:
            return null;
        }
      };

      const pendingCount = appointments.filter(apt => apt.status === "pending").length;

      return (
        <div className="min-h-screen bg-gray-50">
          <div className="bg-white shadow-sm border-b">
            <div className="max-w-7xl mx-auto px-4 py-4">
              <div className="flex items-center justify-between">
                <div>
                  <h1 className="text-2xl font-bold text-gray-800">Quản lý Lịch khám</h1>
                  <p className="text-gray-600 mt-1">Xác nhận và quản lý lịch hẹn của bệnh nhân</p>
                </div>
                <div className="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg">
                  <i className="fa fa-clock text-blue-600 w-5 h-5"></i>
                  <span className="text-blue-600 font-semibold">{pendingCount} lịch chờ xác nhận</span>
                </div>
              </div>
            </div>
          </div>

          <div className="max-w-7xl mx-auto px-4 py-6">
            <div className="bg-white rounded-lg shadow-sm p-4 mb-6">
              <div className="flex items-center gap-4">
                <i className="fa fa-filter text-gray-500 w-5 h-5"></i>
                <div className="flex gap-2">
                  <button
                    onClick={() => setFilterStatus("all")}
                    className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                      filterStatus === "all"
                        ? "bg-blue-500 text-white"
                        : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                    }`}
                  >
                    Tất cả ({appointments.length})
                  </button>
                  <button
                    onClick={() => setFilterStatus("pending")}
                    className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                      filterStatus === "pending"
                        ? "bg-yellow-500 text-white"
                        : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                    }`}
                  >
                    Chờ xác nhận ({pendingCount})
                  </button>
                  <button
                    onClick={() => setFilterStatus("confirmed")}
                    className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                      filterStatus === "confirmed"
                        ? "bg-green-500 text-white"
                        : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                    }`}
                  >
                    Đã xác nhận ({appointments.filter(apt => apt.status === "confirmed").length})
                  </button>
                  <button
                    onClick={() => setFilterStatus("rejected")}
                    className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                      filterStatus === "rejected"
                        ? "bg-red-500 text-white"
                        : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                    }`}
                  >
                    Đã từ chối ({appointments.filter(apt => apt.status === "rejected").length})
                  </button>
                </div>
              </div>
            </div>

            <div className="grid gap-4">
              {filteredAppointments.map((appointment) => (
                <div key={appointment.id} className="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                  <div className="p-6">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center gap-3 mb-3">
                          <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i className="fa fa-user text-blue-600"></i>
                          </div>
                          <div>
                            <h3 className="text-lg font-semibold text-gray-800">{appointment.patientName}</h3>
                            <p className="text-sm text-gray-500">SĐT: {appointment.phone}</p>
                          </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4 mb-4">
                          <div className="flex items-center gap-2">
                            <i className="fa fa-calendar text-gray-400"></i>
                            <div>
                              <p className="text-sm text-gray-500">Ngày khám</p>
                              <p className="font-medium text-gray-800">{appointment.appointmentDate} - {appointment.appointmentTime}</p>
                            </div>
                          </div>

                          <div className="flex items-center gap-2">
                            <i className="fa fa-file-alt text-gray-400"></i>
                            <div>
                              <p className="text-sm text-gray-500">Nơi khám</p>
                              <p className="font-medium text-gray-800">{appointment.location}</p>
                            </div>
                          </div>
                        </div>

                        <div className="bg-gray-50 rounded-lg p-4 mb-4">
                          <p className="text-sm text-gray-500 mb-1">Triệu chứng / Lý do khám:</p>
                          <p className="text-gray-800">{appointment.symptoms}</p>
                        </div>

                        <div className="flex items-center gap-4 text-sm text-gray-500">
                          <span>Đặt lúc: {appointment.bookingDate}</span>
                          {getStatusBadge(appointment.status)}
                        </div>
                      </div>

                      {appointment.status === "pending" && (
                        <div className="ml-6 flex flex-col gap-2">
                          <button
                            onClick={() => setSelectedAppointment(appointment)}
                            className="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium flex items-center gap-2"
                          >
                            <i className="fa fa-file-alt w-4 h-4"></i>
                            Xem chi tiết
                          </button>
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              ))}

              {filteredAppointments.length === 0 && (
                <div className="bg-white rounded-lg shadow-sm p-12 text-center">
                  <i className="fa fa-calendar fa-4x text-gray-300 mx-auto mb-4"></i>
                  <p className="text-gray-500 text-lg">Không có lịch hẹn nào</p>
                </div>
              )}
            </div>
          </div>

          {selectedAppointment && (
            <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
              <div className="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div className="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                  <h2 className="text-2xl font-bold">Xác nhận Lịch khám</h2>
                  <p className="text-blue-100 mt-1">Vui lòng xem xét và xác nhận lịch hẹn</p>
                </div>

                <div className="p-6">
                  <div className="space-y-4 mb-6">
                    <div className="flex items-center gap-3 pb-4 border-b">
                      <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-user text-blue-600 w-8 h-8"></i>
                      </div>
                      <div>
                        <h3 className="text-xl font-bold text-gray-800">{selectedAppointment.patientName}</h3>
                        <p className="text-gray-600">SĐT: {selectedAppointment.phone}</p>
                      </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div className="bg-blue-50 rounded-lg p-4">
                        <p className="text-sm text-blue-600 font-medium mb-1">Ngày khám</p>
                        <p className="text-lg font-semibold text-gray-800">{selectedAppointment.appointmentDate}</p>
                      </div>
                      <div className="bg-blue-50 rounded-lg p-4">
                        <p className="text-sm text-blue-600 font-medium mb-1">Giờ khám</p>
                        <p className="text-lg font-semibold text-gray-800">{selectedAppointment.appointmentTime}</p>
                      </div>
                    </div>

                    <div className="bg-gray-50 rounded-lg p-4">
                      <p className="text-sm text-gray-600 font-medium mb-2">Nơi khám:</p>
                      <p className="text-gray-800 font-medium">{selectedAppointment.location}</p>
                    </div>

                    <div className="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                      <p className="text-sm text-yellow-700 font-medium mb-2">Triệu chứng / Lý do khám:</p>
                      <p className="text-gray-800">{selectedAppointment.symptoms}</p>
                    </div>

                    <div className="bg-gray-50 rounded-lg p-4">
                      <p className="text-sm text-gray-600 mb-2">Thời gian đặt lịch:</p>
                      <p className="text-gray-800 font-medium">{selectedAppointment.bookingDate}</p>
                    </div>
                  </div>

                  <div className="mb-6">
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Ghi chú của bác sĩ (tùy chọn):
                    </label>
                    <textarea
                      value={doctorNote}
                      onChange={(e) => setDoctorNote(e.target.value)}
                      placeholder="Nhập ghi chú hoặc lý do từ chối (bắt buộc nếu từ chối)..."
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                      rows="4"
                    />
                  </div>

                  <div className="flex gap-3">
                    <button
                      onClick={() => handleConfirm(selectedAppointment.id)}
                      className="flex-1 bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors font-semibold flex items-center justify-center gap-2"
                    >
                      <i className="fa fa-check-circle w-5 h-5"></i>
                      Xác nhận Lịch khám
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

    // mount
    const rootEl = document.getElementById('doctor-app');
    if (rootEl) {
      const root = ReactDOM.createRoot(rootEl);
      root.render(<DoctorAppointmentConfirmation />);
    }
  </script>

  <script>
    // Highlight sidebar item for this page (same pattern as medicines.php)
    if (typeof showMenuSelected === 'function') {
      showMenuSelected("#mnu_dashboard", "");
    }

    // Hiển thị message nếu có
    (function(){
      var message = <?php echo json_encode($message, JSON_UNESCAPED_UNICODE); ?>;
      if (message && typeof showCustomMessage === 'function') {
        showCustomMessage(message);
      }
    })();
  </script>
</body>
</html>
