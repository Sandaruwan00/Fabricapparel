<?php
include '../commons/session.php';
include '../model/order_model.php';
include '../model/stock_model.php';
include '../model/production_model.php';

$orderObj = new Order();
$stockObj = new Stock();
$productionObj = new Production();

$userrow = $_SESSION["user"];

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];


switch ($status) {


    case "view_order_production":

        $order_id = $_POST["order_id"];
        $plan_id = $_POST["plan_id"];

        $orderResult = $orderObj->getOrder($order_id);
        $orderrow = $orderResult->fetch_assoc();

        $orderItemsResult = $orderObj->getOrderItems($order_id);

        $stockRequestResult = $stockObj->getStockRequestItems($plan_id);


    ?>

        <div class="modal-body">
            <div class="card-body" style="margin:20px;">

                <!-- Order Info -->
                <h4 class="fw-bold"><i class="bi bi-receipt"></i> Order Information</h4>
                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p class="fw-bold fs-5 m-auto">ORDER #<?php echo $orderrow["order_id"]; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="fw-bold m-auto">ORDER DATE:</p>
                        <p class="fs-5"><?php echo $orderrow["order_date"]; ?></p>
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

                <h5><?php echo $orderrow["address_line_1"] . ", " . $orderrow["address_line_2"] . ", " . $orderrow["address_line_3"]; ?></h5>
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
                        <p class="fw-bold m-auto">DELIVERY STATUS:</p>
                        <p><span class="badge fs-6 <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span></p>
                    </div>
                </div>

                <div class="row">&nbsp;</div>

                <!-- order Items -->
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

                <!-- Items -->
                <h4 class="fw-bold"><i class="bi bi-clipboard-plus"></i> Stock Request Items</h4>
                <hr>

                <table class="table table-bordered">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>#</th>
                            <th>Stock Item</th>
                            <th>Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;
                        while ($item = $stockRequestResult->fetch_assoc()) {
                            $no++;
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $item['stock_item_id'] . " - " . $item['stock_item_name'] . " " . $item['stock_item_color_code']; ?></td>
                                <td class="text-end"><?php echo $item["requested_qty"] . " " . $item["stock_unit_short_name"]; ?></td>

                                <?php
                                if ($item["stock_request_item_status"] == "Hold") {
                                    $status = "bg-secondary";
                                } elseif ($item["stock_request_item_status"] == "Rejected") {
                                    $status = "bg-danger";
                                } elseif ($item["stock_request_item_status"] == "Pending") {
                                    $status = "bg-warning";
                                } else {
                                    $status = "bg-success";
                                }

                                ?>

                                <td class="text-center <?= $status; ?>"><?php echo $item["stock_request_item_status"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>



            </div>
        </div>

        <?php


        break;

    case "start_production":

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $status_id = 6;
        $remarks = "-";

        try {


            $productionObj->addProdcution($order_id);
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);


            $msg = "Production Started !";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/add-production.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/add-production.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        }



        break;


    case "production_stock_request":

        echo $stock_item_id = $_POST["stock_item_id"] . "<br>";
        echo $psr_qty = $_POST["psr_qty"];

        try {

            $productionObj->addProductionStockRequest($stock_item_id, $psr_qty);

            $msg = "Stock Requested !";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/production-request-material.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/production-request-material.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        }


        break;


    case "end_production":

        $production_id = $_POST["production_id"];

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $status_id = 7;
        $remarks = "Production Ended";

        try {

            $productionObj->endProduction($production_id);
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            $msg = "Production Ended";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-production-list.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-production-list.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;
}
