<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Packing
{

    public function getAllCompletedProductions()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM production pro, orders o, buyer_company bc WHERE pro.production_status = 'Finished' AND pro.order_id = o.order_id AND o.company_id = bc.company_id AND o.status_id = 7";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }
    
    public function createPacking($production_id)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO packing(production_id) VALUES ('$production_id')";
        $con->query($sql) or die($con->error);
    }

    public function getAllPackings()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM packing pac, orders o, buyer_company bc, production pro WHERE pac.production_id = pro.production_id AND pro.order_id = o.order_id AND o.company_id = bc.company_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }
    
    public function getPacking($packing_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM packing pac, orders o, buyer_company bc, production pro WHERE pac.production_id = pro.production_id AND pro.order_id = o.order_id AND o.company_id = bc.company_id AND pac.packing_id = '$packing_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }
    
    public function finalizePacking($packing_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE packing SET packing_status='Packed' WHERE packing_id = '$packing_id'";
        $con->query($sql) or die($con->error);
    }
}
