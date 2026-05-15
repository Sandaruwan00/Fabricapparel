<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$transportObj = new Transport();

$vehicleResult = $transportObj->getAllVehicles();

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Vehicle Management</title>
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
                    <a href="driver.php" class="btn btn-outline-info">Drivers</a>
                    <a href="vehicle.php" class="btn btn-outline-dark active">Vehicles</a>
                    <a href="generate-transport-report.php" class="btn btn-outline-warning">Generate Transport Reports</a>
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
                    Vehicle Management
                </h1>
            </div>

            <div class="col-md-4 text-end">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                    Add Vehicle
                </button>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row mt-4">

            <!-- LEFT SIDE - VEHICLE TABLE -->
            <div class="col-md-10">
                <!-- <h5 class="text-center fw-bold mb-3">Vehicle List</h5> -->
                <!-- <hr> -->

                <table class="table table-bordered table-hover text-center align-middle" id="vehicletable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Vehicle No</th>
                            <th>Type</th>
                            <th>Capacity (kg)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $totalVehicalCount = 0;
                        $availableVehicalCount = 0;
                        $assignedVehicalCount = 0;
                        $maintenanceVehicalCount = 0;

                        while ($row = $vehicleResult->fetch_assoc()) {

                            $totalVehicalCount++;
                            $status = $row['vehicle_status'];

                            if ($status == 'Available') {
                                $color = "bg-success";
                                $availableVehicalCount++;
                            } elseif ($status == 'Assigned') {
                                $color = "bg-warning text-dark";
                                $assignedVehicalCount++;
                            } else {
                                $color = "bg-danger";
                                $maintenanceVehicalCount++;
                            }
                        ?>
                            <tr>
                                <td><?php echo $row['vehicle_id']; ?></td>
                                <td><?php echo $row['vehicle_number']; ?></td>
                                <td><?php echo $row['vehicle_type']; ?></td>
                                <td><?php echo $row['vehicle_capacity']; ?></td>
                                <td class="<?php echo $color; ?>">
                                    <?php echo $status; ?>
                                </td>
                                <td class="text-start">
                                    <a href="#" class="btn btn-info btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewVehicleModal"
                                        onclick="loadViewVehicle('<?php echo $row['vehicle_id']; ?>');">

                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="#" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editVehicle"
                                        onclick="loadEditVehicle('<?php echo $row['vehicle_id']; ?>','<?php echo $row['vehicle_number']; ?>','<?php echo $row['vehicle_type']; ?>','<?php echo $row['vehicle_capacity']; ?>');">

                                        <i class="bi bi-pencil"></i> Edit
                                    </a>



                                    <?php if ($status == 'Available') { ?>
                                        <a href="../controller/transport_controller.php?status=set_maintenance&vehicle_id=<?php echo $row['vehicle_id']; ?>" class="btn btn-danger btn-sm">
                                            <i class="bi bi-tools"></i> Set Maintenance
                                        </a>
                                    <?php } ?>

                                    <?php if ($status == 'Maintenance') { ?>
                                        <a href="../controller/transport_controller.php?status=set_available_vehicle&vehicle_id=<?php echo $row['vehicle_id']; ?>" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-circle"></i> Set Available
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

            <!-- RIGHT SIDE - SUMMARY -->
            <div class="col-md-2">
                <div class="card shadow-lg p-3">

                    <h5 class="fw-bold mb-3 text-center">Vehicle Summary</h5>

                    <div class="row text-center">

                        <!-- Total Vehicles -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Total Vehicles</div>
                                <div class="card-body">
                                    <h3><?php echo $totalVehicalCount; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Available -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Available</div>
                                <div class="card-body">
                                    <h3 class="text-success"><?php echo $availableVehicalCount; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Assigned</div>
                                <div class="card-body">
                                    <h3 class="text-warning"><?php echo $assignedVehicalCount; ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance -->
                        <div class="col-md-12 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Maintenance</div>
                                <div class="card-body">
                                    <h3 class="text-danger"><?php echo $maintenanceVehicalCount; ?></h3>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>


    </div>










    <div class="row">&nbsp;</div>

    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addVehicleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST" action="../controller/transport_controller.php?status=add_vehicle">

                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Add Vehicle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control" placeholder="ABC1234" required>
                        </div>

                        <div class="mb-3">
                            <label>Vehicle Type</label>
                            <select name="vehicle_type" class="form-control" required>
                                <option value="">--Select Vehicle Type--</option>
                                <option value="Van">Van</option>
                                <option value="Truck">Truck</option>
                                <option value="Lorry">Lorry</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Capacity (kg)</label>
                            <input type="number" name="vehicle_capacity" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="addVehicle" class="btn btn-primary">
                            Save Vehicle
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <!-- edit vehicle -->
    <div class="modal fade" id="editVehicle" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST" action="../controller/transport_controller.php?status=update_vehicle">

                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Edit Vehicle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <input type="hidden" name="vehicle_id" id="vehicle_id">
                            <label>Vehicle Number</label>
                            <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Vehicle Type</label>
                            <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                <option value="">--Select Vehicle Type--</option>
                                <option value="Van">Van</option>
                                <option value="Truck">Truck</option>
                                <option value="Lorry">Lorry</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Capacity (kg)</label>
                            <input type="number" name="vehicle_capacity" id="vehicle_capacity" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Update Vehicle
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function loadEditVehicle(vehicle_id, vehicle_number, vehicle_type, vehicle_capacity) {

            document.getElementById("vehicle_id").value = vehicle_id;
            document.getElementById("vehicle_number").value = vehicle_number;
            document.getElementById("vehicle_type").value = vehicle_type;
            document.getElementById("vehicle_capacity").value = vehicle_capacity;

        }
    </script>


    <!-- view vehicle modal -->
    <div class="modal fade" id="viewVehicleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-truck"></i> Vehicle Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div id="display_data">

                </div>

            </div>
        </div>
    </div>

    <script>
        function loadViewVehicle(vehicle_id) {

            var url = "../controller/transport_controller.php?status=load_vehicle";

            $.post(url, {
                vehicle_id: vehicle_id
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
        $("#vehicletable").DataTable();
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