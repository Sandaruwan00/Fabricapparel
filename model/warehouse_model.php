<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Warehouse
{
    public function getAllPackedPackages()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * FROM packing pac, production pro, orders o, buyer_company bc WHERE pac.packing_status = 'Packed' AND pac.production_id = pro.production_id AND pro.order_id = o.order_id AND o.company_id = bc.company_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function addWarehousePackage($packing_id)
    {
        $con = $GLOBALS['con'];
        $sql1 = "INSERT INTO warehouse_pkg(packing_id) VALUES ('$packing_id')";
        $sql2 = "UPDATE packing SET packing_status='Dispatched' WHERE packing_id = '$packing_id'";
        $con->query($sql1) or die($con->error);
        $con->query($sql2) or die($con->error);
    }

    public function getAllWarehousePackages()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * FROM warehouse_pkg wp, packing pac, production pro, orders o, buyer_company bc, district d WHERE wp.packing_id = pac.packing_id AND pac.production_id = pro.production_id AND pro.order_id = o.order_id AND o.company_id = bc.company_id AND o.district_id = d.district_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function getInWarehousePackages()
    {
        $con = $GLOBALS['con'];
        $sql = "SELECT * FROM warehouse_pkg wp, packing pac, production pro, orders o, buyer_company bc, district d WHERE wp.packing_id = pac.packing_id AND wp.warehouse_pkg_status = 'In Warehouse' AND pac.production_id = pro.production_id AND pro.order_id = o.order_id AND o.company_id = bc.company_id AND o.district_id = d.district_id";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }

    public function getApprovedOP($order_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT *
                FROM order_payment op
                LEFT JOIN orders o ON op.order_id = o.order_id
                WHERE op.payment_status = 'Approved' AND op.order_id = '$order_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function createShipment($delivery_location)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO shipments(delivery_location) VALUES ('$delivery_location')";
        $con->query($sql) or die($con->error);
        $shipment_id = $con->insert_id;
        return $shipment_id;
    }

    public function addShipmentItems($shipment_id, $packing_id)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO shipment_items(shipment_id, packing_id) VALUES ('$shipment_id','$packing_id')";
        $con->query($sql) or die($con->error);
    }

    public function getOrderID($packing_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM packing pac,production pro, orders o WHERE pac.production_id = pro.production_id AND pro.order_id = o.order_id AND pac.packing_id = '$packing_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllShipments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM shipments s, district d WHERE s.delivery_location = d.district_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function updateWarehousePackageStatus($packing_id, $warehouse_pkg_status)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE warehouse_pkg SET warehouse_pkg_status='$warehouse_pkg_status' WHERE packing_id = '$packing_id'";
        $con->query($sql) or die($con->error);
    }

    public function getShipment($shipment_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM shipments s, district d WHERE s.delivery_location = d.district_id AND s.shipment_id = '$shipment_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllShipmentItems($shipment_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM shipment_items si, packing pac, production p, orders o, buyer_company bc WHERE si.shipment_id = '$shipment_id' AND si.packing_id = pac.packing_id AND pac.production_id = p.production_id AND p.order_id = o.order_id AND o.company_id = bc.company_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function confirmShipment($shipment_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE shipments SET shipment_status='Confirmed' WHERE shipment_id = '$shipment_id'";
        $con->query($sql) or die($con->error);
    }

    public function rejectShipment($shipment_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE shipments SET shipment_status='Rejected' WHERE shipment_id = '$shipment_id'";
        $con->query($sql) or die($con->error);
    }

    public function dispatchShipment($shipment_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE shipments SET shipment_status='Dispatched' WHERE shipment_id = '$shipment_id'";
        $con->query($sql) or die($con->error);
    }

    public function transportAssignShipment($shipment_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE shipments SET shipment_status='Transport Assigned' WHERE shipment_id = '$shipment_id'";
        $con->query($sql) or die($con->error);
    }

    public function getWarehouseActivityReport()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * 
            FROM order_status_log osl 
            INNER JOIN orders o ON o.order_id=osl.order_id 
            INNER JOIN buyer_company bc ON bc.company_id =o.company_id  
            INNER JOIN user u ON u.user_id=osl.changed_by 
            WHERE osl.status_id IN (10,12) 
            ORDER BY osl.changed_at ASC";
        return $con->query($sql);
    }

    public function getShipmentLocationAnalysis()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT d.district_name, COUNT(s.shipment_id) AS shipment_count FROM shipments s INNER JOIN district d ON d.district_id=s.delivery_location GROUP BY d.district_id ORDER BY shipment_count ASC";
        return $con->query($sql);
    }
}
