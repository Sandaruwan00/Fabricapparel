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

            $delivery_charge = $total_amount * 0.10;

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
        $amount = $_POST["amount"];
        $payment_method = $_POST["payment_method"];
        $reference_no = $_POST["reference_no"];

        $orderObj->addNewOrderPayment($order_id, $amount, $payment_method, $reference_no);
        $msg = "New Payment Added!";
        $msg = base64_encode($msg);
        $order_id = base64_encode($order_id);
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

            if ($total_order_cost < $refund_amount) {
                throw new Exception("Refund amount exceeded");
            }

            $requestedrefund = 0;
            $orderRefundResult = $orderObj->getAllOrderRefunds();
            while ($row = $orderRefundResult->fetch_assoc()) {
                if ($row["order_id"] == $order_id && $row["refund_status"] != "Rejected") {
                    $requestedrefund = $requestedrefund + $row["refund_amount"];
                }
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
}

?>