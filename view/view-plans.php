<?php

include_once '../commons/session.php';
include_once '../model/planning_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$planObj = new Planning();
$planResult = $planObj->getAllPlans();

include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 25)) {
    header("Location: access_denied.php");
    exit();
}
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
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportModal">Generate Plan Reports</button>
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
                                    <td><?= "PLAN" . $row["plan_id"]; ?></td>
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
                                    <?php
                                    $plan_id = base64_encode($row["plan_id"]);
                                    ?>
                                    <td>
                                        <a href="view-plan.php?plan_id=<?php echo $plan_id; ?>" class="btn btn-success btn-sm">View</a>
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
    $(document).ready(function() {
        $("#planstable").DataTable();
    });
</script>

</html>