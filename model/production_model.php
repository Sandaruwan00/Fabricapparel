<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Production
{

    public function getAllOrdersInProduction()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM orders o, buyer_company bc, order_status os, plan p WHERE o.status_id = 5 AND o.company_id = bc.company_id AND o.status_id =os.status_id AND o.order_id = p.order_id AND p.plan_status = 'Approved'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function addProdcution($order_id)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO production(order_id) VALUES ('$order_id')";
        $con->query($sql) or die($con->error);
    }

    public function getAllProductionOrders()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM orders o, buyer_company bc, order_status os, plan p, production pr WHERE o.company_id = bc.company_id AND o.status_id =os.status_id AND o.order_id = p.order_id AND o.order_id = pr.order_id AND p.plan_status = 'Approved'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function addProductionStockRequest($stock_item_id, $psr_qty)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO production_stock_request(stock_item_id, psr_qty) VALUES ('$stock_item_id','$psr_qty')";
        $con->query($sql) or die($con->error);
    }
    
    public function endProduction($production_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE production SET production_end = NOW() , production_status = 'Finished' WHERE production_id = '$production_id'";
        $con->query($sql) or die($con->error);
    }
}
