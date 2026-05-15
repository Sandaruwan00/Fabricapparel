<?php
include_once '../commons/session.php';
include_once '../model/packing_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$packingObj = new Packing();

$completedProductions = $packingObj->getAllCompletedProductions()

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Create Packing</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PACKING MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="packing.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-packing.php" class="btn btn-outline-primary active">Add Packing</a>
                    <a href="view-packing-list.php" class="btn btn-outline-success">View Packings</a>
                    <a href="generate-packing-report.php" class="btn btn-outline-warning">Generate Packing Reports</a>
                </div>
            </div>
        </div>
        <div class="row">
            &nbsp;
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Create Packing
                </h1>
            </div>
        </div>


        <div class="row">
            &nbsp;
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="addpackingtable">
                        <thead class="table-secondary">
                            <tr class="text-center">
                                <th width="12%">Production ID</th>
                                <th width="10%">Order ID</th>
                                <th width="50%">Company</th>
                                <th width="15%">Due Date</th>
                                <th width="13%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $completedProductions->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $row["production_id"]; ?></td>
                                    <td><?php echo "ORD" . $row["order_id"]; ?></td>
                                    <td><?php echo $row["company_name"]; ?></td>
                                    <td class="text-center
                                    <?php

                                    if ($row["status_id"] != 0 && $row["status_id"] != 15) {

                                        $expected = $row["expected_delivery_date"];
                                        $today = date("Y-m-d");

                                        $days = ceil((strtotime($expected) - strtotime($today)) / (60 * 60 * 24));

                                        if ($days > 0) {
                                            echo "bg-success text-white";
                                        } elseif ($days == 0) {
                                            echo "bg-warning text-dark";
                                        } else {
                                            echo "bg-danger text-white";
                                        }
                                    } else {
                                        echo "bg-info";
                                    }
                                    ?>
                                    ">
                                        <?php
                                        if ($row["status_id"] != 0 && $row["status_id"] != 15) {

                                            if ($days > 0) {
                                                echo "$days days left";
                                            } elseif ($days == 0) {
                                                echo "Due Today";
                                            } else {
                                                echo abs($days) . " days overdue";
                                            }
                                        } else {

                                            echo "-";
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <a type="button" class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#packingModal" onclick="loadproduction('<?php echo $row['production_id']; ?>','<?php echo $row['order_id']; ?>')">
                                            <i class="bi bi-plus-lg"></i> Create Packing
                                        </a>

                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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


    <div class="modal fade" id="packingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Confirm Packing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="../controller/packing_controller.php?status=create_packing" method="post">
                <input type="hidden" name="production_id" id="production_id">
                <input type="hidden" name="order_id" id="order_id">
                <div class="modal-body">
                    <p>Are you sure you want to create the packing?</p>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Yes, Create</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    function loadproduction(production_id, order_id) {
        document.getElementById("production_id").value = production_id;
        document.getElementById("order_id").value = order_id;
    }
</script>





    <?php include_once '../includes/footer_includes.php'; ?>
</body>

<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    $(document).ready(function() {
        $("#addpackingtable").DataTable();
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

</html>