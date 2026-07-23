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

                 <!-- Plan Info -->
                        <h4 class="fw-bold"><i class="bi bi-receipt"></i> Order Information</h4>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="fs-5 fw-bold">Order #<?php echo $orderrow["order_id"]; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold m-auto">ORDER DATE:</p>
                                <p class="fs-5"><?php echo $orderrow["order_date"]; ?></p>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <p class="fw-bold m-auto">COMMENTS:</p>
                                <p class="fs-5"><?php echo $orderrow["comments"]; ?></p>
                            </div>
                        </div>


                        <div class="row">&nbsp;</div>

                        <?php $orderItemsResult = $orderObj->getOrderItems($orderrow["order_id"]); ?>

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
