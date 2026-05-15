<?php

include_once '../commons/session.php';
include_once '../model/order_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderResult = $orderObj->getAllConfirmedOrders();

?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Planning Management</title>
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
                    <a href="generate-plan-report.php" class="btn btn-outline-warning">Generate Plan Reports</a>
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
                            0 </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Approved Plans</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            0</h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">---------</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            0
                        </h1>
                    </div>
                </div>
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">--------</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            0
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
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
    $(document).ready(function() {
        $("#planstable").DataTable();
    });
</script>

</html>