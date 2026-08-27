<?php
include '../commons/session.php';

$userrow = $_SESSION["user"];

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];

include '../model/stock_model.php';
include '../model/order_model.php';

include_once '../model/permission_model.php';
$permissionObj = new Permission();

$stockObj = new Stock();
$orderObj = new Order();

switch ($status) {

    //stock_unit
    case "add_stock_unit":
        $stock_unit_name  = $_POST["stock_unit_name"];
        $stock_unit_short_name  = $_POST["stock_unit_short_name"];
        $stock_unit_description  = $_POST["stock_unit_description"];
        try {
            $stockObj->addStockUnit($stock_unit_name, $stock_unit_short_name, $stock_unit_description);
            $msg = "Unit Successfully Added!!!";
            $msg = base64_encode($msg);
    ?>
            <script>
                window.location = "../view/stock-units.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-units.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "update_stock_unit":
        $stock_unit_id  = $_POST["stock_unit_id"];
        $stock_unit_name  = $_POST["stock_unit_name"];
        $stock_unit_short_name  = $_POST["stock_unit_short_name"];
        $stock_unit_description  = $_POST["stock_unit_description"];
        try {
            $stockObj->updateStockUnit($stock_unit_id, $stock_unit_name, $stock_unit_short_name, $stock_unit_description);
            $msg = "Unit Successfully Updated!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-units.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-units.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "delete_stock_unit":
        $stock_unit_id = $_GET["stock_unit_id"];
        $stockObj->deleteStockUnit($stock_unit_id);
        $msg = "Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>
        <script>
            window.location = "../view/stock-units.php?msg=<?php echo $msg; ?>";
        </script>
    <?php
        break;


    case "activate_stock_unit":
        $stock_unit_id = $_GET["stock_unit_id"];
        $stockObj->activateStockUnit($stock_unit_id);
        $msg = "Successfully Activated!";
        $msg = base64_encode($msg);
    ?>
        <script>
            window.location = "../view/stock-units.php?msg=<?php echo $msg; ?>";
        </script>
    <?php
        break;


    case "deactivate_stock_unit":
        $stock_unit_id = $_GET["stock_unit_id"];
        $stockObj->deactivateStockUnit($stock_unit_id);
        $msg = "Successfully Deactivated!";
        $msg = base64_encode($msg);
    ?>
        <script>
            window.location = "../view/stock-units.php?msg=<?php echo $msg; ?>";
        </script>
        <?php
        break;


    //stock_category
    case "add_stock_category":
        $stock_category_name  = $_POST["stock_category_name"];
        $stock_category_description  = $_POST["stock_category_description"];
        try {
            $stockObj->addStockCategory($stock_category_name, $stock_category_description);
            $msg = "Category Successfully Added!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-categories.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-categories.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "update_stock_category":
        $stock_category_id  = $_POST["stock_category_id"];
        $stock_category_name  = $_POST["stock_category_name"];
        $stock_category_description  = $_POST["stock_category_description"];
        try {
            $stockObj->updateStockCategory($stock_category_id, $stock_category_name, $stock_category_description);
            $msg = "Category Successfully Updated!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-categories.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-categories.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "delete_stock_category":
        $stock_category_id  = $_GET["stock_category_id"];
        $stockObj->deleteStockCategory($stock_category_id);
        $msg = "Category Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>
        <script>
            window.location = "../view/stock-categories.php?msg=<?php echo $msg; ?>";
        </script>
    <?php
        break;


    case "activate_stock_category":
        $stock_category_id = $_GET["stock_category_id"];
        $stockObj->activateStockCategory($stock_category_id);
        $msg = "Successfully Activated!";
        $msg = base64_encode($msg);
    ?>
        <script>
            window.location = "../view/stock-categories.php?msg=<?php echo $msg; ?>";
        </script>
    <?php
        break;


    case "deactivate_stock_category":
        $stock_category_id = $_GET["stock_category_id"];
        $stockObj->deactivateStockCategory($stock_category_id);
        $msg = "Successfully Deactivated!";
        $msg = base64_encode($msg);
    ?>
        <script>
            window.location = "../view/stock-categories.php?msg=<?php echo $msg; ?>";
        </script>
        <?php
        break;


    //stock_item
    case "add_stock_item":
        $stock_item_name = $_POST["stock_item_name"];
        $stock_category_id = $_POST["stock_category_id"];
        $stock_unit_id = $_POST["stock_unit_id"];
        $stock_item_color_code = !empty($_POST["stock_item_color_code"]) ? $_POST["stock_item_color_code"] : NULL;
        $min_stock_level = $_POST["min_stock_level"];
        try {
            $stockObj->addStockItem($stock_item_name, $stock_category_id, $stock_unit_id, $stock_item_color_code, $min_stock_level);
            $msg = "Item Successfully Added!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-items.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-items.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "update_stock_item":
        $stock_item_id  = $_POST["stock_item_id"];
        $stock_item_name  = $_POST["stock_item_name"];
        $stock_category_id  = $_POST["stock_category_id"];
        $stock_unit_id  = $_POST["stock_unit_id"];
        $stock_item_color_code  = $_POST["stock_item_color_code"];
        $min_stock_level  = $_POST["min_stock_level"];
        try {
            $stockObj->updateStockItem($stock_item_id, $stock_item_name, $stock_category_id, $stock_unit_id, $stock_item_color_code, $min_stock_level);
            $msg = "Item Successfully Updated!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-items.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-items.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "delete_stock_item":
        $stock_item_id  = $_GET["stock_item_id"];
        $stockObj->deleteStockItem($stock_item_id);
        $msg = "Item Successfully Deleted!!!";
        $msg = base64_encode($msg);
        ?>
        <script>
            window.location = "../view/stock-items.php?msg=<?php echo $msg; ?>";
        </script>
    <?php
        break;


    case "activate_stock_item":
        $stock_item_id = $_GET["stock_item_id"];
        $stockObj->activateStockItem($stock_item_id);
        $msg = "Successfully Activated!";
        $msg = base64_encode($msg);
    ?>
        <script>
            window.location = "../view/stock-items.php?msg=<?php echo $msg; ?>";
        </script>
    <?php
        break;


    case "deactivate_stock_item":
        $stock_item_id = $_GET["stock_item_id"];
        $stockObj->deactivateStockItem($stock_item_id);
        $msg = "Successfully Deactivated!";
        $msg = base64_encode($msg);
    ?>
        <script>
            window.location = "../view/stock-items.php?msg=<?php echo $msg; ?>";
        </script>
        <?php
        break;


    case "stock_in":
        $stock_item_id  = $_POST["stock_item_id"];
        $quantity  = $_POST["quantity"];
        $reference  = $_POST["reference"];
        $transaction_type = "IN";
        try {
            $stockObj->addInventoryStockItem($stock_item_id, $quantity);
            $stockObj->addStockTransaction($stock_item_id, $transaction_type, $quantity, $reference);
            $msg = "Inventory Item Successfully Added!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-list.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-list.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "stock_out":
        $stock_item_id  = $_POST["stock_item_id"];
        $quantity  = $_POST["quantity"];
        $reference  = $_POST["reference"];
        $transaction_type = "OUT";
        try {
            $stockObj->outInventoryStockItem($stock_item_id, $quantity);
            $stockObj->addStockTransaction($stock_item_id, $transaction_type, $quantity, $reference);
            $msg = "Inventory Item Successfully Out!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-list.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-list.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "add_purchase_request":
        $stock_item_id = $_POST["stock_item_id"];
        $requested_qty = $_POST["requested_qty"];
        try {
            $stockObj->addPurchaseRequest($stock_item_id, $requested_qty);
            $msg = "Purchase Request Successfully Added!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-purchase-requests.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-purchase-requests.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "approve_purchase_request":
        $stock_purchase_request_id = $_POST["stock_purchase_request_id"];
        $approved_by = $userrow["user_id"];

        date_default_timezone_set('Asia/Colombo');
        $approved_date = date("Y-m-d H:i:s");
        try {
            $stockObj->approvePurchaseRequest($stock_purchase_request_id, $approved_by, $approved_date);
            $msg = "Purchase Request Approved!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/purchase-requests.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/purchase-requests.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "reject_purchase_request":
        $stock_purchase_request_id = $_POST["reject_stock_purchase_request_id"];
        $rejected_by = $userrow["user_id"];
        date_default_timezone_set('Asia/Colombo');
        $rejected_date = date("Y-m-d H:i:s");
        $remarks = $_POST["reject_reason"];
        try {
            $stockObj->rejectPurchaseRequest($stock_purchase_request_id, $rejected_by, $rejected_date, $remarks);
            $msg = "Purchase Request Rejected!!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/purchase-requests.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/purchase-requests.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "load_stock_request":

        $stock_request_id = $_POST["stock_request_id"];
        $stock_request_status = $_POST["stock_request_status"];
        $stockRequestItems = $stockObj->getStockRequestItemsForStocks($stock_request_id);




        ?>

        <form action="../controller/stock_controller.php?status=issue_stock_items" method="post">
            <div class="modal-body">
                <input type="hidden" name="stock_request_id" value="<?= $stock_request_id; ?>">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Requested Qty</th>
                            <th>Available</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $stock_items = [];
                        $hasOutOfStock = false;
                        ?>

                        <?php while ($item = $stockRequestItems->fetch_assoc()) {

                            $stock_items[] = [
                                'stock_item_id' => $item['stock_item_id'],
                                'requested_qty' => $item['requested_qty']
                            ];

                            $qty = $item['quantity'];
                            $min = $item['min_stock_level'];

                            if ($qty == 0) {
                                $status = "Out of Stock";
                                $status_color = "bg-danger";
                                $hasOutOfStock = true;
                            } elseif ($qty <= $min) {
                                $status = "Low Stock";
                                $status_color = "bg-warning";
                            } else {
                                $status = "In Stock";
                                $status_color = "bg-success";
                            }

                            $order_id = $item['order_id'];
                        ?>
                            <input type="hidden" name="order_id" value="<?= $order_id; ?>">
                            <?php


                            ?>
                            <tr>
                                <td><?php echo $item['stock_item_id'] . ") " . $item['stock_item_name'] . " " . $item['stock_item_color_code']; ?></td>
                                <td><?= $item["requested_qty"] . " " . $item["stock_unit_short_name"]; ?></td>
                                <td class="text-center <?php echo $status_color; ?>"><?php echo $status; ?></td>

                                <?php
                                if ($item["stock_request_item_status"] == "Hold") {
                                    $cell_color = "bg-secondary";
                                } elseif ($item["stock_request_item_status"] == "Rejected") {
                                    $cell_color = "bg-danger";
                                } elseif ($item["stock_request_item_status"] == "Pending") {
                                    $cell_color = "bg-warning";
                                } else {
                                    $cell_color = "bg-success";
                                }

                                ?>

                                <td class="text-center <?= $cell_color; ?>"><?php echo $item["stock_request_item_status"]; ?></td>

                            </tr>
                        <?php

                        }

                        ?>
                    </tbody>
                </table>
            </div>


            <input type="hidden" name="order_id" value="<?= $order_id; ?>">
            <input type="hidden" name="stock_items" value="<?= htmlspecialchars(json_encode($stock_items)); ?>">

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?php
                if ($stock_request_status == "Pending") {

                    if (!$hasOutOfStock) {
                        if ($permissionObj->hasPermission($userrow["user_id"], 32)) {
                ?>
                        <button type="submit" class="btn btn-success">Issue</button>
                    <?php
                    } } else {
                    ?>
                        <span class="text-danger fw-bold">
                            Cannot issue stock because one or more requested items are out of stock.
                        </span>
                <?php
                    }
                }
                ?>

            </div>
        </form>

        <?php
        break;


    case "issue_stock_items":

        $stock_request_id = $_POST["stock_request_id"];

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $status_id = 5;
        $remarks = "Stock items issued";

        $stock_items = json_decode($_POST['stock_items'], true);



        $transaction_type = "OUT";
        $reference = "Stock issued for order # $order_id";

        try {

            foreach ($stock_items as $stock_item) {
                $stock_item_id = $stock_item['stock_item_id'];
                $quantity = $stock_item['requested_qty'];

                if (!$stockObj->checkStockExist($stock_item_id, $quantity)) {
                    throw new Exception("Insufficient stock for item ID: $stock_item_id");
                }
            }

            $stockObj->issueStockItems($stock_request_id);
            $stockObj->issueStockRequest($stock_request_id);
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            foreach ($stock_items as $stock_item) {
                $stock_item_id = $stock_item['stock_item_id'];
                $quantity = $stock_item['requested_qty'];

                $stockObj->outInventoryStockItem($stock_item_id, $quantity);
                $stockObj->addStockTransaction($stock_item_id, $transaction_type, $quantity, $reference);
            }





            $msg = "Stock Item Issued !!!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-material-request.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-material-request.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }

        break;

    case "reject_psr":

        $psr_id = $_GET["psr_id"];


        try {

            $stockObj->rejectProductionStockRequest($psr_id);

            $msg = "Production Stock Request Rejected!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-material-request.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-material-request.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }



        break;

    case "issue_psr":

        $psr_id = $_GET["psr_id"];
        $stock_item_id = $_GET["stock_item_id"];
        $quantity = $_GET["quantity"];

        try {

            if (!$stockObj->checkStockExist($stock_item_id, $quantity)) {
                throw new Exception("Insufficient stock for item ID: $stock_item_id");
            }

            $stockObj->outInventoryStockItem($stock_item_id, $quantity);
            $stockObj->issueProductionStockRequest($psr_id);


            $msg = "Production Stock Request Issued!";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-material-request.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/stock-material-request.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;
}
