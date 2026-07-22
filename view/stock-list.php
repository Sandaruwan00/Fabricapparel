<?php
include_once '../commons/session.php';
include_once '../model/stock_model.php';

$userrow = $_SESSION["user"];

$stockObj = new Stock();
$stockResult = $stockObj->getAllStocks(); // JOIN query
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php"; ?>
    <title>Inventory</title>
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
                    <a href="stock-list.php" class="btn btn-outline-success active">Inventory</a>
                    <a href="stock-material-request.php" class="btn btn-outline-info">Stock Requests</a>
                    <a href="stock-purchase-requests.php" class="btn btn-outline-secondary">Purchase Requests</a>
                    <a href="generate-stock-report.php" class="btn btn-outline-warning">Generate Reports</a>
                </div>
            </div>



        </div>

        <div class="row mt-4">
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">Inventory</h1>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#stockInModal">
                    <i class="bi bi-plus-lg"></i> Stock In
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#stockOutModal">
                    <i class="bi bi-dash-lg"></i> Stock Out
                </button>

                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#transactionModal">
                    View Transactions
                </button>
            </div>
        </div>



        <div class="row mt-4">
            <div class="col-md-12">

                <table class="table table-bordered table-striped" id="stockTable">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Item</th>
                            <th width="12%">Category</th>
                            <th width="10%">Quantity</th>
                            <th width="10%">Min Stock</th>
                            <th width="10%">Status</th>
                            <th width="15%">Last Update</th>
                            <th width="13%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $count = 1;
                        while ($row = $stockResult->fetch_assoc()) {

                            $qty = $row['quantity'];
                            $min = $row['min_stock_level'];

                            if ($qty == 0) {
                                $status = "Out of Stock";
                                $status_color = "bg-danger";
                            } elseif ($qty <= $min) {
                                $status = "Low Stock";
                                $status_color = "bg-warning";
                            } else {
                                $status = "In Stock";
                                $status_color = "bg-success";
                            }
                        ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><?php echo $row['stock_item_name'] . " " . $row['stock_item_color_code']; ?></td>
                                <td><?php echo $row['stock_category_name']; ?></td>
                                <td><?php echo $qty . " " . $row['stock_unit_short_name']; ?></td>
                                <td><?php echo $min . " " . $row['stock_unit_short_name']; ?></td>
                                <td class="text-center <?php echo $status_color; ?>"><?php echo $status; ?></td>
                                <td><?php echo $row['last_updated']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#requestModal"
                                        onclick="loadRequest('<?= $row['stock_item_id']; ?>')">
                                        Request Purchase
                                    </button>
                                </td>
                            </tr>
                        <?php
                            $count++;
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <!-- stock in modal -->
    <div class="modal fade" id="stockInModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/stock_controller.php?status=stock_in" method="POST">

                    <!-- Header -->
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Stock In</h5>
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
                                        <?= $item['stock_item_id'] . " - " . $item['stock_item_name'] . " " . $item['stock_item_color_code'] . " | Unit - " . $item['stock_unit_name'] . " | Min - " . $item['min_stock_level']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>

                        <!-- Reference -->
                        <div class="mb-3">
                            <label class="form-label">Reference (GRN / Invoice No)</label>
                            <input type="text" name="reference" class="form-control">
                        </div>


                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Stock</button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <!-- stock out -->
    <div class="modal fade" id="stockOutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="../controller/stock_controller.php?status=stock_out" method="POST">

                    <!-- Header -->
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Stock Out</h5>
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
                                        <?= $item['stock_item_id'] . " - " . $item['stock_item_name'] . " " . $item['stock_item_color_code'] . " | Unit - " . $item['stock_unit_name'] . " | Min - " . $item['min_stock_level']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>

                        <!-- Reference -->
                        <div class="mb-3">
                            <label class="form-label">Reference (Order No / Issue No)</label>
                            <input type="text" name="reference" class="form-control">
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Stock Out</button>
                    </div>

                </form>

            </div>
        </div>
    </div>



    <!-- transaction modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Stock Transactions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="container-fluid">



                        <!-- Table -->
                        <table class="table table-bordered table-striped" id="transactionTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Reference</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $transactions = $stockObj->getAllTransactions();


                                while ($row = $transactions->fetch_assoc()) {


                                    if ($row['transaction_type'] == 'IN') {
                                        $typecolor = "bg-success";
                                    } else {
                                        $typecolor = "bg-danger";
                                    }
                                ?>
                                    <tr>
                                        <td><?= $row["stock_transaction_id"] ?></td>
                                        <td><?= $row['stock_item_name'] . " " . $row['stock_item_color_code']; ?></td>
                                        <td class="text-center <?= $typecolor; ?>"><?= $row['transaction_type']; ?></td>
                                        <td><?= $row['quantity'] . " " . $row["stock_unit_short_name"]; ?></td>
                                        <td><?= $row['reference']; ?></td>
                                        <td><?= $row['transaction_date']; ?></td>
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
                            <input type="number" name="requested_qty" class="form-control" required>
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

    <?php include_once '../includes/footer_includes.php'; ?>

    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../bootstrap/dist/js/bootstrap.js"></script>
    <script src="../js/datatable/bootstrap.bundle.min.js"></script>
    <script src="../js/datatable/dataTables.bootstrap5.js"></script>
    <script src="../js/datatable/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $("#stockTable").DataTable();
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#transactionTable").DataTable({
                order: [
                    [5, "desc"]
                ]
            });
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