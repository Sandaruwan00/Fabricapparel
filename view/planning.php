<?php

include_once '../commons/session.php';
include_once '../model/order_model.php';
include_once '../model/planning_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderResult = $orderObj->getAllConfirmedOrders();

$confirmedOrdersCount = $orderResult->num_rows;

$planObj = new Planning();
$planResult = $planObj->getAllPlans();
$approvedPlansCount = 0;
$pendingPlansCount = 0;
$rejectedPlansCount = 0;
while ($planrow = $planResult->fetch_assoc()) {
    switch ($planrow["plan_status"]) {
        case "Approved":
            $approvedPlansCount++;
            break;
        case "Pending":
            $pendingPlansCount++;
            break;
        case "Rejected":
            $rejectedPlansCount++;
            break;
    }
}

$monthlyRequestLabels = [];
$monthlyRequestCounts = [];

$requestTrendResult = $planObj->getMonthlyStockRequestTrend();

while ($row = $requestTrendResult->fetch_assoc()) {
    $monthlyRequestLabels[] = $row["month"];
    $monthlyRequestCounts[] = (int)$row["total_requests"];
}
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Planning Management</title>
    <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PLANNING MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>


        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-4" style="text-align:center;">

            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-plan.php" class="btn btn-outline-primary">Add Plan</a>
                    <a href="view-plans.php" class="btn btn-outline-success">View Plans</a>
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportModal">Generate Plan Reports</button>

                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
            <span class="h3 mb-4 fw-bold">Plan Summary</span>
            <div class="row d-flex justify-content-around text-center">
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Confirmed Orders</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $confirmedOrdersCount; ?>
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Approved Plans</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $approvedPlansCount; ?>
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Pending Plans</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $pendingPlansCount; ?>
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Rejected Plans</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $rejectedPlansCount; ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row">
            <div class="col-md-6">
                <div id="planStatusChart"></div>
            </div>
            <div class="col-md-6">
                <div id="stockRequestTrendChart"></div>
            </div>
        </div>





    </div>



    <?php include_once '../includes/footer_includes.php'; ?>


    <div class="modal fade" id="reportModal">
        <div class="modal-dialog">
            <form action="generate-plan-report.php" method="post" target="_blank">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Generate Plan Report</h5>
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
        document.addEventListener("DOMContentLoaded", function() {

            let today = new Date().toISOString().split("T")[0];

            document.getElementById("start_date").setAttribute("max", today);
            document.getElementById("end_date").setAttribute("max", today);

        });
    </script>









</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    var data = [{
        type: "pie",
        labels: [
            "Approved",
            "Pending",
            "Rejected"
        ],
        values: [
            <?php echo $approvedPlansCount; ?>,
            <?php echo $pendingPlansCount; ?>,
            <?php echo $rejectedPlansCount; ?>
        ],
        hole: 0.5,
        textinfo: "label+percent",
        textposition: "outside"
    }];

    var layout = {
        title: {
            text: "Plan Status Distribution",
            x: 0.5
        },
        height: 450,
        margin: {
            t: 80,
            b: 50,
            l: 50,
            r: 50
        },
        legend: {
            orientation: "h",
            x: 0.5,
            xanchor: "center"
        }
    };

    Plotly.newPlot(
        "planStatusChart",
        data,
        layout, {
            responsive: true
        }
    );
</script>

<script>
    var data = [{
        x: <?php echo json_encode($monthlyRequestLabels); ?>,
        y: <?php echo json_encode($monthlyRequestCounts); ?>,
        type: "scatter",
        mode: "lines+markers",
        line: {
            width: 3
        },
        marker: {
            size: 8
        },
        name: "Stock Requests"
    }];

    var layout = {
        title: {
            text: "Monthly Stock Request Trend",
            x: 0.5
        },
        xaxis: {
            title: "Month"
        },
        yaxis: {
            title: "Number of Stock Requests",
            rangemode: "tozero"
        },
        height: 500
    };

    Plotly.newPlot(
        "stockRequestTrendChart",
        data,
        layout, {
            responsive: true
        }
    );
</script>


</html>