<?php
include '../commons/session.php';

require '../commons/PHPMailer-master/src/PHPMailer.php';
require '../commons/PHPMailer-master/src/SMTP.php';
require '../commons/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
include '../model/purchase_model.php';
include '../model/supplier_model.php';


$stockObj = new Stock();
$purchaseObj = new Purchase();
$supplierObj = new Supplier();


switch ($status) {

    case "send_supplier_request":

        $stock_purchase_request_id = $_POST["stock_purchase_request_id"];
        $supplier_ids = $_POST["supplier_ids"] ?? [];



        try {

            if (empty($supplier_ids)) {
                throw new Exception("Please select at least one supplier.");
            }

            foreach ($supplier_ids as $supplier_id) {
                $purchaseObj->addSupplierRequest($supplier_id, $stock_purchase_request_id);

                // 2. Get supplier email by supplier_id
                $supplier = $supplierObj->getSupplier($supplier_id);
                $stockpurchaserequestresult = $purchaseObj->getStockPurchaseRequestItem($stock_purchase_request_id);

                // 3. Send email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = '127.0.0.1';   // hMailServer or Papercut
                $mail->SMTPAuth   = false;
                $mail->Port       = 2525;
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;

                $mail->setFrom('sandaruwanekanayake531@gmail.com', 'Stock Manager');
                $mail->addAddress($supplier['supplier_email'], $supplier['supplier_name']);

                $mail->isHTML(true);
                $mail->Subject = 'Stock Purchase Request #' . $stock_purchase_request_id;

                $item_details = $stockpurchaserequestresult['stock_item_name'] . ' ' . $stockpurchaserequestresult['stock_item_color_code'];
                $requested_qty = $stockpurchaserequestresult['requested_qty'] . ' ' . $stockpurchaserequestresult['stock_unit_name'];
                $supplier_name = $supplier['supplier_name'];

                $mail->Body = "
                    <h3>Stock Purchase Request</h3>
                    <p>Dear <b>{$supplier_name}</b>,</p>
                    <p>We would like to request a purchase for the following item:</p>
                    <table border='1' cellpadding='8' cellspacing='0'>
                        <tr><td><b>Request ID</b></td><td>{$stock_purchase_request_id}</td></tr>
                        <tr><td><b>Item Name</b></td><td>{$item_details}</td></tr>
                        <tr><td><b>Requested Qty</b></td><td>{$requested_qty}</td></tr>
                    </table>
                    <br>
                    <p>Please confirm your availability at your earliest convenience.</p>
                    <p>Thank you.</p>
                ";

                $mail->send();
            }

            $request_status = "Sent";
            $stockObj->updatePurchaseRequestStatus($stock_purchase_request_id, $request_status);

            $msg = "Supplier request sent successfully";
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

    case "add_purchase_order":

        $stock_purchase_request_id = $_POST["stock_purchase_request_id"];
        $supplier_id = $_POST["supplier_id"];
        $ordered_qty = $_POST["ordered_qty"];
        $unit_price = $_POST["unit_price"];
        $total_price = $_POST["total_price"];

        $request_status = "PO Created";



        try {

            $purchaseObj->addPO($stock_purchase_request_id, $supplier_id, $ordered_qty, $unit_price, $total_price);
            $purchaseObj->updateStockPurchaseRequestStatus($stock_purchase_request_id, $request_status);

            $msg = "Purchase Order Created";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/purchase-orders.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/purchase-orders.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;

        case "reject_po":
        
                $po_id = $_POST["po_id"];
        
                try {
        
                    $purchaseObj->rejectPO($po_id);
        
                    $msg = "msg";
                    $msg = base64_encode($msg);
                ?>
                    <script>
                        window.location = "../view/purchase-orders.php?msg=<?php echo $msg; ?>";
                    </script>
                <?php
        
                } catch (Exception $ex) {
                    $msg = $ex->getMessage();
                    $msg = base64_encode($msg);
                ?>
                    <script>
                        window.location = "../view/purchase-orders.php?msg=<?php echo $msg; ?>";
                    </script>
                <?php
                }
                break;
}
