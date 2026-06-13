<?php
include_once '../commons/session.php';
include_once '../model/transport_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$transportObj = new Transport();

$vehicleResult = $transportObj->getAllAvailableVehicles();


$totalTransportresult = $transportObj->getAllTransports();
$totaltransportcount = 0;
$startedtransportcount = 0;
$rejectedtransportcount = 0;
$deliveredtransportcount = 0;

while ($row = $totalTransportresult->fetch_assoc()) {

    $totaltransportcount++;

    if ($row["transport_status"] == "Started") {
        $startedtransportcount++;
    }
    else if ($row["transport_status"] == "Rejected") {
        $rejectedtransportcount++;
    }
    else if ($row["transport_status"] == "Delivered") {
        $deliveredtransportcount++;
    }
}

?>
<html>

<head>
    <?php include_once "../includes/bootstrap_css_includes.php" ?>
    <title>Transport Management</title>
</head>

<body style="border-radius:10px;">
    <div class="container">

        <?php $pageName = "TRANSPORT MANAGEMENT" ?>
        <?php include_once "../includes/header_row_module_includes.php"; ?>

        <!-- Top Buttons -->
        <div class="row">
            <div class="col-md-4" style="text-align:left;">
                <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="col-md-8" style="text-align:right;">
                <div class="btn-group">
                    <a href="add-transport.php" class="btn btn-outline-primary">Create Transport</a>
                    <a href="view-transports.php" class="btn btn-outline-success">View Transports</a>
                    <a href="driver.php" class="btn btn-outline-info">Drivers</a>
                    <a href="vehicle.php" class="btn btn-outline-dark">Vehicles</a>
                    <a href="generate-transport-report.php" class="btn btn-outline-warning">Generate Transport Reports</a>
                </div>
            </div>
        </div>

        <div class="row">
            &nbsp;
        </div>

        <!-- Summary Cards -->
        <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">

            <span class="h3 mb-4 fw-bold">Transport Summary</span>

            <div class="row d-flex justify-content-around text-center">

                <!-- Total -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Total Transports</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $totaltransportcount; ?>
                        </h1>
                    </div>
                </div>

                <!-- Assigned -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Started</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $startedtransportcount; ?>
                        </h1>
                    </div>
                </div>

                <!-- In Transit -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Rejected</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $rejectedtransportcount; ?>
                        </h1>
                    </div>
                </div>

                <!-- Delivered -->
                <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
                    <div class="card-header">Delivered</div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $deliveredtransportcount; ?>
                        </h1>
                    </div>
                </div>

            </div>
        </div>




        <div class="row">&nbsp;</div>


        <div class="row justify-content-between" style="gap: 20px;">
            <?php
            $ongoingTransports = $transportObj->getOngoingTransports();
            ?>

            <div class="col-md-6 shadow-lg p-3 bg-white rounded" style="max-width: 40rem;">
                
                <h5 class="fw-bold">Ongoing Transports</h5>
             

                <?php if ($ongoingTransports->num_rows == 0) { ?>

                    
                        <div class="row justify-content-center mt-5">
                            <div class="col-md-6">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-4"></i>
                                    <p class="fs-5 mt-2 mb-0">No ongoing transport</p>
                                </div>
                            </div>
                        </div>
                    

                <?php } else { ?>

                    <table class="table table-bordered table-hover" id="ongoingtransporttable">
                        <thead>
                            <tr>
                                <th>Transport ID</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($row = $ongoingTransports->fetch_assoc()) {

                                if ($row["transport_status"] == "Confirmed") {
                                    $bg = "bg-info";
                                } elseif ($row["transport_status"] == "Started") {
                                    $bg = "bg-primary";
                                } else {
                                    $bg = "bg-secondary";
                                }

                            ?>

                                <tr>
                                    <td>Transport #<?php echo $row['transport_id']; ?></td>
                                    <td class="<?php echo $bg; ?> text-dark text-center">
                                        <?php echo $row["transport_status"]; ?>
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>
                    </table>

                <?php } ?>
            
            </div>

            <div class="col-md-6 shadow-lg p-3 bg-white rounded" style="max-width: 40rem;">
                <h5 class="fw-bold">Available Vehicles</h5>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover text-center align-middle" id="vehicletable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Vehicle No</th>
                                <th>Type</th>
                                <th>Capacity (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $vehicleResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $row['vehicle_id']; ?></td>
                                    <td><?php echo $row['vehicle_number']; ?></td>
                                    <td><?php echo $row['vehicle_type']; ?></td>
                                    <td><?php echo $row['vehicle_capacity']; ?></td>


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

    <?php include_once '../includes/footer_includes.php'; ?>
</body>

<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
    $(document).ready(function() {
        $("#vehicletable").DataTable();
    });
</script>

<script>
    $(document).ready(function() {
        $("#ongoingtransporttable").DataTable();
    });
</script>

</html>