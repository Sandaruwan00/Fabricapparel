<?php
include '../commons/session.php';
include '../model/transport_model.php';
include '../model/warehouse_model.php';
include '../model/order_model.php';

$userrow = $_SESSION["user"];

if (!isset($_GET["status"])) {
?>
    <script>
        window.location = "../view/login.php";
    </script>
    <?php
}

$status = $_GET["status"];


$transportObj = new Transport();
$warehouseObj = new Warehouse();
$orderObj = new Order();


switch ($status) {

    case "add_vehicle":

        $vehicle_number = $_POST["vehicle_number"];
        $vehicle_type = $_POST["vehicle_type"];
        $vehicle_capacity = $_POST["vehicle_capacity"];

        try {

            $transportObj->addVehicle($vehicle_number, $vehicle_type, $vehicle_capacity);

            $msg = "Vehicle Added";
            $msg = base64_encode($msg);
    ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "update_vehicle":

        $vehicle_id = $_POST["vehicle_id"];
        $vehicle_number = $_POST["vehicle_number"];
        $vehicle_type = $_POST["vehicle_type"];
        $vehicle_capacity = $_POST["vehicle_capacity"];

        try {

            $transportObj->updateVehicle($vehicle_id, $vehicle_number, $vehicle_type, $vehicle_capacity);

            $msg = "Vehicle Successfuly Updated";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "set_maintenance":

        $vehicle_id = $_GET["vehicle_id"];
        $vehicle_status = "Maintenance";
        $transport_id = null;
        $vehicle_remarks = "Vehicle set to maintenance";

        try {

            $transportObj->updateVehicleStatus($vehicle_id, $vehicle_status);
            $transportObj->addVehicleLog($vehicle_id, $transport_id, $vehicle_status, $vehicle_remarks);

            $msg = "Vehicle set to maintenance";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "set_available_vehicle":

        $vehicle_id = $_GET["vehicle_id"];
        $vehicle_status = "Available";
        $transport_id = null;
        $vehicle_remarks = "Vehicle set to available";

        try {

            $transportObj->updateVehicleStatus($vehicle_id, $vehicle_status);
            $transportObj->addVehicleLog($vehicle_id, $transport_id, $vehicle_status, $vehicle_remarks);

            $msg = "Vehicle set to available";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/vehicle.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;


    case "load_vehicle":

        $vehicle_id = $_POST["vehicle_id"];

        $vehicleResult = $transportObj->getVehicle($vehicle_id);
        $vehiclerow = $vehicleResult->fetch_assoc();

        if ($vehiclerow["vehicle_status"] == "Available") {
            $color = "bg-success";
        } elseif ($vehiclerow["vehicle_status"] == "Assigned") {
            $color = "bg-warning";
        } else {
            $color = "bg-danger";
        }



        ?>

        <div class="modal-body">

            <!-- Vehicle Info -->
            <div class="row mb-3">

                <div class="col-md-6">
                    <div class="p-2 border rounded mb-2">
                        <strong>Number:</strong>
                        <span class="float-end fw-bold"><?php echo $vehiclerow["vehicle_number"]; ?></span>
                    </div>



                    <div class="p-2 border rounded">
                        <strong>Capacity:</strong>
                        <span class="float-end"><?php echo $vehiclerow["vehicle_capacity"]; ?> kg</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-2 border rounded mb-2">
                        <strong>Type:</strong>
                        <span class="float-end"><?php echo $vehiclerow["vehicle_type"]; ?></span>
                    </div>
                    <div class="p-2 border rounded <?= $color; ?>">
                        <strong>Status:</strong>
                        <span class="float-end"><?php echo $vehiclerow["vehicle_status"]; ?></span>
                    </div>
                </div>

            </div>

            <hr>


            <h6 class="fw-bold">Vehicle Logs</h6>

            <div id="vehicle_logs" style="max-height:300px; overflow-y:auto;">
                <table class="table table-bordered table-hover" id="vehiclelogtable">
                    <thead class="table-dark">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="28%">Log Time</th>
                            <th width="18%">Transport ID</th>
                            <th width="10%">Action</th>
                            <th width="42%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $vehicleLogResult = $transportObj->getVehicleLogs($vehicle_id);
                        while ($row = $vehicleLogResult->fetch_assoc()) {

                        ?>
                            <tr>
                                <td><?php echo $row['vehicle_log_id']; ?></td>
                                <td><?php echo $row['vehicle_log_time']; ?></td>
                                <td><?php echo $row['transport_id'] ?? "-"; ?></td>
                                <td><?php echo $row['vehicle_action']; ?></td>
                                <td><?php echo $row['vehicle_remarks']; ?></td>

                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
        <script>
            $(document).ready(function() {
                $("#vehiclelogtable").DataTable({
                    "order": [
                        [1, "desc"]
                    ]
                });
            });
        </script>


        <?php

        break;


    case "activate_driver":

        $driver_id = $_GET["driver_id"];

        try {

            $transportObj->activateDriver($driver_id);

            $transport_id = null;
            $driver_action = "Available";
            $driver_remarks = "-";

            $transportObj->addDriverLog($driver_id, $transport_id, $driver_action, $driver_remarks);

            $msg = "Driver Activated";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "deactivate_driver":

        $driver_id = $_GET["driver_id"];

        try {

            $transportObj->deactivateDriver($driver_id);

            $transport_id = null;
            $driver_action = "Deactive";
            $driver_remarks = "-";

            $transportObj->addDriverLog($driver_id, $transport_id, $driver_action, $driver_remarks);

            $msg = "Driver Dectivated";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "add_driver":

        $driver_name = $_POST["driver_name"];
        $driver_nic = $_POST["driver_nic"];
        $driver_phone = $_POST["driver_phone"];
        $driver_license_no = $_POST["driver_license_no"];
        $driver_address = $_POST["driver_address"];

        try {

            $transportObj->addDriver($driver_name, $driver_nic, $driver_phone, $driver_license_no, $driver_address);

            $msg = "Driver Successfuly Added";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "update_driver":

        $driver_id = $_POST["driver_id"];
        $driver_name = $_POST["driver_name"];
        $driver_nic = $_POST["driver_nic"];
        $driver_phone = $_POST["driver_phone"];
        $driver_license_no = $_POST["driver_license_no"];
        $driver_address = $_POST["driver_address"];

        try {

            $transportObj->updateDriver($driver_id, $driver_name, $driver_nic, $driver_phone, $driver_license_no, $driver_address);

            $msg = "Driver Successfuly Updated";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/driver.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "load_driver":

        $driver_id = $_POST["driver_id"];

        $driverResult = $transportObj->getDriver($driver_id);
        $driver = $driverResult->fetch_assoc();

        if ($driver["driver_status"] == "Available") {
            $color = "bg-success";
        } elseif ($driver["driver_status"] == "Assigned") {
            $color = "bg-warning";
        } else {
            $color = "bg-danger";
        }
        ?>

        <div class="modal-body">

            <div class="row mb-3">

                <div class="col-md-6">
                    <div class="p-2 border rounded mb-2">
                        <strong>Name:</strong>
                        <span class="float-end"><?php echo $driver["driver_name"]; ?></span>
                    </div>

                    <div class="p-2 border rounded mb-2">
                        <strong>NIC:</strong>
                        <span class="float-end"><?php echo $driver["driver_nic"]; ?></span>
                    </div>

                    <div class="p-2 border rounded">
                        <strong>Phone:</strong>
                        <span class="float-end"><?php echo $driver["driver_phone"]; ?></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-2 border rounded mb-2">
                        <strong>License No:</strong>
                        <span class="float-end"><?php echo $driver["driver_license_no"]; ?></span>
                    </div>

                    <div class="p-2 border rounded mb-2">
                        <strong>Address:</strong>
                        <span class="float-end"><?php echo $driver["driver_address"]; ?></span>
                    </div>

                    <div class="p-2 border rounded <?php echo $color; ?> text-white">
                        <strong>Status:</strong>
                        <span class="float-end"><?php echo $driver["driver_status"]; ?></span>
                    </div>
                </div>

            </div>

            <hr>


            <h6 class="fw-bold">Driver Logs</h6>

            <div class="row">
                <div class="col-md-12">
                    <div id="driver_logs" style="max-height:300px; overflow-y:auto;">
                        <table class="table table-bordered table-hover" id="driverlogtable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="2%">ID</th>
                                    <th width="28%">Log Time</th>
                                    <th width="18%">Transport ID</th>
                                    <th width="10%">Action</th>
                                    <th width="42%">Remarks</th>
                                </tr>                          
                            </thead>
                            <tbody>
                                <?php
                                $driverLogResult = $transportObj->getDriverLogs($driver_id);
                                while ($row = $driverLogResult->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?php echo $row['driver_log_id']; ?></td>
                                        <td><?php echo $row['driver_log_time']; ?></td>
                                        <td><?php echo $row['transport_id']; ?></td>
                                        <td><?php echo $row['driver_action']; ?></td>
                                        <td><?php echo $row['driver_remarks']; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>


        </div>

        <script>
            $(document).ready(function() {
                $("#driverlogtable").DataTable({
                    "order": [
                        [1, "desc"]
                    ]
                });
            });
        </script>

        <?php
        break;

    case "assign_transport":

        $shipment_id = $_POST["shipment_id"];
        $district_id = $_POST["district_id"];
        $vehicle_id = $_POST["vehicle_id"];
        $driver_id = $_POST["driver_id"];

        try {

            if ($vehicle_id == "" || $driver_id == "") {
                throw new Exception("Assign both vehicle and driver");
            }

            $transportObj->addTransport($shipment_id, $district_id, $vehicle_id, $driver_id);

            $packageResults = $warehouseObj->getAllShipmentItems($shipment_id);
            while ($pkgrow = $packageResults->fetch_assoc()) {
                $packing_id = $pkgrow["packing_id"];

                $orderIDResult = $warehouseObj->getOrderID($packing_id);
                $orderIDRow = $orderIDResult->fetch_assoc();
                $order_id = $orderIDRow["order_id"];

                $user_id = $userrow["user_id"];
                $status_id = 13;
                $remarks = "-";

                $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);
            }

            $warehouseObj->transportAssignShipment($shipment_id);

            $driver_status = "Assigned";
            $transportObj->updateDriverStatus($driver_id, $driver_status);

            $vehicle_status = "Assigned";
            $transportObj->updateVehicleStatus($vehicle_id, $vehicle_status);

            $msg = "Transport Successfully Assigned";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/add-transport.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/add-transport.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "reject_transport":

        $transport_id = $_POST["transport_id"];
        $shipment_id = $_POST["shipment_id"];
        $vehicle_id = $_POST["vehicle_id"];
        $driver_id = $_POST["driver_id"];

        try {

            $transportObj->rejectTransport($transport_id);

            $packageResults = $warehouseObj->getAllShipmentItems($shipment_id);
            while ($pkgrow = $packageResults->fetch_assoc()) {
                $packing_id = $pkgrow["packing_id"];

                $orderIDResult = $warehouseObj->getOrderID($packing_id);
                $orderIDRow = $orderIDResult->fetch_assoc();
                $order_id = $orderIDRow["order_id"];

                $user_id = $userrow["user_id"];
                $status_id = 12;
                $remarks = $_POST["remarks"];

                $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);
            }

            $warehouseObj->dispatchShipment($shipment_id);

            $driver_status = "Available";
            $transportObj->updateDriverStatus($driver_id, $driver_status);

            $vehicle_status = "Available";
            $transportObj->updateVehicleStatus($vehicle_id, $vehicle_status);


            $msg = "Transport Rejected";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
        <?php
        }
        break;

    case "confirm_transport":

        $transport_id = $_POST["transport_id"];

        try {

            $transportObj->confirmTransport($transport_id);

            $msg = "Transport Confirmed";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;

        case "start_transport":

        $transport_id = $_POST["transport_id"];
        $shipment_id = $_POST["shipment_id"];


        try {

            $transportObj->startTransport($transport_id);

            $packageResults = $warehouseObj->getAllShipmentItems($shipment_id);
            while ($pkgrow = $packageResults->fetch_assoc()) {
                $packing_id = $pkgrow["packing_id"];

                $orderIDResult = $warehouseObj->getOrderID($packing_id);
                $orderIDRow = $orderIDResult->fetch_assoc();
                $order_id = $orderIDRow["order_id"];

                $user_id = $userrow["user_id"];
                $status_id = 14;
                $remarks = "Transport Started | Transport ID - #$transport_id";

                $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);
            }

            $vehicle_id = $_POST["vehicle_id"];
            $vehicle_action = "Assigned";
            $vehicle_remarks = "Transport Started";
            
            $transportObj->addVehicleLog($vehicle_id, $transport_id, $vehicle_action, $vehicle_remarks);

            $driver_id = $_POST["driver_id"];
            $driver_action = "Assigned";
            $driver_remarks = "Transport Started";

            $transportObj->addDriverLog($driver_id, $transport_id, $driver_action, $driver_remarks);

            $msg = "Transport Started";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;

        case "deliver_transport":

        $transport_id = $_POST["transport_id"];
        $shipment_id = $_POST["shipment_id"];


        try {

            $transportObj->deliverTransport($transport_id);

            $packageResults = $warehouseObj->getAllShipmentItems($shipment_id);
            while ($pkgrow = $packageResults->fetch_assoc()) {
                $packing_id = $pkgrow["packing_id"];

                $orderIDResult = $warehouseObj->getOrderID($packing_id);
                $orderIDRow = $orderIDResult->fetch_assoc();
                $order_id = $orderIDRow["order_id"];

                $user_id = $userrow["user_id"];
                $status_id = 15;
                $remarks = "-";

                $orderObj->updateOrderStatus($order_id, $user_id, $status_id, $remarks);
            }

            $vehicle_id = $_POST["vehicle_id"];
            $vehicle_status = "Available";
            $vehicle_remarks = "Transport Completed";
            
            $transportObj->updateVehicleStatus($vehicle_id, $vehicle_status);
            $transportObj->addVehicleLog($vehicle_id, $transport_id, $vehicle_status, $vehicle_remarks);

            $driver_id = $_POST["driver_id"];
            $driver_status = "Available";
            $driver_remarks = "Transport Completed";

            $transportObj->updateDriverStatus($driver_id, $driver_status);
            $transportObj->addDriverLog($driver_id, $transport_id, $driver_status, $driver_remarks);

            $msg = "Transport Completed";
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
        <?php

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $msg = base64_encode($msg);
        ?>
            <script>
                window.location = "../view/view-transports.php?msg=<?php echo $msg; ?>";
            </script>
<?php
        }
        break;
}
