<?php 
if(!(isset($_SESSION['user_id']))) {
  header("location:index.php");
  exit;
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>

<aside class="main-sidebar elevation-4" style="background: #fff; box-shadow: 3px 0 5px -2px rgba(0,0,0,0.3);">
    <style>
    .nav-sidebar .nav-link {
        color: #222 !important;
        font-weight: 500;
        border-radius: 8px;
        transition: background 0.2s, color 0.2s;
    }

    .nav-sidebar .nav-link .nav-icon,
    .nav-sidebar .nav-link i {
        color: #222 !important;
        transition: color 0.2s;
    }

    .nav-sidebar .nav-link.active,
    .nav-sidebar .nav-link:focus,
    .nav-sidebar .nav-link:hover {
        background: #e7f2ff !important;
        color: #007bff !important;
    }

    .nav-sidebar .nav-link.active .nav-icon,
    .nav-sidebar .nav-link.active i,
    .nav-sidebar .nav-link:focus .nav-icon,
    .nav-sidebar .nav-link:focus i,
    .nav-sidebar .nav-link:hover .nav-icon,
    .nav-sidebar .nav-link:hover i {
        color: #007bff !important;
    }

    .nav-sidebar .nav-treeview .nav-link {
        color: #222 !important;
        font-weight: 400;
        margin-left: 10px;
    }

    .nav-sidebar .nav-treeview .nav-link .nav-icon,
    .nav-sidebar .nav-treeview .nav-link i {
        color: #222 !important;
        transition: color 0.2s;
    }

    .nav-sidebar .nav-treeview .nav-link.active,
    .nav-sidebar .nav-treeview .nav-link:focus,
    .nav-sidebar .nav-treeview .nav-link:hover {
        background: #e7f2ff !important;
        color: #ff006fff !important;
    }

    .nav-sidebar .nav-treeview .nav-link.active .nav-icon,
    .nav-sidebar .nav-treeview .nav-link.active i,
    .nav-sidebar .nav-treeview .nav-link:focus .nav-icon,
    .nav-sidebar .nav-treeview .nav-link:focus i,
    .nav-sidebar .nav-treeview .nav-link:hover .nav-icon,
    .nav-sidebar .nav-treeview .nav-link:hover i {
        color: #007bff !important;
    }

    .brand-link {
        background: linear-gradient(90deg, #e7f2ff 60%, #ebfbff 100%) !important;
        color: #222 !important;
    }

    .user-panel .d-block {
        color: #007bff !important;
    }
    </style>
    <a href="" class="brand-link logo-switch"
        style="display: flex; align-items: center; gap: 12px; justify-content: center;">

        <!-- <img src="assets/images/logoo.png" alt="Logo" style="height: 40px; width: auto; border-radius: 50%;"> -->
         <img src="assets/images/img-tn.png" alt="Logo" style="height: 50px; width: auto; border-radius: 50%;">

        <span
            style="font-size: 1.6rem; font-weight: bold; display: flex; align-items: center; height: 45px;">MedTrack</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="background: #fff;">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3">
            <!-- <div class="image">
                <img src="user_images/<?php echo $_SESSION['profile_picture'];?>" class="img-circle elevation-2"
                    alt="User Image" />
            </div> -->

            <div class="info d-flex flex-column align-items-center justify-content-center">
                <span style="color:#007bff; font-weight:700; font-size:1.08rem; letter-spacing:0.5px; margin-bottom:2px;">
                    <?php
                        $roleLabel = '';
                        if (isset($_SESSION['role'])) {
                            if ($_SESSION['role'] == 1) $roleLabel = 'Quản trị viên';
                            else if ($_SESSION['role'] == 2) $roleLabel = 'Bác sĩ';
                            else if ($_SESSION['role'] == 3) $roleLabel = 'Bệnh nhân';
                        }
                        echo $roleLabel;
                    ?>
                </span>
                <a href="#" class="d-block" style="font-weight: 600; font-size: 1.08rem; color:#222;">
                    <?php echo htmlspecialchars($_SESSION['display_name']); ?>
                </a>

            </div>
        </div>


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"
                style="background: #fff;">
                <li class="nav-item" id="mnu_dashboard" <?php if($role == 3) echo 'style="display:none;"'; ?>>
                    <a href="dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Thống kê
                        </p>
                    </a>
                </li>
                <!-- check role 3: BNhan -->
                <?php if($role == 3): ?>
                <li class="nav-item" id="mnu_medical_record">
                    <a href="user_medication.php" class="nav-link">
                        <i class="nav-icon fas fa-notes-medical"></i>
                        <p>
                            Bệnh án
                        </p>
                    </a>
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-notes-medical"></i>
                        <p>
                            Đặt lịch khám lại
                        </p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if($role != 3): ?>
                <li class="nav-item" id="mnu_patients">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-injured"></i>
                        <p>
                            <i class="fas"></i>
                            Bệnh Nhân
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="patients.php" class="nav-link" id="mi_patientss">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thêm bệnh nhân</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="patients_visit.php" class="nav-link" id="mi_new_prescription">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Khám bệnh</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="doctor_patient.php" class="nav-link" id="mi_doctor_patient">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quản lý bệnh nhân</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item" id="mnu_medicines">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-pills"></i>
                        <p>
                            Các loại thuốc
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="medicines.php" class="nav-link" id="mi_medicines">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Chi tiết thuốc </p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="medicine_details.php" class="nav-link" id="mi_medicine_details">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Chi tiết thuốc</p>
                            </a>

                        </li> -->

                    </ul>
                </li>
                <li class="nav-item" id="mnu_reports">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>
                            Báo Cáo
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="reports.php" class="nav-link" id="mi_reports">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Báo cáo</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php if($role == 1): ?>

                <li class="nav-item" id="mnu_users">
                    <a href="users.php" class="nav-link">
                        <i class="nav-icon fa fa-users"></i>
                        <p>
                            Người dùng
                        </p>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fa fa-sign-out-alt"></i>
                        <p>
                            Đăng xuất
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<script>
// Highlight menu/submenu when active
document.addEventListener('DOMContentLoaded', function() {
    // Lấy pathname hiện tại
    var path = window.location.pathname.split('/').pop();

    // Map file sang id menu con
    var map = {
        'patients.php': 'mi_patientss',

        'patients_visit.php': 'mi_new_prescription',
        'doctor_patient.php': 'mi_doctor_patient'
    };

    // Nếu là 1 trong các trang con thì active cả menu cha và con
    if (map[path]) {
        var sub = document.getElementById(map[path]);
        if (sub) {
            sub.classList.add('active');
            // Active menu cha
            var parent = document.getElementById('mnu_patients');
            if (parent) {
                parent.querySelector('.nav-link').classList.add('active');
            }
        }
    }
    // Nếu không phải trang con, loại bỏ active khỏi các menu con
    else {
        Object.values(map).forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.classList.remove('active');
        });
    }
});
</script>