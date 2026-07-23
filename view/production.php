<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';
include_once '../model/production_model.php';
include_once '../model/stock_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderresult = $orderObj->getAllOrders();

$ongoingCount = 0;
$pendingCount = 0;
$plannedCount = 0;


while ($row = $orderresult->fetch_assoc()) {

    if ($row["status_id"] == 6) {
        $ongoingCount++;
    } else if ($row["status_id"] == 5) {
        $pendingCount++;
    } else if ($row["status_id"] == 4) {
        $plannedCount++;
    }
}

$productionObj = new Production();
$finishedProductionResult = $productionObj->getFinishedProductionCount();
$prorow = $finishedProductionResult->fetch_assoc();
$completedCount = $prorow["total"];



$stockObj = new Stock();

$topMaterialResults = $stockObj->getTopRequestedMaterials();

$materialNames = [];
$materialQty = [];

while ($row = $topMaterialResults->fetch_assoc()) {

    $materialNames[] = $row["stock_item_name"] . " " . $row["stock_item_color_code"];
    $materialQty[] = $row["total_qty"];
}


?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Production Management</title>
    <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PRODUCTION MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-production.php" class="btn btn-outline-primary">Start Production</a>
                    <a href="view-production-list.php" class="btn btn-outline-success">Production List</a>
                    <a href="production-request-material.php" class="btn btn-outline-secondary">Request Materials</a>
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportModal">Generate Reports</button>
                </div>
            </div>
        </div>
        <div class="row">
            &nbsp;
        </div>
        <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
            <span class="h3 mb-4 fw-bold">Production Summary</span>
            <div class="row d-flex justify-content-around text-center">
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Planned Orders</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $plannedCount; ?> </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Pending Productions</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $pendingCount; ?> </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Ongoing Productions</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $ongoingCount; ?>
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Production Completed</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $completedCount; ?> </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-6">
                <div id="productionStatusChart"></div>
            </div>
            <div class="col-md-6">
                <div id="materialChart"></div>
            </div>
        </div>

    </div>

    <script>
        var data = [{
            labels: ["Ongoing", "Finished"],
            values: [
                <?php echo $ongoingCount; ?>,
                <?php echo $completedCount; ?>
            ],
            type: "pie",
            textinfo: "label+percent+value",
            hovertemplate: "%{label}<br>Orders: %{value}<extra></extra>"
        }];

        var layout = {
            title: {
                text: "Production Status Distribution"
            },
            height: 500,
            width: 700,
            margin: {
                l: 40,
                r: 40,
                t: 150,
                b: 40
            },
            legend: {
                orientation: "h",
                x: 0.5,
                xanchor: "center",
                y: -0.1
            }
        };

        Plotly.newPlot("productionStatusChart", data, layout, {
            responsive: true,
            displayModeBar: false
        });
    </script>


    <script>
        var materialNames = <?php echo json_encode($materialNames); ?>;
        var materialQty = <?php echo json_encode($materialQty); ?>;


        var data = [{
            y: materialNames,
            x: materialQty,
            type: 'bar',
            orientation: 'h',
            text: materialQty.map(String),
            textposition: 'auto'
        }];


        var layout = {

            title: {
                text: "Top Requested Materials"
            },

            height: 500,

            margin: {
                l: 180,
                r: 40,
                t: 150,
                b: 40
            },

            xaxis: {
                title: "Requested Quantity"
            },

            yaxis: {
                title: "Materials"
            }

        };


        Plotly.newPlot(
            "materialChart",
            data,
            layout, {
                responsive: true,
                displayModeBar: false
            }
        );
    </script>

<div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-production-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Production Report</h5>
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

</html>