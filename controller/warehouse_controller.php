<?php
include '../commons/session.php';
include '../model/warehouse_model.php';
include '../model/order_model.php';

$userrow = $_SESSION["user"];

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];


$warehouseObj = new Warehouse();
$orderObj = new Order();


switch ($status) {

    case "add_warehouse_package":

        $packing_id = $_GET["packing_id"];
        $order_id = $_GET["order_id"];
        $user_id = $userrow["user_id"];
        $status_id = 10;
        $remarks = "Package is moved to warehouse";


        try {

            $warehouseObj->addWarehousePackage($packing_id);
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            $msg = "Package #$packing_id Added to Warehouse";
            $msg = base64_encode($msg);
    ?>
            <script>
                window.location = "../view/add-warehouse-package.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/add-warehouse-package.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "load_order":

        $order_id = $_POST["order_id"];

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
                                    <p class="fw-bold m-auto">DESIGN</p>
                                    <iframe src="../files/design_pdfs/<?php echo $orderrow["design"]; ?>" width="100%" height="300px"></iframe>
                                </div>
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
                                        if ($pay["payment_status"] == "Pending" || $pay["payment_status"] == "Approved") {
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

        <?php

        break;

    case "create_shipment":

        $delivery_location = $_POST["delivery_location"];
        $packages = $_POST["packages"] ?? [];




        try {


            if (empty($packages)) {
                throw new Exception("No packages selected.");
            }

            $shipment_id = $warehouseObj->createShipment($delivery_location);

            foreach ($packages as $packing_id) {
                $warehouseObj->addShipmentItems($shipment_id, $packing_id);

                $warehouse_pkg_status = "Shipment Assigned";
                $warehouseObj->updateWarehousePackageStatus($packing_id, $warehouse_pkg_status);

                $orderIDResult = $warehouseObj->getOrderID($packing_id);
                $orderIDRow = $orderIDResult->fetch_assoc();
                $order_id = $orderIDRow["order_id"];

                $user_id = $userrow["user_id"];
                $status_id = 11;
                $remarks = "Shipment Assigned | Shipment ID - #$shipment_id";

                $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);
            }

            $msg = "Shipment Successfully Created";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-shipments.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/create-shipment.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "load_shipment":

        $shipment_id = $_POST["shipment_id"];

        $shipmentResult = $warehouseObj->getShipment($shipment_id);
        $shipmentRow = $shipmentResult->fetch_assoc();

        $shipmentItemResult = $warehouseObj->getAllShipmentItems($shipment_id);

        ?>

        <div class="modal-body bg-light">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div>
                            <h3 class="fw-bold text-primary mb-1">
                                Shipment #<?php echo $shipment_id; ?>
                            </h3>

                            <p class="mb-0 text-muted">
                                Delivery Location :
                                <span class="fw-semibold">
                                    <?php echo $shipmentRow["district_name"]; ?>
                                </span>
                            </p>
                        </div>

                        <div class="mt-2 mt-md-0">
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                <?php echo $shipmentItemResult->num_rows; ?> Orders
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam"></i>
                        Shipment Items
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="shipmentitemtable">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>Buyer</th>
                                    <th>Delivery Address</th>
                                    <th>Package Qty</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $count = 0;

                                while ($shipmentItemRow = $shipmentItemResult->fetch_assoc()) {

                                    $count++;

                                    $orderItems = $orderObj->getOrderItems($shipmentItemRow["order_id"]);

                                    $itemCount = 0;
                                    while ($orderItemRow = $orderItems->fetch_assoc()) {
                                        $itemCount += $orderItemRow["qty"];
                                    }
                                ?>

                                    <tr>

                                        <td class="text-center fw-bold">
                                            <?php echo $count; ?>
                                        </td>

                                        <td class="text-center">
                                            <?php echo "ORD" . $shipmentItemRow["order_id"]; ?>
                                        </td>

                                        <td>
                                            <div class="fw-semibold text-dark">
                                                <?php echo $shipmentItemRow["company_name"]; ?>
                                            </div>
                                        </td>

                                        <td style="max-width: 300px;">
                                            <?php
                                            echo $shipmentItemRow["address_line_1"] . ", "
                                                . $shipmentItemRow["address_line_2"] . ", "
                                                . $shipmentItemRow["address_line_3"];
                                            ?>
                                        </td>

                                        <td class="text-center bg-info">

                                            <?php echo $itemCount; ?> Items
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
        </div>


        <script>
            $(document).ready(function() {
                $("#shipmentitemtable").DataTable();
            });
        </script>

        <?php
        break;

    case "confirm_shipment":

        $shipment_id = $_GET["shipment_id"];

        try {

            $warehouseObj->confirmShipment($shipment_id);

            $msg = "Shipment Confrimed";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-shipments.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-shipments.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "reject_shipment":

        $shipment_id = $_GET["shipment_id"];

        try {

            $warehouseObj->rejectShipment($shipment_id);

            $packageResults = $warehouseObj->getAllShipmentItems($shipment_id);
            while ($pkgrow = $packageResults->fetch_assoc()) {
                $packing_id = $pkgrow["packing_id"];

                $warehouse_pkg_status = "In Warehouse";
                $warehouseObj->updateWarehousePackageStatus($packing_id, $warehouse_pkg_status);

                $orderIDResult = $warehouseObj->getOrderID($packing_id);
                $orderIDRow = $orderIDResult->fetch_assoc();
                $order_id = $orderIDRow["order_id"];

                $user_id = $userrow["user_id"];
                $status_id = 10;
                $remarks = $_GET["remarks"];

                $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);
            }



            $msg = "Shipment Rejected";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-shipments.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-shipments.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;

        case "dispatch_shipment":

        $shipment_id = $_GET["shipment_id"];

        try {

            $warehouseObj->dispatchShipment($shipment_id);

            $packageResults = $warehouseObj->getAllShipmentItems($shipment_id);
            while ($pkgrow = $packageResults->fetch_assoc()) {
                $packing_id = $pkgrow["packing_id"];

                $warehouse_pkg_status = "Dispatched";
                $warehouseObj->updateWarehousePackageStatus($packing_id, $warehouse_pkg_status);

                $orderIDResult = $warehouseObj->getOrderID($packing_id);
                $orderIDRow = $orderIDResult->fetch_assoc();
                $order_id = $orderIDRow["order_id"];

                $user_id = $userrow["user_id"];
                $status_id = 12;
                $remarks = "-";

                $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);
            }

            $msg = "Shipment Dispatched";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-shipments.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-shipments.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;
}

?>