<?php

include_once  '../commons/db_connection.php';

$dbcon = new DbConnection();

class Transport
{

    public function getAllDistrict()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM district";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllVehicles()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM vehicles";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllAvailableVehicles()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM vehicles WHERE vehicle_status = 'Available'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function checkVehicleNumberExists($vehicle_number)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM vehicles WHERE vehicle_number = '$vehicle_number'";
        $result = $con->query($sql);
        return $result;
    }

    public function addVehicle($vehicle_number, $vehicle_type, $vehicle_capacity)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO vehicles(vehicle_number, vehicle_type, vehicle_capacity) VALUES ('$vehicle_number','$vehicle_type','$vehicle_capacity')";
        $con->query($sql) or die($con->error);
    }


    public function updateVehicleStatus($vehicle_id, $vehicle_status)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE vehicles SET vehicle_status='$vehicle_status' WHERE vehicle_id = '$vehicle_id'";
        $con->query($sql) or die($con->error);
    }

    public function updateVehicle($vehicle_id, $vehicle_number, $vehicle_type, $vehicle_capacity)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE vehicles SET vehicle_number='$vehicle_number',vehicle_type='$vehicle_type',vehicle_capacity='$vehicle_capacity' WHERE vehicle_id = '$vehicle_id'";
        $con->query($sql) or die($con->error);
    }

    public function checkVehicleNumberExistsForUpdate($vehicle_id, $vehicle_number)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM vehicles 
            WHERE vehicle_number = '$vehicle_number' 
            AND vehicle_id != '$vehicle_id'";
        $result = $con->query($sql);
        return $result;
    }

    public function getVehicle($vehicle_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM vehicles WHERE vehicle_id = '$vehicle_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getVehicleLogs($vehicle_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM vehicle_logs WHERE vehicle_id = '$vehicle_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function addVehicleLog($vehicle_id, $transport_id, $vehicle_action, $vehicle_remarks)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO vehicle_logs(vehicle_id, transport_id, vehicle_action, vehicle_remarks) VALUES ('$vehicle_id','$transport_id','$vehicle_action','$vehicle_remarks')";
        $con->query($sql) or die($con->error);
    }

    public function getAllDrivers()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM drivers";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllAvailableDrivers()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM drivers WHERE driver_status = 'Available'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function activateDriver($driver_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE drivers SET driver_status = 'Available' WHERE driver_id = '$driver_id'";
        $con->query($sql) or die($con->error);
    }

    public function deactivateDriver($driver_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE drivers SET driver_status = 'Deactive' WHERE driver_id = '$driver_id'";
        $con->query($sql) or die($con->error);
    }

    public function addDriver($driver_name, $driver_nic, $driver_phone, $driver_license_no, $driver_address)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO drivers(driver_name, driver_nic, driver_phone, driver_license_no, driver_address) VALUES ('$driver_name','$driver_nic','$driver_phone','$driver_license_no','$driver_address')";
        $con->query($sql) or die($con->error);
    }

    public function checkDriverNICExists($driver_nic)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM drivers WHERE driver_nic = '$driver_nic'";
        $result = $con->query($sql);
        return $result;
    }

    public function checkDriverNICExistsForUpdate($driver_id, $driver_nic)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM drivers 
            WHERE driver_nic = '$driver_nic' 
            AND driver_id != '$driver_id'";
        $result = $con->query($sql);
        return $result;
    }

    public function updateDriver($driver_id, $driver_name, $driver_nic, $driver_phone, $driver_license_no, $driver_address)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE drivers SET driver_name='$driver_name',driver_nic='$driver_nic',driver_phone='$driver_phone',driver_license_no='$driver_license_no',driver_address='$driver_address' WHERE driver_id = '$driver_id'";
        $con->query($sql) or die($con->error);
    }

    public function updateDriverStatus($driver_id, $driver_status)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE drivers SET driver_status='$driver_status' WHERE driver_id = '$driver_id'";
        $con->query($sql) or die($con->error);
    }

    public function getDriver($driver_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM drivers WHERE driver_id = '$driver_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getDispatchedShipments()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM shipments s, district d WHERE s.shipment_status = 'Dispatched' AND s.delivery_location = d.district_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getAllTransports()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM transport t, district d, vehicles v, drivers dr WHERE d.district_id = t.transport_location AND t.vehicle_id = v.vehicle_id AND t.driver_id = dr.driver_id";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function getDriverLogs($driver_id)
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM driver_logs WHERE driver_id = '$driver_id'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }

    public function addDriverLog($driver_id, $transport_id, $driver_action, $driver_remarks)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO driver_logs(driver_id, transport_id, driver_action, driver_remarks) VALUES ('$driver_id','$transport_id','$driver_action','$driver_remarks')";
        $con->query($sql) or die($con->error);
    }

    public function addTransport($shipment_id, $district_id, $vehicle_id, $driver_id)
    {
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO transport(shipment_id, transport_location, vehicle_id, driver_id) VALUES ('$shipment_id','$district_id','$vehicle_id','$driver_id')";
        $con->query($sql) or die($con->error);
    }

    public function rejectTransport($transport_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE transport SET transport_status='Rejected' WHERE transport_id = '$transport_id'";
        $con->query($sql) or die($con->error);
    }

    public function confirmTransport($transport_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE transport SET transport_status='Confirmed' WHERE transport_id = '$transport_id'";
        $con->query($sql) or die($con->error);
    }

    public function startTransport($transport_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE transport SET transport_status='Started' WHERE transport_id = '$transport_id'";
        $con->query($sql) or die($con->error);
    }

    public function deliverTransport($transport_id)
    {
        $con = $GLOBALS["con"];
        $sql = "UPDATE transport SET transport_status='Delivered' WHERE transport_id = '$transport_id'";
        $con->query($sql) or die($con->error);
    }

    public function getOngoingTransports()
    {
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM transport WHERE transport_status = 'Confirmed' AND transport_status = 'Started'";
        $result = $con->query($sql) or die($con->error);
        return $result;
    }
}
