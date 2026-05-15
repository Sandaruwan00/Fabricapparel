<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';

// get user information from session
$userrow = $_SESSION["user"];

$warehouseObj = new Warehouse();

$pkgResults = $warehouseObj->getAllPackedPackages();


?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Add Warehouse Packages</title>
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
                    <a href="add-warehouse-package.php" class="btn btn-outline-primary active">
                        Add Package
                    </a>
                    <a href="view-warehouse-packages.php" class="btn btn-outline-success">
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
        <div class="row text-center">
            <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Add Warehouse Packages
                </h1>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        while ($row = $pkgResults->fetch_assoc()) {

                        ?>
                            <tr>
                                <td><?php echo $row['packing_id']; ?></td>
                                <td><?php echo "ORD".$row['order_id']; ?></td>
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
                                <td>
                                    <a href="../controller/warehouse_controller.php?status=add_warehouse_package&packing_id=<?php echo $row['packing_id']; ?>&order_id=<?php echo $row['order_id']; ?>" class="btn btn-primary btn-sm">Add to warehouse</a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        
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