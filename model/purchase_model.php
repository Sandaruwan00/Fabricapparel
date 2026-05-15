<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Purchase
{
    public function createRFQ($stock_purchase_request_id, $created_by, $created_date, $message)
    {
        $con = $GLOBALS['con'];

        $sql = "INSERT INTO rfq 
            (request_id, created_by, created_date, rfq_message)
            VALUES 
            ('$stock_purchase_request_id', '$created_by', '$created_date', '$message')";

        $con->query($sql) or die($con->error);

        return $con->insert_id;
    }

    public function addRFQSupplier($rfq_id, $supplier_id)
    {
        $con = $GLOBALS['con'];

        $sql = "INSERT INTO rfq_suppliers (rfq_id, supplier_id)
            VALUES ('$rfq_id', '$supplier_id')";

        return $con->query($sql) or die($con->error);
    }

    

    public function getRFQ($rfq_id)
    {
        $con = $GLOBALS['con'];

        $sql = "SELECT *

            FROM rfq r

            JOIN stock_purchase_request spr ON r.request_id = spr.stock_purchase_request_id
            JOIN stock_items si ON spr.stock_item_id = si.stock_item_id
            JOIN stock_units su ON si.stock_unit_id = su.stock_unit_id

            WHERE r.rfq_id = '$rfq_id'";

        return $con->query($sql);
    }

    public function getAllQuotationSuppliers($rfq_id)
    {
        $con = $GLOBALS['con'];

        $sql = "SELECT *
            FROM rfq_suppliers rs

            JOIN supplier s ON rs.supplier_id = s.supplier_id
            JOIN rfq r ON rs.rfq_id = r.rfq_id
            JOIN stock_purchase_request spr ON r.request_id = spr.stock_purchase_request_id
            JOIN stock_items si ON spr.stock_item_id = si.stock_item_id

            WHERE rs.rfq_id = '$rfq_id'";

        return $con->query($sql);
    }

    

    public function closeRFQ($rfq_id)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE rfq SET rfq_status='Closed' WHERE rfq_id = '$rfq_id'";
        $con->query($sql) or die($con->error);
    }

    public function rejectRFQPO($rfq_id)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE rfq SET rfq_po='Rejected' WHERE rfq_id = '$rfq_id'";
        $con->query($sql) or die($con->error);
    }

    public function rejectRFQSuppliers($rfq_id)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE rfq_suppliers SET status='Rejected' WHERE rfq_id = '$rfq_id'";
        $con->query($sql) or die($con->error);
    }

    public function createPurchaseOrder($rfq_id, $supplier_id, $stock_item_id, $quantity, $total_price)
    {
        $con = $GLOBALS['con'];
        $sql = "INSERT INTO purchase_orders(rfq_id, supplier_id, stock_item_id, quantity, total_price) VALUES ('$rfq_id','$supplier_id','$stock_item_id','$quantity','$total_price')";
        $con->query($sql) or die($con->error);
        return $con->insert_id;
    }

    public function selectSupplier($rfq_supplier_id)
    {
        $con = $GLOBALS['con'];
        $sql = "UPDATE rfq_suppliers SET status='Selected' WHERE rfq_supplier_id = '$rfq_supplier_id'";
        $con->query($sql) or die($con->error);
    }

    public function rejectOtherSuppliers($rfq_id){
        $con = $GLOBALS['con'];
        $sql = "UPDATE rfq_suppliers r SET r.status='Rejected' WHERE rfq_id = '$rfq_id' AND r.status != 'Selected'";
        $con->query($sql) or die($con->error);
    }
    
    public function rfqPOUpdate($rfq_id, $rfq_po){
        $con = $GLOBALS['con'];
        $sql = "UPDATE rfq SET rfq_po ='$rfq_po' WHERE rfq_id = '$rfq_id'";
        $con->query($sql) or die($con->error);
    }



}
