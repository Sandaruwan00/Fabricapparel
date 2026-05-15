<?php
include_once '../commons/session.php';
include_once '../model/production_model.php';
include_once '../model/stock_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$productionObj = new Production();
$stockObj = new Stock();

$productionStockRequests = $stockObj->getAllProductionStockRequest();



?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Production Stock Request</title>
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
                    <a href="view-production-list.php" class="btn btn-outline-success">Production List</a>
                    <a href="generate-production-report.php" class="btn btn-outline-warning">Generate Production Reports</a>
                </div>
            </div>
        </div>
        <div class="row">
            &nbsp;
        </div>

        <div class="row">
            <div class="col-md-3">
                
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#PSRModal">
                    Add Production Stock Request
                </button>

            </div>
            <div class="col-md-6 text-center">
                <h1 style="margin:0; font-size:28px; font-weight:600;">
                    Production Stock Request
                </h1>
            </div>
            
        </div>  


        <div class="row">
            &nbsp;
        </div>


        <div class="row">
            <div class="col-md-12">

                <table class="table table-bordered table-striped" id="productionStockRequestsTable">
                    <thead>
                        <tr>
                            <th width="15%">Production Request ID</th>
                            <th width="30%">Stock Item</th>
                            <th width="10%">Quantity</th>
                            <th width="20%">Request Date</th>
                            <th width="25%">Request Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $productionStockRequests->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['psr_id']; ?></td>
                                <td><?= $row['stock_item_id'] . " - " . $row['stock_item_name'] . " " . $row['stock_item_color_code'] . " | Unit - " . $row['stock_unit_name']; ?></td>
                                <td><?= $row['psr_qty']. " " . $row['stock_unit_short_name']; ?></td>
                                <td><?= $row['psr_date']; ?></td>

                                <?php
                                if ($row['psr_status'] == "Pending") {
                                    $status_color = "bg-warning";
                                } elseif ($row['psr_status'] == "Issued") {
                                    $status_color = "bg-success";
                                } else {
                                    $status_color = "bg-danger";
                                }

                                ?>

                                <td class="text-center <?= $status_color; ?>"><?= $row['psr_status']; ?></td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>


    </div>


    <div class="modal fade" id="PSRModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/production_controller.php?status=production_stock_request" method="POST">

                    <!-- Header -->
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Request Stocks for Production</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <!-- Select Item -->
                        <div class="mb-3">
                            <label class="form-label">Item</label>
                            <select name="stock_item_id" class="form-control" required>
                                <option value="">-- Select Item --</option>
                                <?php
                                $items = $stockObj->getAllStockItems();
                                while ($item = $items->fetch_assoc()) { ?>
                                    <option value="<?= $item['stock_item_id']; ?>">
                                        <?= $item['stock_item_id'] . " - " . $item['stock_item_name'] . " " . $item['stock_item_color_code'] . " | Unit - " . $item['stock_unit_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="psr_qty" class="form-control" required>
                        </div>


                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Request Stock</button>
                    </div>

                </form>

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
        $("#productionStockRequestsTable").DataTable();
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