<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Planning{


    public function addPlan($order_id)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO plan(order_id) VALUES ('$order_id');";
        $con->query($sql) or die($con->error);
        $plan_id = $con->insert_id;
        return $plan_id;
    }
    
    public function getPlan($plan_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM plan p, orders o WHERE p.plan_id = '$plan_id' AND p.order_id = o.order_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function getAllPlans(){
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM plan p, orders o, buyer_company bc WHERE p.order_id = o.order_id AND o.company_id = bc.company_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function rejectPlan($plan_id, $status){
        $con = $GLOBALS["con"];
        $sql = "UPDATE plan SET plan_status ='$status' WHERE plan_id = '$plan_id'";
        $con->query($sql) or die($con->error);
    }
    
    public function approvePlan($plan_id, $status){
        $con = $GLOBALS["con"];
        $sql = "UPDATE plan SET plan_status ='$status' WHERE plan_id = '$plan_id'";
        $con->query($sql) or die($con->error);
    }



}