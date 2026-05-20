<?php
include_once '../commons/session.php';
include_once '../model/user_model.php';
include_once '../model/supplier_model.php';
// include_once '../model/purchase_model.php';
// get user information from session
$userrow = $_SESSION["user"];
// // objects
$supplierObj = new Supplier();
// $purchaseObj = new Purchase();


$totalSuppliersResult = $supplierObj->getAllSupplierCount();
$totalSuppliers = 0;
while ($row = $totalSuppliersResult->fetch_assoc()) {
  $totalSuppliers = $row['supplier_count'];;
}
// $activeSuppliers = $supplierObj->getActiveSupplierCount();
// $totalPR = $purchaseObj->getPRCount();
// $totalPO = $purchaseObj->getPOCount();
?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Purchasing Management</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PURCHASING MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <!-- Top Buttons -->
        <div class="row">
            <div class="col-md-4 text-start">
                <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-8 text-end">
                <div class="btn-group">
                    <a href="supplier.php" class="btn btn-outline-primary">
                        Suppliers
                    </a>
                    <a href="purchase-requests.php" class="btn btn-outline-info">
                        Purchase Requests
                    </a>
                    <a href="purchase-orders.php" class="btn btn-outline-success">
                        Purchase Orders
                    </a>
                    <a href="generate-purchase-reports.php" class="btn btn-outline-warning">
                        Generate Purchasing Reports
                    </a>
                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <!-- Summary Cards -->
        <div class="row d-flex justify-content-around align-items-center shadow-lg"
            style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%);
         padding: 20px; border-radius:10px;">
            <span class="h3 mb-4 fw-bold">Purchasing Summary</span>
            <div class="row d-flex justify-content-around text-center">
                <!-- Total Suppliers -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Total Suppliers</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $totalSuppliers;
                            ?>
                        </h1>
                    </div>
                </div>
                <!-- Active Suppliers -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Active Suppliers</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            // echo $activeSuppliers;
                            ?>
                        </h1>
                    </div>
                </div>
                <!-- Purchase Requests -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Purchase Requests</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            // echo $totalPR;
                            ?>
                        </h1>
                    </div>
                </div>
                <!-- Purchase Orders -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Purchase Orders</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            // echo $totalPO;
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