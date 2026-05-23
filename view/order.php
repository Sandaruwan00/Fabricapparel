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
    }
    else if ($row["status_id"] == 1) {
        $pendingCount++;
    }
    else if ($row["status_id"] == 2) {
        $confirmedCount++;
    }
    else if ($row["status_id"] == 0) {
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



?>
<html>

<head>
  <?php include_once "../includes/bootstrap_css_includes.php" ?>
  <title>Order Management</title>
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
          <a href="generate-order-report.php" class="btn btn-outline-warning">Generate Order Reports</a>
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

    <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
      <div class="row d-flex text-center">
        <div class="col-md-3">
    <a href="order-payments.php" class="text-decoration-none">
      <div class="card shadow-sm text-center p-3">
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
              <?php echo $pendinOrderPaymentsCount; ?>
            </span>
        <h4>Order Payments</h4>
        <p>Approve/Reject order payments</p>
      </div>
    </a>
  </div>
        <div class="col-md-3">
    <a href="order-refund.php" class="text-decoration-none">
      <div class="card shadow-sm text-center p-3">
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
              <?php echo $pendingRefundRequestsCount; ?>
            </span>
        <h4>Refund Requests</h4>
        <p>Approve/Reject refund payments</p>
      </div>
    </a>
  </div>
        
        
        
    </div>
    </div>
    

    <div class="row">
      &nbsp;
    </div>

    
    
    
    <div class="row">
      &nbsp;
    </div>
    
  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>