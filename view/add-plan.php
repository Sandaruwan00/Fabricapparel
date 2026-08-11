<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$orderObj = new Order();
$orderResult = $orderObj->getAllConfirmedOrders();

include_once '../model/permission_model.php';
$permissionObj = new Permission();
if (!$permissionObj->hasPermission($userrow["user_id"], 24)) {
    header("Location: access_denied.php");
    exit();
}
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Plan</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "PLANNING MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Header -->
        <div class="row">
            <div class="col-md-4 text-start">
                <a href="planning.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Add Plan</h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-plan.php" class="btn btn-outline-primary active">Add Plan</a>
                    <a href="view-plans.php" class="btn btn-outline-success">View Plans</a>
                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportModal">Generate Plan Reports</button>
                </div>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row justify-content-center">
            <h1 style="margin:0; font-size:20px; font-weight:600;">
                Confirm Orders
            </h1>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="planstable">
                        <thead class="fs-6 table-secondary text-center">
                            <tr>
                                <th width="8%">Order ID</th>
                                <th width="25%">Company Name</th>
                                <th width="20%">Contact Person</th>
                                <th width="15%">Delivery Date</th>
                                <th width="15%">Order Status</th>
                                <th width="17%">&nbsp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $orderResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?= "ORD".$row["order_id"]; ?></td>
                                    <td><?= $row["company_name"]; ?></td>
                                    <td><?= $row["contact_name"]; ?></td>
                                    <td><?= $row["expected_delivery_date"]; ?></td>
                                    <td style="background-color: <?= $row["color_code"]; ?>"><?= $row["status_name"]; ?></td>
                                    <?php
                                    $order_id = base64_encode($row["order_id"]);
                                    ?>
                                    <td>
                                        <a href="create-plan.php?order_id=<?= $order_id; ?>" class="btn btn-primary btn-sm">
                                            Create Plan
                                        </a>
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

        <!-- alert start -->
        <?php
        $msg = "";
        if (isset($_GET["msg"])) {
            $msg = base64_decode($_GET["msg"]);
        }
        ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="msgToast" class="toast align-items-center text-bg-secondary border-0" role="alert" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body" id="toastMsg">
                        <!-- Message -->
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msg = "<?php echo $msg; ?>";
                if (msg !== "") {
                    document.getElementById("toastMsg").innerText = msg;
                    let toastEl = document.getElementById("msgToast");
                    let toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }
            });
        </script>
        <!-- alert end -->

</body>

</html>