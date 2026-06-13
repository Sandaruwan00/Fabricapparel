<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';
include_once '../model/stock_model.php';


$userrow = $_SESSION["user"];

$orderObj = new Order();

$order_id = $_GET["order_id"];

// Get order main details
$orderResult = $orderObj->getOrder($order_id);
$orderrow = $orderResult->fetch_assoc();

// Get order items
$orderItemsResult = $orderObj->getOrderItems($order_id);

// Get payments
$paymentResult = $orderObj->getOrderPayments($order_id);

$orderStatusLogResult = $orderObj->getOrderStatusLogs($order_id);

$stockObj = new Stock();
$stockResult = $stockObj->getAllStocks();
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Create Plan</title>
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
                    Create Plan
                </h1>
            </div>
            <div class="col-md-4" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-plan.php" class="btn btn-outline-primary">Add Plan</a>
                    <a href="view-plans.php" class="btn btn-outline-success">View Plans</a>
                    <a href="generate-plan-report.php" class="btn btn-outline-warning">Generate Plan Reports</a>
                </div>
            </div>
        </div>

        <div class="row mt-5 justify-content-center">
            <div class="col-md-10">
                <div class="card" style="box-shadow: 0 4px 8px rgba(0,0,0,0.2);">

                    <!-- Header -->
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-1">Order #<?php echo $orderrow["order_id"]; ?></h3>
                            <h4 class="mb-0"><?php echo $orderrow["company_name"]; ?></h4>
                            <h6 class="mb-0">Contact Person: <?php echo $orderrow["contact_name"]; ?></h6>
                        </div>
                        <span class="badge fs-5" style="background-color: <?php echo $orderrow['color_code']; ?>; color: white;">
                            <?php echo $orderrow['status_name']; ?>
                        </span>
                    </div>

                    <div class="card-body" style="margin:20px;">

                        <!-- Order Info -->
                        <h4 class="fw-bold"><i class="bi bi-receipt"></i> Order Information</h4>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">ORDER DATE:</p>
                                <p class="fs-5"><?php echo $orderrow["order_date"]; ?></p>
                            </div>

                        </div>

                        <?php
                        $expected = $orderrow["expected_delivery_date"];
                        $today = date("Y-m-d");
                        $days = ceil((strtotime($expected) - strtotime($today)) / (60 * 60 * 24));

                        if ($days > 0) {
                            $badgeClass = "bg-success text-white";
                            $badgeText = "$days days left";
                        } elseif ($days == 0) {
                            $badgeClass = "bg-warning text-dark";
                            $badgeText = "Due Today";
                        } else {
                            $badgeClass = "bg-danger text-white";
                            $badgeText = abs($days) . " days overdue";
                        }
                        ?>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">EXPECTED DELIVERY DATE:</p>
                                <p class="fs-5"><?php echo $expected; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">DELIVERY STATUS:</p>
                                <p><span class="badge fs-6 <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span></p>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">DESIGN</p>
                                <iframe src="../files/design_pdfs/<?php echo $orderrow["design"]; ?>" width="100%" height="300px"></iframe>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">COMMENTS:</p>
                                <p class="fs-5"><?php echo $orderrow["comments"]; ?></p>
                            </div>
                        </div>

                        <div class="row">&nbsp;</div>
                        

                        <h4 class="fw-bold"><i class="bi bi-journal-text"></i> Order History</h4>
                        <hr>

                        <table class="table table-bordered">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                while ($log = $orderStatusLogResult->fetch_assoc()) {
                                    $no++;
                                ?>
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $log["changed_at"]; ?></td>
                                        <td class="text-center" style="background-color: <?php echo $log["color_code"]; ?> ;"><?php echo $log["status_name"]; ?></td>
                                        <td><?php echo $log["description"]; ?></td>
                                        <td><?php echo $log["remarks"]; ?></td>
                                    </tr>
                                <?php

                                } ?>
                            </tbody>
                        </table>

                        
                        
                        <div class="row">&nbsp;</div>

                        <!-- Items -->
                        <h4 class="fw-bold"><i class="bi bi-box-seam"></i> Order Items</h4>
                        <hr>

                        <table class="table table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $orderItemsResult->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $item["product_type_name"]; ?></td>
                                        <td><?php echo $item["size_short_name"]; ?></td>
                                        <td><?php echo $item["qty"]; ?></td>
                                        <td class="text-end"><?php echo number_format($item["unit_price"], 2); ?></td>
                                        <td class="text-end"><?php echo number_format($item["qty"] * $item["unit_price"], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="row">&nbsp;</div>

                        <!-- request stocks -->
                        <h4 class="fw-bold"><i class="bi bi-clipboard-plus"></i> Request Stocks</h4>
                        <hr>



                        <form action="../controller/planning_controller.php?status=add_plan" method="POST">

                            <input type="hidden" name="order_id" value="<?= $order_id; ?>">

                            <!-- Input Row -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Stock Item</label>
                                    <select id="stock_item_id" class="form-control">
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

                                <div class="col-md-3 mb-3">
                                    <label>Qty</label>
                                    <input type="number" id="qty" class="form-control" min="1">
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="button" id="addBtn" class="btn btn-primary">Add</button>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead class="table-secondary text-center">
                                            <tr>
                                                <th>Stock Item</th>
                                                <th>Qty</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stockTableBody"></tbody>
                                    </table>
                                </div>
                            </div>

                            

                            <div class="row">
                                &nbsp;
                            </div>

                            <div class="row">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">
                                        Submit Stock Request
                                    </button>
                                </div>
                            </div>

                        </form>


                        <div class="row">&nbsp;</div>









                    </div>
                </div>
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
    document.getElementById('addBtn').addEventListener('click', function() {
    const itemId = document.getElementById('stock_item_id').value;
    const itemText = document.getElementById('stock_item_id').selectedOptions[0].text;
    const qty = document.getElementById('qty').value;

    if (!itemId || !qty) {
        alert("Please select item and enter qty");
        return;
    }

    // Check for duplicate item
    const existingItems = document.querySelectorAll('#stockTableBody input[name="stock_item_id[]"]');
    for (let input of existingItems) {
        if (input.value === itemId) {
            alert("This item has already been added.");
            return;
        }
    }

    const row = `
    <tr>
        <td>${itemText}</td>
        <td>${qty}</td>
        <td>
            <input type="hidden" name="stock_item_id[]" value="${itemId}">
            <input type="hidden" name="qty[]" value="${qty}">
            <button type="button" class="btn btn-danger btn-sm removeBtn">Remove</button>
        </td>
    </tr>`;

    document.getElementById('stockTableBody').innerHTML += row;

    document.getElementById('stock_item_id').value = "";
    document.getElementById('qty').value = "";
});

document.addEventListener("click", function(e) {
    if (e.target.classList.contains("removeBtn")) {
        e.target.closest("tr").remove();
    }
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