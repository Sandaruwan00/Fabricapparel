<?php
include_once '../commons/session.php';
include_once '../model/production_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$productionObj = new Production();

$productionOrders = $productionObj->getAllProductionOrders()

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Productions</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PRODUCTION MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="production.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-production.php" class="btn btn-outline-primary">Start Production</a>
                    <a href="view-production-list.php" class="btn btn-outline-success active">Production List</a>
                    <a href="generate-production-report.php" class="btn btn-outline-warning">Generate Production Reports</a>
                </div>
            </div>
        </div>
        <div class="row">
            &nbsp;
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Productions
                </h1>
            </div>
        </div>


        <div class="row">
            &nbsp;
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle" id="inproductiontable">
                        <thead class="table-secondary">
                            <tr class="text-center">
                                <th width="8%">Order ID</th>
                                <th width="27%">Company</th>
                                <th width="12%">Due Date</th>
                                <th width="15%">Order Status</th>
                                <th width="15%">Production Status</th>
                                <th width="23%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $productionOrders->fetch_assoc()) {

                                $production_id = $row["production_id"];
                                $order_id = $row["order_id"];
                            ?>
                                <tr>
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
                                    <td class="text-center" style="background-color: <?= $row["color_code"]; ?>;"><?php echo $row["status_name"]; ?></td>

                                    <?php
                                    if ($row["production_status"] == "Ongoing") {
                                        $cell_color = "bg-warning";
                                    } else {
                                        $cell_color = "bg-success";
                                    }
                                    ?>
                                    <td class="<?= $cell_color; ?> text-center"><?php echo $row["production_status"]; ?></td>

                                    <td>

                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewOrderModal" onclick="viewOrder( '<?php echo $order_id; ?>','<?php echo $row['plan_id']; ?>')">
                                            <i class="bi bi-eye-fill"></i> View Order
                                        </button>

                                        <?php
                                        if ($row["production_status"] == "Ongoing") { ?>
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#finishProductionModal" onclick="loadOrder( '<?php echo $order_id; ?>','<?php echo $production_id; ?>')">
                                                <i class="bi bi-stop-circle"></i> Finish Production
                                            </button>

                                        <?php
                                        }
                                        ?>



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


    <!-- start production -->
    <div class="modal fade" id="finishProductionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Finish Production</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/production_controller.php?status=end_production" method="post">
                    <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>">
                    <input type="hidden" name="production_id" id="production_id" value="<?php echo $production_id; ?>">
                    <div class="modal-body py-4">
                        <p class="mt-3 fs-5">Are you sure you want to finish the production?</p>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" id="confirmStartBtn" class="btn btn-warning">
                            Yes, Finish Production
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function loadOrder(order_id, production_id) {
            document.getElementById("order_id").value = order_id;
            document.getElementById("production_id").value = production_id;

        }
    </script>


    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-receipt"></i> View Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>


                <div id="display_data">

                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function viewOrder(order_id, plan_id) {

            var url = "../controller/production_controller.php?status=view_order_production";

            $.post(url, {
                order_id: order_id,
                plan_id: plan_id
            }, function(data) {
                $("#display_data").html(data).show();
            });
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
        $("#inproductiontable").DataTable();
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