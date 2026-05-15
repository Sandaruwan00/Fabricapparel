<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';

// get user information from session
$userrow = $_SESSION["user"];

$warehouseObj = new Warehouse();

$shipmentResults = $warehouseObj->getAllShipments();


?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Shipments</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "WAREHOUSE MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">
            <div class="col-md-4 text-start">
                <a href="warehouse.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-8 text-end">
                <div class="btn-group">
                    <a href="add-warehouse-package.php" class="btn btn-outline-primary">
                        Add Package
                    </a>
                    <a href="view-warehouse-packages.php" class="btn btn-outline-success">
                        View Packages
                    </a>
                    <a href="create-shipment.php" class="btn btn-outline-info">Create Shipment</a>
                    <a href="view-shipments.php" class="btn btn-outline-success active">View Shipments</a>
                    <a href="generate-warehouse-reports.php" class="btn btn-outline-warning">
                        Generate Warehouse Reports
                    </a>
                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-md-4">

            </div>
            <div class="col-md-4 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    View Shipments
                </h1>
            </div>
            <div class="col-md-4">

            </div>

        </div>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">

                <table class="table table-bordered table-hover" id="table">

                    <thead class="table-secondary text-center">
                        <tr>
                            <th width="10%">Shipment ID</th>
                            <th width="20%">Delivery Location</th>
                            <th width="10%">No. of Items</th>
                            <th width="20%">Shipment Status</th>
                            <th width="40%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        while ($shipmentRow = $shipmentResults->fetch_assoc()) {

                            $shipment_id = $shipmentRow["shipment_id"];
                            $shipmentItemResults = $warehouseObj->getAllShipmentItems($shipment_id);
                            $shipmentItemCount = 0;
                            while ($shipmentItemRow = $shipmentItemResults->fetch_assoc()) {
                                $shipmentItemCount++;
                            }
                        ?>
                            <tr>
                                <td><?php echo $shipmentRow["shipment_id"]; ?></td>
                                <td><?php echo $shipmentRow["district_name"]; ?></td>
                                <td><?php echo $shipmentItemCount; ?></td>

                                <?php
                                if ($shipmentRow["shipment_status"] == "Pending") {
                                    $color = "bg-warning";
                                } elseif ($shipmentRow["shipment_status"] == "Confirmed") {
                                    $color = "bg-success";
                                } elseif ($shipmentRow["shipment_status"] == "Dispatched") {
                                    $color = "bg-info";
                                } elseif ($shipmentRow["shipment_status"] == "Transport Assigned") {
                                    $color = "bg-secondary";
                                } else {
                                    $color = "bg-danger";
                                }
                                ?>

                                <td class="<?php echo $color; ?> text-center"><?php echo $shipmentRow["shipment_status"]; ?></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewModal" onclick="loadshipment('<?php echo $shipment_id; ?>');">
                                        <i class="bi bi-eye-fill"></i> View
                                    </a>

                                    <?php
                                    if ($shipmentRow["shipment_status"] == "Pending") {
                                    ?>
                                        <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal" onclick="loadshipmentconfirm('<?php echo $shipment_id; ?>');">
                                            <i class="bi bi-check-lg"></i>
                                            &nbsp
                                            Confirm
                                        </a>

                                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="loadshipmentreject('<?php echo $shipment_id; ?>');">
                                            <i class="bi bi-x-lg"></i>
                                            &nbsp
                                            Reject
                                        </a>
                                    <?php

                                    }
                                    ?>
                                    <?php
                                    if ($shipmentRow["shipment_status"] == "Confirmed") {
                                    ?>
                                        <a href="../controller/warehouse_controller.php?status=dispatch_shipment&shipment_id=<?php echo $shipment_id; ?>" class="btn btn-sm btn-success">
                                            <i class="bi bi-send"></i>
                                            &nbsp
                                            Dispatch
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


    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Shipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to confrim shipment?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a type="button" id="confirmBtn" class="btn btn-success">Confirm</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadshipmentconfirm(shipment_id) {

            document.getElementById("confirmBtn").href =
                "../controller/warehouse_controller.php?status=confirm_shipment&shipment_id=" + shipment_id;
        }
    </script>


    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Confirm Rejection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this shipment?</p>
                    <div class="mb-3">
                        <label for="rejectRemark" class="form-label fw-bold">Remark <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectRemark" rows="3" placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a type="button" id="confirmRejectBtn" class="btn btn-danger">Reject</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadshipmentreject(shipment_id) {

            document.getElementById("rejectRemark").value = "";

            document.getElementById("confirmRejectBtn").onclick = function() {
                var remark = document.getElementById("rejectRemark").value.trim();

                if (remark === "") {
                    alert("Please enter a remark before rejecting.");
                    return;
                }

                window.location.href = "../controller/warehouse_controller.php?status=reject_shipment&shipment_id=" + shipment_id + "remarks=" + encodeURIComponent(remark);
            };
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