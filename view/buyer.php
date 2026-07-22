<?php
include_once '../commons/session.php';
include_once '../model/user_model.php';
include_once '../model/buyer_model.php';
//get user information from session
$userrow = $_SESSION["user"];

$buyerObj = new Buyer();
$buyerResult = $buyerObj->getAllBuyersForCount();

$totalBuyerCount = 0;
$activeBuyerCount = 0;
$deletedBuyerCount = 0;

while ($row = $buyerResult->fetch_assoc()) {
    $totalBuyerCount++;
    if ($row["buyer_company_status"] == 1) {
        $activeBuyerCount++;
    } elseif ($row["buyer_company_status"] == -1) {
        $deletedBuyerCount++;
    }
}

$topBuyerResult = $buyerObj->getTopFiveBuyers();

$buyerNames = [];
$buyerSales = [];

while ($row = $topBuyerResult->fetch_assoc()) {

    $buyerNames[] = $row["company_name"];
    $buyerSales[] = round($row["total_sales"], 2);
}

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Buyer Management</title>
    <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "BUYER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-buyer.php" class="btn btn-outline-primary">Add Buyer</a>
                    <a href="view-buyers.php" class="btn btn-outline-success">View Buyers</a>
                    <a href="generate-buyer-report.php" class="btn btn-outline-warning">Generate Buyer Reports</a>
                </div>
            </div>
        </div>
        <div class="row">
            &nbsp;
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="row shadow-lg justify-content-center" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
                    <span class="h3 mb-4 fw-bold">Buyer Summary</span>


                    <div class="row justify-content-center text-center">
                        <div class="col-md-12 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                            <div class="card-header">Total Buyers</div>
                            <div class="card-body">
                                <h1 class="card-title">
                                    <?php echo $totalBuyerCount; ?> </h1>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center text-center">
                        <div class="col-md-12 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                            <div class="card-header">Active Buyers</div>
                            <div class="card-body">
                                <h1 class="card-title">
                                    <?php echo $activeBuyerCount; ?> </h1>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center text-center">
                        <div class="col-md-12 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                            <div class="card-header">Removed Buyers</div>
                            <div class="card-body">
                                <h1 class="card-title">
                                    <?php echo $deletedBuyerCount; ?>
                                </h1>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
            <div class="col-md-8">
                <div id="topBuyerChart"></div>
            </div>
        </div>


        <div class="row">
            &nbsp;
        </div>
    </div>
    <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    var data = [{
        x: <?php echo json_encode($buyerNames); ?>,
        y: <?php echo json_encode($buyerSales); ?>,
        type: "bar",

        text: <?php echo json_encode(array_map(function ($v) {
                    return number_format($v, 2);
                }, $buyerSales)); ?>,

        textposition: "outside",

        hovertemplate: "<b>%{x}</b><br>" +
            "Sales : LKR %{y:,.2f}<extra></extra>"
    }];

    var layout = {

        height: 540,
        width: 850,

        title: {
            text: "Top 5 Buyers by Total Order Value"
        },

        xaxis: {
            title: "Buyer"
        },

        yaxis: {
            title: "Sales (LKR)"
        },

        margin: {
            t: 60,
            l: 80,
            r: 30,
            b: 120
        }

    };

    Plotly.newPlot("topBuyerChart", data, layout, {
        responsive: true,
        displayModeBar: false
    });
</script>

</html>