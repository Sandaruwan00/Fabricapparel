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

                $mail->setFrom('sandaruwanekanayake531@gmail.com', 'Purchasing Manager');
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

            $msg = "Purchase Order Rejected";
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

    case "confirm_po":

        $po_id = $_POST["po_id"];
        $supplier_id = $_POST["supplier_id"];

        try {

            $supplier = $supplierObj->getSupplier($supplier_id);
            $poDetailsResult = $purchaseObj->getPODetails($po_id);
            $poDetails = $poDetailsResult->fetch_assoc();

            $purchaseObj->confirmPO($po_id);

            // Send confirmation email to supplier
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = '127.0.0.1';
            $mail->SMTPAuth   = false;
            $mail->Port       = 2525;
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;

            $mail->setFrom('sandaruwanekanayake531@gmail.com', 'Purchasing Manager');
            $mail->addAddress($supplier['supplier_email'], $supplier['supplier_name']);

            $mail->isHTML(true);
            $mail->Subject = 'Purchase Order Confirmed #' . $po_id;

            $supplier_contact_person  = $supplier['supplier_contact_person'];
            $supplier_name  = $supplier['supplier_name'];
            $item_details   = $poDetails['stock_item_name'] . ' ' . $poDetails['stock_item_color_code'];
            $ordered_qty    = $poDetails['ordered_qty'] . ' ' . $poDetails['stock_unit_name'];
            $total_price    = number_format($poDetails['total_price'], 2);

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
                
                <div style='background: #1c7c4a; padding: 20px 30px;'>
                    <h2 style='color: #fff; margin: 0;'>Purchase Order Confirmed</h2>
                    <p style='color: #d1fae5; margin: 4px 0 0;'>PO ID: <strong>#$po_id</strong></p>
                </div>

                <div style='padding: 24px 30px;'>
                    <p style='font-size: 15px;'>Dear <strong>{$supplier_contact_person}</strong>,</p>
                    <p style='color: #555;'>We are pleased to confirm the following Purchase Order. Please review the details below and proceed accordingly.</p>

                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;'>
                        <thead>
                            <tr style='background: #f3f4f6;'>
                                <th style='text-align: left; padding: 10px 14px; border: 1px solid #e0e0e0;'>Field</th>
                                <th style='text-align: left; padding: 10px 14px; border: 1px solid #e0e0e0;'>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0; color: #555;'>PO ID</td>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0;'><strong>#$po_id</strong></td>
                            </tr>
                            <tr style='background: #f9fafb;'>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0; color: #555;'>Supplier</td>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0;'>{$supplier_name}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0; color: #555;'>Item</td>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0;'>{$item_details}</td>
                            </tr>
                            <tr style='background: #f9fafb;'>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0; color: #555;'>Ordered Quantity</td>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0;'>{$ordered_qty}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0; color: #555;'>Total Amount</td>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0;'><strong style='color: #1c7c4a;'>Rs. {$total_price}</strong></td>
                            </tr>
                            <tr style='background: #f9fafb;'>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0; color: #555;'>Status</td>
                                <td style='padding: 10px 14px; border: 1px solid #e0e0e0;'>
                                    <span style='background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 20px; font-size: 13px;'>Confirmed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p style='color: #555;'>Please ensure the delivery is made as per the agreed schedule. If you have any questions, feel free to contact us.</p>
                    <p style='color: #555;'>Thank you for your cooperation.</p>
                </div>

                <div style='background: #f3f4f6; padding: 16px 30px; text-align: center;'>
                    <p style='font-size: 12px; color: #999; margin: 0;'>This is an automated email. Please do not reply directly to this message.</p>
                </div>

            </div>
        ";

            $mail->send();

            $msg = "Purchase Order Confirmed & Email Sent";
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

    case "load_po":
        $po_id = $_POST["po_id"];
        $poDetailsResult = $purchaseObj->getPODetails($po_id);
        $poDetails = $poDetailsResult->fetch_assoc();
        ?>

        <div class="modal-body">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <span class="fw-semibold text-secondary">PO ID</span>
                        <span>#<?php echo $poDetails["po_id"]; ?></span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <span class="fw-semibold text-secondary">Supplier</span>
                        <span><?php echo $poDetails["supplier_name"]; ?></span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <span class="fw-semibold text-secondary">Item</span>

                        <span class="text-end">
                            <?php
                            echo "ID: " . $poDetails["stock_item_id"] . " " .
                                $poDetails["stock_item_name"] . " " .
                                $poDetails["stock_item_color_code"];
                            ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <span class="fw-semibold text-secondary">Quantity</span>

                        <span>
                            <?php
                            echo $poDetails["ordered_qty"] . " " .
                                $poDetails["stock_unit_name"];
                            ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <span class="fw-semibold text-secondary">Unit Price</span>
                        <span class="fw-bold text-success">
                            Rs. <?php echo $poDetails["unit_price"]; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <span class="fw-semibold text-secondary">Total Price</span>
                        <span class="fw-bold text-success">
                            Rs. <?php echo $poDetails["total_price"]; ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold text-secondary">PO Status</span>

                        <span class="badge bg-primary px-3 py-2">
                            <?php echo $poDetails["po_status"]; ?>
                        </span>
                    </div>

                </div>

            </div>

        </div>
        <?php
        break;

    case "delivered_po":

        $po_id = $_POST["po_id"];
        $stock_item_id = $_POST["stock_item_id"];
        $ordered_qty = $_POST["ordered_qty"];
        $delivery_ref = $_POST["delivery_ref"];

        try {

            $poDetailsResult = $purchaseObj->getPODetails($po_id);
            $poDetails = $poDetailsResult->fetch_assoc();
            $stock_purchase_request_id = $poDetails["stock_purchase_request_id"];
            $po_amount = $poDetails["total_price"];

            $purchaseObj->deliveredPO($po_id);
            $purchaseObj->completeStockPurchaseRequest($stock_purchase_request_id);

            $transaction_type = "IN";
            $stockObj->addInventoryStockItem($stock_item_id, $ordered_qty);
            $stockObj->addStockTransaction($stock_item_id, $transaction_type, $ordered_qty, $delivery_ref);

            $purchaseObj->addPOPayment($po_id,$po_amount);

            $msg = "Purchase Order Delivered";
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
