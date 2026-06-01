<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$transportObj = new Transport();

$transportResults = $transportObj->getAllTransports();



?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Transports</title>
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
                    <a href="view-transports.php" class="btn btn-outline-success active">View Transports</a>
                    <a href="driver.php" class="btn btn-outline-info">Drivers</a>
                    <a href="vehicle.php" class="btn btn-outline-dark">Vehicles</a>
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
                    View Transports
                </h1>

            </div>

            <div class="col-md-4 text-end">

            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover" id="table">

                    <thead class="table-secondary text-center">
                        <tr>
                            <th width="10%">Transport ID</th>
                            <th width="15%">Delivery Location</th>
                            <th width="10%">Shipment ID</th>
                            <th width="10%">Vehile</th>
                            <th width="22%">Driver</th>
                            <th width="10%">Transport Status</th>
                            <th width="23%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        while ($row = $transportResults->fetch_assoc()) {
                        ?>
                            <tr>
                                <td><?php echo "TRA".$row["transport_id"]; ?></td>
                                <td><?php echo $row["district_name"]; ?></td>
                                <td><?php echo "SHIP".$row["shipment_id"]; ?></td>
                                <td><?php echo $row["vehicle_number"]; ?></td>
                                <td><?php echo "ID:" . $row["driver_id"] . " - " . $row["driver_name"]; ?></td>

                                <?php
                                if ($row["transport_status"] == "Pending") {
                                    $color = "bg-warning";
                                } else if ($row["transport_status"] == "Confirmed") {
                                    $color = "bg-info";
                                } else if ($row["transport_status"] == "Rejected") {
                                    $color = "bg-danger";
                                } else if ($row["transport_status"] == "Started") {
                                    $color = "bg-primary";
                                } else if ($row["transport_status"] == "Delivered") {
                                    $color = "bg-success";
                                } else {
                                    $color = "bg-secondary";
                                }
                                ?>

                                <td class="<?php echo $color; ?> text-center"><?php echo $row["transport_status"]; ?></td>
                                <td>
                                    <a href="#" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewModal" onclick="loadshipment('<?php echo $row['shipment_id']; ?>');">
                                        <i class="bi bi-eye-fill"></i> View
                                    </a>

                                    <?php
                                    if ($row["transport_status"] == "Pending") {
                                    ?>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#confirmModal" onclick="loadtransportconfirm('<?php echo $row['transport_id']; ?>');">
                                            <i class="bi bi-check-lg"></i>
                                            &nbsp
                                            Confirm
                                        </a>

                                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="loadtransportreject('<?php echo $row['transport_id']; ?>','<?php echo $row['shipment_id']; ?>','<?php echo $row['vehicle_id']; ?>','<?php echo $row['driver_id']; ?>');">
                                            <i class="bi bi-x-lg"></i>
                                            &nbsp
                                            Reject
                                        </a>
                                    <?php

                                    }
                                    ?>

                                    <?php
                                    if ($row["transport_status"] == "Confirmed") {
                                    ?>
                                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#startModal" onclick="loadstarttransport('<?php echo $row['transport_id']; ?>','<?php echo $row['shipment_id']; ?>','<?php echo $row['vehicle_id']; ?>','<?php echo $row['driver_id']; ?>');">
                                            <i class="bi bi-play"></i>
                                            Start
                                        </a>
                                    <?php

                                    }
                                    ?>

                                    <?php
                                    if ($row["transport_status"] == "Started") {
                                    ?>
                                        <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#deliveredModal" onclick="loaddeliveredtransport('<?php echo $row['transport_id']; ?>','<?php echo $row['shipment_id']; ?>','<?php echo $row['vehicle_id']; ?>','<?php echo $row['driver_id']; ?>');">
                                            <i class="bi bi-check-circle-fill"></i>&nbsp;
                                            Deliver
                                        </a>
                                    <?php

                                    }
                                    ?>

                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>



    </div>




    </div>

    <!-- view -->
    <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Shipment Details</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div id="display_data">
                    <div class="modal-body text-center">
                        <div class="spinner-border text-secondary" role="status"></div>
                        <p class="mt-2 text-muted">Loading order details...</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>


            </div>
        </div>
    </div>

    <script>
        function loadshipment(shipment_id) {

            var url = "../controller/warehouse_controller.php?status=load_shipment";

            $.post(url, {
                shipment_id: shipment_id
            }, function(data) {
                $("#display_data").html(data).show();
            });
        }
    </script>

    <!-- confirm -->
    <div class="modal fade" id="confirmModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5>Transport Confirmation</h5>
                </div>
                <form action="../controller/transport_controller.php?status=confirm_transport" method="post">
                    <input type="hidden" name="transport_id" id="confirm_transport_id">
                    <div class="modal-body">
                        Are you sure you want to confirm transport?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="transportconfirm" id="confirmBtn" class="btn btn-info">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loadtransportconfirm(transport_id) {

            document.getElementById("confirm_transport_id").value = transport_id;
        }
    </script>




    <!-- reject -->
    <div class="modal fade" id="rejectModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5>Reject Transport</h5>
                </div>
                <form action="../controller/transport_controller.php?status=reject_transport" method="post">
                    <input type="hidden" name="transport_id" id="reject_transport_id">
                    <input type="hidden" name="shipment_id" id="reject_shipment_id">
                    <input type="hidden" name="vehicle_id" id="reject_vehicle_id">
                    <input type="hidden" name="driver_id" id="reject_driver_id">
                    <div class="modal-body">
                        <div>Are you sure you want to reject transport?</div>
                        <br>
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <textarea id="remarks" name="remarks" class="form-control" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="transportreject" id="rejectBtn" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loadtransportreject(transport_id, shipment_id, vehicle_id, driver_id) {

            document.getElementById("reject_transport_id").value = transport_id;
            document.getElementById("reject_shipment_id").value = shipment_id;
            document.getElementById("reject_vehicle_id").value = vehicle_id;
            document.getElementById("reject_driver_id").value = driver_id;
        }
    </script>




    <!-- start -->
    <div class="modal fade" id="startModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5>Start Transport</h5>
                </div>
                <form action="../controller/transport_controller.php?status=start_transport" method="post">
                    <input type="hidden" name="transport_id" id="start_transport_id">
                    <input type="hidden" name="shipment_id" id="start_shipment_id">
                    <input type="hidden" name="vehicle_id" id="start_vehicle_id">
                    <input type="hidden" name="driver_id" id="start_driver_id">
                    <div class="modal-body">
                        Are you sure you want to start transport?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Start</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loadstarttransport(transport_id, shipment_id, vehicle_id, driver_id) {

            document.getElementById("start_transport_id").value = transport_id;
            document.getElementById("start_shipment_id").value = shipment_id;
            document.getElementById("start_vehicle_id").value = vehicle_id;
            document.getElementById("start_driver_id").value = driver_id;
        }
    </script>



    <!-- deliver -->
    <div class="modal fade" id="deliveredModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5>Deliver Confirmation</h5>
                </div>
                <form action="../controller/transport_controller.php?status=deliver_transport" method="post">
                    <input type="hidden" name="transport_id" id="deliver_transport_id">
                    <input type="hidden" name="shipment_id" id="deliver_shipment_id">
                    <input type="hidden" name="vehicle_id" id="deliver_vehicle_id">
                    <input type="hidden" name="driver_id" id="deliver_driver_id">
                    <div class="modal-body">
                        Are you sure you want to finish delivery?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Deliver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loaddeliveredtransport(transport_id, shipment_id, vehicle_id, driver_id) {

            document.getElementById("deliver_transport_id").value = transport_id;
            document.getElementById("deliver_shipment_id").value = shipment_id;
            document.getElementById("deliver_vehicle_id").value = vehicle_id;
            document.getElementById("deliver_driver_id").value = driver_id;
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
        $("#table").DataTable();
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