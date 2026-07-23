<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';
include_once '../model/order_model.php';

// get user information from session
$userrow = $_SESSION["user"];

$warehouseObj = new Warehouse();
$orderObj = new Order();

$pkgResults = $warehouseObj->getAllWarehousePackages();



?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Warehouse Packages</title>
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
                    <a href="view-warehouse-packages.php" class="btn btn-outline-success active">
                        View Packages
                    </a>
                    <a href="create-shipment.php" class="btn btn-outline-info">Create Shipment</a>
                    <a href="view-shipments.php" class="btn btn-outline-success">View Shipments</a>
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
                View Warehouse Packages
            </h1>
            </div>
            
            
        </div>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover" id="table">
                    <thead class="table-secondary">
                        <tr>

                            <th>Packing ID</th>
                            <th>Order ID</th>
                            <th>Buyer</th>
                            <th>Due Date</th>
                            <th>Payments</th>
                            <th>Warehouse Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        while ($row = $pkgResults->fetch_assoc()) {

                        ?>
                            <tr>

                                <td><?php echo "PACK".$row["packing_id"] ?></td>
                                <td><?php echo "ORD" . $row['order_id']; ?></td>
                                <td><?php echo $row['company_name']; ?></td>
                                <td class="text-center
                                    <?php

                                    if ($row["status_id"] != 0 && $row["status_id"] != 15) {

                                        $expected = $row["expected_delivery_date"];
                                        $today = date("Y-m-d");

                                        $days = ceil((strtotime($expected) - strtotime($today)) / (60 * 60 * 24));

                                        if ($days > 0) {
                                            echo "bg-success text-white";
                                        } elseif ($days == 0) {
                                            echo "bg-warning text-dark";
                                        } else {
                                            echo "bg-danger text-white";
                                        }
                                    } else {
                                        echo "bg-info";
                                    }
                                    ?>
                                    ">
                                    <?php
                                    if ($row["status_id"] != 0 && $row["status_id"] != 15) {

                                        if ($days > 0) {
                                            echo "$days days left";
                                        } elseif ($days == 0) {
                                            echo "Due Today";
                                        } else {
                                            echo abs($days) . " days overdue";
                                        }
                                    } else {

                                        echo "-";
                                    }
                                    ?>
                                </td>


                                <?php
                                $totalPayments = 0;
                                $order_id = $row['order_id'];
                                $approvedOP = $warehouseObj->getApprovedOP($order_id);
                                while ($paymentrow = $approvedOP->fetch_assoc()) {

                                    $totalPayments = $totalPayments + $paymentrow["amount"];
                                    
                                }

                                $totalAmount = $row["total_amount"] + $row["delivery_charge"];
                                if ($totalPayments >= $totalAmount) {
                                    $paymentStatus = "Paid";
                                    $pscolor = "bg-success";
                                } else {
                                    $paymentStatus = "Unpaid";
                                    $pscolor = "bg-warning";
                                }

                                ?>

                                <td class="<?php echo $pscolor; ?> text-center"><?php echo $paymentStatus; ?></td>

                                <?php
                                if ($row['warehouse_pkg_status'] == "In Warehouse") {
                                    $color = "bg-primary";
                                } elseif ($row['warehouse_pkg_status'] == "Shipment Assigned") {
                                    $color = "bg-warning";
                                } else {
                                    $color = "bg-info";
                                }
                                ?>

                                <td class="<?php echo $color; ?> text-center"><?php echo $row['warehouse_pkg_status']; ?></td>
                                <td>
                                    <button href="#" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewModal" onclick="loadorder('<?php echo $row['order_id']; ?>');">
                                        <i class="bi bi-eye-fill"></i> View
                                    </button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
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

            var url = "../controller/warehouse_controller.php?status=load_order";

            $.post(url, {
                order_id: order_id
            }, function(data) {
                $("#display_data").html(data).show();
            });
        }
    </script>



        <div class="row">&nbsp;</div>
    </div>
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