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


}
