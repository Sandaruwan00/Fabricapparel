<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderresult = $orderObj->getAllOrders();

$ongoingCount = 0;
$pendingCount = 0;

while ($row = $orderresult->fetch_assoc()) {

    if ($row["status_id"] == 6) {
        $ongoingCount++;
    }
    else if ($row["status_id"] == 5) {
        $pendingCount++;
    }
}
?>
<html>
<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Production Management</title>
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
                    <a href="generate-production-report.php" class="btn btn-outline-warning">Generate Production Reports</a>
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
                    <div class="card-header">Production Completed</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo "-"; ?> </h1>
                    </div>
                </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Ongoing Productions</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $ongoingCount; ?> </h1>
                    </div>
                </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Pending Productions</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $pendingCount; ?>
                        </h1>
                    </div>
                </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">-------------</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo "-"; ?> </h1>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mt-4 shadow-lg cardgroupstyle">

      <div class="col-md-3">
        <a href="production-request-material.php" class="text-decoration-none">
          <div class="card shadow-sm text-center p-3">
            <h4>Request Materials</h4>
            <p>Request materials for production</p>
          </div>
        </a>
      </div>

      

      

      

    </div>
        
        
        <div class="row">
            &nbsp;
        </div>
        <div class="row">
            &nbsp;
        </div>
        <div class="row">
            &nbsp;
        </div>
        <div class="row">
            &nbsp;
        </div>
    </div>
    <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>
</html>