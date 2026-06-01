<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$transportObj = new Transport();

$driverResult = $transportObj->getAllDrivers();

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Driver Management</title>
</head>

<body style="border-radius:10px;">
    <div class="container">

        <?php $pageName = "TRANSPORT MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Top Buttons -->
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="transport.php" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-transport.php" class="btn btn-outline-primary">Create Transport</a>
                    <a href="view-transports.php" class="btn btn-outline-success">View Transports</a>
                    <a href="driver.php" class="btn btn-outline-info active">Drivers</a>
                    <a href="vehicle.php" class="btn btn-outline-dark">Vehicles</a>
                    <a href="generate-driver-report.php" class="btn btn-outline-warning">Generate Transport Reports</a>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row align-items-center">
            <div class="col-md-4"></div>

            <div class="col-md-4 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Driver Management
                </h1>
            
            </div>

            <div class="col-md-4 text-end">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDriverModal">
                    Add Driver
                </button>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row mt-4">

  
            <div class="col-md-10">
                <!-- <h5 class="text-center fw-bold mb-3">Driver List</h5>
                <hr> -->

                <table class="table table-bordered table-hover text-center align-middle" id="drivertable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>NIC</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $totalDriverCount = 0;
                        $availableDriverCount = 0;
                        $assignedDriverCount = 0;
                        $deactiveDriverCount = 0;

                        while ($row = $driverResult->fetch_assoc()) {

                            $totalDriverCount++;
                            $status = $row['driver_status'];

                            if ($status == 'Available') {
                                $color = "bg-success";
                                $availableDriverCount++;
                            } elseif ($status == 'Assigned') {
                                $color = "bg-warning text-dark";
                                $assignedDriverCount++;
                            } else {
                                $color = "bg-danger";
                                $deactiveDriverCount++;
                            }
                        ?>
                            <tr>
                                <td><?php echo $row['driver_id']; ?></td>
                                <td class="text-start"><?php echo $row['driver_name']; ?></td>
                                <td><?php echo $row['driver_nic']; ?></td>
                                <td><?php echo $row['driver_phone']; ?></td>
                                <td class="<?php echo $color; ?>">
                                    <?php echo $status; ?>
                                </td>
                                <td class="text-start">
                                    <a href="#" class="btn btn-info btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewDriverModal"
                                        onclick="loadViewDriver('<?php echo $row['driver_id']; ?>');">

                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="#" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editDriver"
                                        onclick="loadEditDriver('<?php echo $row['driver_id']; ?>','<?php echo $row['driver_name']; ?>','<?php echo $row['driver_nic']; ?>','<?php echo $row['driver_phone']; ?>','<?php echo $row['driver_license_no']; ?>','<?php echo $row['driver_address']; ?>');">

                                        <i class="bi bi-pencil"></i> Edit
                                    </a>



                                    <?php if ($status == 'Available') { ?>
                                        <a href="../controller/transport_controller.php?status=deactivate_driver&driver_id=<?php echo $row['driver_id']; ?>" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-lg"></i> Deactive
                                        </a>
                                    <?php } ?>

                                    <?php if ($status == 'Deactive') { ?>
                                        <a href="../controller/transport_controller.php?status=activate_driver&driver_id=<?php echo $row['driver_id']; ?>" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-lg"></i> Active
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>


            </div>

  
            <div class="col-md-2">
                <div class="card shadow-lg p-3">

                    <h5 class="fw-bold mb-3 text-center">Driver Summary</h5>

                    <div class="row text-center">

                        <!-- Total Drivers -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Total Drivers</div>
                                <div class="card-body">
                                    <h3><?php echo $totalDriverCount; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Available -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Available</div>
                                <div class="card-body">
                                    <h3 class="text-success"><?php echo $availableDriverCount; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Assigned</div>
                                <div class="card-body">
                                    <h3 class="text-warning"><?php echo $assignedDriverCount; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Deactive -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Deactive</div>
                                <div class="card-body">
                                    <h3 class="text-danger"><?php echo $deactiveDriverCount; ?></h3>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>


    </div>










    <div class="row">&nbsp;</div>

    <!-- Add Driver Modal -->
    <div class="modal fade" id="addDriverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg">

            <form method="POST" action="../controller/transport_controller.php?status=add_driver">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                    Add Driver
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Driver Name</label>
                        <input type="text" name="driver_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>NIC</label>
                        <input type="text" name="driver_nic" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="driver_phone" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>License Number</label>
                        <input type="text" name="driver_license_no" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="driver_address" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="addDriver" class="btn btn-primary">
                        Save Driver
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


    <!-- edit driver -->
    <div class="modal fade" id="editDriver" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST" action="../controller/transport_controller.php?status=update_driver">

                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Edit Driver</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <input type="hidden" name="driver_id" id="driver_id">
                            <label>Driver Name</label>
                            <input type="text" name="driver_name" id="driver_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>NIC</label>
                            <input type="text" name="driver_nic" id="driver_nic" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="driver_phone" id="driver_phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>License Number</label>
                            <input type="text" name="driver_license_no" id="driver_license_no" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="driver_address" id="driver_address" class="form-control" required></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Update Driver
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function loadEditDriver(driver_id, driver_name, driver_nic, driver_phone,driver_license_no, driver_address) {

            document.getElementById("driver_id").value = driver_id;
            document.getElementById("driver_name").value = driver_name;
            document.getElementById("driver_nic").value = driver_nic;
            document.getElementById("driver_phone").value = driver_phone;
            document.getElementById("driver_license_no").value = driver_license_no;
            document.getElementById("driver_address").value = driver_address;

        }
    </script>

    <!-- view driver modal -->
    <div class="modal fade" id="viewDriverModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-circle"></i> Driver Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- CONTENT LOAD HERE -->
            <div id="display_data">
            </div>

        </div>
    </div>
</div>

    <script>
        function loadViewDriver(driver_id) {

            var url = "../controller/transport_controller.php?status=load_driver";

            $.post(url, {
                driver_id: driver_id
            }, function(data) {
                $("#display_data").html(data).show();
            });
        }
    </script>




    <?php include_once '../includes/footer_includes.php'; ?>
</body>

<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    $(document).ready(function() {
        $("#drivertable").DataTable();
    });
</script>

<!-- alert start -->
<?php
$msg = "";
if (isset($_GET["msg"])) {
    $msg = base64_decode($_GET["msg"]);
}
?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="msgToast" class="toast align-items-center text-bg-secondary border-0" role="alert" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body" id="toastMsg">
                <!-- Message -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let msg = "<?php echo $msg; ?>";
        if (msg !== "") {
            document.getElementById("toastMsg").innerText = msg;
            let toastEl = document.getElementById("msgToast");
            let toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
</script>
<!-- alert end -->

</html>