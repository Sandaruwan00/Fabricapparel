<?php
include_once '../commons/session.php';
include_once '../model/stock_model.php';
include_once '../model/supplier_model.php';


// get user information from session
$userrow = $_SESSION["user"];

$stockObj = new Stock();
$supplierObj = new Supplier();

$requests = $stockObj->getAllPurchaseRequests();

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Purchase Requests</title>
</head>

<body style="border-radius:10px;">
    <div class="container">
        <?php $pageName = "PURCHASING MANAGEMENT"; ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>
        <!-- Top Buttons -->
        <div class="row">
            <div class="col-md-4 text-start">
                <a href="purchasing.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-8 text-end">
                <div class="btn-group">
                    <a href="supplier.php" class="btn btn-outline-primary">
                        Suppliers
                    </a>
                    <a href="purchase-requests.php" class="btn btn-outline-info active">
                        Purchase Requests
                    </a>
                    <a href="purchase-orders.php" class="btn btn-outline-success">
                        Purchase Orders
                    </a>
                    <a href="generate-purchase-reports.php" class="btn btn-outline-warning">
                        Generate Purchasing Reports
                    </a>
                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>

        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Purchase Requests</h1>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover" id="table">

                    <thead class="table-secondary text-center">
                        <tr>
                            <th width="10%">Request ID</th>
                            <th width="20%">Item</th>
                            <th width="15%">Requested Qty</th>
                            <th width="15%">Requested Date</th>
                            <th width="15%">Reqeust Status</th>
                            <th width="25%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        while ($row = $requests->fetch_assoc()) {

                            // Status color
                            if ($row['request_status'] == 'Pending') {
                                $statusColor = "bg-warning text-dark";
                            } elseif ($row['request_status'] == 'Sent') {
                                $statusColor = "bg-success";
                            } else {
                                $statusColor = "bg-info";
                            }
                        ?>
                            <tr height="45">
                                <td><?php echo $row["stock_purchase_request_id"]; ?></td>
                                <td><?php echo $row['stock_item_id'] . " - " . $row['stock_item_name'] . " " . $row['stock_item_color_code']; ?></td>
                                <td><?php echo $row["requested_qty"]. " ". $row["stock_unit_name"]; ?></td>
                                <td><?php echo $row["requested_date"]; ?></td>
                                <td class="<?php echo $statusColor; ?> text-center"><?php echo $row["request_status"]; ?></td>
                                <td>

                                <?php 
                                if($row["request_status"] == "Pending"){?>
                                <button class="btn btn-success btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#supplierRequestModal"
                                        onclick="loadSupplierRequest('<?php echo $row['stock_purchase_request_id']; ?>','<?php echo $row['stock_item_id']; ?>','<?php echo $row['stock_item_name']; ?>','<?php echo $row['stock_item_color_code']; ?>')">
                                        Send Supplier Request
                                    </button>

                                <?php
                                }
                                ?>
                                    
                                    
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


   <!-- Send Supplier Request Modal -->
<div class="modal fade" id="supplierRequestModal" tabindex="-1" aria-labelledby="supplierRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="supplierRequestModalLabel">Send Supplier Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../controller/purchase_controller.php?status=send_supplier_request" method="post">
                <div class="modal-body">
                     <input type="hidden" name="stock_purchase_request_id" id="stock_purchase_request_id">
                    <input type="hidden" name="supplier_stock_item_id" id="supplier_stock_item_id">

                    <!-- Item Info Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-body py-2 bg-info rounded">
                            <div class="d-flex align-items-center gap-3">

                               

                                <div>
                                    <!-- Item ID -->
                                    <small class="text-muted">Item ID</small>
                                    <div class="fw-semibold" id="supplier_stock_item_id_display">-</div>
                                </div>

                                <div class="flex-grow-1">
                                    <!-- Item Name -->
                                    <small class="text-muted">Item Name</small>
                                    <div class="fw-semibold" id="supplier_stock_item_name">-</div>
                                </div>

                                <div>
                                    <!-- Color Code -->
                                    <small class="text-muted">Color Code</small>
                                    <div class="fw-semibold font-monospace" id="supplier_stock_item_color_code">-</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <?php $supplierResult = $supplierObj->getAllSuppliers(); ?>
                    <div class="mb-3">
                        <label class="form-label">Select Supplier(s)</label>
                        <select class="form-select" name="supplier_ids[]" id="supplier_id" multiple size="5" required>
                            <?php while ($supplier = $supplierResult->fetch_assoc()) { ?>
                                <option value="<?= $supplier['supplier_id']; ?>">
                                    <?= $supplier['supplier_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted">Hold Ctrl to select multiple</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function loadSupplierRequest(stock_purchase_request_id,stock_item_id, stock_item_name, stock_item_color_code) {
        document.getElementById('stock_purchase_request_id').value  = stock_purchase_request_id;
        document.getElementById('supplier_stock_item_id').value  = stock_item_id;
        document.getElementById('supplier_stock_item_id_display').textContent = stock_item_id;
        document.getElementById('supplier_stock_item_name').textContent = stock_item_name;
        document.getElementById('supplier_stock_item_color_code').textContent = stock_item_color_code;
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
        $("#table").DataTable();
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