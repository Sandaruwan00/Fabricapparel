<?php
include_once '../commons/session.php';
include_once '../model/user_model.php';
include_once '../model/supplier_model.php';
include_once '../model/stock_model.php';
include_once '../model/purchase_model.php';


// include_once '../model/purchase_model.php';
// get user information from session
$userrow = $_SESSION["user"];
// // objects
$supplierObj = new Supplier();
$stockObj = new Stock();
$purchaseObj = new Purchase();


$totalSuppliersResult = $supplierObj->getAllSupplierCount();
$totalSuppliers = 0;
while ($row = $totalSuppliersResult->fetch_assoc()) {
    $totalSuppliers = $row['supplier_count'];;
}

$pendingPurchaseRequestsCount = 0;
$pendingPOCount = 0;
$unpaidPOCount = 0;

$requests = $stockObj->getAllPurchaseRequests();
while ($requestsrow = $requests->fetch_assoc()) {
    if ($requestsrow["request_status"] == "Pending") {
        $pendingPurchaseRequestsCount++;
    }
}

$poResults = $purchaseObj->getPOs();
while ($porow = $poResults->fetch_assoc()) {
    if ($porow["po_status"] == "Pending") {
        $pendingPOCount++;
    }
    if ($porow["po_status"] == "Delivered") {
        $unpaidPOCount++;
    }
}

$topSupplierResults = $supplierObj->getTopSuppliers();
$supplierNames = [];
$supplierValues = [];

while ($supplier = $topSupplierResults->fetch_assoc()) {

    $supplierNames[] = $supplier["supplier_name"];
    $supplierValues[] = $supplier["total_purchase"];
}

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Purchasing Management</title>
    <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
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
                    <button
                        class="btn btn-outline-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#reportModal">

                        Generate Reports

                    </button>
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
                    <div class="card-header">Pending Purchase Requests</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $pendingPurchaseRequestsCount;
                            ?>
                        </h1>
                    </div>
                </div>
                <!-- Purchase Requests -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Pending Purchase Orders</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $pendingPOCount;
                            ?>
                        </h1>
                    </div>
                </div>
                <!-- Purchase Orders -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Unpaid Purchase Orders</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php
                            echo $unpaidPOCount;
                            ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div id="supplierChart"></div>
            </div>
        </div>


        <div class="row">&nbsp;</div>
    </div>

    <div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-purchase-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Purchase Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>

                    <br>

                    <label>End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Generate Report
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let today = new Date().toISOString().split("T")[0];

    document.getElementById("start_date").setAttribute("max", today);
    document.getElementById("end_date").setAttribute("max", today);

});
</script>
    
    
    <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    var supplierNames = <?php echo json_encode($supplierNames); ?>;
    var supplierValues = <?php echo json_encode($supplierValues); ?>;


    var data = [{
        y: supplierNames,
        x: supplierValues,
        type: 'bar',
        orientation: 'h',
        text: supplierValues.map(String),
        textposition: 'auto'
    }];


    var layout = {

        title: {
            text: "Top 5 Suppliers by Purchase Value"
        },

        xaxis: {
            title: "Total Purchase (Rs.)"
        },

        yaxis: {
            title: "Supplier"
        },

        height: 450,

        margin: {
        l: 250,   // left margin (for supplier names)
        r: 50,    // right margin
        t: 80,    // top margin (for title)
        b: 80     // bottom margin
    }

    };


    Plotly.newPlot(
        'supplierChart',
        data,
        layout
    );
</script>

</html>