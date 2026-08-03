<?php
include '../commons/session.php';

if (!isset($_GET["status"])) {
    echo "<script>window.location = '../view/login.php';</script>";
    exit;
}

$status = $_GET["status"];
include '../model/order_model.php';
$orderObj = new Order(); // make sure you have an Order class in your model

$userrow = $_SESSION["user"];

switch ($status) {

    case "add_order":

        try {
            // --- Get POST Data ---
            $company_id = $_POST["company_id"];
            $user_id = $userrow["user_id"];
            $address_line_1 = $_POST["address_line_1"];
            $address_line_2 = $_POST["address_line_2"];
            $address_line_3 = $_POST["address_line_3"];
            $district_id = $_POST["district"];
            $expected_delivery_date = $_POST["delivery_date"];
            $total_amount = $_POST["amount"];
            // $delivery_charge = $_POST["delivery_charge"];
            $comments = $_POST["comments"];
            $first_payment_amount = $_POST["installment"];
            $payment_method = $_POST["payment_method"];
            $reference_no = $_POST["reference_no"];



            $order_items = $_POST["order_items"]; // pass orderItems as JSON from JS

            $delivery_charge = $total_amount * 0.05;

            if ($delivery_charge < 1000) {
                $delivery_charge = 1000;
            }

            if (empty($company_id)) {
                throw new Exception("Please select a buyer before submitting the order!");
            }

            if (!$order_items) {
                throw new Exception("Order items cannot be empty!");
            }

            $order_items = json_decode($order_items, true);



            // --- Insert Order ---
            $order_id = $orderObj->addOrder(
                $company_id,
                $user_id,
                $address_line_1,
                $address_line_2,
                $address_line_3,
                $district_id,
                $expected_delivery_date,
                $total_amount,
                $delivery_charge,
                $comments
            );

            if (!$order_id) throw new Exception("Failed to create order!");

            // --- Insert Order Items (with per-item design files) ---
            foreach ($order_items as $item) {
                $item_key = $item["item_key"];
                $item_design_name = "";

                // Each item's design file arrives as item_design[item_key] via the
                // hidden per-row file inputs created in add-order.php
                if (
                    isset($_FILES["item_design"]) &&
                    isset($_FILES["item_design"]["name"][$item_key]) &&
                    $_FILES["item_design"]["error"][$item_key] === UPLOAD_ERR_OK
                ) {
                    $original_name = basename($_FILES["item_design"]["name"][$item_key]);
                    $item_design_name = time() . "_" . $item_key . "_" . $original_name;
                    $item_design_path = "../files/designs/$item_design_name";
                    move_uploaded_file($_FILES["item_design"]["tmp_name"][$item_key], $item_design_path);
                }

                $orderObj->addOrderItem(
                    $order_id,
                    $item["product_id"],
                    $item["size_id"],
                    $item["qty"],
                    $item["price"],
                    $item_design_name
                );
            }

            // --- Insert Initial Order Status Log (Pending) ---
            $initial_status_id = 1; // 1 = Pending
            $orderObj->addOrderStatusLog($order_id, $initial_status_id, $user_id, "Order created");

            // --- Insert First Payment if provided ---
            if ($first_payment_amount > 0) {
                $orderObj->addOrderPayment(
                    $order_id,
                    $first_payment_amount,
                    $payment_method,
                    "Pending", // payment status
                    $reference_no
                );
            }

            $msg = "Order Successfully Added!";
            $msg = base64_encode($msg);

?>

            <script>
                window.location = "../view/view-orders.php?msg=<?php echo $msg; ?>";
            </script>


        <?php

        } catch (Exception $ex) {
            $msg = base64_encode($ex->getMessage());

        ?>

            <script>
                window.location = "../view/add-order.php?msg=<?php echo $msg; ?>";
            </script>


        <?php
        }

        break;

    case "cancel_order":

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $remarks = $_POST["remarks"];

        $orderObj->cancelOrder($order_id, $user_id, $remarks);
        $msg = "Order Cancelled!";
        $msg = base64_encode($msg);
        $order_id = base64_encode($order_id);
        ?>

        <script>
            window.location = "../view/view-order.php?order_id=<?php echo $order_id; ?>&msg=<?php echo urlencode($msg); ?>";
        </script>

    <?php



        break;

    case "add_new_order_payment":

        $order_id = $_POST["order_id"];
        $dueAmount = $_POST["dueAmount"];
        $amount = $_POST["amount"];
        $payment_method = $_POST["payment_method"];
        $reference_no = $_POST["reference_no"];

        try {

            if ($amount > $dueAmount) {

                throw new Exception("Payment amount exceeds due amount!");
            }

            $orderObj->addNewOrderPayment($order_id, $amount, $payment_method, $reference_no);
            $msg = "New Payment Added!";
            $msg = base64_encode($msg);
            $order_id = base64_encode($order_id);
        } catch (Exception $ex) {
            echo "Error: " . $ex->getMessage();
            $msg = base64_encode($ex->getMessage());
            $order_id = base64_encode($order_id);
        }
    ?>

        <script>
            window.location = "../view/view-order.php?order_id=<?php echo $order_id; ?>&msg=<?php echo urlencode($msg); ?>";
        </script>

    <?php



        break;

    case "confirm_order":

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];

        $orderObj->confirmOrder($order_id, $user_id,);
        $msg = "Order Confirmed!";
        $msg = base64_encode($msg);
        $order_id = base64_encode($order_id);
    ?>

        <script>
            window.location = "../view/view-order.php?order_id=<?php echo $order_id; ?>&msg=<?php echo urlencode($msg); ?>";
        </script>

    <?php



        break;

    case "approve_order_payment":

        $order_payment_id = $_GET["order_payment_id"];
        $payment_remarks = "Approved order payment";
        $orderObj->approveOrderPayment($order_payment_id, $payment_remarks);
        $msg = "Payment Approved!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/order-payments.php?msg=<?php echo $msg; ?>";
        </script>

    <?php

        break;


    case "reject_order_payment":

        $order_payment_id = $_GET["order_payment_id"];
        $payment_remarks = $_GET["payment_remarks"];
        $orderObj->rejectOrderPayment($order_payment_id, $payment_remarks);
        $msg = "Payment Rejected!";
        $msg = base64_encode($msg);
    ?>

        <script>
            window.location = "../view/order-payments.php?msg=<?php echo $msg; ?>";
        </script>

        <?php

        break;


    case "order_refund_request":

        $order_id = $_POST["order_id"];
        $refund_amount = $_POST["refund_amount"];
        $remarks = $_POST["remarks"];
        $total_order_cost = $_POST["total_order_cost"];





        try {

            $totalApprovedPayments = 0;
            $approvedPayementsResult = $orderObj->getApprovedOrderPayments();
            while ($row = $approvedPayementsResult->fetch_assoc()) {
                if ($row["order_id"] == $order_id) {
                    $totalApprovedPayments = $totalApprovedPayments + $row["amount"];
                }
            }


            if ($totalApprovedPayments > $refund_amount) {
                throw new Exception("Refund amount exceeded");
            }

            $requestedrefund = 0;
            $paidRefund = 0;
            $orderRefundResult = $orderObj->getAllOrderRefunds();
            while ($row = $orderRefundResult->fetch_assoc()) {
                if ($row["order_id"] == $order_id && $row["refund_status"] != "Rejected") {
                    $requestedrefund = $requestedrefund + $row["refund_amount"];
                }
                if ($row["order_id"] == $order_id && $row["refund_status"] == "Processed") {
                    $paidRefund = $paidRefund + $row["refund_amount"];
                }
            }

            if ($paidRefund >= $refund_amount) {
                throw new Exception("Already Processed Refund");
            }

            if ($requestedrefund >= $refund_amount) {
                throw new Exception("Already Requested Refund");
            }


            $orderObj->addOrderRefund($order_id, $refund_amount, $remarks);

            $msg = "Refund Request Added";
            $msg = base64_encode($msg);
            $order_id = base64_encode($order_id);
        ?>
            <script>
                window.location = "../view/view-order.php?order_id=<?php echo $order_id; ?>&msg=<?php echo urlencode($msg); ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
            $order_id = base64_encode($order_id);
        ?>
            <script>
                window.location = "../view/view-order.php?order_id=<?php echo $order_id; ?>&msg=<?php echo urlencode($msg); ?>";
            </script>
        <?php
        }
        break;

    case "reject_refund":

        $refund_id = $_POST["refund_id"];

        try {

            $orderObj->rejectRefund($refund_id);

            $msg = "Refund Rejected";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/order-refund.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/order-refund.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "approve_refund":

        $refund_id = $_POST["refund_id"];

        try {

            $orderObj->approveRefund($refund_id);

            $msg = "Refund Approved";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/order-refund.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/order-refund.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "load_order":

        $order_id = $_POST["order_id"];

        // Get order main details
        $orderResult = $orderObj->getOrder($order_id);
        $orderrow = $orderResult->fetch_assoc();

        // Get order items
        $orderItemsResult = $orderObj->getOrderItems($order_id);

        // Get payments
        $paymentResult = $orderObj->getOrderPayments($order_id);

        $orderStatusLogResult = $orderObj->getOrderStatusLogs($order_id);

        ?>
        <div class="modal-body">
            <div class="row justify-content-center">
                <div class="col-md-12">
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
                                                    <button
                                                        class="btn btn-outline-primary btn-sm previewDesignBtn"
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


                            <h4 class="fw-bold">
                                <i class="bi bi-image"></i> Design Preview
                            </h4>
                            <hr>

                            <div class="card shadow-sm">
                                <div class="card-body text-center" style="min-height:300px;">

                                    <div id="designPreviewContent">

                                        <div class="text-muted mt-5">
                                            <i class="bi bi-image fs-1"></i>
                                            <p>Select a design to preview.</p>
                                        </div>

                                    </div>

                                </div>
                            </div>


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
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener("click", function(e) {

    const btn = e.target.closest(".previewDesignBtn");
    if (!btn) return;

    const file = btn.dataset.file;
    const filename = btn.dataset.filename;

    const preview = document.getElementById("designPreviewContent");

    const ext = filename.split(".").pop().toLowerCase();

    const imageTypes = ["jpg","jpeg","png","gif","webp"];

    if (imageTypes.includes(ext)) {

        preview.innerHTML = `
            <img src="${file}"
                 class="img-fluid rounded shadow"
                 style="max-height:400px;">
        `;

    } else if (ext === "pdf") {

        preview.innerHTML = `
            <iframe src="${file}"
                    width="100%"
                    height="500"
                    style="border:none;">
            </iframe>
        `;

    } else {

        preview.innerHTML = `
            <div class="alert alert-warning">
                Preview is not available.
                <br><br>
                <a href="${file}" class="btn btn-primary" download>
                    Download ${filename}
                </a>
            </div>
        `;
    }

});
        </script>

<?php

        break;
}

?>