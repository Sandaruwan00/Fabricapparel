<?php

case "create_shipment":

    $start_location = $_POST["start_location"];
    $destination_location = $_POST["start_location"];

    $selected_orders = $_POST["selected_orders"] ?? [];

    if (empty($selected_orders)) {
        $msg = base64_encode("No Orders Selected");
        ?>
        <script>
            window.location = "../view/view-warehouse-parcels.php?msg=<?php echo $msg; ?>";
        </script>
        <?php
        break;
    }

    try {

        $shipment_id = $warehouseObj->addShipment($start_location, $destination_location);

        foreach($selected_orders as $order_id){
            $warehouseObj->addShipmentOrders($shipment_id, $order_id);
        }

        $msg = base64_encode("Shipment Created Successfully");
        ?>
        <script>
            window.location = "../view/view-warehouse-parcels.php?msg=<?php echo $msg; ?>";
        </script>
        <?php

    } catch (Exception $ex) {
        $msg = base64_encode($ex->getMessage());
        ?>
        <script>
            window.location = "../view/view-warehouse-parcels.php?msg=<?php echo $msg; ?>";
        </script>
        <?php
    }
    break;



    public function addShipment($start_location, $destination_location){
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO shipment(shipment_start_location,shipment_destination_location)VALUES('$start_location','$lndestination_locationame')";
        $con->query($sql) or die($con->error);
        $shipment_id = $con->insert_id;
        return $shipment_id;
    }
    
    public function addShipmentOrders($shipment_id, $order_id){
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO shipment_orders(shipment_id,order_id)VALUES('$shipment_id','$order_id')";
        $con->query($sql) or die($con->error)
    }

?>


<?php
if($row["shipment_status"] == "Pending"){
    $color = "bg-warning";
} else {
    $color = "bg-success";
}
?>

<td class="<?php echo $color; ?>"><?php echo $row["shipment_status"]; ?></td>






<!-- Cancel Button -->
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal">
    Cancel
</button>

<!-- Modal -->
<div id="cancelModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content -->
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cancel Shipment</h4>
      </div>
      
      <div class="modal-body">
        <p>Are you sure you want to cancel this shipment?</p>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="submit" class="btn btn-danger">Cancel</button>
      </div>
      
    </div>

  </div>
</div>



<?php 


    public function cancelShipment($shipment_id){
        $con = $GLOBALS["con"];
        $sql = "UPDATE shipment SET shipment_status= 'Cancel' WHERE shipment_id ='$shipment_id'";
        $con->query($sql) or die($con->error)
    }
    
    public function cancelShipmentOrders($shipment_id){
        $con = $GLOBALS["con"];
        $sql = "UPDATE shipment_orders SET shipment_order_status= 'Cancel' WHERE shipment_id ='$shipment_id'";
        $con->query($sql) or die($con->error)
    }

    public function getShipmentOrders($shipment_id){
        $con = $GLOBALS["con"];
        $sql = "SELECT * FROM shipment_orders WHERE shipment_id ='$shipment_id'";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }
    
    public function setStatusCancelShipmentOrders($order_id, $status_id){
        $con = $GLOBALS["con"];
        $sql = "UPDATE orders SET order_status = '$status_id' WHERE order_id = '$order_id'";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }
    
    public function logStatusRecord($order_id, $status_id, $remarks){
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO status_log (order_id, status_id, remarks) VALUES ('$order_id', '$status_id', '$remarks')";
        $con->query($sql) or die($con->error);
    }




?>


<?php

    case "cancel_shipment":

            $shipment_id = $_POST["shipment_id"];
            $shipment_id = base64_decode($shipment_id);

            $status_id = 4;
            $remarks = $_POST["remarks"];

            try {

                $warehouseObj->cancelShipment($shipment_id);
                $warehouseObj->cancelShipmentOrders($shipment_id);


                $shipmentOrders = $warehouseObj->getShipmentOrders($shipment_id);

                while ($shipmentorderrow = $shipmentOrders->fetch_assoc()) {
                        $order_id = $shipmentorderrow['order_id'];
                    

                    $warehouseObj->setStatusCancelShipmentOrders($order_id, $status_id);
                    $orderObj->logStatusRecord($order_id, $status_id, $remarks);
                }
                
                

                $msg = "Shipment Cancelled";
                $msg = base64_encode($msg);
            ?>
                <script>
                    window.location = "../view/file.php?msg=<?php echo $msg; ?>";
                </script>
            <?php

            } catch (Exception $ex) {
                $msg = $ex->getMessage();
                $msg = base64_encode($msg);
            ?>
                <script>
                    window.location = "../view/file.php?msg=<?php echo $msg; ?>";
                </script>
            <?php
            }
            break;











        
            $name = $_POST["name"];
            $nic = $_POST["nic"];
            $dristrict = $_POST["district"];



            $vehicleObj->addDriver(.............$dristrict);


        



public function addDriver(.............$dristrict){
        $con = $GLOBALS["con"];
        $sql = "INSERT INTO driver (...........,driver_district,driver_loaction) VALUES (.........,'$dristrict','$location');";
        $results = $con->query($sql) or die($con->error);
        return $results;
    }


?>


































