<?php
include_once '../commons/session.php';
include_once '../model/packing_model.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$packingObj = new Packing();
$orderObj = new Order();


$packing_id = $_GET["packing_id"];


$packingResults = $packingObj->getPacking($packing_id);
$packingrow = $packingResults->fetch_assoc();

$orderItemsResult = $orderObj->getOrderItems($packingrow["order_id"]);
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Packing</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "PACKING MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Top Buttons -->
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="view-packing-list.php" type="button" class="btn btn-outline-secondary">Back</a>

            </div>
            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-packing.php" class="btn btn-outline-primary">Add Packing</a>
                    <a href="view-packing-list.php" class="btn btn-outline-success">View Packings</a>
                    <a href="generate-packing-report.php" class="btn btn-outline-warning">Generate Packing Reports</a>
                </div>
            </div>
        </div>

        <div class="row mt-3 text-center">
            <h3 class="fw-bold">PACKING DETAILS</h3>
            <hr>
        </div>

        <div class="row mt-4">

            <!-- LEFT SIDE -->
            <div class="col-md-6">

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">
                        <i class="bi bi-view-list"></i> Packing & Order Info
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th width="40%">Packing ID</th>
                                <td><?php echo "PACK" . $packingrow["packing_id"]; ?></td>
                            </tr>
                            <tr>
                                <th>Order ID</th>
                                <td><?php echo "ORD" . $packingrow["order_id"]; ?></td>
                            </tr>
                            <tr>
                                <th>Production ID</th>
                                <td><?php echo "PRO" . $packingrow["production_id"]; ?></td>
                            </tr>
                            <tr>
                                <th>Company</th>
                                <td><?php echo $packingrow["company_name"]; ?></td>
                            </tr>
                            <tr>
                                <th>Due Date</th>
                                <td><?php echo $packingrow["expected_delivery_date"]; ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <?php
                                $color = $packingrow["packing_status"] == "Pending" ? "bg-warning text-dark" : "bg-success text-white";
                                ?>
                                <td class="<?php echo $color; ?> text-center fw-bold">
                                    <?php echo $packingrow["packing_status"]; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="col-md-6">

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">
                        <i class="bi bi-box-seam"></i> Order Items
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = $orderItemsResult->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $item["product_type_name"]; ?></td>
                                            <td><?php echo $item["size_short_name"]; ?></td>
                                            <td><?php echo $item["qty"]; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr style="font-weight: 1000;">
                                        <td colspan="2" class="">Total Items:</td>
                                        <td>10</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- ACTION SECTION -->
        <div class="row mt-3">
            <div class="col-md-12">

                <a href="packing-label.php?packing_id=<?php echo $packing_id; ?>" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Label
                </a>

                <?php
                if ($packingrow["packing_status"] == "Pending") { ?>
                    <button type="button" class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#finalizeModal"
                        onclick="loadpackid('<?php echo $packing_id; ?>','<?php echo $packingrow['order_id']; ?>')">
                        <i class="bi bi-check-circle"></i> Finalize Packing
                    </button>
                <?php
                }
                ?>



            </div>
        </div>


    </div>


    <div class="modal fade" id="finalizeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Confirm Finalization
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>


                <form action="../controller/packing_controller.php?status=finalize_packing" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="packing_id" id="packing_id">
                        <input type="hidden" name="order_id" id="order_id">
                        <p class="mb-2">
                            Are you sure you want to finalize this packing?
                        </p>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            Yes, Finalize
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function loadpackid(packing_id, order_id) {
            document.getElementById("packing_id").value = packing_id;
            document.getElementById("order_id").value = order_id;
        }
    </script>




    <?php include_once '../includes/footer_includes.php'; ?>
</body>

<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>

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