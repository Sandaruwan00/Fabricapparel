<?php
include_once '../commons/session.php';
include_once '../model/warehouse_model.php';

// get user information from session
$userrow = $_SESSION["user"];

$warehouseObj = new Warehouse();

$warehouseResult = $warehouseObj->getAllWarehousePackages();

$inWarehouseCount = 0;
$assignedCount = 0;
$dispatchedCount = 0;
$total = 0;

while ($row = $warehouseResult->fetch_assoc()) {
    $total++;

    if ($row["warehouse_pkg_status"] == "In Warehouse") {
        $inWarehouseCount++;
    }
    else if ($row["warehouse_pkg_status"] == "Shipment Assigned") {
        $assignedCount++;
    }
    else if ($row["warehouse_pkg_status"] == "Dispatched") {
        $dispatchedCount++;
    }
}


?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Warehouse Management</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "WAREHOUSE MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <div class="row">
            <div class="col-md-4 text-start">
                <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
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
                    <a href="view-shipments.php" class="btn btn-outline-success">View Shipments</a>
                    <a href="generate-warehouse-reports.php" class="btn btn-outline-warning">
                        Generate Reports
                    </a>
                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>


        <div class="row d-flex justify-content-around align-items-center shadow-lg"
            style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);
         padding: 20px; border-radius:10px;">
            <span class="h3 mb-4 fw-bold">Warehouse Summary</span>
            <div class="row d-flex justify-content-around text-center">

                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">In Warehouse Packages</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $inWarehouseCount;
                            ?>
                        </h1>
                    </div>
                </div>

                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Shipment Assigned</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $assignedCount;
                            ?>
                        </h1>
                    </div>
                </div>

                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Dispatched Packages</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $dispatchedCount;
                            ?>
                        </h1>
                    </div>
                </div>

                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Total Packages Handled</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $total;
                            ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>
    </div>
    <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>