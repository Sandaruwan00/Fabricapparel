<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';
include_once '../model/order_model.php';
include_once '../model/transport_model.php';

// get user information from session
$userrow = $_SESSION["user"];

$warehouseObj = new Warehouse();
$orderObj = new Order();

$pkgResults = $warehouseObj->getInWarehousePackages();


$transportObj = new Transport();
$disctrictResult = $transportObj->getAllDistrict();



?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Create Shipment</title>
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
                    <a href="create-shipment.php" class="btn btn-outline-info active">Create Shipment</a>
                    <a href="view-shipments.php" class="btn btn-outline-success">View Shipments</a>
                    <a href="generate-warehouse-paid-package-report.php" class="btn btn-outline-warning">
                        Generate Reports
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
                    Create Shipment
                </h1>
            </div>
            <div class="col-md-4">

            </div>

        </div>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-12">

                        <form action="../controller/warehouse_controller.php?status=create_shipment" method="post">

                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        Delivery Location
                                    </label>

                                    <select name="delivery_location" class="form-control form-select" required>
                                        <option value="">--Select--</option>

                                        <?php
                                        while ($districtrow = $disctrictResult->fetch_assoc()) { ?>
                                            <option value="<?php echo $districtrow["district_id"]; ?>">
                                                <?php echo $districtrow["district_name"]; ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>

                            </div>

                            <div class="row">&nbsp;</div>

                            <!-- Package Table -->
                            <div class="col-md-12">



                                <table class="table table-bordered table-hover" id="table">

                                    <thead class="table-secondary text-center">
                                        <tr>
                                            <th></th>
                                            <th>Packing ID</th>
                                            <th>Order ID</th>
                                            <th>Buyer</th>
                                            <th>Package Qty</th>
                                            <th>Location</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php


                                        while ($packageRow = $pkgResults->fetch_assoc()) {

                                            $totalPayments = 0;
                                            $order_id = $packageRow['order_id'];
                                            $approvedOP = $warehouseObj->getApprovedOP($order_id);




                                            while ($paymentrow = $approvedOP->fetch_assoc()) {


                                                if ($paymentrow["order_id"] == $order_id && $paymentrow["payment_status"] == "Approved") {
                                                    $totalPayments = $totalPayments + $paymentrow["amount"];
                                                }
                                            }

                                            $totalAmount = $packageRow["total_amount"] + $packageRow["delivery_charge"];
                                            if ($totalPayments >= $totalAmount) { ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox" name="packages[]" value="<?php echo $packageRow["packing_id"]; ?>">
                                                    </td>
                                                    <td><?php echo "PACK".$packageRow["packing_id"]; ?></td>
                                                    <td><?php echo "ORD" . $packageRow["order_id"]; ?></td>
                                                    <td><?php echo $packageRow["company_name"]; ?></td>

                                                    <?php
                                                    $orderItems = $orderObj->getOrderItems($order_id);
                                                    $itemCount = 0;
                                                    while ($orderItemRow = $orderItems->fetch_assoc()) {
                                                        $itemCount = $itemCount + $orderItemRow["qty"];
                                                    }

                                                    ?>

                                                    <td><?php echo $itemCount; ?></td>
                                                    <td><?php echo $packageRow["district_name"]; ?></td>
                                                    <td>
                                                        <a href="#" class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewModal" onclick="loadorder('<?php echo $order_id; ?>');">
                                                            <i class="bi bi-eye-fill"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>



                                        <?php
                                        }
                                        ?>

                                    </tbody>

                                </table>




                            </div>

                            <!-- Buttons -->
                            <div class="text-end mt-4">

                                <button type="reset"
                                    class="btn btn-secondary">
                                    Reset
                                </button>

                                <button type="submit"
                                    class="btn btn-success">
                                    Save Shipment
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>


    <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Order Details</h5>
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
        function loadorder(order_id) {

            var url = "../controller/order_controller.php?status=load_order";

            $.post(url, {
                order_id: order_id
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