<?php
include_once '../commons/session.php';
include_once '../model/module_model.php';
include_once '../model/user_model.php';
include_once '../model/order_model.php';
//get user information from session
$userrow = $_SESSION["user"];

$orderObj = new Order();

$orderresult = $orderObj->getAllOrders();

$completedCount = 0;
$pendingCount = 0;
$confirmedCount = 0;
$cancelledCount = 0;

while ($row = $orderresult->fetch_assoc()) {

  if ($row["status_id"] == 15) {
    $completedCount++;
  } else if ($row["status_id"] == 1) {
    $pendingCount++;
  } else if ($row["status_id"] == 2) {
    $confirmedCount++;
  } else if ($row["status_id"] == 0) {
    $cancelledCount++;
  }
}

$pendingOrderPayments = $orderObj->getPendingOrderPaymentsCount();
$badge = $pendingOrderPayments->fetch_assoc();

$pendinOrderPaymentsCount = $badge["pending_count"];
$pendingRefundRequestsCount = 0;

$orderPaymentRequestResult = $orderObj->getAllOrderRefunds();
while ($row = $orderPaymentRequestResult->fetch_assoc()) {

  if ($row["refund_status"] == "Pending") {
    $pendingRefundRequestsCount++;
  }
}

// Initialize an array to hold the counts for each order stage
$orderStages = [
  "Cancelled" => 0,
  "Pending" => 0,
  "Confirmed" => 0,
  "In Planning" => 0,
  "Planned" => 0,
  "In Production" => 0,
  "Production Started" => 0,
  "Production Completed" => 0,
  "In Packing" => 0,
  "Packed" => 0,
  "In Warehouse" => 0,
  "Shipment Assigned" => 0,
  "Dispatched" => 0,
  "Transport Assigned" => 0,
  "In Transport" => 0,
  "Delivered" => 0

];

$orderResult = $orderObj->getAllOrders();

while ($row = $orderResult->fetch_assoc()) {

  switch ($row["status_id"]) {

    case 0:
      $orderStages["Cancelled"]++;
      break;
    case 1:
      $orderStages["Pending"]++;
      break;
    case 2:
      $orderStages["Confirmed"]++;
      break;
    case 3:
      $orderStages["In Planning"]++;
      break;
    case 4:
      $orderStages["Planned"]++;
      break;
    case 5:
      $orderStages["In Production"]++;
      break;
    case 6:
      $orderStages["Production Started"]++;
      break;
    case 7:
      $orderStages["Production Completed"]++;
      break;
    case 8:
      $orderStages["In Packing"]++;
      break;
    case 9:
      $orderStages["Packed"]++;
      break;
    case 10:
      $orderStages["In Warehouse"]++;
      break;
    case 11:
      $orderStages["Shipment Assigned"]++;
      break;
    case 12:
      $orderStages["Dispatched"]++;
      break;
    case 13:
      $orderStages["Transport Assigned"]++;
      break;
    case 14:
      $orderStages["In Transport"]++;
      break;
    case 15:
      $orderStages["Delivered"]++;
      break;
  }
}

?>
<html>

<head>
  <?php include_once "../includes/bootstrap_css_includes.php" ?>
  <title>Order Management</title>
  <script src="../js/plotly-3.0.1.min.js" charset="utf-8"></script>
</head>

<body style="border-radius:10px;">
  <div class="container">
    <?php $pageName = "ORDER MANAGEMENT" ?>
    <?php include_once "../includes/header_row_module_includes.php"; ?>
    <div class="row">
      <div class="col-md-4" style="text-align:left;">
        <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>

      </div>
      <div class="col-md-8" style="text-align:right;">
        <div class="btn-group">
          <a href="add-order.php" class="btn btn-outline-primary">Add Order</a>
          <a href="view-orders.php" class="btn btn-outline-success">View Orders</a>
          <a href="order-payments.php" class="btn btn-outline-info">
            Order Payments
            <!-- <span class="badge bg-info text-dark">
              <?php echo $pendinOrderPaymentsCount; ?>
            </span> -->
          </a>
          <a href="order-refund.php" class="btn btn-outline-secondary">Refund Requests
            <!-- <span class="badge bg-secondary text-white">
              <?php echo $pendingRefundRequestsCount; ?>
            </span> -->
          </a>
          <button
            class="btn btn-outline-warning"
            data-bs-toggle="modal"
            data-bs-target="#reportModal">

            Generate Order Reports

          </button>
        </div>
      </div>
    </div>
    <div class="row">
      &nbsp;
    </div>

    <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
      <span class="h3 mb-4 fw-bold">Order Summary</span>
      <div class="row d-flex justify-content-around text-center">
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Completed Orders</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $completedCount; ?> </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Pending Orders</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $pendingCount; ?> </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Confirmed Orders</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $confirmedCount; ?>
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Canceled Orders</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $cancelledCount; ?> </h1>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      &nbsp;
    </div>
   <div class="row">&nbsp;</div>


    <div id="orderProgressChart" style="width:100%; height:700px;"></div>



  </div>

  <div class="modal fade" id="reportModal">
    <div class="modal-dialog">
        <form action="generate-order-report.php" method="post" target="_blank">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Order Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>

                    <br>

                    <label>End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Generate Report
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let today = new Date().toISOString().split("T")[0];

    document.getElementById("start_date").setAttribute("max", today);
    document.getElementById("end_date").setAttribute("max", today);

});
</script>
  
  
  <?php include_once '../includes/footer_includes.php'; ?>
</body>

<script src="../js/jquery-3.7.1.js"></script>
<script src="../bootstrap/dist/js/bootstrap.js"></script>
<script src="../js/datatable/bootstrap.bundle.min.js"></script>
<script src="../js/datatable/dataTables.bootstrap5.js"></script>
<script src="../js/datatable/dataTables.js"></script>

<script>
  var data = [{
    type: "bar",
    orientation: "h",
    x: <?php echo json_encode(array_values($orderStages)); ?>,
    y: <?php echo json_encode(array_keys($orderStages)); ?>,
    text: <?php echo json_encode(array_values($orderStages)); ?>,
    textposition: "outside",
    marker: {
      color: "#0d6efd"
    }
  }];

  var layout = {
    title: {"text": "Orders by Production Stage"},
    height: 700,
    margin: {
      l: 180,
      r: 40,
      t: 60,
      b: 50
    },
    xaxis: {
      title: {"text": "Number of Orders"}
    },
    yaxis: {
      automargin: true
    }
  };

  Plotly.newPlot("orderProgressChart", data, layout, {
    responsive: true
  });
</script>



</html>