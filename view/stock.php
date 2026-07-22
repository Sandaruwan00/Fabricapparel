<?php

include_once '../commons/session.php';
include_once '../model/stock_model.php';

//get user information from session
$userrow = $_SESSION["user"];

$stockObj = new Stock();
$stockItemResult = $stockObj->getAllStockItems();

$materialsCount = 0;
$materialResult = $stockItemResult->fetch_assoc();
while ($row = $stockItemResult->fetch_assoc()) {
  $materialsCount++;
}

?>

<html>

<head>
  <?php include_once "../includes/bootstrap_css_includes.php" ?>
  <title>Stock Management</title>
</head>

<body style="border-radius:10px;">
  <div class="container">
    <?php $pageName = "STOCK MANAGEMENT" ?>
    <?php include_once "../includes/header_row_module_includes.php"; ?>

    <div class="row">
      <div class="col-md-4" style="text-align:left;">
        <a href="dashboard.php" type="button" class="btn btn-outline-secondary">Back</a>
      </div>
      <div class="col-md-8" style="text-align:right;">
        <div class="btn-group">
          <a href="stock-items.php" class="btn btn-outline-primary">Materials</a>
          <a href="stock-list.php" class="btn btn-outline-success">Inventory</a>
          <a href="stock-material-request.php" class="btn btn-outline-info">Stock Requests</a>
          <a href="stock-purchase-requests.php" class="btn btn-outline-secondary">Purchase Requests</a>
          <a href="generate-stock-report.php" class="btn btn-outline-warning">Generate Reports</a>
        </div>
      </div>
    </div>

    <div class="row">&nbsp;</div>

    <div class="row d-flex justify-content-around align-items-center shadow-lg" style="background: linear-gradient(90deg, #FDE9E1 0%, #B9D9EB 100%); padding: 20px; border-radius:10px;">
      <span class="h3 mb-4 fw-bold">Stock Summary</span>
      <div class="row d-flex justify-content-around text-center">
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Materials</div>
          <div class="card-body">
            <h1 class="card-title">
              <?php echo $materialsCount; ?> 
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Low Stocks</div>
          <div class="card-body">
            <h1 class="card-title">
              0</h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Material Requests</div>
          <div class="card-body">
            <h1 class="card-title">
              0
            </h1>
          </div>
        </div>
        <div class="col-md-3 shadow-lg card text-dark bg-white mb-3" style="max-width: 18rem;">
          <div class="card-header">Purchase Requests</div>
          <div class="card-body">
            <h1 class="card-title">
              0
          </div>
        </div>
      </div>
    </div>

    <div class="row">&nbsp;</div>






    <div class="row">&nbsp;</div>




  </div>
  <?php include_once '../includes/footer_includes.php'; ?>
</body>
<script src="../js/jquery-3.7.1.js"></script>

</html>