<?php 
if(!(isset($_SESSION['user_id']))) {
  header("location:index.php");
  exit;
}
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
    .nav-sidebar .nav-link.active, .nav-sidebar .nav-link:focus, .nav-sidebar .nav-link:hover {
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
        color: #007bff !important;
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
    <a href="" class="brand-link logo-switch" style="display: flex; align-items: center; gap: 12px; justify-content: center;">
        <img src="assets/images/logoo.png" alt="Logo" style="height: 40px; width: auto; border-radius: 50%;">
        <span style="font-size: 1.6rem; font-weight: bold; display: flex; align-items: center; height: 45px;">MedTrack</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="background: #fff;">
        <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3">
            <!-- <div class="image">
                <img src="user_images/<?php echo $_SESSION['profile_picture'];?>" class="img-circle elevation-2"
                    alt="User Image" />
            </div> -->
            <div class="info d-flex justify-content-center">
                <a href="#" class="d-block" style="font-weight: 600; font-size: 1.1rem;"><?php echo $_SESSION['display_name'];?></a>
            </div>
        </div>


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="background: #fff;">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item" id="mnu_dashboard">
                    <a href="dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Thống kê
                        </p>
                    </a>
                </li>


                <li class="nav-item" id="mnu_patients">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-injured"></i>
                        <p>
                            <i class="fas "></i>
                            Bệnh Nhân
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="patients.php" class="nav-link" id="mi_patients">
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
                    <!-- mục Quản lý bệnh nhân của bác sĩ -->
                        <li class="nav-item">
                            <a href="doctor_patient.php" class="nav-link" id="mi_doctor_patient">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quản lý bệnh nhân</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="patient_history.php" class="nav-link" id="mi_patient_history">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tiền sử bệnh nhân</p>
                            </a>
                        </li> -->

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
                                <p>Thêm loại thuốc </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="medicine_details.php" class="nav-link" id="mi_medicine_details">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Chi tiết thuốc</p>
                            </a>
                        </li>

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

                <li class="nav-item" id="mnu_users">
                    <a href="users.php" class="nav-link">
                        <i class="nav-icon fa fa-users"></i>
                        <p>
                            Người dùng
                        </p>
                    </a>

                </li>
                <!-- <li class="nav-item" id="mnu_users">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-users"></i>
                        <p>
                            Người dùng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="users.php" class="nav-link" id="mi_users">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh sách admin, bác sỹ</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="user_patients.php" class="nav-link" id="mi_user_patients">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh sách bệnh nhân</p>
                            </a>
                        </li>

                    </ul>
                </li> -->
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