<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Purchase
{
    public function addSupplierRequest($supplier_id, $stock_purchase_request_id)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO supplier_requests(supplier_id, stock_purchase_request_id) VALUES ('$supplier_id','$stock_purchase_request_id')";
        $con->query($sql) or die($con->error);
    }

    public function getStockPurchaseRequestItem($stock_purchase_request_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_purchase_request spr, stock_items si, stock_units su WHERE stock_purchase_request_id = '$stock_purchase_request_id' AND spr.stock_item_id = si.stock_item_id AND si.stock_unit_id = su.stock_unit_id";
        $result = $con->query($sql) or die($con->error);
        return $result->fetch_assoc();
    }
    
    public function getPOs()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM purchase_orders po, stock_purchase_request spr, stock_items si, stock_units su, supplier s WHERE po.stock_purchase_request_id = spr.stock_purchase_request_id AND spr.stock_item_id = si.stock_item_id AND si.stock_unit_id = su.stock_unit_id AND po.supplier_id = s.supplier_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }
    
    public function getSentStockPurchaseRequests()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM stock_purchase_request spr, stock_items si, stock_units su WHERE spr.stock_item_id = si.stock_item_id AND si.stock_unit_id = su.stock_unit_id AND spr.request_status = 'Sent'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function addPO($stock_purchase_request_id,$supplier_id,$ordered_qty,$unit_price,$total_price)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO purchase_orders(stock_purchase_request_id, supplier_id, ordered_qty, unit_price, total_price) VALUES ('$stock_purchase_request_id','$supplier_id','$ordered_qty','$unit_price','$total_price')";
        $con->query($sql) or die($con->error);
    }

    public function updateStockPurchaseRequestStatus($stock_purchase_request_id,$request_status)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_purchase_request SET request_status = '$request_status' WHERE stock_purchase_request_id = '$stock_purchase_request_id'";
        $con->query($sql) or die($con->error);
    }

    public function rejectPO($po_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE purchase_orders SET po_status = 'Cancelled' WHERE po_id = '$po_id'";
        $con->query($sql) or die($con->error);
    }
    
    public function confirmPO($po_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE purchase_orders SET po_status = 'Confirmed' WHERE po_id = '$po_id'";
        $con->query($sql) or die($con->error);
    }

    public function getPODetails($po_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM purchase_orders po, stock_purchase_request spr, stock_items si, stock_units su, supplier s WHERE po.stock_purchase_request_id = spr.stock_purchase_request_id AND spr.stock_item_id = si.stock_item_id AND si.stock_unit_id = su.stock_unit_id AND po.supplier_id = s.supplier_id AND po.po_id = '$po_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function deliveredPO($po_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE purchase_orders SET po_status = 'Delivered' WHERE po_id = '$po_id'";
        $con->query($sql) or die($con->error);
    }

    public function completeStockPurchaseRequest($stock_purchase_request_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE stock_purchase_request SET request_status = 'Completed' WHERE stock_purchase_request_id = '$stock_purchase_request_id'";
        $con->query($sql) or die($con->error);
    }

    public function addPOPayment($po_id,$po_amount)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO purchase_order_payments(po_id, po_amount) VALUES ('$po_id','$po_amount')";
        $con->query($sql) or die($con->error);
    }


}
