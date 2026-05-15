<?php
include_once '../commons/session.php';

//get user information from session
$userrow = $_SESSION["user"];
?>
<html>
<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Packing Management</title>
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
                    <div class="card-header">All Completed Packings</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo "2"; ?> </h1>
                    </div>
                </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Pending to Packing</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo "2"; ?> </h1>
                    </div>
                </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">-------------</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo "2"; ?>
                        </h1>
                    </div>
                </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">-------------</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo "2"; ?> </h1>
                    </div>
                </div>
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