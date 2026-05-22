<?php
include_once '../commons/session.php';
include_once '../model/purchase_model.php';
include_once '../model/supplier_model.php';



// get user information from session
$userrow = $_SESSION["user"];


$purchaseObj = new Purchase();
$supplierObj = new Supplier();


$poResults = $purchaseObj->getPOs();


?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Purchase Orders</title>
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
                    <a href="purchase-requests.php" class="btn btn-outline-info">
                        Purchase Requests
                    </a>
                    <a href="purchase-orders.php" class="btn btn-outline-success active">
                        Purchase Orders
                    </a>
                    <a href="generate-purchase-reports.php" class="btn btn-outline-warning">
                        Generate Purchasing Reports
                    </a>
                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>

        <div class="row justify-content-end">

            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Purchase Orders</h1>
            </div>
            <div class="col-md-4 text-end">
                <div class="mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseOrderModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Purchase Order
                    </button>
                </div>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover" id="table">

                    <thead class="table-secondary text-center">
                        <tr>
                            <th width="7%">PO ID</th>
                            <th width="23%">Supplier</th>
                            <th width="20%">Item</th>
                            <th width="10%">Qty</th>
                            <th width="10%">Total (Rs.)</th>
                            <th width="10%">PO Status</th>
                            <th width="20%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        while ($row = $poResults->fetch_assoc()) {

                            if ($row["po_status"] == "Pending") {
                                $bg = "bg-warning";
                            } elseif ($row["po_status"] == "Confirmed") {
                                $bg = "bg-success";
                            } elseif ($row["po_status"] == "Delivered") {
                                $bg = "bg-info";
                            } else {
                                $bg = "bg-danger";
                            }

                        ?>
                            <tr>
                                <td><?php echo $row["po_id"]; ?></td>
                                <td><?php echo $row["supplier_name"]; ?></td>
                                <td><?php echo $row["stock_item_id"] . ") " . $row["stock_item_name"] . " " . $row["stock_item_color_code"]; ?></td>
                                <td><?php echo $row["ordered_qty"] . " " . $row["stock_unit_name"]; ?></td>
                                <td><?php echo $row["total_price"]; ?></td>
                                <td class="text-center <?php echo $bg; ?>"><?php echo $row["po_status"]; ?></td>

                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal">
                                        <i class="bi bi-eye-fill"></i> View
                                    </button>
                                    <?php
                                    if ($row["po_status"] == "Pending") { ?>
                                        <button class="btn btn-sm btn-success" onclick="loadConfirm('<?php echo $row['po_id']; ?>','<?php echo $row['supplier_id']; ?>')" data-bs-toggle="modal" data-bs-target="#confirmModal"><i class="bi bi-check-circle"></i> Confirm</button>

                                        <button class="btn btn-sm btn-danger" onclick="loadReject(<?php echo $row['po_id']; ?>)" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-circle"></i> Reject</button>
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


    <!-- Add Purchase Order Modal -->
    <div class="modal fade" id="addPurchaseOrderModal" tabindex="-1" aria-labelledby="addPurchaseOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addPurchaseOrderModalLabel">
                        Add Purchase Order
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../controller/purchase_controller.php?status=add_purchase_order" method="post">
                    <div class="modal-body">


                        <div class="mb-3">
                            <label class="form-label">Select Stock Purchase Request</label>
                            <select class="form-select" name="stock_purchase_request_id" required>
                                <option value="">-- Select Supplier Request --</option>
                                <?php
                                $sentStockPurchaseRequestResults = $purchaseObj->getSentStockPurchaseRequests();
                                while ($row = $sentStockPurchaseRequestResults->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['stock_purchase_request_id']; ?>">
                                        <?php echo $row['stock_item_name'] . " - " . $row['stock_item_color_code'] . "&nbsp;&nbsp;&nbsp;&nbsp; Qty - " . $row['requested_qty'] . " " . $row['stock_unit_name']; ?>
                                    </option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Supplier</label>
                            <select class="form-select" name="supplier_id" required>
                                <option value="">-- Select Supplier --</option>
                                <?php
                                $supplierResult = $supplierObj->getAllSuppliers();
                                while ($row = $supplierResult->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['supplier_id']; ?>">
                                        <?php echo "ID" . $row['supplier_id'] . " - " . $row['supplier_name']; ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row">
                            <!-- Ordered Qty -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ordered Qty</label>
                                <input type="number" class="form-control" name="ordered_qty" min="1" required>
                            </div>

                            <!-- Unit Price -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Unit Price (Rs.)</label>
                                <input type="number" class="form-control" name="unit_price" min="0" step="0.01" required>
                            </div>

                            <!-- Total Price (auto calculated) -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Total Price (Rs.)</label>
                                <input type="number" class="form-control" name="total_price" id="total_price" readonly>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto calculate total price
        document.querySelector('[name="ordered_qty"]').addEventListener('input', calculateTotal);
        document.querySelector('[name="unit_price"]').addEventListener('input', calculateTotal);

        function calculateTotal() {
            const qty = parseFloat(document.querySelector('[name="ordered_qty"]').value) || 0;
            const price = parseFloat(document.querySelector('[name="unit_price"]').value) || 0;
            document.getElementById('total_price').value = (qty * price).toFixed(2);
        }
    </script>


    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt"></i>
                        Purchase Order Details
                    </h5>

                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">PO ID</small>
                        <h6><?php echo $row["po_id"]; ?></h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Supplier</small>
                        <h6><?php echo $row["supplier_name"]; ?></h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Item</small>
                        <h6>
                            <?php
                            echo $row["stock_item_id"] . ") " .
                                $row["stock_item_name"] . " " .
                                $row["stock_item_color_code"];
                            ?>
                        </h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Quantity</small>
                        <h6>
                            <?php
                            echo $row["ordered_qty"] . " " .
                                $row["stock_unit_name"];
                            ?>
                        </h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Total Price</small>
                        <h6>Rs. <?php echo $row["total_price"]; ?></h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">PO Status</small>
                        <span class="badge bg-primary">
                            <?php echo $row["po_status"]; ?>
                        </span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Confirmation
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

              
                <div class="modal-body">
                    Are you sure you want to confirm this Purchase Order?
                </div>

               
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <form method="POST" action="../controller/purchase_controller.php?status=confirm_po">
                        <input type="hidden" name="po_id" id="confirm_po_id">
                        
                        <input type="hidden" name="supplier_id" id="confirm_supplier_id">

                        <button type="submit" class="btn btn-success">
                            Confirm
                        </button>
                    </form>

                </div>

            </div>
        </div>
    </div>




    <!-- reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

               
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        Reject Confrimation
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

              
                <div class="modal-body">
                    Are you sure you want to reject this Purchase Order?
                </div>

               
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <form method="POST" action="../controller/purchase_controller.php?status=reject_po">
                        <input type="hidden" name="po_id" id="reject_po_id">

                        <button type="submit" class="btn btn-danger">
                            Reject
                        </button>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <script>
        function loadConfirm(id,supplier_id) {
            document.getElementById("confirm_po_id").value = id;
            document.getElementById("confirm_supplier_id").value = supplier_id;
        }

        function loadReject(id) {
            document.getElementById("reject_po_id").value = id;
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