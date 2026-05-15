<?php

include_once '../commons/session.php';
include_once '../model/planning_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$planObj = new Planning();
$planResult = $planObj->getAllPlans();

?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Plans</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PLANNING MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>


        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="planning.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-4" style="text-align:center;">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    View Plans
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-plan.php" class="btn btn-outline-primary">Add Plan</a>
                    <a href="view-plans.php" class="btn btn-outline-success active">View Plans</a>
                    <a href="generate-plan-report.php" class="btn btn-outline-warning">Generate Plan Reports</a>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="planstable">
                        <thead class="fs-6 table-secondary text-center">
                            <tr>
                                <th width="10%">Plan ID</th>
                                <th width="10%">Order No.</th>
                                <th width="45%">Company Name</th>
                                <th width="15%">Delivery Date</th>
                                <th width="10%">Plan Status</th>
                                <th width="10%">&nbsp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $planResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?= $row["plan_id"]; ?></td>
                                    <td><?= "ORD" . $row["order_id"]; ?></td>
                                    <td><?= $row["company_name"]; ?></td>
                                    <td><?= $row["expected_delivery_date"]; ?></td>

                                    <?php
                                    if ($row["plan_status"] == "Pending") {
                                        $class = "bg-warning";
                                    } elseif ($row["plan_status"] == "Approved") {
                                        $class = "bg-success";
                                    } else {
                                        $class = "bg-danger";
                                    }
                                    ?>

                                    <td class="<?= $class; ?>"><?= $row["plan_status"]; ?></td>
                                    <td>
                                        <a href="view-plan.php?plan_id=<?php echo $row["plan_id"]; ?>" class="btn btn-success btn-sm">View</a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
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