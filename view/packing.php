<?php
include_once '../commons/session.php';
include_once '../model/packing_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$packingObj = new Packing();

$completedProductions = $packingObj->getAllCompletedProductions();
$completedProductionsCount = 0;
while ($row = $completedProductions->fetch_assoc()) {
    $completedProductionsCount++;
}

$pendingPackingCount = 0;
$packedOrdersCount = 0;
$dispatchedPackagesCount = 0;

$packingResults = $packingObj->getAllPackings();

while ($row = $packingResults->fetch_assoc()) {
    switch ($row["packing_status"]) {
        case "Pending":
            $pendingPackingCount++;
            break;

        case "Packed":
            $packedOrdersCount++;
            break;

        case "Dispatched":
            $dispatchedPackagesCount++;
            break;
    }
}

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Packing Management</title>
    <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PACKING MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-packing.php" class="btn btn-outline-primary">Add Packing</a>
                    <a href="view-packing-list.php" class="btn btn-outline-success">View Packings</a>
                    <a href="generate-packing-report.php" class="btn btn-outline-warning">Generate Packing Reports</a>
                </div>
            </div>
        </div>
        <div class="row">
            &nbsp;
        </div>
        <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
            <span class="h3 mb-4 fw-bold">Packing Summary</span>
            <div class="row d-flex justify-content-around text-center">
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Production Complete</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $completedProductionsCount; ?>
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Pending to Packing</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $pendingPackingCount; ?>
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Packed Orders</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $packedOrdersCount; ?>
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Dispatched Packages</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $dispatchedPackagesCount; ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row">
            <div class="col-md-12">
                <div id="packingStatusComparison"></div>
            </div>
        </div>

        <script>
            var packingStatus = [
                "Dispatched",
                "Packed",
                "Pending"
            ];

            var packingValues = [
                <?php echo $dispatchedPackagesCount; ?>,
                <?php echo $packedOrdersCount; ?>,
                <?php echo $pendingPackingCount; ?>
            ];


            var data = [{
                y: packingStatus,
                x: packingValues,
                type: "bar",
                orientation: "h",
                text: packingValues.map(String),
                textposition: "auto"
            }];


            var layout = {

                title: {
                    text: "Packing Status Comparison"
                },

                xaxis: {
                    title: "Number of Orders"
                },

                yaxis: {
                    title: "Packing Status"
                },

                height: 400,

                margin: {
                    l: 120,
                    r: 40,
                    t: 60,
                    b: 50
                }

            };


            Plotly.newPlot(
                "packingStatusComparison",
                data,
                layout
            );
        </script>

    </div>
    <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

</html>