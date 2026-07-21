<?php
include '../commons/session.php';
include '../model/planning_model.php';
include '../model/stock_model.php';
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


$planObj = new Planning();
$stockObj = new Stock();
$orderObj = new Order();

switch ($status) {
    case "add_plan":

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $status_id = 3;
        $remarks = "-";

        $stock_item_ids = $_POST['stock_item_id'] ?? [];
        $qtys = $_POST['qty'] ?? [];

        try {

            if (empty($stock_item_ids)) {
                throw new Exception("Stock items cannot be empty!");
            }

            if (count($stock_item_ids) !== count($qtys)) {
                throw new Exception("Stock items and quantities mismatch!");
            }

            foreach ($qtys as $qty) {
                if (!is_numeric($qty) || $qty <= 0) {
                    throw new Exception("Invalid quantity: $qty");
                }
            }

            $plan_id = $planObj->addPlan($order_id);
            $stock_request_id = $stockObj->addStockRequest($plan_id);
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            foreach ($stock_item_ids as $index => $stock_item_id) {
                $qty = $qtys[$index];

                $stockObj->addStockRequestItem($stock_request_id, $stock_item_id, $qty);
            }

            $msg = "Plan Successfully Added";
            $msg = base64_encode($msg);

    ?>
            <script>
                window.location = "../view/view-plan.php?plan_id=<?php echo $plan_id; ?>&msg=<?php echo $msg; ?>";
            </script>

        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
            $order_id = base64_encode($order_id);
        ?>
            <script>
                window.location = "../view/create-plan.php?order_id=<?php echo $order_id; ?>&msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "reject_plan":

        $plan_id = $_POST["plan_id"];
        $stock_request_id = $_POST["stock_request_id"];
        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $remarks = $_POST["remarks"];
        $status_id = 2;


        try {

            if (!$remarks) {
                throw new Exception("Remarks cannot be empty!");
            }

            $planObj->rejectPlan($plan_id, "Rejected");
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            $stockObj->updateStockRequestStatus($plan_id, "Rejected");
            $stockObj->updateStockRequestItemStatus($stock_request_id, "Rejected");

            $msg = "Plan Rejected Successfully";
            $msg = base64_encode($msg);


            ?>
            <script>
                window.location = "../view/view-plan.php?plan_id=<?php echo $plan_id; ?>&msg=<?php echo $msg; ?>";
            </script>
        <?php
            
            
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-plan.php?plan_id=<?php echo $plan_id; ?>&msg=<?php echo $msg; ?>";
            </script>
        <?php
        }

        break;
    
    
    
        case "approve_plan":

        $plan_id = $_POST["plan_id"];
        $stock_request_id = $_POST["stock_request_id"];
        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $remarks = "-";
        $status_id = 4;


        try {

            $planObj->approvePlan($plan_id, "Approved");
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            $stockObj->updateStockRequestStatus($plan_id, "Pending");
            $stockObj->updateStockRequestItemStatus($stock_request_id, "Pending");

            $msg = "Plan Approved Successfully";
            $msg = base64_encode($msg);


            ?>
            <script>
                window.location = "../view/view-plan.php?plan_id=<?php echo $plan_id; ?>&msg=<?php echo $msg; ?>";
            </script>
        <?php
            
            
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-plan.php?plan_id=<?php echo $plan_id; ?>&msg=<?php echo $msg; ?>";
            </script>
        <?php
        }

        break;
}

?>