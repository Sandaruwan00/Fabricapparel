<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';
include_once '../model/warehouse_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$transportObj = new Transport();
$warehouseObj = new Warehouse();

$dispatchedShipmentsResults = $transportObj->getDispatchedShipments();
$vehicleResults = $transportObj->getAllAvailableVehicles();
$driverResults = $transportObj->getAllAvailableDrivers();

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Create Transport</title>
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
                    <a href="add-transport.php" class="btn btn-outline-primary active">Create Transport</a>
                    <a href="view-transports.php" class="btn btn-outline-success">View Transports</a>
                    <a href="driver.php" class="btn btn-outline-info">Drivers</a>
                    <a href="vehicle.php" class="btn btn-outline-secondary">Vehicles</a>
                    <a href="generate-dispatched-shipment-report.php" class="btn btn-outline-warning">Generate Reports</a>
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
                    Create Transport
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
                            <th width="10%">Shipment ID</th>
                            <th width="15%">Delivery Location</th>
                            <th width="12%">No. of Orders</th>
                            <th width="20%">Select Vehicle</th>
                            <th width="20%">Select Driver</th>
                            <th width="23%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        while ($Row = $dispatchedShipmentsResults->fetch_assoc()) {

                            $shipment_id = $Row["shipment_id"];
                            $shipmentItemResults = $warehouseObj->getAllShipmentItems($shipment_id);
                            $shipmentItemCount = 0;
                            while ($shipmentItemRow = $shipmentItemResults->fetch_assoc()) {
                                $shipmentItemCount++;
                            }
                        ?>
                            <tr>
                                <td><?php echo "SHIP".$Row["shipment_id"]; ?></td>
                                <td><?php echo $Row["district_name"]; ?></td>
                                <td><?php echo $shipmentItemCount; ?></td>
                                <td>
                                    <!-- Give each select a unique ID using shipment_id -->
                                    <select name="select_vehicle" id="select_vehicle_<?php echo $shipment_id; ?>" class="form-control form-select" required>
                                        <option value="">--Select--</option>
                                        <?php
                                        mysqli_data_seek($vehicleResults, 0);
                                        while ($vehiclerow = $vehicleResults->fetch_assoc()) { ?>
                                            <option value="<?php echo $vehiclerow["vehicle_id"]; ?>">
                                                <?php echo $vehiclerow["vehicle_number"]; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <!-- Give each select a unique ID using shipment_id -->
                                    <select name="select_driver" id="select_driver_<?php echo $shipment_id; ?>" class="form-control form-select" required>
                                        <option value="">--Select--</option>
                                        <?php
                                        mysqli_data_seek($driverResults, 0);
                                        while ($driverrow = $driverResults->fetch_assoc()) { ?>
                                            <option value="<?php echo $driverrow["driver_id"]; ?>">
                                                <?php echo "ID:" . $driverrow["driver_id"] . " - " . $driverrow["driver_name"]; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>

                                <td>
                                    <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal" onclick="loadshipment('<?php echo $shipment_id; ?>');">
                                        <i class="bi bi-eye-fill"></i> View
                                    </a>
                                    <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal" onclick="loadassign('<?php echo $shipment_id; ?>','<?php echo $Row['district_id']; ?>');">
                                        <i class="bi bi-link"></i> Assign
                                    </a>
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



    <div class="modal fade" id="assignModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5>Assign Confirmation</h5>
                </div>
                <form action="../controller/transport_controller.php?status=assign_transport" method="post">
                    <input type="hidden" name="shipment_id" id="shipment_id">
                    <input type="hidden" name="district_id" id="district_id">
                    <input type="hidden" name="vehicle_id" id="vehicle_id">
                    <input type="hidden" name="driver_id" id="driver_id">
                    <div class="modal-body">
                        Are you sure you want to assign the transport </strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        function loadassign(shipment_id, district_id) {
    // Read the selected vehicle and driver from that row's dropdowns
    var vehicle_id = document.getElementById("select_vehicle_" + shipment_id).value;
    var driver_id  = document.getElementById("select_driver_"  + shipment_id).value;

    
    document.getElementById("shipment_id").value = shipment_id;
    document.getElementById("district_id").value  = district_id;
    document.getElementById("vehicle_id").value   = vehicle_id;
    document.getElementById("driver_id").value    = driver_id;
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