<?php
include_once '../commons/session.php';
include_once '../model/stock_model.php';

$userrow = $_SESSION["user"];

$stockObj = new Stock();
$lowStockItems = $stockObj->getLowStockItems();

include_once '../model/permission_model.php';
$permissionObj = new Permission();
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <title>Stock Purchase Requests</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "STOCK MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Back Button -->
        <div class="row">
            <div class="col-md-4">
                <a href="stock.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-8" style="text-align:right;">
        <div class="btn-group">
          <a href="stock-items.php" class="btn btn-outline-primary">Materials</a>
          <a href="stock-list.php" class="btn btn-outline-success">Inventory</a>
          <a href="stock-material-request.php" class="btn btn-outline-info">Stock Requests</a>
          <a href="stock-purchase-requests.php" class="btn btn-outline-secondary active">Purchase Requests</a>
          <button
            class="btn btn-outline-warning"
            data-bs-toggle="modal"
            data-bs-target="#reportModal">

            Generate Report

          </button>
        </div>
      </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Purchase Requests</h1>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#purchaseRequestModal">
                    View Sent Purchase Requests
                </button>
            </div>
        </div>



        <div class="row mt-4">
            <div class="col-md-12">

                <table class="table table-bordered table-striped" id="stockPurchaseTable">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Item</th>
                            <th width="15%">Category</th>
                            <th width="10%">Unit</th>
                            <th width="15%">Current Qty</th>
                            <th width="15%">Min Stock</th>
                            <th width="20%">Request</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $lowStockItems->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['stock_item_id'];?></td>
                                <td><?= $row['stock_item_name'] . " " . $row['stock_item_color_code']; ?></td>
                                <td><?= $row['stock_category_name']; ?></td>
                                <td><?= $row['stock_unit_name']; ?></td>
                                <td><?= $row['quantity'] . " " . $row['stock_unit_short_name']; ?></td>
                                <td><?= $row['min_stock_level'] . " " . $row['stock_unit_short_name']; ?></td>
                                <td>
                                    <?php if ($permissionObj->hasPermission($userrow["user_id"], 31)) { ?>
                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#requestModal"
                                        onclick="loadRequest('<?= $row['stock_item_id']; ?>')">
                                        Request Purchase
                                    </button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <div class="modal fade" id="requestModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/stock_controller.php?status=add_purchase_request" method="POST">

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Purchase Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="stock_item_id" id="stock_item_id">

                        <div class="mb-3">
                            <label>Requested Quantity</label>
                            <input type="number" name="requested_qty" min="1" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Submit Request</button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="purchaseRequestModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Sent Purchase Requests</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <div class="container-fluid">

                    <table class="table table-bordered table-striped" id="purchaseRequests">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                                
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $requests = $stockObj->getAllPurchaseRequests();

                            while ($row = $requests->fetch_assoc()) {

                                // Status color
                                if ($row['request_status'] == 'Pending') {
                                    $statusColor = "bg-warning text-dark";
                                } elseif ($row['request_status'] == 'Sent') {
                                    $statusColor = "bg-success";
                                } elseif ($row['request_status'] == 'PO Created') {
                                    $statusColor = "bg-secondary";
                                } else {
                                    $statusColor = "bg-info";
                                }
                            ?>
                                <tr>
                                    <td><?= $row["stock_purchase_request_id"] ?></td>

                                    <td>
                                        <?= $row['stock_item_id']." - ".$row['stock_item_name'] . " " . $row['stock_item_color_code']; ?>
                                    </td>

                                    <td>
                                        <?= $row['requested_qty'] . " " . $row["stock_unit_short_name"]; ?>
                                    </td>

                                    <td>
                                        <?= $row['requested_date']; ?>
                                    </td>

                                    <td class="text-center <?= $statusColor; ?>">
                                        <?= $row['request_status']; ?>
                                    </td>

                                   
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>

                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>



 <div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-stock-purchase-request-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Stock Purchase Request Report</h5>
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
document.addEventListener("DOMContentLoaded", function () {

    let today = new Date().toISOString().split("T")[0];

    document.getElementById("start_date").setAttribute("max", today);
    document.getElementById("end_date").setAttribute("max", today);

});
</script>




    <?php include_once '../includes/footer_includes.php'; ?>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/datatable/bootstrap.bundle.min.js"></script>
    <script src="../js/datatable/dataTables.bootstrap5.js"></script>
    <script src="../js/datatable/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $("#stockPurchaseTable").DataTable();
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#purchaseRequests").DataTable();
        });
    </script>



    <script>
        const msg = document.getElementById('msg');
        const delayTime = 3000;
        setTimeout(() => {
            msg.style.display = 'none';
        }, delayTime);
    </script>

    <script>
        function loadRequest(stock_item_id) {
            document.getElementById("stock_item_id").value = stock_item_id;
        }
    </script>

</body>

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