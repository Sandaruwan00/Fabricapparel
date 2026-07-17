<?php
include_once '../commons/session.php';
include_once '../model/order_model.php';

$userrow = $_SESSION["user"];

$orderObj = new Order();

$order_id = base64_decode($_GET["order_id"]);

// Get order main details
$orderResult = $orderObj->getOrder($order_id);
$orderrow = $orderResult->fetch_assoc();

// Get order items
$orderItemsResult = $orderObj->getOrderItems($order_id);

// Get payments
$paymentResult = $orderObj->getOrderPayments($order_id);

$orderStatusLogResult = $orderObj->getOrderStatusLogs($order_id);
?>

<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>View Order</title>
</head>

<body>
    <div class="container">

        <?php $pageName = "ORDER MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Header -->
        <div class="row">
            <div class="col-md-4 text-start">
                <a href="view-orders.php" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="col-md-4 text-center">
                <h1 style="font-size:28px; font-weight:600;">View Order</h1>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="add-order.php" class="btn btn-outline-primary">Add Order</a>
                    <a href="view-orders.php" class="btn btn-outline-success">View Orders</a>
                    <a href="generate-order-report.php" class="btn btn-outline-warning">Generate Order Reports</a>
                </div>
            </div>
        </div>

        <div class="row">&nbsp;</div>

        <!-- Card -->
        <div class="row justify-content-center">
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

                        <div class="row">
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">TOTAL AMOUNT:</p>
                                <p class="fs-5">Rs <?php echo number_format($orderrow["total_amount"], 2); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">DELIVERY CHARGE:</p>
                                <p class="fs-5">Rs <?php echo number_format($orderrow["delivery_charge"], 2); ?></p>
                            </div>

                        </div>

                        <div class="row">
                           
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">COMMENTS:</p>
                                <p class="fs-5"><?php echo $orderrow["comments"]; ?></p>
                            </div>
                        </div>

                        <div class="row">&nbsp;</div>

                        <!-- Delivery -->
                        <h4 class="fw-bold"><i class="bi bi-truck"></i> Delivery Details</h4>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo $orderrow["address_line_1"] . ", " . $orderrow["address_line_2"] . ", " . $orderrow["address_line_3"]; ?></h5>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">DISTRICT:</p>
                                <p class="fs-5"><?php echo $orderrow["district_name"]; ?></p>
                            </div>
                        </div>


                        <br>

                        <?php
                        $expected = $orderrow["expected_delivery_date"];
                        $badgeText = "-";
                        if ($orderrow["status_name"] != "Cancelled" && $orderrow["status_name"] != "Delivered") {
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
                        }
                        ?>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">EXPECTED DELIVERY DATE:</p>
                                <p class="fs-5"><?php echo $expected; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">DELIVERY DUE:</p>
                                <p><span class="badge fs-6 <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span></p>
                            </div>
                        </div>

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
                                    <th>Design</th>
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
                                        <td class="text-center">
                                            <?php if (!empty($item["item_design"])) { ?>
                                                <button type="button"
                                                        class="btn btn-outline-primary btn-sm previewDesignBtn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#designPreviewModal"
                                                        data-file="../files/designs/<?php echo $item["item_design"]; ?>"
                                                        data-filename="<?php echo $item["item_design"]; ?>">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="row">&nbsp;</div>



                        <!-- Payments -->
                        <h4 class="fw-bold"><i class="bi bi-cash"></i> Payments</h4>
                        <hr>

                        <table class="table table-bordered">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalPayments = 0;
                                while ($pay = $paymentResult->fetch_assoc()) { ?>
                                    <tr>
                                        <td width="20%"><?php echo $pay["payment_datetime"]; ?></td>
                                        <td width="10%"><?php echo $pay["payment_method"]; ?></td>
                                        <td width="15%" class="text-end"><?php echo number_format($pay["amount"], 2); ?></td>
                                        <td width="15%" class="text-center"><?php echo $pay["payment_status"]; ?></td>
                                        <td width="20%"><?php echo $pay["reference_no"]; ?></td>
                                        <td width="20%"><?php echo $pay["payment_remarks"]; ?></td>
                                    </tr>
                                <?php
                                    if ($pay["payment_status"] == "Approved") {
                                        $totalPayments = $totalPayments + $pay["amount"];
                                    }
                                } ?>
                            </tbody>
                        </table>

                        <div class="row">&nbsp;</div>

                        <!-- Order Summary -->

                        <h4 class="fw-bold"><i class="bi bi-calculator"></i> Order Summary</h4>
                        <hr>

                        <?php


                        // ✅ Calculate totals
                        $totalOrderCost = $orderrow["total_amount"] + $orderrow["delivery_charge"];
                        $dueAmount = $totalOrderCost - $totalPayments;
                        ?>

                        <div class="row g-3 mb-3">

                            <!-- TOTAL ORDER COST -->
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light shadow-lg">
                                    <p class="text-muted mb-1 small">TOTAL ORDER COST</p>
                                    <p class="fs-4 fw-bold mb-0">
                                        Rs <?php echo number_format($totalOrderCost, 2); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- TOTAL PAYMENTS -->
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light shadow-lg">
                                    <p class="text-muted mb-1 small">TOTAL PAYMENTS</p>
                                    <p class="fs-4 fw-bold mb-0 text-success">
                                        Rs <?php echo number_format($totalPayments, 2); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- DUE AMOUNT -->
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light shadow-lg">
                                    <p class="text-muted mb-1 small">DUE AMOUNT</p>
                                    <p class="fs-4 fw-bold mb-0 <?php echo ($dueAmount > 0) ? 'text-danger' : 'text-success'; ?>">
                                        Rs <?php echo number_format($dueAmount, 2); ?>
                                    </p>
                                </div>
                            </div>

                        </div>

                        <div class="row">&nbsp;</div>

                        <h4 class="fw-bold"><i class="bi bi-journal-text"></i> Order History</h4>
                        <hr>

                        <table class="table table-bordered">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th width="2%">#</th>
                                    <th width="18%">Date & Time</th>
                                    <th width="20%">Status</th>
                                    <th width="35%">Description</th>
                                    <th width="25%">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                while ($log = $orderStatusLogResult->fetch_assoc()) {
                                    $no++;
                                ?>
                                    <tr height="50px" class="align-middle">
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
                        <!-- Buttons -->
                        <div class="row justify-content-end">
                            <?php
                            if ($orderrow['status_id'] != "0") {

                            ?>
                                <div class="col-md-2">
                                    <button href="#" class="btn btn-success w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addNewPaymentModal">
                                        <i class="bi bi-credit-card"></i> Add Payment
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button href="#" class="btn w-100" style="background-color: #0D6EFD;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal" <?php
                                                                        if ($orderrow['status_id'] >= "2") {
                                                                            echo "disabled";
                                                                        }
                                                                        ?>
                                                                        >
                                        <i class="bi bi-check-lg"></i> Confirm
                                    </button>
                                </div>

                                <div class="col-md-2">
                                    <button href="#" type="button" id="cancelBtn" class="btn btn-danger w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#cancelModal" <?php
                                                                        if ($orderrow['status_id'] >= "6") {
                                                                            echo "disabled";
                                                                        }
                                                                        ?>
                                                                        >
                                        <i class="bi bi-slash-circle"></i> Cancel
                                    </button>
                                </div>
                            <?php
                            } else { ?>
                                <div class="col-md-3">
                                    <a href="#" class="btn btn-success w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#refundRequestModal">
                                        <i class="bi bi-credit-card"></i> Refund Request
                                    </a>
                                </div>
                            <?php } ?>
                        </div>

                        <?php
                        $refundResult = $orderObj->getAllOrderRefunds();
                        $refundAmount = 0;
                        $display = 0;
                        while ($row = $refundResult->fetch_assoc()) {
                            if ($row["order_id"] == $order_id && $row["refund_status"] == "Processed") {
                                $refundAmount = $refundAmount + $row["refund_amount"];
                                $display++;
                            }
                        }
                        if ($display != 0) { ?>
                            <div class="row g-3 mt-3 mb-3 justify-content-center">

                                <!-- TOTAL REFUND AMOUNT -->
                                <div class="col-md-4">
                                    <div class="p-3 rounded bg-success bg-opacity-10 border border-success shadow-lg">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <p class="text-success mb-0 small fw-semibold">SUCCESSFULLY REFUNDED</p>
                                        </div>
                                        <p class="fs-4 fw-bold mb-0 text-success">
                                            Rs <?php echo number_format($refundAmount, 2); ?>
                                        </p>
                                        <p class="text-muted mb-0 small mt-1">Total refund amount processed</p>
                                    </div>
                                </div>
                            </div>

                        <?php
                        }
                        ?>


                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include_once '../includes/footer_includes.php'; ?>

    <!-- Add new payment Modal -->
    <div class="modal fade" id="addNewPaymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addNewPaymentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add New Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/order_controller.php?status=add_new_order_payment" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">------</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Online">Online</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Reference No.</label>
                                <input type="reference_no" name="reference_no" id="reference_no" class="form-control">
                            </div>

                        </div>

                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="addneworderpayment" id="cancelBtn" class="btn btn-success">Add Payment</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Confrim Modal -->
    <div class="modal fade" id="confirmModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Order Confirmation</h5>
                </div>
                <form action="../controller/order_controller.php?status=confirm_order" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        Are you sure you want to confirm Order <strong><?php echo $order_id; ?></strong> ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="orderconfirm" id="confirmBtn" class="btn" style="background-color: #0D6EFD;">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelBtnLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/order_controller.php?status=cancel_order" method="post">
                    <div class="modal-body">
                        <p>Are you sure you want to cancel Order
                            <strong><?php echo $order_id; ?></strong>?
                        </p>
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <textarea id="remarks" name="remarks" class="form-control" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="ordercancel" id="cancelBtn" class="btn btn-danger">Cancel Order</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- refund request Modal -->
    <div class="modal fade" id="refundRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="refundBtnLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="../controller/order_controller.php?status=order_refund_request" method="post">
                    <?php if ($no >= 5) { ?>
                        <div class="modal-body text-center py-4">
                            <i class="bi bi-tools text-danger" style="font-size: 48px;"></i>
                            <p class="mt-3 mb-0 fs-5 fw-bold">Refund Not Available</p>
                            <p class="text-muted small">Production has already started for this order.<br>Refunds cannot be processed at this stage.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    <?php } elseif ($totalPayments != 0) { ?>
                        <form action="../controller/order_controller.php?status=order_refund_request" method="post">
                            <div class="modal-body">
                                <p>Submit a refund request for Order <strong>#<?php echo $order_id; ?></strong>?</p>
                                <!-- Refund Summary -->
                                <div class="rounded p-3 mb-3" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Total Order Cost</span>
                                        <span>Rs <?php echo number_format($totalOrderCost, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Amount Paid</span>
                                        <span class="text-success fw-bold">Rs <?php echo number_format($totalPayments, 2); ?></span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Refund Amount</span>
                                        <span class="fw-bold text-success fs-5">Rs <?php echo number_format($totalPayments, 2); ?></span>
                                    </div>
                                </div>
                                <label class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" id="btnGroupAddon">Rs</div>
                                    </div>
                                    <input type="number" class="form-control" name="refund_amount" value="<?php echo $totalPayments;?>" readonly>
                                </div>
                                <label class="form-label fw-bold mt-3">Remarks <span class="text-danger">*</span></label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="Reason for refund..." required></textarea>
                                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                <input type="hidden" name="total_order_cost" value="<?php echo $totalOrderCost; ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="refund_request_submit" class="btn btn-success">
                                    Submit Request
                                </button>
                            </div>
                        </form>
                    <?php } else { ?>
                        <div class="modal-body text-center py-4">
                            <i class="bi bi-exclamation-circle text-warning" style="font-size: 48px;"></i>
                            <p class="mt-3 mb-0 fs-5">No payments found for this order.</p>
                            <p class="text-muted small">A refund can only be requested if a payment has been made.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    <?php } ?>

                </form>

            </div>
        </div>
    </div>


    <!-- item design view modal -->

    <!-- Design Preview Modal -->
<div class="modal fade" id="designPreviewModal" tabindex="-1" aria-labelledby="designPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="designPreviewModalLabel">Design Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="min-height: 400px;">
                <div id="designPreviewContent">
                    <!-- populated via JS -->
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="designDownloadBtn" class="btn btn-outline-secondary" download>
                    <i class="bi bi-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("click", function(e) {
        const btn = e.target.closest(".previewDesignBtn");
        if (!btn) return;

        const filePath = btn.dataset.file;
        const fileName = btn.dataset.filename;
        const content = document.getElementById("designPreviewContent");
        const downloadBtn = document.getElementById("designDownloadBtn");

        const ext = fileName.split('.').pop().toLowerCase();
        const imageExts = ["jpg", "jpeg", "png", "gif", "webp"];

        if (ext === "pdf") {
            content.innerHTML = `<iframe src="${filePath}" width="100%" height="500px" style="border:none;"></iframe>`;
        } else if (imageExts.includes(ext)) {
            content.innerHTML = `<img src="${filePath}" class="img-fluid" alt="Design Preview">`;
        } else {
            content.innerHTML = `
                <p class="text-muted">Preview not available for this file type (.${ext}).</p>
                <p><strong>${fileName}</strong></p>
            `;
        }

        downloadBtn.href = filePath;
        downloadBtn.setAttribute("download", fileName);
    });

    // Clean up content when modal closes, so old previews don't flash before new ones load
    document.getElementById("designPreviewModal").addEventListener("hidden.bs.modal", function() {
        document.getElementById("designPreviewContent").innerHTML = "";
    });
</script>


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