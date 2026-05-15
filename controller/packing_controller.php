<?php
include '../commons/session.php';
include '../model/packing_model.php';
include_once '../model/order_model.php';



$userrow = $_SESSION["user"];

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];

$packingObj = new Packing();
$orderObj = new Order();


switch ($status) {

    case "create_packing":

        $production_id = $_POST["production_id"];

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $status_id = 8;
        $remarks = "Packing Order Created";


        try {

            $packingObj->createPacking($production_id);
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            $msg = "Packing Order Created";
            $msg = base64_encode($msg);
    ?>
            <script>
                window.location = "../view/add-packing.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/add-packing.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "finalize_packing":

        $packing_id = $_POST["packing_id"];

        $order_id = $_POST["order_id"];
        $user_id = $userrow["user_id"];
        $status_id = 9;
        $remarks = "Order is packed";

        try {

            $packingObj->finalizePacking($packing_id);
            $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);

            $msg = "Packing Finalized";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-packing.php?packing_id=<?php echo $packing_id; ?>&msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-packing.php?packing_id=<?php echo $packing_id; ?>&msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;
}

?>